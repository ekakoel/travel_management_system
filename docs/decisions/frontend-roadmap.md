# Frontend Roadmap

## 2026-08-03 - Tour Package Manifest-Driven Pax

- Modal Create Order Tour Package sekarang meminta Travel Date, Pickup
  Location, dan Drop-off Location pada step awal; input manual Number of Guests
  dan Select Agent dihapus dari UI.
- Step Guest Details hanya memuat full name, contact opsional, age/category,
  dan gender. ID/passport serta pemilihan leader dihapus dari editor, tabel,
  review, hidden payload, dan validasi frontend.
- Jumlah pax preview dihitung dari manifest guest. Server tetap menghitung
  ulang jumlah row tervalidasi untuk memilih tier harga dan menyimpan order,
  sehingga perubahan DOM atau payload tidak dapat memalsukan jumlah pax.
- Field compatibility pickup contact memakai guest pertama secara deterministik
  tanpa memperkenalkan aturan leader kepada user.
- Review selalu meminta ulang authoritative quote saat dibuka. Respons sukses
  memakai field root `display.unit_price_usd` dan `display.final_total_usd`;
  pembacaan wrapper Laravel Resource `data` tetap didukung selama transisi cache.

Status: active
Updated: 2026-07-29

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

## 2026-07-29 - Activity Order Edit Picker Migration

- Status: done
- Objective: memigrasikan hanya `travel_date` pada Activity Order Edit dari dua initializer legacy yang overlap ke `FrontendPickerSystem`, tanpa mengubah controller, payload, pricing, atau lifecycle.
- Flow: `resources/views/frontend/home/orders/partials/edit-activity.blade.php` pada legacy `layouts.head`.
- Field contract: `name="travel_date"` dan `id="travel-date"` tetap; nilai existing dan `old('travel_date')` memakai `DD MMM YYYY HH.mm`, backend tetap menormalisasi request ke `Y-m-d H.i`; field tetap editable, required, tanpa maximum date, dan memakai increment satu menit.
- Minimum/default: minimum existing tetap hari ini melalui atribut `min` dan opt-in reusable `data-ui-picker-allow-today="true"`. Existing/old value tidak ditimpa saat initialization dan tidak ada prefill atau event initialization baru.
- Asset integration: menggunakan kembali standalone canonical `build/frontend/{js,css}/components/frontend-pickers` yang dibuat untuk legacy layout; tidak ada entry bundle, CDN, style, atau initializer Activity baru.
- Legacy cleanup: class `.datetimepicker` dihapus dari field. Initializer compatibility bersama `input[name="travel_date"]` sekarang mengecualikan input yang sudah menyatakan `[data-ui-picker]`; consumer legacy tanpa atribut canonical tetap tidak berubah.
- Reusable capability: `FrontendPickerSystem` sekarang mendukung `data-ui-picker-allow-today="true"` sebagai generic opt-in untuk business rule terverifikasi yang mengizinkan tanggal hari ini.
- Event compatibility: partial tidak mempunyai listener `input`/`change`, AJAX, validation JS, atau dynamic row untuk `travel_date`; pricing hanya membaca perubahan `number_of_guests`. Canonical tetap mengirim masing-masing satu event `input` dan `change` ketika user menerapkan nilai.
- Visual/functional effect: input memakai shared form state, datetime icon, picker theme, runtime `--ui-*`, dan idempotent initializer; field name, request payload, required/error class, precision, serta backend normalization tidak berubah.
- Files: `resources/views/frontend/home/orders/partials/edit-activity.blade.php`, `resources/views/layouts/footjs.blade.php`, `resources/frontend/js/components/frontend-pickers.js`, `docs/decisions/frontend-picker-standard.md`, `docs/decisions/frontend-roadmap.md`, dan `public/mix-manifest.json`.
- Risk: legacy Date Range Picker CSS tetap dimuat oleh layout dan belum dihapus. Route audit juga menemukan bahwa link Activity memakai `view.edit-order-hotel` sementara controller tersebut saat ini di-scope ke Accommodation, dan form `/fupdate-order/{id}` tidak memiliki route terdaftar; keduanya pre-existing, di luar scope picker, dan belum diperbaiki.
- Verification: syntax/static check, Blade compilation, route inspection, frontend build, Mix manifest inspection, legacy selector search, `git diff --check`, dan task diff review.
- Not done: tidak memigrasikan picker Activity Detail atau flow lain, tidak mengubah route/controller/model/service/database/pricing, dan tidak menjalankan PHPUnit database.
- Follow-up optional: lakukan task terpisah untuk merekonsiliasi route GET/PUT Activity Order Edit beserta ownership, validation, dan focused HTTP test pada database testing terisolasi.

