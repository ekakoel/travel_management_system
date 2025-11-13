<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivitiesSeeder extends Seeder
{

    public function run()
    {
        DB::table('activities')->insert(
            [
                [
                    'name' => 'Paragliding',
                    'code' => Str::random(23),
                    'type'=>"Extream Sport",
                    'location' => 'Nusa Dua',
                    'map' => 'https://goo.gl/maps/oDNq1xEVnvCPghDM9',
                    'partners_id' => 1,
                    'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    'itinerary'=>'1. Meet and greet <br>
                    2. Penumpang pergi ke konter check-in untuk pendaftaran, mendapatkan nomor penerbangan dan pengukuran berat penumpang <br>
                    3. Awak darat memanggil penumpang untuk memakai sepatu olahraga, tali kekang, dan helm (Kami sediakan sepatu olahraga jika penumpang tidak memakai sepatu olahraga) <br>
                    4. Tandem master akan menjelaskan peralatan keselamatan dan prosedur keselamatan <br>
                    5. Lepas landas di situs Terbang RIUG atau situs Terbang Tebing Melasti <br>
                    6. Terbang sekitar 10 -15 menit, tergantung kondisi angin, dan penumpang bisa menikmati pantai berpasir putih, dan villa-villa <br>
                    7. Top Landing di lokasi Flying site atau pantai RIUG atau Tebing Melasti, tergantung kondisi angin.',
                    'duration'=> 4,
                    'include' => '1. Mineral water <br>
                    2. Internet Wi-Fi Hotspot, port listrik untuk pengisian daya dan hiburan musik <br>
                    3. Ruang Tunggu,toilet dan loker <br>
                    4. Pertolongan Pertama dan Pengobatan <br>
                    5. Asuransi (Pelanggan kami dilindungi oleh asuransi hingga Rp250.000.000 (kematian), Rp250.000.000 (cacat tetap), Rp25.000.000 (biaya pengobatan))<br>
                    6. Pilot Tandem Profesional dengan peringkat T2 (pengalaman lisensi tandem)<br>
                    7. Peralatan keamanan (menurut Standar Eropa (EN) yang kami sediakan bersertifikat glider, helm dan harness untuk tamu kami yang kompatibel dengan FAI (Federation Standar keselamatan Aeronautique Internationale))<br>
                    8. Sepatu olahraga untuk penumpang jika diperlukan <br>
                    9. Flight with Go Pro menyertakan kartu memori 8 GB <br>
                    10. Certificate Achievement of RIUG Paragliding or Melasti Cliff ( Sertifikat Prestasi Paralayang RIUG atau Tebing Melasti )',
                    'additional_info' => 'Minimum 2 Pax, valid for domestic guest only<br>
                    1. Glider kami dapat menampung hingga 210 kg. Rentang keamanan kami untuk penumpang terberat adalah 110 kg. <br>
                    2. Penumpang termuda adalah berusia 12 tahun dan tertua adalah 64 tahun tergantung pada mereka kesehatan, tinggi dan berat badan <br>
                    3. Musim terbaik untuk RIUG kegiatan ini adalah bulan April hingga November <br>
                    4. Tidak ada pengembalian uang jika penumpang',
                    'contract_rate' => rand(500000, 9000000),
                    'markup'=>5,
                    'qty' => 8,
                    'author_id'=> 1,
                    'min_pax'=> 10,
                    'cover' => '1.jpg',
                    'status' => 'Active',
                    'validity' => '2023-12-31',
                ],
                [
                    'name' => 'Snorkeling',
                    'code' => Str::random(23),
                    'location' => 'Amed',
                    'map' => 'https://goo.gl/maps/oDNq1xEVnvCPghDM9',
                    'partners_id' => 2,
                    'type'=>"Water Sport",
                    'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    'itinerary'=>'1. Meet and greet <br>
                    2. Penumpang pergi ke konter check-in untuk pendaftaran, mendapatkan nomor penerbangan dan pengukuran berat penumpang <br>
                    3. Awak darat memanggil penumpang untuk memakai sepatu olahraga, tali kekang, dan helm (Kami sediakan sepatu olahraga jika penumpang tidak memakai sepatu olahraga) <br>
                    4. Tandem master akan menjelaskan peralatan keselamatan dan prosedur keselamatan <br>
                    5. Lepas landas di situs Terbang RIUG atau situs Terbang Tebing Melasti <br>
                    6. Terbang sekitar 10 -15 menit, tergantung kondisi angin, dan penumpang bisa menikmati pantai berpasir putih, dan villa-villa <br>
                    7. Top Landing di lokasi Flying site atau pantai RIUG atau Tebing Melasti, tergantung kondisi angin.',
                    'duration'=> 4,
                    'include' => '1. Mineral water <br>
                    2. Internet Wi-Fi Hotspot, port listrik untuk pengisian daya dan hiburan musik <br>
                    3. Ruang Tunggu,toilet dan loker <br>
                    4. Pertolongan Pertama dan Pengobatan <br>
                    5. Asuransi (Pelanggan kami dilindungi oleh asuransi hingga Rp250.000.000 (kematian), Rp250.000.000 (cacat tetap), Rp25.000.000 (biaya pengobatan))<br>
                    6. Pilot Tandem Profesional dengan peringkat T2 (pengalaman lisensi tandem)<br>
                    7. Peralatan keamanan (menurut Standar Eropa (EN) yang kami sediakan bersertifikat glider, helm dan harness untuk tamu kami yang kompatibel dengan FAI (Federation Standar keselamatan Aeronautique Internationale))<br>
                    8. Sepatu olahraga untuk penumpang jika diperlukan <br>
                    9. Flight with Go Pro menyertakan kartu memori 8 GB <br>
                    10. Certificate Achievement of RIUG Paragliding or Melasti Cliff ( Sertifikat Prestasi Paralayang RIUG atau Tebing Melasti )',
                    'additional_info' => 'Minimum 2 Pax, valid for domestic guest only<br>
                    1. Glider kami dapat menampung hingga 210 kg. Rentang keamanan kami untuk penumpang terberat adalah 110 kg. <br>
                    2. Penumpang termuda adalah berusia 12 tahun dan tertua adalah 64 tahun tergantung pada mereka kesehatan, tinggi dan berat badan <br>
                    3. Musim terbaik untuk RIUG kegiatan ini adalah bulan April hingga November <br>
                    4. Tidak ada pengembalian uang jika penumpang',
                    'contract_rate' => rand(500000, 9000000),
                    'markup'=>5,
                    'qty' => 8,
                    'min_pax'=> 10,
                    'author_id'=> 1,
                    'cover' => '2.jpg',
                    'status' => 'Active',
                    'validity' => '2023-12-31',
                ],
                [
                    'name' => 'Rafting',
                    'code' => Str::random(23),
                    'location' => 'Ubud',
                    'map' => 'https://goo.gl/maps/oDNq1xEVnvCPghDM9',
                    'partners_id' => 3,
                    'type'=>"Adventure",
                    'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    'itinerary'=>'1. Meet and greet <br>
                    2. Penumpang pergi ke konter check-in untuk pendaftaran, mendapatkan nomor penerbangan dan pengukuran berat penumpang <br>
                    3. Awak darat memanggil penumpang untuk memakai sepatu olahraga, tali kekang, dan helm (Kami sediakan sepatu olahraga jika penumpang tidak memakai sepatu olahraga) <br>
                    4. Tandem master akan menjelaskan peralatan keselamatan dan prosedur keselamatan <br>
                    5. Lepas landas di situs Terbang RIUG atau situs Terbang Tebing Melasti <br>
                    6. Terbang sekitar 10 -15 menit, tergantung kondisi angin, dan penumpang bisa menikmati pantai berpasir putih, dan villa-villa <br>
                    7. Top Landing di lokasi Flying site atau pantai RIUG atau Tebing Melasti, tergantung kondisi angin.',
                    'duration'=> 4,
                    'include' => '1. Mineral water <br>
                    2. Internet Wi-Fi Hotspot, port listrik untuk pengisian daya dan hiburan musik <br>
                    3. Ruang Tunggu,toilet dan loker <br>
                    4. Pertolongan Pertama dan Pengobatan <br>
                    5. Asuransi (Pelanggan kami dilindungi oleh asuransi hingga Rp250.000.000 (kematian), Rp250.000.000 (cacat tetap), Rp25.000.000 (biaya pengobatan))<br>
                    6. Pilot Tandem Profesional dengan peringkat T2 (pengalaman lisensi tandem)<br>
                    7. Peralatan keamanan (menurut Standar Eropa (EN) yang kami sediakan bersertifikat glider, helm dan harness untuk tamu kami yang kompatibel dengan FAI (Federation Standar keselamatan Aeronautique Internationale))<br>
                    8. Sepatu olahraga untuk penumpang jika diperlukan <br>
                    9. Flight with Go Pro menyertakan kartu memori 8 GB <br>
                    10. Certificate Achievement of RIUG Paragliding or Melasti Cliff ( Sertifikat Prestasi Paralayang RIUG atau Tebing Melasti )',
                    'additional_info' => 'Minimum 2 Pax, valid for domestic guest only<br>
                    1. Glider kami dapat menampung hingga 210 kg. Rentang keamanan kami untuk penumpang terberat adalah 110 kg. <br>
                    2. Penumpang termuda adalah berusia 12 tahun dan tertua adalah 64 tahun tergantung pada mereka kesehatan, tinggi dan berat badan <br>
                    3. Musim terbaik untuk RIUG kegiatan ini adalah bulan April hingga November <br>
                    4. Tidak ada pengembalian uang jika penumpang',
                    'contract_rate' => rand(500000, 9000000),
                    'markup'=>5,
                    'qty' => 8,
                    'min_pax'=> 10,
                    'author_id'=> 1,
                    'cover' => '3.jpg',
                    'status' => 'Active',
                    'validity' => '2023-12-31',
                ],
                [
                    'name' => 'Hiking',
                    'code' => Str::random(23),
                    'location' => 'Kintamani',
                    'map' => 'https://goo.gl/maps/oDNq1xEVnvCPghDM9',
                    'partners_id' => 4,
                    'type'=>"Adventure",
                    'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    'itinerary'=>'1. Meet and greet <br>
                    2. Penumpang pergi ke konter check-in untuk pendaftaran, mendapatkan nomor penerbangan dan pengukuran berat penumpang <br>
                    3. Awak darat memanggil penumpang untuk memakai sepatu olahraga, tali kekang, dan helm (Kami sediakan sepatu olahraga jika penumpang tidak memakai sepatu olahraga) <br>
                    4. Tandem master akan menjelaskan peralatan keselamatan dan prosedur keselamatan <br>
                    5. Lepas landas di situs Terbang RIUG atau situs Terbang Tebing Melasti <br>
                    6. Terbang sekitar 10 -15 menit, tergantung kondisi angin, dan penumpang bisa menikmati pantai berpasir putih, dan villa-villa <br>
                    7. Top Landing di lokasi Flying site atau pantai RIUG atau Tebing Melasti, tergantung kondisi angin.',
                    'duration'=> 4,
                    'include' => '1. Mineral water <br>
                    2. Internet Wi-Fi Hotspot, port listrik untuk pengisian daya dan hiburan musik <br>
                    3. Ruang Tunggu,toilet dan loker <br>
                    4. Pertolongan Pertama dan Pengobatan <br>
                    5. Asuransi (Pelanggan kami dilindungi oleh asuransi hingga Rp250.000.000 (kematian), Rp250.000.000 (cacat tetap), Rp25.000.000 (biaya pengobatan))<br>
                    6. Pilot Tandem Profesional dengan peringkat T2 (pengalaman lisensi tandem)<br>
                    7. Peralatan keamanan (menurut Standar Eropa (EN) yang kami sediakan bersertifikat glider, helm dan harness untuk tamu kami yang kompatibel dengan FAI (Federation Standar keselamatan Aeronautique Internationale))<br>
                    8. Sepatu olahraga untuk penumpang jika diperlukan <br>
                    9. Flight with Go Pro menyertakan kartu memori 8 GB <br>
                    10. Certificate Achievement of RIUG Paragliding or Melasti Cliff ( Sertifikat Prestasi Paralayang RIUG atau Tebing Melasti )',
                    'additional_info' => 'Minimum 2 Pax, valid for domestic guest only<br>
                    1. Glider kami dapat menampung hingga 210 kg. Rentang keamanan kami untuk penumpang terberat adalah 110 kg. <br>
                    2. Penumpang termuda adalah berusia 12 tahun dan tertua adalah 64 tahun tergantung pada mereka kesehatan, tinggi dan berat badan <br>
                    3. Musim terbaik untuk RIUG kegiatan ini adalah bulan April hingga November <br>
                    4. Tidak ada pengembalian uang jika penumpang',
                    'contract_rate' => rand(500000, 9000000),
                    'markup'=>5,
                    'qty' => 8,
                    'min_pax'=> 10,
                    'author_id'=> 1,
                    'cover' => '4.jpg',
                    'status' => 'Active',
                    'validity' => '2023-12-31',
                ],
                [
                    'name' => 'Diving',
                    'code' => Str::random(23),
                    'location' => 'Tulamben',
                    'map' => 'https://goo.gl/maps/oDNq1xEVnvCPghDM9',
                    'partners_id' => 1,
                    'type'=>"Water Sport",
                    'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                    'itinerary'=>'1. Meet and greet <br>
                    2. Penumpang pergi ke konter check-in untuk pendaftaran, mendapatkan nomor penerbangan dan pengukuran berat penumpang <br>
                    3. Awak darat memanggil penumpang untuk memakai sepatu olahraga, tali kekang, dan helm (Kami sediakan sepatu olahraga jika penumpang tidak memakai sepatu olahraga) <br>
                    4. Tandem master akan menjelaskan peralatan keselamatan dan prosedur keselamatan <br>
                    5. Lepas landas di situs Terbang RIUG atau situs Terbang Tebing Melasti <br>
                    6. Terbang sekitar 10 -15 menit, tergantung kondisi angin, dan penumpang bisa menikmati pantai berpasir putih, dan villa-villa <br>
                    7. Top Landing di lokasi Flying site atau pantai RIUG atau Tebing Melasti, tergantung kondisi angin.',
                    'duration'=> 4,
                    'include' => '1. Mineral water <br>
                    2. Internet Wi-Fi Hotspot, port listrik untuk pengisian daya dan hiburan musik <br>
                    3. Ruang Tunggu,toilet dan loker <br>
                    4. Pertolongan Pertama dan Pengobatan <br>
                    5. Asuransi (Pelanggan kami dilindungi oleh asuransi hingga Rp250.000.000 (kematian), Rp250.000.000 (cacat tetap), Rp25.000.000 (biaya pengobatan))<br>
                    6. Pilot Tandem Profesional dengan peringkat T2 (pengalaman lisensi tandem)<br>
                    7. Peralatan keamanan (menurut Standar Eropa (EN) yang kami sediakan bersertifikat glider, helm dan harness untuk tamu kami yang kompatibel dengan FAI (Federation Standar keselamatan Aeronautique Internationale))<br>
                    8. Sepatu olahraga untuk penumpang jika diperlukan <br>
                    9. Flight with Go Pro menyertakan kartu memori 8 GB <br>
                    10. Certificate Achievement of RIUG Paragliding or Melasti Cliff ( Sertifikat Prestasi Paralayang RIUG atau Tebing Melasti )',
                    'additional_info' => 'Minimum 2 Pax, valid for domestic guest only<br>
                    1. Glider kami dapat menampung hingga 210 kg. Rentang keamanan kami untuk penumpang terberat adalah 110 kg. <br>
                    2. Penumpang termuda adalah berusia 12 tahun dan tertua adalah 64 tahun tergantung pada mereka kesehatan, tinggi dan berat badan <br>
                    3. Musim terbaik untuk RIUG kegiatan ini adalah bulan April hingga November <br>
                    4. Tidak ada pengembalian uang jika penumpang',
                    'contract_rate' => rand(500000, 9000000),
                    'markup'=>5,
                    'qty' => 8,
                    'min_pax'=> 10,
                    'author_id'=> 1,
                    'cover' => '5.jpg',
                    'status' => 'Active',
                    'validity' => '2023-12-31',
                ],
            ]
        );
    }
}
