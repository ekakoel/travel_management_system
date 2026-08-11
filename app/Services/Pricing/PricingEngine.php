<?php

namespace App\Services\Pricing;

use App\Data\Pricing\PricingQuote;
use App\Data\Pricing\ResolvedCurrencyRate;
use App\Data\Pricing\ResolvedTaxPolicy;
use App\Exceptions\PricingException;
use App\Support\Pricing\FixedScale;
use App\ValueObjects\Money;
use Carbon\CarbonImmutable;

final class PricingEngine
{
    public const VERSION = 'tour-package-v2';
    public const ROUNDING_POLICY = 'half-up-v1';

    public function calculate(
        string $service,
        int $serviceId,
        int $priceId,
        int $contractRateIdr,
        Money $markup,
        int $quantity,
        ResolvedCurrencyRate $rate,
        ResolvedTaxPolicy $tax,
        array $discountCandidates = [],
        ?CarbonImmutable $calculatedAt = null,
        array $context = [],
    ): PricingQuote {
        if ($service !== 'Tour Package' || $tax->service !== $service) {
            throw PricingException::tax('PRICING_TAX_INVALID', 'Tax policy does not match service.');
        }

        if ($contractRateIdr <= 0
            || $quantity <= 0
            || ! in_array($markup->currency, [Money::USD, Money::IDR], true)
        ) {
            throw PricingException::arithmetic('Invalid Tour Package pricing input.');
        }

        if ($rate->pair !== 'USD/IDR' || $rate->side !== 'sell') {
            throw PricingException::rate('PRICING_RATE_INVALID', 'Unsupported Tour Package rate.');
        }

        if ($tax->calculationType !== 'exclusive' || $tax->taxableBase !== 'contract_plus_markup') {
            throw PricingException::tax('PRICING_TAX_INVALID', 'Unsupported Tour Package tax policy.');
        }

        $markupIdr = $markup->currency === Money::USD
            ? FixedScale::multiplyDivideHalfUp(
                $markup->amount,
                $rate->valueScaled,
                100 * $rate->scale
            )
            : $markup->amount;
        $subtotalIdr = FixedScale::checkedAdd($contractRateIdr, $markupIdr);
        $taxAmountIdr = FixedScale::multiplyDivideHalfUp(
            $subtotalIdr,
            $tax->percentageScaled,
            100 * $tax->percentageScale
        );
        $unitPriceIdr = FixedScale::checkedAdd($subtotalIdr, $taxAmountIdr);
        $contractRateUsdMinor = $this->idrToUsdMinor($contractRateIdr, $rate);
        $taxAmountUsdMinor = $this->idrToUsdMinor($taxAmountIdr, $rate);
        $unitPriceUsdMinor = $this->idrToUsdMinor($unitPriceIdr, $rate);
        $grossTotalIdr = FixedScale::checkedMultiply($unitPriceIdr, $quantity);
        $grossTotalUsdMinor = $this->idrToUsdMinor($grossTotalIdr, $rate);
        [$resolvedCandidates, $selectedDiscount] = $this->resolveDiscounts(
            $discountCandidates,
            $grossTotalIdr,
            $rate
        );
        $discountTotalIdr = $selectedDiscount['amount_idr'] ?? 0;
        $discountTotalUsdMinor = $this->idrToUsdMinor($discountTotalIdr, $rate);
        $finalTotalIdr = FixedScale::checkedSubtractNonNegative($grossTotalIdr, $discountTotalIdr);
        $finalTotalUsdMinor = $this->idrToUsdMinor($finalTotalIdr, $rate);
        $calculatedAt ??= CarbonImmutable::now();

        return new PricingQuote([
            'pricing_version' => self::VERSION,
            'service' => $service,
            'service_id' => $serviceId,
            'price_id' => $priceId,
            'base_currency' => Money::IDR,
            'display_currency' => Money::USD,
            'contract_rate_idr' => $contractRateIdr,
            'contract_rate_usd_minor' => $contractRateUsdMinor,
            'markup_amount_minor' => $markup->amount,
            'markup_currency' => $markup->currency,
            'markup_type' => $context['markup_type'] ?? strtolower($markup->currency),
            'markup_input_amount' => $context['markup_input_amount'] ?? null,
            'markup_idr' => $markupIdr,
            'subtotal_idr' => $subtotalIdr,
            'tax_policy_id' => $tax->id,
            'tax_name' => $tax->name,
            'tax_percentage_scaled' => $tax->percentageScaled,
            'tax_percentage_scale' => $tax->percentageScale,
            'tax_amount_idr' => $taxAmountIdr,
            'tax_amount_usd_minor' => $taxAmountUsdMinor,
            'rate_id' => $rate->id,
            'rate_pair' => $rate->pair,
            'rate_side' => $rate->side,
            'rate_value_scaled' => $rate->valueScaled,
            'rate_value_scale' => $rate->scale,
            'rate_source' => $rate->source,
            'rate_retrieved_at' => $rate->retrievedAt->format('Y-m-d H:i:s.u'),
            'rate_max_age_seconds' => $rate->maxAgeSeconds,
            'rate_age_seconds' => $rate->ageSeconds,
            'quantity' => $quantity,
            'unit_price_idr' => $unitPriceIdr,
            'unit_price_usd_minor' => $unitPriceUsdMinor,
            'gross_total_idr' => $grossTotalIdr,
            'gross_total_usd_minor' => $grossTotalUsdMinor,
            'discount_candidates' => $resolvedCandidates,
            'selected_discount' => $selectedDiscount,
            'discount_total_idr' => $discountTotalIdr,
            'discount_total_usd_minor' => $discountTotalUsdMinor,
            'addon_total_idr' => 0,
            'addon_total_usd_minor' => 0,
            'final_total_idr' => $finalTotalIdr,
            'final_total_usd_minor' => $finalTotalUsdMinor,
            'rounding_policy' => self::ROUNDING_POLICY,
            'calculated_at' => $calculatedAt->format('Y-m-d H:i:s.u'),
            'input_fingerprint' => hash('sha256', json_encode([
                'service' => $service,
                'service_id' => $serviceId,
                'price_id' => $priceId,
                'quantity' => $quantity,
                'service_date' => $context['service_date'] ?? null,
                'promotion_id' => $context['promotion_id'] ?? null,
                'booking_code' => $context['booking_code'] ?? null,
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)),
        ]);
    }

    public function idrToUsdMinor(int $amountIdr, ResolvedCurrencyRate $rate): int
    {
        return FixedScale::multiplyDivideHalfUp(
            $amountIdr,
            100 * $rate->scale,
            $rate->valueScaled
        );
    }

    public function usdMinorToIdr(int $amountUsdMinor, ResolvedCurrencyRate $rate): int
    {
        return FixedScale::multiplyDivideHalfUp(
            $amountUsdMinor,
            $rate->valueScaled,
            100 * $rate->scale
        );
    }

    private function resolveDiscounts(
        array $candidates,
        int $grossTotalIdr,
        ResolvedCurrencyRate $rate,
    ): array {
        $resolved = [];

        foreach ($candidates as $candidate) {
            $type = $candidate['type'] ?? null;

            if ($type === 'fixed') {
                $amountIdr = match ($candidate['currency'] ?? null) {
                    Money::IDR => (int) ($candidate['amount_minor'] ?? 0),
                    Money::USD => $this->usdMinorToIdr((int) ($candidate['amount_minor'] ?? 0), $rate),
                    default => throw PricingException::unavailable('PRICING_DISCOUNT_INVALID'),
                };
            } elseif ($type === 'percentage') {
                $amountIdr = FixedScale::multiplyDivideHalfUp(
                    $grossTotalIdr,
                    (int) ($candidate['percentage_scaled'] ?? 0),
                    FixedScale::PERCENTAGE_DENOMINATOR
                );
            } else {
                throw PricingException::unavailable('PRICING_DISCOUNT_INVALID');
            }

            $amountIdr = min($amountIdr, $grossTotalIdr);
            $resolved[] = $candidate + [
                'amount_idr' => $amountIdr,
                'amount_usd_minor' => $this->idrToUsdMinor($amountIdr, $rate),
                'selected' => false,
            ];
        }

        usort($resolved, static function (array $left, array $right): int {
            $amountComparison = $right['amount_idr'] <=> $left['amount_idr'];

            if ($amountComparison !== 0) {
                return $amountComparison;
            }

            return ($left['source'] === 'promotion' ? 0 : 1)
                <=> ($right['source'] === 'promotion' ? 0 : 1);
        });

        $selected = $resolved[0] ?? null;

        if ($selected !== null) {
            $selected['selected'] = true;

            foreach ($resolved as $index => $candidate) {
                $resolved[$index]['selected'] = $candidate['source'] === $selected['source']
                    && ($candidate['identifier'] ?? null) === ($selected['identifier'] ?? null);
            }
        }

        return [$resolved, $selected];
    }
}
