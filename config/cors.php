<?php

return [

    'paths' => [
        'api/*',
        'submit-review',
        'submit-wedding-review',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://reviewyourtour.fwh.is',
        'https://reviewyourtour.fwh.is',
        'http://reviewyourwedding.fwh.is',
        'https://reviewyourwedding.fwh.is',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
