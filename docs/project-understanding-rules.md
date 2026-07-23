# Project Understanding Rules

Dokumen ini adalah aturan wajib untuk setiap AI agent, developer, reviewer, atau pihak lain yang akan melakukan perubahan, penyesuaian, refactor, debugging, atau penambahan fitur pada project `balikamitour`.

## Status Wajib

Mulai sekarang tidak boleh ada perubahan kode yang dilakukan hanya berdasarkan asumsi lokal pada satu file.

Sebelum melanjutkan implementasi, setiap AI atau developer wajib memahami project ini secara menyeluruh terlebih dahulu, terutama:

1. flow bisnis utama
2. struktur route
3. group middleware dan proteksi akses
4. relasi antar controller, Blade, asset, helper, dan redirect
5. pola route frontend, backend, auth, approval, dan profile completeness
6. dampak perubahan terhadap flow lain yang terhubung
7. konfigurasi database dan testing bila pekerjaan membutuhkan test, migration, seeder, atau command database
8. cakupan multi-language bila perubahan menambah atau mengubah teks UI

Jika pemahaman menyeluruh itu belum dilakukan, maka pekerjaan dianggap belum siap untuk diimplementasikan.

## Tujuan

- Mencegah perubahan parsial yang merusak flow lain.
- Mencegah AI hanya membaca satu view atau satu controller lalu langsung mengubah perilaku sistem.
- Menjaga konsistensi route, redirect, middleware, dan authorization.
- Mengurangi regression pada flow login, profile, approval, booking, dashboard, dan frontend service pages.
- Memastikan perubahan dilakukan berdasarkan konteks sistem, bukan tebakan.

## Prinsip Inti

1. Jangan pernah menganggap file yang sedang dibuka adalah sumber kebenaran tunggal.
2. Setiap perubahan harus dipahami dalam konteks request flow dari route sampai response akhir.
3. Nama route lebih penting daripada menebak URL manual.
4. Middleware, route group, dan guard access harus dipahami sebelum mengubah flow.
5. Redirect, login return flow, approval flow, dan profile completeness flow harus diperiksa sebelum mengubah CTA, form submit, atau navigation.
6. Perubahan kecil pada Blade atau tombol dapat berdampak pada controller, auth flow, intended redirect, translation, dan route canonicalization.
7. Jika sebuah flow menyentuh frontend, backend, dan auth sekaligus, maka AI wajib membaca ketiga area tersebut sebelum mengedit.
8. Test dan command database tidak boleh dijalankan sebelum database target diverifikasi sebagai database testing atau database disposable.
9. Copy user-facing tidak boleh ditambahkan tanpa language key untuk semua locale aktif.

## Aturan Wajib Sebelum Mengubah Apa Pun

Sebelum menulis patch, AI atau developer wajib melakukan minimal langkah berikut:

1. Membaca `README.md`.
2. Membaca dokumen aturan yang relevan di `docs/`.
3. Mengidentifikasi route yang terlibat dari `routes/web.php` atau file route terkait.
4. Memeriksa middleware, route group, dan proteksi akses yang membungkus route tersebut.
5. Menemukan controller method yang menangani flow itu.
6. Menelusuri view, partial, helper, JavaScript, dan redirect yang dipanggil oleh flow itu.
7. Memeriksa apakah flow tersebut punya dependency pada:
   - `auth`
   - `verified`
   - `approve`
   - `profile.complete`
   - `checkPosition`
   - helper redirect atau canonical URL
   - session seperti `url.intended`, `previous_url`, `booking_dates`, atau state lain
8. Memastikan perubahan tidak menabrak route lain yang masih satu keluarga flow.
9. Memastikan tidak ada implementasi lama, route transisional, atau alias route yang masih dipakai oleh halaman lain.
10. Jika akan menjalankan test atau command database, baca `docs/testing-database-safety-standard.md` dan verifikasi database target lebih dulu.
11. Jika menambah atau mengubah teks UI, baca `docs/multi-language-standard.md` dan siapkan language key untuk semua locale aktif.
12. Baru setelah itu melakukan perubahan.

## Fokus Khusus Yang Wajib Dipahami

### 1. Route Project

Setiap AI wajib memahami bahwa project ini memiliki banyak route dengan pola campuran:

- route frontend public
- route frontend yang berubah perilakunya setelah login
- route auth
- route dengan redirect canonical
- route transisional atau alias
- route dalam middleware group bertingkat
- route backend/admin berdasarkan role dan position

Karena itu, sebelum mengubah satu halaman, AI wajib mencari:

1. route name yang dipakai
2. URL alias atau URL lama yang masih redirect ke route canonical
3. controller method tujuan
4. middleware group yang membungkus route
5. redirect lanjutan setelah action selesai

### 2. Middleware Group dan Access Rules

AI tidak boleh mengubah flow hanya dari tampilan view tanpa memahami lapisan akses.

Minimal harus dicek:

- apakah route public atau protected
- apakah route berada di dalam `auth`
- apakah route membutuhkan `verified`
- apakah route membutuhkan `approve`
- apakah route membutuhkan `profile.complete`
- apakah route memakai `checkPosition` atau middleware akses lain
- apakah user guest, user login belum approved, atau user role tertentu akan menerima perilaku berbeda

