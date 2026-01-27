<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->where('email', 'admin@gmail.com')->delete();

        DB::table('users')->insert(
            [
                [
                    'name' => 'Admin Bali Kami',
                    'username' => 'admin1',
                    'email' => config('app.reservation_mail'),
                    'position' => "developer",
                    'password' => bcrypt('B4l1k4m12022'),
                    'type' => 'admin',
                    'phone' => '081222154254',
                    'office' => 'BLK',
                    'address' => 'Denpasar',
                    'country' => 'Indonesia',
                    'email_verified_at' => '2023-06-01 12:00:00',
                    'status' => 'Active',
                ],
                [
                    'name' => 'Anton',
                    'username' => 'anton',
                    'email' => 'anton@gmail.com',
                    'position' => 'agent',
                    'password' => bcrypt('123456'),
                    'type' => 'admin',
                    'phone' => '081222154254',
                    'office' => 'BLK',
                    'address' => 'Denpasar',
                    'country' => 'Indonesia',
                    'email_verified_at' => '2023-06-01 12:00:00',
                    'status' => 'Active',
                ],
           
        ]);
    }
}
