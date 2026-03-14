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
$defaultPage = config('hashtagcmsadmin.cmsInfo.defaultPage', 'dashboard');
$adminBasePath = config('hashtagcmsadmin.cmsInfo.base_path', 'admin');
//Register Admin
Route::prefix($adminBasePath)->group(function () use ($namespace, $appNamespace, $defaultPage) {
    Route::get('/heartbeat', function () {
        return response()->json(['status' => 'alive']);
    })->middleware(['web', 'auth:sanctum'])->name('admin.heartbeat');


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

    })->middleware(['web', 'auth:sanctum', 'cmsModuleInfo', 'cmsInterceptor'])->where('params', HashtagCms::getIgnoredPath());
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
                    'code' => method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500,
                    'message' => $exception->getMessage(),
                    'controller' => "$callable",
                ];
            }

        }

    })->where('all', HashtagCms::getIgnoredPath())->middleware(['web', 'interceptor']);
    
    // Authentication Routes...
    $loginController = class_exists($appNamespace . 'Http\Controllers\LoginController') ? $appNamespace . 'Http\Controllers\LoginController' : $namespace . 'Http\Controllers\LoginController';
    $logoutController = class_exists($appNamespace . 'Http\Controllers\LogoutController') ? $appNamespace . 'Http\Controllers\LogoutController' : $namespace . 'Http\Controllers\LogoutController';

    Route::get('login', $loginController . '@index')->name('login');
    Route::post('login', $loginController . '@index');
    Route::post('logout', $logoutController . '@logout')->name('logout');

    // Registration Routes...
    $registerController = class_exists($appNamespace . 'Http\Controllers\RegisterController') ? $appNamespace . 'Http\Controllers\RegisterController' : $namespace . 'Http\Controllers\RegisterController';
    Route::get('register', $registerController . '@index')->name('register');
    Route::post('register', $registerController . '@register');

    // Password Reset Routes...
    $passwordController = class_exists($appNamespace . 'Http\Controllers\PasswordController') ? $appNamespace . 'Http\Controllers\PasswordController' : $namespace . 'Http\Controllers\PasswordController';
    Route::get('password/reset', $passwordController . '@index')->name('password.request');
    Route::post('password/email', $passwordController . '@email')->name('password.email');
    Route::get('password/reset/{token}', $passwordController . '@reset')->name('password.reset');
    Route::post('password/reset', $passwordController . '@update')->name('password.update');
}
