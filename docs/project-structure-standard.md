# Project Structure Standard

Dokumen ini menjadi standar penataan file frontend dan backend untuk project `balikamitour`.

Tujuannya:

- File mudah ditemukan berdasarkan area aplikasi dan tipe akses.
- Frontend publik, frontend login user, dan backend/admin tidak saling bercampur.
- Asset JS/SCSS mengikuti lokasi view agar ownership jelas.
- Migrasi struktur bisa dilakukan bertahap tanpa mematahkan route, Blade include, Mix entry, atau test.

## Prinsip Utama

1. Struktur folder mengikuti area aplikasi terlebih dahulu, lalu domain fitur.
2. View, JS, SCSS, partial, dan language key harus punya domain ownership yang sama.
3. Frontend publik dan frontend authenticated dipisah.
4. Backend/admin dipisah dari frontend sepenuhnya.
5. Shared component hanya masuk folder `shared` jika benar-benar dipakai lintas area.
6. Jangan membuat file baru di folder legacy seperti `resources/views/home`, `resources/views/main`, `resources/views/form`, atau `resources/views/order` kecuali sedang migrasi bertahap.

## Struktur Target

```text
app/
  Http/
    Controllers/
      Frontend/
        LandingPage/
        Home/
      Backend/
        Admin/
        Sales/
        Operations/
      Api/
    Requests/
      Frontend/
      Backend/
  Services/
    Frontend/
    Backend/
    Shared/

resources/
  views/
    frontend/
      layouts/
      shared/
      landing-page/
        home/
        about/
        contact/
        accommodations/
        activities/
        tours/
        transports/
        policies/
      home/
        dashboard/
        orders/
        profile/
        booking/
        payment/
      auth/
    backend/
      layouts/
      shared/
      admin/
      sales/
      operations/
      finance/
      settings/

  frontend/
    js/
      shared/
      landing-page/
        home/
        accommodations/
        activities/
        tours/
        transports/
        policies/
      home/
        orders/
        profile/
        booking/
        payment/
    scss/
      shared/
      landing-page/
        home/
        accommodations/
        activities/
        tours/
        transports/
        policies/
      home/
        orders/
        profile/
        booking/
        payment/
    images/
      shared/
      landing-page/
      home/

  backend/
    js/
      shared/
      admin/
      sales/
      operations/
      finance/
      settings/
    scss/
      shared/
      admin/
      sales/
      operations/
      finance/
      settings/
    images/
      shared/
      admin/

  lang/
    en/
    zh/
    zh-CN/
```

## Definisi Area

### `frontend/landing-page`

Untuk halaman yang bisa diakses tanpa login.

Contoh:

- Homepage publik
- About
- Contact
- Directory accommodation/activity/tour/transport
- Detail public service
- Policy, terms, FAQ

View target:

```text
resources/views/frontend/landing-page/transports/index.blade.php
resources/views/frontend/landing-page/transports/detail.blade.php
```

Asset target:

```text
resources/frontend/js/landing-page/transports/index.js
resources/frontend/js/landing-page/transports/detail.js
resources/frontend/scss/landing-page/transports/index-entry.scss
resources/frontend/scss/landing-page/transports/detail-entry.scss
resources/frontend/scss/landing-page/transports/_detail.scss
```

### `frontend/home`

Untuk halaman frontend yang hanya bisa diakses user login.

Contoh:

- User order dashboard
- Detail order user
- Edit order user
- Profile
- Payment flow
- Booking form lanjutan setelah login

View target:

```text
resources/views/frontend/home/orders/index.blade.php
resources/views/frontend/home/orders/detail-tour.blade.php
resources/views/frontend/home/profile/edit.blade.php
```

Asset target:

```text
resources/frontend/js/home/orders/index.js
resources/frontend/js/home/orders/detail.js
resources/frontend/scss/home/orders/index-entry.scss
resources/frontend/scss/home/orders/_detail.scss
```

### `backend`

Untuk admin panel, reservation, sales, operations, finance, settings, report, dan semua halaman internal staff.

View target:

