# Backend UI Standardization Roadmap

Dokumen ini adalah roadmap hidup untuk standardisasi tampilan backend `balikamitour`. Baseline visual saat ini adalah halaman `backend/admin/dashboard`; setiap pola UI yang sudah stabil harus dipindahkan ke shared style/component agar semua halaman backend memakai satu sumber style yang sama.

## Rules of Work

- [x] Gunakan `docs/backend-ui-standards.md` sebagai aturan utama style backend.
- [x] Gunakan `docs/backend-richtext-textarea-roadmap.md` sebagai checklist migrasi rich text textarea backend.
- [x] Gunakan `docs/backend-form-standardization-roadmap.md` sebagai checklist migrasi form input/dropdown/checkbox/button backend.
- [x] Gunakan halaman `resources/views/backend/admin/dashboard/index.blade.php` sebagai baseline visual KPI, panel, list, dan dashboard density.
- [x] Simpan style shared di `resources/backend/scss/components`.
- [x] Import style shared dari `resources/backend/scss/app.scss`.
- [x] Simpan markup reusable di `resources/views/components/backend`.
- [x] Jangan menambahkan visual primitive baru di SCSS halaman tanpa mengecek apakah pola itu harus menjadi shared `backend-*`.
- [x] Setiap refactor halaman backend harus memperbarui checklist roadmap ini.
- [x] Setiap shared style baru harus memiliki assertion struktur agar tidak mudah regresi.

## Phase 1 - Shared Foundation

- [x] Backend theme tokens tersedia di `resources/backend/scss/components/_backend-theme.scss`.
- [x] Hero shared tersedia di `resources/backend/scss/components/_backend-hero.scss`.
- [x] Hero component tersedia di `resources/views/components/backend/page-hero.blade.php`.
- [x] Breadcrumb/toolbar shared tersedia di `resources/backend/scss/components/_backend-breadcrumb.scss`.
- [x] Sidebar shared tersedia di `resources/backend/scss/components/_backend-sidebar.scss`.
- [x] KPI shared tersedia di `resources/backend/scss/components/_backend-kpi.scss`.
- [x] Panel/section shared tersedia di `resources/backend/scss/components/_backend-panel.scss`.
- [x] Status badge shared tersedia di `resources/backend/scss/components/_backend-status.scss`.
- [x] Empty state shared tersedia di `resources/backend/scss/components/_backend-empty-state.scss`.
- [x] Filter/control toolbar shared tersedia di `resources/backend/scss/components/_backend-filter.scss`.
- [x] Modal shared tersedia di `resources/backend/scss/components/_backend-modal.scss`.
- [x] Detail layout shared tersedia di `resources/views/components/backend/detail-layout.blade.php` dan `resources/backend/scss/components/_backend-detail-layout.scss`.
- [x] Rich text textarea shared tersedia di `resources/backend/js/app.js` dan `resources/backend/scss/components/_backend-richtext.scss`.
- [x] Shared form style tersedia di `resources/backend/scss/components/_backend-form.scss`.
- [x] Phase BF-2 Product Operations Forms selesai untuk Hotels, Activities, Tours, dan Transports.
- [x] Phase BF-3 Admin Content and User Forms selesai untuk Company Profile, Footer Manager, Terms, Reviews, Currency, dan User Manager.
- [x] Phase BF-4 Remaining Operations and Legacy Admin selesai untuk Guides, Drivers, Partners, Weddings, Orders Admin, Reservations, Transport Management, Villas, Vendors, dan Promotions.

## Phase 2 - Dashboard Baseline

- [x] Hero Admin Dashboard memakai `x-backend.page-hero`.
- [x] Filter period Admin Dashboard dipindahkan dari hero ke `backend-page-toolbar`.
- [x] KPI Admin Dashboard memakai `backend-kpi-grid` dan `backend-kpi-card`.
- [x] Panel Admin Dashboard diekstrak menjadi shared `backend-panel`.
- [x] Section header Admin Dashboard diekstrak menjadi shared `backend-section-header`.
- [x] List item Admin Dashboard diekstrak menjadi shared `backend-list`.
- [x] Badge Admin Dashboard diekstrak menjadi shared `backend-status-badge`.
- [x] Empty state Admin Dashboard diekstrak menjadi shared `backend-empty-state`.

## Phase 3 - KPI Migration

- [x] `backend/admin/dashboard` memakai shared KPI.
- [x] `backend/developer/index` Admin Panel stats memakai shared KPI.
- [x] `admin/ordersadmin` Orders Admin summary memakai shared KPI.
- [x] `backend/admin/users/manager` User Manager summary memakai shared KPI.
- [x] `backend/admin/company-profile/edit` Company Profile summary/card stats memakai shared KPI.
- [x] `backend/admin/footer-manager/index` Footer Manager summary/card stats memakai shared KPI.
- [x] `backend/developer/currency` Currency dievaluasi: Payment Accounts summary memakai shared KPI; rate/tax cards tetap domain card karena punya struktur data dan aksi khusus.
- [x] `backend/admin/reviews/index` Review summary metrics memakai shared KPI.
- [x] `backend/admin/terms/index` Terms summary metrics memakai shared KPI.
- [x] `admin/transportmanagement/spks/index` Transport Management status metrics memakai shared KPI.

