# Project Structure Migration Todo

Dokumen ini adalah tracker eksekusi untuk `docs/project-structure-standard.md`.

Status dibuat agar migrasi struktur folder dilakukan bertahap, domain demi domain, tanpa mematahkan route, Blade include, asset build, atau test.

## Status Saat Ini

Tanggal mulai: 2026-07-15

Step awal yang sudah dilakukan:

- [x] Standar struktur dibuat di `docs/project-structure-standard.md`.
- [x] Standar modal frontend dihubungkan ke aturan struktur.
- [x] Folder target awal dibuat untuk `frontend/landing-page`, `frontend/home`, `frontend/shared`, dan beberapa domain backend utama.
- [x] Domain pertama untuk migrasi ditetapkan: `Transport`.
- [x] Transport route/view tests sudah dijalankan sebelum cleanup dan memastikan route aktif memakai view baru.
- [x] Step 3 Transport Cleanup sudah menghapus view dan asset source legacy yang tidak lagi direferensikan.
- [x] Domain kedua untuk migrasi ditetapkan: `Activities`.
- [x] Activities route/view tests sudah dijalankan sebelum cleanup dan memastikan route aktif memakai view baru.
- [x] Activities cleanup sudah menghapus view dan asset source legacy yang tidak lagi direferensikan.
- [x] Domain ketiga untuk migrasi ditetapkan: `Tours`.
- [x] Tours route/view tests sudah dijalankan sebelum cleanup dan memastikan route aktif memakai view baru.
- [x] Tours cleanup sudah menghapus view modern dan asset source legacy yang tidak lagi direferensikan.
- [x] Domain keempat untuk migrasi ditetapkan: `Accommodations`.
- [x] Accommodations route/view tests sudah dijalankan sebelum cleanup dan memastikan route aktif memakai view baru.
- [x] Accommodations cleanup sudah menghapus view dan asset source legacy yang tidak lagi direferensikan.
- [x] Flow berikutnya untuk migrasi ditetapkan: `Hotel Availability / Check Price`.
- [x] Hotel Availability route/view tests sudah dijalankan sebelum cleanup dan memastikan route aktif memakai view baru.
- [x] Hotel Availability cleanup sudah menghapus view dan asset source legacy yang tidak lagi direferensikan.
- [x] Domain berikutnya untuk migrasi ditetapkan: `Static Landing Pages - About & Contact`.
- [x] About/Contact route/view tests sudah dijalankan sebelum cleanup dan memastikan route aktif memakai view baru.
- [x] About/Contact cleanup sudah menghapus view dan SCSS source legacy yang tidak lagi direferensikan.
- [x] Domain berikutnya untuk migrasi ditetapkan: `Public Policies`.
- [x] Public Policies route/view tests sudah dijalankan sebelum cleanup dan memastikan route aktif memakai view baru.
- [x] Public Policies cleanup sudah menghapus wrapper Terms/FAQ dan SCSS source legacy yang tidak lagi direferensikan.
- [x] Legacy privacy manager flow tetap dipertahankan untuk migrasi terpisah karena masih dipanggil oleh `TermAndConditionController::v_privacy_policy`.
- [x] Domain berikutnya untuk migrasi ditetapkan: `Authenticated Profile`.
- [x] Profile route/view tests sudah dijalankan sebelum cleanup dan memastikan route aktif memakai view baru.
- [x] Profile cleanup sudah menghapus view, JS, dan SCSS source legacy yang tidak lagi direferensikan.
- [x] Domain berikutnya untuk migrasi ditetapkan: `Authenticated Orders Dashboard`.
- [x] Orders Dashboard route/view tests sudah dijalankan sebelum cleanup dan memastikan route aktif memakai view baru.
- [x] Orders Dashboard cleanup sudah menghapus view, JS, dan SCSS source legacy yang tidak lagi direferensikan.
- [x] Domain berikutnya untuk migrasi ditetapkan: `Authenticated Order Detail`.
- [x] Order Detail route/view tests sudah dijalankan sebelum cleanup dan memastikan route aktif memakai view baru.
- [x] Order Detail cleanup sudah menghapus view, JS, dan SCSS source legacy yang tidak lagi direferensikan.
- [x] Domain berikutnya untuk migrasi ditetapkan: `Authenticated Orders History`.
- [x] Orders History route/view tests sudah dijalankan sebelum cleanup dan memastikan route aktif memakai view baru.
- [x] Orders History cleanup sudah menghapus view legacy yang tidak lagi direferensikan.
- [x] Domain berikutnya untuk migrasi ditetapkan: `Authenticated Tour Order Edit`.
- [x] Tour Order Edit route/view tests sudah dijalankan sebelum cleanup dan memastikan route aktif memakai view baru.
- [x] Tour Order Edit cleanup sudah menghapus view dan JS source legacy yang tidak lagi direferensikan.
- [x] Domain berikutnya untuk migrasi ditetapkan: `Legacy Order Edit Wrapper`.
- [x] Legacy Order Edit Wrapper tests sudah dijalankan sebelum cleanup dan memastikan controller aktif memakai wrapper baru.
- [x] Legacy Order Edit Wrapper cleanup sudah menghapus wrapper lama yang tidak lagi direferensikan.
- [x] Domain berikutnya untuk migrasi ditetapkan: `Transport Order Edit Partial`.
- [x] Transport Order Edit Partial tests sudah dijalankan sebelum cleanup dan memastikan wrapper aktif memakai partial baru.
- [x] Transport Order Edit Partial cleanup sudah menghapus partial legacy yang tidak lagi direferensikan.
- [x] Domain berikutnya untuk migrasi ditetapkan: `Villa Order Edit Partial`.
- [x] Villa Order Edit Partial tests sudah dijalankan sebelum cleanup dan memastikan wrapper aktif memakai partial baru.
- [x] Villa Order Edit Partial cleanup sudah menghapus partial legacy yang tidak lagi direferensikan.
- [x] Domain berikutnya untuk migrasi ditetapkan: `Hotel Order Edit Partial`.
- [x] Hotel Order Edit Partial tests sudah dijalankan sebelum cleanup dan memastikan wrapper aktif memakai partial baru.
- [x] Hotel Order Edit Partial cleanup sudah menghapus partial legacy yang tidak lagi direferensikan.
- [x] Domain berikutnya untuk migrasi ditetapkan: `Activity Order Edit Partial`.
- [x] Activity Order Edit Partial tests sudah dijalankan sebelum cleanup dan memastikan wrapper aktif memakai partial baru.
- [x] Activity Order Edit Partial cleanup sudah menghapus partial legacy yang tidak lagi direferensikan.
- [x] Domain berikutnya untuk migrasi ditetapkan: `Additional Charge Order Edit`.
- [x] Additional Charge Order Edit route/view tests sudah dijalankan sebelum cleanup dan memastikan route aktif memakai view baru.
- [x] Additional Charge Order Edit cleanup sudah menghapus view legacy yang tidak lagi direferensikan.
- [x] Domain berikutnya untuk cleanup ditetapkan: `Legacy Optional Service Order Edit`.
- [x] Legacy Optional Service Order Edit diverifikasi tidak memiliki route/controller/include aktif.
- [x] Legacy Optional Service Order Edit cleanup sudah menghapus orphan view lama.
- [x] Kelompok berikutnya untuk migrasi ditetapkan: `Wedding Order Frontend Views`.
- [x] Wedding Order edit/detail views dipindahkan ke `frontend/home/orders/weddings`.
- [x] Wedding Order legacy partials dipindahkan sebagai satu kelompok ke `frontend/home/orders/weddings/partials`.
- [x] Wedding Order route/view structure tests dan build sudah dijalankan sebelum cleanup legacy selesai.
- [x] Kelompok berikutnya untuk migrasi ditetapkan: `Service Order Detail Legacy Views`.
- [x] Service Order Detail wrapper, Activity/Villa legacy details, Transport legacy detail, dan shared payment partials dipindahkan ke `frontend/home/orders/details`.
- [x] Service Order Detail route/view structure tests dan build sudah dijalankan sebelum cleanup legacy selesai.
- [x] Kelompok berikutnya untuk migrasi ditetapkan: `Admin Order Helper Views`.
- [x] Admin Order helper views dipindahkan ke `backend/operations/orders/actions`.
- [x] Admin Order helper structure tests dan build sudah dijalankan sebelum cleanup legacy selesai.
- [x] Kelompok berikutnya untuk migrasi ditetapkan: `Remaining User Order Edit Legacy Views`.
- [x] Active user order room edit view dipindahkan ke `frontend/home/orders`.
- [x] Remaining orphan user order edit/promo/wedding legacy views dibersihkan dari `resources/views/order`.
- [x] Kelompok berikutnya untuk closure ditetapkan: `Legacy Order View Namespace`.
- [x] Legacy Order View Namespace diverifikasi tidak memiliki Blade aktif tersisa.
- [x] Guard test ditambahkan agar tidak ada file Blade baru di `resources/views/order`.
- [x] Kelompok berikutnya untuk migrasi ditetapkan: `Wedding Order Package Sections`.
- [x] Wedding Order Package sections dipindahkan dari `resources/views/order-wedding-package` ke `frontend/home/orders/weddings/sections`.
- [x] Wedding edit view diperbarui agar memakai namespace section baru.
- [x] Kelompok berikutnya untuk migrasi ditetapkan: `Modern Order Detail Views`.
- [x] Modern Tour/Transport detail views dan legacy Tour detail template dipindahkan dari `frontend/orders` ke `frontend/home/orders/details`.

## Folder Target Awal

Frontend public:

- [x] `resources/views/frontend/landing-page/transports`
- [x] `resources/views/frontend/landing-page/activities`
- [x] `resources/views/frontend/landing-page/tours`
- [x] `resources/views/frontend/landing-page/accommodations`
- [x] `resources/views/frontend/landing-page/about`
- [x] `resources/views/frontend/landing-page/contact`
- [x] `resources/views/frontend/landing-page/policies`
- [x] `resources/frontend/js/landing-page/transports`
- [x] `resources/frontend/js/landing-page/activities`
- [x] `resources/frontend/js/landing-page/tours`
- [x] `resources/frontend/js/landing-page/accommodations`
- [x] `resources/frontend/scss/landing-page/transports`
- [x] `resources/frontend/scss/landing-page/activities`
- [x] `resources/frontend/scss/landing-page/tours`
- [x] `resources/frontend/scss/landing-page/accommodations`
- [x] `resources/frontend/scss/landing-page/about`
- [x] `resources/frontend/scss/landing-page/contact`
- [x] `resources/frontend/scss/landing-page/policies`

