<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'pricing_version')) {
                $table->string('pricing_version', 64)->nullable()->after('final_price');
            }
            if (!Schema::hasColumn('orders', 'pricing_snapshot_id')) {
                $table->unsignedBigInteger('pricing_snapshot_id')->nullable()->after('pricing_version');
            }
            if (!Schema::hasColumn('orders', 'base_currency')) {
                $table->char('base_currency', 3)->nullable()->after('pricing_snapshot_id');
            }
            if (!Schema::hasColumn('orders', 'display_currency')) {
                $table->char('display_currency', 3)->nullable()->after('base_currency');
            }
            if (!Schema::hasColumn('orders', 'final_total_idr')) {
                $table->unsignedBigInteger('final_total_idr')->nullable()->after('display_currency');
            }
            if (!Schema::hasColumn('orders', 'final_total_usd_minor')) {
                $table->unsignedBigInteger('final_total_usd_minor')->nullable()->after('final_total_idr');
            }
            if (!Schema::hasColumn('orders', 'pricing_calculated_at')) {
                $table->dateTime('pricing_calculated_at', 6)->nullable()->after('final_total_usd_minor');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('pricing_snapshot_id', 'orders_pricing_snapshot_idx');

            if (Schema::hasTable('order_pricing_snapshots')) {
                $table->foreign('pricing_snapshot_id', 'orders_pricing_snapshot_fk')
                    ->references('id')
                    ->on('order_pricing_snapshots')
                    ->restrictOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'pricing_snapshot_id')) {
                if (DB::getDriverName() !== 'sqlite') {
                    $table->dropForeign('orders_pricing_snapshot_fk');
                }
                $table->dropIndex('orders_pricing_snapshot_idx');
            }
        });

        $columns = array_values(array_filter([
            'pricing_version',
            'pricing_snapshot_id',
            'base_currency',
            'display_currency',
            'final_total_idr',
            'final_total_usd_minor',
            'pricing_calculated_at',
        ], fn (string $column) => Schema::hasColumn('orders', $column)));

        if ($columns !== []) {
            Schema::table('orders', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
