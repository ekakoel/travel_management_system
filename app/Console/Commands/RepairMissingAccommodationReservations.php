<?php

namespace App\Console\Commands;

use App\Models\OrderLog;
use App\Models\Orders;
use App\Services\AccommodationReservationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairMissingAccommodationReservations extends Command
{
    protected $signature = 'accommodation:repair-missing-reservations {--dry-run : Report eligible orders without writing}';

    protected $description = 'Create missing pending reservations for pending Accommodation orders in an idempotent way.';

    public function handle(AccommodationReservationService $reservationService): int
    {
        $orders = Orders::query()
            ->accommodationService()
            ->where('status', 'Pending')
            ->whereNull('rsv_id')
            ->orderBy('id')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No missing Accommodation reservations found.');
            $this->line('Changed: 0');

            return self::SUCCESS;
        }

        $this->table(
            ['Order ID', 'Order No', 'Service', 'Status', 'Check-in', 'Check-out'],
            $orders->map(fn (Orders $order) => [
                $order->id,
                $order->orderno,
                $order->service,
                $order->status,
                $order->checkin,
                $order->checkout,
            ])->all()
        );

        if ($this->option('dry-run')) {
            $this->line('Dry run only. No reservation was created.');
            $this->line('Changed: 0');
            $this->line('Eligible: '.$orders->count());

            return self::SUCCESS;
        }

        $changed = 0;

        foreach ($orders as $order) {
            DB::transaction(function () use ($order, $reservationService, &$changed) {
                $lockedOrder = Orders::query()
                    ->whereKey($order->id)
                    ->lockForUpdate()
                    ->first();

                if (
                    !$lockedOrder
                    || !in_array($lockedOrder->service, Orders::ACCOMMODATION_SERVICES, true)
                    || $lockedOrder->status !== 'Pending'
                    || $lockedOrder->rsv_id
                ) {
                    return;
                }

                $reservation = $reservationService->ensurePendingReservationForOrder($lockedOrder);

                if (!$reservation) {
                    return;
                }

                OrderLog::create([
                    'order_id' => $lockedOrder->id,
                    'action' => 'Repair Missing Accommodation Reservation',
                    'url' => 'console',
                    'method' => 'Create',
                    'agent' => $lockedOrder->name,
                    'admin' => null,
                ]);

                $changed++;
            });
        }

        $this->info("Created {$changed} missing Accommodation reservation(s).");
        $this->line("Changed: {$changed}");

        return self::SUCCESS;
    }
}
