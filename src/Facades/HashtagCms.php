<?php

namespace HashtagCms\Facades;

use Illuminate\Support\Facades\Facade;

class HashtagCms extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'hashtagcms';
    }
}
