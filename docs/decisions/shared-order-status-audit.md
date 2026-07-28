# Shared Order Status Audit

Status: Active
Task: `ACC-STATUS-003`
Date: 2026-07-27
Scope: read-only audit of shared order status values across service booking flows.
Scope correction: owner decision on 2026-07-27 limits the service booking roadmap to Accommodation, Transports, Tour Packages, and Activities. Transport Management/SPK, Wedding, DOKU, and Private Villa are out of scope unless a future instruction explicitly includes them.

This audit records current implementation behavior only. It does not introduce a new status, migration, database write, query change, controller change, payment change, scheduler change, authorization change, or Blade change.

## 1. Executive Summary

The project uses `orders.status` as a shared lifecycle field for the in-scope public booking services: Accommodation, Transports, Tour Packages, and Activities. Transport Management/SPK, Wedding, DOKU, and Private Villa are not part of the shared public service booking lifecycle for this roadmap.

The active `orders.status` schema enum allows:

- `Draft`
- `Pending`
- `Confirmed`
- `Approved`
- `Canceled`
- `Rejected`
- `Invalid`
- `Paid`
- `Deleted`

Code reads or writes additional values that are not safe for `orders.status` without a schema or compatibility decision:

- `Active`
- `Archive`
- `Archived`
- `Removed`
- `Accepted`
- lowercase variants: `confirmed`, `active`, `invalid`, `rejected`
- proposed future value: `Completed`

Live read-only database data is too small to prove all in-scope service behavior from data alone. Current `orders` rows only contain `Hotel Promo` and `Transport`. No live rows were found in `orders` for `Tour Package`, `Activity`, `Hotel`, or `Hotel Package`.

Transport Management/SPK findings from the original static pass are retained below as historical context only and are classified as `Internal Operational Dependency - Out of Scope`. They are not blockers for Accommodation or for the shared public service order lifecycle.

## 2. Documentation Reviewed

- `AGENTS.md`
- `README.md`
- `docs/README.md`
- `docs/coding-standards.md`
- `docs/testing.md`
- `docs/modules/transport.md`
- `docs/decisions/accommodation-status-lifecycle-audit.md`
- `docs/decisions/accommodation-status-contract.md`
- `docs/decisions/service-booking-flow-audit-roadmap.md`

Important rule applied: database is active and must not be mutated during audit. All database checks were SELECT/SHOW only.

## 3. Scope Boundary: Transports vs Transport Management

Service Transports is the public/customer-facing booking service and remains in scope:

```text
Transport listing/detail
-> Price display/calculation
-> Date and route selection
-> Booking form
-> Order creation
-> Reservation review
-> Approval
-> Invoice/payment
-> Paid
-> Waiting for service date
-> Completion rule to be audited in the Transports phase
-> History Order
```

Transport Management is an internal operational module and is out of scope for this service booking roadmap:

- SPK
- SPK destinations
- driver assignment
- vehicle operational assignment
- driver check-in
- driver report
- public token SPK
- internal route execution monitoring

Classification: `Internal Operational Dependency - Out of Scope`.

SPK status is not a shared order status. SPK lifecycle mismatch is not a blocker for Accommodation, Transports public booking lifecycle, Tour Packages, Activities, payment contract, current order, history order, or `ACC-STATUS-004`. If Service Transports hands off a paid order to Transport Management, this audit only treats that handoff as an integration boundary to verify later: it must not change customer price, payment state, order duplication, or history behavior unexpectedly.

## 4. Services Audited

| Service | Primary order table | Evidence | Current notes |
| --- | --- | --- | --- |
| Accommodation: `Hotel`, `Hotel Promo`, `Hotel Package` | `orders` | `OrderController`, `OrdersAdminController`, order Blade, live `Hotel Promo` rows | Contract is already approved in `docs/decisions/accommodation-status-contract.md`. |
| `Transport` / Service Transports | `orders`, plus booking `reservations`/finance tables | `OrderController`, transport order Blade, live row | Public booking lifecycle is in scope. Transport Management/SPK is out of scope and only an internal operational dependency. |
| `Tour Package` | `orders` | `OrderController`, scheduled command in `Kernel`, tour order Blade | Only standard service with scheduler auto-cancel currently wired. |
| `Activity` | `orders` | `OrderController`, activity order Blade | Detail routing reuses hotel detail route in one resolver path. |
| `Private Villa` | `orders` | `OrderController`, villa order Blade | Out of scope for this roadmap unless the owner gives a new instruction. |
| `Wedding` / `Wedding Package` | `order_weddings`; legacy references to `orders` | `OrderWeddingController`, `OrdersAdminController`, wedding routes/views | Out of scope for this roadmap; retained only as historical context from the original static pass. |

