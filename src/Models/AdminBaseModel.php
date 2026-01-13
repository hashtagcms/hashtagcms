<?php

namespace HashtagCms\Models;

/***
 * AdminBaseModel
 * this is base admin model
 *
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class AdminBaseModel extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->perPage = config('hashtagcmsadmin.cmsInfo.records_per_page');
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

        //DB::enableQueryLog();
        $obj = ($with != '') ? static::with($with) : new static;

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

        $res = $obj->paginate(htcms_admin_config('records_per_page'));

        //dd(DB::getQueryLog());
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
        //echo "$field, $opr, $val";

        if ($field !== null && $opr != null && $val != null) {

            switch ($opr) {
                case 'like%':
                    $val = $val.'%';
                    break;
                case '%like%':
                    $val = '%'.$val.'%';
                    break;
            }

            $opr = (Str::contains($opr, 'like') == 1) ? 'like' : $opr;

        }

        if (Str::contains($field, '.') == 1) {

            $field = explode('.', $field);
            $relationWhere = $field[0];
            $field = $field[1];

            //in case $source with (relation) is not defined but you have this in relationship
            if (empty($relationWhere)) {
                $with = $relationWhere;
            }

            $data = self::with($with)->whereHas($relationWhere, function ($query) use ($field, $opr, $val) {

                $query->where($field, $opr, $val);

            })->paginate(htcms_admin_config('records_per_page'));

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

            QueryLogger::log('editStart', $queryLog, $data, $id);

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
     * Get Enum Values from any table
     */
    public static function getEnumValues(?string $table = null, ?string $field = null): array
    {

        $enum = [];
        if ($table != '' && $field != '') {
            $type = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'");
            if (count($type) > 0) {
                $type = $type[0]->Type;
                preg_match('/^enum\((.*)\)$/', $type, $matches);
                foreach (explode(',', $matches[1]) as $value) {
                    $enum[] = trim($value, "'");
                }

                return $enum;
            }
        }

        return $enum;

    }
}
