<?php

namespace App\Services\Hotels;

use App\Models\HotelPackage;
use App\Models\HotelPromo;
use App\Models\Hotels;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class HotelStatusService
{
    public function expirePromosForHotel(int $hotelId, CarbonInterface|string $date): int
    {
        $today = $date instanceof CarbonInterface
            ? $date->toDateString()
            : Carbon::parse($date)->toDateString();

        return HotelPromo::where('hotels_id', $hotelId)
            ->whereDate('book_periode_end', '<', $today)
            ->where('status', '!=', 'Expired')
            ->update(['status' => 'Expired']);
    }

    public function expirePackagesForHotel(int $hotelId, CarbonInterface|string $date): int
    {
        $today = $date instanceof CarbonInterface
            ? $date->toDateString()
            : Carbon::parse($date)->toDateString();

        return HotelPackage::where('hotels_id', $hotelId)
            ->whereDate('stay_period_end', '<', $today)
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
