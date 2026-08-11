---
title: Accommodation Status Contract
status: superseded
updated_at: 2026-07-28
approved_at: 2026-07-27
superseded_by: docs/status-contract.md
applies_to:
  - accommodation
  - orders
  - reservations
  - payments
  - history
---

# Accommodation Status Contract

> Status: `superseded` pada 2026-07-28 oleh
> `docs/status-contract.md`. Isi di bawah dipertahankan sebagai audit trail
> keputusan Accommodation sebelumnya. Bagian yang menetapkan
> `orders.status = Completed` tidak lagi aktif. Kontrak final mempertahankan
> order sukses sebagai `Paid`, memakai `orders.completed_at` /
> `orders.completed_by` untuk fulfillment, dan tetap mengizinkan reservation
> `Completed`.

## 1. Purpose

Dokumen ini mendefinisikan kontrak status canonical untuk lifecycle Accommodation sebelum ada perubahan code, schema, query, data, scheduler, authorization, atau payment behavior.

Status dokumen ini adalah `superseded`. Kontrak ini disetujui pada 2026-07-27,
kemudian bagian commercial completion-nya digantikan oleh kontrak lintas
public service pada 2026-07-28.

Tujuan kontrak:

- Memisahkan domain status order, reservation, payment confirmation, dan invoice payment state.
- Menentukan vocabulary canonical yang aman untuk Accommodation.
- Menentukan mapping legacy status yang ditemukan pada audit.
- Menentukan aturan history, cancellation, payment reversal, authorization, dan invariant.
- Menjadi input untuk task implementasi berikutnya tanpa melakukan perubahan implementasi di dokumen ini.

## Approval Decision

Pemilik project menyetujui kontrak status Accommodation pada 2026-07-27 dengan keputusan bisnis berikut:

- Order status dan reservation status tetap dipisahkan sebagai domain berbeda.
- Canonical order lifecycle target adalah `Draft -> Pending -> Approved -> Paid -> Completed`, dengan cabang terminal `Pending -> Rejected`, `Pending -> Invalid`, `Pending -> Canceled`, dan `Approved -> Canceled`.
- Canonical order statuses adalah `Draft`, `Pending`, `Approved`, `Paid`, `Completed`, `Canceled`, `Rejected`, `Invalid`, dan `Deleted`.
- `Confirmed`, order `Active`, `Archive`, `Archived`, `Removed`, lowercase status variants, dan `Accepted` tidak boleh menjadi write baru untuk Accommodation order.
- `Active` tetap valid untuk reservation dan product status.
- Canonical reservation lifecycle target adalah `Pending -> Active -> Completed`, dengan cabang `Pending -> Canceled` dan `Active -> Canceled`.
- Reservation harus dibuat otomatis ketika order berubah menjadi `Pending` dalam service transaction terkontrol.
- Payment confirmation canonical tetap `Pending`, `Valid`, dan `Invalid`.
- Invoice payment state tetap derived: `Unpaid`, `Partially Paid`, dan `Paid`.
- Accommodation completion dilakukan melalui kombinasi scheduler dan manual override yang sah.
- History tidak boleh lagi hanya bergantung pada `checkin < now`.
- Auto-cancel Accommodation harus ditargetkan sebagai scheduler idempotent setiap 15 menit pada task implementasi terpisah.
- Archive bukan order lifecycle status.
- Invariants pada section 15 menjadi aturan wajib untuk implementasi berikutnya.

## 2. Scope

Scope utama:

- Service Accommodation: `Hotel`, `Hotel Promo`, dan `Hotel Package`.
- Shared table: `orders`, `reservations`, `invoice_admins`, `payment_confirmations`, dan `transactions`.
- Customer current order, upcoming/in-service/history grouping, admin review, invoice, manual payment confirmation, and auto-cancel recommendation.

Out of scope:

- Migration atau perubahan enum database.
- Update data existing.
- Perubahan route, controller, model, service, Blade, scheduler, payment gateway, atau policy.
- Refund implementation.
- Doku virtual account integration, kecuali sebagai pertimbangan future payment state.
- Normalisasi lintas service selain compatibility consideration.

## 3. Domain Separation

Status order, reservation, payment confirmation, dan invoice payment state tidak boleh dianggap sebagai daftar status yang sama.

| Domain | Entity/Table | Meaning | Canonical Owner |
| --- | --- | --- | --- |
| Order Status | `orders.status` | Posisi booking customer dalam lifecycle bisnis. | Booking/order domain |
| Reservation Status | `reservations.status` | Proses operasional reservation team untuk mengamankan booking. | Reservation operations |
| Payment Confirmation Status | `payment_confirmations.status` | Hasil pemeriksaan bukti pembayaran manual. | Finance/reservation payment handler |
| Invoice Payment State | derived from `invoice_admins.balance` and valid receipts | Kondisi pelunasan tagihan. Tidak ada kolom `status` pada `invoice_admins` saat audit. | Finance/invoice domain |
| Product Status | `hotels`, `hotel_rooms`, `hotel_promos`, `hotel_packages` | Visibility dan availability produk/rate. | Product/inventory domain |

