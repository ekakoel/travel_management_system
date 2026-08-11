<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'invoice_admins';
    private const COLUMN = 'rsv_id';
    private const PARENT_TABLE = 'reservations';
    private const FOREIGN_KEY = 'invoice_admins_rsv_id_foreign';
    private const UNIQUE_INDEX = 'invoice_admins_rsv_id_unique';
    private const SIGNED_INTEGER_MAX = 2147483647;

    public function up(): void
    {
        $this->assertRequiredSchemaExists();

        if ($this->foreignKeyExists()) {
            return;
        }

        $this->assertDataCanBeConstrained();
        $this->alignChildColumnWithParent();
        $this->ensureUniqueIndexExists();

        $this->addForeignKeyConstraint();
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::TABLE) || !Schema::hasColumn(self::TABLE, self::COLUMN)) {
            return;
        }

        $this->assertChildColumnCanBeNarrowed();

        if ($this->foreignKeyExists()) {
            $this->dropForeignKeyConstraint();
        }

        $this->restoreLegacyChildColumnType();
        $this->ensureUniqueIndexExists();
    }

    private function assertRequiredSchemaExists(): void
    {
        if (
            !Schema::hasTable(self::TABLE)
            || !Schema::hasColumn(self::TABLE, self::COLUMN)
            || !Schema::hasTable(self::PARENT_TABLE)
            || !Schema::hasColumn(self::PARENT_TABLE, 'id')
        ) {
            throw new \RuntimeException(
                'Cannot add invoice reservation foreign key: required tables or columns are missing.'
            );
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            $database = DB::connection()->getDatabaseName();
            $parentType = DB::table('information_schema.COLUMNS')
                ->where('TABLE_SCHEMA', $database)
                ->where('TABLE_NAME', self::PARENT_TABLE)
                ->where('COLUMN_NAME', 'id')
                ->value('COLUMN_TYPE');

            $normalizedParentType = strtolower((string) $parentType);
            if (!str_starts_with($normalizedParentType, 'bigint') || !str_contains($normalizedParentType, 'unsigned')) {
                throw new \RuntimeException(
                    'Cannot add invoice reservation foreign key: reservations.id must be BIGINT UNSIGNED.'
                );
            }
        }
    }

    private function assertDataCanBeConstrained(): void
    {
        $hasNullReservation = DB::table(self::TABLE)
            ->whereNull(self::COLUMN)
            ->exists();

        $hasNegativeReservation = DB::table(self::TABLE)
            ->where(self::COLUMN, '<', 0)
            ->exists();

        $hasDuplicateReservation = DB::table(self::TABLE)
            ->select(self::COLUMN)
            ->groupBy(self::COLUMN)
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        $hasOrphanReservation = DB::table(self::TABLE.' AS invoices')
            ->leftJoin(self::PARENT_TABLE.' AS reservations', 'reservations.id', '=', 'invoices.'.self::COLUMN)
            ->whereNull('reservations.id')
            ->exists();

        if ($hasNullReservation || $hasNegativeReservation || $hasDuplicateReservation || $hasOrphanReservation) {
            throw new \RuntimeException(
                'Cannot add invoice reservation foreign key: invoice_admins contains null, negative, duplicate, or orphan rsv_id values.'
            );
        }
    }

    private function alignChildColumnWithParent(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `invoice_admins` MODIFY `rsv_id` BIGINT UNSIGNED NOT NULL');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE "invoice_admins" ALTER COLUMN "rsv_id" TYPE BIGINT');
            DB::statement('ALTER TABLE "invoice_admins" ALTER COLUMN "rsv_id" SET NOT NULL');

            return;
        }

        if ($driver !== 'sqlite') {
            throw new \RuntimeException("Unsupported database driver for invoice reservation foreign key: {$driver}.");
        }
    }

    private function restoreLegacyChildColumnType(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `invoice_admins` MODIFY `rsv_id` INT NOT NULL');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE "invoice_admins" ALTER COLUMN "rsv_id" TYPE INTEGER');

            return;
        }

        if ($driver !== 'sqlite') {
            throw new \RuntimeException("Unsupported database driver for invoice reservation rollback: {$driver}.");
        }
    }

    private function assertChildColumnCanBeNarrowed(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        $hasOutOfRangeValue = DB::table(self::TABLE)
            ->where(self::COLUMN, '<', 0)
            ->orWhere(self::COLUMN, '>', self::SIGNED_INTEGER_MAX)
            ->exists();

        if ($hasOutOfRangeValue) {
            throw new \RuntimeException(
                'Cannot roll back invoice reservation foreign key: rsv_id contains values outside the signed INT range.'
            );
        }
    }

    private function ensureUniqueIndexExists(): void
    {
        if ($this->indexExists(self::UNIQUE_INDEX)) {
            return;
        }

        Schema::table(self::TABLE, function (Blueprint $table): void {
            $table->unique(self::COLUMN, self::UNIQUE_INDEX);
        });
    }

    private function addForeignKeyConstraint(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->alterSqliteForeignKey(true);

            return;
        }

        Schema::table(self::TABLE, function (Blueprint $table): void {
            $table->foreign(self::COLUMN, self::FOREIGN_KEY)
                ->references('id')
                ->on(self::PARENT_TABLE)
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }

    private function dropForeignKeyConstraint(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->alterSqliteForeignKey(false);

            return;
        }

        Schema::table(self::TABLE, function (Blueprint $table): void {
            $table->dropForeign(self::FOREIGN_KEY);
        });
    }

    private function alterSqliteForeignKey(bool $add): void
    {
        $schemaManager = DB::connection()->getDoctrineSchemaManager();
        $currentTable = $schemaManager->introspectTable(self::TABLE);
        $targetTable = clone $currentTable;

        if ($add) {
            $targetTable->addForeignKeyConstraint(
                self::PARENT_TABLE,
                [self::COLUMN],
                ['id'],
                ['onDelete' => 'RESTRICT', 'onUpdate' => 'RESTRICT'],
                self::FOREIGN_KEY
            );
        } else {
            foreach ($targetTable->getForeignKeys() as $foreignKey) {
                if (
                    $foreignKey->getForeignTableName() === self::PARENT_TABLE
                    && $foreignKey->getLocalColumns() === [self::COLUMN]
                    && $foreignKey->getForeignColumns() === ['id']
                ) {
                    $targetTable->removeForeignKey($foreignKey->getName());
                }
            }
        }

        $difference = $schemaManager->createComparator()->compareTables($currentTable, $targetTable);
        $schemaManager->alterTable($difference);
    }

    private function foreignKeyExists(): bool
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return collect(DB::select("PRAGMA foreign_key_list('".self::TABLE."')"))
                ->contains(fn ($key) => ($key->table ?? null) === self::PARENT_TABLE
                    && ($key->from ?? null) === self::COLUMN
                    && ($key->to ?? null) === 'id');
        }

        if ($driver === 'mysql') {
            return DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('CONSTRAINT_SCHEMA', DB::connection()->getDatabaseName())
                ->where('TABLE_NAME', self::TABLE)
                ->where('COLUMN_NAME', self::COLUMN)
                ->where('CONSTRAINT_NAME', self::FOREIGN_KEY)
                ->where('REFERENCED_TABLE_NAME', self::PARENT_TABLE)
                ->exists();
        }

        if ($driver === 'pgsql') {
            return DB::table('information_schema.TABLE_CONSTRAINTS')
                ->where('constraint_schema', 'public')
                ->where('table_name', self::TABLE)
                ->where('constraint_name', self::FOREIGN_KEY)
                ->where('constraint_type', 'FOREIGN KEY')
                ->exists();
        }

        return false;
    }

    private function indexExists(string $indexName): bool
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return collect(DB::select("PRAGMA index_list('".self::TABLE."')"))
                ->contains(fn ($index) => ($index->name ?? null) === $indexName);
        }

        if ($driver === 'mysql') {
            return collect(DB::select('SHOW INDEX FROM '.self::TABLE))
                ->contains(fn ($index) => ($index->Key_name ?? null) === $indexName);
        }

        if ($driver === 'pgsql') {
            return collect(DB::select(
                'SELECT indexname FROM pg_indexes WHERE schemaname = current_schema() AND tablename = ?',
                [self::TABLE]
            ))->contains(fn ($index) => ($index->indexname ?? null) === $indexName);
        }

        return false;
    }
};
