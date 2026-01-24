<?php

namespace HashtagCms\Http\Controllers\Admin;

class LocalizationController extends BaseAdminController
{
    public function index($more = null)
    {
        return htcms_admin_view('common.index');
    }
}
