<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportPricesTable extends Migration
{
    public function up()
    {
        Schema::create('transport_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId("transports_id")->constrained("transports")->onDelete("cascade");
            $table->string('type');
            $table->string('src')->nullable();
            $table->string('dst')->nullable();
            $table->integer('duration');
            $table->integer('contract_rate');
            $table->integer('markup')->nullable();
            $table->integer('extra_time')->nullable();
            $table->longText('additional_info')->nullable();
            $table->integer('author_id');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('transport_prices');
    }
}
