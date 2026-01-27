<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Tours;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToursSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ToursTableSeeder::class);
    }
}
