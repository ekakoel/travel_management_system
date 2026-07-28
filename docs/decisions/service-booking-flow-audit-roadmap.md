# Service Booking Flow Audit Roadmap

Status: Active
Updated: 2026-07-27
Scope: Roadmap and discovery only for Accommodation, Transports, Tour Packages, and Activities. No business logic, database, migration, or payment behavior changes are included in this document.

## 1. Purpose

Dokumen ini menjadi roadmap audit end-to-end untuk seluruh service booking flow pada project Balikami Tour.

Fokus audit awal:

- Memetakan alur booking service dari katalog frontend sampai order history.
- Mengidentifikasi dependensi lintas modul booking.
- Mencatat gap dan risiko yang terlihat dari kode saat ini.
- Menyusun urutan kerja aman untuk audit dan perbaikan berikutnya.
- Menganalisis Accommodation secara paling detail sebagai modul prioritas pertama.

Dokumen ini tidak menginstruksikan perubahan schema, perubahan payment gateway, reset data, atau perubahan workflow produksi tanpa audit lanjutan.

Final service order:

1. Accommodation.
2. Transports.
3. Tour Packages.
4. Activities.

Out of scope for this roadmap unless a future owner instruction explicitly includes them:

- Transport Management.
- SPK and SPK Destinations.
- Driver management and vehicle operational assignment.
- Driver check-in, driver report, route execution, and internal transport monitoring.
- Wedding, Wedding Package, and `order_weddings`.
- DOKU.
- Private Villa.

## 2. Reference Documents

Dokumentasi project yang menjadi dasar:

- `AGENTS.md`
- `README.md`
- `docs/README.md`
- `docs/coding-standards.md`
- `docs/architecture.md`
- `docs/testing.md`
- `docs/decisions/multi-language-standard.md`
- `docs/decisions/blade-asset-rules.md`
- `docs/decisions/form-submit-standard.md`
- `docs/frontend-standards.md`
- `docs/decisions/backend-ui-standards.md`
- `docs/decisions/frontend-order-modal-standard.md`
- `docs/decisions/project-blueprint-roadmap.md`
- `docs/decisions/frontend-roadmap.md`
- `docs/decisions/backend-ui-standardization-roadmap.md`
- `docs/decisions/asset-architecture-blueprint.md`
- `docs/decisions/asset-migration-inventory.md`

Aturan penting yang dipakai:

- Perlakukan database sebagai data aktif.
- Jangan menjalankan `migrate:fresh`, `migrate:refresh`, `db:wipe`, truncate, drop table, atau mass delete.
- Jangan mengubah migration lama yang mungkin sudah pernah berjalan.
- Perubahan schema harus melalui migration baru setelah audit data.
- Semua perubahan booking harus menjaga route, status, authorization, validation, reporting, invoice, payment confirmation, dan order history.
- Dokumentasi harus diperbarui jika workflow, rule, permission, API, UI behavior utama, deployment, atau operational command berubah.

## 3. Current Service Booking Map

### 3.1 Public Discovery

Service yang terlihat dari route dan controller saat audit:

- Accommodation
- Tour
- Activity
- Transport

Restaurant, Wedding, Private Villa, DOKU, and Transport Management were observed in the project, but they are outside the final service booking roadmap scope.

Pintu masuk umum:

- Public catalog dan detail page melalui `FrontEndController`.
- Availability atau price check melalui controller service terkait.
- Form order frontend melalui `OrderController` atau controller service terkait.
- Order detail dan order history melalui halaman authenticated frontend.
- Admin review, reservation, invoice, dan payment confirmation melalui backend operation routes.

### 3.2 Shared Booking Objects

Objek yang sering menjadi dependensi lintas service:

- `orders`
- `reservations`
- `invoice_admins`
- `payment_confirmations`
- `transactions`
- `guests`
- `users`
- `usd_rates`
- `taxes`
- `order_logs`
- `order_notes`
- optional service tables seperti optional rate, additional service, transport order, airport shuttle, dan related service snapshots.

### 3.3 Shared Lifecycle

Lifecycle umum yang terlihat:

1. Customer membuka katalog service.
2. Customer membuka detail service.
3. Customer memilih tanggal, guest, room, package, promo, vehicle, tour package, atau activity option.
4. Sistem menghitung harga, availability, atau request quotation.
5. Customer membuat order.
6. Order masuk ke status awal seperti `Draft` atau `Pending`.
7. Admin/reservation melakukan review.
8. Admin membuat atau memperbarui reservation dan confirmation number.
9. Invoice dibuat atau dikirim.
10. Customer/admin mengunggah atau memperbarui payment confirmation.
11. Admin memvalidasi payment.
12. Order masuk ke status lanjutan seperti `Approved`, `Confirmed`, `Paid`, `Canceled`, `Rejected`, atau `Invalid`.
13. Order tampil pada current order, detail order, invoice, atau history.

Status di atas masih perlu dinormalisasi karena kode memakai beberapa variasi status dan kapitalisasi.

### 3.4 Scope Boundary: Transports vs Transport Management

Service Transports is the public/customer booking service and is in scope. Its audit covers:

- Transport listing/detail.
- Public/customer pricing.
- Airport Shuttle, Daily Rent, Transfer, and customer transport service variants found in the catalog.
- Vehicle/service selection.
- Pickup date, pickup time, pickup location, destination, passenger count, luggage rule, additional fee, airport information, and flight information.
- Booking form and order creation.
- Reservation review, confirmation, invoice/payment, paid state, service date, completion rule, and History Order.

Transport Management is an internal operational domain and is out of scope:

- SPK.
- Driver assignment.
- Vehicle operational assignment.
- SPK destination.
- Driver report.
- Public token SPK.
- Driver check-in.
- Destination visited status.
- Internal route execution monitoring.
- Transport administration internal.

Classification: `Internal Operational Dependency - Out of Scope`.

Do not use SPK status to define global order lifecycle, Service Transports booking lifecycle, public pricing, reservation review, payment contract, Current Order, History Order, shared fulfillment architecture, or `ACC-STATUS-004` acceptance criteria.

If paid Service Transports orders hand off to Transport Management, document it later only as an integration boundary. That handoff must not fail the order, change customer price, corrupt payment status, create duplicate orders, or mutate History Order unexpectedly.

## 4. Accommodation Flow - Current Map

Accommodation adalah prioritas audit pertama karena memiliki banyak varian harga dan booking path.

### 4.1 Routes

Public accommodation routes:

- `GET /accommodations`
- `GET /accommodation/{code}`
- `GET /hotel/{code}`
- `GET /hotel-{code}`
- `GET /accommodation/{code}/check-price`
- `GET /hotel/{code}/check-price`

Authenticated customer order routes:

- `GET /orders`
- `GET /orders/history`
- `GET /order-{id}`
- `GET /detail-order-hotel/{id}`
- `GET /edit-order-hotel/{id}`
- `PUT /fsubmit-order-hotel/{id}`
- `POST /order-hotel-promo-{id}`
- `POST /fcreate-order-hotel-promo`
- `POST /order-hotel-package-{id}`
- `POST /fcreate-order-hotel-package-{id}`
- `POST /order-hotel-normal-{id}`
- `POST /fcreate-order-hotel-normal`
- Additional charge, optional rate, and room-order routes.

Admin/reservation routes related to order handling:

- `GET /orders-admin`
- `GET /orders-admin-{id}`
- `PUT /fupdate-confirmation-number-{id}`
- `PUT /fsend-confirmation-{id}`
- `PUT /fresend-confirmation-order-{id}`
- `PUT /fgenerate-invoice-{id}`
- `PUT /factivate-order/{id}`
- `PUT /fadmin-update-order/{id}`
- `PUT /farchive-order/{id}`
- `PUT /fupdate-order-invalid/{id}`
- `PUT /fupdate-order-rejected/{id}`
- `PUT /ffinalization-order-{id}`
- `POST /fpayment-confirmation-{id}`
- `PUT /fupdate-payment-confirmation/{id}`

### 4.2 Controllers

Primary controllers found:

- `FrontEndController`
  - `accommodation_service`
  - `accommodation_detail`
- `HotelsController`
  - `checkPriceEntry`
  - `hotel_price`
  - `hotel_price_page`
  - `extractStayDates`
  - `hotel_price_bookingcode`
- `OrderController`
  - `order_hotel_promo`
  - `order_hotel_package`
  - `order_hotel_normal`
  - `func_create_order_hotel_promo`
  - `func_create_order_hotel_package`
  - `func_create_order_hotel_normal`
  - `create_order`
  - `detail_order_hotel`
  - `detail_order`
  - `order_history`
  - order update and admin-facing helpers
