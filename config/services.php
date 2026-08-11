<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'whatsapp' => [
        'url' => env('WA_SERVER_URL', 'http://127.0.0.1/send-message'),
        'bot_url' => env('WHATSAPP_BOT_URL', 'http://127.0.0.1:3000'),
        'api_key' => env('WA_API_KEY'),
    ],

    'accommodation' => [
        'default_room_inventory' => (int) env('ACCOMMODATION_DEFAULT_ROOM_INVENTORY', 1),
    ],

    'exchange_rate' => [
        'key' => env('EXCHANGE_RATE_API_KEY'),
        'base_url' => env('EXCHANGE_RATE_API_URL', 'https://v6.exchangerate-api.com/v6'),
    ],
];
