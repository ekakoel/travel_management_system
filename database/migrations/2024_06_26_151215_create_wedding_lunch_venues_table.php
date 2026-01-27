<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeddingLunchVenuesTable extends Migration
{
    public function up()
    {
        Schema::create('wedding_lunch_venues', function (Blueprint $table) {
            $table->id();
            $table->integer('hotel_id');
            $table->string('name');
            $table->string('cover');
            $table->longText('description');
            $table->longText('terms_and_conditions');
            $table->integer('max_capacity');
            $table->integer('min_capacity');
            $table->string('periode_start');
            $table->string('periode_end');
            $table->integer('markup');
            $table->integer('publish_rate');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wedding_lunch_venues');
    }
}
