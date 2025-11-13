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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->nullable();
            $table->string('travel_agent')->nullable();
            $table->date('arrival_date')->nullable();
            $table->date('departure_date')->nullable();
        
            $table->foreign('guide_id')->references('id')->on('guides')->onDelete('set null');
            $table->string('guide_name')->nullable();
            $table->tinyInteger('attitude')->nullable();
            $table->tinyInteger('explanation')->nullable();
            $table->tinyInteger('knowledge')->nullable();
            $table->tinyInteger('time_control')->nullable();
            $table->tinyInteger('guide_neatness')->nullable();

            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->string('driver_name')->nullable();
            $table->tinyInteger('driver_punctuality')->nullable();
            $table->tinyInteger('driver_driving_skills')->nullable();
            $table->tinyInteger('driver_neatness')->nullable();
            
            $table->tinyInteger('transportation_cleanliness');
            $table->tinyInteger('transportation_air_condition');
            

            $table->tinyInteger('accommodation');
            $table->tinyInteger('meals');
            $table->tinyInteger('tour_sites');
            
            $table->text('travel_mood')->nullable();
            $table->string('customer_name')->nullable();
            $table->longText('customer_review')->nullable();
            $table->enum('status',['pending','accepted','rejected'])->default('pending');
    
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
