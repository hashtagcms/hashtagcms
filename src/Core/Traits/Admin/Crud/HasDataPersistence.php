<?php

namespace HashtagCms\Core\Traits\Admin\Crud;

use Illuminate\Support\Facades\DB;
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
        //Better to be safe
        if (!$this->checkPolicy('edit')) {
            return Message::getWriteError();
        }

        //Start db transaction
        DB::beginTransaction();
        //need to log query
        QueryLogger::enableQueryLog();

        $savedDataModel = $data['saveData']['model'];
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
            QueryLogger::log($actionLog, $queryLog, $data, $rData['id'] ?? 0);
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

        //Site Data
        try {
            if (isset($data['siteData'])) {
                if (!method_exists($mainModel, 'site')) {
                    DB::rollBack();
                    throw new \Exception("'site' relation method is needed in source class.");
                }

                //Model must have belongsToMany relation with 'site'
                $siteData = $data['siteData']['data'];
                $siteInfo = Site::find($siteData['site_id']);
                unset($siteData['site_id']);
                $mainModel->site()->attach($siteInfo, $siteData);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('rollback: ' . $e->getMessage());
        }

        //Platform Data
        if (isset($data['platformData'])) {
            if (!method_exists($mainModel, 'platform')) {
                DB::rollBack();
                throw new \Exception("'platform' relation method is needed in source class.");
            }
            //Model must have belongsToMany relation with 'platform'
            $platformData = $data['platformData']['data'];
            $supportedSitePlatform = $this->getSupportedSitePlatform($platformData['site_id']); //platform data must have a site_id

            //add in supported platform
            foreach ($supportedSitePlatform as $key => $platform) {
                $platformData['platform_id'] = $platform['id'];
                $mainModel->platform()->attach($platform, $platformData);
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
                //in one lang
                $rData['isSavedLang'] = $mainModel->lang()->update($langData);
            }
        }

        if (isset($data['siteData'])) {
            if (!method_exists($mainModel, 'site')) {
                DB::rollBack();
                throw new \Exception("Update Error:  'site' relation method is needed in source class.");
            }

            //Model must have belongsToMany relation with 'site'
            $siteData = $data['siteData']['data'];
            $mainModel->site()->updateExistingPivot($siteData['site_id'], $siteData);
        }

        if (isset($data['platformData'])) {
            if (!method_exists($mainModel, 'platform')) {
                DB::rollBack();
                throw new \Exception("Update Error: 'platform' relation method is needed in source class.");
            }
            //Model must have belongsToMany relation with 'platform'
            $platformData = $data['platformData']['data'];
            $mainModel->platform()->updateExistingPivot($platformData['platform_id'], $platformData);
        }

        return $rData;
    }
}
