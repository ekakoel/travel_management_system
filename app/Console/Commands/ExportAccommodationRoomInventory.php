<?php

namespace App\Console\Commands;

use App\Models\HotelRoom;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class ExportAccommodationRoomInventory extends Command
{
    protected $signature = 'accommodation:export-room-inventory
        {path? : CSV output path, relative to project root when not absolute}
        {--active-only : Export only active rooms}';

    protected $description = 'Export Accommodation hotel room inventory to CSV without changing data.';

    public function handle(): int
    {
        if (!Schema::hasTable('hotel_rooms') || !Schema::hasTable('hotels')) {
            $this->error('Required hotel tables are not available.');

            return self::FAILURE;
        }

        $path = $this->resolvePath($this->argument('path'));
        $directory = dirname($path);

        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            $this->error("Unable to create export directory: {$directory}");

            return self::FAILURE;
        }

        $handle = fopen($path, 'wb');

        if ($handle === false) {
            $this->error("Unable to write CSV: {$path}");

            return self::FAILURE;
        }

        fputcsv($handle, [
            'hotel_id',
            'hotel_name',
            'room_id',
            'room_name',
            'status',
            'current_inventory',
            'new_inventory',
        ]);

        $query = HotelRoom::query()
            ->with('hotels:id,name')
            ->orderBy('hotels_id')
            ->orderBy('id');

        if ($this->option('active-only')) {
            $query->where('status', 'Active');
        }

        $count = 0;

        $query->chunkById(200, function ($rooms) use ($handle, &$count) {
            foreach ($rooms as $room) {
                fputcsv($handle, [
                    $room->hotels_id,
                    optional($room->hotels)->name,
                    $room->id,
                    $room->rooms,
                    $room->status,
                    $room->inventory,
                    '',
                ]);

                $count++;
            }
        });

        fclose($handle);

        $this->info("Exported {$count} room(s).");
        $this->line("CSV: {$path}");

        return self::SUCCESS;
    }

    private function resolvePath(?string $path): string
    {
        $path = $path ?: 'storage/app/exports/accommodation-room-inventory-'.now()->format('Ymd-His').'.csv';

        if (preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) || str_starts_with($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        return base_path($path);
    }
}
