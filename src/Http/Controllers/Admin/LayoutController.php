<?php

namespace HashtagCms\Http\Controllers\Admin;

class LayoutController extends BaseAdminController
{
    public function index($more = null)
    {
        return htcms_admin_view('common.index');
    }
}
