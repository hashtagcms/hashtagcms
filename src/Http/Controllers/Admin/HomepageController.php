<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Category;
use HashtagCms\Models\CmsPermission;
use HashtagCms\Models\Module;
use HashtagCms\Models\Platform;
use HashtagCms\Models\Site;
use HashtagCms\Models\Theme;

class HomepageController extends BaseAdminController
{
    private $tableModuleSite = 'module_site';

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError(), $request->ajax());
        }

        $rules = [];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $saveData['name'] = $data['name'];

        $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];

        if ($data['actionPerformed'] == 'edit') {

            $where = $data['id'];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData, $where);
            //$savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

        } else {
            //This is in base controller
            $savedData = $this->saveData($arrSaveData);
            //$savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);
        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }

    /**
     * Ui Alias
     *
     * @param  null  $more
     * @return \Illuminate\Http\Response|void
     */
    public function index($more = null)
    {

        if (! $this->checkPolicy('read')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $site = Site::find(htcms_get_siteId_for_admin()); //Get Site Info

        $categoryInfo = Category::find($site->category_id);

        if ($categoryInfo != null) {
            $themeInfo = Theme::find($categoryInfo->theme_id);

            $viewData['siteInfo'] = $site;
            $viewData['categoryInfo'] = $categoryInfo;
            $viewData['themeInfo'] = $themeInfo;
            $viewData['categoryModules'] = Category::getModules($site->category_id);
            $viewData['allModules'] = Module::all();
            $viewData['user_rights'] = $this->getUserRights();
        }

        $site_id = htcms_get_siteId_for_admin();
        $site = Site::find($site_id); //Get Site Info
        $category_id = (isset($site->category_id) || $site->category_id > 0) ? $site->category_id : 0;
        $platform_id = (isset($site->platform_id) || $site->platform_id > 0) ? $site->platform_id : Platform::all()[0]->id;

        $params = ['category_id' => $category_id, 'platform_id' => $platform_id, 'site_id' => $site_id];

        return redirect()->intended(htcms_admin_path('homepage/ui', $params));
    }

    /**
     * Show UI for homepage
     *
     * @return mixed
     */
    public function ui(Request $request)
    {

        if (! $this->checkPolicy('read')) {
            return htcms_admin_view('common.error', Message::getReadError());
        }

        $request = request()->all();
        $site_id = $request['site_id'] ?? htcms_get_siteId_for_admin();

        $microsite_id = $request['microsite_id'] ?? 0;

        $site = Site::find($site_id); //Get Site Info

        $category_id = $request['category_id'] ?? $site->category_id;
        $platform_id = $request['platform_id'] ?? $site->platform_id;

        $categoryInfo = Category::find($category_id);
        $themeInfo = [];
        $categoryModules = [];

        if (isset($categoryInfo) && ! empty($categoryInfo)) {

            $theme_id = $categoryInfo->getFromSitePivot($platform_id, 'theme_id');

            if ($theme_id) {

                $themeInfo = Theme::find($theme_id);
            }

            //info("theme_id ".$theme_id);
            $categoryModules = Category::getModules($category_id, $platform_id, $site_id, $microsite_id)->toArray();
        }

        $allModules = Module::where('site_id', '=', $site_id)->get();
        $allSites = Site::with('category')->get();

        $viewData['site_id'] = $site_id;
        $viewData['microsite_id'] = $microsite_id;
        $viewData['platform_id'] = $platform_id;
        $viewData['category_id'] = $category_id;

        $viewData['siteInfo'] = $site;

        $viewData['allSites'] = $allSites;
        $viewData['allModules'] = $allModules;
        $viewData['categoryModules'] = $categoryModules;
        $viewData['categoryInfo'] = (isset($categoryInfo) && ! empty($categoryInfo)) ? $categoryInfo : [];
        $viewData['themeInfo'] = $themeInfo;
        $viewData['user_rights'] = $this->getUserRights();
        $viewData['isModuleReadonly'] = CmsPermission::isReadyOnly(request()->module_info->id, auth()->user()->id);

        return $this->viewNow('homepage.index', $viewData);

    }

    /**
     * Save Settings
     *
     * @return mixed
     */
    public function saveSettings()
    {

        $request = request();

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError(), $request->ajax());
        }

        $allData = $request->all();
        $data = $allData['data'];
        $where = $allData['where'];
        $applicableForAllPlatforms = $allData['applicableForAllPlatforms'] ?? 0;

        $site_id = $where['site_id'];
        $microsite_id = $where['microsite_id'];
        $platform_id = $where['platform_id'];
        $category_id = $where['category_id'];
        $toBeSaved = [];
        $userId = auth()->user()->id;

        foreach ($data as $key => $val) {
            $hook_id = $val['hook_id'];
            $modules = $val['modules'];
            foreach ($modules as $module) {
                $toBeSaved[] = ['site_id' => $site_id, 'microsite_id' => $microsite_id,
                    'platform_id' => $platform_id, 'category_id' => $category_id,
                    'position' => $module['position'],
                    'hook_id' => $hook_id,
                    'module_id' => $module['module_id'],
                    'publish_status' => 1,
                    'insert_by' => $userId,
                    'update_by' => $userId,
                    'approved_by' => $userId,
                ];
            }
        }

        $isSaved = false;
        //delete old one
        try {
            DB::beginTransaction();
            $this->rawDelete($this->tableModuleSite, $where);
            $isSaved = $this->rawInsert($this->tableModuleSite, $toBeSaved);
        } catch (\Exception $exception) {
            info($exception->getMessage());
            DB::rollBack();

            return $rData['isSaved'] = $isSaved;
        }
        DB::commit();
        $rData['isSaved'] = $isSaved;

        return $rData;
    }

    /**
     * Removed Modules
     *
     * @return mixed
     */
    public function removeModules()
    {

        $request = request();

        if (! $this->checkPolicy('delete')) {
            return htcms_admin_view('common.error', Message::getDeleteError(), $request->ajax());
        }

        $allData = $request->all();
        $where = $allData['where'];
        $applicableForAllPlatforms = $allData['applicableForAllPlatforms'];

        //remove platform_id
        if ($applicableForAllPlatforms == true) {
            unset($where['platform_id']);
        }

        $rData['isSaved'] = $this->rawDelete($this->tableModuleSite, $where);

        return $rData;
    }

    /**
     * Copy data form one cateogry to another
     *
     * @return array|mixed
     */
    public function copyData()
    {

        $request = request();

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError(), $request->ajax());
        }

        $allData = $request->all();
        $fromData = $allData['fromData']; //{site_id:1, microsite_id:0, platform_id:1, category_id:1}
        $toData = $allData['toData'];
        if ($fromData['platform_id'] == 0) {
            $site = Site::with('platform')->find($toData['site_id']);
            $allPlatform = $site->platform;
            $data = ['success' => false];
            foreach ($allPlatform as $platform) {
                $fromData['platform_id'] = $platform->id;
                $toData['platform_id'] = $platform->id;
                $data = Module::copyData($fromData, $toData); //this will return only last
            }

            return $data;
        } else {
            return Module::copyData($fromData, $toData);
        }

    }
}
