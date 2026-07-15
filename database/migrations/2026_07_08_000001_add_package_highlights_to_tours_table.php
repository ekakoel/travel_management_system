<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackageHighlightsToToursTable extends Migration
{
    public function up()
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->longText('package_highlights')->nullable()->after('description_simplified');
            $table->longText('package_highlights_traditional')->nullable()->after('package_highlights');
            $table->longText('package_highlights_simplified')->nullable()->after('package_highlights_traditional');
        });
    }

    public function down()
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'package_highlights',
                'package_highlights_traditional',
                'package_highlights_simplified',
            ]);
        });
    }
}
