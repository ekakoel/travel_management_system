# Accommodation Status Lifecycle Audit

Status: historical
Updated: 2026-07-27
Task: ACC-STATUS-001
Scope: Read-only audit and documentation. No code, database, migration, configuration, route, model behavior, Blade behavior, or payment behavior was changed.

This document records point-in-time behavior from 2026-07-27. The active final
contract is `docs/status-contract.md`. Date-driven history behavior and legacy
status observations below are retained as audit evidence, not canonical rules.

## 1. Executive Summary

Accommodation booking currently uses three order services in code:

- `Hotel`
- `Hotel Promo`
- `Hotel Package`

The actual database currently contains Accommodation orders only for `Hotel Promo`.

The most important finding is a status contract mismatch:

- `orders.status` is an enum containing `Draft`, `Pending`, `Confirmed`, `Approved`, `Canceled`, `Rejected`, `Invalid`, `Paid`, and `Deleted`.
- Code and views also read or write `Active`, `Archive`, `Removed`, `Accepted`, lowercase `confirmed`, lowercase `active`, lowercase `invalid`, lowercase `rejected`, `Valid`, `Used`, and `Expired`.
- The current database audit did not find out-of-schema status values for Accommodation orders, but the code can still query and sometimes write values that are not accepted by the current `orders.status` enum.

Current live Accommodation order data is small:

- `Hotel Promo` with `Pending`: 2 orders.
- `Hotel Promo` with `Approved`: 1 order.
- No `Hotel` or `Hotel Package` orders in the database audit result.
- No Accommodation payment confirmations or transactions were found through the actual order -> reservation -> invoice relationship.

The safest next task is to define a status contract and compatibility strategy before changing any query or transition behavior.

## 2. Documents Reviewed

- `AGENTS.md`
- `README.md`
- `docs/README.md`
- `docs/coding-standards.md`
- `docs/testing.md`
- `docs/decisions/form-submit-standard.md`
- `docs/decisions/frontend-order-modal-standard.md`
- `docs/decisions/project-blueprint-roadmap.md`
- `docs/decisions/service-booking-flow-audit-roadmap.md`

Documentation and implementation conflict found:

- `docs/decisions/form-submit-standard.md` requires one-time submit tokens for important create forms, but the audited Accommodation create flows show duplicate order protection by `orderno` and do not show the `submission_token` pattern that exists in the tour flow.
- `docs/decisions/service-booking-flow-audit-roadmap.md` correctly flags status inconsistency as a priority risk.

## 3. Entities And Status Columns

| Entity | Table | Column | Schema Values | Default | Nullable | Source File |
| --- | --- | --- | --- | --- | --- | --- |
| Order | `orders` | `status` | enum: `Draft`, `Pending`, `Confirmed`, `Approved`, `Canceled`, `Rejected`, `Invalid`, `Paid`, `Deleted` | `Draft` | No | `database/migrations/2022_08_31_060155_create_orders_table.php` |
| Reservation | `reservations` | `status` | string | none | No | `database/migrations/2022_11_10_082855_create_reservations_table.php` |
| Invoice | `invoice_admins` | none | no `status` column | n/a | n/a | `database/migrations/2022_11_08_090414_create_invoice_admins_table.php` |
| Payment Confirmation | `payment_confirmations` | `status` | string | null | Yes | `database/migrations/2023_12_08_142026_create_payment_confirmations_table.php` |
| Transaction | `transactions` | `status` | string | none | No | `database/migrations/2024_07_12_155314_create_transactions_table.php` |
| Hotel | `hotels` | `status` | string | none | No | `database/migrations/2022_07_06_075834_create_hotels_table.php` |
| Hotel Room | `hotel_rooms` | `status` | string | none | No | `database/migrations/2022_08_23_064452_create_hotel_rooms_table.php` |
| Hotel Price | `hotel_prices` | none | no `status` column in actual DB | n/a | n/a | `database/migrations/2022_08_23_062703_create_hotel_prices_table.php` |
| Hotel Promo | `hotel_promos` | `status` | string | none | No | `database/migrations/2022_09_20_094058_create_hotel_promos_table.php` |
| Hotel Package | `hotel_packages` | `status` | string | none | No | `database/migrations/2022_09_22_090854_create_hotel_packages_table.php` |
| Optional Rate | `optional_rates` | none | no `status` column in actual DB | n/a | n/a | `database/migrations/2022_12_19_115329_create_optional_rates_table.php` |
| Booking Code | `booking_codes` | `status` | string | none | No | `database/migrations/2023_06_22_105103_create_booking_codes_table.php` |
| Promotion | `promotions` | `status` | string | none | No | `database/migrations/2023_07_12_093334_create_promotions_table.php` |
| Doku Virtual Account | `doku_virtual_accounts` | `status` | string | `pending` | No | `database/migrations/2025_03_04_113408_create_doku_virtual_accounts_table.php` |

Model notes:

- `Hotels::scopeActive()` filters `status = Active`.
- `HotelRoom::scopeActive()` filters `status = Active`.
- `HotelPromo::scopeActive()` filters `status = Active`.
- `HotelPackage::scopeActive()` filters `status = Active`.
- `HotelStatusService` can update hotel promo/package status to `Expired`.
- `Orders` has no status cast, constant, enum class, accessor, or mutator found during this audit.
- `Reservation`, `PaymentConfirmation`, and `Transactions` have no status cast, constant, enum class, accessor, or mutator found during this audit.

Seeder notes:

- `HotelsSeeder`, `HotelRoomSeeder`, `HotelPromoSeeder`, `BookingCodeSeeder`, `ReservationSeeder`, and `ServicesSeeder` seed `Active`.
- `OrdersSeeder` seeds `Waiting`, which is not in the current `orders.status` enum and is not present in actual Accommodation order data.
- `InvoiceAdminSeeder` contains commented sample `status = Active`, but actual invoice schema has no `status` column.

## 4. Schema Status Inventory

Actual DB column metadata from read-only `SHOW COLUMNS`:

| Table | Status Column Type | Null | Default | Key |
| --- | --- | --- | --- | --- |
| `orders` | `enum('Draft','Pending','Confirmed','Approved','Canceled','Rejected','Invalid','Paid','Deleted')` | No | `Draft` | Indexed |
| `reservations` | `varchar(255)` | No | null | none |
| `invoice_admins` | none | n/a | n/a | n/a |
| `payment_confirmations` | `varchar(255)` | Yes | null | none |
| `transactions` | `varchar(255)` | No | null | none |
| `hotels` | `varchar(255)` | No | null | none |
| `hotel_rooms` | `varchar(255)` | No | null | none |
| `hotel_prices` | none | n/a | n/a | n/a |
| `hotel_promos` | `varchar(255)` | No | null | none |
| `hotel_packages` | `varchar(255)` | No | null | none |
| `optional_rates` | none | n/a | n/a | n/a |
| `booking_codes` | `varchar(255)` | No | null | none |
| `promotions` | `varchar(255)` | No | null | none |
| `doku_virtual_accounts` | `varchar(255)` | No | `pending` | none |