Frontend authenticated:

- [x] `resources/views/frontend/home/orders`
- [x] `resources/views/frontend/home/profile`
- [x] `resources/views/frontend/home/booking`
- [x] `resources/frontend/js/home/orders`
- [x] `resources/frontend/js/home/profile`
- [x] `resources/frontend/js/home/booking`
- [x] `resources/frontend/scss/home/orders`
- [x] `resources/frontend/scss/home/profile`
- [x] `resources/frontend/scss/home/booking`

Shared frontend:

- [x] `resources/views/frontend/shared`
- [x] `resources/frontend/js/shared`
- [x] `resources/frontend/scss/shared`

Backend starter:

- [x] `resources/views/backend/operations`
- [x] `resources/views/backend/sales`
- [x] `resources/backend/js/admin`
- [x] `resources/backend/js/operations`
- [x] `resources/backend/js/sales`
- [x] `resources/backend/scss/admin`
- [x] `resources/backend/scss/operations`
- [x] `resources/backend/scss/sales`

## Transport Domain Inventory

Current active routes:

- `GET /transportations`
- Route name: `view.transport-service`
- Controller: `FrontEndController::transport_service`
- Previous view: `resources/views/home/landing-page/transport.blade.php`
- Active view: `resources/views/frontend/landing-page/transports/index.blade.php`

- `GET /transportation-{id}`
- Route name: `transport.show`
- Controller: `HomeController::show_transport`
- Previous view: `resources/views/home/transports/detail.blade.php`
- Active view: `resources/views/frontend/landing-page/transports/detail.blade.php`

Previous transport asset source paths:

- `resources/frontend/js/pages/transportations-index.js`
- `resources/frontend/js/pages/transport-detail.js`
- `resources/frontend/scss/pages/transportations-index-entry.scss`
- `resources/frontend/scss/pages/transportations-index.scss`
- `resources/frontend/scss/pages/transport-detail-entry.scss`
- `resources/frontend/scss/pages/transport-detail.scss`

Target transport assets:

- `resources/frontend/js/landing-page/transports/index.js`
- `resources/frontend/js/landing-page/transports/detail.js`
- `resources/frontend/scss/landing-page/transports/index-entry.scss`
- `resources/frontend/scss/landing-page/transports/_index.scss`
- `resources/frontend/scss/landing-page/transports/detail-entry.scss`
- `resources/frontend/scss/landing-page/transports/_detail.scss`

Known references that must be checked during transport migration:

- `routes/web.php`
- `app/Http/Controllers/FrontEndController.php`
- `app/Http/Controllers/HomeController.php`
- `resources/views/frontend/layouts/navbar.blade.php`
- `resources/views/frontend/home/partials/services.blade.php`
- `resources/views/home/landing-page/services.blade.php`
- `resources/views/home/partials/footer.blade.php`
- `resources/views/form/order-transport.blade.php`
- `webpack.mix.js`
- `tests/Feature/FrontendOrderModalStandardTest.php`
- `tests/Feature/TransportOrderNumberTest.php`

## Activities Domain Inventory

Current active routes:

- `GET /activity-services`
- Route name: `view.activity-services`
- Controller: `FrontEndController::activity_services`
- Previous view: `resources/views/frontend/activities/index.blade.php`
- Active view: `resources/views/frontend/landing-page/activities/index.blade.php`

- `GET /activity/{code}`
- Route name: `view.activity-public-detail`
- Controller: `FrontEndController::activity_detail`
- Previous view: `resources/views/frontend/activities/detail.blade.php`
- Active view: `resources/views/frontend/landing-page/activities/detail.blade.php`

Previous activities asset source paths:

- `resources/frontend/js/pages/activities-index.js`
- `resources/frontend/js/pages/activity-detail.js`
- `resources/frontend/scss/pages/activities-index-entry.scss`
- `resources/frontend/scss/pages/activities-index.scss`
- `resources/frontend/scss/pages/activity-detail-entry.scss`
- `resources/frontend/scss/pages/activity-detail.scss`

Target activities assets:

- `resources/frontend/js/landing-page/activities/index.js`
- `resources/frontend/js/landing-page/activities/detail.js`
- `resources/frontend/scss/landing-page/activities/index-entry.scss`
- `resources/frontend/scss/landing-page/activities/_index.scss`
- `resources/frontend/scss/landing-page/activities/detail-entry.scss`
- `resources/frontend/scss/landing-page/activities/_detail.scss`

Known references checked during activities migration:

- `routes/web.php`
- `app/Http/Controllers/FrontEndController.php`
- `webpack.mix.js`
- `tests/Feature/ProjectStructureStandardTest.php`
- `tests/Feature/FrontendOrderModalStandardTest.php`
- `tests/Feature/ActivityDetailGuestManifestTest.php`

## Tours Domain Inventory

Current active public routes:

- `GET /tour-package-services`
- Route name: `view.tour-package-services`
- Controller: `FrontEndController::tour_package_services`
- Previous view: `resources/views/frontend/tours/directory.blade.php`
- Active view: `resources/views/frontend/landing-page/tours/directory.blade.php`

- `GET /tour/{slug}`
- Route name: `view.tour-detail`
- Controller: `ToursController::view_tour_detail`
- Previous view: `resources/views/frontend/tours/detail-modern.blade.php`
- Active view: `resources/views/frontend/landing-page/tours/detail.blade.php`

Previous tours asset source paths:

- `resources/frontend/js/pages/tour-packages-index.js`
- `resources/frontend/js/pages/tour-detail.js`
- `resources/frontend/scss/pages/tour-packages-index-entry.scss`
- `resources/frontend/scss/pages/tour-packages-directory.scss`
- `resources/frontend/scss/pages/tour-packages-index.scss`
- `resources/frontend/scss/pages/tour-detail-entry.scss`
- `resources/frontend/scss/pages/tour-detail.scss`

Target tours assets:

- `resources/frontend/js/landing-page/tours/index.js`
- `resources/frontend/js/landing-page/tours/detail.js`
- `resources/frontend/scss/landing-page/tours/index-entry.scss`
- `resources/frontend/scss/landing-page/tours/_directory.scss`
- `resources/frontend/scss/landing-page/tours/detail-entry.scss`
- `resources/frontend/scss/landing-page/tours/_detail.scss`

Known references checked during tours migration:

- `routes/web.php`
- `app/Http/Controllers/FrontEndController.php`
- `app/Http/Controllers/ToursController.php`
- `webpack.mix.js`
- `tests/Feature/ProjectStructureStandardTest.php`
- `tests/Feature/FrontendOrderModalStandardTest.php`
- `tests/Feature/TourDetailGuestManifestTest.php`

## Accommodations Domain Inventory

Current active public routes:

- `GET /accommodations`
- Route name: `view.accommodation-service`
- Controller: `FrontEndController::accommodation_service`
- Previous view: `resources/views/frontend/accommodations/index.blade.php`
- Active view: `resources/views/frontend/landing-page/accommodations/index.blade.php`

- `GET /accommodation/{code}`
- Route name: `view.accommodation-detail`
- Controller: `FrontEndController::accommodation_detail`
- Previous view: `resources/views/frontend/accommodations/detail.blade.php`
- Active view: `resources/views/frontend/landing-page/accommodations/detail.blade.php`

Previous accommodations asset source paths:

- `resources/frontend/js/pages/accommodations-index.js`
- `resources/frontend/js/pages/accommodation-detail.js`
- `resources/frontend/scss/pages/accommodations-index-entry.scss`
- `resources/frontend/scss/pages/accommodations-index.scss`
- `resources/frontend/scss/pages/accommodation-detail-entry.scss`
- `resources/frontend/scss/pages/accommodation-detail.scss`

Target accommodations assets:

- `resources/frontend/js/landing-page/accommodations/index.js`
- `resources/frontend/js/landing-page/accommodations/detail.js`
- `resources/frontend/scss/landing-page/accommodations/index-entry.scss`
- `resources/frontend/scss/landing-page/accommodations/_index.scss`
- `resources/frontend/scss/landing-page/accommodations/detail-entry.scss`
- `resources/frontend/scss/landing-page/accommodations/_detail.scss`

Known references checked during accommodations migration:

- `routes/web.php`
- `app/Http/Controllers/FrontEndController.php`
- `webpack.mix.js`
- `tests/Feature/ProjectStructureStandardTest.php`

Completed accommodations-related booking flow:

- `resources/frontend/js/pages/hotel-availability.js`
- `resources/frontend/scss/pages/hotel-availability-entry.scss`
- `resources/frontend/scss/pages/hotel-availability.scss`
- Target: `resources/frontend/js/home/booking/hotel-availability.js`
- Target: `resources/frontend/scss/home/booking/hotel-availability-entry.scss`
- Target: `resources/frontend/scss/home/booking/_hotel-availability.scss`
- Reason: check-price/availability flow is driven by `HotelsController` routes and belongs to authenticated frontend booking.

## Hotel Availability / Check Price Inventory

Current active authenticated routes:

- `GET /hotel-price-{code}`
- Route name: `view.hotel-prices.page`
- Controller: `HotelsController::hotel_price_page`
- Previous view: `resources/views/main/hotelavailability.blade.php`
- Active view: `resources/views/frontend/home/booking/hotel-availability.blade.php`

- `POST /accommodation/price-{code}`
- Route name: `view.accommodation-prices`
- Controller: `HotelsController::hotel_price`
- Behavior: redirects to `view.hotel-prices.page` with selected stay dates.

- `POST /hotel-price-{code}`
- Route name: `view.hotel-prices`
- Controller: `HotelsController::hotel_price`
- Behavior: redirects to `view.hotel-prices.page` with selected stay dates.

Previous hotel availability asset source paths:

- `resources/frontend/js/pages/hotel-availability.js`
- `resources/frontend/scss/pages/hotel-availability-entry.scss`
- `resources/frontend/scss/pages/hotel-availability.scss`

Target hotel availability assets:

- `resources/frontend/js/home/booking/hotel-availability.js`
- `resources/frontend/scss/home/booking/hotel-availability-entry.scss`
- `resources/frontend/scss/home/booking/_hotel-availability.scss`

