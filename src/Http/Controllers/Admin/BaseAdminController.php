<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use HashtagCms\Core\Traits\Admin\AdminCrud;
use HashtagCms\Core\Traits\Admin\BaseAdmin;
use HashtagCms\Core\Traits\Admin\LogManager;
use HashtagCms\Core\Traits\Admin\UploadManager;

class BaseAdminController extends BaseController
{
    use AdminCrud, AuthorizesRequests, BaseAdmin,
        DispatchesJobs, LogManager, UploadManager, ValidatesRequests;

    public function __construct(Request $request)
    {

    }

    /**
     * Get By Id
     *
     * @param  int  $id
     * @return array
     */
    protected function getById($id = 0)
    {

        $dataSource = $this->getDataSource();
        $dataWith = $this->getDataWith();

        $data['results'] = [];

        if ($id > 0) {
            $data['results'] = $dataSource::getById($id, $dataWith);
        }

        return $data;

    }
}
