# Asset Migration Inventory

Dokumen ini memetakan area asset dan view yang perlu dimigrasikan bertahap dari struktur legacy ke struktur domain-based baru.

## Frontend Candidates

### Views

- `resources/views/frontend/*`
- `resources/views/home/*`
- `resources/views/main/order.blade.php`
- `resources/views/main/hotelavailability.blade.php`
- `resources/views/main/hotel-price.blade.php`
- `resources/views/main/hotelsearch.blade.php`
- `resources/views/villas/*`
- `resources/views/order/detail-order-*` yang user-facing
- `resources/views/form/order-hotel-normal.blade.php`
- `resources/views/form/order-hotel-package.blade.php`
- `resources/views/form/order-hotel-promo.blade.php`

### CSS

- `public/css/components/frontend-*.css`
- `public/css/pages/frontend-*.css`
- `public/css/pages/hotel-availability.css`
- `public/css/pages/hotel-booking.css`
- `public/css/pages/accommodation-detail.css`

### JavaScript

- `public/frontend/js/pages/*`
- `public/frontend/js/components/*`
- `public/js/pages/*` yang khusus page frontend

### Images

- `public/images/balikami/*`
- `public/images/background/*`
- `public/images/icons/*`
- `public/images/partner/*`
- `public/images/property/*` yang dipakai halaman frontend

## Backend Candidates

### Views

- `resources/views/admin/*`
- `resources/views/backend/*`
- sebagian besar `resources/views/form/*` yang CRUD internal
- `resources/views/layouts/head.blade.php`
- `resources/views/layouts/footjs.blade.php`

### CSS/JS/Assets

- `public/panel/*`
- `public/vendors/scripts/*`
- `resources/js/*` yang dipakai dashboard dan komponen panel
- `resources/sass/*` legacy panel bundle

## Shared Candidates

- logo dan brand utama
- favicon
- helper blade generik
- JS helper umum
- token warna bila benar-benar lintas domain

## Vendor Static Candidates

- `public/panel/ckeditor/*`
- `public/panel/summernote/*`
- `public/panel/datatables/*`
- `public/panel/fullcalendar/*`
- `public/panel/dropzone/*`
- `public/assets/dist/pdfreader/*`
- `public/assets/owlcarousel/*`
- `public/lib/*`

## Runtime Files Yang Tidak Dimigrasikan Ke Source Asset

- `public/storage/*`
- `public/upload/*`
- seluruh file hasil upload user, partner, hotel, receipt, logo dinamis

## Aturan Kerja Selama Masa Transisi

1. Asset baru jangan ditambahkan ke folder legacy jika sudah ada folder target yang sesuai.
2. Refactor dilakukan per domain halaman, bukan per ekstensi file semata.
3. Setiap pemindahan asset harus sekaligus memperbarui referensi Blade yang relevan.
4. Hapus asset legacy hanya setelah dipastikan tidak ada referensi tersisa.

## Legacy Frontend Assets With Zero Active References

Status audit per `2026-07-03`:

- seluruh path di bawah ini sudah `0 legacy references` pada pencarian `resources`, `app`, dan `routes`
- file fisik masih ada di `public/*`
- upaya hapus otomatis pada environment ini ditolak oleh filesystem dengan `Access is denied`
- file-file ini aman diperlakukan sebagai kandidat cleanup manual atau cleanup pada sesi berikutnya jika izin filesystem memungkinkan

### Legacy CSS Components

- `public/css/components/frontend-tokens.css`
- `public/css/components/frontend-base.css`
- `public/css/components/frontend-page-shell.css`
- `public/css/components/frontend-layout.css`
- `public/css/components/frontend-components.css`
- `public/css/components/frontend-forms.css`
- `public/css/components/frontend-swiper.css`
- `public/css/components/frontend-availability-family.css`
- `public/css/components/frontend-footer.css`
- `public/css/components/hotel-check-price-card.css`

### Legacy CSS Pages

- `public/css/pages/frontend-home.css`
- `public/css/pages/frontend-home-services.css`
- `public/css/pages/frontend-orders.css`
- `public/css/pages/hotel-availability.css`
- `public/css/pages/accommodation-detail.css`
- `public/css/pages/hotel-booking.css`

### Legacy Frontend JS Components

- `public/frontend/js/components/frontend-footer-subscribe.js`
- `public/frontend/js/components/frontend-hotel-check-price.js`
- `public/frontend/js/components/frontend-loop-swiper.js`

### Legacy Frontend JS Pages

- `public/frontend/js/pages/accommodation-detail.js`
- `public/frontend/js/pages/hotel-availability.js`
- `public/frontend/js/pages/hotel-booking.js`

## Next Cleanup Priorities

1. Hapus file orphan di atas saat filesystem mengizinkan operasi delete.
2. Audit halaman frontend lain di luar family accommodation untuk melihat apakah masih ada CSS/JS page legacy yang perlu dipindahkan ke `resources/frontend/*`.
3. Setelah cleanup fisik selesai, evaluasi apakah folder `public/css/components`, `public/css/pages`, `public/frontend/js/components`, dan `public/frontend/js/pages` masih perlu dipertahankan atau bisa dipensiunkan bertahap.
