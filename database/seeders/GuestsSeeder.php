<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuestsSeeder extends Seeder
{

    public function run()
    {
        DB::table('guests')->insert(
            [
                [
                    'name' => 'Andiana',
                    'rsv_id' => 1,
                    'name_mandarin' => "安迪亚娜",
                    'date_of_birth' => "2000-08-01",
                    'sex' => "m",
                    'phone' => "123345123",
                ],
            ]);
    }
}
