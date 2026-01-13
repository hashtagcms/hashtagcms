<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Category;
use HashtagCms\Models\CategorySite;
use HashtagCms\Models\Platform;
use HashtagCms\Models\Site;
use HashtagCms\Models\Theme;
use Mockery\Exception;

class CategoryController extends BaseAdminController
{
    protected $dataFields = ['id', 'lang.name as name', 'link_rewrite', 'publish_status', 'read_count', 'updated_at'];

    protected $dataSource = Category::class;

    protected $dataWith = ['lang'];

    //protected $minResults = 2;

    protected $moreActionBarItems = [
        ['label' => 'Category Site Settings',
            'as' => 'icon',
            'icon_css' => 'fa fa-cogs', 'action' => 'category/settings',
        ],
    ];

    protected $bindDataWithListing = ['platforms' => ['dataSource' => Site::class, 'method' => 'getSupportedPlatforms']];

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    protected $bindDataWithAddEdit = ['themes' => ['dataSource' => Theme::class, 'method' => 'all'],
        'sites' => ['dataSource' => Site::class, 'method' => 'all'],
        'siteDefaults' => ['dataSource' => Site::class, 'method' => 'getDefaults'],
        'categories' => ['dataSource' => Category::class, 'method' => 'parentOnly'],
        'target_types' => ['dataSource' => Category::class, 'method' => 'getTargetType'],
        'relation_types' => ['dataSource' => Category::class, 'method' => 'getLinkRelationType'],
        'platforms' => ['dataSource' => Platform::class, 'method' => 'all'],
    ];

    /**
     * @Override
     *
     * @param  int  $id
     * @param  int  $platform_id
     * @return \Illuminate\Http\Response|void
     */
    public function edit($id = 0, $platform_id = 1)
    {

        return parent::edit($id, $platform_id);

    }

