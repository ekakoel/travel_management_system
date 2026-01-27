<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraBedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_beds', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->foreignId("hotels_id")->constrained("hotels")->onDelete("cascade");
            // $table->foreignId("rooms_id")->constrained("hotelroom")->onDelete("cascade");
            $table->string('type');
            $table->string('max_age')->nullable();
            $table->string('min_age')->nullable();
            $table->longText('description')->nullable();
            $table->integer('contract_rate');
            $table->integer('markup');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extra_beds');
    }
}