## Phase 4 - Panel and Section Migration

- [x] Admin Dashboard panel menjadi baseline shared panel.
- [x] Admin Panel sections memakai shared panel/header.
- [x] Orders Admin panels memakai shared panel/header.
- [x] Orders Admin Detail panels memakai shared panel/header.
- [x] User Manager panels memakai shared panel/header.
- [x] Company Profile panels memakai shared panel/header.
- [x] Footer Manager panels memakai shared panel/header.
- [x] Currency panels memakai shared panel/header.
- [x] Reviews panels memakai shared panel/header.
- [x] Terms panels memakai shared panel/header.
- [x] Transport Management panels memakai shared panel/header.

## Phase 5 - Toolbar, Filter, and Status Migration

- [x] Hero action button standar memakai `backend-page-primary-action`.
- [x] Breadcrumb toolbar standar memakai `backend-page-toolbar`.
- [x] Admin Dashboard period filter memakai shared toolbar filter class.
- [x] Orders Admin filters memakai shared toolbar/filter class.
- [x] User Manager filters memakai shared toolbar/filter class.
- [x] Company Profile preview/action toolbar memakai shared toolbar/action class.
- [x] Footer Manager preview/action toolbar memakai shared toolbar/action class.
- [x] Status badge seluruh backend memakai shared `backend-status-badge`.
- [x] Alert/feedback seluruh backend memakai shared `backend-alert`.

## Phase 6 - Guardrails

- [x] Test struktur memastikan shared hero dan shared toolbar tersedia.
- [x] Test struktur memastikan Dashboard KPI memakai shared KPI.
- [x] Test struktur melarang selector KPI legacy baru seperti `.admin-dashboard-stat`, `.admin-panel-stat`, `.orders-admin-summary`, dan `.user-manager-stats` setelah halaman dimigrasikan.
- [x] Test struktur memastikan semua import shared backend tetap masuk lewat `resources/backend/scss/app.scss`.
- [x] Dokumentasikan cara membuat halaman backend baru memakai shared style.
- [x] Tambahkan review checklist untuk PR backend UI.
- [x] Guardrail detail backend: halaman detail yang disentuh memakai `x-backend.detail-layout` dengan side panel kanan `backend-detail-side`.
- [x] Guardrail rich text backend: semua textarea backend otomatis diinisialisasi dari shared `initBackendRichText`, bukan inline Summernote per halaman.

## Phase 7 - Operations Resource Pages

- [x] `guides-admin` dipindahkan dari `resources/views/guides/guides-admin.blade.php` ke `resources/views/backend/operations/guides/index.blade.php`.
- [x] Legacy `resources/views/guides/guides-admin.blade.php` hanya menjadi wrapper kompatibilitas.
- [x] Asset Guides Admin dipindahkan ke `resources/backend/js/operations/guides/index.js` dan `resources/backend/scss/operations/guides`.
- [x] Guides Admin memakai shared hero, toolbar, KPI, filter, panel, table, empty state, status badge, button, dan modal.
- [x] Inline script Guides Admin dipindahkan ke asset backend JS.
- [x] Test struktur memastikan Guides Admin tidak kembali memakai `card-box`, inline script, table legacy, atau URL hardcoded untuk action utama.
- [x] `drivers-admin` dipindahkan dari `resources/views/drivers/drivers-admin.blade.php` ke `resources/views/backend/operations/drivers/index.blade.php`.
- [x] Legacy `resources/views/drivers/drivers-admin.blade.php` hanya menjadi wrapper kompatibilitas.
- [x] Asset Drivers Admin dipindahkan ke `resources/backend/js/operations/drivers/index.js` dan `resources/backend/scss/operations/drivers`.
- [x] Drivers Admin memakai shared hero, toolbar, KPI, filter, panel, table, empty state, status badge, button, dan modal.
- [x] Inline script Drivers Admin dipindahkan ke asset backend JS.
- [x] Test struktur memastikan Drivers Admin tidak kembali memakai `card-box`, inline script, table legacy, atau URL hardcoded untuk action utama.
- [x] `hotels-admin` dipindahkan dari `resources/views/admin/hotelsadmin.blade.php` ke `resources/views/backend/operations/hotels/index.blade.php`.
- [x] Legacy `resources/views/admin/hotelsadmin.blade.php` hanya menjadi wrapper kompatibilitas.
- [x] Asset Hotels Admin dipindahkan ke `resources/backend/js/operations/hotels/index.js` dan `resources/backend/scss/operations/hotels`.
- [x] Hotels Admin memakai shared hero, toolbar, KPI, filter, panel, table, card list, empty state, status badge, dan button.
- [x] Inline script Hotels Admin dipindahkan ke asset backend JS.
- [x] Test struktur memastikan Hotels Admin tidak kembali memakai `card-box`, inline script, table legacy, atau URL hardcoded untuk action utama.
- [x] `detail-hotel-{id}` dipindahkan dari `resources/views/admin/hotelsadmindetail.blade.php` ke `resources/views/backend/operations/hotels/detail.blade.php`.
- [x] Legacy `resources/views/admin/hotelsadmindetail.blade.php` hanya menjadi wrapper kompatibilitas.
- [x] Asset Hotel Detail dipindahkan ke `resources/backend/js/operations/hotels/detail.js` dan `resources/backend/scss/operations/hotels/detail-entry.scss`.
- [x] Hotel Detail memakai shared hero, toolbar, KPI, panel, filter, table, empty state, status badge, button, dan modal.
- [x] Hotel Detail memakai `x-backend.detail-layout` dengan side panel kanan standar untuk rooms, contract, pricing, promo, package, additional charges, audit, currency, dan quick actions.
- [x] Hotel Detail KPI menjadi sumber tunggal untuk Status, Rooms, Contracts, Pricing Rows, dan Latest Price; side panel hanya menampilkan quick actions, audit, dan currency context.
- [x] Hotel Detail profile summary memakai layout dua kolom: cover image 50% di kiri dan detail data hotel 50% di kanan, responsive menjadi satu kolom di mobile.
- [x] Inline script Hotel Detail dipindahkan ke asset backend JS.
- [x] Test struktur memastikan Hotel Detail tidak kembali memakai `card-box`, inline script, table legacy, atau URL hardcoded untuk action utama.

