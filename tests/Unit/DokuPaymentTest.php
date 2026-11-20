<?php

namespace Tests\Unit;

use App\Models\InvoiceAdmin;
use App\Models\Orders;
use App\Models\UsdRates;
use App\Services\Payments\DokuPaymentService;
use App\Services\Payments\DokuService;
use Illuminate\Http\Request;
use Tests\TestCase;

class DokuPaymentTest extends TestCase
{
    public function test_usd_invoice_is_charged_full_balance_in_idr(): void
    {
        $invoice = new InvoiceAdmin([
            'balance' => 100,
            'sell_usd' => 15000,
        ]);
        $invoice->setRelation('currency', new UsdRates(['name' => 'USD']));

        $amount = app(DokuPaymentService::class)->amountForInvoice($invoice);

        $this->assertSame(1500000, $amount['amount_idr']);
        $this->assertSame('USD', $amount['currency']);
    }

    public function test_idr_invoice_is_charged_one_to_one(): void
    {
        $invoice = new InvoiceAdmin([
            'balance' => 1250000,
        ]);
        $invoice->setRelation('currency', new UsdRates(['name' => 'IDR']));

        $amount = app(DokuPaymentService::class)->amountForInvoice($invoice);

        $this->assertSame(1250000, $amount['amount_idr']);
    }

    public function test_only_public_services_support_doku(): void
    {
        $service = app(DokuPaymentService::class);

        $this->assertTrue($service->supportsOrder(new Orders(['service' => 'Hotel'])));
        $this->assertTrue($service->supportsOrder(new Orders(['service' => 'Hotel Promo'])));
        $this->assertTrue($service->supportsOrder(new Orders(['service' => 'Hotel Package'])));
        $this->assertTrue($service->supportsOrder(new Orders(['service' => 'Transport'])));
        $this->assertTrue($service->supportsOrder(new Orders(['service' => 'Tour Package'])));
        $this->assertTrue($service->supportsOrder(new Orders(['service' => 'Activity'])));
        $this->assertFalse($service->supportsOrder(new Orders(['service' => 'Private Villa'])));
        $this->assertFalse($service->supportsOrder(new Orders(['service' => 'Wedding Package'])));
    }

    public function test_notification_signature_accepts_the_doku_hmac(): void
    {
        config([
            'doku.enabled' => true,
            'doku.client_id' => 'BRN-TEST',
            'doku.secret_key' => 'test-secret',
            'doku.notification_path' => '/api/doku/notification',
        ]);

        $requestId = 'request-123';
        $timestamp = '2026-11-20T10:00:00Z';
        $target = '/api/doku/notification';
        $body = '{"transaction":{"status":"SUCCESS"},"order":{"invoice_number":"DK-1"}}';
        $digest = base64_encode(hash('sha256', $body, true));
        $stringToSign = implode("\n", [
            'Client-Id:BRN-TEST',
            'Request-Id:'.$requestId,
            'Request-Timestamp:'.$timestamp,
            'Request-Target:'.$target,
            'Digest:'.$digest,
        ]);
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, 'test-secret', true));

        $request = Request::create($target, 'POST', [], [], [], [], $body);
        $request->headers->set('Client-Id', 'BRN-TEST');
        $request->headers->set('Request-Id', $requestId);
        $request->headers->set('Request-Timestamp', $timestamp);
        $request->headers->set('Signature', 'HMACSHA256='.$signature);

        $this->assertTrue(app(DokuService::class)->notificationIsValid($request));
    }

    public function test_notification_signature_rejects_a_modified_body(): void
    {
        config([
            'doku.enabled' => true,
            'doku.client_id' => 'BRN-TEST',
            'doku.secret_key' => 'test-secret',
            'doku.notification_path' => '/api/doku/notification',
        ]);

        $requestId = 'request-123';
        $timestamp = '2026-11-20T10:00:00Z';
        $target = '/api/doku/notification';
        $body = '{"transaction":{"status":"SUCCESS"},"order":{"invoice_number":"DK-1"}}';
        $digest = base64_encode(hash('sha256', $body, true));
        $stringToSign = implode("\n", [
            'Client-Id:BRN-TEST',
            'Request-Id:'.$requestId,
            'Request-Timestamp:'.$timestamp,
            'Request-Target:'.$target,
            'Digest:'.$digest,
        ]);
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, 'test-secret', true));

        $request = Request::create($target, 'POST', [], [], [], [], '{"order":{"invoice_number":"DK-9"}}');
        $request->headers->set('Client-Id', 'BRN-TEST');
        $request->headers->set('Request-Id', $requestId);
        $request->headers->set('Request-Timestamp', $timestamp);
        $request->headers->set('Request-Target', $target);
        $request->headers->set('Signature', 'HMACSHA256='.$signature);

        $this->assertFalse(app(DokuService::class)->notificationIsValid($request));
    }

    public function test_get_request_signature_omits_the_digest_component(): void
    {
        config([
            'doku.enabled' => true,
            'doku.client_id' => 'BRN-TEST',
            'doku.secret_key' => 'test-secret',
        ]);

        $method = new \ReflectionMethod(DokuService::class, 'sign');
        $method->setAccessible(true);
        $stringToSign = implode("\n", [
            'Client-Id:BRN-TEST',
            'Request-Id:request-123',
            'Request-Timestamp:2026-11-20T10:00:00Z',
            'Request-Target:/orders/v1/status/DK-1',
        ]);
        $expected = base64_encode(hash_hmac('sha256', $stringToSign, 'test-secret', true));

        $this->assertSame(
            $expected,
            $method->invoke(app(DokuService::class), '/orders/v1/status/DK-1', null, 'request-123', '2026-11-20T10:00:00Z')
        );
    }

    public function test_notification_signature_rejects_a_modified_target(): void
    {
        config([
            'doku.enabled' => true,
            'doku.client_id' => 'BRN-TEST',
            'doku.secret_key' => 'test-secret',
            'doku.notification_path' => '/api/doku/notification',
        ]);

        $requestId = 'request-123';
        $timestamp = '2026-11-20T10:00:00Z';
        $body = '{}';
        $digest = base64_encode(hash('sha256', $body, true));
        $stringToSign = implode("\n", [
            'Client-Id:BRN-TEST',
            'Request-Id:'.$requestId,
            'Request-Timestamp:'.$timestamp,
            'Request-Target:/api/doku/notification',
            'Digest:'.$digest,
        ]);
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, 'test-secret', true));

        $request = Request::create('/api/doku/notification', 'POST', [], [], [], [], $body);
        $request->headers->set('Client-Id', 'BRN-TEST');
        $request->headers->set('Request-Id', $requestId);
        $request->headers->set('Request-Timestamp', $timestamp);
        $request->headers->set('Request-Target', '/api/doku/other');
        $request->headers->set('Signature', 'HMACSHA256='.$signature);

        $this->assertFalse(app(DokuService::class)->notificationIsValid($request));
    }

    public function test_failed_notification_is_ignored_without_database_access(): void
    {
        $payment = app(DokuPaymentService::class)->applyNotification([
            'transaction' => ['status' => 'FAILED'],
            'order' => ['invoice_number' => 'DK-1'],
        ]);

        $this->assertNull($payment);
    }
}
