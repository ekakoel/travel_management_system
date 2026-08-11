<?php

namespace App\Services\Activities;

use App\Data\Pricing\ActivityPricingQuote;
use App\Data\Pricing\ResolvedCurrencyRate;
use App\Exceptions\PricingException;
use App\Models\Activities;
use App\Models\Promotion;
use App\Models\Tax;
use App\Services\Pricing\CurrencyRateResolver;
use App\Support\Pricing\FixedScale;
use Carbon\CarbonImmutable;

class ActivityPricingService
{
    private const USD_MINOR_SCALE = 100;
    private const USD_CALCULATION_SCALE = 1_000_000;

    private ?Tax $resolvedTax = null;

    private ?ResolvedCurrencyRate $resolvedUsdSellRate = null;

    private array $resolvedPromotions = [];

    public function __construct(
        private readonly CurrencyRateResolver $rateResolver,
    ) {
    }

    public function quote(
        Activities $activity,
        int $guestCount,
        ?CarbonImmutable $activityDate = null,
        ?CarbonImmutable $calculatedAt = null,
    ): ActivityPricingQuote {
        $calculatedAt ??= CarbonImmutable::now();
        $this->assertEligibility($activity, $guestCount, $activityDate, $calculatedAt);

        $rate = $this->resolvedUsdSellRate ??= $this->rateResolver->resolveUsdSell($calculatedAt);
        $tax = $this->resolveTax();
        $taxPercentageScaled = FixedScale::parseDecimal(
            $this->normaliseDecimal($tax->tax),
            FixedScale::PERCENTAGE_SCALE
        );
        $contractRateIdr = $this->wholeAmount($activity->contract_rate, 'contract rate');
        $markupUsdScaled = FixedScale::checkedMultiply(
            $this->wholeAmount($activity->markup, 'markup', true),
            self::USD_CALCULATION_SCALE
        );
        $contractRateUsdScaled = FixedScale::multiplyDivideHalfUp(
            $contractRateIdr,
            self::USD_CALCULATION_SCALE * $rate->scale,
            $rate->valueScaled
        );
        $subtotalUsdScaled = FixedScale::checkedAdd($contractRateUsdScaled, $markupUsdScaled);
        $taxAmountUsdScaled = FixedScale::multiplyDivideHalfUp(
            $subtotalUsdScaled,
            $taxPercentageScaled,
            FixedScale::PERCENTAGE_DENOMINATOR
        );
        $unitPriceUsdMinor = $this->roundActivityPriceUpFromScale(
            FixedScale::checkedAdd($subtotalUsdScaled, $taxAmountUsdScaled),
            self::USD_CALCULATION_SCALE
        );
        $grossTotalUsdMinor = FixedScale::checkedMultiply($unitPriceUsdMinor, $guestCount);
        [$promotions, $discountTotalUsdMinor] = $this->resolvePromotions($calculatedAt);
        $discountTotalUsdMinor = min($discountTotalUsdMinor, $grossTotalUsdMinor);
        $finalTotalUsdMinor = $this->roundActivityPriceUpFromScale(
            FixedScale::checkedSubtractNonNegative($grossTotalUsdMinor, $discountTotalUsdMinor)
        );

        return new ActivityPricingQuote(
            activityId: (int) $activity->getKey(),
            quantity: $guestCount,
            unitPriceUsdMinor: $unitPriceUsdMinor,
            grossTotalUsdMinor: $grossTotalUsdMinor,
            discountTotalUsdMinor: $discountTotalUsdMinor,
            finalTotalUsdMinor: $finalTotalUsdMinor,
            contractRateUsdMinor: $this->toUsdMinorHalfUp($contractRateUsdScaled),
            markupUsdMinor: $this->toUsdMinorHalfUp($markupUsdScaled),
            taxAmountUsdMinor: $this->toUsdMinorHalfUp($taxAmountUsdScaled),
            rate: $rate,
            taxId: (int) $tax->getKey(),
            taxPercentageScaled: $taxPercentageScaled,
            taxPercentageScale: FixedScale::PERCENTAGE_SCALE,
            validUntil: filled($activity->validity)
                ? CarbonImmutable::parse($activity->validity)->toDateString()
                : null,
            promotions: $promotions,
            calculatedAt: $calculatedAt->format('Y-m-d H:i:s.u'),
        );
    }

