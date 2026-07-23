<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tour_package_locations') && Schema::hasColumn('tour_package_locations', 'itinerary_id')) {
            $this->dropForeignIfExists('tour_package_locations', 'tour_package_locations_itinerary_id_foreign');

            Schema::table('tour_package_locations', function (Blueprint $table) {
                $table->dropColumn('itinerary_id');
            });
        }

        if (Schema::hasTable('reservations') && Schema::hasColumn('reservations', 'itinerary_id')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('itinerary_id');
            });
        }

        Schema::dropIfExists('data_itineraries');
        Schema::dropIfExists('itineraries');
    }

    public function down(): void
    {
        if (! Schema::hasTable('itineraries')) {
            Schema::create('itineraries', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('tour_id')->nullable()->index();
                $table->integer('day_number')->default(1);
                $table->string('time')->nullable();
                $table->string('title')->nullable();
                $table->string('title_traditional')->nullable();
                $table->string('title_simplified')->nullable();
                $table->text('description')->nullable();
                $table->text('description_traditional')->nullable();
                $table->text('description_simplified')->nullable();
                $table->string('location')->nullable();
                $table->integer('sort_order')->default(0);
                $table->enum('status', ['Draft', 'Active'])->default('Draft');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('tour_package_locations') && ! Schema::hasColumn('tour_package_locations', 'itinerary_id')) {
            Schema::table('tour_package_locations', function (Blueprint $table) {
                $table->foreignId('itinerary_id')
                    ->nullable()
                    ->after('tour_id')
                    ->constrained('itineraries')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('reservations') && ! Schema::hasColumn('reservations', 'itinerary_id')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->integer('itinerary_id')->nullable()->after('guests_id');
            });
        }

        if (! Schema::hasTable('data_itineraries')) {
            Schema::create('data_itineraries', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_traditional')->nullable();
                $table->string('title_simplified')->nullable();
                $table->string('code');
                $table->longText('itinerary');
                $table->longText('itinerary_traditional')->nullable();
                $table->longText('itinerary_simplified')->nullable();
                $table->timestamps();
            });
        }
    }

    private function dropForeignIfExists(string $table, string $constraint): void
    {
        try {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
        } catch (Throwable $exception) {
            // The cleanup must be safe across databases where the FK was already removed or never created.
        }
    }
};
