<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'invoice_admins';
    private const STANDARD_INDEX = 'invoice_admins_rsv_id_idx';
    private const UNIQUE_INDEX = 'invoice_admins_rsv_id_unique';

    public function up(): void
    {
        if (!Schema::hasTable(self::TABLE) || !Schema::hasColumn(self::TABLE, 'rsv_id')) {
            return;
        }

        if ($this->indexExists(self::UNIQUE_INDEX)) {
            return;
        }

        $this->assertDataCanBeConstrained();

        Schema::table(self::TABLE, function (Blueprint $table): void {
            $table->unique('rsv_id', self::UNIQUE_INDEX);
        });

        if ($this->indexExists(self::STANDARD_INDEX)) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->dropIndex(self::STANDARD_INDEX);
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::TABLE) || !Schema::hasColumn(self::TABLE, 'rsv_id')) {
            return;
        }

        if (!$this->indexExists(self::STANDARD_INDEX)) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->index('rsv_id', self::STANDARD_INDEX);
            });
        }

        if ($this->indexExists(self::UNIQUE_INDEX)) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->dropUnique(self::UNIQUE_INDEX);
            });
        }
    }

    private function assertDataCanBeConstrained(): void
    {
        $hasNullReservation = DB::table(self::TABLE)
            ->whereNull('rsv_id')
            ->exists();

        $hasDuplicateReservation = DB::table(self::TABLE)
            ->select('rsv_id')
            ->groupBy('rsv_id')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        $hasOrphanReservation = Schema::hasTable('reservations')
            && DB::table(self::TABLE.' AS invoices')
                ->leftJoin('reservations AS reservations', 'reservations.id', '=', 'invoices.rsv_id')
                ->whereNull('reservations.id')
                ->exists();

        if ($hasNullReservation || $hasDuplicateReservation || $hasOrphanReservation) {
            throw new \RuntimeException(
                'Cannot enforce one invoice per reservation: invoice_admins contains null, duplicate, or orphan rsv_id values.'
            );
        }
    }

    private function indexExists(string $indexName): bool
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return collect(DB::select("PRAGMA index_list('".self::TABLE."')"))
                ->contains(fn ($index) => ($index->name ?? null) === $indexName);
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            return collect(DB::select('SHOW INDEX FROM '.self::TABLE))
                ->contains(fn ($index) => ($index->Key_name ?? null) === $indexName);
        }

        return collect(DB::select(
            'SELECT indexname FROM pg_indexes WHERE schemaname = current_schema() AND tablename = ?',
            [self::TABLE]
        ))->contains(fn ($index) => ($index->indexname ?? null) === $indexName);
    }
};
