<?php

namespace HashtagCms\Core\Utils;

use Illuminate\Http\Client\PendingRequest;

class Result extends PendingRequest
{
    /**
     * To make it more universal.
     *
     * @todo: Add some more method to it
     *
     * @return array
     */
    public static function forView($data)
    {

        return collect($data)->all();
    }
}
