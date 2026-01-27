<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirportShuttlesTable extends Migration
{
    public function up()
    {
        Schema::create('airport_shuttles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date')->nullable();
            $table->string('flight_number', 125)->nullable();
            $table->integer('number_of_guests')->nullable();
            $table->foreignId("order_id")->nullable()->constrained('orders')->onDelete('cascade');
            $table->foreignId("transport_id")->nullable()->constrained("transports")->onDelete("cascade");
            $table->foreignId("price_id")->nullable()->constrained("transport_prices")->onDelete("cascade");
            $table->foreignId("order_wedding_id")->nullable()->constrained('order_weddings')->onDelete('cascade');
            $table->string('src')->nullable();
            $table->string('dst')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('distance')->nullable();
            $table->string('price', 125)->nullable();
            $table->enum('nav', ['In', 'Out'])->nullable();
            $table->string('invitation_name')->nullable();
            $table->string('invitation_number')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('airport_shuttles');
    }
}
