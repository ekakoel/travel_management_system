<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuidesSeeder extends Seeder
{

    public function run()
    {
        DB::table('guides')->insert(
            [
                [
                    'name' => "Wanwan",
                    'phone' => "123123123123",
                    'email' => "wanwan@balikamitour.com",
                    'address' => "Denpasar",
                    'country' => "Indonesia",
                ],
            ]);
    }
}