    private function assertEligibility(
        Activities $activity,
        int $guestCount,
        ?CarbonImmutable $activityDate,
        CarbonImmutable $calculatedAt
    ): void
    {
        if ($activity->status !== 'Active') {
            throw PricingException::unavailable('ACTIVITY_NOT_ACTIVE');
        }

        $minimumPax = max((int) ($activity->min_pax ?: 1), 1);
        $capacity = max((int) ($activity->qty ?: 0), 0);

        if ($guestCount < $minimumPax || ($capacity > 0 && $guestCount > $capacity)) {
            throw PricingException::unavailable('ACTIVITY_PAX_INVALID');
        }

        $validUntil = filled($activity->validity)
            ? CarbonImmutable::parse($activity->validity)->startOfDay()
            : null;

        if ($validUntil !== null && $validUntil->lt($calculatedAt->startOfDay())) {
            throw PricingException::unavailable('ACTIVITY_EXPIRED');
        }

        if ($validUntil !== null && $activityDate !== null && $activityDate->startOfDay()->gt($validUntil)) {
            throw PricingException::unavailable('ACTIVITY_PRICE_DATE_OUT_OF_VALIDITY');
        }

        if ($this->wholeAmount($activity->contract_rate, 'contract rate', true) <= 0) {
            throw PricingException::unavailable('ACTIVITY_PRICE_UNAVAILABLE');
        }
    }

    private function resolveTax(): Tax
    {
        if ($this->resolvedTax !== null) {
            return $this->resolvedTax;
        }

        $taxes = Tax::query()->where('name', 'tax')->limit(2)->get();

        if ($taxes->count() !== 1) {
            throw PricingException::tax(
                $taxes->isEmpty() ? 'ACTIVITY_TAX_MISSING' : 'ACTIVITY_TAX_AMBIGUOUS',
                'Activity tax configuration is unavailable.'
            );
        }

        return $this->resolvedTax = $taxes->first();
    }

    private function roundActivityPriceUpFromScale(int $amount, int $scale = self::USD_MINOR_SCALE): int
    {
        if ($amount <= 0) {
            return 0;
        }

        $wholeUsd = intdiv($amount, $scale);

        if ($amount % $scale !== 0) {
            $wholeUsd = FixedScale::checkedAdd($wholeUsd, 1);
        }

        return FixedScale::checkedMultiply(
            $wholeUsd,
            self::USD_MINOR_SCALE
        );
    }

    private function toUsdMinorHalfUp(int $amount): int
    {
        return FixedScale::multiplyDivideHalfUp($amount, self::USD_MINOR_SCALE, self::USD_CALCULATION_SCALE);
    }

    private function resolvePromotions(CarbonImmutable $calculatedAt): array
    {
        $cacheKey = $calculatedAt->format('Y-m-d');

        if (array_key_exists($cacheKey, $this->resolvedPromotions)) {
            return $this->resolvedPromotions[$cacheKey];
        }

        $promotions = Promotion::query()
            ->where('status', 'Active')
            ->whereDate('periode_start', '<=', $calculatedAt->toDateString())
            ->whereDate('periode_end', '>=', $calculatedAt->toDateString())
            ->orderBy('id')
            ->get();
        $resolved = [];
        $totalUsdMinor = 0;

        foreach ($promotions as $promotion) {
            $amountUsdMinor = FixedScale::parseDecimal(
                $this->normaliseDecimal($promotion->discounts),
                self::USD_MINOR_SCALE
            );
            $totalUsdMinor = FixedScale::checkedAdd($totalUsdMinor, $amountUsdMinor);
            $resolved[] = [
                'id' => (int) $promotion->getKey(),
                'name' => (string) $promotion->name,
                'amount_usd_minor' => $amountUsdMinor,
            ];
        }

        return $this->resolvedPromotions[$cacheKey] = [$resolved, $totalUsdMinor];
    }

    private function wholeAmount(float|int|string|null $value, string $field, bool $allowZero = false): int
    {
        $normalised = $this->normaliseDecimal($value ?? 0);

        try {
            $amount = FixedScale::parseDecimal($normalised, 1);
        } catch (PricingException $exception) {
            throw PricingException::arithmetic(
                "Activity {$field} must be a whole non-negative amount.",
                ['field' => $field]
            );
        }

        if (!$allowZero && $amount <= 0) {
            throw PricingException::arithmetic("Activity {$field} must be positive.");
        }

        return $amount;
    }

    private function normaliseDecimal(float|int|string|null $value): string
    {
        if (is_float($value)) {
            return rtrim(rtrim(sprintf('%.6F', $value), '0'), '.');
        }

        return trim((string) ($value ?? '0'));
    }
}
