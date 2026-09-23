<?php

return [

    /*
     * Master switch. When false, no listener is registered at all — zero
     * overhead for installs that don't want external log shipping.
     */
    'enabled' => env('CMS_LOG_EXPORT_ENABLED', false),

    /*
     * Which backend to ship to: none | graylog | last9
     */
    'driver' => env('CMS_LOG_EXPORT_DRIVER', 'none'),

    /*
     * Minimum PSR-3 level that gets shipped externally (debug, info, notice,
     * warning, error, critical, alert, emergency). Laravel keeps writing
     * everything to its normal channels regardless of this setting — this
     * only filters what goes to the external system.
     */
    'min_level' => env('CMS_LOG_EXPORT_MIN_LEVEL', 'error'),

    /*
     * Ship log entries via a queued job instead of inline during the
     * request, so a slow/unreachable log backend never adds latency to the
     * request that triggered the log line.
     */
    'async' => env('CMS_LOG_EXPORT_ASYNC', true),
    'queue' => env('CMS_LOG_EXPORT_QUEUE', 'default'),

    /*
     * Context keys stripped (case-insensitively, at any depth) before an
     * entry leaves the app.
     */
    'redact_keys' => [
        'password', 'password_confirmation', 'token', 'secret',
        'api_key', 'authorization', 'access_token', 'refresh_token',
    ],

    'graylog' => [
        'host' => env('GRAYLOG_HOST'),
        'port' => env('GRAYLOG_PORT', 12201),
        // tcp | udp
        'protocol' => env('GRAYLOG_PROTOCOL', 'tcp'),
    ],

    'last9' => [
        // OTLP/HTTP logs endpoint, e.g. https://otlp.last9.io/v1/logs
        'endpoint' => env('LAST9_OTLP_ENDPOINT'),
        // Full header value, e.g. "Basic base64(user:pass)" or "Bearer xxx"
        'auth_header' => env('LAST9_AUTH_HEADER', 'Authorization'),
        'auth_value' => env('LAST9_AUTH_VALUE'),
        'timeout' => env('LAST9_TIMEOUT', 2),
    ],

];
