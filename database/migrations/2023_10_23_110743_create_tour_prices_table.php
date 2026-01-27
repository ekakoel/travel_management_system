<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_prices', function (Blueprint $table) {
            $table->id();
            $table->string("tour_id")->constrained("tours")->onDelete("cascade");;
            $table->integer("min_qty");
            $table->integer("max_qty");
            $table->string("contract_rate");
            $table->string("markup");
            $table->string("expired_date");
            $table->string("status");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tour_prices');
    }
}
