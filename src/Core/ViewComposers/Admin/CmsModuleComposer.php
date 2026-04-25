<?php

namespace HashtagCms\Core\ViewComposers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use HashtagCms\Models\CmsModule;
use HashtagCms\Models\User;
use Illuminate\Support\Collection;

class CmsModuleComposer
{
    protected static $data = null;

    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        if (self::$data === null) {
            $user = User::find(Auth::user()->id);

            $allModules = CmsModule::getAdminModules()->toArray();
            $modulesAllowed = [];

            if ($user->isAdmin()) {
                foreach ($allModules as $module) {
                    $module['readonly'] = 0;
                    if (isset($module['child']) && is_array($module['child'])) {
                        foreach ($module['child'] as $k => $child) {
                            $module['child'][$k]['readonly'] = 0;
                        }
                    }
                    $modulesAllowed[] = $module;
                }
            } else {
                $modules = $user->cmsmodules();
                $permissions = $modules instanceof Collection ? $modules : $modules->get();
                $allowedModuleIds = $permissions->pluck('module_id')->toArray();
                $permissionMap = $permissions->keyBy('module_id')->toArray();

                foreach ($allModules as $module) {
                    $isParentAllowed = in_array($module['id'], $allowedModuleIds);
                    
                    $filteredChildren = [];
                    if (isset($module['child']) && is_array($module['child'])) {
                        foreach ($module['child'] as $child) {
                            if (in_array($child['id'], $allowedModuleIds)) {
                                $child['readonly'] = $permissionMap[$child['id']]['readonly'] ?? 0;
                                $filteredChildren[] = $child;
                            }
                        }
                    }

                    if ($isParentAllowed || count($filteredChildren) > 0) {
                        $module['child'] = $filteredChildren;
                        $module['readonly'] = $isParentAllowed ? ($permissionMap[$module['id']]['readonly'] ?? 0) : 0;
                        $modulesAllowed[] = $module;
                    }
                }
            }

            self::$data = [
                'allModules' => collect($modulesAllowed)
            ];
        }

        $view->with('allModules', self::$data['allModules']);
    }
}
