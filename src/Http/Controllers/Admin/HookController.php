<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Hook;
use HashtagCms\Models\Site;

class HookController extends BaseAdminController
{
    protected $dataFields = ['id', 'name', 'alias', 'description', 'direction', 'created_at', 'updated_at'];

    protected $dataSource = Hook::class;

    protected $dataWith = '';

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    protected $bindDataWithAddEdit = ['sites' => ['dataSource' => Site::class, 'method' => 'all', 'params' => ['id', 'name']],
        'directions' => ['dataSource' => Hook::class, 'method' => 'getDirections']];

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = [
            'name' => 'required|max:64|string',
            'alias' => 'required|max:64|string|unique:hooks,alias',
        ];

        if ($request->input('id') > 0) {
            $rules['alias'] = 'required|max:64|string|unique:hooks,alias,'.$request->input('id');
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $saveData['name'] = $data['name'];
        $saveData['alias'] = strtoupper($data['alias']);
        $saveData['description'] = $data['description'];
        $saveData['direction'] = $data['direction'];

        //date
        $saveData['updated_at'] = htcms_get_current_date();
        if ($data['actionPerformed'] !== 'edit') {
            $saveData['created_at'] = htcms_get_current_date();
        }

        $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];

        if ($data['actionPerformed'] == 'edit') {

            $where = $data['id'];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData, $where);
            //$savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

        } else {
            //This is in base controller
            $savedData = $this->saveData($arrSaveData);
            //$savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);
        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }
}
