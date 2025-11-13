<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('villa_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('villa_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('markup')->nullable();
            $table->integer('kick_back')->nullable();
            $table->integer('contract_rate');
            $table->longText('benefits')->nullable();
            $table->longText('benefits_traditional')->nullable();
            $table->longText('benefits_simplified')->nullable();
            $table->longText('additional_info')->nullable();
            $table->longText('additional_info_traditional')->nullable();
            $table->longText('additional_info_simplified')->nullable();
            $table->longText('cancellation_policy')->nullable();
            $table->longText('cancellation_policy_traditional')->nullable();
            $table->longText('cancellation_policy_simplified')->nullable();
            $table->integer('author');
            $table->enum('status',['Draft','Active','Expired'])->default('Draft');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_prices');
    }
};