## 5. Shared Statuses Found

| Status | Schema table | Actual DB data | Writers/readers found | Current meaning |
| --- | --- | --- | --- | --- |
| `Draft` | `orders.status`, `order_weddings.status` string | None in live `orders` sample | Public/customer draft flows, order list views | Created but not submitted, or editable draft. |
| `Pending` | `orders.status`, `reservations.status`, `payment_confirmations.status`; also appears in out-of-scope `order_weddings`/SPK tables | `orders`: Hotel Promo 2, Transport 1. `reservations`: Hotel Promo 1, Transport 1. | Order creation/submit, reservation creation, receipt upload | Waiting for admin/payment/review depending on table. |
| `Confirmed` | `orders.status` enum | None | Dashboard and order list readers; not confirmed as active writer in audited paths | Ambiguous old payment/reservation milestone. |
| `Approved` | `orders.status`; also appears in out-of-scope Wedding code | `orders`: Hotel Promo 1 | Admin activation; out-of-scope wedding confirmation also uses it | Approved/validated by admin; standard payment can be uploaded only when order is Approved. |
| `Paid` | `orders.status`; also appears in out-of-scope Wedding code | None | Standard payment validation and final order actions | Order fully paid or manually finalized. |
| `Canceled` | `orders.status`, `reservations.status` | None | Auto-cancel helpers and scheduler | Terminal cancellation due payment deadline or manual path. |
| `Rejected` | `orders.status`, `order_weddings.status` string | None | Admin rejection and list/detail views | Terminal rejection. |
| `Invalid` | `orders.status`, `order_weddings.status` string | None | Admin invalidation and list/detail views | Correction/invalid state. |
| `Deleted` | `orders.status` enum | None | Dashboard exclusion | Soft-deleted-like status, but no dedicated archive/delete timestamp found in `orders`. |
| `Active` | not in `orders.status`; appears in reservations, service catalog models, out-of-scope Wedding/SPK views | `reservations`: Hotel Promo 1, Transport 1 | Reservation activation, service catalog, some order views | Means live/active product or reservation; unsafe as global `orders.status` without schema change. |
| `Archive` / `Archived` | not in `orders.status` | None possible in strict enum | Order list/history filters and admin archive writer | Archive is a view/storage concept, not a valid current `orders.status`. |
| `Removed` | not in `orders.status` | None possible in strict enum | Frontend current/history exclusions | Legacy removal marker; no compatible enum value in current schema. |
| `Accepted` | not in `orders.status` | None possible in strict enum | Frontend active order exclusion | Legacy/obsolete reader value. |
| `Completed` | not in `orders.status`; appears in reservation and out-of-scope Transport Management code | None in `orders` | Completion references outside global order lifecycle | Completion rule for Service Transports requires a future Transports business flow audit. |
| lowercase variants | not in `orders.status` | None possible in strict enum | Admin status grouping/filtering | Case mismatch can hide valid exact-case rows. |

## 6. Database Findings

Read-only database: `online_bali_kami_26`.

### 6.1 `orders`

| Service | Service type | Status | Total |
| --- | --- | --- | ---: |
| `Hotel Promo` | null | `Pending` | 2 |
| `Hotel Promo` | null | `Approved` | 1 |
| `Transport` | `Daily Rent` | `Pending` | 1 |

No unexpected `orders.status` values were present in the live data sample.

### 6.2 `orders` and `reservations`

| Order service | Reservation status | Total |
| --- | --- | ---: |
| `Hotel Promo` | null | 1 |
| `Hotel Promo` | `Active` | 1 |
| `Hotel Promo` | `Pending` | 1 |
| `Transport` | `Pending` | 1 |

Current live data includes one `Hotel Promo` order without a reservation link. Under the Accommodation contract, a submitted `Pending` order without reservation is an anomaly and must be investigated before status migration or automated normalization.

### 6.3 `order_weddings` - Out of Scope Context

`order_weddings` exists and has nullable string `service` and nullable string `status`. No live grouped rows were returned during this audit. Wedding and `order_weddings` are out of scope for the service booking roadmap and must not drive `ACC-STATUS-004`.

