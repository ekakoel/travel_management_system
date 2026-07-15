<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_package_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_package_locations', 'visit_time')) {
                $table->time('visit_time')->nullable()->after('visit_order');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tour_package_locations', function (Blueprint $table) {
            if (Schema::hasColumn('tour_package_locations', 'visit_time')) {
                $table->dropColumn('visit_time');
            }
        });
    }
};
