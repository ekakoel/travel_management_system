<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogDataTable extends Migration
{

    public function up()
    {
        Schema::create('log_data', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('service');
            $table->text('initial_data')->nullable();
            $table->text('final_data')->nullable();
            $table->string('action');
            $table->integer('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_data');
    }
}
