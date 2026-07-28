# Database Rules and Data Model

Status: active
Updated: 2026-07-28

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

## Referensi Rinci

- `docs/testing.md`
- `docs/security-rules.md`
- `docs/status-contract.md`
- `docs/modules/accommodation.md`
- `docs/modules/transport.md`
- `docs/modules/tour-package.md`
- `docs/modules/activity.md`
- `docs/decisions/accommodation-pricing-contract.md`
- `docs/decisions/accommodation-status-contract.md`
- `docs/decisions/shared-order-status-audit.md`