Known references checked during hotel availability migration:

- `routes/web.php`
- `app/Http/Controllers/HotelsController.php`
- `webpack.mix.js`
- `tests/Feature/ProjectStructureStandardTest.php`

## Static Landing Pages - About & Contact Inventory

Current active public routes:

- `GET /about-us`
- Route name: `about-us`
- Controller: `HomeController::about_us`
- Previous view: `resources/views/home/landing-page/about.blade.php`
- Active view: `resources/views/frontend/landing-page/about/index.blade.php`

- `GET /contact-us`
- Route name: `contact-us`
- Controller: `HomeController::contact_us`
- Previous view: `resources/views/home/landing-page/contact.blade.php`
- Active view: `resources/views/frontend/landing-page/contact/index.blade.php`

Previous static landing page asset source paths:

- `resources/frontend/scss/pages/about-page-entry.scss`
- `resources/frontend/scss/pages/about-page.scss`
- `resources/frontend/scss/pages/contact-page-entry.scss`
- `resources/frontend/scss/pages/contact-page.scss`

Target static landing page assets:

- `resources/frontend/scss/landing-page/about/index-entry.scss`
- `resources/frontend/scss/landing-page/about/_index.scss`
- `resources/frontend/scss/landing-page/contact/index-entry.scss`
- `resources/frontend/scss/landing-page/contact/_index.scss`

Known references checked during static landing page migration:

- `routes/web.php`
- `app/Http/Controllers/HomeController.php`
- `app/Providers/AppServiceProvider.php`
- `webpack.mix.js`
- `tests/Feature/ProjectStructureStandardTest.php`

## Next Execution Plan

### Step 1 - Transport Public Views

- [x] Copy `resources/views/home/landing-page/transport.blade.php` to `resources/views/frontend/landing-page/transports/index.blade.php`.
- [x] Copy `resources/views/home/transports/detail.blade.php` to `resources/views/frontend/landing-page/transports/detail.blade.php`.
- [x] Update `FrontEndController::transport_service` to return `frontend.landing-page.transports.index`.
- [x] Update `HomeController::show_transport` to return `frontend.landing-page.transports.detail`.
- [x] Keep old views temporarily until tests/build pass.
- [x] Run route/view tests.

### Step 2 - Transport Public Assets

- [x] Copy `transportations-index.js` to `frontend/js/landing-page/transports/index.js`.
- [x] Copy `transport-detail.js` to `frontend/js/landing-page/transports/detail.js`.
- [x] Copy `transportations-index-entry.scss` to `frontend/scss/landing-page/transports/index-entry.scss`.
- [x] Copy `transportations-index.scss` to `frontend/scss/landing-page/transports/_index.scss`.
- [x] Copy `transport-detail-entry.scss` to `frontend/scss/landing-page/transports/detail-entry.scss`.
- [x] Copy `transport-detail.scss` to `frontend/scss/landing-page/transports/_detail.scss`.
- [x] Update imports inside new entry SCSS files.
- [x] Update `webpack.mix.js` to compile from new paths.
- [x] Keep Blade `mix()` references unchanged because public output filenames are preserved.
- [x] Run `npm run development`.

### Step 3 - Transport Cleanup

- [x] Confirm no active reference uses old transport views/assets.
- [x] Remove old transport views/assets or mark deprecated if still needed temporarily.
- [x] Update documentation with completed transport migration.
- [x] Run relevant feature tests and build.

### Step 4 - Activities Public Views

- [x] Copy `resources/views/frontend/activities/index.blade.php` to `resources/views/frontend/landing-page/activities/index.blade.php`.
- [x] Copy `resources/views/frontend/activities/detail.blade.php` to `resources/views/frontend/landing-page/activities/detail.blade.php`.
- [x] Update `FrontEndController::activity_services` to return `frontend.landing-page.activities.index`.
- [x] Update `FrontEndController::activity_detail` to return `frontend.landing-page.activities.detail`.
- [x] Run route/view tests before cleanup.

### Step 5 - Activities Public Assets

- [x] Copy `activities-index.js` to `frontend/js/landing-page/activities/index.js`.
- [x] Copy `activity-detail.js` to `frontend/js/landing-page/activities/detail.js`.
- [x] Copy `activities-index-entry.scss` to `frontend/scss/landing-page/activities/index-entry.scss`.
- [x] Copy `activities-index.scss` to `frontend/scss/landing-page/activities/_index.scss`.
- [x] Copy `activity-detail-entry.scss` to `frontend/scss/landing-page/activities/detail-entry.scss`.
- [x] Copy `activity-detail.scss` to `frontend/scss/landing-page/activities/_detail.scss`.
- [x] Update imports inside new entry SCSS files.
- [x] Update `webpack.mix.js` to compile from new paths.
- [x] Keep Blade `mix()` references unchanged because public output filenames are preserved.

### Step 6 - Activities Cleanup

- [x] Confirm no active reference uses old activities views/assets.
- [x] Remove old activities views/assets or mark deprecated if still needed temporarily.
- [x] Update documentation with completed activities migration.
- [x] Run relevant feature tests and build.

### Step 7 - Tours Public Views

- [x] Copy `resources/views/frontend/tours/directory.blade.php` to `resources/views/frontend/landing-page/tours/directory.blade.php`.
- [x] Copy `resources/views/frontend/tours/detail-modern.blade.php` to `resources/views/frontend/landing-page/tours/detail.blade.php`.
- [x] Update `FrontEndController::tour_package_services` to return `frontend.landing-page.tours.directory`.
- [x] Update `ToursController::view_tour_detail` to return `frontend.landing-page.tours.detail`.
- [x] Run route/view tests before cleanup.
- [x] Keep legacy login-user `resources/views/frontend/tours/index.blade.php` and partials because they are a separate active route group.

### Step 8 - Tours Public Assets

- [x] Copy `tour-packages-index.js` to `frontend/js/landing-page/tours/index.js`.
- [x] Copy `tour-detail.js` to `frontend/js/landing-page/tours/detail.js`.
- [x] Copy `tour-packages-index-entry.scss` to `frontend/scss/landing-page/tours/index-entry.scss`.
- [x] Copy `tour-packages-directory.scss` to `frontend/scss/landing-page/tours/_directory.scss`.
- [x] Copy `tour-detail-entry.scss` to `frontend/scss/landing-page/tours/detail-entry.scss`.
- [x] Copy `tour-detail.scss` to `frontend/scss/landing-page/tours/_detail.scss`.
- [x] Update imports inside new entry SCSS files.
- [x] Update `webpack.mix.js` to compile from new paths.
- [x] Keep Blade `mix()` references unchanged because public output filenames are preserved.

### Step 9 - Tours Cleanup

- [x] Confirm no active reference uses old modern tours views/assets.
- [x] Remove old modern tours views/assets or mark deprecated if still needed temporarily.
- [x] Update documentation with completed tours migration.
- [x] Run relevant feature tests and build.

### Step 10 - Accommodations Public Views

- [x] Copy `resources/views/frontend/accommodations/index.blade.php` to `resources/views/frontend/landing-page/accommodations/index.blade.php`.
- [x] Copy `resources/views/frontend/accommodations/detail.blade.php` to `resources/views/frontend/landing-page/accommodations/detail.blade.php`.
- [x] Update `FrontEndController::accommodation_service` to return `frontend.landing-page.accommodations.index`.
- [x] Update `FrontEndController::accommodation_detail` to return `frontend.landing-page.accommodations.detail`.
- [x] Run route/view tests before cleanup.

### Step 11 - Accommodations Public Assets

- [x] Copy `accommodations-index.js` to `frontend/js/landing-page/accommodations/index.js`.
- [x] Copy `accommodation-detail.js` to `frontend/js/landing-page/accommodations/detail.js`.
- [x] Copy `accommodations-index-entry.scss` to `frontend/scss/landing-page/accommodations/index-entry.scss`.
- [x] Copy `accommodations-index.scss` to `frontend/scss/landing-page/accommodations/_index.scss`.
- [x] Copy `accommodation-detail-entry.scss` to `frontend/scss/landing-page/accommodations/detail-entry.scss`.
- [x] Copy `accommodation-detail.scss` to `frontend/scss/landing-page/accommodations/_detail.scss`.
- [x] Update imports inside new entry SCSS files.
- [x] Update `webpack.mix.js` to compile from new paths.
- [x] Keep Blade `mix()` references unchanged because public output filenames are preserved.

### Step 12 - Accommodations Cleanup

- [x] Confirm no active reference uses old accommodations views/assets.
- [x] Remove old accommodations views/assets or mark deprecated if still needed temporarily.
- [x] Update documentation with completed accommodations migration.
- [x] Run relevant feature tests and build.

### Step 13 - Hotel Availability Booking View

- [x] Copy `resources/views/main/hotelavailability.blade.php` to `resources/views/frontend/home/booking/hotel-availability.blade.php`.
- [x] Update `HotelsController::renderHotelPricePage` to return `frontend.home.booking.hotel-availability`.
- [x] Add `$errors` fallback in the migrated view so alerts remain robust in route/view tests.
- [x] Run route/view tests before cleanup.

### Step 14 - Hotel Availability Booking Assets

- [x] Copy `hotel-availability.js` to `frontend/js/home/booking/hotel-availability.js`.
- [x] Copy `hotel-availability-entry.scss` to `frontend/scss/home/booking/hotel-availability-entry.scss`.
- [x] Copy `hotel-availability.scss` to `frontend/scss/home/booking/_hotel-availability.scss`.
- [x] Update imports inside new entry SCSS file.
- [x] Update `webpack.mix.js` to compile from new paths.
- [x] Keep Blade `mix()` references unchanged because public output filenames are preserved.

### Step 15 - Hotel Availability Cleanup

- [x] Confirm no active reference uses old hotel availability view/assets.
- [x] Remove old hotel availability view/assets or mark deprecated if still needed temporarily.
- [x] Update documentation with completed hotel availability migration.
- [x] Run relevant feature tests and build.

### Step 16 - Static Landing Page Views

- [x] Copy `resources/views/home/landing-page/about.blade.php` to `resources/views/frontend/landing-page/about/index.blade.php`.
- [x] Copy `resources/views/home/landing-page/contact.blade.php` to `resources/views/frontend/landing-page/contact/index.blade.php`.
- [x] Update `HomeController::about_us` to return `frontend.landing-page.about.index`.
- [x] Update `HomeController::contact_us` to return `frontend.landing-page.contact.index`.
- [x] Update `AppServiceProvider` view composer for About page business profile data.
- [x] Run route/view tests before cleanup.

