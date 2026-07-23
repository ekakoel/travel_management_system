# Backend UI Standards

Standar ini berlaku hanya untuk area backend/internal staff. Perubahan style backend harus ditempatkan di `resources/backend/scss` dan perubahan behavior backend harus ditempatkan di `resources/backend/js`. Jangan menaruh aturan visual backend di folder frontend, dan jangan memakai selector global yang bisa mengubah halaman publik.

## Scope

- Halaman backend menggunakan layout internal seperti `resources/views/layouts/head.blade.php`, sidebar `resources/views/layouts/left-navbar.blade.php`, dan view di bawah `resources/views/backend`.
- Frontend publik dan area customer tetap mengikuti `docs/frontend-ui-standards.md`.
- Shared component lintas frontend/backend hanya boleh dibuat jika benar-benar tidak membawa bahasa visual salah satu area.

## Visual Language

- Backend harus terasa operasional: padat, mudah dipindai, tenang, dan fokus pada pekerjaan.
- Gunakan section flat, tabel/list yang jelas, metrik ringkas, dan kontrol yang familiar.
- Kartu boleh digunakan untuk item berulang, panel data, atau modal. Hindari card di dalam card.
- Radius utama 6-8px, border halus, shadow ringan, dan warna status yang jelas.
- Hindari hero marketing, ornamen dekoratif, gradient besar, atau layout yang membuat data utama turun terlalu jauh.
- Halaman `resources/views/backend/admin/dashboard/index.blade.php` menjadi baseline visual backend untuk KPI, panel operasional, list ringkas, dan layout dashboard. Jika style halaman lain berbeda untuk pola UI yang sama, migrasikan halaman itu ke shared backend component/style, bukan menyalin style dashboard ke file halaman.

## Shared Style Governance

- Satu pola UI backend hanya boleh punya satu sumber style shared di `resources/backend/scss/components`.
- Page-specific SCSS boleh mengatur layout domain, width kolom, atau pengecualian konten yang benar-benar spesifik, tetapi tidak boleh mendefinisikan ulang visual primitive seperti hero, KPI, panel, toolbar, button, status badge, empty state, form control, table, dan alert.
- Jika sebuah style dipakai atau berpotensi dipakai oleh lebih dari satu halaman, buat class `backend-*` shared dan import dari `resources/backend/scss/app.scss`.
- Jika markup dipakai atau berpotensi dipakai oleh lebih dari satu halaman, buat Blade component di `resources/views/components/backend`.
- Class domain seperti `admin-dashboard-*`, `orders-admin-*`, atau `user-manager-*` boleh dipertahankan sebagai hook layout/JavaScript, tetapi visual utama harus berasal dari class shared seperti `backend-kpi-card`, `backend-page-toolbar`, atau `backend-primary-action`.
- Setiap standardisasi backend wajib memperbarui `docs/backend-ui-standardization-roadmap.md` agar status migrasi dapat diaudit.

## Hero Standard

- Hero halaman backend mengikuti baseline shared dan memakai component `x-backend.page-hero`, class `backend-page-hero`, `backend-page-eyebrow`, dan `backend-page-primary-action` untuk setiap aksi utama di dalam hero. Class `backend-page-primary-action` adalah satu-satunya sumber style visual untuk tombol hero; class domain hanya boleh dipertahankan sebagai hook behavior bila benar-benar diperlukan.
- View internal backend/admin tidak boleh memakai `.page-header` sebagai hero. Jika halaman membutuhkan hero, gunakan component `x-backend.page-hero` supaya struktur, spacing, background, tinggi, warna, dan tombol tetap konsisten.
- Hero memiliki lebar penuh container, tinggi minimum 142px (128px di mobile), padding 34px (24px di mobile), dan satu primary action berlebar minimum 160px; seluruh nilai tersebut didefinisikan di shared stylesheet.
- Hero hanya dipakai sebagai ringkasan konteks halaman dan satu aksi utama. Jangan memasukkan statistik, tabel, atau banyak tombol ke dalam hero.
- Style shared hero berada di `resources/backend/scss/components/_backend-hero.scss` dan dimuat lewat bundle backend global.
- Halaman backend boleh menambahkan class domain seperti `admin-panel-hero` atau `currency-admin-header`, tetapi tidak boleh mengubah warna, spacing, typography, atau style tombol utama hero dari standar shared tanpa alasan produk yang jelas.

