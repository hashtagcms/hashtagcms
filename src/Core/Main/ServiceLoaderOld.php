<?php

namespace HashtagCms\Core\Main;

use HashtagCms\Models\Lang;
use HashtagCms\Models\Platform;
use HashtagCms\Models\Site;

class ServiceLoaderOld extends DataLoader
{
    protected InfoLoader $infoLoader;

    protected SessionManager $sessionManager;

    protected LayoutManager $layoutManager;

    protected ModuleLoader $moduleLoader;

    public function __construct()
    {
        parent::__construct();
        $this->moduleLoader = app()->HashtagCms->moduleLoader();
        $this->layoutManager = app()->HashtagCms->layoutManager();
        $this->infoLoader = app()->HashtagCms->infoLoader();
    }

    /**
     * Get site config
     */
    public function allConfigs(string $context, ?string $lang = null, ?string $platform = null): array
    {
        // fetch site info from request -> context
        // fetch lang info from request -> lang
        // fetch platform info from request -> platform
        // if lang is passed in request use that else fetch lang_id from site info
        // if platform is passed in request use that else fetch platform_id from site info
        // after that fetch all the info.

        if (empty($context)) {
            return $this->getErrorMessage('Site context is missing', 400);
        }
        $lang_id = null;
        $platform_id = null;
        $site = Site::where('context', '=', $context)->first();

        if (empty($site)) {
            return $this->getErrorMessage('Site not found', 404);
        }

        if (! empty($lang)) {
            $langInfo = Lang::where('iso_code', '=', $lang)->first();
            if (! empty($langInfo)) {
                $lang_id = $langInfo->id;
            }
        }
        if (! empty($platform)) {
            $platformInfo = Platform::where('link_rewrite', '=', $platform)->first();
            if (! empty($platformInfo)) {
                $platform_id = $platformInfo->id;
            }
        }

        $site_id = $site->id;
        $lang_id = ($lang_id == null) ? $site->lang_id : $lang_id;
        $platform_id = ($platform_id == null) ? $site->platform_id : $platform_id; //will use this

        $siteInfo = $this->infoLoader->getSiteInfo($context, $lang_id);
        $platforms = $this->infoLoader->getAllSupportedPlatforms($site_id);
        $langs = $this->infoLoader->getAllSupportedLangs($site_id);
        $currencies = $this->infoLoader->getAllSupportedCurrencies($site_id);
        $zones = $this->infoLoader->getAllSupportedZones($site_id);
        $categories = $this->infoLoader->getAllSupportedCategories($site_id, $lang_id, $platform_id);
        $countries = $this->infoLoader->getAllSupportedCountries($site_id, $lang_id);
        $props = $this->infoLoader->getSitePropsInfo($site_id, $platform_id);

        $data['site'] = $siteInfo;
        $data['defaults'] = ['category_id' => $site->category_id,
            'lang_id' => $site->lang_id,
            'country_id' => $site->country_id,
            'platform_id' => $site->platform_id,
            'currency_id' => $site->currency_id ?? 1];
        $data['platforms'] = $platforms;
        $data['langs'] = $langs;
        $data['categories'] = $categories;
        $data['currencies'] = $currencies;
        $data['zones'] = $zones;
        $data['counties'] = $countries;
        $data['props'] = $props;

        return ['data' => $data, 'status' => 200];
    }

    /**
     * Load data
     */
    public function loadData(?array $params = null): mixed
    {

        $this->moduleLoader::setMandatoryCheck(false);

        return parent::loadData($params);

    }

    /**
     * Load data
     */
    public function loadModule(?array $params = null): mixed
    {
        if (empty($params['name'])) {
            return $this->getErrorMessage('Module alias is missing', 400);
        }

        $this->moduleLoader::setMandatoryCheck(false);
        $data = parent::loadData($params);
        if ($data['status'] != 200) {
            return $data;
        }
        $alias = $params['name'];
        $hooks = $data['meta']['theme']['hooks'];
        $moduleData = null;
        $moduleInfo = [];
        foreach ($hooks as $hook) {
            foreach ($hook['modules'] as $module) {
                if ($module->alias === $alias) {
                    $moduleInfo = (array) $module;
                    $moduleData = $module->data;
                    break;
                }
            }
        }

        $this->layoutManager->setFinalObject($data);
        $this->layoutManager->setThemePath($data['meta']['theme']['directory']);

        $moduleInfo['data'] = $moduleData;

        if ($moduleData === null) {
            return $this->getErrorMessage('Could not find the module alias', 400);
        }

        return ['meta' => $data['meta'], 'module' => $moduleInfo, 'status' => 200];

    }

    /**
     * Load data by hook alias
     */
    public function loadHook(?array $params = null): mixed
    {
        if (empty($params['name'])) {
            return $this->getErrorMessage('Hook alias is missing', 400);
        }

        $this->moduleLoader::setMandatoryCheck(false);
        $data = parent::loadData($params);
        if ($data['status'] != 200) {
            return $data;
        }
        $alias = $params['name'];
        $hooks = $data['meta']['theme']['hooks'];
        $hookInfo = [];
        $hookData = null;
        foreach ($hooks as $hook) {
            if ($hook['alias'] === $alias) {
                $hookData = $hook;
                break;
            }
        }
        $this->layoutManager->setFinalObject($data);
        $this->layoutManager->setThemePath($data['meta']['theme']['directory']);

        return ($hookData === null) ? null : ['meta' => $data['meta'], 'hook' => $hookData, 'status' => 200];

    }
}
