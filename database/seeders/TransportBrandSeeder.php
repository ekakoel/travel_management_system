<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransportBrandSeeder extends Seeder
{
    public function run()
    {
        DB::table('transport_brands')->insert(
            [
                [
                    'brand' => 'Toyota',
                ],
                [
                    'brand' => 'Daihatsu',
                ],
                [
                    'brand' => 'Suzuki',
                ],
                [
                    'brand' => 'Honda',
                ],
                [
                    'brand' => 'Mitsubishi',
                ],
                [
                    'brand' => 'KIA',
                ],
                [
                    'brand' => 'Nissan',
                ],
                [
                    'brand' => 'Datsun',
                ],
                [
                    'brand' => 'Mazda',
                ],
                [
                    'brand' => 'Isuzu',
                ],
                [
                    'brand' => 'BMW',
                ],
                [
                    'brand' => 'Mercedes-Benz',
                ],
            ]);
    }
}
