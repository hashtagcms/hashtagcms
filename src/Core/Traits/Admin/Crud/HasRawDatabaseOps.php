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
            QueryLogger::log('rawInsert', 'raw insert failed', null, 0);
            return $status;
        }

        DB::commit();

        //Logging
        try {
            $queryLog = QueryLogger::getQueryLog();
            QueryLogger::log('rawInsert', $queryLog, $data, (int)$status);
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
            QueryLogger::log('rawDelete', 'raw delete failed', null, 0);
            return $status;
        }

        DB::commit();

        //Logging
        try {
            $queryLog = QueryLogger::getQueryLog();
            QueryLogger::log('rawDelete', $queryLog, ['table' => $table, 'where' => $where], 0);
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
                QueryLogger::log('rawUpdate', $queryLog, ['table' => $table, 'data' => $data, 'where' => $where], 0);
            } catch (\Exception $exception) {
                info($exception->getMessage());
            }
        }

        return $update;
    }

    /**
     * Bulk update position/index for a set of records in a single query.
     *
     * Instead of N individual UPDATE queries (one per row), this fires a single
     * CASE WHEN statement:
     *
     *   UPDATE `table`
     *   SET `positionColumn` = CASE `idColumn`
     *       WHEN 5 THEN 1
     *       WHEN 3 THEN 2
     *       WHEN 9 THEN 3
     *   END
     *   WHERE `idColumn` IN (5, 3, 9)
     *
     * @param string $table          Table name (e.g. 'cms_modules')
     * @param array  $rows           Array of [ ['id' => X, 'position' => Y], ... ]
     * @param string $idColumn       Primary key column name (default: 'id')
     * @param string $positionColumn Column to update                (default: 'position')
     * @return int   Number of affected rows
     */
    public function bulkUpdateIndex(
        string $table,
        array  $rows,
        string $idColumn       = 'id',
        string $positionColumn = 'position'
    ): int {
        if (empty($rows)) {
            return 0;
        }

        // Build parameterised searched CASE expression:
        //   SET `position` = CASE WHEN `id` = ? THEN ? ... END
        $caseBindings = [];
        $ids          = [];
        $whens        = [];

        foreach ($rows as $row) {
            $whens[] = "WHEN `{$idColumn}` = ? THEN ?";
            $caseBindings[] = $row[$idColumn];
            $caseBindings[] = $row[$positionColumn];
            $ids[]          = $row[$idColumn];
        }

        $allBindings = array_merge($caseBindings, $ids);
        $inPlaceholders = implode(', ', array_fill(0, count($ids), '?'));

        // Ensure table name is safe.
        $safeTable = strpos($table, '`') !== false ? $table : "`{$table}`";

        $sql = sprintf(
            'UPDATE %s SET `%s` = CASE %s END WHERE `%s` IN (%s)',
            $safeTable,
            $positionColumn,
            implode(' ', $whens),
            $idColumn,
            $inPlaceholders
        );

        $affected = 0;
        DB::beginTransaction();
        try {
            $affected = DB::affectingStatement($sql, $allBindings);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            info("bulkUpdateIndex error: " . $exception->getMessage());
            throw $exception;
        }

        return $affected;
    }

    /**
     * General-purpose bulk UPDATE — handles composite WHERE keys and multiple SET columns.
     *
     * Collapses N rawUpdate() calls into a SINGLE SQL statement per updated column:
     *
     *   UPDATE `category_site`
     *   SET
     *     `position` = CASE
     *         WHEN `category_id` = ? AND `site_id` = ? THEN ?
     *         WHEN `category_id` = ? AND `site_id` = ? THEN ?
     *     END
     *   WHERE (`category_id` = ? AND `site_id` = ?)
     *      OR (`category_id` = ? AND `site_id` = ?)
     *
     * @param string $table   Table name
     * @param array  $items   Array of [ ['where' => [col => val, ...], 'data' => [col => val, ...]], ... ]
     * @return int   Number of affected rows
     */
    public function bulkRawUpdate(string $table, array $items): int
    {
        if (empty($items)) {
            return 0;
        }

        // Derive column lists from the first item (all items must have the same shape)
        $dataKeys  = array_keys($items[0]['data']);
        $whereKeys = array_keys($items[0]['where']);

        // ── SET  ─────────────────────────────────────────────────────────────
        // For each column being SET, build one CASE WHEN block:
        //   `position` = CASE WHEN `category_id` = ? AND `site_id` = ? THEN ? ... END
        $setClauses   = [];
        $caseBindings = [];

        foreach ($dataKeys as $dataCol) {
            $whens = [];
            foreach ($items as $item) {
                $condParts = array_map(fn($wk) => "`{$wk}` = ?", $whereKeys);
                foreach ($whereKeys as $wk) {
                    $caseBindings[] = $item['where'][$wk];
                }
                $caseBindings[] = $item['data'][$dataCol];
                $whens[] = 'WHEN ' . implode(' AND ', $condParts) . ' THEN ?';
            }
            $setClauses[] = sprintf('`%s` = CASE %s END', $dataCol, implode(' ', $whens));
        }

        // ── WHERE ─────────────────────────────────────────────────────────────
        //   (`category_id` = ? AND `site_id` = ?) OR (`category_id` = ? AND `site_id` = ?)
        $whereParts    = [];
        $whereBindings = [];

        foreach ($items as $item) {
            $condParts = array_map(fn($wk) => "`{$wk}` = ?", $whereKeys);
            foreach ($whereKeys as $wk) {
                $whereBindings[] = $item['where'][$wk];
            }
            $whereParts[] = '(' . implode(' AND ', $condParts) . ')';
        }

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $table,
            implode(', ', $setClauses),
            implode(' OR ', $whereParts)
        );

        $allBindings = array_merge($caseBindings, $whereBindings);

        QueryLogger::enableQueryLog();
        DB::beginTransaction();

        try {
            $affected = DB::affectingStatement($sql, $allBindings);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            QueryLogger::log('bulkRawUpdate', 'bulk raw update failed: ' . $e->getMessage(), $items, 0);
            throw $e;
        }

        try {
            $queryLog = QueryLogger::getQueryLog();
            QueryLogger::log('bulkRawUpdate', $queryLog, ['table' => $table, 'items' => $items], $affected);
        } catch (\Exception $e) {
            info($e->getMessage());
        }

        return $affected;
    }
}