### Step 17 - Static Landing Page Assets

- [x] Copy `about-page-entry.scss` to `frontend/scss/landing-page/about/index-entry.scss`.
- [x] Copy `about-page.scss` to `frontend/scss/landing-page/about/_index.scss`.
- [x] Copy `contact-page-entry.scss` to `frontend/scss/landing-page/contact/index-entry.scss`.
- [x] Copy `contact-page.scss` to `frontend/scss/landing-page/contact/_index.scss`.
- [x] Update imports inside new entry SCSS files.
- [x] Update `webpack.mix.js` to compile from new paths.
- [x] Keep Blade `mix()` references unchanged because public output filenames are preserved.

### Step 18 - Static Landing Page Cleanup

- [x] Confirm no active reference uses old About/Contact views/assets.
- [x] Remove old About/Contact views/assets or mark deprecated if still needed temporarily.
- [x] Update documentation with completed About/Contact migration.
- [x] Run relevant feature tests and build.

## Public Policies Domain Inventory

Current active public routes:

- `GET /terms-and-conditions`
- Route name: `terms-and-conditions`
- Controller: `TermAndConditionController::terms_and_conditions`
- View target: `resources/views/frontend/landing-page/policies/terms-and-conditions.blade.php`
- `GET /privacy-policy`
- Route name: `privacy-policy`
- Controller: `TermAndConditionController::privacy_policy`
- View target: `resources/views/frontend/landing-page/policies/privacy-policy.blade.php`
- `GET /faq`
- Route name: `faq`
- Controller: `TermAndConditionController::faq`
- View target: `resources/views/frontend/landing-page/policies/faq.blade.php`
- `GET /help`
- Route name: `help`
- Controller: `TermAndConditionController::faq`
- View target: `resources/views/frontend/landing-page/policies/faq.blade.php`

Legacy policy manager flow retained:

- `TermAndConditionController::index`
- `TermAndConditionController::v_privacy_policy`
- `resources/views/privacy-policy/privacy-policy.blade.php`
- `resources/views/privacy-policy/term-and-condition.blade.php`
- `resources/views/privacy-policy/partials/public-policy-page.blade.php`
- `resources/views/privacy-policy/partials/policy-manager.blade.php`
- `resources/views/privacy-policy/partials/policy-modal.blade.php`

### Step 19 - Public Policy Views

- [x] Create `resources/views/frontend/landing-page/policies`.
- [x] Move active public Terms, Privacy, FAQ wrappers to `frontend/landing-page/policies`.
- [x] Move shared public policy page partial to `frontend/landing-page/policies/partials`.
- [x] Update `TermAndConditionController` public route methods to return `frontend.landing-page.policies.*` views.
- [x] Keep legacy privacy manager views in place because they are still used by separate controller methods.
- [x] Run route/view tests before cleanup.

### Step 20 - Public Policy Assets

- [x] Copy `public-policy-entry.scss` to `frontend/scss/landing-page/policies/index-entry.scss`.
- [x] Copy `public-policy.scss` to `frontend/scss/landing-page/policies/_index.scss`.
- [x] Update imports inside the new entry SCSS file.
- [x] Update `webpack.mix.js` to compile from the new source path while preserving the existing public output filename.
- [x] Keep Blade `mix()` references unchanged because public output filenames are preserved.

### Step 21 - Public Policy Cleanup

- [x] Confirm active public route methods use the new policy views.
- [x] Remove legacy Terms and FAQ public wrappers that are no longer referenced.
- [x] Remove old public policy SCSS source files after Mix uses the new source path.
- [x] Retain legacy privacy public wrapper and old shared partial until the legacy `v_privacy_policy` flow is migrated or removed.
- [x] Update documentation with completed Public Policies migration.
- [x] Run relevant feature tests and build.

## Authenticated Profile Domain Inventory

Current active authenticated route:

- `GET /profile`
- Route name: `profile`
- Middleware: `auth`
- Controller: `ProfileController::profile`
- View target: `resources/views/frontend/home/profile/index.blade.php`
- JS source: `resources/frontend/js/home/profile/index.js`
- SCSS source: `resources/frontend/scss/home/profile/index-entry.scss`

### Step 22 - Profile View

- [x] Copy `resources/views/main/profile.blade.php` to `resources/views/frontend/home/profile/index.blade.php`.
- [x] Update `ProfileController::profile` to return `frontend.home.profile.index`.
- [x] Keep route URL and route name unchanged for compatibility.
- [x] Run route/view tests before cleanup.

### Step 23 - Profile Assets

- [x] Copy `profile.js` to `frontend/js/home/profile/index.js`.
- [x] Copy `profile-entry.scss` to `frontend/scss/home/profile/index-entry.scss`.
- [x] Copy `profile.scss` to `frontend/scss/home/profile/_index.scss`.
- [x] Update imports inside the new entry SCSS file.
- [x] Update `webpack.mix.js` to compile from the new source paths while preserving existing public output filenames.
- [x] Keep Blade `mix()` references unchanged because public output filenames are preserved.

### Step 24 - Profile Cleanup

- [x] Confirm no active reference uses old profile view/assets.
- [x] Remove old profile view/assets.
- [x] Update documentation with completed Profile migration.
- [x] Run relevant feature tests and build.

## Authenticated Orders Dashboard Inventory

Current active authenticated route:

- `GET /orders`
- Route name: `view.orders`
- Middleware: `auth`, `profile.complete`, `approve`
- Controller: `OrderController::index`
- View target: `resources/views/frontend/home/orders/index.blade.php`
- JS source: `resources/frontend/js/home/orders/index.js`
- SCSS source: `resources/frontend/scss/home/orders/index-entry.scss`

Legacy/deferred order flows retained for separate migration:

- Legacy edit wrappers under `resources/views/order/*` for hotel, villa, transport, room, optional service, and wedding flows.

### Step 25 - Orders Dashboard View

- [x] Copy `resources/views/main/order.blade.php` to `resources/views/frontend/home/orders/index.blade.php`.
- [x] Update `OrderController::index` to return `frontend.home.orders.index`.
- [x] Update `MenuController::index` legacy dashboard reference to return `frontend.home.orders.index`.
- [x] Keep route URL and route name unchanged for compatibility.
- [x] Run route/view tests before cleanup.

### Step 26 - Orders Dashboard Assets

- [x] Copy `frontend-orders.js` to `frontend/js/home/orders/index.js`.
- [x] Copy `frontend-orders-entry.scss` to `frontend/scss/home/orders/index-entry.scss`.
- [x] Copy `frontend-orders.scss` to `frontend/scss/home/orders/_index.scss`.
- [x] Update imports inside the new entry SCSS file.
- [x] Update `webpack.mix.js` to compile from the new source paths while preserving existing public output filenames.
- [x] Keep Blade `mix()` references unchanged because public output filenames are preserved.

### Step 27 - Orders Dashboard Cleanup

- [x] Confirm no active reference uses old Orders Dashboard view/assets.
- [x] Update `FrontendOrdersDashboardVisibilityTest` to inspect the new view path.
- [x] Remove old Orders Dashboard view/assets.
- [x] Update documentation with completed Orders Dashboard migration.
- [x] Run relevant feature tests and build.

## Authenticated Order Detail Inventory

Current active authenticated route:

- `GET /order-{id}`
- Route name: `view.detail-order`
- Middleware: `auth`, `profile.complete`, `approve`
- Controller: `OrderController::detail_order`
- View target: `resources/views/frontend/home/orders/detail.blade.php`
- JS source: `resources/frontend/js/home/orders/detail.js`
- SCSS source: `resources/frontend/scss/home/orders/detail-entry.scss`

Shared output retained for compatibility:

- CSS output: `public/build/frontend/css/pages/order-detail-entry.css`
- JS output: `public/build/frontend/js/pages/order-detail.js`
- These output files remain shared by modern hotel, villa, tour, transport, and invoice detail views.

### Step 28 - Order Detail View

- [x] Copy `resources/views/main/orderdetail.blade.php` to `resources/views/frontend/home/orders/detail.blade.php`.
- [x] Update `OrderController::detail_order` to return `frontend.home.orders.detail`.
- [x] Pass `optionalrates` to the detail view because the legacy template already uses it.
- [x] Keep route URL and route name unchanged for compatibility.
- [x] Run route/view tests before cleanup.

### Step 29 - Order Detail Assets

- [x] Copy `order-detail.js` to `frontend/js/home/orders/detail.js`.
- [x] Copy `order-detail-entry.scss` to `frontend/scss/home/orders/detail-entry.scss`.
- [x] Copy `order-detail.scss` to `frontend/scss/home/orders/_detail.scss`.
- [x] Update imports inside the new entry SCSS file.
- [x] Update `webpack.mix.js` to compile from the new source paths while preserving existing public output filenames.
- [x] Keep Blade `mix()` references unchanged because public output filenames are preserved.

### Step 30 - Order Detail Cleanup

- [x] Confirm no active reference uses old Order Detail view/assets.
- [x] Remove old Order Detail view/assets.
- [x] Update documentation with completed Order Detail migration.
- [x] Run relevant feature tests and build.

## Authenticated Orders History Inventory

Current active authenticated route:

- `GET /orders/history`
- Route name: `orders.history`
- Middleware: `auth`, `profile.complete`, `approve`
- Controller: `OrderController::order_history`
- View target: `resources/views/frontend/home/orders/history.blade.php`
- Shared SCSS output: `public/build/frontend/css/pages/frontend-orders-entry.css`

### Step 31 - Orders History View

- [x] Copy `resources/views/layouts/order-history.blade.php` to `resources/views/frontend/home/orders/history.blade.php`.
- [x] Update `OrderController::order_history` to return `frontend.home.orders.history`.
- [x] Keep route URL and route name unchanged for compatibility.
- [x] Keep shared dashboard stylesheet output unchanged because history uses the same order dashboard visual system.
- [x] Run route/view tests before cleanup.

### Step 32 - Orders History Cleanup

- [x] Confirm no active reference uses old Orders History view.
- [x] Remove old Orders History view.
- [x] Update documentation with completed Orders History migration.
- [x] Run relevant feature tests and build.

## Authenticated Tour Order Edit Inventory

