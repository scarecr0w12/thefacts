<?php

return [
    'name' => 'VeriCrowd',
    'env' => 'production',
    'debug' => false,
    'url' => 'http://localhost',
    'timezone' => 'UTC',
    'locale' => 'en',
    'cipher' => 'AES-256-CBC',
    'key' => 'base64:' . base64_encode(random_bytes(32)),
    'previous_keys' => [],
];
