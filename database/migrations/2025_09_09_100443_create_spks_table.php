<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('spks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_number')->nullable();
            $table->string('type')->nullable();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete()->nullable();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('transport_id')->nullable()->constrained()->nullOnDelete();
            $table->string('spk_number')->unique();
            $table->integer('number_of_guests')->nullable();
            $table->date('spk_date')->nullable();
            $table->enum('status', ['Pending', 'In_progress', 'Completed'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('spks');
    }
};

