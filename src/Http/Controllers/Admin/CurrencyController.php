<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Currency;

class CurrencyController extends BaseAdminController
{
    protected $dataFields = ['id', 'name', 'iso_code', 'iso_code_num',
        'sign', 'format', 'conversion_rate', 'updated_at'];

    protected $dataSource = Currency::class;

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
            'name' => 'required|max:32|string',
            'iso_code' => 'required|max:3|string',
            'iso_code_num' => 'required|max:3|string',
            'sign' => 'required|max:255|string',
            'blank' => 'nullable|integer',
            'format' => 'nullable|integer',
            'decimals' => 'nullable|integer',
            'conversion_rate' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $saveData['name'] = $data['name'];

        $saveData['iso_code'] = $data['iso_code'];
        $saveData['iso_code_num'] = $data['iso_code_num'];
        $saveData['sign'] = $data['sign'];
        $saveData['blank'] = $data['blank'] ?? 0;
        $saveData['format'] = $data['format'] ?? 0;
        $saveData['decimals'] = $data['decimals'] ?? 0;
        $saveData['conversion_rate'] = $data['conversion_rate'] ?? 1.00000;

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
