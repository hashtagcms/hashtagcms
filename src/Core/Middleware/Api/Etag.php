<?php

namespace HashtagCms\Core\Middleware\Api;

use Closure;
use Illuminate\Http\Request;

class Etag
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        //Keep it for set it later
        $originalMethod = $request->method();

        //Set the method type as get to enable eTAG
        $request->setMethod('get');

        //Get the response
        $response = $next($request);

        //Generate eTAG
        $eTag = md5(json_encode($response->headers->get('origin')).$response->getContent());

        //Load the eTAG sent by client
        $requestEtag = str_replace('"', '', $request->getETags());

        //Check to see if eTAG has changed
        if ($requestEtag && $requestEtag[0] == $eTag) {
            $response->setNotModified();
        }

        //Set Etag
        $response->setEtag($eTag);

        // Set back to original method
        $request->setMethod($originalMethod);

        //Send the response
        return $response;
    }
}