### 6.4 `payment_confirmations` and `transactions`

No live grouped rows were returned from `payment_confirmations` or `transactions`. DOKU is out of scope for this roadmap.

### 6.5 `spks` and `spk_destinations` - Internal Operational Dependency - Out of Scope

| Table | Status | Total |
| --- | --- | ---: |
| `spks` | `Pending` | 1 |
| `spk_destinations` | `Pending` | 3 |

Schema for `spks.status` is `enum('Pending','In_progress','Completed')`, but `SpksController` reads/writes `In Progress`, `Expired`, and `Canceled`. This is an internal Transport Management risk only. It is not a blocker for Accommodation, shared public service order lifecycle, or Service Transports booking lifecycle. It should be handled by a separate Transport Management roadmap/task.

## 7. Route and Handler Map

| Area | Routes/controllers involved | Models/tables | Views/partials |
| --- | --- | --- | --- |
| Public standard order detail and history | `OrderController`, `PaymentConfirmationController` | `orders`, `reservations`, `invoice_admins`, `payment_confirmations`, `order_logs` | `layouts/order-hotel.blade.php`, `layouts/order-villa.blade.php`, `layouts/order-tour.blade.php`, `layouts/order-transport.blade.php`, `layouts/order-activity.blade.php`, detail/history views |
| Admin standard order lifecycle | `OrdersAdminController` | `orders`, `reservations`, `invoice_admins`, `payment_confirmations`, `order_logs` | admin order views, `partials/admin-order-status-sidebar.blade.php`, `partials/admin-order-receipt-report-sidebar.blade.php` |
| Wedding order lifecycle | `OrderWeddingController`, `OrdersAdminController`, `PaymentConfirmationController` | `order_weddings`, `reservations`, `invoice_admins`, `payment_confirmations`, `order_logs`, wedding adjunct tables | Out of scope context only |
| Transport Management | `SpksController`, `TransportManagementController` | `spks`, `spk_destinations`, `spks_checkins`, operational transport tables | Internal Operational Dependency - Out of Scope |
| Scheduler | `app/Console/Kernel.php` | `orders`, `reservations`, `invoice_admins`, `payment_confirmations`, `order_logs` | none |

## 8. Status Writers Found

| Writer | Target table | Values written | Service scope |
| --- | --- | --- | --- |
| `OrderController` create/submit handlers | `orders` | `Draft`, `Pending` | Accommodation, Transports, Tour Packages, Activities, generic service paths |
| `OrdersAdminController::func_activate_order` | `orders`, `reservations`, `invoice_admins` | standard order `Approved`; reservation `Active`; special `Wedding Package` branch attempts `Active` for `orders` | standard services, legacy Wedding Package branch |
| `OrdersAdminController::func_update_order_rejected` | `orders` | `Rejected` | standard services |
| `OrdersAdminController::func_update_order_invalid` | `orders` | `Invalid` | standard services |
| `OrdersAdminController::func_archive_order` | `orders` | `Archive` | standard services; incompatible with current `orders.status` enum |
| `OrdersAdminController::func_finalization_order` and related final actions | `orders`, `reservations`, `invoice_admins` | `Paid`, reservation `Paid`, invoice balance zero | standard services |
| `PaymentConfirmationController::payment_confirmation` | `payment_confirmations`; may auto-cancel `orders` | receipt `Pending`; order/reservation `Canceled` through deadline helper | standard services |
| `OrdersAdminController::fconfirmation_payment` | `payment_confirmations`, `invoice_admins`, `orders` | receipt `Valid`/`Invalid`; order `Paid` when balance is settled | standard services |
| `OrderWeddingController` | `order_weddings`, wedding adjunct tables | `Draft`, `Pending`, `Requested`, `Request`, `Active` | Out of scope Wedding context |
| `OrdersAdminController::forder_wedding_confirmation_payment` | `order_weddings`, `payment_confirmations`, `invoice_admins` | receipt `Valid`/`Invalid`; wedding order `Paid` or `Approved` | Out of scope Wedding context |
| `SpksController` | `spks`, `spk_destinations`, `reservations` | `Pending`, `In Progress`, `Completed`, `Expired`, `Canceled`, destination `Visited`, reservation `Completed` | Internal Operational Dependency - Out of Scope |
| `TransportManagementController::checkin` | `spk_destinations`, `spks_checkins` | destination `Visited` | Internal Operational Dependency - Out of Scope |
| `Kernel` scheduler | `orders`, `reservations`, `order_logs` | `Canceled` | Tour Package only |

