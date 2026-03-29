<?php

namespace HashtagCms\Http\Controllers\Installer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use HashtagCms\Http\Controllers\Controller;
use HashtagCms\Models\Site;
use HashtagCms\Models\SiteProp;
use HashtagCms\User;
use HashtagCms\Core\Utils\CacheKeys;

class InstallController extends Controller
{
    /**
     * Render the installer page.
     *
     * If required tables / seed data are missing the view receives
     * needsDbSetup=true and the Vue component will call POST /install/run
     * before showing the configuration form.
     */
    public function index(Request $request)
    {
        // If the DB hasn't been migrated yet, skip the model queries entirely.
        if (! $this->tablesExist()) {
            return view('hashtagcms::installer/index', [
                'siteInfo'     => (object) ['context' => Str::uuid(), 'domain' => '', 'name' => '', 'lang' => null],
                'isInstalled'  => false,
                'needsDbSetup' => true,
            ]);
        }

        $data = $this->getInfo();

        if ($data['isInstalled'] === true) {
            return redirect('/')->with(CacheKeys::CMS_MESSAGE, 'Site is already configured');
        }

        $data['siteInfo']->context = Str::uuid();
        $data['needsDbSetup'] = false;

        return view('hashtagcms::installer/index', $data);
    }

    // -----------------------------------------------------------------------
    // Status endpoint  –  GET /install/status
    // -----------------------------------------------------------------------

    /**
     * Return the current installation state as JSON.
     * Safe to call even before migrations have run.
     */
    public function status()
    {
        $tablesExist = $this->tablesExist();
        $isInstalled = false;

        if ($tablesExist) {
            $isInstalled = $this->isInstalled();
        }

        return response()->json([
            'tablesExist' => $tablesExist,
            'isInstalled' => $isInstalled,
        ]);
    }

    // -----------------------------------------------------------------------
    // Individual granular install steps (migrate → seed → publish)
    // -----------------------------------------------------------------------

    /**
     * Step 1: Run migrations
     */
    public function runMigrate(Request $request) {
        return $this->runStep(function() {
            Artisan::call('migrate', ['--force' => true]);
        }, 'migrate');
    }

    /**
     * Step 2: Seed the DB
     */
    public function runSeed(Request $request) {
        return $this->runStep(function() {
            Artisan::call('db:seed', [
                '--class' => 'HashtagCms\\Database\\Seeds\\HashtagCmsDatabaseSeeder',
                '--force' => true,
            ]);
        }, 'seed');
    }

    /**
     * Step 3: Publish assets
     */
    public function runPublish(Request $request) {
        return $this->runStep(function() {
            Artisan::call('vendor:publish', ['--tag' => 'hashtagcms.assets', '--force' => true]);
            Artisan::call('vendor:publish', ['--tag' => 'hashtagcms.views.frontend', '--force' => true]);
            Artisan::call('vendor:publish', ['--tag' => 'hashtagcms.views.admincommon', '--force' => true]);
        }, 'publish');
    }

    /**
     * Helper to run an Artisan command safely with output buffering.
     */
    protected function runStep(callable $callback, string $stepName) {
        // Clear all buffers and start fresh
        while (ob_get_level() > 0) ob_end_clean();
        ob_start();

        try {
            $callback();
            $strayOutput = ob_get_clean();
            if (!empty($strayOutput)) {
                \Illuminate\Support\Facades\Log::debug("HashtagCms Step $stepName Stray Output:", ['output' => $strayOutput]);
            }

            $siteInfo = ($stepName === 'publish' && $this->tablesExist()) ? Site::with('lang')->find(1) : null;

            if($siteInfo){
                $siteInfo->context = Str::uuid();
                $siteInfo->domain = request()->schemeAndHttpHost();
            }
            
            return response()->json([
                'success'  => true,
                'step'     => $stepName,
                'siteInfo' => $siteInfo
            ]);

        } catch (\Exception $e) {
            $strayOutput = ob_get_clean();
            $error = $e->getMessage() . ' | ' . Artisan::output();
            if (!empty($strayOutput)) {
                $error .= " | Stray: $strayOutput";
            }
            return response()->json([
                'success' => false,
                'step'    => $stepName,
                'error'   => $error
            ], 500);
        }
    }

    /**
     * Keep the old endpoint for compatibility if needed, but it's likely unused now.
     */
    public function runInstall(Request $request)
    {
        // ... (This can be kept or removed once frontend is updated. Let's keep it simple for now).
        return response()->json(['message' => 'Please use step-by-step endpoints.']);
    }

    // -----------------------------------------------------------------------
    // Save endpoint  –  POST /install/save  (unchanged logic, hardened)
    // -----------------------------------------------------------------------

    public function save(Request $request)
    {
        $rules = [
            'site_name'     => 'required|max:100|string',
            'site_title'    => 'required|max:255|string',
            'site_context'  => 'required|max:40|string',
            'site_domain'   => 'required|max:255|string',
            'name'          => 'required|max:255|string',
            'user_email'    => 'required|max:255|email',
            'user_password' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $validator->getMessageBag()->toArray(),
                ], 400);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        if (! $this->isInstalled()) {
            $user = User::find(1);
            $user->email    = $data['user_email'];
            $user->name     = $data['name'];
            $user->password = Hash::make($data['user_password']);
            $user->save();

            $site = Site::with('lang')->find(1);
            $site->name    = $data['site_name'];
            $site->context = $data['site_context'];
            $site->domain  = $data['site_domain'];
            $site->save();

            $site->lang()->update(['title' => $data['site_title']]);

            $siteInstalled        = SiteProp::where('name', '=', 'site_installed')->first();
            $siteInstalled->value = 1;
            $siteInstalled->save();

            $info               = $this->getInfo();
            $info['isInstalled'] = 1;
        } else {
            $info          = $this->getInfo();
            $info['error'] = 'Unable to make the changes. Please login to change the info.';
            $info['title'] = 'Error!';
        }

        return $info;
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * True when both the sites table and site_props table exist,
     * meaning cms:install (or equivalent) has already been run.
     */
    private function tablesExist(): bool
    {
        return Schema::hasTable('sites') && Schema::hasTable('site_props');
    }

    private function getInfo(): array
    {
        $site = Site::with('lang')->find(1);

        return [
            'siteInfo'    => $site,
            'isInstalled' => $this->isInstalled(),
        ];
    }

    /**
     * True when site_props has `site_installed` = 1.
     */
    private function isInstalled(): bool
    {
        $siteInstalled = SiteProp::where('name', '=', 'site_installed')->first();

        return ($siteInstalled !== null && (string) $siteInstalled->value === '1');
    }
}
