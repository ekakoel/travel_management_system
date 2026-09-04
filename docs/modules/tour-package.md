# Tour Package Module

Status: active
Updated: 2026-08-11

Dokumen ini adalah pintu masuk modul Tour Packages, termasuk aturan data lokasi dan map yang sudah aktif.

Flow yang wajib dijaga:

```text
Listing/detail -> price -> availability -> booking -> reservation review
-> confirmation -> invoice -> payment -> upcoming -> completion -> history
```

Referensi lifecycle dan keamanan:

- `docs/status-contract.md`
- `docs/security-rules.md`
- `docs/database.md`
- `docs/testing.md`
- `docs/decisions/shared-order-status-audit.md` (`historical`)
- `docs/decisions/service-booking-flow-audit-roadmap.md`

## Commercial Status dan Fulfillment

- Commercial order mengikuti
  `Draft -> Pending -> Approved -> Paid`, dengan terminal
  `Canceled`, `Rejected`, `Invalid`, atau `Deleted`.
- Tour Package yang selesai tetap memiliki `orders.status = Paid`.
- Completion mengisi `orders.completed_at` dan actor manual pada
  `orders.completed_by`; `Completed` tidak ditulis ke `orders.status`.
- Reservation mengikuti `Pending -> Active -> Completed`, dengan cabang
  `Canceled`.
- Current adalah order non-terminal dengan `completed_at = null`.
- Completed History memerlukan `completed_at != null`; Closed History memakai
  terminal commercial state. Tanggal tour yang lewat saja bukan bukti
  fulfillment selesai.
- Completion harus atomic dan idempotent.
- Scheduler auto-cancel order unpaid adalah proses berbeda dari fulfillment
  completion order `Paid`.

## Map Data Model

Domain map tour memakai model/relasi terkait:

- `Tours`
- `TourPackageLocation`
- `TourLocationReference`
- service `App\Services\Tours\TourLocationService`

Lokasi harus disimpan sebagai data terstruktur, bukan hanya teks bebas di Blade.

## Admin Usage

- Create/edit tour memakai Form Request dan `TourLocationService`.
- Daftar dan detail inventory Tour Package dapat dibaca oleh admin berposisi
  developer, author, atau reservation; mutation tetap dibatasi untuk developer
  dan author.
- Navigasi Services memakai `ServiceNavigationRegistry`. Slug canonical
  `tour-packages` dan alias legacy `tours`, `tour`, serta `tour-package`
  mengarah ke `view.tour-packages-service` dan
  `admin.tour-packages.index`; Blade tidak boleh merangkai nama route dari
  slug. Hanya registry berstatus `Active` dengan named route valid yang tampil.
- Location repeater behavior berada di JS domain Tours, bukan inline script.
- Validasi koordinat, urutan itinerary, nama lokasi, dan referensi lokasi dilakukan sebelum sync.
- Cover/marker/gambar mengikuti upload validation domain tour.

## Backend Add Tour Standard

Backend Add Tour adalah Create page untuk master `Tours` saja. Field yang
dibuat pada tahap ini adalah:

- master data: cover, code, translated name, type, duration days, dan duration
  nights;
- structured route map locations melalui `TourLocationService`;
- setiap structured destination dapat menyimpan customer-facing description
  dalam English, Traditional Chinese, dan Simplified Chinese melalui kolom
  `description`, `description_traditional`, dan `description_simplified` pada
  `tour_package_locations`;
- translated customer-facing content: short description, description, package
  highlights, include, exclude, additional information, dan cancellation
  policy;
- server-authoritative metadata: initial `status = Draft`, authenticated actor,
  storage filename, route coordinates, and audit log.

`TourPrice` adalah resource pricing terpisah. Add Tour tidak boleh membuat,
mengubah, menghitung, atau menampilkan formula TourPrice. Price tiers dikelola
dari halaman detail Tour setelah master Tour dibuat, sedangkan public quote dan
order tetap memakai canonical Tour Package pricing service.

Create Tour wajib memakai backend Create/Edit/Detail layout standard dengan
wizard ringan project-native:

- `x-backend.page-hero`;
- `x-backend.breadcrumb-toolbar`;
- `x-backend.detail-layout` dengan main/sidebar 70%/30%;
- main column berisi wizard 5 step: Basic Information, Route & Itinerary,
  Content & Translations, Media, dan Review & Create;
- final persistence tetap satu `POST /fadd-tour`; wizard tidak membuat draft
  record per step dan tidak memakai autosave;
- Route & Itinerary bersifat optional pada create. `0` lokasi valid dan tidak
  boleh dibuat gagal oleh placeholder default seperti `day_number = 1` atau
  `visit_order = 1`;
- Create Tour tidak menampilkan atau menerima input manual `itinerary`,
  `itinerary_traditional`, atau `itinerary_simplified`. Untuk Tour baru,
  structured route locations adalah source utama itinerary dan
  `BuildsTourLocationItinerary` menghasilkan output saat data dibaca/render.
