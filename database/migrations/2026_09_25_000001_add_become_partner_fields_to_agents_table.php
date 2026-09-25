<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('company_name')->nullable();
            $table->string('company_type')->nullable();
            $table->string('website')->nullable();
            $table->string('business_license_number')->nullable();

            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('position')->nullable();
            $table->string('preferred_contact')->nullable();

            $table->string('main_market')->nullable();
            $table->unsignedInteger('monthly_bali_clients')->nullable();
            $table->json('interested_services')->nullable();

            $table->timestamp('agreed_to_terms_at')->nullable();
            $table->timestamp('agreed_to_contact_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn([
                'user_id',
                'company_name',
                'company_type',
                'website',
                'business_license_number',
                'contact_name',
                'contact_email',
                'position',
                'preferred_contact',
                'main_market',
                'monthly_bali_clients',
                'interested_services',
                'agreed_to_terms_at',
                'agreed_to_contact_at',
            ]);
        });
    }
};
