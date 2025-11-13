<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantRsvsTable extends Migration
{
    public function up()
    {
        Schema::create('restaurant_rsvs', function (Blueprint $table) {
            $table->id();
            $table->string('rsv_id');
            $table->string('date');
            $table->string('breakfast');
            $table->string('lunch');
            $table->string('dinner');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('restaurant_rsvs');
    }
}
