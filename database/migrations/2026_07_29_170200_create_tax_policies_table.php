<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tax_policies')) {
            return;
        }

        Schema::create('tax_policies', function (Blueprint $table) {
            $table->id();
            $table->string('service', 64);
            $table->string('name', 191);
            $table->unsignedBigInteger('percentage_scaled');
            $table->unsignedInteger('percentage_scale')->default(1_000_000);
            $table->string('calculation_type', 32)->default('exclusive');
            $table->string('taxable_base', 64);
            $table->string('status', 32)->default('draft');
            $table->dateTime('effective_from', 6);
            $table->dateTime('effective_until', 6)->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at', 6)->nullable();
            $table->timestamps(6);

            $table->index(
                ['service', 'status', 'effective_from', 'effective_until'],
                'tax_policies_effective_lookup_idx'
            );
            $table->index('approved_by', 'tax_policies_approver_idx');

            if (Schema::hasTable('users')) {
                $table->foreign('approved_by', 'tax_policies_approver_fk')
                    ->references('id')
                    ->on('users')
                    ->restrictOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_policies');
    }
};
