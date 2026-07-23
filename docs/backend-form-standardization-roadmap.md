# Backend Form Standardization Roadmap

Roadmap ini menjadi checklist resmi untuk standarisasi form backend: input, dropdown/select, textarea, checkbox, radio, button, icon button, state hover/focus/active/disabled/invalid, dan form action.

## Standard Final

- Semua style form backend berasal dari `resources/backend/scss/components/_backend-form.scss`.
- Form baru/refactor memakai class `backend-form-*`.
- Legacy Bootstrap `.form-control`, `.custom-select`, `.form-group`, `.invalid-feedback`, dan `.btn-*` tetap dinormalisasi oleh shared style selama migrasi.
- Button standar baru memakai `backend-button`, `backend-button-primary`, `backend-button-secondary`, dan `backend-button-danger`.
- Icon button tabel/card tetap memakai `backend-icon-action` dari `resources/backend/scss/components/_backend-actions.scss`.
- Checkbox backend tampil sebagai toggle/switch shared.
- SCSS halaman/domain tidak boleh mendefinisikan ulang background, border, radius, padding, min-height, color, hover, focus, active, disabled, invalid, atau button state form.

## Phase BF-1 - Shared Foundation

- [x] Buat shared component `resources/backend/scss/components/_backend-form.scss`.
- [x] Import shared form component di `resources/backend/scss/app.scss`.
- [x] Normalisasi input, select/dropdown, textarea plain, label, help text, error, action row, checkbox, radio, dan button dari satu sumber.
- [x] Pertahankan kompatibilitas `.form-control`, `.custom-select`, `.form-group`, `.invalid-feedback`, `.btn-*`, `backend-page-primary-action`, dan `backend-toolbar-action`.
- [x] Dokumentasikan aturan di `docs/backend-ui-standards.md`.
- [x] Tambahkan guard test struktur shared form.

## Phase BF-2 - Product Operations Forms

- [x] Refactor form Hotels agar markup utama memakai `backend-form-*`.
- [x] Refactor form Activities agar markup utama memakai `backend-form-*`.
- [x] Refactor form Tours agar markup utama memakai `backend-form-*`.
- [x] Refactor form Transports agar markup utama memakai `backend-form-*`.
- [x] Pastikan tidak ada SCSS domain product yang mendefinisikan ulang visual dasar form.

## Phase BF-3 - Admin Content and User Forms

- [x] Refactor Company Profile, Footer Manager, Terms, Reviews, Currency, User Manager agar markup form memakai `backend-form-*`.
- [x] Pastikan modal admin memakai `backend-form-actions` dan `backend-button-*`.
- [x] Pastikan tidak ada button legacy visual yang belum dinormalisasi.

## Phase BF-4 - Remaining Operations and Legacy Admin

- [x] Refactor Guides, Drivers, Partners, Weddings, Orders Admin, Reservations, Transport Management, Villas, Vendors, Promotions.
- [x] Audit `resources/views/admin` yang masih aktif agar tetap kompatibel dengan shared form.
- [x] Hapus style form domain yang sudah digantikan oleh shared component.

## Phase BF-5 - Final Acceptance

- [x] Audit global `resources/views/admin`, `resources/views/backend`, dan partial backend/admin tidak menemukan `form-control`, `custom-select`, `custom-file-input`, `btn btn-*`, atau action button legacy.
- [x] Audit SCSS domain/admin tidak menemukan form control/button base style di domain page yang menggantikan shared style.
- [x] `php artisan view:cache` berhasil.
- [x] `npm run development` berhasil.
- [x] Test struktur backend form berhasil.
- [x] Audit visual sample: modal, detail page, create/edit form, filter, table action, richtext toolbar.
