# Asset Architecture Blueprint

Dokumen ini menjadi acuan resmi untuk pemisahan asset `frontend` dan `backend` pada project `balikamitour`.

## Tujuan

- Memisahkan domain kerja frontend dan backend secara jelas.
- Mengurangi kebingungan karena asset tersebar di banyak lokasi `public/`.
- Menjadikan `resources/` sebagai sumber asset buatan developer.
- Menjadikan `public/` sebagai output build atau vendor static runtime.
- Mempermudah onboarding, maintenance, dan refactor bertahap.

## Temuan Struktur Saat Ini

Saat ini asset project tersebar di beberapa lokasi sekaligus:

- `public/css`
- `public/js`
- `public/frontend`
- `public/panel`
- `public/images`
- `public/assets`
- `public/lib`
- `public/vendors`
- `resources/js`
- `resources/sass`
- `resources/views/frontend`
- `resources/views/admin`
- `resources/views/main`
- `resources/views/layouts`
- `resources/views/partials`

Masalah utamanya:

1. source asset dan compiled asset masih bercampur
2. asset frontend dan backend belum dipisah konsisten
3. third-party vendor asset bercampur dengan asset buatan tim
4. banyak Blade masih memakai path asset lama yang tersebar
5. build pipeline saat ini baru punya satu entry global `app.js` dan `app.scss`

## Prinsip Arsitektur Baru

1. `resources/` adalah sumber utama asset yang ditulis tim developer.
2. `public/build/` adalah output build untuk asset aplikasi.
3. `public/vendor-assets/` adalah tempat vendor static yang tidak dibundle.
4. `storage/` atau `public/storage/` tetap dipakai untuk file dinamis user atau CMS.
5. frontend dan backend dipisahkan berdasarkan domain kerja, bukan hanya jenis file.
6. asset shared hanya dipakai jika benar-benar dipakai lintas domain.

## Struktur Target

```text
resources/
  frontend/
    js/
      app.js
      components/
      pages/
      utils/
    scss/
      app.scss
      base/
      components/
      layouts/
      pages/
      vendors/
    images/
      brand/
      icons/
      pages/
      shared/
  backend/
    js/
      app.js
      components/
      pages/
      utils/
    scss/
      app.scss
      base/
      components/
      layouts/
      pages/
      vendors/
    images/
      brand/
      icons/
      pages/
      shared/
  shared/
    js/
    scss/
    images/
  views/
    frontend/
      layouts/
      pages/
      partials/
      components/
    backend/
      layouts/
      pages/
      partials/
      components/
    shared/
      partials/
      components/

public/
  build/
    frontend/
      css/
      js/
      images/
    backend/
      css/
      js/
      images/
  vendor-assets/
    ckeditor/
    summernote/
    datatables/
    fullcalendar/
    owlcarousel/
    pdfreader/
```

## Klasifikasi Domain

### Frontend

Masuk kelompok frontend bila:

- dipakai oleh halaman public, agent, order flow user, home, hotel detail, accommodation detail, service pages, review pages
- dipanggil dari `resources/views/frontend/*`, `resources/views/home/*`, `resources/views/main/*` yang bersifat public/agent-facing
- merupakan CSS/JS/UI component yang dipakai oleh user-facing pages

Contoh dari repo saat ini:

- `public/css/components/frontend-*.css`
- `public/css/pages/frontend-home.css`
- `public/css/pages/frontend-orders.css`
- `public/css/pages/hotel-availability.css`
- `public/css/pages/hotel-booking.css`
- `public/frontend/js/pages/*`
- `resources/views/frontend/*`
- sebagian `resources/views/main/*`

### Backend

Masuk kelompok backend bila:

- dipakai oleh admin panel, reservation, operations, content management, dashboard, CRUD vendor/hotel/tour/transport
- dipanggil dari `resources/views/admin/*`, `resources/views/backend/*`, `resources/views/form/*` yang bersifat admin/internal
- bergantung pada library panel seperti datatables, dropzone, ckeditor, admin styles

Contoh dari repo saat ini:

- `public/panel/*`
- sebagian `public/vendors/scripts/*`
- `resources/views/admin/*`
- banyak file di `resources/views/form/*`
- `resources/js/components/*` yang berhubungan panel/dashboard

### Shared

Masuk kelompok shared bila:

- dipakai lintas frontend dan backend
- tidak masuk akal digandakan
- berupa helper visual atau helper runtime yang memang umum

Contoh kandidat shared:

- logo brand
- helper JS ringan yang tidak spesifik halaman
- tokens warna atau utility SCSS lintas domain
- Blade partial generik yang tidak memuat context frontend/backend kuat

### Vendor Static

Masuk `public/vendor-assets` bila:

- file third-party perlu diakses langsung oleh browser
- tidak dibundle melalui build pipeline
- merupakan distribusi vendor utuh

Contoh dari repo saat ini:

- `public/panel/ckeditor`
- `public/panel/summernote`
- `public/panel/datatables`
- `public/panel/fullcalendar`
- `public/assets/dist/pdfreader`
- `public/lib/owlcarousel`

### Runtime Uploads