## 5. Actual Database Status Inventory

Database audited: `online_bali_kami_26`

### Order Services

| Service | Count |
| --- | ---: |
| `Hotel Promo` | 3 |
| `Transport` | 1 |

### Accommodation Order Status

| Service | Status | Count |
| --- | --- | ---: |
| `Hotel Promo` | `Pending` | 2 |
| `Hotel Promo` | `Approved` | 1 |

No `Hotel` or `Hotel Package` order rows were found in the actual status audit.

### Reservation Status For Accommodation Orders

| Reservation Status | Count |
| --- | ---: |
| `Pending` | 1 |
| `Active` | 1 |

One Accommodation `Pending` order has no linked reservation in the combination query.

### Payment Confirmation Status For Accommodation Orders

No payment confirmation rows were found for Accommodation orders through:

`orders.rsv_id -> reservations.id -> invoice_admins.rsv_id -> payment_confirmations.inv_id`

### Transaction Status For Accommodation Orders

No transaction rows were found for Accommodation orders through:

`orders.rsv_id -> reservations.id -> invoice_admins.rsv_id -> transactions.invoice_id`

### Status Combination

| Order Status | Reservation Status | Payment Status | Count |
| --- | --- | --- | ---: |
| `Pending` | null | null | 1 |
| `Pending` | `Pending` | null | 1 |
| `Approved` | `Active` | null | 1 |

### Anomaly Counts

| Check | Count |
| --- | ---: |
| Accommodation orders with status outside `orders.status` enum | 0 |
| Accommodation orders with null or empty status | 0 |
| Accommodation reservations with null or empty status | 0 |
| Accommodation payment confirmations with null or empty status | 0 |
| `Approved` Accommodation orders without invoice | 0 |
| `Paid` Accommodation orders without valid payment | 0 |
| `Canceled`/`Cancelled` Accommodation orders with valid payment | 0 |
| Future Accommodation orders | 3 |
| Past Accommodation orders | 0 |

### Product Status Inventory

| Entity | Status | Count |
| --- | --- | ---: |
| `hotels` | `Active` | 42 |
| `hotels` | `Draft` | 91 |
| `hotels` | `Removed` | 11 |
| `hotel_rooms` | `Active` | 176 |
| `hotel_rooms` | `Draft` | 592 |
| `hotel_promos` | `Active` | 895 |
| `hotel_promos` | `Draft` | 22 |
| `hotel_promos` | `Expired` | 7454 |
| `hotel_packages` | `Active` | 70 |

## 6. Status Writers

| ID | Entity | Status Written | Previous Status Expected | Trigger | File | Method | Authorization | Side Effect |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| W01 | Order | `Draft` | none | Customer creates hotel normal/package/promo order | `app/Http/Controllers/OrderController.php` | `func_create_order_hotel_normal`, `func_create_order_hotel_package`, `func_create_order_hotel_promo`, `create_order` | route group `auth`, `verified`, `profile.complete`, `approve`; controller middleware `auth`, `verified` | Creates order, logs, may create airport shuttle rows |
| W02 | Order | `Pending` | `Draft` or none | Staff/developer creates order, or customer submit runs after create | `app/Http/Controllers/OrderController.php` | hotel create methods and `submitCreatedHotelOrder` | same as W01; staff role check by `position` in controller | Sends reservation email, creates order log |
| W03 | Reservation | `Pending` | none | Admin opens order detail and reservation does not exist | `app/Http/Controllers/OrdersAdminController.php` | `getOrCreateReservationForOrder`, `view_order_admin_detail` | route group `checkPosition:developer,reservation,weddingRsv` | Creates reservation, include/remark, assigns order `rsv_id`, links guests |
| W04 | Order | `Approved` | not explicitly guarded except route/admin action context | Admin activates standard order | `app/Http/Controllers/OrdersAdminController.php` | `func_activate_order` | route group `checkPosition:developer,reservation,weddingRsv` | Updates reservation `Active`, creates/updates invoice, sends/logs operational data |
| W05 | Reservation | `Active` | linked reservation exists | Admin activates order | `app/Http/Controllers/OrdersAdminController.php` | `func_activate_order` | route group `checkPosition:developer,reservation,weddingRsv` | Invoice creation/update |
| W06 | Invoice | no `status` column | n/a | Admin activates/generates invoice | `app/Http/Controllers/OrdersAdminController.php` | `func_activate_order`, `fgenerate_invoice` | route group `checkPosition:developer,reservation,weddingRsv` | Creates invoice balance, currency totals, due date, PDF |
| W07 | Payment Confirmation | `Pending` | order must be `Approved` for customer upload | Customer uploads receipt | `app/Http/Controllers/PaymentConfirmationController.php` | `payment_confirmation` | route group `auth`, `verified`, `profile.complete`, `approve`; status guard `Approved` | Stores receipt file, sends payment confirmation email, creates order log |
| W08 | Payment Confirmation | `Pending` | existing receipt | Customer updates receipt | `app/Http/Controllers/PaymentConfirmationController.php` | `update_payment_confirmation` | route group `auth`, `verified`, `profile.complete`, `approve`; no explicit ownership found in snippet beyond order lookup | Replaces receipt file, creates order log |
| W09 | Payment Confirmation | request status, commonly `Valid` or `Invalid` | `Pending`, `Valid`, or `Invalid` | Admin validates payment receipt | `app/Http/Controllers/OrdersAdminController.php` | `fconfirmation_payment` | route group `checkPosition:developer,reservation,weddingRsv` | Updates receipt amount, currency, date, note; adjusts invoice balance |
| W10 | Order | `Paid` | payment balance becomes `< 1`, or finalization by handler | Admin validates payment or finalizes order | `app/Http/Controllers/OrdersAdminController.php` | `fconfirmation_payment`, `func_finalization_order` | route group `checkPosition:developer,reservation,weddingRsv`; finalization requires `handled_by == Auth::id()` | Sets invoice balance to zero in finalization; may leave reservation unchanged in payment validation |
| W11 | Reservation | `Paid` | linked reservation exists | Admin finalizes order | `app/Http/Controllers/OrdersAdminController.php` | `func_finalization_order` | route group `checkPosition:developer,reservation,weddingRsv`; `handled_by` guard | Sets invoice balance zero |
| W12 | Order | `Rejected` | none explicit | Admin rejects order | `app/Http/Controllers/OrdersAdminController.php` | `func_update_order_rejected` | route group `checkPosition:developer,reservation,weddingRsv` | Stores message, user log, order log |
| W13 | Order | `Invalid` | none explicit | Admin marks order invalid | `app/Http/Controllers/OrdersAdminController.php` | `func_update_order_invalid` | route group `checkPosition:developer,reservation,weddingRsv` | Stores message, user log, order log |
| W14 | Order | `Archive` | usually invalid/rejected context by route hash | Admin archives order | `app/Http/Controllers/OrdersAdminController.php` | `func_archive_order` | route group `checkPosition:developer,reservation,weddingRsv` | Stores message, user log, order log |
| W15 | Order | `Canceled` | `Approved` with expired invoice and no active payment submission | Customer opens invoice/detail/payment paths, or payment upload path | `app/Http/Controllers/OrderController.php`, `app/Http/Controllers/PaymentConfirmationController.php` | `autoCancelExpiredApprovedOrder`, `autoCancelExpiredOrder` | authenticated customer context | Updates reservation `Canceled`, logs auto-cancel |
| W16 | Reservation | `Canceled` | linked order is auto-canceled | Auto-cancel | `app/Http/Controllers/OrderController.php`, `app/Http/Controllers/PaymentConfirmationController.php` | auto-cancel helpers | authenticated customer context | Order log |
| W17 | Reservation | `Active`, `Draft`, `Pending` | reservation admin actions | Manual reservation actions | `app/Http/Controllers/ReservationController.php` | `func_activate_reservation`, `func_deactivate_reservation`, `func_add_rsv_order`, transport reservation methods | `auth`, `verified`; routes vary | Affects reservation listing and downloadable reservation views |
| W18 | Hotel Promo | `Expired` | promo not already `Expired` and past booking period | Hotel admin detail inventory service | `app/Services/Hotels/HotelStatusService.php` | `expirePromosForHotel` | called from hotel admin inventory service | Mutates product status during detail data load |
| W19 | Hotel Package | `Expired` | package not already `Expired` and past stay period | Hotel admin detail inventory service | `app/Services/Hotels/HotelStatusService.php` | `expirePackagesForHotel` | called from hotel admin inventory service | Mutates product status during detail data load |
| W20 | Order/Reservation | `Canceled` | `Tour Package` only | Scheduler every fifteen minutes | `app/Console/Kernel.php` | scheduled closure `orders:auto-cancel-expired-tour-payment` | scheduler | Does not cover Accommodation because query filters `service = Tour Package` |

