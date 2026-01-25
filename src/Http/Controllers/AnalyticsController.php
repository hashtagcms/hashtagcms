<?php

namespace HashtagCms\Http\Controllers;

use Illuminate\Support\Facades\Http;
use HashtagCms\Events\UserVisit;

//Keeping out of hahatgcms controller scope;
class AnalyticsController extends Controller
{
    /**
     * Publish data
     *
     * @return false|string
     */
    public function publish()
    {
        // 1. Check if External API is enabled
        if (config('hashtagcms.enable_external_api')) {
            return $this->publishViaExternalApi();
        }

        // 2. Local Processing (Default)
        $data = \request()->post();
        if (isset($data['categoryId']) && $data['categoryId'] > 0) {
            event(new UserVisit('category', (int) $data['categoryId']));
        }
        if (isset($data['pageId']) && $data['pageId'] > 0) {
            event(new UserVisit('page', (int) $data['pageId']));
        }

        return json_encode(['success' => true]);
    }

    /**
     * Publish analytics data via External API
     * @return false|string
     */
    private function publishViaExternalApi()
    {
        $context = config('hashtagcms.context');
        $apiSecret = config('hashtagcms.api_secrets.' . $context);
        $apiUrl = config('hashtagcms.publish_api');

        if (empty($apiUrl) || empty($apiSecret)) {
            logger()->error("Analytics: Missing External API URL or Secret for context: $context");
            return json_encode(['success' => false, 'message' => 'Configuration Error']);
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'api_key' => $apiSecret
            ])->post($apiUrl, array_merge(request()->all(), ['site' => $context]));

            if ($response->successful()) {
                return json_encode(['success' => true]);
            } else {
                logger()->error("Analytics External API Error: " . $response->body());
                return json_encode(['success' => false, 'message' => 'Remote Server Error']);
            }
        } catch (\Exception $e) {
            logger()->error("Analytics External API Exception: " . $e->getMessage());
            return json_encode(['success' => false, 'message' => 'Connection Error']);
        }
    }
}