## Phase 8 - Hotel Management Workspace

Phase ini adalah roadmap khusus untuk merapikan pengelolaan layanan Hotels sebagai satu workspace backend yang konsisten, mudah dirawat, dan tetap mengikuti `docs/backend-ui-standards.md`.

### Phase 8A - Hotel Workspace Foundation

- [x] Hotel index berada di `resources/views/backend/operations/hotels/index.blade.php`.
- [x] Hotel detail berada di `resources/views/backend/operations/hotels/detail.blade.php`.
- [x] Legacy `resources/views/admin/hotelsadmin.blade.php` hanya menjadi wrapper kompatibilitas.
- [x] Legacy `resources/views/admin/hotelsadmindetail.blade.php` hanya menjadi wrapper kompatibilitas.
- [x] Asset index Hotels berada di `resources/backend/js/operations/hotels/index.js` dan `resources/backend/scss/operations/hotels/index-entry.scss`.
- [x] Asset detail Hotels berada di `resources/backend/js/operations/hotels/detail.js` dan `resources/backend/scss/operations/hotels/detail-entry.scss`.
- [x] `webpack.mix.js` memiliki entry build untuk Hotels index dan Hotels detail.
- [x] Hotel index memakai shared hero, toolbar, KPI, filter, panel, table, card list, empty state, status badge, dan button.
- [x] Hotel detail memakai shared hero, toolbar, KPI, panel, filter, table, empty state, status badge, button, dan modal.
- [x] Guard test tersedia untuk mencegah Hotel index/detail kembali memakai style legacy.

### Phase 8B - Split Hotel Detail Into Maintainable Partials

- [x] Pecah profile summary dari `detail.blade.php` ke `resources/views/backend/operations/hotels/partials/profile-summary.blade.php`.
- [x] Pecah audit/sidebar summary ke `resources/views/backend/operations/hotels/partials/audit-summary.blade.php`.
- [x] Refactor audit/sidebar Hotel Detail dari custom `hotel-detail-sidebar` ke shared `backend-detail-side`.
- [x] Pecah contract section ke `resources/views/backend/operations/hotels/partials/contracts.blade.php`.
- [x] Pecah room section ke `resources/views/backend/operations/hotels/partials/rooms.blade.php`.
- [x] Pecah normal price section ke `resources/views/backend/operations/hotels/partials/normal-prices.blade.php`.
- [x] Pecah promo price section ke `resources/views/backend/operations/hotels/partials/promo-prices.blade.php`.
- [x] Pecah package price section ke `resources/views/backend/operations/hotels/partials/package-prices.blade.php`.
- [x] Pecah additional charge section ke `resources/views/backend/operations/hotels/partials/additional-charges.blade.php`.
- [x] Pecah modal preview contract ke `resources/views/backend/operations/hotels/modals/contract-preview.blade.php`.
- [x] Pecah modal preview room ke `resources/views/backend/operations/hotels/modals/room-preview.blade.php`.
- [x] Pastikan setiap partial tetap memakai shared class `backend-*` dan hanya memakai class domain `hotel-detail-*` sebagai hook layout/behavior.
- [x] Tambahkan/update guard test yang memastikan partial Hotels berada di namespace backend operations.

### Phase 8C - Standardize Hotel CRUD Forms