No payment webhook that directly updates standard Accommodation order status was confirmed in the inspected web/api routes. `doku_virtual_accounts.status` exists but was not linked to Accommodation order lifecycle in this audit.

## 7. Status Readers And Filters

| ID | Page/Process | Entity | Included Status | Excluded Status | File | Method/View | Risk |
| --- | --- | --- | --- | --- | --- | --- | --- |
| R01 | Accommodation listing | Hotel | `Active` | all others | `app/Http/Controllers/FrontEndController.php` | `hotels_service` | Draft/Removed hotels are hidden as expected. |
| R02 | Accommodation detail | Hotel, room, promo, package | `Active`; package/promo date-valid | all others | `app/Http/Controllers/FrontEndController.php` | `accommodation_detail` | Product status and product expiry affect customer entry. |
| R03 | Price page | Hotel room, promo, package, promotion, booking code | room `Active`, promo `Active`, package `Active`, promotion `Active`, booking code `Active` | all others | `app/Http/Controllers/HotelsController.php` | `renderHotelPricePage`, `processPromo` | Shows booking code discount in rate cards, but create storage is not consistently proven for hotel flows. |
| R04 | Customer current orders | Order | all service orders with `checkin >= now` | `Removed`, `Archive` | `app/Http/Controllers/OrderController.php` | `index` | Uses date and excludes statuses not in `orders.status` enum. |
| R05 | Customer current active helper | Order | `status != Accepted`, `status != Draft`, `checkin > now + 7 days` | `Accepted`, `Draft` | `app/Http/Controllers/OrderController.php` | `index` | `Accepted` is not in schema; date logic differs from main current list. |
| R06 | Customer attention cards | Order | `Rejected`, `Invalid` in Blade collection split | none | `resources/views/frontend/home/orders/index.blade.php` | view mapping | Works only for exact-case statuses. |
| R07 | Customer edit visibility | Order | `Draft`, `Invalid` | all others | `resources/views/frontend/home/orders/index.blade.php`, `OrderController` edit methods | view/controller | Rejected can be deleteable in UI but hotel detail rejects it. |
| R08 | Customer hotel detail | Order | future orders not `Draft`, `Invalid`, `Rejected` | `Draft`, `Invalid`, `Rejected`; past orders by date | `app/Http/Controllers/OrderController.php` | `detail_order_hotel` | Approved orders can auto-cancel during detail flow; past orders go to history and cannot use this detail route. |
| R09 | Customer invoice preview/download | Order | `Approved` only | all others | `app/Http/Controllers/OrderController.php` | `preview_order_invoice`, `download_order_invoice` | `Paid` orders do not use these frontend invoice actions. |
| R10 | Hotel detail/sidebar payment UI | Order/payment | invoice exists and order not `Paid`; receipts statuses `Valid`, `Invalid`, `Pending` | order `Paid` hides upload action | `resources/views/frontend/home/orders/details/partials/hotel-detail-modern-sidebar.blade.php`, `hotel-detail-modern-modals.blade.php` | Blade | Upload modal can appear whenever invoice exists and order is not `Paid`, but backend still requires `Approved`. |
| R11 | Customer history | Order | any status, optionally filtered by `Paid`, `Approved`, `Confirmed`, `Active`, `Canceled`, `Rejected`, `Invalid`, `Pending` | `Removed`, `Archive`; `checkin >= now` | `app/Http/Controllers/OrderController.php` | `order_history` | History is based on `checkin < now`, not checkout/completion status. |
| R12 | History badge map | Order | `Draft`, `Pending`, `Approved`, `Confirmed`, `Active`, `Paid`, `Rejected`, `Invalid`, `Canceled` | none | `resources/views/frontend/home/orders/history.blade.php` | view | Includes `Active`, which is outside order enum; exact-case only. |
| R13 | Admin order list | Order | `Paid`, `Approved`, lowercase `confirmed`, lowercase `active`, `Pending`, lowercase `invalid`, lowercase `rejected` | statuses not in this list | `app/Http/Controllers/OrdersAdminController.php` | `index` | Case mismatch means `Confirmed`, `Active`, `Invalid`, `Rejected` exact-case may not appear in expected admin sections. |
| R14 | Admin detail | Order/reservation/payment | any found order by id | none in finder | `app/Http/Controllers/OrdersAdminController.php` | `view_order_admin_detail` | Opening detail can create reservation if missing. |
| R15 | Admin invoice regenerate | Order | `Approved` | all others | `app/Http/Controllers/OrdersAdminController.php` | `canRegenerateStandardInvoice`, `fgenerate_invoice` | Invoice generation blocked if order is not `Approved`. |
| R16 | Payment upload backend | Order/payment | order `Approved`, invoice exists, no expired unpaid state | all others | `app/Http/Controllers/PaymentConfirmationController.php` | `payment_confirmation` | If due date expired and no payment exists, backend cancels before upload. |
| R17 | Payment validation | Payment | request-provided status, logic branches for old/new `Pending`, `Valid`, `Invalid` | none explicit | `app/Http/Controllers/OrdersAdminController.php` | `fconfirmation_payment` | Request status is not visibly constrained in method snippet; balance/status may drift on unusual transitions. |
| R18 | Reservation hotel views | Order | `Active` for Hotel/Hotel Promo/Hotel Package | all others | `app/Http/Controllers/ReservationController.php` | reservation hotel/download/add-order views | Standard Accommodation order flow uses `Approved`, not `Active`; this can hide current approved hotel orders from reservation-specific views. |
| R19 | Scheduler auto-cancel | Order/payment | `Tour Package`, `Approved` | non-tour services | `app/Console/Kernel.php` | scheduled closure | Accommodation not covered by scheduler auto-cancel. |
| R20 | Admin dashboard service | Order/reservation | order statuses `Pending`, `Confirmed`, `Approved`, `Paid`, `Canceled`, `Rejected`, `Invalid`; reservation statuses `Pending`, `Active`, `Approved`, `Paid`, `Canceled`, `Cancelled` | others | `app/Services/AdminDashboardService.php` | status counts | Shows broader status vocabulary than current Accommodation data. |

