<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeddingReceptionVenuesTable extends Migration
{
    public function up()
    {
        Schema::create('wedding_reception_venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cover');
            $table->longText('description')->nullable();
            $table->longText('terms_and_conditions')->nullable();
            $table->integer('capacity');
            $table->integer('hotel_id');
            $table->string('periode_start');
            $table->string('periode_end');
            $table->integer('markup')->nullable();
            $table->integer('price');
            $table->integer('author_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wedding_reception_venues');
    }
}