Decision:

- `Active` boleh tetap digunakan untuk product dan reservation.
- `Active` tidak direkomendasikan sebagai canonical `orders.status` untuk standard Accommodation karena tidak ada dalam enum `orders.status` saat audit dan bertabrakan makna dengan reservation/product.
- `Paid` boleh tetap menjadi order lifecycle milestone, tetapi invoice payment state tetap harus dihitung dari invoice balance dan valid receipts.

## 4. Order Status Contract

Canonical order statuses for Accommodation:

- `Draft`
- `Pending`
- `Approved`
- `Paid`
- `Completed`
- `Canceled`
- `Rejected`
- `Invalid`
- `Deleted` as technical terminal only, not normal business flow

`Confirmed` is deprecated for Accommodation. New writes must not use `Confirmed` or lowercase `confirmed`. Runtime read compatibility may remain until data and query migration is complete.

| Entity | Canonical Status | Definition | Entry Condition | Exit Condition | Final Status |
| --- | --- | --- | --- | --- | --- |
| Order | `Draft` | Order sudah dibuat tetapi belum dikirim oleh customer untuk diproses. | Customer/sales agent starts Accommodation order and minimum order data is stored. | Customer submits order, deletes draft, or admin invalidates after review if allowed. | No |
| Order | `Pending` | Order sudah dikirim dan menunggu review reservation team. | `Draft` submitted, or staff creates order directly for review; reservation `Pending` must be created in the same controlled service transaction. | Approved, rejected, invalid, canceled, or deleted by authorized flow. | No |
| Order | `Approved` | Order diterima operasional, reservation active, invoice exists, and payment is open. | Reservation/admin approves after validating availability and required booking data. | Paid after invoice settlement, canceled before valid/partial payment, invalid/rejected only with controlled admin override. | No |
| Order | `Paid` | Invoice is fully settled or manually settled with auditable reason. | Invoice balance `<= 0` and at least one valid payment or manual settlement log exists. | Completed after checkout/operational completion, refunded if refund flow is approved, or downgraded to Approved after authorized payment reversal. | No |
| Order | `Completed` | Accommodation stay is operationally finished. | Checkout date has passed and order is paid, or reservation/admin performs approved manual completion override. | None, except audited correction/reopen policy in a separate approved workflow. | Yes |
| Order | `Canceled` | Order stopped before completion by customer/admin/system according to cancellation rules. | Pending/Approved order meets manual or auto-cancel condition. | None, except audited reopen policy in a separate approved workflow. | Yes |
| Order | `Rejected` | Reservation/admin rejects the order as not accepted by business. | Pending order fails business acceptance, supplier availability, or admin review. | None, except resubmission creates new order or controlled reopen policy. | Yes |
| Order | `Invalid` | Order requires correction or contains invalid data and cannot proceed as submitted. | Draft/Pending/Approved fails validation or data consistency check. | Customer/admin correction returns to Draft/Pending, or terminal cleanup if abandoned. | No |
| Order | `Deleted` | Technical removed state retained only for compatibility with current enum. | Only explicit, authorized delete/archive strategy after business decision. | None. | Yes |

### Order Status Details

