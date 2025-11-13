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

    'doku' => [
        'client_id' => env('DOKU_CLIENT_ID', 'BRN-0285-1740552446421'),
        'secret_key' => env('DOKU_SECRET_KEY', 'SK-KejpIZZwlcVTrtRCSXOY'),
        'shared_key' => env('DOKU_SHARED_KEY','MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAotLt35UewYbIYXhGHvyI0ZfroqGukb2XH5IYN2HbfxC9ILuk3ZmL8rLGwUU4vov2jkrIbgw0kKMx5wHAl+joJqYCQAtiI7a/fSn8tmIY/e0wUVpzMJk8NH+XTTvlvwWO0SC3vV6dPsAvwRClMUTtjg/lMlNz26njLzY4VAgQuPApzF++xphv0H+2pYyMQQ4aEN2ml2AmAp0XC/IPy8lT61StuqAoHbaj3K3u7E9DAFqTm6GUGjoDTB3GUu3X6EfJoYi41fCf2FAt2tcuKFYztad3bU2+pbgGU/OmpqGQHk78zM6l67j6CUSNVe/JvKQgBxRH/ss2XFNIVAHU5Cmn4wIDAQAB'),
        'base_url' => env('DOKU_BASE_URL', 'https://api-sandbox.doku.com/'),
    ],
    
    'whatsapp' => [
        'url' => env('WA_SERVER_URL', 'http://127.0.0.1:3000/send-message'),
    ],
    // 'doku' => [
    //     'client_id' => env('DOKU_CLIENT_ID', 'BRN-0224-1653983386996'),
    //     'secret_key' => env('DOKU_SECRET_KEY', 'SK-IuHUEKNHHrM8LyLO1bnD'),
    //     'shared_key' => env('DOKU_SHARED_KEY','MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxUDjrqqnNi3C0JHib9qLIfgQ7KXmMae1VX8ZB7q8vvLI2+iEUvomwaC9AQ2eOi9/KMhQLP/VsvcK2zpXOBwPPYK3T/YS1MzTeQ6hro0gs9ZXIu0/oQ60kgqAMz4W/lHjESoro/x7W/B1VQEd00kg1KZNGU1xGMngsCEf7beoOEOukruKVSFTwtmbHLHp0qZR0cUEFvA9e8vhABzn20UXBYknMP00iDUjF6ucOXWt0CHSHkHOhBUtBtd/Z+l+EjzajHt/1aZN6K+B8CTM+O6oE8CweX19l2vks9srCdnu4t1sHExJrFfRjWT3V2cpXZXkZFxo8bjIiVVfbLX564sVfwIDAQAB'),
    //     'base_url' => env('DOKU_BASE_URL', 'https://api-sandbox.doku.com/'),
    // ],

];
