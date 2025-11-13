<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExcludeReservationsTable extends Migration
{
    public function up()
    {
        Schema::create('exclude_reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('rsv_id');
            $table->longText('exclude');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exclude_reservations');
    }
}
