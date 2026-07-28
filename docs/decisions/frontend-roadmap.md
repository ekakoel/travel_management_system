# Frontend Roadmap

Status: active
Updated: 2026-07-27

Roadmap ini menyimpan status frontend aktif secara ringkas. Entry historis panjang sudah dipadatkan agar agent bisa cepat memahami kondisi project.

## Current Baseline

- Standar UI frontend: `docs/frontend-standards.md`.
- Standar Blade/asset: `docs/decisions/blade-asset-rules.md`.
- Standar submit: `docs/decisions/form-submit-standard.md`.
- Standar modal order: `docs/decisions/frontend-order-modal-standard.md`.
- Standar multi-language: `docs/decisions/multi-language-standard.md`.

## Status Aktif

- Public landing pages utama sudah memakai namespace `resources/views/frontend/landing-page`.
- Authenticated customer/order area sudah memakai namespace `resources/views/frontend/home` untuk banyak flow utama.
- Frontend public detail service memakai shared shell/card/modal pattern secara bertahap.
- Modal order Activity Detail menjadi baseline order modal.
- Hotel availability/check price menjadi baseline shell frontend.
- Shared Swiper tersedia melalui `frontend-loop-swiper`.
- Language coverage aktif untuk `en`, `zh`, dan `zh-CN`, tetapi legacy view masih perlu audit.

## Area Yang Masih Perlu Perhatian

- Beberapa view legacy masih berada di `resources/views/main` dan perlu audit domain sebelum migrasi.
- Beberapa JS/CSS legacy di `public/css/pages` dan `public/frontend/js/pages` masih mungkin aktif.
- Flow order hotel, transport, villa, dan wedding perlu terus disamakan dengan idempotent submit standard.
- Copy frontend legacy perlu audit hardcoded text.
- Setiap perubahan frontend baru harus menambah entry ringkas di bawah.

## Recent Entries

## 2026-07-28 - Activity Detail Datepicker Stabilization

- Status: done
- Files:
  - `resources/views/frontend/landing-page/activities/detail.blade.php`
  - `resources/frontend/js/landing-page/activities/detail.js`
- Summary: Activity order `travel_date` now uses the shared frontend datetime picker contract with body-level panel positioning, modal-open refresh, scoped Date Range Picker fallback, and Bootstrap focus-trap compatibility.
- Impact: Activity Date picker opens reliably inside the create order modal while keeping the existing `travel_date` field name and backend-compatible `YYYY-MM-DD HH:mm` value.
- Verification: `php -l resources/views/frontend/landing-page/activities/detail.blade.php`, `npm run development`, `php artisan view:cache`.

## 2026-07-28 - Hotel Order Picker Stabilization

- Status: done
- Files:
  - `resources/frontend/js/pages/hotel-booking.js`
  - `resources/views/frontend/home/booking/orders/hotel-normal.blade.php`
  - `resources/views/frontend/home/booking/orders/hotel-package.blade.php`
  - `resources/views/frontend/home/booking/orders/hotel-promo.blade.php`
  - `resources/views/partials/hotel-booking-room-card.blade.php`
  - `resources/views/partials/hotel-booking-transfer-fields.blade.php`
- Summary: Hotel order special-date and flight datetime fields now declare the global `data-ui-picker` contract directly, flight datetime pickers use Apply/Cancel controls, cloned rows clean global picker state before re-init, and hidden legacy transfer submit fields are no longer initialized as visible pickers.
- Impact: Normal, package, and promo hotel order pages use the shared picker system consistently while keeping existing field names and submitted date formats.
- Verification: `npm run development`, `php -l` on affected Blade files.

## 2026-07-28 - Global Frontend Picker System

- Status: done
- Files:
  - `resources/frontend/js/components/frontend-pickers.js`
  - `resources/frontend/scss/components/frontend-pickers.scss`
  - `resources/frontend/js/app.js`
  - `resources/frontend/scss/app.scss`
  - `resources/views/frontend/landing-page/activities/detail.blade.php`
  - `resources/views/frontend/landing-page/tours/detail.blade.php`
  - `resources/views/frontend/landing-page/transports/detail.blade.php`
  - `resources/views/frontend/home/booking/orders/transport.blade.php`
  - `resources/frontend/js/pages/hotel-booking.js`
  - `resources/frontend/js/pages/transport-booking.js`
  - `resources/frontend/js/landing-page/transports/detail.js`
- Summary: Added global `data-ui-picker` system using Date Range Picker, shared styling for picker inputs/panels, and compatibility auto-init for legacy frontend picker classes.
- Impact: Frontend date, datetime, range, and legacy picker fields can share one reusable design-system layer without page-level duplicate Date Range Picker initialization.
- Verification: `npm run development`.

## 2026-07-28 - Tour Order Datepicker Standardization

- Status: done
- Files:
  - `app/Http/Controllers/ToursController.php`
  - `resources/views/frontend/landing-page/tours/detail.blade.php`
  - `resources/frontend/js/landing-page/tours/detail.js`
- Summary: Modal create order pada `/tour/{slug}` memakai input `datetime-local` dengan minimum dan prefill terstruktur, serta review menampilkan tanggal sebagai `YYYY-MM-DD HH:mm`.
- Impact: Tour Package order modal lebih selaras dengan baseline modal order Activity Detail dan standar tanggal frontend.
- Verification: `php -l app/Http/Controllers/ToursController.php`, `npm run development`.

## 2026-07-27 - Documentation Compaction

- Status: done
- Files:
  - `docs/decisions/frontend-roadmap.md`
  - `docs/frontend-standards.md`
  - `docs/decisions/blade-asset-rules.md`
  - `docs/decisions/form-submit-standard.md`
- Summary: Roadmap frontend dipadatkan dari log panjang menjadi status aktif, baseline, dan area follow-up.
- Impact: Agent dapat memahami arah frontend tanpa membaca changelog historis yang terlalu panjang.
- Verification: Markdown/link audit dan status git.

## Template

Gunakan `docs/decisions/frontend-roadmap-entry-template.md` untuk entry berikutnya.