Current active authenticated route:

- `GET /edit-order-tour/{id}`
- Route name: `view.edit-order-tour`
- Middleware: `auth`, `profile.complete`, `approve`
- Controller: `OrderController::edit_order_tour`
- View target: `resources/views/frontend/home/orders/edit-tour.blade.php`
- JS source: `resources/frontend/js/home/orders/edit.js`
- Shared SCSS output: `public/build/frontend/css/pages/order-detail-entry.css`

### Step 33 - Tour Order Edit View

- [x] Copy `resources/views/frontend/orders/edit-order-tour.blade.php` to `resources/views/frontend/home/orders/edit-tour.blade.php`.
- [x] Update `OrderController::edit_order_tour` to return `frontend.home.orders.edit-tour`.
- [x] Keep route URL and route name unchanged for compatibility.
- [x] Keep shared order detail stylesheet output unchanged because the edit view uses the same order detail visual system.
- [x] Run route/view tests before cleanup.

### Step 34 - Tour Order Edit Asset

- [x] Copy `order-edit.js` to `frontend/js/home/orders/edit.js`.
- [x] Update `webpack.mix.js` to compile from the new source path while preserving the existing public output filename.
- [x] Keep Blade `mix()` reference unchanged because public output filename is preserved.

### Step 35 - Tour Order Edit Cleanup

- [x] Confirm no active reference uses old Tour Order Edit view/assets.
- [x] Remove old Tour Order Edit view/assets.
- [x] Update documentation with completed Tour Order Edit migration.
- [x] Run relevant feature tests and build.

## Legacy Order Edit Wrapper Inventory

Current active authenticated routes using the wrapper:

- `GET /edit-order-hotel/{id}`
- Route name: `view.edit-order-hotel`
- Controller: `OrderController::edit_order_hotel`
- `GET /edit-order-villa/{id}`
- Route name: `view.edit-order-villa`
- Controller: `OrderController::edit_order_villa`
- `GET /edit-order-transport/{id}`
- Route name: `view.edit-order-transport`
- Controller: `OrderController::edit_order_transport`
- View target: `resources/views/frontend/home/orders/edit-legacy.blade.php`

Deferred partials retained for service-by-service migration:

- None. Active legacy order views have been moved or retired.

### Step 36 - Legacy Order Edit Wrapper View

- [x] Copy `resources/views/order/user-edit-order.blade.php` to `resources/views/frontend/home/orders/edit-legacy.blade.php`.
- [x] Update hotel, villa, and transport edit controller methods to return `frontend.home.orders.edit-legacy`.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Keep legacy service partial includes unchanged for a separate service-by-service migration.
- [x] Run route/view structure tests before cleanup.

### Step 37 - Legacy Order Edit Wrapper Cleanup

- [x] Confirm no active reference uses old `order.user-edit-order` wrapper.
- [x] Remove old wrapper view.
- [x] Update documentation with completed Legacy Order Edit Wrapper migration.
- [x] Run relevant feature tests and build.

## Transport Order Edit Partial Inventory

Current active wrapper:

- Wrapper: `resources/views/frontend/home/orders/edit-legacy.blade.php`
- Service branch: `Transport`
- Partial target: `resources/views/frontend/home/orders/partials/edit-transport.blade.php`

### Step 38 - Transport Order Edit Partial

- [x] Create `resources/views/frontend/home/orders/partials`.
- [x] Copy `resources/views/order/edit-order-transport.blade.php` to `resources/views/frontend/home/orders/partials/edit-transport.blade.php`.
- [x] Update `frontend.home.orders.edit-legacy` to include `frontend.home.orders.partials.edit-transport`.
- [x] Keep route URL and controller behavior unchanged for compatibility.
- [x] Run structure tests before cleanup.

### Step 39 - Transport Order Edit Partial Cleanup

- [x] Confirm no active reference uses old transport edit partial.
- [x] Remove old transport edit partial.
- [x] Update documentation with completed Transport Order Edit Partial migration.
- [x] Run relevant feature tests and build.

## Villa Order Edit Partial Inventory

Current active wrapper:

- Wrapper: `resources/views/frontend/home/orders/edit-legacy.blade.php`
- Service branch: `Private Villa`
- Partial target: `resources/views/frontend/home/orders/partials/edit-villa.blade.php`

### Step 40 - Villa Order Edit Partial

- [x] Copy `resources/views/order/edit-order-villa.blade.php` to `resources/views/frontend/home/orders/partials/edit-villa.blade.php`.
- [x] Update `frontend.home.orders.edit-legacy` to include `frontend.home.orders.partials.edit-villa`.
- [x] Keep route URL and controller behavior unchanged for compatibility.
- [x] Run structure tests before cleanup.

### Step 41 - Villa Order Edit Partial Cleanup

- [x] Confirm no active reference uses old villa edit partial.
- [x] Remove old villa edit partial.
- [x] Update documentation with completed Villa Order Edit Partial migration.
- [x] Run relevant feature tests and build.

## Hotel Order Edit Partial Inventory

Current active wrapper:

- Wrapper: `resources/views/frontend/home/orders/edit-legacy.blade.php`
- Service branches: `Hotel`, `Hotel Promo`, `Hotel Package`
- Partial target: `resources/views/frontend/home/orders/partials/edit-hotel.blade.php`

### Step 42 - Hotel Order Edit Partial

- [x] Copy `resources/views/order/edit-order-hotel.blade.php` to `resources/views/frontend/home/orders/partials/edit-hotel.blade.php`.
- [x] Update `frontend.home.orders.edit-legacy` to include `frontend.home.orders.partials.edit-hotel`.
- [x] Keep route URL and controller behavior unchanged for compatibility.
- [x] Run structure tests before cleanup.

### Step 43 - Hotel Order Edit Partial Cleanup

- [x] Confirm no active reference uses old hotel edit partial.
- [x] Remove old hotel edit partial.
- [x] Update documentation with completed Hotel Order Edit Partial migration.
- [x] Run relevant feature tests and build.

## Activity Order Edit Partial Inventory

Current active wrapper:

- Wrapper: `resources/views/frontend/home/orders/edit-legacy.blade.php`
- Service branch: `Activity`
- Partial target: `resources/views/frontend/home/orders/partials/edit-activity.blade.php`

### Step 44 - Activity Order Edit Partial

- [x] Copy `resources/views/order/edit-order-activity.blade.php` to `resources/views/frontend/home/orders/partials/edit-activity.blade.php`.
- [x] Update `frontend.home.orders.edit-legacy` to include `frontend.home.orders.partials.edit-activity`.
- [x] Keep route URL and controller behavior unchanged for compatibility.
- [x] Run structure tests before cleanup.

### Step 45 - Activity Order Edit Partial Cleanup

- [x] Confirm no active reference uses old activity edit partial.
- [x] Remove old activity edit partial.
- [x] Update documentation with completed Activity Order Edit Partial migration.
- [x] Run relevant feature tests and build.

## Additional Charge Order Edit Inventory

Current active authenticated route:

- `GET /edit-order-additional-charge/{id}`
- Route name: `view.edit-order-additional-charge`
- Controller: `OrderController::edit_order_additional_charge`
- View target: `resources/views/frontend/home/orders/edit-additional-charge.blade.php`

### Step 46 - Additional Charge Order Edit View

- [x] Copy `resources/views/order/edit-order-additional-charge.blade.php` to `resources/views/frontend/home/orders/edit-additional-charge.blade.php`.
- [x] Update `OrderController::edit_order_additional_charge` to return `frontend.home.orders.edit-additional-charge`.
- [x] Keep route URL and route name unchanged for compatibility.
- [x] Run route/view tests before cleanup.

### Step 47 - Additional Charge Order Edit Cleanup

- [x] Confirm no active reference uses old additional charge edit view.
- [x] Remove old additional charge edit view.
- [x] Update documentation with completed Additional Charge Order Edit migration.
- [x] Run relevant feature tests and build.

## Legacy Optional Service Order Edit Cleanup Inventory

Current status:

- Legacy file: `resources/views/order/edit-order-optional-service.blade.php`
- Active route: none found.
- Active controller view target: none found.
- Active include: none found.
- Replacement flow: `resources/views/frontend/home/orders/edit-additional-charge.blade.php`

### Step 48 - Legacy Optional Service Order Edit Cleanup

- [x] Confirm `edit-order-optional-service` has no active route in `routes/web.php`.
- [x] Confirm `OrderController` no longer returns `order.edit-order-optional-service`.
- [x] Confirm no active Blade include uses `order.edit-order-optional-service`.
- [x] Remove orphan legacy view from `resources/views/order`.
- [x] Add structure test to keep the orphan view retired.
- [x] Run relevant feature tests and build.

## Wedding Order Frontend Views Inventory

Current active authenticated routes:

- `GET /detail-order-wedding-{orderno}`
- Route name: `view.detail-order-wedding`
- Controller: `OrderWeddingController::detail_order_wedding`
- View target: `resources/views/frontend/home/orders/weddings/detail.blade.php`
- `GET /edit-order-wedding-{orderno}`
- Route name: `view.edit-order-wedding`
- Controller: `OrderWeddingController::edit_order_wedding`
- View target: `resources/views/frontend/home/orders/weddings/edit.blade.php`

Grouped legacy partials moved:

- `resources/views/frontend/home/orders/weddings/partials/order_wedding_decoration.blade.php`
- `resources/views/frontend/home/orders/weddings/partials/order_wedding_dinner_venue.blade.php`
- `resources/views/frontend/home/orders/weddings/partials/order_wedding_documentation.blade.php`
- `resources/views/frontend/home/orders/weddings/partials/order_wedding_entertainment.blade.php`
- `resources/views/frontend/home/orders/weddings/partials/order_wedding_fixed_service.blade.php`
- `resources/views/frontend/home/orders/weddings/partials/order_wedding_makeup.blade.php`
- `resources/views/frontend/home/orders/weddings/partials/order_wedding_other.blade.php`
- `resources/views/frontend/home/orders/weddings/partials/order_wedding_room.blade.php`
- `resources/views/frontend/home/orders/weddings/partials/order_wedding_transport.blade.php`
- `resources/views/frontend/home/orders/weddings/partials/order_wedding_venues.blade.php`

### Step 49 - Wedding Order Frontend Views Batch

