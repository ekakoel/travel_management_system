# Activity Module

Status: active
Updated: 2026-07-28

## Scope

Activity mencakup discovery service publik, detail dan harga, booking, reservation review, confirmation, invoice/payment yang digunakan flow aktif, upcoming service, completion, dan history.

## Boundary

- Activity memakai sebagian object lifecycle bersama, tetapi implementasi aktual harus diaudit sebelum menyamakan status atau behavior dengan service lain.
- Jangan memasukkan Wedding, DOKU, Private Villa, Transport Management, atau SPK.
- Harga, availability, ownership, service type, dan status transition wajib diverifikasi server-side.

## Referensi Aktif

- Shared lifecycle: `docs/status-contract.md`
- Security: `docs/security-rules.md`
- Database: `docs/database.md`
- Testing: `docs/testing.md`
- Audit lintas service: `docs/decisions/shared-order-status-audit.md`
- Compatibility plan: `docs/decisions/shared-order-status-compatibility-plan.md`
- Roadmap end-to-end: `docs/decisions/service-booking-flow-audit-roadmap.md`

## Flow Verifikasi

```text
Listing/detail -> price -> availability -> booking -> reservation review
-> confirmation -> invoice -> payment -> upcoming -> completion -> history
```

Setiap perubahan wajib memiliki HTTP Feature Test dan database assertion yang relevan pada database testing terpisah.

