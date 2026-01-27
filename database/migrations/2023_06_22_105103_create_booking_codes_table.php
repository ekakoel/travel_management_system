<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingCodesTable extends Migration
{
    public function up()
    {
        Schema::create('booking_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('discounts');
            $table->integer('amount');
            $table->integer('used');
            $table->integer('author');
            $table->date('expired_date');
            $table->string('status');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('booking_codes');
    }
}
