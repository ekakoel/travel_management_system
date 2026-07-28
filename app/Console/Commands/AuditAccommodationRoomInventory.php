<?php

namespace App\Console\Commands;

use App\Models\HotelRoom;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class AuditAccommodationRoomInventory extends Command
{
    protected $signature = 'accommodation:audit-room-inventory {--active-only : Show only active rooms}';

    protected $description = 'Audit Accommodation room inventory without changing data.';

    public function handle(): int
    {
        if (!Schema::hasTable('hotel_rooms') || !Schema::hasTable('hotels')) {
            $this->error('Required hotel tables are not available.');

            return self::FAILURE;
        }

        $hasInventory = Schema::hasColumn('hotel_rooms', 'inventory');
        $fallback = max((int) config('services.accommodation.default_room_inventory', 1), 1);
        $query = HotelRoom::query()
            ->with('hotels:id,name')
            ->orderBy('hotels_id')
            ->orderBy('rooms');

        if ($this->option('active-only')) {
            $query->where('status', 'Active');
        }

        $rooms = $query->get();
        $nullInventory = 0;
        $zeroInventory = 0;
        $fallbackRooms = 0;

        $rows = $rooms->map(function (HotelRoom $room) use ($hasInventory, $fallback, &$nullInventory, &$zeroInventory, &$fallbackRooms) {
            $inventory = $hasInventory ? $room->inventory : null;
            $usesFallback = !$hasInventory || $inventory === null;

            if ($inventory === null) {
                $nullInventory++;
            }

            if ((int) $inventory === 0 && $inventory !== null) {
                $zeroInventory++;
            }

            if ($usesFallback) {
                $fallbackRooms++;
            }

            return [
                optional($room->hotels)->name ?: 'Unknown hotel',
                $room->rooms ?: 'Unknown room',
                $room->status ?: '-',
                $inventory === null ? 'null' : (string) $inventory,
                $usesFallback ? "yes ({$fallback})" : 'no',
            ];
        });

        $this->table(['Hotel', 'Room', 'Status', 'Inventory', 'Uses Fallback'], $rows->all());
        $this->line("Active filter: ".($this->option('active-only') ? 'yes' : 'no'));
        $this->line("Rooms audited: {$rooms->count()}");
        $this->line("Inventory null: {$nullInventory}");
        $this->line("Inventory zero: {$zeroInventory}");
        $this->line("Using fallback {$fallback}: {$fallbackRooms}");

        return self::SUCCESS;
    }
}
