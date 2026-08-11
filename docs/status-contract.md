# Shared Service Status Contract

Status: active
Updated: 2026-07-28

Dokumen ini adalah sumber kebenaran kanonik untuk commercial order status dan
service fulfillment empat service publik: Accommodation, Public Transport,
Tour Package, dan Activity.

## Scope

Flow yang dicakup:

```text
Service listing/detail
-> Price display
-> Availability
-> Booking
-> Reservation review
-> Confirmation
-> Invoice
-> Payment
-> Upcoming service
-> Service completion
-> History Order
```

Tidak termasuk Transport Management/SPK, Driver management, Wedding, DOKU, dan Private Villa. Service Transports publik tidak boleh disamakan dengan Transport Management internal.

## Kontrak Final

### Commercial Order Lifecycle

`orders.status` hanya menyatakan posisi komersial order.

```text
Draft -> Pending -> Approved -> Paid
```

Cabang terminal:

```text
Canceled
Rejected
Invalid
Deleted
```

- `Paid` adalah status komersial akhir yang sukses.
- `Completed` bukan nilai canonical `orders.status` dan tidak boleh menjadi
  write baru.
- `Confirmed` dan status legacy lain hanya dapat dibaca melalui compatibility
  path yang terdokumentasi; status tersebut bukan bagian kontrak final.
- Perubahan fulfillment tidak boleh mengubah order sukses dari `Paid`.

### Service Fulfillment Lifecycle

Fulfillment menyatakan apakah layanan yang sudah dibayar telah selesai
diberikan. Marker resmi:

```text
orders.completed_at
orders.completed_by
```

Interpretasi:

```text
completed_at = null     -> layanan belum selesai
completed_at != null    -> layanan telah selesai
```

- `completed_by` diisi dengan user/admin yang melakukan manual completion.
- Automated completion harus tetap auditable dan tidak boleh menyamar sebagai
  user/admin.
- Completion hanya berlaku untuk order `Paid` yang memenuhi aturan tanggal dan
  eligibility service terkait.
- Completion harus atomic dan idempotent: pemrosesan ulang order yang sudah
  memiliki `completed_at` tidak boleh membuat side effect duplikat.
- Keempat public service memakai pemisahan commercial status dan fulfillment
  yang sama. Tanggal/eligibility completion tetap boleh service-specific.

### Reservation Lifecycle

```text
Pending -> Active -> Completed
```

Cabang:

```text
Canceled
```

- Reservation `Completed` adalah status operasional reservation, bukan
  `orders.status`.
- Pada fulfillment completion, reservation relevan menjadi `Completed`.
- Order terkait tetap `Paid`.

### Payment Confirmation dan Invoice

- Payment confirmation tetap memakai `Pending`, `Valid`, dan `Invalid`.
- Invoice payment state tetap diturunkan dari balance dan receipt valid:
  `Unpaid`, `Partially Paid`, atau `Paid`.
- Payment confirmation, invoice payment state, order status, reservation
  status, dan fulfillment state tidak boleh disamakan.

## Current dan History

Definisi canonical lintas empat public service:

| Group | Definisi |
| --- | --- |
| Current | Order non-terminal dengan `completed_at = null`: `Draft`, `Pending`, `Approved`, atau `Paid`. |
| Upcoming / In Service | Subgroup dari order `Paid` dengan `completed_at = null`, diturunkan dari tanggal/jam service-specific. Nilai ini bukan `orders.status`. |
| Completed History | Order tetap `Paid` dan memiliki `completed_at != null`. |
| Closed History | Order dengan commercial terminal state `Canceled`, `Rejected`, `Invalid`, atau `Deleted`, terlepas dari fulfillment marker. |

Aturan tambahan:

- Tanggal yang sudah lewat tidak cukup untuk membuktikan fulfillment selesai.
- Order `Paid` tetap Current/Upcoming/In Service sampai `completed_at` terisi.
- History tidak boleh memerlukan `orders.status = Completed`.
- Compatibility reader untuk nilai legacy dapat dipertahankan sementara, tetapi
  tidak mengubah definisi canonical di atas.

## Completion Side Effects

Satu completion yang sah harus menghasilkan state berikut secara atomic:

```text
orders.status       = Paid
orders.completed_at = waktu completion
orders.completed_by = actor manual jika ada
reservation.status  = Completed
```

Order log/audit trail harus dibuat sesuai pola service. Re-run harus tidak
mengubah order yang sudah selesai dan tidak membuat log atau notification
duplikat.

## Scheduler Contract

- Completion command dapat digunakan sebagai mekanisme automated fulfillment
  hanya setelah didaftarkan dan disetujui untuk environment terkait.
- Command wajib memilih order `Paid` dengan `completed_at = null` dan eligibility
  tanggal yang sah.
- Dokumentasi snippet scheduler bukan bukti bahwa scheduler aktif.
- Status registrasi aktual harus diverifikasi pada `app/Console/Kernel.php`.
- Scheduler fulfillment completion tetap harus disetujui per environment;
  scheduler payment deadline adalah kontrak aktif lintas service.

Payment deadline scheduler adalah kontrak aktif yang terpisah dari fulfillment
completion. Seluruh order `Approved` pada tabel `orders` memakai batas pembayaran
48 jam dan command `orders:auto-cancel-expired-payments` setiap 15 menit. Rule,
payment protection, transaction, dan idempotency dijelaskan pada
`docs/decisions/order-payment-deadline-standard.md`.

## Sumber Kebenaran dan Compatibility

- Dokumen ini menggantikan bagian commercial order completion pada
  `docs/decisions/accommodation-status-contract.md`.
- `docs/decisions/shared-order-status-compatibility-plan.md` adalah rencana yang
  telah digantikan oleh keputusan final ini.
- `docs/decisions/shared-order-status-audit.md` dan
  `docs/decisions/accommodation-status-lifecycle-audit.md` adalah historical
  point-in-time audit, bukan kontrak write baru.
- Dokumen lama tidak dihapus karena tetap berguna sebagai audit trail.

## Aturan Implementasi

- Status transition wajib divalidasi di backend berdasarkan record aktual.
- Order, reservation, payment confirmation, invoice payment state, fulfillment,
  dan history adalah state yang berkaitan tetapi tidak boleh disamakan secara
  implisit.
- Nilai legacy dipertahankan sampai mapping, data verification, migration, rollback, dan regression test siap.
- Jangan melakukan bulk normalization status pada database aktif.
- Satu perubahan status penting harus atomic dengan side effect terkait.
- History harus memakai fulfillment marker dan terminal state canonical, bukan
  filter tanggal atau string legacy dari satu halaman.
- Authorization dan ownership tetap berlaku pada setiap transisi.

## Urutan Prioritas

```text
P0 Security and data integrity
-> P1 Pricing, availability, booking, payment
-> P2 Completion and history
-> P3 UI, cleanup, optimization
```

## Verifikasi Wajib

- Test happy path lifecycle.
- Test transition yang tidak valid.
- Test ownership dan authorization.
- Test duplicate submission dan transaction rollback.
- Test payment, completion, upcoming service, dan history.
- Gunakan database testing terpisah sesuai `docs/testing.md`.

## Referensi Modul

- `docs/modules/accommodation.md`
- `docs/modules/transport.md`
- `docs/modules/tour-package.md`
- `docs/modules/activity.md`