- kolom manual itinerary pada tabel `tours` tetap dipertahankan untuk
  backward compatibility, Edit Tour legacy, order snapshot historis, dan
  fallback Tour lama yang belum memiliki route locations;
- jika lokasi diisi, setiap lokasi nyata tetap harus memiliki nama, tipe, day,
  order, koordinat valid, dan validasi marker sesuai `TourLocationService`;
- destination description pada Route & Itinerary bersifat optional tetapi wajib
  disediakan sebagai tiga textarea locale: English, Traditional Chinese, dan
  Simplified Chinese. Nilai translation ikut disimpan ke location reference
  ketika reference lokasi dibuat/diperbarui sehingga autocomplete berikutnya
  dapat mengisi ulang deskripsi multi-bahasa.
- location reference autocomplete tetap memakai route
  `admin.tour-location.references`, sedangkan resolve koordinat tetap memakai
  `admin.tour-location.resolve-coordinates`. Route & Itinerary tidak menampilkan
  tombol manual "Resolve Location" atau "Enter Coordinates Manually"; koordinat
  harus tetap otomatis dibaca saat Google Maps URL diinput atau dipilih dari
  reference, dengan fallback field koordinat manual hanya muncul saat resolver
  gagal;
- marker image divalidasi tanpa upload file permanen. Upload marker dilakukan
  saat lokasi disinkronkan setelah Tour master tersedia, sehingga validasi
  tidak menulis file orphan. UI marker cover pada Route & Itinerary memakai dua
  kolom: preview di kiri dan file input di kanan;
- right sidebar berisi initial status dan pricing boundary tanpa menduplikasi
  progress, summary, atau field editable utama yang sudah tampil pada wizard
  dan Review & Create;
- Review & Create wajib menampilkan preview terstruktur dari input sebelumnya:
  master data, nama per bahasa, Route & Itinerary per day, completion content,
  dan media summary. Preview ini hanya UX helper; server validation dan
  persistence tetap authoritative.
- Route & Itinerary tidak boleh menambahkan wrapper card lagi di dalam panel
  utama. Repeater locations tampil flat/unframed; hanya individual stop editor
  yang boleh memakai card level kedua agar tidak terjadi card di dalam card
  berlebihan.
- Destination card pada Route & Itinerary dapat diurutkan ulang memakai drag
  handle di header card. Setelah drag/drop, frontend wajib menyusun ulang
  nomor tampilan, name input `locations[index]`, dan `visit_order` agar submit
  menyimpan urutan baru.
- translated fields memakai `backend-translation-group`,
  `backend-translation-grid`, dan urutan English, Traditional Chinese,
  Simplified Chinese;
- pada Create/Edit Tour, `package_highlights`, `include`, `exclude`, dan
  `additional_info` beserta field Traditional/Simplified bersifat optional.
  Field tersebut boleh kosong dan tidak boleh memblokir submit; required
  content tetap `short_description`, `description`, dan `cancellation_policy`
  dalam tiga bahasa.
- rich text memakai `data-backend-richtext="true"` dari initializer shared;
- cover preview memakai JS domain Tour dan upload tetap divalidasi server-side
  sebagai JPG/JPEG/PNG/WEBP maksimum 2 MB.

## Backend Edit Tour Standard

Edit Tour memakai wizard 5 step yang sama dengan Create Tour:

- Basic Information;
- Route & Itinerary;
- Content & Translations;
- Media;
- Review & Update.

Perbedaan Edit hanya pada existing data, status saat ini, metadata, current
cover, contextual actions, dan update behavior. Edit tetap memakai
`x-backend.detail-layout` 70%/30% dan JS domain Tour yang sama dengan Create;
tidak boleh dibuat wizard khusus Edit atau inline initializer baru.

Route & Itinerary pada Edit memakai `tour_package_locations` sebagai source
authoritative ketika structured stops tersedia. Existing stops dimuat melalui
relasi Tour dengan urutan `day_number`, `visit_order`, lalu `id`, dan disubmit
kembali melalui `TourLocationService`. Request tidak mempercayai location ID
client untuk mengikat data ke Tour lain; sync selalu berjalan melalui relasi
Tour yang sedang diedit.

Update Tour tidak merender input manual `itinerary`,
`itinerary_traditional`, atau `itinerary_simplified`. Kolom legacy tersebut
tetap dipertahankan di database untuk fallback historis. Saat Tour lama belum
memiliki structured locations, Edit hanya menampilkan notice non-editable dan
menyimpan field lain tanpa mengubah nilai itinerary legacy. Jika admin
menambahkan structured locations, frontend/detail yang memakai
`BuildsTourLocationItinerary` dapat memakai structured itinerary sesuai
precedence yang sudah aktif.

