<?php

namespace HashtagCms\Models;

use Illuminate\Support\Facades\DB;

class QueryLogger extends AdminBaseModel
{
    protected $table = 'logs';

    protected $guarded = [];

    protected static $queryLogging = true;

    /**
     * Log query and action
     *
     * @param  string  $action
     * @param  string  $query
     * @param  string  $exequted_query
     * @param  int  $record_id
     * @param  null  $author
     */
    protected static function log($action = '', $query = '', $exequted_query = '', $record_id = 0, $author = null)
    {

        $request = \request();
        $module_name = $request->module_info->controller_name;
        $module_id = $request->module_info->id;
        $site_id = htcms_get_siteId_for_admin();
        $author = ($author == null) ? $request->user()->id : $author;

        $record_id = (!is_int($record_id)) ? (int) $record_id : $record_id;

        $query = is_string($query) ? $query : json_encode($query);
        $exequted_query = is_string($exequted_query) ? $exequted_query : json_encode($exequted_query);

        $data = [
            'site_id' => $site_id,
            'module_id' => $module_id,
            'user_id' => $author,
            'action_performed' => $action,
            'query' => $query,
            'executed_query' => $exequted_query,
            'record_id' => $record_id
        ];

        QueryLogger::create($data);
    }

    /**
     * Enable Query Log
     */
    protected static function enableQueryLog()
    {
        if (self::$queryLogging == true) {
            DB::enableQueryLog();
        }
    }

    /**
     * Get query log
     *
     * @return mixed
     */
    protected static function getQueryLog()
    {
        return DB::getQueryLog();
    }

    /**
     * @return void
     */
    public static function setLogginStatus($enable)
    {
        self::$queryLogging = $enable;
    }

    /**
     * Disable Query Log
     */
    public static function disableLogging(): void
    {
        self::setLogginStatus(false);
    }

    /**
     * Enable Query Log
     */
    public static function enableLogging(): void
    {
        self::setLogginStatus(true);
    }
}
