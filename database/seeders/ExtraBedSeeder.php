<?php


namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExtraBedSeeder extends Seeder
{
    
    public function run()
    {
        DB::table('extra_beds')->insert(
            [
                [
                    'name'=>"Extra Bed",
                    'hotels_id'=>1,
                    'type'=>'Adult',
                    'max_age'=>'11',
                    'min_age' => "",
                    'description' => "Include breakfast",
                    'contract_rate' => "900000",
                    'markup' => "5",
                ],
                [
                    'name'=>"Extra Bed",
                    'hotels_id'=>1,
                    'type'=>'Child',
                    'max_age'=>'11',
                    'min_age' => "",
                    'description' => "Include breakfast",
                    'contract_rate' => "400000",
                    'markup' => "5",
                ],
                [
                    'name'=>"Extra Bed",
                    'hotels_id'=>1,
                    'type'=>'Adult',
                    'max_age'=>'11',
                    'min_age' => "",
                    'description' => "Include breakfast",
                    'contract_rate' => "900000",
                    'markup' => "5",
                ],
                [
                    'name'=>"Extra Bed",
                    'hotels_id'=>1,
                    'type'=>'Child',
                    'max_age'=>'11',
                    'min_age' => "",
                    'description' => "Include breakfast",
                    'contract_rate' => "400000",
                    'markup' => "5",
                ],
                [
                    'name'=>"Extra Bed",
                    'hotels_id'=>1,
                    'type'=>'Adult',
                    'max_age'=>'11',
                    'min_age' => "",
                    'description' => "Include breakfast",
                    'contract_rate' => "900000",
                    'markup' => "5",
                ],
                [
                    'name'=>"Extra Bed",
                    'hotels_id'=>1,
                    'type'=>'Child',
                    'max_age'=>'11',
                    'min_age' => "",
                    'description' => "Include breakfast",
                    'contract_rate' => "400000",
                    'markup' => "5",
                ],
            ]);
    }
}
