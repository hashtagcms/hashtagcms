<?php

namespace HashtagCms\Core\Traits\Admin\Crud;

use Illuminate\Support\Facades\DB;
use HashtagCms\Models\QueryLogger;

/**
 * Trait HasRawDatabaseOps
 *
 * Provides raw database operations that bypass Eloquent ORM
 * Useful for bulk operations, performance-critical updates, or legacy table operations
 *
 * @package HashtagCms\Core\Traits\Admin\Crud
 */
trait HasRawDatabaseOps
{
    /**
     * Insert data directly into a table
     *
     * @param string $table Table name
     * @param array $data Data to insert
     * @return bool Success status
     */
    public function rawInsert($table = '', $data = [])
    {
        QueryLogger::enableQueryLog();
        DB::beginTransaction();

        $status = 0;

        try {
            $status = DB::table($table)->insert($data);
        } catch (\Exception $exception) {
            DB::rollBack();
            QueryLogger::log('rawInsert', 'raw insert failed');
            return $status;
        }

        DB::commit();

        //Logging
        try {
            $queryLog = QueryLogger::getQueryLog();
            QueryLogger::log('rawInsert', $queryLog, $data, $status);
        } catch (\Exception $exception) {
            info($exception->getMessage());
        }

        return $status;
    }

    /**
     * Delete data directly from a table
     *
     * @param string $table Table name
     * @param array $where Where conditions
     * @return int Number of rows deleted
     */
    public function rawDelete($table = '', $where = [])
    {
        QueryLogger::enableQueryLog();
        DB::beginTransaction();

        $status = 0;

        try {
            $status = DB::table($table)->where($where)->delete();
        } catch (\Exception $exception) {
            DB::rollBack();
            QueryLogger::log('rawDelete', 'raw delete failed');
            return $status;
        }

        DB::commit();

        //Logging
        try {
            $queryLog = QueryLogger::getQueryLog();
            QueryLogger::log('rawDelete', $queryLog, $table, $where);
        } catch (\Exception $exception) {
            info($exception->getMessage());
        }

        return $status;
    }

    /**
     * Update data directly in a table
     *
     * @param string $table Table name
     * @param array $data Data to update
     * @param array $where Where conditions
     * @param bool $enableLog Whether to enable query logging
     * @return int Number of rows updated
     */
    public function rawUpdate($table = '', $data = [], $where = [], $enableLog = true)
    {
        if ($enableLog) {
            QueryLogger::enableQueryLog();
        }

        $update = DB::table($table)
            ->where($where)
            ->update($data);

        //Logging
        if ($enableLog) {
            try {
                $queryLog = QueryLogger::getQueryLog();
                QueryLogger::log('rawUpdate', $queryLog, $data, $where);
            } catch (\Exception $exception) {
                info($exception->getMessage());
            }
        }

        return $update;
    }
}
