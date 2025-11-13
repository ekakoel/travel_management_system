<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeddingsTable extends Migration
{
    public function up()
    {
        Schema::create('weddings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->uniqid;
            $table->string('hotel_id')->nullable();

            $table->text('cover');
            $table->string('name');
            $table->string('duration');
            $table->integer('capacity');
           
            $table->string('suite_and_villas_id')->nullable();
            $table->string('ceremony_venue_id')->nullable();
            $table->string('ceremony_venue_decoration_id')->nullable();
            $table->string('reception_venue_id')->nullable();
            $table->string('reception_venue_decoration_id')->nullable();
            $table->integer('lunch_venue_id')->nullable();
            $table->integer('dinner_venue_id')->nullable();
            $table->string('transport_id')->nullable();

            $table->string('additional_service_id')->nullable();
            $table->longText('include')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('week_day_price');
            $table->string('holiday_price');
            $table->string('slot');
            $table->longText('cancellation_policy')->nullable();
            $table->longText('payment_process')->nullable();
            $table->longText('terms_and_conditions')->nullable();
            $table->string('status');
            $table->integer('author_id');
            $table->softDeletes();
            $table->timestamps();
            // $table->foreignId('reception_venue_id')->constrained('wedding_reception_venues')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('weddings');
    }
}
