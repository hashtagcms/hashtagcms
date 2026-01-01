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
     * Analytics: Publish
     */
    Route::post('kpi/v1/publish', function (Request $request) use ($callable) {
        return app()->call($callable . 'Kpi\AnalyticsController@publish');
    });

});

//Authentication
Route::middleware(['api', 'auth:sanctum'])->prefix('api/hashtagcms/user')->group(function () use ($callable) {

    Route::get('v1/me', function (Request $request) use ($callable) {

        return app()->call($callable . 'AuthController@me');

    });

    Route::post('v1/logout', function (Request $request) use ($callable) {

        return app()->call($callable . 'AuthController@logout');

    });

});
