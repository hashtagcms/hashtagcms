<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Lcobucci\JWT\Exception;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Category;
use HashtagCms\Models\CategoryLang;
use HashtagCms\Models\CategorySite;
use HashtagCms\Models\Country;
use HashtagCms\Models\CountryLang;
use HashtagCms\Models\Currency;
use HashtagCms\Models\Hook;
use HashtagCms\Models\Lang;
use HashtagCms\Models\Module;
use HashtagCms\Models\ModuleProp;
use HashtagCms\Models\ModulePropLang;
use HashtagCms\Models\Platform;
use HashtagCms\Models\Site;
use HashtagCms\Models\SiteProp;
use HashtagCms\Models\StaticModuleContent;
use HashtagCms\Models\StaticModuleContentLang;
use HashtagCms\Models\Theme;
use HashtagCms\Models\User;
use HashtagCms\Models\Zone;

class SiteController extends BaseAdminController
{
    protected $dataFields = ['id', 'name', 'lang.title as title', 'domain', 'context'];

    protected $dataSource = Site::class;

    protected $dataWith = ['lang', 'category', 'theme', 'platform'];

    protected $minResults = 1;

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    protected $moreActionFields = [
        ['label' => 'Site Settings',
            'icon_css' => 'fa fa-cogs',
            'action' => 'settings',
            'action_append_field' => 'id'],
    ];

    protected $bindDataWithAddEdit = [
        'languages' => ['dataSource' => Lang::class, 'method' => 'combo'],
        'countries' => ['dataSource' => CountryLang::class, 'method' => 'all'],
    ];

    protected $moreActionBarItems = [
        ['label' => 'Clone Site',
            'as' => 'icon',
            'icon_css' => 'fa fa-copy', 'action' => 'site/copysite',
        ],
    ];

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = [
            'name' => 'required|max:100|string',
            'category_id' => 'nullable|numeric',
            'theme_id' => 'nullable|numeric',
            'lang_id' => 'nullable|numeric',
            'under_maintenance' => 'nullable|integer',
            'domain' => 'required|max:255|string|unique:sites,domain',
            'context' => 'required|max:40|string|unique:sites,context',
            'favicon' => 'nullable|file',
            'lang_count' => 'nullable|numeric',
            'lang_title' => 'required|max:255|string',
        ];

        if ($request->input('id') > 0) {
            $rules['domain'] = 'required|max:255|string|unique:sites,domain,'.$request->input('id');
            $rules['context'] = 'required|max:40|string|unique:sites,context,'.$request->input('id');
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $module_name = request()->module_info->controller_name;

        $saveData['name'] = $data['name'];
        $saveData['domain'] = $data['domain'];
        $saveData['context'] = $data['context'];
        $saveData['under_maintenance'] = $data['under_maintenance'] ?? 0;

        $saveData['category_id'] = $data['category_id'] ?? 0;
        $saveData['theme_id'] = $data['theme_id'] ?? 0;
        $saveData['lang_id'] = $data['lang_id'] ?? 0;
        $saveData['platform_id'] = $data['platform_id'] ?? 0;
        $saveData['country_id'] = $data['country_id'] ?? 0;

        $langData['title'] = $data['lang_title'];

        $icon = $this->upload($module_name, request()->file('favicon'));

        if ($icon != null) {
            $saveData['favicon'] = $icon;
        }

        //it will have some value if user has clicked on delete
        if ($data['favicon_deleted'] != '0') {
            $saveData['favicon'] = '';
        }

        $arrLangData = ['data' => $langData];

        $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];

