<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');

            $table->string('company_name')->nullable()->after('name');
            $table->string('company_type')->nullable()->after('company_name');
            $table->string('business_license_number')->nullable()->after('company_type');

            $table->string('contact_name')->nullable()->after('name');
            $table->string('contact_email')->nullable()->after('email');
            $table->string('position')->nullable()->after('contact_email');
            $table->string('preferred_contact')->nullable()->after('position');

            $table->string('main_market')->nullable()->after('preferred_contact');
            $table->unsignedInteger('monthly_bali_clients')->nullable()->after('main_market');
            $table->json('interested_services')->nullable()->after('monthly_bali_clients');

            $table->timestamp('agreed_to_terms_at')->nullable()->after('interested_services');
            $table->timestamp('agreed_to_contact_at')->nullable()->after('agreed_to_terms_at');
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn([
                'user_id',
                'company_name',
                'company_type',
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
