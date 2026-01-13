<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Module;
use HashtagCms\Models\Site;

class ModuleController extends BaseAdminController
{
    protected $dataFields = ['id', 'name', 'alias', 'view_name', 'data_type', 'updated_at'];

    protected $dataSource = Module::class;

    protected $bindDataWithAddEdit = ['sites' => ['dataSource' => Site::class, 'method' => 'all', 'params' => ['id', 'name']],
        'methodTypes' => ['dataSource' => Module::class, 'method' => 'getMethodTypes'],
        'dataTypes' => ['dataSource' => Module::class, 'method' => 'getDataTypes'],
        'dataTypesInfo' => ['dataSource' => Module::class, 'method' => 'getDataTypesInfo'],
    ];

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }
        //
        $data = $request->all();

        $rules = [
            'site_id' => 'required|numeric',
            'name' => 'required|max:60|string',
            'alias' => [
                'required',
                'max:60',
                'string',
                Rule::unique('modules')->where(function ($query) use ($request) {
                    $query->where('site_id', $request->input('site_id'))
                        ->where('alias', $request->input('alias'));
                }),
            ],
            'linked_module' => 'nullable|max:60|string',
            'view_name' => 'required|max:200|string',
            'data_type' => 'required',
            'is_mandatory' => 'nullable|integer',
            'service_params' => 'nullable|max:255|string',
            'individual_cache' => 'nullable|integer',
            'cache_group' => 'nullable|max:100|string',
            'live_edit' => 'nullable|integer',
        ];

        if ($request->input('id') > 0) {
            $rules['alias'] = [
                'required',
                'max:60',
                'string',
                Rule::unique('modules')->where(function ($query) use ($request) {
                    $query->where('site_id', $request->input('site_id'))
                        ->where('alias', $request->input('alias'))
                        ->where('id', '<>', $request->input('id'));
                }),
            ];
        }

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

        $saveData['name'] = $data['name'];

        //$saveData['title'] = $data["title"];

        $saveData['alias'] = strtoupper($data['alias']);
        $saveData['view_name'] = Str::kebab(strtolower($data['view_name']));
        $saveData['service_params'] = $data['service_params'];
        $saveData['data_type'] = $data['data_type'];
        $saveData['method_type'] = $data['method_type'];
        $saveData['is_mandatory'] = $data['is_mandatory'];

        $saveData['data_handler'] = $data['data_handler'];
        $saveData['data_key_map'] = $data['data_key_map'];
        $saveData['description'] = $data['description'];

        $saveData['cache_group'] = $data['cache_group'];
        $saveData['individual_cache'] = $data['individual_cache'];
        $saveData['shared'] = $data['shared'];
        $saveData['is_seo_module'] = $data['is_seo_module'];

        $saveData['headers'] = $data['headers'] ?? ''; //added on 20 Jan 2022

        $saveData['query_statement'] = $data['query_statement'];
        $saveData['query_as'] = $data['query_as'];

        $saveData['linked_module'] = $data['linked_module'];
        $saveData['live_edit'] = $data['live_edit'];
        $saveData['site_id'] = $data['site_id'];

        if ($data['actionPerformed'] !== 'edit') {
            $saveData['created_at'] = htcms_get_current_date();
        }

        $saveData['updated_at'] = htcms_get_current_date();

        $arrSaveData = ['model' => $this->dataSource, 'data' => $saveData];

        //For getting all available sites
        $sites = Site::select('id')->get();

        $logic = '';
        if ($data['update_inAllSites'] == 1) {
            if ($data['actionPerformed'] == 'edit') {
                $Module = Module::find($data['id']);
                if ($Module['alias'] == $data['alias']) {
                    $logic = 'ALL_SITES_ENABLED_EDIT_ALL';
                } else {
                    $logic = 'ALL_SITES_ENABLED_EDIT_ONE';
                }
            } else {
                $logic = 'ALL_SITES_ENABLED_CREATE_ALL';
            }
        } else {
            if ($data['actionPerformed'] == 'edit') {
                $logic = 'EDIT';
            } else {
                $logic = 'CREATE';
            }
        }

        switch ($logic) {
            case 'ALL_SITES_ENABLED_EDIT_ALL':
                foreach ($sites as $site) {
                    $Module = Module::updateOrCreate(
                        ['site_id' => $site['id'], 'alias' => $saveData['alias']],
                        [
                            'name' => $saveData['name'],
                            'view_name' => $saveData['view_name'],
                            'data_type' => $saveData['data_type'],
                            'linked_module' => $saveData['linked_module'],
                            'query_statement' => $saveData['query_statement'],
                            'query_as' => $saveData['query_as'],
                            'data_handler' => $saveData['data_handler'],
                            'data_key_map' => $saveData['data_key_map'],
                            'description' => $saveData['description'],
                            'is_mandatory' => $saveData['is_mandatory'],
                            'method_type' => $saveData['method_type'],
                            'service_params' => $saveData['service_params'],
                            'individual_cache' => $saveData['individual_cache'],
                            'cache_group' => $saveData['cache_group'],
                            'live_edit' => $saveData['live_edit'],
                            'created_at' => htcms_get_current_date(),
                            'updated_at' => htcms_get_current_date(),
                        ]
                    );
                    $savedData['id'] = $Module['id'];
                    $savedData['isSaved'] = true;
                }
                break;
            case 'ALL_SITES_ENABLED_EDIT_ONE':
                $where = $data['id'];
                //This is in base controller
                $savedData = $this->saveData($arrSaveData, $where);
                break;
            case 'ALL_SITES_ENABLED_CREATE_ALL':
                foreach ($sites as $site) {
                    $Module = Module::create([
                        'site_id' => $site['id'],
                        'name' => $saveData['name'],
                        'alias' => $saveData['alias'],
                        'linked_module' => $saveData['linked_module'],
                        'view_name' => $saveData['view_name'],
                        'data_type' => $saveData['data_type'],
                        'query_statement' => $saveData['query_statement'],
                        'query_as' => $saveData['query_as'],
                        'data_handler' => $saveData['data_handler'],
                        'data_key_map' => $saveData['data_key_map'],
                        'description' => $saveData['description'],
                        'is_mandatory' => $saveData['is_mandatory'],
                        'method_type' => $saveData['method_type'],
                        'service_params' => $saveData['service_params'],
                        'individual_cache' => $saveData['individual_cache'],
                        'cache_group' => $saveData['cache_group'],
                        'live_edit' => $saveData['live_edit'],
                        'created_at' => htcms_get_current_date(),
                        'updated_at' => htcms_get_current_date(),
                    ]);
                    $savedData['id'] = $Module['id'];
                    $savedData['isSaved'] = true;
                }
                break;
            case 'EDIT':
                $where = $data['id'];
                //This is in base controller
                $savedData = $this->saveData($arrSaveData, $where);
                break;
            case 'CREATE':
                // This is in base controller
                $savedData = $this->saveData($arrSaveData);
                break;
            default:
                $viewData['isSaved'] = 0;
        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return $viewData;

    }
}
