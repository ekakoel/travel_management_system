<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->unique();
            $table->string('name');
            $table->string('type');
            $table->string('code')->nullable();
            $table->string('email')->unique();
            $table->string('profileimg')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->string('office')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_approved')->default(false)->change();
            $table->longText('comment')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_subscribed');
            $table->boolean('subscriber');
            $table->longText('unsubscribe_reason')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
