<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourTypeSeeder extends Seeder
{
    
    public function run()
    {
        DB::table('tour_types')->insert(
            [
                [
                    'type' => 'Group',
                ],
                [
                    'type' => 'Couple',
                ],
                [
                    'type' => 'Private',
                ]
            ]);
    }
}
