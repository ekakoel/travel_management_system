<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tourOrder = App\Models\Orders::where('service', 'Tour Package')
    ->where('status', 'Approved')
    ->latest('id')
    ->first();

if (!$tourOrder) {
    echo "tour:none" . PHP_EOL;
    exit(0);
}

Auth::loginUsingId($tourOrder->sales_agent);

$request = Illuminate\Http\Request::create('/detail-order-tour/' . $tourOrder->id, 'GET');
$request->setLaravelSession(app('session')->driver());
$response = app(Illuminate\Contracts\Http\Kernel::class)->handle($request);
$html = $response->getContent();

$invoiceRequest = Illuminate\Http\Request::create('/orders/' . $tourOrder->id . '/invoice/preview', 'GET');
$invoiceRequest->setLaravelSession(app('session')->driver());
$invoiceResponse = app(Illuminate\Contracts\Http\Kernel::class)->handle($invoiceRequest);

$checks = [
    'tour_id' => $tourOrder->id,
    'has_preview_trigger' => str_contains($html, 'data-invoice-preview-trigger'),
    'has_compact_modal' => str_contains($html, 'data-invoice-preview-modal'),
    'has_compact_class' => str_contains($html, 'order-invoice-sheet--compact'),
    'has_preview_invoice_label' => str_contains($html, 'Preview Invoice'),
    'invoice_preview_status' => $invoiceResponse->getStatusCode(),
];

foreach ($checks as $key => $value) {
    echo $key . ':' . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . PHP_EOL;
}
