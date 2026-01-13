<?php

namespace HashtagCms\Core\Traits\Api;

trait ErrorHandler
{
    //public $id;

    /**
     * @param  null  $collection
     * @param  string  $message
     * @return array
     */
    public function handleError($message = '')
    {
        return [
            'data' => [],
            'error' => true,
            'message' => ($message == '') ? 'Collection not found!' : $message,
        ];
    }

    public function hasError($collectionId = null)
    {
        return empty($collectionId) ? true : false;
    }
}
