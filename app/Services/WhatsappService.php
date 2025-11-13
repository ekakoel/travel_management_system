<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected $endpoint;

    public function __construct()
    {
        $this->endpoint = env('WHATSAPP_BOT_URL');
    }

    public function send(string $phone, string $message)
    {
        try {
            $response = Http::timeout(10)->post($this->endpoint, [
                'phone' => $phone,
                'message' => $message
            ]);
            
            Log::info('WA Send Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'ok' => false,
                'error' => $response->body(),
            ];
            // return $response->json();
        } catch (\Exception $e) {
            Log::error('WA Send Failed', [
                'error' => $e->getMessage(),
                'endpoint' => $this->endpoint,
            ]);
            return null;
        }
    }
}
