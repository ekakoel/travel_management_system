<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('doku_payments')) {
            return;
        }

        Schema::create('doku_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('invoice_number', 30)->unique();
            $table->string('request_id', 64)->unique();
            $table->string('notification_request_id', 64)->nullable()->unique();
            $table->unsignedBigInteger('amount_idr');
            $table->decimal('credited_amount', 15, 2)->nullable();
            $table->string('invoice_currency', 3);
            $table->decimal('sell_rate', 18, 8)->nullable();
            $table->string('status', 20)->default('Pending')->index();
            $table->text('checkout_url')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('original_request_id', 64)->nullable();
            $table->string('channel', 64)->nullable();
            $table->unsignedBigInteger('payment_confirmation_id')->nullable()->unique();
            $table->text('review_reason')->nullable();
            $table->json('raw_response')->nullable();
            $table->json('raw_notification')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doku_payments');
    }
};