## 9. Status Readers and Filters Found

| Reader/filter | Values used | Risk |
| --- | --- | --- |
| Frontend current orders in `OrderController::index` | Excludes `Removed`, `Archive`; history by `checkin < now` | Excluded values are not valid in current enum; history is date-driven. |
| Frontend active orders in `OrderController::index` | Excludes `Accepted`, `Draft`; uses future checkin window | `Accepted` is legacy and not valid in current enum. |
| `OrderController::order_history` | Excludes `Removed`, `Archive`; filters standard orders by `checkin < now`; Wedding by event date in out-of-scope branch | Not a lifecycle-completion-aware history for in-scope services. |
| `OrdersAdminController::index` | Groups `Paid`, `Approved`, `confirmed`, `active`, `Pending`, `invalid`, `rejected` | Lowercase values can hide exact-case rows such as `Confirmed`, `Invalid`, `Rejected`. |
| Dashboard service | Counts exact-case order values; reservation includes both `Canceled` and `Cancelled` | Inconsistent cancellation spelling across entities. |
| Order Blade layouts | Read `Active` in several service order UIs | `Active` is not valid in `orders.status`. |
| SPK views/controllers | Use `In Progress`, `Expired`, `Canceled`; schema uses `In_progress` | Internal Transport Management risk only; not part of shared public service status. |

## 10. History Findings

Standard service history is currently derived mainly from date:

- Current order lists use `checkin >= now` with selected status exclusions.
- History uses `checkin < now` with selected status exclusions.
- Wedding history uses `wedding_date` or `reception_date_start` fallback.

There is no verified global `Completed` order status in `orders`. Because of that, "History Order" currently does not mean order lifecycle completion. It means date-based archival/history visibility with service-specific exceptions.

This matches the Accommodation contract warning that history must not depend only on check-in date if the target behavior is lifecycle completion.

## 11. Payment Findings

Standard service payment:

- Customer/admin uploads receipt as `payment_confirmations.status = Pending`.
- Upload is allowed only after standard order is `Approved` in `PaymentConfirmationController::payment_confirmation`.
- Admin validation changes receipt to `Valid` or `Invalid`.
- When accepted payment settles the invoice balance, `orders.status` becomes `Paid`.
- Reversing a previously valid receipt to `Invalid` increases invoice balance, but the audited standard path only sets order to `Paid` when the new balance is below 1. It does not clearly downgrade a previously `Paid` order when balance becomes outstanding again.

Out-of-scope Wedding payment context:

- Wedding receipt upload also uses `payment_confirmations.status = Pending`.
- Wedding validation updates `order_weddings.status` to `Paid` when balance is settled, otherwise `Approved`.
- Wedding payment handling is therefore closer to a derived payment state than standard orders, but it is implemented separately.

Current data has no payment confirmations, so payment status findings are based on code paths, not live payment rows.

## 12. Scheduler Findings

`app/Console/Kernel.php` contains a 15-minute scheduler that auto-cancels only `Tour Package` orders when:

- `orders.status = Approved`
- order has `rsv_id`
- invoice due date is past
- invoice has no payment with status `Pending`, `Valid`, or `Paid`

It updates:

- `orders.status = Canceled`
- `reservations.status = Canceled`
- creates an `order_logs` row

Accommodation, Transports, and Activities do not have the same scheduler path in the audited implementation. Accommodation has request-triggered auto-cancel helpers in public/payment flows, but not a global scheduled command.

## 13. Major Conflicts

