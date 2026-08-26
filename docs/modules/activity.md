# Activity Module

Status: active
Updated: 2026-08-12

## Scope

Activity mencakup discovery service publik, detail dan harga, booking, reservation review, confirmation, invoice/payment yang digunakan flow aktif, upcoming service, completion, dan history.

## Boundary

- Activity memakai sebagian object lifecycle bersama, tetapi implementasi aktual harus diaudit sebelum menyamakan status atau behavior dengan service lain.
- Jangan memasukkan Wedding, DOKU, Private Villa, Transport Management, atau SPK.
- Harga, availability, ownership, service type, dan status transition wajib diverifikasi server-side.

## Referensi

- Shared lifecycle: `docs/status-contract.md`
- Security: `docs/security-rules.md`
- Database: `docs/database.md`
- Testing: `docs/testing.md`
- Audit lintas service:
  `docs/decisions/shared-order-status-audit.md` (`historical`)
- Compatibility plan:
  `docs/decisions/shared-order-status-compatibility-plan.md` (`superseded`)
- Roadmap end-to-end: `docs/decisions/service-booking-flow-audit-roadmap.md`

## Commercial Status dan Fulfillment

- Commercial order mengikuti
  `Draft -> Pending -> Approved -> Paid`, dengan terminal
  `Canceled`, `Rejected`, `Invalid`, atau `Deleted`.
- Activity yang selesai tetap memiliki `orders.status = Paid`.
- Completion mengisi `orders.completed_at` dan actor manual pada
  `orders.completed_by`; `Completed` tidak ditulis ke `orders.status`.
- Reservation mengikuti `Pending -> Active -> Completed`, dengan cabang
  `Canceled`.
- Current adalah order non-terminal dengan `completed_at = null`.
- Completed History memerlukan `completed_at != null`; Closed History memakai
  terminal commercial state. Tanggal activity yang lewat saja bukan bukti
  fulfillment selesai.
- Completion harus atomic dan idempotent.

## Flow Verifikasi

```text
Listing/detail -> price -> availability -> booking -> reservation review
-> confirmation -> invoice -> payment -> upcoming -> completion -> history
```

Setiap perubahan wajib memiliki HTTP Feature Test dan database assertion yang relevan pada database testing terpisah.

## Kontrak Harga Activity

`ActivityPricingService` adalah satu-satunya sumber perhitungan harga Activity
untuk preview backend, preview frontend, dan create order. Controller, Blade,
JavaScript, view model, dan jalur URL legacy tidak boleh mengulang formula atau
menerima `price_total`, `price_pax`, pajak, rate, markup, maupun diskon dari
request sebagai nilai authoritative.

Kontrak data Activity yang berlaku saat ini:

- `activities.contract_rate` adalah nilai IDR utuh per pax;
- `activities.markup` adalah nilai USD utuh per pax;
- harga publik dan order disajikan dalam USD dengan dua desimal;
- konversi Activity memakai tepat satu `usd_rates` USD `sell` rate canonical
  yang tersimpan di database melalui `CurrencyRateResolver`;
- perhitungan memakai integer fixed-scale tanpa binary-float; contract rate,
  markup, dan pajak dihitung pada precision internal terlebih dahulu, lalu
  selling price canonical dibulatkan ke atas ke USD utuh berikutnya;
- tepat satu konfigurasi pajak legacy bernama `tax` harus tersedia;
- pajak dihitung atas contract rate yang telah dikonversi ditambah markup;
- promosi legacy Activity adalah fixed USD order discount yang aktif pada waktu
  kalkulasi, bukan berdasarkan tanggal perjalanan, lalu diterapkan setelah gross
  total;
- harga akhir tidak boleh negatif.

Boundary rounding canonical Activity:

```text
contract_rate_idr / usd_sell_rate
+ markup_usd
+ tax
= precise unit selling amount
-> CEIL to whole USD unit price
-> unit price x quantity
-> subtract active fixed USD promotion
-> CEIL final total to whole USD
```

Dengan demikian quantity memakai aturan `CEIL(unit price) x quantity`.
Final total juga tetap whole USD setelah discount order-level diterapkan.

