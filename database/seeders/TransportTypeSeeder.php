<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class TransportTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('transport_types')->insert(
            [
                [
                    'type' => 'Micro Bus',
                ],
                [
                    'type' => 'Bus',
                ],
                [
                    'type' => 'Car',
                ],
                [
                    'type' => 'Luxuri',
                ],
            ]);
    }
}
