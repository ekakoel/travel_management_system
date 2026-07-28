<?php

namespace App\Console\Commands;

use App\Models\Transports;
use App\Services\TransportAvailabilityService;
use Illuminate\Console\Command;

class AuditTransportInventory extends Command
{
    protected $signature = 'transport:audit-inventory {--active-only}';

    protected $description = 'Audit public Transport inventory without changing data.';

    public function handle(TransportAvailabilityService $availability): int
    {
        $query = Transports::query()->orderBy('id');

        if ($this->option('active-only')) {
            $query->where('status', 'Active');
        }

        $rows = $query->get();
        $nullInventory = $rows->whereNull('inventory')->count();
        $zeroInventory = $rows->filter(fn (Transports $transport) => $transport->inventory !== null && (int) $transport->inventory === 0)->count();
        $activeNull = $rows->where('status', 'Active')->whereNull('inventory')->count();
        $activeZero = $rows->filter(fn (Transports $transport) => $transport->status === 'Active' && $transport->inventory !== null && (int) $transport->inventory === 0)->count();

        $this->table(
            ['ID', 'Name', 'Type', 'Capacity', 'Inventory', 'Effective', 'Fallback'],
            $rows->map(fn (Transports $transport) => [
                $transport->id,
                $transport->name,
                $transport->type,
                $transport->capacity,
                $transport->inventory === null ? 'NULL' : $transport->inventory,
                $availability->effectiveInventory($transport),
                $transport->inventory === null ? 'yes' : 'no',
            ])->all()
        );

        $this->line('Total transports: '.$rows->count());
        $this->line('Inventory null: '.$nullInventory);
        $this->line('Inventory zero: '.$zeroInventory);
        $this->line('Active inventory null: '.$activeNull);
        $this->line('Active inventory zero: '.$activeZero);
        $this->line('Fallback usage: '.$nullInventory);

        return self::SUCCESS;
    }
}
