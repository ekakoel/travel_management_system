<?php

namespace App\Services;

use App\Models\Orders;
use App\Models\Transports;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class TransportAvailabilityService
{
    public const BLOCKING_STATUSES = [
        'Pending',
        'Approved',
        'Paid',
        'Active',
        'Confirmed',
    ];

    public function effectiveInventory(Transports $transport): int
    {
        return $transport->inventory === null ? 1 : max((int) $transport->inventory, 0);
    }

    public function remainingInventory(int $transportId, string $checkin, string $checkout, ?int $ignoreOrderId = null): int
    {
        $transport = Transports::findOrFail($transportId);

        return max($this->effectiveInventory($transport) - $this->blockingOrderCount($transportId, $checkin, $checkout, $ignoreOrderId), 0);
    }

    public function ensureCanBook(int $transportId, string $checkin, string $checkout, ?int $ignoreOrderId = null, bool $lock = false): void
    {
        $transportQuery = Transports::whereKey($transportId);

        if ($lock) {
            $transportQuery->lockForUpdate();
        }

        $transport = $transportQuery->firstOrFail();
        $inventory = $this->effectiveInventory($transport);

        if ($transport->status !== 'Active' || $inventory < 1) {
            throw ValidationException::withMessages([
                'transport_id' => 'The selected transport is not available for public booking.',
            ]);
        }

        $blockingCount = $this->blockingOrderCount($transportId, $checkin, $checkout, $ignoreOrderId, $lock);

        if ($blockingCount >= $inventory) {
            throw ValidationException::withMessages([
                'transport_id' => 'The selected transport is fully booked for the selected service time.',
            ]);
        }
    }

    public function blockingOrderCount(int $transportId, string $checkin, string $checkout, ?int $ignoreOrderId = null, bool $lock = false): int
    {
        $start = Carbon::parse($checkin);
        $end = Carbon::parse($checkout);

        $query = Orders::query()
            ->where('service', Orders::PUBLIC_TRANSPORT_SERVICE)
            ->where('service_id', $transportId)
            ->whereIn('status', self::BLOCKING_STATUSES)
            ->where('checkin', '<', $end)
            ->where('checkout', '>', $start);

        if ($ignoreOrderId) {
            $query->whereKeyNot($ignoreOrderId);
        }

        if (Schema::hasColumn('orders', 'completed_at')) {
            $query->whereNull('completed_at');
        }

        if ($lock) {
            return $query->select('id')->lockForUpdate()->get()->count();
        }

        return $query->count();
    }
}