## 8. Actual Lifecycle Diagram

```text
Accommodation selected
-> price/date checked in HotelsController
-> rate card selected
-> order form opened
-> order row created as Draft for customer or Pending for staff
-> customer create path immediately calls submitCreatedHotelOrder and updates Draft -> Pending
-> admin opens order detail
-> reservation is created as Pending if missing
-> admin activates standard Accommodation order
-> order becomes Approved
-> reservation becomes Active
-> invoice is created or updated
-> customer uploads payment receipt
-> payment confirmation becomes Pending
-> admin validates receipt
-> payment confirmation becomes Valid or Invalid
-> invoice balance is adjusted
-> order becomes Paid if balance < 1
-> future order remains upcoming/current until checkin passes
-> after checkin < now, order appears in History Order unless status is Removed or Archive
```

Rejected branch:

```text
Pending/Approved/etc.
-> admin rejects
-> order becomes Rejected
-> customer current page treats as attention order
-> hotel detail route rejects access
-> history can include it after checkin < now
```

Invalid branch:

```text
Pending/Approved/etc.
-> admin marks invalid
-> order becomes Invalid
-> customer can edit when current
-> hotel detail route rejects access
-> history can include it after checkin < now
```

Auto-cancel branch:

```text
Approved + invoice due_date past + no Pending/Valid/Paid payment
-> detail/invoice/payment handler is opened
-> order becomes Canceled
-> reservation becomes Canceled
-> order log is written
```

Archive branch:

```text
Any order reachable by admin archive route
-> order becomes Archive
-> current/history queries exclude it
```

Removed/Deleted branch:

- `Removed` is read/excluded in customer queries and present as product hotel status, but no standard Accommodation order writer was confirmed in the audited flow.
- `Deleted` exists in `orders.status` enum, but no standard Accommodation transition was confirmed in the audited flow.

Partial payment:

- Supported indirectly by invoice `balance`. A `Valid` payment reduces balance; order becomes `Paid` only when balance `< 1`.
- No distinct order status such as `Partially Paid` exists in schema or audited code.

## 9. Transition Matrix

| From Status | To Status | Entity | Trigger | Actor | Validation | Side Effect | File |
| --- | --- | --- | --- | --- | --- | --- | --- |
| none | `Draft` | Order | Hotel order create by customer | Customer/sales agent | `terms_accepted`; route auth/verified/profile/approve | Order row, logs, optional airport shuttle | `OrderController.php` |
| `Draft` | `Pending` | Order | `submitCreatedHotelOrder` after customer create/submit | Customer/sales agent | Existing order object | Reservation email, order log in DB transaction | `OrderController.php` |
| none | `Pending` | Order | Hotel order create by developer/reservation/author | Staff | position check in controller | Order row, logs, email, admin redirect | `OrderController.php` |
| none | `Pending` | Reservation | Admin opens detail for order without reservation | Reservation/admin/developer | order exists | Reservation, include/remark, guest linking, order `rsv_id` | `OrdersAdminController.php` |
| `Pending` or other | `Approved` | Order | Admin activates standard order | Reservation/admin/developer | no explicit status guard seen in method snippet | Reservation `Active`, invoice create/update, logs | `OrdersAdminController.php` |
| `Pending` | `Active` | Reservation | Admin activates order | Reservation/admin/developer | linked reservation | Invoice create/update | `OrdersAdminController.php` |
| none | none | Invoice | Admin activates/generates invoice | Reservation/admin/developer | order must be `Approved` in `fgenerate_invoice`; activate path writes invoice | Invoice balance/due date/PDF | `OrdersAdminController.php` |
| none | `Pending` | Payment Confirmation | Customer uploads receipt | Customer/sales agent | order must be `Approved`, invoice exists, not auto-canceled | Receipt file, email, order log | `PaymentConfirmationController.php` |
| `Pending` | `Valid` | Payment Confirmation | Admin validates receipt | Reservation/admin/developer | request-provided status | Decrease invoice balance; order `Paid` if balance < 1 | `OrdersAdminController.php` |
| `Pending` | `Invalid` | Payment Confirmation | Admin rejects receipt | Reservation/admin/developer | request-provided status | Receipt fields updated; balance branch for pending->invalid not explicit | `OrdersAdminController.php` |
| `Valid` | `Invalid` | Payment Confirmation | Admin reverses receipt | Reservation/admin/developer | old status `Valid`, new `Invalid` | Increase invoice balance; may leave order `Paid` if new balance >= 1 | `OrdersAdminController.php` |
| `Invalid` | `Valid` | Payment Confirmation | Admin revalidates receipt | Reservation/admin/developer | old status `Invalid`, new `Valid` | Decrease invoice balance; order `Paid` if balance < 1 | `OrdersAdminController.php` |
| `Approved` | `Canceled` | Order | Expired invoice and no payment submission | Customer-triggered page/payment access | order `Approved`, due date past, no `Pending`/`Valid`/`Paid` payment | Reservation `Canceled`, order log | `OrderController.php`, `PaymentConfirmationController.php` |
| `Active` | `Canceled` | Reservation | Auto-cancel linked order | Customer-triggered page/payment access | linked `rsv_id` | order log | `OrderController.php`, `PaymentConfirmationController.php` |
| any | `Rejected` | Order | Admin rejects order | Reservation/admin/developer | order exists | Message, user log, order log | `OrdersAdminController.php` |
| any | `Invalid` | Order | Admin marks invalid | Reservation/admin/developer | order exists | Message, user log, order log | `OrdersAdminController.php` |
| any | `Archive` | Order | Admin archives order | Reservation/admin/developer | order exists | Message, user log, order log; hidden by queries | `OrdersAdminController.php` |
| any handled by current admin | `Paid` | Order | Admin finalization | Handling admin | `handled_by == Auth::id()` | Reservation `Paid`, invoice balance zero, order log | `OrdersAdminController.php` |
| any handled by current admin | `Paid` | Reservation | Admin finalization | Handling admin | `handled_by == Auth::id()` | Invoice balance zero | `OrdersAdminController.php` |

## 10. Canonical And Legacy Status Classification

