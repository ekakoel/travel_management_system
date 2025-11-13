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
        Schema::create('villas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->unique();
            $table->string('region');
            $table->string('address');
            $table->integer('airport_duration')->nullable();
            $table->integer('airport_distance')->nullable();
            $table->string('contact_person');
            $table->string('phone');
            $table->text('cover');
            $table->string('web')->nullable();
            $table->string('min_stay')->nullable();
            $table->string('check_in_time')->nullable();
            $table->string('check_out_time')->nullable();
            $table->string('map')->nullable();
            $table->string('benefits')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description_traditional')->nullable();
            $table->longText('description_simplified')->nullable();
            $table->longText('facility')->nullable();
            $table->longText('facility_traditional')->nullable();
            $table->longText('facility_simplified')->nullable();
            $table->longText('additional_info')->nullable();
            $table->longText('additional_info_traditional')->nullable();
            $table->longText('additional_info_simplified')->nullable();
            $table->longText('cancellation_policy')->nullable();
            $table->longText('cancellation_policy_traditional')->nullable();
            $table->longText('cancellation_policy_simplified')->nullable();
            $table->integer('author_id');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villas');
    }
};
