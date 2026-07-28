<?php

namespace App\Services;

use App\Models\Guests;
use App\Models\IncludeReservation;
use App\Models\Orders;
use App\Models\RemarkReservation;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AccommodationReservationService
{
    public function ensurePendingReservationForOrder(Orders $order): ?Reservation
    {
        if (!in_array($order->service, Orders::ACCOMMODATION_SERVICES, true)) {
            return null;
        }

        if ($order->rsv_id) {
            return Reservation::find($order->rsv_id);
        }

        if ($order->status !== 'Pending') {
            return null;
        }

        $reservation = Reservation::create($this->filterReservationPayload([
            'rsv_no' => $order->orderno,
            'service' => $order->service ?: 'Hotel',
            'checkin' => $order->checkin,
            'checkout' => $order->checkout,
            'agn_id' => $order->sales_agent ?: $order->user_id,
            'adm_id' => Auth::id() ?: ($order->sales_agent ?: $order->user_id),
            'status' => 'Pending',
            'arrival_flight' => $order->arrival_flight,
            'arrival_time' => $order->arrival_time,
            'departure_flight' => $order->departure_flight,
            'departure_time' => $order->departure_time,
        ]));

        $order->forceFill(['rsv_id' => $reservation->id])->save();

        if (Schema::hasTable('include_reservations') && $order->include) {
            IncludeReservation::create([
                'rsv_id' => $reservation->id,
                'include' => $order->include,
            ]);
        }

        if (Schema::hasTable('remark_reservations') && $order->note) {
            RemarkReservation::create([
                'rsv_id' => $reservation->id,
                'remark' => $order->note,
            ]);
        }

        if (Schema::hasTable('guests') && Schema::hasColumn('guests', 'order_id')) {
            Guests::where('order_id', $order->id)
                ->whereNull('rsv_id')
                ->update(['rsv_id' => $reservation->id]);
        }

        return $reservation;
    }

    private function filterReservationPayload(array $payload): array
    {
        static $columns = null;

        if ($columns === null) {
            $columns = collect(Schema::getColumnListing('reservations'))->flip()->all();
        }

        return collect($payload)
            ->filter(fn ($value, $key) => array_key_exists($key, $columns))
            ->all();
    }
}
