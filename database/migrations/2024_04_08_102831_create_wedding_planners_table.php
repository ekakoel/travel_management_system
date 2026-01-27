<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeddingPlannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wedding_planners', function (Blueprint $table) {
            $table->id();
            $table->string('wedding_planner_no');
            $table->string('type');
            $table->string('bride_id');
            $table->string('wedding_date');
            $table->string('number_of_invitations');
            $table->integer('wedding_venue_id')->nullable();
            $table->integer('ceremonial_venue_id')->nullable();
            $table->string('slot')->nullable();
            $table->integer('dinner_venue_id')->nullable();
            $table->string('dinner_venue_time_start')->nullable();
            $table->string('dinner_venue_time_end')->nullable();
            $table->integer('wedding_package_id')->nullable();
            $table->string('optional_service')->nullable();
            $table->string('checkin')->nullable();
            $table->string('checkout')->nullable();
            $table->string('duration')->nullable();
            $table->string('arrival_flight')->nullable();
            $table->string('arrival_time')->nullable();
            $table->integer('airport_shuttle_in_id')->nullable();
            $table->string('departure_flight')->nullable();
            $table->string('departure_time')->nullable();
            $table->integer('airport_shuttle_out_id')->nullable();
            $table->integer('driver_id')->nullable();
            $table->integer('guide_id')->nullable();
            $table->integer('agent_id')->nullable();
            $table->integer('handled_by')->nullable();
            $table->integer('person_in_charge_id')->nullable();
            $table->integer('bank_account_id')->nullable();
            $table->integer('remark')->nullable();
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
        Schema::dropIfExists('wedding_planners');
    }
}