| Status | Allowed Actors | Minimum Condition | Allowed Transitions | Forbidden Transitions | Required Side Effects | Reversible | Applies To |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `Draft` | Customer, sales agent, staff create flow | Authenticated/approved user, valid base order fields, idempotency token recommended | `Pending`, `Invalid`, `Deleted` | `Approved`, `Paid`, `Completed` without review | Order log, duplicate-submit guard | Yes | Shared candidate |
| `Pending` | Customer submit, staff create, reservation/admin review | Submitted order; reservation `Pending` exists or is created by controlled service transaction | `Approved`, `Rejected`, `Invalid`, `Canceled`, `Deleted` | `Paid`, `Completed` | Order log, reservation notification | Yes, only to `Invalid`/correction | Shared candidate |
| `Approved` | Reservation/admin | Availability accepted, reservation `Active`, invoice created, amount known | `Paid`, `Canceled`, `Invalid` with override | `Draft`, `Pending` without reopen reason, `Completed` while unpaid | Reservation active, invoice, confirmation/invoice logs | Yes, controlled override | Shared candidate |
| `Paid` | Finance/reservation/admin, system after valid payment | Invoice balance `<= 0`, valid payment or manual settlement | `Completed`, future `Refunded`, `Approved` after reversal | `Draft`, `Pending`, `Rejected` | Payment log, invoice balance sync, reservation sync decision | Yes, only through reversal | Shared candidate |
| `Completed` | Reservation/admin, scheduler | Checkout passed and paid, or manual completion override | None by default | Any backward transition without correction flow | Completion log, history classification | No by default | Service fulfillment candidate |
| `Canceled` | Customer if allowed, reservation/admin, system scheduler | Cancel policy met; no blocking valid/partial payment unless refund flow exists | None by default | `Paid` without payment reversal/refund | Cancel log, reservation cancel, inventory release | No by default | Shared candidate |
| `Rejected` | Reservation/admin | Review decision rejects order | None by default | `Approved`, `Paid` without reopen task | Rejection reason/log | No by default | Shared candidate |
| `Invalid` | Reservation/admin, validation service | Data invalid or correction required | `Draft`, `Pending`, `Deleted` | `Approved` without corrected data | Invalid reason/log | Yes | Shared candidate |
| `Deleted` | Admin/system only | Approved deletion/archive policy | None | Normal lifecycle transitions | Audit log, hidden from normal lists | No | Shared technical |

## 5. Reservation Status Contract

Canonical reservation statuses for Accommodation:

- `Pending`
- `Active`
- `Completed`
- `Canceled`

`Paid` on reservation is not recommended as a canonical operational status. Codex recommendation: keep payment state in order/invoice/payment domains. If existing code writes reservation `Paid`, treat it as a legacy alias for "reservation active with paid order" until a migration plan exists.

| Entity | Canonical Status | Definition | Entry Condition | Exit Condition | Final Status |
| --- | --- | --- | --- | --- | --- |
| Reservation | `Pending` | Reservation record exists but booking is not operationally active. | Order is submitted or admin opens detail and creates reservation. | Active after admin confirms booking, canceled if order fails or expires. | No |
| Reservation | `Active` | Reservation team has confirmed operational booking and supplier/booking details are being handled. | Order is approved and reservation data exists. | Completed after checkout/operational close, canceled before/within service according to policy. | No |
| Reservation | `Completed` | Reservation operation is finished. | Checkout passed and order is paid, or manual completion override. | None by default. | Yes |
| Reservation | `Canceled` | Reservation operation is stopped. | Linked order is canceled/rejected, or reservation/admin cancels before completion. | None by default. | Yes |

Reservation creation decision:

- Reservation must be created when order enters `Pending`, not only when admin opens detail.
- Existing implementation creates reservation when admin opens order detail if missing; keep compatibility until implementation task changes this.
- Order `Pending` without reservation is a data anomaly and must be included in audit/repair task. Runtime compatibility may tolerate it temporarily until repair is implemented.

Reservation `Active` decision:

- `Active` means operational booking is confirmed/being handled by reservation team.
- It does not mean invoice is paid.
- It should not force order status to `Active`.

## 6. Payment Confirmation Contract

Canonical payment confirmation statuses:

- `Pending`
- `Valid`
- `Invalid`

| Entity | Canonical Status | Definition | Entry Condition | Exit Condition | Final Status |
| --- | --- | --- | --- | --- | --- |
| Payment Confirmation | `Pending` | Receipt uploaded and waiting for admin/finance validation. | Order `Approved`, invoice exists, order not canceled/rejected/invalid/completed, file passes validation. | Valid, Invalid, or replaced by controlled update. | No |
| Payment Confirmation | `Valid` | Receipt accepted and counted toward invoice payment. | Finance/reservation validates receipt amount, currency, date, and invoice link. | Invalid through authorized reversal only. | No |
| Payment Confirmation | `Invalid` | Receipt rejected or reversed and must not count toward invoice balance. | Finance/reservation rejects receipt or reverses a previously valid receipt. | Pending after resubmission or Valid after revalidation. | No |

Rules:

- Receipt may be created only when the order is `Approved`, invoice exists, and auto-cancel check does not cancel the order.
- One invoice may have multiple receipts to support partial payment.
- Duplicate receipt submission must be idempotent by submit token or duplicate guard.
- Partial payment keeps order `Approved`; invoice payment state becomes `Partially Paid`.
- A receipt `Valid -> Invalid` reversal must restore invoice balance by the validated amount.
- If reversal makes invoice balance `> 0`, order should downgrade from `Paid` to `Approved`, not remain `Paid`.
- Reservation remains `Active` during payment reversal as long as order is not canceled. Reversal on `Completed` order is blocked until a reopen/refund workflow is approved.
- Every payment validation and reversal must create audit log/order log.
- Reversal should require elevated admin/finance authorization and must run in a database transaction.

