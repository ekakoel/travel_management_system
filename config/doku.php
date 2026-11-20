<?php

return [
    'enabled' => (bool) env('DOKU_ENABLED', false),
    'client_id' => env('DOKU_CLIENT_ID'),
    'secret_key' => env('DOKU_SECRET_KEY'),
    'base_url' => rtrim((string) env('DOKU_API_URL', 'https://api-sandbox.doku.com'), '/'),
    'checkout_path' => '/checkout/v1/payment',
    'status_path' => '/orders/v1/status/{invoice_number}',
    'notification_path' => '/api/doku/notification',
    'timeout' => (int) env('DOKU_TIMEOUT', 30),
    'max_payment_due_minutes' => 8640,
];
