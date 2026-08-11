<?php

namespace App\Services\Pricing;

use App\Data\Pricing\ResolvedCurrencyRate;
use App\Exceptions\PricingException;
use App\Models\UsdRates;
use App\Support\Pricing\FixedScale;
use Carbon\CarbonImmutable;

final class CurrencyRateResolver
{
    public const MAX_AGE_SECONDS = 86_400;
    public const FUTURE_TOLERANCE_SECONDS = 60;

    public function resolveUsdSell(?CarbonImmutable $calculatedAt = null): ResolvedCurrencyRate
    {
        $calculatedAt ??= CarbonImmutable::now();
        $matches = UsdRates::query()->where('name', 'USD')->limit(2)->get();

        if ($matches->isEmpty()) {
            throw PricingException::rate('PRICING_RATE_MISSING', 'USD sell rate is unavailable.');
        }

        if ($matches->count() !== 1) {
            throw PricingException::rate('PRICING_RATE_AMBIGUOUS', 'USD sell rate is ambiguous.');
        }

        $row = $matches->first();

        try {
            $valueScaled = FixedScale::parseDecimal((string) $row->sell, FixedScale::FX_SCALE);
        } catch (PricingException $exception) {
            throw PricingException::rate('PRICING_RATE_INVALID', 'USD sell rate is invalid.', [
                'rate_id' => $row->id,
            ]);
        }

        if ($valueScaled <= 0) {
            throw PricingException::rate('PRICING_RATE_INVALID', 'USD sell rate must be positive.', [
                'rate_id' => $row->id,
            ]);
        }

        $retrievedAt = $row->retrieved_at ?? $row->updated_at;

        if ($retrievedAt === null) {
            throw PricingException::rate('PRICING_RATE_INVALID', 'USD sell rate has no retrieval timestamp.', [
                'rate_id' => $row->id,
            ]);
        }

        $retrievedAt = CarbonImmutable::instance($retrievedAt);
        $ageSeconds = $retrievedAt->diffInSeconds($calculatedAt, false);

        if ($ageSeconds < -self::FUTURE_TOLERANCE_SECONDS) {
            throw PricingException::rate('PRICING_RATE_INVALID', 'USD sell rate timestamp is in the future.', [
                'rate_id' => $row->id,
            ]);
        }

        $ageSeconds = max($ageSeconds, 0);

        if ($ageSeconds > self::MAX_AGE_SECONDS) {
            throw PricingException::rate('PRICING_RATE_STALE', 'USD sell rate is stale.', [
                'rate_id' => $row->id,
                'age_seconds' => $ageSeconds,
                'max_age_seconds' => self::MAX_AGE_SECONDS,
            ]);
        }

        return new ResolvedCurrencyRate(
            id: (int) $row->id,
            pair: 'USD/IDR',
            side: 'sell',
            valueScaled: $valueScaled,
            scale: FixedScale::FX_SCALE,
            source: (string) ($row->retrieval_source ?: 'legacy_updated_at'),
            retrievedAt: $retrievedAt->toDateTimeImmutable(),
            maxAgeSeconds: self::MAX_AGE_SECONDS,
            ageSeconds: $ageSeconds,
        );
    }
}
