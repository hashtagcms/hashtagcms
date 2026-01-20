<?php

namespace HashtagCms\Core\Middleware\Admin;

use Closure;
use HashtagCms\Models\CmsModule;

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

        $segments = request()->segments();

        // Assume the first segment is the admin base path (e.g. 'admin') and remove it
        if (count($segments) > 0) {
            array_shift($segments);
        }

        $path = implode('/', $segments);

        if (empty($path)) {
            $controllerName = config('hashtagcmsadmin.cmsInfo.defaultPage');
            $request->module_info = $this->adminModule::getInfoByName($controllerName);
        } else {
            // Use robust longest-prefix matching
            $request->module_info = $this->adminModule::getModuleFromUrl($path);
        }

        $result = $next($request);

        //info("cmsModuleInfo: Moving to Next middleware");
        return $result;
    }
}