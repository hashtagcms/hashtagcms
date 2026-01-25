<?php

namespace HashtagCms\Core\Main;

use HashtagCms\Core\Utils\InfoKeys;
use HashtagCms\Core\Utils\LayoutKeys;
use HashtagCms\Models\Category;
use HashtagCms\Models\Lang;
use HashtagCms\Models\Platform;
use HashtagCms\Models\Site;
use HashtagCms\Models\SiteProp;
use HashtagCms\Models\Theme;

class InfoLoader
{
    private Site $siteInfo;

    private Lang $langInfo;

    private Platform $platformInfo;

    private Category $categoryInfo;

    private array $infoKeeper;

    private array $contextVars;

    private array $fullInfoKeeper;

    private array $layoutKeeper;

    protected SessionManager $sessionManager;

    protected $dataLoader;

    /** v2 */
    private array $infoData = [];

    private array $loadData = [];

    public function __construct()
    {
        $this->sessionManager = app()->HashtagCms->sessionManager();

    }

    /**
     * Site installed
     */
    public function isSiteInstalled(): bool
    {
        $siteProp = SiteProp::where('name', '=', 'site_installed')->first();

        return $siteProp ? $siteProp['value'] == 1 : false;
    }

    /**
     * Get Site Info by domain and context
     */
    public function geSiteInfoByContextAndDomain(string $context = '', string $domain = '', string $fullDomain = ''): ?Site
    {
        $siteInfo = Site::where('context', '=', $context)
            ->orWhere('domain', '=', $domain)
            ->orWhere('domain', '=', $fullDomain)
            ->with(['platform', 'language'])->first();

        return $siteInfo != null ? $siteInfo : null;
    }

    /**
     * Set Info Keeper
     */
    public function setInfoKeeper($key, $value)
    {
        $this->sessionManager->setInfoKeeper($key, $value);
    }

    /**
     * Get Info Keeper
     *
     * @param  null  $key
     */
    public function getInfoKeeper($key = null): mixed
    {
        return $this->sessionManager->getInfoKeeper($key);
    }

    /**
     * Has in infoKeeper
     */
    public function hasInInfoKeeper($key): mixed
    {
        return $this->infoKeeper[$key] ?? null;
    }

    /**
     * This is used to replace value in db query
     */
    public function setContextVars(string $key, mixed $value): void
    {
        $this->contextVars[":$key"] = ['key' => $key, 'value' => $value];
    }

    /**
     * Get context vars
     */
    public function getContextVars(string $key): mixed
    {
        return $this->contextVars[$key] ?? null;
    }

    /**
     * Set Language Id
     *
     * @param  null  $locale
     */
    public function setLanguageId(int $lang_id = 1, $locale = null): void
    {
        $this->setContextVars(InfoKeys::LANG_ID, $lang_id);
        if ($locale != null) {
            app()->setLocale($locale);
        }
    }

    /**
     * Get Language Id
     */
    public function getLanguageId(): int
    {
        return $this->getContextVars(InfoKeys::LANG_ID);
    }

    public function getObjInfo(string $key, ?string $subKey = null): mixed
    {
        if ($subKey == null) {
            return $this->fullInfoKeeper[$key] ?? null;
        }

        return $this->fullInfoKeeper[$key][$subKey] ?? null;
    }

