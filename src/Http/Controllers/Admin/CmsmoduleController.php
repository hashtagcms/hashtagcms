<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\CmsModule;
use HashtagCms\Models\QueryLogger;
use HashtagCms\Core\Utils\RedisCacheManager;

class CmsmoduleController extends BaseAdminController
{
    protected $dataFields = ['id', 'name', 'sub_title', 'controller_name', 'updated_at'];

    protected $actionFields = ['edit', 'delete'];

    protected $moreActionBarItems = [
        [
            'label' => 'Sort Modules',
            'as' => 'icon',
            'icon_css' => 'fa fa-sort',
            'action' => 'cmsmodule/sort'
        ]
    ];

    protected $dataSource = CmsModule::class;

    protected $bindDataWithAddEdit = ['cmsModules' => ['dataSource' => CmsModule::class, 'method' => 'parentOnly']];

    /**
     * @return mixed
     */
    public function store(Request $request)
    {
        if (!$this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = [
            'name' => 'required|max:255',
            'controller_name' => ['required', 'max:255', Rule::unique('cms_modules')->whereNull('deleted_at')->ignore($request->input('id', 0))],
            'sub_title' => 'required|max:100',
            'icon_css' => 'max:255',
            'parent_id' => 'nullable|numeric',
            'position' => 'numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $saveData['name'] = $data['name'];
        $saveData['sub_title'] = $data['sub_title'];
        $saveData['controller_name'] = $data['controller_name'];

        $saveData['parent_id'] = ($data['parent_id'] == '') ? 0 : $data['parent_id'];

        $saveData['icon_css'] = $data['icon_css'];
        $saveData['list_view_name'] = $data['list_view_name'];
        $saveData['edit_view_name'] = $data['edit_view_name'];

        //date
        $saveData['updated_at'] = htcms_get_current_date();

        if ($data['actionPerformed'] !== 'edit') {
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
        $allModules = CmsModule::getAdminModules();

        $viewData['backURL'] = $this->getBackURL();
        $viewData['data'] = $allModules;
        $viewData['fields'] = ['id' => 'id', 'label' => 'name'];

        return htcms_admin_view('common.sorting', $viewData);
        //return $allModules;
    }

    /**
     * @return array
     */
    public function updateIndex()
    {
        if (!$this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError(), \request()->ajax());
        }

        $payload = request()->all();
        $datas = $payload['data'] ?? $payload;

        if (!is_array($datas)) {
            return ['isSaved' => 0, 'indexUpdated' => 0, 'error' => 'Invalid data format'];
        }

        $rows = [];
        foreach ($datas as $posData) {
            if (is_array($posData)) {
                $id = $posData['id'] ?? ($posData['where']['id'] ?? null);
                if ($id !== null) {
                    $rows[] = [
                        'id' => (int) $id,
                        'position' => (int) ($posData['position'] ?? 0),
                    ];
                }
            }
        }

        $table = (new $this->dataSource)->getTable();
        try {
            $affected = $this->bulkUpdateIndex($table, $rows);
            // Clear caches so the new order is reflected immediately
            RedisCacheManager::flush();
        } catch (\Exception $exception) {
            return ['isSaved' => false, 'error' => true, 'message' => $exception->getMessage()];
        }

        return ['isSaved' => true, 'indexUpdated' => $affected, 'affected' => $affected];
    }

    /**
     * @return mixed
     */
    //@override
    public function create()
    {

        $backURL = $this->getBackURL(false);
        $data['actionPerformed'] = 'add';
        $data['backURL'] = $backURL;
        $data['results']['name'] = 'Adding New...';

        $extraData = ['allTables' => ['dataSource' => CmsModule::class, 'method' => 'getAllTables']];

        $extra = $this->getExtraDataForEdit($extraData, true);

        $data = array_merge($data, $extra);

        return htcms_admin_view('cmsmodule.add', $data);
    }

    /********* Create Admin modules ***************/

    /**
     * Desc: Create Module
     *
     * @return array
     */
    public function createModule(Request $request)
    {

        if (!$this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = [
            'name' => 'required|max:255',
            'controller_name' => ['required', 'max:255', Rule::unique('cms_modules')->whereNull('deleted_at')->ignore($request->input('id', 0))],
            'sub_title' => 'required|max:100',
            'icon_css' => 'max:255',
            'parent_id' => 'nullable|numeric',
            'position' => 'numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            if ($request->ajax()) {
                $msg['errors'] = $validator->getMessageBag()->toArray();

                return response()->json($msg, 400);
            } else {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $data = $request->all();

        $controller_name = Str::studly($data['controller_name']);
        $validator_name = $data['validator_name'];

        $dataSource = $data['dataSource'];
        $dataSource = str_replace('::class', '', $dataSource);
        $dataSource = Str::studly(Str::singular($dataSource));

        $dataFields = (!empty($data['selectedFields'])) ? implode(',', $data['selectedFields']) : '*';
        $dataWith = (!empty($data['dataWith']) && count($data['relationModels']['models'] ?? []) > 0) ? implode(',', $data['dataWith']) : '[]';

        $createFiles = (isset($data['createFiles']) && $data['createFiles'] == false) ? false : true;


        try {

            if ($createFiles == true) {

                Artisan::call('cms:controller', [
                    'name' => $controller_name,
                    'dataSource' => $dataSource,
                    'dataWith' => $dataWith,
                    'dataFields' => $dataFields,
                ]);

                $relationModels = $data['relationModels']['models'];
                $methods = '';

                foreach ($relationModels as $key => $model) {

                    $current = $model;
                    $model_name = str_replace('::class', '', $current['model']);
                    $relationAlias = $current['relationAlias'];
                    $relationType = $current['relationType'];
                    // isLanguage flag (4th segment) — tells cms:model to add LangScope to the related model
                    $isLanguage = (!empty($current['isLanguage']) && $current['isLanguage']) ? '1' : '0';
                    $methods .= "$relationAlias,$relationType,$model_name,$isLanguage~";
                }

                //remove last tilt
                if ($methods != '') {
                    $methods = rtrim($methods, '~');
                }

                Artisan::call('cms:model', [
                    'name' => $dataSource,
                    'methods' => $methods,
                ]);

                // Scaffold FormRequest validator class from DB table schema
                Artisan::call('cms:validator', [
                    'name' => $dataSource,
                    'validatorName' => $controller_name . 'Request',
                ]);

            }

            //Save in DB
            $saveData['name'] = $data['name'];
            $saveData['sub_title'] = $data['sub_title'];
            $saveData['controller_name'] = $controller_name;
            $saveData['parent_id'] = $data['parent_id'];
            $saveData['icon_css'] = $data['icon_css'];

            $saveData['sub_title'] = $data['sub_title'];
            $saveData['controller_name'] = $data['controller_name'];

            $saveData['list_view_name'] = $data['list_view_name'];
            $saveData['edit_view_name'] = $data['edit_view_name'];

            $saveData['parent_id'] = ($data['parent_id'] == '') ? 0 : $data['parent_id'];

            $saveData['position'] = $this->dataSource::count() + 1;

            //info($saveData);

            $arrSaveData = ['model' => $this->dataSource, 'data' => $saveData];

            $created = $this->saveData($arrSaveData);

        } catch (\Exception $exception) {
            return ['created' => 0, 'message' => $exception->getMessage()];
        }

        return ['created' => $created];
    }

    /**
     * @return mixed
     */
    public function getFields()
    {

        $data = request()->all();
        $source = new $this->dataSource;

        return $source->getFieldsName($data['table']);
    }

    /**
     * @param  string  $name
     */
    public function isControllerExists($name = '')
    {

        $request = request()->all();

        echo $this->isExists($request['name'], true);

    }

    /**
     * @param  bool  $isController
     * @return bool
     */
    private function isExists($name, $isController = true)
    {

        //$namespace = app()->getNamespace();
        $namespace = config('hashtagcms.namespace');
        $namespace = (Str::endsWith($namespace, '\\')) ? substr($namespace, 0, strlen($namespace) - 1) : $namespace;

        if ($isController == true) {

            $file_name = $namespace . '/Http/Controllers/Admin/' . ucfirst($name) . 'Controller.php';

        } else {

            $file_name = $namespace . '/Models/' . ucfirst($name) . '.php';
        }

        return file_exists(base_path($file_name));

    }
}
