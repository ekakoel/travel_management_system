# Balikami Tour Documentation Index

Status: active
Updated: 2026-07-27

Dokumentasi ini adalah peta kerja resmi untuk agent dan developer. Gunakan dokumen di folder ini sebelum mengubah route, controller, model, view, asset, test, atau database.

## Wajib Dibaca Sebelum Bekerja

1. `AGENTS.md`
2. `README.md`
3. `docs/project-understanding-rules.md`
4. Dokumen standar atau modul yang sesuai dengan task
5. `docs/testing-database-safety-standard.md` sebelum test, migration, seeder, tinker, atau command database

## Standar Aktif

- `docs/project-understanding-rules.md` - aturan analisis route, middleware, controller, view, auth, redirect, dan dampak flow.
- `docs/project-structure-standard.md` - struktur target frontend, authenticated frontend, backend, asset, route, controller, dan language file.
- `docs/testing-database-safety-standard.md` - guard database dan test agar tidak menyentuh database aktif.
- `docs/multi-language-standard.md` - semua UI copy wajib memakai language key untuk `en`, `zh`, dan `zh-CN`.
- `docs/blade-asset-rules.md` - Blade tidak boleh berisi inline CSS/JS atau query.
- `docs/form-submit-standard.md` - standar submit idempotent, spinner, dan POST -> Redirect -> GET.
- `docs/frontend-ui-standards.md` - standar UI frontend publik dan area customer.
- `docs/backend-ui-standards.md` - standar UI backend/internal staff.
- `docs/frontend-order-modal-standard.md` - standar modal order service frontend.

## Modul Dan Flow Domain

- `docs/tour-package-map.md` - standar lokasi/map pada tour package.
- `docs/transport-order-number-standard.md` - standar nomor order transport.
- `docs/spk-whatsapp-public-report.md` - standar public SPK report token dan WhatsApp sharing.

## Roadmap Dan Tracker Ringkas

- `docs/roadmaps/service-booking-flow-audit-roadmap.md` - roadmap audit end-to-end service booking flow, dengan Accommodation sebagai prioritas pertama.
- `docs/project-blueprint-roadmap.md` - blueprint produk dan arah arsitektur.
- `docs/project-structure-migration-todo.md` - status migrasi struktur folder.
- `docs/frontend-roadmap.md` - status dan perubahan penting frontend.
- `docs/backend-ui-standardization-roadmap.md` - status standardisasi UI backend.
- `docs/backend-form-standardization-roadmap.md` - status form backend.
- `docs/backend-richtext-textarea-roadmap.md` - status rich text backend.
- `docs/backend-legacy-ui-deep-cleanup-roadmap.md` - status cleanup legacy UI backend.
- `docs/backend-legacy-ui-audit.md` - audit ringkas legacy UI.
- `docs/asset-architecture-blueprint.md` - blueprint asset.
- `docs/asset-migration-inventory.md` - inventory asset aktif dan legacy.
- `docs/frontend-roadmap-entry-template.md` - template entry perubahan frontend.

## Aturan Update Dokumentasi

- Jika perubahan mengubah workflow, database schema, permission, UI behavior utama, API, route, deployment, atau command operasional, update dokumen terkait pada commit yang sama.
- Jangan menambah dokumen panjang baru jika cukup memperbarui dokumen ringkas yang sudah ada.
- Roadmap adalah status dan arah, bukan aturan aktif kecuali dirujuk oleh dokumen standar.
- Dokumen vendor di `public/` bukan dokumentasi project dan tidak perlu diubah untuk task aplikasi.
