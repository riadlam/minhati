<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | When using two-host setup: UI on one host (APP_URL), API+DB on another
    | (MINHATI_APP_URL), the API host must allow the UI origin here.
    | Set CORS_ALLOWED_ORIGINS in .env on the API host (e.g. https://test.hcamex.com).
    | With supports_credentials=true, do not use '*' for allowed_origins; set explicit origins.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_filter(array_merge(
        env('APP_URL') ? [rtrim(env('APP_URL'), '/')] : [],
        env('CORS_ALLOWED_ORIGINS') ? array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS'))) : []
    ))) ?: ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Authorization', 'Accept', 'Content-Type', 'X-CSRF-TOKEN', 'X-Requested-With'],

    'exposed_headers' => [],

    'max_age' => 600,

    'supports_credentials' => true,

];
