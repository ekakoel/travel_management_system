<?php

namespace App\Console\Commands;

use App\Models\Transports;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ExportTransportInventory extends Command
{
    protected $signature = 'transport:export-inventory {path=exports/transport-inventory.csv}';

    protected $description = 'Export public Transport inventory to CSV.';

    public function handle(): int
    {
        $path = $this->normalizeStoragePath((string) $this->argument('path'));
        $absolutePath = Storage::disk('local')->path($path);
        $directory = dirname($absolutePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $handle = fopen($absolutePath, 'w');
        fputcsv($handle, ['transport_id', 'transport_name', 'type', 'status', 'passenger_capacity', 'current_inventory', 'new_inventory']);

        Transports::query()->orderBy('id')->chunkById(200, function ($transports) use ($handle) {
            foreach ($transports as $transport) {
                fputcsv($handle, [
                    $transport->id,
                    $transport->name,
                    $transport->type,
                    $transport->status,
                    $transport->capacity,
                    $transport->inventory,
                    '',
                ]);
            }
        });

        fclose($handle);

        $this->line('Exported public Transport inventory to '.$absolutePath);

        return self::SUCCESS;
    }

    private function normalizeStoragePath(string $path): string
    {
        $path = str_replace('\\', '/', ltrim($path, '/\\'));

        return str_starts_with($path, 'storage/app/')
            ? substr($path, strlen('storage/app/'))
            : $path;
    }
}
