<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_legal_name')->nullable()->after('office');
            $table->string('job_title')->nullable()->after('company_legal_name');
            $table->string('whatsapp', 50)->nullable()->after('phone');
            $table->string('website')->nullable()->after('country');
            $table->string('city')->nullable()->after('address');
            $table->string('state_region')->nullable()->after('city');
            $table->string('postal_code', 40)->nullable()->after('state_region');
            $table->string('timezone', 64)->nullable()->after('preferred_language');
            $table->string('company_registration_number')->nullable()->after('timezone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'company_legal_name',
                'job_title',
                'whatsapp',
                'website',
                'city',
                'state_region',
                'postal_code',
                'timezone',
                'company_registration_number',
            ]);
        });
    }
};
