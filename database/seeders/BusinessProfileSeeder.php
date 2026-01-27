<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BusinessProfileSeeder extends Seeder
{

    public function run()
    {
        DB::table('business_profiles')->insert(
            [
                [
                    'name' => 'PT Bali Kami',
                    'nickname'=> 'Bali Kami Tour & Travel',
                    'type'=>'Travel Agent',
                    'address' => 'Denpasar - Bali',
                    'phone' => '(0361) 710661, 710663, 710664',
                    'logo'=>'bali-kami-tour-logo.png',
                    'caption'=>"Bali Kami Tour, Weddings, Photography",
                    'website'=>"www.balikamitour.com",
                    'map'=>"-",
                    'instagram' =>'-',
                    'facebook' => '-',
                    'twitter'=> '-',
                ],
            ]);
    }
}