## 2026-07-28 - Hotel Order Edit Picker Migration

- Status: done
- Objective: memigrasikan hanya picker `flight_time[]` pada Hotel Order Edit dari Flatpickr page-level ke `FrontendPickerSystem` tanpa mengubah field contract, payload, business logic, atau flow order lain.
- Flow: `resources/views/frontend/home/orders/partials/edit-hotel.blade.php`, yang dirender oleh legacy `layouts.head`.
- Files:
  - `resources/views/frontend/home/orders/partials/edit-hotel.blade.php`
  - `resources/frontend/js/components/frontend-pickers.js`
  - `webpack.mix.js`
  - `docs/decisions/frontend-picker-standard.md`
  - `docs/decisions/frontend-roadmap.md`
  - `public/mix-manifest.json`
- Before: partial memuat Flatpickr CSS/JS CDN, membuat ulang instance melalui `initDatePickers()`, dan memanggil initializer secara manual untuk DOM awal serta row shuttle baru.
- After: input existing dan dynamic memakai `data-ui-picker="datetime"`, format `YYYY-MM-DD HH:mm`, increment 5 menit, minimum besok, Apply/Cancel canonical, icon global, dan opt-in reusable `data-ui-picker-prefill="true"` untuk mempertahankan default value pada input kosong. MutationObserver canonical menangani dynamic row secara idempotent.
- Asset integration: karena `layouts.head` tidak memuat public frontend bundle, build menyediakan standalone JS/CSS dari source canonical yang sama dan partial memuatnya melalui Blade stack hanya untuk Hotel Order Edit. Tidak ada initializer atau style picker page-specific baru.
- Removed: link stylesheet Flatpickr, script Flatpickr, fungsi `initDatePickers()`, serta dua pemanggilan manualnya. jQuery dan Date Range Picker milik legacy layout dipertahankan karena masih menjadi dependency layout/consumer lain.
- Format contract: sebelum dan sesudah tetap `YYYY-MM-DD HH:mm`; `name="flight_time[]"`, `readonly`, value existing, validasi/request payload, dan event `input`/`change` tetap dipertahankan.
- Effect: picker existing dan row shuttle dinamis memakai satu initializer/style canonical; kalkulasi harga tetap menerima event `change`; tidak ada double initialization dari Flatpickr.
- Risk: standalone picker CSS berjalan berdampingan dengan compatibility `public/css/daterangepicker.css` milik legacy layout, sehingga smoke test visual pada browser tetap diperlukan ketika environment interaktif tersedia.
- Not done: tidak menghapus Flatpickr global/consumer lain, tidak memigrasikan picker halaman lain, dan tidak mengubah controller, request, backend format, atau business logic.
- Follow-up optional: migrasikan satu flow legacy Hotel Order/booking lain yang masih mempunyai Flatpickr page-level setelah seluruh selector dan consumer-nya dipetakan.

## 2026-07-28 - Public Picker Style Ownership Consolidation

- Status: done
- Objective: menjadikan `resources/frontend/scss/components/frontend-pickers.scss` satu-satunya maintained owner shared picker styling tanpa mengubah library, field contract, format value, atau initializer behavior.
- Files:
  - `resources/frontend/scss/components/frontend-pickers.scss`
  - `resources/frontend/scss/components/frontend-buttons.scss`
  - `resources/frontend/scss/components/hotel-check-price-card.scss`
  - `resources/frontend/scss/components/frontend-tokens.scss`
  - `docs/decisions/frontend-picker-standard.md`
  - `docs/decisions/frontend-roadmap.md`
  - `public/mix-manifest.json`
