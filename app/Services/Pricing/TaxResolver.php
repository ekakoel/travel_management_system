<?php

namespace App\Services\Pricing;

use App\Data\Pricing\ResolvedTaxPolicy;
use App\Exceptions\PricingException;
use App\Models\TaxPolicy;
use Carbon\CarbonImmutable;

final class TaxResolver
{
    public function resolve(string $service, ?CarbonImmutable $calculatedAt = null): ResolvedTaxPolicy
    {
        $calculatedAt ??= CarbonImmutable::now();
        $matches = TaxPolicy::query()
            ->where('service', $service)
            ->where('status', 'active')
            ->where('effective_from', '<=', $calculatedAt)
            ->where(function ($query) use ($calculatedAt) {
                $query->whereNull('effective_until')
                    ->orWhere('effective_until', '>', $calculatedAt);
            })
            ->orderByDesc('effective_from')
            ->limit(2)
            ->get();

        if ($matches->isEmpty()) {
            throw PricingException::tax('PRICING_TAX_MISSING', 'Tour Package tax policy is unavailable.');
        }

        if ($matches->count() !== 1) {
            throw PricingException::tax('PRICING_TAX_AMBIGUOUS', 'Tour Package tax policy is ambiguous.');
        }

        $policy = $matches->first();

        if ((int) $policy->percentage_scaled < 0
            || (int) $policy->percentage_scale <= 0
            || $policy->approved_at === null
            || $policy->approved_by === null) {
            throw PricingException::tax('PRICING_TAX_INVALID', 'Tour Package tax policy is invalid.');
        }

        return new ResolvedTaxPolicy(
            id: (int) $policy->id,
            name: (string) $policy->name,
            service: (string) $policy->service,
            percentageScaled: (int) $policy->percentage_scaled,
            percentageScale: (int) $policy->percentage_scale,
            calculationType: (string) $policy->calculation_type,
            taxableBase: (string) $policy->taxable_base,
            effectiveFrom: CarbonImmutable::instance($policy->effective_from)->toDateTimeImmutable(),
            effectiveUntil: $policy->effective_until
                ? CarbonImmutable::instance($policy->effective_until)->toDateTimeImmutable()
                : null,
        );
    }
}
