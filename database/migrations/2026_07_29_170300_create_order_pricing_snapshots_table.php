<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_pricing_snapshots')) {
            return;
        }

        Schema::create('order_pricing_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedInteger('snapshot_sequence')->default(1);
            $table->string('pricing_version', 64);
            $table->string('service', 64);
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('price_id');
            $table->char('base_currency', 3)->default('IDR');
            $table->char('display_currency', 3)->default('USD');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('contract_rate_idr');
            $table->unsignedBigInteger('markup_amount_minor');
            $table->char('markup_currency', 3);
            $table->unsignedBigInteger('markup_idr');
            $table->unsignedBigInteger('subtotal_idr');
            $table->unsignedBigInteger('tax_policy_id');
            $table->unsignedBigInteger('tax_percentage_scaled');
            $table->unsignedInteger('tax_percentage_scale')->default(1_000_000);
            $table->unsignedBigInteger('tax_amount_idr');
            $table->unsignedBigInteger('rate_id');
            $table->string('rate_pair', 16)->default('USD/IDR');
            $table->string('rate_side', 16)->default('sell');
            $table->unsignedBigInteger('rate_value_scaled');
            $table->unsignedInteger('rate_value_scale')->default(1_000_000);
            $table->string('rate_source', 64);
            $table->dateTime('rate_retrieved_at', 6);
            $table->unsignedInteger('rate_max_age_seconds')->default(86_400);
            $table->unsignedBigInteger('unit_price_idr');
            $table->unsignedBigInteger('unit_price_usd_minor');
            $table->unsignedBigInteger('gross_total_idr');
            $table->unsignedBigInteger('gross_total_usd_minor');
            $table->unsignedBigInteger('discount_total_idr')->default(0);
            $table->unsignedBigInteger('discount_total_usd_minor')->default(0);
            $table->unsignedBigInteger('addon_total_idr')->default(0);
            $table->unsignedBigInteger('addon_total_usd_minor')->default(0);
            $table->unsignedBigInteger('final_total_idr');
            $table->unsignedBigInteger('final_total_usd_minor');
            $table->string('rounding_policy', 64)->default('half-up-v1');
            $table->dateTime('calculated_at', 6);
            $table->unsignedBigInteger('calculated_by')->nullable();
            $table->string('reason', 500)->nullable();
            $table->char('input_fingerprint', 64);
            $table->char('snapshot_checksum', 64);
            $table->longText('breakdown');
            $table->dateTime('created_at', 6);

            $table->unique(['order_id', 'snapshot_sequence'], 'ops_order_sequence_unique');
            $table->index(['service', 'calculated_at'], 'ops_service_calculated_idx');
            $table->index(['rate_id', 'calculated_at'], 'ops_rate_calculated_idx');
            $table->index(['tax_policy_id', 'calculated_at'], 'ops_tax_calculated_idx');
            $table->index('price_id', 'ops_price_idx');
            $table->index('calculated_by', 'ops_calculated_by_idx');

            $table->foreign('order_id', 'ops_order_fk')->references('id')->on('orders')->restrictOnDelete();
            $table->foreign('tax_policy_id', 'ops_tax_policy_fk')->references('id')->on('tax_policies')->restrictOnDelete();
            $table->foreign('rate_id', 'ops_rate_fk')->references('id')->on('usd_rates')->restrictOnDelete();

            if (Schema::hasTable('users')) {
                $table->foreign('calculated_by', 'ops_calculated_by_fk')
                    ->references('id')
                    ->on('users')
                    ->restrictOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_pricing_snapshots');
    }
};
