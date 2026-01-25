<?php

namespace HashtagCms\Http\Controllers\Admin;

use HashtagCms\Models\CmsLog;

class CmslogController extends BaseAdminController
{
    protected $dataFields = ['id', 'user.name', 'module.name', 'record_id', 'action_performed', 'created_at'];

    protected $dataSource = CmsLog::class;

    protected $dataWith = ['user', 'module'];

    protected $actionFields = []; //This is last column of the row

    protected $moreActionFields = [
        ['label' => 'Show all info',
            'css' => 'js_ajax',
            'icon_css' => 'fa fa-info-circle',
            'hrefAttributes' => ['data-info' => 'cmslog', 'data-editable' => false, 'data-excludefields' => ['user', 'module']],
            'action' => 'showinfo',
            'action_append_field' => 'id',
        ],
    ];
}
