<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Festival;
use HashtagCms\Models\QueryLogger;
use HashtagCms\Models\Site;

class FestivalController extends BaseAdminController
{
    protected $dataFields = ['id',
        'name', ['label' => 'Image', 'key' => 'image', 'isImage' => true],
        'body_css', 'start_date', 'end_date', 'publish_status', 'updated_at'];

    protected $dataSource = Festival::class;

    protected $dataWith = '';

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    // protected $minResults = 1; //this will disable delete when record count is one
    /*
    //This will be added after add/edit etc action fields
    protected $moreActionFields = array(
            array("label"=>"Show all info",
                "css"=>"js_ajax",
                "icon_css"=>"fa fa-info-circle",
                "hrefAttributes"=>["data-info"=>"cmslog", "data-editable"=>false, "data-excludefields"=>["user", "module"]],
                "action"=>"showinfo",
                "action_append_field"=>"id"
                ),
                 array("label"=>"Site Settings",
                            "icon_css"=>"fa fa-cogs",
                            "action"=>"settings",
                            "action_append_field"=>"id")
        );
    */

    //This is action bar items. (Add/Search bar)
    protected $moreActionBarItems = [
        ['label' => 'Sort',
            'as' => 'icon',
            'icon_css' => 'fa fa-sort', 'action' => 'festival/sort',
        ],
    ];

    //Get data for editing.
    /*protected $bindDataWithAddEdit = array("zones"=>array("dataSource"=>Zone::class, "method"=>"all"),
                                        "currencies"=>array("dataSource"=>Currency::class, "method"=>"all"));
                                        */

    public function store(Request $request)
    {

        $module_name = $request->module_info->controller_name;

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }
        $rules = [
            'site_id' => 'required',
            'name' => 'required',
            'image' => 'nullable|file',
            'body_css' => 'nullable|max:255|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        $data = $request->all();
        $msg = '';

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        //dd(request()->file("lottie"),  request()->file("image"));

        $image = $this->upload($module_name, request()->file('image'));

        if ($image != null) {
            $saveData['image'] = $image;
        }

        $lottie = $this->upload($module_name, request()->file('lottie'), null, true);

        if ($lottie != null) {
            $saveData['lottie'] = $lottie;
        }

        if ($data['lottie_deleted'] != '0') {
            $saveData['lottie'] = '';
        }
        if ($data['image_deleted'] != '0') {
            $saveData['image'] = '';
        }

        $saveData['body_css'] = $data['body_css'];
        $saveData['start_date'] = $data['start_date'];
        $saveData['end_date'] = $data['end_date'];
        $saveData['publish_status'] = $data['publish_status'] ?? 0;
        $saveData['site_id'] = $data['site_id'];

        $saveData['name'] = $data['name'];
        $saveData['extra'] = $data['extra'];
        $saveData['width'] = $data['width'];
        $saveData['height'] = $data['height'];
        $saveData['background'] = $data['background'];
        $saveData['hide_on_complete'] = $data['hide_on_complete'] ?? 0;
        $saveData['top'] = $data['top'];
        $saveData['left'] = $data['left'];
        $saveData['z_index'] = $data['z_index'] ?? 9999999;
        $saveData['play_mode'] = $data['play_mode'];
        $saveData['direction'] = $data['direction'] ?? 1;
        $saveData['autoplay'] = $data['autoplay'] ?? 0;
        $saveData['loop'] = $data['loop'] ?? 0;
        $saveData['hover'] = $data['hover'] ?? 0;
        $saveData['controls'] = $data['controls'] ?? 0;

        $saveData['updated_at'] = htcms_get_current_date();

        if ($data['actionPerformed'] !== 'edit') {
            $saveData['position'] = $this->dataSource::count() + 1;
            $saveData['created_at'] = htcms_get_current_date();
        }

        $arrSaveData = ['model' => $this->dataSource, 'data' => $saveData];

        if ($data['actionPerformed'] == 'edit') {

            $where = $data['id'];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData, $where);

        } else {
            //This is in base controller
            $savedData = $this->saveData($arrSaveData);
        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }

    /**
     * Sort Modules
     *
     * @param  null  $allModules
     * @return mixed
     */
    public function sort()
    {
        $allData = Festival::getAllFestivals();

        $viewData['backURL'] = $this->getBackURL();
        $viewData['data'] = $allData;
        $viewData['fields'] = ['id' => 'id', 'label' => 'name'];

        return htcms_admin_view('common.sorting', $viewData);
        //return $allModules;
    }

    /**
     * Update index
     *
     * @return array
     */
    public function updateIndex()
    {
        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError(), \request()->ajax());
        }

        $payload = request()->all();
        $datas   = $payload['data'] ?? $payload;

        if (!is_array($datas)) {
            return ['isSaved' => 0, 'indexUpdated' => 0, 'error' => 'Invalid data format'];
        }

        $rows = [];
        foreach ($datas as $posData) {
            if (is_array($posData)) {
                $id = $posData['id'] ?? ($posData['where']['id'] ?? null);
                if ($id !== null) {
                    $rows[] = [
                        'id'       => (int) $id,
                        'position' => (int) ($posData['position'] ?? 0),
                    ];
                }
            }
        }

        $table    = (new $this->dataSource)->getTable();
        try {
            $affected = $this->bulkUpdateIndex($table, $rows);
            Site::clearConfigCache();
        } catch (\Exception $exception) {
            return ['isSaved' => false, 'error' => true, 'message' => $exception->getMessage()];
        }

        return ['isSaved' => true, 'indexUpdated' => $affected, 'affected' => $affected];
    }
}
