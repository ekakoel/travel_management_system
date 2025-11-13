<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransportsSeeder extends Seeder
{
    public function run()
    {
        DB::table('transports')->insert(
            [
                [
                    'name' => 'HiAce',
                    'code' => Str::random(26),
                    'type'=>'Micro Bus',
                    'brand'=>'Toyota',
                    'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    'include' => '- Mobil, Driver, BBM',
                    'additional_info' => 'Minimum 2 Pax, valid for domestic guest only',
                    'capacity' => '14',
                    'status' => 'Active',
                    'author_id'=> 1,
                    'cover' => 'Hiace.png',
                ],
                [
                    'name' => 'Avanza',
                    'code' => Str::random(26),
                    'type'=>'Car',
                    'brand'=>'Toyota',
                    'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    'include' => '- Mobil, Driver, BBM',
                    'additional_info' => 'Minimum 2 Pax, valid for domestic guest only',
                    'capacity' => '4',
                    'status' => 'Active',
                    'author_id'=> 1,
                    'cover' => 'Avanza.png',
                ],
                [
                    'name' => 'Veloz',
                    'code' => Str::random(26),
                    'type'=>'Car',
                    'brand'=>'Toyota',
                    'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    'include' => '- Mobil, Driver, BBM',
                    'additional_info' => 'Minimum 2 Pax, valid for domestic guest only',
                    'capacity' => '4',
                    'author_id'=> 1,
                    'status' => 'Active',
                    'cover' => 'Veloz.jpg',
                ],
                [
                    'name' => 'Bus',
                    'code' => Str::random(26),
                    'type'=>'Bus',
                    'brand'=>'Mercedes-benz',
                    'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    'include' => '- Mobil, Driver, BBM',
                    'additional_info' => 'Minimum 2 Pax, valid for domestic guest only',
                    'capacity' => '35',
                    'author_id'=> 1,
                    'status' => 'Active',
                    'cover' => 'Bus 2.png',
                ],
                [
                    'name' => 'Alphard',
                    'code' => Str::random(26),
                    'type'=>'Luxuri',
                    'brand'=>'Toyota',
                    'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    'include' => '- Mobil, Driver, BBM',
                    'additional_info' => 'Minimum 2 Pax, valid for domestic guest only',
                    'capacity' => '4',
                    'author_id'=> 1,
                    'status' => 'Active',
                    'cover' => 'Alphard.jpg',
                ],
                [
                    'name' => 'Elf',
                    'code' => Str::random(26),
                    'type'=>'Micro Bus',
                    'brand'=>'Isuzu',
                    'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    'include' => '- Mobil, Driver, BBM',
                    'additional_info' => 'Minimum 2 Pax, valid for domestic guest only',
                    'capacity' => '14',
                    'author_id'=> 1,
                    'status' => 'Active',
                    'cover' => 'Elf.jpg',
                ],
                [
                    'name' => 'Vellfire',
                    'code' => Str::random(26),
                    'type'=>'Luxuri',
                    'brand'=>'Toyota',
                    'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    'include' => '- Mobil, Driver, BBM',
                    'additional_info' => 'Minimum 2 Pax, valid for domestic guest only',
                    'capacity' => '4',
                    'author_id'=> 1,
                    'status' => 'Active',
                    'cover' => 'Vellfire.webp',
                ],
                
            ]
                );
    }
}
