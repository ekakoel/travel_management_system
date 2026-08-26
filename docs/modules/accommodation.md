# Accommodation Module

Status: active
Updated: 2026-08-21

## Scope

Accommodation mencakup service publik `Hotel`, `Hotel Promo`, dan `Hotel Package`, dari listing/detail sampai booking, reservation, invoice, payment, completion, dan history.

## Boundary

- Tidak mencakup Private Villa.
- Gunakan ownership dan service guard pada route order/payment bersama.
- Harga authoritative dihitung server-side.
- Availability dan inventory kamar harus diverifikasi pada saat write, bukan hanya saat render halaman.

## Kontrak Aktif

- Pricing: `docs/decisions/accommodation-pricing-contract.md`
- Status dan fulfillment final: `docs/status-contract.md`
- Kontrak Accommodation lama:
  `docs/decisions/accommodation-status-contract.md` (`superseded`)
- Security: `docs/security-rules.md`
- Database dan testing: `docs/database.md`, `docs/testing.md`
- Frontend booking contract:
  `docs/decisions/accommodation-frontend-booking-standard.md`

## Commercial Status dan Fulfillment

- Commercial order mengikuti
  `Draft -> Pending -> Approved -> Paid`, dengan terminal
  `Canceled`, `Rejected`, `Invalid`, atau `Deleted`.
- Order Accommodation yang selesai tetap memiliki `orders.status = Paid`.
- Completion tidak menulis `Completed` ke `orders.status`; completion mengisi
  `orders.completed_at` dan actor manual pada `orders.completed_by`.
- Reservation mengikuti `Pending -> Active -> Completed`, dengan cabang
  `Canceled`.
- Current mencakup order non-terminal dengan `completed_at = null`.
- Completed History memerlukan `completed_at != null`; Closed History memakai
  terminal commercial state. Checkout yang lewat saja tidak cukup untuk
  menyatakan fulfillment selesai.
- Completion berdasarkan checkout/manual eligibility harus atomic dan
  idempotent.

## Audit dan Implementasi

- Authorization/IDOR: `docs/decisions/accommodation-authorization-idor-audit.md`
- Status lifecycle audit:
  `docs/decisions/accommodation-status-lifecycle-audit.md` (`historical`)
- Roadmap end-to-end: `docs/decisions/service-booking-flow-audit-roadmap.md`

## Flow Verifikasi

```text
Listing/detail -> price -> availability -> booking -> reservation review
-> confirmation -> invoice -> payment -> upcoming -> completion -> history
```

Setiap perubahan wajib menelusuri route, middleware, controller/request/service, model/query, view/asset, dan redirect/response.

## Frontend Hotel Availability Pricing

- Halaman frontend `hotel-price-{code}` wajib mencari Normal Price, Promo
  Price, dan Package Price berdasarkan selected stay window yang sama.
- Normal Price query wajib mengambil rate Hotel + active Room yang overlap
  dengan stay dates (`start_date <= last stay night` dan
  `end_date >= checkin`), lalu per malam tetap dicocokkan secara server-side.
- Promo Price query wajib mengambil promo Hotel + active Room yang sedang dalam
  booking period, status `Active`, dan overlap dengan selected stay dates
  (`periode_start <= last stay night` dan `periode_end >= checkin`). Promo yang
  tidak menutup semua malam tetap boleh tampil sebagai promo mixed-rate bila
  malam lain memiliki Normal Price fallback.
- Package Price query wajib mengambil package Hotel + active Room dengan
  status `Active`, duration sesuai selected stay, dan menutup full stay window
  (`stay_period_start <= checkin` dan `stay_period_end >= last stay night`).
- Availability display tidak boleh memakai query yang hanya cocok pada checkin
  date ketika flow membutuhkan full stay coverage. Order creation tetap wajib
  menghitung ulang seluruh total melalui `HotelPricingService`.

## Hotel Package Cancellation Policy

- Hotel Package menyimpan cancellation policy per locale melalui
  `cancellation_policy`, `cancellation_policy_traditional`, dan
  `cancellation_policy_simplified`.
