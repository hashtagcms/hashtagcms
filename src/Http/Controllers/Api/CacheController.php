<?php

namespace HashtagCms\Http\Controllers\Api;

use Illuminate\Http\Request;
use HashtagCms\Core\Utils\RedisCacheManager;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class CacheController extends ApiBaseController
{
    /**
     * Get all cache keys
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) 
    {
        $this->authenticate($request);
        $pattern = $request->input('pattern', '*');
        $keys = RedisCacheManager::getAllKeys($pattern);
        
        return response()->json([
            'success' => true,
            'count' => count($keys),
            'data' => $keys
        ], Response::HTTP_OK);
    }

    /**
     * Clear all cache
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearAll(Request $request)
    {
        try {
            $this->authenticate($request);


            RedisCacheManager::flush();
            return response()->json(['message' => 'Cache cleared successfully', 'status' => Response::HTTP_OK]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => Response::HTTP_INTERNAL_SERVER_ERROR], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Clear specific cache key
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearKey(Request $request)
    {
        $this->authenticate($request);

        $key = $request->input('key');
        if (empty($key)) {
            return response()->json(['message' => 'Key is required', 'status' => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        }

        try {
            RedisCacheManager::forget($key);
            return response()->json(['message' => "Cache key '{$key}' cleared successfully", 'status' => Response::HTTP_OK]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => Response::HTTP_INTERNAL_SERVER_ERROR], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Clear site configs cache
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearSiteConfig(Request $request)
    {
        $this->authenticate($request);
        
        $context = $request->input('site') ?? $request->header('x-site');
        
        // Match keys with this context. Using * prefix to handle Laravel cache prefix.
        $prefix = RedisCacheManager::getApiPrefix();
        $pattern = "*{$prefix}" . \HashtagCms\Core\Utils\CacheKeys::SITE_CONFIGS . "_{$context}_*";
        
        try {
            $count = RedisCacheManager::clearByPattern($pattern);
            return response()->json([
                'success' => true,
                'message' => "Cleared {$count} site config entries for site '{$context}'",
                'keys_cleared' => $count
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => Response::HTTP_INTERNAL_SERVER_ERROR], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Clear load data cache
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearLoadData(Request $request) 
    {
        $this->authenticate($request);
        
        $context = $request->input('site') ?? $request->header('x-site');
        
        // Match keys with this context. Using * prefix to handle Laravel cache prefix.
        $prefix = RedisCacheManager::getApiPrefix();
        $pattern = "*{$prefix}" . \HashtagCms\Core\Utils\CacheKeys::LOAD_DATA . "_{$context}_*";
        
        try {
            $count = RedisCacheManager::clearByPattern($pattern);
            return response()->json([
                'success' => true,
                'message' => "Cleared {$count} load data entries for site '{$context}'",
                'keys_cleared' => $count
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => Response::HTTP_INTERNAL_SERVER_ERROR], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Authenticate Request
     * @param Request $request
     * @return void
     * @throws \Exception
     */
    private function authenticate(Request $request)
    {
        $context = $request->input('site') ?? $request->header('x-site');
        $api_secret = $request->input('api_secret') ?? $request->header('x-api-secret');

        if (empty($context) || empty($api_secret)) {
             throw new HttpResponseException(response()->json(['message' => 'Unauthorized: Site context and API secret are required.', 'status' => Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED));
        }

        $secrets = config('hashtagcms.api_secrets', []);
        if (!isset($secrets[$context]) || $secrets[$context] !== $api_secret) {
            throw new HttpResponseException(response()->json(['message' => 'Unauthorized: Invalid API secret or site context.', 'status' => Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED));
        }
    }
}
