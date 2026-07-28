# Security Rules

Status: active
Updated: 2026-07-28

Dokumen ini adalah aturan keamanan kanonik untuk perubahan route, controller, request, service, model, file, Blade, API, dan proses internal.

## Authorization dan Ownership

- Middleware adalah lapisan awal, bukan pengganti authorization backend.
- Setiap read atau mutation berbasis ID wajib memverifikasi actor, ownership, service type, dan status record.
- Guard yang hanya ada di Blade tidak cukup; controller/service harus menolak request yang tidak berhak.
- Jangan mempercayai `user_id`, `author`, `handled_by`, nominal pembayaran, status, atau service type dari request jika nilainya dapat diturunkan dari user login atau record server.
- Gunakan route model binding atau query ter-scope bila sesuai, tanpa menghilangkan service/ownership guard.
- Evaluasi guest, authenticated user, unapproved user, staff/admin, driver/operator, dan role/position relevan.

## Route dan Middleware

- Audit route canonical dan alias sebelum mengubah URL atau redirect.
- Pertahankan middleware penting seperti `auth`, `verified`, `approve`, `profile.complete`, `checkPosition`, dan `apikey`.
- Authorization untuk aksi penting harus dapat diuji sebagai perilaku HTTP, termasuk request milik user lain dan request dengan ID/service yang dimanipulasi.

## Input, Output, dan File

- Validasi input di backend; gunakan Form Request untuk validasi kompleks atau reusable.
- Terapkan allowlist MIME/extension, batas ukuran, nama file aman, dan storage non-public untuk dokumen sensitif.
- Jangan membuat URL publik yang dapat ditebak untuk receipt, invoice, atau file finansial.
- Escape copy/note dari user pada output. Jangan memakai raw Blade output kecuali konten telah disanitasi dan memang mengizinkan HTML.
- Pertahankan CSRF, mass-assignment protection, safe redirect, dan query parameter allowlist.

## Transaksi dan Status

- Mutation multi-table penting wajib transactional.
- Validasi status transition di backend berdasarkan status tersimpan, bukan hidden input.
- Upload, penggantian, dan penghapusan file harus konsisten dengan record database jika operasi gagal.
- Cegah duplicate submission sesuai `docs/decisions/form-submit-standard.md`.
- Ikuti kontrak lifecycle di `docs/status-contract.md`.

## API dan Secret

- Jangan menaruh credential, token, API key, atau secret di source, dokumentasi, log, atau output test.
- Endpoint WhatsApp tetap dilindungi `X-API-KEY`; detail flow ada di `docs/decisions/spk-whatsapp-public-report.md`.
- Jangan mengubah `.env` atau konfigurasi production tanpa instruksi eksplisit.

## Verifikasi Minimum

- Test akses actor yang berhak.
- Test actor yang tidak berhak dan ownership silang.
- Test ID/service/status yang dimanipulasi.
- Test file invalid, duplicate submission, dan rollback bila relevan.
- Jalankan test hanya setelah guard database di `docs/testing.md` terverifikasi.

## Referensi Audit

- `docs/decisions/accommodation-authorization-idor-audit.md`
- `docs/decisions/accommodation-status-lifecycle-audit.md`
- `docs/decisions/shared-order-status-audit.md`