- Field bersifat nullable untuk kompatibilitas data lama. Saat policy package
  kosong, order baru memakai policy hotel induk sebagai fallback.
- Saat order dibuat, ketiga versi policy disalin ke `orders` sebagai snapshot.
  Detail order wajib membaca snapshot sesuai locale agar perubahan package
  berikutnya tidak mengubah ketentuan booking historis.

## Backend Room Management

- Backend Add Room dan Edit Room mengikuti
  `docs/decisions/backend-page-layout-standard.md` dengan layout dua kolom
  canonical: main content 70% dan right sidebar 30%.
- Add Room dan Edit Room harus mempertahankan information architecture yang
  sama: Cover / Media, Basic Information, Occupancy and Inventory, lalu Content
  and Translations.
- Edit Room menampilkan parent Hotel sebagai read-only context. Relasi Room ke
  Hotel tetap authoritative dari record `hotel_rooms.hotels_id` dan tidak boleh
  dipindahkan melalui hidden input dari form edit.
- `hotel_rooms.inventory` adalah master stock field yang dapat diedit sesuai
  validasi Room master. Form edit tidak menghitung booked room, blocking
  reservation, atau available inventory langsung di Blade.
- Field multilingual Room dengan pola `field`, `field_traditional`, dan
  `field_simplified` wajib tampil dalam translation group horizontal:
  English, Traditional Chinese, Simplified Chinese.
- Backend Hotel detail menampilkan Room/Suites & Villa detail melalui modal
  `backend-modal-detail` dua kolom: cover image di kiri dan author-facing Room
  summary, capacity, inventory, view, bed, size, serta copy terkait di kanan.
  Modal ini hanya membaca Room master data; pricing, promo, booking
  availability, order, dan reservation lifecycle tetap dikelola pada flow
  masing-masing.
- Backend Hotel detail menampilkan toggle status manual pada setiap Room di
  bagian Inventory Suites & Villa. Toggle ditempatkan sebelum action detail
  Room, memakai reusable `data-backend-status-toggle`, endpoint PATCH,
  validation `Active` / `Draft`, transaction, dan audit log. Toggle hanya
  mengubah lifecycle Room master; tidak mengubah Hotel status audit, Normal
  Price, Promotion Price, Package Price, order, reservation, atau payment data.
  Status `Archived` tetap dikelola melalui edit Room dan tidak diaktifkan
  kembali oleh toggle Active/Draft.

## Backend Hotel Gallery Management

- Backend Edit Hotel Gallery mengikuti
  `docs/decisions/backend-page-layout-standard.md` dengan layout dua kolom:
  main gallery management dan right admin sidebar.
- Upload gallery wajib melalui endpoint gallery khusus, bukan melalui update
  Hotel profile. Form gallery tidak boleh mengirim hidden field Hotel profile,
  pricing, room, promo, order, reservation, atau payment.
- Upload flow canonical:
  `Validated UploadedFile -> Storage public disk -> hotels_images` record.
  Database tetap menyimpan filename gallery untuk kompatibilitas URL existing
  `storage/hotels/hotels-galery/{filename}`.
- File gallery harus memakai generated safe filename. Original filename hanya
  boleh dipakai sebagai UI label lokal dan tidak boleh menjadi storage name.
- Delete gallery wajib memvalidasi authorization dan ownership melalui relasi
  Hotel ke `hotels_images` sebelum record atau file dihapus.
  Image Hotel A tidak boleh bisa dihapus melalui route Hotel B.
- Sidebar gallery hanya berisi Hotel context, summary, upload guidance, cover
  context read-only, dan related actions. Core profile/pricing/room data tetap
  dikelola di halaman masing-masing.

## Backend Hotel Status Refresh

- Backend Hotel index menyediakan action eksplisit untuk refresh/audit status
  Hotel berdasarkan data harga valid dari normal price, promo price, dan
  package price. Halaman index tidak boleh melakukan mutasi status otomatis
  saat hanya dibuka.
