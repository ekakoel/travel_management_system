<?php

namespace App\Services\Hotels;

use App\Models\HotelPackage;
use App\Models\HotelPromo;
use App\Models\HotelRoom;
use App\Models\Hotels;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

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

    public function auditPriceDrivenStatuses(CarbonInterface|string|null $date = null): array
    {
        $today = $date instanceof CarbonInterface
            ? $date->toDateString()
            : Carbon::parse($date ?? now())->toDateString();

        $summary = [
            'hotels_checked' => 0,
            'hotels_activated' => 0,
            'hotels_drafted' => 0,
            'rooms_activated' => 0,
            'rooms_drafted' => 0,
        ];

        DB::transaction(function () use (&$summary, $today): void {
            Hotels::query()
                ->whereNotIn('status', ['Archived', 'Removed'])
                ->withCount([
                    'prices as valid_normal_prices_count' => fn ($query) => $query->notExpired($today),
                    'promos as valid_promos_count' => fn ($query) => $query->notExpired($today),
                    'packages as valid_packages_count' => fn ($query) => $query->notExpired($today),
                ])
                ->orderBy('id')
                ->chunkById(100, function ($hotels) use (&$summary): void {
                    foreach ($hotels as $hotel) {
                        $summary['hotels_checked']++;

                        $hasPrice = $hotel->valid_normal_prices_count > 0
                            || $hotel->valid_promos_count > 0
                            || $hotel->valid_packages_count > 0;
                        $targetStatus = $hasPrice ? 'Active' : 'Draft';

                        if ($hotel->status !== $targetStatus) {
                            $hotel->update(['status' => $targetStatus]);

                            if ($targetStatus === 'Active') {
                                $summary['hotels_activated']++;
                            } else {
                                $summary['hotels_drafted']++;
                            }
                        }

                        $roomUpdates = HotelRoom::query()
                            ->where('hotels_id', $hotel->id)
                            ->where('status', '!=', 'Archived')
                            ->where('status', '!=', $targetStatus)
                            ->update(['status' => $targetStatus]);

                        if ($targetStatus === 'Active') {
                            $summary['rooms_activated'] += $roomUpdates;
                        } else {
                            $summary['rooms_drafted'] += $roomUpdates;
                        }
                    }
                });
        }, 3);

        return $summary;
    }
}
