<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('rsv_no');
            $table->string('service');
            $table->integer('inv_id')->nullable();
            $table->integer('agn_id');
            $table->integer('adm_id');
            $table->string('checkin')->nullable();
            $table->string('checkout')->nullable();
            $table->string('pickup_name')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('pickup_date')->nullable();
            $table->integer('guide_id')->nullable();
            $table->integer('guests_id')->nullable();
            $table->integer('driver_id')->nullable();
            $table->string('arrival_flight')->nullable();
            $table->string('arrival_time')->nullable();
            $table->string('departure_flight')->nullable();
            $table->string('departure_time')->nullable();
            $table->string('orders')->nullable();
            $table->integer('itinerary_id')->nullable();
            $table->longText('additional_info')->nullable();
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
