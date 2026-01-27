<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentsTable extends Migration
{
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('pic_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('country');
            $table->string('company_address');
            $table->string('website')->nullable();
            $table->string('business_license');
            $table->string('tax_document')->nullable();
            $table->string('company_letter');
            $table->json('translation_documents')->nullable();
            $table->enum('status', ['pending', 'approved','verified' ,'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agents');
    }
}
