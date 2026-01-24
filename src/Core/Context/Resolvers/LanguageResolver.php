<?php

namespace HashtagCms\Core\Context\Resolvers;

use HashtagCms\Core\Context\Contracts\ContextResolver;
use HashtagCms\Core\Context\RequestContext;
use Closure;

class LanguageResolver implements ContextResolver
{
    public function resolve(RequestContext $context, Closure $next)
    {
        $config = $context->configData;
        $request = $context->request;
        $pathSegments = $context->pathSegments;

        $langList = isset($config['lang']) ? $config['lang'] : $config['langs'];
        $defaultData = $config['defaultData'];
        
        // 1. Check Header
        $headerLang = $request->header('x-lang');
        $langData = ($headerLang) ? $this->findData($langList, 'isoCode', $headerLang) : null;
        
        // 2. Check URL (First Segment)
        // Only if not found in header
        if ($langData == null && !empty($pathSegments)) {
             $possibleLang = $pathSegments[0];
             $foundInUrl = $this->findData($langList, 'isoCode', $possibleLang);
             
             if ($foundInUrl) {
                 $langData = $foundInUrl;
                 $context->foundLangInUrl = true;
                  // We do NOT remove the segment yet, we just mark it.
                  // Removing modifies the index for Platform check if we are strictly following index logic.
             }
        }

        $foundLang = ($langData != null);

        // 3. Set Default if not found
        if (!$foundLang) {
            $langData = $this->findData($langList, 'id', $defaultData['langId']);
        }

        $context->language = $langData;

        // If found in URL, we need to shift the path segment provided the header didn't take precedence
        // Actually, strictly speaking about URL structure: if it IS in the URL, we should shift it regardless 
        // if we used the header or not, otherwise the controller logic will break thinking 'en' is a category.
        // BUT the original logic says: "Remove lang if found in URL (not header)"
        
        // Wait! The original logic says:
        // if ($foundLang && empty($headerLang)) { array_shift($path_arr); }
        // So if I send x-lang: en, and url is /es/blog, it might get confused?
        // Original logic prioritizes header for *identification*, but only shifts URL if header is empty.
        // This implies if header is set, we treat the URL as pure content path? 
        // Let's stick to the original logic:
        
        if ($context->foundLangInUrl && empty($headerLang)) {
            array_shift($context->pathSegments); // Modify the context path
        }

        return $next($context);
    }

    private function findData(array $arr, string $key, mixed $val): ?array
    {
        foreach ($arr as $item) {
            if (isset($item[$key]) && $item[$key] === $val) {
                return $item;
            }
        }
        return null;
    }
}
