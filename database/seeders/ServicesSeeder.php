<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesSeeder extends Seeder
{

    public function run()
    {
        DB::table('services')->insert(
            [
                [
                    'name' => 'Tours',
                    'nicname' => 'tours',
                    'icon' => '<i class="icon-copy fa fa-briefcase" aria-hidden="true"></i>',
                    'status' => 'Active',
                ],
                [
                    'name' => 'Hotels',
                    'nicname' => 'hotels',
                    'icon' => '<i class="icon-copy dw dw-hotel" aria-hidden="true"></i>',
                    'status' => 'Active',
                ],
                [
                    'name' => 'Activities',
                    'nicname' => 'activities',
                    'icon' => '<i class="icon-copy fa fa-child" aria-hidden="true"></i>',
                    'status' => 'Active',
                ],
                [
                    'name' => 'Transports',
                    'nicname' => 'transports',
                    'icon' => '<i class="icon-copy ion-android-car"></i>',
                    'status' => 'Active',
                ],
                [
                    'name' => 'Weddings',
                    'nicname' => 'weddings',
                    'icon' => '<i class="icon-copy fi-torso-business"></i><i class="icon-copy fi-torso-female"></i>',
                    'status' => 'Active',
                ],
            ]);
    }
}