- Audit status mengecek Hotel aktif/draft yang tidak berstatus `Archived` atau
  `Removed`. Hotel dengan setidaknya satu harga valid dari `hotel_prices`,
  `hotel_promos`, atau `hotel_packages` menjadi `Active`; Hotel tanpa harga
  valid dari ketiga sumber tersebut menjadi `Draft`.
- Room milik Hotel yang diaudit mengikuti status Hotel hasil audit, kecuali
  Room berstatus `Archived` harus tetap dipertahankan dan tidak diaktifkan
  kembali secara otomatis.
- Action refresh wajib memakai POST, authorization backend, transaction, audit
  log, dan redirect kembali ke Hotel index agar halaman menampilkan data
  terbaru setelah proses selesai.
- Audit log untuk refresh status global memakai `subservice_id = 0` karena
  `user_logs.subservice_id` wajib terisi dan action ini tidak mewakili satu
  Hotel tertentu.
- Tombol refresh memakai shared backend submit/loading spinner
  `data-backend-action-loading`, bukan spinner inline atau custom per halaman.

## Backend Hotel Detail Normal Price Grouping

- Backend Hotel detail menampilkan Normal Price dalam group per Room.
- Rate Plan Normal Price di backend detail hanya boleh menampilkan Normal Price
  yang stay period end-nya belum terlewat dari tanggal berjalan
  (`hotel_prices.end_date >= today`). Data ini harus berasal dari dataset
  current/table, bukan dataset historical KPI chart.
- Setiap group Room menampilkan daftar normal price Room tersebut dan daftar
  price wajib diurutkan berdasarkan stay period: `start_date`, lalu `end_date`,
  lalu record id sebagai penentu stabil.
- Card/group Normal Price juga wajib diurutkan berdasarkan stay period paling
  awal di dalam group tersebut, lalu `end_date`, lalu nama Room sebagai fallback
  stabil.
- Edit Normal Price dilakukan melalui modal di backend Hotel detail agar admin
  dapat memperbarui Room, stay period, `contract_rate`, `markup`, dan
  `kick_back` tanpa meninggalkan workspace Hotel. Modal submit ke endpoint
  update Normal Price existing dan tetap memakai validasi server-side,
  transaction, audit log, room ownership check, serta overlap validator.
- Halaman GET edit Normal Price terpisah tidak digunakan lagi. Jangan
  menambahkan kembali page `edit-hotel-price/{id}` kecuali ada keputusan
  layout baru yang secara eksplisit membatalkan modal edit di detail Hotel.
- Grouping dan sorting dilakukan melalui Hotel detail view-model agar Blade
  tidak berisi business/data-shaping logic dan relasi Room tetap memakai data
  eager-loaded dari detail query.
- Normal Price saat ini tidak memiliki kolom `status` pada `hotel_prices`, baik
  di migration maupun live schema yang diverifikasi secara read-only pada
  2026-08-21. Tabel Normal Price di backend detail tidak menampilkan kolom
  Status dan jangan menampilkan toggle Active/Draft sampai ada additive schema
  decision dan pricing contract yang menjelaskan dampaknya pada public
  availability, Hotel status audit, dan historical price behavior.

## Backend Hotel Detail Extra Bed Management

- Backend Hotel detail menampilkan Extra Bed sebagai panel Pricing Add-on
  modern di workspace detail Hotel, bukan melalui view legacy
  `resources/views/admin/extrabed.blade.php`.
- Create, Edit, dan Delete Extra Bed dilakukan melalui modal di detail Hotel
  memakai route existing `func.extrabed.add`, `func.extrabed.edit`, dan
  `func.extrabed.delete` agar tidak menambah lifecycle route baru.
- Controller Extra Bed wajib memakai Form Request, authorization backend,
  validasi server-side, transaction, lock saat update/delete, dan
  `HotelAuditService`. Form tidak boleh mengirim hidden `author`, published
  rate, atau calculated total dari browser sebagai nilai authoritative.
