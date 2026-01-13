<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Gallery;
use HashtagCms\Models\QueryLogger;

class GalleryController extends BaseAdminController
{
    protected $dataFields = [
        'id',
        ['label' => 'image', 'key' => 'path', 'isImage' => true],
        'media_type',
        'group_name',
        ['label' => 'Tags', 'key' => 'tag.name', 'showAllScopes' => true],
        'media_key',
    ];

    protected $dataSource = Gallery::class;

    protected $dataWith = ['tag'];

    protected $actionFields = ['edit', 'delete'];

    protected $moreActionBarItems = [['label' => 'Sort Modules',
        'as' => 'icon', 'icon_css' => 'fa fa-sort',
        'action' => 'gallery/sort']];

    protected $bindDataWithAddEdit = ['typeGroups' => ['dataSource' => Gallery::class, 'method' => 'getTypeGroup'],
        'imageGroups' => ['dataSource' => Gallery::class, 'method' => 'getImageGroup']];

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }
        $data = $request->all();

        $rules = [
            'site_id' => 'required',
            'media_type' => 'required|max:50|string',
            'tags' => 'required|string',
            'group_name' => 'nullable|max:50|string',
            'media_key' => 'nullable|max:50|string',
        ];
        $module_name = request()->module_info->controller_name;

        //edit
        if (isset($data['id']) && $data['id'] > 0) {
            //$rules["image"] = "max:255|string";
            $filesRequired = false;
        } else {
            $rules['image'] = 'required';
            $filesRequired = count(request()->allFiles()) === 0;
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails() || $filesRequired) {
            if ($filesRequired === true) {
                $validator->errors()->add('image[]', 'Please choose at least one file.');
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $saveData['group_name'] = $data['group_name'] ?? null;

        if ($saveData['group_name']) {
            $saveData['group_name'] = strtolower($saveData['group_name']);
        }

        $saveData['site_id'] = $data['site_id'];
        $saveData['media_type'] = strtolower($data['media_type']);
        $saveData['updated_at'] = htcms_get_current_date();

        $tags_array = explode(',', $data['tags']);

        //Edit
        if ($data['id'] > 0) {
            if (request()->file('image') != null) {
                $saveData['path'] = $this->upload($module_name, request()->file('image'));
            }
            $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];

            $where = $data['id'];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData, $where);

            //Tags
            (new $this->dataSource)->saveTags($data['id'], $tags_array);

        } else {
            //Add
            $allFiles = request()->allFiles()['image'];

            for ($count = 0; $count < count($allFiles); $count++) {
                $saveData['path'] = $this->upload($module_name, $allFiles[$count]);
                $saveData['created_at'] = htcms_get_current_date();
                $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];
                //This is in base controller
                $savedData = $this->saveData($arrSaveData);
                if (count($tags_array)) {
                    (new $this->dataSource)->saveTags($savedData['id'], $tags_array);
                }
            }
        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }

    /**
     * Get all images
     *
     * @return mixed
     */
    public function getAllImages()
    {
        return $this->dataSource::orderBy('id', 'desc')->with($this->dataWith)->where('media_type', 'image')->get();
    }

    /**
     * Search images
     *
     * @return mixed
     */
    public function searchImages($tag)
    {
        return $this->dataSource::with($this->dataWith)->whereHas('tag', function ($q) use ($tag) {
            $q->where('name', 'like', '%'.$tag.'%');
        })->get();
    }

    /**
     * Upload files
     *
     * @return array
     */
    public function uploadFiles(Request $request)
    {
        $data = $request->all();
        $module_name = request()->module_info->controller_name;

        $allFiles = request()->allFiles();
        $allFiles = $allFiles['images'];
        $saveData['group_name'] = $data['groupName'] ?? null;

        if ($saveData['group_name']) {
            $saveData['group_name'] = strtolower($saveData['group_name']);
        }
        $saveData['site_id'] = htcms_get_site_id();
        $saveData['media_type'] = $data['mediaType'];

        if ($saveData['media_type']) {
            $saveData['media_type'] = strtolower($saveData['media_type']);
        }

        $saveData['updated_at'] = htcms_get_current_date();
        $saveData['created_at'] = htcms_get_current_date();

        $ids = [];

        for ($count = 0; $count < count($allFiles); $count++) {
            $saveData['path'] = $this->upload($module_name, $allFiles[$count]);
            $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];
            $tags_array = explode(',', $data['tags']);
            //This is in base controller
            $savedData = $this->saveData($arrSaveData);
            if (count($tags_array)) {
                (new $this->dataSource)->saveTags($savedData['id'], $tags_array);
            }
            $ids[] = $savedData['id'];
        }

        return ['status' => true, 'message' => 'Images uploaded successfully', 'data' => $this->dataSource::find($ids)];
    }

    /**
     * Sort Modules
     *
     * @param  null  $allModules
     * @return mixed
     */
    public function sort($media_type = null, $group_name = null)
    {

        $data = Gallery::getMedias($media_type, $group_name);
        $viewData['backURL'] = $this->getBackURL();
        $viewData['data'] = $data;
        $viewData['imageGroups'] = $this->dataSource::getImageGroup();
        $viewData['typeGroups'] = $this->dataSource::getTypeGroup();

        $viewData['mediaType'] = $media_type;
        $viewData['groupName'] = $group_name;

        $viewData['fields'] = ['id' => 'id', 'label' => 'path', 'isImage' => true];

        return htcms_admin_view('gallery.sorting', $viewData);
    }

    /**
     * Update Index
     *
     * @return array
     */
    public function updateIndex()
    {

        $a = [];
        $data = request()->all();
        foreach ($data as $key => $posData) {
            if ($posData != null) {
                $where = $posData['where']['id'];
                $saveData['position'] = $posData['position'];
                $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];
                QueryLogger::setLogginStatus(false);
                $savedData = $this->saveData($arrSaveData, $where);
                array_push($a, $posData);
                QueryLogger::setLogginStatus(true);
            }
        }

        return ['indexUpdated' => $a];
    }
}