        if ($data['actionPerformed'] == 'edit') {

            $where = $data['id'];
            //This is in base controller
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

        } else {

            //At the time of creation

            //This is in base controller
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);

        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return htcms_admin_view('site.saveinfo', $viewData);
    }

    /**
     * Get settings setting
     *
     * @param  int  $id
     * @param  string  $tab
     * @return mixed
     */
    public function settings($id = 1, $tab = 'platforms')
    {

        if (! $this->checkPolicy('read')) {
            return htcms_admin_view('common.error', Message::getReadError());
        }

        $siteInfo = Site::allInfo($id);

        $tab = (empty($tab)) ? 'platforms' : $tab;

        $viewData['siteInfo'] = $siteInfo;
        $viewData['tabs'] = ['Platforms', 'Hooks', 'Languages', 'Zones',
            'Countries', 'Currencies', 'Modules',
            'Static Modules', 'Themes', 'Categories', 'Site Properties', 'Module Properties'];

        $tab = str_replace(' ', '', strtolower($tab));
        $toBeSelected = Str::singular($tab);

        $selectedData = $viewData['siteInfo']->$toBeSelected; //lazy load

        $data = [];
        $viewName = 'settings';
        $tabName = Str::plural($tab);
        switch ($tabName) {
            case 'countries':
                $data = ['label' => 'Countries', 'data' => Country::with('lang:country_id,name')->get(['id'])];
                break;

            case 'zones':
                $data = ['label' => 'Zones', 'data' => Zone::all(['id', 'name'])];
                break;

            case 'platforms':
                $data = ['label' => 'Platforms', 'data' => Platform::all(['id', 'name'])];
                break;

            case 'languages':
                $data = ['label' => 'Languages',
                    'data' => Lang::all(['id', 'name']),
                ];
                break;

            case 'hooks':
                $data = ['label' => 'Hooks', 'data' => Hook::all(['id', 'name'])];
                break;

            case 'currencies':
                $data = ['label' => 'Currency', 'data' => Currency::all(['id', 'name'])];
                break;

            case 'modules':
                $viewName = 'copier';
                $selectedData = Module::withoutGlobalScopes()->where('site_id', '=', $siteInfo->id)->get();

                $message = ($selectedData->count() > 0) ? 'Below modules are already available in your site.' : 'You can copy module from desired site. Choose site option, select and click on Add Selected.';
                //Because we are shwoing site to choose
                $data = ['label' => 'Modules',
                    'data' => ['allSites' => Site::with('lang')->get()],
                    'message' => $message,
                ];

                break;

            case 'staticmodules':
                $viewName = 'copier';
                $selectedData = StaticModuleContent::withoutGlobalScopes()->where('site_id', '=', $siteInfo->id)->get();

                $message = ($selectedData->count() > 0) ? 'Below static modules are already available in your site.' : 'You can copy static module from desired site. Choose site option, select and click on Add Selected.';
                //Because we are shwoing site to choose
                $data = ['label' => 'StaticModules',
                    'data' => ['allSites' => Site::with('lang')->get()],
                    'message' => $message,
                ];

                break;

            case 'siteproperties':
                $viewName = 'copier';
                $selectedData = SiteProp::withoutGlobalScopes()->where('site_id', '=', $siteInfo->id)->get();
                $message = ($selectedData->count() > 0) ? 'Below properties are already available in your site.' : 'You can copy properties from desired site. Choose site option, select and click on Add Selected.';
                //Because we are shwoing site to choose
                $data = ['label' => 'SiteProperties',
                    'data' => ['allSites' => Site::with('lang')->get()],
                    'message' => $message,
                ];

                break;

            case 'themes':
                $viewName = 'copier';
                $selectedData = Theme::withoutGlobalScopes()->where('site_id', '=', $siteInfo->id)->get();
                //info($siteInfo->id. " " .json_encode($selectedData));
                $message = ($selectedData->count() > 0) ? 'Below themes are already available in your site.' : 'You can copy theme from desired site. Choose site option, select and click on Add Selected.';
                //Because we are shwoing site to choose
                $data = ['label' => 'Themes',
                    'data' => ['allSites' => Site::with('lang')->get()],
                    'message' => $message,
                ];

                break;

            case 'categories':
                $viewName = 'copier';
                $selectedData = Category::withoutGlobalScopes()->with('lang')->where('site_id', '=', $siteInfo->id)->get();

                //info(json_encode($selectedData));
                $path = htcms_admin_path('category/settings');
                $message = ($selectedData->count() > 0) ? 'Below categories are already available in your site.' : 'You can copy categories from desired site. Choose site option, select and click on Add Selected.';
                //Because we are shwoing site to choose
                $data = ['label' => 'Categories',
                    'data' => ['allSites' => Site::with('lang')->get()],
                    'message' => $message];
                break;

            case 'moduleproperties':
                $viewName = 'copier';
                $selectedData = ModuleProp::withoutGlobalScopes()->where('site_id', '=', $siteInfo->id)->get();
                $message = ($selectedData->count() > 0) ? 'Below properties are already available in your site.' : 'You can copy properties from desired site. Choose site option, select and click on Add Selected.';
                //Because we are shwoing site to choose
                $data = ['label' => 'SiteProperties',
                    'data' => ['allSites' => Site::with('lang')->get()],
                    'message' => $message,
                ];

                break;
        }

        $viewData['selectedData'] = $selectedData;
        $viewData['activeTab'] = Str::plural(strtolower($tab));

        $viewData['allData'] = $data;
        $viewData['backURL'] = $this->getBackURL();

        //return $viewData;
        return $this->viewNow('site.'.$viewName, $viewData);
    }

    /**
     * Save settings in current site. If you have pivot table
     * Find relationa and attach and detach from the site
     *
     * @return mixed
     */
    public function saveSettings($byPrams = null)
    {
        //return array("message"=>"okay!");
        if (! $this->checkPolicy('edit')) {
            if (\request()->ajax()) {
                return response()->json(Message::getWriteError(), 401);
            } else {
                return htcms_admin_view('common.error', Message::getWriteError());
            }
        }

        $data = ($byPrams == null) ? request()->all() : $byPrams;
        $site_id = $data['site_id'];
        $key = $data['key'];
        $ids = $data['ids'];
        $action = $data['action'];

        $site = Site::find($site_id);

        if ($action === 'add') {
            $site->attachThings($key, $ids);

            return ['isSaved' => 1, 'ids' => $ids];
        }
        $site->detachThings($key, $ids);

        return ['isSaved' => 1, 'ids' => $ids];
    }

    /**
     * Check if module is exist
     *
     * @param  array  $data
     * @param  string  $compare
     *                           $param string $compareKey - row key
     * @return bool
     */
    private function isExist($data = [], $compare = '', $compareKey = '')
    {
        //if array -> comare = (value1, value2); //comparreKey = (key1, key2);
        //else -> comare = value1; //comparreKey = key1;
        foreach ($data as $key => $val) {
            if (is_array($compareKey)) {
                $valArr = [];
                foreach ($compareKey as $cKey) {
                    $valArr[] = $val[$cKey];
                }
                //check with compare keys and values
                if ($valArr === $compare) {
                    return true;
                }
            } else {
                if ($compare === $val[$compareKey]) {
                    return true;
                }
            }

        }

        return false;
    }

    /**
     * Remove settings. such as modules, categories, themes etc
     *
     * @return mixed
     */
    public function removeSettings()
    {
        if (! $this->checkPolicy('edit')) {
            if (\request()->ajax()) {
                return response()->json(Message::getWriteError(), 401);
            } else {
                return htcms_admin_view('common.error', Message::getWriteError());
            }
        }
        $data = request()->all();

        $site_id = $data['site_id'];
        $ids = $data['ids'];
        $removeWhat = $data['type'];
        //To make it more readable - let use switch.

        $withData = [];
        switch (strtolower($removeWhat)) {
            case 'modules':
                $source = Module::class;
                break;
            case 'categories':
                $withData = ['lang'];
                $source = Category::class;
                break;
            case 'staticmodules':
                $source = StaticModuleContent::class;
                break;
            case 'siteproperties':
                $source = SiteProp::class;
                break;
            case 'moduleproperties':
                $source = ModuleProp::class;
                break;
            case 'themes':
                $source = Theme::class;
                break;
        }

        $rData['deleted'] = $source::withoutGlobalScopes()->find($ids)->each->forceDelete();
        $rData['siteData'] = $source::with($withData)->withoutGlobalScopes()->where('site_id', '=', $site_id)->get(); //get fresh one

        return $rData;

    }

    /**
     * @param  null  $tabs
     * @return bool
     */
    private function saveSettingWithLangs($data, $source, $langSource, $idField = null, $tabs = null, $toSite = null)
    {

        $inserted = false;

        //info(json_encode($data));
        //return false;
        $toSiteId = $toSite['site_id'];
        $idField = ($idField === null) ? '' : $idField;
        $data = ($data === null) ? [] : $data;

        DB::beginTransaction();

        foreach ($data as $key => $val) {

            $oldId = $val['id'];

            //get all languages for a category
            $langData = $langSource::withoutGlobalScopes()->where($idField, '=', $oldId)->get()->toArray();

            unset($val['id']);
            unset($val['lang']); //if exists

            $content = new $source;
            foreach ($val as $k => $v) {
                $content->$k = $v;

                //module id - need to solve for below
                if ($tabs === 'moduleproperties') {
                    //change module id based on site
                    if ($k === 'module_id') {
                        $mD = DB::selectOne("select id from modules where alias=(select alias from modules where id=$v) and site_id=$toSiteId");
                        if ($mD) {
                            $content->$k = $mD->id;
                        }
                    }
                    json_encode($content);
                }
            }
            $inserted = $content->save();

            //Need to copy category_sites
            if ($tabs === 'categories') {
                $siteData = CategorySite::where('category_id', '=', $oldId)->get()->toArray();
                $sData = [];
                foreach ($siteData as $s => $sd) {
                    $sd['category_id'] = $content->id;
                    $sd['site_id'] = $content->site_id;
                    $sd['theme_id'] = Theme::getThemeIdThroughSite((int) $sd['theme_id'], (int) $val['site_id']); //it already has withoutGlobalScopes()
                    $sd['created_at'] = htcms_get_current_date();
                    $sd['updated_at'] = htcms_get_current_date();

                    //if that category has theme and it is available in category_site table.
                    if ($sd['theme_id']) {
                        $sData[] = $sd;
                    }

                }
                try {
                    //info($sData);
                    $inserted = CategorySite::insert($sData);
                } catch (\Exception $e) {
                    DB::rollBack();

                    $inserted = false;
                }
            }

            foreach ($langData as $lKey => $lVal) {

                $lVal[$idField] = $content->id;

                $lVal['created_at'] = htcms_get_current_date();
                $lVal['updated_at'] = htcms_get_current_date();

                try {
                    $inserted = $content->lang()->create($lVal);
                } catch (\Exception $e) {
                    DB::rollBack();
                    $inserted = false;
                }

            }

        }

        DB::commit();

        return $inserted;
    }

    /**
     * Copy things from source site to target site
     *
     * @return array
     */
    public function copySettings($byPrams = null)
    {

        if (! $this->checkPolicy('edit')) {
            if (\request()->ajax()) {
                return response()->json(Message::getWriteError(), 401);
            } else {
                return htcms_admin_view('common.error', Message::getWriteError());
            }
        }

        $data = ($byPrams == null) ? request()->all() : $byPrams;
        $fromSite = $data['fromSite'];
        $toSite = $data['toSite'];
        $copyWhat = $data['type'];
        $toSite_id = $toSite['site_id'];

        $resetId = true;
        $compareKey = 'alias';
        switch (strtolower($copyWhat)) {
            case 'modules':
                $source = Module::class;
                break;

            case 'staticmodules':
                $source = StaticModuleContent::class;
                $resetId = false;
                break;

            case 'siteproperties':
                $compareKey = ['name', 'platform_id'];
                $source = SiteProp::class;
                break;

            case 'themes':
                $source = Theme::class;
                break;

            case 'categories':
                $compareKey = 'link_rewrite';
                $source = Category::class;
                $resetId = false;
                break;

            case 'moduleproperties':
                $compareKey = ['name', 'platform_id'];
                $source = ModuleProp::class;
                $resetId = false;
                break;
            default:
                return ['error' => true, 'message' => "Don't know what to do."];
                break;
        }

        $existing = $source::where('site_id', '=', $toSite_id)->withoutGlobalScopes()->get(); //fetch existing

        $data = $fromSite['data'];

        $toBeInserted = [];
        $alreadyExist = [];

        if (count($existing) > 0) {
            //filter
            foreach ($data as $key => $val) {
                if (is_array($compareKey)) {
                    $values = [];
                    foreach ($compareKey as $cKey) {
                        $values[] = $val[$cKey];
                    }
                } else {
                    $values = $val[$compareKey];
                }
                if ($this->isExist($existing, $values, $compareKey) === false) {
                    $toBeInserted[] = $val;
                } else {
                    $alreadyExist[] = $val;
                }
            }

        } else {
            $toBeInserted = $data;
        }

        //return $toBeInserted; //for testing

        //remove some of keys and add site id
        $finalData = [];
        if (count($toBeInserted) > 0) {
            foreach ($toBeInserted as $key => $val) {
                $finalData[$key] = $val;
                $finalData[$key]['site_id'] = $toSite_id;

                if ($resetId === true) {
                    unset($finalData[$key]['id']);
                }
                unset($finalData[$key]['selected']);
                unset($finalData[$key]['created_at']);
                unset($finalData[$key]['updated_at']);
                unset($finalData[$key]['deleted_at']);

                $finalData[$key]['created_at'] = htcms_get_current_date();
                $finalData[$key]['updated_at'] = htcms_get_current_date();
            }
        }
        $inserted = false;
        //return $finalData;
        $msg = '';
        $withData = [];
        if (count($finalData) > 0) {
            DB::beginTransaction();
            try {
                switch (strtolower($copyWhat)) {
                    case 'modules':
                        $inserted = Module::insert($finalData);
                        break;

                    case 'staticmodules':
                        $inserted = $this->saveSettingWithLangs($finalData, StaticModuleContent::class, StaticModuleContentLang::class, 'static_module_content_id', 'staticmodules', $toSite);
                        break;

                    case 'siteproperties':
                        $inserted = SiteProp::insert($finalData);
                        break;

                    case 'themes':
                        $inserted = Theme::insert($finalData);
                        break;

                    case 'categories':
                        $withData = ['lang'];
                        $inserted = $this->saveSettingWithLangs($finalData, Category::class, CategoryLang::class, 'category_id', 'categories', $toSite);
                        break;

                    case 'moduleproperties':
                        $withData = ['lang'];
                        $inserted = $this->saveSettingWithLangs($finalData, ModuleProp::class, ModulePropLang::class, 'module_prop_id', 'moduleproperties', $toSite);
                        break;
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $msg = $e->getMessage().' @ '.$e->getLine().' fileName: '.$e->getFile();
                $inserted = false;
            }
            DB::commit();
        }

        //info("toSite_id: ".$toSite_id);
        $rData['siteData'] = $source::with($withData)->where('site_id', '=', $toSite_id)->withoutGlobalScopes()->get(); //get fresh one
        $rData['inserted'] = $inserted;
        $rData['copied'] = $finalData;
        $rData['ignored'] = $alreadyExist;
        $rData['message'] = $msg;

        return $rData;
    }

    /**
     * Get all sites
     *
     * @return Site[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllSite()
    {
        return Site::all();
    }

    /**
     * Get all sites
     *
     * @return Site[]|\Illuminate\Database\Eloquent\Collection
     */
    public function allsites()
    {
        return Site::all();
    }

    /**
     * Get all sites for user
     *
     * @return Site[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getSitesForUsers()
    {
        //$user = User::find(Auth::user()->id);

        $allSites = Site::getSupportedSitesForUser(Auth::user()->id);

        /*        $isAdmin = $user->isSuperAdmin();

                $currentSiteId = htcms_get_siteId_for_admin();

                $allSites = Site::all();
                if ($isAdmin == 1) {
                    return $allSites;
                }
                $supportedSites = $user->supportedSites();
                $supportedSites = collect($supportedSites)->pluck("site_id")->toArray();

                $allSites = Site::find($supportedSites);*/

        //Set sites for theme
        /*if (array_search($currentSiteId, $supportedSites) === false) {
            htcms_set_siteId_for_admin($supportedSites[0]);
        }*/

        return $allSites;
    }

    /**
     * Get tabs data by site
     *
     * @param  null  $site_id
     * @return array
     */
    public function getBySite($site_id = null, $key = 'modules')
    {

        if ($site_id != null) {
            switch (strtolower($key)) {
                case 'modules':
                    $data = Module::withoutGlobalScopes()->where('site_id', '=', $site_id)->get();
                    break;

                case 'staticmodules':
                    $data = StaticModuleContent::withoutGlobalScopes()->where('site_id', '=', $site_id)->get();
                    break;

                case 'siteproperties':
                    $data = SiteProp::withoutGlobalScopes()->where('site_id', '=', $site_id)->get();
                    break;

                case 'themes':
                    $data = Theme::withoutGlobalScopes()->where('site_id', '=', $site_id)->get();
                    break;

                case 'categories':
                    $data = Category::withoutGlobalScopes()->with('lang')->where('site_id', '=', $site_id)->get();
                    break;

                case 'moduleproperties':
                    $data = ModuleProp::withoutGlobalScopes()->where('site_id', '=', $site_id)->get();
                    break;
            }

            return $data;
        }

        return ['error' => true, 'message' => 'Site id is not provided'];
    }

    /**
     * @return mixed
     */
    public function copysite()
    {
        $viewData['siteInfo'] = Site::with('lang')->get();
        $viewData['backURL'] = $this->getBackURL();

        return $this->viewNow('site.site-clone', $viewData);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed|string|void
     */
    public function cloneSite($source_site_id = null, $target_site_id = null)
    {

        if (! $this->checkPolicy('edit')) {
            if (\request()->ajax()) {
                return response()->json(Message::getWriteError(), 401);
            } else {
                return htcms_admin_view('common.error', Message::getWriteError());
            }
        }

        if (empty($source_site_id)) {
            $data = request()->all();
            $source_site_id = $data['sourceSiteId'];
            $target_site_id = $data['tagetSiteId'];
        }

        if ($source_site_id == $target_site_id) {
            $errorData['status'] = 400;
            $errorData['title'] = 'Alert';
            $errorData['message'] = "Source and target site id can't be the same.";

            return response()->json($errorData, $errorData['status']);
        }

        $siteInfo = Site::allInfo($source_site_id);
        $datas = [];
        $targetSiteInfo = Site::find($target_site_id);

        if ($targetSiteInfo === null || $siteInfo === null) {
            return 'Source or target data is missing';
        }

        try {

        } catch (\Exception $exception) {

        }

        //Pivot setting things
        $itemsToAttach = ['platform', 'hook', 'language', 'zone', 'country', 'currency'];
        foreach ($itemsToAttach as $key => $item) {
            $finder = ($item == 'language') ? 'lang' : $item;
            $finder = Str::title($finder);
            $namespace = config('hashtagcms.namespace');
            $finder = resolve($namespace.'Models\\'.$finder);
            $attach['key'] = Str::plural($item);
            $attach['ids'] = $finder::all('id')->pluck('id')->toArray();
            $attach['site_id'] = $target_site_id;
            $attach['action'] = 'add';
            $res = $this->saveSettings($attach);
            $msg = (! empty($res) && $res['isSaved']) ? Str::title($item).' copied' : "Unable to copy $item";
            $datas[] = ['message' => $msg, 'component' => $item];
        }

        //Copy things
        $itemsToCopy = ['modules', 'staticmodules', 'themes', 'categories', 'siteproperties', 'moduleproperties'];
        foreach ($itemsToCopy as $key => $item) {
            try {
                $data['fromSite'] = ['site_id' => $source_site_id, 'data' => $this->getBySite($source_site_id, $item)->toArray()];
                $data['toSite'] = ['site_id' => $target_site_id];
                $data['type'] = $item;
                $res = $this->copySettings($data);

                $title = $item;
                $ignored = count($res['ignored']);
                $copied = count($res['copied']);
                $datas[] = ['message' => "$copied $title copied and $ignored $title ignored", 'component' => $item];
            } catch (Exception $exception) {
                $datas[] = ['message' => "$copied $title copied and $ignored $title ignored", 'component' => $item];
                $datas[] = $exception->getMessage();
            }

        }
        //Copy modules by category in module_site
        $datas[] = ['message' => '----------------------------------------', 'component' => ''];

        //Set default ids for target site;
        $category_id = $siteInfo->category_id;
        $theme_id = $siteInfo->theme_id;
        $platform_id = $siteInfo->platform_id;
        $lang_id = $siteInfo->lang_id;
        $country_id = $siteInfo->country_id;

        //set category
        $categoryInfo = Category::withoutGlobalScopes()->where('id', '=', $category_id)->first();
        $targetCategoryInfo = Category::withoutGlobalScopes()->where([['link_rewrite', '=', $categoryInfo->link_rewrite], ['site_id', '=', $target_site_id]])->first();

        //set theme
        $themeInfo = Theme::withoutGlobalScopes()->where('id', '=', $theme_id)->first();
        $targetThemeInfo = Theme::withoutGlobalScopes()->where([['alias', '=', $themeInfo->alias], ['site_id', '=', $target_site_id]])->first();

        $targetSiteInfo->category_id = $targetCategoryInfo->id;
        $targetSiteInfo->theme_id = $targetThemeInfo->id;
        $targetSiteInfo->platform_id = $platform_id;
        $targetSiteInfo->lang_id = $lang_id;
        $targetSiteInfo->country_id = $country_id;
        $targetSiteInfo->save();

        //Time to copy the modules
        $site = Site::with(['platform'])->find($source_site_id);
        $allTeants = $site->platform;
        $categories = Category::withoutGlobalScopes()->where('site_id', '=', $source_site_id)->get();

        $data = ['success' => false];
        foreach ($categories as $category) {
            $link_rewrite = $category->link_rewrite;
            $tagetCategoryInfo = Category::withoutGlobalScopes()->where([['site_id', '=', $target_site_id], ['link_rewrite', '=', $link_rewrite]])->first();
            if ($tagetCategoryInfo) {
                foreach ($allTeants as $platform) {
                    $fromData['site_id'] = $source_site_id;
                    $fromData['platform_id'] = $platform->id;
                    $fromData['category_id'] = $category->id;
                    $fromData['microsite_id'] = 0; //@todo: For microsite

                    $toData['site_id'] = $target_site_id;
                    $toData['platform_id'] = $platform->id;
                    $toData['category_id'] = $tagetCategoryInfo->id;
                    $toData['microsite_id'] = 0; //@todo: For microsite
                    $data = Module::copyData($fromData, $toData); //this will return only last
                    $datas[] = ['success' => $data['success'], 'message' => $data['message']." - $link_rewrite, and platform: {$platform->link_rewrite}", 'component' => 'module_site_copy', 'data' => ['fromData' => $fromData, 'toData' => $toData]];
                }
            } else {
                $datas[] = ['success' => false, 'message' => "Could not find cateogry $link_rewrite in target site", 'component' => 'module_site_copy'];
            }

        }

        return $datas;
    }
}
