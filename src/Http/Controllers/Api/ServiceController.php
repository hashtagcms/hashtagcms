<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Api;

use Illuminate\Http\Request;
use MarghoobSuleman\HashtagCms\Core\Main\ServiceLoader;
use MarghoobSuleman\HashtagCms\Core\Traits\FeEssential;
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
        $api_secret = $query['api_secret'] ?? $request->header('x-api-secret') ?? $request->header('api_key');
        if (empty($api_secret)) {
            return response()->json(['message' => 'Api key is missing.', 'status' => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        }

        $secrets = config('hashtagcms.api_secrets');
        $foundSecret = false;
        foreach ($secrets as $key => $secret) {
            if ($context === $key && $api_secret === $secret) {
                $foundSecret = true;
                break;
            }
        }
        if (!$foundSecret) {
            return response()->json(['message' => 'API key or site context is not valid', 'status' => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        }

        $loader = new ServiceLoader();

        try {
            $result = $loader->allConfigs($context, $lang, $platform);
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
        try {
            $result = $loader->loadData($context, $lang, $platform, $category, $microsite);
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
        try {
            $result = $loader->loadData($context, $lang, $platform, $category, $microsite);
            if (isset($result['status']) && $result['status'] != 200) {
                return response()->json($result, $result['status'] ?? Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), $exception->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        unset($result['html']);

        return $result;
    }
}
