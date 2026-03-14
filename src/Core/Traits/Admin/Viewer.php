<?php

namespace HashtagCms\Core\Traits\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use HashtagCms\Models\CmsModule;
use HashtagCms\Models\CmsPermission;
use HashtagCms\Core\Helpers\Message;
use Illuminate\Support\Facades\Log;

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

                return htcms_admin_view('common.error', Message::getReadError(['backUrl' => $this->getBackURL()]));

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
        $user = Auth::user();
        $isSuperAdmin = $user->isAdmin();

        // Tier 3: Request-Level Cache Optimization
        // If the middleware already pre-fetched and attached the permission, use it.
        $requestModule = request()->module_info;
        if ($module_name === null && isset($requestModule->permission)) {
            $permission = $requestModule->permission;
            Log::info("[Viewer:getModuleInfo] Using pre-fetched permission from middleware cache.", ['module' => $requestModule->controller_name]);
        } else {
            $id = ($module_name == null) ? ($requestModule?->id ?? 0) : CmsModule::getInfoByName($module_name)?->id ?? 0;
            $permission = $isSuperAdmin ? true : CmsPermission::has($id, $user->id);
            Log::info("[Viewer:getModuleInfo] Re-fetching/checking permission.", [
                'module_id' => $id,
                'module_name' => $module_name,
                'is_super_admin' => $isSuperAdmin,
                'has_permission' => ($permission !== false)
            ]);
        }

        return compact('isSuperAdmin', 'permission');
    }

    /**
     * @param  $rights  - 'read'|'write' etc
     * @return bool
     */
    protected function checkPolicy($rights = '', $resource = null, $module = null)
    {

        //return false;

        $moduleInfo = $this->getModuleInfo($module);

        Log::info("[Viewer:checkPolicy] Checking rights.", [
            'rights' => $rights,
            'is_super_admin' => $moduleInfo['isSuperAdmin']
        ]);

        if (!$moduleInfo['isSuperAdmin']) {
            //handle special case. User has rights but readonly for a module
            switch ($rights) {
                case 'edit':
                    //User can edit but we want to give readonly on a module.
                    if ($this->isReadOnly($moduleInfo['permission']) == true) {
                        Log::warning("[Viewer:checkPolicy] Denied: Module is readonly.", ['rights' => $rights]);
                        return false;
                    }
                    break;
            }

            if (Gate::denies($rights, [$moduleInfo['permission'], $resource]) || $moduleInfo['permission'] == false) {
                Log::warning("[Viewer:checkPolicy] Denied: Gate check or permission missing.", [
                    'rights' => $rights,
                    'has_permission_object' => ($moduleInfo['permission'] !== false)
                ]);
                return false;
            }
        }

        Log::info("[Viewer:checkPolicy] Access granted.", ['rights' => $rights]);

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