- Selector audit: seluruh maintained public SCSS diperiksa untuk `.daterangepicker`, `.ui-picker-*`, date/datetime/time/month input, calendar/time/navigation icon, `.applyBtn`, `.cancelBtn`, focus, hover, selected, disabled, invalid, dan range state.
- Consolidation: override global `.daterangepicker { z-index: 2000 }` di Hotel dipindahkan ke canonical picker; `.ui-picker-panel` tetap `3000`. Alias library `.cancelBtn` dipindahkan dari button system menjadi `.daterangepicker .drp-buttons .cancelBtn`.
- Runtime theme: off-date, range, disabled, invalid, panel shadow, dan Cancel state memakai supporting `--ui-picker-*` variables dengan fallback yang sama dengan nilai aktif.
- Specialized rules retained: Hotel check-price initializer tetap mengelola min-stay dan hidden checkin/checkout; Flatpickr/direct Date Range Picker code pada flow modern tetap hanya conditional fallback ketika `FrontendPickerSystem` tidak tersedia. Tidak ada JavaScript atau Blade yang diubah.
- Not migrated: optional Clear action dan dedicated datetime-range belum tersedia; `public/css/daterangepicker.css` tetap compatibility dependency layout legacy/backend; unused compiled `public/css/components/hotel-check-price-card.css` tidak diedit manual.
- Bundle impact: global frontend `app.css` berubah dari `62,914` menjadi `65,256` byte (`+2,342` byte) karena canonical state ownership dan runtime fallbacks.
- Verification: `npm run development` berhasil tanpa error; maintained source search hanya menemukan picker selector pada `frontend-pickers.scss` dan color defaults pada `frontend-tokens.scss`; `git diff --check` lulus.
- Risk: visual browser regression belum dijalankan; legacy layouts masih mempunyai vendor/custom picker CSS sendiri dan tidak boleh dikonsolidasikan tanpa memisahkan consumer backend/admin.
- Follow-up optional: audit dan migrasikan satu flow legacy Flatpickr berisiko rendah ke `FrontendPickerSystem`, lalu hapus initializer/style legacy hanya untuk consumer yang telah terbukti.

## 2026-07-28 - Public Frontend Runtime Color Foundation

- Status: done
- Objective: menyiapkan satu integration point warna public frontend untuk backend theme configuration masa depan tanpa membuat backend, database, theme switcher, atau perubahan visual.
- Files:
  - `resources/frontend/scss/components/frontend-tokens.scss`
  - `resources/frontend/scss/components/frontend-base.scss`
  - `resources/frontend/scss/components/frontend-buttons.scss`
  - `resources/frontend/scss/components/frontend-pickers.scss`
  - `resources/frontend/scss/components/frontend-order-modal.scss`
  - `docs/frontend-standards.md`
  - `docs/decisions/frontend-roadmap.md`
  - `public/mix-manifest.json`
- Summary: menambahkan SCSS `!default` palette dan runtime `:root --ui-*` palette, lalu menjadikan `--frontend-shell-*` sebagai compatibility alias. Shared button, form base, picker beserta calendar/time/navigation icon, dan order modal sekarang membaca warna canonical dari runtime palette dengan fallback yang identik dengan warna sebelumnya.
- Core variables: `--ui-primary`, `--ui-primary-hover`, `--ui-secondary`, `--ui-accent`, `--ui-surface`, `--ui-background`, `--ui-text`, `--ui-muted`, `--ui-border`, `--ui-success`, `--ui-warning`, `--ui-danger`, dan `--ui-info`. Supporting variables mempertahankan hover, soft surface, foreground, focus ring, serta picker icon state.
- Visual impact: default runtime values sama dengan palette aktif sebelum migrasi; selector, specificity, markup, layout, contrast target, dan semantic status tidak diubah.
- Bundle impact: global frontend `app.css` bertambah dari `61,403` menjadi `62,914` byte (`+1,511` byte) untuk menampung 28 runtime definitions dan compatibility aliases.
- Not migrated: page-specific SCSS, footer/illustration palette, translucent overlay, domain-specific colors, legacy public CSS, status-soft variants yang belum diaudit, serta seluruh backend/admin theme.
- Verification: `npm run development` berhasil tanpa error; seluruh 28 core/supporting color defaults ditemukan pada compiled `app.css`; shared source menggunakan runtime variables dengan literal fallback; browser smoke test tidak dapat dijalankan karena in-app Browser tidak tersedia.
- Backend integration plan: task masa depan dapat menyimpan palette tervalidasi dan mengeluarkan allowlisted `--ui-*` override setelah `app.css`; jangan menulis selector component atau compiled CSS dari backend.
- Risk: custom theme masa depan tetap memerlukan contrast validation, sanitization, cache strategy, tenant/site scoping, dan visual regression test sebelum activation.
- Follow-up optional: implementasikan read-only theme payload contract dan sanitizer/validator unit test tanpa membuat UI pengaturan terlebih dahulu.

## 2026-07-28 - Shared Button CSS Bundle Deduplication

