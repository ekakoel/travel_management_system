<?php

namespace App\Services;

use App\Models\BusinessProfile;
use App\Models\InvoiceAdmin;
use App\Models\Orders;
use App\Models\Reservation;
use App\Models\User;
use App\Services\Pricing\OrderPricingSnapshotReader;
use Carbon\Carbon;

final class OrderConfirmationEmailDataService
{
    public function build(
        Orders $order,
        ?Reservation $reservation,
        ?InvoiceAdmin $invoice,
        ?User $confirmedBy
    ): array {
        $business = BusinessProfile::query()->first();
        $agent = User::query()->find($order->sales_agent ?: $order->user_id);
        $currencyCode = $invoice?->currency()->value('name') ?: 'USD';
        $totalUsd = $this->totalUsd($order, $invoice);
        $brandName = $business?->name ?: config('app.name', 'Bali Kami Tour');

        return [
            'subject' => "Order Confirmed | {$order->orderno} | {$brandName}",
            'preheader' => "Order {$order->orderno} has been confirmed. Review the details and attached invoices.",
            'brand' => [
                'name' => $brandName,
                'tagline' => $business?->public_tagline ?: $business?->caption ?: 'Travel services in Bali',
                'email' => $business?->email ?: config('app.reservation_mail'),
                'phone' => $business?->phone,
                'website' => $business?->website ?: config('app.url'),
            ],
            'recipient_name' => $agent?->name ?: $order->name ?: 'Travel Partner',
            'status' => 'Confirmed',
            'confirmed_at' => $this->formatDateTime($order->updated_at),
            'confirmed_by' => $confirmedBy?->name ?: 'Reservations Team',
            'order' => [
                'reference' => $order->orderno ?: '-',
                'reservation_reference' => $reservation?->rsv_no ?: '-',
                'service' => $order->service ?: '-',
                'product' => $order->servicename ?: $order->subservice ?: '-',
                'travel_start' => $this->formatDate($order->checkin),
                'travel_end' => $this->formatDate($order->checkout),
                'guests' => $order->number_of_guests ? (int) $order->number_of_guests.' pax' : '-',
                'pickup' => $order->pickup_location ?: '-',
                'dropoff' => $order->dropoff_location ?: '-',
            ],
            'billing' => [
                'invoice_number' => $invoice?->inv_no ?: '-',
                'due_date' => $this->formatDate($invoice?->due_date),
                'total_usd' => $this->formatUsd($totalUsd),
                'currency' => $currencyCode,
                'amount_due' => $this->formatAmountDue($invoice, $currencyCode, $totalUsd),
            ],
            'attachments' => $order->service === Orders::PUBLIC_TOUR_SERVICE
                ? 'Three invoice PDFs are attached: English, Chinese Simplified, and Chinese Traditional.'
                : 'Your order documents are attached to this email.',
            'action_url' => $this->orderDetailUrl($order),
        ];
    }

    private function totalUsd(Orders $order, ?InvoiceAdmin $invoice): string
    {
        if ($order->service === Orders::PUBLIC_TOUR_SERVICE) {
            return app(OrderPricingSnapshotReader::class)->historicalValues($order, $invoice)['total_usd'];
        }

        return (string) ($invoice?->total_usd ?: $order->final_price ?: 0);
    }

    private function orderDetailUrl(Orders $order): string
    {
        return match (true) {
            in_array($order->service, ['Hotel', 'Hotel Promo', 'Hotel Package', 'Activity'], true) =>
                route('view.detail-order-hotel', ['id' => $order->id]),
            $order->service === 'Private Villa' =>
                route('view.detail-order-villa', ['id' => $order->id]),
            $order->service === Orders::PUBLIC_TOUR_SERVICE =>
                route('view.detail-order-tour', ['id' => $order->id]),
            $order->service === Orders::PUBLIC_TRANSPORT_SERVICE =>
                route('view.detail-order-transport', ['id' => $order->id]),
            default => route('view.detail-order', ['id' => $order->id]),
        };
    }

    private function formatAmountDue(?InvoiceAdmin $invoice, string $currencyCode, string $totalUsd): string
    {
        return match ($currencyCode) {
            'CNY' => 'CNY '.number_format((float) $invoice?->total_cny, 0),
            'TWD' => 'NT$ '.number_format((float) $invoice?->total_twd, 0),
            'IDR' => 'IDR '.number_format((float) $invoice?->total_idr, 0),
            default => $this->formatUsd($totalUsd),
        };
    }

    private function formatUsd(string $amount): string
    {
        return '$'.number_format((float) $amount, 2, '.', ',');
    }

    private function formatDate($value): string
    {
        return $value ? Carbon::parse($value)->format('d M Y') : '-';
    }

    private function formatDateTime($value): string
    {
        return $value ? Carbon::parse($value)->format('d M Y, H:i T') : '-';
    }
}
