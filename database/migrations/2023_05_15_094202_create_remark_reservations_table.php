<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemarkReservationsTable extends Migration
{
    public function up()
    {
        Schema::create('remark_reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('rsv_id');
            $table->longText('remark');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('remark_reservations');
    }
}