- [x] Copy active wedding edit/detail views into `resources/views/frontend/home/orders/weddings`.
- [x] Copy legacy wedding partial group into `resources/views/frontend/home/orders/weddings/partials`.
- [x] Update `OrderWeddingController` to return the new frontend home wedding views.
- [x] Update backup wedding form includes to use the new partial namespace.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Run route/view structure tests before cleanup.

### Step 50 - Wedding Order Frontend Views Cleanup

- [x] Confirm no active controller returns old wedding edit/detail view names.
- [x] Remove old wedding edit/detail views and grouped legacy partials from `resources/views/order`.
- [x] Remove orphan `edit-order-wedding.blade copy.php`.
- [x] Update documentation with completed Wedding Order frontend batch migration.
- [x] Run relevant feature tests and build.

## Service Order Detail Legacy Views Inventory

Current active authenticated routes using this wrapper:

- `GET /detail-order-hotel/{id}`
- Route name: `view.detail-order-hotel`
- Controller: `OrderController::detail_order_hotel`
- `GET /detail-order-villa/{id}`
- Route name: `view.detail-order-villa`
- Controller: `OrderController::detail_order_villa`
- `GET /detail-order-transport/{id}`
- Route name: `view.detail-order-transport`
- Controller: `OrderController::detail_order_transport`
- View target: `resources/views/frontend/home/orders/details/legacy.blade.php`

Grouped detail views moved:

- `resources/views/frontend/home/orders/details/activity.blade.php`
- `resources/views/frontend/home/orders/details/villa.blade.php`
- `resources/views/frontend/home/orders/details/transport-legacy.blade.php`

Grouped shared partials moved:

- `resources/views/frontend/home/orders/details/partials/hotel-detail-modern.blade.php`
- `resources/views/frontend/home/orders/details/partials/hotel-detail-modern-addons.blade.php`
- `resources/views/frontend/home/orders/details/partials/hotel-detail-modern-modals.blade.php`
- `resources/views/frontend/home/orders/details/partials/hotel-detail-modern-price.blade.php`
- `resources/views/frontend/home/orders/details/partials/hotel-detail-modern-sidebar.blade.php`
- `resources/views/frontend/home/orders/details/partials/invoice-action-buttons.blade.php`
- `resources/views/frontend/home/orders/details/partials/invoice-preview-modal.blade.php`
- `resources/views/frontend/home/orders/details/partials/invoice-preview-modal-compact.blade.php`
- `resources/views/frontend/home/orders/details/partials/legacy-order-payment-sidebar.blade.php`

### Step 51 - Service Order Detail Legacy Views Batch

- [x] Copy legacy detail wrapper to `resources/views/frontend/home/orders/details/legacy.blade.php`.
- [x] Copy Activity, Villa, and legacy Transport detail views to `resources/views/frontend/home/orders/details`.
- [x] Copy shared hotel detail, payment, and invoice partials to `resources/views/frontend/home/orders/details/partials`.
- [x] Update `OrderController` detail methods to return `frontend.home.orders.details.legacy`.
- [x] Update all moved internal includes and active modern detail/payment includes to use the new shared partial namespace.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Run route/view structure tests before cleanup.

### Step 52 - Service Order Detail Legacy Views Cleanup

- [x] Confirm no active controller returns old `order.user-detail-order`.
- [x] Remove old legacy detail wrapper, Activity/Villa/Transport legacy detail views, orphan Tour detail view, and shared partials from `resources/views/order`.
- [x] Update documentation with completed Service Order Detail legacy batch migration.
- [x] Run relevant feature tests and build.

## Admin Order Helper Views Inventory

Current active backend/admin routes:

- `GET /add-additional-services-{id}`
- Controller: `OrdersAdminController::edit_additional_services`
- View target: `resources/views/backend/operations/orders/actions/add-additional-services.blade.php`
- `GET /add-order-itinerary-{id}`
- Controller: `OrdersAdminController::admin_edit_order_itinerary`
- View target: `resources/views/backend/operations/orders/actions/add-order-itinerary.blade.php`
- `GET /edit-airport-shuttle-{id}`
- Controller: `OrdersAdminController::edit_airport_shuttle`
- View target: `resources/views/backend/operations/orders/actions/edit-airport-shuttle.blade.php`

### Step 53 - Admin Order Helper Views Batch

- [x] Copy admin helper views to `resources/views/backend/operations/orders/actions`.
- [x] Update `OrdersAdminController` to return the backend operations view namespace.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Run structure tests before cleanup.

### Step 54 - Admin Order Helper Views Cleanup

- [x] Confirm no active controller returns old admin helper views from `resources/views/order`.
- [x] Remove old admin helper views from `resources/views/order`.
- [x] Update documentation with completed Admin Order Helper Views migration.
- [x] Run relevant feature tests and build.

## Remaining User Order Edit Legacy Views Inventory

Current active authenticated route:

- `GET /edit-order-room/{id}`
- Route name: `view.edit-order-room`
- Controller: `OrderController::edit_order_room`
- View target: `resources/views/frontend/home/orders/edit-room.blade.php`

Legacy views retired:

- `resources/views/order/edit-order-tour.blade.php`
- `resources/views/order/edit-order-hotel-promo.blade.php`
- `resources/views/order/view-order-hotel-promo.blade.php`
- `resources/views/order/add-order-wedding.blade.php`

### Step 55 - Remaining User Order Edit Legacy Views Batch

- [x] Copy active room edit view to `resources/views/frontend/home/orders/edit-room.blade.php`.
- [x] Update `OrderController::edit_order_room` to return `frontend.home.orders.edit-room`.
- [x] Remove stale Tour Package branch from legacy edit wrapper because Tour edit has its dedicated `frontend.home.orders.edit-tour` route/view.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Run structure tests before cleanup.

### Step 56 - Remaining User Order Edit Legacy Views Cleanup

- [x] Confirm no active controller/include references old user order edit legacy views.
- [x] Remove old active room edit view and orphan edit/promo/wedding order views from `resources/views/order`.
- [x] Update documentation with completed Remaining User Order Edit Legacy Views migration.
- [x] Run relevant feature tests and build.

## Legacy Order View Namespace Closure

Current status:

- Active Blade files under `resources/views/order`: none.
- Active controller return using `view('order.*')`: none.
- Active Blade include using `@include('order.*')`: none.
- Replacement user order namespace: `resources/views/frontend/home/orders`.
- Replacement backend operations order namespace: `resources/views/backend/operations/orders`.

### Step 57 - Legacy Order View Namespace Closure

- [x] Confirm no Blade files remain under `resources/views/order`.
- [x] Confirm previous order views are covered by frontend home or backend operations namespaces.
- [x] Add guard test to prevent reintroducing active Blade files under `resources/views/order`.
- [x] Update migration tracker with namespace closure status.
- [x] Run relevant feature tests and build.

## Wedding Order Package Sections Inventory

Current active parent view:

- Parent: `resources/views/frontend/home/orders/weddings/edit.blade.php`
- Section target: `resources/views/frontend/home/orders/weddings/sections`

Grouped sections moved:

- `accommodation.blade.php`
- `additional-services.blade.php`
- `bride.blade.php`
- `ceremony-and-decoration-venue.blade.php`
- `flight.blade.php`
- `include-services.blade.php`
- `invitations.blade.php`
- `reception-and-decoration-venue.blade.php`
- `suite-and-villa-brides.blade.php`
- `suite-and-villa-invitations.blade.php`
- `transports.blade.php`
- `wedding-detail.blade.php`
- `wedding-dinner-venue.blade.php`
- `wedding-lunch-venue.blade.php`
- `wedding-venue.blade.php`

### Step 58 - Wedding Order Package Sections Batch

- [x] Copy all wedding order package section views to `resources/views/frontend/home/orders/weddings/sections`.
- [x] Update active wedding edit view includes to use `frontend.home.orders.weddings.sections`.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Run structure tests before cleanup.

### Step 59 - Wedding Order Package Sections Cleanup

- [x] Confirm active wedding edit view no longer includes `order-wedding-package.*`.
- [x] Remove old wedding order package section views from `resources/views/order-wedding-package`.
- [x] Update documentation with completed Wedding Order Package Sections migration.
- [x] Run relevant feature tests and build.

## Modern Order Detail Views Inventory

Current active authenticated routes:

- `GET /detail-order-tour/{id}`
- Route name: `view.detail-order-tour`
- Controller: `OrderController::detail_order_tour`
- View target: `resources/views/frontend/home/orders/details/tour-modern.blade.php`
- Transport detail wrapper include target: `resources/views/frontend/home/orders/details/transport-modern.blade.php`

Grouped views moved:

- `resources/views/frontend/home/orders/details/tour-modern.blade.php`
- `resources/views/frontend/home/orders/details/tour-legacy.blade.php`
- `resources/views/frontend/home/orders/details/transport-modern.blade.php`

### Step 60 - Modern Order Detail Views Batch

- [x] Copy modern Tour detail, legacy Tour detail template, and modern Transport detail into `resources/views/frontend/home/orders/details`.
- [x] Update `OrderController::detail_order_tour` to return `frontend.home.orders.details.tour-modern`.
- [x] Update legacy order detail wrapper includes to use `frontend.home.orders.details.tour-modern` and `transport-modern`.
- [x] Update tests that inspect Tour detail templates to use the new paths.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Run structure tests before cleanup.

### Step 61 - Modern Order Detail Views Cleanup

- [x] Confirm no active controller/include references `frontend.orders.detail-order-*`.
- [x] Remove old detail views from `resources/views/frontend/orders`.
- [x] Update documentation with completed Modern Order Detail Views migration.
- [x] Run relevant feature tests and build.

## Reservation Helper Forms Inventory

Current active authenticated/internal routes:

- `ReservationController::view_add_rsv_order`
- `ReservationController::view_add_rsv_transport`
- `ReservationController::view_add_rsv_activity_tour`
- `ReservationController::view_add_itinerary`

Target namespace:

- `resources/views/backend/operations/reservations/actions`

Grouped views moved:

- `resources/views/backend/operations/reservations/actions/add-order.blade.php`
- `resources/views/backend/operations/reservations/actions/add-transport.blade.php`
- `resources/views/backend/operations/reservations/actions/add-activity-tour.blade.php`
- `resources/views/backend/operations/reservations/actions/add-itinerary.blade.php`

### Step 62 - Reservation Helper Forms Batch