- `OrdersAdminController`
- `PaymentConfirmationController`

### 4.3 Views

Frontend accommodation and booking views found:

- `resources/views/frontend/landing-page/accommodations/index.blade.php`
- `resources/views/frontend/landing-page/accommodations/detail.blade.php`
- `resources/views/frontend/home/booking/hotel-availability.blade.php`
- `resources/views/frontend/home/booking/orders/hotel-normal.blade.php`
- `resources/views/frontend/home/booking/orders/hotel-package.blade.php`
- `resources/views/frontend/home/booking/orders/hotel-promo.blade.php`
- `resources/views/frontend/home/orders/index.blade.php`
- `resources/views/frontend/home/orders/history.blade.php`
- `resources/views/frontend/home/orders/detail.blade.php`
- `resources/views/frontend/home/orders/details/partials/hotel-detail-modern.blade.php`
- `resources/views/frontend/home/orders/details/partials/hotel-detail-modern-child.blade.php`
- `resources/views/frontend/home/orders/details/partials/hotel-detail-modern-room.blade.php`

Backend operation views related to reservations and order actions are under:

- `resources/views/backend/operations/reservations`

### 4.4 Models and Tables

Accommodation product data:

- `Hotels`
- `HotelRoom`
- `HotelPrice`
- `HotelPromo`
- `HotelPackage`
- Extra bed and optional rate models/tables
- Hotel contract, asset, tag, and related hotel services

Booking and finance data:

- `Orders`
- `Reservation`
- `InvoiceAdmin`
- `PaymentConfirmation`
- `Transactions`
- `Guests`
- `AdditionalService`
- `OptionalRateOrder`
- `AirportShuttle`

### 4.5 Pricing Inputs

Accommodation price appears to depend on:

- Base hotel room price.
- Stay date range.
- Number of nights.
- Room quantity.
- Adult and child capacity.
- Extra bed.
- Package or promo rate.
- Booking code or promotion code.
- Optional rates.
- Airport shuttle.
- Currency rate.
- Tax.
- Discount.
- Kickback or markup.

Pricing logic previously appeared spread across controller paths and service classes. `ACC-PRICE-001` now centralizes authoritative Accommodation create-order pricing in `HotelPricingService` for Hotel, Hotel Promo, and Hotel Package create flows; frontend hidden price fields may still exist for display compatibility but must not be treated as the source of truth during order creation. `ACC-PRICE-002` documents and regression-verifies the current source priority, USD/tax rounding, invoice snapshot parity, and transaction rollback expectations in `docs/decisions/accommodation-pricing-contract.md`.

## 5. Status Workflow Risks

Status values observed in routes, controllers, views, and schema include:

- `Draft`
- `Pending`
- `Confirmed`
- `Approved`
- `Canceled`
- `Rejected`
- `Invalid`
- `Paid`
- `Deleted`
- `Active`
- `Removed`
- `Archive`
- `Accepted`
- Lowercase variants such as `confirmed`, `active`, `invalid`, and `rejected`

The original `orders.status` enum migration lists:

- `Draft`
- `Pending`
- `Confirmed`
- `Approved`
- `Canceled`
- `Rejected`
- `Invalid`
- `Paid`
- `Deleted`

Audit risk:

- Code appears to reference statuses not present in the original enum.
- Lowercase status variants may create inconsistent filters.
- Current order and history filtering depends on a mixture of status and dates.
- Payment and reservation status synchronization must be verified before any status cleanup.

No status change should be implemented before running read-only database checks against production-like data.

## 6. Accommodation Gap Analysis

