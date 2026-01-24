<?php

namespace HashtagCms\Core\Context\Resolvers;

use HashtagCms\Core\Context\Contracts\ContextResolver;
use HashtagCms\Core\Context\RequestContext;
use Closure;

class PlatformResolver implements ContextResolver
{
    public function resolve(RequestContext $context, Closure $next)
    {
        $config = $context->configData;
        $request = $context->request;
        $pathSegments = $context->pathSegments; // This is already modified by LanguageResolver if needed

        $platformList = isset($config['platformData']) ? $config['platformData'] : $config['platforms'];
        $defaultData = $config['defaultData'];

        // 1. Check Header
        $headerPlatform = $request->header('x-platform');
        $platformData = ($headerPlatform) ? $this->findData($platformList, 'linkRewrite', $headerPlatform) : null;

        // 2. Check URL
        // If language was in URL (and removed), the start index is 0.
        // If language was NOT in URL, the start index is 0.
        // Wait, the original logic had complex index calculation because it didn't mutate the array until later.
        // Since LanguageResolver mutated (shifted) request->pathSegments, we can just look at index 0 again!
        
        if ($platformData == null && !empty($pathSegments)) {
            $possiblePlatform = $pathSegments[0];
            $foundInUrl = $this->findData($platformList, 'linkRewrite', $possiblePlatform);
            
            if ($foundInUrl) {
                $platformData = $foundInUrl;
                $context->foundPlatformInUrl = true;
            }
        }

        $foundPlatform = ($platformData != null);

        // 3. Set Default
        if (!$foundPlatform) {
            $platformData = $this->findData($platformList, 'id', $defaultData['platformId']);
        }

        $context->platform = $platformData;

        // Remove from path if found in URL and no header override
        if ($context->foundPlatformInUrl && empty($headerPlatform)) {
            array_shift($context->pathSegments);
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
