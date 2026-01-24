<?php

namespace HashtagCms\Core\Main;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;
use HashtagCms\Core\Utils\LayoutKeys;
use Mockery\Exception;

class LayoutManager extends Results
{
    private InfoLoader $infoLoader;

    private array $layoutData = [];

    private array $viewAlias = [];

    private array $viewData = [];

    private string $themeFolder;

    private string $cssPath;

    private string $jsPath;

    private string $imgPath;

    private string $resourcePath;

    private string $resourceDir;

    private string $resourceUrl;

    private string $jsFolder;

    private string $cssFolder;

    private string $imageFolder;

    private array $backupAssetFolder = ['base_url' => '', 'base_path' => '/assets/hashtagcms/fe', 'js' => 'js', 'css' => 'css', 'image' => 'img'];

    private static bool $mandatoryModuleCheck = true;

    private array $directives = [];

    public function __construct()
    {
        parent::__construct();

        $this->infoLoader = app()->HashtagCmsInfoLoader;

        $this->themeFolder = config('hashtagcms.info.view_folder');
        $host = request()->getHost();
        $assetPath = config('hashtagcms.info.assets_path');
        //get domain wise or defautl one
        $assetPath = (isset($assetPath[$host])) ? $assetPath[$host] : $assetPath;

        //External url (CDN) is not setup.
        if (! isset($assetPath['base_url'])) {
            $assetPath = $this->backupAssetFolder;
        }

        $this->resourceUrl = $assetPath['base_url'];
        $this->resourceDir = $assetPath['base_path'];
        $this->jsFolder = $assetPath['js'];
        $this->cssFolder = $assetPath['css'];
        $this->imageFolder = $assetPath['image'];
    }

    /**
     * init
     *
     * @return void
     */
    public function init()
    {
        try {
            $context = $this->infoLoader->getInfoKeeper(LayoutKeys::CONTEXT);
            $lang = $this->infoLoader->getInfoKeeper(LayoutKeys::LANG_ISO_CODE);
            $platform = $this->infoLoader->getInfoKeeper(LayoutKeys::PLATFORM_LINKREWRITE);
            $categoryName = $this->infoLoader->getInfoKeeper(LayoutKeys::CATEGORY_NAME);
            $microsite = $this->infoLoader->getInfoKeeper(LayoutKeys::MICROSITE);
            $isExternal = $this->infoLoader->getInfoKeeper(LayoutKeys::IS_EXTERNAL);

            $dataLoader = app()->HashtagCms->dataLoader();

            //load data will be initiated from controller
            if ($isExternal) {
                $allData = $dataLoader->loadDataFromExternalApi($context, $lang, $platform, $categoryName, null);
            } else {
                $allData = $dataLoader->loadData($context, $lang, $platform, $categoryName, null);
            }

            //check if there is an error -- && $foundController==false
            if (isset($allData['status']) && $allData['status'] != 200) {
                return $allData;
            }
            if (isset($allData['totalModules']) && $allData['totalModules'] == 0) {
                exit(config('hashtagcms.message.zeroModuleSelected'));
            }

            //Set everything; this has to come before setting the controller info and then return the data

            $this->infoLoader->setLoadDataObjectAndEverything($allData);

            //Set everything for the layout
            $this->setLoadDataObjectAndEverything($allData);

        } catch (\Exception $exception) {
            $msg = "{$exception->getMessage()} in LayoutManager->init in {$exception->getFile()} @ lineNumber {$exception->getLine()}";
            logger()->error($msg);
            $allData['status'] = Response::HTTP_PRECONDITION_FAILED;
            $allData['message'] = $msg;
        }

        return $allData;
    }

    /**
     * Set object and make everything ready for the view
     *
     * @return void
     */
    public function setLoadDataObjectAndEverything(array $data)
    {

        $html = $data['html'];
        $meta = $data['meta'];
        $this->setHtmlObject($html);
        $this->setMetaObject($meta);

        //Set base index
        $this->setThemePath($meta['theme']['directory']);
        $this->setBaseIndex($meta['theme']);
        $this->parseSkeletonForView($meta['theme']);

    }

    /**
     * Set body content
     */
    public function setBodyContent(string $content): void
    {
        $this->setData(LayoutKeys::BODY_CONTENT, $content);
    }

