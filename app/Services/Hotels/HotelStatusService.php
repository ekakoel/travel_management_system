<?php

namespace App\Services\Hotels;

use App\Models\HotelPackage;
use App\Models\HotelPromo;
use App\Models\Hotels;
use Carbon\CarbonInterface;

class HotelStatusService
{
    public function expirePromosForHotel(int $hotelId, CarbonInterface|string $date): int
    {
        return HotelPromo::where('hotels_id', $hotelId)
            ->where('book_periode_end', '<', $date)
            ->where('status', '!=', 'Expired')
            ->update(['status' => 'Expired']);
    }

    public function expirePackagesForHotel(int $hotelId, CarbonInterface|string $date): int
    {
        return HotelPackage::where('hotels_id', $hotelId)
            ->where('stay_period_end', '<', $date)
            ->where('status', '!=', 'Expired')
            ->update(['status' => 'Expired']);
    }

    public function defaultHotelStatus(): string
    {
        return 'Draft';
    }

    public function defaultRoomStatus(): string
    {
        return 'Active';
    }

    public function shouldDraftHotel(Hotels $hotel): bool
    {
        return $hotel->rooms()->active()->doesntExist();
    }
}
