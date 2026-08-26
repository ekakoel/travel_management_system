<?php

namespace App\Data\Pricing;

use App\Support\MoneyFormatter;
use App\Support\Pricing\FixedScale;
use App\ValueObjects\Money;
use JsonSerializable;

final class ActivityPricingQuote implements JsonSerializable
{
    public function __construct(
        public readonly int $activityId,
        public readonly int $quantity,
        public readonly int $unitPriceUsdMinor,
        public readonly int $grossTotalUsdMinor,
        public readonly int $discountTotalUsdMinor,
        public readonly int $finalTotalUsdMinor,
        public readonly int $contractRateUsdMinor,
        public readonly int $markupUsdMinor,
        public readonly int $taxAmountUsdMinor,
        public readonly ResolvedCurrencyRate $rate,
        public readonly int $taxId,
        public readonly int $taxPercentageScaled,
        public readonly int $taxPercentageScale,
        public readonly ?string $validUntil,
        public readonly array $promotions,
        public readonly string $calculatedAt,
    ) {
    }

    public function unitPriceUsd(): string
    {
        return app(MoneyFormatter::class)->decimal(Money::usdCents($this->unitPriceUsdMinor));
    }

    public function unitPriceIdr(): int
    {
        return $this->usdMinorToIdr($this->unitPriceUsdMinor);
    }

    public function grossTotalUsd(): string
    {
        return app(MoneyFormatter::class)->decimal(Money::usdCents($this->grossTotalUsdMinor));
    }

    public function grossTotalIdr(): int
    {
        return $this->usdMinorToIdr($this->grossTotalUsdMinor);
    }

    public function discountTotalUsd(): string
    {
        return app(MoneyFormatter::class)->decimal(Money::usdCents($this->discountTotalUsdMinor));
    }

    public function discountTotalIdr(): int
    {
        return $this->usdMinorToIdr($this->discountTotalUsdMinor);
    }

    public function finalTotalUsd(): string
    {
        return app(MoneyFormatter::class)->decimal(Money::usdCents($this->finalTotalUsdMinor));
    }

    public function finalTotalIdr(): int
    {
        return $this->usdMinorToIdr($this->finalTotalUsdMinor);
    }

    public function taxAmountUsd(): string
    {
        return app(MoneyFormatter::class)->decimal(Money::usdCents($this->taxAmountUsdMinor));
    }

    public function taxAmountIdr(): int
    {
        return $this->usdMinorToIdr($this->taxAmountUsdMinor);
    }

    public function contractRateUsd(): string
    {
        return app(MoneyFormatter::class)->decimal(Money::usdCents($this->contractRateUsdMinor));
    }

    public function contractRateIdr(): int
    {
        return $this->usdMinorToIdr($this->contractRateUsdMinor);
    }

    public function markupUsd(): string
    {
        return app(MoneyFormatter::class)->decimal(Money::usdCents($this->markupUsdMinor));
    }

    public function markupIdr(): int
    {
        return $this->usdMinorToIdr($this->markupUsdMinor);
    }

    public function taxPercentage(): string
    {
        return rtrim(rtrim(
            FixedScale::formatDecimal($this->taxPercentageScaled, $this->taxPercentageScale),
            '0'
        ), '.');
    }

    public function toArray(): array
    {
        return [
            'pricing_version' => 'activity-v1',
            'service' => 'Activity',
            'activity_id' => $this->activityId,
            'quantity' => $this->quantity,
            'display_currency' => Money::USD,
            'contract_rate_usd_minor' => $this->contractRateUsdMinor,
            'markup_usd_minor' => $this->markupUsdMinor,
            'tax_amount_usd_minor' => $this->taxAmountUsdMinor,
            'unit_price_usd_minor' => $this->unitPriceUsdMinor,
            'gross_total_usd_minor' => $this->grossTotalUsdMinor,
            'discount_total_usd_minor' => $this->discountTotalUsdMinor,
            'final_total_usd_minor' => $this->finalTotalUsdMinor,
            'rate_id' => $this->rate->id,
            'rate_pair' => $this->rate->pair,
            'rate_side' => $this->rate->side,
            'rate_value_scaled' => $this->rate->valueScaled,
            'rate_value_scale' => $this->rate->scale,
            'rate_source' => $this->rate->source,
            'rate_retrieved_at' => $this->rate->retrievedAt->format('Y-m-d H:i:s.u'),
            'tax_id' => $this->taxId,
            'tax_percentage_scaled' => $this->taxPercentageScaled,
            'tax_percentage_scale' => $this->taxPercentageScale,
            'valid_until' => $this->validUntil,
            'promotions' => $this->promotions,
            'calculated_at' => $this->calculatedAt,
            'rounding_policy' => 'ceiling-whole-usd-v1',
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    private function usdMinorToIdr(int $usdMinor): int
    {
        return FixedScale::multiplyDivideHalfUp(
            $usdMinor,
            $this->rate->valueScaled,
            100 * $this->rate->scale
        );
    }
}
