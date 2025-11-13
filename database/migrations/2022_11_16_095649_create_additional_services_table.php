<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalServicesTable extends Migration
{
    public function up()
    {
        Schema::create('additional_services', function (Blueprint $table) {
            $table->id();
            $table->integer('rsv_id');
            $table->integer('order_wedding_id')->nullable();
            $table->integer('admin_id');
            $table->string('tgl');
            $table->string('service');
            $table->string('type')->nullable();
            $table->string('location')->nullable();
            $table->integer('qty')->nullable();
            $table->string('price')->nullable();
            $table->string('total_price')->nullable();
            $table->string('loc_name')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('additional_services');
    }
}
