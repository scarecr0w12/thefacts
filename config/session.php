<?php

return [
    'default' => 'null',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'stores' => [
        'null' => [
            'driver' => 'null',
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
