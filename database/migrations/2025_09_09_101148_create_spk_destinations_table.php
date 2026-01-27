<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('spk_destinations', function (Blueprint $table) {
            $table->id();
            $table->datetime('date')->nullable();
            $table->foreignId('spk_id')->constrained()->cascadeOnDelete();
            $table->string('destination_name');
            $table->string('destination_address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('checkin_latitude', 10, 7)->nullable();
            $table->decimal('checkin_longitude', 10, 7)->nullable();
            $table->string('checkin_map_link')->nullable();
            $table->longText('description')->nullable();
            $table->timestamp('visited_at')->nullable();
            $table->enum('status', ['Pending', 'Visited'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('spk_destinations');
    }
};

