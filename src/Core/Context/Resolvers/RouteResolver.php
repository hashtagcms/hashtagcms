<?php

namespace HashtagCms\Core\Context\Resolvers;

use HashtagCms\Core\Context\Contracts\ContextResolver;
use HashtagCms\Core\Context\RequestContext;
use HashtagCms\Core\Main\InfoLoader;
use HashtagCms\Core\Utils\LayoutKeys;
use Illuminate\Support\Str;
use Closure;
use ReflectionMethod;

class RouteResolver implements ContextResolver
{
    protected InfoLoader $infoLoader;
    protected array $configData;

    public function __construct(InfoLoader $infoLoader)
    {
        $this->infoLoader = $infoLoader;
    }

    public function resolve(RequestContext $context, Closure $next)
    {
        $this->configData = $context->configData;
        $pathSegments = array_values($context->pathSegments);

        // 1. Resolve Category Name
        $categoryName = $this->resolveCategoryName($pathSegments);
        $categoryInfo = $this->getCategoryData($categoryName);
        
        // Save to context
        $context->category = $categoryInfo;

        // 2. Resolve Controller and Method
        $route = $this->resolveControllerAndMethod($pathSegments, $categoryInfo, $categoryName);

        // 3. Resolve Parameters (Arguments)
        // Adjusts params based on what was found (Controller/Method) and maps via Reflection
        $finalParams = $this->resolveParameters($route, $pathSegments);

        // 4. Validate Dynamic Link Patterns (e.g. blog/{slug})
        if ($categoryInfo && !empty($categoryInfo['link_rewrite_pattern'])) {
            $this->validateDynamicPattern($categoryInfo['link_rewrite_pattern'], $finalParams, $route['foundController']);
        }

        // 5. Update InfoLoader
        $this->updateInfoLoader($route, $categoryName, $finalParams);

        // Explicitly set category name to the full path version
        $this->infoLoader->setInfoKeeper(LayoutKeys::CATEGORY_NAME, $categoryName);

        return $next($context);
    }

    /**
     * Determine the category name from path segments or defaults
     */
    private function resolveCategoryName(array $pathSegments): string
    {
        if (empty($pathSegments) || (count($pathSegments) === 1 && $pathSegments[0] === '')) {
            $defaultData = $this->configData['defaultData'];
            $categoryList = $this->configData['categories'];
            
            $categoryData = isset($defaultData['category']) 
                ? $defaultData['category'] 
                : collect($categoryList)->firstWhere('id', $defaultData['categoryId']);
                
            return $categoryData['linkRewrite'] ?? '/';
        }

        $name = implode('/', $pathSegments);
        return ($name === '') ? '/' : $name;
    }

    /**
     * Determine the Controller Class and Method based on the path
     */
    private function resolveControllerAndMethod(array $pathSegments, ?array $categoryInfo, string $categoryName): array
    {
        $pathLen = count($pathSegments);
        
        // Default Logic
        $controllerSegment = LayoutKeys::DEFAULT_CONTROLLER_NAME;
        $methodSegment = LayoutKeys::DEFAULT_METHOD_NAME;
        $methodNameParam = $methodSegment; // Default param value if not found

        // Logic to extract potential controller/method names from path
        if ($pathLen === 1) {
            $controllerSegment = $pathSegments[0]; // e.g. "blog"
        } elseif ($pathLen > 1) {
            $controllerSegment = $pathSegments[0]; // e.g. "blog"
            $methodSegment = $pathSegments[1];     // e.g. "story-slug"
            $methodNameParam = $pathSegments[1];   // Keep original for params logic
        }

        // Normalize Category Name for Controller Lookup (Legacy logic override)
        $lookupName = ($pathLen > 0) ? $pathSegments[0] : ($categoryName === '' ? '/' : $categoryName);
        $lookupName = ($lookupName === '/') ? LayoutKeys::DEFAULT_CONTROLLER_NAME : $lookupName;

        // Fetch Controller Class
        $resolution = $this->findControllerClass($categoryInfo, $lookupName, $methodSegment);
        
        // Add metadata used for parameter resolution
        $resolution['methodNameParam'] = $methodNameParam;
        
        return $resolution;
    }

