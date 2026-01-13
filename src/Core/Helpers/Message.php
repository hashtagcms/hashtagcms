<?php

namespace HashtagCms\Core\Helpers;

class Message
{
    /**
     * Get read error
     *
     * @return mixed
     */
    public static function getReadError()
    {
        $errorData['status'] = 401;
        $errorData['title'] = 'Access Denied';
        $errorData['message'] = "Sorry! You don't have permission to view this page.";

        return $errorData;
    }

    /**
     * Get write error
     *
     * @return mixed
     */
    public static function getWriteError()
    {
        $errorData['status'] = 401;
        $errorData['title'] = 'Access Denied';
        $errorData['message'] = "Sorry! You don't have permission to write.";

        return $errorData;
    }

    /**
     * Get delete error
     *
     * @return mixed
     */
    public static function getDeleteError()
    {
        $errorData['status'] = 401;
        $errorData['title'] = 'Access Denied';
        $errorData['message'] = "Sorry! You don't have permission to delete.";

        return $errorData;
    }
}
