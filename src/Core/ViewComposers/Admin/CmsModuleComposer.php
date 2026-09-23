<?php

namespace HashtagCms\Core\ViewComposers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use HashtagCms\Models\CmsModule;
use HashtagCms\Models\User;
use Illuminate\Support\Collection;

class CmsModuleComposer
{
    /**
     * Per-request memo keyed by user id. NOT a cross-request cache: a fresh
     * instance is created per request, so this only avoids recomputing when the
     * sidebar composer fires more than once within a single request.
     *
     * @var array<int, \Illuminate\Support\Collection>
     */
    protected $memo = [];

    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('allModules', $this->buildAllowedModules());
    }

    /**
     * Build the admin menu the current user is allowed to see.
     *
     * This is computed per request (and per user) rather than cached in a
     * static: a process-lifetime static persists across requests in long-running
     * runtimes (`php artisan serve`, Octane, Swoole, RoadRunner), which would
     * both serve a stale menu after modules change AND leak one user's menu to
     * the next user handled by the same worker.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function buildAllowedModules(): Collection
    {
        $authUser = Auth::user();
        if ($authUser === null) {
            return collect();
        }

        if (isset($this->memo[$authUser->id])) {
            return $this->memo[$authUser->id];
        }

        $user = User::find($authUser->id);
        if ($user === null) {
            return collect();
        }

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

        return $this->memo[$authUser->id] = collect($modulesAllowed);
    }
}
