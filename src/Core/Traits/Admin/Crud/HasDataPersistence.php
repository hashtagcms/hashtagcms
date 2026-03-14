<?php

namespace HashtagCms\Core\Traits\Admin\Crud;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\QueryLogger;
use HashtagCms\Models\Site;

/**
 * Trait HasDataPersistence
 *
 * Handles all data saving operations with support for:
 * - Multi-language data
 * - Site relationships
 * - Platform relationships
 * - Transactions and rollback
 * - Query logging
 *
 * @package HashtagCms\Core\Traits\Admin\Crud
 */
trait HasDataPersistence
{
    /**
     * Save Data with Lang
     *
     * @param array $saveData
     * @param array $langData
     * @param mixed $where
     * @param bool $updateInAllLangs
     * @return mixed
     */
    protected function saveDataWithLang($saveData = [], $langData = [], $where = null, $updateInAllLangs = false)
    {
        $data['saveData'] = $saveData;
        $data['langData'] = $langData;

        return $this->saveAllData($data, $where, $updateInAllLangs);
    }

    /**
     * Save Data with Lang and Site
     *
     * @param array $saveData
     * @param array $langData
     * @param array $siteData
     * @param mixed $where
     * @param bool $updateInAllLangs
     * @return mixed
     */
    protected function saveDataWithLangAndSite($saveData = [], $langData = [], $siteData = [], $where = null, $updateInAllLangs = false)
    {
        $data['saveData'] = $saveData;
        $data['langData'] = $langData;
        $data['siteData'] = $siteData;

        return $this->saveAllData($data, $where, $updateInAllLangs);
    }

    /**
     * Save Data with Lang and Platform
     *
     * @param array $saveData
     * @param array $langData
     * @param array $platformData
     * @param mixed $where
     * @param bool $updateInAllLangs
     * @return mixed
     */
    protected function saveDataWithLangAndPlatform($saveData = [], $langData = [], $platformData = [], $where = null, $updateInAllLangs = false)
    {
        $data['saveData'] = $saveData;
        $data['langData'] = $langData;
        $data['platformData'] = $platformData;

        return $this->saveAllData($data, $where, $updateInAllLangs);
    }

    /**
     * Save Data with Lang, Site and Platform
     *
     * @param array $saveData
     * @param array $langData
     * @param array $siteData
     * @param array $platformData
     * @param mixed $where
     * @param bool $updateInAllLangs
     * @return mixed
     */
    protected function saveDataWithLangAndSiteAndPlatform($saveData = [], $langData = [], $siteData = [], $platformData = [], $where = null, $updateInAllLangs = false)
    {
        $data['saveData'] = $saveData;
        $data['langData'] = $langData;
        $data['siteData'] = $siteData;
        $data['platformData'] = $platformData;

        return $this->saveAllData($data, $where, $updateInAllLangs);
    }

    /**
     * Save Data
     *
     * @param array $saveData
     * @param mixed $where
     * @param bool $updateInAllLangs
     * @return mixed
     */
    protected function saveData($saveData = [], $where = null, $updateInAllLangs = false)
    {
        $data['saveData'] = $saveData;

        return $this->saveAllData($data, $where, $updateInAllLangs);
    }

    /**
     * Save All Data - Main persistence method
     *
     * Handles insert/update with relationships (lang, site, platform)
     *
     * @param array $data - saveData, langData, siteData, platformData
     * @param mixed $where
     * @param bool $updateInAllLangs
     * @return array
     * @throws \Exception
     */
    private function saveAllData($data, $where = null, $updateInAllLangs = false)
    {
        $savedDataModel = $data['saveData']['model'];
        $resource = ($where != null && $where > 0) ? $savedDataModel::find($where) : null;

        //Better to be safe
        if (!$this->checkPolicy('edit', $resource)) {
            return Message::getWriteError();
        }

        //Start db transaction
        DB::beginTransaction();
        //need to log query
        QueryLogger::enableQueryLog();

        $savedData = $data['saveData']['data'];

        $langData = null;
        $siteData = null;

        $supportedSiteLangs = [];
        //Lang Data
        if (isset($data['langData'])) {
            $langData = $data['langData']['data'];
            $site_id = htcms_get_siteId_for_admin();
            if (isset($data['siteData'])) {
                $site_id = $data['siteData']['site_id'] ?? htcms_get_siteId_for_admin();
            }
            if (isset($data['platformData'])) {
                $site_id = $data['platformData']['site_id'] ?? htcms_get_siteId_for_admin();
            }
            $supportedSiteLangs = $this->getSupportedSiteLang($site_id); //This is in Common Trait
        }

        $rData = [];

        /**************************** Insert ********************************/
        if ($where == null || $where <= 0) {
            $rData = $this->performInsert($savedDataModel, $savedData, $data, $langData, $supportedSiteLangs);
            $actionLog = 'insert';
        } else {
            /**************************** Update ********************************/
            $rData = $this->performUpdate($savedDataModel, $savedData, $where, $data, $langData, $supportedSiteLangs, $updateInAllLangs);
            $actionLog = 'update';
        }

        //Logging
        try {
            $queryLog = QueryLogger::getQueryLog();
            QueryLogger::log($actionLog, $queryLog, $data, (int)$rData['id'] ?? 0);
        } catch (\Exception $exception) {
            info($exception->getMessage());
        }

        DB::commit();

        return $rData;
    }

