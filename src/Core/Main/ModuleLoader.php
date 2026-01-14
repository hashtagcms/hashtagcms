<?php

namespace HashtagCms\Core\Main;

use Illuminate\Support\Str;
use HashtagCms\Core\Utils\InfoKeys;
use HashtagCms\Models\Module;
use HashtagCms\Models\ModuleProp;
use HashtagCms\Events\ModuleLoaded;

/**
 * Class ModuleLoader
 *
 * @version 1.5
 */
class ModuleLoader
{
    private int $totalModules = 0;

    private mixed $common;

    private array $seoModuleInfo = [];

    private bool $foundSeoModule = false;

    private bool $loginRequired = false;

    private bool $contentFound = true;

    private array $sharedModuleData = [];

    public function __construct()
    {
        $this->common = app()->HashtagCms;
        //$this->infoLoader->setContextVars($key, $lr);
    }

    /**
     * Get header json
     *
     * @return array|mixed
     */
    private function getHeaderJson(mixed $module)
    {
        $moduleHeaderJson = $module->headers;
        $headerJson = [];
        if (!empty($moduleHeaderJson)) {
            try {
                $headerJson = json_decode($moduleHeaderJson, true);
                if (!empty($headerJson)) {
                    foreach ($headerJson as $key => $value) {
                        $headerJson[$key] = ($value === '') ? request()->headers->get($key) : $value;
                    }
                } else {
                    info('Unable to parse header json for the module name: ' . $module->name . ', alias: ' . $module->alias);
                    $headerJson = [];
                }
            } catch (\Exception $exception) {
                info('Invalid header json for the module name: ' . $module->name . ', alias: ' . $module->alias);
                $headerJson = [];
            }
        }

        return $headerJson;
    }

    /**
     * Get query module
     *
     * @param  Module  $module
     */
    public function getQueryModule(mixed $module): array
    {
        $query = $module->data_handler;
        $database = $module->description;
        $ml = new QueryModuleLoader($query, $database);

        return $ml->getResult();
    }

    /**
     * Get service module
     */
    public function getServiceModule(mixed $module, array $withData = []): mixed
    {

        $serviceUrl = ($module->data_handler == '' || $module->data_handler == null) ? '' : $module->data_handler;
        $methodType = $module->method_type;
        $headerJson = $this->getHeaderJson($module);

        $service_params = $module->service_params; //make data
        if ($service_params) {
            parse_str($service_params, $arguments);
            $withData = array_merge($withData, $arguments);
        }

        $ml = new ServiceModuleLoader($serviceUrl, $methodType, $withData, $headerJson);

        return $ml->getResult();
    }

    /**
     * Get module from a service URL. It also parse all query params and send it the service url
     */
    public function getUrlServiceModule(mixed $module, array $withData = []): ?array
    {

        $serviceUrl = ($module->data_handler == '' || $module->data_handler == null) ? '' : $module->data_handler;
        $methodType = $module->method_type;
        $dataKeyMap = $module->data_key_map;
        $headerJson = $this->getHeaderJson($module);

        $service_params = $module->service_params; //make data
        if ($service_params) {
            parse_str($service_params, $arguments);
            $withData = array_merge($withData, $arguments);
        }

        $ml = new UrlServiceModuleLoader($serviceUrl, $methodType, $withData, $dataKeyMap, $headerJson);

        return $ml->getResult();
    }

    /**
     * Get query service module
     * if query as data: call service, execute query and return datas
     * else if query is as param: execute query, replace query with servie url param/value and call service and return data
     */
    public function getQueryServiceModule(mixed $module): ?array
    {
        $query = $module->query_statement;
        $serviceUrl = ($module->data_handler == '' || $module->data_handler == null) ? '' : $module->data_handler;
        $methodType = $module->method_type;
        $dataKeyMap = $module->data_key_map;
        $database = $module->description;

        $dataHandler = $module->data_handler;
        $queryAs = $module->query_as;

        $ml = new QueryServiceModuleLoader($query, $serviceUrl, $queryAs, $methodType);

        return $ml->getResult();
    }

    /**
     * Get Static Module
     * Fetch data from a content table
     */
    public function getStaticModule(mixed $module): ?array
    {
        return $this->getStaticModuleByAlias($module->data_handler);
    }

    /**
     * Get Static Module
     * Fetch data from a content table
     */
    public function getStaticModuleByAlias(string $alias): ?array
    {
        $ml = new StaticModuleLoader($alias);

        return $ml->getResult();
    }

