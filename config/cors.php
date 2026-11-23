<?php

return [

    'paths' => [
        'api/*',
        'submit-review',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://tourreview.site',
        'https://tourreview.site',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
