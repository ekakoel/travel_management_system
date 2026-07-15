<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_package_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_package_locations', 'location_type')) {
                $table->string('location_type', 40)->default('Attraction')->after('destination_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tour_package_locations', function (Blueprint $table) {
            if (Schema::hasColumn('tour_package_locations', 'location_type')) {
                $table->dropColumn('location_type');
            }
        });
    }
};