Edit Media menampilkan current cover dan input optional untuk mengganti cover.
Jika tidak ada file baru, cover lama dipertahankan. Marker image location lama
dipertahankan melalui `existing_marker_image`; marker baru diupload ketika
submitted, upload baru dibersihkan saat sync gagal, dan marker lama yang tidak
lagi dipakai dijadwalkan dihapus setelah commit berhasil.

Sidebar Edit berisi context admin, bukan duplikasi field utama:

- current status dan control status yang tervalidasi server;
- metadata record;
- setup progress;
- route summary dan legacy notice bila relevan;
- related actions ke detail, pricing, dan index.

## Backend Tour Detail Modal Standard

Backend detail Tour menampilkan structured route destinations langsung di card
`Tour Profile` sebagai ringkasan author-facing. Data wajib berasal dari relasi
`locations` yang diurutkan berdasarkan `day_number`, `visit_order`, lalu `id`;
Blade tidak boleh melakukan query manual atau menyusun ulang itinerary dari
string legacy. Ringkasan ini hanya menampilkan jumlah stop, nama destination,
day, jam kunjungan bila tersedia, tipe lokasi, dan status draft bila lokasi
tidak aktif. Full itinerary/customer-facing copy tetap dikelola dari Route &
Itinerary dan fallback legacy sesuai aturan di atas.

Backend Tour Detail modal Price wajib mengikuti shared backend modal standard:

- root modal memakai `backend-modal` dan section `backend-modal__header`,
  `backend-modal__body`, serta `backend-modal__footer`;
- close control hanya memakai `<x-backend.modal-close>` di header, bukan
  button footer atau `data-dismiss="modal"` pada modal canonical;
- footer modal form hanya memuat action submit utama dengan
  `backend-button backend-button-primary`;
- Add Price dan Edit Price memakai partial `price-fields` yang sama, dengan
  `backend-form-grid`, `backend-form-field`, `backend-form-label`,
  `backend-form-control`, `data-backend-money-unit`, dan
  `data-backend-picker="date"`;
- tabel Tour Prices pada detail backend hanya menampilkan `#`, `Pax Tier`,
  `Published Rate`, `Validity`, `Availability`, dan `Action`. Kolom
  `Published Rate` menampilkan Agent Rate/Published Rate dari quote canonical
  dalam urutan USD lalu IDR serta tombol teks `View calculation` yang membuka
  modal breakdown rate;
- modal breakdown rate menampilkan markup type dan formula markup di dalam card
  Markup yang sama; tidak boleh menambah card terpisah untuk metadata markup;
- pricing behavior tetap server-side melalui `StoreTourPriceAdminRequest`,
  `UpdateTourPriceAdminRequest`, dan canonical Tour pricing services. Blade dan
  JavaScript modal tidak boleh menambahkan formula pricing baru.

Backend Tour Gallery management sudah dihapus dari detail Tour. Halaman detail
admin tidak merender panel, tabel, modal, action, JavaScript manager, stylesheet,
atau route mutation untuk upload/update/delete gallery. Data dan schema
`tours_images` tetap dipertahankan untuk kompatibilitas read-only historical dan
frontend/public display; penghapusan fitur UI backend tidak boleh menghapus data
existing.

## Frontend Behavior

- Detail tour menampilkan itinerary/map dari data yang sudah dibentuk controller/service.
- Jika `activeLocations` tersedia, itinerary frontend Tour Package dihasilkan
  dari `BuildsTourLocationItinerary` dengan urutan `day_number` lalu
  `visit_order`. Description tiap stop mengikuti locale aktif:
  `description_traditional` untuk `zh`, `description_simplified` untuk
  `zh-CN`, dan fallback ke `description`.
- Jika Tour lama tidak memiliki `activeLocations`, frontend tetap memakai
  fallback manual `itinerary` sesuai locale yang tersedia.
- Blade tidak melakukan geocoding, sorting kompleks, atau query lokasi.
- Jika peta tidak memiliki koordinat valid, tampilkan fallback/empty state yang diterjemahkan.

## Public Pricing dan Create Order

Kontrak public Tour Package:

- `TourPackagePricingService` adalah source of truth quoteability untuk listing,
  detail, quote endpoint, dan Create Order. `TourPricingService` menangani
  mutation master price dan tidak menghitung selling price.
- Harga bookable wajib milik Tour yang diminta, memiliki
  `deleted_at = null`, canonical IDR contract rate, explicit markup type,
  mencakup travel date pada `valid_from`/`valid_until`, dan pax pada
  `min_qty`/`max_qty`. Quote Tour Package tidak lagi bergantung pada
  `markup_source`, `markup_verified_at`, atau `markup_verified_by`.
- Tepat satu tier harus ditemukan. Tier kosong atau lebih dari satu gagal
  tertutup. Quote memakai USD sell rate yang tersimpan di database saat ini
  tanpa freshness gate 24 jam, dan memakai tax yang tersimpan di tabel `taxes`
  saat ini tanpa dependency pada effective approved `tax_policies`.
