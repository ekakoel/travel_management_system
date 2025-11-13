<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected $url;

    public function __construct()
    {
        $this->url = config('services.whatsapp.url');
    }

    /**
     * Send message via Node WA server.
     *
     * @param string $phone  International format with + (e.g. +62812...)
     * @param string $message
     * @return array
     * @throws \Exception
     */
    public function send(string $phone, string $message): array
    {
        // sanitize phone: keep + and digits
        $phone = preg_replace('/[^\d\+]/', '', $phone);

        try {
            $resp = Http::timeout(10)
                ->post($this->url, [
                    'phone' => $phone,
                    'message' => $message,
                ]);

            // log for debugging
            Log::info('WA request', ['url'=> $this->url, 'phone'=>$phone, 'status'=>$resp->status(), 'body'=>$resp->body()]);

            // Throw if server returned non-2xx
            if ($resp->failed()) {
                throw new \Exception('WA server returned status '.$resp->status());
            }

            return $resp->json();
        } catch (\Throwable $e) {
            Log::error('WA send failed', ['err' => $e->getMessage()]);
            throw $e;
        }
    }
}
