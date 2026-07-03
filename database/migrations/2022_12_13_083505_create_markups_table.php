<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarkupsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('markups')) {
            return;
        }

        Schema::create('markups', function (Blueprint $table) {
            $table->id();
            $table->string("service");
            $table->integer("service_id");
            $table->integer("markup");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('markups');
    }
}
