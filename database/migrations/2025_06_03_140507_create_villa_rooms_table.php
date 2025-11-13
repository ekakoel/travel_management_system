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
        Schema::create('villa_rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('villa_id')->constrained('villas')->onDelete('cascade');
            $table->string('name');
            $table->string('cover')->nullable();
            $table->string('room_type');
            $table->string('bed_type')->nullable();
            $table->integer('guest_adult')->default(2);
            $table->integer('guest_child')->default(0);
            $table->string('size')->nullable();
            $table->text('description')->nullable(); 
            $table->text('amenities')->nullable();
            $table->string('view')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_rooms');
    }
};