- [x] Form create hotel berada di `resources/views/backend/operations/hotels/forms/create.blade.php`.
- [x] Form edit hotel berada di `resources/views/backend/operations/hotels/forms/edit.blade.php`.
- [x] Form gallery edit berada di `resources/views/backend/operations/hotels/forms/gallery-edit.blade.php`.
- [x] Form room create berada di `resources/views/backend/operations/hotels/forms/room-create.blade.php`.
- [x] Form room edit berada di `resources/views/backend/operations/hotels/forms/room-edit.blade.php`.
- [x] Form normal price create berada di `resources/views/backend/operations/hotels/forms/add-normal-price.blade.php`.
- [x] Form promo create berada di `resources/views/backend/operations/hotels/forms/add-promo.blade.php`.
- [x] Rename form normal price create menjadi `normal-price-create.blade.php` dan jadikan file lama wrapper kompatibilitas.
- [x] Rename form promo create menjadi `promo-create.blade.php` dan jadikan file lama wrapper kompatibilitas.
- [x] Buat form normal price edit di `resources/views/backend/operations/hotels/forms/normal-price-edit.blade.php`.
- [x] Buat form promo edit di `resources/views/backend/operations/hotels/forms/promo-edit.blade.php`.
- [x] Buat form package create di `resources/views/backend/operations/hotels/forms/package-create.blade.php`.
- [x] Buat form package edit di `resources/views/backend/operations/hotels/forms/package-edit.blade.php`.
- [ ] Buat form contract create di `resources/views/backend/operations/hotels/forms/contract-create.blade.php` bila form contract tidak lagi memakai modal.
- [ ] Buat form contract edit di `resources/views/backend/operations/hotels/forms/contract-edit.blade.php` bila form contract tidak lagi memakai modal.
- [x] Buat form additional charge create di `resources/views/backend/operations/hotels/forms/additional-charge-create.blade.php`.
- [x] Buat form additional charge edit di `resources/views/backend/operations/hotels/forms/additional-charge-edit.blade.php`.
- [x] Standarisasi seluruh form Hotels agar memakai shared hero, toolbar, feedback, panel, section header, form label, button, status badge, dan modal jika diperlukan.
- [x] Hilangkan `card-box`, inline style, inline script, hardcoded URL, dan button legacy dari seluruh form Hotels.
- [x] Tambahkan asset form Hotels di `resources/backend/js/operations/hotels/forms.js` dan `resources/backend/scss/operations/hotels/forms-entry.scss` bila form memerlukan behavior/style page-specific.
- [x] Tambahkan guard test untuk semua form Hotels agar tidak kembali ke `resources/views/form/*` atau style legacy.

### Phase 8D - Route Naming and URL Cleanup

- [x] Route Hotel index bernama `hotels-admin.index`.
- [x] Route Hotel detail bernama `admin.hotels.show`.
- [x] Route Hotel create bernama `admin.hotels.create`.
- [x] Route Hotel edit bernama `admin.hotels.edit`.
- [x] Route Hotel gallery edit bernama `admin.hotels.gallery.edit`.
- [x] Route Hotel normal price create bernama `admin.hotels.prices.create`.
- [x] Route Hotel promo create bernama `admin.hotels.promos.create`.
- [x] Route Hotel room create bernama `admin.hotels.rooms.create`.
- [x] Route Hotel room edit bernama `admin.hotels.rooms.edit`.
- [x] Route Hotel room update bernama `func.room.update`.
- [x] Route Hotel room delete bernama `func.room.delete`.
- [x] Route Hotel price delete bernama `func.hotel-price.delete`.
- [x] Route Hotel contract add/update/delete bernama `func.hotel-contract.add`, `func.hotel-contract.update`, dan `func.hotel-contract.delete`.
- [x] Tambahkan route name untuk normal price update/store dengan pola final `admin.hotels.normal-prices.*`.
- [x] Tambahkan route name untuk promo store/update/delete dengan pola final `admin.hotels.promos.*`.
- [x] Tambahkan route name untuk package store/update/delete dengan pola final `admin.hotels.packages.*`.
- [x] Tambahkan route name untuk additional charge store/update/delete dengan pola final `admin.hotels.additional-charges.*`.
- [x] Tambahkan route name untuk contract store/update/delete dengan pola final `admin.hotels.contracts.*`.
- [x] Hilangkan hardcoded `href="/..."` dan `action="/..."` dari semua view Hotels backend.
- [x] Hilangkan hardcoded redirect `redirect("/detail-hotel-$id...")` dari `HotelsAdminController` dan ganti ke `redirect()->route(...)`.
- [x] Pertahankan route lama hanya sebagai compatibility layer sampai semua caller sudah memakai route name baru.
- [x] Tambahkan guard test untuk melarang hardcoded URL Hotels di Blade backend dan redirect string legacy di controller.

### Phase 8E - Controller Decomposition