    /**
     * Find class and method existence
     */
    private function findControllerClass(?array $categoryInfo, string $controllerName, string $methodName): array
    {
        // 1. Determine Expected Class Name
        if ($categoryInfo !== null) {
            $className = isset($categoryInfo['controllerName']) 
                ? $categoryInfo['controllerName'] 
                : str_replace('-', '', Str::title($controllerName)) . 'Controller';
        } else {
            $className = str_replace('-', '', Str::title($controllerName)) . 'Controller';
        }

        // 2. Resolve Namespace
        $namespace = config('hashtagcms.namespace');
        $appNamespace = app()->getNamespace();
        
        $callableVendor = $namespace . "Http\Controllers\\" . $className;
        $callableApp = $appNamespace . "Http\Controllers\\" . $className;
        $callableDefault = $namespace . "Http\Controllers\FrontendController";

        $finalCallable = class_exists($callableApp) ? $callableApp : $callableVendor;

        // 3. Verify Existence
        $data = [
            'foundController' => false,
            'foundMethod' => false,
            'callable' => $callableDefault,
            'method' => LayoutKeys::DEFAULT_METHOD_NAME
        ];

        if (class_exists($finalCallable)) {
            $data['foundController'] = true;
            $data['callable'] = $finalCallable;
            
            if (method_exists($finalCallable, $methodName)) {
                $data['foundMethod'] = true;
                $data['method'] = $methodName;
            }
        }

        return $data;
    }

    /**
     * Map available path segments to method arguments
     */
    private function resolveParameters(array $route, array $pathSegments): array
    {
        $paramsValues = [];
        $pathLen = count($pathSegments);

        // Adjust parameters based on path length
        if ($pathLen > 2) {
            $paramsValues = array_slice($pathSegments, 2);
        }

        // Adjust mapping if Controller/Method were NOT found
        if (!$route['foundController'] && !empty($pathSegments)) {
            $controllerSegment = ($pathLen > 0) ? $pathSegments[0] : LayoutKeys::DEFAULT_CONTROLLER_NAME;
            array_unshift($paramsValues, $controllerSegment, $route['methodNameParam']);
        }
        elseif ($route['foundController'] && !$route['foundMethod']) {
            array_unshift($paramsValues, $route['methodNameParam']);
        }

        // Map via Reflection
        $ref = new ReflectionMethod($route['callable'], $route['method']);
        $params = $ref->getParameters();
        $args = [];

        foreach ($params as $param) {
            // Regex for legacy compliance (prefer $param->getType() in future)
            preg_match('/<(required|optional)> (?:([\\\\a-z\d_]+) )?(?:\\$(\w+))(?: = (\S+))?/i', (string) $param, $matches);

            if (empty($matches[2])) { // Type is missing or mixed
                $args[$matches[3]] = array_shift($paramsValues);
            }
        }

        return array_merge($args, $paramsValues);
    }

    /**
     * Validate and processing dynamic link rewrites
     */
    private function validateDynamicPattern(string $pattern, array $values, bool $foundController)
    {
        $totalCount = preg_match_all("/\{*+\}/", $pattern, $matches);
        $optionalCount = preg_match_all("/\?}/", $pattern, $matches);
        $requiredCount = $totalCount - $optionalCount;

        // Validation
        if ($requiredCount !== 0 && $requiredCount < count($values) - 1) {
            info('Dynamic url is mismatched');
            abort(404, 'Dynamic url is mismatched');
        }

        // Prep values for Context Var mapping
        $valuesForContext = $values;
        if (!$foundController) {
            array_splice($valuesForContext, 0, 1);
        }

        $patterns = explode('/', $pattern);
        
        if (count($patterns) === count($values)) {
            foreach ($valuesForContext as $index => $lr) {
                $key = preg_replace("/\{|\}|\?/", '', $patterns[$index]);
                $this->infoLoader->setContextVars($key, $lr);
            }
        } else {
            $key = preg_replace("/\{|\}|\?/", '', $pattern);
            $this->infoLoader->setContextVars($key, implode('/', $valuesForContext));
        }
    }

    /**
     * Update InfoLoader with final resolved data
     */
    private function updateInfoLoader(array $route, string $categoryName, array $paramValues)
    {
        $this->infoLoader->setInfoKeeper(LayoutKeys::CONTROLLER_NAME, $route['callable']);
        $this->infoLoader->setInfoKeeper(LayoutKeys::METHOD_NAME, $route['method']);
        $this->infoLoader->setInfoKeeper(LayoutKeys::CATEGORY_NAME, $categoryName); // Safety set
        
        $this->infoLoader->setInfoKeeper(LayoutKeys::FOUND_CONTROLLER, $route['foundController']);
        $this->infoLoader->setInfoKeeper(LayoutKeys::FOUND_METHOD, $route['foundMethod']);
        
        $this->infoLoader->setInfoKeeper(LayoutKeys::CALLABLE_CONTROLLER, $route['callable'] . '@' . $route['method']);
        $this->infoLoader->setInfoKeeper(LayoutKeys::CONTROLLER_VALUE, $paramValues);
    }

    private function getCategoryData(string $categoryName)
    {
        // Optimized O(N) search without object overhead.
        // Faster than array_column or collect() for single lookups on unsorted data.
        foreach ($this->configData['categories'] as $item) {
            if (($item['linkRewrite'] ?? '') === $categoryName) {
                return $item;
            }
        }
        return null;
    }
}
