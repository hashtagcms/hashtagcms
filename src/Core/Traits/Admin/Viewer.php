<?php

namespace HashtagCms\Core\Traits\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use HashtagCms\Models\CmsModule;
use HashtagCms\Models\CmsPermission;

trait Viewer
{
    /**
     * @param  bool  $checkPolicy
     * @return mixed
     */
    public function viewNow($viewName, $data, $checkPolicy = true)
    {

        if ($checkPolicy == true) {

            if (!$this->checkPolicy('read')) {

                return htcms_admin_view('common.error');

            }
        }

        return htcms_admin_view($viewName, $data);

    }

    /**
     * @param  null  $module_name
     * @return array
     */
    public function getModuleInfo($module_name = null)
    {

        //info("module_name: ".$module_name. " request module id ".request()->module_info->id);
        $id = ($module_name == null) ? (request()->module_info?->id ?? 0) : CmsModule::getInfoByName($module_name)->id;

        $isSuperAdmin = Auth::user()->isSuperAdmin();

        $permission = CmsPermission::has($id, Auth::user()->id);

        //info("permission ".json_encode($permission));
        //info("compact: ".json_encode(compact('isSuperAdmin', 'permission')));
        return compact('isSuperAdmin', 'permission');
    }

    /**
     * @param  $rights  - 'read'|'write' etc
     * @return bool
     */
    protected function checkPolicy($rights = '', $module = null)
    {

        //return false;

        $moduleInfo = $this->getModuleInfo($module);

        // info("moduleInfo: ". json_encode($moduleInfo));

        if (!$moduleInfo['isSuperAdmin']) {

            //handle special case. User has rights but readonly for a module
            switch ($rights) {
                case 'edit':
                    //User can edit but we want to give readonly on a module.
                    if ($this->isReadOnly($moduleInfo['permission']) == true) {
                        return false;
                    }
                    break;

            }

            //info("rights: $rights ".$moduleInfo['permission']." id: ".Auth::user()->id);
            //$this->authorize($rights);
            if (Gate::denies($rights, $moduleInfo['permission']) || $moduleInfo['permission'] == false) {
                return false;
            }

        }

        return true;

    }

    /**
     * check if it has readonly access for a module
     *
     * @return bool
     */
    private function isReadOnly($moduleInfo)
    {
        return ($moduleInfo == false) ? true : (($moduleInfo->readonly == 1) ? true : false);
    }
}
