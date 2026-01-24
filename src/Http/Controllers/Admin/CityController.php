<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\City;
use HashtagCms\Models\Country;
use HashtagCms\Models\Zone;

class CityController extends BaseAdminController
{
    protected $dataFields = [
        'id',
        'name',
        'country.name',
        'zone.name as Zone',
        'airport_name',
        'airport_code',
        'latitude',
        'longitude',
    ];

    protected $dataSource = City::class;

    protected $dataWith = ['country', 'zone'];

    protected $actionFields = ['edit', 'delete'];

    protected $bindDataWithAddEdit = ['zones' => ['dataSource' => Zone::class, 'method' => 'all'],
        'countries' => ['dataSource' => Country::class, 'method' => 'combo', 'params' => ['id', 'iso_code']]];

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = [
            'country_id' => 'required|numeric',
            'zone_id' => 'numeric',
            'name' => 'required|max:100|string',
            'iso_code' => 'nullable|max:7|string',
            'tax_behavior' => 'nullable|integer',
            'airport_name' => 'nullable|max:256|string',
            'airport_code' => 'nullable|max:20|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
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
        $saveData['airport_code'] = $data['airport_code'];
        $saveData['airport_name'] = $data['airport_name'];
        $saveData['country_id'] = $data['country_id'];
        $saveData['tax_behavior'] = (isset($data['tax_behavior'])) ? $data['tax_behavior'] : 0;
        $saveData['latitude'] = $data['latitude'];
        $saveData['longitude'] = $data['longitude'];

        $country = Country::with('zone')->find($saveData['country_id']);

        $saveData['zone_id'] = $country->zone->id;

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
