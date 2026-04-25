<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Site;
use HashtagCms\Models\Theme;
use HashtagCms\Models\Hook;
use HashtagCms\Models\Module;

class ThemeController extends BaseAdminController
{
    protected $dataFields = [
        ['label' => 'ID', 'key' => 'id'],
        ['label' => 'Name', 'key' => 'name'],
        ['label' => 'Alias', 'key' => 'alias'],
        ['label' => 'Directory', 'key' => 'directory'],
    ];

    protected $actionFields = ['edit', 'delete'];

    protected $dataSource = Theme::class;

    protected $minResults = 1;

    protected $bindDataWithAddEdit = ['sites' => ['dataSource' => Site::class, 'method' => 'all']];

    //private $request;

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = [
            'site_id' => 'required|numeric',
            'name' => 'required|max:60|string',
            'alias' => ['required', 'max:60', 'string',
                Rule::unique('themes')->where(function ($query) use ($request) {
                    $query->where('site_id', $request->input('site_id'))
                        ->whereNull('deleted_at');
                })->ignore($request->input('id', 0), 'id')],
            'directory' => 'required|max:60|string',
            'body_class' => 'nullable|max:255|string',
            'img_preview' => 'nullable|file',
            'skeleton' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $module_name = request()->module_info->controller_name;

        $saveData['name'] = $data['name'];
        $saveData['alias'] = strtoupper($data['alias']);
        $saveData['site_id'] = $data['site_id'];
        $saveData['skeleton'] = $data['skeleton'];

        // Check for spaces in %{...}% tags
        preg_match_all('/%{(.*?)}%/', $data['skeleton'], $allMatches);
        $invalidTags = [];
        if (!empty($allMatches[0])) {
            foreach ($allMatches[0] as $tag) {
                if (str_contains($tag, ' ')) {
                    $invalidTags[] = $tag;
                }
            }
        }
        if (!empty($invalidTags)) {
            return redirect()->back()
                ->withErrors(['skeleton' => "The following tags contain spaces, which are not allowed: " . implode(', ', $invalidTags)])
                ->withInput();
        }
        //hooks validation
        preg_match_all('/%{cms\.hook\.(.*?)}%/', $data['skeleton'], $matches);
        if (!empty($matches[1])) {
            $hooks = array_unique($matches[1]);
            $existingHooks = Hook::whereIn('alias', $hooks)->pluck('alias')->toArray();
            $missingHooks = array_diff($hooks, $existingHooks);
            if (!empty($missingHooks)) {
                return redirect()->back()
                    ->withErrors(['skeleton' => "The following hooks are not available: " . implode(', ', $missingHooks)])
                    ->withInput();
            }
        }
        //module validation
        preg_match_all('/%{cms\.module\.(.*?)}%/', $data['skeleton'], $moduleMatches);
        if (!empty($moduleMatches[1])) {
            $modules = array_unique($moduleMatches[1]);
            $existingModules = Module::withoutGlobalScopes()
                ->whereIn('alias', $modules)
                ->where('site_id', $data['site_id'])
                ->pluck('alias')->toArray();
            $missingModules = array_diff($modules, $existingModules);
            if (!empty($missingModules)) {
                return redirect()->back()
                    ->withErrors(['skeleton' => "The following modules are not available for this site: " . implode(', ', $missingModules)])
                    ->withInput();
            }
        }
        $saveData['directory'] = Str::kebab(strtolower($data['directory']));
        $saveData['body_class'] = $data['body_class'];

        $saveData['header_content'] = $data['header_content'];
        $saveData['footer_content'] = $data['footer_content'];

        //update Image
        $img_preview = $this->upload($module_name, request()->file('img_preview'));

        if ($data['img_preview_deleted'] != '0') {
            $saveData['img_preview'] = '';
        }

        if ($img_preview != null) {
            $saveData['img_preview'] = $img_preview;
        }

        $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];

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
}