| Priority | Area | Gap / Risk | Impact | First Safe Action |
| --- | --- | --- | --- | --- |
| P0 | Status lifecycle | Status values in code may not match `orders.status` enum and may vary by case. | Orders can disappear from active/history views, fail updates, or be filtered incorrectly. | Build current status inventory from code and data before changing behavior. |
| P0 | Duplicate submit | Tour order flow shows submission token/idempotency patterns, while accommodation create paths need verification. | Double booking, duplicate invoice, duplicate payment request, or inconsistent room allocation. | Audit accommodation forms and create handlers for token/idempotency coverage. |
| P0 | Availability and inventory | Accommodation availability must consider room capacity, date overlap, existing active orders, packages, promos, optional rates, and cancellations. A single locking or availability source was not confirmed during static review. | Overbooking or stale availability display. | Trace `checkPriceEntry`, hotel order creation, and reservation finalization together. |
| P1 | Pricing source of truth | Pricing inputs are broad and may be calculated in multiple controller paths. | Frontend quote, submitted order, invoice, and admin update may diverge. | Compare `HotelsController`, `OrderController`, and `HotelPricingService` formulas. |
| P1 | Reservation relation | `InvoiceAdmin` relation naming needs verification against `invoice_admins.rsv_id` and `inv_id` usage. | Invoice, payment, and reservation links can be incorrect or fragile. | Audit model relationships and live data foreign-key consistency read-only. |
| P1 | Guest relationship | Guest relationships appear service-dependent and should be verified for accommodation orders. | Guest detail can be missing from reservation, invoice, or SPK/reporting flow. | Map guest creation and retrieval from accommodation order form through detail page. |
| P1 | Payment sync | Manual payment confirmation statuses and invoice/order/reservation status transitions need one authoritative workflow. | Order can show paid while payment is pending, or payment accepted while order is not finalized. | Map payment confirmation state machine before code edits. |
| P1 | Admin action side effects | Admin action routes update order status, confirmation, invoice, reservation, and payment-adjacent fields. | Partial update can leave data inconsistent. | Identify every admin route side effect and transaction boundary. |
| P2 | UI consistency | Accommodation order detail uses modern partials while some legacy order views still exist. | Different pages can display different totals, statuses, or labels. | Compare active/detail/history/admin display fields. |
| P2 | Tests | Test coverage and isolated DB test configuration require verification before relying on automated tests. | Regression risk remains high for booking changes. | Add focused tests only after test database isolation is confirmed. |

## 7. Roadmap Phases

### Phase 0 - Discovery Baseline

Status: In progress

Goal:

- Produce a reliable, read-only map of all service booking flows.
- Identify high-risk shared dependencies.
- Prioritize Accommodation audit.

Tasks:

- `DISC-001` Read project rules and docs.
- `DISC-002` List service routes and route middleware.
- `DISC-003` List booking controllers, models, views, and service classes.
- `DISC-004` Map shared booking tables and relations.
- `DISC-005` Record status values used by code and schema.
- `DISC-006` Create this roadmap and update docs index.

Exit criteria:

- Roadmap exists.
- Docs index links roadmap.
- No production behavior changed.

### Phase 1 - Accommodation End-to-End Audit

Status: Not started

Goal:

- Build a verified map of Accommodation booking from public detail page to history, invoice, and payment confirmation.
- Identify exact implementation tasks before modifying behavior.

Tasks:

- `ACC-CAT-001` Audit public Accommodation catalog filters, published status, region, tags, and visibility.
- `ACC-STATUS-001` Completed 2026-07-27: build a read-only Accommodation status and lifecycle inventory. See `docs/decisions/accommodation-status-lifecycle-audit.md`.
- `ACC-STATUS-002` Completed 2026-07-27: define and approve canonical Accommodation status contract. See `docs/decisions/accommodation-status-contract.md`.
- `ACC-STATUS-003` Needs Data Verification 2026-07-27: audit shared order status values across public service booking flows read-only, corrected to exclude Transport Management/SPK, Wedding, DOKU, and Private Villa from the shared lifecycle scope. See `docs/decisions/shared-order-status-audit.md`.
- `ACC-STATUS-004` Completed 2026-07-27: define shared status compatibility and data verification plan for Accommodation, Transports, Tour Packages, and Activities. See `docs/decisions/shared-order-status-compatibility-plan.md`.
- `ACC-DETAIL-001` Audit Accommodation detail page data loading, room display, promo/package display, and status filtering.
- `ACC-PRICE-001` Implemented 2026-07-27: centralize authoritative server-side Accommodation create-order pricing in `HotelPricingService`; ignore hidden client totals at final order creation; add unit and feature regression tests.
- `ACC-PRICE-002` Implemented 2026-07-27: verify Accommodation price parity and transaction safety for Hotel, Hotel Promo, Hotel Package, server-side add-ons, invoice snapshots, and rollback behavior. See `docs/decisions/accommodation-pricing-contract.md`.
- `ACC-PRICE-003` Check whether frontend quoted price and stored order price can diverge.
- `ACC-AVL-001` Audit room availability query for date overlap, status exclusion, canceled/rejected orders, and package/promo paths.
- `ACC-AVL-002` Audit inventory locking or absence of locking during order creation and admin confirmation.
- `ACC-BOOK-001` Audit hotel normal order form request fields and validation.
- `ACC-BOOK-002` Audit hotel package order form request fields and validation.
- `ACC-BOOK-003` Audit hotel promo order form request fields and validation.
- `ACC-BOOK-004` Audit duplicate submit protection for Accommodation forms.
- `ACC-ORDER-001` Trace `func_create_order_hotel_normal` into stored `orders` fields.
- `ACC-ORDER-002` Trace `func_create_order_hotel_package` into stored `orders` fields.
- `ACC-ORDER-003` Trace `func_create_order_hotel_promo` into stored `orders` fields.
- `ACC-ORDER-004` Verify whether Accommodation order creation is atomic across order, guest, optional rate, airport shuttle, and booking code side effects.
- `ACC-REVIEW-001` Audit admin order detail and update actions for Accommodation.
- `ACC-CONF-001` Audit confirmation number creation, sending, resending, and status changes.
- `ACC-INV-001` Audit invoice generation and relation to reservation/order.
- `ACC-PAY-001` Audit payment confirmation upload and update flow.
- `ACC-HISTORY-001` Audit active order and history filters.
- `ACC-HISTORY-002` Define "upcoming", "completed", "canceled", "invalid", and "archived" behavior based on current data.
- `ACC-SEC-001` Completed 2026-07-27: audit authorization and IDOR protection for Accommodation order detail, edit, invoice, payment upload/update, receipt files, and admin payment validation. See `docs/decisions/accommodation-authorization-idor-audit.md`.
- `ACC-SEC-002` Completed 2026-07-27: implement owned Accommodation order and payment authorization guards for customer payment upload/update, hotel detail/edit/submit service guard, admin payment mutation backend guard, safe receipt filename, upload validation, and receipt note escaping. See `docs/decisions/accommodation-authorization-idor-audit.md`.
- `ACC-AUTH-001` Backlog: separate Accommodation payment permissions from legacy Wedding roles; do not change `weddingRsv` compatibility inside ACC-FILE-001.
- `ACC-FILE-001` Audit and plan guarded Accommodation receipt and invoice file delivery.
- `ACC-TEST-001` Identify minimal test coverage needed for the current Accommodation behavior before any fix.

Exit criteria:

- One definitive Accommodation flow diagram exists.
- One status lifecycle table exists.
- Canonical Accommodation status contract is approved and active in `docs/decisions/accommodation-status-contract.md`.
- One pricing rule table exists.
- One validation and authorization matrix exists.
- Implementation tickets can be created with clear blast radius.

### Phase 2 - Accommodation Stabilization Plan

Status: Blocked by Phase 1

Goal:

- Convert audit findings into scoped implementation tasks.

Potential tasks:

- Centralize status definitions without breaking legacy values.
- Add or align idempotency protection for Accommodation order creation.
- Centralize Accommodation pricing calculation if audit confirms divergence.
- Add read-only compatibility checks before changing status filters.
- Add focused tests for pricing, availability, authorization, and duplicate submit.
- Update user-facing docs after behavior changes.

No task in this phase should be implemented until Phase 1 confirms exact current behavior.

### Phase 3 - Other Service Audits

Status: Blocked by Phase 1

Goal:

- Repeat end-to-end audit pattern for the remaining in-scope public service booking flows.

Service order:

1. Transports
2. Tour Packages
3. Activities

Implementation notes:

- `TOUR-E2E-001` Ready 2026-07-27: public Tour Packages availability does not use per-date inventory/quota. Booking eligibility is based on active package status, active non-expired price tier, service date, participant count within backend rules, and reservation team confirmation. Pending Tour orders remain unconfirmed until reservation/admin approval; supplier unavailability is handled by the existing reject/cancel flow. No quota/capacity field is required or approved for Tour Packages.

Reason:

- Transports, Tour Packages, and Activities have service-specific pricing/inventory services and order paths.
- Transport Management/SPK, Wedding, DOKU, Restaurant, and Private Villa are not part of this roadmap unless a future owner instruction explicitly adds them.
- Service Transports completion must be marked `Requires Transports Business Flow Audit` until the Transports phase verifies whether completion is manual, service-date based, integration-handoff based, or a combination.

