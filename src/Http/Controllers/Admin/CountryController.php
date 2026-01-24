<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Country;
use HashtagCms\Models\Currency;
use HashtagCms\Models\Zone;

class CountryController extends BaseAdminController
{
    protected $dataFields = ['id', 'iso_code', 'lang.name', 'zone.name', 'updated_at'];

    protected $dataSource = Country::class;

    protected $dataWith = ['lang', 'zone'];

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    protected $bindDataWithAddEdit = ['zones' => ['dataSource' => Zone::class, 'method' => 'all'],
        'currencies' => ['dataSource' => Currency::class, 'method' => 'all']];

    //Save Data

    /**
     * @return mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = [
            'zone_id' => 'required|numeric',
            'iso_code' => 'required|max:255|string',
            'currency_id' => 'required|numeric',
            'call_prefix' => 'nullable|numeric',
            'contains_states' => 'nullable|integer',
            'need_identification_number' => 'nullable|integer',
            'need_zip_code' => 'nullable|integer',
            'zip_code_format' => 'nullable|max:12|string',
            'display_tax_label' => 'nullable|integer',
            'lang_name' => 'required|max:65|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $langData['name'] = $data['lang_name'];

        $saveData['iso_code'] = $data['iso_code'];
        $saveData['zone_id'] = $data['zone_id'];
        $saveData['call_prefix'] = $data['call_prefix'];
        $saveData['currency_id'] = $data['currency_id'];
        $saveData['contains_states'] = $data['contains_states'] ?? 0;
        $saveData['need_identification_number'] = $data['need_identification_number'] ?? 0;
        $saveData['need_zip_code'] = $data['need_zip_code'] ?? 0;
        $saveData['zip_code_format'] = $data['zip_code_format'];
        $saveData['display_tax_label'] = $data['display_tax_label'] ?? 00;

        //date
        $saveData['updated_at'] = htcms_get_current_date();
        $langData['updated_at'] = htcms_get_current_date();

        if ($data['actionPerformed'] !== 'edit') {
            $saveData['created_at'] = htcms_get_current_date();
            $langData['created_at'] = htcms_get_current_date();
        }

        $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];

        $arrLangData = ['data' => $langData];

        if ($data['actionPerformed'] == 'edit') {

            $where = $data['id'];

            //This is in base controller
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

        } else {
            //This is in base controller
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);
        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }
}