Tanggal perjalanan dikumpulkan untuk kebutuhan order/reservation dan menjadi
input validity harga Activity. Endpoint `activity.quote` menerima tanggal
perjalanan, menghitung ulang total ketika pax atau tanggal berubah, dan menolak
tanggal perjalanan yang melewati `activities.validity`. Create order menjalankan
kalkulasi yang sama sekali lagi dan menyimpan hasilnya ke field financial order.
Hidden field atau nilai total yang dikirim browser diabaikan. Rate, pajak,
harga, validity, minimum pax, capacity, dan promosi yang tidak valid membuat
pricing gagal tertutup dan order tidak dibuat.

Backend menampilkan calculated selling price per pax dari quote yang sama tanpa
membuat preview travel date. Nilai unit price tidak dikurangi promo order-level.
Backend calculation tidak memakai status Activity sebagai dependency harga.
Jika dependency pricing wajib belum tersedia, backend menampilkan pesan
diagnostic spesifik dan tidak membuat harga alternatif. URL detail/search
Activity legacy hanya mengarahkan ke halaman canonical agar tidak ada formula
lama yang dapat menghasilkan harga berbeda.

Backend Activity price display wajib menampilkan pasangan mata uang USD dan IDR
untuk setiap calculated price yang ditampilkan kepada admin. USD tetap menjadi
nilai canonical pricing/order, sedangkan IDR adalah secondary display yang
diturunkan dari `ActivityPricingQuote` memakai stored USD sell rate yang sama.
Urutan tampilan wajib USD terlebih dahulu lalu IDR. Blade tidak boleh
mengonversi harga dengan formula sendiri. Khusus `Contract Rate`, nilai IDR
yang ditampilkan adalah `activities.contract_rate` dari database karena itu
adalah master vendor/partner rate; nilai USD hanya reference conversion dari
quote.
Route booking-code legacy menggunakan pola tidak ambigu
`/activity-{code}/booking-code/{bcode}`; pola lama dengan dua parameter yang
dipisahkan tanda hubung tidak digunakan karena kode Activity sendiri dapat
mengandung tanda hubung.

## Kontrak Waktu Activity

`ActivityTimingService` adalah sumber canonical untuk waktu order Activity.
Activity Start memakai `travel_date` yang dipilih customer. Activity End
dihitung dari Activity Start ditambah `activities.duration` dengan unit durasi
yang eksplisit: `Minute(s)` menambah menit, `Hour(s)` menambah jam, dan
`Day(s)` menambah hari. Durasi tanpa unit tetap diperlakukan sebagai jam untuk
kompatibilitas data lama. Tanggal baru hanya berubah bila penambahan durasi
benar-benar melewati tengah malam.

Selama Activity belum memiliki input pickup/drop-off time independen,
`orders.pickup_date` dan `reservations.pickup_date` harus sama dengan Activity
Start, sedangkan `orders.dropoff_date` dan `reservations.dropoff_date` harus
sama dengan Activity End. Tampilan frontend customer-facing harus memakai
formatter datetime canonical agar waktu mulai, waktu selesai, pickup, dan
drop-off tidak kehilangan informasi jam/menit.

## Public Booking Wizard dan Guest Manifest

Frontend Activity Detail memakai booking wizard 3 step:

1. Booking Details: customer memilih Activity Date, Number of Guests,
   Pick-up Location, dan Drop-off Location.
2. Guest Details: manifest tamu manual atau upload guest list.
3. Review and Submit: ringkasan tanggal, pax, guest manifest, dan canonical
   quote.

Step Booking Details menampilkan `activities.validity` sebagai batas available
until dan `activities.qty` sebagai maximum capacity. Input pax memakai batas
minimum dari `activities.min_pax` dan maximum dari `activities.qty`, tetapi
validasi server tetap authoritative. Quote frontend hanya boleh diambil dari
endpoint canonical `activity.quote`; create order selalu menghitung ulang melalui
`ActivityPricingService`. Pick-up dan Drop-off Location memakai text input
canonical, wajib diisi, divalidasi server-side sebagai string maksimal 255
karakter, lalu disimpan pada snapshot `orders.pickup_location` dan
`orders.dropoff_location` untuk kebutuhan fulfillment. Lokasi tidak menjadi input
pricing Activity.

Guest manifest memakai threshold berikut:

- `1-10 pax`: manual guest details. UI minimal menampilkan Guest 1 dan tombol
  Add More Guest. Customer wajib mengisi minimal 1 guest detail dan tidak boleh
  mengirim lebih banyak guest row daripada `number_of_guests`. Field manual
  memakai struktur yang sama dengan guest list upload: `Guest Name`,
  `Age Category`, `Sex`, dan `Phone Number`.
- `>10 pax`: customer wajib upload guest list `.xlsx` atau `.csv`. UI tidak
  membuat ratusan block form. Template resmi berisi kolom `Guest Name`,
  `Age Category`, `Sex`, dan `Phone Number`.

Raw guest list upload tidak disimpan sebagai file permanen setelah parsing
sukses. Backend membaca file sementara Laravel, memvalidasi header dan row,
memastikan jumlah row persis sama dengan `number_of_guests`, lalu menyimpan row
normalisasi ke table `guests` yang terhubung ke order/reservation. `Guest Name`,
`Age Category`, dan `Sex` wajib; `Age Category` hanya menerima `Adult`/`Child`,
`Sex` hanya menerima `Male`/`Female`, dan `Phone Number` opsional serta disimpan
sebagai string. Tidak ada field Guest Leader pada Activity public booking;
pickup compatibility memakai guest pertama yang memiliki phone, atau guest
pertama jika tidak ada phone.

## Validity, Price Eligibility, dan Status Publikasi

`activities.validity` adalah tanggal terakhir Activity boleh dipublikasikan dan
tanggal terakhir harga Activity boleh dipakai untuk tanggal layanan yang dipilih
customer. Tanggal tersebut masih valid bila `selected activity date <= validity`.
Tanggal layanan setelah `validity` harus ditolak oleh quote endpoint dan create
order, meskipun order dibuat sebelum tanggal validity berakhir. Setelah
`validity` terlewati terhadap tanggal aplikasi:

- `ActivityValidityService` mengubah record `Active` menjadi `Draft` secara
  idempotent;
- command `activities:draft-expired` dijalankan scheduler setiap hari pukul
  `00:00` pada timezone aplikasi;
- backend inventory/detail menjalankan sinkronisasi yang sama sebelum menampilkan
  status agar admin segera melihat nilai `Draft`;
- scope model `published()` hanya mengizinkan status `Active`, validity yang
  terisi, dan validity yang belum lewat;
- listing, detail, quote, dan create order frontend memakai scope tersebut agar
  Activity expired terhadap tanggal aplikasi tetap tertutup jika scheduler
  deployment sedang terlambat;
- form backend menolak publikasi `Active` jika validity sudah lewat.

## Backend Activity Price Calculation Dependencies

Activity backend price calculation hanya bergantung pada lima dependency:

```text
Contract Rate
+ Markup
+ Tax
+ USD Rate
+ Valid Until
-> Calculated Price
```

Status Activity tidak boleh menentukan apakah backend price dapat dihitung.
`Draft` dan `Active` dengan dependency pricing yang sama harus menghasilkan
calculated selling price yang sama. Status tetap dipakai untuk publikasi,
visibility, booking eligibility, dan lifecycle, tetapi bukan untuk formula atau
availability backend reference price.

Dependency failure memakai reason code internal berikut:

- `MISSING_CONTRACT_RATE`
- `MISSING_MARKUP`
- `MISSING_TAX`
- `MISSING_USD_RATE`
- `MISSING_VALID_UNTIL`

`activities.validity` yang sudah lewat tetap memenuhi dependency `Valid Until`
untuk backend reference calculation selama nilainya tersedia. Kondisi expired
adalah masalah lifecycle, bukan kegagalan formula. Jika `today > validity`,
`ActivityValidityService` mengubah Activity non-Draft yang masih aktif menjadi
`Draft` secara idempotent. Jika `today = validity`, Activity belum expired.
Jika admin memperbarui validity ke tanggal masa depan, sistem tidak otomatis
mengubah status kembali ke `Active`; admin harus mengaktifkan secara eksplisit
sesuai lifecycle form backend.

Field financial order Activity masih menjadi committed legacy transaction
snapshot. Aktivasi immutable `order_pricing_snapshots` untuk Activity memerlukan
migration dan keputusan lifecycle tersendiri; implementasi Tour Package tidak
boleh dibaca sebagai snapshot Activity secara otomatis.
