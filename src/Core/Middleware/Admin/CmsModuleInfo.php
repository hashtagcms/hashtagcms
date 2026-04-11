<?php

namespace HashtagCms\Core\Middleware\Admin;

use Closure;
use HashtagCms\Models\CmsModule;
use HashtagCms\Models\CmsPermission;
use Illuminate\Support\Facades\Log;

class CmsModuleInfo
{
    protected $adminModule;

    public function __construct(CmsModule $adminModuleInfo)
    {

        $this->adminModule = $adminModuleInfo;

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $segments = $request->segments();
        $debugLogging = config('hashtagcmsadmin.cmsInfo.debug_logging', false);

        // Assume the first segment is the admin base path (e.g. 'admin') and remove it
        if (count($segments) > 0) {
            array_shift($segments);
        }

        $path = implode('/', $segments);

        if ($debugLogging) {
            Log::info("[CmsModuleInfo] Admin request started.", [
                'method' => $request->method(),
                'path' => $path,
                'url' => $request->fullUrl()
            ]);
        }

        if (empty($path)) {
            $controllerName = config('hashtagcmsadmin.cmsInfo.defaultPage', 'dashboard');
            $moduleInfo = $this->adminModule::getInfoByName($controllerName);
        } else {
            $moduleInfo = $this->adminModule::getModuleFromUrl($path);
        }

        // Tier 2: Permission Pre-fetching & Early Rejection
        if ($moduleInfo) {
            $user = auth()->user();
            if ($debugLogging) {
                Log::info("[CmsModuleInfo] Module found.", ['module' => $moduleInfo->controller_name, 'id' => $moduleInfo->id]);
            }
            if ($user) {
                $isAdmin = $user->isAdmin();

                // If not admin, pre-fetch and attach permission to the module object
                $permission = $isAdmin ? true : CmsPermission::has($moduleInfo->id, $user->id);

                if ($debugLogging) {
                    Log::info("[CmsModuleInfo] User permission resolved.", [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'is_admin' => $isAdmin,
                        'has_permission' => ($permission !== false)
                    ]);
                }

                // SECURITY: Early rejection at the middleware level
                if (!$isAdmin && $permission === false) {
                    Log::warning("[CmsModuleInfo] SECURITY: Access denied for user.", ['user_id' => $user->id, 'module_id' => $moduleInfo->id]);
                    abort(403, "You don't have permission to access this module.");
                }

                $moduleInfo->permission = $permission;
            }
        } else {
            if ($debugLogging) {
                Log::warning("[CmsModuleInfo] Module not found for path.", ['path' => $path]);
            }
        }

        $request->module_info = $moduleInfo;

        return $next($request);
    }
}