- ID harga dari form hanya preferred identifier. Server wajib mencari ulang
  record melalui relasi tour dan seluruh filter di atas; ID dari tour lain,
  expired, deleted, incomplete, atau tier pax yang tidak cocok harus ditolak.
- Contract rate, markup, USD conversion, tax, harga per pax, dan total order
  dihitung server-side. Nilai harga/total dari frontend bukan sumber
  authoritative.
- Boundary rounding Tour Package mengikuti standar price project
  `ceiling-whole-usd-v1`: precise unit selling amount diproyeksikan ke USD lalu
  dibulatkan ke atas ke USD utuh; gross total memakai `CEIL(unit price) x
  quantity`; final total USD dibulatkan ke atas lagi setelah discount
  order-level. Field price IDR untuk unit/gross/final dikonversi dari USD yang
  sudah dibulatkan memakai stored USD sell rate yang sama, sedangkan raw
  pre-rounding IDR tetap disimpan di breakdown untuk audit.
- Halaman detail boleh memakai kandidat harga aktif untuk preview dinamis,
  tetapi harus menyaringnya terhadap tanggal perjalanan dan rentang pax yang
  sama. Tidak ada fallback ke tier tertinggi di luar `max_qty`.
- Daftar harga pada detail memakai row hasil `quoteEachTier`, bukan membaca
  atau memfilter metadata `status` legacy di Blade. Setiap row menampilkan pax,
  periode `valid_from`/`valid_until`, dan selling price USD per pax yang dihitung
  server. Modal mengulang quote berdasarkan tanggal perjalanan dan jumlah pax
  sebelum mengisi `tour_price_id`.
- `quoteEachTierReport` memeriksa kandidat tier, stored USD sell rate, dan
  stored tax database secara independen. Detail frontend
  menampilkan checklist tanggal evaluasi, pax tier/validity yang ditemukan,
  kurs, stored tax, dan final quote. Kegagalan dependency tidak lagi berubah
  menjadi empty state tanpa penjelasan, tetapi selling price dan tombol Order
  tetap fail-closed sampai seluruh requirement valid.
- Update USD pada halaman Currency tetap menyimpan nilai rate database yang
  digunakan Tour Package. Update Tax pada halaman yang sama tetap mengisi tabel
  compatibility `taxes`; nilai tersebut menjadi tax yang dipakai quote Tour
  Package.
- Create Order tetap memakai transaction, snapshot harga order, pending
  reservation, dan duplicate-submission guard yang sudah aktif.

### Kontrak Input Create Order — 2026-08-03

- Step 1 hanya mengumpulkan `travel_date`, `pickup_location`, dan
  `dropoff_location`. Tidak ada input manual Number of Guests dan tidak ada
  pemilihan agent pada modal public.
- Step 2 mengumpulkan manifest guest dengan field `name`, `phone` (opsional),
  `age` (`Adult`/`Child`), dan `sex` (`Male`/`Female`). ID type, nomor
  ID/passport, dan pilihan leader bukan bagian dari kontrak order Tour Package.
- Jumlah pax authoritative selalu dihitung server dari jumlah row `guests`
  tervalidasi (minimum 2, maksimum 200). Nilai `number_of_guests` yang dikirim
  client akan ditimpa sebelum validasi dan tidak boleh menentukan tier harga.
- Guest pertama dipetakan ke `orders.pickup_name`/`pickup_phone` hanya untuk
  compatibility consumer order lama. Pemetaan ini bukan status leader dan
  tidak dapat dipilih user.
- Quote preview menerima jumlah manifest saat ini dari JavaScript, sedangkan
  Create Order menghitung ulang jumlah tersebut dan melakukan quote authoritative
  di dalam transaction. Dengan demikian preview dan tier order berasal dari
  manifest yang sama tanpa mempercayai total frontend.
- Pada Step 1, preview harga Trip Details boleh meminta quote menggunakan
  minimum pax sebelum manifest guest diisi agar `travel_date` langsung
  tervalidasi terhadap validity harga. Tombol Next pada Step 1 wajib tetap
  disabled sampai quote canonical untuk tanggal tersebut berhasil.
- Header price card pada modal Create Order wajib mengikuti state quote Step 1:
  default menampilkan Price dan unit price USD per pax, sukses menampilkan unit
  price USD per pax hasil quote, sisi kanan menampilkan minimum order dari
  `min_qty` terkecil price tiers yang quoteable, dan gagal setelah user memilih
  tanggal menampilkan pesan "Price unavailable on date :date".
- Frontend booking wizard wajib menganggap quote sebagai state server-side yang
  terikat pada fingerprint `travel_date`, jumlah guest manifest, booking code,
  dan promotion. Perubahan tanggal atau guest membuat quote lama stale,
  mengosongkan `tour_price_id`, menonaktifkan submit, dan meminta ulang quote.
  User tidak boleh lanjut ke review/submit sampai quote canonical terbaru
  berhasil.
