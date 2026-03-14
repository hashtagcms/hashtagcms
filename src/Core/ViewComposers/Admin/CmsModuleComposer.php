<?php

namespace HashtagCms\Core\ViewComposers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use HashtagCms\Models\CmsModule;
use HashtagCms\Models\User;
use HashtagCms\Core\Utils\CacheKeys;
use HashtagCms\Core\Utils\RedisCacheManager;
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

            $cachePrefix = config('hashtagcmsadmin.permissions.cache_key_prefix', CacheKeys::CMS_PERMISSIONS_CACHE_KEY_PREFIX);
            $cacheKey = $cachePrefix . CacheKeys::CMS_PERMISSIONS_MENU_ALLOWED . $user->id;

            // If CMS_PERMISSIONS_BOOT was cleared (e.g. a module was saved/deleted),
            // invalidate each user's session-based sidebar cache so it gets rebuilt.
            $bootKey = RedisCacheManager::getInternalPrefix() . CacheKeys::CMS_PERMISSIONS_BOOT;
            $bootCacheExists = Cache::has($bootKey);
            if (!$bootCacheExists) {
                session()->forget($cacheKey);
                // Re-arm the boot flag so we only rebuild once per change
                Cache::forever($bootKey, true);
            }

            if (session()->has($cacheKey)) {
                $modulesAllowed = session()->get($cacheKey);
            } else {
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
                session()->put($cacheKey, $modulesAllowed);
            }

            self::$data = [
                'allModules' => collect($modulesAllowed)
            ];
        }

        $view->with('allModules', self::$data['allModules']);
    }
}