## 7. Invoice Payment State

`invoice_admins` has no `status` column in the audited schema. Invoice payment state should be derived.

| Invoice Payment State | Definition | Derivation Recommendation | Final State |
| --- | --- | --- | --- |
| `Unpaid` | No valid payment counted against invoice. | `balance` equals invoice total and no `Valid` payment confirmation. | No |
| `Partially Paid` | At least one valid payment exists but balance remains positive. | Valid receipt sum is greater than 0 and `balance > 0`. | No |
| `Paid` | Invoice is fully settled. | `balance <= 0` and valid payment/manual settlement exists. | Yes for normal payment, reversible by correction |
| `Overpaid` | Valid payment exceeds invoice total. | `balance < 0`. | Future flow required |
| `Refunded` | Payment has been returned to customer. | No schema/flow confirmed; future feature only. | Future flow required |

Approved rule:

- Do not add invoice status until reporting and data backfill are approved in a separate task.
- Use derived state consistently in UI/reporting after implementation task.

## 8. Canonical Lifecycle Diagram

Approved order lifecycle:

```text
Draft
-> Pending
-> Approved
-> Paid
-> Completed
```

Branches:

```text
Draft -> Deleted
Pending -> Rejected
Pending -> Invalid
Pending -> Canceled
Approved -> Canceled
Approved -> Paid
Paid -> Approved only by payment reversal that reopens balance
Paid -> Completed
```

Approved reservation lifecycle:

```text
Pending
-> Active
-> Completed
```

Branches:

```text
Pending -> Canceled
Active -> Canceled
Active -> Completed
```

Approved payment lifecycle:

```text
Pending -> Valid
Pending -> Invalid
Invalid -> Pending
Invalid -> Valid
Valid -> Invalid only by authorized reversal
```

Evaluation of `Completed`:

- `Completed` is the approved target state for Accommodation because a paid hotel booking is not operationally finished until checkout or manual close.
- Current `orders.status` enum does not include `Completed`; implementation requires a future schema/data compatibility task.
- Until schema/data compatibility is implemented, history must use derived completion rules without writing `Completed`.

## 9. Transition Matrix

| Transition | Entity | Allowed Actor | Required Condition | Side Effects | Forbidden When |
| --- | --- | --- | --- | --- | --- |
| none -> `Draft` | Order | Customer, sales agent | Authenticated/approved user and valid minimum booking data | Order log, idempotency token record recommended | Duplicate submit without idempotency |
| `Draft` -> `Pending` | Order | Customer, sales agent, staff | Order complete enough for review | Reservation `Pending` creation, reservation notification | Order already terminal |
| none -> `Pending` | Reservation | System/service, reservation/admin | Order enters `Pending` or compatibility detail-open path creates reservation | Link `orders.rsv_id`, guest link | Order terminal |
| `Pending` -> `Approved` | Order | Reservation/admin | Reservation exists, availability confirmed, invoice can be created | Reservation `Active`, invoice create/update, logs | Missing reservation/invoice data |
| `Pending` -> `Rejected` | Order | Reservation/admin | Review decision and rejection reason | Log and customer/admin message | Payment exists |
| `Pending` -> `Invalid` | Order | Reservation/admin | Invalid data and correction reason | Log and correction message | Already paid/completed |
| `Pending` -> `Canceled` | Order | Customer if allowed, reservation/admin, system | Cancel policy met before approval | Reservation canceled if exists, log | Valid/partial payment exists |
| `Approved` -> `Paid` | Order | Finance/reservation/admin, system | Invoice balance `<= 0`, valid payment/manual settlement | Payment log, invoice sync | Balance positive |
| `Approved` -> `Canceled` | Order | Reservation/admin, scheduler | Invoice due expired and no valid/partial/pending payment, or manual cancellation policy | Reservation canceled, inventory release, notification/log | Pending, valid, or partial payment exists without review/refund path |
| `Paid` -> `Completed` | Order | Reservation/admin, scheduler | Checkout passed and no open dispute/payment issue, or manual override reason exists | Completion log, history classification, reservation `Completed` | Checkout not passed without override |
| `Paid` -> `Approved` | Order | Finance/admin with reversal permission | Valid receipt reversed and invoice balance becomes positive | Invoice balance restored, log, notify if needed | Order `Completed` until reopen/refund workflow is approved |
| `Pending` -> `Active` | Reservation | Reservation/admin | Order approved and operational booking accepted | Invoice create/update | Order not approved |
| `Active` -> `Completed` | Reservation | Reservation/admin, scheduler | Linked order `Completed`; checkout passed or manual completion override | Completion log | Linked order unpaid |
| `Active` -> `Canceled` | Reservation | Reservation/admin, scheduler | Linked order canceled before completion | Log and inventory release | Linked order completed |
| `Pending` -> `Valid` | Payment Confirmation | Finance/reservation/admin | Receipt verified and invoice linked | Reduce invoice balance; maybe order `Paid` | Receipt belongs to another invoice/order |
| `Valid` -> `Invalid` | Payment Confirmation | Finance/admin with reversal permission | Reversal reason recorded | Restore invoice balance; maybe order `Approved` | Missing transaction/log context |

