<?php

return [

    'paths' => [
        'api/*',
        'submit-review',
        'submit-wedding-review',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://tourreview.site',
        'https://tourreview.site',
        'http://reviewyourwedding.fwh.is',
        'https://reviewyourwedding.fwh.is',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
