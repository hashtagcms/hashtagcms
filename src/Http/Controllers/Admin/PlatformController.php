<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Platform;

class PlatformController extends BaseAdminController
{
    //

    protected $dataFields = ['id', 'name', 'link_rewrite', 'updated_at'];

    protected $dataWith = ['site'];

    protected $dataSource = Platform::class;

    protected $minResults = 1;

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = [
            'name' => 'required|max:100|string',
            'link_rewrite' => ['required',
                'max:100',
                'string',
                "regex:/^\w{1,}$/",
                'unique:platforms,link_rewrite'],
        ];

        if ($request->input('id') > 0) {
            $rules['link_rewrite'] = ['required',
                'max:100',
                'string',
                "regex:/^\w{1,}$/",
                'unique:platforms,link_rewrite,'.$request->input('id')];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $saveData['name'] = $data['name'];
        $saveData['link_rewrite'] = strtolower($data['link_rewrite']);
        $saveData['updated_at'] = htcms_get_current_date();

        if ($data['actionPerformed'] !== 'edit') {
            $saveData['created_at'] = htcms_get_current_date();
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