- Endpoint quote sukses memakai kontrak JSON root yang eksplisit:
  `price_available`, `quote`, dan `display`. `display.unit_price_usd` mengisi
  Price/pax dan `display.final_total_usd` mengisi Total Price pada Review.
  Frontend sementara tetap menerima wrapper `data` untuk kompatibilitas cache
  asset/respons lama dan meminta ulang quote ketika tab Review dibuka.
- Kolom database guest legacy untuk identitas tetap dipertahankan agar order
  historis dan flow layanan lain tidak rusak; order Tour Package baru tidak
  mengisi kolom tersebut.
- Guest Details pada customer detail order Tour Package hanya menampilkan
  nomor urut, nama, kontak, kategori umur, dan gender. Kolom ID serta indikator
  leader tidak dirender dan tidak dihitung kembali dari pickup contact. Aturan
  ini berlaku pada template modern dan fallback Tour legacy tanpa mengubah
  tampilan guest untuk layanan lain.

### Kontrak Repricing Manifest Admin — 2026-08-03

- Add, update, dan delete guest untuk order Tour Package berstatus `Draft` atau
  `Pending` selalu menghitung ulang quote authoritative berdasarkan
  `travel_date` dan jumlah row guest yang tersimpan. `orders.number_of_guests`
  bukan input perhitungan dan selalu disinkronkan dari manifest.
- Pax tier dipilih ulang tanpa mempercayai `orders.price_id` lama. Karena itu,
  penambahan atau pengurangan guest dapat memindahkan order ke tier harga lain;
  update detail guest tetap memvalidasi ulang pricing dengan aturan yang sama.
- Mutasi guest, sinkronisasi field harga compatibility pada `orders`, dan
  pembuatan immutable `order_pricing_snapshots` baru berjalan dalam satu
  database transaction. Snapshot historis tidak ditimpa.
- Promotion dan booking code milik order dievaluasi kembali untuk order yang
  sama tanpa dianggap sebagai pemakaian ganda. Order lain tetap tunduk pada
  limit dan ownership discount yang berlaku.
- Jika jumlah guest keluar dari batas 2–200, tidak ada tier/validity yang cocok,
  stored USD sell rate atau stored tax database tidak valid, atau invoice sudah dibuat, mutasi
  guest dibatalkan seluruhnya dan harga lama tetap utuh.
- Manifest dikunci setelah order keluar dari `Draft`/`Pending` atau invoice
  dibuat agar nilai invoice, order, reservation, dan pricing snapshot tidak
  berbeda.

### Invoice PDF Multi-language Contract — 2026-08-03

- Confirm/Generate/Regenerate Invoice menghasilkan tiga invoice dari snapshot
  finansial yang sama: English (`en`), Chinese Simplified (`zh-CN`), dan Chinese
  Traditional (`zh`). Kode `zh` lama tetap bermakna Traditional.
- Validation Actions hanya menampilkan button invoice ketika file locale itu
  tersedia dan setiap button membuka protected preview untuk locale exact.
- Template Chinese memakai dictionary locale explicit dan font lokal Dompdf
  `notosans`; tidak ada ketergantungan font browser, font OS, CDN, atau URL
  remote. Ini mencegah glyph Chinese berubah menjadi kotak pada PDF.
- Confirmation email Tour melampirkan ketiga file. Perubahan bahasa tidak
  menghitung ulang harga dan tidak mengubah order, reservation, atau invoice.
- Setiap bahasa selalu menampilkan `Total Price (USD)` dari pricing snapshot
  historis. `Amount Due` tetap memakai currency invoice yang dipilih
  (USD/CNY/TWD), sehingga agent dapat membandingkan nilai dasar USD dengan
  nominal yang benar-benar harus dibayar tanpa memakai kurs terbaru.
- Standard lintas-PDF dijelaskan di
  `docs/decisions/pdf-localization-standard.md`.

### Order Confirmation Email Contract — 2026-08-03

- Confirmation dan Resend memakai payload canonical dari
  `OrderConfirmationEmailDataService` dan template transactional yang sama.
- Subject memuat order reference dan brand. Body memuat status, order dan
  reservation reference, ringkasan Tour, travel dates, pax, pickup/drop-off,
  invoice, Total Price USD dari pricing snapshot, Amount Due dalam selected
  currency, due date, attachment notice, serta protected order-detail CTA.
- Email tidak menampilkan guest identity, raw JSON, internal notes, atau data
  workflow admin. Semua dynamic output di-escape.
- CTA dan fallback URL dibentuk dari named route `view.detail-order-tour`, bukan
  URL generic atau domain hard-coded, sehingga mengarah ke
  `/detail-order-tour/{id}` dan tetap memakai ownership guard customer.
- Layout menggunakan presentation tables maksimum 640px dengan responsive
  fallback dan tidak memakai grid/flex/JavaScript agar kompatibel dengan Gmail,
  Outlook, Apple Mail, dan mobile email clients.
- Standard lengkap ada di
  `docs/decisions/order-confirmation-email-standard.md`.

## Tour Package Price CRUD

