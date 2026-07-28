# Testing And Database Safety Standard

Status: active
Updated: 2026-07-28

Project ini memiliki database aktif. Semua command yang bisa membaca berat, menulis, mengubah schema, atau menghapus data wajib diperlakukan hati-hati.

## Rule Mutlak

Jangan menjalankan test, migration, seeder, tinker, import SQL, atau command database tulis sebelum target database diverifikasi sebagai testing/disposable.

## Command Berisiko Tinggi

Butuh audit database dan persetujuan eksplisit jika berpotensi menyentuh database utama:

- `php artisan test`
- `vendor/bin/phpunit`
- `php artisan migrate`
- `php artisan migrate --seed`
- `php artisan migrate:fresh`
- `php artisan migrate:refresh`
- `php artisan migrate:reset`
- `php artisan db:wipe`
- `php artisan db:seed`
- `php artisan tinker`
- SQL `DROP`, `TRUNCATE`, `DELETE`, `ALTER`, atau `UPDATE` tanpa scope aman

## Verifikasi Wajib Sebelum Test

1. `APP_ENV=testing`.
2. `DB_DATABASE` test berbeda jelas dari database utama.
3. `.env.testing` atau `phpunit.xml` eksplisit mengarah ke database testing.
4. Jika memakai SQLite, pakai konfigurasi eksplisit `DB_CONNECTION=sqlite` dan `DB_DATABASE=:memory:`.
5. Jika memakai MySQL/MariaDB, nama database test harus berbeda, misalnya `online_bali_kami_26_testing`.

Catatan saat ini: `phpunit.xml` menetapkan `APP_ENV=testing`, tetapi DB testing belum eksplisit karena SQLite masih dikomentari. Audit dulu sebelum menjalankan test yang menyentuh database.

## Audit Read-Only Yang Boleh

- `php artisan route:list`
- `php artisan migrate:status`
- `php artisan config:show database`
- `SELECT DATABASE()`
- `SHOW TABLES`
- `SELECT COUNT(*) FROM table_name`

## Migration Baru

- Jangan edit migration lama.
- Buat migration baru untuk perubahan schema.
- Periksa data existing sebelum `NOT NULL`, unique, foreign key, enum, atau perubahan tipe.
- Siapkan backfill bila perlu.
- `down()` harus masuk akal dan tidak menghapus data penting tanpa alasan.
- Jelaskan tujuan, data terdampak, risiko, verifikasi, dan kebutuhan backup sebelum menjalankan migration.