### Phase 4 - Cross-Service Normalization

Status: Blocked by Phase 1 and Phase 3

Goal:

- Align shared booking behavior without breaking service-specific rules.

Potential tasks:

- Shared status map and transition policy.
- Shared payment confirmation lifecycle.
- Shared invoice/reservation linking rules.
- Shared duplicate-submit policy.
- Shared authorization matrix.
- Shared order history semantics.
- Shared reporting field contract.

Scope guard:

- The shared status map and transition policy must cover only Accommodation, Transports, Tour Packages, and Activities.
- Do not include Wedding, DOKU, Private Villa, SPK, driver assignment, vehicle assignment, destination visited status, driver report, geolocation, or internal Transport Management flow.
- Transport Management findings must be moved to separate non-blocking tasks.

### Phase TM - Transport Management Out-of-Scope Backlog

Status: Non-blocking, out of scope for service booking roadmap

These tasks must not be worked now and are not dependencies for Accommodation, `ACC-STATUS-004`, or the shared public service status contract.

- `TM-DISC-001` Audit Transport Management architecture and SPK lifecycle.
- `TM-STATUS-001` Standardize SPK and destination status values.
- `TM-INTEGRATION-001` Audit handoff from paid Transport orders to Transport Management.

These tasks may only affect the service booking roadmap later as an integration boundary, not as a source of canonical public order lifecycle.

### Phase 5 - Regression and Release Readiness

Status: Blocked by implementation scope

Goal:

- Verify production safety before deployment.

Checklist:

- Focused automated tests for changed flows.
- Manual happy-path test for each affected service.
- Validation failure test.
- Unauthorized access test.
- Duplicate submit test.
- Payment confirmation test.
- Invoice generation test.
- Active/history display test.
- Old data compatibility audit.
- Rollback plan for every migration or config change.

## 8. Read-Only Data Audit Checklist

Run only against a safe read-only connection or backup. Do not update, delete, truncate, drop, or alter data during audit.

### 8.1 Status Inventory

```sql
SELECT status, COUNT(*) AS total
FROM orders
GROUP BY status
ORDER BY total DESC;
```

```sql
SELECT status, COUNT(*) AS total
FROM reservations
GROUP BY status
ORDER BY total DESC;
```

```sql
SELECT status, COUNT(*) AS total
FROM payment_confirmations
GROUP BY status
ORDER BY total DESC;
```

### 8.2 Orders With Unexpected Status

```sql
SELECT id, orderno, service, service_type, status, created_at
FROM orders
WHERE status NOT IN (
    'Draft',
    'Pending',
    'Confirmed',
    'Approved',
    'Canceled',
    'Rejected',
    'Invalid',
    'Paid',
    'Deleted'
)
ORDER BY created_at DESC;
```

### 8.3 Accommodation Order Shape

```sql
SELECT id, orderno, service, service_type, service_id, subservice_id, price_id,
       checkin, checkout, number_of_room, duration, status, final_price, created_at
FROM orders
WHERE service LIKE '%Hotel%'
   OR service LIKE '%Accommodation%'
   OR service_type LIKE '%Hotel%'
   OR service_type LIKE '%Accommodation%'
ORDER BY created_at DESC;
```

### 8.4 Duplicate Order Candidates

```sql
SELECT user_id, service, service_id, subservice_id, checkin, checkout, created_at, COUNT(*) AS total
FROM orders
GROUP BY user_id, service, service_id, subservice_id, checkin, checkout, created_at
HAVING COUNT(*) > 1
ORDER BY total DESC;
```

### 8.5 Orphan Reservation Links

```sql
SELECT o.id, o.orderno, o.rsv_id, o.status
FROM orders o
LEFT JOIN reservations r ON r.id = o.rsv_id
WHERE o.rsv_id IS NOT NULL
  AND r.id IS NULL;
```

```sql
SELECT r.id, r.rsv_no, r.service, r.status
FROM reservations r
LEFT JOIN orders o ON o.rsv_id = r.id
WHERE o.id IS NULL;
```

### 8.6 Invoice and Payment Links

```sql
SELECT i.id, i.inv_no, i.rsv_id
FROM invoice_admins i
LEFT JOIN reservations r ON r.id = i.rsv_id
WHERE i.rsv_id IS NOT NULL
  AND r.id IS NULL;
```

