# Testing And Database Safety Standard

Dokumen ini menetapkan aturan wajib untuk menjalankan test, migration, seeder, tinker, command artisan, atau tool lain yang dapat membaca maupun mengubah database project `balikamitour`.

## Status Mutlak

Tidak boleh ada test atau command database yang dijalankan sebelum target database diverifikasi.

Database aplikasi utama tidak boleh dipakai sebagai database test. Jika environment test belum terisolasi, pekerjaan harus berhenti dan konfigurasi testing harus diperbaiki lebih dulu.

## Tujuan

- Mencegah kehilangan data akibat test atau migration yang berjalan pada database utama.
- Memastikan setiap developer dan AI agent dapat membedakan database local, testing, staging, dan production.
- Menjadikan validasi environment sebagai langkah wajib sebelum menjalankan command yang berpotensi menulis data.
- Menutup celah konfigurasi seperti `phpunit.xml` yang tidak mengarahkan test ke database testing khusus.

## Aturan Wajib Sebelum Menjalankan Test

Sebelum menjalankan `php artisan test`, `vendor/bin/phpunit`, atau command test lain, wajib memastikan:

1. `APP_ENV` saat test bernilai `testing`.
2. `DB_DATABASE` untuk test bukan database utama dari `.env`.
3. Project memiliki `.env.testing` atau konfigurasi `phpunit.xml` yang eksplisit mengarah ke database testing.
4. Jika memakai MySQL/MariaDB, nama database test wajib berbeda jelas, misalnya `online_bali_kami_26_testing`.
5. Jika memakai SQLite in-memory, konfigurasi wajib eksplisit:

```xml
<server name="DB_CONNECTION" value="sqlite"/>
<server name="DB_DATABASE" value=":memory:"/>
```

6. Jangan menjalankan test yang memakai `RefreshDatabase`, `DatabaseMigrations`, `migrate:fresh`, atau cleanup schema jika database test belum terisolasi.
7. Jika test membutuhkan data domain yang panjang, gunakan factory/fixture test khusus, bukan data produksi.
8. Jika ada keraguan database mana yang aktif, hentikan pekerjaan dan lakukan audit read-only terlebih dahulu.

## Command Yang Wajib Dianggap Berisiko Tinggi

Command berikut tidak boleh dijalankan tanpa verifikasi database dan persetujuan eksplisit bila mengarah ke database utama:

```text
php artisan migrate:fresh
php artisan migrate:refresh
php artisan migrate:reset
php artisan db:wipe
php artisan db:seed
php artisan test
vendor/bin/phpunit
php artisan tinker
php artisan migrate --seed
```

Command SQL manual seperti `DROP`, `TRUNCATE`, `DELETE`, `ALTER`, dan `UPDATE` tanpa scope ketat juga dianggap tindakan berisiko tinggi.

## Audit Read-Only Yang Diizinkan

Audit berikut boleh dilakukan untuk memahami kondisi tanpa mengubah data:

```text
php artisan migrate:status
php artisan route:list
php artisan config:show database
SELECT DATABASE()
SHOW TABLES
SELECT COUNT(*) FROM table_name
```

Jika tool CLI database tidak tersedia, gunakan cara read-only lain yang tidak membuat file migration, tidak menjalankan seeder, dan tidak menulis ke database.

## Standar Konfigurasi Testing

Project wajib memiliki isolasi testing sebelum test feature dijalankan.

Opsi yang direkomendasikan:

### SQLite In-Memory

Cocok untuk test unit/feature yang tidak membutuhkan behavior MySQL spesifik.

```xml
<server name="APP_ENV" value="testing"/>
<server name="DB_CONNECTION" value="sqlite"/>
<server name="DB_DATABASE" value=":memory:"/>
```

### MySQL Testing Database

Cocok jika migration atau query sangat bergantung pada MySQL/MariaDB.

```env
APP_ENV=testing
DB_CONNECTION=mysql
DB_DATABASE=online_bali_kami_26_testing
```

Database testing boleh di-reset oleh test. Database utama tidak boleh.

## Guard Runtime Yang Direkomendasikan

Tambahkan guard di `tests/TestCase.php` atau base testing helper untuk menghentikan test jika database aktif sama dengan database utama.

Contoh prinsip:

```php
if (app()->environment('testing') && config('database.connections.mysql.database') === env('DB_DATABASE')) {
    throw new RuntimeException('Testing database is not isolated.');
}
```

Implementasi final harus menyesuaikan struktur config project dan tidak boleh bergantung pada nilai yang ambigu.

## Aturan Untuk AI Agent Dan Developer

AI agent atau developer wajib:

1. Membaca `README.md`, `docs/project-understanding-rules.md`, dan dokumen ini sebelum menjalankan test atau command database.
2. Menyebutkan database target sebelum command berisiko dijalankan.
3. Tidak menjalankan test hanya untuk validasi UI bila test environment belum aman.
4. Mengutamakan `php -l`, `php artisan route:list`, `php artisan view:cache`, dan audit read-only sebagai verifikasi awal.
5. Meminta persetujuan eksplisit sebelum restore backup, import SQL, migration destructive, atau seeder besar.
6. Tidak memakai data produksi sebagai fixture test.
7. Mencatat perubahan konfigurasi testing di dokumentasi project.

## Recovery Standard

Jika data hilang atau database berubah tidak sengaja:

1. Hentikan semua command tulis.
2. Jangan menjalankan migration atau seeder lanjutan.
3. Identifikasi database aktif dari `.env`, `config`, dan server database.
4. Cari backup lokal/server tanpa restore langsung ke database aktif.
5. Restore backup ke database baru terlebih dahulu.
6. Validasi jumlah tabel, jumlah row penting, user admin, orders, reservations, services, dan modul domain lain.
7. Baru setelah validasi, putuskan apakah aplikasi diarahkan ke database restore atau dilakukan replace.

## Checklist Sebelum Menjalankan Test

Jawab `ya` untuk semua:

1. Apakah database test berbeda dari database utama?
2. Apakah `.env.testing` atau `phpunit.xml` sudah eksplisit?
3. Apakah test tidak memakai credential database produksi?
4. Apakah command yang akan dijalankan tidak melakukan reset pada database utama?
5. Apakah ada backup terbaru jika test menyentuh schema penting?
6. Apakah perubahan bisa diverifikasi dengan command non-database terlebih dahulu?

Jika salah satu jawaban belum `ya`, jangan jalankan test.

## Status

Dokumen ini adalah aturan wajib untuk semua pekerjaan berikutnya pada project `balikamitour`.
