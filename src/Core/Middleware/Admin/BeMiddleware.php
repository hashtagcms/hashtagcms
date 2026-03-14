<?php

namespace HashtagCms\Core\Middleware\Admin;

use Closure;
use HashtagCms\Models\Site;
use Illuminate\Support\Facades\Log;
use HashtagCms\Core\Utils\CacheKeys;

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

        Log::info("[BeMiddleware] Checking backend access.", ['user' => $user?->id]);

        if ($user == null || ($user != null && $user->user_type == 'Visitor')) {
            Log::warning("[BeMiddleware] Access Denied: User is null or Visitor.", ['user' => $user?->email]);
            abort(403);
        }

        $allSites = Site::getSupportedSitesForUser($user->id);

        if ($allSites->isEmpty()) {
            Log::warning("[BeMiddleware] Access Denied: User has no sites.", ['user_id' => $user->id]);
            abort(403, 'User does not have access to any sites.');
        }

        if ($allSites->find(htcms_get_siteId_for_admin()) == null) {
            $newSiteId = $allSites->first()->id;
            Log::info("[BeMiddleware] Defaulting admin site ID to first accessible site.", ['site_id' => $newSiteId]);
            htcms_set_siteId_for_admin($newSiteId);
        }

        Log::info("[BeMiddleware] Current Admin Site ID.", ['site_id' => htcms_get_siteId_for_admin()]);

        // Layout preference handling
        if (!session()->has(CacheKeys::CMS_LAYOUT)) {
            session([CacheKeys::CMS_LAYOUT => 'table']);
        }

        $this->handleQuery($request);

        return $next($request);
    }

    private function handleQuery($request)
    {
        // View Style
        if ($request->has('layout')) {
            $layout = ($request->query('layout') === 'grid') ? 'grid' : 'table';
            Log::info("[BeMiddleware] Changing layout preference.", ['layout' => $layout]);
            session([CacheKeys::CMS_LAYOUT => $layout]);
            session()->save();
        }

        // Records per page
        if ($request->has('records_per_page') && !empty($request->query('records_per_page'))) {
            $recordsPerPage = (int) $request->query('records_per_page');
            Log::info("[BeMiddleware] Changing records per page.", ['records_per_page' => $recordsPerPage]);
            session([CacheKeys::CMS_RECORDS_PER_PAGE => $recordsPerPage]);
            session()->save();
        }

    }
}
