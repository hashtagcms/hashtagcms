<?php

namespace HashtagCms\Core\Context;

class RequestContext
{
    public $request;
    public array $pathSegments = [];
    public ?array $configData = null;

    // Resolved Data
    public ?array $language = null;
    public ?array $platform = null;
    public ?array $category = null;
    
    // Controller Data
    public ?string $controllerName = null;
    public ?string $methodName = null;
    public bool $foundController = false;
    public bool $foundMethod = false;
    public ?string $callable = null; // Full Class Path
    public array $controllerParams = []; // Arguments for the method
    public array $controllerValue = []; // Original values from URL

    public ?array $siteData = null;
    public bool $isExternal = false;

    // Flags to help resolvers know what happened previously
    public bool $foundLangInUrl = false;
    public bool $foundPlatformInUrl = false;

    public function __construct($request)
    {
        $this->request = $request;
        $path = $request->path();
        $path = str_replace('//', '/', $path);
        
        // Handle root path
        if ($path === '/') {
            $this->pathSegments = [];
        } else {
            $this->pathSegments = explode('/', $path);
        }
    }
}
