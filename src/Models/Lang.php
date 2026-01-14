<?php

namespace HashtagCms\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use HashtagCms\Events\CopyLangData;

class Lang extends AdminBaseModel
{
    protected $guarded = [];

    /**
     * With site
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function site()
    {
        return $this->belongsToMany(Site::class);
    }


    /**
     * Insert Lang in all tables
     *
     * @param  $ids
     */
    public static function insertLangInAllTables($id = null)
    {

        if ($id == null) {
            return false;
        }

        $newLangId = $id;

        $tableNames = self::getTables();

        $lang = Lang::first(); // @todo: should fetch default site language

        foreach ($tableNames as $table_name) {

            if (Str::endsWith($table_name, '_langs')) {

                $where = [['lang_id', '=', $lang->id]]; //get first lang

                //fetched default lang
                $fetchedData = DB::table($table_name)->where($where)->get()->toArray();

                $toBeInsertedData = [];

                if (count($fetchedData) > 0) {
                    //$fetchedData = json_decode(json_encode($fetchedData), true);
                    foreach ($fetchedData as $currentData) {
                        $currentData->lang_id = $newLangId;
                        // in case there is any primary key in lang table
                        if (isset($currentData->id)) {
                            unset($currentData->id); //remove primary key
                        }
                        if (isset($currentData->_id)) {
                            unset($currentData->_id); //remove mongo primary key
                        }
                        $toBeInsertedData[] = (array) $currentData;
                    }

                    if (count($toBeInsertedData) > 0) {

                        DB::table($table_name)->insert($toBeInsertedData);

                    }

                }

            }

        }

    }

    /**
     * Copy lang data
     *
     * @return array
     */
    public function copyLangData($sourceLangId = null, $targetLangId = null, $tables = [], $isQueue = false)
    {

        if ($isQueue) {
            event(new CopyLangData($sourceLangId, $targetLangId, $tables));

            return [['table' => 'Queue', 'status' => 1, 'message' => 'Process has been added in the queue.']];
        }

        $data = [];
        foreach ($tables as $table) {
            $rows = DB::table($table)->where('lang_id', $sourceLangId)->get();
            $targetCount = DB::table($table)->where('lang_id', $targetLangId)->count();

            if ($targetCount == 0) {
                $insertData = [];
                //manipulate data
                foreach ($rows as $row) {
                    $item = (array) $row;
                    $item['lang_id'] = $targetLangId;
                    $item['created_at'] = date('Y-m-d H:i:s');
                    $item['updated_at'] = date('Y-m-d H:i:s');
                    if (isset($item['id'])) {
                        unset($item['id']);
                    }
                    if (isset($item['_id'])) {
                        unset($item['_id']);
                    }

                    $insertData[] = $item;
                }

                $status = $this->rawInsert($table, $insertData);
                $msg = ($status == 0) ? 'There is some error while copying content.' : "$table has been copied.";
            } else {
                $status = 0;
                $msg = 'Target language is already there. Unable to copy';
            }
            $data[] = ['table' => $table, 'status' => $status, 'message' => $msg];

        }

        return $data;
    }

    /**
     * Get only language tables (ending with _langs)
     *
     * @return array
     */
    public static function getOnlyLangTables()
    {
        $allTables = self::getTables();
        $langTables = [];
        foreach ($allTables as $table) {
            if (Str::endsWith($table, '_langs')) {
                $langTables[] = $table;
            }
        }
        return $langTables;
    }

    /**
     * Get all language tables
     *
     * @return array
     */
    public static function getAllLangTables()
    {
        $rawTables = self::getTables();
        $arr = [];
        foreach ($rawTables as $value) {
            if (!Str::endsWith($value, '_langs')) {
                $table = $value;
                $langTable = Str::singular($value) . '_langs';
                if (Schema::hasTable($langTable)) {
                    $arr[] = ['name' => $langTable, 'baseTable' => $table];
                }
            }
        }

        return $arr;
    }
}
