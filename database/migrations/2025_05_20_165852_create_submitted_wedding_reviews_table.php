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
        Schema::create('submitted_wedding_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('booking_code');
            $table->json('review_data');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('submitted_wedding_reviews');
    }
};
