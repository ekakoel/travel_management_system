# Transport Module

Status: active
Updated: 2026-07-28

Dokumen ini adalah pintu masuk modul Service Transports publik. Service ini berbeda dari Transport Management/SPK internal.

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
- Public Transport yang selesai tetap memiliki `orders.status = Paid`.
- Completion mengisi `orders.completed_at` dan actor manual pada
  `orders.completed_by`; `Completed` tidak ditulis ke `orders.status`.
- Reservation mengikuti `Pending -> Active -> Completed`, dengan cabang
  `Canceled`.
- Current adalah order non-terminal dengan `completed_at = null`.
- Completed History memerlukan `completed_at != null`; Closed History memakai
  terminal commercial state.
- Completion harus atomic dan idempotent.
- Status SPK/Transport Management bukan fulfillment marker public Transport.

Standar nomor order berikut menjaga identitas order dan SPK mudah dilacak.

## Order Number Format

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
