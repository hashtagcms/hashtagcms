<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers;

use MarghoobSuleman\HashtagCms\Events\UserVisit;

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
        $data = \request()->post();
        if (isset($data['categoryId']) && $data['categoryId'] > 0) {
            event(new UserVisit('category', (int) $data['categoryId']));
        }
        if (isset($data['pageId']) && $data['pageId'] > 0) {
            event(new UserVisit('page', (int) $data['pageId']));
        }

        return json_encode(['success' => true]);
    }
}