    /**
     * get custom module
     */
    public function getCustomModule(mixed $module): ?array
    {
        $handler = $module->data_handler;
        $data = [];
        try {
            if (strpos($handler, '@') !== false) {
                list($class, $method) = explode('@', $handler);
                if (class_exists($class)) {
                    $instance = app($class);
                    if (method_exists($instance, $method)) {
                        // Pass module info and params
                        $params = $module->service_params;
                        $args = [];
                        if ($params) {
                            parse_str($params, $args);
                        }
                        // Call the method
                        $data = $instance->{$method}($module, $args);
                        // Ensure array return
                        $data = (is_array($data)) ? $data : json_decode(json_encode($data), true);
                    } else {
                        info("ModuleLoader: Method $method not found in $class");
                    }
                } else {
                    info("ModuleLoader: Class $class not found");
                }
            } else {
                info("ModuleLoader: Invalid handler format. Expected Class@method");
            }
        } catch (\Exception $e) {
            info("ModuleLoader: Error executing custom module: " . $e->getMessage());
        }

        return $data;
    }

    public function getServiceLaterModule(mixed $module): ?array
    {
        return [];
    }

    /**
     * Handle unknown module
     * Need a class under app\Parser\ModuleParser
     * Need a method whatever module type is. for example if module type is MenuService,
     * you need to create a method called getMenuServiceModule(mixed $module) in ModuleParser Class
     */
    public function getUnknownModule(mixed $module): ?array
    {
        $dataType = $module->data_type;
        $moduleType = "get{$dataType}Module";
        $appNamespace = app()->getNamespace();
        $callableApp = $appNamespace . "Parser\ModuleParser";
        if (class_exists($callableApp) && method_exists($callableApp, $moduleType)) {
            $moduleParser = new $callableApp;
            $data = $moduleParser->{$moduleType}($module);

            return $this->manipulateModuleData($data, $module);
        }

        return [];
    }

    /**
     * Manipulate module data
     * added in v1.4.2
     */
    private function manipulateModuleData($data, $module_obj): mixed
    {
        $dataType = $module_obj->data_type;
        // has Parser\ModuleDataModifier ?
        // has Parser\ModuleDataModifier->moduleName();
        //based on ModuleAlias; MODULE_HEADER will be header() or MODULE_TOP_BANNER will be topBanner()
        // has Parser\ModuleDataModifier->moduleType();
        $moduleNameFn = Str::camel(strtolower($module_obj->alias));
        $moduleTypeFn = Str::camel(strtolower($dataType));

        $appNamespace = app()->getNamespace();
        $callableModifierApp = $appNamespace . "Parser\ModuleDataModifier";
        if (class_exists($callableModifierApp) && method_exists($callableModifierApp, $moduleNameFn)) {
            $moduleModifier = new $callableModifierApp;
            $data = $moduleModifier->{$moduleNameFn}($data, $module_obj);
        } elseif (class_exists($callableModifierApp) && method_exists($callableModifierApp, $moduleTypeFn)) {
            $moduleModifier = new $callableModifierApp;
            $data = $moduleModifier->{$moduleTypeFn}($data, $module_obj);
        }

        return $data;
    }

