# Accommodation Module

Status: active
Updated: 2026-08-10

## Scope

Accommodation mencakup service publik `Hotel`, `Hotel Promo`, dan `Hotel Package`, dari listing/detail sampai booking, reservation, invoice, payment, completion, dan history.

## Boundary

- Tidak mencakup Private Villa.
- Gunakan ownership dan service guard pada route order/payment bersama.
- Harga authoritative dihitung server-side.
- Availability dan inventory kamar harus diverifikasi pada saat write, bukan hanya saat render halaman.

## Kontrak Aktif

- Pricing: `docs/decisions/accommodation-pricing-contract.md`
- Status dan fulfillment final: `docs/status-contract.md`
- Kontrak Accommodation lama:
  `docs/decisions/accommodation-status-contract.md` (`superseded`)
- Security: `docs/security-rules.md`
- Database dan testing: `docs/database.md`, `docs/testing.md`
- Frontend booking contract:
  `docs/decisions/accommodation-frontend-booking-standard.md`

## Commercial Status dan Fulfillment

- Commercial order mengikuti
  `Draft -> Pending -> Approved -> Paid`, dengan terminal
  `Canceled`, `Rejected`, `Invalid`, atau `Deleted`.
- Order Accommodation yang selesai tetap memiliki `orders.status = Paid`.
- Completion tidak menulis `Completed` ke `orders.status`; completion mengisi
  `orders.completed_at` dan actor manual pada `orders.completed_by`.
- Reservation mengikuti `Pending -> Active -> Completed`, dengan cabang
  `Canceled`.
- Current mencakup order non-terminal dengan `completed_at = null`.
- Completed History memerlukan `completed_at != null`; Closed History memakai
  terminal commercial state. Checkout yang lewat saja tidak cukup untuk
  menyatakan fulfillment selesai.
- Completion berdasarkan checkout/manual eligibility harus atomic dan
  idempotent.

## Audit dan Implementasi

- Authorization/IDOR: `docs/decisions/accommodation-authorization-idor-audit.md`
- Status lifecycle audit:
  `docs/decisions/accommodation-status-lifecycle-audit.md` (`historical`)
- Roadmap end-to-end: `docs/decisions/service-booking-flow-audit-roadmap.md`

## Flow Verifikasi

```text
Listing/detail -> price -> availability -> booking -> reservation review
-> confirmation -> invoice -> payment -> upcoming -> completion -> history
```

Setiap perubahan wajib menelusuri route, middleware, controller/request/service, model/query, view/asset, dan redirect/response.

## Hotel Package Cancellation Policy

- Hotel Package menyimpan cancellation policy per locale melalui
  `cancellation_policy`, `cancellation_policy_traditional`, dan
  `cancellation_policy_simplified`.
- Field bersifat nullable untuk kompatibilitas data lama. Saat policy package
  kosong, order baru memakai policy hotel induk sebagai fallback.
- Saat order dibuat, ketiga versi policy disalin ke `orders` sebagai snapshot.
  Detail order wajib membaca snapshot sesuai locale agar perubahan package
  berikutnya tidak mengubah ketentuan booking historis.