### 3. Redirect dan Flow Return

Sebelum mengubah tombol, CTA, submit form, login flow, atau language switch, AI wajib memeriksa:

1. apakah flow memakai `redirect()->guest(...)`
2. apakah flow memakai `url.intended`
3. apakah flow memakai canonical route helper
4. apakah flow setelah login harus kembali ke halaman asal
5. apakah URL final harus shareable, refresh-safe, dan language-switch-safe

### 4. Hubungan Blade, Controller, JS, dan Helper

AI tidak boleh menyimpulkan behavior hanya dari Blade.

Minimal harus dipahami:

1. dari mana data view berasal
2. apakah shaping data dilakukan di controller
3. apakah ada helper yang mengubah URL atau locale
4. apakah ada JS page-level yang mengubah interaksi
5. apakah ada partial reusable yang dipakai di beberapa halaman

## Checklist Wajib Sebelum Implementasi

Sebelum melakukan perubahan, jawab ya untuk semua pertanyaan ini:

1. Apakah route name yang benar sudah ditemukan?
2. Apakah semua alias atau redirect route terkait sudah dicek?
3. Apakah middleware group yang menaungi flow ini sudah dipahami?
4. Apakah controller method utama dan redirect lanjutannya sudah dibaca?
5. Apakah view utama dan partial terkait sudah dibaca?
6. Apakah helper, JS, dan session state yang mempengaruhi flow sudah dicek?
7. Apakah dampak pada user guest, user login, dan user yang belum approved sudah dipertimbangkan?
8. Apakah perubahan ini aman terhadap login return flow, submit flow, dan canonical URL?
9. Apakah ada dokumentasi project lain yang perlu diperbarui bersamaan?
10. Apakah perubahan dilakukan berdasarkan pemahaman sistem, bukan asumsi?
11. Jika test akan dijalankan, apakah database test sudah terisolasi dari database utama?
12. Jika teks UI berubah, apakah semua language key sudah tersedia untuk `en`, `zh`, dan `zh-CN`?

Jika salah satu jawabannya belum, maka jangan lanjut implementasi.

## Checklist Wajib Sesudah Implementasi

Setelah perubahan selesai, wajib dicek kembali:

1. route masih menuju controller yang benar
2. middleware behavior tidak berubah tanpa sengaja
3. redirect akhir masih benar
4. login flow dan guest flow tetap masuk akal
5. route canonical tetap konsisten
6. halaman tetap kompatibel dengan aturan asset separation
7. dokumentasi yang relevan ikut diperbarui bila standard atau flow berubah
8. tidak ada hardcoded user-facing text baru yang melewati language file
9. tidak ada test atau command database yang dijalankan pada database utama

## Dokumen Yang Wajib Dibaca Sebelum Perubahan

Minimal baca dokumen berikut lebih dulu:

1. `README.md`
2. `docs/project-understanding-rules.md`
3. `docs/blade-asset-rules.md`
4. `docs/multi-language-standard.md`
5. `docs/testing-database-safety-standard.md` jika akan menjalankan test, migration, seeder, tinker, atau command database
6. `docs/frontend-ui-standards.md` jika menyentuh frontend
7. `docs/backend-ui-standards.md` jika menyentuh backend
8. `docs/frontend-roadmap.md` jika menyentuh frontend flow, UI, copy, layout, atau interaction

## File Kode Yang Biasanya Wajib Ditelusuri

Tergantung task, AI biasanya wajib menelusuri area berikut:

1. `routes/web.php`
2. `app/Http/Controllers/...`
3. `app/Http/Middleware/...`
4. `app/Helpers/helpers.php`
5. `resources/views/...`
6. `public/css/...`
7. `public/frontend/js/...`
8. `resources/lang/...`
9. `.env`, `.env.testing`, dan `phpunit.xml` sebelum menjalankan test atau command database

## Larangan

AI atau developer tidak boleh:

1. langsung mengubah satu file hanya karena menemukan teks yang ingin diubah
2. mengubah URL hardcoded tanpa memeriksa route name dan alias flow
3. mengubah CTA tanpa memeriksa login state dan middleware flow
4. memindahkan logic tanpa memahami siapa saja yang memanggil flow tersebut
5. menyimpulkan bahwa perubahan aman hanya karena halaman tampak sederhana
6. menjalankan test atau command database saat database testing belum terisolasi
7. menambahkan hardcoded copy user-facing tanpa language key untuk semua locale aktif

## Aturan Review

Perubahan harus ditolak atau direvisi jika:

1. implementasi dibuat tanpa penelusuran route dan middleware terkait
2. perubahan flow login, redirect, booking, atau detail page dilakukan tanpa memeriksa controller dan route group
3. AI hanya membaca satu Blade atau satu controller lalu langsung mengubah behavior
4. perubahan menghasilkan flow yang benar di satu kondisi user tetapi rusak di kondisi user lain

## Status

Mulai sekarang dokumen ini adalah aturan default untuk seluruh perubahan pada repository ini.

Jika ada AI atau developer yang membaca file Markdown project ini sebelum bekerja, maka dokumen ini harus dianggap sebagai instruksi wajib, bukan rekomendasi opsional.
