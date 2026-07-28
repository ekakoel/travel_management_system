# Backend UI Standards

Status: active
Updated: 2026-07-27

Standar ini berlaku hanya untuk area backend/internal staff. Backend harus terasa operasional, padat, mudah dipindai, dan konsisten.

## Shared Style Governance

Satu pola UI backend hanya boleh punya satu sumber style shared. Tidak ada visual primitive baru di SCSS halaman untuk hero, KPI, panel, list, empty state, toolbar, filter, status badge, alert, modal, form control, table, atau button tanpa alasan dan dokumentasi.

## Source Of Truth

- Global backend SCSS: `resources/backend/scss/app.scss`.
- Shared components: `resources/backend/scss/components`.
- Backend JS shared/runtime: `resources/backend/js/app.js`.
- Baseline dashboard: `resources/views/backend/admin/dashboard/index.blade.php`.

## Hero Standard

- Hero memakai `backend-page-hero`.
- Primary action memakai `backend-page-primary-action` untuk setiap aksi utama di dalam hero.

## Breadcrumb Standard

- Breadcrumb backend memakai wrapper `backend-page-toolbar`.
- Root Admin Panel memakai `route('view.admin-panel-main')`.

## KPI Standard

- KPI memakai `backend-kpi-grid` dan `backend-kpi-card`.

## Panel and Section Header Standard

- Panel memakai `backend-panel`.
- Header memakai `backend-section-header`.

## Toolbar Filter Standard

- Filter memakai `backend-toolbar-filter` atau `backend-filter-panel`.

## Status Badge Standard

- Status memakai `backend-status-badge`.

## Alert Standard

- Feedback memakai `backend-feedback` dan `backend-alert`.

## List and Empty State Standard

- List memakai `backend-list` dan `backend-list-item`.
- Empty state memakai `backend-empty-state`.

## Modal Standard

- Modal memakai `backend-modal`, `backend-modal__header`, `backend-modal__body`, dan `backend-modal__footer`.

## Button Standard

- Button memakai `backend-button-primary`, `backend-button-secondary`, atau `backend-button-danger`.
- `Cancel`/close destructive-neutral modal action memakai tone danger.
- Hover/focus memakai token `--backend-button-hover-transform`, `--backend-button-hover-shadow`, dan `--backend-button-focus-ring`.

## Link Standard

- Semua link backend wajib tanpa underline.

## Form Label Standard

- Required marker memakai token `--backend-required`.

## Checkbox Standard

- Checkbox backend tampil sebagai switch/toggle shared kecuali ada alasan teknis.

## Form Control Standard

- Form style utama berada di `resources/backend/scss/components/_backend-form.scss`.

## Table Action Button Standard

- Action table memakai `backend-icon-action--view`, `backend-icon-action--edit`, dan `backend-icon-action--delete`.

## Rich Text Area Standard

- Rich text memakai `initBackendRichText`.
- Textarea rich memakai `data-backend-richtext="true"`.
- Textarea plain memakai `data-backend-richtext="false"`.

## Table Display Standard

- Backend memakai multi-display dan tidak boleh bergantung pada horizontal scroll sebagai UX utama.

## Detail Layout and Context Side Panel Standard

- Detail page memakai `x-backend.detail-layout` dan `backend-detail-side`.

## Komponen Wajib Ringkas

- Hero: `x-backend.page-hero`, `backend-page-hero`, `backend-page-primary-action`.
- Breadcrumb toolbar: `backend-page-toolbar`.
- KPI: `backend-kpi-grid`, `backend-kpi-card`.
- Panel/header: `backend-panel`, `backend-section-header`.
- List/empty: `backend-list`, `backend-list-item`, `backend-empty-state`.
- Filter: `backend-toolbar-filter`, `backend-filter-panel`, `backend-filter-field`, `backend-filter-control`, `backend-filter-actions`.
- Status: `backend-status-badge` dengan modifier tone/status.
- Alert: `backend-feedback`, `backend-alert`.
- Modal: `backend-modal`, `backend-modal__header`, `backend-modal__body`, `backend-modal__footer`.
- Form: `backend-form`, `backend-form-grid`, `backend-form-field`, `backend-form-label`, `backend-form-control`, `backend-form-actions`.
- Action table: `backend-icon-action`, `backend-icon-action--view|edit|delete`.
- Detail page: `x-backend.detail-layout`, `backend-detail-main`, `backend-detail-side`.

## Rules

- Jangan membuat ulang primitive visual di SCSS halaman.
- Class domain boleh untuk hook layout/JS, bukan mengganti style global.
- Backend baru/refactor harus responsive mobile, tablet, desktop, wide desktop.
- Tabel backend baru tidak boleh bergantung pada horizontal scroll sebagai UX utama.
- Semua link backend tanpa underline, tetap punya hover/focus yang jelas.
- Textarea backend otomatis rich text via `initBackendRichText`; gunakan `data-backend-richtext="true"` atau `false` bila plain text memang wajib.
- Tombol cancel/close yang menutup modal tanpa menyimpan memakai tone danger.
- Copy backend wajib mengikuti `docs/decisions/multi-language-standard.md`.

## Roadmap

Setiap standardisasi backend wajib memperbarui `docs/decisions/backend-ui-standardization-roadmap.md`. Jika menyentuh form atau rich text, update juga tracker terkait.

## New Backend Page Checklist

- View backend baru harus berada di namespace backend/domain yang sesuai.
- Gunakan shared component/class `backend-*`.
- Jangan membuat ulang visual primitive.
- Copy memakai language key.
- Verifikasi responsive dan asset build.

## Backend UI PR Review Checklist

- Tidak ada visual primitive baru di SCSS halaman.
- Roadmap `docs/decisions/backend-ui-standardization-roadmap.md` diperbarui sesuai progress.
