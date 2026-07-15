<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tour_location_references')) {
            Schema::create('tour_location_references', function (Blueprint $table) {
                $table->id();
                $table->string('destination_name');
                $table->string('location_type', 40)->default('Attraction');
                $table->string('google_maps_url', 2048)->nullable();
                $table->string('marker_image')->nullable();
                $table->decimal('latitude', 10, 7);
                $table->decimal('longitude', 10, 7);
                $table->text('description')->nullable();
                $table->string('lookup_key')->unique();
                $table->timestamps();

                $table->index(['destination_name', 'location_type'], 'tlr_name_type_idx');
                $table->index(['latitude', 'longitude'], 'tlr_coordinate_idx');
            });
        }

        if (Schema::hasTable('tour_package_locations') && !Schema::hasColumn('tour_package_locations', 'location_reference_id')) {
            Schema::table('tour_package_locations', function (Blueprint $table) {
                $table->foreignId('location_reference_id')->nullable()->after('itinerary_id')->constrained('tour_location_references')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('tour_package_locations')) {
            return;
        }

        DB::table('tour_package_locations')
            ->whereNotNull('destination_name')
            ->orderBy('id')
            ->get()
            ->each(function ($location) {
                $lookupKey = Str::of($location->destination_name)
                    ->lower()
                    ->squish()
                    ->toString()
                    . '|' . ($location->location_type ?: 'Attraction')
                    . '|' . round((float) $location->latitude, 7)
                    . '|' . round((float) $location->longitude, 7);

                $reference = DB::table('tour_location_references')->where('lookup_key', $lookupKey)->first();

                if (!$reference) {
                    $referenceId = DB::table('tour_location_references')->insertGetId([
                        'destination_name' => $location->destination_name,
                        'location_type' => $location->location_type ?: 'Attraction',
                        'google_maps_url' => $location->google_maps_url,
                        'marker_image' => $location->marker_image,
                        'latitude' => $location->latitude,
                        'longitude' => $location->longitude,
                        'description' => $location->description,
                        'lookup_key' => $lookupKey,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $referenceId = $reference->id;
                }

                DB::table('tour_package_locations')->where('id', $location->id)->update([
                    'location_reference_id' => $referenceId,
                ]);
            });
    }

    public function down(): void
    {
        if (Schema::hasTable('tour_package_locations') && Schema::hasColumn('tour_package_locations', 'location_reference_id')) {
            Schema::table('tour_package_locations', function (Blueprint $table) {
                $table->dropConstrainedForeignId('location_reference_id');
            });
        }

        Schema::dropIfExists('tour_location_references');
    }
};
