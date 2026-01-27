<?php
return [
    'client_id' => env('DOKU_CLIENT_ID'),
    'secret_key' => env('DOKU_SECRET_KEY'),
    'api_url' => env('DOKU_API_URL'),
    'timeout' => env('DOKU_TIMEOUT', 30),
    'currency' => 'IDR',
    'payment_due_hours' => 60,
];
