<?php

namespace App\Services\Reservations;

use App\Models\Orders;
use App\Models\Reservation;
use App\Services\Pricing\OrderPricingSnapshotReader;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReservationDetailService
{
    public function __construct(
        private readonly OrderPricingSnapshotReader $pricingSnapshotReader
    ) {
    }

    public function data(Reservation $reservation): array
    {
        $reservation->loadMissing([
            'agent',
            'driver',
            'guide',
            'guests.order',
            'invoice',
            'restaurants',
            'includes',
            'excludes',
            'remarks',
            'spks',
        ]);

        $orders = Orders::query()
            ->with('activePricingSnapshot')
            ->where('rsv_id', $reservation->id)
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status', '!=', 'Deleted');
            })
            ->orderByRaw('CASE WHEN checkin IS NULL THEN 1 ELSE 0 END')
            ->orderBy('checkin')
            ->orderBy('id')
            ->get();

        $checkin = Carbon::parse($reservation->checkin);
        $checkout = Carbon::parse($reservation->checkout);
        $duration = max(1, $checkin->diffInDays($checkout));

        return [
            'reservation' => $reservation,
            'reservationStats' => $this->stats($reservation, $orders, $duration),
            'reservationOverview' => $this->overview($reservation, $checkin, $checkout, $duration),
            'reservationGuests' => $this->guestRows($reservation),
            'reservationServiceGroups' => $this->serviceGroups($orders, $reservation),
            'reservationMeals' => $reservation->restaurants
                ->sortBy('date')
                ->map(fn ($meal) => [
                    'date' => $this->formatDate($meal->date),
                    'breakfast' => $meal->breakfast ?: '-',
                    'lunch' => $meal->lunch ?: '-',
                    'dinner' => $meal->dinner ?: '-',
                ])
                ->values(),
            'reservationIncludes' => $reservation->includes->pluck('include')->filter()->values(),
            'reservationExcludes' => $reservation->excludes->pluck('exclude')->filter()->values(),
            'reservationRemarks' => $reservation->remarks->pluck('remark')->filter()->values(),
        ];
    }

    private function stats(Reservation $reservation, Collection $orders, int $duration): array
    {
        return [
            [
                'label' => __('reservations.service_duration'),
                'value' => $duration,
                'meta' => trans_choice('reservations.duration_days', $duration, ['count' => $duration]),
                'icon' => 'fa fa-calendar',
                'tone' => 'blue',
            ],
            [
                'label' => __('reservations.guests'),
                'value' => $reservation->guests->count(),
                'meta' => __('reservations.manifest_records'),
                'icon' => 'fa fa-users',
                'tone' => 'teal',
            ],
            [
                'label' => __('reservations.orders'),
                'value' => $orders->count(),
                'meta' => __('reservations.service_orders'),
                'icon' => 'fa fa-shopping-cart',
                'tone' => 'amber',
            ],
            [
                'label' => __('reservations.spk'),
                'value' => $reservation->spks->count(),
                'meta' => __('reservations.operational_documents'),
                'icon' => 'fa fa-file-alt',
                'tone' => 'green',
            ],
        ];
    }

    private function overview(Reservation $reservation, Carbon $checkin, Carbon $checkout, int $duration): array
    {
        $pickupGuest = $reservation->guests->firstWhere('id', (int) $reservation->pickup_name);

        return [
            'reference' => $reservation->rsv_no ?: '#'.$reservation->id,
            'status' => $reservation->status,
            'service' => $reservation->service ?: __('reservations.mixed_services'),
            'created_at' => $this->formatDate($reservation->created_at),
            'checkin' => $checkin->format('d M Y'),
            'checkout' => $checkout->format('d M Y'),
            'duration' => trans_choice('reservations.duration_days', $duration, ['count' => $duration]),
            'pickup_name' => $pickupGuest?->name ?: $reservation->customer_name ?: '-',
            'pickup_phone' => $pickupGuest?->phone ?: $reservation->phone ?: '-',
            'arrival_flight' => $reservation->arrival_flight ?: '-',
            'arrival_time' => $reservation->arrival_time ?: '-',
            'departure_flight' => $reservation->departure_flight ?: '-',
            'departure_time' => $reservation->departure_time ?: '-',
            'agent_name' => $reservation->agent?->name ?: '-',
            'agent_office' => $reservation->agent?->office ?: '-',
            'agent_phone' => $reservation->agent?->phone ?: '-',
            'agent_email' => $reservation->agent?->email ?: '-',
            'guide_name' => $reservation->guide?->name ?: __('reservations.not_assigned'),
            'guide_meta' => $reservation->guide?->language ?: null,
            'driver_name' => $reservation->driver?->name ?: __('reservations.not_assigned'),
            'driver_meta' => $reservation->driver?->phone ?: null,
            'invoice_reference' => $reservation->invoice?->inv_no,
            'invoice_due_date' => $this->formatDate($reservation->invoice?->due_date),
        ];
    }

    private function guestRows(Reservation $reservation): Collection
    {
        return $reservation->guests
            ->sortBy(fn ($guest) => [$guest->order?->checkin ?: '9999-12-31', $guest->id])
            ->map(fn ($guest) => [
                'name' => $guest->name ?: '-',
                'mandarin_name' => $guest->name_mandarin,
                'category' => $guest->age ?: '-',
                'gender' => $guest->sex ?: '-',
                'phone' => $guest->phone ?: '-',
                'service' => $guest->order?->servicename ?: $guest->order?->service,
                'order_reference' => $guest->order?->orderno,
                'order_url' => $guest->order ? route('admin.order.show', $guest->order->id) : null,
            ])
            ->values();
    }

    private function serviceGroups(Collection $orders, Reservation $reservation): Collection
    {
        return $orders
            ->groupBy(fn (Orders $order) => $this->serviceGroup($order->service))
            ->map(function (Collection $groupOrders, string $group) use ($reservation) {
                return [
                    'key' => $group,
                    'label' => __('reservations.service_group_'.$group),
                    'icon' => $this->serviceIcon($group),
                    'orders' => $groupOrders
                        ->map(fn (Orders $order) => $this->orderRow($order, $reservation))
                        ->values(),
                ];
            })
            ->values();
    }

    private function orderRow(Orders $order, Reservation $reservation): array
    {
        $tourPricing = $order->service === Orders::PUBLIC_TOUR_SERVICE
            ? $this->pricingSnapshotReader->historicalValues($order, $reservation->invoice)
            : null;
        $unitPrice = $tourPricing['unit_price_usd'] ?? null;
        $totalPrice = $tourPricing['total_usd']
            ?? ($order->final_total_usd_minor !== null ? $order->final_total_usd_minor / 100 : null)
            ?? $order->final_price
            ?? $order->price_total;

        return [
            'id' => $order->id,
            'reference' => $order->orderno ?: '#'.$order->id,
            'service' => $order->service ?: __('reservations.unknown_service'),
            'name' => $order->servicename ?: $order->package_name ?: $order->subservice ?: '-',
            'period' => $this->orderPeriod($order),
            'pax' => (int) ($order->number_of_guests ?: $order->capacity ?: 0),
            'status' => $order->status ?: __('reservations.unknown_status'),
            'status_tone' => $this->statusTone($order->status),
            'unit_price' => $unitPrice !== null ? $this->usd($unitPrice) : null,
            'total_price' => $totalPrice !== null ? $this->usd($totalPrice) : null,
            'location' => $order->pickup_location ?: $order->location ?: $order->src,
            'destination' => $order->dropoff_location ?: $order->dst,
            'detail_url' => route('admin.order.show', $order->id),
        ];
    }

    private function serviceGroup(?string $service): string
    {
        return match ($service) {
            'Hotel', 'Hotel Promo', 'Hotel Package' => 'accommodation',
            Orders::PUBLIC_TOUR_SERVICE, Orders::PUBLIC_ACTIVITY_SERVICE => 'experience',
            Orders::PUBLIC_TRANSPORT_SERVICE => 'transport',
            default => 'other',
        };
    }

    private function serviceIcon(string $group): string
    {
        return match ($group) {
            'accommodation' => 'fa fa-hotel',
            'experience' => 'fa fa-map',
            'transport' => 'fa fa-bus',
            default => 'fa fa-bell',
        };
    }

    private function statusTone(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'active', 'approved', 'paid' => 'success',
            'pending', 'draft' => 'warning',
            'canceled', 'rejected', 'invalid' => 'danger',
            default => 'muted',
        };
    }

    private function orderPeriod(Orders $order): string
    {
        $start = $order->travel_date ?: $order->checkin ?: $order->pickup_date;
        $end = $order->checkout ?: $order->dropoff_date;

        if (! $start) {
            return '-';
        }

        $formattedStart = $this->formatDate($start);
        $formattedEnd = $end ? $this->formatDate($end) : null;

        return $formattedEnd && $formattedEnd !== $formattedStart
            ? $formattedStart.' — '.$formattedEnd
            : $formattedStart;
    }

    private function formatDate($value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('d M Y');
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function usd($value): string
    {
        return '$'.number_format((float) $value, 2, '.', ',');
    }
}