```text
resources/views/backend/admin/users/index.blade.php
resources/views/backend/sales/customers/index.blade.php
resources/views/backend/operations/bookings/index.blade.php
```

Asset target:

```text
resources/backend/js/admin/users/index.js
resources/backend/scss/admin/users/index-entry.scss
```

Contoh implementasi aktif:

```text
resources/views/backend/developer/index.blade.php
resources/backend/js/admin/panel/index.js
resources/backend/scss/admin/panel/index-entry.scss
resources/backend/scss/admin/panel/_index.scss
```

Halaman backend baru wajib menempatkan Blade, JS, dan SCSS di area `backend` sesuai domainnya. Hindari inline script/style untuk behavior halaman yang dapat dipindahkan ke asset backend.

## Mapping Legacy Ke Struktur Target

| Legacy | Target |
| --- | --- |
| `resources/views/home/landing-page/*` | `resources/views/frontend/landing-page/*` |
| `resources/views/home/transports/*` | `resources/views/frontend/landing-page/transports/*` |
| `resources/views/frontend/activities/*` | `resources/views/frontend/landing-page/activities/*` |
| `resources/views/frontend/tours/*` | `resources/views/frontend/landing-page/tours/*` |
| `resources/views/frontend/accommodations/*` | `resources/views/frontend/landing-page/accommodations/*` |
| `resources/views/frontend/orders/*` | `resources/views/frontend/home/orders/*` |
| `resources/views/main/profile.blade.php` | `resources/views/frontend/home/profile/*` |
| `resources/views/form/order-*.blade.php` | pindahkan sesuai area: public booking atau user booking |
| `resources/views/order/*` | `resources/views/backend/operations/orders/*` atau `frontend/home/orders/*`, tergantung akses |
| `resources/views/admin/*` | `resources/views/backend/admin/*` |
| `resources/views/backend/*` | tetap `resources/views/backend/*`, rapikan per domain |
| `resources/views/layouts/*` | `resources/views/frontend/layouts`, `backend/layouts`, atau `shared/layouts` |
| `resources/views/partials/*` | `frontend/shared`, `backend/shared`, atau `views/shared` sesuai pemakaian |

## Naming Standard

### View

Gunakan nama berbasis fungsi halaman:

```text
index.blade.php
detail.blade.php
create.blade.php
edit.blade.php
partials/_rate-card.blade.php
partials/_order-modal.blade.php
```

Jangan gunakan nama ambigu:

```text
detail-modern.blade.php
order-new.blade.php
page2.blade.php
test.blade.php
```

Jika ada redesign, migrasikan sampai nama final kembali sederhana seperti `detail.blade.php`.

### JS

Gunakan satu entry page per halaman utama:

```text
resources/frontend/js/landing-page/transports/detail.js
resources/frontend/js/home/orders/detail.js
```

Shared JS ditempatkan di:

```text
resources/frontend/js/shared/components/
resources/frontend/js/shared/utils/
```

### SCSS

Entry page:

```text
detail-entry.scss
```

Partial page:

```text
_detail.scss
_order-modal.scss
```

Shared SCSS:

```text
resources/frontend/scss/shared/components/_order-modal.scss
resources/frontend/scss/shared/layouts/_page-shell.scss
```

## Route Naming Standard

Frontend public:

```php
Route::name('frontend.landing.transports.')->group(...)
```

Frontend authenticated:

```php
Route::middleware(['auth', 'verified'])
    ->name('frontend.home.orders.')
    ->group(...);
```

Backend:

```php
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('backend.admin.')
    ->group(...);
```

## Controller Naming Standard

Frontend public:

```text
App\Http\Controllers\Frontend\LandingPage\TransportController
```

Frontend authenticated:

```text
App\Http\Controllers\Frontend\Home\OrderController
```

Backend:

```text
App\Http\Controllers\Backend\Admin\UserController
App\Http\Controllers\Backend\Operations\OrderController
```

## Language File Standard

Domain frontend baru wajib memiliki file language domain jika teksnya banyak.

Contoh:

```text
resources/lang/en/transports.php
resources/lang/zh/transports.php
resources/lang/zh-CN/transports.php
```

Gunakan `messages.php` hanya untuk kata/frasa global yang benar-benar dipakai lintas domain.

## Asset Build Standard

`webpack.mix.js` harus mengikuti struktur target.

Contoh target:

```js
mix.js('resources/frontend/js/landing-page/transports/detail.js', 'public/build/frontend/js/landing-page/transports')
   .sass('resources/frontend/scss/landing-page/transports/detail-entry.scss', 'public/build/frontend/css/landing-page/transports');
```

Untuk mengurangi daftar manual, tahap akhir migrasi disarankan membuat helper daftar entry agar `webpack.mix.js` tidak terlalu panjang.

## Migration Todo

Status checklist ini disinkronkan terakhir pada proses migrasi struktur frontend/backend. Detail batch dan bukti test ada di `docs/project-structure-migration-todo.md`.

### Phase 0 - Freeze Rule

- [x] Jangan menambah file frontend baru di folder legacy.
- [x] Jangan menambah file backend baru di folder frontend.
- [x] Dokumentasikan setiap pengecualian di PR atau commit message.

### Phase 1 - Inventory

- [x] Petakan semua route publik, route login-user, dan route backend.
- [x] Petakan semua Blade yang dipanggil oleh controller.
- [x] Petakan semua `@include`, `@extends`, `mix()`, dan script/style push.
- [x] Tandai file legacy yang masih aktif dan file yang sudah tidak terpakai.

### Phase 2 - Shared Foundation

- [x] Buat folder target `frontend/landing-page`, `frontend/home`, dan `backend` jika belum ada.
- [x] Pindahkan shared frontend layout ke `resources/views/frontend/layouts`.
- [x] Pindahkan shared frontend partial ke `resources/views/frontend/shared`.
- [x] Pindahkan shared frontend SCSS/JS ke `resources/frontend/{js,scss}/shared`.
- [x] Pastikan modal order shared tetap punya satu source of truth.

### Phase 3 - Public Landing Pages

- [x] Migrasi homepage.
- [x] Migrasi about/contact/policies.
- [x] Migrasi accommodations public.
- [x] Migrasi activities public.
- [x] Migrasi tours public.
- [x] Migrasi transports public.
- [x] Update controller return view.
- [x] Update `@include` dan `@extends`.
- [x] Update `webpack.mix.js`.
- [x] Test route dan build asset setelah setiap domain.

### Phase 4 - Authenticated Frontend

- [x] Migrasi profile.
- [x] Migrasi order dashboard.
- [x] Migrasi detail order user.
- [x] Migrasi edit order user.
- [x] Migrasi payment/booking flow user.
- [x] Update route middleware group.
- [x] Test akses guest harus redirect login.
- [x] Test user login bisa akses.

### Phase 5 - Backend

- [x] Migrasi admin user/role/module.
- [x] Migrasi sales.
- [x] Migrasi operations/order management.
- [x] Migrasi transport/accommodation/tour/activity management.
- [x] Migrasi finance/payment/report.
- [x] Update controller namespace secara bertahap.
- [x] Update route names dan breadcrumbs.

Catatan: Phase 5 checklist utama sudah ditutup untuk batch backend yang menjadi standar struktur project: admin user views, sales, operations/order management, transport/accommodation/tour/activity management, finance invoice, dan report downloads. Beberapa halaman internal legacy lain masih berada di namespace `admin`, `layouts`, dan `main`; lanjutkan sebagai hardening per domain tanpa mematahkan route internal yang sudah stabil.

### Phase 6 - Cleanup

- [x] Hapus view legacy yang sudah tidak direferensikan.
- [x] Hapus asset legacy yang sudah tidak dipakai.
- [x] Rapikan `webpack.mix.js`.
- [x] Tambahkan test guard struktur folder.
- [x] Update README onboarding developer.

Catatan: cleanup yang sudah selesai mencakup `resources/views/home`, `resources/views/form`, `resources/views/order`, `resources/views/order-wedding-package`, orphan `resources/views/main`, serta source asset legacy halaman public/home. Sisa `resources/views/main` masih aktif dipakai controller lama dan harus dimigrasi per domain.