Purpose CRUD admin adalah mengelola master input quote baru tanpa mengubah
snapshot order historis. Field canonical adalah:

```text
tour_id
min_qty / max_qty
contract_rate_idr
markup_type = percentage / usd / idr
markup_amount
valid_from / valid_until
```

Operator tidak memilih status. Create/update yang lolos server-side
completeness, ownership, pax/date, markup-type, dan overlap validation otomatis
mengisi metadata compatibility `status = Active`,
`pricing_data_status = ready`, `markup_verified_at`, dan
`markup_verified_by`. Metadata ini dipertahankan untuk isolasi data legacy dan
tidak ditampilkan sebagai workflow CRUD serta tidak menentukan availability.
Availability ditentukan dari kelengkapan konfigurasi, pax, dan validity dates.

Jenis markup mempunyai arti tunggal:

- `percentage`: persentase dari `contract_rate_idr` per pax; maksimum 100%,
  maksimum dua desimal, dan dihitung fixed-scale dengan pembulatan half-up.
- `usd`: nominal USD per pax, maksimum dua desimal; dikonversi memakai USD
  sell rate yang tersimpan di database saat quote.
- `idr`: nominal rupiah bulat per pax; tidak menjalani currency round-trip.

Pembuatan/update berjalan dalam transaction. Overlap price dicegah bila pax
interval dan validity interval sama-sama beririsan untuk Tour yang sama.
Soft-deleted row tidak memblokir; resolver runtime tetap menolak ambiguity
sebagai perlindungan lapis kedua.

Admin list menampilkan pax tier, Agent Rate/Published Rate, validity,
derived availability, action, serta Needs Review filter. Soft-deleted
row tidak ditampilkan pada halaman detail Tour; restore
service dan route tetap dipertahankan untuk kebutuhan recovery terkontrol. Legacy
`contract_rate`, `markup`, `expired_date`, dan `status` hanya mirror
compatibility untuk schema lama; nilainya tidak pernah digunakan sebagai
fallback quote dan 72 row legacy tidak dipetakan otomatis.

Validity bersifat inklusif: price dapat dipakai hanya bila
`valid_from <= travel_date <= valid_until`. Record bertipe `Scheduled` belum
berlaku, `Expired` sudah tidak berlaku, dan `Valid` berada di dalam periode.
Quoteability tetap memerlukan tepat satu tier pax, stored USD sell rate yang
valid, dan stored tax database yang valid.

Implemented: 2026-07-31. Impact hanya berlaku untuk quote dan order baru.
Existing order, invoice, payment, reservation, email, PDF, report, dan pricing
snapshot tidak direprice atau dimutasi oleh perubahan master Tour Price.

### Canonical Date Input Fix — 2026-07-31

Create dan update Tour Price memakai `data-backend-picker="date"` dengan value
canonical `Y-m-d`. Field ini tidak lagi memakai `.date-picker` legacy karena
initializer panel lama memformat value menjadi `dd MM yyyy`.

`StoreTourPriceAdminRequest` menormalisasi dua format legacy yang memang pernah
dihasilkan backend (`d F Y` dan `d M Y`) sebelum menjalankan validation.
Format numeric ambigu tidak diparse atau ditebak. Dengan demikian request dari
asset/browser lama tetap dapat dipulihkan secara aman, sedangkan seluruh write
baru tetap masuk ke service dan database sebagai typed canonical date.

### Update Self-Overlap dan Markup Display Fix — 2026-07-31

`UpdateTourPriceAdminRequest` wajib mengimpor model `App\Models\TourPrices`
agar route-bound current price dapat dikecualikan dari overlap query. Tanpa
import tersebut, current ID menjadi `null` dan sebuah tier bertabrakan dengan
dirinya sendiri walaupun hanya contract rate yang berubah. Regression test
menjalankan request validation dengan route-bound Tour dan Tour Price untuk
menjamin current ID selalu diteruskan.

Input markup memakai maksimum dua desimal untuk Percentage/USD dan rupiah bulat
untuk IDR. `markup_amount` disimpan sebagai numeric-string tervalidasi dalam
representasi minimal tanpa fixed padding (`20.00 -> 20`, `20.50 -> 20.5`).
Migration menormalkan data lama (`20.000000` menjadi `20`) secara idempotent. Form edit tetap
memformat Percentage/USD menjadi dua desimal dan IDR sebagai integer.

### Verifikasi 2026-07-29

- Audit menemukan detail lama hanya memfilter `status = Active`, sementara
  Create Order juga memfilter expiry terhadap waktu submit. Akibatnya harga
  kedaluwarsa ditampilkan tetapi ditolak saat submit.
- Audit read-only data aktif menemukan harga Tour Package ID 58 sampai 69
  berstatus `Active` dengan `expired_date = 2026-06-30`. Pada 2026-07-29
  record tersebut tidak valid dan tetap harus ditolak; tidak ada data yang
  diubah oleh pekerjaan ini.
