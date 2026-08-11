<?php

namespace App\Services\Pricing;

use App\Exceptions\CurrencyRateRefreshException;
use App\Models\UsdRates;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class CurrencyRateRefreshService
{
    public const CURRENCIES = ['USD', 'CNY', 'TWD'];

    private const DEFAULT_SPREADS = [
        'USD' => 100,
        'CNY' => 30,
        'TWD' => 5,
    ];

    public function refresh(): array
    {
        $referenceRates = $this->fetchReferenceRates();
        $retrievedAt = Carbon::now();

        DB::transaction(function () use ($referenceRates, $retrievedAt) {
            $records = UsdRates::query()
                ->whereIn('name', self::CURRENCIES)
                ->lockForUpdate()
                ->get()
                ->groupBy('name');

            foreach (self::CURRENCIES as $currency) {
                if (($records->get($currency)?->count() ?? 0) > 1) {
                    throw new CurrencyRateRefreshException(
                        "Automatic currency refresh stopped because {$currency} has duplicate rate records."
                    );
                }
            }

            foreach (self::CURRENCIES as $currency) {
                $record = $records->get($currency)?->first() ?? new UsdRates(['name' => $currency]);
                $spread = is_numeric($record->difference)
                    ? max((float) $record->difference, 0)
                    : self::DEFAULT_SPREADS[$currency];
                $sell = (float) $referenceRates[$currency];

                $record->fill([
                    'rate' => $this->decimal($sell),
                    'sell' => $this->decimal($sell),
                    'buy' => $this->decimal(max($sell - $spread, 0)),
                    'difference' => $this->decimal($spread),
                    'retrieved_at' => $retrievedAt,
                    'retrieval_source' => 'exchangerate-api-v6',
                ])->save();
            }
        });

        $this->clearRateCaches();

        Log::info('Currency rates refreshed.', [
            'currencies' => self::CURRENCIES,
            'retrieved_at' => $retrievedAt->toIso8601String(),
            'source' => 'exchangerate-api-v6',
        ]);

        return [
            'rates' => $referenceRates,
            'retrieved_at' => $retrievedAt,
            'source' => 'exchangerate-api-v6',
        ];
    }

    public function fetchReferenceRates(): array
    {
        $apiKey = (string) config('services.exchange_rate.key');

        if ($apiKey === '') {
            throw new CurrencyRateRefreshException('Exchange-rate API key is not configured.');
        }

        $baseUrl = rtrim((string) config('services.exchange_rate.base_url'), '/');

        try {
            $response = Http::acceptJson()
                ->timeout(10)
                ->retry(3, 500, null, false)
                ->get("{$baseUrl}/{$apiKey}/latest/USD");
        } catch (Throwable $exception) {
            throw new CurrencyRateRefreshException(
                'Exchange-rate provider could not be reached.',
                previous: $exception,
            );
        }

        if (! $response->successful() || $response->json('result') !== 'success') {
            $errorType = (string) $response->json('error-type', 'unavailable');

            throw new CurrencyRateRefreshException(
                "Exchange-rate provider rejected the request ({$errorType})."
            );
        }

        $conversionRates = $response->json('conversion_rates', []);
        $idrPerUsd = $this->positiveRate($conversionRates, 'IDR');
        $cnyPerUsd = $this->positiveRate($conversionRates, 'CNY');
        $twdPerUsd = $this->positiveRate($conversionRates, 'TWD');
        $this->positiveRate($conversionRates, 'USD');

        return [
            'USD' => $this->decimal($idrPerUsd),
            'CNY' => $this->decimal($idrPerUsd / $cnyPerUsd),
            'TWD' => $this->decimal($idrPerUsd / $twdPerUsd),
        ];
    }

    public function clearRateCaches(): void
    {
        Cache::forget('usd_rates');
        Cache::forget('pricing.usd_sell');
        Cache::forget('backend.currency.external_rates');
    }

    private function positiveRate(array $rates, string $currency): float
    {
        $value = $rates[$currency] ?? null;

        if (! is_numeric($value) || ! is_finite((float) $value) || (float) $value <= 0) {
            throw new CurrencyRateRefreshException(
                "Exchange-rate response has an invalid {$currency} conversion rate."
            );
        }

        return (float) $value;
    }

    private function decimal(float $value): string
    {
        return rtrim(rtrim(number_format($value, 6, '.', ''), '0'), '.');
    }
}
