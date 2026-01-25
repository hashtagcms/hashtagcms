<?php

namespace HashtagCms\Core\Middleware\Admin;

use Closure;
use HashtagCms\Models\Site;

class BeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if ($user == null || ($user != null && $user->user_type == 'Visitor')) {

            abort(403);
        }

        $allSites = Site::getSupportedSitesForUser($user->id);

        if ($allSites->find(htcms_get_siteId_for_admin()) == null) {
            htcms_set_siteId_for_admin($allSites->get(0)->id);
        }

        $result = $next($request);

        //info("cmsInterceptor: Moving to Next middleware");

        return $result;
    }
}
