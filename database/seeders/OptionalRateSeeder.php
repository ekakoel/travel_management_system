<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OptionalRateSeeder extends Seeder
{

    public function run()
    {
        DB::table('optional_rates')->insert(
            [
                [
                    'name' => "Set Breakfast",
                    'hotels_id'=>1,
                    'service' => "Hotel",
                    'service_id' => '1',
                    'type'=> 'Meals',
                    'contract_rate' => 500000,
                    'markup' => 5,
                    'description' => 'Net per person',
                ],
                [
                    'name' => "Set Lunch",
                    'hotels_id'=>1,
                    'service' => "Hotel",
                    'service_id' => '1',
                    'type'=> 'Meals',
                    'contract_rate' => 600000,
                    'markup' => 5,
                    'description' => 'Net per person',
                ],
                [
                    'name' => "Set Dinner",
                    'hotels_id'=>1,
                    'service' => "Hotel",
                    'service_id' => '1',
                    'type'=> 'Meals',
                    'contract_rate' => 700000,
                    'markup' => 5,
                    'description' => 'Net per person',
                ],
            ]);
    }
}