    /**
     * get body content
     */
    public function getBodyContent(): string
    {
        return $this->getData(LayoutKeys::BODY_CONTENT);
    }

    /**
     * get header content
     */
    public function getHeaderContent(): string
    {
        return $this->infoLoader->getHeaderContent();
    }

    /**
     * Get footer content
     */
    public function getFooterContent(): string
    {
        return $this->infoLoader->getFooterContent();
    }

    /**
     * Set meta and html object
     */
    public function setFinalObject(array $data): void
    {
        $html = $data['html'];
        $meta = $data['meta'];
        $this->setHtmlObject($html);
        $this->setMetaObject($meta);

        $this->setBaseIndex($meta['theme']);

    }

    /**
     * Set base index
     */
    public function setBaseIndex(array $theme): void
    {
        $directory = $theme['directory'];
        $baseFolder = config('hashtagcms.info.view_folder');
        $viewName = $baseFolder.'.'.$directory.'/'.LayoutKeys::BASE_INDEX_FILE;
        $viewName = str_replace('/', '.', $viewName);
        $this->setData(LayoutKeys::BASE_INDEX, $viewName);

        //for service
        $viewName = $baseFolder.'.'.$directory.'/'.LayoutKeys::SERVICE_BASE_INDEX_FILE;
        $viewName = str_replace('/', '.', $viewName);
        $this->setData(LayoutKeys::SERVICE_BASE_INDEX, $viewName);
    }

    /**
     * Get base index file name
     */
    public function getBaseIndex(): string
    {
        return $this->getData(LayoutKeys::BASE_INDEX);
    }

    /**
     * Get base service index file name
     */
    public function getBaseServiceIndex(): string
    {
        return $this->getData(LayoutKeys::SERVICE_BASE_INDEX);
    }

    /**
     * Set meta object
     */
    public function setMetaObject(array $obj): void
    {
        $this->setData('meta', $obj);
    }

    /**
     * get meta object
     */
    public function getMetaObject(?string $key = null): mixed
    {
        return ! empty($key) ? $this->getData('meta')[$key] : $this->getData('meta');
    }

    /**
     * Set html object that being set after loadData
     *
     * @return void
     */
    public function setHtmlObject(array $obj)
    {
        $this->setData('html', $obj);
    }

    /**
     * This is html object
     */
    public function getHtmlObject(): mixed
    {
        return $this->getData('html');
    }

    /**
     * Set html data
     */
    public function setData(string $key, mixed $value): void
    {
        $this->layoutData[$key] = $value;
    }

    /**
     * Get html data
     */
    public function getData(string $key): mixed
    {
        return $this->layoutData[$key] ?? null;
    }

    /**
     * get meta content
     */
    public function getMetaContent(): string
    {

        $metaCanonical = $this->infoLoader->getMetaCanonical();
        $metaDescription = $this->infoLoader->getMetaDescription();
        $metaKewords = $this->infoLoader->getMetaKeywords();
        $metaRobots = $this->infoLoader->getMetaRobots();
        $favIcon = $this->infoLoader->getFavIcon();

        $metas = '';
        if ($favIcon != null) {
            $metas .= "<link rel='shortcut icon' href='$favIcon'>";
        }
        if ($metaDescription != null) {
            $metas .= "<meta name='description' content='$metaDescription'> ";
        }
        if ($metaKewords != null) {
            $metas .= "<meta name='keywords' content='$metaKewords'> ";
        }
        if ($metaRobots != null) {
            $metas .= "<meta name='robots' content='$metaRobots'> ";
        }
        if ($metaCanonical != null) {
            $metas .= "<link rel='canonical' href='$metaCanonical'> ";
        }

        return $metas;
    }

    /**
     * Get page title
     */
    public function getTitle(): string
    {
        return $this->infoLoader->getMetaTitle();
    }

    /**
     * Get theme object
     */
    public function getThemeObj(): array
    {
        return $this->getMetaObject('theme');
    }

