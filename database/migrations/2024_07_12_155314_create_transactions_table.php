<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('receipt');
            $table->string('kurs');
            $table->integer('wallet_id');
            $table->string('transaction_code');
            $table->string('transaction_date');
            $table->integer('invoice_id');
            $table->string('name');
            $table->string('type');
            $table->string('amount');
            $table->longText('description')->nullable();
            $table->integer('author_id');
            $table->longText('note')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
