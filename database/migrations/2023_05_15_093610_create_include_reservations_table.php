<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncludeReservationsTable extends Migration
{
    public function up()
    {
        Schema::create('include_reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('rsv_id');
            $table->longText('include');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('include_reservations');
    }
}
