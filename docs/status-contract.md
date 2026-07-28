# Shared Service Status Contract

Status: active
Updated: 2026-07-28

Dokumen ini adalah pintu masuk kanonik untuk status order dan lifecycle empat service publik: Accommodation, Transports, Tour Packages, dan Activities.

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

## Sumber Kebenaran

- Kontrak Accommodation yang telah disetujui berada di `docs/decisions/accommodation-status-contract.md`.
- Audit nilai status aktual lintas empat service berada di `docs/decisions/shared-order-status-audit.md`.
- Rencana kompatibilitas lintas service masih bersifat proposed di `docs/decisions/shared-order-status-compatibility-plan.md`.
- Jangan menganggap kandidat status bersama sudah diimplementasikan hanya karena tercatat pada rencana kompatibilitas.

## Aturan Implementasi

- Status transition wajib divalidasi di backend berdasarkan record aktual.
- Order, reservation, payment confirmation, invoice payment state, fulfillment, dan history adalah state yang berkaitan tetapi tidak boleh disamakan secara implisit.
- Nilai legacy dipertahankan sampai mapping, data verification, migration, rollback, dan regression test siap.
- Jangan melakukan bulk normalization status pada database aktif.
- Satu perubahan status penting harus atomic dengan side effect terkait.
- History harus berasal dari aturan lifecycle kanonik, bukan hanya filter string yang kebetulan dipakai satu halaman.
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

