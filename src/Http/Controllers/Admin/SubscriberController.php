<?php

namespace HashtagCms\Http\Controllers\Admin;

use HashtagCms\Models\Subscriber;

class SubscriberController extends BaseAdminController
{
    protected $dataFields = ['id', 'email', 'name', 'subscribed_for', 'created_at'];

    protected $dataSource = Subscriber::class;

    protected $actionFields = ['delete']; //This is last column of the row
}
