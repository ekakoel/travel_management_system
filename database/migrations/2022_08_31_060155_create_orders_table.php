<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer("user_id");
            $table->integer("rsv_id")->nullable();
            $table->string("orderno");
            $table->string("confirmation_order");
            $table->string("name");
            $table->string("email");
            $table->string("servicename");
            $table->string("service");
            $table->string("service_type")->nullable();
            $table->integer("service_id")->nullable();
            $table->string("subservice")->nullable();
            $table->integer("subservice_id")->nullable();
            $table->integer("extra_time")->nullable();
            $table->integer("price_id") ->nullable();
            $table->date("checkin")->nullable();
            $table->date("checkout")->nullable();
            $table->dateTime("travel_date")->nullable();
            $table->string("location")->nullable();
            $table->string("src")->nullable();
            $table->string("dst")->nullable();
            $table->string("tour_type")->nullable();
            $table->text("itinerary")->nullable();
            $table->integer("number_of_guests")->nullable();
            $table->string("number_of_guests_room")->nullable();
            
            $table->text("guest_detail")->nullable();
            $table->string("request_quotation")->nullable();
            $table->string("airport_shuttle")->nullable();
            $table->string("airport_shuttle_price")->nullable();
            $table->string("wedding_order_id")->nullable();
            $table->string("wedding_date")->nullable();
            $table->string("groom_name")->nullable();
            $table->string("bride_name")->nullable();

            
            $table->text("special_day")->nullable();
            $table->string("special_date")->nullable();
            $table->string("extra_bed")->nullable();
            $table->string("capacity")->nullable();
            $table->text("benefits") ->nullable();
            $table->text("booking_code") ->nullable();
            $table->text("include") ->nullable();
            $table->text("exclude") ->nullable();
            $table->string("destinations") ->nullable();
            $table->text("additional_info") ->nullable();
            $table->text("msg") ->nullable();
            $table->integer("number_of_room")->nullable();
            $table->string("duration")->nullable();
            $table->string("additional_service_date")->nullable();
            $table->string("additional_service")->nullable();
            $table->string("additional_service_qty")->nullable();
            $table->string("additional_service_price")->nullable();
            $table->string("additional_service_total_price")->nullable();

            $table->string("price_pax")->nullable();
            $table->string("normal_price")->nullable();
            $table->string("kick_back")->nullable();
            $table->string("kick_back_per_pax")->nullable();
            $table->string("extra_bed_id")->nullable();
            $table->string("extra_bed_price")->nullable();
            $table->string("extra_bed_total_price")->nullable();
            $table->string("optional_price")->nullable();
            $table->string("price_total")->nullable();
            $table->text("alasan_discounts")->nullable();
            $table->string("discounts")->nullable();
            $table->string("bookingcode")->nullable();
            $table->string("bookingcode_disc")->nullable();
            $table->string("promotion")->nullable();
            $table->string("promotion_disc")->nullable();
            $table->string("order_tax")->nullable();
            $table->string("final_price")->nullable();
            $table->string("usd_rate")->nullable();
            $table->string("twd_rate")->nullable();
            $table->string("cny_rate")->nullable();
            $table->string("package_name")->nullable();
            $table->string("promo_id")->nullable();
            $table->string("promo_name")->nullable();
            $table->string("book_period_start")->nullable();
            $table->string("book_period_end")->nullable();
            $table->string("period_start")->nullable();
            $table->string("period_end")->nullable();
            $table->enum('status',['Draft','Pending','Confirmed','Approved','Canceled','Rejected','Invalid','Paid','Deleted'])->default('Draft');
            $table->string("sales_agent")->nullable();
            $table->integer("driver_id")->nullable();
            $table->integer("guide_id")->nullable();
            $table->string("arrival_flight")->nullable();
            $table->string("arrival_time")->nullable();
            $table->string("airport_shuttle_in")->nullable();
            $table->string("departure_flight")->nullable();
            $table->string("departure_time")->nullable();
            $table->string("airport_shuttle_out")->nullable();
            $table->string("notification")->nullable();
            $table->text("note") ->nullable();
            $table->longText("cancellation_policy") ->nullable();
            $table->integer("verified_by") ->nullable();
            $table->integer("handled_by") ->nullable();
            $table->date("handled_date") ->nullable();
            $table->string("pickup_name") ->nullable();
            $table->string("pickup_phone") ->nullable();
            $table->string("pickup_location") ->nullable();
            $table->string("pickup_date") ->nullable();
            $table->string("dropoff_date") ->nullable();
            $table->string("dropoff_location") ->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
