<?php

namespace HashtagCms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use HashtagCms\Http\Resources\PageResource;
use HashtagCms\Models\Page;

class BlogController extends FrontendBaseController
{
    /**
     * Render page (@override)
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $infoLoader = app()->HashtagCms->infoLoader();
        $callableValue = $infoLoader->getAppCallableValue();
        //check it's blog home
        if (empty($callableValue[0])) {
            $this->setModuleMandatoryCheck(false); //in base controlle            

            $siteId = $infoLoader->getSiteId();
            $langId = $infoLoader->getLangId();
            $categoryName = $infoLoader->getCategoryName();

            $perPage = config('hashtagcms.blog_per_page');

            $moreCategories = config('hashtagcms.more_categories_on_blog_listing');
            $useMore = false;
            if (count($moreCategories) > 0) {
                $useMore = true;
                $moreCategories[] = $categoryName; //add one more
            }

            $requestCat = ($useMore) ? $moreCategories : $categoryName;

            if (config('hashtagcms.enable_external_api')) {
                // Fetch from External API
                $results = $this->fetchBlogsFromExternalApi($requestCat, $perPage);
                $data['results'] = $results;
            } else {
                // Fetch from Local DB
                $results = Page::getLatestBlog($siteId, $langId, $requestCat, $perPage);
                $data['results'] = PageResource::collection($results)->toArray($request);
            }        
            //replace mandatory module with new module.
            $this->replaceViewWith('story', 'stories', $data);

            $forComments = ['isBlogHome' => true];
            $this->bindDataForView('story-comments', $forComments);
           
            return parent::index($request);
        }
         //dd($callableValue);
        
        return parent::index($request);

    }

    public function story($arg1, $arg2)
    {
        return ['From blog/story contoller', $arg1, $arg2];
    }

    /**
     * Fetch blogs from External API
     * @param mixed $category
     * @param int $limit
     * @return array
     */
    private function fetchBlogsFromExternalApi($category, $limit)
    {
        $context = config('hashtagcms.context');
        $apiSecret = config('hashtagcms.api_secrets.' . $context);
        $apiUrl = config('hashtagcms.blog_latests_api') ?? str_replace('/load-data', '/blog/latests', config('hashtagcms.data_api'));

        // Cache Key
        $catKey = is_array($category) ? implode('_', $category) : $category;
        $prefix = \HashtagCms\Core\Utils\RedisCacheManager::getExternalSourcePrefix();
        $cacheKey = "{$prefix}" . \HashtagCms\Core\Utils\CacheKeys::EXTERNAL_BLOG . "_{$context}_{$catKey}_{$limit}";
        $cacheTTL = config('hashtagcms.external_data_cache_ttl', 30);

        $callback = function () use ($apiUrl, $apiSecret, $context, $category, $limit) {

            $payload = [
                'site' => $context,
                'category' => $category, // Can be array or string
                'limit' => $limit
            ];

            //If we have lang
            $infoLoader = app()->HashtagCms->infoLoader();
            if ($infoLoader->getLangId()) {
                //Ideally we should pass lang code, but let's see if API handles it.
                //The API expects 'lang' code (iso_code). Site has default.
                //Let's rely on site context for language unless we have exact code.
                //We don't have easy access to ISO Code here without DB. 
                //But wait, in Stateless mode, we might not have Lang DB.
                //However, request()->get('lang') might be available if passed. 
            }

            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'api_key' => $apiSecret
                ])->get($apiUrl, $payload);

                if ($response->successful()) {
                    return $response->json();
                }
                logger()->error("External Blog API Error: " . $response->body());
                return [];
            } catch (\Exception $e) {
                logger()->error("External Blog API Excepton: " . $e->getMessage());
                return [];
            }
        };

        if (config('hashtagcms.enable_cache') === false) {
            return $callback();
        }

        return Cache::remember($cacheKey, now()->addMinutes($cacheTTL), $callback);
    }
}
