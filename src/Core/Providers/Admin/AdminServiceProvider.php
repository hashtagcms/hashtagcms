<?php

namespace HashtagCms\Core\Providers\Admin;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use HashtagCms\Core\Policies\CmsPolicy;
use HashtagCms\Core\ViewComposers\Admin\CmsModuleComposer;
use HashtagCms\Models\CmsPermission;
use HashtagCms\Models\Permission;
use Illuminate\Support\Facades\Schema;
use HashtagCms\Core\Utils\RedisCacheManager;
use HashtagCms\Core\Utils\CacheKeys;
class AdminServiceProvider extends ServiceProvider
{
    protected $policies = [
        CmsPermission::class => CmsPolicy::class,
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->registerPolicies();

        // Only load permissions if tables exist (prevents errors during migrations/fresh install)
        $allPermission = $this->loadPermissions();

        if ($allPermission != null) {
            Log::info("[AdminServiceProvider] Defining Gate permissions.", ['count' => $allPermission->count()]);
            foreach ($allPermission as $permission) {

                Gate::define($permission->name, function ($user) use ($permission) {
                    return (($user->hasRole($permission->roles)) || $user->isAdmin()) && $user->user_type == 'Staff';
                });

            }
        }

        //Only For Admin
        $theme = config('hashtagcmsadmin.cmsInfo.theme');

        View::composer([$theme . '.common.sidebar', $theme . '.common.index'], CmsModuleComposer::class);
    }

    /**
     * Load permissions with proper error handling
     * 
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    protected function loadPermissions()
    {
        try {
            // Check if required tables exist before querying
            // This prevents errors during fresh installation or migrations
            if (!Schema::hasTable('permissions')) {
                return null;
            }

            $ttl = (int)config('hashtagcmsadmin.permissions.cache_ttl', 0);

            // If TTL is 0 or less, skip caching
            if ($ttl <= 0) {
                Log::info("[AdminServiceProvider] Loading permissions from database (cache disabled).");
                return $this->getPermission();
            }

            $cacheKey = RedisCacheManager::getInternalPrefix() . CacheKeys::CMS_PERMISSIONS_BOOT;

            return Cache::remember($cacheKey, $ttl, function () {
                Log::info("[AdminServiceProvider] Cache MISS: Loading permissions from database.");
                return $this->getPermission();
            });

        } catch (\Exception $e) {
            // Log the error but don't break the application
            Log::error("[AdminServiceProvider] Failed to load permissions.", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get permission
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|null
     */
    protected function getPermission()
    {
        try {
            return Permission::with('roles')->get();
        } catch (\Exception $e) {
            logger($e->getMessage());

            return null;
        }

    }
}