- [x] Copy reservation helper forms from `resources/views/form/add_rsv_*` into backend operations reservation actions.
- [x] Update `ReservationController` view targets to `backend.operations.reservations.actions.*`.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Add structure guard test for reservation helper action views.
- [x] Run structure tests before cleanup.

### Step 63 - Reservation Helper Forms Cleanup

- [x] Confirm no active controller/include references `form.add_rsv_*`.
- [x] Remove old reservation helper views from `resources/views/form`.
- [x] Update documentation with completed Reservation Helper Forms migration.
- [x] Run relevant feature tests and build.

## Transport Admin Forms Inventory

Current active authenticated/admin routes:

- `TransportsAdminController::view_add_transport`
- `TransportsAdminController::view_edit_transport`
- `TransportsController::view_edit_galery_transport`

Target namespace:

- `resources/views/backend/operations/transports/forms`

Grouped views moved:

- `resources/views/backend/operations/transports/forms/create.blade.php`
- `resources/views/backend/operations/transports/forms/edit.blade.php`
- `resources/views/backend/operations/transports/forms/gallery-edit.blade.php`

### Step 64 - Transport Admin Forms Batch

- [x] Copy transport admin add/edit/gallery forms from `resources/views/form` into backend operations transport forms.
- [x] Update transport controllers to return `backend.operations.transports.forms.*`.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Add structure guard test for transport admin forms.
- [x] Run structure tests before cleanup.

### Step 65 - Transport Admin Forms Cleanup

- [x] Confirm no active controller/include references `form.transport*` admin form views.
- [x] Remove old transport admin form views from `resources/views/form`.
- [x] Update documentation with completed Transport Admin Forms migration.
- [x] Run relevant feature tests and build.

## Activity Admin Forms Inventory

Current active authenticated/admin routes:

- `ActivitiesAdminController::view_add_activity`
- `ActivitiesAdminController::view_edit_activity`
- `ActivitiesController::view_edit_galery_activity`

Target namespace:

- `resources/views/backend/operations/activities/forms`

Grouped views moved:

- `resources/views/backend/operations/activities/forms/create.blade.php`
- `resources/views/backend/operations/activities/forms/edit.blade.php`
- `resources/views/backend/operations/activities/forms/gallery-edit.blade.php`

### Step 66 - Activity Admin Forms Batch

- [x] Copy activity admin add/edit/gallery forms from `resources/views/form` into backend operations activity forms.
- [x] Update activity controllers to return `backend.operations.activities.forms.*`.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Add structure guard test for activity admin forms.
- [x] Run structure tests before cleanup.

### Step 67 - Activity Admin Forms Cleanup

- [x] Confirm no active controller/include references `form.activity*` admin form views.
- [x] Remove old activity admin form views from `resources/views/form`.
- [x] Update documentation with completed Activity Admin Forms migration.
- [x] Run relevant feature tests and build.

## Hotel Admin Forms Inventory

Current active authenticated/admin routes:

- `HotelsAdminController::view_add_hotel`
- `HotelsAdminController::view_edit_hotel`
- `HotelsAdminController::view_edit_galery_hotel`
- `HotelsAdminController::view_add_hotel_price`
- `HotelsAdminController::view_add_promo`
- `HotelsAdminController::view_add_room`
- `HotelsAdminController::view_edit_room`

Target namespace:

- `resources/views/backend/operations/hotels/forms`

Grouped views moved:

- `resources/views/backend/operations/hotels/forms/create.blade.php`
- `resources/views/backend/operations/hotels/forms/edit.blade.php`
- `resources/views/backend/operations/hotels/forms/gallery-edit.blade.php`
- `resources/views/backend/operations/hotels/forms/add-normal-price.blade.php`
- `resources/views/backend/operations/hotels/forms/add-promo.blade.php`
- `resources/views/backend/operations/hotels/forms/room-create.blade.php`
- `resources/views/backend/operations/hotels/forms/room-edit.blade.php`

### Step 68 - Hotel Admin Forms Batch

- [x] Copy hotel, room, price, promo, and gallery admin forms from `resources/views/form` into backend operations hotel forms.
- [x] Update `HotelsAdminController` to return `backend.operations.hotels.forms.*`.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Add structure guard test for hotel admin forms.
- [x] Run structure tests before cleanup.

### Step 69 - Hotel Admin Forms Cleanup

- [x] Confirm no active controller/include references hotel or room admin form views under `resources/views/form`.
- [x] Remove old hotel admin form views from `resources/views/form`.
- [x] Update documentation with completed Hotel Admin Forms migration.
- [x] Run relevant feature tests and build.

## Wedding Admin Forms Inventory

Current active authenticated/admin routes:

- `WeddingsController` create/edit and wedding venue create/edit entry points.
- `HotelsAdminController` hotel-scoped wedding venue create/edit entry points.
- `WeddingReceptionVenuesController::view_edit_wedding_reception_venue`
- `WeddingLunchVenuesController::view_edit_wedding_lunch_venue`
- `WeddingDinnerVenuesController` dinner venue and dinner package create/edit entry points.
- `WeddingMenuController::view_add_wedding_menu`

Target namespace:

- `resources/views/backend/operations/weddings/forms`

Grouped views moved:

- `resources/views/backend/operations/weddings/forms/create.blade.php`
- `resources/views/backend/operations/weddings/forms/edit.blade.php`
- `resources/views/backend/operations/weddings/forms/venue-create.blade.php`
- `resources/views/backend/operations/weddings/forms/venue-edit.blade.php`
- `resources/views/backend/operations/weddings/forms/reception-venue-edit.blade.php`
- `resources/views/backend/operations/weddings/forms/lunch-venue-edit.blade.php`
- `resources/views/backend/operations/weddings/forms/dinner-venue-create.blade.php`
- `resources/views/backend/operations/weddings/forms/dinner-venue-edit.blade.php`
- `resources/views/backend/operations/weddings/forms/dinner-package-create.blade.php`
- `resources/views/backend/operations/weddings/forms/dinner-package-edit.blade.php`
- `resources/views/backend/operations/weddings/forms/food-and-beverage-create.blade.php`

### Step 70 - Wedding Admin Forms Batch

- [x] Copy active wedding admin forms from `resources/views/form` into backend operations wedding forms.
- [x] Update wedding-related controllers to return `backend.operations.weddings.forms.*`.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Add structure guard test for active wedding admin forms.
- [x] Run structure tests before cleanup.

### Step 71 - Wedding Admin Forms Cleanup

- [x] Confirm no active controller/include references active wedding admin form views under `resources/views/form`.
- [x] Remove old active wedding admin form views from `resources/views/form`.
- [x] Keep inactive `wedding-planner*` legacy files for a separate retire/migration decision.
- [x] Update documentation with completed Wedding Admin Forms migration.
- [x] Run relevant feature tests and build.

## Partner Service Forms Inventory

Current active authenticated/admin routes:

- `PartnersController::view_partner_add_activity`
- `PartnersController::view_partner_add_tour`

Target namespace:

- `resources/views/backend/operations/partners/forms`

Grouped views moved:

- `resources/views/backend/operations/partners/forms/add-activity.blade.php`
- `resources/views/backend/operations/partners/forms/add-tour.blade.php`

### Step 72 - Partner Service Forms Batch

- [x] Copy active partner service forms from `resources/views/form` into backend operations partner forms.
- [x] Update `PartnersController` to return `backend.operations.partners.forms.*`.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Add structure guard test for active partner service forms.
- [x] Run structure tests before cleanup.

### Step 73 - Partner Service Forms Cleanup

- [x] Confirm no active controller/include references active partner service forms under `resources/views/form`.
- [x] Remove old active partner service form views from `resources/views/form`.
- [x] Update documentation with completed Partner Service Forms migration.
- [x] Run relevant feature tests and build.

## Frontend Order Booking Forms Inventory

Current active authenticated/user booking routes:

- `OrderController::order_hotel_normal`
- `OrderController::order_hotel_package`
- `OrderController::order_hotel_promo`
- `OrderController::order_transport`

Target namespace:

- `resources/views/frontend/home/booking/orders`

Grouped views moved:

- `resources/views/frontend/home/booking/orders/hotel-normal.blade.php`
- `resources/views/frontend/home/booking/orders/hotel-package.blade.php`
- `resources/views/frontend/home/booking/orders/hotel-promo.blade.php`
- `resources/views/frontend/home/booking/orders/transport.blade.php`

### Step 74 - Frontend Order Booking Forms Batch

- [x] Copy active user booking order forms from `resources/views/form/order-*` into `frontend/home/booking/orders`.
- [x] Update `OrderController` to return `frontend.home.booking.orders.*`.
- [x] Update frontend order modal standard tests to inspect the new template paths.
- [x] Keep route URLs and route names unchanged for compatibility.
- [x] Add structure guard test for frontend order booking forms.
- [x] Run structure tests before cleanup.

### Step 75 - Frontend Order Booking Forms Cleanup

- [x] Confirm no active controller/include references active `form.order-*` booking views.
- [x] Remove old active booking order form views from `resources/views/form`.
- [x] Resolve the previous open decision for `resources/views/form/order-transport.blade.php`: it belongs to `frontend/home/booking/orders/transport.blade.php`.
- [x] Update documentation with completed Frontend Order Booking Forms migration.
- [x] Run relevant feature tests and build.

## Legacy Form Namespace Closure Inventory

Current remaining files before closure:

- `resources/views/form/order-room.blade.php`
- `resources/views/form/order_weddings.blade.php`
- `resources/views/form/order_weddings_backup.blade.php`
- `resources/views/form/tourgaleryedit.blade.php`
- `resources/views/form/wedding-planner.blade.php`
- `resources/views/form/wedding-planner-hotel.blade.php`
- `resources/views/form/insert_hotel_price.php`

Target namespace for retained legacy wedding reference templates:

- `resources/views/frontend/home/orders/weddings/legacy`

Retained reference templates:

- `resources/views/frontend/home/orders/weddings/legacy/order-weddings.blade.php`
- `resources/views/frontend/home/orders/weddings/legacy/order-weddings-backup.blade.php`

### Step 76 - Legacy Form Namespace Closure Batch

- [x] Confirm no active controller/include references the remaining `resources/views/form` files.
- [x] Move retained wedding legacy reference templates into `frontend/home/orders/weddings/legacy`.
- [x] Update tests that inspect the wedding legacy backup template to use the new path.
- [x] Add guard test to prevent reintroducing active files under `resources/views/form`.
- [x] Run structure tests before cleanup.

