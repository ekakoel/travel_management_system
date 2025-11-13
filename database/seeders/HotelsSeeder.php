<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HotelsSeeder extends Seeder
{
    public function run()
    {
        DB::table('hotels')->insert(
            [
                [
                    'name' => 'SWISS BELHOTEL',
                    'code' => Str::random(26),
                    'region' => 'Suwung',
                    'phone'=>'0857777777777',
                    'contact_person'=>"Ms. A",
                   
                    'address' =>'Jl. By Pass Ngurah Rai, No639, Suwung, Denpasar, 80225',
                    'description' => 'The charm of this car. Choose CIMA. It is the pride and pleasure of choosing one vertex.',
                    
                    'facility' => 'Pool, SPA, Bar',
                    'additional_info' => 'EAT & DRINKS <br>All You Can Eat Buffet Breakfast, The Plantation Grill, Double-Six Rooftop Sunset Bar, Lagoon Bar',
                    'author_id' => 1,
                    'status'=> "Active",
                    'cover'=>"1.jpg",
                    'web' => "www.swissbelhotel.com",
                    'map'=> "https://goo.gl/maps/tBphtJD9p6GAt2EcA"
                ],
                [
                    'name' => 'The Ritz Carlton',
                    'code' => Str::random(26),
                    'region' => 'Nusa Dua',
                    'phone'=>'0857777777777',
                    'contact_person'=>"Ms. A",
                    
                    'address' =>'Jalan Raya Nusa Dua Selatan Lot III Sawangan, Nusa Dua Bali, 80363 Indonesia',
                    'description' => 'Every detail of The Ritz-Carlton, Bali experience is inspired by the beauty and allure of the ocean. Overlooking the beach in Nusa Dua, this luxury resort celebrates its Bali surroundings at every moment. Luxury resort villas feature private pools, lush gardens and breathtaking views.',
                    
                    'facility' => 'Pool, SPA, Bar',
                    'additional_info' => 'OVERLOOKING THE INDIAN OCEAN, THIS CLIFFSIDE RESORT IN NUSA DUA, BALI OFFERS MODERN LUXURY WOVEN WITH BALINESE TRADITION.',
                    'author_id' => 1,
                    'status'=> "Active",
                    'cover'=>"2.jpg",
                    'web' => "www.doublesix.com",
                    'map'=> "https://goo.gl/maps/tBphtJD9p6GAt2EcA"
                ],
                [
                    'name' => 'Hyatt Regency',
                    'code' => Str::random(26),
                    'region' => 'Sanur',
                    'phone'=>'0857777777777',
                    'contact_person'=>"Ms. A",
                    
                    'address' =>'Jl. By Pass Ngurah Rai, No639, Suwung, Denpasar, 80225',
                    'description' => 'The charm of this car. Choose CIMA. It is the pride and pleasure of choosing one vertex.',
                    
                    'facility' => 'Pool, SPA, Bar',
                    'additional_info' => '* Min 3N Stay : 1x Special Dinner & Choice of 3 activities for 2 during the stay',
                    'author_id' => 1,
                    'status'=> "Active",
                    'cover'=>"3.jpg",
                    'web' => "www.hyattregency.com",
                    'map'=> "https://goo.gl/maps/tBphtJD9p6GAt2EcA",
                ],
                [
                    'name' => 'Rits Carlton',
                    'code' => Str::random(26),
                    'region' => 'Nusa Dua',
                    'phone'=>'0857777777777',
                    'contact_person'=>"Ms. A",
                    
                    'address' =>'Jl. By Pass Ngurah Rai, No639, Suwung, Denpasar, 80225',
                    'description' => 'The charm of this car. Choose CIMA. It is the pride and pleasure of choosing one vertex.',
                    
                    'facility' => 'Pool, SPA, Bar',
                    'additional_info' => '* Min 3N Stay : 1x Special Dinner & Choice of 3 activities for 2 during the stay',
                    'author_id' => 1,
                    'status'=> "Active",
                    'cover'=>"4.jpg",
                    'web' => "www.ritscarlton.com",
                    'map'=> "https://goo.gl/maps/tBphtJD9p6GAt2EcA",
                ],
            ]
                ); 
    }
}
