<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderWeddingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_weddings', function (Blueprint $table) {
            $table->id();
            $table->string('orderno');
            $table->string('confirmation_number')->nullable();
            $table->string('rsv_id')->nullable();
            $table->string('service')->nullable();
            $table->integer('service_id')->nullable();
            $table->string('type')->nullable();
            $table->string('slot')->nullable();
            $table->integer('wedding_planner_id')->nullable();
            $table->integer('hotel_id');
            $table->string('checkin')->nullable();
            $table->string('checkout')->nullable();
            $table->integer('duration')->nullable();
            $table->string('wedding_date');
            $table->integer('number_of_invitation');
            $table->integer('brides_id');
            $table->string('basic_or_arrangement')->nullable();
            $table->integer('ceremony_venue_duration')->nullable();
            $table->integer('ceremony_venue_id')->nullable();
            $table->string('ceremony_venue_price')->nullable();
            $table->integer('ceremony_venue_decoration_id')->nullable();
            $table->string('ceremony_venue_decoration_price')->nullable();
            $table->integer('ceremony_venue_invitations')->nullable();
            $table->string('reception_date_start')->nullable();
            $table->integer('reception_venue_id')->nullable();
            $table->string('reception_venue_price')->nullable();
            $table->integer('reception_venue_decoration_id')->nullable();
            $table->string('reception_venue_decoration_price')->nullable();
            $table->integer('reception_venue_invitations')->nullable();
            $table->integer('dinner_venue_id')->nullable();
            $table->string('dinner_venue_date')->nullable();
            $table->string('dinner_venue_price')->nullable();
            $table->integer('lunch_venue_id')->nullable();
            $table->string('lunch_venue_date')->nullable();
            $table->string('lunch_venue_price')->nullable();
            $table->integer('room_bride_id')->nullable();
            $table->string('room_bride_price')->nullable();
            $table->string('room_invitations_id')->nullable();
            $table->string('accommodation_price')->nullable();
            $table->string('extra_bed_price')->nullable();
            $table->integer('transport_id')->nullable();
            $table->string('transport_type')->nullable();
            $table->string('transport_price')->nullable();
            $table->string('transport_invitations_price')->nullable();
            $table->integer('makeup_id')->nullable();
            $table->string('makeup_price')->nullable();
            $table->integer('documentation_id')->nullable();
            $table->string('documentation_price')->nullable();
            $table->string('entertainment_id')->nullable();
            $table->string('entertainment_price')->nullable();
            $table->string('additional_services')->nullable();
            $table->string('additional_services_price')->nullable();
            $table->string('addser_price')->nullable();
            $table->string('markup')->nullable();
            $table->string('package_price')->nullable();
            $table->string('price_type')->nullable();
            $table->string('final_price')->nullable();
            $table->longText('remark')->nullable();
            $table->integer('agent_id')->nullable();
            $table->integer('verified_by')->nullable();
            $table->integer('verified_date')->nullable();
            $table->integer('handled_by')->nullable();
            $table->integer('handled_date')->nullable();
            $table->integer('confirm_with')->nullable();
            $table->integer('confirm_by')->nullable();
            $table->integer('confirm_address')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_weddings');
    }
}
