<?php

namespace HashtagCms;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use HashtagCms\Console\Commands\CmsFrontendControllerCommand;
use HashtagCms\Console\Commands\CmsInstall;
use HashtagCms\Console\Commands\CmsModuleControllerCommand;
use HashtagCms\Console\Commands\CmsModuleCreateCommand;
use HashtagCms\Console\Commands\CmsModuleModelCommand;
use HashtagCms\Console\Commands\CmsModuleValidatorCommand;
use HashtagCms\Console\Commands\CmsLanguageInstall;
use HashtagCms\Console\Commands\CmsValidatorCommand;
use HashtagCms\Console\Commands\Cmsversion;
use HashtagCms\Console\Commands\ImportDatabaseData;
use HashtagCms\Console\Commands\ExportDatabaseData;
use HashtagCms\Console\Commands\SetupStandalone;
use HashtagCms\Console\Commands\CmsShowInstructions;
use HashtagCms\Console\Commands\CmsAdminPanelTestCommand;
use HashtagCms\Core\Middleware\Admin\BeMiddleware;
use HashtagCms\Core\Middleware\Admin\CmsModuleInfo;
use HashtagCms\Core\Middleware\API\Etag;
use HashtagCms\Core\Middleware\FeMiddleware;
use HashtagCms\Core\Providers\Admin\AdminServiceProvider;
use HashtagCms\Core\Providers\FeServiceProvider;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use HashtagCms\Services\AnalyticsLogger;
use HashtagCms\Events\UserVisit;
use HashtagCms\Listeners\RecordUserVisit;
use HashtagCms\Events\CopyLangData;
use HashtagCms\Listeners\ProcessLangCopy;
use Illuminate\Support\Facades\Auth;
use HashtagCms\Core\Auth\ExternalApiUserProvider;
use HashtagCms\Console\Commands\RegisterModules;

