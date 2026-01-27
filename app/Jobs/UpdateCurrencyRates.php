<?php

namespace App\Jobs;

use App\Models\UsdRates;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class UpdateCurrencyRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $apiKey = config('app.exchange_rate_api_key');
        $response = Http::get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD");

        if ($response->successful()) {
            $rates = $response->json()['conversion_rates'];
            $currencies = ['USD', 'CNY', 'TWD'];
            foreach ($currencies as $currency) {
                $rate = $rates['IDR'] / $rates[$currency];
                $existingRate = UsdRates::where('name', $currency)->first();
                $difference = $existingRate ? $existingRate->difference : 100;
                UsdRates::updateOrCreate(
                    ['name' => $currency],
                    [
                        'rate' => $rate,
                        'sell' => $rate, 
                        'buy' => $rate - $difference, 
                    ]
                );
            }
        }
    }
}
