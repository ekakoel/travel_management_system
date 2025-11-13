<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PartnersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('partners')->where('email', 'partner1@gmail.com')->delete();

        DB::table('partners')->insert(
            [
                [
                    'name' => 'Cahaya SPA',
                    'address' => 'Jl.baru no.1A, Pedungan',
                    'location' => 'Nusa Dua',
                    'map'=>'https://goo.gl/maps/ToLecKRR9jnUWq2z7',
                    'cover'=>'images/partner/partner1.jpg',
                    'type' => 'SPA',
                    'status'=> 'Active',
                    'phone' => '88888888888',
                    'contact_person' => 'Raditya',
                ],
                [
                    'name' => 'Sinar Tour',
                    'address' => 'Jl.baru no.2A, Pedungan',
                    'location' => 'Denpasar',
                    'map'=>'https://goo.gl/maps/ToLecKRR9jnUWq2z7',
                    'cover'=>'images/partner/partner1.jpg',
                    'type' => 'Travel Agent',
                    'status'=> 'Active',
                    'phone' => '88888888888',
                    'contact_person' => 'Melky',
                ],
                [
                    'name' => 'Wahana Jaya',
                    'address' => 'Jl.baru no.3A, Pedungan',
                    'location' => 'Gianyar',
                    'map'=>'https://goo.gl/maps/ToLecKRR9jnUWq2z7',
                    'cover'=>'images/partner/partner1.jpg',
                    'type' => 'Transport',
                    'status'=> 'Active',
                    'phone' => '88888888888',
                    'contact_person' => 'Rony',
                ],
                [
                    'name' => 'Bahari Star',
                    'address' => 'Jl.baru no.4A, Pedungan',
                    'location' => 'Nusa Dua',
                    'map'=>'https://goo.gl/maps/ToLecKRR9jnUWq2z7',
                    'cover'=>'images/partner/partner1.jpg',
                    'type' => 'Travel Agent',
                    'status'=> 'Active',
                    'phone' => '88888888888',
                    'contact_person' => 'Mitha',
                ],
            ]
        );
    }
}
