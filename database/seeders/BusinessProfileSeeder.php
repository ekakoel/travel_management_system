<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessProfileSeeder extends Seeder
{

    public function run()
    {
        DB::table('business_profiles')->updateOrInsert(
            ['profile_key' => 'primary'],
            [
                'name' => 'PT Bali Kami',
                'nickname'=> 'Bali Kami Tour & Travel',
                'type'=>'B2B Travel Agent',
                'address' => 'Jl. Raya Sesetan Gg. Ikan Jangki 617e, Denpasar City, Bali 80222',
                'phone' => '+62 361 710661',
                'phone_2' => '+62 361 710663',
                'phone_3' => '+62 361 710664',
                'email' => 'e-admin@balikamitour.com',
                'whatsapp' => '+62 361 710661',
                'logo'=>'bali-kami-tour-logo.png',
                'logo_dark' => config('app.logo_img_white'),
                'caption'=>"Bali Kami Tour, Weddings, Photography",
                'public_tagline' => 'Bali based B2B travel partner for premium Indonesia travel services.',
                'public_description' => 'Bali Kami Tour supports professional travel agents with curated accommodations, executive transportation, and tailored journeys across Indonesia.',
                'website'=>"www.balikamitour.com",
                'map'=>'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3943.821050968989!2d115.22173570000001!3d-8.708537300000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd24112fd4d0b17%3A0x6c3c0731cda9e79!2sBali%20Kami%20Tour%20and%20Wedding!5e0!3m2!1sen!2sid!4v1745998344458!5m2!1sen!2sid',
                'instagram' =>'-',
                'facebook' => '-',
                'twitter'=> '-',
                'youtube' => 'https://www.youtube.com/@balikamichannel',
                'linkedin' => 'https://id.linkedin.com/company/bali-kami-group',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