- Detail dan Create Order telah diselaraskan melalui `TourPricingService`;
  Create Order memeriksa expiry terhadap `travel_date`, bukan waktu submit.
- Focused feature test dijalankan dengan SQLite `:memory:` dan mencakup
  authoritative price snapshot, reservation Pending, price tour lain,
  expired-for-travel-date, inactive, logically deleted, pax tier, dan
  duplicate submission.

## Files

- `app/Http/Controllers/Backend/Operations/Tours/TourAdminController.php`
- `app/Services/Tours/TourPricingService.php`
- `app/Services/Tours/TourLocationService.php`
- `app/Http/Controllers/Concerns/BuildsTourLocationItinerary.php`
- `resources/views/backend/operations/tours/forms`
- `resources/views/frontend/landing-page/tours/detail.blade.php`
- `resources/lang/{en,zh,zh-CN}/tour-map.php`

### Frontend Multi-language Contract — 2026-08-03

Seluruh surface frontend Tour Package aktif wajib mendukung locale `en`, `zh`,
dan `zh-CN`: directory `/tour-package-services`, detail dan Create Order
`/tour/{slug}`, customer order detail, serta customer edit order. Route lama
`/tour-packages` diarahkan ke directory canonical agar tidak merender template
legacy yang tidak mengikuti kontrak ini.

Compatibility URL `/tour-package-service` dan canonical URL `/tour-packages`
wajib menunjuk langsung ke `FrontEndController::tour_package_services` melalui
Laravel route dispatcher. Controller tidak boleh memanggil action controller
lain secara langsung karena pemanggilan tersebut melewati dependency injection
untuk `TourPackagePricingService` dan `MoneyFormatter`.
Named route legacy `view.tours` tetap tersedia melalui redirect `/tours` menuju
`/tour-packages`; route ini tidak boleh mengaktifkan kembali controller atau
template directory lama.

Aturannya adalah:

- Copy antarmuka memakai key `tour-packages`, `tour-detail`, `tour-map`, atau
  `messages`; controller, Form Request, dan response quote tidak mengirim pesan
  English literal kepada pengguna.
- JavaScript menerima semua label yang dapat terlihat melalui atribut `data-*`
  dari Blade. String internal seperti nama class, nilai enum, dan pesan exception
  teknis bukan copy antarmuka.
- Konten master Tour membaca kolom sesuai locale (`*_traditional` untuk `zh`,
  `*_simplified` untuk `zh-CN`) dan selalu fallback ke kolom English bila
  terjemahan kosong. Data transaksi dan input pengguna tidak diterjemahkan.
- Nilai domain tersimpan seperti `Adult`, `Child`, `Male`, dan `Female` tetap
  stabil untuk compatibility database, tetapi label yang dirender dilokalkan.
- Durasi tidak lagi dirakit dengan literal `D/N`; semua surface memakai key
  `tour-detail.duration_days` atau `tour-detail.duration_days_nights`.
- Validation create order dan quote API menggunakan pesan domain terjemahan,
  sehingga locale Chinese tidak bergantung pada fallback English dari
  `validation.php`.

Regression coverage ada di `PublicTourPackageFlowTest`, termasuk rendering UI
dan kontrak label JavaScript untuk kedua locale Chinese.

Package Overview pada customer detail order memakai prioritas konten berikut:

- Locale `zh`: `tours.itinerary_traditional` dan
  `tours.additional_info_traditional`.
- Locale `zh-CN`: `tours.itinerary_simplified` dan
  `tours.additional_info_simplified`.
- Bila kolom locale kosong, halaman fallback ke snapshot order lalu konten
  English Tour. Locale `en` tetap mendahulukan snapshot order agar histori
  order tidak berubah akibat edit master Tour.
- Itinerary terjemahan yang tersedia tidak boleh digantikan oleh itinerary
  generator lokasi yang saat ini hanya memiliki konten master non-locale.

Price Details pada customer detail order menampilkan `Price/pax` dari
`OrderPricingSnapshotReader.unit_price_usd`, diikuti gross price untuk jumlah
pax order, adjustment yang tersimpan, dan final total. View tidak membagi ulang
total atau membaca master Tour Price terbaru, sehingga penjelasan total selalu
sesuai snapshot authoritative pada saat order dibuat.

### Backend Order Detail Contract — 2026-08-03

Route `/orders-admin-{id}` memakai cabang presentasi dan workflow khusus ketika
`orders.service = Tour Package`, tanpa mengubah surface layanan lain:

- Snapshot utama menampilkan nama/type Tour, travel date, duration, pickup,
  drop-off, jumlah guest, reservation, agent, operator, dan payment state.
- Order Details mendahulukan snapshot transaksi untuk itinerary, include,
  exclude, additional information, cancellation policy, dan agent remark;
  master Tour hanya dipakai sebagai fallback bila snapshot lama kosong.
