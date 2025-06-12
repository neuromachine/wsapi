<?php
return [
//    'paths' => ['api/*'],
//    'allowed_methods' => ['*'],
//    'allowed_origins' => ['*'],
//    'allowed_origins_patterns' => [],
//    'allowed_headers' => ['*'],
//    'exposed_headers' => [],
//    'max_age' => 0,
//    'supports_credentials' => false,
    'paths' => ['api/*', 'blocks/categories/structure/*', 'tree/*'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'allowed_origins' => ['https://ws-pro.ru'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
