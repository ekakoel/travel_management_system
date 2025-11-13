<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingCodeSeeder extends Seeder
{
    public function run()
    {
        DB::table('booking_codes')->insert(
            [
                [
                    'name'=>'Test Code',
                    'code'=>'TESTCODE',
                    'discounts'=>5,
                    'amount'=>3,
                    'used'=>0,
                    'author'=> 1,
                    'expired_date' => '2024-01-01',
                    'status' => 'Active',
                ],
            ]);
    }
}