| Conflict | Severity | Evidence | Impact |
| --- | --- | --- | --- |
| `orders.status` enum is stricter than code references | P0 | `Archive`, `Active`, `Removed`, `Accepted`, lowercase variants appear in code/views | Writes may fail and filters may silently misclassify orders. |
| Admin order index uses lowercase status keys | P0 | `confirmed`, `active`, `invalid`, `rejected` in admin grouping | Exact-case rows can be absent from admin sections. |
| `Archive` is treated as lifecycle status | P0 | Admin archive writer and frontend exclusions | Current enum does not allow it; archive should be separate state/flag or mapped. |
| SPK schema/controller mismatch | Out of scope | Schema uses `In_progress`; controller uses `In Progress`, `Expired`, `Canceled` | Internal Transport Management risk only; not a blocker for service booking roadmap. |
| `Completed` is not an order state | P1 | `orders.status` does not include it; completion behavior is not verified for Transports/Tour/Activity | Adding global `Completed` without service-specific design can break current/history semantics. |
| Standard and Wedding payment reconciliation differ | Out of scope | Standard does not clearly downgrade from `Paid`; Wedding sets `Approved` if balance remains | Wedding is out of scope and must not drive shared public service status architecture. |
| History is date-based | P1 | `OrderController::order_history` uses date filters | Paid or active fulfillment may appear in history solely because checkin passed. |

## 14. Corrected Risks

### P0

- Status writes to values outside `orders.status` can fail at runtime, especially `Archive` and any `Active` write to `orders`.
- Admin dashboard/order index can hide valid exact-case statuses because some filters use lowercase status strings.
- Any implementation that changes Accommodation statuses globally can affect Transports, Tour Packages, and Activities because they share `orders.status`.

### P1

- Payment validation and reversal can leave `orders.status` and invoice balance semantically inconsistent.
- Service Transports completion rule is not verified yet and must be marked `Requires Transports Business Flow Audit`; do not infer completion from SPK.
- The current "History Order" behavior is not lifecycle-aware and cannot safely be used as proof that an order is completed.
- Dashboard operational attention checks are broader than service-specific fulfillment needs and can flag service rows using generic nullable driver/guide fields.

### Internal Transport Management Risks - Out of Scope

- SPK schema/controller status mismatch exists, but it is not a public service order lifecycle blocker.
- SPK, driver assignment, vehicle assignment, destination visited status, driver report, geolocation, and internal operational dashboard require separate Transport Management tasks.

## 15. Recommended Shared Architecture

Do not overload `orders.status` with service fulfillment states until each service has a verified migration and compatibility plan.

Recommended separation:

| Layer | Recommended responsibility | Current values to preserve initially |
| --- | --- | --- |
| Global order lifecycle | Commercial/order lifecycle across Accommodation, Transports, Tour Packages, and Activities | `Draft`, `Pending`, `Approved`, `Paid`, `Canceled`, `Rejected`, `Invalid`, `Deleted`; keep `Confirmed` as legacy read-compatible until audited. |
| Payment confirmation lifecycle | Receipt workflow | `Pending`, `Valid`, `Invalid`; derive invoice state from invoice balance and valid receipts. |
| Reservation lifecycle | Operational reservation handling | `Pending`, `Active`, `Completed`, `Canceled`; handle `Cancelled` as legacy spelling where existing data/code requires it. |
| Service fulfillment lifecycle | Service-specific service date/completion rules | Accommodation stay state, Transports completion rule marked `Requires Transports Business Flow Audit`, Tour execution state, Activity attendance state. |
| Archive/history | Visibility and reporting classification | Prefer separate archived flag/date or derived history filters, not `orders.status = Archive`. |

Compatibility principles:

- Keep exact-case schema values as the only valid write targets for `orders.status`.
- Treat lowercase status strings and non-enum values as read-only legacy aliases until removed by a planned cleanup.
- Do not add `Completed` to `orders.status` before deciding whether completion is global order lifecycle or service-specific service completion for the four in-scope services.
- Standardize payment-derived state before changing list/history filters.
- Do not use SPK completion as canonical Service Transports completion before a separate integration contract exists.

## 16. Recommended Next Task

Recommended next task:

`ACC-STATUS-004 - Define shared status compatibility and data verification plan`

Why this should be first:

- `ACC-STATUS-003` found cross-service conflicts that can break writes or hide records before Accommodation-only implementation begins.
- Owner scope now limits the shared status compatibility target to Accommodation, Transports, Tour Packages, and Activities.
- Live data does not contain all four in-scope services, so a compatibility plan must state what can be verified from data and what needs seed/staging fixtures or production-like read-only snapshots.
- It lets the project decide whether `Completed`, `Archive`, and `Active` are global order states or service-specific states before any code or migration work.

Dependencies:

- Approved Accommodation contract in `docs/decisions/accommodation-status-contract.md`.
- This shared audit.
- Read-only data snapshot that includes at least one row per in-scope service, or documented absence of data per service.
- Review of admin list/history/payment writers before any status write is changed.

