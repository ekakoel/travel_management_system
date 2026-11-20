<?php

namespace App\Services\Payments;

use App\Exceptions\DokuException;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DokuService
{
    public function enabled(): bool
    {
        return (bool) config('doku.enabled')
            && filled(config('doku.client_id'))
            && filled(config('doku.secret_key'))
            && filled(config('doku.base_url'));
    }

    public function assertConfigured(): void
    {
        if (!$this->enabled()) {
            throw DokuException::notConfigured();
        }
    }

    public function notificationTarget(): string
    {
        return (string) config('doku.notification_path');
    }

    public function notificationUrl(): string
    {
        return url($this->notificationTarget());
    }

    public function createCheckout(array $payload): array
    {
        $this->assertConfigured();
        $path = (string) config('doku.checkout_path');
        $body = $this->encode($payload);

        $response = $this->send('POST', $path, $body);

        if (!$response->successful()) {
            throw DokuException::requestFailed($response->status());
        }

        $data = $response->json();

        if (!is_array($data) || !data_get($data, 'response.payment.url')) {
            throw DokuException::invalidResponse();
        }

        return $data;
    }

    public function checkStatus(string $invoiceNumber): array
    {
        $this->assertConfigured();
        $path = str_replace(
            '{invoice_number}',
            rawurlencode($invoiceNumber),
            (string) config('doku.status_path')
        );

        $response = $this->send('GET', $path, '');

        if (!$response->successful()) {
            throw DokuException::requestFailed($response->status());
        }

        $data = $response->json();

        if (!is_array($data)) {
            throw DokuException::invalidResponse();
        }

        return $data;
    }

    public function notificationIsValid(Request $request): bool
    {
        if (!$this->enabled()) {
            return false;
        }

        $clientId = (string) $request->header('Client-Id', '');
        $requestId = (string) $request->header('Request-Id', '');
        $timestamp = (string) $request->header('Request-Timestamp', '');
        $headerTarget = (string) $request->header('Request-Target', '');
        $signature = (string) $request->header('Signature', '');
        $target = $this->notificationTarget();

        if ($clientId !== (string) config('doku.client_id')
            || $requestId === ''
            || $timestamp === ''
            || $target === ''
            || !str_starts_with($signature, 'HMACSHA256=')) {
            return false;
        }

        if ($headerTarget !== '' && rtrim($headerTarget, '/') !== rtrim($target, '/')) {
            return false;
        }

        $digest = $this->digest($request->getContent());

        $expected = 'HMACSHA256='.$this->sign($target, $digest, $requestId, $timestamp);

        return hash_equals($expected, $signature);
    }

    public function expiredAt(array $payload): ?Carbon
    {
        $value = data_get($payload, 'response.payment.expired_date');

        if (!$value) {
            return null;
        }

        try {
            $date = Carbon::parse($value);

            if (!preg_match('/(Z|[+-]\d{2}:?\d{2})$/i', (string) $value)) {
                $date = Carbon::parse($value, '+07:00');
            }

            return $date->utc();
        } catch (\Throwable) {
            return null;
        }
    }

    private function send(string $method, string $path, string $body): Response
    {
        $target = '/'.ltrim($path, '/');
        $hasBody = $method === 'POST';
        $headers = $this->signatureHeaders($target, $body, $hasBody);
        $url = rtrim((string) config('doku.base_url'), '/').$target;
        $timeout = max(1, (int) config('doku.timeout', 30));

        $request = Http::withHeaders($headers)
            ->timeout($timeout)
            ->acceptJson();

        if ($hasBody) {
            return $request->withBody($body, 'application/json')->post($url);
        }

        return $request->get($url);
    }

    private function signatureHeaders(string $target, string $body, bool $hasBody): array
    {
        $requestId = (string) Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $digest = $hasBody ? $this->digest($body) : null;

        $headers = [
            'Client-Id' => (string) config('doku.client_id'),
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Request-Target' => $target,
            'Signature' => 'HMACSHA256='.$this->sign($target, $digest, $requestId, $timestamp),
        ];

        if ($digest !== null) {
            $headers['Digest'] = $digest;
        }

        return $headers;
    }

    private function sign(string $target, ?string $digest, string $requestId, string $timestamp): string
    {
        $components = [
            'Client-Id:'.config('doku.client_id'),
            'Request-Id:'.$requestId,
            'Request-Timestamp:'.$timestamp,
            'Request-Target:'.$target,
        ];

        if ($digest !== null && $digest !== '') {
            $components[] = 'Digest:'.$digest;
        }

        $stringToSign = implode("\n", $components);

        return base64_encode(hash_hmac('sha256', $stringToSign, (string) config('doku.secret_key'), true));
    }

    private function digest(string $body): string
    {
        return base64_encode(hash('sha256', $body, true));
    }

    private function encode(array $payload): string
    {
        return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
