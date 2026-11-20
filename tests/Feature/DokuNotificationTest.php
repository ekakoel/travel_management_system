<?php

namespace Tests\Feature;

use Tests\TestCase;

class DokuNotificationTest extends TestCase
{
    private function signatureHeaders(string $body): array
    {
        $requestId = 'request-123';
        $timestamp = '2026-11-20T10:00:00Z';
        $target = '/api/doku/notification';
        $digest = base64_encode(hash('sha256', $body, true));
        $stringToSign = implode("\n", [
            'Client-Id:BRN-TEST',
            'Request-Id:'.$requestId,
            'Request-Timestamp:'.$timestamp,
            'Request-Target:'.$target,
            'Digest:'.$digest,
        ]);
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, 'test-secret', true));

        return [
            'Client-Id' => 'BRN-TEST',
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => 'HMACSHA256='.$signature,
        ];
    }

    private function configureDoku(): void
    {
        config([
            'doku.enabled' => true,
            'doku.client_id' => 'BRN-TEST',
            'doku.secret_key' => 'test-secret',
            'doku.notification_path' => '/api/doku/notification',
        ]);
    }

    public function test_failed_notification_is_acknowledged_without_database_access(): void
    {
        $this->configureDoku();
        $body = '{"transaction":{"status":"FAILED"},"order":{"invoice_number":"DK-1"}}';
        $server = $this->transformHeadersToServerVars($this->signatureHeaders($body));

        $this->call('POST', '/api/doku/notification', [], [], [], $server, $body)
            ->assertOk();
    }

    public function test_modified_notification_body_is_unauthorized(): void
    {
        $this->configureDoku();
        $body = '{"transaction":{"status":"FAILED"},"order":{"invoice_number":"DK-1"}}';
        $server = $this->transformHeadersToServerVars($this->signatureHeaders($body));

        $this->call('POST', '/api/doku/notification', [], [], [], $server, '{"transaction":{"status":"FAILED"}}')
            ->assertUnauthorized();
    }
}
