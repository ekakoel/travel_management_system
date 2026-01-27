<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeddingInvitationsTable extends Migration
{
    public function up()
    {
        Schema::create('wedding_invitations', function (Blueprint $table) {
            $table->id();
            $table->integer("wedding_planner_id")->nullable();
            $table->integer("order_wedding_id")->nullable();
            $table->string("sex");
            $table->string("name");
            $table->string("chinese_name")->nullable();
            $table->string("country");
            $table->string("passport_no");
            $table->string("passport_img");
            $table->string("phone")->nullable();
            $table->string("date_of_birth")->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('wedding_invitations');
    }
}
