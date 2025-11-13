<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceAdminsTable extends Migration
{
    public function up()
    {
        Schema::create('invoice_admins', function (Blueprint $table) {
            $table->id();
            $table->string('admin_code')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('agent_id')->nullable();
            $table->string('inv_no');
            $table->integer('rsv_id');
            $table->string('inv_date');
            $table->string('due_date');
            $table->string('total_usd')->nullable();
            $table->string('total_idr')->nullable();
            $table->string('total_cny')->nullable();
            $table->string('total_twd')->nullable();
            $table->string('rate_usd')->nullable();
            $table->string('sell_usd');
            $table->string('rate_twd')->nullable();
            $table->string('sell_twd');
            $table->string('rate_cny')->nullable();
            $table->string('sell_cny');
            $table->string('balance')->nullable();
            $table->string('bank_id')->nullable();
            $table->integer('currency_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_admins');
    }
}
