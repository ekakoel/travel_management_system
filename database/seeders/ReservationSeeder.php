<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservationSeeder extends Seeder
{
    public function run()
    {
        DB::table('reservations')->insert(
            [
                [
                    'rsv_no' => "BKT230426A",
                    'inv_id' => 1,
                    'agn_id' => 1,
                    'adm_id' => 1,
                    'guide_id' => 1,
                    'driver_id' => 1,
                    'orders'=> '[1,2,3,4]',
                    'additional_info' => "",
                    'status' => "Active",
                ],
            ]);
    }
}
