<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('doku_virtual_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('invoice_id')->nullable();
            $table->string('invoice_number')->unique();
            $table->string('currency')->unique();
            $table->decimal('amount', 15, 2)->nullable();
            $table->timestamp('created_date')->nullable();
            $table->timestamp('expired_date')->nullable();
            $table->string('token_id')->nullable();
            $table->string('payment_Payment deadline')->nullable();
            $table->string('checkout_url')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('provider')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doku_virtual_accounts');
    }
};

