# Coding Standards and Project Understanding Rules

Status: active
Updated: 2026-07-28

Dokumen ini wajib dipakai sebelum debugging, refactor, penambahan fitur, atau perubahan UI di project `balikamitour`.

## Prinsip Utama

- Jangan mengubah kode dari satu file saja tanpa memahami flow lengkap.
- Telusuri request dari route -> middleware -> controller/request/service -> model/query -> view/asset -> redirect/response.
- Nama route lebih aman daripada URL hardcoded.
- Perubahan UI tetap wajib divalidasi backend, terutama permission dan ownership data.
- Jangan menjalankan test, migration, seeder, tinker, atau command database sebelum membaca `docs/testing.md`.
- Copy user-facing baru wajib memakai language key `en`, `zh`, dan `zh-CN`.

## Checklist Sebelum Edit

1. Baca `AGENTS.md`, `README.md`, dan indeks `docs/README.md`.
2. Pilih dokumen standar/modul yang relevan.
3. Cek `git status --short` dan jangan menimpa perubahan user.
4. Temukan route name, alias route, redirect canonical, dan middleware group.
5. Baca controller method, Form Request, service, model relation, helper, view, partial, JS, SCSS, dan language file terkait.
6. Identifikasi dampak untuk guest, authenticated user, unapproved user, staff/admin, dan role/position yang relevan.
7. Pastikan perubahan tidak mematahkan login return flow, profile completeness, approval, booking/order, dashboard, SPK, WhatsApp, atau language switch.
8. Tentukan verifikasi paling aman sebelum membuat klaim selesai.

## Area Sistem Yang Sering Terkait

- Public frontend: `FrontEndController`, `HomeController`, `ToursController`, `ActivitiesController`, `TransportsController`, `HotelsController`, `VillasController`.
- Authenticated frontend: `OrderController`, `ProfileController`, payment/order detail/edit flow.
- Backend/internal: admin dashboard, user/role/module, operations hotels/tours/activities/transports, reservations, orders, finance, reports.
- SPK/transport management: `TransportManagementController`, `SpksController`, `SpkReportController`, `SpkWhatsappController`, `WhatsAppController`, model `Spks`, `SpkDestinations`, `SpksCheckins`.
- API: `routes/api.php`, API V1 controllers, WhatsApp endpoint protected by `apikey`.

## Laravel Standards

- Controller tetap tipis bila memungkinkan.
- Validasi kompleks atau reusable memakai Form Request.
- Business logic domain memakai service/helper yang sudah ada.
- Gunakan Eloquent relationship dan eager loading untuk menghindari query berulang.
- Gunakan transaction untuk write multi-table penting.
- Gunakan named route, CSRF, authorization backend, dan mass-assignment protection.
- Jangan membuat abstraksi baru jika perubahan sederhana dapat mengikuti pola existing.
- Jangan mempercayai harga, total, actor, ownership, service type, atau status dari request.

## Blade, Asset, dan Copy

- Blade tidak boleh berisi query atau inline CSS/JS baru.
- Source asset aktif berada di `resources/frontend` atau `resources/backend`; jangan mengedit compiled output sebagai source utama jika source tersedia.
- Semua copy user-facing baru memakai language key untuk `en`, `zh`, dan `zh-CN`.
- Submit penting mengikuti `docs/decisions/form-submit-standard.md`.
- Frontend mengikuti `docs/frontend-standards.md`; backend mengikuti `docs/decisions/backend-ui-standards.md`.
- Detail aturan berada di `docs/decisions/blade-asset-rules.md` dan `docs/decisions/multi-language-standard.md`.

## Security, Database, dan Status

- Ikuti `docs/security-rules.md` untuk ownership, authorization, validation, dan file security.
- Ikuti `docs/database.md` dan `docs/testing.md` untuk schema, data aktif, dan test.
- Ikuti `docs/status-contract.md` sebelum mengubah lifecycle order, reservation, payment, invoice, fulfillment, atau history.

## Larangan

- Jangan menjalankan destructive database command.
- Jangan mengedit migration lama yang mungkin sudah pernah dijalankan.
- Jangan menambah inline CSS/JS di Blade.
- Jangan menambah hardcoded UI copy.
- Jangan menyimpulkan file tidak dipakai tanpa `rg` referensi, route, include, asset manifest, dan kemungkinan pemanggilan dinamis.
- Jangan menghapus atau memindahkan file yang sedang dimodifikasi user tanpa instruksi eksplisit.

## Laporan Selesai

Setiap laporan perubahan harus menyebutkan:

- ringkasan perubahan,
- file berubah,
- dokumen referensi,
- alasan teknis,
- verifikasi yang dijalankan dan hasilnya,
- risiko/batasan tersisa,
- langkah manual aman jika ada.
