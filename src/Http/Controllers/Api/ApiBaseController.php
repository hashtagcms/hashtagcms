<?php

namespace HashtagCms\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class ApiBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
