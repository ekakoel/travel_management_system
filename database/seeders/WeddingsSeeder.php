<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeddingsSeeder extends Seeder
{

    public function run()
    {
        DB::table('weddings')->insert(
            [
                [
                    'code'=> Str::random(26),
                    'pdf'=>'2020 Alila Manggis 2020 阿里拉曼吉斯婚禮配套 20191224.pdf',
                    'cover'=>'1676367735_Alila.png',
                    'name'=>'Alila Manggis Wedding Packages',
                    'hotel_name'=>'Alila Manggis',
                    'description'=>'<p>Beach Club, Pool Island atau Courtyard Area untuk ceremony dan dinner</p>',
                    'status'=>'Active',
                    'additional_info'=>'1. Final checking <br>
                    Sebelum acara dimulai, keluarga calon pengantin wanita dan keluarga calon pengantin pria dipastikan sudah berada di tempat acara akad berlangsung dan siap untuk memulai acara.<br>
                    2. Pembukaan<br>
                    MC membuka acara dengan mengucapkan salam dan jika dalam agama Islam mengucapkan bismillah dan surat Al-Fatihah. MC kemudian menyebutkan nama pihak keluarga atau yang memiliki hajat pernikahan serta nama kedua mempelai.<br>
                    3. Sambutan keluarga calon pengantin pria<br>
                    Acara sambutan perwakilan dari keluarga calon pengantin pria kepada keluarga calon pengantin wanita.<br>
                    4. Sambutan balasan<br>
                    Sambutan balasan dari keluarga calon pengantin wanita kepada calon pengantin pria yang diwakilkan oleh salah satu keluarga calon pengantin wanita.',
                ]
            ]
        );
    }
}
