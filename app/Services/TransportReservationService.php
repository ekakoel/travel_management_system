<?php

namespace App\Services;

use App\Models\Guests;
use App\Models\Orders;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class TransportReservationService
{
    public function ensurePendingReservationForOrder(Orders $order): ?Reservation
    {
        if ($order->service !== Orders::PUBLIC_TRANSPORT_SERVICE) {
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
            'service' => Orders::PUBLIC_TRANSPORT_SERVICE,
            'checkin' => $order->checkin,
            'checkout' => $order->checkout,
            'agn_id' => $order->sales_agent ?: $order->user_id,
            'adm_id' => Auth::id() ?: ($order->sales_agent ?: $order->user_id),
            'status' => 'Pending',
            'arrival_flight' => $order->arrival_flight,
            'arrival_time' => $order->arrival_time,
            'departure_flight' => $order->departure_flight,
            'departure_time' => $order->departure_time,
            'pickup_date' => $order->pickup_date,
            'pickup_location' => $order->pickup_location,
            'dropoff_date' => $order->dropoff_date,
            'dropoff_location' => $order->dropoff_location,
        ]));

        $order->forceFill(['rsv_id' => $reservation->id])->save();

        if (Schema::hasTable('guests') && Schema::hasColumn('guests', 'order_id') && Schema::hasColumn('guests', 'rsv_id')) {
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
