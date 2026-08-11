# Database Rules and Data Model

Status: active
Updated: 2026-08-11

Dokumen ini adalah pintu masuk kanonik untuk pekerjaan yang menyentuh database project `balikamitour`. Project memakai database aktif; keselamatan data lebih penting daripada kecepatan perubahan schema.

## Aturan Mutlak

- Baca `docs/testing.md` sebelum test, migration, seeder, tinker, atau command database.
- Dilarang menjalankan `migrate:fresh`, `migrate:refresh`, `migrate:reset`, `db:wipe`, `DROP`, `TRUNCATE`, mass `DELETE`, atau command lain yang dapat menghapus data tanpa kebutuhan dan persetujuan eksplisit.
- Jangan mengubah migration lama yang mungkin sudah dijalankan.
- Perubahan schema memakai migration baru dengan `up()` dan `down()` yang aman untuk data existing.
- Lakukan pemeriksaan read-only sebelum merancang perubahan schema atau perbaikan data.
- Jangan mengubah `.env` untuk mengarahkan test atau command ke database lain tanpa instruksi eksplisit.

## Domain Data Utama

- Service publik: accommodations/hotels, transports, tour packages, dan activities.
- Transaksi bersama: orders, reservations, invoices, payment confirmations, dan history.
- Schema canonical `reservations` tidak memiliki kolom `send`. Untuk Tour
  Package, state pengiriman confirmation harus dibaca dari audit
  `order_logs` (`Send Confirmation`/`Resend Confirmation`), bukan ditambahkan
  ke lifecycle reservation. Pemakaian `reservations.send` pada layanan legacy
  hanya compatibility read/write bila deployment terkait memang memiliki
  kolom tersebut dan tidak boleh dipakai oleh implementasi Tour baru.
- `orders.status = Paid` adalah akhir komersial yang sukses. Fulfillment empat
  public service tidak menulis `Completed` ke kolom tersebut.
- `orders.completed_at` adalah marker resmi fulfillment:
  `null` berarti belum selesai dan non-null berarti selesai.
- `orders.completed_by` merekam user/admin untuk manual completion; automated
  completion harus tetap memiliki audit trail yang dapat dibedakan.
- Reservation dapat memakai `Completed` sebagai status operasional tanpa
  mengubah `orders.status` dari `Paid`.
- Operasional internal dan service publik dapat memakai tabel/flow yang berdekatan, tetapi Service Transports berbeda dari Transport Management/SPK.
- Detail struktur dan kepemilikan area aplikasi berada di `docs/architecture.md`.
- Status lintas tabel berada di `docs/status-contract.md`.

## Perubahan Schema

1. Telusuri model, relasi, query, validation, service, controller, Blade, test, dan penggunaan kolom aktual.
2. Periksa data existing secara read-only, termasuk nilai null, duplikat, orphan relation, dan nilai status legacy.
3. Buat migration baru yang backward-compatible.
4. Hindari default atau constraint yang mengubah arti data lama tanpa mapping eksplisit.
5. Tambahkan rollback yang aman.
6. Tambahkan test terhadap perilaku nyata memakai database testing terpisah.
7. Jangan menjalankan migration production; laporkan command manual yang perlu dijalankan user.

## Transaksi dan Integritas

- Gunakan database transaction untuk write multi-table yang harus berhasil atau gagal sebagai satu unit.
- Harga dan total transaksi harus dihitung dari sumber server-side; nilai request/frontend bukan sumber kebenaran.
- Mutation wajib menjaga ownership, authorization, mass-assignment protection, dan status transition.
- Duplicate submission harus ditangani dengan idempotency atau guard unik yang sesuai domain.
- File finansial dan record database terkait harus dikelola sebagai satu lifecycle yang konsisten.

## Invoice dan Reservation

- Relasi canonical invoice adalah `orders.rsv_id -> reservations.id ->
  invoice_admins.rsv_id`. Kolom legacy `reservations.inv_id` bukan sumber
  kebenaran relasi invoice.
- Satu reservation hanya boleh memiliki satu record `invoice_admins`. Aturan
  ini dijamin oleh unique index `invoice_admins_rsv_id_unique` setelah
  migration integritas dijalankan.
- Foreign key canonical adalah `invoice_admins.rsv_id -> reservations.id`
  dengan constraint `invoice_admins_rsv_id_foreign`. Child column wajib
  `BIGINT UNSIGNED NOT NULL`, mengikuti tipe `reservations.id`.
- Foreign key invoice memakai `ON DELETE RESTRICT` dan `ON UPDATE RESTRICT`.
  Reservation yang sudah memiliki invoice adalah record finansial dan tidak
  boleh menghapus invoice secara cascade.
- Sebelum memasang atau memulihkan constraint, audit wajib memastikan tidak
  ada `rsv_id` duplikat, null, negatif, atau orphan. Migration harus gagal
  tertutup bila kondisi data berubah setelah audit. Rollback tipe juga wajib
  menolak narrowing bila terdapat nilai di luar rentang signed `INT`.
- Payment confirmation tetap menunjuk invoice melalui
  `payment_confirmations.inv_id -> invoice_admins.id`.

## Tour Package Price Markup

- `tour_prices.markup_type` adalah discriminator canonical yang nullable untuk
  kompatibilitas data lama: `percentage`, `usd`, atau `idr`.
- `markup_amount` adalah numeric-string tervalidasi `VARCHAR(32)` agar database
  tidak menambahkan fixed decimal padding. CRUD menyimpan representasi numerik
  minimal: `20.00 -> 20`, `20.50 -> 20.5`, dan `20.25 -> 20.25`.
  Percentage memakai contract rate IDR sebagai basis, USD menyimpan major unit
  maksimal dua desimal, dan IDR menyimpan rupiah bulat.
- Seluruh perhitungan mengubah numeric-string tersebut ke fixed-scale integer;
  tidak ada aritmetika finansial berbasis float atau string concatenation.
- Migration hanya melakukan mapping deterministik `USD -> usd` dan `IDR -> idr`
  dari `markup_currency`. Nilai legacy lain tidak ditebak atau diaktifkan.
- Kolom status dan verification lama tetap tersedia sebagai metadata internal;
  operator CRUD tidak mengirim atau mengendalikannya.

## Referensi Rinci

- `docs/testing.md`
- `docs/security-rules.md`
- `docs/status-contract.md`
- `docs/modules/accommodation.md`
- `docs/modules/transport.md`
- `docs/modules/tour-package.md`
- `docs/modules/activity.md`
- `docs/decisions/accommodation-pricing-contract.md`
- `docs/decisions/accommodation-status-contract.md` (`superseded`)
- `docs/decisions/shared-order-status-audit.md` (`historical`)
