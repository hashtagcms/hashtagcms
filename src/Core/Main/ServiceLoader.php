<?php

namespace HashtagCms\Core\Main;

use HashtagCms\Models\Site;

/** v2 */
class ServiceLoader extends DataLoader
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
        return parent::loadConfig($context, $lang, $platform);
    }

    /**
     * Load data
     *
     * @param  array|null  $params
     */
    public function loadData(string $context, ?string $lang = null, ?string $platform = null, $category = null, $microsite = null): array
    {
        return parent::loadData($context, $lang, $platform, $category, $microsite);

    }

    /**
     * Load data
     */
    public function loadModule(?array $params = null): mixed
    {
        if (empty($params['name'])) {
            return $this->getErrorMessage('Module alias is missing', 400);
        }

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
