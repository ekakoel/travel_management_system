<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_package_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_package_locations', 'marker_image')) {
                $table->string('marker_image')->nullable()->after('google_maps_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tour_package_locations', function (Blueprint $table) {
            if (Schema::hasColumn('tour_package_locations', 'marker_image')) {
                $table->dropColumn('marker_image');
            }
        });
    }
};
