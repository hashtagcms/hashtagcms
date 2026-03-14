<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Module;
use HashtagCms\Models\ModuleType;
use HashtagCms\Models\Site;

class ModuleController extends BaseAdminController
{
    protected $dataFields = ['id', 'name', 'alias', 'view_name', 'data_type', 'updated_at'];

    protected $dataSource = Module::class;

    protected $bindDataWithAddEdit = ['sites' => ['dataSource' => Site::class, 'method' => 'all', 'params' => ['id', 'name']],
        'methodTypes' => ['dataSource' => Module::class, 'method' => 'getMethodTypes'],
        'moduleTypes' => ['dataSource' => ModuleType::class, 'method' => 'getActiveTypes'],
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
                    $query->where('site_id', $request->input('site_id'));
                })->ignore($request->input('id', 0), 'id'),
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
        $saveData['alias'] = strtoupper(str_replace(" ", "_", $data['alias']));
        $saveData['view_name'] = Str::kebab(strtolower($data['view_name']));
        $saveData['service_params'] = $data['service_params'];
        $saveData['data_type'] = str_replace(" ", "", $data['data_type']);
        $saveData['method_type'] = $data['method_type'];
        $saveData['is_mandatory'] = $data['is_mandatory'];

        $saveData['data_handler'] = $data['data_handler'];
        $saveData['data_key_map'] = $data['data_key_map'];
        $saveData['description'] = $data['description'];

        $saveData['cache_group'] = $data['cache_group'];
        $saveData['individual_cache'] = $data['individual_cache'];
        $saveData['shared'] = $data['shared'];
        $saveData['is_seo_module'] = $data['is_seo_module'];

        $saveData['headers'] = $data['headers'] ?? '';

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
        $id = $data['id'] ?? 0;
        $updateInAllSites = (int)$data['update_inAllSites'] === 1;
        $savedData = ['id' => $id, 'isSaved' => false];

        if ($updateInAllSites) {
            $sites = Site::select('id')->get();
            $canSyncAll = true;

            // Check if it's an edit and if the alias has changed
            if ($data['actionPerformed'] === 'edit') {
                $module = Module::find($id);
                if ($module && $module->alias !== $saveData['alias']) {
                    $canSyncAll = false;
                }
            }

            if ($canSyncAll) {
                $syncData = $saveData;
                unset($syncData['site_id']); // Don't sync site_id
                unset($syncData['created_at']); // Let updateOrCreate handle creation date if new
                
                foreach ($sites as $site) {
                    $module = Module::updateOrCreate(
                        ['site_id' => $site->id, 'alias' => $syncData['alias']],
                        $syncData
                    );
                    $savedData['id'] = $module->id;
                    $savedData['isSaved'] = true;
                }
            } else {
                // Alias changed, only update the current record
                $savedData = $this->saveData($arrSaveData, $id);
            }
        } else {
            // Normal single site save/create
            $where = ($data['actionPerformed'] === 'edit') ? $id : null;
            $savedData = $this->saveData($arrSaveData, $where);
        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return $viewData;

    }

    /**
     * Get module alias for auto suggest
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModuleAlias(Request $request)
    {
        if (! $this->checkPolicy('read')) {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        $q = $request->get('q');
        
        $results = Module::where('alias', 'LIKE', "%$q%")
            ->limit(10)
            ->get(['alias']);

        return response()->json($results);
    }
}
