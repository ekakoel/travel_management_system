<?php

namespace App\Services\Hotels;

use App\Models\HotelPrice;
use Illuminate\Validation\ValidationException;

class HotelNormalPriceOverlapValidator
{
    public function ensureAvailable(
        int $hotelId,
        int $roomId,
        string $startDate,
        string $endDate,
        ?int $ignorePriceId = null
    ): void {
        $overlap = HotelPrice::query()
            ->where('hotels_id', $hotelId)
            ->where('rooms_id', $roomId)
            ->when($ignorePriceId, fn ($query) => $query->where('id', '!=', $ignorePriceId))
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->lockForUpdate()
            ->get(['id'])
            ->isNotEmpty();

        if ($overlap) {
            throw ValidationException::withMessages([
                'start_date' => 'The selected room already has a normal price during this stay period.',
            ]);
        }
    }
}
