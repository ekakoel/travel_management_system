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
        Schema::create('temporary_wedding_review_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('wedding_organizer')->nullable();
            $table->string('booking_code')->unique();
            $table->string('groom')->nullable();
            $table->string('bride')->nullable();
            $table->integer('jumlah_review')->default(1);
            $table->timestamp('expires_at');
            $table->string('qr_code_path')->nullable();
            $table->string('link')->nullable();
            $table->date('wedding_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_wedding_review_links');
    }
};
