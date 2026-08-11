# Activity Module

Status: active
Updated: 2026-08-11

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
- konversi memakai tepat satu `usd_rates` USD `sell` rate melalui
  `CurrencyRateResolver`, termasuk validasi freshness 24 jam;
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

Backend menampilkan published price per pax dari quote yang sama tanpa membuat
preview travel date. Nilai unit price tidak dikurangi promo order-level. Jika
Activity, validity, kurs, pajak, atau price belum valid, backend menampilkan
`Unavailable` beserta kode operasional dan tidak membuat harga alternatif. URL
detail/search Activity legacy hanya mengarahkan ke halaman canonical agar tidak
ada formula lama yang dapat menghasilkan harga berbeda.
Route booking-code legacy menggunakan pola tidak ambigu
`/activity-{code}/booking-code/{bcode}`; pola lama dengan dua parameter yang
dipisahkan tanda hubung tidak digunakan karena kode Activity sendiri dapat
mengandung tanda hubung.

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

Field financial order Activity masih menjadi committed legacy transaction
snapshot. Aktivasi immutable `order_pricing_snapshots` untuk Activity memerlukan
migration dan keputusan lifecycle tersendiri; implementasi Tour Package tidak
boleh dibaca sebagai snapshot Activity secara otomatis.