- [x] Buat namespace controller khusus Hotel backend, misalnya `App\Http\Controllers\Backend\Operations\Hotels`.
- [x] Pindahkan Hotel profile CRUD ke `HotelAdminController`.
- [x] Pindahkan Room CRUD ke `HotelRoomAdminController`.
- [x] Pindahkan Contract CRUD ke `HotelContractAdminController`.
- [x] Pindahkan Normal Price CRUD ke `HotelNormalPriceAdminController`.
- [x] Pindahkan Promo Price CRUD ke `HotelPromoAdminController`.
- [x] Pindahkan Package Price CRUD ke `HotelPackageAdminController`.
- [x] Pindahkan Additional Charge CRUD ke `HotelAdditionalChargeAdminController`.
- [x] Pindahkan Gallery CRUD ke `HotelGalleryAdminController`.
- [x] Pastikan controller lama `HotelsAdminController` hanya menjadi compatibility adapter sementara atau dihapus setelah route final stabil.
- [x] Tambahkan test struktur yang memastikan method CRUD Hotels tidak terus bertambah di satu controller besar.

### Phase 8F - Form Request Validation

- [x] Buat `StoreHotelRequest` dan `UpdateHotelRequest`.
- [x] Buat `StoreHotelRoomRequest` dan `UpdateHotelRoomRequest`.
- [x] Buat `StoreHotelContractRequest` dan `UpdateHotelContractRequest`.
- [x] Buat `StoreHotelNormalPriceRequest` dan `UpdateHotelNormalPriceRequest`.
- [x] Buat `StoreHotelPromoRequest` dan `UpdateHotelPromoRequest`.
- [x] Buat `StoreHotelPackageRequest` dan `UpdateHotelPackageRequest`.
- [x] Buat `StoreHotelAdditionalChargeRequest` dan `UpdateHotelAdditionalChargeRequest`.
- [x] Validasi period contract: `period_start <= period_end` dan file wajib PDF saat create.
- [x] Validasi normal price: room harus milik hotel terkait, `start_date <= end_date`, contract rate/markup numeric.
- [x] Validasi promo: room harus milik hotel terkait, booking period valid, stay period valid, contract rate/markup numeric.
- [x] Validasi package: room harus milik hotel terkait, duration minimal 1, stay period valid, contract rate/markup numeric.
- [x] Validasi additional charge: mandatory date wajib bila mandatory aktif, contract rate/markup numeric.
- [x] Tambahkan feature/unit test untuk validasi utama setiap CRUD.

### Phase 8G - Service Layer and Business Rules

- [x] Buat `app/Services/Hotels/HotelInventoryService.php` untuk summary room/price/promo/package/additional charge.
- [x] Buat `app/Services/Hotels/HotelPricingService.php` untuk kalkulasi published rate, USD/IDR, tax, markup, dan kickback.
- [x] Buat `app/Services/Hotels/HotelContractService.php` untuk upload, replace, preview, dan delete file contract.
- [x] Buat `app/Services/Hotels/HotelStatusService.php` untuk auto-draft hotel/room dan expire promo/package.
- [x] Buat `app/Services/Hotels/HotelAssetService.php` untuk upload/delete cover, gallery, dan room image.
- [x] Buat `app/Services/Hotels/HotelAuditService.php` untuk pencatatan `UserLog` dan `ActionLog`.
- [x] Pindahkan kalkulasi price dari Blade/controller ke `HotelPricingService`.
- [x] Pindahkan auto update status hotel/room dari controller ke `HotelStatusService`.
- [x] Pindahkan file operation contract/gallery/room image dari controller ke service terkait.
- [x] Tambahkan test service untuk pricing, status transition, dan contract file lifecycle.

### Phase 8H - Data Loading and View Model

- [x] Rapikan query Hotel detail memakai eager loading `rooms`, `prices`, `promos`, `packages`, dan `optionalrates`.
- [x] Hindari query berulang di Blade; siapkan data dari controller/service.
- [x] Buat ViewModel/DTO sederhana untuk summary Hotel detail bila diperlukan.
- [x] Pastikan Blade hanya render data dan tidak memuat business rule berat.
- [x] Cache data referensi yang stabil seperti USD rate/tax bila sesuai pola project.
- [x] Tambahkan test bahwa Hotel detail menerima data yang dibutuhkan tanpa query/view dependency legacy.

### Phase 8I - UI and Asset Guardrails for Hotels

- [x] Semua view Hotels backend wajib berada di `resources/views/backend/operations/hotels`.
- [x] File legacy `resources/views/admin/*hotel*` hanya boleh wrapper kompatibilitas selama transition.
- [x] Semua asset Hotels backend wajib berada di `resources/backend/js/operations/hotels` dan `resources/backend/scss/operations/hotels`.
- [x] Semua halaman Hotels wajib memakai `x-backend.page-hero`.
- [x] Semua action utama memakai `backend-page-primary-action`.
- [x] Semua action sekunder memakai shared action class: `backend-toolbar-action`, `backend-icon-action`, atau `backend-secondary-action` sesuai konteks.
- [x] Semua panel memakai `backend-panel` dan `backend-section-header`.
- [x] Semua table/list data memakai `backend-table`, `backend-table-card`, `backend-table-empty`, atau `backend-empty-state`.
- [x] Semua status memakai `backend-status-badge`.
- [x] Semua feedback memakai `backend-feedback` dan `backend-alert`.
- [x] Semua modal memakai `backend-modal`, `backend-modal__header`, `backend-modal__body`, dan `backend-modal__footer`.
- [x] Page-specific SCSS Hotels hanya mengatur layout domain seperti grid, column width, image ratio, dan spacing section.
- [x] Dilarang menambahkan ulang primitive visual legacy seperti `card-box`, `btn-view`, `btn-edit`, `btn-delete`, `status-active`, `status-draft`, `.data-table.table`, inline `style=`, inline `<script>`, dan `onkeyup`.
- [x] Tambahkan guard test yang memindai seluruh `resources/views/backend/operations/hotels/**/*.blade.php` untuk pola legacy terlarang.

