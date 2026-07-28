# Backend Form Standardization Roadmap

Status: active
Updated: 2026-07-27

Tracker ringkas untuk migrasi form backend ke shared form system.

## Standard Final

- Semua form backend baru/refactor memakai `backend-form-*`.
- Form style utama berada di `resources/backend/scss/components/_backend-form.scss`.
- Tombol memakai `backend-button`, `backend-button-primary`, `backend-button-secondary`, dan `backend-button-danger`.
- Hidden input dan checkbox tidak boleh diberi class `backend-form-control`.

## Phase BF-1 - Shared Foundation

- [x] Shared form style tersedia.
- [x] Token form height, radius, border, focus, danger ring, dan button tersedia.
- [x] Dokumentasi aturan ada di `docs/decisions/backend-ui-standards.md`.

## Phase BF-2 - Product Operations Forms

- [x] Refactor form Hotels agar markup utama memakai `backend-form-*`.
- [x] Refactor form Activities agar markup utama memakai `backend-form-*`.
- [x] Refactor form Tours agar markup utama memakai `backend-form-*`.
- [x] Refactor form Transports agar markup utama memakai `backend-form-*`.

## Phase BF-3 - Admin Content And User Forms

- [x] Refactor Company Profile, Footer Manager, Terms, Reviews, Currency, User Manager agar markup form memakai `backend-form-*`.
- [x] Pastikan modal admin memakai `backend-form-actions` dan `backend-button-*`.

## Phase BF-4 - Remaining Operations

- [x] Create Hotel Order cleanup memakai shared form helpers.
- [ ] Audit wedding/reservation/order legacy forms yang masih berada di namespace admin/main.
- [ ] Pindahkan override visual form page-specific ke shared form component bila dipakai ulang.

## Phase BF-5 - Final Acceptance

- [ ] Tidak ada form backend baru memakai `.form-group`, `.form-control`, `.custom-select`, atau `.btn btn-*` sebagai standar utama.
- [ ] Semua form backend aktif punya label, validation state, disabled state, responsive layout, dan button state yang konsisten.
