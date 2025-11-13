<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('tax_dokus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tax_name');
            $table->decimal('tax_rate', 5, 2);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_dokus');
    }
};
