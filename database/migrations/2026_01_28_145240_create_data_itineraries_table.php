<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_itineraries', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("title_traditional")->nullable();
            $table->string("title_simplified")->nullable();
            $table->string("code");
            $table->longText("itinerary");
            $table->longText("itinerary_traditional")->nullable();
            $table->longText("itinerary_simplified")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_itineraries');
    }
};