### Phase 8J - Final Hotel Workspace Acceptance

- [x] Semua CRUD Hotel profile berjalan dari route name final.
- [x] Semua CRUD Contract berjalan dari route name final.
- [x] Semua CRUD Rooms berjalan dari route name final.
- [x] Semua CRUD Normal Price berjalan dari route name final.
- [x] Semua CRUD Promo Price berjalan dari route name final.
- [x] Semua CRUD Package Price berjalan dari route name final.
- [x] Semua CRUD Additional Charges berjalan dari route name final.
- [x] Semua halaman/form Hotels lolos `php artisan view:cache`.
- [x] Semua test struktur Hotels lolos.
- [x] Semua test validasi dan service Hotels lolos.
- [x] `npm run development` berhasil dan menghasilkan asset Hotels yang sesuai `webpack.mix.js`.
- [x] `git diff --check` bersih untuk file Hotels dan dokumen roadmap.
- [x] Developer sudah menandai checklist roadmap sesuai progres terakhir.

## Activities Backend Standardization Roadmap

### Activities Phase 1 - Routing and Architecture Baseline

- [x] Audit controller, route, view, dan asset Activities backend existing.
- [x] Tambahkan route name final `admin.activities.*` untuk profile CRUD dan gallery.
- [x] Pertahankan URL lama hanya sebagai compatibility path sementara, bukan sumber pemanggilan utama di Blade.
- [x] Buat view wrapper legacy `resources/views/admin/activitiesadmin*.blade.php` menuju `resources/views/backend/operations/activities`.
- [x] Pastikan semua redirect controller Activities memakai route name, bukan string URL.
- [x] Tambahkan guard test route/view architecture Activities.

### Activities Phase 2 - UI Standardization

- [x] Pindahkan index Activities ke `resources/views/backend/operations/activities/index.blade.php`.
- [x] Pindahkan detail Activities ke `resources/views/backend/operations/activities/detail.blade.php`.
- [x] Standarisasi UI index Activities memakai shared hero, toolbar, feedback, KPI, panel, table, card list mobile, status badge, empty state, dan button/action backend.
- [x] Standarisasi UI detail Activities memakai shared hero, toolbar, feedback, KPI, panel, table-card grid, section header, status badge, dan toolbar action backend.
- [x] Standarisasi create/edit/gallery form memakai `x-backend.page-hero`, toolbar, feedback, panel, section header, form label, dan button shared.
- [x] Hilangkan `card-box`, `btn-view`, `btn-edit`, `btn-delete`, `status-active`, `status-draft`, `.data-table.table`, inline style, inline script, dan `onkeyup` dari seluruh form Activities.
- [x] Semua action utama memakai `backend-page-primary-action`.
- [x] Semua action sekunder memakai `backend-toolbar-action` atau `backend-icon-action`.
- [x] Semua status memakai `backend-status-badge`.
- [x] Semua feedback memakai `backend-feedback` dan `backend-alert`.
- [x] Semua tabel/list memakai `backend-table`, `backend-table-card`, `backend-table-empty`, atau `backend-empty-state`.

### Activities Phase 3 - Asset Architecture

- [x] Buat asset backend Activities di `resources/backend/js/operations/activities`.
- [x] Buat asset backend Activities di `resources/backend/scss/operations/activities`.
- [x] Daftarkan entry Activities di `webpack.mix.js`.
- [x] Pindahkan behavior delete confirmation dan file selection state dari inline/event legacy ke file JS domain.
- [x] Page-specific SCSS hanya mengatur layout domain Activities, bukan primitive visual shared.

### Activities Phase 4 - Controller Decomposition and Validation

- [x] Buat namespace controller `App\Http\Controllers\Backend\Operations\Activities`.
- [x] Pindahkan Activity profile CRUD ke `ActivityAdminController`.
- [x] Pindahkan Activity gallery CRUD ke `ActivityGalleryAdminController`.
- [x] Buat `StoreActivityAdminRequest` dan `UpdateActivityAdminRequest`.
- [x] Validasi price, capacity, min pax, validity, partner, cover image, dan status.
- [x] Pastikan controller lama hanya compatibility adapter sementara atau dihapus setelah route final stabil.

### Activities Phase 5 - Service Layer and ViewModel