- Billing membaca `OrderPricingSnapshotReader`: price/pax, gross total sesuai
  jumlah pax, adjustment tersimpan, final USD, serta committed invoice. Harga
  tidak dihitung ulang dari master price aktif.
- Approval Tour hanya valid dari `Draft`/`Pending`. Bank harus ada, currency
  invoice dapat dipilih antara USD, CNY, dan TWD, reservation menjadi `Active`,
  dan order, reservation, invoice, serta audit log ditulis atomik. Nilai CNY
  dan TWD dihitung dari authoritative total IDR menggunakan sell rate positif
  yang tersedia saat approval, dibulatkan ke atas ke satu unit mata uang, lalu
  total serta rate/sell USD, CNY, dan TWD dibekukan pada invoice. Karena itu
  perubahan master kurs berikutnya tidak mengubah tagihan historis. PDF invoice
  memakai protected public-order storage.
- Confirm Order mengunci row order dan memeriksa ulang service serta status di
  dalam transaction sebelum reservation, invoice, dan audit log ditulis.
  Reservation harus tersedia dan masih `Pending`; reservation terminal tidak
  boleh diaktifkan kembali oleh Confirm Order.
  Status pengiriman confirmation Tour tidak memakai kolom compatibility
  `reservations.send` karena kolom tersebut bukan bagian schema canonical.
  Pengiriman yang berhasil dicatat secara auditable pada `order_logs` dengan
  action `Send Confirmation` atau `Resend Confirmation`; Blade memakai log ini
  untuk menentukan action berikutnya tanpa mencampurnya dengan lifecycle
  reservation.
  Request kedua/paralel setelah order diproses menerima HTTP 409 dan tidak boleh
  membuat invoice, log, atau pengiriman confirmation tambahan. Semua form action
  backend memakai shared spinner dan submit lock; button di luar form yang
  menunjuk lewat atribut `form` ikut dikunci.
- Generate invoice hanya tersedia pada `Approved`; send/resend confirmation
  hanya pada `Approved`/`Paid` dan wajib mempunyai reservation serta invoice.
- Notes Tour hanya dapat ditambah pada `Draft`/`Pending`/`Approved`, divalidasi,
  disimpan sebagai plain text, dan selalu di-escape saat dirender.
- `orders.msg` khusus untuk alasan status terminal yang terlihat agent
  (`Canceled`/`Rejected`/`Invalid`), bukan tempat menyimpan manifest guest,
  pricing, terms, atau payload JSON internal. Order baru menyimpan remark atau
  special request di `orders.note`; Agent Communication menampilkannya sebagai
  context terstruktur. Payload JSON legacy dideteksi dan tidak pernah dirender
  mentah; hanya `guest_notes`/`special_request` yang tidak kosong yang dapat
  dipresentasikan sebagai context.
- Reject/Invalid hanya dari `Draft`/`Pending`; archive UI hanya tersedia setelah
  `Rejected`/`Invalid`/`Canceled` dan menulis status canonical `Deleted`, bukan
  status legacy `Archive`. Reservation ditutup sebagai `Canceled` dalam
  transaction yang sama.
- Tour tidak boleh difinalisasi langsung ke `Paid`; perubahan ke `Paid` tetap
  hanya berasal dari payment confirmation yang valid.
- Guest admin Tour memakai kontrak canonical `Adult`/`Child` dan
  `Male`/`Female`; reservation ID harus sama dengan reservation milik order.

### Customer Detail Actions and Payment Confirmation — 2026-08-03

Route `/detail-order-tour/{id}` mengikuti lifecycle canonical berikut:

- `Draft`/`Invalid`: Edit Order menjadi primary action. Delete hanya tersedia
  pada `Draft`/`Invalid`/`Rejected`, diperiksa ulang di server, lalu mengubah
  order ke `Deleted` dan reservation ke `Canceled` secara atomik serta menulis audit log;
  record Tour tidak dihapus secara fisik.
- `Pending`: detail bersifat read-only sambil menunggu validasi.
- `Approved`: Payment Confirmation menjadi primary action bila invoice masih
  memiliki balance, deadline belum lewat, dan tidak ada submission `Pending`.
- `Paid`: detail dan invoice tetap menjadi final booking reference; edit,
  payment submission, dan delete tidak tersedia.

Semua tombol action memakai primitive `.ui-btn` dari frontend standard. Invoice
dan Orders adalah secondary action, payment/edit adalah satu primary action,
dan delete memakai danger action. Form payment dan delete langsung menampilkan
spinner dan mengunci submit untuk mencegah eksekusi berulang.

Payment confirmation Tour memakai shared public standard pada
`decisions/payment-confirmation-standard.md`: payment date, reported amount
dalam committed invoice currency, serta proof JPG/PNG/PDF maksimal 5 MB. Nilai
invoice, balance, currency, ownership, status, dan duplicate `Pending` selalu
divalidasi ulang di server. Submission hanya menjadi `Pending`; hanya validasi
finance yang dapat mengurangi balance dan membawa order ke `Paid`.
