<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tour_package_locations')) {
            Schema::table('tour_package_locations', function (Blueprint $table) {
                if (! Schema::hasColumn('tour_package_locations', 'description_traditional')) {
                    $table->text('description_traditional')->nullable()->after('description');
                }

                if (! Schema::hasColumn('tour_package_locations', 'description_simplified')) {
                    $table->text('description_simplified')->nullable()->after('description_traditional');
                }
            });
        }

        if (Schema::hasTable('tour_location_references')) {
            Schema::table('tour_location_references', function (Blueprint $table) {
                if (! Schema::hasColumn('tour_location_references', 'description_traditional')) {
                    $table->text('description_traditional')->nullable()->after('description');
                }

                if (! Schema::hasColumn('tour_location_references', 'description_simplified')) {
                    $table->text('description_simplified')->nullable()->after('description_traditional');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tour_location_references')) {
            Schema::table('tour_location_references', function (Blueprint $table) {
                if (Schema::hasColumn('tour_location_references', 'description_simplified')) {
                    $table->dropColumn('description_simplified');
                }

                if (Schema::hasColumn('tour_location_references', 'description_traditional')) {
                    $table->dropColumn('description_traditional');
                }
            });
        }

        if (Schema::hasTable('tour_package_locations')) {
            Schema::table('tour_package_locations', function (Blueprint $table) {
                if (Schema::hasColumn('tour_package_locations', 'description_simplified')) {
                    $table->dropColumn('description_simplified');
                }

                if (Schema::hasColumn('tour_package_locations', 'description_traditional')) {
                    $table->dropColumn('description_traditional');
                }
            });
        }
    }
};
