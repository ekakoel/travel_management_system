<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdditionalServiceSeeder extends Seeder
{
    public function run()
    {
        DB::table('additional_services')->insert(
            [
                [
                    'rsv_id' => 1,
                    'admin_id' => 1,
                    'tgl' => '2022-12-01',
                    'service' => 'Breakfast',
                    'type' => 'Include',
                    'location' => 'Nusa Dua',
                    'loc_name' => 'Hotel A',
                    'qty' => 2,
                    'price' => '',
                ],
                [
                    'rsv_id' => 1,
                    'admin_id' => 1,
                    'tgl' => '2022-12-01',
                    'service' => 'Lunch',
                    'type' => 'Charged',
                    'location' => 'Nusa Dua',
                    'loc_name' => 'Restourant B',
                    'qty' => 2,
                    'price' => rand(10,100), 
                ],
                [
                    'rsv_id' => 1,
                    'admin_id' => 1,
                    'tgl' => '2022-12-01',
                    'service' => 'Dinner',
                    'type' => 'Charged',
                    'location' => 'Nusa Dua',
                    'loc_name' => 'Restourant C',
                    'qty' => 2,
                    'price' => rand(10,100),
                ],
                [
                    'rsv_id' => 1,
                    'admin_id' => 1,
                    'tgl' => '2022-12-01',
                    'service' => 'Mineral Water',
                    'type' => 'Charged',
                    'location'=> '',
                    'loc_name' => '',
                    'qty' => 2,
                    'price' => rand(10,100), 
                ],
                [
                    'rsv_id' => 1,
                    'admin_id' => 1,
                    'tgl' => '2022-12-01',
                    'service' => 'Tipping Guide',
                    'type' => 'Charged',
                    'location' => '',
                    'loc_name' => '',
                    'qty' => 2,
                    'price' => rand(10,100), 
                ],
            ]);
    }
}
