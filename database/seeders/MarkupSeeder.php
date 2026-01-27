<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarkupSeeder extends Seeder
{

    public function run()
    {
        DB::table('markups')->insert(
            [
                [
                    "service"=>"Hotel",
                    "service_id"=>1,
                    "markup"=>10,
                ]
            ]
        );
    }
}
