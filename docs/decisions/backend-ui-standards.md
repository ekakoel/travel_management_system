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
- Tone KPI canonical adalah `teal`, `blue`, `green`, `amber`, `red`, dan
  `slate`; data projection tidak boleh mengirim modifier Bootstrap seperti
  `primary`, `success`, `warning`, atau `info`.
- Icon KPI memakai Font Awesome 4 lokal dengan class `fa fa-*`. Primitive
  shared mengunci font glyph lokal dan menyediakan background slate sebagai
  fallback agar icon tidak menjadi putih/transparan ketika modifier tone
  terlewat.

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
- Satu-satunya control yang hanya berfungsi menutup modal adalah
  `<x-backend.modal-close>` di pojok kanan atas header. Modal tidak boleh
  menduplikasi tombol `Close` pada footer.
- Footer hanya untuk business action seperti Save, Confirm, atau Open Detail.
  Tombol Cancel yang membatalkan perubahan form harus mempunyai semantik yang
  jelas dan tidak dibuat sebagai duplikat control close pada informational
  modal.
- Component close memakai `data-backend-modal-close`; jangan menambahkan
  `data-dismiss="modal"` atau `data-bs-dismiss="modal"` pada canonical backend
  modal. Project masih memiliki runtime Bootstrap 4 dan 5 selama masa
  compatibility, sehingga close wajib melalui adapter shared.
- Modal yang dibuka secara programmatic wajib memakai
  `window.showBackendModal(element)`. Penutupan programmatic memakai
  `window.closeBackendModal(element)`. Adapter memilih instance Bootstrap yang
  benar dan mempunyai fallback cleanup untuk backdrop/body state.
- Close control wajib mempunyai localized `aria-label`, target minimum 36x36,
  visible hover/focus state, dan tetap berada di kanan atas pada mobile.

## Button Standard

- Button memakai `backend-button-primary`, `backend-button-secondary`, atau `backend-button-danger`.
- `Cancel`/close destructive-neutral modal action memakai tone danger.
- Hover/focus memakai token `--backend-button-hover-transform`, `--backend-button-hover-shadow`, dan `--backend-button-focus-ring`.

## Link Standard

- Semua link backend wajib tanpa underline.

## Form Label Standard

- Atribut HTML `required` pada control adalah source of truth marker wajib;
  label tidak boleh menentukan required secara terpisah dari control.
- Runtime bersama `initBackendRequiredMarkers` menambahkan satu
  `backend-required-marker` (`*`, `aria-hidden="true"`) ke label yang terhubung
  dengan `input`, `select`, atau `textarea` required. Marker memakai token
  `--backend-required` dan juga berlaku untuk form/modal yang dimuat dinamis.
- Control wajib mempunyai pasangan `label[for]`/`id`, wrapping label, atau
  berada dalam container `backend-form-field`/`form-group` dengan label.
- Hidden input tidak mempunyai marker visual. Marker lama berupa `<span>*</span>`
  dinormalisasi oleh runtime dan tidak diduplikasi.

## Checkbox Standard

- Checkbox backend tampil sebagai switch/toggle shared kecuali ada alasan teknis.

## Form Control Standard

- Form style utama berada di `resources/backend/scss/components/_backend-form.scss`.
- Semua form backend bermethod selain GET memakai initializer bersama
  `initBackendSubmitGuards`. Klik submit pertama langsung memberi state
  `backendSubmitPending` dan menampilkan `backend-action-spinner` pada tombol
  yang diklik sebelum native validation selesai. Event submit yang valid
  mempromosikan state menjadi `backendSubmitting`, mengunci seluruh submit
  control yang terasosiasi dengan form (termasuk button dengan atribut `form`),
  dan menolak submit berikutnya sampai navigasi selesai. Form yang
  sengaja dikelola penuh oleh runtime lain dapat opt-out eksplisit memakai
  `data-backend-submit-guard="false"`.
- State submit wajib dipulihkan ketika event dibatalkan atau halaman dikembalikan
  dari browser history, agar validation/client-side cancellation tidak
  meninggalkan form terkunci.
- Action button/link non-form dapat opt-in memakai
  `data-backend-action-loading`. Runtime memberi spinner instan dan mencegah
  klik ulang; action asynchronous yang tetap berada pada halaman wajib memanggil
  `window.setBackendActionLoading(element, false)` pada blok `finally`.
- Input tanggal canonical baru memakai `data-backend-picker="date"` dan
  `data-backend-picker-format="yyyy-mm-dd"`. Initializer tunggal berada di
  `resources/backend/js/app.js`.
- Class legacy `.date-picker` menghasilkan display/request `dd MM yyyy` dan
  tidak boleh dipakai untuk field database baru yang berkontrak `Y-m-d`.
- Backend tetap wajib menormalisasi format compatibility lama melalui
  allow-list eksplisit; format numeric ambigu tidak boleh ditebak.
- Setiap input monetary backend wajib menampilkan unit yang digunakan melalui
  shared `initBackendMoneyInputs`. Nama field canonical memakai pemetaan unit
  global; field baru atau pengecualian wajib mendeklarasikan
  `data-backend-money-unit`. Unit dinamis memakai
  `data-backend-money-unit-source` dan `data-backend-money-unit-map`. Prefix
  unit serta helper text hanya presentation dan tidak boleh mengubah nilai
  request. Persentase memakai `%`, bukan kode currency.
- Shared monetary input menampilkan pemisah ribuan secara langsung: IDR memakai
  format `1.000.000`, sedangkan USD memakai `1,000` dan desimal `1,000.50`.
  Pemisah hanya presentation; sebelum native validation, submit, atau pembuatan
  `FormData`, nilai dinormalisasi menjadi decimal string canonical tanpa
  pemisah ribuan (contoh `1000000` atau `1000.50`). Controller dan database
  tidak boleh menyimpan format tampilan.

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
- Modal: `backend-modal`, `backend-modal__header`, `backend-modal__body`,
  `backend-modal__footer`, `<x-backend.modal-close>`,
  `showBackendModal`, dan `closeBackendModal`.
- Form: `backend-form`, `backend-form-grid`, `backend-form-field`, `backend-form-label`, `backend-form-control`, `backend-form-actions`.
- Monetary input: `data-backend-money-unit`, `backend-money-control`, dan shared `initBackendMoneyInputs`.
- Required marker: atribut `required` + initializer shared `initBackendRequiredMarkers`; jangan mengandalkan warna/markup page-specific.
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