## 10. Authorization Matrix

Backend authorization is mandatory for every transition. UI visibility is not sufficient.

| Transition | Customer | Sales Agent | Reservation | Admin | System/Scheduler | Required Condition |
| --- | --- | --- | --- | --- | --- | --- |
| `Draft` -> `Pending` | Yes, owner only | Yes, owner/assigned only | Yes, staff-created order | Yes | No | Auth, verified, approved profile, order ownership or staff context |
| `Pending` -> `Approved` | No | No | Yes | Yes | No | Availability confirmed, reservation exists, invoice can be generated |
| `Pending` -> `Rejected` | No | No | Yes | Yes | No | Rejection reason required |
| `Pending` -> `Invalid` | No | No | Yes | Yes | No | Correction/invalid reason required |
| `Approved` -> `Paid` | No | No | Yes | Yes | Yes only through verified payment integration | Invoice balance `<= 0`, valid payment/manual settlement |
| `Approved` -> `Canceled` | Maybe, if cancellation policy approved | Maybe, assigned only | Yes | Yes | Yes | Due expired or cancellation policy met; no valid/partial payment unless refund path |
| `Paid` -> `Completed` | No | No | Yes | Yes | Yes if checkout scheduler approved | Checkout passed or manual override |
| Payment `Pending` -> `Valid` | No | No | Yes | Yes | Yes only for trusted gateway | Receipt verified |
| Payment `Valid` -> `Invalid` | No | No | No by default | Yes or finance supervisor | No | Reversal reason and audit log required |
| Reservation `Pending` -> `Active` | No | No | Yes | Yes | No | Linked order approved |
| Reservation `Active` -> `Completed` | No | No | Yes | Yes | Yes if checkout scheduler approved | Checkout passed or manual override |
| Auto-cancel | No direct write | No direct write | Yes manual | Yes manual | Yes recommended | Idempotent due-date/expiry job with payment guard |

## 11. History Order Rules

Approved rule: use a combination model, not current checkin-only behavior.

Rejected options:

- Check-in only is not sufficient because Accommodation can still be in service after check-in.
- Checkout only is not sufficient because canceled/rejected/invalid orders should leave active work queues immediately.
- `Completed` only is not sufficient until `Completed` exists and legacy data is backfilled.

Recommended grouping:

| Group | Rule | Notes |
| --- | --- | --- |
| Current / Attention | `Draft`, `Pending`, `Approved`, and order bermasalah yang membutuhkan tindakan. | Unpaid orders past checkin/checkout stay here as exception/attention, not completed history. |
| Upcoming Orders | `status = Paid` and `checkin > today`. | Paid booking before stay starts. |
| In-Service Orders | `status = Paid` and `checkin <= today` and `checkout >= today`. | Important for Accommodation because stay is active until checkout. |
| Completed History | `status = Completed`. | `Paid` past checkout can be completed by approved scheduler/manual override after implementation. |
| Closed History | `Canceled`, `Rejected`, or terminal `Invalid`. | These are closed regardless of stay date. |
| Archived Records | Hidden by archive flag/timestamp or `Deleted`, not by unsupported `Archive` order status. | Archive must not rely on an enum value that schema does not support. |

Approved rules:

- History must not depend only on `checkin < now`.
- Keep unpaid past orders visible in an attention/exception bucket, not silently completed history.
- Do not add canonical order status `In Service` in the first implementation stage; derive in-service from `Paid`, `checkin`, and `checkout`.

## 12. Auto-Cancel Rules

Approved contract:

| Rule | Recommendation |
| --- | --- |
| Eligible status | `Approved` only. |
| Time basis | Invoice due date is the primary basis. |
| Grace period | No additional grace is approved in this contract. |
| Payment pending | Do not auto-cancel if any receipt is `Pending`, because payment is under review. |
| Partial paid | Do not auto-cancel without refund/settlement workflow. Keep order `Approved` with invoice state `Partially Paid`. |
| Valid payment | Never auto-cancel without reversal/refund workflow. |
| Reservation | Linked reservation should become `Canceled` with same transaction. |
| Invoice | No invoice status exists. Keep invoice record; do not delete. Optional derived state remains unpaid/partial. |
| Notification | Recommended: send customer and reservation/admin notification once. |
| Inventory | Recommended: release room/availability holds if implementation introduces inventory locking. |
| Scheduler | Active: `orders:auto-cancel-expired-payments`; do not depend on customer opening a page. |
| Frequency | Every 15 minutes as approved initial target. |
| Audit log | Required order log with reason, due date, actor `system`, and idempotency key. |