- [x] Buat `ActivityInventoryService` untuk index/detail summary.
- [x] Buat `ActivityPricingService` untuk contract rate, USD/IDR, tax, markup, dan published rate.
- [x] Buat `ActivityAssetService` untuk cover/gallery upload, replace, delete.
- [x] Buat `ActivityAuditService` untuk `UserLog`.
- [x] Buat `ActivityDetailViewModel` dan `ActivityIndexViewModel`.
- [x] Hindari query/kalkulasi berat di Blade.

### Activities Phase 6 - Final Activities Acceptance

- [x] Semua CRUD Activity berjalan dari route name final.
- [x] Semua halaman/form Activities lolos `php artisan view:cache`.
- [x] Semua test struktur Activities lolos.
- [x] Semua test validasi/service Activities lolos.
- [x] `npm run development` berhasil menghasilkan asset Activities sesuai `webpack.mix.js`.
- [x] `git diff --check` bersih untuk file Activities dan roadmap.
- [x] Re-audit detail Activity: cover image compact, profile summary card, content card, dan rich text memakai shared backend UI standard.
- [x] Developer sudah menandai checklist roadmap sesuai progres terakhir.

## Tours Backend Standardization Roadmap

### Tours Phase 1 - Routing and Architecture Baseline

- [x] Audit controller, route, view, dan asset Tours backend existing.
- [x] Tambahkan route name final `admin.tours.*` untuk profile CRUD.
- [x] Tambahkan route name final `admin.tours.prices.*` untuk price CRUD.
- [x] Pertahankan URL lama hanya sebagai compatibility path sementara, bukan sumber pemanggilan utama di Blade.
- [x] Buat view backend baru di `resources/views/backend/operations/tours`.
- [x] Buat wrapper legacy `resources/views/admin/toursadmin*.blade.php` menuju view backend baru.
- [x] Pastikan redirect controller Tours memakai route name, bukan string URL.
- [x] Tambahkan asset backend Tours di `resources/backend/js/operations/tours` dan `resources/backend/scss/operations/tours`.
- [x] Daftarkan entry Tours di `webpack.mix.js`.
- [x] Tambahkan guard test route/view/asset architecture Tours.

### Tours Phase 2 - UI Standardization

- [x] Standarisasi index Tours memakai shared hero, toolbar, feedback, KPI, filter, panel, table, card list mobile, status badge, empty state, dan button/action backend.
- [x] Standarisasi detail Tours memakai shared hero, toolbar, feedback, KPI, panel, gallery, pricing table, modal, status badge, dan button/action backend.
- [x] Re-audit detail Tours: cover image compact, profile summary card, content card, dan rich text mengikuti pola detail product backend.
- [x] Hilangkan legacy UI dari semua view Tours backend: `card-box`, `btn-view`, `btn-edit`, `btn-delete`, `.data-table.table`, inline `style=`, inline `<script>`, dan event inline.
- [x] Pindahkan semua search/filter/delete/gallery behavior ke JS domain Tours.
- [x] Pastikan page-specific SCSS Tours hanya mengatur layout domain, bukan primitive visual shared.

### Tours Phase 3 - Forms and Asset Architecture

- [x] Pindahkan create/edit Tours dari `resources/views/backend/tours` ke `resources/views/backend/operations/tours/forms`.
- [x] Standarisasi form create/edit memakai shared hero, toolbar, feedback, panel, section header, form label, button, dan shared asset backend.
- [x] Pindahkan partial location repeater ke struktur backend operations Tours.
- [x] Pecah inline script location repeater menjadi JS domain Tours.
- [x] Tambahkan asset form Tours di `resources/backend/js/operations/tours/forms.js` dan `resources/backend/scss/operations/tours/forms-entry.scss`.
- [x] Tambahkan guard test agar form Tours tidak kembali ke struktur legacy.

### Tours Phase 4 - Controller Decomposition and Validation

- [x] Buat namespace controller `App\Http\Controllers\Backend\Operations\Tours`.
- [x] Pindahkan Tour profile CRUD ke `TourAdminController`.
- [x] Pindahkan Tour price CRUD ke `TourPriceAdminController`.
- [x] Pindahkan Tour gallery CRUD ke `TourGalleryAdminController`.
- [x] Buat Form Request untuk create/update Tour dan create/update Tour Price.
- [x] Validasi content, duration, type, status, cover image, location, capacity, contract rate, markup, dan expired date.
- [x] Pastikan route final tidak lagi menunjuk ke controller legacy `ToursAdminController` atau `ToursImagesController`.

### Tours Phase 5 - Service Layer and ViewModel

- [x] Buat `TourInventoryService` untuk index/detail summary.
- [x] Buat `TourPricingService` untuk contract rate, USD/IDR, tax, markup, dan published rate.
- [x] Buat `TourAssetService` untuk cover/gallery upload, replace, delete.
- [x] Buat `TourLocationService` untuk validasi, resolve, dan sync location.
- [x] Buat `TourAuditService` untuk `UserLog`.
- [x] Buat `TourIndexViewModel` dan `TourDetailViewModel`.
- [x] Hindari query/kalkulasi berat di Blade.

### Tours Phase 6 - Final Tours Acceptance

