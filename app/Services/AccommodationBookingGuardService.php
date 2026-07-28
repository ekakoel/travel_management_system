<?php

namespace App\Services;

use App\Models\Orders;
use App\Models\HotelRoom;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class AccommodationBookingGuardService
{
    public const BLOCKING_ORDER_STATUSES = [
        'Pending',
        'Approved',
        'Paid',
        'Confirmed',
        'Active',
    ];

    public function ensureRoomCanBeBooked(
        int $hotelId,
        int $roomId,
        string $checkin,
        string $checkout,
        int $requestedRooms = 1,
        ?int $ignoreOrderId = null,
        bool $lock = false
    ): void {
        [$checkin, $checkout] = $this->normalizeStay($checkin, $checkout);

        if ($requestedRooms < 1) {
            throw ValidationException::withMessages([
                'number_of_guests' => 'At least one room must be selected.',
            ]);
        }

        $inventory = $this->resolveRoomInventory($hotelId, $roomId, $lock);

        if ($requestedRooms > $inventory) {
            throw ValidationException::withMessages([
                'availability' => "Only {$inventory} room(s) are available for this room type.",
            ]);
        }

        $query = Orders::query()
            ->accommodationService()
            ->where('service_id', $hotelId)
            ->where('subservice_id', $roomId)
            ->whereIn('status', self::BLOCKING_ORDER_STATUSES)
            ->whereDate('checkin', '<', $checkout)
            ->whereDate('checkout', '>', $checkin);

        if ($ignoreOrderId) {
            $query->where('id', '!=', $ignoreOrderId);
        }

        if ($lock) {
            $query->lockForUpdate();
        }

        $bookedRooms = (int) $query->sum('number_of_room');

        if (($bookedRooms + $requestedRooms) > $inventory) {
            $remaining = max($inventory - $bookedRooms, 0);
            throw ValidationException::withMessages([
                'availability' => "Only {$remaining} room(s) are available for these dates.",
            ]);
        }
    }

    public function resolveRoomInventory(int $hotelId, int $roomId, bool $lock = false): int
    {
        $fallback = max((int) config('services.accommodation.default_room_inventory', 1), 1);

        if (!Schema::hasTable('hotel_rooms')) {
            return $fallback;
        }

        $query = HotelRoom::query()
            ->where('id', $roomId)
            ->where('hotels_id', $hotelId);

        if ($lock) {
            $query->lockForUpdate();
        }

        $room = $query->first();

        if (!$room || !Schema::hasColumn('hotel_rooms', 'inventory')) {
            return $fallback;
        }

        $inventory = (int) $room->inventory;

        return $inventory > 0 ? $inventory : $fallback;
    }

    private function normalizeStay(string $checkin, string $checkout): array
    {
        try {
            $checkinDate = Carbon::parse($checkin)->startOfDay();
            $checkoutDate = Carbon::parse($checkout)->startOfDay();
        } catch (\Throwable $exception) {
            throw ValidationException::withMessages([
                'checkin' => 'Stay dates are invalid.',
            ]);
        }

        if (!$checkoutDate->greaterThan($checkinDate)) {
            throw ValidationException::withMessages([
                'checkout' => 'Checkout must be after checkin.',
            ]);
        }

        return [$checkinDate->format('Y-m-d'), $checkoutDate->format('Y-m-d')];
    }
}
