<?php

return [
    'login_session' => '+ 1 year',
    'login_session_expiry_format' => 'Y-m-d\TH:i:s.u\Z',
    'redis_cache_enabled'=>true,
    'cache_load_data_ttl'=>14400, // 4 hours
    'cache_load_data_mobile_ttl'=>14400, // 4 hours
    'cache_load_config_ttl'=>14400, // 4 hours
    'cache_header_include'=>['x-api-secret','x-site','x-lang','x-platform','x-category','x-microsite'],
];
