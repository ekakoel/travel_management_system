<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('area',125);
            $table->string('area_traditional',125)->nullable();
            $table->string('area_simplified',125)->nullable();
            $table->string('coverage_area',125)->nullable();
            $table->integer('airport_duration',11)->nullable();
            $table->integer('airport_distance',11)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
