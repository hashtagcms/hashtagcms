<?php

namespace HashtagCms\Core\Middleware\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use HashtagCms\Core\Main\DataLoader;
use HashtagCms\Core\Main\InfoLoader;
/**
 * Trait BaseInfo
 */
use HashtagCms\Core\Main\LayoutManager;
use HashtagCms\Core\Main\SessionManager;
use HashtagCms\Core\Utils\LayoutKeys;
use HashtagCms\Models\Category;

use HashtagCms\Core\Context\ContextPipeline;
use HashtagCms\Core\Context\Resolvers\SiteConfigResolver;
use HashtagCms\Core\Context\RequestContext;
use HashtagCms\Core\Context\Resolvers\LanguageResolver;
use HashtagCms\Core\Context\Resolvers\PlatformResolver;
use HashtagCms\Core\Context\Resolvers\RouteResolver;

trait BaseInfo
{
    protected InfoLoader $infoLoader;

    protected SessionManager $sessionManager;

    protected LayoutManager $layoutManager;

    protected DataLoader $dataLoader;

    protected $configData;

    /***
     * Web link could be
     * www.hashtagcms.org/en/web/home
     * en - language (optional)
     * web - platform  (optional)
     * home or / - category
     */

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function setBaseInfo($request)
    {

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            exit('Could not connect to the database.  Please check your configuration. Error: ' . $e->getMessage());
        }

        info('BaseInfo: Start Processing...');

        // ******************** Init Managers ********************* //
        $this->infoLoader = app()->HashtagCms->infoLoader();
        $this->layoutManager = app()->HashtagCms->layoutManager();
        $this->sessionManager = app()->HashtagCms->sessionManager();
        $this->dataLoader = app()->HashtagCms->dataLoader();


        // ******************** Pipeline Step 3: Route Resolution (Final) ********************* //

        $requestContext = new RequestContext($request);

        app(ContextPipeline::class)
            ->send($requestContext)
            ->through([
                SiteConfigResolver::class,
                LanguageResolver::class,
                PlatformResolver::class,
                RouteResolver::class,
            ])
            ->via('resolve')
            ->then(function (RequestContext $context) {
                // Finalize keys from context (Map to InfoLoader)
                $this->finalizeContext($context);
            });

    }

    /**
     * Map the resolved context back to the legacy InfoLoader storage
     * (Only strictly necessary parts from Context)
     */
    protected function finalizeContext(RequestContext $context)
    {
        $infoLoader = $this->infoLoader;

        // Site
        if ($context->siteData) {
            $infoLoader->setInfoKeeper(LayoutKeys::CONTEXT, $context->siteData['context']);
            $infoLoader->setInfoKeeper(LayoutKeys::SITE_ID, $context->siteData['id']);
        }

        // Lang
        if ($context->language) {
            $infoLoader->setInfoKeeper(LayoutKeys::FOUND_LANG, ($context->language != null));
            $infoLoader->setInfoKeeper('foundLangInUrl', $context->foundLangInUrl ?? false);
            
            $infoLoader->setInfoKeeper(LayoutKeys::LANG_ID, $context->language['id']);
            $infoLoader->setInfoKeeper(LayoutKeys::LANG_ISO_CODE, $context->language['isoCode']);
            $infoLoader->setLanguageId($context->language['id']);
        }

        // Platform
        if ($context->platform) {
            $infoLoader->setInfoKeeper(LayoutKeys::FOUND_PLATFORM, ($context->platform != null));
            $infoLoader->setInfoKeeper('foundPlatformInUrl', $context->foundPlatformInUrl ?? false);
            
            $infoLoader->setInfoKeeper(LayoutKeys::PLATFORM_ID, $context->platform['id']);
            $infoLoader->setInfoKeeper(LayoutKeys::PLATFORM_LINKREWRITE, $context->platform['linkRewrite']);
        }
        
        // MultiContextVars
        $defaultData = $context->configData['defaultData'];
        $microsite_id = 0; 
        
        $infoLoader->setMultiContextVars(
            $defaultData['categoryId'], 
            $context->siteData['id'], 
            $context->platform['id'], 
            $microsite_id
        );
    }    
    /**
     * Load config
     */
    public function loadConfig(string $context, ?string $lang, ?string $platform, bool $isExternal): ?array
    {

        if ($isExternal) {
            return $this->dataLoader->loadConfigFromExternalApi($context, $lang, $platform);
        }

        return $this->dataLoader->loadConfig($context, $lang, $platform);
    }


    protected function findData(array $arr, string $key, mixed $val): ?array
    {
        foreach ($arr as $item) {
             if (isset($item[$key]) && $item[$key] === $val) {
                 return $item;
             }
        }
        return null;
    }



}