| Status | Entity | Classification | Recommendation | Evidence |
| --- | --- | --- | --- | --- |
| `Draft` | Order | Canonical | Keep as editable/unsubmitted order state. | In `orders.status` enum; create flow and edit guards use it. |
| `Pending` | Order | Canonical | Keep as submitted/review state. | In enum; create/submit flow writes it; admin list reads it. |
| `Confirmed` | Order | Requires Business Decision | Decide whether it is a real order state or display-only/legacy. | In enum and frontend maps it; main audited admin list reads lowercase `confirmed`. |
| `confirmed` | Order | Invalid/Unexpected | Do not write for `orders.status` while enum is case-sensitive; treat as data/code mismatch unless schema/data says otherwise. | Admin index reads lowercase `confirmed`; not in enum. |
| `Approved` | Order | Canonical | Keep as invoice/payment-open state. | In enum; invoice/payment guards require it; DB has 1 row. |
| `Active` | Order | Requires Business Decision | Clarify if standard orders should ever be `Active`; current enum rejects it. | Frontend maps it; reservation views filter orders `status = Active`; not in `orders.status` enum. |
| `active` | Order | Invalid/Unexpected | Do not write for `orders.status` enum; fix/alias only after data audit. | Admin index reads lowercase `active`; not in enum. |
| `Paid` | Order/Reservation | Canonical for paid completion | Keep for fully paid state; define whether reservation should always match order. | In enum; payment validation/finalization writes order paid; finalization writes reservation paid. |
| `Canceled` | Order/Reservation | Canonical | Keep as auto/manual cancellation value. | In enum; auto-cancel writes order/reservation `Canceled`. |
| `Cancelled` | Reservation | Legacy Alias | Normalize only after business decision; keep as read alias in dashboard until data checked. | Admin dashboard counts reservation `Cancelled`; not seen in Accommodation data. |
| `Rejected` | Order | Canonical | Keep as admin rejection state. | In enum; admin writer and frontend attention use exact case. |
| `rejected` | Order | Invalid/Unexpected | Treat as case inconsistency for orders. | Admin index reads lowercase `rejected`; not in enum. |
| `Invalid` | Order | Canonical | Keep as correction-required state. | In enum; admin writer and edit guards use it. |
| `invalid` | Order | Invalid/Unexpected | Treat as case inconsistency for orders. | Admin index reads lowercase `invalid`; not in enum. |
| `Archive` | Order | Invalid/Unexpected | Requires compatibility plan because code writes it but enum does not allow it. | Admin archive writer uses `Archive`; customer queries exclude it; not in enum. |
| `Archived` | Reservation/product domains | Requires Business Decision | Decide if distinct from `Archive`. | ReservationController reads `Archived`; product domains use `Archived` elsewhere. |
| `Removed` | Order/product | Requires Business Decision | Keep for product removal; decide if orders can use it. | Customer order/history filters exclude `Removed`; hotels data has `Removed`; not in order enum. |
| `Deleted` | Order | Deprecated or Requires Business Decision | Enum value exists but no standard Accommodation writer confirmed. | In enum; not found in actual Accommodation data. |
| `Accepted` | Order | Invalid/Unexpected | Remove from order filters or define business meaning in later task. | Customer index excludes it; not in enum. |
| `Valid` | Payment Confirmation / booking code check | Canonical for payment confirmation; transient for booking code validation | Keep as payment accepted state. | Payment validation and receipt UI use it; booking code helper returns it. |
| `Pending` | Payment Confirmation | Canonical | Keep as receipt awaiting validation. | Customer/admin upload writes it. |
| `Invalid` | Payment Confirmation | Canonical | Keep as rejected receipt state. | Admin validation and receipt UI use it. |
| `Paid` | Payment Confirmation | Requires Business Decision | Code treats it as active payment submission, but writer not confirmed in standard Accommodation flow. | Auto-cancel checks payment status in `Pending`, `Valid`, `Paid`. |
| `Used` | Booking code validation | Canonical transient result | Keep as user feedback, not stored order status. | `HotelsController`/`OrderController` booking code helpers return it. |
| `Expired` | Hotel promo/package, booking code validation | Canonical for product expiry/transient validation | Keep for promo/package expiry and booking code result. | HotelStatusService writes it for promos/packages; DB has expired promos. |

## 11. Current Order Analysis

Current order page uses multiple collections:

- Main `$hotelorders`: `service IN ('Hotel', 'Hotel Promo', 'Hotel Package')`, `sales_agent = current user`, `checkin >= now`, exclude `Removed`, `Archive`.
- General `$orders`: all services for current user, `checkin >= now`, exclude `Removed`, `Archive`.
- `$activeorders`: excludes `Accepted` and `Draft`, uses `checkin > now + 7 days`, and does not exclude `Archive`/`Removed`.
- Blade splits current/attention based on exact-case statuses.

Risk:

- `Archive`, `Removed`, and `Accepted` are not valid order enum values but are part of query logic.
- Future `Rejected`/`Invalid` orders can appear as attention cards.
- `Draft` orders can be editable but hotel detail route rejects them.
- Current page and history page use `checkin`, not `checkout`, to decide whether Accommodation is future/current or historical.

## 12. Reservation Review Analysis

Admin order detail creates a reservation automatically when an order has no `rsv_id`, with reservation `status = Pending`. This means merely opening admin detail can create operational data.

Reservation-specific hotel views filter Accommodation orders by `status = Active`:

- `ReservationController::view_reservation_hotel`
- download/reservation related views
- add-order reservation views

However, standard Accommodation activation uses order `Approved`, not order `Active`, while reservation becomes `Active`.

Risk:

- If reservation screens expect order `Active`, standard approved Accommodation orders can be absent from reservation-specific lists.
- Reservation `Active` and order `Approved` represent different stages but are easy to confuse.

## 13. Payment Status Analysis

Customer upload:

- Requires order `Approved`.
- Requires invoice exists.
- Creates `PaymentConfirmation` with `Pending`.
- Sends reservation mailbox email.

Admin validation:

- Reads `PaymentConfirmation` by receipt id.
- Reads invoice by `receipt.inv_id`.
- Reads reservation by `invoice.rsv_id`.
- Reads order by `order.rsv_id`.
- Request status is assigned to receipt.
- If status changes into `Valid`, invoice balance is reduced.
- If invoice balance `< 1`, order status becomes `Paid`.

Important risks:

- Payment validation can set order `Paid`, but the audited branch does not always update reservation `Paid`.
- Reversing `Valid` to `Invalid` increases balance but does not visibly downgrade an already `Paid` order when new balance is greater than zero.
- `PaymentConfirmationController::update_payment_confirmation` redirects non-wedding orders to `/detail-order-$order->id`, while hotel detail route is `/detail-order-hotel/{id}`. The initial upload uses the hotel-specific redirect helper.
- `invoice_admins` has no `status`; payment completion is inferred from `invoice_admins.balance`.

## 14. Auto-Cancel Analysis

Accommodation auto-cancel mechanisms found:

| Mechanism | Applies To Accommodation | Trigger | Condition | Effect |
| --- | --- | --- | --- | --- |
| `OrderController::autoCancelExpiredApprovedOrder` | Yes, when invoked from frontend invoice/detail flows | Customer opens invoice/detail path using methods that call helper | order `Approved`, invoice due date past, no payment with `Pending`, `Valid`, or `Paid` | order `Canceled`, reservation `Canceled`, order log |
| `PaymentConfirmationController::autoCancelExpiredOrder` | Yes | Customer attempts payment confirmation upload | same condition | order `Canceled`, reservation `Canceled`, order log |
| Scheduler in `app/Console/Kernel.php` | No | every fifteen minutes | `service = Tour Package`, order `Approved`, due date past, no payment | tour order/reservation canceled |
| Admin manual rejected/invalid/archive | Yes | admin action | order exists | order status `Rejected`, `Invalid`, or `Archive` |

Findings:

- Accommodation auto-cancel depends on customer/request access, not on scheduler.
- Auto-cancel is mostly idempotent because it exits if status is not `Approved` or payment exists.
- Order and reservation change together.
- Invoice status cannot change because invoice has no `status` column.
- Payment with `Pending`, `Valid`, or `Paid` blocks auto-cancel.
- No repeated email notification was found in auto-cancel helpers; only order logs are created.
- Race condition risk remains if a payment is being uploaded/validated while auto-cancel evaluates stale state.

## 15. Upcoming Order Analysis

Upcoming/current logic for standard orders is date based:

- Current orders: `checkin >= now`.
- Hotel detail: `checkin > now`.
- Future DB audit: all 3 Accommodation orders are future.

Risk:

- Hotel bookings usually remain operational until checkout, but current/history split uses checkin rather than checkout.
- An ongoing stay after checkin but before checkout can move to history.
- Approved unpaid order can remain current until checkin, then history, even if not completed.

## 16. History Order Analysis

History uses:

- `checkin < now`
- current user as `sales_agent`
- excludes `Removed`, `Archive`
- status filter is optional and exact-case
- service options include `Hotel`, `Hotel Promo`, `Hotel Package`

Answers to required checks:

- Order enters history based on `checkin`, not status completion.
- For Accommodation, checkout would usually be a safer completed boundary, but current implementation uses checkin.
- Unpaid orders can enter history if checkin is past.
- Rejected, Invalid, and Canceled can enter history unless status is filtered by user.
- Paid past orders can be hidden if status is `Archive` or `Removed`.
- Archived orders are excluded from history; direct access depends on route/controller conditions and was not verified for every route.
- Current order, admin order, reservation view, and history queries are not consistent in status case or status vocabulary.

Actual data:

- No past Accommodation orders existed in the audit database, so history behavior could not be verified against live Accommodation rows.

## 17. Authorization Matrix

| Action | Allowed Actor | Backend Guard | UI Guard | Status Guard | Risk |
| --- | --- | --- | --- | --- | --- |
| View Accommodation catalog/detail | Guest/customer | public routes | public UI | product `Active` filters | Low |
| Check price | Customer/sales agent | redirects guest to login; route in authenticated group for price post | check price CTA | valid stay dates | Low |
| Create hotel order | Customer/sales agent/staff | `auth`, `verified`, `profile.complete`, `approve`; controller `auth`, `verified` | order form | none beyond terms and duplicate order number | Missing idempotency token for important create flow. |
| Submit created hotel order | Customer/sales agent | same as create | form submit overlay | none besides existing order | Changes `Draft` to `Pending` immediately. |
| View hotel detail order | Customer/sales agent owner | `sales_agent = Auth::id()` and future `checkin` | order card detail link | rejects `Draft`, `Invalid`, `Rejected` | Uses `checkin > now`; ongoing/past access moves elsewhere. |
| Preview/download invoice | Customer/sales agent owner | owner lookup helper, `Approved` only | invoice action buttons | `Approved` only | `Paid` invoice access comes from history links, not same guard. |
| Upload payment confirmation | Customer/sales agent | route auth/approve; order find by id; status guard `Approved` | modal shown if invoice and not paid | `Approved`, invoice exists, not auto-canceled | Ownership verification relies on surrounding route/session assumptions; should be explicitly audited next. |
| Admin order list/detail | Developer/reservation/weddingRsv | `checkPosition:developer,reservation,weddingRsv` | backend UI | list filters statuses | Opening detail creates reservation. |
| Activate/approve order | Developer/reservation/weddingRsv | route group | backend action | not visibly constrained before update in method snippet | Can move any found non-wedding order to `Approved` if route callable. |
| Generate invoice | Developer/reservation/weddingRsv | route group | backend action | `Approved` only in `fgenerate_invoice` | Activate path also creates/updates invoice. |
| Reject/invalid/archive | Developer/reservation/weddingRsv | route group | backend action | no explicit previous-status guard found | Archive writes value not valid in `orders.status` enum. |
| Validate payment | Developer/reservation/weddingRsv | route group | backend action | request status and receipt old status branches | Request status constraint not confirmed in method; paid reversal risk. |
| Auto-cancel | Customer-triggered helper / scheduler for tour only | authenticated request context for Accommodation helpers | none | `Approved`, expired due date, no payment | Accommodation has no scheduler coverage. |

## 18. Data Anomalies

Confirmed by database:

- No out-of-schema Accommodation order statuses currently present.
- No null or empty Accommodation order/reservation/payment statuses through audited joins.
- No paid-without-valid-payment Accommodation order found.
- No approved-without-invoice Accommodation order found.
- One `Pending` Accommodation order has no linked reservation.

Code/schema anomalies:

- `Archive` is written to `orders.status`, but `orders.status` enum does not allow it.
- `Active` is used as an order status in reservation filters, but standard order enum does not allow it.
- Lowercase `confirmed`, `active`, `invalid`, `rejected` are read by admin order index, but are not enum values.
- `Accepted` is excluded by customer current helper, but is not an enum value.
- `OrdersSeeder` contains `Waiting`, which is not a current enum value.
- `InvoiceAdmin::reservations()` uses `inv_id`, while current invoice lookup and `Reservation::invoice()` use `rsv_id`.

## 19. P0/P1 Risks

P0:

- Status vocabulary mismatch can break admin listing, current order, history, archive behavior, and reservation review.
- `Archive` writer conflicts with `orders.status` enum. If executed against the current schema, it risks SQL failure.
- Accommodation auto-cancel is not scheduled and depends on customer/request access, so expired unpaid orders can remain `Approved` until touched.

P1:

- Reservation views expect order `Active`, while standard Accommodation activation writes order `Approved`.
- Admin index reads lowercase statuses for several sections, while writers use exact-case enum values.
- Payment reversal from `Valid` to `Invalid` can increase invoice balance without clearly downgrading order `Paid`.
- History uses checkin instead of checkout or completion status.
- Customer payment upload is guarded by status, but ownership/IDOR protection should be explicitly verified in the next audit.

## 20. Recommended Canonical Lifecycle

This is a recommendation only. Do not implement without a separate task and business approval.

Recommended order statuses:

```text
Draft -> Pending -> Approved -> Paid
Draft/Pending/Approved -> Invalid
Draft/Pending/Approved -> Rejected
Approved -> Canceled
Invalid/Rejected -> Archived or Deleted only after schema/business decision
```

