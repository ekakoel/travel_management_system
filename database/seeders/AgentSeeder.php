<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AgentSeeder extends Seeder
{

    public function run()
    {
        DB::table('agents')->insert(
            [
                [
                    'name' => "Bali Kami",
                    'phone' => "3213123123123",
                    'email' => "Reservation@balikamitour.com",
                    'address' => "Denpasar",
                    'office' => "BLK",
                    'country' => "Indonesia",
                ],
            ]);
    }
}
