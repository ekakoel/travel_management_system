<?php

namespace App\Services\Tours;

use App\Models\TourPrices;

class TourPriceOverlapValidator
{
    public function conflicts(
        int $tourId,
        int $minPax,
        int $maxPax,
        string $validFrom,
        string $validUntil,
        ?int $exceptPriceId = null,
        bool $lockForUpdate = false,
    ): bool {
        $query = TourPrices::query()
            ->where('tour_id', $tourId)
            ->whereNotNull('valid_from')
            ->whereNotNull('valid_until')
            ->where('min_qty', '<=', $maxPax)
            ->where('max_qty', '>=', $minPax)
            ->whereDate('valid_from', '<=', $validUntil)
            ->whereDate('valid_until', '>=', $validFrom);

        if ($exceptPriceId !== null) {
            $query->whereKeyNot($exceptPriceId);
        }

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        return $query->exists();
    }
}
