<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tour_prices') || Schema::hasColumn('tour_prices', 'markup_type')) {
            return;
        }

        Schema::table('tour_prices', function (Blueprint $table) {
            $table->string('markup_type', 16)->nullable()->after('markup_amount');
        });

        DB::table('tour_prices')
            ->whereNull('markup_type')
            ->whereIn('markup_currency', ['USD', 'IDR'])
            ->update([
                'markup_type' => DB::raw("LOWER(markup_currency)"),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('tour_prices') || ! Schema::hasColumn('tour_prices', 'markup_type')) {
            return;
        }

        Schema::table('tour_prices', function (Blueprint $table) {
            $table->dropColumn('markup_type');
        });
    }
};
