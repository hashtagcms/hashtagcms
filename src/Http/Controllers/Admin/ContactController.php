<?php

namespace HashtagCms\Http\Controllers\Admin;

use HashtagCms\Models\Contact;

class ContactController extends BaseAdminController
{
    protected $dataFields = ['id', 'name', 'email', 'phone', 'comment', 'created_at'];

    protected $dataSource = Contact::class;

    protected $actionFields = ['delete']; //This is last column of the row

    /*protected $bindDataWithAddEdit = array("zones"=>array("dataSource"=>Zone::class, "method"=>"all"),
                                        "currencies"=>array("dataSource"=>Currency::class, "method"=>"all"));*/

}
