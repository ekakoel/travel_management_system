<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('tour_package_locations')
            && ! Schema::hasColumn('tour_package_locations', 'route_type')
        ) {
            Schema::table('tour_package_locations', function (Blueprint $table) {
                $table->string('route_type', 20)
                    ->default('land')
                    ->after('location_type');
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('tour_package_locations')
            && Schema::hasColumn('tour_package_locations', 'route_type')
        ) {
            Schema::table('tour_package_locations', function (Blueprint $table) {
                $table->dropColumn('route_type');
            });
        }
    }
};