# Transport Order Number Standard

Dokumen ini menetapkan format order number untuk order transport frontend/backend.

## Format

```text
{USER_CODE}{YY}{MM}{DD}{DAILY_SEQUENCE}
```

Contoh:

```text
ABC260715A
```

Penjelasan:

- `ABC`: kode user atau agent yang menjadi sales agent order.
- `26`: tahun order dibuat, format dua digit.
- `07`: bulan order dibuat.
- `15`: tanggal order dibuat.
- `A`: urutan order transport pada hari yang sama untuk sales agent tersebut.

## Sequence Harian

Sequence memakai format huruf spreadsheet-style:

- Order pertama: `A`
- Order kedua: `B`
- Order ketiga: `C`
- Setelah `Z`: `AA`
- Setelah `ZZ`: `AAA`

Sequence dihitung berdasarkan order transport yang sudah memiliki prefix `{USER_CODE}{YY}{MM}{DD}` dan `sales_agent` yang sama.

## Implementasi

- Generator utama: `App\Services\TransportOrderNumberService`
- Submit backend: `OrderController::generateTransportOrderNumber()` mendelegasikan ke shared service.
- Preview halaman detail transport: `HomeController::show_transport()` wajib memakai shared service yang sama.
- Konversi angka ke huruf: `TransportOrderNumberService::numberToLetters()`
- Konversi huruf ke angka: `TransportOrderNumberService::lettersToNumber()`
- Tanggal yang dipakai adalah tanggal order dibuat, bukan tanggal service/pickup.
- Modal order transport wajib menyinkronkan preview `Order No`, hidden `orderno`, dan `data-transport-booking-order-number`. Jika user memilih agent berbeda, preview order number wajib mengikuti `data-order-number` milik agent tersebut.

## Test Guard

Guard test berada di `tests/Feature/TransportOrderNumberTest.php` dan wajib menjaga:

- Format awal `ABC260715A`
- Increment `A`, `B`, `C`
- Transisi `Z -> AA`
- Transisi `ZZ -> AAA`
- Sequence terpisah berdasarkan sales agent dan tanggal order.
