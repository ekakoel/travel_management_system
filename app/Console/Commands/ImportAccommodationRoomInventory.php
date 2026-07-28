<?php

namespace App\Console\Commands;

use App\Models\ActionLog;
use App\Models\HotelRoom;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ImportAccommodationRoomInventory extends Command
{
    protected $signature = 'accommodation:import-room-inventory
        {path : CSV path, relative to project root when not absolute}
        {--dry-run : Validate and report changes without writing}';

    protected $description = 'Import Accommodation hotel room inventory from CSV with validation and transaction safety.';

    private const HEADERS = [
        'hotel_id',
        'hotel_name',
        'room_id',
        'room_name',
        'status',
        'current_inventory',
        'new_inventory',
    ];

    public function handle(): int
    {
        if (!Schema::hasTable('hotel_rooms') || !Schema::hasTable('hotels')) {
            $this->error('Required hotel tables are not available.');

            return self::FAILURE;
        }

        $path = $this->resolvePath((string) $this->argument('path'));

        if (!is_file($path) || !is_readable($path)) {
            $this->error("CSV file is not readable: {$path}");

            return self::FAILURE;
        }

        [$rows, $errors] = $this->readCsv($path);
        $seenRoomIds = [];
        $updates = [];
        $unchanged = 0;

        foreach ($rows as $rowNumber => $row) {
            $roomId = $this->integerOrNull($row['room_id'] ?? null);
            $hotelId = $this->integerOrNull($row['hotel_id'] ?? null);
            $newInventoryRaw = trim((string) ($row['new_inventory'] ?? ''));

            if ($roomId === null) {
                $errors[] = "Row {$rowNumber}: room_id must be an integer.";
                continue;
            }

            if (isset($seenRoomIds[$roomId])) {
                $errors[] = "Row {$rowNumber}: duplicate room_id {$roomId}.";
                continue;
            }

            $seenRoomIds[$roomId] = true;

            if ($hotelId === null) {
                $errors[] = "Row {$rowNumber}: hotel_id must be an integer.";
                continue;
            }

            if ($newInventoryRaw === '') {
                $unchanged++;
                continue;
            }

            $newInventory = $this->integerOrNull($newInventoryRaw);

            if ($newInventory === null) {
                $errors[] = "Row {$rowNumber}: new_inventory must be an integer.";
                continue;
            }

            if ($newInventory < 0) {
                $errors[] = "Row {$rowNumber}: new_inventory cannot be negative.";
                continue;
            }

            $room = HotelRoom::query()->with('hotels:id,name')->find($roomId);

            if (!$room) {
                $errors[] = "Row {$rowNumber}: room_id {$roomId} was not found.";
                continue;
            }

            if ((int) $room->hotels_id !== $hotelId) {
                $errors[] = "Row {$rowNumber}: room {$roomId} belongs to hotel {$room->hotels_id}, not {$hotelId}.";
                continue;
            }

            if ($room->status === 'Active' && $newInventory < 1) {
                $errors[] = "Row {$rowNumber}: active room {$roomId} requires inventory at least 1.";
                continue;
            }

            if ((int) $room->inventory === $newInventory && $room->inventory !== null) {
                $unchanged++;
                continue;
            }

            $updates[$roomId] = [
                'room' => $room,
                'old' => $room->inventory,
                'new' => $newInventory,
            ];
        }

        $missing = HotelRoom::query()
            ->whereNotIn('id', array_keys($seenRoomIds ?: [0]))
            ->count();

        if ($errors) {
            foreach ($errors as $error) {
                $this->error($error);
            }

            $this->printSummary(count($updates), $unchanged, count($errors), $missing);

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->line('Dry run only. No room inventory was changed.');
            $this->printSummary(count($updates), $unchanged, 0, $missing);

            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use ($updates, $path) {
                foreach ($updates as $roomId => $change) {
                    HotelRoom::query()
                        ->whereKey($roomId)
                        ->update(['inventory' => $change['new']]);

                    $this->writeActionLog($change['room'], $change['old'], $change['new'], $path);
                }
            });
        } catch (Throwable $exception) {
            $this->error('Import failed. No room inventory was changed.');
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->printSummary(count($updates), $unchanged, 0, $missing);

        return self::SUCCESS;
    }

    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'rb');
        $errors = [];
        $rows = [];

        if ($handle === false) {
            return [[], ["CSV file is not readable: {$path}"]];
        }

        $headers = fgetcsv($handle);

        if ($headers === false) {
            fclose($handle);

            return [[], ['CSV file is empty.']];
        }

        $headers = array_map(fn ($header) => trim((string) $header), $headers);

        foreach (self::HEADERS as $requiredHeader) {
            if (!in_array($requiredHeader, $headers, true)) {
                $errors[] = "Missing required CSV header: {$requiredHeader}.";
            }
        }

        $rowNumber = 1;

        while (($values = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if ($values === [null] || $values === false) {
                continue;
            }

            $row = [];

            foreach ($headers as $index => $header) {
                $row[$header] = $values[$index] ?? '';
            }

            $rows[$rowNumber] = $row;
        }

        fclose($handle);

        return [$rows, $errors];
    }

    private function writeActionLog(HotelRoom $room, mixed $oldInventory, int $newInventory, string $path): void
    {
        if (!Schema::hasTable('action_logs')) {
            return;
        }

        ActionLog::create([
            'user_id' => Auth::id() ?: 0,
            'action' => 'Import Accommodation Room Inventory',
            'service' => 'HotelRoom',
            'service_id' => $room->id,
            'page' => 'console',
            'user_ip' => 'console',
            'initial_state' => $oldInventory === null ? 'null' : (string) $oldInventory,
            'final_state' => (string) $newInventory,
            'action_note' => 'Imported from '.$path,
        ]);
    }

    private function integerOrNull(mixed $value): ?int
    {
        $value = trim((string) $value);

        if ($value === '' || !preg_match('/^-?\d+$/', $value)) {
            return null;
        }

        return (int) $value;
    }

    private function printSummary(int $changed, int $unchanged, int $rejected, int $missing): void
    {
        $this->line("Changed: {$changed}");
        $this->line("Unchanged: {$unchanged}");
        $this->line("Rejected: {$rejected}");
        $this->line("Missing: {$missing}");
    }

    private function resolvePath(string $path): string
    {
        if (preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) || str_starts_with($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        return base_path($path);
    }
}
