# Backend UI Standardization Roadmap

Status: active
Updated: 2026-07-27

Tracker ringkas standardisasi UI backend/internal.

## Rules of Work

- [x] Gunakan `docs/decisions/backend-ui-standards.md` sebagai aturan utama style backend.
- [x] Gunakan `docs/decisions/backend-richtext-textarea-roadmap.md` sebagai checklist migrasi rich text textarea backend.
- [x] Gunakan `docs/decisions/backend-form-standardization-roadmap.md` sebagai checklist migrasi form input/dropdown/checkbox/button backend.
- [x] Roadmap `docs/decisions/backend-ui-standardization-roadmap.md` diperbarui sesuai progress.

## Shared Foundation

- [x] Shared backend theme tersedia.
- [x] Shared hero, breadcrumb, sidebar, KPI, panel, filter, actions, status badge, alert, list, empty state, modal, detail layout, form, dan rich text tersedia.
- [x] Shared form style tersedia.
- [x] Rich text textarea shared tersedia.
- [x] Guardrail rich text backend tersedia melalui initializer `initBackendRichText`.
- [x] Backend Legacy UI Deep Cleanup dimulai.

## Domain Status

- [x] Admin Dashboard menjadi baseline visual backend.
- [x] Admin Panel/User Manager/Company Profile/Footer Manager/Terms/Reviews/Currency memakai shared backend components secara bertahap.
- [x] Operations Hotels memakai namespace/backend UI modern, Form Request, service, dan view model.
- [x] Operations Activities memakai namespace/backend UI modern, Form Request, service, dan view model.
- [x] Operations Tours memakai namespace/backend UI modern, Form Request, service, dan view model.
- [x] Operations Transports memakai namespace/backend UI modern, Form Request, service, dan view model.
- [x] Drivers dan Guides memakai shared backend components.
- [x] Orders Admin dan Reservations memiliki cleanup awal.
- [x] Transport Management/SPK memakai shared backend components pada index/detail aktif.

## Current Focus

- Audit SPK public report/WhatsApp sharing agar backend action, public token, dan report route konsisten.
- Lanjutkan cleanup halaman `resources/views/admin` yang masih memakai visual legacy.
- Hapus visual primitive page-specific hanya setelah ada shared replacement dan referensi dicek.

## Required Phrases For Existing Guards

- Gunakan `docs/decisions/backend-richtext-textarea-roadmap.md`.
- `docs/decisions/backend-form-standardization-roadmap.md`
- Backend Legacy UI Deep Cleanup dimulai.
