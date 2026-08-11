<?php

namespace App\Services\Pricing;

use App\Models\Orders;
use App\Models\TaxPolicy;
use App\Support\Pricing\FixedScale;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class TourTaxPolicyActivationService
{
    public function activateInitialPolicy(
        CarbonImmutable $effectiveFrom,
        int $approvedBy,
        string $name = 'Tour Package Tax 1.5%',
        string $percentage = '1.5',
    ): TaxPolicy {
        return DB::transaction(function () use ($effectiveFrom, $approvedBy, $name, $percentage) {
            $overlapExists = TaxPolicy::query()
                ->where('service', Orders::PUBLIC_TOUR_SERVICE)
                ->where('status', 'active')
                ->where(function ($query) use ($effectiveFrom) {
                    $query->whereNull('effective_until')
                        ->orWhere('effective_until', '>', $effectiveFrom);
                })
                ->lockForUpdate()
                ->exists();

            if ($overlapExists) {
                throw new InvalidArgumentException(
                    'An active Tour Package tax policy overlaps the requested effective period.'
                );
            }

            return $this->createPolicy($percentage, $effectiveFrom, $approvedBy, $name);
        });
    }

    public function replaceActivePolicy(
        string $percentage,
        CarbonImmutable $effectiveFrom,
        int $approvedBy,
    ): TaxPolicy {
        return DB::transaction(function () use ($percentage, $effectiveFrom, $approvedBy) {
            $percentageScaled = FixedScale::parseDecimal(
                $percentage,
                FixedScale::PERCENTAGE_SCALE
            );
            $overlapping = TaxPolicy::query()
                ->where('service', Orders::PUBLIC_TOUR_SERVICE)
                ->where('status', 'active')
                ->where(function ($query) use ($effectiveFrom) {
                    $query->whereNull('effective_until')
                        ->orWhere('effective_until', '>', $effectiveFrom);
                })
                ->orderBy('effective_from')
                ->lockForUpdate()
                ->get();

            if ($overlapping->count() > 1) {
                throw new InvalidArgumentException(
                    'Multiple active Tour Package tax policies overlap the requested effective period.'
                );
            }

            $current = $overlapping->first();

            if ($current !== null) {
                if ($current->effective_from->gt($effectiveFrom)) {
                    throw new InvalidArgumentException(
                        'A future Tour Package tax policy already overlaps the requested effective period.'
                    );
                }

                if ((int) $current->percentage_scaled === $percentageScaled
                    && (int) $current->percentage_scale === FixedScale::PERCENTAGE_SCALE
                ) {
                    return $current;
                }

                $current->update(['effective_until' => $effectiveFrom]);
            }

            return $this->createPolicy(
                $percentage,
                $effectiveFrom,
                $approvedBy,
                "Tour Package Tax {$percentage}%",
            );
        });
    }

    private function createPolicy(
        string $percentage,
        CarbonImmutable $effectiveFrom,
        int $approvedBy,
        string $name,
    ): TaxPolicy {
        return TaxPolicy::create([
            'service' => Orders::PUBLIC_TOUR_SERVICE,
            'name' => $name,
            'percentage_scaled' => FixedScale::parseDecimal(
                $percentage,
                FixedScale::PERCENTAGE_SCALE
            ),
            'percentage_scale' => FixedScale::PERCENTAGE_SCALE,
            'calculation_type' => 'exclusive',
            'taxable_base' => 'contract_plus_markup',
            'status' => 'active',
            'effective_from' => $effectiveFrom,
            'effective_until' => null,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }
}
