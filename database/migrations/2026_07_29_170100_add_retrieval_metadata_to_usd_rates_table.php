<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('usd_rates')) {
            return;
        }

        Schema::table('usd_rates', function (Blueprint $table) {
            if (!Schema::hasColumn('usd_rates', 'retrieved_at')) {
                $table->dateTime('retrieved_at', 6)->nullable()->after('difference');
            }
            if (!Schema::hasColumn('usd_rates', 'retrieval_source')) {
                $table->string('retrieval_source', 64)->nullable()->after('retrieved_at');
            }
        });

        Schema::table('usd_rates', function (Blueprint $table) {
            $table->index(['name', 'retrieved_at'], 'usd_rates_name_retrieved_idx');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('usd_rates')) {
            return;
        }

        Schema::table('usd_rates', function (Blueprint $table) {
            if (Schema::hasColumn('usd_rates', 'retrieved_at')) {
                $table->dropIndex('usd_rates_name_retrieved_idx');
            }
        });

        $columns = array_values(array_filter(
            ['retrieval_source', 'retrieved_at'],
            fn (string $column) => Schema::hasColumn('usd_rates', $column)
        ));

        if ($columns !== []) {
            Schema::table('usd_rates', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
