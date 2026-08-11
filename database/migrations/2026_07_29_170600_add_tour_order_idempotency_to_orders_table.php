<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders') || Schema::hasColumn('orders', 'submission_token_hash')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->char('submission_token_hash', 64)->nullable()->after('pricing_calculated_at');
            $table->unique(
                ['service', 'user_id', 'submission_token_hash'],
                'orders_service_user_submission_unique'
            );
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders') || !Schema::hasColumn('orders', 'submission_token_hash')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique('orders_service_user_submission_unique');
            $table->dropColumn('submission_token_hash');
        });
    }
};
