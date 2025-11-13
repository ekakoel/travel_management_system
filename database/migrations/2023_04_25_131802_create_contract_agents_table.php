<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_agents', function (Blueprint $table) {
            $table->id();
            $table->string('contract_no');
            $table->string('guests_id');
            $table->string('orders_id');
            $table->string('agents_id');
            $table->string('guides_id');
            $table->string('drivers_id');
            $table->string('flight_id');
            $table->string('include');
            $table->string('exclude');
            $table->date('date_start');
            $table->date('date_end');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_agents');
    }
}
