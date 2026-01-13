<?php

namespace HashtagCms\Http\Controllers\Admin;

use HashtagCms\Core\Helpers\Message;

class OauthController extends BaseAdminController
{
    public function index($more = null)
    {
        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        return htcms_admin_view('oauth.index');
    }
}
