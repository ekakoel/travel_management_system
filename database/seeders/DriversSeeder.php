<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DriversSeeder extends Seeder
{

    public function run()
    {
        DB::table('drivers')->insert(
            [
                [
                    'name' => "Rangga",
                    'phone' => "123123123123",
                    'email' => "rangga@balikamitour.com",
                    'address' => "Denpasar",
                    'country' => "Indonesia",
                ],
            ]);
    }
}
