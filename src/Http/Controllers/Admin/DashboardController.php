<?php

namespace HashtagCms\Http\Controllers\Admin;

use HashtagCms\Models\Category;
use HashtagCms\Models\Comment;
use HashtagCms\Models\Contact;
use HashtagCms\Models\Subscriber;

class DashboardController extends BaseAdminController
{
    public function index($more = null)
    {
        $cCount = Contact::today()->count();
        $contactToday = ($cCount > 0) ? " ( $cCount <span class='new'>new</span> )" : '';

        $sCount = Subscriber::today()->count();
        $subscriberToday = ($sCount > 0) ? " ( $sCount <span class='new'>new</span> )" : '';

        $commentsCount = Comment::today()->count();
        $commentsToday = ($commentsCount > 0) ? " ( $commentsCount <span class='new'>new</span> )" : '';

        $topPageLimit = htcms_admin_config('chartPages', true);

        $graphData['categories'] = Category::getReadCounts($topPageLimit);
        $graphData['pages'] = Category::getContentReadCounts($topPageLimit);

        $dashboardData = [
            [
                'label' => 'Contacts '.$contactToday,
                'total' => Contact::where('site_id', htcms_get_siteId_for_admin())->count(),
                'icon' => 'fa fa-telegram',
                'link' => 'contact',
            ],
            [
                'label' => 'Subscribers '.$subscriberToday,
                'total' => Subscriber::where('site_id', htcms_get_siteId_for_admin())->count(),
                'icon' => 'fa fa-handshake-o',
                'link' => 'subscriber',
            ],
            [
                'label' => 'Comments '.$commentsToday,
                'total' => Comment::where('site_id', htcms_get_siteId_for_admin())->count(),
                'icon' => 'fa fa-comments-o',
                'link' => 'comment',
            ],

        ];
        $data['data'] = $dashboardData;
        $data['graphData'] = json_encode($graphData);

        return htcms_admin_view('dashboard.index', $data);
    }
}
