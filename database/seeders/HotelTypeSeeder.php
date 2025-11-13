<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HotelTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('hotel_types')->insert(
            [
                [
                    'hotels_id' => 1,
                    'type' => 'Hotel',
                ],
                [
                    'hotels_id' => 2,
                    'type' => 'Vila',
                ],
                [
                    'hotels_id' => 3,
                    'type' => 'Hotel',
                ],
                [
                    'hotels_id' => 4,
                    'type' => 'Hotel',
                ],
                [
                    'hotels_id' => 5,
                    'type' => 'Hotel',
                ],
                [
                    'hotels_id' => 6,
                    'type' => 'Hotel',
                ],
                [
                    'hotels_id' => 7,
                    'type' => 'Hotel',
                ],
                [
                    'hotels_id' => 8,
                    'type' => 'Hotel',
                ],
            ]);
    }
}
