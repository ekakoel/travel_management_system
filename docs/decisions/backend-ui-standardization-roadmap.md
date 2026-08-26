# Backend UI Standardization Roadmap

Status: active
Updated: 2026-08-21

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
- [x] Canonical backend date picker `data-backend-picker="date"` tersedia dengan request format `Y-m-d`.
- [x] Required marker backend otomatis mengikuti atribut HTML `required`
  melalui `initBackendRequiredMarkers` dan token `--backend-required`.
- [x] Monetary input backend memakai unit IDR/USD/% yang eksplisit melalui
  shared `initBackendMoneyInputs`, termasuk markup Tour yang dinamis.
- [x] Mutation form backend memakai shared `initBackendSubmitGuards`, spinner
  inline sejak fase click, associated-button lock, double-submit prevention,
  validation/cancel reset, history reset, serta hook reusable action non-form.
- [x] Canonical backend Create/Edit/Detail layout standard tersedia melalui
  `docs/decisions/backend-page-layout-standard.md`, termasuk two-column
  main/sidebar layout, right sidebar wajib, semantic sections, page actions,
  dan horizontal translation groups.
- [x] Spinner action distandarkan sebagai centered overlay yang mempertahankan
  ukuran control dan menyembunyikan konten lama; renderer spinner lokal pada
  Transport Management, Reviews, Footer Manager, dan legacy SPK dihapus.
- [x] Canonical backend modal memakai satu `<x-backend.modal-close>` di header
  dan shared Bootstrap 4/5 compatibility adapter `showBackendModal` /
  `closeBackendModal`; informational modal tidak menduplikasi Close di footer.
- [x] Backend Legacy UI Deep Cleanup dimulai.
- [x] Reusable backend status toggle tersedia melalui
  `data-backend-status-toggle`, shared AJAX PATCH handler, CSRF header,
  `setBackendActionLoading`, `backend-status-toggle` styling, optional paired
  `backend-status-badge` update, dan compact icon-only mode untuk status
  sederhana seperti `Active` / `Draft`.
- [x] Kolom action pada shared `backend-table` distandarkan rata kanan melalui
  `td[data-label="Action"]`, `td[data-label="Actions"]`,
  `backend-table-actions`, dan optional `backend-table-action-column` untuk
  header action baru.

## Domain Status

- [x] Admin Dashboard menjadi baseline visual backend.
- [x] Admin Panel/User Manager/Company Profile/Footer Manager/Terms/Reviews/Currency memakai shared backend components secara bertahap.
- [x] Operations Hotels memakai namespace/backend UI modern, Form Request, service, dan view model.
- [x] Hotel detail Promotion Price memakai reusable backend status toggle untuk
  update status `Active` / `Draft` via AJAX tanpa refresh halaman, dengan
  endpoint PATCH, transaction, authorization, validation, UserLog, dan toggle
  berlabel langsung di kolom Status tanpa badge status terpisah.
- [x] Hotel detail Package Price memakai reusable backend status toggle untuk
  update status `Active` / `Draft` via AJAX tanpa refresh halaman, dengan
  endpoint PATCH, transaction, authorization, validation, dan UserLog.
- [x] Operations Activities memakai namespace/backend UI modern, Form Request, service, dan view model.
- [x] Backend Create Activity menjadi reference implementation awal untuk
  canonical Create/Edit/Detail layout standard dengan main/sidebar layout,
  contextual Create sidebar, semantic sections, dan translation group
  horizontal.
- [x] Backend Edit Activity memakai canonical Create/Edit/Detail page layout,
  form grid shared, Edit-specific sidebar, translation group horizontal, cover
  preview compact, date picker canonical, monetary input shared, dan update
  flow tervalidasi tanpa hidden-field author.
- [x] Backend Detail Activity memakai canonical Create/Edit/Detail page layout,
  Detail-specific sidebar, read-only semantic sections sejajar Create/Edit,
  translation group horizontal, cover/media summary, dan pricing canonical dari
  ActivityDetailViewModel.
- [x] Operations Tours memakai namespace/backend UI modern, Form Request, service, dan view model.
- [x] Operations Transports memakai namespace/backend UI modern, Form Request, service, dan view model.
- [x] Drivers dan Guides memakai shared backend components.
- [x] Orders Admin dan Reservations memiliki cleanup awal.
- [x] Reservation Active work queue memiliki read-only operational calendar
  dengan month/week/list view, localized event summary, mobile list fallback,
  serta in-memory filter tanpa query tambahan.
- [x] Reservation detail memakai shared hero, breadcrumb, KPI,
  `x-backend.detail-layout`, operational context sidebar, quick action,
  localized section navigation, canonical modal close runtime, serta projection
  service terikat reservation tanpa full-table Orders/Users lookup.
- [x] Reservation detail runtime dipindahkan dari Blade legacy 2.800+ baris ke
  namespace `backend.operations.reservations.detail` dengan partial overview,
  manifest, linked service order, trip notes, dan context; order Deleted tidak
  diproyeksikan, detail order memakai route canonical, invoice creation
  idempotent/server-authoritative, serta tidak ada JSON pricing logic di Blade.
- [x] Invoice detail memakai shared hero, breadcrumb, KPI, panel,
  `x-backend.detail-layout`, context sidebar, canonical modal/form/button,
  localized EN/zh-CN/zh, projection service tanpa query/perhitungan di Blade,
  historical Tour pricing snapshot, serta mutation service transactional.
- [x] Invoice index memakai finance work queue terikat active billing window,
  shared hero/KPI/filter/table/card/status/action, projection service dengan
  constrained eager loading, shared payment-state resolver, dan tanpa modal
  invoice duplikatif atau query/perhitungan di Blade.
- [x] Left navbar mengelompokkan Orders, Reservations, dan Invoices dalam
  dropdown Operations yang localized, role-aware, mempertahankan pending badge,
  dan terbuka otomatis pada halaman list maupun detail.
- [x] Data notification header, pending badge Operations, service menu, dan
  promotion sidebar diproyeksikan oleh shared navigation view composer; Blade
  navigation tidak menjalankan query dan hasil dipakai ulang satu kali per request.
- [x] Transport Management/SPK memakai shared backend components pada index/detail aktif.

## Current Focus

- Audit SPK public report/WhatsApp sharing agar backend action, public token, dan report route konsisten.
- Lanjutkan cleanup halaman `resources/views/admin` yang masih memakai visual legacy.
- Hapus visual primitive page-specific hanya setelah ada shared replacement dan referensi dicek.

## Required Phrases For Existing Guards

- Gunakan `docs/decisions/backend-richtext-textarea-roadmap.md`.
- `docs/decisions/backend-form-standardization-roadmap.md`
- Backend Legacy UI Deep Cleanup dimulai.