Risks:

- If skipped, a status fix for Accommodation may unintentionally affect Transports, Tour Packages, or Activities.
- If `Archive` or `Active` are normalized without compatibility aliases, existing UI filters and old records may become unreachable.
- If `Completed` is added globally too early, history and fulfillment semantics can diverge further.

Acceptance criteria for `ACC-STATUS-004`:

- One approved compatibility table for canonical values, legacy aliases, forbidden writes, and display labels.
- One data verification checklist for Accommodation, Transports, Tour Packages, and Activities.
- Decision recorded for `Completed`, `Archive`, `Active`, `Confirmed`, `Removed`, and lowercase variants.
- Section `Scope Boundary: Transports vs Transport Management` included.
- Table included with columns: `Service`, `Order Lifecycle`, `Reservation Review`, `Payment`, `Service Date`, `Completion`, `History`.
- Exclusion boundary recorded for Wedding, DOKU, Private Villa, Transport Management, SPK, driver, vehicle assignment, and internal operational flow.
- Explicit migration/no-migration recommendation with rollback and data-safety notes.
- No production data mutation during planning.

## 17. Acceptance Criteria for This Audit

| Criteria | Result |
| --- | --- |
| In-scope public booking services were audited statically | Passed |
| Live status values inventoried read-only | Passed with limitation: active DB lacks rows for Tour Packages and Activities |
| Shared writers/readers documented | Passed |
| Payment, history, and scheduler interactions documented | Passed |
| P0/P1 cross-service risks listed | Passed |
| Transport Management/SPK separated from Service Transports lifecycle | Passed after owner scope correction |
| Next task recommended without implementation | Passed |
| Code/database/config/migration unchanged | Passed |

Task status recommendation for roadmap: `Needs Data Verification 2026-07-27`. The audit is complete from static code and available read-only data, but live data does not cover every in-scope service.

## 18. Files Inspected

- `routes/web.php`
- `app/Console/Kernel.php`
- `app/Services/AdminDashboardService.php`
- `app/Http/Controllers/OrderController.php`
- `app/Http/Controllers/OrdersAdminController.php`
- `app/Http/Controllers/OrderWeddingController.php`
- `app/Http/Controllers/PaymentConfirmationController.php`
- `app/Http/Controllers/ReservationController.php`
- `app/Http/Controllers/SpksController.php`
- `app/Http/Controllers/TransportManagementController.php`
- `app/Models/Orders.php`
- `app/Models/OrderWedding.php`
- `app/Models/Reservation.php`
- `app/Models/InvoiceAdmin.php`
- `app/Models/PaymentConfirmation.php`
- `app/Models/Spks.php`
- `app/Models/SpkDestinations.php`
- `database/migrations/2022_08_31_060155_create_orders_table.php`
- `database/migrations/2022_11_10_082855_create_reservations_table.php`
- `database/migrations/2022_11_08_090414_create_invoice_admins_table.php`
- `database/migrations/2023_12_08_142026_create_payment_confirmations_table.php`
- `database/migrations/2024_01_09_102710_create_order_weddings_table.php`
- `resources/views/layouts/order-hotel.blade.php`
- `resources/views/layouts/order-villa.blade.php`
- `resources/views/layouts/order-tour.blade.php`
- `resources/views/layouts/order-transport.blade.php`
- `resources/views/layouts/order-activity.blade.php`
- `resources/views/layouts/order-wedding.blade.php`
- `resources/views/partials/admin-order-status-sidebar.blade.php`
- `resources/views/partials/admin-order-receipt-report-sidebar.blade.php`

Out-of-scope Wedding and Transport Management files remain listed because they were inspected during the original static pass and are now classified as out-of-scope context.

## 19. Read-Only Queries Used

The audit used framework-bootstrapped read-only `SELECT` and `SHOW COLUMNS` queries for:

- `orders` status by service and service type
- `orders` unexpected status values
- `reservations` status by service
- `invoice_admins` presence through reservation relation
- `payment_confirmations` status counts
- `transactions` status counts
- `order_weddings` status by service, now out-of-scope context
- `spks` status counts, now internal out-of-scope context
- `spk_destinations` status counts, now internal out-of-scope context
- relevant column definitions for `orders`, `order_weddings`, and `spks`

No write query, migration, tinker mutation, seed, truncate, drop, or schema change was executed.
