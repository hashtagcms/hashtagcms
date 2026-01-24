<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
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

        $this->middleware(function ($request, $next) {
            //Some session for layout
            if (!Session::has('layout')) {
                $request->session()->put('layout', 'table');
            }
            //if there is param in url
            if ($request->get('layout')) {
                $layoutType = ($request->get('layout') == 'grid') ? 'grid' : 'table';
                $request->session()->put('layout', $layoutType);
            }
            return $next($request);
        });

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