- Status: done
- Objective: memastikan shared button CSS hanya dikompilasi melalui global `app.scss` pada halaman yang selalu memuat `app.css`, tanpa mengubah desain, selector, markup, atau perilaku button.
- Files:
  - `resources/frontend/scss/pages/{auth,frontend-home,hotel-booking,transport-booking}-entry.scss`
  - `resources/frontend/scss/landing-page/{about,contact,policies}/index-entry.scss`
  - `resources/frontend/scss/landing-page/{accommodations,activities,tours,transports}/{index,detail}-entry.scss`
  - `resources/frontend/scss/home/booking/hotel-availability-entry.scss`
  - `resources/frontend/scss/home/{manual-book,orders,profile}/index-entry.scss`
  - `public/mix-manifest.json`
  - `docs/decisions/frontend-roadmap.md`
- Summary: duplicate import `frontend-buttons.scss` dihapus dari 19 page entry setelah seluruh Blade consumer diverifikasi memakai `frontend.layouts.app`, `layouts.master-login`, atau shared frontend head assets yang selalu memuat global `build/frontend/css/app.css`. Import pada `resources/frontend/scss/home/orders/detail-entry.scss` dipertahankan karena consumer Villa Order Detail masih memakai `layouts.head` yang tidak memuat global frontend `app.css`.
- Bundle impact: total 19 page bundle turun dari `466,770` byte menjadi `320,223` byte (`-146,547` byte / `-31.4%`). Setiap bundle turun `7,713` byte. Global `app.css` tetap `61,403` byte dengan SHA-256 yang identik sebelum dan sesudah build; Order Detail tetap `37,398` byte.
- Functional impact: tidak ada selector, token, Bootstrap compatibility, Blade, JavaScript, atau perilaku button yang diubah. Source canonical tetap `resources/frontend/scss/components/_frontend-buttons.scss` melalui global `resources/frontend/scss/app.scss`.
- Verification: consumer/layout and asset-loading trace; `npm run development` berhasil tanpa build error (terdapat tiga child-compilation warnings); pencarian ulang menemukan hanya import Order Detail yang sengaja dipertahankan; output 19 page bundle tidak lagi memuat selector `.ui-btn`; `git diff --check` lulus.
- Risk: visual browser smoke test belum dijalankan karena in-app Browser tidak tersedia pada sesi verifikasi. Authenticated Orders dan booking state juga belum diuji secara interaktif.
- Follow-up optional: audit dan konsolidasikan asset loading Villa Order Detail ke frontend layout contract sebelum mempertimbangkan penghapusan import button terakhir.

## 2026-07-29 - Activity Detail Datepicker Visibility Fix

- Status: fixed statically; authenticated browser verification pending
- Files:
  - `resources/frontend/js/components/frontend-pickers.js`
  - `resources/frontend/scss/components/frontend-pickers.scss`
  - `resources/frontend/js/landing-page/activities/detail.js`
  - `docs/decisions/frontend-picker-standard.md`
  - `docs/decisions/frontend-roadmap.md`
- Summary: browser-reported Activity Create picker failure ditelusuri ke
  stacking mismatch antara legacy modal `z-index: 999999` dan body-level
  canonical picker `z-index: 3000`. Canonical initializer sekarang menentukan
  layer panel dari floating parent secara runtime; refresh, fallback
  Date Range Picker, dan click handler khusus Activity dihapus.
- Impact: Create Activity Order tetap memakai `travel_date`,
  `YYYY-MM-DD HH:mm`, minute step `5`, minimum dinamis `now + 1 hour` melalui
  reusable allow-today capability, dan global `FrontendPickerSystem`, dengan
  satu instance/initializer owner.
- Verification: `node --check`, route inspection, manifest/HTTP asset check,
  `npm run development`, `php artisan view:cache`, duplicate search, dan
  `git diff --check`. Chrome headless fixture membuktikan satu instance, panel
  visible di atas modal legacy, value backend-compatible, dan satu pasang event
  `input`/`change`; fixture telah dihapus. Browser in-app tidak tersedia, jadi
  smoke test flow authenticated desktop/mobile, pricing, submit, dan validation
  redirect masih pending.

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

## 2026-08-03 - Tour Package Frontend Multi-language

- Status: implemented; browser smoke test mengikuti hasil availability browser.
- Scope: directory, detail/Create Order, quote/validation copy, customer detail,
  customer edit, dan JavaScript Tour Package.
- Summary: user-facing literal dipindahkan ke domain locale `en`, `zh`, dan
  `zh-CN`; JavaScript memakai label `data-*`; konten master memiliki fallback
  English yang konsisten; `/tour-packages` memakai directory canonical.
- Compatibility: nilai enum/database dan pricing/order lifecycle tidak berubah;
  input pengguna tidak diterjemahkan.
- Verification: focused feature tests, PHP lint, Blade cache, production asset
  build, translation/source audit, dan `git diff --check`.
