<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    
    public function up()
    {
        if (Schema::hasTable('orders')) {
            return;
        }

        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer("user_id");
            $table->integer("rsv_id")->nullable();
            $table->string("orderno", 64);
            $table->string("confirmation_order", 64);
            $table->string("name", 191);
            $table->string("email", 191);
            $table->string("servicename", 191);
            $table->string("service", 100);
            $table->string("service_type", 100)->nullable();
            $table->integer("service_id")->nullable();
            $table->string("subservice", 191)->nullable();
            $table->integer("subservice_id")->nullable();
            $table->integer("extra_time")->nullable();
            $table->integer("price_id") ->nullable();
            $table->date("checkin")->nullable();
            $table->date("checkout")->nullable();
            $table->dateTime("travel_date")->nullable();
            $table->text("location")->nullable();
            $table->text("src")->nullable();
            $table->text("dst")->nullable();
            $table->string("tour_type", 100)->nullable();
            $table->text("itinerary")->nullable();
            $table->integer("number_of_guests")->nullable();
            $table->text("number_of_guests_room")->nullable();
            
            $table->text("guest_detail")->nullable();
            $table->text("request_quotation")->nullable();
            $table->string("airport_shuttle", 50)->nullable();
            $table->text("airport_shuttle_price")->nullable();
            $table->string("wedding_order_id", 64)->nullable();
            $table->string("wedding_date", 50)->nullable();
            $table->string("groom_name", 191)->nullable();
            $table->string("bride_name", 191)->nullable();

            
            $table->text("special_day")->nullable();
            $table->string("special_date", 50)->nullable();
            $table->text("extra_bed")->nullable();
            $table->text("capacity")->nullable();
            $table->text("benefits") ->nullable();
            $table->text("booking_code") ->nullable();
            $table->text("include") ->nullable();
            $table->text("exclude") ->nullable();
            $table->text("destinations") ->nullable();
            $table->text("additional_info") ->nullable();
            $table->text("msg") ->nullable();
            $table->integer("number_of_room")->nullable();
            $table->string("duration", 50)->nullable();
            $table->text("additional_service_date")->nullable();
            $table->text("additional_service")->nullable();
            $table->text("additional_service_qty")->nullable();
            $table->text("additional_service_price")->nullable();
            $table->text("additional_service_total_price")->nullable();

            $table->text("price_pax")->nullable();
            $table->text("normal_price")->nullable();
            $table->text("kick_back")->nullable();
            $table->text("kick_back_per_pax")->nullable();
            $table->text("extra_bed_id")->nullable();
            $table->text("extra_bed_price")->nullable();
            $table->text("extra_bed_total_price")->nullable();
            $table->text("optional_price")->nullable();
            $table->text("price_total")->nullable();
            $table->text("alasan_discounts")->nullable();
            $table->text("discounts")->nullable();
            $table->text("bookingcode")->nullable();
            $table->text("bookingcode_disc")->nullable();
            $table->text("promotion")->nullable();
            $table->text("promotion_disc")->nullable();
            $table->text("order_tax")->nullable();
            $table->text("final_price")->nullable();
            $table->string("usd_rate", 50)->nullable();
            $table->string("twd_rate", 50)->nullable();
            $table->string("cny_rate", 50)->nullable();
            $table->text("package_name")->nullable();
            $table->text("promo_id")->nullable();
            $table->text("promo_name")->nullable();
            $table->string("book_period_start", 50)->nullable();
            $table->string("book_period_end", 50)->nullable();
            $table->string("period_start", 50)->nullable();
            $table->string("period_end", 50)->nullable();
            $table->enum('status',['Draft','Pending','Confirmed','Approved','Canceled','Rejected','Invalid','Paid','Deleted'])->default('Draft');
            $table->string("sales_agent", 191)->nullable();
            $table->integer("driver_id")->nullable();
            $table->integer("guide_id")->nullable();
            $table->string("arrival_flight", 100)->nullable();
            $table->string("arrival_time", 50)->nullable();
            $table->text("airport_shuttle_in")->nullable();
            $table->string("departure_flight", 100)->nullable();
            $table->string("departure_time", 50)->nullable();
            $table->text("airport_shuttle_out")->nullable();
            $table->string("notification", 100)->nullable();
            $table->text("note") ->nullable();
            $table->longText("cancellation_policy") ->nullable();
            $table->integer("verified_by") ->nullable();
            $table->integer("handled_by") ->nullable();
            $table->date("handled_date") ->nullable();
            $table->string("pickup_name", 191) ->nullable();
            $table->string("pickup_phone", 50) ->nullable();
            $table->text("pickup_location") ->nullable();
            $table->string("pickup_date", 50) ->nullable();
            $table->string("dropoff_date", 50) ->nullable();
            $table->text("dropoff_location") ->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