Recommended reservation statuses:

```text
Pending -> Active -> Paid/Completed
Pending/Active -> Canceled
```

Recommended payment confirmation statuses:

```text
Pending -> Valid
Pending -> Invalid
Invalid -> Pending/Valid only if resubmitted or revalidated
```

Recommended handling:

- Keep `Approved` as the order state where invoice/payment is open.
- Keep `Active` for reservation/product status only unless schema is expanded deliberately.
- Do not use lowercase status values for `orders.status`.
- Replace or formalize `Archive` before any archive action is used against current enum schema.
- Define whether completed Accommodation history should use checkout date, paid status, or a new completion status.

## 21. Required Business Decisions

1. Is `Confirmed` a real order state, or should confirmation be represented by `confirmation_order` and `reservation.send`?
2. Should standard Accommodation orders ever use `Active`, or is `Active` only for reservations/products?
3. Should `Archive` be a real order status? If yes, schema must support it through a new migration; if no, archive behavior must use an existing status or soft delete strategy.
4. Is `Removed` for products only, or can orders also use it?
5. Should history start after checkin, checkout, payment, or operational completion?
6. Should payment validation downgrade order `Paid` if a valid receipt is later marked invalid and balance becomes positive?
7. Should Accommodation auto-cancel be scheduled like Tour Package?
8. Should Doku virtual account status integrate with standard Accommodation payment lifecycle?

## 22. Recommended Next Task

Recommended next task:

`ACC-STATUS-002 - Define and approve canonical Accommodation status contract`

Scope:

- Produce a business-approved status contract for order, reservation, invoice-derived payment state, and payment confirmation.
- Decide compatibility handling for `Confirmed`, `Active`, `Archive`, `Removed`, lowercase statuses, `Accepted`, and `Deleted`.
- Do not migrate or mutate data until the contract is approved.

Reason:

- This task must come before code fixes because current writers, readers, database enum values, and Blade labels do not share one status vocabulary.
- Changing filters or writers before the contract is approved can hide live orders, make admin actions fail, or create inconsistent order/reservation/payment states.
- The live Accommodation data is small, but the same shared order tables are used by other services, so compatibility rules must be explicit before implementation.

Dependencies:

- Business decision on whether `Confirmed`, `Active`, `Archive`, `Removed`, `Accepted`, `Deleted`, and lowercase status variants are real states, legacy aliases, or invalid values.
- Decision on whether history should be based on checkin, checkout, payment completion, or operational completion.
- Decision on whether Accommodation auto-cancel should be scheduled like Tour Package.
- Read-only data audit for shared order statuses outside Accommodation before changing shared status readers or database schema.

Risks:

- A canonical contract that ignores legacy query values can make existing admin or customer records disappear from lists.
- Adding new enum values without data/backfill planning can create schema drift across environments.
- Normalizing status labels in one module can break shared dashboards, reservation views, invoices, or reports that still expect old values.
- Payment reversal behavior can leave order, reservation, invoice balance, and receipt status inconsistent if not included in the contract.

Acceptance criteria for `ACC-STATUS-002`:

- A canonical status table exists for Accommodation order, reservation, payment confirmation, and invoice-derived payment state.
- Each observed non-canonical value is classified as supported, legacy alias, invalid, product-only, or migration candidate.
- Every current writer and reader is mapped to the approved canonical status or compatibility alias.
- Required schema changes, data backfills, query changes, tests, and documentation updates are listed as separate follow-up tasks.
- No production data or schema is changed by `ACC-STATUS-002`.

## 23. Read-Only Queries Used

```sql
SELECT DATABASE() AS db;
```

```sql
SELECT service, COUNT(*) AS total
FROM orders
GROUP BY service
ORDER BY total DESC;
```

```sql
SELECT service, status, COUNT(*) AS total
FROM orders
WHERE service IN ('Hotel','Hotel Promo','Hotel Package')
GROUP BY service, status
ORDER BY service, total DESC;
```

```sql
SELECT r.status, COUNT(*) AS total
FROM reservations r
JOIN orders o ON o.rsv_id = r.id
WHERE o.service IN ('Hotel','Hotel Promo','Hotel Package')
GROUP BY r.status
ORDER BY total DESC;
```

```sql
SELECT pc.status, COUNT(*) AS total
FROM payment_confirmations pc
JOIN invoice_admins i ON i.id = pc.inv_id
JOIN reservations r ON r.id = i.rsv_id
JOIN orders o ON o.rsv_id = r.id
WHERE o.service IN ('Hotel','Hotel Promo','Hotel Package')
GROUP BY pc.status
ORDER BY total DESC;
```

```sql
SELECT t.status, COUNT(*) AS total
FROM transactions t
JOIN invoice_admins i ON i.id = t.invoice_id
JOIN reservations r ON r.id = i.rsv_id
JOIN orders o ON o.rsv_id = r.id
WHERE o.service IN ('Hotel','Hotel Promo','Hotel Package')
GROUP BY t.status
ORDER BY total DESC;
```

```sql
SELECT o.status AS order_status, r.status AS reservation_status, pc.status AS payment_status, COUNT(*) AS total
FROM orders o
LEFT JOIN reservations r ON r.id = o.rsv_id
LEFT JOIN invoice_admins i ON i.rsv_id = r.id
LEFT JOIN payment_confirmations pc ON pc.inv_id = i.id
WHERE o.service IN ('Hotel','Hotel Promo','Hotel Package')
GROUP BY o.status, r.status, pc.status
ORDER BY total DESC;
```

```sql
SELECT status, COUNT(*) AS total
FROM orders
WHERE service IN ('Hotel','Hotel Promo','Hotel Package')
  AND (
    status IS NULL
    OR status = ''
    OR status NOT IN ('Draft','Pending','Confirmed','Approved','Canceled','Rejected','Invalid','Paid','Deleted')
  )
GROUP BY status;
```

```sql
SELECT COUNT(*) AS total
FROM orders o
LEFT JOIN reservations r ON r.id=o.rsv_id
LEFT JOIN invoice_admins i ON i.rsv_id=r.id
WHERE o.service IN ('Hotel','Hotel Promo','Hotel Package')
  AND o.status='Approved'
  AND i.id IS NULL;
```

```sql
SELECT COUNT(DISTINCT o.id) AS total
FROM orders o
LEFT JOIN reservations r ON r.id=o.rsv_id
LEFT JOIN invoice_admins i ON i.rsv_id=r.id
LEFT JOIN payment_confirmations pc ON pc.inv_id=i.id AND pc.status='Valid'
WHERE o.service IN ('Hotel','Hotel Promo','Hotel Package')
  AND o.status='Paid'
  AND pc.id IS NULL;
```

```sql
SELECT COUNT(DISTINCT o.id) AS total
FROM orders o
LEFT JOIN reservations r ON r.id=o.rsv_id
LEFT JOIN invoice_admins i ON i.rsv_id=r.id
LEFT JOIN payment_confirmations pc ON pc.inv_id=i.id AND pc.status='Valid'
WHERE o.service IN ('Hotel','Hotel Promo','Hotel Package')
  AND o.status IN ('Canceled','Cancelled')
  AND pc.id IS NOT NULL;
```

