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
        Schema::create('wedding_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('booking_code')->nullable();
            $table->string('bride')->nullable();
            $table->string('groom')->nullable();
            $table->string('wedding_organizer')->nullable();
            $table->date('wedding_date')->nullable();
            $table->tinyInteger('communication_efficiency')->nullable();
            $table->tinyInteger('workflow_planning')->nullable();
            $table->tinyInteger('material_preparation')->nullable();
            $table->tinyInteger('service_attitude')->nullable();
            $table->tinyInteger('execution_of_workflow')->nullable();
            $table->tinyInteger('time_management')->nullable();
            $table->tinyInteger('guest_care')->nullable();
            $table->tinyInteger('team_coordination')->nullable();
            $table->tinyInteger('third_party_coordination')->nullable();
            $table->tinyInteger('problem_solving_ability')->nullable();
            $table->tinyInteger('wrap_up_and_item_check')->nullable();
            $table->text('couple_mood')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_review')->nullable();
            $table->enum('status',['pending','accepted','rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wedding_reviews');
    }
};
