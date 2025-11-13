<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionalRatesTable extends Migration
{
    public function up()
    {
        Schema::create('optional_rates', function (Blueprint $table) {
            $table->id();
            $table->integer('hotels_id')->nullable();
            $table->integer('villas_id')->nullable();
            $table->string('name');
            $table->string('service');
            $table->integer('service_id');
            $table->string('type');
            $table->boolean('mandatory')->default(0);
            $table->integer('contract_rate');
            $table->date('active_date')->nullable();
            $table->date('must_buy_start')->nullable();
            $table->date('must_buy_end')->nullable();
            $table->integer('markup')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description_traditional')->nullable();
            $table->longText('description_simplified')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('optional_rates');
    }
}