Tidak ikut dipindah ke arsitektur asset source:

- `public/storage/*`
- `storage/app/public/*`
- `public/upload/*`

Ini adalah file dinamis runtime, bukan asset source buatan tim.

## Pemetaan Folder Saat Ini ke Target

| Lokasi saat ini | Target domain | Target akhir |
| --- | --- | --- |
| `public/css/components/frontend-*.css` | frontend | `resources/frontend/scss/components/*` lalu build ke `public/build/frontend/css` |
| `public/css/pages/frontend-*.css` | frontend | `resources/frontend/scss/pages/*` lalu build ke `public/build/frontend/css` |
| `public/css/pages/hotel-*.css` | frontend | `resources/frontend/scss/pages/*` |
| `public/frontend/js/pages/*` | frontend | `resources/frontend/js/pages/*` lalu build ke `public/build/frontend/js` |
| `public/frontend/js/components/*` | frontend | `resources/frontend/js/components/*` |
| `public/js/pages/*` | evaluasi | pindahkan ke frontend atau backend sesuai pemakai |
| `public/js/sweetalert/*` | shared/backend | idealnya via npm bundle atau `resources/shared/js/vendor` |
| `public/panel/*` | backend/vendor | pisahkan menjadi `resources/backend/*` untuk custom asset dan `public/vendor-assets/*` untuk vendor statis |
| `public/assets/dist/pdfreader/*` | vendor static | `public/vendor-assets/pdfreader/*` |
| `public/assets/owlcarousel/*` dan `public/lib/owlcarousel/*` | vendor static | satu sumber di `public/vendor-assets/owlcarousel/*` |
| `public/vendors/term/*` | frontend legacy/vendor | evaluasi, lalu pecah ke `resources/frontend/*` dan `public/vendor-assets/*` |
| `public/images/balikami/*` | shared brand/frontend | `resources/shared/images/brand/*` atau `resources/frontend/images/brand/*` |
| `public/images/property/*` | evaluasi | frontend atau backend sesuai pemakai |
| `resources/js/*` | backend legacy/shared | pecah ke `resources/backend/js/*` dan `resources/shared/js/*` |
| `resources/sass/*` | backend legacy/shared | pecah ke `resources/backend/scss/*` dan `resources/shared/scss/*` |
| `resources/views/main/*` | frontend legacy | migrasi ke `resources/views/frontend/pages/*` bertahap |
| `resources/views/layouts/*` | mixed | pecah ke `resources/views/frontend/layouts/*`, `resources/views/backend/layouts/*`, `resources/views/shared/*` |
| `resources/views/partials/*` | mixed | pecah ke partial frontend/backend/shared sesuai pemakai |

## Aturan Penamaan Setelah Migrasi

1. jangan buat folder baru langsung di `public/` untuk asset custom aplikasi
2. asset custom baru wajib dibuat di `resources/frontend`, `resources/backend`, atau `resources/shared`
3. Blade frontend hanya boleh mengarah ke asset frontend build atau shared build
4. Blade backend hanya boleh mengarah ke asset backend build, shared build, atau vendor static yang memang dibutuhkan
5. image brand reusable harus dipusatkan, jangan disalin ke banyak folder

## Tahapan Migrasi yang Direkomendasikan

### Phase 1

- tetapkan blueprint ini
- scaffold struktur target baru
- inventaris pemakaian asset
- hentikan penambahan asset custom baru ke folder legacy

### Phase 2

- pecah build entry menjadi frontend dan backend
- mulai migrasi CSS/JS halaman frontend baru atau yang aktif dikembangkan
- tetap pertahankan compatibility path lama selama masa transisi

### Phase 3

- migrasi layout frontend utama ke bundle baru
- migrasi panel/backend utama ke bundle baru
- konsolidasikan vendor static ke `public/vendor-assets`

### Phase 4

- hapus folder legacy yang sudah tidak direferensikan
- bersihkan duplicate vendor
- ratakan naming convention di Blade

## Quick Wins Prioritas Tinggi

1. bundle frontend khusus untuk layout `resources/views/frontend/layouts/app.blade.php`
2. bundle backend khusus untuk layout admin/panel utama
3. pindahkan file CSS frontend baru dari `public/css` ke source `resources/frontend/scss`
4. pindahkan file JS frontend baru dari `public/frontend/js` ke source `resources/frontend/js`
5. normalisasi vendor carousel dan pdf reader yang sekarang tersebar

## Jangan Dilakukan

- jangan memindahkan semua folder legacy sekaligus dalam satu commit besar
- jangan mengubah semua path Blade tanpa inventaris usage
- jangan mencampur source asset baru ke `public/`
- jangan memindahkan `public/storage` atau `public/upload` ke folder source asset

## Keputusan Saat Ini

Mulai sekarang struktur target resmi project adalah:

- `resources/frontend/*`
- `resources/backend/*`
- `resources/shared/*`
- `resources/views/frontend/*`
- `resources/views/backend/*`
- `resources/views/shared/*`
- `public/build/*`
- `public/vendor-assets/*`

Folder lama tetap hidup sementara sampai migrasi bertahap selesai.
