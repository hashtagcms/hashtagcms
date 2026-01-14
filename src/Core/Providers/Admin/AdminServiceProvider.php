<?php

namespace HashtagCms\Core\Providers\Admin;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use HashtagCms\Core\Policies\CmsPolicy;
use HashtagCms\Core\ViewComposers\Admin\CmsModuleComposer;
use HashtagCms\Models\CmsPermission;
use HashtagCms\Models\Permission;
use Illuminate\Support\Facades\Schema;
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

            foreach ($allPermission as $permission) {

                Gate::define($permission->name, function ($user) use ($permission) {
                    return (($user->hasRole($permission->roles)) || $user->isSuperAdmin()) && $user->user_type == 'Staff';
                });

            }
        }

        //Only For Admin
        $theme = config('hashtagcmsadmin.cmsInfo.theme');

        View::composer($theme . '.common.*', CmsModuleComposer::class);
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
            if (!Schema::hasTable('permissions') || 
                !Schema::hasTable('cache')) {
                return null;
            }

            // Use array cache as fallback if database cache is not available
            // This ensures the service provider works even if cache table doesn't exist
            $cacheDriver = Schema::hasTable('cache') ? null : 'array';
            
            $cacheStore = $cacheDriver ? Cache::store($cacheDriver) : Cache::getFacadeRoot();

            // Cache permissions for 1 hour to reduce database queries
            return $cacheStore->remember('cms_permissions_boot', 3600, function () {
                return $this->getPermission();
            });

        } catch (\Exception $e) {
            // Log the error but don't break the application
            logger('AdminServiceProvider: Failed to load permissions - ' . $e->getMessage());
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