class HashtagCmsServiceProvider extends ServiceProvider
{
    protected $groupName = 'hashtagcms';

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(RouteRegistrar $router)
    {
        //Register Custom User Provider for External API
        Auth::provider('hashtagcms_external_api', function ($app, array $config) {
            return new ExternalApiUserProvider();
        });

        //Register Event Listener

        Event::listen(UserVisit::class, RecordUserVisit::class);
        Event::listen(CopyLangData::class, ProcessLangCopy::class);

        //Flush Analytics buffer on termination
        $this->app->terminating(function () {
            app(AnalyticsLogger::class)->flush();
        });

        //More providers for admin and frontend
        $this->app->register(AdminServiceProvider::class);
        $this->app->register(FeServiceProvider::class);

        //Middleware for Admin
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('cmsInterceptor', BeMiddleware::class);
        $router->aliasMiddleware('cmsModuleInfo', CmsModuleInfo::class);

        //Middleware for Frontend
        $router->aliasMiddleware('interceptor', FeMiddleware::class);
        $router->aliasMiddleware('etag', Etag::class);

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'hashtagcms');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'hashtagcms');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Register a callback to load routes after application routes
        $this->app->booted(function () {
            $this->loadHashtagRoutes();
        });

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }


        // Register all package commands in one place
        $this->registerPackageCommands();

        // Handle oversized POST/upload requests gracefully site-wide.
        // Laravel's built-in ValidatePostSize middleware throws PostTooLargeException
        // BEFORE StartSession runs, so withErrors()/session flash is unreliable.
        // We use a URL query param instead — no session dependency.
        $this->app->make(ExceptionHandler::class)->renderable(
            function (PostTooLargeException $e, $request) {
                $maxSize = ini_get('post_max_size') ?: ini_get('upload_max_filesize') ?: '(unknown)';
                $message = "The uploaded file is too large. Maximum allowed size is {$maxSize}. Please reduce the file size and try again.";

                // AJAX / fetch — return structured JSON
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'isSaved' => false,
                        'success' => false,
                        'message' => $message,
                        'errors'  => ['img_preview' => [$message]],
                    ], 413);
                }

                // Regular form POST — redirect back with error in the URL query string
                // so it survives the redirect without needing a started session.
                $backUrl  = $request->headers->get('referer') ?: url()->previous();
                $separator = str_contains($backUrl, '?') ? '&' : '?';
                return redirect($backUrl . $separator . '_upload_error=' . urlencode($message));
            }
        );

    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/hashtagcms.php', $this->groupName);
        $this->mergeConfigFrom(__DIR__ . '/../config/hashtagcmsadmin.php', $this->groupName . 'admin');
        $this->mergeConfigFrom(__DIR__ . '/../config/hashtagcmscommon.php', $this->groupName . 'common');
        $this->mergeConfigFrom(__DIR__ . '/../config/hashtagcmsapi.php', $this->groupName . 'api');

        // Register the service the package provides.
        $this->app->singleton('hashtagcms', function ($app) {
            return new HashtagCms;
        });

        //Bind AnalyticsLogger as Singleton
        $this->app->singleton(AnalyticsLogger::class, function ($app) {
            return new AnalyticsLogger();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['hashtagcms'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        // php artisan vendor:publish --tag=hashtagcms.config
        $this->publishes([
            __DIR__ . '/../config/hashtagcms.php' => config_path('hashtagcms.php'),
            __DIR__ . '/../config/hashtagcmsadmin.php' => config_path('hashtagcmsadmin.php'),
            __DIR__ . '/../config/hashtagcmsapi.php' => config_path('hashtagcmsapi.php'),
        ], $this->groupName . '.config');

        // Publishing the views.
        // php artisan vendor:publish --tag=hashtagcms.views.frontend
        $this->publishes([
            __DIR__ . '/../resources/views/fe' => resource_path('views/vendor/hashtagcms/fe'),
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/hashtagcms'),
        ], $this->groupName . '.views.frontend');

        //Publishing the views for admin
        // php artisan vendor:publish --tag=hashtagcms.views.admin
        $this->publishes([
            __DIR__ . '/../resources/views/be' => resource_path('views/vendor/hashtagcms/be'),
        ], $this->groupName . '.views.admin');

        //Publishing the views for admin common
        // hashtagcms.views.admincommon
        $this->publishes([
            __DIR__ . '/../resources/views/be/modern/common' => resource_path('views/vendor/hashtagcms/be/modern/common'),
            __DIR__ . '/../resources/views/be/modern/index.blade.php' => resource_path('views/vendor/hashtagcms/be/modern/index.blade.php'),
        ], $this->groupName . '.views.admincommon');

        //Export view and js for admin and frontend
        // php artisan vendor:publish --tag=hashtagcms.views.all
        $this->publishes([
            __DIR__ . '/../resources/views/be' => resource_path('views/vendor/hashtagcms/be'),
            __DIR__ . '/../resources/views/fe' => resource_path('views/vendor/hashtagcms/fe'),
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/hashtagcms'),
            __DIR__ . '/../resources/assets' => resource_path('assets/hashtagcms'),

        ], $this->groupName . '.views.all');

        // Publishing assets.
        // php artisan vendor:publish --tag=hashtagcms.assets
        $this->publishes([
            __DIR__ . '/../public/assets' => public_path('assets/hashtagcms'),
            __DIR__ . '/../resources/assets/fe' => resource_path('assets/hashtagcms/fe'),
            __DIR__ . '/../resources/assets/be' => resource_path('assets/hashtagcms/be'),
            __DIR__ . '/../resources/assets/js' => resource_path('assets/hashtagcms/js'),
            __DIR__ . '/../resources/support' => resource_path('assets/hashtagcms/support'),
        ], $this->groupName . '.assets');

    }

    /**
     * Register all package commands
     *
     * @return void
     */
    protected function registerPackageCommands()
    {
        $this->commands([
                // Core installation commands
            CmsInstall::class,
            CmsLanguageInstall::class,
            CmsValidatorCommand::class,
            RegisterModules::class, //added in 3.0.1

                // Code generation commands
            CmsModuleCreateCommand::class,
            CmsModuleModelCommand::class,
            CmsModuleControllerCommand::class,
            CmsModuleValidatorCommand::class,
            CmsFrontendControllerCommand::class,

                // Utility commands
            Cmsversion::class,        

            // Data management commands
            ImportDatabaseData::class,
            ExportDatabaseData::class,
            SetupStandalone::class,
            CmsShowInstructions::class,
            CmsAdminPanelTestCommand::class
            
        ]);
    }



    /**
     * Load routes
     * @return void
     */
    protected function loadHashtagRoutes()
    {
        // Load API routes
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');

        // Create a route filter to add additional middleware from config
        app('router')->matched(function (RouteMatched $event) {
            $route = $event->route;
            $middleware = $route->middleware();

            // If this route uses the 'interceptor' middleware (HashtagCms frontend routes)
            if (in_array('interceptor', $middleware)) {
                // Get additional middleware from config
                $additionalMiddleware = config('hashtagcms.additional_middleware', []);

                // If there are additional middleware defined, add them to the route
                if (!empty($additionalMiddleware) && is_array($additionalMiddleware)) {
                    foreach ($additionalMiddleware as $middleware) {
                        // Check if this middleware is already applied to avoid duplicates
                        if (!in_array($middleware, $route->middleware())) {
                            $route->middleware($middleware);
                        }
                    }
                }

                // Log the final middleware stack for debugging
                //info('HashtagCms route middleware: ' . implode(', ', $route->middleware()));
            }
        });

        // Load web routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }
}
