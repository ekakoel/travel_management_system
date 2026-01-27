<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ActivityTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('activity_types')->insert(
            [
                [
                    'type' => 'Shopping Tour',
                ],
                [
                    'type' => 'Adventure',
                ],
                [
                    'type' => 'Culture',
                ],
                [
                    'type' => 'Ecotourism',
                ],
                [
                    'type' => 'Business Tourism',
                ],
                [
                    'type' => 'MICE',
                ],
                [
                    'type' => 'Culinary',
                ],
            ]);
    }
}
