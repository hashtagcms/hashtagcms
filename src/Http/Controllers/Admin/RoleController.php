<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Permission;
use HashtagCms\Models\Role;

class RoleController extends BaseAdminController
{
    protected $dataFields = ['id', 'name', 'label', 'updated_at'];

    protected $dataSource = Role::class;

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    protected $bindDataWithAddEdit = ['allPermissions' => ['dataSource' => Permission::class, 'method' => 'all']];

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = [
            'name' => 'required|max:255|string',
            'label' => 'nullable|max:255|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $saveData['name'] = $data['name'];
        $saveData['label'] = $data['label'];

        $permissions = $data['permissions'];

        $updatePermission = $data['updatePermission'];

        $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];

        if ($data['actionPerformed'] == 'edit') {

            $where = $data['id'];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData, $where);

            $id = $savedData['id'];

        } else {
            //This is in base controller
            $savedData = $this->saveData($arrSaveData);
            $id = $savedData['id'];
            $updatePermission = true;
        }

        //Insert/Update Permission
        if (! empty($permissions) && $updatePermission == true) {
            $role = Role::find($id);
            $role->detachAllPermissions(); //remove old roles

            //Get Roles
            $allPermissions = Permission::find($permissions);
            $role->giveAllPermissionTo($allPermissions); //Assign new roles

        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }
}
