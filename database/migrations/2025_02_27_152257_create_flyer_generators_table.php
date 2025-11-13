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
        Schema::create('flyer_generators', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId("hotel_id")->constrained("hotels")->onDelete("cascade");
            $table->enum('status', ['Draft', 'Published'])->nullable();
            $table->date('expired_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flyer_generators');
    }
};
