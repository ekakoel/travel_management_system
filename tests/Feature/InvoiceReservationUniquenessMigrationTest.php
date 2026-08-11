<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InvoiceReservationUniquenessMigrationTest extends TestCase
{
    public function test_migration_enforces_one_invoice_per_reservation_and_is_reversible(): void
    {
        Schema::create('reservations', function (Blueprint $table): void {
            $table->id();
        });
        Schema::create('invoice_admins', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('rsv_id');
            $table->index('rsv_id', 'invoice_admins_rsv_id_idx');
        });

        DB::table('reservations')->insert(['id' => 10]);
        DB::table('invoice_admins')->insert(['id' => 1, 'rsv_id' => 10]);

        $migration = require database_path('migrations/2026_08_11_120000_enforce_one_invoice_per_reservation.php');
        $migration->up();

        try {
            DB::table('invoice_admins')->insert(['id' => 2, 'rsv_id' => 10]);
            $this->fail('The unique reservation constraint did not reject a duplicate invoice.');
        } catch (QueryException) {
            $this->assertSame(1, DB::table('invoice_admins')->where('rsv_id', 10)->count());
        }

        $migration->down();

        DB::table('invoice_admins')->insert(['id' => 2, 'rsv_id' => 10]);
        $this->assertSame(2, DB::table('invoice_admins')->where('rsv_id', 10)->count());

        try {
            $migration->up();
            $this->fail('The migration did not stop when duplicate reservation invoices existed.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('duplicate', $exception->getMessage());
            $this->assertSame(2, DB::table('invoice_admins')->where('rsv_id', 10)->count());
        }
    }
}
