<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoomFacilitiesSeeder extends Seeder
{

    public function run()
    {
        DB::table('room_facilities')->insert(
            [
                [
                    'rooms_id' => 1,
                    'wifi' => 1,
                    'single_bed'=> 1,
                    'double_bed' => 0,
                    'extra_bed' => 0,
                    'air_conditioning' => 1,
                    'pool'=>1,
                    'tv_channel'=>1,
                    'water_heater'=>1,
                    'bathtub'=>1,
                ],
                [
                    'rooms_id' => 2,
                    'wifi' => 1,
                    'single_bed'=> 1,
                    'double_bed' => 0,
                    'extra_bed' => 0,
                    'air_conditioning' => 1,
                    'pool'=>0,
                    'tv_channel'=>1,
                    'water_heater'=>1,
                    'bathtub'=>1,
                ],
                [
                    'rooms_id' => 3,
                    'wifi' => 1,
                    'single_bed'=> 1,
                    'double_bed' => 0,
                    'extra_bed' => 0,
                    'air_conditioning' => 1,
                    'pool'=>0,
                    'tv_channel'=>1,
                    'water_heater'=>1,
                    'bathtub'=>0,
                ],
                [
                    'rooms_id' => 4,
                    'wifi' => 1,
                    'single_bed'=> 1,
                    'double_bed' => 0,
                    'extra_bed' => 0,
                    'air_conditioning' => 1,
                    'pool'=>0,
                    'tv_channel'=>1,
                    'water_heater'=>1,
                    'bathtub'=>1,
                ],
                [
                    'rooms_id' => 5,
                    'wifi' => 1,
                    'single_bed'=> 1,
                    'double_bed' => 0,
                    'extra_bed' => 0,
                    'air_conditioning' => 1,
                    'pool'=>1,
                    'tv_channel'=>1,
                    'water_heater'=>0,
                    'bathtub'=>1,
                ]

                
            ]);
    }
}