## Backend Theme Tokens

- Token warna, radius, dan shadow backend didefinisikan di `resources/backend/scss/components/_backend-theme.scss`.
- Selector theme harus scoped ke backend shell, misalnya `body.sidebar-light` atau class `backend-*`.
- Jangan override class frontend seperti `.tour-*`, `.landing-*`, atau `.frontend-*` dari bundle backend.

## Navigation Standard

- Sidebar kiri adalah navigasi utama backend dan harus mempertahankan class legacy yang dibutuhkan JavaScript panel: `left-side-bar`, `brand-logo`, `menu-block`, `sidebar-menu`, `dropdown-toggle`, `submenu`, dan `accordion-menu`.
- Tambahkan class `backend-sidebar*` untuk styling baru agar perubahan tetap aman dan terisolasi.
- Item aktif harus terlihat jelas, submenu harus tetap ringkas, dan badge seperti pending order harus tidak menggeser layout.

## Link Standard

- Semua link backend wajib tanpa underline pada normal, hover, focus, dan active state.
- Link tetap harus terlihat interaktif melalui warna, berat font, icon, badge, background lembut, atau focus ring, bukan underline.
- Aturan global link backend berada di `resources/backend/scss/components/_backend-theme.scss` dan harus tetap scoped ke `body.sidebar-light`.
- Jangan menambahkan rule halaman seperti `text-decoration: underline` pada link, text action, tab, breadcrumb, atau action link backend.

## Breadcrumb Standard

- Breadcrumb backend wajib berada di dalam wrapper shared `backend-page-toolbar`, mengikuti baseline halaman Currency.
- Wrapper toolbar memakai surface putih, border halus, radius 8px, shadow ringan, padding ringkas, dan bisa memuat action/status ringkas di sisi kanan.
- Breadcrumb tetap memakai class Bootstrap existing `.breadcrumb` dan `.breadcrumb-item`, tetapi styling final berasal dari bundle backend global `resources/backend/scss/components/_backend-breadcrumb.scss`.
- Link root breadcrumb harus memakai route name, bukan URL hardcoded. Untuk Admin Panel gunakan `route('view.admin-panel-main')` agar konsisten dengan navigation backend.
- Link breadcrumb harus berwarna `backend-brand-strong`, tidak memakai underline, dan tetap memiliki hover/focus state yang jelas.
- Item aktif breadcrumb memakai warna muted dan tidak terlihat seperti link.
- Pada mobile, `backend-page-toolbar` boleh stack vertikal agar breadcrumb dan action/status tidak saling menekan.

## Language Standard

- Semua teks backend yang tampil ke user internal wajib mengikuti `docs/multi-language-standard.md`.
- Heading, breadcrumb, table header, KPI label, status meta, button, modal, form label, placeholder, helper text, empty state, tooltip, dan alert harus memakai language key.
- Halaman backend baru dengan banyak copy, seperti dashboard atau workflow moderation, wajib memakai file language domain di `resources/lang/{locale}`.
- Jangan menambahkan hardcoded English atau Indonesian di Blade, service, controller, atau JavaScript backend untuk copy yang tampil di UI.
- JavaScript backend harus menerima label melalui `data-*` attribute atau payload translation dari Blade.

## Dashboard Standard