Idempotency:

- Scheduler must skip orders not `Approved`.
- Scheduler must skip orders with `Pending`, `Valid`, or settlement-equivalent payment.
- Scheduler must not send duplicate notifications for an already canceled order.
- Payment window aktif adalah tepat 48 jam sejak approval/invoice, tanpa grace period.
- Implementasi lintas service mengikuti `docs/decisions/order-payment-deadline-standard.md`.

## 13. Payment Reversal Rules

Recommended reversal contract:

- Only admin/finance supervisor role may reverse `Valid` to `Invalid`.
- Reversal requires reason, timestamp, actor id, original amount, and invoice id.
- Reversal must run inside database transaction.
- Invoice balance must be restored by the reversed valid amount.
- If invoice balance becomes `> 0`, order must downgrade from `Paid` to `Approved`.
- Reservation should remain `Active` unless the order is also canceled or a completed order is reopened by approved correction flow.
- If order is `Completed`, reversal should be blocked unless an explicit reopen/refund workflow exists.
- Reversal should create order log and payment audit log.
- Customer notification is recommended when customer-facing payment state changes.

Partial payment:

- Valid partial payment should not move order to `Paid`.
- Order remains `Approved`.
- Invoice payment state becomes `Partially Paid`.

Overpayment:

- Do not implement `Overpaid` behavior without finance approval.
- If balance becomes negative, display/report as derived `Overpaid` candidate and require manual review.

## 14. Legacy Status Mapping

| Current/Legacy Status | Entity | Canonical Mapping | Migration Required | Runtime Alias Needed | Risk |
| --- | --- | --- | --- | --- | --- |
| `Confirmed` | Order | Deprecated for Accommodation. | Maybe, if existing data uses it. | Yes, temporary read compatibility until migration/query cleanup. | Medium: currently in enum and UI maps. |
| `confirmed` | Order | Legacy runtime alias to relevant canonical mapping; reject writes. | Maybe, if data exists outside enum in other environments. | Yes for reads in admin index until fixed. | High: case mismatch hides records. |
| `Active` | Order | Deprecated for order; use reservation `Active` or future fulfillment status. | Maybe, if data exists. | Yes for history/admin reads until data checked. | High: not in order enum. |
| `active` | Order | Legacy runtime alias; reject writes. | Maybe. | Yes for admin index compatibility. | High: not in enum and lowercase. |
| `Archive` | Order | Deprecated; not an order lifecycle status. Use archive flag/timestamp in future design. | Yes if live data exists or code currently writes it. | Yes, exclude from lists until replaced. | P0: writer conflicts with enum. |
| `Archived` | Reservation/order display | Not canonical order status; use label/flag compatibility only. | Maybe if live data exists. | Yes if old reports use it. | Medium. |
| `Removed` | Order/Product | Product-only canonical; deprecated for orders. | Maybe if order data exists. | Yes for current filters until audited. | Medium: filters exclude it. |
| `Accepted` | Order | Legacy alias to `Approved` only if data/code evidence confirms same meaning; reject writes. | Maybe. | Yes for current helper until fixed. | Medium: not in enum. |
| `invalid` | Order | Alias to `Invalid`; reject writes. | Maybe. | Yes for admin reads until fixed. | High: case mismatch. |
| `rejected` | Order | Alias to `Rejected`; reject writes. | Maybe. | Yes for admin reads until fixed. | High: case mismatch. |
| `Deleted` | Order | Keep as technical terminal status in current enum. Not normal business archive. | No for enum; maybe data cleanup policy. | Yes, keep hidden from active/history by explicit rules. | Medium: meaning unclear. |
| `Canceled` | Order/Reservation | Canonical spelling. | No. | Yes, map `Cancelled` to it. | Low. |
| `Cancelled` | Reservation/dashboard legacy | Alias to `Canceled`; reject new writes after migration plan. | Maybe, if data exists. | Yes for reads until normalized. | Medium: spelling divergence. |

## 15. Invariants