- Extra Bed saat ini adalah add-on level Hotel melalui `extra_beds.hotels_id`.
  Jangan mengasumsikan relasi Room-level Extra Bed kecuali ada additive schema
  decision yang menambahkan dan mendokumentasikan kolom ownership Room.
- Backend published rate hanya untuk preview admin dan wajib dibentuk dari
  `HotelPricingService::rateBreakdown`. Public Accommodation booking tetap
  mengambil Extra Bed dari database dan menghitung total server-side sebelum
  masuk ke order/reservation pricing service.

## Backend Hotel Detail Additional Charge Management

- Backend Hotel detail menampilkan Additional Charge sebagai panel modal CRUD
  di workspace detail Hotel. Create dan Edit tidak memakai halaman GET
  terpisah.
- Route GET `admin.hotels.additional-charges.create` dan
  `admin.hotels.additional-charges.edit` tidak digunakan lagi. CRUD modal tetap
  submit ke route mutation existing `admin.hotels.additional-charges.store`,
  `admin.hotels.additional-charges.update`, dan
  `admin.hotels.additional-charges.destroy`.
- Controller Additional Charge wajib berada di
  `Backend\Operations\Hotels\HotelAdditionalChargeAdminController`, memakai
  Form Request, authorization backend, transaction, lock saat update/delete,
  dan `HotelAuditService`.
- Create memakai `hotel_id` dari modal detail Hotel. Update dan delete wajib
  mengambil Hotel ownership dari record `optional_rates.hotels_id` atau
  `optional_rates.service_id`, bukan dari hidden field browser.
- Form modal tidak boleh mengirim hidden `author`, published rate, calculated
  total, atau ownership update untuk record existing. Pricing preview tetap
  memakai `HotelPricingService::rateBreakdown`.

## Backend Hotel Detail Calculation Modal

- Calculation modal pada Hotel detail wajib memakai shared partial
  `backend.operations.hotels.partials.price-breakdown`.
- Modal menampilkan summary Agent Rate sebagai card penuh selebar dua kolom,
  dengan USD sebagai nilai utama dan IDR di bawahnya. Published Rate tidak
  ditampilkan sebagai summary terpisah pada modal calculation.
- Nilai USD dan IDR wajib berasal dari `HotelPricingService::rateBreakdown`;
  Blade tidak boleh menghitung published/agent rate sendiri.
- Breakdown contract, markup, tax, formula, dan kickback tetap berada di bawah
  summary rate dalam grid dua kolom. Markup, Tax, dan Formula wajib
  menampilkan nilai USD di atas dan nilai IDR di bawah.

## Backend Add Hotel Normal Price

- Backend Add Hotel Normal Price mengikuti
  `docs/decisions/backend-page-layout-standard.md` dengan canonical two-column
  layout: main form 70% dan right admin sidebar 30%.
- Create Normal Price memakai Hotel context dari route create dan Room options
  yang di-query hanya untuk Hotel tersebut. Store menerima `hotels_id` untuk
  kompatibilitas form, tetapi wajib memvalidasi `hotel_context` terenkripsi dan
  memastikan Room benar-benar milik Hotel sebelum record dibuat.
- Normal Price tidak memiliki lifecycle status. Form create tidak menerima
  hidden status, author, calculated price, published rate, net rate, ownership,
  atau pricing result dari browser sebagai nilai authoritative.
- Price period memakai `start_date` dan `end_date`. Semua date field memakai
  canonical backend picker `data-backend-picker="date"` dan server validation
  memastikan end date tidak lebih awal dari start date.
- Normal Price CRUD menolak overlap period untuk Hotel + Room yang sama melalui
  `HotelNormalPriceOverlapValidator` agar order normal selalu memiliki tepat
  satu rate authoritative per stay night.
- Pricing form hanya menyimpan master input `contract_rate`, `markup`, dan
  `kick_back` memakai canonical money control. Halaman create tidak membuat
  formula published rate baru di Blade, JavaScript, atau controller.

