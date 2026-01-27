<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('additional_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('inv_id');
            $table->string('date');
            $table->string('description');
            $table->string('rate');
            $table->string('unit');
            $table->string('times');
            $table->string('amount');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('additional_invoices');
    }
}
