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

        // Cache permissions for 1 hour to reduce database queries
        // Fallback to direct call if cache fails
        $allPermission = Cache::remember('cms_permissions_boot', 3600, function () {
            return $this->getPermission();
        });

        // Fallback if cache returns null
        if ($allPermission === null) {
            $allPermission = $this->getPermission();
        }

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