## Backend Hotel Detail Promotion Price Grouping

- Backend Hotel detail menampilkan Promotion Price dalam group per Room.
- Panel Promotion Price di backend detail harus menampilkan semua Promo Price
  yang booking period end-nya belum terlewat dari tanggal berjalan
  (`hotel_promos.book_periode_end >= today`). Panel ini tidak boleh
  menyembunyikan promo hanya karena stay period end (`periode_end`) sudah lewat.
- Setiap group Room menampilkan daftar promo Room tersebut dan daftar promo
  wajib diurutkan berdasarkan stay period: `periode_start`, lalu `periode_end`,
  lalu nama promo dan record id sebagai penentu stabil.
- Card/group Promotion Price juga wajib diurutkan berdasarkan stay period start
  paling awal di dalam group tersebut, lalu stay period end, lalu nama Room
  sebagai fallback stabil.
- Booking period, published rate, status, edit/delete actions, dan calculation
  modal tetap ditampilkan per promo row di dalam group Room.
- Promotion Price row menyediakan reusable backend status toggle untuk mengubah
  status `Active` atau `Draft` via AJAX tanpa refresh halaman. Pada table Promo,
  kolom Status menampilkan toggle berlabel status tanpa badge terpisah.
  Endpoint status wajib memakai PATCH, authorization backend, validation status
  eksplisit, transaction, dan `UserLog`. Toggle hanya mengubah lifecycle Promo
  row; tidak menghitung ulang pricing, tidak mengubah HotelPrice, Package,
  order, reservation, atau payment data.
- Backend Hotel detail mengganti KPI Contract dan KPI Pricing Rows dengan chart
  Pricing Agent Rate tahunan berjalan. Chart menampilkan Agent Rate tertinggi
  per bulan dari awal tahun sampai bulan berjalan untuk Normal Price, Promo
  Price, dan Package Price tanpa memfilter status `Active` / `Draft` atau data
  historis yang sudah expired. Grafik ini ditujukan untuk membaca rate history
  sepanjang tahun, sehingga price lama yang sudah menjadi `Draft` tetap harus
  ikut sebagai sumber chart. Backend detail wajib mengambil dataset chart dari
  query year to date tersendiri yang overlap dengan awal tahun sampai tanggal
  berjalan, bukan dari eager load table yang memakai `notExpired`. Dataset
  chart historical tidak boleh dipakai untuk Rate Plan table; table tetap
  memakai dataset current sesuai aturan masing-masing panel. Setiap series
  memakai warna line berbeda, legend, serta price range scale. Jika dalam satu bulan terdapat
  beberapa price dari beberapa Room, titik curve wajib memakai Agent Rate
  termahal pada bulan tersebut. Khusus Package Price, nilai chart wajib
  dinormalisasi menjadi Agent Rate per durasi package terlebih dahulu sebelum
  dibandingkan dengan package lain; normalisasi ini hanya berlaku untuk KPI
  chart dan tidak mengubah package pricing public, calculation modal, order,
  reservation, atau payment. Data chart wajib memakai
  `HotelDetailViewModel::pricingAgentRateChart()` dan
  `HotelPricingService::rateBreakdown()` sebagai source of truth. Blade hanya
  merender data chart dan SVG yang sudah disiapkan. Label bulan wajib dirender
  di dalam SVG memakai koordinat titik chart agar sejajar dengan curve, bukan
  sebagai grid HTML terpisah. JavaScript tidak boleh membuat formula pricing
  baru untuk chart ini.

## Backend Add Hotel Promo

- Backend Add Hotel Promo mengikuti
  `docs/decisions/backend-page-layout-standard.md` dengan canonical two-column
  layout: main form 70% dan right admin sidebar 30%.
- Source of truth untuk Promotion Price saat ini adalah record `hotel_promos`.
  Tidak ada tabel/model `HotelPromoPrice` terpisah. Satu record `hotel_promos`
  menyimpan konteks promotion, booking period, stay period, Room, master
  pricing input, status, dan customer-facing copy.