| Invariant ID | Rule | Entities | Severity | Enforcement Layer |
| --- | --- | --- | --- | --- |
| ACC-INV-000 | Order `Pending` must have reservation `Pending`. | Order, Reservation | P0 | Service transaction, audit command, repair task, test |
| ACC-INV-001 | Order `Approved` must have a linked reservation. | Order, Reservation | P0 | Service, validation, audit command, test |
| ACC-INV-002 | Order `Approved` must have invoice or be inside one transaction that creates it. | Order, Reservation, Invoice | P0 | Service, transaction, test |
| ACC-INV-003 | Order `Paid` must have invoice balance `<= 0`. | Order, Invoice | P0 | Service, audit command, test |
| ACC-INV-004 | Order `Paid` must have at least one valid payment or explicit manual settlement log. | Order, Payment, Invoice, Log | P0 | Service, audit command, test |
| ACC-INV-005 | Reservation `Active` must not be linked to order `Draft`. | Order, Reservation | P0 | Service, audit command, test |
| ACC-INV-006 | Order `Canceled` must not accept new payment confirmation. | Order, Payment | P0 | Validation, policy, service |
| ACC-INV-007 | Completed Accommodation must previously be `Paid` and have checkout date passed or manual override. | Order, Reservation | P1 | Service, scheduler, policy, test |
| ACC-INV-008 | Payment reversal must keep invoice balance, order status, and receipt status synchronized. | Order, Invoice, Payment | P0 | Transactional service, test |
| ACC-INV-009 | Order must not be written with a status outside approved contract/schema. | Order | P0 | Validation, enum/service, database |
| ACC-INV-010 | Status comparison must be exact-case and centralized. | Order, Reservation, Payment | P1 | Service, test, code review |
| ACC-INV-011 | Legacy alias must only be used for temporary read compatibility, not new writes. | Order, Reservation, Payment | P1 | Service, validation, audit command |
| ACC-INV-012 | Terminal statuses must not transition without explicit reopen/correction flow. | Order, Reservation | P1 | Policy, service, log |
| ACC-INV-013 | Auto-cancel must be idempotent and logged once. | Order, Reservation, Invoice, Payment | P1 | Scheduler, service, audit command |

## 16. Shared Order Considerations

The `orders` table is shared across services, so Accommodation-specific semantics should not overload global status names.

Recommended global order statuses:

- `Draft`
- `Pending`
- `Approved`
- `Paid`
- `Canceled`
- `Rejected`
- `Invalid`
- `Deleted`

Future global status candidate:

- `Completed`, only after cross-service audit confirms how Transport, Tour, Activity, Restaurant, and Wedding close fulfillment.

Recommended separation:

| Layer | Purpose | Examples |
| --- | --- | --- |
| Global Order Status | Shared customer/business order lifecycle. | `Draft`, `Pending`, `Approved`, `Paid`, `Canceled`, `Rejected`, `Invalid`, `Deleted` |
| Service Fulfillment Status | Service-specific operational state. | Accommodation: `Upcoming`, `In Service`, `Completed`; Transport: `Scheduled`, `In Progress`, `Completed`; Activity: `Scheduled`, `Checked In`, `No Show`, `Completed` |
| Derived Display Group | UI grouping without mutating status. | Current, upcoming, in-service, history, attention |

Future recommendation:

- Consider a separate fulfillment state column/table only after cross-service audit.
- Do not force Accommodation checkout semantics into `orders.status` if other services need different fulfillment rules.

## 17. Approved Business Decisions

| Decision | Approved Choice | Benefits | Risks | Implementation Note | Legacy Data Impact |
| --- | --- | --- | --- | --- | --- |
| When is Accommodation completed? | Combined scheduler and manual override. | Checkout aligns with hotel business; manual supports exceptions. | Scheduler requires reliable dates and timezone handling. | Scheduler may change `Paid -> Completed` after checkout; manual override requires reason and audit trail. | `Paid` past orders may need derived completion/backfill. |
| Is `Confirmed` still needed? | Deprecated for Accommodation. | Reduces ambiguity with reservation confirmation fields. | Existing enum/UI references remain until migration. | Runtime read compatibility can remain; new writes are forbidden. | Existing `Confirmed` data should be audited and mapped safely. |
| Should order and reservation have separate status? | Yes, keep separate. | Matches domain boundaries. | UI mapping must be explicit. | Order lifecycle and reservation operations must be synced by service rules. | Existing reservation `Active` with order `Approved` becomes valid and expected. |
| How should partial payment affect order? | Keep order `Approved`; derive invoice `Partially Paid`. | Avoids extra order status and keeps payment state in invoice domain. | UI/reporting must show invoice state separately. | Order becomes `Paid` only when invoice balance `<= 0`. | No data mutation required initially. |
| What happens when valid payment is reversed? | Downgrade `Paid` to `Approved` if balance becomes positive. | Keeps order/invoice truthful. | Requires careful transaction/logging and notification. | Reversal on `Completed` is blocked until reopen/refund workflow exists. | Existing paid orders need audit if receipts reversed. |
| Should canceled/rejected/invalid appear in History? | Yes, as closed history. | Customer/admin trace remains visible. | Could clutter history without grouping. | Use closed history group separate from completed history. | Existing terminal records remain discoverable. |
| Is archive a status or flag/timestamp? | Archive is not lifecycle status; use flag/timestamp or non-lifecycle mechanism later. | Preserves lifecycle status and relationships. | Requires cleanup of existing `Archive` references. | Do not add `Archive` to canonical `orders.status`. | Existing filters/writers for `Archive` need compatibility and migration task. |
| When should reservation be created? | When order becomes `Pending`. | Prevents missing operational reservation records. | Existing detail-open side effect must be replaced carefully. | Create inside controlled service transaction. | Existing `Pending` without reservation requires repair/audit task. |

