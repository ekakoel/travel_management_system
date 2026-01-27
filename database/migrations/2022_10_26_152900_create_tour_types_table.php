<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourTypesTable extends Migration
{
    public function up()
    {
        Schema::create('tour_types', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('type_traditional');
            $table->string('type_simplified');
            $table->longText('description');
            $table->longText('description_traditional');
            $table->longText('description_simplified');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tour_types');
    }
}
