<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {

            if (!Schema::hasColumn('agents', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }

            if (!Schema::hasColumn('agents', 'company_name')) {
                $table->string('company_name')->nullable();
            }

            if (!Schema::hasColumn('agents', 'company_type')) {
                $table->string('company_type')->nullable();
            }

            if (!Schema::hasColumn('agents', 'pic_name')) {
                $table->string('pic_name')->nullable();
            }

            if (!Schema::hasColumn('agents', 'contact_name')) {
                $table->string('contact_name')->nullable();
            }

            if (!Schema::hasColumn('agents', 'contact_email')) {
                $table->string('contact_email')->nullable();
            }

            if (!Schema::hasColumn('agents', 'position')) {
                $table->string('position')->nullable();
            }

            if (!Schema::hasColumn('agents', 'preferred_contact')) {
                $table->string('preferred_contact')->nullable();
            }

            if (!Schema::hasColumn('agents', 'main_market')) {
                $table->string('main_market')->nullable();
            }

            if (!Schema::hasColumn('agents', 'monthly_bali_clients')) {
                $table->unsignedInteger('monthly_bali_clients')->nullable();
            }

            if (!Schema::hasColumn('agents', 'interested_services')) {
                $table->json('interested_services')->nullable();
            }

            if (!Schema::hasColumn('agents', 'agreed_to_terms_at')) {
                $table->timestamp('agreed_to_terms_at')->nullable();
            }

            if (!Schema::hasColumn('agents', 'agreed_to_contact_at')) {
                $table->timestamp('agreed_to_contact_at')->nullable();
            }

            if (!Schema::hasColumn('agents', 'company_address')) {
                $table->text('company_address')->nullable();
            }

            if (!Schema::hasColumn('agents', 'website')) {
                $table->string('website')->nullable();
            }

            if (!Schema::hasColumn('agents', 'business_license_number')) {
                $table->string('business_license_number')->nullable();
            }

            if (!Schema::hasColumn('agents', 'business_license')) {
                $table->string('business_license')->nullable();
            }

            if (!Schema::hasColumn('agents', 'company_letter')) {
                $table->string('company_letter')->nullable();
            }

            if (!Schema::hasColumn('agents', 'tax_document')) {
                $table->string('tax_document')->nullable();
            }

            if (!Schema::hasColumn('agents', 'translation_documents')) {
                $table->json('translation_documents')->nullable();
            }

            if (!Schema::hasColumn('agents', 'status')) {
                $table->string('status')->default('pending');
            }

            if (!Schema::hasColumn('agents', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }

            if (!Schema::hasColumn('agents', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $columns = [
                'user_id',
                'company_name',
                'company_type',
                'pic_name',
                'contact_name',
                'contact_email',
                'position',
                'preferred_contact',
                'main_market',
                'monthly_bali_clients',
                'interested_services',
                'agreed_to_terms_at',
                'agreed_to_contact_at',
                'company_address',
                'website',
                'business_license_number',
                'business_license',
                'company_letter',
                'tax_document',
                'translation_documents',
                'status',
                'approved_at',
                'rejection_reason',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('agents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};