<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuidesTable extends Migration
{
    public function up()
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sex');
            $table->string('language');
            $table->string('phone');
            $table->integer('rsv_id')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nulllable();
            $table->string('country')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('guides');
    }
}