- Dashboard backend adalah workspace operasional, bukan landing page marketing.
- Dashboard baru wajib ditempatkan di `resources/views/backend/...` dengan asset di `resources/backend/js` dan `resources/backend/scss`.
- Data dashboard harus dibentuk di controller/service/view model. Blade tidak boleh menjalankan query database atau shaping data berat.
- KPI harus ringkas, relevan dengan role, dan tidak menampilkan data yang tidak dibutuhkan role tersebut.
- Dashboard wajib responsive untuk mobile, tablet, desktop, dan wide desktop tanpa horizontal scroll sebagai pengalaman utama.
- Setiap dashboard baru wajib memiliki authorization eksplisit dari route middleware, policy, atau gate yang terdokumentasi.
- Dashboard baru wajib memakai language key untuk semua label, metric meta, empty state, filter, dan action.
- Jika dashboard membutuhkan test, ikuti `docs/testing-database-safety-standard.md` sebelum test dijalankan.

## KPI Standard

- KPI backend mengikuti baseline Admin Dashboard dan memakai shared class `backend-kpi-grid`, `backend-kpi-grid--3`, `backend-kpi-grid--4`, `backend-kpi-grid--5`, `backend-kpi-grid--6`, `backend-kpi-card`, `backend-kpi-card__icon`, dan tone modifier `backend-kpi-card--teal|blue|green|amber`.
- Style KPI hanya boleh didefinisikan di `resources/backend/scss/components/_backend-kpi.scss` dan dimuat lewat `resources/backend/scss/app.scss`.
- Halaman backend tidak boleh membuat ulang visual KPI dengan class domain seperti `.admin-dashboard-stat`, `.admin-panel-stat`, atau `.orders-admin-summary article`. Saat halaman tersebut disentuh, migrasikan markup-nya ke class shared KPI.
- KPI harus ringkas: label, nilai, meta kecil, dan icon opsional. Jangan memasukkan form, tabel, tombol kompleks, atau paragraph panjang ke dalam KPI card.

## Panel and Section Header Standard

- Panel backend mengikuti baseline Admin Dashboard dan memakai shared class `backend-panel`.
- Header section backend memakai shared class `backend-section-header` dan label kecil memakai `backend-section-header__label`.
- Style panel/header hanya boleh didefinisikan di `resources/backend/scss/components/_backend-panel.scss` dan dimuat lewat `resources/backend/scss/app.scss`.
- Page-specific SCSS hanya boleh mengatur layout/isi panel seperti grid domain, list item, table wrapper, chart, atau form. Jangan mendefinisikan ulang background, border, radius, shadow, padding, typography header, atau responsive header untuk panel yang polanya sama.
- Saat halaman backend disentuh, migrasikan panel domain seperti `.admin-dashboard-section`, `.company-profile-panel`, `.footer-manager-panel`, atau `.terms-admin-section` ke `backend-panel` dan `backend-section-header` jika strukturnya panel umum.

## List and Empty State Standard

- List ringkas backend mengikuti baseline Admin Dashboard dan memakai shared class `backend-list`, `backend-list-item`, dan `backend-list-item__meta`.
- Empty state umum memakai shared class `backend-empty-state`; versi ringkas memakai `backend-empty-state--compact`.
- Style list hanya boleh didefinisikan di `resources/backend/scss/components/_backend-list.scss` dan style empty state hanya boleh didefinisikan di `resources/backend/scss/components/_backend-empty-state.scss`.
- Class domain seperti `admin-dashboard-list`, `admin-dashboard-empty`, atau `currency-admin-empty` boleh dipertahankan sebagai hook layout/JS, tetapi visual umum harus berasal dari shared class.
- Empty state di dalam tabel tetap boleh memakai `backend-table-empty` karena pola tersebut bagian dari Table Display Standard.

## Toolbar Filter Standard

