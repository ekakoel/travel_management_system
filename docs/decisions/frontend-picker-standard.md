# Frontend Picker Standard

Status: active
Updated: 2026-07-28

Standar ini mengatur seluruh picker tanggal, waktu, dan rentang tanggal pada frontend Balikami Tour. Scope utamanya adalah public frontend dan authenticated customer/agent area. Backend/admin mengikuti standar backend terpisah kecuali sedang dimigrasikan secara eksplisit.

## Tujuan

- Satu arsitektur picker global yang reusable dan mudah dirawat.
- Tampilan Date Range Picker modern dan konsisten dengan frontend shell.
- Tidak ada double initialization, double dependency, atau initializer page-level baru.
- Backward-compatible dengan nama field dan format request Laravel yang sudah berjalan.

## Library

Library utama:

```text
Date Range Picker: https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js
Moment.js: https://cdn.jsdelivr.net/momentjs/latest/moment.min.js
```

Keduanya dimuat oleh layout frontend utama:

- `resources/views/frontend/layouts/frontend-head-assets.blade.php`
- `resources/views/frontend/layouts/app.blade.php`

Jangan menambah CDN Date Range Picker atau Moment.js di halaman frontend yang memakai `frontend.layouts.app`.

## Struktur File

- JS global: `resources/frontend/js/components/frontend-pickers.js`
- SCSS global: `resources/frontend/scss/components/frontend-pickers.scss`
- JS entry: `resources/frontend/js/app.js`
- SCSS entry: `resources/frontend/scss/app.scss`

Build output dibuat oleh Laravel Mix ke:

- `public/build/frontend/js/app.js`
- `public/build/frontend/css/app.css`

## Data Attribute

Gunakan kontrak baru berikut untuk markup baru:

```html
<input type="text" class="form-control" data-ui-picker="date" data-ui-picker-format="YYYY-MM-DD">
<input type="text" class="form-control" data-ui-picker="range" data-ui-picker-format="YYYY-MM-DD">
<input type="text" class="form-control" data-ui-picker="datetime" data-ui-picker-format="YYYY-MM-DD HH:mm">
<input type="text" class="form-control" data-ui-picker="time" data-ui-picker-format="HH:mm">
```

Alias dari request yang juga didukung selama migrasi:

```html
data-picker="date"
data-picker="daterange"
data-picker="datetime"
data-picker="time"
data-format="YYYY-MM-DD"
data-min-date="2026-07-28"
data-max-date="2026-12-31"
data-start-date="2026-08-01"
data-end-date="2026-08-03"
data-auto-apply="true"
data-single-date="true"
data-show-dropdowns="true"
data-disable-past="true"
data-disable-future="true"
data-opens="left"
data-drops="down"
```

Project-prefixed attributes are preferred:

```html
data-ui-picker="datetime"
data-ui-picker-format="YYYY-MM-DD HH:mm"
data-ui-picker-min="2026-07-28 00:00"
data-ui-picker-max="2026-12-31 23:59"
data-ui-picker-auto-apply="true"
data-ui-picker-show-buttons="true"
data-ui-picker-minute-step="5"
data-ui-picker-opens="center"
data-ui-picker-drops="auto"
data-ui-picker-icon="false"
```

## Format Standar

- Date display/value baru: `YYYY-MM-DD`
- Datetime display/value baru: `YYYY-MM-DD HH:mm`
- Time display/value baru: `HH:mm`
- Date and time picker serta time picker default menampilkan tombol Apply/Cancel agar user dapat memilih tanggal dan jam sebelum nilai input disimpan.
- Month native fallback: `YYYY-MM`
- Year native/text fallback: `YYYY`

Jangan mengubah format request existing tanpa audit controller/request, helper date, AJAX handler, dan validation rule.

## Minimum Date Standard

- Semua frontend date, datetime, dan range picker default hanya boleh memilih tanggal setelah hari ini.
- Minimum global adalah besok pada `00:00` waktu browser/user.
- Jika input memiliki `data-ui-picker-min`, `data-min-date`, atau atribut `min` yang lebih jauh dari besok, nilai yang lebih jauh dipakai.
- Jika konfigurasi lama masih mengarah ke hari ini atau tanggal lampau, global picker akan menaikkannya ke besok.
- Time-only, month, dan year picker tidak memakai aturan minimum tanggal harian ini.

## Dynamic Content API

Global object:

```javascript
window.FrontendPickerSystem.init(document);
window.FrontendPickerSystem.init(container);
window.FrontendPickerSystem.initPicker(input);
window.FrontendPickerSystem.destroy(input);
window.FrontendPickerSystem.refresh(input);
```

Modal Bootstrap dan node baru dari AJAX/repeater akan diinisialisasi otomatis. Tetap panggil `FrontendPickerSystem.init(container)` setelah render manual bila container dibuat oleh kode lama yang tidak memicu event DOM normal.

## Positioning Standard

- Default positioning baru adalah `data-ui-picker-drops="auto"`.
- Picker di modal/floating layer memakai `parentEl: body`, `opens: center`, dan menentukan `drops` saat panel dibuka berdasarkan ruang viewport aktual.
- Jangan override fungsi internal `picker.move()` dan jangan menjalankan reposition berulang pada click/change kalender karena akan menyebabkan flicker/blink.
- Jika halaman benar-benar membutuhkan arah tetap, pakai `data-ui-picker-drops="up"` atau `data-ui-picker-drops="down"` pada input terkait.

## Compatibility Selector

Selector lama berikut tetap di-auto-init:

- `.datetimepicker`
- `.date-picker`
- `.frontend-datepicker`
- `.frontend-datetimepicker`
- `.frontend-timepicker`
- `.daterangepicker-input`
- `data-booking-datetime`
- `data-transport-datetime`
- `input[name="checkincout"]`
- native `input[type="date"]`
- native `input[type="datetime-local"]`
- native `input[type="time"]`
- native `input[type="month"]`

Compatibility ini bersifat sementara. Markup baru wajib memakai `data-ui-picker`.

## Icon

Picker manager membungkus input dengan:

```html
<span class="ui-picker-field ui-picker-field--datetime">
    <input ...>
    <span class="ui-picker-field__icon" aria-hidden="true"></span>
</span>
```

Wrapper tidak dibuat jika input sudah berada di `.input-group` atau `.input-group-icon`. Set `data-ui-picker-icon="false"` jika wrapper tidak aman untuk komponen tertentu.

## Styling

Semua style global berada di `frontend-pickers.scss`. Jangan membuat override page-level baru untuk:

- `.daterangepicker`
- day cell active/range/today
- Apply button
- date/time input focus
- picker panel z-index

Jika perlu variasi, gunakan data attribute konfigurasi atau modifier class terarah.

Tombol Cancel Date Range Picker memakai class bawaan library `.cancelBtn`, yang distandarisasi di `resources/frontend/scss/components/frontend-buttons.scss`. Untuk button cancel reusable di markup baru, gunakan `.ui-btn.ui-btn--cancel` atau `.frontend-btn.frontend-btn--cancel`; legacy Bootstrap alias `.btn.btn-cancel` juga mengikuti style cancel frontend.

## Audit Summary

Hasil audit 2026-07-28:

| Area | File/Pattern | Library/Initializer | Status |
| --- | --- | --- | --- |
| Frontend main layout | `resources/views/frontend/layouts/app.blade.php` | Moment + Date Range Picker CDN | Active global dependency |
| Frontend head assets | `resources/views/frontend/layouts/frontend-head-assets.blade.php` | Date Range Picker CSS CDN | Active global CSS dependency |
| Legacy frontend header | `resources/views/frontend/layouts/header.blade.php` | Moment + Date Range Picker CDN | Legacy duplicate, not removed yet |
| Legacy app layouts | `resources/views/layouts/head.blade.php`, `resources/views/layouts/footjs.blade.php` | Date Range Picker + `public/css/daterangepicker.css` | Backend/legacy dependency, not frontend-cleanup scope |
| Hotel check price | `resources/views/partials/hotel-check-price-card.blade.php`, `resources/frontend/js/components/frontend-hotel-check-price.js` | Date Range Picker, custom min-stay sync | Active specialized range flow |
| Old checkincout | `public/frontend/js/main.js` | Old direct Date Range Picker init | Replaced with `FrontendPickerSystem.init()` |
| Activity detail order | `resources/views/frontend/landing-page/activities/detail.blade.php` | `data-ui-picker="datetime"` | Migrated |
| Tour detail order | `resources/views/frontend/landing-page/tours/detail.blade.php` | `data-ui-picker="datetime"` | Migrated |
| Transport detail order | `resources/views/frontend/landing-page/transports/detail.blade.php` | `data-ui-picker="datetime"` | Migrated |
| Transport booking order | `resources/views/frontend/home/booking/orders/transport.blade.php` | `data-ui-picker="datetime"` | Migrated |
| Hotel booking wizard | `resources/views/frontend/home/booking/orders/hotel-*.blade.php`, `resources/views/partials/hotel-booking-*.blade.php`, `resources/frontend/js/pages/hotel-booking.js` | `data-ui-picker` markup with `FrontendPickerSystem`, Flatpickr fallback only when global picker is unavailable | Migrated |
| Transport JS | `resources/frontend/js/pages/transport-booking.js`, `resources/frontend/js/landing-page/transports/detail.js` | Uses `FrontendPickerSystem` first, Flatpickr fallback | Partially migrated |
| Wedding/edit legacy frontend | `resources/views/frontend/home/orders/**`, `resources/views/main/**` | `.date-picker`, `.datetimepicker`, Flatpickr, inline scripts | Compatibility only, needs phase 4 audit per flow |

## Dependencies Removed

Removed duplicate page-level CDN includes from modern frontend pages that already use `frontend.layouts.app`:

- `resources/views/frontend/home/booking/hotel-availability.blade.php`
- `resources/views/frontend/home/booking/orders/hotel-normal.blade.php`
- `resources/views/frontend/home/booking/orders/hotel-package.blade.php`
- `resources/views/frontend/home/booking/orders/hotel-promo.blade.php`
- `resources/views/frontend/home/booking/orders/transport.blade.php`
- `resources/views/frontend/landing-page/transports/detail.blade.php`

No file was deleted.

## Known Limitations

- Month/year picker currently uses styled native/text fallback. Do not use it for complex month/year grid UX until a real need is audited.
- Legacy wedding/edit pages still contain inline scripts and Flatpickr usage. They are supported through compatibility selectors where possible, but full cleanup requires page-by-page flow testing.
- `public/css/daterangepicker.css` is still referenced by legacy layouts and must not be deleted until those references are removed and verified.
- Visual browser testing is still required for every migrated modal/sidebar/page because no browser automation was run in this pass.

## Migration Rules

1. Add `data-ui-picker` to new frontend date/time inputs.
2. Keep existing `name`, `id`, `value`, and backend format unless controller/request audit says otherwise.
3. Prefer `YYYY-MM-DD` and `YYYY-MM-DD HH:mm`.
4. Do not add new inline `<script>` or page-level picker initializer.
5. If dynamic content is added, call `window.FrontendPickerSystem.init(container)`.
6. Remove duplicate dependency includes only after confirming the layout already loads the dependency.