```sql
SELECT status, COUNT(*) AS total
FROM orders
WHERE service IN ('Hotel','Hotel Promo','Hotel Package')
  AND checkin >= CURRENT_DATE
GROUP BY status
ORDER BY total DESC;
```

```sql
SELECT status, COUNT(*) AS total
FROM orders
WHERE service IN ('Hotel','Hotel Promo','Hotel Package')
  AND checkin < CURRENT_DATE
GROUP BY status
ORDER BY total DESC;
```

```sql
SHOW COLUMNS FROM orders WHERE Field = 'status';
SHOW COLUMNS FROM reservations WHERE Field = 'status';
SHOW COLUMNS FROM invoice_admins WHERE Field = 'status';
SHOW COLUMNS FROM payment_confirmations WHERE Field = 'status';
SHOW COLUMNS FROM transactions WHERE Field = 'status';
SHOW COLUMNS FROM hotels WHERE Field = 'status';
SHOW COLUMNS FROM hotel_rooms WHERE Field = 'status';
SHOW COLUMNS FROM hotel_prices WHERE Field = 'status';
SHOW COLUMNS FROM hotel_promos WHERE Field = 'status';
SHOW COLUMNS FROM hotel_packages WHERE Field = 'status';
SHOW COLUMNS FROM optional_rates WHERE Field = 'status';
SHOW COLUMNS FROM booking_codes WHERE Field = 'status';
SHOW COLUMNS FROM promotions WHERE Field = 'status';
SHOW COLUMNS FROM doku_virtual_accounts WHERE Field = 'status';
```

## 24. Files Inspected

Documentation:

- `AGENTS.md`
- `README.md`
- `docs/README.md`
- `docs/coding-standards.md`
- `docs/testing.md`
- `docs/decisions/form-submit-standard.md`
- `docs/decisions/frontend-order-modal-standard.md`
- `docs/decisions/project-blueprint-roadmap.md`
- `docs/decisions/service-booking-flow-audit-roadmap.md`

Routes and controllers:

- `routes/web.php`
- `routes/api.php`
- `app/Http/Controllers/FrontEndController.php`
- `app/Http/Controllers/HotelsController.php`
- `app/Http/Controllers/OrderController.php`
- `app/Http/Controllers/OrdersAdminController.php`
- `app/Http/Controllers/PaymentConfirmationController.php`
- `app/Http/Controllers/ReservationController.php`

Models and services:

- `app/Models/Orders.php`
- `app/Models/Reservation.php`
- `app/Models/InvoiceAdmin.php`
- `app/Models/PaymentConfirmation.php`
- `app/Models/Transactions.php`
- `app/Models/Hotels.php`
- `app/Models/HotelRoom.php`
- `app/Models/HotelPrice.php`
- `app/Models/HotelPromo.php`
- `app/Models/HotelPackage.php`
- `app/Models/BookingCode.php`
- `app/Services/Hotels/HotelStatusService.php`
- `app/Services/Hotels/HotelInventoryService.php`
- `app/Services/Hotels/HotelPricingService.php`
- `app/Services/AdminDashboardService.php`

Jobs and scheduler:

- `app/Console/Kernel.php`
- `app/Jobs/UpdateCurrencyRates.php`
- `app/Jobs/SendOrderUpdatedEmail.php`

Views:

- `resources/views/frontend/landing-page/accommodations/index.blade.php`
- `resources/views/frontend/landing-page/accommodations/detail.blade.php`
- `resources/views/frontend/home/booking/hotel-availability.blade.php`
- `resources/views/frontend/home/booking/orders/hotel-normal.blade.php`
- `resources/views/frontend/home/booking/orders/hotel-package.blade.php`
- `resources/views/frontend/home/booking/orders/hotel-promo.blade.php`
- `resources/views/frontend/home/orders/index.blade.php`
- `resources/views/frontend/home/orders/history.blade.php`
- `resources/views/frontend/home/orders/details/partials/hotel-detail-modern.blade.php`
- `resources/views/frontend/home/orders/details/partials/hotel-detail-modern-sidebar.blade.php`
- `resources/views/frontend/home/orders/details/partials/hotel-detail-modern-modals.blade.php`
- `resources/views/frontend/home/orders/details/partials/invoice-action-buttons.blade.php`
- `resources/views/frontend/home/orders/details/partials/invoice-preview-modal.blade.php`
- `resources/views/frontend/home/orders/details/partials/legacy-order-payment-sidebar.blade.php`
- `resources/views/admin/ordersadmin.blade.php`
- `resources/views/backend/operations/reservations/actions/add-order.blade.php`

Schema and seeders:

- `database/migrations/*orders*`
- `database/migrations/*reservations*`
- `database/migrations/*invoice_admins*`
- `database/migrations/*payment_confirmations*`
- `database/migrations/*transactions*`
- `database/migrations/*hotels*`
- `database/migrations/*hotel_rooms*`
- `database/migrations/*hotel_prices*`
- `database/migrations/*hotel_promos*`
- `database/migrations/*hotel_packages*`
- `database/migrations/*booking_codes*`
- `database/migrations/*promotions*`
- `database/migrations/*doku_virtual_accounts*`
- `database/seeders/*`

## 25. Acceptance Criteria

| Criterion | Result | Evidence |
| --- | --- | --- |
| All Accommodation-related statuses are inventoried from code and schema. | Passed | Sections 3 to 10 list order, reservation, invoice, payment, product, booking code, promotion, and Doku VA status surfaces. |
| Live database status values are audited read-only. | Passed | Section 5 records the read-only query results for orders, reservations, payments, transactions, product statuses, and anomaly checks. |
| Current order and history behavior are documented. | Passed | Sections 11, 15, and 16 document current/upcoming and history filters. |
| Status transition triggers and side effects are documented. | Passed | Sections 6, 8, 9, 13, and 14 document writers, lifecycle, payment handling, and auto-cancel behavior. |
| P0 and P1 risks are identified. | Passed | Section 19 lists P0 and P1 findings. |
| Recommended next task is stated with reason, dependencies, risks, and acceptance criteria. | Passed | Section 22 documents `ACC-STATUS-002`. |
| No code, database, migration, route, model, Blade, configuration, or payment behavior is changed. | Passed | This audit changed documentation only. The database commands used were read-only `SELECT` and `SHOW` queries. |

## 26. Limitations And Unverified Areas

- The audit database contains only three Accommodation orders, all `Hotel Promo`; no live `Hotel` or `Hotel Package` order rows were available to validate those statuses against data.
- No past Accommodation order existed, so History Order behavior is based on code inspection rather than live Accommodation history rows.
- No Accommodation payment confirmation or transaction rows were found, so payment lifecycle data combinations are based on code inspection.
- The audit did not execute route actions or mutate records.
- Payment webhook/Doku integration was not proven to affect standard Accommodation orders.
- Dynamic route/action invocation and Blade forms were inspected by search and targeted reads, but not through browser interaction.
