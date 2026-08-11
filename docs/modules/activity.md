# Activity Module

Status: active
Updated: 2026-07-28

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
