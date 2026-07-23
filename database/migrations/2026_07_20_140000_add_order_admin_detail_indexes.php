<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddOrderAdminDetailIndexes extends Migration
{
    public function up()
    {
        $this->addIndexIfMissing('orders', ['status', 'checkin', 'updated_at'], 'orders_status_checkin_updated_idx');
        $this->addIndexIfMissing('orders', ['rsv_id'], 'orders_rsv_id_idx');
        $this->addIndexIfMissing('orders', ['user_id'], 'orders_user_id_idx');
        $this->addIndexIfMissing('orders', ['sales_agent'], 'orders_sales_agent_idx');
        $this->addIndexIfMissing('orders', ['handled_by'], 'orders_handled_by_idx');

        $this->addIndexIfMissing('reservations', ['rsv_no'], 'reservations_rsv_no_idx');
        $this->addIndexIfMissing('reservations', ['agn_id'], 'reservations_agn_id_idx');
        $this->addIndexIfMissing('reservations', ['service', 'status', 'checkin'], 'reservations_service_status_checkin_idx');

        $this->addIndexIfMissing('guests', ['rsv_id'], 'guests_rsv_id_idx');
        $this->addIndexIfMissing('invoice_admins', ['rsv_id'], 'invoice_admins_rsv_id_idx');
        $this->addIndexIfMissing('payment_confirmations', ['inv_id', 'status'], 'payment_confirmations_inv_status_idx');
        $this->addIndexIfMissing('order_notes', ['order_id'], 'order_notes_order_id_idx');
        $this->addIndexIfMissing('order_logs', ['order_id'], 'order_logs_order_id_idx');
    }

    public function down()
    {
        $this->dropIndexIfExists('order_logs', 'order_logs_order_id_idx');
        $this->dropIndexIfExists('order_notes', 'order_notes_order_id_idx');
        $this->dropIndexIfExists('payment_confirmations', 'payment_confirmations_inv_status_idx');
        $this->dropIndexIfExists('invoice_admins', 'invoice_admins_rsv_id_idx');
        $this->dropIndexIfExists('guests', 'guests_rsv_id_idx');

        $this->dropIndexIfExists('reservations', 'reservations_service_status_checkin_idx');
        $this->dropIndexIfExists('reservations', 'reservations_agn_id_idx');
        $this->dropIndexIfExists('reservations', 'reservations_rsv_no_idx');

        $this->dropIndexIfExists('orders', 'orders_handled_by_idx');
        $this->dropIndexIfExists('orders', 'orders_sales_agent_idx');
        $this->dropIndexIfExists('orders', 'orders_user_id_idx');
        $this->dropIndexIfExists('orders', 'orders_rsv_id_idx');
        $this->dropIndexIfExists('orders', 'orders_status_checkin_updated_idx');
    }

    private function addIndexIfMissing(string $table, array $columns, string $indexName): void
    {
        if (!Schema::hasTable($table) || $this->indexExists($table, $indexName)) {
            return;
        }

        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                return;
            }
        }

        Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
            $table->index($columns, $indexName);
        });
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (!Schema::hasTable($table) || !$this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($indexName) {
            $table->dropIndex($indexName);
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            return count(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName])) > 0;
        }

        if ($driver === 'sqlite') {
            return collect(DB::select("PRAGMA index_list('{$table}')"))
                ->contains(fn ($index) => ($index->name ?? null) === $indexName);
        }

        if ($driver === 'pgsql') {
            return count(DB::select(
                'select indexname from pg_indexes where tablename = ? and indexname = ?',
                [$table, $indexName]
            )) > 0;
        }

        return false;
    }
}
