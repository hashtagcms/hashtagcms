<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use HashtagCms\Facades\HashtagCms;

if (HashtagCms::isInstallationRoutesEnabled()) {
    Route::get('/install', config('hashtagcms.namespace') . "Http\Controllers\Installer\InstallController@index");
    Route::post('/install/save', config('hashtagcms.namespace') . "Http\Controllers\Installer\InstallController@save");
}
// Get configuration values once outside the route to avoid calling on each request
$namespace = config('hashtagcms.namespace');
$appNamespace = app()->getNamespace();
$defaultPage = config('admin.cmsInfo.defaultPage', 'dashboard');
//Register Admin
Route::prefix('admin')->group(function () use ($namespace, $appNamespace, $defaultPage) {

    Route::match(['get', 'post', 'delete'], '{controller?}/{method?}/{params?}', function (Request $request, $controller = '', $method = '', $params = null) use ($namespace, $appNamespace, $defaultPage) {

        // Set controller only once
        $controller = ($controller === '') ? $defaultPage : $controller;

        $methodType = $request->method();

        //Hashtag Controller
        $callable = $namespace . "Http\Controllers\\Admin\\" . str_replace('-', '', Str::title($controller)) . 'Controller';
        //App Controller
        $callableApp = $appNamespace . "Http\Controllers\\Admin\\" . str_replace('-', '', Str::title($controller)) . 'Controller';

        $controllerName = class_exists($callableApp) ? $callableApp : $callable;

        if (class_exists($controllerName)) {

            $method = ($method === '' && $methodType === 'GET') ? 'index' : $method;

            $callable = $controllerName . '@' . $method;
            $values = explode('/', $params);
            $ref = new ReflectionMethod($controllerName, $method);
            $params = $ref->getParameters();
            $args = [];

            foreach ($params as $param) {
                // parse signature [match, optional, type, name, default]
                preg_match('/<(required|optional)> (?:([\\\\a-z\d_]+) )?(?:\\$(\w+))(?: = (\S+))?/i', (string) $param, $matches);

                // assign untyped segments
                if ($matches[2] == null) {
                    $args[$matches[3]] = array_shift($values);
                }
            }
            $values = array_merge($args, $values);

            try {
                // Only log in non-production environments
                if (app()->environment(['local', 'development', 'testing'])) {
                    Log::info('Admin route call', [
                        'method' => $methodType,
                        'controller' => $callable,
                        'parameters' => $values
                    ]);
                }

                return app()->call($callable, $values);

            } catch (Exception $e) {
                Log::error('Admin route error', [
                    'controller' => $controllerName,
                    'method' => $method,
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Don't expose error details in production
                if (app()->environment('production')) {
                    abort(500, 'Server Error');
                } else {
                    return [
                        'error' => $e->getMessage(),
                        'controller' => $controllerName,
                        'method' => $method
                    ];
                }
            }

        } else {
            abort(404);
        }

    })->middleware(['web', 'auth:sanctum', 'cmsModuleInfo', 'cmsInterceptor'])->where('params', '^((?!assets/).)*?');
});

if (HashtagCms::isRoutesEnabled()) {

    Route::match(['get', 'post', 'delete'], '{all?}', function (Request $request, $all = '/') {

        $infoLoader = app()->HashtagCms->infoLoader();

        $infoKeeper = $infoLoader->getInfoKeeper();

        $callable = $infoLoader->getAppCallable(); //Controller and method
        $values = $infoLoader->getAppCallableValue(); //controller params
        try {

            if ($callable != '') {
                return app()->call($callable, $values); //FrontendController@index, []
            } else {
                // Log with proper context
                Log::warning("No callable found for route", [
                    'path' => $request->path(),
                    'method' => $request->method()
                ]);

                try {
                    DB::connection()->getPdo();
                } catch (\Exception $e) {
                    Log::error('Database connection error', [
                        'message' => $e->getMessage()
                    ]);

                    // Don't expose error details in production
                    if (app()->environment(['production'])) {
                        abort(500, 'Server Error');
                    } else {
                        return "RouteError: I don't know what to process...";
                    }
                }

                return "RouteError: I don't know what to process...";
            }

        } catch (Exception $exception) {
            // Don't expose error details in production
            if (app()->environment(['production'])) {
                abort(500, $exception->getMessage());
            } else {
                return [
                    'code' => $exception->getStatusCode() ?? ' unknown',
                    'message' => $exception->getMessage(),
                    'controller' => "$callable",
                ];
            }

        }

    })->where('all', HashtagCms::getIgnoredPath())->middleware(['web', 'interceptor']);
    //Keep some original routes
    //Auth::routes();

    // Authentication Routes...
    Route::get('login', 'App\Http\Controllers\LoginController@index')->name('login');
    Route::post('login', 'App\Http\Controllers\LoginController@index');
    Route::post('logout', 'App\Http\Controllers\LoginController@logout')->name('logout');

    // Registration Routes...
    if (Route::has('register')) {
        Route::get('register', 'App\Http\Controllers\RegisterController@index')->name('register');
        Route::post('register', 'App\Http\Controllers\RegisterController@register');
    }

    // Password Reset Routes...
    Route::get('password/reset', 'App\Http\Controllers\PasswordController@index')->name('password.request');
    Route::post('password/email', 'App\Http\Controllers\PasswordController@email')->name('password.email');
    Route::get('password/reset/{token}', 'App\Http\Controllers\PasswordController@reset')->name('password.reset');
    Route::post('password/reset', 'App\Http\Controllers\PasswordController@update')->name('password.update');
}
