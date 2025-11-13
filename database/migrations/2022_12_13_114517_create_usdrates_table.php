<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsdRatesTable extends Migration
{
    public function up()
    {
        Schema::create('usd_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rate');
            $table->string('sell');
            $table->string('buy');
            $table->string('difference');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usdrates');
    }
}
