<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tour_prices') || Schema::hasColumn('tour_prices', 'valid_from')) {
            return;
        }

        Schema::table('tour_prices', function (Blueprint $table) {
            $table->date('valid_from')->nullable()->after('pricing_data_status');
            $table->index(
                ['tour_id', 'pricing_data_status', 'valid_from', 'valid_until', 'min_qty', 'max_qty'],
                'tour_prices_overlap_lookup_idx'
            );
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('tour_prices') || ! Schema::hasColumn('tour_prices', 'valid_from')) {
            return;
        }

        Schema::table('tour_prices', function (Blueprint $table) {
            $table->dropIndex('tour_prices_overlap_lookup_idx');
            $table->dropColumn('valid_from');
        });
    }
};
