<?php

namespace HashtagCms\Models;

/***
 * AdminBaseModel
 * this is base admin model
 *
 */

use HashtagCms\Models\BaseModel;
use HashtagCms\Models\QueryLogger;
use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use HashtagCms\Core\Utils\CacheKeys;
use InvalidArgumentException;
use ReflectionClass;

abstract class AdminBaseModel extends BaseModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->perPage = session(CacheKeys::CMS_RECORDS_PER_PAGE, config('hashtagcmsadmin.cmsInfo.records_per_page'));
    }

    /**
     * Override to prevent Laravel 13's HasCollection trait from trying to
     * instantiate this abstract class when walking the inheritance chain.
     */
    public function resolveCollectionFromAttribute(): ?string
    {
        try {
            $reflectionClass = new ReflectionClass(static::class);
            $attributes = $reflectionClass->getAttributes(CollectedBy::class);

            if (!empty($attributes[0])) {
                $args = $attributes[0]->getArguments();
                if (!empty($args[0])) {
                    return $args[0];
                }
            }
        } catch (\Throwable $e) {
            // Silently fail — returning null falls back to the default collection.
        }

        return null;
    }

    /**
     * Get Data from current model
     *
     * @param  string  $with
     * @param  array  $searchParams
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getData($with = '', $searchParams = [], $where = [])
    {

        $obj = ($with != '') ? static::with($with) : new static;

        // Apply Contributor filter: Only show their own content
        $user = Auth::user();
        if ($user && $user->isContributor() && !$user->isAdmin()) {
            $tableName = $obj->getModel()->getTable();
            if (Schema::hasColumn($tableName, 'insert_by')) {
                $obj = $obj->where($tableName . '.insert_by', $user->id);
            } else if (Schema::hasColumn($tableName, 'user_id')) {
                $obj = $obj->where($tableName . '.user_id', $user->id);
            }
        }

        //add where condition
        if (count($searchParams) > 0) {


            foreach ($searchParams as $key => $searchParam) {

                switch ($key) {
                    case 'where':
                        $obj = $obj->where($searchParam[0], $searchParam[1], $searchParam[2]);
                        break;
                }

            }
        }

        if (count($where) > 0) {

            //array("field"=>"", "operator"=>"", "value"=>"")
            foreach ($where as $index => $item) {
                $obj = $obj->where($item['field'], $item['operator'], $item['value']);
            }
        }

        //Order by id desc
        $obj = $obj->orderBy($obj->getModel()->getKeyName(), 'DESC');

        $perPage = session(CacheKeys::CMS_RECORDS_PER_PAGE, config('hashtagcmsadmin.cmsInfo.records_per_page'));
        $res = $obj->paginate($perPage);

        return $res;
    }

    /**
     * @param  string  $with
     * @param  null  $field
     * @param  null  $opr
     * @param  null  $val
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function searchData($with = '', $field = null, $opr = null, $val = null, $where = [])
    {

        $arr = null;

        if ($field !== null && $opr != null && $val != null) {

            switch ($opr) {
                case 'like%':
                    $val = $val . '%';
                    break;
                case '%like%':
                    $val = '%' . $val . '%';
                    break;
            }

            $opr = Str::contains($opr, 'like') ? 'like' : $opr;

        }

        if (Str::contains($field, '.')) {

            $field = explode('.', $field);
            $relationWhere = $field[0];
            $field = $field[1];

            $perPage = session(CacheKeys::CMS_RECORDS_PER_PAGE, config('hashtagcmsadmin.cmsInfo.records_per_page'));

            $data = self::with($with)->whereHas($relationWhere, function ($query) use ($field, $opr, $val) {

                $query->where($field, $opr, $val);

            })->orderBy((new static)->getKeyName(), 'DESC')->paginate($perPage);

            return $data;

        } else {

            $arr = ['where' => [$field, $opr, $val]];
        }

        return self::getData($with, $arr);
    }

    /**
     * Find By Id
     *
     * @param  int  $id
     * @param  string  $with
     * @return array
     */
    public static function getById($id = 0, $with = '')
    {

        QueryLogger::enableQueryLog();

        if ($with != '') {
            $with = (is_array($with)) ? $with : [$with];
        }

        $obj = ($with != '') ? static::withoutGlobalScopes()->with($with) : new static;

        $data = $obj->findOrFail($id)->toArray();

        $queryLog = QueryLogger::getQueryLog();

        try {

            QueryLogger::log('editStart', $queryLog, $data, (int) $id);

        } catch (\Exception $exception) {

            info($exception->getMessage());

        }

        return $data;
    }

    /**
     * Get data as compatible to render combobox
     *
     * @param  array  $fileds
     * @param  string  $loadWith
     * @return AdminBaseModel[]|\Illuminate\Database\Eloquent\Collection
     */
    public function combo($fileds = [], $loadWith = 'lang')
    {

        if (method_exists($this, 'lang')) {
            if (count($fileds) > 0) {
                return self::all($fileds)->load($loadWith);
            } else {
                return self::all()->load($loadWith);
            }
        } else {
            if (count($fileds) > 0) {
                return self::all($fileds);
            } else {
                return self::all();
            }
        }

    }

    /**
     * Get all tables (Driver agnostic)
     * @return array
     */
    public static function getTables()
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $tables = [];

        if ($driver === 'mongodb') {
            try {
                foreach ($connection->getMongoDB()->listCollections() as $collection) {
                    $tables[] = $collection->getName();
                }
            } catch (\Exception $e) {
                // Fallback
            }
        } elseif ($driver === 'sqlite') {
            $results = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($results as $row) {
                $tables[] = $row->name;
            }
        } elseif ($driver === 'pgsql') {
            $results = DB::select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public'");
            foreach ($results as $row) {
                $tables[] = $row->tablename;
            }
        } else {
            // MySQL/MariaDB
            $results = DB::select('SHOW TABLES');
            foreach ($results as $row) {
                // Determine the key (e.g. "Tables_in_database")
                $rowArray = (array) $row;
                $tables[] = array_shift($rowArray);
            }
        }
        return $tables;
    }

    /**
     * Get Enum Values from any table.
     *
     * Table and field names are validated against the live schema before use
     * (PDO cannot parameterize SQL identifiers, so whitelist validation is the
     * correct defence). Backtick-quoting is applied as a second layer.
     *
     * @throws InvalidArgumentException if table or field cannot be found in the schema.
     */
    public static function getEnumValues(?string $table = null, ?string $field = null): array
    {
        $enum = [];

        if (empty($table) || empty($field)) {
            return $enum;
        }

        // SHOW COLUMNS is MySQL-specific.
        if (config('database.default') !== 'mysql') {
            return $enum;
        }

        // Whitelist: confirm the table exists in the current schema.
        if (!Schema::hasTable($table)) {
            throw new InvalidArgumentException("getEnumValues: table '{$table}' does not exist.");
        }

        // Whitelist: confirm the column exists in that table.
        if (!Schema::hasColumn($table, $field)) {
            throw new InvalidArgumentException("getEnumValues: column '{$field}' does not exist in table '{$table}'.");
        }

        // Backtick-quote the validated identifier; use a binding for the field value.
        $type = DB::select('SHOW COLUMNS FROM `' . $table . '` WHERE Field = ?', [$field]);

        if (!empty($type)) {
            preg_match('/^enum\((.*)\)$/', $type[0]->Type, $matches);
            foreach (explode(',', $matches[1] ?? '') as $value) {
                $enum[] = trim($value, "'");
            }
        }

        return $enum;
    }
}
