<?php

namespace HashtagCms\Testing;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use HashtagCms\Models\SiteProp;
use HashtagCms\Database\Seeds\HashtagCmsDatabaseSeeder;
use HashtagCms\Models\Site;
use HashtagCms\User;

abstract class TestCase extends \Tests\TestCase
{
    /**
     * Whether this test class should use SQLite with migrations.
     * Set to false in subclasses (e.g., AdminPanelBrowserTest) to use MySQL without migrations.
     */
    protected bool $usesSqlite = true;

    protected function setUp(): void
    {
        parent::setUp();

        // Only bootstrap SQLite for tests that need it (frontend tests).
        // Admin panel tests set $usesSqlite = false to use MySQL without migrations.
        if ($this->usesSqlite) {
            static::bootstrapSqliteIfNeeded();
        }

        $host = parse_url(env('APP_URL'), PHP_URL_HOST);
        $domains = config('hashtagcms.domains');
        $domains[$host] = 'web';
        config(['hashtagcms.domains' => $domains]);
    }

    /**
     * ✅ GOLDEN RULE: Tests ALWAYS use SQLite.
     * 
     * This method FORCES SQLite — it does NOT read .env, it does NOT check what
     * driver the app is configured with. It simply sets SQLite and moves on.
     * - Creates the sqlite file if it doesn't exist.
     * - Runs migrations + seeds if the database is empty.
     * - MySQL is never touched. Not now. Not ever.
     */
    public static function bootstrapSqliteIfNeeded(): void
    {
        $dbPath = base_path('database/dusk.sqlite');

        // 1. CREATE the SQLite file if it doesn't exist yet
        if (!file_exists($dbPath)) {
            touch($dbPath);
            fwrite(STDOUT, "\n📁 [HashtagCMS] Created SQLite: {$dbPath}\n");
        }

        // 2. FORCE the app to use this SQLite file — ignore .env completely
        config([
            'database.default'                       => 'sqlite',
            'database.connections.sqlite.driver'     => 'sqlite',
            'database.connections.sqlite.database'   => $dbPath,
            'database.connections.sqlite.prefix'     => '',
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        // 3. Run migrations + seeds if the database is empty
        if (!Schema::hasTable('sites')) {
            fwrite(STDOUT, "\n⚙️  [HashtagCms] Fresh SQLite — running migrate:fresh --seed...\n");
            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
            fwrite(STDOUT, "✅ [HashtagCms] Database ready.\n\n");
        }
    }

    /**
     * Check if the site is already installed.
     */
    protected function isSiteInstalled(): bool
    {
        try {
            if (!Schema::hasTable('site_props')) {
                return false;
            }
            $prop = SiteProp::where('name', 'site_installed')->first();
            return $prop && $prop->value == 1;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Ensure the site is NOT installed before running tests that require fresh installation.
     */
    protected function ensureSiteNotInstalled(): void
    {
        if ($this->isSiteInstalled()) {
            $this->markTestSkipped('Site is already installed. This test requires a fresh database.');
        }
    }

    /**
     * Ensure the site IS installed before running tests.
     * Safe: only seeds on SQLite (bootstrapSqliteIfNeeded already enforces this).
     */
    protected function ensureSiteInstalled(): void
    {
        if (!$this->isSiteInstalled()) {
            $this->seed(HashtagCmsDatabaseSeeder::class);
            $this->installSite();
        }
    }

    /**
     * Simulate a site installation.
     */
    protected function installSite($overrides = [])
    {
        $site = Site::find(1);
        if ($site) {
            if (isset($overrides['site_name']))    $site->name    = $overrides['site_name'];
            if (isset($overrides['site_domain']))  $site->domain  = $overrides['site_domain'];
            if (isset($overrides['site_context'])) $site->context = $overrides['site_context'];
            $site->save();
        }

        $user = User::find(1);
        if ($user) {
            if (isset($overrides['user_email']))    $user->email    = $overrides['user_email'];
            if (isset($overrides['user_password'])) $user->password = \Illuminate\Support\Facades\Hash::make($overrides['user_password']);
            if (isset($overrides['name']))          $user->name     = $overrides['name'];
            $user->save();
        }

        $siteInstalled = SiteProp::where('name', '=', 'site_installed')->first();
        if ($siteInstalled) {
            $siteInstalled->value = 1;
            $siteInstalled->save();
        }

        return Site::with('lang')->find(1);
    }

    /**
     * Get a staff user for testing.
     */
    protected function getStaffUser()
    {
        $user = User::where('user_type', 'Staff')->first();
        $this->assertNotNull($user, "Staff user not found. Did migrations and seeds run?");
        return $user;
    }

    /**
     * Get the admin base path.
     */
    protected function getAdminPath()
    {
        return ltrim(config('hashtagcmsadmin.cmsInfo.base_path', 'admin'), '/');
    }
}
