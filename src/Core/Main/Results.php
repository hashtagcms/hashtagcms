<?php

namespace HashtagCms\Core\Main;

use Illuminate\Support\Facades\DB;

/**
 * Using session for now
 */
class Results
{
    public function __construct()
    {

    }

    /**
     * Convert to array
     */
    private function makeArray(mixed $data): array
    {
        return json_decode(json_encode($data), true);
    }

    /**
     * Select one or many records
     */
    private function selectOneOrMany(string $query, array $byParams = [], ?string $database = null, ?bool $selectOne = null): array
    {

        $queryParams = (count($byParams) == 0) ? $this->findAndReplaceGlobalIds($query) : $byParams;

        $select = ($selectOne === true) ? 'selectOne' : 'select';

        /*info("======================== query ============= ");
        info(json_encode($query));
        info(json_encode($queryParams));*/
        try {
            if ($database === null) {
                if (count($queryParams) > 0) {
                    return $this->makeArray(DB::{$select}($query, $queryParams));
                }

                return $this->makeArray(DB::{$select}($query));
            } else {
                if (count($queryParams) > 0) {
                    return $this->makeArray(DB::connection($database)->{$select}($query, $queryParams));
                }

                return $this->makeArray(DB::connection($database)->{$select}($query));
            }

        } catch (\Exception $e) {
            info('dbSelect: There is an error: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Parse query and get the results
     *
     * @return array|null (optional)
     */
    public function dbSelect(string $query, array $byParams = [], ?string $database = null): ?array
    {
        return $this->selectOneOrMany($query, $byParams, $database, false);
    }

    /**
     * Parse query and get the results
     *
     * @return array|null (optional)
     */
    public function dbSelectOne(string $query, array $byParams = [], ?string $database = null): ?array
    {

        return $this->selectOneOrMany($query, $byParams, $database, true);

    }

    /**
     * Find and Replace for making query
     * :site_id will be array("site_id"=>1)
     */
    private function findAndReplaceGlobalIds(string $str): array
    {
        $infoLoader = app()->HashtagCms->infoLoader();
        $subject = $str;
        $pattern = "/:\w+/i";
        preg_match_all($pattern, $subject, $matches); //PREG_OFFSET_CAPTURE
        $arr = [];
        $matches = $matches[0];
        //info("matches: ". json_encode($matches));
        foreach ($matches as $key => $val) {
            $queryKey = $val;
            $gKey = $infoLoader->getContextVars($queryKey);
            if (isset($gKey) && $gKey != null) {
                //info("queryKey: $queryKey queryValue:". $gKey["value"]);
                $arr[$gKey['key']] = $gKey['value'];
            }
        }

        return $arr;
    }
}
