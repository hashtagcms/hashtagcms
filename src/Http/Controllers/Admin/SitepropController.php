<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        'platforms' => ['dataSource' => Platform::class, 'method' => 'all'],
        'siteGroups' => ['dataSource' => SiteProp::class, 'method' => 'getSiteGroup']];

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = [];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        //dd($data);

        $saveData['name'] = $data['name'];
        $saveData['value'] = $data['value'];
        $saveData['site_id'] = $data['site_id'];
        $saveData['platform_id'] = $data['platform_id'];
        $saveData['group_name'] = $data['group_name'];
        $saveData['created_at'] = htcms_get_current_date();
        $saveData['updated_at'] = htcms_get_current_date();
        $saveData['is_public'] = $data['is_public'] ?? 0;

        $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];

        if ($data['actionPerformed'] == 'edit') {

            $where = $data['id'];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData, $where);
            //$savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

        } else {
            foreach ($saveData['platform_id'] as $platform) {
                $SiteProp = SiteProp::create([
                    'site_id' => $saveData['site_id'],
                    'platform_id' => $platform,
                    'group_name' => $saveData['group_name'],
                    'name' => $saveData['name'],
                    'value' => $saveData['value'],
                    'is_public' => $saveData['is_public'],
                    'created_at' => htcms_get_current_date(),
                    'updated_at' => htcms_get_current_date(),
                ]);
                $savedData['id'] = $SiteProp['id'];
                $savedData['isSaved'] = $SiteProp;
            }
        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }
}
