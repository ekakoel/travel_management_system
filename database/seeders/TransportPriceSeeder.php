<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransportPriceSeeder extends Seeder
{
    public function run()
    {
        DB::table('transport_prices')->insert(
            [
                [
                    'transports_id' => 1,
                    'type' => 'Airport Shuttle',
                    'duration' => '2',
                    'contract_rate' => 500000,
                    'markup' => '5',
                    'extra_time' => '10',
                    'additional_info' => 'Airport shuttle fare, no stops on the way',
                    'author_id' => 1,
                ],
                [
                    'transports_id' => 1,
                    'type' => 'Airport Shuttle',
                    'duration' => '3',
                    'contract_rate' => 600000,
                    'markup' => '5',
                    'extra_time' => '10',
                    'additional_info' => 'Airport shuttle fare, no stops on the way',
                    'author_id' => 1,
                ],
                [
                    'transports_id' => 1,
                    'type' => 'Airport Shuttle',
                    'duration' => '4',
                    'contract_rate' => 700000,
                    'markup' => '5',
                    'extra_time' => '10',
                    'additional_info' => 'Airport shuttle fare, no stops on the way',
                    'author_id' => 1,
                ],
                [
                    'transports_id' => 1,
                    'type' => 'Airport Shuttle',
                    'duration' => '5',
                    'contract_rate' => 800000,
                    'markup' => '5',
                    'extra_time' => '10',
                    'additional_info' => 'Airport shuttle fare, no stops on the way',
                    'author_id' => 1,
                ],
                [
                    'transports_id' => 1,
                    'type' => 'Airport Shuttle',
                    'duration' => '6',
                    'contract_rate' => 900000,
                    'markup' => '5',
                    'extra_time' => '10',
                    'additional_info' => 'Airport shuttle fare, no stops on the way',
                    'author_id' => 1,
                ],
            ]);
    }
}
