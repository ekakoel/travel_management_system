# Backend Rich Text Textarea Roadmap

Status: active
Updated: 2026-07-27

Tracker ringkas untuk standar rich text backend.

## Standard Final

- Semua textarea backend otomatis memakai `initBackendRichText` dari `resources/backend/js/app.js`.
- Textarea rich text eksplisit memakai `data-backend-richtext="true"`.
- Textarea plain text wajib memakai `data-backend-richtext="false"` dan alasannya jelas.
- Style editor berasal dari `resources/backend/scss/components/_backend-richtext.scss`.
- Initializer harus idempotent untuk halaman, modal, dan dynamic content.

## Audit Awal

- `resources/views/backend` memiliki 167 textarea pada saat baseline dibuat.
- Class legacy `textarea_editor` masih dipertahankan sebagai compatibility bridge.

## Phase RT-1 - Shared Foundation

- [x] Rich text textarea shared tersedia.
- [x] `initBackendRichText` tersedia dan diekspos ke `window`.
- [x] Selector mencakup backend main container dan modal.
- [x] Compatibility `textarea.textarea_editor` dipertahankan.
- [x] Style z-index toolbar/dropdown/tooltip diatur di shared SCSS.

## Domain Status

- [x] Hotels.
- [x] Activities.
- [x] Tours.
- [x] Transports.
- [x] Admin content pages utama.
- [ ] Remaining legacy backend forms perlu audit saat disentuh.

## Phase RT-6 - Final Acceptance

- [ ] Semua textarea backend aktif punya attribute eksplisit `data-backend-richtext`.
- [ ] Legacy `textarea_editor` hanya dihapus setelah tidak ada consumer aktif.
- [ ] Tidak ada inisialisasi Summernote inline di Blade baru.
