<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InvoiceReservationForeignKeyMigrationTest extends TestCase
{
    public function test_foreign_key_integrity_and_rollback_are_safe(): void
    {
        DB::statement('PRAGMA foreign_keys = ON');

        Schema::create('reservations', function (Blueprint $table): void {
            $table->id();
        });
        Schema::create('invoice_admins', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('rsv_id');
            $table->unique('rsv_id', 'invoice_admins_rsv_id_unique');
        });

        DB::table('reservations')->insert(['id' => 10]);

        $migration = require database_path('migrations/2026_08_11_130000_add_invoice_reservation_foreign_key.php');
        $migration->up();

        DB::table('invoice_admins')->insert(['id' => 1, 'rsv_id' => 10]);
        $this->assertSame(1, DB::table('invoice_admins')->where('rsv_id', 10)->count());

        $this->assertQueryException(function (): void {
            DB::table('invoice_admins')->insert(['id' => 2, 'rsv_id' => 999]);
        });
        $this->assertQueryException(function (): void {
            DB::table('reservations')->where('id', 10)->delete();
        });
        $this->assertQueryException(function (): void {
            DB::table('invoice_admins')->insert(['id' => 3, 'rsv_id' => 10]);
        });

        $migration->down();

        $this->assertSame(1, DB::table('invoice_admins')->where('rsv_id', 10)->count());
        $this->assertSame(1, DB::table('reservations')->where('id', 10)->count());
        $this->assertSame([], DB::select("PRAGMA foreign_key_list('invoice_admins')"));

        DB::table('invoice_admins')->insert(['id' => 2, 'rsv_id' => 999]);
        $this->assertSame(2, DB::table('invoice_admins')->count());

        $this->assertQueryException(function (): void {
            DB::table('invoice_admins')->insert(['id' => 3, 'rsv_id' => 10]);
        });
    }

    public function test_migration_has_explicit_mysql_type_alignment_and_restrict_rules(): void
    {
        $migration = file_get_contents(
            database_path('migrations/2026_08_11_130000_add_invoice_reservation_foreign_key.php')
        );

        $this->assertStringContainsString('BIGINT UNSIGNED NOT NULL', $migration);
        $this->assertStringContainsString("->onDelete('restrict')", $migration);
        $this->assertStringContainsString("->onUpdate('restrict')", $migration);
        $this->assertStringContainsString('assertDataCanBeConstrained()', $migration);
        $this->assertStringContainsString('assertChildColumnCanBeNarrowed()', $migration);
        $this->assertStringContainsString("private const FOREIGN_KEY = 'invoice_admins_rsv_id_foreign'", $migration);
        $this->assertStringContainsString("private const UNIQUE_INDEX = 'invoice_admins_rsv_id_unique'", $migration);
    }

    public function test_preflight_stops_before_schema_changes_when_orphan_data_exists(): void
    {
        DB::statement('PRAGMA foreign_keys = ON');

        Schema::create('reservations', function (Blueprint $table): void {
            $table->id();
        });
        Schema::create('invoice_admins', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('rsv_id');
            $table->unique('rsv_id', 'invoice_admins_rsv_id_unique');
        });

        DB::table('invoice_admins')->insert(['id' => 1, 'rsv_id' => 999]);
        $migration = require database_path('migrations/2026_08_11_130000_add_invoice_reservation_foreign_key.php');

        try {
            $migration->up();
            $this->fail('The migration did not stop when orphan invoice data existed.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('orphan', $exception->getMessage());
        }

        $this->assertSame([], DB::select("PRAGMA foreign_key_list('invoice_admins')"));
        $this->assertSame(1, DB::table('invoice_admins')->where('rsv_id', 999)->count());
        $this->assertTrue(collect(DB::select("PRAGMA index_list('invoice_admins')"))
            ->contains(fn ($index) => ($index->name ?? null) === 'invoice_admins_rsv_id_unique'));
    }

    private function assertQueryException(callable $callback): void
    {
        try {
            $callback();
            $this->fail('The database integrity constraint did not reject the operation.');
        } catch (QueryException) {
            $this->addToAssertionCount(1);
        }
    }
}
