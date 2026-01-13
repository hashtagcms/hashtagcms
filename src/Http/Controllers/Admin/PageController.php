<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Core\Traits\Admin\BlogPageCommon;
use HashtagCms\Models\Category;
use HashtagCms\Models\Page;
use HashtagCms\Models\Platform;
use HashtagCms\Models\User as UserData;

class PageController extends BaseAdminController
{
    use BlogPageCommon;

    protected $dataFields = ['id', 'lang.title as title', 'lang.name as name', 'category.link_rewrite as category', 'platform.name as platform', 'link_rewrite', 'read_count', 'publish_status', 'updated_at'];

    protected $dataSource = Page::class;

    protected $dataWith = ['lang', 'category', 'platform'];

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    protected $dataWhere = [['field' => 'content_type', 'operator' => '=', 'value' => 'page']];

    protected $users;

    protected $bindDataWithAddEdit = ['platforms' => ['dataSource' => Platform::class, 'method' => 'all'],
        'targetTypes' => ['dataSource' => Category::class, 'method' => 'getTargetType'],
        'relationTypes' => ['dataSource' => Category::class, 'method' => 'getLinkRelationType'],
        'contentCategories' => ['dataSource' => Category::class, 'method' => 'parentOnlyDynamic'],
        'contentTypes' => ['dataSource' => Page::class, 'method' => 'getContentTypes'],
        'menuPlacements' => ['dataSource' => Page::class, 'method' => 'getMenuPlacements'],
        'authors' => ['dataSource' => UserData::class, 'method' => 'all'],
        'defaultContentType' => 'page',
    ];

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = $this->getRulesArray($request);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $module_name = request()->module_info->controller_name;

        $viewData = $this->saveBlogPageData($data, $module_name);

        return htcms_admin_view('common.saveinfo', $viewData);
    }

    /**
     * @return mixed
     */
    public function getParentCategory(Request $request)
    {

        $data = $request->all();
        $content_type = $data['content_type'];
        $category_id = $data['category_id'];

        $where = [['content_type', '=', $content_type],
            ['category_id', '=', $category_id],
            ['site_id', '=', htcms_get_siteId_for_admin()],
        ];

        return Page::where($where)->with('lang')->get();

    }
}