    public function setObjInfo(mixed $key, mixed $value = null): void
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->fullInfoKeeper[$k] = $v;
            }
        } else {
            $this->fullInfoKeeper[$key] = $value;
        }
    }

    /**
     * Set context variable to replace in query etc
     */
    public function setMultiContextVars(int $category_id, int $site_id, int $platform_id, int $microsite_id = 0): void
    {
        $this->setContextVars(InfoKeys::CATEGORY_ID, $category_id);
        $this->setContextVars(InfoKeys::SITE_ID, $site_id);
        $this->setContextVars(InfoKeys::PLATFORM_ID, $platform_id);
        $this->setContextVars(InfoKeys::MICROSITE_ID, $microsite_id);
    }

    /**
     * Set layout info
     */
    public function setLayoutInfo(string $key, mixed $value): void
    {
        $this->layoutKeeper[$key] = $value;
    }

    public function getLayoutInfo(string $key): mixed
    {
        return $this->layoutKeeper[$key] ?? null;
    }

    /** v2 */

    /**
     * Set site data
     */
    public function setSiteData(array $siteData): void
    {
        $this->infoData[InfoKeys::SITE_DATA] = $siteData;
    }

    /**
     * Get site data
     */
    public function getSiteData(): array
    {
        return $this->infoData[InfoKeys::SITE_DATA];
    }

    /**
     * Set platform data
     */
    public function setPlatformData(array $platfomData): void
    {
        $this->infoData[InfoKeys::PLATFORM_DATA] = $platfomData;
    }

    /**
     * Get platofrm data
     */
    public function getPlatformData(): array
    {
        return $this->infoData[InfoKeys::PLATFORM_DATA];
    }

    /**
     * Set lang data
     */
    public function setLangData(array $langData): void
    {
        $this->setLanguageId($langData['id'], $langData['isoCode']);
        $this->infoData[InfoKeys::LANG_DATA] = $langData;
    }

    /**
     * Get lang data
     */
    public function getLangData(): array
    {
        return $this->infoData[InfoKeys::LANG_DATA];
    }

    /**
     * Set category data
     */
    public function setCategoryData(array $categoryData): void
    {
        $this->infoData[InfoKeys::CATEGORY_DATA] = $categoryData;
    }

    /**
     * Get category data
     */
    public function getCategoryData(): ?array
    {
        return $this->infoData[InfoKeys::CATEGORY_DATA] ?? null;
    }

    /**
     * Set page data
     */
    public function setPageData(array $pageData): void
    {
        $this->infoData[InfoKeys::PAGE_DATA] = $pageData;
    }

    /**
     * Get page data
     */
    public function getPageData(): ?array
    {
        return $this->infoData[InfoKeys::PAGE_DATA] ?? null;
    }

    /**
     * Set theme data
     */
    public function setThemeData(array $themeData): void
    {
        $this->infoData[InfoKeys::THEME_DATA] = $themeData;
    }

    /**
     * Get theme data
     */
    public function getThemeData(): array
    {
        return $this->infoData[InfoKeys::THEME_DATA];
    }

    /**
     * Set site props
     */
    public function setSitePropsData(array $sitePropsData): void
    {
        $this->infoData[InfoKeys::SITE_PROP_DATA] = $sitePropsData;
    }

    /**
     * Get site props
     */
    public function getSitePropsData(): array
    {
        return $this->infoData[InfoKeys::SITE_PROP_DATA];
    }

    /**
     * Get site props as key val
     */
    public function getSitePropsDataKeyVal(): array
    {
        $props = [];
        $siteProps = $this->getSitePropsData();
        foreach ($siteProps as $key => $val) {
            $props[$val['name']] = $val['value'];
        }

        return $props;
    }

    /**
     * Working here
     *
     * @return string
     */
    private function addDomainInCssAndJsPath(string $content)
    {
        $regex = '/(?:href|src)=["\']([^"\']+\.css|[^"\']+\.js)["\']/i';
        preg_match_all($regex, $content, $matches);

        $isExternal = app()->HashtagCms->useExternalApi();
        $host = request()->getHost();
        $assetPath = config('hashtagcms.info.assets_path');
        //get domain wise or defautl one
        $assetPath = (isset($assetPath[$host])) ? $assetPath[$host] : $assetPath;
        $baseUrl = $assetPath['base_url'];
        //dd($matches[1]);
        foreach ($matches[1] as $index => $match) {
            $str = $baseUrl.$match;
            $content = str_replace($match, $str, $content);
        }
        info('============= ');

        return $content;
    }

    /**
     * Set header content
     */
    public function setHeaderContent(array $headerContentData): void
    {
        $content = $headerContentData[0]['html'];
        $this->infoData[InfoKeys::HEADER_CONTENT] = $this->addDomainInCssAndJsPath($content);
    }

    /**
     * Get header content
     */
    public function getHeaderContent(): string
    {
        return $this->infoData[InfoKeys::HEADER_CONTENT];
    }

    /**
     * Set footer content
     */
    public function setFooterContent(array $footerContent): void
    {
        $content = $footerContent[0]['html'];
        //$content .= $content;
        //$content .= $content;
        $this->infoData[InfoKeys::FOOTER_CONTENT] = $this->addDomainInCssAndJsPath($content);
    }

    /**
     * Get footer content
     */
    public function getFooterContent(): string
    {
        return $this->infoData[InfoKeys::FOOTER_CONTENT];
    }

    /**
     * Set meta title
     */
    public function setMetaTitle(string $metaTitle): void
    {
        $this->infoData[InfoKeys::META_TITLE] = $metaTitle;
    }

    /**
     * Get meta title
     */
    public function getMetaTitle(): string
    {
        return $this->infoData[InfoKeys::META_TITLE];
    }

    /**
     * Set meta canonical
     */
    public function setMetaCanonical(?string $metaCanonical = null): void
    {
        $this->infoData[InfoKeys::META_CANONICAL] = $metaCanonical;
    }

    /**
     * Get meta canonical
     */
    public function getMetaCanonical(): ?string
    {
        return $this->infoData[InfoKeys::META_CANONICAL];
    }

    /**
     * Set meta description
     */
    public function setMetaDescription(?string $metaDescription = null): void
    {
        $this->infoData[InfoKeys::META_DESCRIPTION] = $metaDescription;
    }

    /**
     * Get meta description
     */
    public function getMetaDescription(): ?string
    {
        return $this->infoData[InfoKeys::META_DESCRIPTION];
    }

    /**
     * Set meta keywords
     */
    public function setMetaKeywords(?string $metaKeywords = null): void
    {
        $this->infoData[InfoKeys::META_KEYWORDS] = $metaKeywords;
    }

    /**
     * Get meta keywords
     */
    public function getMetaKeywords(): ?string
    {
        return $this->infoData[InfoKeys::META_KEYWORDS];
    }

    /**
     * Set meta robots
     */
    public function setMetaRobots(string $metaRobots): void
    {
        $this->infoData[InfoKeys::META_ROBOTS] = $metaRobots;
    }

    /**
     * Get meta robots
     */
    public function getMetaRobots(): ?string
    {
        return $this->infoData[InfoKeys::META_ROBOTS];
    }

    /**
     * Set fav icon
     */
    public function setFavIcon(string $favicon): void
    {
        $this->infoData[InfoKeys::FAV_ICON] = $favicon;
    }

    /**
     * Get fav icon
     */
    public function getFavIcon(): ?string
    {
        return $this->infoData[InfoKeys::FAV_ICON];
    }

    /**
     * Set theme skeleton
     */
    public function setThemeSkeleton(string $skeleton): void
    {
        $this->infoData[InfoKeys::THEME_SKELETON] = $skeleton;
    }

    /**
     * Get theme skeleton
     */
    public function getThemeSkeleton(): string
    {
        return $this->infoData[InfoKeys::THEME_SKELETON];
    }

    /**
     * Set everything for later use
     */
    public function setLoadDataObjectAndEverything(array $loadDataObject): void
    {
        $this->loadData = $loadDataObject;

        $meta = $loadDataObject['meta'];
        $html = $loadDataObject['html'];

        $this->setObjInfo('htmlObject', $html);
        $this->setObjInfo('metaObject', $meta);

        //set everything now
        $this->setSiteData($meta['site']);
        $this->setPlatformData($meta['platform']);
        $this->setLangData($meta['lang']);
        $this->setCategoryData($meta['category']);
        $this->setPageData($meta['page']);
        $this->setThemeData($meta['theme']);
        $this->setSitePropsData($meta['props']);
        $this->setHeaderContent($html['head']['headerContent']);
        $this->setFooterContent($html['body']['footer']['footerContent']);
        $this->setMetaTitle($html['head']['title']);
        $this->setMetaCanonical($html['head']['meta']['metaCanonical']);
        $this->setMetaDescription($html['head']['meta']['metaDescription']);
        $this->setMetaKeywords($html['head']['meta']['metaKeywords']);
        $this->setMetaRobots($html['head']['meta']['metaRobots']);
        $this->setFavIcon($html['head']['links'][0]['href'] ?? '');
        $this->setThemeSkeleton($html['body']['content']['skeleton']);
    }

    /**
     * Get site context after process
     */
    public function getContext(): string
    {
        return $this->getInfoKeeper(LayoutKeys::CONTEXT);
    }

    /**
     * Get site id
     */
    public function getSiteId(): int
    {
        return $this->getInfoKeeper(LayoutKeys::SITE_ID);
    }

    /**
     * Get lang iso code
     */
    public function getLangIsoCode(): string
    {
        return $this->getInfoKeeper(LayoutKeys::LANG_ISO_CODE);
    }

    /**
     * Get site id
     */
    public function getLangId(): int
    {
        return $this->getInfoKeeper(LayoutKeys::LANG_ID);
    }

    /**
     * Get platform linkrewrite
     */
    public function getPlatformLinkrewrite(): string
    {
        return $this->getInfoKeeper(LayoutKeys::PLATFORM_LINKREWRITE);
    }

    /**
     * Get category linkrewrite
     */
    public function getCategoryName(): string
    {
        return $this->getInfoKeeper(LayoutKeys::CATEGORY_NAME);
    }

    /**
     * get microsite name
     */
    public function getMicrositeName(): ?string
    {
        return $this->getInfoKeeper(LayoutKeys::MICROSITE) ?? null;
    }

    /**
     * Get callable for route
     */
    public function getAppCallable(): string
    {
        return $this->getInfoKeeper(LayoutKeys::CALLABLE_CONTROLLER) ?? '';
    }

    /**
     * Get callable values for route
     */
    public function getAppCallableValue(): mixed
    {
        return $this->getInfoKeeper(LayoutKeys::CONTROLLER_VALUE) ?? [];
    }
}
