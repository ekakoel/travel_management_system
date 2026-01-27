<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContractSeeder extends Seeder
{
    
    public function run()
    {
        DB::table('contracts')->insert(
            [
                [
                    'hotels_id'=> 1,
                    'name'=>'Contract Price',
                    'file_name'=>'Price',
                    'period_start'=>'2023-03-01',
                    'period_end'=>'2024-03-31',
                ],
                [
                    'hotels_id'=> 1,
                    'name'=>'Contract Signature',
                    'file_name'=>'Signature',
                    'period_start'=>'2023-03-01',
                    'period_end'=>'2024-03-31',
                ],
                [
                    'hotels_id'=> 1,
                    'name'=>'Contract Promo',
                    'file_name'=>'Promo',
                    'period_start'=>'2023-03-01',
                    'period_end'=>'2024-03-31',
                ],
            ]);
    }
}