## 18. Recommended Implementation Sequence

Do not implement these inside `ACC-STATUS-002`.

1. `ACC-STATUS-003 - Audit shared order status values across all services read-only`.
2. `ACC-SEC-001 - Audit authorization and IDOR protection for Accommodation order detail, invoice, and payment confirmation`.
3. `ACC-PAY-001 - Audit payment confirmation upload, validation, and reversal transaction boundaries`.
4. `ACC-HISTORY-001 - Audit active/current/history filters against checkout-based grouping`.
5. `ACC-STATUS-004 - Implement centralized read-only status constants and compatibility aliases`.
6. `ACC-STATUS-005 - Add focused tests for status readers/writers using isolated test database only`.
7. `ACC-STATUS-006 - Implement status writer guards and payment reversal synchronization`.
8. `ACC-CANCEL-001 - Implement idempotent Accommodation auto-cancel scheduler`.
9. `ACC-HISTORY-003 - Implement checkout-aware current/history grouping`.
10. `ACC-STATUS-007 - Plan schema/data migration for `Completed` and archive flag/timestamp compatibility`.

Recommended next task after this contract:

`ACC-STATUS-003 - Audit shared order status values across all services read-only`

Reason:

- `orders.status` is shared, so no Accommodation status implementation should start until non-Accommodation data/status usage is known.
- It is non-destructive and can be completed with read-only queries and code search.

## 19. Backward Compatibility Strategy

Short-term:

- Keep current schema unchanged.
- Do not write `Completed`, `Archive`, `Active`, lowercase variants, or `Accepted` into `orders.status`.
- Add read aliases in future implementation before changing filters.
- Keep `Canceled` as canonical spelling and read `Cancelled` as legacy alias where reservation/dashboard data requires it.

Medium-term:

- Centralize status vocabulary in service/enum-like constants.
- Replace hardcoded lowercase query filters with canonical exact-case filters plus compatibility aliases.
- Replace archive-as-status with explicit archive flag/timestamp in a future implementation task.
- Use derived history groups before adding `Completed` to schema.

Long-term:

- Consider schema changes only after shared service audit and backfill plan.
- Add migration for `Completed` only if business approves and cross-service semantics are clear.
- Add audit command to detect out-of-contract statuses.

## 20. Acceptance Criteria

| Criterion | Result |
| --- | --- |
| Document has `active` metadata and `approved_at: 2026-07-27`. | Passed |
| Order, reservation, payment confirmation, and invoice payment state are separated. | Passed |
| Canonical status tables include definition, entry condition, exit condition, and final status. | Passed |
| Transition and authorization matrices are documented. | Passed |
| History, auto-cancel, and payment reversal recommendations are documented. | Passed |
| Legacy status mapping covers all requested statuses. | Passed |
| Invariants and enforcement layers are documented. | Passed |
| Shared order considerations are documented. | Passed |
| Approved business decisions include approved choice, benefits, risks, implementation note, and legacy data impact. | Passed |
| Recommended implementation sequence and next task are documented. | Passed |
| No code, database, migration, config, route, controller, service, model, Blade, scheduler, or payment behavior is changed. | Passed |

## 21. Open Questions

1. Should `Completed` be added directly to `orders.status`, or implemented through a separate fulfillment/status mechanism after shared service audit?
2. Should archive be implemented as `archived_at`, a boolean flag, or another non-lifecycle mechanism that fits the project architecture?
3. Should Doku virtual account statuses become authoritative for invoice payment state?
4. Who is the final owner for payment reversal approval: admin, finance, reservation manager, or developer?
5. What business timezone configuration should the completion and auto-cancel schedulers use if project timezone and hotel/service timezone diverge?

## 22. References

- `AGENTS.md`
- `README.md`
- `docs/README.md`
- `docs/coding-standards.md`
- `docs/testing.md`
- `docs/decisions/form-submit-standard.md`
- `docs/decisions/frontend-order-modal-standard.md`
- `docs/decisions/accommodation-status-lifecycle-audit.md`
- `docs/decisions/service-booking-flow-audit-roadmap.md`
