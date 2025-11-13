<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class DokuHelper
{
    public static function generateSignature($clientId, $requestId, $timestamp, $body, $secretKey)
    {
        $digest = base64_encode(hash('sha256', json_encode($body), true));
        $rawSignature = "Client-Id:$clientId\nRequest-Id:$requestId\nRequest-Timestamp:$timestamp\nRequest-Target:/credit-card/v1/payment-page\nDigest:$digest";
        return "HMACSHA256=" . base64_encode(hash_hmac('sha256', $rawSignature, $secretKey, true));
    }
}