### Step 77 - Legacy Form Namespace Closure Cleanup

- [x] Remove remaining inactive remnant files from `resources/views/form`.
- [x] Confirm `resources/views/form` contains no files.
- [x] Update documentation with completed legacy form namespace closure.
- [x] Run relevant feature tests and build.

## Review Views Inventory

Current active routes:

- Public review form: `GET /create-review`
- Admin reviews: `GET /admin/reviews`
- Admin wedding reviews: `GET /admin/wedding-reviews`
- Review print pages and review link generator pages.

Target namespace:

- `resources/views/frontend/home/reviews`

Grouped views moved:

- `index.blade.php`
- `wedding-index.blade.php`
- `print-reviews.blade.php`
- `print-wedding-reviews.blade.php`
- `create.blade.php`
- `create-review.blade.php`
- `create-wedding-review.blade.php`
- `review_link_form.blade.php`
- `wedding_review_link_form.blade.php`
- `layouts/app.blade.php`
- `partials/review_card.blade.php`
- `partials/review_modal.blade.php`

### Step 78 - Review Views Batch

- [x] Copy review views from `resources/views/home/reviews` into `resources/views/frontend/home/reviews`.
- [x] Update `ReviewController` to return `frontend.home.reviews.*`.
- [x] Update review form templates to extend `frontend.home.reviews.layouts.app`.
- [x] Add structure guard test for review views.
- [x] Run structure tests before cleanup.

### Step 79 - Review Views Cleanup

- [x] Confirm no active controller/include references `home.reviews.*`.
- [x] Remove old review views from `resources/views/home/reviews`.
- [x] Update documentation with completed Review Views migration.
- [x] Run relevant feature tests and build.

## Home Public Legacy Routes Inventory

Current active legacy routes:

- `GET /services`
- `GET /tour-package-service`
- `GET /tour-package-{id}`
- Legacy `HomeController::accommodation_service`
- Legacy `HomeController::show`

Target namespace:

- `resources/views/frontend/landing-page/services`
- Existing canonical landing views in `resources/views/frontend/landing-page/accommodations` and `resources/views/frontend/landing-page/tours`

Grouped changes:

- `resources/views/frontend/landing-page/services/index.blade.php`
- `HomeController::accommodation_service` delegates to the modern accommodation directory implementation.
- `HomeController::tour_package_service` delegates to the modern tour package directory implementation.
- `HomeController::show` redirects to the canonical accommodation detail route.
- `HomeController::show_tour_package` redirects to the canonical tour detail route.

### Step 80 - Home Public Legacy Routes Batch

- [x] Copy services page from `resources/views/home/landing-page/services.blade.php` to `resources/views/frontend/landing-page/services/index.blade.php`.
- [x] Update `HomeController` legacy directory methods to use canonical frontend landing-page implementations.
- [x] Update legacy detail methods to redirect to canonical detail routes instead of rendering `home.*` views.
- [x] Add structure guard test for Home public legacy routes.
- [x] Run structure tests before cleanup.

### Step 81 - Home Public Legacy Routes Cleanup

- [x] Confirm no active controller references the migrated `home.landing-page.*`, `home.hotels.detail`, or `home.tour-packages.detail` views.
- [x] Remove old Home public legacy views from `resources/views/home`.
- [x] Update documentation with completed Home Public Legacy Routes migration.
- [x] Run relevant feature tests and build.

## Home Agent And Shared Partials Inventory

Current active references:

- `AgentRegistrationController::showForm`
- `resources/views/frontend/landing-page/accommodations/detail.blade.php`
- `resources/views/layouts/app.blade.php`

Target namespaces:

- `resources/views/frontend/home/agents`
- `resources/views/frontend/shared`

Grouped views moved:

- `resources/views/frontend/home/agents/register.blade.php`
- `resources/views/frontend/shared/room-modal.blade.php`
- `resources/views/frontend/shared/footer-legacy.blade.php`

### Step 82 - Home Agent And Shared Partials Batch

- [x] Copy active agent registration view to `frontend/home/agents`.
- [x] Copy active shared room modal and legacy footer partial to `frontend/shared`.
- [x] Update controller and active includes to use frontend namespaces.
- [x] Add structure guard test for active Home agent/shared partial migration.
- [x] Run structure tests before cleanup.

### Step 83 - Home Agent And Shared Partials Cleanup

- [x] Confirm no active controller/include references migrated `home.agents.*` or `home.partials.*`.
- [x] Remove old active Home agent/shared partial views from `resources/views/home`.
- [x] Update documentation with completed Home Agent And Shared Partials migration.
- [x] Run relevant feature tests and build.

## Legacy Home Namespace Closure Inventory

Remaining files in `resources/views/home` after active route/controller migrations:

- `resources/views/home/index.blade.php`
- `resources/views/home/landing-page/index.blade.php`
- `resources/views/home/partials/spinner.blade.php`
- `resources/views/home/partials/rating-star-view.blade.php`
- `resources/views/home/partials/notifications.blade.php`
- `resources/views/home/partials/galery-modal.blade.php`
- `resources/views/home/partials/home/about.blade.php`
- `resources/views/home/partials/home/faqs.blade.php`
- `resources/views/home/partials/home/hero.blade.php`
- `resources/views/home/partials/home/hotel-promotion.blade.php`
- `resources/views/home/partials/home/services.blade.php`
- `resources/views/home/partials/home/why-us.blade.php`

Target status:

- No active Blade files remain under `resources/views/home`.
- Public landing pages use `resources/views/frontend/landing-page`.
- Logged-in frontpage/home pages use `resources/views/frontend/home`.
- Reusable legacy-compatible shared partials use `resources/views/frontend/shared`.

### Step 84 - Legacy Home Namespace Closure Batch

- [x] Confirm remaining `resources/views/home` files are not referenced by active controllers/routes.
- [x] Add structure guard test to keep `resources/views/home` empty of active Blade files.
- [x] Remove remaining legacy Home namespace Blade files as one grouped cleanup.
- [x] Run focused structure tests, full structure tests, and frontend build.

## Legacy Empty Folder And Orphan Main Cleanup

Removed empty folders after completed frontend/backend view migration:

- `resources/views/form`
- `resources/views/order`
- `resources/views/order-wedding-package`
- `resources/views/home`
- Empty pre-standard frontend folders under `resources/views/frontend/*`

Removed orphan `resources/views/main` files with no active `view('main.*')` or `@include('main.*')` references:

- `resources/views/main/bookingcode-promotion.blade.php`
- `resources/views/main/createdata.sql`
- `resources/views/main/dashboard.blade.php`
- `resources/views/main/download-data-hotel.blade.php`
- `resources/views/main/error-500.blade.php`
- `resources/views/main/error-msg.blade.php`
- `resources/views/main/loading-page.blade.php`
- `resources/views/main/test-input.blade.php`
- `resources/views/main/wedding-planner-detail.blade.php`
- `resources/views/main/weddingdetail.blade.php`
- `resources/views/main/weddingsearch.blade.php`

### Step 85 - Legacy Cleanup Batch

- [x] Verify candidate folders are empty and resolved inside the project workspace before removal.
- [x] Remove empty legacy folders left by previous migration batches.
- [x] Scan orphan `resources/views/main` candidates against `app`, `resources`, `routes`, `tests`, `docs`, `config`, and `database`.
- [x] Remove confirmed orphan/debug/legacy files from `resources/views/main`.
- [x] Add structure guard test for removed orphan main files.
- [x] Run focused structure tests, full structure tests, and frontend build.

## Tooling Warning Cleanup

### Step 86 - PHPUnit Schema Warning Cleanup

- [x] Migrate `phpunit.xml` to the PHPUnit 10.5 schema format.
- [x] Move legacy coverage include configuration into the PHPUnit 10 `<source>` section.
- [x] Remove the temporary `phpunit.xml.bak` file generated by the migrator.
- [x] Run focused PHPUnit test to confirm the deprecated XML schema warning no longer appears.

### Step 87 - VS Code Problems Diagnostics Cleanup

- [x] Replace unbound Composer constraints for `maatwebsite/excel` and `spatie/image-optimizer` with versioned constraints that match installed major versions.
- [x] Add missing Blade components used by the Livewire registration password form: `input-label`, `text-input`, and `input-error`.
- [x] Run Composer validation in strict mode.
- [x] Run PHP lint across `app`, `config`, `database`, `routes`, and `tests`.
- [x] Run `php artisan view:cache` to verify Blade/component compilation.

## Backend Phase 5 Closure

### Step 88 - Backend Admin User, Finance, And Report Batch

- [x] Move admin user views from `resources/views/admin` to `resources/views/backend/admin/users`.
- [x] Update `UsersController` to return `backend.admin.users.*` views.
- [x] Move invoice views from `resources/views/admin` to `resources/views/backend/finance/invoices`.
- [x] Update `InvoiceAdminController` to return `backend.finance.invoices.*` views.
- [x] Move download/export report views from `resources/views/main` to `resources/views/backend/reports/downloads`.
- [x] Update `DownloadDataHotelController` and PDF rendering to use `backend.reports.downloads.*` views.
- [x] Remove replaced legacy admin user, invoice, and report download views.
- [x] Add structure guard tests for backend admin users, finance invoices, and report downloads.

### Step 89 - README Structure Onboarding Cleanup

- [x] Add frontend/backend folder placement guidance to `README.md`.
- [x] Correct the production asset command from `npm run build` to `npm run prod`.
- [x] Close the README onboarding checklist in `project-structure-standard.md`.

## Guard Rules During Migration

- Do not move more than one frontend domain in one step.
- Do not remove old files until route/view tests and `npm run development` pass.
- Every moved Blade file must update all `@extends`, `@include`, `@push`, and `mix()` references.
- Every moved JS/SCSS file must update `webpack.mix.js`.
- Every completed step must update this tracker.

## Open Decisions

- [ ] Decide whether route names should stay legacy-compatible first, then rename later.
- [ ] Decide whether old public paths like `/transportations` stay unchanged. Recommendation: keep public URLs unchanged during folder migration.
- [x] Decide whether `resources/views/form/order-transport.blade.php` belongs to `frontend/home/booking` or should be deprecated after modal order standard is complete. Decision: moved to `frontend/home/booking/orders/transport.blade.php`.
