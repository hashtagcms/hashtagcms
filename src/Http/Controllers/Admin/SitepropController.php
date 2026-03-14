<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Platform;
use HashtagCms\Models\Site;
use HashtagCms\Models\SiteProp;

class SitepropController extends BaseAdminController
{
    protected $dataFields = ['id', 'name', 'value', 'platform.name', 'group_name', 'updated_at'];

    protected $dataSource = SiteProp::class;

    protected $dataWith = ['platform'];

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    protected $bindDataWithAddEdit = ['sites' => ['dataSource' => Site::class, 'method' => 'all'],
        'platforms' => ['dataSource' => Platform::class, 'method' => 'all']];

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {
        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $data        = $request->all();
        $isEdit      = $data['actionPerformed'] === 'edit';
        $groupName   = $data['group_name'] ?? '';   // coerce null → '' for consistent index behaviour
        $siteId      = $data['site_id'];
        $editId      = $data['id'] ?? null;

        // --- Basic required-field validation ---
        $basicValidator = Validator::make($data, [
            'name'       => ['required', 'string', 'max:100'],
            'value'      => ['required', 'string'],            
        ], [
            'name.required'       => 'The property key (name) cannot be blank.',
            'value.required'      => 'The property value cannot be blank.',
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

        // --- Uniqueness validation ---
        // On edit: validate the single platform_id against the composite key, ignoring current row.
        // On create: platform_id is an array — validate each one before the insert loop.
        $platformIds = is_array($data['platform_id']) ? $data['platform_id'] : [$data['platform_id']];

        foreach ($platformIds as $platformId) {
            $uniqueRule = Rule::unique('site_props')
                ->where('site_id',     $siteId)
                ->where('platform_id', $platformId)
                ->where('name',        $name)
                ->where('group_name',  $groupName);

            if ($isEdit && $editId) {
                $uniqueRule = $uniqueRule->ignore($editId);
            }

            $validator = Validator::make(
                ['name' => $name],
                ['name' => ['required', 'max:100', 'string', $uniqueRule]],
                ['name.unique' => "A property named \"{$name}\" already exists for this site / platform / group combination."]
            );

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // --- Save ---
        $saveData = [
            'name'       => $name,
            'value'      => $data['value'],
            'site_id'    => $siteId,
            'platform_id' => $data['platform_id'],
            'group_name' => $groupName,
            'is_public'  => $data['is_public'] ?? 0,
            'created_at' => htcms_get_current_date(),
            'updated_at' => htcms_get_current_date(),
        ];

        $arrSaveData = ['model' => $this->dataSource, 'data' => $saveData];

        if ($isEdit) {
            $savedData = $this->saveData($arrSaveData, $editId);
        } else {
            foreach ($platformIds as $platform) {
                $SiteProp            = SiteProp::create([
                    'site_id'     => $siteId,
                    'platform_id' => $platform,
                    'group_name'  => $groupName,
                    'name'        => $saveData['name'],
                    'value'       => $saveData['value'],
                    'is_public'   => $saveData['is_public'],
                    'created_at'  => htcms_get_current_date(),
                    'updated_at'  => htcms_get_current_date(),
                ]);
                $savedData['id']      = $SiteProp['id'];
                $savedData['isSaved'] = $SiteProp;
            }
        }

        $viewData['id']      = $savedData['id'];
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
        // Split on spaces, hyphens, underscores, or any non-alphanumeric boundary
        $words = preg_split('/[\s\-_]+/', trim($value));
        $first = strtolower(array_shift($words));
        $rest  = array_map('ucfirst', array_map('strtolower', $words));
        return $first . implode('', $rest);
    }

    public function getSiteGroup(Request $request)
    {
        $term = $request->input('q') ?? $request->input('term');
        $siteId = $request->input('site_id') ?? htcms_get_siteId_for_admin();

        $groups = SiteProp::searchSitePropGroup($siteId, $term);

        return response()->json($groups);
    }
}

