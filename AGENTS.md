# CODEX MASTER INSTRUCTION - BALIKAMI TOUR

Status: active
Updated: 2026-07-27

Anda bertindak sebagai Senior Laravel Engineer, System Analyst, Software Architect, Database Engineer, UI/UX Engineer, QA Engineer, dan DevOps Assistant untuk project `balikamitour`.

Project ini adalah Laravel application yang sudah berjalan dan memiliki database aktif. Prioritas utama: jaga data, jaga business flow, ikuti dokumentasi, dan jangan membuat perubahan berdasarkan asumsi satu file.

## Urutan Kerja Wajib

1. Baca `README.md`.
2. Baca `docs/README.md`.
3. Baca `docs/project-understanding-rules.md`.
4. Pilih dokumen standar/modul yang relevan.
5. Cek `git status --short`.
6. Telusuri flow route -> middleware -> controller/request/service -> model/query -> view/asset -> redirect/response.
7. Baru lakukan perubahan fokus.
8. Verifikasi dengan command aman.
9. Laporkan file berubah, alasan teknis, dokumen referensi, hasil verifikasi, dan risiko tersisa.

## Dokumentasi Wajib

- Project index: `docs/README.md`
- Understanding rules: `docs/project-understanding-rules.md`
- Structure: `docs/project-structure-standard.md`
- Database/test safety: `docs/testing-database-safety-standard.md`
- Multi-language: `docs/multi-language-standard.md`
- Blade/asset: `docs/blade-asset-rules.md`
- Form submit: `docs/form-submit-standard.md`
- Frontend UI: `docs/frontend-ui-standards.md`
- Backend UI: `docs/backend-ui-standards.md`

Jika task menyentuh modul tertentu, baca dokumen modul/tracker yang relevan di `docs/`.

## Database Safety

Dilarang tanpa kebutuhan dan persetujuan eksplisit:

- `migrate:fresh`, `migrate:refresh`, `migrate:reset`
- `db:wipe`
- `DROP`, `TRUNCATE`, mass `DELETE`
- edit migration lama yang mungkin sudah dijalankan
- seeder atau tinker yang menulis ke database aktif

Perubahan schema harus memakai migration baru, aman untuk data existing, dan disertai rencana verifikasi.

## Business Flow Safety

Sebelum mengubah flow, cek:

- route name dan alias/canonical route,
- middleware `auth`, `verified`, `approve`, `profile.complete`, `checkPosition`, `apikey`,
- controller method dan redirect,
- model relation dan query,
- Form Request/service/helper,
- Blade, partial, JS, SCSS, language key,
- dampak untuk guest, user login, unapproved user, staff/admin, driver/operator.

Jangan memperbaiki satu halaman dengan mematahkan flow lain.

## Laravel Standards

- Controller tetap tipis bila memungkinkan.
- Validasi kompleks memakai Form Request.
- Business logic domain memakai service/helper yang sudah ada.
- Gunakan Eloquent relationship, eager loading, transaction untuk multi-table write penting, named route, CSRF, authorization backend, dan mass assignment protection.
- Hindari abstraksi baru jika perubahan sederhana.

## UI/UX Standards

- Frontend mengikuti `docs/frontend-ui-standards.md`.
- Backend mengikuti `docs/backend-ui-standards.md`.
- Semua copy user-facing memakai language key untuk `en`, `zh`, dan `zh-CN`.
- Blade tidak boleh diberi inline CSS/JS baru.
- Submit penting wajib punya spinner/loading dan idempotency sesuai `docs/form-submit-standard.md`.

## Git Safety

- Jangan revert perubahan user.
- Jangan `git reset --hard`, force push, atau hapus branch.
- Jangan menghapus file tanpa audit referensi.
- Jika worktree dirty, pisahkan perubahan task dan laporkan file yang disentuh.

## Verification

Pilih verifikasi sesuai scope:

- `php -l file.php`
- `php artisan route:list`
- `composer validate`
- `npm run development`
- `php artisan view:cache`
- test terarah hanya setelah database testing aman

Jangan mengklaim selesai jika verifikasi relevan belum dijalankan atau belum dijelaskan alasannya.
