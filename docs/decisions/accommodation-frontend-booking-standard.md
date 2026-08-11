# Accommodation Frontend Booking Standard

Status: active
Updated: 2026-08-10

## Scope

Standard ini berlaku untuk wizard order frontend `Hotel`, `Hotel Promo`, dan
`Hotel Package`. Private Villa tidak termasuk.

## Wizard Contract

1. **Stay and rooms** mengumpulkan data occupancy per kamar: jumlah adult,
   jumlah child, usia setiap child, extra bed, dan kebutuhan khusus kamar.
   Room type dan tanggal berasal dari offer yang dipilih dan tidak boleh
   dipercaya ulang dari hidden financial fields.
2. **Guests and transfers** mengumpulkan satu record untuk setiap tamu sesuai
   occupancy: full name, category `Adult|Child`, phone optional, dan sex
   `Male|Female`. Airport transfer tetap optional pada step ini.
3. **Review and submit** menampilkan stay, rooming list, guest manifest,
   transfer, dan breakdown quote sebelum submit. Nilai UI adalah preview;
   backend selalu menghitung ulang nilai authoritative.

Jumlah guest manifest harus sama dengan total occupancy. Jumlah Adult dan
Child per room harus sama dengan pilihan pada step pertama. Setiap child wajib
memiliki usia 0–17. Occupancy tidak boleh melebihi kapasitas adult, child, atau
total room pada record `hotel_rooms`.

Untuk kompatibilitas tampilan dan proses lama, backend membentuk snapshot
`orders.number_of_guests_room` dan `orders.guest_detail` dari payload
terstruktur. Setiap tamu juga disimpan pada relasi `orders.guests` dan, bila
reservation sudah terbentuk, dihubungkan melalui `guests.rsv_id`.

## Authoritative Pricing and Lifecycle

- Semua varian wajib menggunakan `HotelPricingService`; hidden total dari
  browser tidak authoritative.
- Normal rate membutuhkan tepat satu rate per room/night. Promo hanya berlaku
  untuk malam yang memenuhi rule dan fallback ke normal rate untuk malam lain.
  Package wajib cocok dengan room, stay window, status, dan duration.
- Currency conversion, markup, tax, kickback, promotions, booking-code
  discount, optional rate, extra bed, dan airport shuttle mengikuti
  `accommodation-pricing-contract.md`.
- Availability diverifikasi ulang dalam transaksi sebelum order disimpan.
- Order menyimpan financial snapshot. Invoice dan payment memakai snapshot
  order, bukan rate master terbaru.
- Lifecycle tetap `Draft -> Pending -> Approved -> Paid`; fulfillment selesai
  memakai `completed_at`/`completed_by`, bukan status `Completed` pada order.

## UI and Submission

- Gunakan komponen `.ui-btn`, form control, required marker, picker, alert, dan
  submit guard bersama dari frontend design system.
- Copy wajib tersedia untuk `en`, `zh`, dan `zh-CN`.
- Wizard normal, promo, dan package memakai partial dan JavaScript yang sama
  untuk occupancy, manifest, review, transfer, serta spinner submit.
- Submit memakai token idempotency dan pola Post/Redirect/Get.
