<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderLogsTable extends Migration
{
    public function up()
    {
        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->integer('order_wedding_id');
            $table->string('action');
            $table->string('url');
            $table->string('method');
            $table->string('agent')->nullable();
            $table->string('admin')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_logs');
    }
}
