<?php

$activeTheme = 'modern';
try {
    if (function_exists('session') && session()->has('ADMIN_THEME')) {
        $activeTheme = session('ADMIN_THEME');
    }
} catch (\Exception $e) {
    // Session might not be available during early boot or CLI
}

return [
    'cmsInfo' => [
        'defaultPage' => 'dashboard',
        'site_label' => '#CMS - Admin',
        'base_path' => '/admin',
        'version' => env('BE_RESOURCE_VERSION', '210320261130'),
        'theme' => "hashtagcms::be.$activeTheme",
        'theme_assets' => "assets/hashtagcms/be/$activeTheme",
        'app_url' => env('APP_URL'),
        'resource_dir' => "be/$activeTheme",
        'media_path' => '/storage/media', //media path
        'show_delete_popup' => true,
        'show_download_button' => true,
        'records_per_page' => 20,
        'action_field_title' => ['label' => 'Action', 'key' => 'action'],
        'action_as_ajax' => ['delete', 'approve', 'publish_status'],
        'make_field_as_link' => [
            ['key' => 'id', 'action' => 'edit'],
            [
                'key' => 'publish_status',
                'action' => 'publish',
                'css_0' => 'text-warning fa fa-circle-o',
                'css_1' => 'text-success fa fa-check-square-o'
            ]
        ],
        'action_icon_css' => [
            'edit' => 'fa fa-edit',
            'delete' => 'fa fa-trash-o',
            'approve' => 'glyphicon glyphicon-ok',
            'loading' => 'fa-spinner fa-pulse fa-fw',
        ],
        /**
         * Moved to database: Admin -> Settings -> Module Types
         */
        /*'module_types' => ['Static', 'Query', 'Service', 'Custom', 'QueryService', 'UrlService', 'ServiceLater'], */
    ],
    'media' => [
        'upload_path' => 'media', // /storage/app/public/media  >_ php artisan storage:link
    ],
    'imageSupportedByBrowsers' => ['apng', 'avif', 'gif', 'jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp', 'png', 'svg', 'webp', 'bmp', 'ico', 'cur', 'tif', 'tiff'],
    'chartPages' => 10,
    'json_query_in_query_module' => false, // Enable/Disable JSON query in query module, 
    'permissions' => [
        'module_cache_ttl' => 60 * 60 * 24,
        'cache_key_prefix' => 'cms_permissions_',
        'cache_ttl' => 0 //60 * 60 * 24, // 24 hours
    ],
    // example: 
    // {
    //     "from": "users",
    //     "select": "id, name, email",
    //     "where": [
    //         ["id", "=", 1]
    //     ],
    //     "orderBy": ["id", "desc"],
    //     "limit": 1
    // }

];