- Filter ringkas di dalam toolbar backend harus memakai shared class `backend-toolbar-filter`.
- Label filter memakai `backend-toolbar-filter__label` dan input/select memakai `backend-toolbar-filter__control`.
- Filter block yang lebih besar memakai `backend-filter-panel`, field memakai `backend-filter-field`, control memakai `backend-filter-control`, search input dengan icon memakai `backend-filter-search`, dan action row memakai `backend-filter-actions`.
- Filter block backend default harus compact: grid responsive dari shared `backend-filter-panel`, field/control desktop dibatasi lebar standarnya agar tidak terlalu panjang, input/select memakai tinggi kontrol ringkas, label uppercase kecil, dan spacing tidak boleh dibuat ulang di SCSS halaman.
- Lebar field filter desktop mengikuti shared rule `backend-filter-field`/`backend-filter-panel` dan tidak boleh dibuat full-width manual di SCSS halaman, kecuali layout mobile yang sudah diatur shared menjadi `100%`.
- Filter block yang berada di dalam panel/table section memakai modifier shared `backend-filter-panel--flush` agar tidak menambah card visual bersarang.
- Action ringkas di sisi kanan toolbar memakai `backend-page-toolbar__actions` dan link/button visual memakai `backend-toolbar-action`.
- Style filter/control toolbar hanya boleh didefinisikan di `resources/backend/scss/components/_backend-filter.scss` dan dimuat lewat `resources/backend/scss/app.scss`.
- Page-specific SCSS boleh mempertahankan class domain seperti `admin-dashboard-filter` atau `orders-admin-filter` sebagai hook, tetapi tidak boleh mendefinisikan ulang background, border, radius, padding, label typography, control border, atau focus state untuk filter toolbar yang polanya sama.

## Status Badge Standard

- Status badge backend memakai shared class `backend-status-badge`.
- Tone/status memakai modifier shared seperti `backend-status-badge--success`, `--warning`, `--danger`, `--info`, `--muted`, atau status slug seperti `--pending`, `--approved`, `--rejected`, `--active`, dan `--inactive`.
- Style status badge hanya boleh didefinisikan di `resources/backend/scss/components/_backend-status.scss` dan dimuat lewat `resources/backend/scss/app.scss`.
- Class domain seperti `orders-admin-status` atau `user-manager-badge` boleh dipertahankan sebagai hook, tetapi visual badge umum harus berasal dari shared `backend-status-badge`.

## Alert Standard

- Feedback/alert backend memakai wrapper `backend-feedback` dan item `backend-alert`.
- Tone alert memakai `backend-alert--success`, `backend-alert--danger`, atau `backend-alert--warning`.
- Style alert hanya boleh didefinisikan di `resources/backend/scss/components/_backend-alert.scss` dan dimuat lewat `resources/backend/scss/app.scss`.
- Page-specific SCSS tidak boleh membuat ulang background, border, radius, padding, atau color alert untuk pola feedback umum.

## Modal Standard

- Modal backend memakai wrapper `backend-modal` pada root `.modal` dan struktur shared `backend-modal__header`, `backend-modal__body`, dan `backend-modal__footer`.
- Style modal hanya boleh didefinisikan di `resources/backend/scss/components/_backend-modal.scss` dan dimuat lewat `resources/backend/scss/app.scss`.
- Class domain seperti `currency-admin-modal`, `transport-management-modal`, atau `transport-spk-detail-modal` boleh dipertahankan sebagai hook behavior/layout, tetapi tidak boleh mendefinisikan ulang border, radius, shadow, header/body/footer background, padding, atau layout dasar modal.
- Isi modal seperti grid form, detail list, map, QR, atau tabel tetap boleh memiliki style domain karena kontennya spesifik halaman.

## Button Standard

