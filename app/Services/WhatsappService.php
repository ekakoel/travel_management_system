<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected $base;

    public function __construct()
    {
        $this->base = rtrim(env('WHATSAPP_BOT_URL'), '/');
    }
    /**
     * Send message via Node bot.
     * phone: any format (will be normalized)
     */
    public function send(string $phone, string $message)
    {
        try {
            $payload = [
                'phone' => $this->formatPhone($phone),
                'message' => $message,
            ];

            $response = Http::timeout(10)->post($this->base . '/send', $payload);

            Log::info('WA Send Response', [
                'endpoint' => $this->base . '/send',
                'payload' => $payload,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('WA Send Failed', [
                'error' => $e->getMessage(),
                'phone' => $phone,
                'message' => $message,
            ]);
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /* -------------------------
       Static helpers for controller or blade usage
       Keep these static so existing Calls (WhatsappService::isConnected()) keep working
       ------------------------- */

    public static function isConnected()
    {
        try {
            $base = rtrim(env('WHATSAPP_BOT_URL', 'http://127.0.0.1/whatsapp'), '/');
            $res = Http::timeout(5)->get($base . '/status');
            if ($res->failed()) return false;
            return $res->json()['connected'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getPhone()
    {
        try {
            $base = rtrim(env('WHATSAPP_BOT_URL', 'http://127.0.0.1/whatsapp'), '/');
            $res = Http::timeout(5)->get($base . '/status');
            if ($res->failed()) return null;
            return $res->json()['phone'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function generateQRCode()
    {
        try {
            $base = rtrim(env('WHATSAPP_BOT_URL', 'http://127.0.0.1/whatsapp'), '/');
            $res = Http::timeout(10)->get($base . '/qr');
            if ($res->failed()) return null;
            return $res->json()['qr'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function disconnect()
    {
        try {
            $base = rtrim(env('WHATSAPP_BOT_URL', 'http://127.0.0.1/whatsapp'), '/');
            $res = Http::timeout(10)->post($base . '/disconnect');
            return ($res->ok() && ($res->json()['ok'] ?? false));
        } catch (\Exception $e) {
            return false;
        }
    }

    /* normalize phone to digits only and ensure country code for Indonesia (62) if leading 0 */
    private function formatPhone(string $phone): string
    {
        $p = preg_replace('/[^0-9]/', '', $phone);

        if ($p === '') return $phone; // fallback raw

        // if starts with 0 -> replace with 62
        if (substr($p, 0, 1) === '0') {
            $p = '62' . substr($p, 1);
        }

        return $p;
    }
}
