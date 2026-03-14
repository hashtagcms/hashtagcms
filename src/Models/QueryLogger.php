<?php

namespace HashtagCms\Models;

use Illuminate\Support\Facades\DB;

class QueryLogger extends AdminBaseModel
{
    protected $table = 'logs';

    protected $guarded = [];

    protected static $queryLogging = true;
    protected static $buffer = [];
    protected static $buffering = false;

    /**
     * Summary of log
     * @param string|null $action
     * @param mixed $query
     * @param mixed $executed_query
     * @param int|null $record_id
     * @param mixed $author
     * @return void
     */
    public static function log(?string $action = '', $query = null, $executed_query = null, ?int $record_id = 0, $author = null)
    {
        if (!self::$queryLogging) {
            return;
        }

        if (self::$buffering) {
            self::buffer($action, $query, $executed_query, $record_id, $author);
            return;
        }

        $data = self::getLogData($action, $query, $executed_query, $record_id, $author);
        QueryLogger::create($data);
    }

    private static function getLogData(?string $action = '', $query = null, $executed_query = null, ?int $record_id = 0, $author = null): array
    {
        $request = \request();
        $module_info = $request->module_info;
        $module_id = is_object($module_info) ? ($module_info->id ?? 0) : ($module_info['id'] ?? 0);

        $site_id = htcms_get_siteId_for_admin();
        $author = ($author == null) ? ($request->user() ? $request->user()->id : null) : (is_object($author) ? ($author->id ?? null) : $author);

        $record_id = is_object($record_id) ? ($record_id->id ?? 0) : (is_array($record_id) ? 0 : $record_id);
        $record_id = (!is_int($record_id)) ? (int) $record_id : $record_id;

        $query = is_string($query) ? $query : json_encode($query);
        $executed_query = is_string($executed_query) ? $executed_query : json_encode($executed_query);

        return [
            'site_id' => $site_id,
            'module_id' => $module_id,
            'user_id' => $author,
            'action_performed' => $action,
            'query' => (string) $query,
            'executed_query' => (string) $executed_query,
            'record_id' => $record_id,
        ];
    }

    /**
     * Buffer query logs
     * 
     * @param mixed $action
     * @param mixed $query
     * @param mixed $executed_query
     * @param mixed $record_id
     * @param mixed $author
     * @return void
     */
    public static function buffer(?string $action = '', $query = null, $executed_query = null, ?int $record_id = 0, $author = null)
    {
        if (self::$queryLogging) {
            $data = self::getLogData($action, $query, $executed_query, $record_id, $author);
            $data['created_at'] = now();
            $data['updated_at'] = now();
            self::$buffer[] = $data;
        }
    }

    /**
     * Save all logs in buffer to database
     * @return bool
     */
    public static function flushBuffer()
    {
        if (empty(self::$buffer)) {
            return false;
        }

        $status = self::insert(self::$buffer);
        self::$buffer = []; // Clear buffer after save
        return $status;
    }

    /**
     * Summary of enableQueryLog
     * @return void
     */
    protected static function enableQueryLog()
    {
        if (self::$queryLogging == true) {
            DB::enableQueryLog();
        }
    }

    /**
     * Summary of getQueryLog
     * @return array
     */
    protected static function getQueryLog()
    {
        return DB::getQueryLog();
    }

    /**
     * Summary of setLoggingStatus
     * @param mixed $enable
     * @return void
     */
    public static function setLoggingStatus($enable)
    {
        self::$queryLogging = $enable;
    }

    /**
     * Summary of disableLogging
     * @return void
     */
    public static function disableLogging(): void
    {
        self::setLoggingStatus(false);
    }

    /**
     * Summary of enableLogging
     * @return void
     */
    public static function enableLogging(): void
    {
        self::setLoggingStatus(true);
    }

    /**
     * Start buffering mode. All log() calls will be buffered instead of direct insert.
     * @return void
     */
    public static function startBuffering(): void
    {
        self::$buffering = true;
    }

    /**
     * Stop buffering mode and save all buffered logs to database.
     * @return bool
     */
    public static function commitLogs(): bool
    {
        self::$buffering = false;
        return self::flushBuffer();
    }
}