    /**
     * Perform Insert Operation
     *
     * @param string $savedDataModel
     * @param array $savedData
     * @param array $data
     * @param array|null $langData
     * @param array $supportedSiteLangs
     * @return array
     * @throws \Exception
     */
    private function performInsert($savedDataModel, $savedData, $data, $langData, $supportedSiteLangs)
    {
        //make key val;
        $fieldKeyVal = [];
        foreach ($savedData as $key => $val) {
            $fieldKeyVal[$key] = $val;
        }

        // Automatically set ownership if columns exist
        $tableName = (new $savedDataModel)->getTable();
        if (Schema::hasColumn($tableName, 'insert_by') && !isset($fieldKeyVal['insert_by'])) {
            $fieldKeyVal['insert_by'] = Auth::id();
        } else if (Schema::hasColumn($tableName, 'user_id') && !isset($fieldKeyVal['user_id'])) {
            $fieldKeyVal['user_id'] = Auth::id();
        }

        //Save main model using create() which returns the model instance
        $mainModel = $savedDataModel::create($fieldKeyVal);
        $rData['isSaved'] = true;
        $rData['id'] = $mainModel->getKey();
        $rData['source'] = $mainModel;

        //Save Language Model
        if ($langData != null) {
            if (!method_exists($mainModel, 'lang')) {
                DB::rollBack();
                throw new \Exception("'lang' relation method is needed in source class.");
            }

            $langDatas = [];
            foreach ($supportedSiteLangs as $key => $siteLang) {
                $langData['lang_id'] = $siteLang['id'];
                $langDatas[] = $langData;
            }
            $rData['isSavedLang'] = $mainModel->lang()->createMany($langDatas);
        }

        // Pivot Data Persistence (Site/Platform)
        $siteInput = $data['siteData'] ?? null;
        $platformInput = $data['platformData'] ?? null;

        if ($siteInput || $platformInput) {
            $relMethod = method_exists($mainModel, 'site') ? 'site' : (method_exists($mainModel, 'platform') ? 'platform' : null);

            if ($relMethod) {
                $pivotData = array_merge($siteInput['data'] ?? [], $platformInput['data'] ?? []);
                $pivotTable = $mainModel->$relMethod()->getTable();
                $modelIdKey = Str::singular($mainModel->getTable()) . '_id';

                $insertRecord = $pivotData;
                $insertRecord[$modelIdKey] = $mainModel->getKey();

                // Assign site_id if missing but table has it
                if (Schema::hasColumn($pivotTable, 'site_id') && !isset($insertRecord['site_id'])) {
                    $insertRecord['site_id'] = $siteInput['site_id'] ?? ($siteInput['data']['site_id'] ?? htcms_get_siteId_for_admin());
                }

                // Handle platform_id column
                if (Schema::hasColumn($pivotTable, 'platform_id')) {
                    $targetPlatformId = $platformInput['platform_id'] ?? ($platformInput['data']['platform_id'] ?? ($siteInput['data']['platform_id'] ?? null));

                    if ($targetPlatformId) {
                        $insertRecord['platform_id'] = $targetPlatformId;
                        DB::table($pivotTable)->insert($insertRecord);
                    } else {
                        // Insert for all supported platforms of the site
                        $targetSiteId = $insertRecord['site_id'] ?? htcms_get_siteId_for_admin();
                        $supportedPlatforms = $this->getSupportedSitePlatform($targetSiteId);
                        foreach ($supportedPlatforms as $platform) {
                            $record = $insertRecord;
                            $record['platform_id'] = $platform['id'];
                            DB::table($pivotTable)->insert($record);
                        }
                    }
                } else {
                    // Simple site-only pivot (no platform_id column)
                    DB::table($pivotTable)->insert($insertRecord);
                }
            }
        }

        return $rData;
    }

