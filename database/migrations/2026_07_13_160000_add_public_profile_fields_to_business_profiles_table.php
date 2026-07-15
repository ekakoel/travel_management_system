<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicProfileFieldsToBusinessProfilesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('business_profiles')) {
            return;
        }

        Schema::table('business_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('business_profiles', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('business_profiles', 'phone_2')) {
                $table->string('phone_2')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('business_profiles', 'phone_3')) {
                $table->string('phone_3')->nullable()->after('phone_2');
            }

            if (!Schema::hasColumn('business_profiles', 'whatsapp')) {
                $table->string('whatsapp')->nullable()->after('email');
            }

            if (!Schema::hasColumn('business_profiles', 'public_tagline')) {
                $table->string('public_tagline')->nullable()->after('caption');
            }

            if (!Schema::hasColumn('business_profiles', 'public_tagline_traditional')) {
                $table->string('public_tagline_traditional')->nullable()->after('public_tagline');
            }

            if (!Schema::hasColumn('business_profiles', 'public_tagline_simplified')) {
                $table->string('public_tagline_simplified')->nullable()->after('public_tagline_traditional');
            }

            if (!Schema::hasColumn('business_profiles', 'public_description')) {
                $table->text('public_description')->nullable()->after('public_tagline_simplified');
            }

            if (!Schema::hasColumn('business_profiles', 'public_description_traditional')) {
                $table->text('public_description_traditional')->nullable()->after('public_description');
            }

            if (!Schema::hasColumn('business_profiles', 'public_description_simplified')) {
                $table->text('public_description_simplified')->nullable()->after('public_description_traditional');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('business_profiles')) {
            return;
        }

        Schema::table('business_profiles', function (Blueprint $table) {
            $columns = [
                'email',
                'phone_2',
                'phone_3',
                'whatsapp',
                'public_tagline',
                'public_tagline_traditional',
                'public_tagline_simplified',
                'public_description',
                'public_description_traditional',
                'public_description_simplified',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('business_profiles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
