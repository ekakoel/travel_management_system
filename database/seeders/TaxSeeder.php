<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaxSeeder extends Seeder
{

    public function run()
    {
        DB::table('taxes')->insert(
            [
                [
                    'name' => "Tax",
                    'tax' => 1.5,
                ],
            ]);
    }
}
