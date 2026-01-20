<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$callable = config('hashtagcms.namespace') . "Http\Controllers\Api\\";

/**
 * Health check
 */
Route::get('api/hashtagcms/health-check', function (Request $request) {
    return ['result' => 'okay'];
});
//'api', 'etag' - will add etag later
Route::middleware(['api'])->prefix('api/hashtagcms/public')->group(function () use ($callable) {

    /**
     * Registration: V1
     */
    Route::post('user/v1/register', function (Request $request) use ($callable) {
        return app()->call($callable . 'AuthController@register');
    });

    /**
     * Login: V1
     */
    Route::post('user/v1/login', function (Request $request) use ($callable) {
        //return array("result"=>"okay");
        return app()->call($callable . 'AuthController@login');
    });

    /**
     * Site config
     */
    Route::get('configs/v1/site-configs', function (Request $request) use ($callable) {

        return app()->call($callable . 'ServiceController@siteConfigs');

    });

    /**
     * Load data
     */
    Route::get('sites/v1/load-data', function (Request $request) use ($callable) {

        return app()->call($callable . 'ServiceController@loadData');

    });

    /**
     * Load data for mobile
     */
    Route::get('sites/v1/load-data-mobile', function (Request $request) use ($callable) {

        return app()->call($callable . 'ServiceController@loadDataMobile');

    });

    /**
     * Load a module
     */
    Route::get('service/v1/load-module', function (Request $request) {

        return ['result' => 'will be available in later version'];

    });

    /**
     * Load a hook
     */
    Route::get('service/v1/load-hook', function (Request $request) {

        return ['result' => 'will be available in later version'];

    });



    /**
     * Blog: Get Latests
     */
    Route::get('sites/v1/blog/latests', function (Request $request) use ($callable) {
        return app()->call($callable . 'ServiceController@blogLatests');
    });

    /**
     * Analytics: Publish
     */
    Route::middleware([config('hashtagcmsapi.throttle_analytics', 'throttle:60,1')])->post('kpi/v1/publish', function (Request $request) use ($callable) {
        return app()->call($callable . 'Kpi\AnalyticsController@publish');
    });

    /**
     * Contact: Submit
     */
    Route::middleware([config('hashtagcmsapi.throttle_contact', 'throttle:5,1')])->post('common/v1/contact', function (Request $request) use ($callable) {
        return app()->call($callable . 'CommonController@contact');
    });

    /**
     * Subscribe: Submit
     */
    Route::middleware([config('hashtagcmsapi.throttle_subscribe', 'throttle:10,1')])->post('common/v1/subscribe', function (Request $request) use ($callable) {
        return app()->call($callable . 'CommonController@subscribe');
    }); 

});


/**
 * Private routes
 * You should protect these url under VPN
 */
Route::middleware(['api', 'auth:sanctum'])->prefix('api/hashtagcms/private')->group(function () use ($callable) {

    Route::middleware([config('hashtagcmsapi.throttle_admin', 'throttle:60,1')])->group(function () use ($callable) {
        /**
         * Cache: List Keys
         */
        Route::get('cache/v1/keys', function (Request $request) use ($callable) {
            return app()->call($callable . 'CacheController@index');
        });

        /**
         * Cache: Clear Site Config
         */
        Route::get('cache/v1/clear-site-config', function (Request $request) use ($callable) {
            return app()->call($callable . 'CacheController@clearSiteConfig');
        });

        /**
         * Cache: Clear Load Data
         */
        Route::get('cache/v1/clear-load-data', function (Request $request) use ($callable) {
            return app()->call($callable . 'CacheController@clearLoadData');
        });

        /**
         * Cache: Clear All
         */
        Route::get('cache/v1/clear-all', function (Request $request) use ($callable) {
            return app()->call($callable . 'CacheController@clearAll');
        });

        /**
         * Cache: Clear Key
         */
        Route::get('cache/v1/clear-key', function (Request $request) use ($callable) {
            return app()->call($callable . 'CacheController@clearKey');
        });
    });

});

//Authentication
Route::middleware(['api', 'auth:sanctum'])->prefix('api/hashtagcms/user')->group(function () use ($callable) {

    Route::get('v1/me', function (Request $request) use ($callable) {

        return app()->call($callable . 'AuthController@me');

    });

    Route::middleware([config('hashtagcmsapi.throttle_profile', 'throttle:5,1')])->post('v1/profile', function (Request $request) use ($callable) {
        return app()->call($callable . 'AuthController@updateProfile');
    });

    Route::post('v1/logout', function (Request $request) use ($callable) {

        return app()->call($callable . 'AuthController@logout');

    });

});