- Create Promo memakai Hotel context dari route create dan Room options yang
  di-query hanya untuk Hotel tersebut. Store menerima `hotels_id` untuk
  kompatibilitas form, tetapi wajib memvalidasi `hotel_context` terenkripsi dan
  memastikan Room benar-benar milik Hotel sebelum record dibuat.
- Promo baru selalu dibuat sebagai `Draft` oleh server. Form create tidak
  menerima hidden status, author, calculated price, atau pricing result dari
  browser sebagai nilai authoritative.
- Booking period memakai `book_periode_start` dan `book_periode_end`; stay
  period memakai `periode_start` dan `periode_end`. Semua date field memakai
  canonical backend picker `data-backend-picker="date"` dan server validation
  memastikan end date tidak lebih awal dari start date.
- Pricing form hanya menyimpan master input `contract_rate` dan `markup`
  memakai canonical money control. Halaman create tidak membuat formula
  published rate baru di Blade, JavaScript, atau controller.
- Customer-facing copy Promo mengikuti translation group horizontal untuk
  `benefits`, `include`, dan `additional_info` dalam urutan English,
  Traditional Chinese, Simplified Chinese.
- `hotel_promos` tidak memiliki cancellation policy columns, sehingga Add Promo
  tidak menampilkan field cancellation policy. Cancellation policy untuk order
  Hotel Promo tetap mengikuti Hotel induk sesuai flow booking accommodation.

## Backend Edit Hotel Promo

- Backend Edit Hotel Promo mengikuti information architecture yang sama dengan
  Add Hotel Promo: Basic Information, Promotion Period, Pricing Inputs, lalu
  Benefits and Inclusions.
- Edit Promo memakai parent Hotel dari record `hotel_promos.hotels_id`.
  Request tetap mengirim `hotels_id` untuk kompatibilitas form, tetapi update
  wajib memvalidasi `hotel_context` terenkripsi, menolak Hotel context yang
  tidak cocok dengan record Promo, dan memastikan Room benar-benar milik Hotel
  tersebut sebelum record diperbarui.
- Status Promo dapat diedit sebagai master lifecycle field dengan nilai
  eksplisit `Active` atau `Draft`. Sidebar Edit hanya menampilkan current
  status dan metadata sebagai read-only admin context.
- Booking period tetap memakai `book_periode_start` dan `book_periode_end`;
  stay period tetap memakai `periode_start` dan `periode_end`. Semua date field
  memakai canonical backend picker `data-backend-picker="date"` dan server
  validation memastikan end date tidak lebih awal dari start date.
- Pricing form Edit hanya menyimpan master input `contract_rate` dan `markup`
  memakai canonical money control. Edit Promo tidak membuat formula published
  rate baru di Blade, JavaScript, atau controller.
- Update Promo berjalan dalam transaction dengan lock terhadap record Promo dan
  audit `UserLog`. Authenticated actor menjadi sumber authoritative untuk
  `author`; form tidak boleh mengirim hidden `author`.
- Customer-facing copy Promo Edit mengikuti translation group horizontal untuk
  `benefits`, `include`, dan `additional_info` dalam urutan English,
  Traditional Chinese, Simplified Chinese.

## Backend Hotel Detail Package Price Grouping

- Backend Hotel detail menampilkan Package Price dalam group per Room.
- Panel Package Price di backend detail hanya boleh menampilkan Package Price
  yang stay period end-nya belum terlewat dari tanggal berjalan
  (`hotel_packages.stay_period_end >= today`).
- Setiap group Room menampilkan daftar package Room tersebut dan daftar package
  wajib diurutkan berdasarkan stay period: `stay_period_start`, lalu
  `stay_period_end`, lalu nama package dan record id sebagai penentu stabil.
- Card/group Package Price juga wajib diurutkan berdasarkan stay period start
  paling awal di dalam group tersebut, lalu stay period end, lalu nama Room
  sebagai fallback stabil.