- Semua form backend wajib mengambil style dari `resources/backend/scss/components/_backend-form.scss`.
- Tombol utama halaman backend memakai warna brand dan hanya dipakai untuk aksi utama.
- Tombol `Cancel`, `Close`, dan tombol lain yang menutup modal tanpa menyimpan data harus memakai warna danger dari token `--backend-danger`.
- Semua tombol backend harus memakai hover/focus yang konsisten: lift ringan dari `--backend-button-hover-transform`, shadow dari `--backend-button-hover-shadow`, dan focus ring dari `--backend-button-focus-ring`.
- Tombol standar baru memakai `backend-button`, `backend-button-primary`, `backend-button-secondary`, dan `backend-button-danger`.
- Legacy Bootstrap `.btn`, `.btn-primary`, `.btn-success`, `.btn-secondary`, `.btn-light`, `.btn-default`, dan `.btn-danger` tetap dinormalisasi oleh shared form style selama migrasi.
- Icon tombol harus berada di dalam tombol yang sama dan mengikuti spacing shared; jangan membuat margin/icon color per halaman.
- Style dasar tombol backend berada di `resources/backend/scss/components/_backend-form.scss` dan harus tetap scoped ke `body.sidebar-light`.

## Form Control Standard

- Semua input, dropdown/select, textarea plain, checkbox, radio, label, help text, validation error, dan action row backend wajib memakai shared style dari `resources/backend/scss/components/_backend-form.scss`.
- Markup form baru/refactor memakai class standar:
  - `backend-form` untuk form wrapper.
  - `backend-form-grid` atau `backend-form-grid--compact` untuk layout field.
  - `backend-form-field` untuk setiap field wrapper.
  - `backend-form-label` untuk label eksplisit bila tidak memakai `<label>` langsung.
  - `backend-form-control` untuk input/select/textarea.
  - `backend-form-help` untuk teks bantuan.
  - `backend-form-error` untuk error custom selain `.invalid-feedback`.
  - `backend-form-actions` untuk area tombol submit/cancel.
- Class Bootstrap legacy seperti `.form-control`, `.custom-select`, `.form-group`, dan `.invalid-feedback` tetap didukung oleh shared style selama migrasi, tetapi halaman baru sebaiknya memakai class `backend-form-*`.
- Field wajib memakai label netral dengan indikator wajib `b` atau `.backend-required`; jangan mewarnai seluruh label menjadi merah.
- Dropdown/select memakai arrow dan spacing dari shared style. Jangan membuat ulang background image, padding kanan, border, height, atau focus ring di SCSS halaman.
- Checkbox backend tetap ditampilkan sebagai switch/toggle shared. Jika benar-benar membutuhkan checkbox native, harus ada alasan teknis jelas dan tidak boleh merusak visibility/focus.
- Radio backend memakai accent color brand shared dan tidak boleh dibuat ulang per halaman.
- State disabled, readonly, invalid, hover, focus, active, dan placeholder wajib berasal dari shared style.
- Form action baru memakai `backend-button-primary` untuk aksi submit utama, `backend-button-secondary` untuk aksi netral, dan `backend-button-danger` untuk cancel/delete/destructive. Untuk kompatibilitas, `backend-page-primary-action` dan `backend-toolbar-action` tetap dinormalisasi oleh shared style.
- SCSS halaman/domain tidak boleh mendefinisikan ulang properti visual dasar form seperti background, border, border-radius, min-height, padding, color, hover, focus ring, disabled, invalid, atau button hover. Class domain hanya boleh dipakai untuk layout spesifik.

## Table Action Button Standard

- Action button di dalam tabel/card backend wajib memakai shared class `backend-icon-action` dan wrapper `backend-table-actions`.
- Tone action dibedakan dari satu sumber di `resources/backend/scss/components/_backend-actions.scss`: detail/view memakai `backend-icon-action--view`, edit memakai `backend-icon-action--edit`, dan delete memakai `backend-icon-action--delete`.
- Untuk kompatibilitas halaman yang sedang migrasi, `backend-icon-action.is-danger` masih diperlakukan sebagai delete, tetapi halaman baru/refactor harus memakai modifier eksplisit `backend-icon-action--delete`.
- Warna action table tidak boleh didefinisikan ulang di SCSS halaman/domain. Class domain seperti `hotels-admin-actions`, `guides-admin-actions`, atau `drivers-admin-actions` hanya boleh menjadi hook layout/JavaScript.
- Mapping warna standar: detail/view biru, edit amber, delete merah; hover/focus tetap memakai shared lift, shadow, dan focus ring.

