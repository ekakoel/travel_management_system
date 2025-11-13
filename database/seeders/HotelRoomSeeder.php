<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HotelRoomSeeder extends Seeder
{
public function run()
    {
        DB::table('hotel_rooms')->insert(
            [
                [
                    'cover' => 'hotel_room_01.jpg',
                    'rooms' => 'Superior',
                    'hotels_id' => 1,
                    'capacity'=>2,
                   
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'status' => "Active",
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
                [
                    'cover' => 'hotel_room_02.jpg',
                    'room' => 'Delux',
                    'hotels_id' => 1,
                    'capacity'=>2,
                    'status' => "Active",
                    
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
                [
                    'cover' => 'hotel_room_03.jpg',
                    'room' => 'Suite',
                    'hotels_id' => 1,
                    'capacity'=>2,
                    'status' => "Active",
                   
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
                [
                    'cover' => 'hotel_room_01.jpg',
                    'room' => 'Royal',
                    'hotels_id' => 2,
                    'capacity'=>2,
                    'status' => "Active",
                    
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    
                ],
                [
                    'cover' => 'hotel_room_02.jpg',
                    'rooms' => 'Superior',
                    'hotels_id' => 2,
                    'capacity'=>2,
                    'status' => "Active",
                   
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
                [
                    'cover' => 'hotel_room_03.jpg',
                    'room' => 'Delux',
                    'hotels_id' => 2,
                    'capacity'=>2,
                    'status' => "Active",
                   
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
                [
                    'cover' => 'hotel_room_01.jpg',
                    'room' => 'Suite',
                    'hotels_id' => 3,
                    'capacity'=>2,
                    'status' => "Active",
                    
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
                [
                    'cover' => 'hotel_room_02.jpg',
                    'room' => 'Royal',
                    'hotels_id' => 3,
                    'capacity'=>2,
                    'status' => "Active",
                   
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],

                [
                    'cover' => 'hotel_room_03.jpg',
                    'rooms' => 'Superior',
                    'hotels_id' => 3,
                    'capacity'=>2,
                    'status' => "Active",
                    
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
                [
                    'cover' => 'hotel_room_01.jpg',
                    'room' => 'Delux',
                    'hotels_id' => 3,
                    'capacity'=>2,
                    'status' => "Active",
                   
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
                [
                    'cover' => 'hotel_room_02.jpg',
                    'room' => 'Suite',
                    'hotels_id' => 4,
                    'capacity'=>2,
                    'status' => "Active",
                   
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
                [
                    'cover' => 'hotel_room_03.jpg',
                    'room' => 'Royal',
                    'hotels_id' => 4,
                    'capacity'=>2,
                    'status' => "Active",
                   
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],

                [
                    'cover' => 'hotel_room_01.jpg',
                    'room' => 'Grand',
                    'hotels_id' => 4,
                    'capacity'=>2,
                    'status' => "Active",
                  
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
                [
                    'cover' => 'hotel_room_02.jpg',
                    'room' => 'Standar',
                    'hotels_id' => 4,
                    'capacity'=>2,
                    'status' => "Active",
                   
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
                [
                    'cover' => 'hotel_room_03.jpg',
                    'room' => 'Delux',
                    'hotels_id' => 5,
                    'capacity'=>2,
                    'status' => "Active",
                    
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
                [
                    'cover' => 'hotel_room_01.jpg',
                    'room' => 'Royal',
                    'hotels_id' => 5,
                    'capacity'=>2,
                    'status' => "Active",
                   
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
                [
                    'cover' => 'hotel_room_02.jpg',
                    'room' => 'Superior',
                    'hotels_id' => 5,
                    'capacity'=>2,
                    'status' => "Active",
                   
                    'include'=> 'Breakfirst, Coffee Time, 1 Hours SPA',
                    'additional_info' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                ],
            ]);
        }
}