- Tabel Package Price wajib menampilkan Published Rate per night sebagai angka
  utama. Package total tetap harus ditampilkan sebagai konteks di bawah rate.
- Calculation modal Package Price wajib memakai breakdown per-night allocation
  sebagai angka utama, dengan Package Total tetap ditampilkan di summary modal.
  Perubahan ini hanya untuk tampilan backend detail dan tidak mengubah package
  pricing public, order snapshot, reservation, invoice, atau payment.
- Summary calculation modal Package Price wajib menampilkan `Agent Rate Per
  Night` dan `Package Total` sejajar sebagai dua kartu satu kolom masing-masing
  pada desktop/tablet, lalu menumpuk satu kolom pada mobile.
- Duration, published rate, status, edit/delete actions, dan calculation modal
  tetap ditampilkan per package row di dalam group Room.
- Package Price row menyediakan reusable backend status toggle untuk mengubah
  status `Active` atau `Draft` via AJAX tanpa refresh halaman. Endpoint status
  wajib memakai PATCH, authorization backend, validation status eksplisit,
  transaction, dan `UserLog`. Toggle hanya mengubah lifecycle Package row;
  tidak menghitung ulang pricing, tidak mengubah Normal Price, Promotion Price,
  order, reservation, atau payment data.

## Backend Add/Edit Hotel Package

- Backend Add dan Edit Hotel Package mengikuti
  `docs/decisions/backend-page-layout-standard.md` dengan canonical two-column
  layout: main form 70% dan right admin sidebar 30%.
- Source of truth Package Price saat ini adalah record `hotel_packages`.
  Tidak ada tabel/model `HotelPackagePrice` terpisah pada flow backend Package
  ini. Satu record `hotel_packages` menyimpan parent Hotel, Room, package
  name, duration, stay period, master pricing inputs, status, customer-facing
  copy, dan package-level cancellation policy.
- Add dan Edit Package memakai information architecture yang sama: Basic
  Information, Package Configuration, Availability / Stay Period, Pricing
  Inputs, Benefits and Inclusions, lalu Cancellation Policy.
- Create Package memakai Hotel context dari route create dan Room options yang
  di-query hanya untuk Hotel tersebut. Store menerima `hotels_id` untuk
  kompatibilitas form, tetapi wajib memvalidasi `hotel_context` terenkripsi dan
  memastikan Room benar-benar milik Hotel sebelum record dibuat.
- Edit Package memakai parent Hotel dari record `hotel_packages.hotels_id`.
  Update wajib menolak Hotel context yang tidak cocok dengan record Package dan
  memastikan Room yang dipilih tetap milik Hotel tersebut.
- Package baru selalu dibuat sebagai `Draft` oleh server. Form create tidak
  menerima hidden status, author, calculated price, atau pricing result dari
  browser sebagai nilai authoritative.
- Status Package pada Edit dapat diubah hanya ke nilai lifecycle existing
  `Active` atau `Draft`. Authenticated actor menjadi source authoritative untuk
  `author`; form tidak boleh mengirim hidden `author`.
- Package tidak memiliki booking period terpisah. Availability memakai
  `stay_period_start` dan `stay_period_end`; semua date field memakai canonical
  backend picker `data-backend-picker="date"` dan server validation memastikan
  end date tidak lebih awal dari start date.
- Pricing form hanya menyimpan master input `contract_rate`, `markup`, dan
  `duration` memakai canonical money controls untuk monetary fields. Halaman
  Add/Edit tidak membuat formula published rate baru di Blade, JavaScript, atau
  controller.
- Create dan Update Package berjalan dalam transaction, memakai audit
  `UserLog`, dan update memakai lock terhadap record Package. Operation ini
  tidak mengubah Hotel master, Room inventory, Normal Price, Promotion Price,
  order, reservation, atau payment data.
- Customer-facing copy Package mengikuti translation group horizontal untuk
  `benefits`, `include`, `additional_info`, dan `cancellation_policy` dalam
  urutan English, Traditional Chinese, Simplified Chinese.