    /**
     * Get Module Data
     * It handles all type of module
     */
    public function getModuleData($module_obj): array|string|null
    {
        $dataType = $module_obj->data_type;
        $dataHandler = ($module_obj->data_handler == '' || $module_obj->data_handler == null) ? '' : $module_obj->data_handler;
        $methodType = $module_obj->method_type;
        $is_seo_module = $module_obj->is_seo_module;
        $is_mandatory = $module_obj->is_mandatory;
        $is_shared = $module_obj->shared;

        $moduleType = "get{$dataType}Module";

        //info("dataHandler: ".$dataHandler);
        $data = match (strtolower($dataType)) {
            'query', 'service', 'urlservice', 'queryservice', 'static', 'custom', 'servicelater' => $this->{$moduleType}($module_obj),
            default => $this->getUnknownModule($module_obj),
        };

        //Added in v1.4.2
        $data = $this->manipulateModuleData($data, $module_obj);

        //Save seo info
        if ($is_seo_module == 1 && $this->foundSeoModule == false) {

            $forSeo = (array) json_decode(json_encode($data), true);
            $forSeo = (count($forSeo) >= 1) ? $forSeo[0] : $forSeo;

            if (count($forSeo) > 0) {
                info('=== Set this data for seo module === ');
                //info(json_encode($forSeo));
                $this->foundSeoModule = true;
                $active_key = (isset($forSeo['active_key']) && !empty($forSeo['active_key'])) ? $forSeo['active_key'] : null;
                $page_link_rewrite = (isset($forSeo['link_rewrite']) && !empty($forSeo['link_rewrite'])) ? $forSeo['link_rewrite'] : null;
                $meta_title = (isset($forSeo['meta_title']) && !empty($forSeo['meta_title'])) ? $forSeo['meta_title'] : null;
                $meta_keywords = (isset($forSeo['meta_keywords']) && !empty($forSeo['meta_keywords'])) ? $forSeo['meta_keywords'] : null;
                $meta_description = (isset($forSeo['meta_description']) && !empty($forSeo['meta_description'])) ? $forSeo['meta_description'] : null;
                $meta_robots = (isset($forSeo['meta_robots']) && !empty($forSeo['meta_robots'])) ? $forSeo['meta_robots'] : null;
                $meta_canonical = (isset($forSeo['meta_canonical']) && !empty($forSeo['meta_canonical'])) ? $forSeo['meta_canonical'] : null;

                $this->seoModuleInfo = [
                    'metaTitle' => $meta_title,
                    'metaKeywords' => $meta_keywords,
                    'metaDescription' => $meta_description,
                    'metaRobots' => $meta_robots,
                    'metaCanonical' => $meta_canonical,
                    'activeKey' => $active_key,
                    'link_rewrite' => $page_link_rewrite,
                    'page_id' => $forSeo['id'],
                    'page_name' => $forSeo['name'],
                ];

                if (isset($forSeo['header_content'])) {
                    $this->seoModuleInfo['headerContent'] = (isset($forSeo['header_content']) && !empty($forSeo['header_content'])) ? $forSeo['header_content'] : '';
                    $this->seoModuleInfo['footerContent'] = (isset($forSeo['footer_content']) && !empty($forSeo['footer_content'])) ? $forSeo['footer_content'] : '';
                }
            }
        }

        // Is there any module that requires login?
        // let check it
        if (isset($data[0]) && isset($data[0]->required_login) && $data[0]->required_login == 1) {
            info('Login is required');
            $this->loginRequired = true;
        }

        // Is there any module is required in a category?
        // let's check it -- && self::getMandatoryCheck()
        if (($is_mandatory == 1 && is_array($data) && count($data) === 0)) {
            //dd("Mandatory content is missing");
            $this->contentFound = false;
        }

        //increase module count
        $this->totalModules++;

        //Check if this is sharable
        if ($is_shared == 1) {
            //info("module_obj->site_id ". json_encode($module_obj));
            $this->setSharedModuleData($module_obj->alias, $data);
        }

        try {
            //Dispatch Event
            ModuleLoaded::dispatch($module_obj, $data);
        } catch (\Exception $e) {
            info('ModuleLoaded Event Dispatch Error: ' . $e->getMessage());
        }

        return $data;
    }

    /**
     * Set shared module data
     */
    public function setSharedModuleData(string $alias, mixed $data): void
    {
        $infoLoader = app()->HashtagCms->infoLoader();
        $alias = $alias . '_' . $infoLoader->getContextVars(InfoKeys::SITE_ID);
        $this->sharedModuleData[$alias] = $data;
    }

    /**
     * Get shared module data
     */
    public function getSharedModuleData(string $alias): mixed
    {
        $infoLoader = app()->HashtagCms->infoLoader();
        $alias = $alias . '_' . $infoLoader->getContextVars(InfoKeys::SITE_ID);

        return (isset($this->sharedModuleData[$alias]) && !empty($this->sharedModuleData[$alias])) ? $this->sharedModuleData[$alias] : null;
    }

    /**
     * This is set when you call getModules();
     */
    public function getModulesCount(): int
    {
        return $this->totalModules;
    }

    /**
     * Get module info by alias
     */
    public function getModuleInfo(string $alias = '', bool $asArray = true): mixed
    {

        $where = [['alias', '=', $alias], ['site_id', '=', htcms_get_site_id()]];
        $moduleInfo = Module::withoutGlobalScopes()->where($where)->first();

        if ($asArray == true) {
            return $moduleInfo->toArray();
        }

        return $moduleInfo;
    }

    /**
     * If any module is enabled for seo
     */
    public function getSeoContent(): ?array
    {

        if ($this->foundSeoModule) {
            return $this->seoModuleInfo;
        }

        return null;
    }

    /**
     * Check if login is required
     */
    public function isLoginRequired(): bool
    {
        return $this->loginRequired;
    }

    /**
     * check if content is found
     * This works if module is flag as is_mandatory=1
     * and return data is zero
     */
    public function isContentFound(): bool
    {
        return $this->contentFound;
    }

    /**
     * Load module by alias
     */
    public function loadModuleByModuleAlias(string $alias = ''): array|string|null
    {
        $info = $this->getModuleInfo($alias, false);

        return $this->getModuleData($info);
    }

    /**
     * Get module props
     */
    public function getModuleProps(int $moduleId, int $siteId, int $platformId): array
    {
        $modulePropsData = ModuleProp::where([
            ['site_id', '=', $siteId],
            ['platform_id', '=', $platformId],
            ['module_id', '=', $moduleId],
        ])->get();

        $props = [];
        foreach ($modulePropsData as $key => $prop) {
            $props[$prop->name] = $prop->lang->value;
        }

        return $props;
    }
}