    /**
     * Get view name | theme object must be defined
     */
    private function getViewName(?string $name = null): string
    {
        $name = ($name === null) ? '' : $name;
        $theme = $this->getThemeObj();
        $directory = $theme['directory'];
        //check if has an alias - means it will load module instead of specified in backend
        $alisView = $this->hasInAlias($name);
        $viewName = ($alisView != null) ? $alisView : $name;
        $themeFolder = $this->themeFolder;
        $viewName = $themeFolder.'.'.$directory.'.'.$viewName;

        //info("viewName: ".$viewName);
        return str_replace('/', '.', $viewName);
    }

    /**
     * Bind Data for a view
     *
     * @param  array  $data
     */
    public function bindDataForView(string $viewName, mixed $data = []): void
    {
        $viewName = str_replace('/', '.', $viewName);
        $this->viewData[$viewName] = $data;
    }

    /**
     * Get if module has an alternate module - This is mainly assign from Controller
     */
    public function hasInAlias(?string $viewName = null): mixed
    {
        $viewName = ($viewName === null) ? '' : $viewName;
        $viewName = str_replace('/', '.', $viewName);

        return isset($this->viewAlias[$viewName]) ? $this->viewAlias[$viewName]['name'] : null;
    }

    /**
     * Get Data for a view
     */
    public function getDataForView(string $viewName): mixed
    {
        $viewName = str_replace('/', '.', $viewName);

        //info("getDataForView ". $viewName." ". json_encode(isset($this->viewData[$viewName]) ? $this->viewData[$viewName] : array()));
        return isset($this->viewData[$viewName]) ? $this->viewData[$viewName] : [];
    }

    /**
     * Bind Data for a view
     */
    public function replaceViewWith(?string $sourceViewName = null, ?string $targetViewName = null, ?array $data = null): void
    {
        $sourceViewName = ($sourceViewName === null) ? '' : $sourceViewName;
        $targetViewName = ($targetViewName === null) ? '' : $targetViewName;
        $sourceViewName = str_replace('/', '.', $sourceViewName);
        if ($data != null) {
            $this->bindDataForView($sourceViewName, $data);
        }

        if (! empty($sourceViewName)) {
            $this->viewAlias[$sourceViewName] = ['name' => $targetViewName, 'data' => $data];
        }

    }

