<?php

namespace HashtagCms\Core\Helpers;

class Message
{
    /**
     * Get read error
     *
     * @return mixed
     */
    public static function getReadError($data = [])
    {
        $errorData['status'] = 401;
        $errorData['title'] = 'Access Denied';
        $errorData['message'] = "Sorry! You don't have permission to view this page.";

        $errorData = array_merge($data, $errorData);

        return $errorData;
    }

    /**
     * Get write error
     *
     * @return mixed
     */
    public static function getWriteError($data = [])
    {
        $errorData['status'] = 401;
        $errorData['title'] = 'Access Denied';
        $errorData['message'] = "Sorry! You don't have permission to write.";
        //merge data with errorData
        $errorData = array_merge($data, $errorData);

        return $errorData;
    }

    /**
     * Get delete error
     *
     * @return mixed
     */
    public static function getDeleteError($data = [])
    {
        $errorData['status'] = 401;
        $errorData['title'] = 'Access Denied';
        $errorData['message'] = "Sorry! You don't have permission to delete.";

        return array_merge($data, $errorData);
    }
}