## Form Label Standard

- Label input backend harus memakai warna netral `--backend-muted-link`, bukan warna danger/merah.
- Tanda wajib seperti `*` harus memakai token `--backend-required`, sehingga hanya indikator wajib yang berwarna aksen dan bukan seluruh label.
- Pesan validasi/error tetap memakai token danger. Jangan memakai `.form-label`, `label`, atau `label span` dengan warna merah di asset halaman.
- Standard label backend berada di `resources/backend/scss/components/_backend-theme.scss` dan harus tetap scoped ke `body.sidebar-light`.

## Checkbox Standard

- Setiap checkbox backend harus terlihat sebagai toggle/switch yang jelas, bukan checkbox browser default.
- Checkbox backend memakai style global dari `resources/backend/scss/components/_backend-form.scss`, termasuk state checked, focus, disabled, dan kompatibilitas `.custom-control.custom-checkbox`.
- Jangan menambahkan rule halaman seperti `input[type="checkbox"] { width: auto; opacity: 0; position: absolute; }` yang membuat toggle tidak terlihat.
- Jika halaman membutuhkan label khusus untuk toggle, bungkus checkbox dan teks dalam label yang bisa diklik seluruhnya.

## Rich Text Area Standard

- Semua `textarea` pada halaman backend wajib otomatis memakai rich text editor dari satu sumber shared, yaitu initializer `initBackendRichText` di `resources/backend/js/app.js`.
- Initializer backend rich text berlaku untuk semua textarea di `body.sidebar-light .main-container` dan `body.sidebar-light .modal`, serta tetap kompatibel dengan class legacy `textarea_editor`.
- Textarea baru/refactor sebaiknya memakai attribute eksplisit `data-backend-richtext="true"` dan class form shared yang sesuai. Class legacy `textarea_editor` masih boleh ada selama migrasi, tetapi bukan standard final.
- Jika ada textarea yang benar-benar harus tetap plain text karena alasan teknis, wajib diberi `data-backend-richtext="false"` dan alasan penggunaannya harus jelas di review.
- Style editor harus berasal dari `resources/backend/scss/components/_backend-richtext.scss`; jangan membuat ulang border, toolbar, focus, typography, atau min-height editor di SCSS halaman.
- Initializer harus idempotent agar aman untuk modal dan dynamic content: jangan memanggil Summernote langsung dari Blade inline script.

## Table Display Standard

