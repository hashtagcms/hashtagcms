<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Module;
use HashtagCms\Models\ModuleProp;
use HashtagCms\Models\Platform;
use HashtagCms\Models\QueryLogger;

class ModulepropertyController extends BaseAdminController
{
    protected $dataFields = ['id', 'name', 'lang.value as value', 'group', 'module.alias', 'platform.name', 'updated_at'];

    protected $dataSource = ModuleProp::class;

    protected $dataWith = ['lang', 'module', 'platform'];

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    protected $bindDataWithAddEdit = [
        'modules' => ['dataSource' => Module::class, 'method' => 'all', 'params' => ['id', 'alias']],
        'platforms' => ['dataSource' => Platform::class, 'method' => 'all', 'params' => ['id', 'name']],
    ];

    public function store(Request $request)
    {
        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $data      = $request->all();
        $isEdit    = $data['actionPerformed'] === 'edit';
        $siteId    = $data['site_id'];
        $editId    = $data['id'] ?? null;
        $group     = $data['group'] ?? '';  // coerce null → '' for uniqueness index

        // --- Basic required-field validation ---
        $basicValidator = Validator::make($data, [
            'module_id'   => ['required'],
            'platform_id' => ['required'],
            'name'        => ['required', 'string', 'max:100'],
            'value'       => ['required', 'string', 'max:500'],
            'group'       => ['nullable', 'string', 'max:100'],
        ], [
            'name.required'  => 'The property key (name) cannot be blank.',
            'value.required' => 'The property value cannot be blank.',
        ]);

        if ($basicValidator->fails()) {
            return redirect()->back()
                ->withErrors($basicValidator)
                ->withInput();
        }

        // --- camelCase conversion ---
        if (! empty($data['convert_camelcase'])) {
            $data['name'] = $this->toCamelCase($data['name']);
        }
        $name = $data['name'];  // local alias — may be camelCased at this point

        // --- Uniqueness validation across every module × platform combination ---
        $allModules   = is_array($data['module_id'])   ? $data['module_id']   : [$data['module_id']];
        $allPlatforms = is_array($data['platform_id']) ? $data['platform_id'] : [$data['platform_id']];

        foreach ($allModules as $moduleId) {
            foreach ($allPlatforms as $platformId) {
                $uniqueRule = Rule::unique('module_props')
                    ->where('module_id',   $moduleId)
                    ->where('site_id',     $siteId)
                    ->where('platform_id', $platformId)
                    ->where('name',        $name)
                    ->where('group',       $group)
                    ->whereNull('deleted_at');

                if ($isEdit && $editId) {
                    $uniqueRule = $uniqueRule->ignore($editId);
                }

                $validator = Validator::make(
                    ['name' => $name],
                    ['name' => ['required', 'max:100', 'string', $uniqueRule]],
                    ['name.unique' => "A property named \"{$name}\" already exists for this module / site / platform / group combination."]
                );

                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }
            }
        }

        // --- Save ---
        $updateInAllLanguages = (isset($data['update_in_all_language']) && (string) $data['update_in_all_language'] === '1');

        $saveData = [
            'group'      => $group,
            'name'       => $name,
            'site_id'    => $siteId,
            'updated_at' => htcms_get_current_date(),
        ];

        $langData    = ['value' => $data['value'], 'updated_at' => htcms_get_current_date()];
        $arrLangData = ['data' => $langData];

        QueryLogger::startBuffering();

        if ($isEdit) {
            $saveData['platform_id'] = $data['platform_id'];
            $saveData['module_id']   = $data['module_id'];

            $arrSaveData = ['model' => $this->dataSource, 'data' => $saveData];
            $savedData   = $this->saveDataWithLang($arrSaveData, $arrLangData, $editId, $updateInAllLanguages);
        } else {
            $saveData['created_at'] = htcms_get_current_date();

            foreach ($allPlatforms as $current_platform_id) {
                $saveData['platform_id'] = $current_platform_id;
                foreach ($allModules as $current_module_id) {
                    $saveData['module_id'] = $current_module_id;
                    $arrSaveData = ['model' => $this->dataSource, 'data' => $saveData];
                    $savedData   = $this->saveDataWithLang($arrSaveData, $arrLangData);
                }
            }
        }

        QueryLogger::commitLogs();

        $viewData['id']       = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL']  = $data['backURL'];
        $viewData['isSaved']  = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }

    /**
     * Convert an arbitrary string to camelCase.
     * e.g. "my-key_name" → "myKeyName", "My Key" → "myKey"
     */
    private function toCamelCase(string $value): string
    {
        // If it's already camelCase (has both uppercase and lowercase, and no delimiters)
        if (preg_match('/[A-Z]/', $value) && preg_match('/[a-z]/', $value) && ! preg_match('/[\s\-_]/', $value)) {
            return lcfirst($value);
        }

        $words = preg_split('/[\s\-_]+/', trim($value));
        $first = strtolower(array_shift($words));
        $rest = array_map('ucfirst', array_map('strtolower', $words));

        return $first . implode('', $rest);
    }

    public function getModuleGroup(Request $request)
    {        
        $term   = $request->input('q') ?? $request->input('term');
        $siteId = $request->input('site_id') ?? htcms_get_siteId_for_admin();

        $groups = ModuleProp::searchModuleGroup($siteId, $term);

        return response()->json($groups);
    }
}
