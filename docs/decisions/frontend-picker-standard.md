# Frontend Picker Standard

Status: active
Updated: 2026-07-29

Standar ini mengatur seluruh picker tanggal, waktu, dan rentang tanggal pada frontend Balikami Tour. Scope utamanya adalah public frontend dan authenticated customer/agent area. Backend/admin mengikuti standar backend terpisah kecuali sedang dimigrasikan secara eksplisit.

## Tujuan

- Satu arsitektur picker global yang reusable dan mudah dirawat.
- Tampilan Date Range Picker modern dan konsisten dengan frontend shell.
- Tidak ada double initialization, double dependency, atau initializer page-level baru.
- Backward-compatible dengan nama field dan format request Laravel yang sudah berjalan.
- Satu kontrak untuk date, date range, datetime, time, month, year, date-time
  range, dan kebutuhan picker tanggal/waktu lain.
- Satu sumber icon, state visual, responsive behavior, dan localization.

Dokumen ini adalah kontrak aktif dan wajib. Halaman atau flow baru tidak boleh
membuat picker theme, wrapper icon, dependency, atau initializer sendiri.

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

Mode aktif initializer saat ini:

- `date`
- `range`
- `datetime`
- `time`
- `month`
- `year`

`datetime-range` adalah kebutuhan canonical yang dicadangkan, tetapi belum
didukung sebagai mode aktif. Sampai centralized implementation tersedia,
jangan menulis `data-ui-picker="datetime-range"` dan jangan membuat fallback
page-level. Implementasinya harus ditambahkan pada
`resources/frontend/js/components/frontend-pickers.js` dan
`resources/frontend/scss/components/frontend-pickers.scss`, disertai audit
request format dan test.

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
data-ui-picker-prefill="true"
data-ui-picker-allow-today="true"
data-ui-picker-opens="center"
data-ui-picker-drops="auto"
data-ui-picker-icon="false"
```

## Format Standar

- Date display/value baru: `YYYY-MM-DD`
- Datetime display/value baru: `YYYY-MM-DD HH:mm`
- Time display/value baru: `HH:mm`
- Date and time picker serta time picker default menampilkan tombol Apply/Cancel agar user dapat memilih tanggal dan jam sebelum nilai input disimpan.
- `data-ui-picker-prefill="true"` hanya dipakai ketika input kosong memang
  harus langsung diisi dengan tanggal minimum/default untuk mempertahankan
  kontrak flow existing. Default global tetap tidak mengubah input kosong saat
  initialization.
- Month native fallback: `YYYY-MM`
- Year native/text fallback: `YYYY`

Jangan mengubah format request existing tanpa audit controller/request, helper date, AJAX handler, dan validation rule.

## Minimum Date Standard

- Semua frontend date, datetime, dan range picker default hanya boleh memilih tanggal setelah hari ini.
- Minimum global adalah besok pada `00:00` waktu browser/user.
- Jika input memiliki `data-ui-picker-min`, `data-min-date`, atau atribut `min` yang lebih jauh dari besok, nilai yang lebih jauh dipakai.
- Jika konfigurasi lama masih mengarah ke hari ini atau tanggal lampau, global picker akan menaikkannya ke besok.
- `data-ui-picker-allow-today="true"` adalah opt-in reusable untuk flow existing
  yang business rule terverifikasinya memang mengizinkan hari ini. Opsi ini
  memakai awal hari ini sebagai floor dan tetap menghormati minimum
  terkonfigurasi yang lebih jauh; jangan digunakan sebagai default baru.
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

Initializer wajib idempotent:

- input yang sudah memiliki marker initialization tidak boleh diinisialisasi
  ulang;
- event handler harus namespaced/dilepas sebelum binding ulang;
- dynamic DOM harus masuk melalui `FrontendPickerSystem.init(container)`;
- destroy/refresh harus memakai public API, bukan mengubah instance library
  langsung;
- modal, repeater, AJAX fragment, dan DOM hasil render client harus memakai
  initializer yang sama.

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

Kontrak icon:

- date dan range memakai calendar icon shared;
- datetime dan time memakai time icon shared;
- previous/next navigation memakai icon dari shared picker theme;
- clear icon hanya ditampilkan jika flow memang mendukung clear dan harus
  diimplementasikan secara centralized;
- icon tidak boleh ditambahkan secara berbeda di setiap Blade;
- jangan menambah Font Awesome/Bootstrap Icon markup pada input jika global
  wrapper sudah menyediakan affordance;
- icon-only control harus mempunyai accessible name bila interaktif;
- penambahan icon tidak boleh mengubah `name`, `value`, `id`, event picker,
  format request, atau validation behavior.

Gunakan shared CSS, pseudo-element, centralized wrapper, atau shared Blade
component sesuai pola existing yang paling aman. Jangan memindahkan authoritative
input value ke elemen visual.

## Styling

Semua style global berada di `frontend-pickers.scss`. Jangan membuat override page-level baru untuk:

- `.daterangepicker`
- day cell active/range/today
- Apply button
- date/time input focus
- picker panel z-index

Jika perlu variasi, gunakan data attribute konfigurasi atau modifier class terarah.

Status ownership aktif setelah konsolidasi 2026-07-28:

- `frontend-pickers.scss` adalah satu-satunya maintained SCSS yang memiliki
  selector picker global `.daterangepicker`, `.ui-picker-*`, `.applyBtn`, dan
  `.cancelBtn`.
- Default panel non-modal tetap memakai `z-index: 2000`; panel dari
  `FrontendPickerSystem` memakai modifier `.ui-picker-panel` dan
  `z-index: 3000`.
- `hotel-check-price-card.scss` tidak boleh memiliki override
  `.daterangepicker` global. Styling Hotel harus tetap memakai selector
  `.hotel-check-*`; kebutuhan panel yang shared masuk ke canonical picker.
- `frontend-buttons.scss` tetap menjadi button system umum, tetapi tidak lagi
  memiliki alias library `.cancelBtn`. Cancel picker dimiliki dan di-scope
  melalui `.daterangepicker .drp-buttons .cancelBtn`.
- Vendor Date Range Picker CSS yang dimuat layout tetap menjadi dependency
  library. Override project terhadap vendor tersebut hanya boleh berada di
  `frontend-pickers.scss`.
- Seluruh warna override picker memakai runtime `--ui-*` dengan fallback yang
  mempertahankan nilai aktif.

Satu theme wajib mencakup:

- tinggi dan padding input;
- typography;
- border, radius, background, spacing, dan shadow;
- hover dan focus;
- today, selected, start date, end date, dan in-range;
- disabled, readonly, unavailable, dan invalid;
- calendar/time icon serta previous/next navigation;
- Apply, Cancel, dan optional Clear action;
- mobile width, viewport overflow, stacking, dan touch target;
- modal/floating-layer positioning.

State invalid harus tetap mengikuti validation state form existing. Picker tidak
boleh menyembunyikan error message, menghapus invalid class, atau mengganti
server-side validation behavior.

Localization label, nama bulan/hari, Apply, Cancel, dan Clear harus berasal dari
locale/configuration shared. Hardcoded label baru pada page-level tidak
diperbolehkan. Format tampilan boleh dikonfigurasi, tetapi format value/backend
tidak boleh berubah tanpa audit kontrak request.

Tombol Cancel Date Range Picker memakai class bawaan library `.cancelBtn` dan
distandarisasi secara scoped di
`resources/frontend/scss/components/frontend-pickers.scss`. Untuk button cancel
reusable di markup baru, gunakan `.ui-btn.ui-btn--cancel` atau
`.frontend-btn.frontend-btn--cancel`; legacy Bootstrap alias `.btn.btn-cancel`
tetap mengikuti button system frontend.

## Audit Summary

Hasil audit 2026-07-28:

| Area | File/Pattern | Library/Initializer | Status |
| --- | --- | --- | --- |
| Frontend main layout | `resources/views/frontend/layouts/app.blade.php` | Moment + Date Range Picker CDN | Active global dependency |
| Frontend head assets | `resources/views/frontend/layouts/frontend-head-assets.blade.php` | Date Range Picker CSS CDN | Active global CSS dependency |
| Legacy frontend header | `resources/views/frontend/layouts/header.blade.php` | Moment + Date Range Picker CDN | Legacy duplicate, not removed yet |
| Legacy app layouts | `resources/views/layouts/head.blade.php`, `resources/views/layouts/footjs.blade.php` | Date Range Picker + `public/css/daterangepicker.css` | Backend/legacy dependency, not frontend-cleanup scope |
| Hotel check price | `resources/views/partials/hotel-check-price-card.blade.php`, `resources/frontend/js/components/frontend-hotel-check-price.js` | Date Range Picker, custom min-stay sync; global z-index override removed from Hotel SCSS | Active specialized range flow with canonical shared styling |
| Old checkincout | `public/frontend/js/main.js` | Old direct Date Range Picker init | Replaced with `FrontendPickerSystem.init()` |
| Activity detail order | `resources/views/frontend/landing-page/activities/detail.blade.php` | `data-ui-picker="datetime"`; canonical runtime stacking for body-level modal panel | Fixed statically; authenticated browser verification pending |
| Tour detail order | `resources/views/frontend/landing-page/tours/detail.blade.php` | `data-ui-picker="datetime"` | Migrated |
| Transport detail order | `resources/views/frontend/landing-page/transports/detail.blade.php` | `data-ui-picker="datetime"` | Migrated |
| Transport booking order | `resources/views/frontend/home/booking/orders/transport.blade.php` | `data-ui-picker="datetime"` | Migrated |
| Hotel booking wizard | `resources/views/frontend/home/booking/orders/hotel-*.blade.php`, `resources/views/partials/hotel-booking-*.blade.php`, `resources/frontend/js/pages/hotel-booking.js` | `data-ui-picker` markup with `FrontendPickerSystem`, Flatpickr fallback only when global picker is unavailable | Migrated |
| Hotel Order Edit | `resources/views/frontend/home/orders/partials/edit-hotel.blade.php` | Standalone canonical picker assets + `data-ui-picker="datetime"`; no page-level Flatpickr initializer | Migrated |
| Activity Order Edit | `resources/views/frontend/home/orders/partials/edit-activity.blade.php` | Reuses standalone canonical assets; `travel_date` uses datetime mode and audited allow-today exception | Migrated |
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
- Dedicated date-time range belum tersedia pada centralized initializer. Ini
  adalah gap implementasi, bukan izin untuk membuat initializer page-level.
- Optional Clear action dan localization label penuh belum menjadi kemampuan
  global yang terdokumentasi sebagai selesai; implementasinya harus dilakukan
  pada shared initializer/theme.
- Legacy wedding/edit pages still contain inline scripts and Flatpickr usage. They are supported through compatibility selectors where possible, but full cleanup requires page-by-page flow testing.
- `public/css/daterangepicker.css` is still referenced by legacy layouts and must not be deleted until those references are removed and verified.
- `public/css/components/hotel-check-price-card.css` masih merupakan compiled
  legacy artifact tanpa consumer Blade yang ditemukan. File tersebut tidak
  diedit manual dan baru boleh dihapus setelah audit asset legacy terpisah.
- Visual browser testing is still required for every migrated modal/sidebar/page because no browser automation was run in this pass.

## Migration Rules

1. Add `data-ui-picker` to new frontend date/time inputs.
2. Keep existing `name`, `id`, `value`, and backend format unless controller/request audit says otherwise.
3. Prefer `YYYY-MM-DD` and `YYYY-MM-DD HH:mm`.
4. Do not add new inline `<script>` or page-level picker initializer.
5. If dynamic content is added, call `window.FrontendPickerSystem.init(container)`.
6. Remove duplicate dependency includes only after confirming the layout already loads the dependency.
7. Do not add per-Blade calendar/time/clear/navigation icon.
8. Verify normal, hover, focus, selected, disabled, invalid, modal, and mobile
   behavior for the modes touched by the task.
9. Treat date-time range, full localization, or Clear support as centralized
   component enhancements, not page customization.

## Catatan Keputusan

Tanggal: 2026-07-28

Pekerjaan dan tujuan:

- Memperluas kontrak picker agar mencakup seluruh keluarga tanggal/waktu,
  shared visual states, icon, localization, responsive behavior, dynamic DOM,
  dan duplicate-initialization prevention.
- Menegaskan bahwa gap capability harus diselesaikan pada global picker
  system, bukan melalui initializer atau style baru per halaman.

Efek:

- Seluruh task frontend berikutnya wajib menggunakan `FrontendPickerSystem`,
  `data-ui-picker-*`, dan shared picker theme.
- Dokumen ini tidak mengubah library, request value, event behavior, Blade,
  SCSS, atau JavaScript saat ini.

Risiko dan pekerjaan belum dilakukan:

- `datetime-range`, optional Clear, dan localization label penuh masih
  memerlukan implementasi dan verification terpisah.
- Compatibility selector dan legacy dependency belum boleh dihapus sebelum
  semua consumer diaudit.

## 2026-07-29 - Create Activity Order Panel Visibility

Tanggal: 2026-07-29

Tujuan dan flow:

- Memperbaiki picker `travel_date` pada modal Create Activity Order yang
  dirender oleh route `view.activity-public-detail` dan dikirim ke
  `view.activity-order.store`.
- Menjaga field `travel_date`, request payload, server validation, price
  calculation, dan format backend `YYYY-MM-DD HH:mm` tetap sama.

Root cause:

- Layout aktif memuat legacy `public/frontend/css/custom_style.css`, yang
  menetapkan `.modal { z-index: 999999; }`.
- Field meminta panel ditempel ke `body`, sedangkan canonical
  `.ui-picker-panel` sebelumnya memiliki `z-index: 3000`; panel dapat dibuat
  tetapi berada di belakang modal.
- Page script juga memiliki refresh, direct Date Range Picker fallback, dan
  click-to-show handler sendiri. Jalur tersebut menduplikasi ownership
  `FrontendPickerSystem` dan tidak dapat memperbaiki stacking karena nilai
  `3000` tetap lebih rendah dari modal.

Keputusan implementasi:

- `FrontendPickerSystem` menghitung z-index panel secara generik dari floating
  layer terdekat dan menyalurkannya melalui
  `--ui-picker-panel-z-index`; fallback non-modal/modal tetap `2000`/`3000`.
- Activity detail tidak lagi membuat, me-refresh, atau membuka instance picker
  secara page-level. Initial load, modal event, idempotency, apply event,
  positioning, dan dynamic DOM dimiliki canonical initializer.
- Atribut Blade tetap `data-ui-picker="datetime"`,
  `data-ui-picker-format="YYYY-MM-DD HH:mm"`, minimum dinamis `now + 1 hour`
  dari controller dengan reusable `data-ui-picker-allow-today="true"`, minute
  step `5`, serta body-level parent yang diperlukan flow ini.

Asset integration:

- CSS canonical tetap berasal dari global `build/frontend/css/app.css`.
- JavaScript canonical tetap berasal dari global
  `build/frontend/js/app.js`; page bundle hanya mengelola wizard, guest
  manifest, review, price display, submit guard, dan loading overlay.
- Tidak ada CDN, bundle standalone, inline style, atau inline initializer baru.

Verifikasi aktual:

- `node --check` lulus untuk canonical picker dan Activity detail script.
- Laravel Mix development build berhasil; compiled canonical/global CSS dan
  JS memuat runtime z-index variable serta page bundle tidak lagi memuat
  initializer Activity-specific.
- Route, manifest entry, HTTP asset response, Blade cache, duplicate search,
  dan `git diff --check` diverifikasi.
- Chrome headless menjalankan fixture sementara dengan dependency dan compiled
  canonical asset yang sama: satu instance terbentuk, panel terlihat,
  `panelZIndex=1000009` berada di atas `modalZIndex=999999`, Apply menghasilkan
  value `2026-07-31 10:15`, serta event `input` dan `change` masing-masing satu.
  Fixture dihapus setelah verifikasi.
- Browser in-app tidak tersedia pada sesi ini. Karena modal Create Activity
  Order aktual memerlukan user authenticated dan approved, full-flow
  click/select, mobile viewport, pricing interaction, submit payload, dan
  validation redirect tetap harus diverifikasi dengan session yang sesuai
  sebelum status `FIXED AND VERIFIED` boleh digunakan.

Risiko:

- Modal legacy dengan z-index non-numeric tetap memakai fallback `3000`.
- External Moment/Date Range Picker dependency masih dimuat oleh layout aktif
  dan tidak dihapus karena memiliki consumer frontend lain.
