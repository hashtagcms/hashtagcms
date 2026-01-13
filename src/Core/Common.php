<?php

namespace HashtagCms\Core;

use HashtagCms\Core\Main\DataLoader;
use HashtagCms\Core\Main\InfoLoader;
use HashtagCms\Core\Main\LayoutManager;
use HashtagCms\Core\Main\ModuleLoader;
use HashtagCms\Core\Main\Results;
use HashtagCms\Core\Main\SessionManager;

class Common extends Results
{
    public function __construct()
    {
        info('==========- init common -===============');
    }

    /**
     * @override
     * Parse query and get the results
     *
     * @return array|null (optional)
     */
    public function dbSelect(string $query, array $byParams = [], ?string $database = null): ?array
    {
        return parent::dbSelect($query, $byParams, $database);
    }

    /**
     * @override
     * Parse query and get the results
     *
     * @return array|null (optional)
     */
    public function dbSelectOne(string $query, array $byParams = [], ?string $database = null): ?array
    {
        return parent::dbSelectOne($query, $byParams, $database);
    }

    /**
     * Get Header menu
     */
    public function getHeaderMenu(?string $active = null): array
    {
        return $this->layoutManager()->getHeaderMenu($active);
    }

    /**
     * Get header Menu HTML
     */
    public function getHeaderMenuHtml(array $data, int $maxLimit = -1, ?array $css = null): string
    {
        return $this->layoutManager()->getHeaderMenuHtml($data, $maxLimit, $css);
    }

    /**
     * Set shared module data
     */
    public function setSharedModuleData(string $alias, mixed $data): void
    {
        $this->moduleLoader()->setSharedModuleData($alias, $data);
    }

    /**
     * Get shared module data
     */
    public function getSharedModuleData(string $alias): mixed
    {
        return $this->moduleLoader()->getSharedModuleData($alias);
    }

    /**
     * Get layout manager instance
     */
    public function layoutManager(): LayoutManager
    {
        return app()->HashtagCmsLayoutManager;
    }

    /**
     * Get info loader
     */
    public function infoLoader(): InfoLoader
    {
        return app()->HashtagCmsInfoLoader;
    }

    /**
     * Get data loader
     */
    public function dataLoader(): DataLoader
    {
        return app()->HashtagCmsDataLoader;
    }

    /**
     * Get module loader
     */
    public function moduleLoader(): ModuleLoader
    {
        return app()->HashtagCmsModuleLoader;
    }

    /**
     * Get cache manager
     */
    public function sessionManager(): SessionManager
    {
        return app()->HashtagCmsCache;
    }

    /**
     * is external api enabled
     */
    public function useExternalApi(): bool
    {
        return env('HASHTAG_CMS_ENABLE_EXTERNAL_API') === true;
    }

    /**
     * Get config url
     */
    public function getConfigApiSource(): string
    {
        return env('HASHTAG_CMS_CONFIG_API');
    }

    /**
     * Get load data url
     */
    public function getLoadDataApiSource(): string
    {
        return env('HASHTAG_CMS_DATA_API');
    }
}
