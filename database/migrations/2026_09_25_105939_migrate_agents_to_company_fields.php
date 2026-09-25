<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // Migrate existing legacy data into the new fields.
            if (!Schema::hasColumn('agents', 'company_name')) {
                $table->string('company_name')->nullable();
            }

            if (!Schema::hasColumn('agents', 'company_address')) {
                $table->text('company_address')->nullable();
            }
        });

        // Copy existing legacy values.
        DB::statement("
            UPDATE agents
            SET
                company_name = COALESCE(NULLIF(company_name, ''), name),
                company_address = COALESCE(NULLIF(company_address, ''), Address)
        ");

        Schema::table('agents', function (Blueprint $table) {
            if (Schema::hasColumn('agents', 'name')) {
                $table->dropColumn('name');
            }

            if (Schema::hasColumn('agents', 'Address')) {
                $table->dropColumn('Address');
            }

            if (Schema::hasColumn('agents', 'office')) {
                $table->dropColumn('office');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            if (!Schema::hasColumn('agents', 'name')) {
                $table->string('name')->nullable();
            }

            if (!Schema::hasColumn('agents', 'Address')) {
                $table->text('Address')->nullable();
            }

            if (!Schema::hasColumn('agents', 'office')) {
                $table->string('office')->nullable();
            }
        });

        DB::statement("
            UPDATE agents
            SET
                name = company_name,
                Address = company_address,
                office = company_address
        ");
    }
};