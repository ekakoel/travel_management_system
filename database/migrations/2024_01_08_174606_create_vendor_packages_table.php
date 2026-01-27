<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_packages', function (Blueprint $table) {
            $table->id();
            $table->text('cover');
            $table->string('venue');
            $table->string('service');
            $table->string('duration');
            $table->string('type');
            $table->string('time');
            $table->string('price')->nullable();
            $table->text('description')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('status');
            $table->integer('vendor_id');
            $table->integer('hotel_id')->nullable();
            $table->integer('author');
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
        Schema::dropIfExists('vendor_packages');
    }
}
