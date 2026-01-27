<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\UsdRates;
use App\Models\DokuVirtualAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DokuService
{
    protected $clientId;
    protected $secretKey;
    protected $sharedKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.doku.client_id');
        $this->secretKey = config('services.doku.secret_key');
        $this->sharedKey = config('services.doku.shared_key');
        $this->baseUrl = rtrim(config('services.doku.base_url'), '/');
    }
    public function generateSignature($body, $requestTarget, $timestamp)
    {
        $digest = base64_encode(hash('sha256', json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), true));
        $parsedUrl = parse_url($this->baseUrl . $requestTarget, PHP_URL_PATH);
        $signatureString = "Client-Id:".$this->clientId."\n".
                      "Request-Id:".$body['order']['invoice_number']."\n".
                      "Request-Timestamp:".$timestamp."\n".
                      "Request-Target:".$parsedUrl."\n".
                      "Digest:".$digest;
        $signature = base64_encode(hash_hmac('sha256', $signatureString, $this->secretKey, true));
        return "HMACSHA256=".$signature;
    }
    public function createPayment($order, $invoice, $agent)
    {
        $now = Carbon::now();
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $cnyrates = Cache::remember('cny_rate', 3600, fn() => UsdRates::where('name', 'CNY')->first());
        $twdrates = Cache::remember('twd_rate', 3600, fn() => UsdRates::where('name', 'TWD')->first());
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $requestTarget = "/checkout/v1/payment";
        $final_price_idr = $order->final_price * $usdrates->rate;
        $currency = "IDR";
        $amount = $final_price_idr + ($final_price_idr * 0.03);
        $body = [
            "order" => [
                "invoice_number" => $invoice->inv_no,
                "amount" => (int) $amount,
                "currency" => $currency,
            ],
            "customer" => [
                "id" => $agent->id,
                "name" => $agent->name,
                "email" => $agent->email,
                "phone" => $agent->phone,
                "address" => $agent->address,
            ],
            "payment" => [
                "payment_due_date" => 10080,
                "type" => "SALE",
                "payment_method_types"=> [
                    "VIRTUAL_ACCOUNT_BCA",
                    "VIRTUAL_ACCOUNT_BANK_MANDIRI",
                    "VIRTUAL_ACCOUNT_BANK_SYARIAH_MANDIRI",
                    "VIRTUAL_ACCOUNT_DOKU",
                    "VIRTUAL_ACCOUNT_BRI",
                    "VIRTUAL_ACCOUNT_BNI",
                    "VIRTUAL_ACCOUNT_BANK_PERMATA",
                    "VIRTUAL_ACCOUNT_BANK_CIMB",
                    "VIRTUAL_ACCOUNT_BANK_DANAMON",
                    "ONLINE_TO_OFFLINE_ALFA",
                    "CREDIT_CARD",
                    "DIRECT_DEBIT_BRI",
                    "EMONEY_SHOPEEPAY",
                    "EMONEY_OVO",
                    "EMONEY_DANA",
                    "QRIS",
                    "PEER_TO_PEER_AKULAKU",
                    "PEER_TO_PEER_KREDIVO",
                    "PEER_TO_PEER_INDODANA"
                ]
            ],
            "virtual_account_info" => [
                "reusable_status" => false,
                "expired_time" => 10080,
                "info1" => "Detail tambahan 1",
                "info2" => "Detail tambahan 2"
            ],
        ];

        $signature = $this->generateSignature($body, $requestTarget, $timestamp);
        try {
            $response = Http::withHeaders([
                "Client-Id" => $this->clientId,
                "Request-Id" => $invoice->inv_no,
                "Request-Timestamp" => $timestamp,
                "Signature" => $signature,
                "Content-Type" => "application/json"
            ])->post($this->baseUrl . $requestTarget, $body);
            $responseData = $response->json();
            $created_date = $now;
            $countdown = isset($responseData['response']['payment']['payment_due_date']) ? (int) $responseData['response']['payment']['payment_due_date'] : 10080;
            $expired_date = $now->copy()->addMinutes($countdown);
            $doku_virtual_account = DokuVirtualAccount::create([
                'invoice_id' => $invoice->id,
                'invoice_number' => $responseData['response']['order']['invoice_number'] ?? $invoice->inv_no,
                'currency' => $responseData['response']['order']['currency'] ?? $currency,
                'amount' => $responseData['response']['order']['amount'] ?? $amount,
                'created_date' => $created_date,
                'expired_date' => $expired_date,
                'token_id' => isset($responseData['response']['payment']['token_id']) ? $responseData['response']['payment']['token_id'] : null,
                'payment_duedate' => $countdown,
                'checkout_url' => isset($responseData['response']['payment']['url']) ? $responseData['response']['payment']['url'] : null,
                'status' => "Pending",
            ]);
            return $doku_virtual_account;
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function generateDokuPayment($order, $invoice, $agent){
        $now = Carbon::now();
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $cnyrates = Cache::remember('cny_rate', 3600, fn() => UsdRates::where('name', 'CNY')->first());
        $twdrates = Cache::remember('twd_rate', 3600, fn() => UsdRates::where('name', 'TWD')->first());
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $requestTarget = "/checkout/v1/payment";
        $final_price_idr = $order->final_price * $usdrates->rate;
        $currency = "IDR";
        $amount = $final_price_idr + ($final_price_idr * 0.03);
        $dokuPayments = DokuVirtualAccount::where('invoice_id',$invoice->id)->get();
        $reInvoiceNo = $dokuPayments ? count($dokuPayments) : 1;
        $invoice_number = "RE-".$invoice->inv_no."-".$reInvoiceNo;
        $body = [
            "order" => [
                "invoice_number" => $invoice_number,
                "amount" => (int) $amount,
                "currency" => $currency,
            ],
            "customer" => [
                "id" => $agent->id,
                "name" => $agent->name,
                "email" => $agent->email,
                "phone" => $agent->phone,
                "address" => $agent->address,
            ],
            "payment" => [
                "payment_due_date" => 10080,
                "type" => "SALE",
                "payment_method_types"=> [
                    "VIRTUAL_ACCOUNT_BCA",
                    "VIRTUAL_ACCOUNT_BANK_MANDIRI",
                    "VIRTUAL_ACCOUNT_BANK_SYARIAH_MANDIRI",
                    "VIRTUAL_ACCOUNT_DOKU",
                    "VIRTUAL_ACCOUNT_BRI",
                    "VIRTUAL_ACCOUNT_BNI",
                    "VIRTUAL_ACCOUNT_BANK_PERMATA",
                    "VIRTUAL_ACCOUNT_BANK_CIMB",
                    "VIRTUAL_ACCOUNT_BANK_DANAMON",
                    "ONLINE_TO_OFFLINE_ALFA",
                    "CREDIT_CARD",
                    "DIRECT_DEBIT_BRI",
                    "EMONEY_SHOPEEPAY",
                    "EMONEY_OVO",
                    "EMONEY_DANA",
                    "QRIS",
                    "PEER_TO_PEER_AKULAKU",
                    "PEER_TO_PEER_KREDIVO",
                    "PEER_TO_PEER_INDODANA"
                ]
            ],
            "virtual_account_info" => [
                "reusable_status" => false,
                "expired_time" => 10080,
                "info1" => "Detail tambahan 1",
                "info2" => "Detail tambahan 2"
            ],
        ];

        $signature = $this->generateSignature($body, $requestTarget, $timestamp);
        try {
            $response = Http::withHeaders([
                "Client-Id" => $this->clientId,
                "Request-Id" => $invoice_number,
                "Request-Timestamp" => $timestamp,
                "Signature" => $signature,
                "Content-Type" => "application/json"
            ])->post($this->baseUrl . $requestTarget, $body);
            $responseData = $response->json();
            $created_date = $now;
            $countdown = isset($responseData['response']['payment']['payment_due_date']) ? (int) $responseData['response']['payment']['payment_due_date'] : 10080;
            $expired_date = $now->copy()->addMinutes($countdown);
            DokuVirtualAccount::create([
                'invoice_id' => $invoice->id,
                'invoice_number' => $responseData['response']['order']['invoice_number'] ?? $invoice_number,
                'currency' => $responseData['response']['order']['currency'] ?? $currency,
                'amount' => $responseData['response']['order']['amount'] ?? $amount,
                'created_date' => $created_date,
                'expired_date' => $expired_date,
                'token_id' => isset($responseData['response']['payment']['token_id']) ? $responseData['response']['payment']['token_id'] : null,
                'payment_duedate' => $countdown,
                'checkout_url' => isset($responseData['response']['payment']['url']) ? $responseData['response']['payment']['url'] : null,
                'status' => "Pending",
            ]);

            return $responseData;
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
