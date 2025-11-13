<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank');
            $table->string('currency');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('location');
            $table->string('address')->nullable();
            $table->string('telephone')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('bank_code')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('bank_accounts');
    }
}
