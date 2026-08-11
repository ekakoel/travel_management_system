<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tour_prices') || ! Schema::hasColumn('tour_prices', 'markup_amount')) {
            return;
        }

        if (Schema::getColumnType('tour_prices', 'markup_amount') !== 'string') {
            Schema::table('tour_prices', function (Blueprint $table) {
                $table->string('markup_amount', 32)->nullable()->change();
            });
        }

        DB::table('tour_prices')
            ->select(['id', 'markup_amount'])
            ->whereNotNull('markup_amount')
            ->orderBy('id')
            ->chunkById(500, function ($prices) {
                foreach ($prices as $price) {
                    $stored = (string) $price->markup_amount;
                    $normalized = $this->trimInsignificantDecimalZeros($stored);

                    if ($normalized !== $stored) {
                        DB::table('tour_prices')->where('id', $price->id)->update([
                            'markup_amount' => $normalized,
                        ]);
                    }
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('tour_prices') || ! Schema::hasColumn('tour_prices', 'markup_amount')) {
            return;
        }

        if (Schema::getColumnType('tour_prices', 'markup_amount') === 'string') {
            Schema::table('tour_prices', function (Blueprint $table) {
                $table->decimal('markup_amount', 20, 6)->nullable()->change();
            });
        }
    }

    private function trimInsignificantDecimalZeros(string $value): string
    {
        $value = trim($value);

        if (! preg_match('/^\d+\.\d+$/', $value)) {
            return $value;
        }

        return rtrim(rtrim($value, '0'), '.');
    }
};