- Semua halaman backend wajib support multi-display: mobile, tablet, desktop, dan wide desktop.
- Semua tabel backend baru atau halaman backend yang sedang di-refactor wajib memakai class standar global dari `resources/backend/scss/components/_backend-theme.scss`: `.backend-table-wrap`, `.backend-table`, `.backend-table-actions`, `.backend-table-empty`, `.backend-table-card-list`, `.backend-table-card`, `.backend-table-card__header`, dan `.backend-table-card-grid`.
- Markup tabel standar tidak boleh membawa class visual Bootstrap/DataTables seperti `.table`, `.stripe`, `.hover`, `.table-bordered`, atau `.no-footer` sebagai styling utama. Jika DataTables dibutuhkan, init plugin boleh memakai selector `id`/data attribute; visual tetap dikendalikan oleh `.backend-table`.
- Halaman backend baru atau halaman backend yang sedang di-redesign tidak boleh bergantung pada horizontal scroll sebagai pengalaman utama. Gunakan responsive reflow seperti table-to-card, stacked detail rows, split panels, atau grid cards agar seluruh data tetap terlihat pada layar kecil.
- Tabel desktop boleh tetap dipakai untuk scanning cepat, tetapi tabel harus memakai `table-layout: fixed`, `width: 100%`, `min-width: 0`, dan cell harus mengizinkan teks panjang wrap dengan `overflow-wrap: anywhere`.
- Untuk layar tablet/mobile, data padat wajib punya alternate responsive view seperti card/list stack yang menampilkan label field dan value secara eksplisit. Jangan menyembunyikan kolom penting hanya untuk menghindari overflow.
- Wrapper seperti `.table-responsive`, `.table-container`, atau class domain yang mengandung `table-wrap` tetap boleh dipakai sebagai fallback untuk tabel legacy yang belum direfactor, tetapi bukan standar final untuk halaman baru.
- Class `.nowrap` hanya boleh dipakai untuk data pendek seperti kode, tanggal, status, atau action, dan tidak boleh membuat layout melebar keluar viewport.
- Action buttons pada tabel/card harus bisa wrap dan tetap dapat diklik pada layar kecil.
- Halaman baru tidak boleh mengandalkan viewport desktop saja. Cek mobile, tablet, desktop, dan wide desktop sebelum selesai.
- Standard global tabel backend berada di `resources/backend/scss/components/_backend-theme.scss`. Class domain halaman boleh ditambahkan untuk width kolom, status badge, atau detail visual yang benar-benar spesifik halaman, tetapi tidak boleh mengganti spacing, border, typography header, empty state, atau responsive table-to-card standar.

## Detail Layout and Context Side Panel Standard

- Semua halaman detail backend baru atau halaman detail backend yang sedang di-redesign wajib memakai `x-backend.detail-layout`.
- Layout detail standar memakai `backend-detail-layout`, `backend-detail-main`, dan `backend-detail-side` dari `resources/backend/scss/components/_backend-detail-layout.scss`.
- Area kiri (`backend-detail-main`) dipakai untuk konten utama seperti profile, gallery, pricing, tables, forms, atau audit trail.
- Area kanan (`backend-detail-side`) dipakai sebagai context side panel untuk informasi terkait yang membantu pengambilan keputusan cepat, misalnya status, validity, partner/vendor, capacity, gallery count, price rows, route/identity, owner, audit, dan quick actions.
- Side panel kanan harus memakai `backend-panel backend-detail-side-card`, header memakai `backend-section-header`, list ringkas memakai `backend-detail-side-list`, dan action memakai `backend-detail-side-actions`.
- Jangan membuat ulang side panel domain seperti `.activity-side`, `.tour-side`, `.transport-side`, atau grid custom untuk pola umum detail. Class domain seperti `activity-detail-context-panel` boleh ditambahkan hanya sebagai hook layout atau JavaScript.
- Side panel harus responsive: pada layar kecil layout turun menjadi satu kolom dan side panel tidak sticky. Aturan ini berasal dari shared component, bukan SCSS halaman.
- Jika halaman detail tidak memiliki informasi tambahan yang cukup, tetap tampilkan minimal status, owner/source, lifecycle/date, dan quick action agar posisi kanan tetap konsisten.

## Asset Rule

- Bundle backend global berada di `resources/backend/scss/app.scss` dan dimuat lewat `mix('build/backend/css/app.css')` di layout backend.
- Asset per halaman backend tetap dibuat di domain masing-masing, misalnya `resources/backend/scss/admin/panel`.
- Inline style/script hanya boleh dipakai untuk data kecil dari Blade yang tidak layak menjadi file asset sendiri.

## New Backend Page Checklist

Gunakan checklist ini setiap membuat halaman backend baru atau refactor halaman backend lama. Tujuannya sederhana: setiap halaman backend harus terasa seperti satu produk yang sama, bukan kumpulan halaman dengan selera visual masing-masing.