    /**
     * Perform Update Operation
     *
     * @param string $savedDataModel
     * @param array $savedData
     * @param mixed $where
     * @param array $data
     * @param array|null $langData
     * @param array $supportedSiteLangs
     * @param bool $updateInAllLangs
     * @return array
     * @throws \Exception
     */
    private function performUpdate($savedDataModel, $savedData, $where, $data, $langData, $supportedSiteLangs, $updateInAllLangs)
    {
        $mainModel = new $savedDataModel();

        //lang data
        if ($langData != null) {
            $langMethod = 'lang';
            $foundLangMethod = true;
            if ($updateInAllLangs === true) {
                $langMethod = 'langs';
            }

            if (!method_exists($mainModel, $langMethod)) {
                $foundLangMethod = false;
            }

            //if saving lang and don't have 'lang' relation in model. ignore everything.
            if (!$foundLangMethod) {
                DB::rollBack();
                throw new \Exception("Update Error: '$langMethod' relation method is needed in source class. ");
            }

            $mainModel = $mainModel->with($langMethod)->find($where);
        } else {
            $mainModel = $mainModel->find($where);
        }

        $rData['isSaved'] = $mainModel->update($savedData);
        $rData['id'] = $where;
        $rData['source'] = $mainModel;

        if ($langData != null) {
            $langData['lang_id'] = (isset($langData['lang_id'])) ? $langData['lang_id'] : htcms_get_language_id_for_admin();

            if ($updateInAllLangs === true) {
                // in all langs
                $mainTable = Str::singular($mainModel->getTable());
                $primaryKey = $mainTable . '_' . $mainModel->getKeyName();
                $langTable = $mainTable . '_langs';
                foreach ($supportedSiteLangs as $supportedLang) {
                    $newLangData = $langData;
                    $newLangData['lang_id'] = $supportedLang->id;
                    $arrWhere = [[$primaryKey, '=', $where], ['lang_id', '=', $supportedLang->id]];
                    $rData['isSavedLang'] = $this->rawUpdate($langTable, $newLangData, $arrWhere, false);
                }
            } else {
                // Strip lang_id from the UPDATE payload — it is part of the composite primary key
                // (country_id, lang_id) and must only appear in the WHERE clause (handled by the
                // Eloquent relation scope), NOT in the SET clause. Including it causes a duplicate
                // key violation when Eloquent fires: UPDATE ... SET lang_id = 2 WHERE country_id = X
                $updateLangData = array_diff_key($langData, ['lang_id' => null]);
                $rData['isSavedLang'] = $mainModel->lang()->update($updateLangData);
            }
        }

        // Handle Pivot Data Persistence (Site/Platform)
        $siteInput = $data['siteData'] ?? null;
        $platformInput = $data['platformData'] ?? null;

        if ($siteInput || $platformInput) {
            $relMethod = method_exists($mainModel, 'site') ? 'site' : (method_exists($mainModel, 'platform') ? 'platform' : null);

            if ($relMethod) {
                $rel = $mainModel->$relMethod();
                $pivotTable = $rel->getTable();
                $pivotData = array_merge($siteInput['data'] ?? [], $platformInput['data'] ?? []);
                $modelIdKey = Str::singular($mainModel->getTable()) . '_id';

                $whereClause = [[$modelIdKey, '=', $where]];

                // Site constraint
                if (Schema::hasColumn($pivotTable, 'site_id')) {
                    $targetSiteId = $siteInput['site_id'] ?? ($siteInput['data']['site_id'] ?? ($platformInput['data']['site_id'] ?? null));
                    if ($targetSiteId) {
                        $whereClause[] = ['site_id', '=', $targetSiteId];
                    }
                }

                // Platform constraint
                if (Schema::hasColumn($pivotTable, 'platform_id')) {
                    $targetPlatformId = $platformInput['platform_id'] ?? ($platformInput['data']['platform_id'] ?? ($siteInput['data']['platform_id'] ?? null));
                    if ($targetPlatformId) {
                        $whereClause[] = ['platform_id', '=', $targetPlatformId];
                    }
                }

                // Ensure the pivot record exists before updating
                $exists = DB::table($pivotTable)->where($whereClause)->exists();
                if ($exists) {
                    DB::table($pivotTable)->where($whereClause)->update($pivotData);
                } else {
                    // If it doesn't exist (e.g., adding to a new platform during Edit), insert it
                    $insertRecord = array_merge($this->whereClauseToData($whereClause), $pivotData);
                    DB::table($pivotTable)->insert($insertRecord);
                }
            }
        }

        return $rData;
    }

    /**
     * Convert where clause array [col, op, val] to [col => val]
     * @param $where
     * @return array
     */
    private function whereClauseToData($where)
    {
        $data = [];
        foreach ($where as $w) {
            if (is_array($w) && count($w) == 3) {
                $data[$w[0]] = $w[2];
            }
        }
        return $data;
    }
}
