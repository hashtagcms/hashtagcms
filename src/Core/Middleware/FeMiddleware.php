<?php

namespace HashtagCms\Core\Middleware;

use Closure;
use HashtagCms\Core\Middleware\Traits\BaseInfo;

/**
 * Frontend Interceptor Middleware
 * Registered as 'interceptor' => HashtagCms\Http\Middleware\FeMiddleware::class
 */
class FeMiddleware
{
    use BaseInfo;

    protected $request;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->request = $request;

        info('================== FeMiddleware: ['.$request->path().'] ====================');
        $this->process($this->request);
        info('================== End============================================');

        return $next($this->request);
    }

    /**
     * Check if request should be processed
     *
     * @return bool
     */
    private function shouldProcess($path)
    {

        //Handle something special
        return true;

    }

    private function process($request)
    {

        $path = $request->path();

        if ($this->shouldProcess($path)) {

            $this->setBaseInfo($request);

        }

    }
}