- View backend baru harus berada di namespace backend/domain yang sesuai, misalnya `resources/views/backend/admin/...`, `resources/views/backend/operations/...`, atau path legacy `resources/views/admin/...` hanya bila route lama belum bisa dipindahkan.
- Layout internal harus memuat bundle backend global dari `resources/backend/scss/app.scss`; jangan membuat bundle global kedua untuk theme backend.
- Hero halaman wajib memakai `x-backend.page-hero`. Aksi utama di hero memakai `backend-page-primary-action`; aksi sekunder/preview yang bukan primary ditempatkan di `backend-page-toolbar__actions` dengan `backend-toolbar-action`.
- Breadcrumb wajib memakai wrapper `backend-page-toolbar` dan root Admin Panel memakai `route('view.admin-panel-main')`.
- KPI memakai `backend-kpi-grid` dan `backend-kpi-card`; jangan membuat visual KPI baru dengan class domain seperti `*-stat`, `*-stats`, atau summary card custom untuk pola KPI umum.
- Panel umum memakai `backend-panel`; header panel memakai `backend-section-header` dan label kecil memakai `backend-section-header__label`.
- Halaman detail wajib memakai `x-backend.detail-layout`; konten utama berada di `backend-detail-main` dan informasi terkait/quick action berada di `backend-detail-side`.
- List ringkas memakai `backend-list`, `backend-list-item`, dan `backend-list-item__meta`; empty state memakai `backend-empty-state`.
- Filter ringkas memakai `backend-toolbar-filter`; filter block memakai `backend-filter-panel`, `backend-filter-grid`, `backend-filter-field`, `backend-filter-control`, `backend-filter-search`, dan `backend-filter-actions`.
- Textarea backend otomatis menjadi rich text dari shared initializer `initBackendRichText`; textarea baru/refactor sebaiknya menambahkan `data-backend-richtext="true"`.
- Status memakai `backend-status-badge` beserta modifier tone/status shared. Class domain boleh ditambahkan hanya sebagai hook JavaScript/layout.
- Feedback memakai `backend-feedback` dan `backend-alert backend-alert--success|danger|warning`.
- Modal memakai `backend-modal`, `backend-modal__header`, `backend-modal__body`, dan `backend-modal__footer`.
- Page-specific SCSS hanya boleh mengatur layout domain, grid, width kolom, chart, atau spacing khusus isi halaman. Jangan mendefinisikan ulang background, border, radius, shadow, typography, focus state, atau warna untuk primitive shared.
- Jika pola UI baru dipakai atau berpotensi dipakai lebih dari satu halaman, buat shared class `backend-*` di `resources/backend/scss/components`, import dari `resources/backend/scss/app.scss`, dokumentasikan di file ini, dan tambahkan assertion struktur.
- Setelah halaman distandardisasi, update `docs/backend-ui-standardization-roadmap.md` dan jalankan test struktur yang relevan.

## Backend UI PR Review Checklist

Checklist ini wajib dipakai saat review PR yang menyentuh backend UI.

- [ ] Perubahan memakai baseline Dashboard dan shared style dari `resources/backend/scss/components`.
- [ ] Tidak ada visual primitive baru di SCSS halaman untuk hero, KPI, panel, list, empty state, toolbar, filter, status badge, alert, modal, form control, table, atau button tanpa alasan dan dokumentasi.
- [ ] Semua shared style baru diimport lewat `resources/backend/scss/app.scss`.
- [ ] View memakai class shared `backend-*` untuk pola umum dan class domain hanya untuk hook layout/JavaScript.
- [ ] Halaman tetap responsive di mobile, tablet, desktop, dan wide desktop tanpa horizontal scroll sebagai pengalaman utama.
- [ ] Copy UI backend memakai language key sesuai `docs/multi-language-standard.md` bila teks tampil ke user internal.
- [ ] Roadmap `docs/backend-ui-standardization-roadmap.md` diperbarui sesuai progress.
- [ ] Test struktur atau assertion guard ditambahkan/diperbarui untuk mencegah regresi style.
- [ ] `php artisan view:cache`, test terarah, dan build asset dijalankan sesuai scope perubahan.
