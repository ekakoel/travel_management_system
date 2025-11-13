<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tours;
use Faker\Factory as Faker;

class ToursTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 20; $i++) {
            Tours::create([
                'name' => $faker->sentence(3),
                'name_traditional' => '示例旅遊 ' . $i,
                'name_simplified' => '示例旅游 ' . $i,
                'slug' => $faker->unique()->slug(),
                'cover' => 'tours/default.jpg',
                'type_id' => rand(1, 3), // pastikan ID type ada di tabel 'types'
                'short_description' => $faker->text(120),
                'short_description_traditional' => '這是第 ' . $i . ' 個旅遊的簡短介紹。',
                'short_description_simplified' => '这是第 ' . $i . ' 个旅游的简短介绍。',
                'description' => $faker->paragraph(5),
                'description_traditional' => '這是一個隨機的旅遊描述。',
                'description_simplified' => '这是一个随机的旅游描述。',
                'duration_days' => rand(1, 3),
                'duration_nights' => rand(0, 2),
                'itinerary' => "Day 1: {$faker->sentence()}\nDay 2: {$faker->sentence()}",
                'itinerary_traditional' => "第1天: {$faker->sentence()}\n第2天: {$faker->sentence()}",
                'itinerary_simplified' => "第1天: {$faker->sentence()}\n第2天: {$faker->sentence()}",
                'include' => 'Transportation, Entrance Fees, Lunch',
                'include_traditional' => '交通、門票、午餐',
                'include_simplified' => '交通、门票、午餐',
                'exclude' => 'Personal expenses, Tips',
                'exclude_traditional' => '個人開銷、小費',
                'exclude_simplified' => '个人开销、小费',
                'additional_info' => 'Bring sunscreen and comfortable shoes.',
                'additional_info_traditional' => '帶上防曬乳和舒適的鞋子。',
                'additional_info_simplified' => '带上防晒乳和舒适的鞋子。',
                'cancellation_policy' => 'Free cancellation up to 24 hours before.',
                'cancellation_policy_traditional' => '出發前24小時可免費取消。',
                'cancellation_policy_simplified' => '出发前24小时可免费取消。',
                'status' => 'Active',
            ]);
        }
    }
}