- [x] Semua CRUD Tour berjalan dari route name final.
- [x] Semua halaman/form Tours lolos `php artisan view:cache`.
- [x] Semua test struktur Tours lolos.
- [x] Semua test validasi/service Tours lolos.
- [x] `npm run development` berhasil menghasilkan asset Tours sesuai `webpack.mix.js`.
- [x] `git diff --check` bersih untuk file Tours dan roadmap.
- [x] Developer sudah menandai checklist roadmap sesuai progres terakhir.

### Transports Phase 1 - Route, View, and Asset Foundation

- [x] Audit batas domain Transports master data dan pisahkan dari Transport Management/SPK.
- [x] Pindahkan source view index/detail Transports ke `resources/views/backend/operations/transports`.
- [x] Jadikan `resources/views/admin/transportsadmin*.blade.php` sebagai wrapper legacy menuju backend view baru.
- [x] Beri route name final untuk Transports profile, price, gallery, dan cover.
- [x] Ubah link/action backend Transports agar memakai `route()` final.
- [x] Buat folder asset domain di `resources/backend/js/operations/transports` dan `resources/backend/scss/operations/transports`.
- [x] Daftarkan entry asset Transports di `webpack.mix.js`.
- [x] Tambahkan guard test struktur Phase 1 agar fondasi route/view/asset tidak regress.

### Transports Phase 2 - Index and Detail UI Standardization

- [x] Refactor index Transports agar memakai shared hero, toolbar, feedback, KPI, panel, table, status badge, empty state, dan button standard.
- [x] Refactor detail Transports agar memakai shared hero, toolbar, feedback, KPI/summary, panel, section header, table, status badge, modal, dan button standard.
- [x] Re-audit detail Transports: cover image compact, profile summary card, content card, dan rich text mengikuti pola detail product backend.
- [x] Hilangkan `card-box`, `btn-view`, `btn-edit`, `btn-delete`, inline `style`, inline `onclick`, inline `onkeyup`, dan `<script>` legacy dari index/detail.
- [x] Pindahkan behavior search/delete confirmation index/detail ke JS domain Transports.
- [x] Pastikan route/action detail pricing tetap memakai route name final.

### Transports Phase 3 - Forms and Gallery Standardization

- [x] Standardisasi create, edit, dan gallery-edit agar memakai shared hero, toolbar, feedback, panel, section header, form label, file input, dan button standard.
- [x] Hilangkan `card-box`, button Bootstrap langsung, inline style/script, dan struktur form lama.
- [x] Pindahkan behavior preview/upload/gallery interaction ke `resources/backend/js/operations/transports/forms.js`.
- [x] Pastikan semua form action memakai route name final.

### Transports Phase 4 - Controller Decomposition and Validation

- [x] Buat namespace controller baru `App\Http\Controllers\Backend\Operations\Transports`.
- [x] Pisahkan profile CRUD ke `TransportAdminController`.
- [x] Pisahkan price CRUD ke `TransportPriceAdminController`.
- [x] Pisahkan gallery/cover lifecycle ke `TransportGalleryAdminController`.
- [x] Buat Form Request untuk create/update Transport dan create/update Transport Price.
- [x] Update route agar memakai controller baru tanpa memutus URL legacy.

### Transports Phase 5 - Service Layer and ViewModel

- [x] Buat `TransportInventoryService` untuk index/detail summary dan form options.
- [x] Buat `TransportPricingService` untuk kalkulasi contract rate, markup, tax, published rate, dan price CRUD.
- [x] Buat `TransportAssetService` untuk cover/gallery lifecycle.
- [x] Buat `TransportAuditService` untuk UserLog agar controller tidak membuat log manual.
- [x] Buat `TransportIndexViewModel` dan `TransportDetailViewModel`.
- [x] Kurangi query/kalkulasi berulang di Blade dan controller.

### Transports Phase 6 - Final Transports Acceptance

- [x] Semua CRUD Transports berjalan dari route name final.
- [x] Semua halaman/form Transports lolos `php artisan view:cache`.
- [x] Semua test struktur Transports lolos.
- [x] Semua test validasi/service Transports lolos.
- [x] `npm run development` berhasil menghasilkan asset Transports sesuai `webpack.mix.js`.
- [x] `git diff --check` bersih untuk file Transports dan roadmap.
- [x] Developer sudah menandai checklist roadmap sesuai progres terakhir.

## Current Focus

- [x] Menetapkan Admin Dashboard sebagai baseline visual.
- [x] Mengekstrak KPI dashboard ke shared `backend-kpi`.
- [x] Refactor KPI Admin Panel ke shared `backend-kpi`.
- [x] Refactor Orders Admin summary ke shared `backend-kpi`.
- [x] Phase BF-5 Final Global Form Acceptance dimulai dengan audit global backend/admin untuk memastikan form, dropdown, textarea, dan button memakai shared backend form standard.
- [x] Backend Legacy UI Deep Cleanup dimulai melalui `docs/backend-legacy-ui-deep-cleanup-roadmap.md`, dimulai dari `create-hotel-order`.
