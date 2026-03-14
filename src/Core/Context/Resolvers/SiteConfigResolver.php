<?php

namespace HashtagCms\Core\Context\Resolvers;

use HashtagCms\Core\Context\Contracts\ContextResolver;
use HashtagCms\Core\Context\RequestContext;
use HashtagCms\Core\Main\DataLoader;
use HashtagCms\Core\Main\InfoLoader;
use HashtagCms\Core\Main\LayoutManager;
use HashtagCms\Core\Utils\LayoutKeys;
use HashtagCms\Core\Utils\RedisCacheManager;
use Illuminate\Support\Facades\Cache;
use HashtagCms\Core\Utils\CacheKeys;
use Closure;

class SiteConfigResolver implements ContextResolver
{
    protected DataLoader $dataLoader;
    protected InfoLoader $infoLoader;
    protected LayoutManager $layoutManager;

    public function __construct()
    {
        $this->dataLoader = app('HashtagCmsDataLoader');
        $this->infoLoader = app('HashtagCmsInfoLoader');
        $this->layoutManager = app('HashtagCmsLayoutManager');
    }

    /**
     * @param RequestContext $context
     * @param Closure $next
     * @return mixed
     */ 
    public function resolve(RequestContext $context, Closure $next)
    {
        $request = $context->request;
        
        $isExternal = app()->HashtagCms->useExternalApi();
        $this->infoLoader->setInfoKeeper(LayoutKeys::IS_EXTERNAL, $isExternal);
        $context->isExternal = $isExternal;

        // Domain Info
        $domain = $request->getHost();
        $fullDomain = htcms_get_domain_path();
        
        // Fetch context from config
        $domainList = config('hashtagcms.domains');
        $siteContext = $domainList[$domain] ?? '';

        if ($isExternal) {
            // External API Logic
            if (empty($siteContext)) {
                logger()->error('Unable to find context and domain mapping in config.');
                exit('Unable to find context and domain mapping in config.');
            }
            $configData = $this->loadConfig($siteContext, null, null, true);
            
            if ($configData == null) {
                logger()->error("was trying to loadConfig($siteContext)");
                exit('Unable to load config from api');
            }

        } else {
            // Local DB Logic
            $isSiteInstalled = $this->infoLoader->isSiteInstalled();
            
            if (!$isSiteInstalled) {
                 // In a real scenario we might redirect, but following original logic:
                 // should fetched from browser url and port
                exit('Site is not installed. Please visit ' . config('app.url') . '/install'); 

            }

            if(empty($siteContext)) {
                $siteContext = config('hashtagcms.context');
            }

            $siteDataInfo = $this->infoLoader->geSiteInfoByContextAndDomain($siteContext, $domain, $fullDomain);
            
            if ($siteDataInfo == null) {
                logger()->error("was trying to load infoLoader->geSiteInfoByContextAndDomain($siteContext, $domain, $fullDomain)");
                exit('Site not found');
            }
            
            $siteContext = $siteDataInfo->context;
            $configData = $this->loadConfig($siteContext, null, null, false);            
        }

        // Set Festival Info
        if (isset($configData['festivals'])) {
            $this->layoutManager->setFestivalObject($configData['festivals']);
        }

        // Check Status
        if (isset($configData['status']) && $configData['status'] != 200) {
            logger()->error($configData['message']);
            abort($configData['status'], $configData['message']);
        }

        $context->configData = $configData;
        $context->siteData = $configData['site'];

        // Check for maintenance mode
        if (isset($context->siteData['underMaintenance']) && $context->siteData['underMaintenance'] == 1) {
            $maintenanceConfig = config('hashtagcms.message.maintenance');
            
            $content = "Site is under maintenance";
            $status = 503; 

            if (!empty($maintenanceConfig)) {
                if (!empty($maintenanceConfig['view']) && view()->exists($maintenanceConfig['view'])) {
                    return response()->view($maintenanceConfig['view'], ['siteData' => $context->siteData], $status);
                }
                
                if (!empty($maintenanceConfig['html'])) {
                    $content = $maintenanceConfig['html'];
                }
            }
            
            // Wrap in full HTML to prevent server-side interception
            if (!str_contains($content, '<html')) {
                $content = "<!DOCTYPE html><html><head><title>Maintenance</title></head><body>$content</body></html>";
            }
            
            return response($content, $status)
                ->header('Content-Type', 'text/html')
                ->header('Retry-After', '3600');
        }

        return $next($context);
    }

    /**
     * Load config from external API or local database
     * @param string $context
     * @param ?string $lang
     * @param ?string $platform
     * @param bool $isExternal
     */
    private function loadConfig(string $context, ?string $lang, ?string $platform, bool $isExternal): ?array
    {
        $ttl = config('hashtagcms.cache_site_config_ttl', 30) * 60; // Minutes to seconds
        
        // Select prefix based on source
        $prefix = $isExternal 
            ? RedisCacheManager::getExternalSourcePrefix() 
            : RedisCacheManager::getDatabasePrefix();
            
        $cacheKey = $prefix . CacheKeys::SITE_CONFIG . "_{$context}";

        // Clear cache if requested
        if (request()->has('clear_cache') || request()->has('clearCache')) {
            Cache::forget($cacheKey);
        }

        // Define the data loading logic based on source
        $loaderCallback = function() use ($context, $lang, $platform, $isExternal) {
            return $isExternal 
                ? $this->dataLoader->loadConfigFromExternalApi($context, $lang, $platform)
                : $this->dataLoader->loadConfig($context, $lang, $platform);
        };

        // If cache is disabled, load directly
        if (config('hashtagcms.enable_cache') === false) {
            return $loaderCallback();
        }

        // Otherwise, cache the result
        return Cache::remember($cacheKey, $ttl, $loaderCallback);

    }
}