    /**
     * Save
     *
     * @return mixed
     */
    public function store(Request $request)
    {

        $module_name = $request->module_info->controller_name;

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = [
            'parent_id' => 'nullable|numeric',
            'site_id' => 'required|numeric',
            'is_site_default' => 'nullable|integer',
            'is_root_category' => 'nullable|integer',
            'is_new' => 'nullable|integer',
            'theme_id' => 'nullable|numeric',
            'icon' => 'nullable|file',
            'icon_css' => 'nullable|max:255|string',
            'position' => 'nullable|numeric',
            'has_wap' => 'nullable|integer',
            'wap_url' => 'nullable|max:255|string',
            'link_rewrite' => [
                'required',
                'max:255',
                'string',
                Rule::unique('categories')->where(function ($query) use ($request) {
                    $query->where('site_id', $request->input('site_id'))
                        ->where('link_rewrite', $request->input('link_rewrite'));
                }),
            ],
            'link_rewrite_pattern' => 'nullable|max:255|string',
            'link_navigation' => 'nullable|max:255|string',
            'exclude_in_listing' => 'nullable|integer',
            'cache_category' => 'nullable|max:100|string',
            'has_some_special_module' => 'nullable|integer',
            'special_module_alias' => 'nullable|max:255|string',
            'controller_name' => 'nullable|max:255|string',
            'insert_by' => 'required|numeric',
            'update_by' => 'required|numeric',
            'publish_status' => 'nullable|numeric',
            'lang_name' => 'required|max:128|string',
            'lang_title' => 'required|max:128|string',
            'lang_excerpt' => 'nullable|max:255|string',
            'lang_active_key' => 'nullable|max:128|string',
            'lang_third_party_mapping_key' => 'nullable|max:255|string',
            'lang_b2b_mapping' => 'nullable|max:255|string',
            'lang_is_external' => 'nullable|integer',
            'lang_meta_title' => 'nullable|max:160|string',
            'lang_meta_keywords' => 'nullable|max:255|string',
            'lang_meta_description' => 'nullable|max:255|string',
            'lang_meta_robots' => 'nullable|max:255|string',
            'lang_meta_canonical' => 'nullable|max:255|string',
        ];

        if ($request->input('id') > 0) {
            $rules['link_rewrite'] = [
                'required',
                'max:255',
                'string',
                Rule::unique('categories')->where(function ($query) use ($request) {
                    $query->where('site_id', $request->input('site_id'))
                        ->where('link_rewrite', $request->input('link_rewrite'))
                        ->where('id', '!=', $request->input('id'));
                }),
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        //
        $data = $request->all();

        $saveData['parent_id'] = $data['parent_id'];
        $saveData['site_id'] = $data['site_id'];
        $saveData['is_site_default'] = $data['is_site_default'] ?? 0;

        $saveData['is_root_category'] = $data['is_root_category'] ?? 0;

        //Disable all root category if current is 1
        if ($saveData['is_root_category'] == 1 && $data['actionPerformed'] == 'edit') {
            Category::where('id', '!=', $data['id'])->update(['is_root_category' => 0]);
        }

        $saveData['is_new'] = $data['is_new'] ?? 0;
        $saveData['has_wap'] = $data['has_wap'] ?? 0;
        $saveData['wap_url'] = $data['wap_url'];
        $saveData['link_rewrite'] = $data['link_rewrite'];
        $saveData['link_rewrite_pattern'] = $data['link_rewrite_pattern'];
        $saveData['link_navigation'] = $data['link_navigation'];
        $saveData['has_some_special_module'] = $data['has_some_special_module'] ?? 0;
        $saveData['special_module_alias'] = $data['special_module_alias'];
        $saveData['insert_by'] = Auth::user()->id;
        $saveData['update_by'] = Auth::user()->id;
        $saveData['required_login'] = $data['required_login'] ?? 0;
        $saveData['publish_status'] = $data['publish_status'] ?? 0;
        $saveData['controller_name'] = $data['controller_name'] ?? null;

        //Language
        $langData['lang_id'] = $data['lang_id'] ?? htcms_get_language_id_for_admin();
        $langData['name'] = $data['lang_name'];
        $langData['title'] = $data['lang_title'];
        $langData['excerpt'] = $data['lang_excerpt'];
        $langData['content'] = $data['lang_content'];
        $langData['active_key'] = $data['lang_active_key'];
        $langData['third_party_mapping_key'] = $data['lang_third_party_mapping_key'];
        $langData['b2b_mapping'] = $data['lang_b2b_mapping'];
        $langData['is_external'] = $data['lang_is_external'] ?? 0;
        $langData['link_relation'] = $data['lang_link_relation'];
        $langData['target'] = $data['lang_target'];
        $langData['meta_title'] = $data['lang_meta_title'];
        $langData['meta_keywords'] = $data['lang_meta_keywords'];
        $langData['meta_description'] = $data['lang_meta_description'];
        $langData['meta_robots'] = $data['lang_meta_robots'];
        $langData['meta_canonical'] = $data['lang_meta_canonical'];

        //Site Data
        $siteData['site_id'] = $data['site_id'];
        $siteData['platform_id'] = $data['platform_id'];
        $siteData['exclude_in_listing'] = $data['exclude_in_listing'] ?? 0;

        $siteData['theme_id'] = $data['theme_id'];

        //update Image
        $icon = $this->upload($module_name, request()->file('icon'));

        if ($icon != null) {

            $siteData['icon'] = $icon;
        }

        //it will have some value if user has clicked on delete
        if ($data['icon_deleted'] != '0') {
            $siteData['icon'] = '';
        }

        $siteData['icon_css'] = $data['icon_css'];
        $siteData['header_content'] = $data['header_content'];
        $siteData['footer_content'] = $data['footer_content'];
        $siteData['exclude_in_listing'] = $data['exclude_in_listing'] ?? 0;

        //update date
        $saveData['updated_at'] = htcms_get_current_date();
        $langData['updated_at'] = htcms_get_current_date();
        $siteData['updated_at'] = htcms_get_current_date();

        if ($data['actionPerformed'] !== 'edit') {
            //created at
            $saveData['created_at'] = htcms_get_current_date();
            $langData['created_at'] = htcms_get_current_date();
            $siteData['created_at'] = htcms_get_current_date();
        }

        //update position only if created
        if ($data['actionPerformed'] !== 'edit') {
            $siteData['position'] = $this->dataSource::count() + 1;
        }

        $siteData['cache_category'] = $data['cache_category'];

        $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];

        $arrLangData = ['data' => $langData];
        $arrSiteData = ['data' => $siteData, 'site_id' => $siteData['site_id']];

        //dd($arrLangData);
        if ($data['actionPerformed'] == 'edit') {

            $where = $data['id'];
            $saveData['update_by'] = Auth::user()->id;

            //dd($arrSaveData, $arrLangData, $arrSiteData);
            //This is in base controller
            $savedData = $this->saveDataWithLangAndPlatform($arrSaveData, $arrLangData, $arrSiteData, $where);

        } else {

            //This is in base controller
            $savedData = $this->saveDataWithLangAndPlatform($arrSaveData, $arrLangData, $arrSiteData);

        }

        //First or creating/updating with default category
        if ($this->dataSource::count() == 1 || $saveData['is_root_category'] == 1) {
            Site::where('id', '=', $data['site_id'])->update(['category_id' => $savedData['id']]);
        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }

    /**
     * Setting view
     *
     * @return mixed
     */
    public function settings()
    {

        if (! $this->checkPolicy('read')) {
            return htcms_admin_view('common.error', Message::getReadError());
        }

        $request = request()->all();
        $platform_id = $request['platform_id'] ?? 1;
        $site_id = $request['site_id'] ?? htcms_get_siteId_for_admin();
        $microsite_id = $request['microsite_id'] ?? 0;

        $sites = Site::with(['microsite:site_id,id,name',
            'platform:id,name',
            'theme:site_id,id,name',
            'category:site_id,category_id,name'])->find($site_id)->toArray();

        $categories = CategorySite::with('lang')->where([['site_id', '=', $site_id], ['platform_id', '=', $platform_id]])->get();

        $viewData['microsite_id'] = $microsite_id;
        $viewData['platform_id'] = $platform_id;
        $viewData['sitePlatforms'] = $sites['platform'];
        $viewData['siteMicrosites'] = $sites['microsite'];
        $viewData['siteThemes'] = $sites['theme'];
        $viewData['siteCategories'] = $sites['category'];
        $viewData['categories'] = $categories;
        $viewData['userRights'] = $this->getUserRights();

        return htcms_admin_view('category.settings', $viewData);
    }

    /**
     * update theme etc
     *
     * @return mixed
     */
    public function updateThemeAndEtc()
    {
        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError(), \request()->ajax());
        }
        $request = request()->all();
        $where = $request['where'];
        //@todo: unset microsite for now - will add in db
        unset($where['microsite_id']);
        $where['site_id'] = htcms_get_siteId_for_admin();
        $data = $request['data'];
        $rData['isSaved'] = $this->rawUpdate('category_site', $data, $where);

        return $rData;
    }

