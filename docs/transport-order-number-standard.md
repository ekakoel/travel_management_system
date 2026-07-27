# Transport Order Number Standard

Status: active
Updated: 2026-07-27

Standar nomor order transport dipakai untuk menjaga identitas order dan SPK mudah dilacak.

## Format

Gunakan service `app/Services/TransportOrderNumberService.php` sebagai satu source of truth.

Format business-facing:

```text
TRP-YYYYMMDD-XXX
```

- `TRP` = transport order prefix.
- `YYYYMMDD` = tanggal order/creation sesuai domain service.
- `XXX` = sequence harian zero-padded.

## Rules

- Jangan generate nomor manual di controller atau Blade.
- Sequence harian harus konsisten dan aman dari duplikasi.
- Jika nomor dipakai untuk SPK/report/WhatsApp, gunakan nilai dari model/order yang sudah tersimpan.
- Jangan mengubah format tanpa audit invoice, SPK, report, WhatsApp message, search/filter, dan test.

## Implementasi

- Service: `app/Services/TransportOrderNumberService.php`.
- Test guard: `tests/Feature/TransportOrderNumberTest.php`.
- Domain terkait: `OrderController`, `TransportManagementController`, `Spks`, `SpkDestinations`.
