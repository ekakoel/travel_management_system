<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tour_package_locations')) {
            return;
        }

        Schema::create('tour_package_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('itinerary_id')->nullable()->constrained('itineraries')->nullOnDelete();
            $table->unsignedInteger('day_number')->default(1);
            $table->unsignedInteger('visit_order')->default(1);
            $table->string('destination_name');
            $table->string('google_maps_url', 2048)->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tour_id', 'is_active', 'day_number', 'visit_order'], 'tpl_tour_active_order_idx');
            $table->index(['tour_id', 'day_number'], 'tpl_tour_day_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_package_locations');
    }
};
