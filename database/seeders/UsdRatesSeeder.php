<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsdRatesSeeder extends Seeder
{

    public function run()
    {
        DB::table('usd_rates')->insert(
            [
                [
                    "name"=>"USD",
                    "rate"=>15500,
                ]
            ]
        );
    }
}