## Definition Of Done Per Domain

Satu domain dianggap selesai jika:

- [x] Route masih berjalan.
- [x] Controller sudah return view path baru.
- [x] Semua `@include` dan `@extends` sudah valid.
- [x] JS page entry sudah pindah ke folder target.
- [x] SCSS page entry sudah pindah ke folder target.
- [x] `webpack.mix.js` sudah menunjuk path baru.
- [x] Language key tetap berjalan.
- [x] Feature test terkait lulus.
- [x] `npm run development` berhasil.
- [x] File legacy domain tersebut sudah dihapus atau ditandai deprecated.

Status di atas berlaku untuk domain yang sudah dimigrasi dan dilindungi test struktur: public transport, public activity, public tour, public accommodation, static/policy pages, profile, order dashboard/detail/history/edit, hotel availability, booking order forms, review views, wedding order package sections, dan shared frontend modal/order foundation.

## Guard Rule Untuk File Baru

Sebelum membuat file baru, jawab pertanyaan ini:

1. Apakah file bisa diakses tanpa login?

Jika ya, tempatkan di `frontend/landing-page`.

2. Apakah file hanya untuk user login non-admin?

Jika ya, tempatkan di `frontend/home`.

3. Apakah file untuk staff/admin/internal operation?

Jika ya, tempatkan di `backend`.

4. Apakah file dipakai lintas frontend public dan frontend login?

Jika ya, tempatkan di `frontend/shared`.

5. Apakah file dipakai lintas frontend dan backend?

Jika ya, pertimbangkan `resources/views/shared` atau `app/Services/Shared`, tetapi jangan jadikan shared sebelum ada minimal dua pemakai nyata.

## Recommended First Migration

Selesai. Domain Transport sudah menjadi batch awal dan menjadi pola untuk domain public lain.

Source legacy yang sudah ditutup:

1. `resources/views/home/landing-page/transport.blade.php`
2. `resources/views/home/transports/detail.blade.php`
3. `resources/frontend/js/pages/transportations-index.js`
4. `resources/frontend/js/pages/transport-detail.js`
5. `resources/frontend/scss/pages/transportations-index-entry.scss`
6. `resources/frontend/scss/pages/transport-detail-entry.scss`
7. `resources/frontend/scss/pages/transportations-index.scss`
8. `resources/frontend/scss/pages/transport-detail.scss`

Target:

```text
resources/views/frontend/landing-page/transports/index.blade.php
resources/views/frontend/landing-page/transports/detail.blade.php
resources/frontend/js/landing-page/transports/index.js
resources/frontend/js/landing-page/transports/detail.js
resources/frontend/scss/landing-page/transports/index-entry.scss
resources/frontend/scss/landing-page/transports/detail-entry.scss
resources/frontend/scss/landing-page/transports/_index.scss
resources/frontend/scss/landing-page/transports/_detail.scss
```

## Remaining Migration Work

Gunakan daftar ini sebagai arah berikutnya setelah migrasi frontend utama selesai:

1. Migrasi namespace aktif `resources/views/main` per domain, terutama wedding, hotel legacy, activity legacy, transport legacy, chat, dan calendar. Manual booking sudah dipindahkan ke `resources/views/frontend/home/manual-book`.
2. Migrasi namespace aktif `resources/views/admin` ke `resources/views/backend/admin` per modul lanjutan di luar admin user/finance/report yang sudah selesai.
3. Rapikan controller namespace fisik ke `App\Http\Controllers\Frontend\*` dan `App\Http\Controllers\Backend\*` secara bertahap setelah view target stabil.
4. Rapikan route name lanjutan ke standar `frontend.landing.*`, `frontend.home.*`, dan `backend.*` tanpa mematahkan route publik lama.
5. Update README onboarding developer setelah struktur backend aktif selesai.

Jangan migrasi semua domain sekaligus. Selesaikan satu domain, test, build, baru lanjut domain berikutnya.
