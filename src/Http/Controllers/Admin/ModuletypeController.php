<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Http\Controllers\Admin\BaseAdminController;
use HashtagCms\Models\ModuleType;


class ModuletypeController extends BaseAdminController
{
    protected $dataFields = ['id','name','label','publish_status','updated_at'];

    protected $dataSource = ModuleType::class;

    protected $dataWith = [];

    protected $actionFields = ['edit', 'delete'];

    // No relational dropdowns needed

    // protected $minResults = 1; // Uncomment to disable delete when record count hits this threshold

    /*
    // Extra row-level action buttons (appear after edit/delete)
    protected $moreActionFields = [
        [
            'label'           => 'Show all info',
            'css'             => 'js_ajax',
            'icon_css'        => 'fa fa-info-circle',
            'hrefAttributes'  => ['data-info' => 'moduletype', 'data-editable' => false, 'data-excludefields' => []],
            'action'          => 'showinfo',
            'action_append_field' => 'id',
        ],
    ];
    */

    /*
    // Extra action bar buttons (appear in the Add/Search toolbar)
    protected $moreActionBarItems = [
        ['label' => 'Custom Action', 'as' => 'icon', 'icon_css' => 'fa fa-cogs', 'action' => 'moduletype/custom'],
    ];
    */

    /**
     * Save / Update a record.
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $data = $request->all();

        $rules = [
            'name' => ['required', 'string', 'max:60', Rule::unique('module_types')->whereNull('deleted_at')->ignore($data['id'] ?? null)],
            'label' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:60'],
            'description' => ['nullable', 'string'],
            'field_hint' => ['nullable', 'string', 'max:200'],
            'placeholder' => ['nullable', 'string', 'max:200'],
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $saveData = [
            'name' => $data['name'],
            'label' => $data['label'] ?? '',
            'icon' => $data['icon'] ?? '',
            'description' => $data['description'] ?? '',
            'field_hint' => $data['field_hint'] ?? '',
            'placeholder' => $data['placeholder'] ?? '',
            'publish_status' => $data['publish_status'] ?? 0,
            'updated_at' => htcms_get_current_date(),
        ];

        if ($data['actionPerformed'] !== 'edit') {
            $saveData['created_at'] = htcms_get_current_date();
        }

        $arrSaveData = ['model' => $this->dataSource, 'data' => $saveData];

        if ($data['actionPerformed'] == 'edit') {
            $where = $data['id'];
            $savedData = $this->saveData($arrSaveData, $where);
        } else {
            $savedData = $this->saveData($arrSaveData);
        }

        $viewData['id']       = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL']  = $data['backURL'];
        $viewData['isSaved']  = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }
}