    /**
     * Get html content by html object (Fetch by all mdules/theme/hook)
     *
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function parseSkeletonForView(array $theme): string
    {

        $bodyContent = $theme['skeleton'];
        $hooks = $theme['hooks'];

        $modulesInTheme = isset($theme['modules']) ? $theme['modules'] : [];

        $allData = [];
        $infoKeeper = app()->HashtagCmsInfoLoader->getInfoKeeper();

        info('Parsing content');
        //info(json_encode($infoKeeper));
        try {
            foreach ($hooks as $key => $hook) {
                $placeholder = $hook['placeholder'];
                $modules = $hook['modules'];
                //making string
                if (! isset($allData[$placeholder])) {
                    $allData[$placeholder] = [];
                }
                //dd("modules", $modules);
                foreach ($modules as $index => $module) {
                    $viewData = $this->getParsedViewData($module, $infoKeeper);
                    $allData[$placeholder][] = $viewData;
                }
                //info("placeholder: ".$placeholder);
            }
        } catch (\Exception $exception) {
            logger()->error('Error while parsing: '.$exception->getMessage());
        }

        info('Parsing content end');
        //Parse module if it's is in theme
        foreach ($modulesInTheme as $index => $moduleT) {
            $placeholder = $moduleT['placeholder'];
            $viewData = $this->getParsedViewData($moduleT, $infoKeeper);
            $allData[$placeholder][] = $viewData;
        }
        // info("parseBodyContent: 3");
        //make string
        foreach ($allData as $placeholder => $data) {
            $bodyContent = str_replace($placeholder, implode('', $data), $bodyContent);
        }
        info('setting body content');
        //set in layout
        $bodyContent = $this->getEssentialsElements().$bodyContent;
        $this->setBodyContent($bodyContent);
        info('Setting body content done...');

        return $bodyContent;
    }

    /**
     * Get essentials elements
     */
    private function getEssentialsElements(): string
    {

        $message = session('__hashtagcms_message__');
        $messageError = session('__hashtagcms_message_error__', false);

        $message = ($messageError == false) ? $message : $messageError;

        $css = ($messageError == false) ? config('hashtagcms.redirect_with_message_design.css_success') : config('hashtagcms.redirect_with_message_design.css_error');

        if (is_array($message)) {
            $css = $message['type'];
            $message = $message['message'];
        }
        $css_close = config('hashtagcms.redirect_with_message_design.css_error_close_button');
        $error_close_text = config('hashtagcms.redirect_with_message_design.error_close_text');

        $finalMsg = '';
        if (! empty($message)) {
            $finalMsg = "<div class='$css'>$message <span style='float:right; cursor: pointer' onClick=\"document.getElementById('__hashtagcms__').style.display='none'\" ><i class='$css_close'>$error_close_text</i></span></div>";
        }

        return "<div id='__hashtagcms__'>$finalMsg</div>";
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getParsedViewDataFromMultipleModules(array $modules, array $infoKeeper): string
    {
        //dd("modules", $modules);
        $parseData = [];
        foreach ($modules as $index => $module) {
            $parseData[] = $this->getParsedViewData($module, $infoKeeper);
        }

        return implode('', $parseData);
    }

    /**
     * Get View Data
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getParsedViewData(array $module, array $infoKeeper = []): mixed
    {

        $viewData = '';
        $viewName = $this->getViewName($module['viewName']);
        $mergeData = $this->getDataForView($module['viewName']);
        try {
            if ($module['dataType'] == 'Static') {
                //View name is not needed for "Static" module
                $viewData = (isset($module['data']) && isset($module['data']['content'])) ? $module['data']['content'] : '';
                logger()->info("getParsedViewData: view start (static) : $module[alias]");
            } else {

                logger()->info("getParsedViewData: view start $viewName");
                if (View::exists($viewName)) {
                    try {
                        $moduleInfo = $module;
                        unset($moduleInfo['data']);
                        unset($moduleInfo['placeholder']);
                        $moduleData = isset($module['data']) ? $module['data'] : [];
                        //Handle query service
                        if ($module['dataType'] == 'QueryService') {
                            $moduleData['data'] = $moduleData;
                            $moduleData['queryData'] = isset($module['queryData']) ? $module['queryData'] : [];
                        }
                        $viewData = $this->viewMake($viewName, ['data' => $moduleData, 'infoKeeper' => $infoKeeper, 'moduleInfo' => $moduleInfo], $mergeData);

                    } catch (Exception $error) {
                        logger()->error('View Loading error: '.$error->getMessage());
                    }
                } else {
                    logger()->error("Unable to find view: $viewName");
                }
            }
        } catch (Exception $error) {
            logger()->error("getParsedViewData: Some parsing error: $viewName");
        }

        //logger()->info("getParsedViewData done");
        return $this->parseStringForView($viewData);
    }

    /**
     * Make view
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function viewMake(string $name, array $data = [], array $mergeData = []): mixed
    {
        $newData = array_merge($data['data'], $mergeData);
        $data['data'] = $newData;

        // Create a view instance without rendering it
        $view = view()->make($name, $data, $mergeData);

        // Get the view path to access the raw content before rendering
        try {
            $viewPath = $view->getPath();

            if (file_exists($viewPath)) {
                // Process the view content before rendering
                $rawContent = file_get_contents($viewPath);

                // Process @push directives if needed
                $pushPattern = "/@push\s*\(\s*['\"]([^'\"]+)['\"]\s*\)([\s\S]*?)@endpush/";
                preg_match_all($pushPattern, $rawContent, $pushMatches, PREG_SET_ORDER);

                if (!empty($pushMatches)) {
                    foreach ($pushMatches as $match) {
                        $stackName = $match[1];
                        $stackContent = trim($match[2]);

                        // Store the directive content for later use
                        $this->pushToDirectives([
                            'type' => 'push',
                            'name' => $stackName,
                            'content' => $stackContent
                        ]);
                    }
                }

                // Process @stack directives
                $stackPattern = "/@stack\s*\(\s*['\"]([^'\"]+)['\"]\s*\)/";
                preg_match_all($stackPattern, $rawContent, $stackMatches, PREG_SET_ORDER);

                if (!empty($stackMatches)) {
                    foreach ($stackMatches as $match) {
                        $stackName = $match[1];

                        // Store the stack directive for later use
                        $this->pushToDirectives([
                            'type' => 'stack',
                            'name' => $stackName,
                            'content' => ''
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            logger()->error('Error processing view directives: ' . $e->getMessage());
        }

        // Extract the view content as a string
        $viewData = $view->render();

        // Remove @push directives from the rendered content
        $pushPattern = "/@push\s*\(\s*['\"]([^'\"]+)['\"]\s*\)([\s\S]*?)@endpush/";
        $viewData = preg_replace($pushPattern, '', $viewData);

        // Remove @stack directives from the rendered content
        $stackPattern = "/@stack\s*\(\s*['\"]([^'\"]+)['\"]\s*\)/";
        $viewData = preg_replace($stackPattern, '', $viewData);

        //check if it has another module
        //@todo: We need another api here if it's external - make it data loader
        $pattern = "/\%{cms.module.+}\%/";
        preg_match_all($pattern, $viewData, $matches); //PREG_OFFSET_CAPTURE
        if (count($matches[0]) > 0) {
            $ml = app()->HashtagCms->moduleLoader();
            foreach ($matches[0] as $key => $val) {
                $val = preg_replace("/\%{cms.module.|}\%/", '', $val);
                $moduleInfo = $ml->getModuleInfo($val, false);
                $mData = $ml->getModuleData($moduleInfo);
                //dd($moduleInfo, $mData);
                $vData = view()->make($this->getViewName($moduleInfo->view_name), ['data' => $mData])->render();

                $viewData = str_replace("%{cms.module.$val}%", $vData, $viewData);
            }
        }

        return $viewData;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function viewMaster(string $view_name, mixed $data, mixed $merge_data, ?int $status = null)
    {
        $data = ['data' => $data];

        return response()->view($view_name, $data, 200);
    }

    /**
     * Set Theme Path
     */
    public function setThemePath(string $directory): void
    {
        //set theme path
        $this->resourcePath = $this->resourceDir.'/'.$directory;

        //css/js media path
        $this->cssPath = $this->resourcePath.'/'.$this->cssFolder;
        $this->jsPath = $this->resourcePath.'/'.$this->jsFolder;
        $this->imgPath = $this->resourcePath.'/'.$this->imageFolder;
    }

    /**
     * Parse String for path
     */
    public function parseStringForPath(?string $str = ''): string
    {

        if ($str != '' && $str != null) {
            $patterns = [];
            $patterns[0] = '/%{resource_path}%/';
            $patterns[1] = '/%{css_path}%/';
            $patterns[2] = '/%{js_path}%/';
            $patterns[3] = '/%{image_path}%/';

            $replacements = [];
            $replacements[0] = '/'.$this->getResourcePath();
            $replacements[1] = $this->getCssPath();
            $replacements[2] = $this->getJsPath();
            $replacements[3] = $this->getImagePath();
            //info("asset: this->getJsPath() ".asset($this->getJsPath()) ." === ". $str);

            $str = preg_replace($patterns, $replacements, $str);

            return $str;
        }

        return '';
    }

    /**
     * Parse string and load some view if needed
     *
     * @return string|string[]|null
     *
     * @todo: path etc handled: need to handle dynamic view and template and some php tags
     */
    public function parseStringForView(string $string = ''): array|string|null
    {
        return $this->parseStringForPath($string);
    }

    /**
     * get resource path
     */
    public function getResourcePath(): string
    {
        return $this->resourcePath;
    }

    /**
     * Get CSS Path
     */
    public function getCssPath(): string
    {
        return $this->cssPath;
    }

    /**
     * Get JS Path
     */
    public function getJsPath(): string
    {
        return $this->jsPath;
    }

    /**
     * Get Image path
     */
    public function getImagePath(): string
    {
        return $this->imgPath;
    }

    /**
     * Should use full path or simple url
     * 1. en/web/home - if true
     * 2. /home - if false
     */
    public function hasLangOrPlatformInUrl(): bool
    {
        // found lang and has in url too.
        $foundLang = $this->infoLoader->getInfoKeeper('foundLangInUrl');
        $foundPlatform = $this->infoLoader->getInfoKeeper('foundPlatformInUrl');
        return $foundLang || $foundPlatform;
    }

    /**
     * Make menu
     */
    private function makeMenu(array $data, bool $withLi = true, ?array $css = null, bool $isChild = false): string
    {

        $active = htcms_get_category_info('activeKey');
        $active_css = ($data['active_key'] == $active) ? ' '.$css['active'] : '';
        $otherAttributes = '';
        //$title = $data['title'];

        $link_rewrite = (isset($data['link_navigation']) && ! empty($data['link_navigation']) && $data['link_navigation'] != null) ? $data['link_navigation'] : $data['link_rewrite'];
        $link_rewrite = htcms_get_path($link_rewrite);

        $text = $data['name'];

        $liEnd = ($withLi == true) ? '</li>' : '';

        $dataCss = ($withLi == true) ? '' : ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ';

        $liCss = ($isChild == true) ? $css['childItem']['li'] : $css['item']['li'];
        $aCss = ($isChild == true) ? $css['childItem']['a'] : $css['item']['a'];

        if ($withLi == false) {
            $liCss = $css['itemWithChild']['li'];
            $aCss = $css['itemWithChild']['a'];
        }
        $fewParams = ['target' => 'target', 'link_relation' => 'rel', 'title' => 'title'];
        foreach ($fewParams as $param => $paramValue) {
            if (isset($data[$param]) && ! empty($data[$param])) {
                $otherAttributes .= " $paramValue='$data[$param]' ";
            }
        }

        $liCss = $liCss.$active_css;

        //$liStart = ($isChild == true) ? "" : "<li class='$liCss' $dataCss>";
        $liStart = "<li class='$liCss' $dataCss>";

        return "$liStart<a $otherAttributes class='$aCss' href='$link_rewrite'>$text</a>$liEnd";
    }

    /**
     * Get header Menu HTML
     */
    public function getHeaderMenuHtml(array $data, int $maxLimit = -1, ?array $css = null): string
    {

        $css = ($css != null) ? $css : ['item' => ['li' => 'nav-item', 'a' => 'nav-link'],
            'childItem' => ['li' => '', 'a' => 'dropdown-item'],
            'itemWithChild' => ['li' => 'nav-item dropdown', 'a' => 'nav-link dropdown-toggle', 'group' => 'dropdown-menu'],
            'active' => 'active',
        ];

        $menuMax = ($maxLimit == -1) ? count($data) : $maxLimit;
        $shouldAddMore = count($data) > $menuMax;
        $htmlMenu = [];
        foreach ($data as $index => $menu) {
            if ($shouldAddMore && $index == $menuMax - 1) {
                $htmlMenu[] = "<li class='".$css['itemWithChild']['li']."'><a data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' class='".$css['itemWithChild']['a']."' title='More' href='#'>More</a>";
                $htmlMenu[] = "<ul class='".$css['itemWithChild']['group']."'>";
            }
            $hasChild = false;
            if (isset($menu['child'])) {
                $hasChild = (count($menu['child']) > 0) ? true : false;
            }
            $htmlMenu[] = $this->makeMenu($menu, (($hasChild) ? false : true), $css, false);
            if ($hasChild) {
                $htmlMenu[] = "<ul class='".$css['itemWithChild']['group']."'>";
                foreach ($menu['child'] as $childMenu) {
                    $htmlMenu[] = $this->makeMenu($childMenu, true, $css, true);
                }
                $htmlMenu[] = '</ul></li>';
            }
            if ($shouldAddMore && $index == count($data) - 1) {
                $htmlMenu[] = '</ul></li>';
            }

        }

        return implode('', $htmlMenu);
    }

    /*** Unused ***/

    /**
     * Get Header menu
     *
     * @return array
     */
    public function getHeaderMenu(?string $active = null)
    {

        $active = (! empty($active)) ? '' : $active;
        $query = 'select c.id, c.parent_id, c.is_new, c.has_wap, c.wap_url, c.link_rewrite, 
                    c.link_navigation, cl.name, cl.title, cl.is_external, cs.position, cl.link_relation, 
                    cl.target, cl.active_key from categories c left join category_langs cl on (cl.category_id = c.id) 
                    left join category_site cs on (cs.category_id = c.id) where c.deleted_at is null and c.publish_status=1 
                    and c.site_id=:site_id and cl.lang_id=:lang_id and cs.platform_id=:platform_id and 
                    cs.exclude_in_listing = 0 order by cs.position asc';

        $data = $this->dbSelect($query);

        $sortedMenu = [];
        foreach ($data as $index => $menu) {
            $id = $menu->id;
            $key = 'menu_'.$id;
            $parent_id = $menu->parent_id;

            if ($parent_id == null || $parent_id == 0) {
                $sortedMenu[$key] = (array) $menu;
                if (! isset($sortedMenu[$key]['child'])) {
                    $sortedMenu[$key]['child'] = [];
                }
            }
            if ($parent_id > 0) {
                $sortedMenu['menu_'.$parent_id]['child'][] = (array) $menu;
            }

        }

        //make it sensible
        $allMenu = [];
        foreach ($sortedMenu as $m) {
            if (strtolower($m['active_key']) == strtolower($active)) {
                $m['is_active'] = true;
            }
            $allMenu[] = $m;
        }

        return $allMenu;
    }

    /**
     * Check module mandatory
     */
    public static function setMandatoryCheck(bool $checkMandatory = true)
    {
        self::$mandatoryModuleCheck = $checkMandatory;
    }

    /**
     * Get mandatory check
     */
    public static function getMandatoryCheck(): bool
    {
        return self::$mandatoryModuleCheck;
    }

    /** version 1.4.2 */
    /**
     * Set festival object
     */
    public function setFestivalObject(?array $festival): void
    {
        $this->setData(LayoutKeys::FESTIVAL_OBJ, $festival);
        $festival = is_array($festival) ? $festival[0] : $festival;
        $this->setFestivalCss($festival['bodyCss'] ?? '');
    }

    /**
     * Get festival object
     */
    public function getFestivalObject(): ?array
    {
        return $this->getData(LayoutKeys::FESTIVAL_OBJ) ?? null;
    }

    /**
     * Set CSS for body
     */
    public function setFestivalCss(string $content): void
    {
        $this->setData(LayoutKeys::FESTIVAL_CSS, $content);
    }

    /***
     * Get festival css for body
     * @return string
     */
    public function getFestivalCss(): string
    {
        return $this->getData(LayoutKeys::FESTIVAL_CSS) ?? '';
    }

    /**
     * Get background image
     */
    public function getBodyBackgroundImage(): string
    {
        $festival = $this->getFestivalObject();
        $festival = is_array($festival) ? $festival[0] : $festival;
        if ($festival != null && isset($festival['image'])) {
            return "background-image: url('".htcms_get_media($festival['image'])."')";
        }

        return '';
    }

    /**
     * Get theme folder
     *
     * @param  string|null  $name
     */
    public function getThemeFolder(): string
    {
        $theme = $this->getThemeObj();

        return $theme['directory'];
    }

    public function getViewThemeFolder(): string
    {
        $themeFolder = $this->getThemeFolder();

        return 'hashtagcms::fe.'.$themeFolder;
    }


    /** stacks */
    //@push, @stack, @section, @yield
    public function pushToDirectives($content) {
        array_push($this->directives, $content);
    }

    /**
     * Get all directives
     *
     * @return array
     */
    public function getDirectives() {
        return $this->directives;
    }

    /**
     * Get all directives of a specific type
     *
     * @param string $type The directive type (push, stack, etc.)
     * @return array
     */
    public function getDirectivesByType(string $type) {
        return array_filter($this->directives, function($directive) use ($type) {
            return isset($directive['type']) && $directive['type'] === $type;
        });
    }

    /**
     * Render stack content for a specific stack name
     *
     * @param string $name The stack name
     * @return string The combined content of all pushes to this stack
     */
    public function renderStack(string $name) {
        $pushes = array_filter($this->directives, function($directive) use ($name) {
            return isset($directive['type']) && $directive['type'] === 'push' &&
                isset($directive['name']) && $directive['name'] === $name;
        });

        $content = '';
        foreach ($pushes as $push) {
            $content .= $push['content'] . "\n";
        }
        $content = $this->parseStringForPath($content);

        return $content;
    }
}