    /**
     * Insert category
     *
     * @return mixed
     */
    public function insertCategory()
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError(), \request()->ajax());
        }

        $request = request()->all();
        $data = $request['data'];
        $applicableForAllPlatforms = $request['applicableForAllPlatforms'];
        $inserted = [];
        if ($applicableForAllPlatforms) {
            $site = Site::with('platform')->find($data['site_id']);
            //loop
            $allPlatform = $site->platform;
            foreach ($allPlatform as $platform) {
                try {
                    $d = $data;
                    $d['platform_id'] = $platform->id;
                    $inserted[] = ['isSaved' => $this->rawInsert('category_site', $d), 'platform_id' => $platform->id];

                } catch (Exception $exception) {
                    $isSaved = false;
                }
            }
        } else {
            $inserted[] = ['isSaved' => $this->rawInsert('category_site', $data), 'platform_id' => $data['platform_id']];
        }

        return $inserted;
    }

    /**
     * Delete a category
     *
     * @return mixed
     */
    public function deleteCategory()
    {
        if (! $this->checkPolicy('delete')) {
            return htcms_admin_view('common.error', Message::getDeleteError(), \request()->ajax());
        }
        $request = request()->all();
        $where = $request['where'];

        // @todo: unset microsite for now - will add in db
        unset($where['microsite_id']);

        $rData['isSaved'] = $this->rawDelete('category_site', $where);

        return $rData;
    }

    /**
     * Update Index
     *
     * @return mixed
     */
    public function updateIndex()
    {
        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError(), \request()->ajax());
        }
        $request = request()->all();
        $datas = $request['data'];
        $updated = [];
        DB::beginTransaction();
        try {
            foreach ($datas as $key => $value) {
                $where = $value['where'];
                $where['site_id'] = htcms_get_siteId_for_admin();
                $data = $value['data'];
                $updated[] = $this->rawUpdate('category_site', $data, $where);
            }
        } catch (Exception $exception) {
            DB::rollBack();

            return ['error' => true, 'message' => $exception->getMessage()];
        }

        DB::commit();
        $rData['isSaved'] = $updated;

        return $rData;
    }

    /**
     * Update theme for all categories
     *
     * @return array|mixed
     */
    public function updateThemeForAllCategories()
    {
        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError(), \request()->ajax());
        }
        $request = request()->all();
        $data = $request['data'];
        $where = $request['where'];
        try {
            DB::beginTransaction();
            $updated = $this->rawUpdate('category_site', $data, $where);
        } catch (\Exception $exception) {
            DB::rollBack();

            return ['error' => true, 'message' => $exception->getMessage()];
        }
        DB::commit();

        return ['data' => $data, 'where' => $where, 'updated' => $updated];
    }
}
