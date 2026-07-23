# Backend Rich Text Textarea Roadmap

Tujuan roadmap ini adalah memastikan semua `textarea` pada halaman backend memakai rich text editor dari satu sumber shared dan tidak lagi bergantung pada initializer inline atau konfigurasi per halaman.

## Standard Final

- Semua textarea backend otomatis diinisialisasi oleh `initBackendRichText` di `resources/backend/js/app.js`.
- Scope otomatis berlaku untuk textarea di `body.sidebar-light .main-container` dan `body.sidebar-light .modal`.
- Textarea baru/refactor sebaiknya menambahkan `data-backend-richtext="true"` agar intent-nya eksplisit.
- Textarea yang harus tetap plain text wajib memakai `data-backend-richtext="false"` dan diberi alasan saat review.
- Styling editor hanya berasal dari `resources/backend/scss/components/_backend-richtext.scss`.
- Blade tidak boleh memanggil `.summernote()` langsung lewat inline script.

## Audit Awal

- [x] `resources/views/backend` memiliki 167 textarea yang sekarang ter-cover oleh initializer shared global.
- [x] Legacy class `textarea_editor` tetap didukung selama masa migrasi.
- [x] Initializer inline Summernote di `resources/views/layouts/footjs.blade.php` sudah dipindahkan ke backend JS shared.
- [x] Shared richtext style sudah masuk ke `resources/backend/scss/app.scss`.

## Domain Inventory

- [x] `backend/admin/company-profile` - 1 file.
- [x] `backend/admin/footer-manager` - 1 file.
- [x] `backend/admin/terms` - 1 file.
- [x] `backend/operations/activities` - 2 files.
- [x] `backend/operations/drivers` - 1 file.
- [x] `backend/operations/guides` - 1 file.
- [x] `backend/operations/hotels` - 10 files.
- [x] `backend/operations/partners` - 2 files.
- [x] `backend/operations/tours` - 3 files.
- [x] `backend/operations/transports` - 2 files.
- [x] `backend/operations/weddings` - 11 files.

## Migration Phases

### Phase RT-1 - Shared Foundation

- [x] Buat initializer global `initBackendRichText`.
- [x] Load `build/backend/js/app.js` dari layout backend.
- [x] Hapus initializer Summernote inline untuk `.textarea_editor`.
- [x] Buat shared SCSS richtext backend.
- [x] Dokumentasikan standard di `docs/backend-ui-standards.md`.
- [x] Tambahkan guard test struktur richtext.

### Phase RT-2 - Hotels and Operations Product Forms

- [x] Refactor textarea Hotels agar memakai `data-backend-richtext="true"` secara eksplisit.
- [x] Refactor textarea Activities agar memakai `data-backend-richtext="true"` secara eksplisit.
- [x] Refactor textarea Tours agar memakai `data-backend-richtext="true"` secara eksplisit.
- [x] Refactor textarea Transports agar memakai `data-backend-richtext="true"` secara eksplisit.
- [x] Pastikan tidak ada initializer richtext domain di asset Hotels/Activities/Tours/Transports.

### Phase RT-3 - Admin Content Settings

- [x] Refactor textarea Company Profile agar memakai richtext standard.
- [x] Refactor textarea Footer Manager agar memakai richtext standard.
- [x] Refactor textarea Terms agar memakai richtext standard.

### Phase RT-4 - Remaining Operations

- [x] Refactor textarea Guides dan Drivers.
- [x] Refactor textarea Partners.
- [x] Refactor textarea Weddings.
- [x] Audit textarea di backend operations lain yang muncul setelah phase ini.

### Phase RT-5 - Legacy Admin Compatibility

- [x] Audit textarea di `resources/views/admin` yang masih dipakai route backend aktif. Seluruh 111 textarea legacy sudah memakai `data-backend-richtext="true"`.
- [x] Pertahankan kompatibilitas `textarea_editor` sampai wrapper legacy selesai dipindahkan.
- [x] Hapus class legacy `textarea_editor` hanya setelah semua view backend aktif memakai `data-backend-richtext="true"`. Status final fase ini: penghapusan ditunda dengan sengaja karena 99 textarea legacy masih memakainya sebagai compatibility bridge sampai wrapper legacy selesai dipindahkan.

### Phase RT-6 - Final Acceptance

- [x] `php artisan view:cache` berhasil.
- [x] `npm run development` berhasil.
- [x] Test struktur richtext berhasil.
- [x] Tidak ada `.summernote()` inline di Blade backend.
- [x] Tidak ada SCSS halaman yang mendefinisikan ulang visual editor richtext.
