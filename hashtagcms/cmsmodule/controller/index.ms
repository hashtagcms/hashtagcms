<?php

namespace {{namespace}}Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Http\Controllers\Admin\BaseAdminController;
use {{namespace}}Models\{{dataSource}};
{{useRelatedModels}}

class {{controller_name}} extends BaseAdminController
{
    protected $dataFields = {{dataFields}};

    protected $dataSource = {{dataSource}}::class;

    protected $dataWith = {{dataWith}};

    protected $actionFields = ['edit', 'delete'];

    {{bindDataWithAddEdit}}

    // protected $minResults = 1; // Uncomment to disable delete when record count hits this threshold

    /*
    // Extra row-level action buttons (appear after edit/delete)
    protected $moreActionFields = [
        [
            'label'           => 'Show all info',
            'css'             => 'js_ajax',
            'icon_css'        => 'fa fa-info-circle',
            'hrefAttributes'  => ['data-info' => '{{dataSourceLower}}', 'data-editable' => false, 'data-excludefields' => []],
            'action'          => 'showinfo',
            'action_append_field' => 'id',
        ],
    ];
    */

    /*
    // Extra action bar buttons (appear in the Add/Search toolbar)
    protected $moreActionBarItems = [
        ['label' => 'Custom Action', 'as' => 'icon', 'icon_css' => 'fa fa-cogs', 'action' => '{{dataSourceLower}}/custom'],
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

        $rules = [{{validationFields}}];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        {{saveDataBlock}}

        $saveData['updated_at'] = htcms_get_current_date();
        if ($data['actionPerformed'] !== 'edit') {
            $saveData['created_at'] = htcms_get_current_date();
        }

        $arrSaveData = ['model' => $this->dataSource, 'data' => $saveData];

        {{langDataBlock}}

        if ($data['actionPerformed'] == 'edit') {
            $where = $data['id'];
            $savedData = {{saveMethod}};
        } else {
            $savedData = {{saveMethodInsert}};
        }

        $viewData['id']       = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL']  = $data['backURL'];
        $viewData['isSaved']  = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }
}
