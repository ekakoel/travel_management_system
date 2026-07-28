<?php

namespace App\Console\Commands;

use App\Models\Transports;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ImportTransportInventory extends Command
{
    protected $signature = 'transport:import-inventory {path} {--dry-run}';

    protected $description = 'Import public Transport inventory from CSV.';

    public function handle(): int
    {
        $path = (string) $this->argument('path');
        $absolutePath = $this->resolvePath($path);
        $rows = $this->readCsv($absolutePath);
        $seen = [];
        $changes = [];
        $rejected = [];

        foreach ($rows as $line => $row) {
            $transportId = (int) ($row['transport_id'] ?? 0);
            $newInventory = trim((string) ($row['new_inventory'] ?? ''));

            if ($transportId < 1) {
                $rejected[] = "Line {$line}: invalid transport_id.";
                continue;
            }

            if (isset($seen[$transportId])) {
                $rejected[] = "Line {$line}: duplicate transport_id {$transportId}.";
                continue;
            }

            $seen[$transportId] = true;

            if ($newInventory === '') {
                continue;
            }

            if (! preg_match('/^\d+$/', $newInventory)) {
                $rejected[] = "Line {$line}: new_inventory must be a non-negative integer.";
                continue;
            }

            $transport = Transports::find($transportId);

            if (! $transport) {
                $rejected[] = "Line {$line}: transport {$transportId} was not found.";
                continue;
            }

            if (isset($row['status']) && trim((string) $row['status']) !== (string) $transport->status) {
                $rejected[] = "Line {$line}: status mismatch for transport {$transportId}.";
                continue;
            }

            if (isset($row['transport_name']) && trim((string) $row['transport_name']) !== (string) $transport->name) {
                $rejected[] = "Line {$line}: name mismatch for transport {$transportId}.";
                continue;
            }

            if (isset($row['type']) && trim((string) $row['type']) !== (string) $transport->type) {
                $rejected[] = "Line {$line}: type mismatch for transport {$transportId}.";
                continue;
            }

            if (isset($row['passenger_capacity']) && trim((string) $row['passenger_capacity']) !== (string) $transport->capacity) {
                $rejected[] = "Line {$line}: passenger capacity mismatch for transport {$transportId}.";
                continue;
            }

            $inventory = (int) $newInventory;
            if ($transport->status === 'Active' && $inventory < 1) {
                $rejected[] = "Line {$line}: active transport {$transportId} requires inventory at least 1.";
                continue;
            }

            $changes[$transportId] = [
                'transport' => $transport,
                'inventory' => $inventory,
            ];
        }

        $changed = collect($changes)->filter(fn ($change) => (int) ($change['transport']->inventory ?? -1) !== $change['inventory'])->count();
        $unchanged = collect($changes)->count() - $changed;
        $missing = Transports::query()->whereNotIn('id', array_keys($seen))->count();

        foreach ($rejected as $message) {
            $this->error($message);
        }

        $this->line('Changed: '.$changed);
        $this->line('Unchanged: '.$unchanged);
        $this->line('Rejected: '.count($rejected));
        $this->line('Missing from CSV: '.$missing);

        if ($rejected) {
            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->line('Dry run complete. No data changed.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($changes) {
            foreach ($changes as $transportId => $change) {
                Transports::whereKey($transportId)->lockForUpdate()->update([
                    'inventory' => $change['inventory'],
                ]);
            }
        });

        $this->line('Import complete.');

        return self::SUCCESS;
    }

    private function resolvePath(string $path): string
    {
        $normalizedStoragePath = $this->normalizeStoragePath($path);
        $candidates = [
            $path,
            storage_path('app/'.$normalizedStoragePath),
            base_path($path),
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        throw new RuntimeException('CSV file was not found: '.$path);
    }

    private function normalizeStoragePath(string $path): string
    {
        $path = str_replace('\\', '/', ltrim($path, '/\\'));

        return str_starts_with($path, 'storage/app/')
            ? substr($path, strlen('storage/app/'))
            : $path;
    }

    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        if (! $header) {
            return [];
        }

        $rows = [];
        $line = 1;
        while (($data = fgetcsv($handle)) !== false) {
            $line++;
            if (count($data) !== count($header)) {
                throw new RuntimeException("Line {$line}: CSV column count does not match the header.");
            }
            $rows[$line] = array_combine($header, array_pad($data, count($header), null));
        }

        fclose($handle);

        return $rows;
    }
}
