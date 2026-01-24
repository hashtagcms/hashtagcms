<?php

namespace HashtagCms\Http\Controllers\Api;

use Illuminate\Http\Request;
use HashtagCms\Models\Site;
use HashtagCms\Core\Main\ServiceLoader;
use HashtagCms\Core\Traits\FeEssential;
use HashtagCms\Core\Utils\RedisCacheManager;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends ApiBaseController
{
    use FeEssential;

    /**
     * Get data for mobile splash screen
     */
    public function siteConfigs(Request $request): array|string
    {
        $query = $request->all();
        $context = $query['site'] ?? $request->header('x-site');
        $lang = $query['lang'] ?? $request->header('x-lang');
        $platform = $query['platform'] ?? $request->header('x-platform');

        //Basic level of api check -
        // site context and api secret should be there in config/hashtagcms.php
        //Basic level of api check -
        // site context and api secret should be there in config/hashtagcms.php
        $api_secret = $query['api_secret'] ?? $request->header('x-api-secret');
        if (empty($api_secret)) {
            return response()->json(['message' => 'Api secret is missing.', 'status' => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        }

        $secrets = config('hashtagcms.api_secrets');
        if (!isset($secrets[$context]) || $secrets[$context] !== $api_secret) {
            return response()->json(['message' => 'API secret or site context is not valid', 'status' => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        }

        $loader = new ServiceLoader();

        $keyParams = array_merge([$context, $lang, $platform], $this->getHeaderCacheKeys($request));
        $prefix = RedisCacheManager::getApiPrefix();
        $key = RedisCacheManager::generateKey($prefix . \HashtagCms\Core\Utils\CacheKeys::SITE_CONFIGS, $keyParams);

        try {
            $ttl = $this->getCacheTTL('cache_load_config_ttl');
            $result = RedisCacheManager::remember($key, function () use ($loader, $context, $lang, $platform) {
                return $loader->allConfigs($context, $lang, $platform);
            }, $ttl);

            if (isset($result['status']) && $result['status'] != 200) {
                return response()->json($result, $result['status'] ?? Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), $exception->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $result;

    }

    /**
     * Load data
     *
     * @queryParam $lang language code
     * @queryParam $platform Platform link rewrite
     * @queryParam $category Category link rewrite or id
     */
    public function loadData(Request $request): array|string
    {

        $query = $request->all();
        $context = $query['site'] ?? $request->header('x-site');
        $lang = $query['lang'] ?? $request->header('x-lang');
        $platform = $query['platform'] ?? $request->header('x-platform');
        $category = $query['category'] ?? $request->header('x-category');
        $microsite = $query['microsite'] ?? $request->header('x-microsite');

        $loader = new ServiceLoader();
        $keyParams = array_merge([$context, $lang, $platform, $category, $microsite], $this->getHeaderCacheKeys($request));
        $prefix = config('hashtagcms.external_api_cache_prefix', 'api_');
        $key = RedisCacheManager::generateKey("{$prefix}load_data", $keyParams);

        try {
            $ttl = $this->getCacheTTL('cache_load_data_ttl');
            $result = RedisCacheManager::remember($key, function () use ($loader, $context, $lang, $platform, $category, $microsite) {
                return $loader->loadData($context, $lang, $platform, $category, $microsite);
            }, $ttl);

            if (isset($result['status']) && $result['status'] != 200) {
                return response()->json($result, $result['status'] ?? Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), $exception->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $result;
    }

    /**
     * Load data mobile
     *
     * @queryParam $lang language code
     * @queryParam $platform Platform link rewrite
     * @queryParam $category Category link rewrite or id
     */
    public function loadDataMobile(Request $request): array|string
    {

        $query = $request->all();
        $context = $query['site'] ?? $request->header('x-site');
        $lang = $query['lang'] ?? $request->header('x-lang');
        $platform = $query['platform'] ?? $request->header('x-platform');
        $category = $query['category'] ?? $request->header('x-category');
        $microsite = $query['microsite'] ?? $request->header('x-microsite');

        $loader = new ServiceLoader();
        $keyParams = array_merge([$context, $lang, $platform, $category, $microsite], $this->getHeaderCacheKeys($request));
        $prefix = config('hashtagcms.external_api_cache_prefix', 'api_');
        $key = RedisCacheManager::generateKey("{$prefix}load_data", $keyParams);

        try {
            $ttl = $this->getCacheTTL('cache_load_data_mobile_ttl');
            // Reusing the same key as loadData since it's the same data source, we just unset html later
            $result = RedisCacheManager::remember($key, function () use ($loader, $context, $lang, $platform, $category, $microsite) {
                return $loader->loadData($context, $lang, $platform, $category, $microsite);
            }, $ttl);

            if (isset($result['status']) && $result['status'] != 200) {
                return response()->json($result, $result['status'] ?? Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), $exception->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        unset($result['html']);

        return $result;
    }
    /**
     * Get Latest Blogs
     * @param Request $request
     * @return array|string|\Illuminate\Http\JsonResponse
     */
    public function blogLatests(Request $request)
    {
        $query = $request->all();
        $context = $query['site'] ?? $request->header('x-site');
        $lang = $query['lang'] ?? $request->header('x-lang');
        $platform = $query['platform'] ?? $request->header('x-platform');
        $limit = $query['limit'] ?? 10;

        $loader = new ServiceLoader();
        try {
            $category = $query['category'] ?? null;
            $keyParams = array_merge([$context, $lang, $platform, $category, $limit], $this->getHeaderCacheKeys($request));
            $prefix = RedisCacheManager::getApiPrefix();
            $key = RedisCacheManager::generateKey($prefix . \HashtagCms\Core\Utils\CacheKeys::BLOG_LATESTS, $keyParams);
            
            $result = RedisCacheManager::remember($key, function () use ($loader, $context, $lang, $platform, $category, $limit) {
                return $loader->blogLatests($context, $lang, $platform, $category, $limit);
            });
            
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), $exception->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $result;
    }



    /**
     * Get headers for cache key
     * @param Request $request
     * @return array
     */
    private function getHeaderCacheKeys(Request $request): array
    {
        $headers = config('hashtagcmsapi.cache_header_include', []);
        $values = [];
        foreach ($headers as $header) {
            $val = $request->header($header);
            if (!empty($val)) {
                $values[] = $val;
            }
        }
        return $values;
    }

    /**
     * Get Cache TTL from config
     * @param string $configKey
     * @param int $default
     * @return int
     */
    private function getCacheTTL(string $configKey, int $default = 300): int {
        $val = config('hashtagcmsapi.'.$configKey);
        if (empty($val)) {
            return $default;
        }
        // If numeric, assume seconds
        if (is_numeric($val)) {
            return (int)$val;
        }
        
        // Try parsing string (e.g. "4 hours" -> "+4 hours")
        $str = trim($val);
        // Ensure it starts with + or - for strtotime relative parsing if it's just "4 hours"
        if (!str_starts_with($str, '+') && !str_starts_with($str, '-')) {
            $str = "+ " . $str;
        }
        
        $timestamp = strtotime($str);
        if ($timestamp === false) {
            return $default;
        }
        
        // Calculate difference in seconds from now
        $ttl = $timestamp - time();
        return $ttl > 0 ? $ttl : $default;
    }
}
