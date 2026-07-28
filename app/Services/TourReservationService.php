<?php

namespace App\Services;

use App\Models\Guests;
use App\Models\Orders;
use App\Models\Reservation;
use Illuminate\Support\Facades\Schema;

class TourReservationService
{
    public function ensurePendingReservationForOrder(Orders $order): ?Reservation
    {
        if ($order->service !== Orders::PUBLIC_TOUR_SERVICE) {
            return null;
        }

        if ($order->rsv_id) {
            return Reservation::find($order->rsv_id);
        }

        if ($order->status !== 'Pending') {
            return null;
        }

        $reservation = Reservation::create([
            'rsv_no' => $order->orderno,
            'service' => Orders::PUBLIC_TOUR_SERVICE,
            'agn_id' => $order->sales_agent ?: $order->user_id,
            'adm_id' => $order->handled_by ?: $order->sales_agent ?: $order->user_id,
            'checkin' => $order->checkin,
            'checkout' => $order->checkout,
            'pickup_location' => $order->pickup_location,
            'dropoff_location' => $order->dropoff_location,
            'status' => 'Pending',
        ]);

        $order->forceFill(['rsv_id' => $reservation->id])->save();

        if (Schema::hasColumn('guests', 'rsv_id')) {
            Guests::where('order_id', $order->id)->whereNull('rsv_id')->update(['rsv_id' => $reservation->id]);
        }

        return $reservation;
    }
}
