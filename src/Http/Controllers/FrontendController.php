<?php

namespace HashtagCms\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends FrontendBaseController
{
    public function __construct(Request $request)
    {

        parent::__construct($request);

    }

    /**
     * Render page (@override)
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return parent::index($request);

    }
}