```sql
SELECT p.id, p.inv_id, p.status, p.amount, p.payment_date
FROM payment_confirmations p
LEFT JOIN invoice_admins i ON i.id = p.inv_id
WHERE p.inv_id IS NOT NULL
  AND i.id IS NULL;
```

### 8.7 Hotel Price Overlap Candidates

```sql
SELECT p1.id AS price_id, p2.id AS overlapping_price_id,
       p1.hotels_id, p1.rooms_id,
       p1.start_date, p1.end_date,
       p2.start_date AS overlap_start_date, p2.end_date AS overlap_end_date
FROM hotel_prices p1
JOIN hotel_prices p2
  ON p1.id < p2.id
 AND p1.hotels_id = p2.hotels_id
 AND p1.rooms_id = p2.rooms_id
 AND p1.start_date <= p2.end_date
 AND p2.start_date <= p1.end_date
ORDER BY p1.hotels_id, p1.rooms_id, p1.start_date;
```

### 8.8 Expired Active Promos and Packages

```sql
SELECT id, hotels_id, rooms_id, name, stay_period_end, status
FROM hotel_promos
WHERE status = 'Active'
  AND stay_period_end < CURRENT_DATE;
```

```sql
SELECT id, hotels_id, rooms_id, name, stay_period_end, status
FROM hotel_packages
WHERE status = 'Active'
  AND stay_period_end < CURRENT_DATE;
```

## 9. Test Matrix For Future Changes

Accommodation tests should cover:

- Catalog only shows available active accommodation records.
- Detail page loads rooms, packages, promos, and price check inputs correctly.
- Normal booking calculates correct price and stores consistent order fields.
- Package booking calculates correct price and stores package snapshot fields.
- Promo booking calculates correct price and stores promotion snapshot fields.
- Invalid date range is rejected.
- Guest count beyond capacity is rejected or quoted correctly.
- Room quantity affects total price correctly.
- Extra bed, optional rate, and airport shuttle affect total price correctly.
- Duplicate submit does not create duplicate order.
- Unauthenticated users cannot create orders.
- Users cannot view or edit other users' orders.
- Admin-only reservation routes reject unauthorized users.
- Invoice generation remains linked to the correct reservation.
- Payment confirmation cannot be attached to another user's invoice.
- Canceled, rejected, invalid, paid, and historical orders appear in the correct list.

Before adding tests that touch database state, confirm the test database is isolated and no production data is reachable from test commands.

## 10. Recommended Next Task

Recommended next task after `ACC-SEC-002`:

`ACC-FILE-001 - Audit and plan guarded Accommodation receipt and invoice file delivery`

Reason:

- `ACC-SEC-002` guards route-ID ownership, Accommodation service scope, customer payment update eligibility, upload validation, safe receipt filename, and admin backend payment mutation authorization.
- Receipt and invoice files are still displayed through public `/storage/...` asset paths when filenames are known.
- File delivery redesign can affect existing customer views, admin views, mail attachments, historical receipt links, and invoice preview/download behavior, so it should be audited and planned separately before implementation.
- The task supports Accommodation security only and must not introduce dependencies on Transport Management, SPK, Wedding, DOKU, or Private Villa.

Expected output:

- Inventory of every Accommodation receipt and invoice file link, preview, download, mail attachment, and storage write/read path.
- Decision on private disk, signed route, authorized controller route, or transitional compatibility path.
- Backward-compatible migration plan for existing stored public filenames.
- Test plan for guest, owner, non-owner, assigned admin, unassigned admin, and leaked filename scenarios.

Dependencies and risks:

- Confirm whether financial files may remain publicly accessible by direct URL during a transition period.
- Confirm whether mail attachments should use private storage paths or generated temporary files.
- Preserve existing invoice preview/download routes and admin receipt preview behavior until replacement is tested.
- Do not change schema, migration, payment accounting, DOKU, Wedding, Transport Management, or SPK inside this audit task.

## 11. Completion Criteria For This Roadmap

This roadmap is complete when:

- The current service flow is mapped at a high level.
- Accommodation has a detailed route, controller, model, view, pricing, status, and risk map.
- Gaps are prioritized.
- Future work is split into safe phases.
- The first recommended task is non-destructive and auditable.
- `docs/README.md` links to this roadmap.
