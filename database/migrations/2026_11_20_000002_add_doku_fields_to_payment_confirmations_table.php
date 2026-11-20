<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payment_confirmations')) {
            return;
        }

        Schema::table('payment_confirmations', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_confirmations', 'source')) {
                $table->string('source', 20)->nullable()->default('manual')->after('status');
            }

            if (!Schema::hasColumn('payment_confirmations', 'gateway_reference')) {
                $table->string('gateway_reference', 64)->nullable()->unique()->after('source');
            }
        });

        DB::table('payment_confirmations')
            ->whereNull('source')
            ->update(['source' => 'manual']);
    }

    public function down(): void
    {
        if (!Schema::hasTable('payment_confirmations')) {
            return;
        }

        Schema::table('payment_confirmations', function (Blueprint $table) {
            if (Schema::hasColumn('payment_confirmations', 'gateway_reference')) {
                $table->dropUnique('payment_confirmations_gateway_reference_unique');
                $table->dropColumn('gateway_reference');
            }

            if (Schema::hasColumn('payment_confirmations', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};
