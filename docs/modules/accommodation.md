# Accommodation Module

Status: active
Updated: 2026-07-28

## Scope

Accommodation mencakup service publik `Hotel`, `Hotel Promo`, dan `Hotel Package`, dari listing/detail sampai booking, reservation, invoice, payment, completion, dan history.

## Boundary

- Tidak mencakup Private Villa.
- Gunakan ownership dan service guard pada route order/payment bersama.
- Harga authoritative dihitung server-side.
- Availability dan inventory kamar harus diverifikasi pada saat write, bukan hanya saat render halaman.

## Kontrak Aktif

- Pricing: `docs/decisions/accommodation-pricing-contract.md`
- Status: `docs/decisions/accommodation-status-contract.md`
- Shared lifecycle entry: `docs/status-contract.md`
- Security: `docs/security-rules.md`
- Database dan testing: `docs/database.md`, `docs/testing.md`

## Audit dan Implementasi

- Authorization/IDOR: `docs/decisions/accommodation-authorization-idor-audit.md`
- Status lifecycle: `docs/decisions/accommodation-status-lifecycle-audit.md`
- Roadmap end-to-end: `docs/decisions/service-booking-flow-audit-roadmap.md`

## Flow Verifikasi

```text
Listing/detail -> price -> availability -> booking -> reservation review
-> confirmation -> invoice -> payment -> upcoming -> completion -> history
```

Setiap perubahan wajib menelusuri route, middleware, controller/request/service, model/query, view/asset, dan redirect/response.

