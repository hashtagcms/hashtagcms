<?php

namespace HashtagCms\Core\Context\Contracts;

use HashtagCms\Core\Context\RequestContext;
use Closure;

interface ContextResolver
{
    /**
     * Resolve a specific part of the request context.
     *
     * @param RequestContext $context
     * @param Closure $next
     * @return mixed
     */
    public function resolve(RequestContext $context, Closure $next);
}
