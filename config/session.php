<?php

return [
    'default' => 'cookie',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => true,
    'stores' => [
        'array' => [
            'driver' => 'array',
        ],
        'cookie' => [
            'driver' => 'cookie',
        ],
    ],
    'files' => storage_path('framework/sessions'),
    'connection' => null,
    'table' => 'sessions',
    'store' => null,
    'lottery' => [2, 100],
    'cookie' => 'vericrown_session',
    'path' => '/',
    'domain' => null,
    'secure' => false,
    'http_only' => true,
    'same_site' => 'lax',
];
