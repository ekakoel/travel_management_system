---
title: Accommodation Authorization and IDOR Audit
status: active
updated_at: 2026-07-27
applies_to:
  - accommodation
  - orders
  - reservations
  - invoices
  - payment-confirmations
---

# Accommodation Authorization and IDOR Audit

Status: Active  
Updated: 2026-07-27  
Scope: Read-only audit for public Accommodation service only: `Hotel`, `Hotel Promo`, and `Hotel Package`.

Out of scope:

- Transports.
- Tour Packages.
- Activities.
- Transport Management.
- SPK and SPK Destinations.
- Wedding and Wedding Package.
- DOKU.
- Private Villa.

This document records current implementation behavior only. It does not change code, route, middleware, database schema, data, payment logic, file storage, or production configuration.

## 1. Executive Summary

Accommodation customer order detail routes mostly use `orders.sales_agent = Auth::id()` as the ownership guard, but several adjacent payment routes do not. The highest-risk area is payment confirmation: customer upload and update actions load the order by route ID with `Orders::findOrFail($id)` and do not verify that the order belongs to the authenticated user or that the order is an Accommodation order.

Main risks found:

- P0: customer payment upload can attach a receipt to another user's approved order if the attacker knows the order ID.
- P0: customer payment upload can trigger auto-cancel side effects against another user's approved order before ownership is checked.
- P0: customer payment update can replace/reset another order's receipt through an unowned order ID.
- P0/P1: admin receipt validation is protected by route role middleware, but backend does not enforce the Blade `handled_by` ownership guard.
- P1: receipt and invoice files are exposed through public `/storage/...` asset URLs when filenames are known or leaked.
- P1: hotel detail, edit, submit, invoice, and payment routes do not consistently verify the service is `Hotel`, `Hotel Promo`, or `Hotel Package`.
- P1: payment file uploads do not use active FormRequest validation, MIME rules, max-size rules, extension rules, safe file names, or transaction boundaries.
- P2: `edit_order_hotel()` dereferences `$order` before the missing/unauthorized order check.

Read-only data checks did not find orphan Accommodation invoices or paid Accommodation orders with positive balance. One pending Accommodation order currently has no reservation link.

## 2. Documents Reviewed

- `AGENTS.md`
- `docs/README.md`
- `docs/coding-standards.md`
- `docs/decisions/form-submit-standard.md`
- `docs/testing.md`
- `docs/decisions/service-booking-flow-audit-roadmap.md`
- `docs/decisions/accommodation-status-lifecycle-audit.md`
- `docs/decisions/shared-order-status-audit.md`
- `docs/decisions/accommodation-status-contract.md`
- `docs/decisions/shared-order-status-compatibility-plan.md`

Rules applied:

- Analyze project rules and docs before changing anything.
- Treat database as active data.
- Do not make destructive schema or data changes.
- Verify route, middleware, controller, model, Blade, payment, and status behavior from actual implementation.
- Do not assume roadmap items are implemented without code evidence.

## 3. Routes Audited

### 3.1 Customer Accommodation and Shared Order Routes

All routes below are inside authenticated frontend middleware: `auth`, `profile.complete`, and `approve`.

| Route | Method | Controller | Scope Finding |
| --- | --- | --- | --- |
| `/orders` | GET | `OrderController@index` | Owner-filtered order lists; Accommodation list filters `Hotel`, `Hotel Promo`, and `Hotel Package`. |
| `/orders/history` | GET | `OrderController@order_history` | Owner-filtered history; service filter is shared and optional. |
| `/order-{id}` | GET | `OrderController@detail_order` | Owner-filtered shared detail route. |
| `/detail-order-hotel/{id}` | GET | `OrderController@detail_order_hotel` | Owner-filtered, but no Accommodation service guard. |
| `/edit-order-hotel/{id}` | GET | `OrderController@edit_order_hotel` | Owner-filtered, but no Accommodation service guard and null dereference occurs before missing-order handling. |
| `/fsubmit-order-hotel/{id}` | PUT | `OrderController@func_submit_order_hotel` | Owner-filtered and Draft/Invalid filtered, but no Accommodation service guard. |
| `/orders/{id}/invoice/preview` | GET | `OrderController@preview_order_invoice` | Owner-filtered and requires Approved; no Accommodation service guard because route is shared. |
| `/orders/{id}/invoice/download` | GET | `OrderController@download_order_invoice` | Owner-filtered and requires Approved; no Accommodation service guard because route is shared. |
| `/fpayment-confirmation-{id}` | POST | `PaymentConfirmationController@payment_confirmation` | No owner guard and no Accommodation service guard. |
| `/fupdate-payment-confirmation/{id}` | PUT | `PaymentConfirmationController@update_payment_confirmation` | No owner guard, no Accommodation service guard, no payment status guard. |

### 3.2 Admin and Reservation Routes

Routes below are inside `checkPosition:developer,reservation,weddingRsv`.

| Route | Method | Controller | Scope Finding |
| --- | --- | --- | --- |
| `/orders-admin` | GET | `OrdersAdminController@index` | Role middleware only; shared order admin list. |
| `/orders-admin-{id}` | GET | `OrdersAdminController@view_order_admin_detail` | Role middleware only; creates reservation on GET when missing. |
| `/factivate-order/{id}` | PUT | `OrdersAdminController@func_activate_order` | Role middleware only; no Accommodation service guard, status guard, or backend assignment guard. |
| `/fgenerate-invoice-{id}` | PUT | `OrdersAdminController@fgenerate_invoice` | Role middleware and Approved status guard; no Accommodation service guard or assignment guard. |
| `/ffinalization-order-{id}` | PUT | `OrdersAdminController@func_finalization_order` | Has backend `handled_by == Auth::id()` guard, but weak payment/invoice status verification. |
| `/fpayment-confirmation-{id}` | POST | `OrdersAdminController@fconfirmation_payment` | Role middleware only; no backend `handled_by` guard and receipt/status/amount values are request-driven. |
| `/fadmin-add-payment-confirmation-{id}` | POST | `OrdersAdminController@admin_add_payment_confirmation` | Role middleware only; no backend assignment guard and no file validation. |
| `/fupdate-order-invalid/{id}` | PUT | `OrdersAdminController@func_update_order_invalid` | Role middleware only; trusts request `author` for log user ID. |
| `/fupdate-order-rejected/{id}` | PUT | `OrdersAdminController@func_update_order_rejected` | Role middleware only; trusts request `author` for log user ID. |
| `/farchive-order/{id}` | PUT | `OrdersAdminController@func_archive_order` | Role middleware only; trusts request `author` for log user ID and writes `Archive`. |

## 4. Actors and Role Gates

| Actor | Current Gate | Accommodation Access Behavior |
| --- | --- | --- |
| Guest | `auth` middleware | Blocked from customer order, invoice, and payment routes. |
| Authenticated approved customer or agent | `auth`, `profile.complete`, `approve` | Can access frontend order routes; detail/history usually use `orders.sales_agent = Auth::id()`. |
| Another customer or agent | Same frontend middleware | Blocked on detail/history where `sales_agent` guard exists; not blocked on payment upload/update routes. |
| `developer` position | `checkPosition:developer,reservation,weddingRsv` | Can access shared admin order/payment routes. |
| `reservation` position | `checkPosition:developer,reservation,weddingRsv` | Can access shared admin order/payment routes. |
| `weddingRsv` position | `checkPosition:developer,reservation,weddingRsv` | Included in the same shared admin group, even though Wedding is out of scope for this audit. |
| `admin` type | `adminType` middleware | Not the primary gate on the standard order admin routes audited here. |

## 5. Ownership Model

Current ownership and linkage model inferred from code:

| Object | Ownership / Link Source | Current Risk |
| --- | --- | --- |
| `orders` | Customer-facing ownership uses `orders.sales_agent = Auth::id()`. | Payment controller bypasses this guard by using route ID only. |
| `reservations` | Linked from `orders.rsv_id` to `reservations.id`. | Admin detail can create missing reservation on GET. |
| `invoice_admins` | Linked from `reservations.id` to `invoice_admins.rsv_id`. | File access can bypass controller if public asset path is known. |
| `payment_confirmations` | Linked from `payment_confirmations.inv_id` to `invoice_admins.id`. | Customer update chooses receipt by unguarded order/invoice; admin validation chooses receipt by route ID. |
| Receipt file | Stored in `storage/receipt/` and displayed with `asset('storage/receipt/...')`. | Direct public URL access if filename is known or leaked. |
| Invoice PDF | Stored under public `storage/document/...` and returned by controller or linked as asset. | Controller has owner guard; direct public URL does not. |

Recommended ownership rule for future fixes:

1. Frontend customer routes must first resolve an order by `id`, `sales_agent = Auth::id()`, and Accommodation service set.
2. Reservation, invoice, payment confirmation, guests, optional rates, and files must be derived only from that resolved order.
3. Backend admin mutation routes must enforce role plus assignment/status/service policy in controller or policy, not only in Blade.

## 6. Order Detail Findings

`OrderController@detail_order_hotel`:

- Uses `Orders::with([...])->where('sales_agent', Auth::id())->where('checkin', '>', now())->where('id', $id)->first()`.
- Blocks missing order and statuses `Draft`, `Invalid`, and `Rejected`.
- Loads reservation and invoice after the owner-guarded order is found.
- Does not verify `orders.service` is `Hotel`, `Hotel Promo`, or `Hotel Package`.
- Allows future orders with other statuses not explicitly blocked, including statuses such as `Canceled`, `Deleted`, or `Paid` if the date condition matches.

`OrderController@edit_order_hotel`:

- Uses `sales_agent = Auth::id()` and future check-in guard.
- Does not verify Accommodation service.
- Calls `$agent = User::find($order->sales_agent)` before checking `if (!$order)`, so unauthorized, missing, or past orders can produce a server error instead of a clean redirect.

`OrderController@func_submit_order_hotel`:

- Uses `sales_agent = Auth::id()` and restricts status to `Draft` or `Invalid`.
- Does not verify Accommodation service.
- Can submit an owned non-Accommodation Draft/Invalid order through a hotel route if such an order matches the ID and status.

Access matrix:

| Scenario | Current Result |
| --- | --- |
| Guest opens hotel detail | Blocked by `auth`. |
| Owner opens own future valid hotel order | Allowed if status is not Draft, Invalid, or Rejected. |
| Owner opens own past hotel order detail | Blocked by `checkin > now`; history route is separate. |
| User opens another user's hotel detail | Blocked by `sales_agent` filter. |
| User opens another user's hotel edit | Query returns null, but current code can dereference null before handling. |
| Owner opens non-Accommodation order via hotel detail | Not blocked by service guard. |
| Admin opens order detail | Allowed by position role; no Accommodation service filter. |

## 7. Invoice Findings

`OrderController@preview_order_invoice` and `download_order_invoice`:

- Resolve order through `findFrontendOrderForInvoice((int) $id)`.
- `findFrontendOrderForInvoice()` uses `Orders::with('reservations.invoice.payment')->where('sales_agent', Auth::id())->where('id', $id)->first()`.
- Requires status `Approved`.
- Resolves invoice from the order reservation relation.
- Returns the invoice file through `response()->file()` or `response()->download()`.
- Does not verify Accommodation service because the route is shared.

Security result:

- Frontend invoice controller access is owner-guarded.
- The generated PDF path is also built under public storage, and admin/sidebar views use public `asset(...)` links. Direct file access is therefore not protected by the controller when the filename is known.

Admin invoice generation:

- `OrdersAdminController@fgenerate_invoice` has route role middleware and requires order status `Approved`.
- It does not enforce Accommodation service, assignment ownership, or per-order authorization beyond role access.

## 8. Payment Upload Findings

`PaymentConfirmationController@payment_confirmation`:

- Loads order with `Orders::findOrFail($id)`.
- Does not check `orders.sales_agent = Auth::id()`.
- Does not check service is `Hotel`, `Hotel Promo`, or `Hotel Package`.
- Looks up reservation through `$order->rsv_id` and invoice through `$reservation->id`.
- Runs auto-cancel logic before any ownership or service authorization.
- Requires order status `Approved` and an invoice before proceeding, but that status guard does not replace ownership.
- Uploads file from `receipt_name` to `storage/receipt/`.
- Stores a filename containing invoice number, timestamp, and original filename.
- Does not call a FormRequest or `$request->validate()`.
- Does not enforce MIME type, extension whitelist, file size, image dimensions, or safe filename generation.
- Does not use a database transaction around receipt creation and file movement.

The `StorePaymentConfirmationRequest` class exists, but currently has `authorize()` returning `false`, empty rules, and is not used by this controller action.

## 9. Payment Update Findings

`PaymentConfirmationController@update_payment_confirmation`:

- Loads order with `Orders::findOrFail($id)`.
- Does not check `orders.sales_agent = Auth::id()`.
- Does not check service is Accommodation.
- Does not restrict updates to a specific payment confirmation status such as `Pending` or `Invalid`.
- For non-Wedding orders, selects the first receipt by invoice ID, not by a receipt ID tied to the authenticated user's resolved order.
- Deletes the previous receipt file and replaces it if a new file is uploaded.
- Sets receipt status back to `Pending`.
- References `$note`, but `$note` is not defined in the method.
- Does not call a FormRequest or `$request->validate()`.

The `UpdatePaymentConfirmationRequest` class exists, but currently has `authorize()` returning `false`, empty rules, and is not used by this controller action.

## 10. Payment File Findings

Receipt files:

- Frontend modern order modal displays receipts through `asset('storage/receipt/' . $receipt->receipt_img)`.
- Admin order detail and admin sidebar also display receipt files through `/storage/receipt/...`.
- These URLs are public asset paths, not authorization-checked download routes.
- Stored names include invoice number, timestamp, and the original uploaded filename.

Invoice files:

- Frontend invoice preview/download controller routes have owner guards.
- The file itself is under public storage path convention and may also be linked via public asset paths in admin-facing views.

Risk:

- If a receipt or invoice filename is known, leaked, logged, copied from HTML, or shared, direct access does not re-check authentication, ownership, role, order status, or service type.

## 11. Admin and Reservation Findings

`OrdersAdminController@view_order_admin_detail`:

- Protected by `checkPosition:developer,reservation,weddingRsv`.
- Loads any order by ID.
- No Accommodation service guard because the route is shared.
- Calls `getOrCreateReservationForOrder()` and can create a reservation as a side effect of a GET/admin detail view.

`OrdersAdminController@fconfirmation_payment`:

- Protected by role middleware only.
- Loads `PaymentConfirmation` by route receipt ID.
- Derives invoice, reservation, and order from the receipt.
- Does not enforce the Blade-level `order->handled_by == $admin->id` guard in the backend.
- Accepts payment status, amount, currency rate, payment date, and note from request.
- Does not validate allowed receipt statuses in the method.
- Updates payment and invoice balance without an explicit transaction.

`OrdersAdminController@admin_add_payment_confirmation`:

- Protected by role middleware only.
- Loads order by route ID.
- Does not enforce backend `handled_by` guard.
- Does not validate uploaded receipt file.

Status action routes:

- Invalid, rejected, archive, activation, invoice, and finalization actions are shared admin actions.
- Some routes trust request `author` for log user ID instead of deriving the actor from `Auth::id()`.
- Several actions do not verify service type or current status before mutation.

## 12. Blade vs Backend Guards

Frontend:

- Blade controls whether payment buttons and receipt modals are shown.
- Hidden form `order_id` is present in payment upload UI, but customer upload controller ignores it and relies on route ID.
- UI visibility is not the security boundary; backend route ID authorization is missing in payment upload/update.

Admin:

- Admin Blade calculates ownership-like variables such as `$isOwner` or checks `order->handled_by == $admin->id` in some partials.
- Backend payment validation does not enforce the same assignment guard.
- A direct POST to the admin receipt validation route can bypass UI-only restrictions if the actor already has the role middleware.

## 13. Service-Type Confusion

Accommodation routes use names such as `detail-order-hotel`, `edit-order-hotel`, and `fsubmit-order-hotel`, but their backend queries do not consistently restrict service to:

- `Hotel`
- `Hotel Promo`
- `Hotel Package`

Current effects:

- Owner guards prevent viewing another user's order on detail/edit/submit paths.
- Owned non-Accommodation orders can reach hotel handlers if the ID and status/date filters match.
- Payment confirmation routes are worse because they have no owner guard and no service guard.
- Invoice preview/download routes are owner-guarded but shared across services.

## 14. Status-Based Authorization

| Flow | Current Status Guard | Gap |
| --- | --- | --- |
| Hotel detail | Blocks Draft, Invalid, Rejected; requires future check-in. | Does not block Canceled or Deleted explicitly; no service guard. |
| Hotel edit | Requires future check-in. | No Draft-only/Invalid-only guard visible in the initial query; no service guard; null dereference risk. |
| Hotel submit | Requires Draft or Invalid. | No service guard. |
| Invoice preview/download | Requires Approved. | No service guard; public file path risk remains. |
| Customer payment upload | Requires Approved and invoice exists. | No owner/service guard; auto-cancel before auth. |
| Customer payment update | No clear status guard. | Can reset receipt to Pending; no owner/service guard. |
| Admin activate | No strong previous-status guard found. | Shared mutation route can affect any service in the admin group. |
| Admin receipt validation | Request-driven receipt status. | No backend assignment guard or status whitelist in method. |
| Admin finalization | Requires `handled_by == Auth::id()`. | Weak payment/invoice validation before marking Paid. |

## 15. Mass Assignment and Request Trust

| Input | Current Use | Risk |
| --- | --- | --- |
| Route `{id}` in customer payment upload/update | Used to load `Orders::findOrFail($id)`. | High: IDOR because owner is not checked. |
| Hidden `order_id` in frontend payment form | Present in Blade but ignored by upload method. | Low by itself; route ID is the real issue. |
| Uploaded receipt original filename | Included in stored filename. | High: unsafe naming, leakage, overwrite/path edge cases depending uploaded name handling. |
| Admin receipt `status` | Written to `payment_confirmations.status`. | High: no whitelist in method. |
| Admin receipt `amount` and `kurs_id` | Used for invoice balance calculation. | High: request-tampering can alter payment accounting if route is accessible. |
| Admin status action `author` | Used in order logs by some status methods. | Medium: actor should be derived from authenticated user, not request body. |
| Receipt `note` | Rendered with unescaped Blade in receipt modals. | Medium/High: XSS risk if note can contain unsafe HTML from untrusted or semi-trusted actor. |

## 16. Current Data Findings

Read-only data checks were executed through Laravel bootstrap against the current configured database. No write, delete, migration, schema, or configuration command was executed.

Accommodation order owner/status inventory:

| Service | Status | Total | Missing `user_id` | Missing `sales_agent` |
| --- | --- | ---: | ---: | ---: |
| Hotel Promo | Pending | 2 | 0 | 0 |
| Hotel Promo | Approved | 1 | 0 | 0 |

Other checks:

| Check | Result |
| --- | --- |
| Duplicate Accommodation order numbers | None found. |
| Pending Accommodation orders without reservation | 1 found: order `HPP260720-3`, ID `4`, service `Hotel Promo`, status `Pending`, `sales_agent = 24`, `user_id = 24`. |
| Approved Accommodation orders without invoice | None found. |
| Paid Accommodation orders with positive invoice balance | None found. |
| Paid Accommodation orders without valid payment confirmation | None found. |
| Orphan payment confirmation invoice links | None found. |
| Empty receipt path in payment confirmations | None found. |
| Unknown Accommodation service/status values | None found. |

Data note:

- The current sample is small and does not prove the flow is safe.
- The one pending order without reservation is consistent with the status lifecycle audit concern that Pending/Reservation linkage is not fully normalized.

## 17. Read-Only Queries Used

```sql
SELECT service, status, COUNT(*) AS total,
       SUM(user_id IS NULL) AS missing_user_id,
       SUM(sales_agent IS NULL) AS missing_sales_agent
FROM orders
WHERE service IN ('Hotel', 'Hotel Promo', 'Hotel Package')
GROUP BY service, status
ORDER BY service, status;
```

```sql
SELECT orderno, COUNT(*) AS total
FROM orders
WHERE service IN ('Hotel', 'Hotel Promo', 'Hotel Package')
GROUP BY orderno
HAVING COUNT(*) > 1;
```

```sql
SELECT id, orderno, service, status, sales_agent, user_id
FROM orders
WHERE service IN ('Hotel', 'Hotel Promo', 'Hotel Package')
  AND status = 'Pending'
  AND rsv_id IS NULL;
```

```sql
SELECT o.id, o.orderno, o.service, o.status
FROM orders o
LEFT JOIN reservations r ON r.id = o.rsv_id
LEFT JOIN invoice_admins i ON i.rsv_id = r.id
WHERE o.service IN ('Hotel', 'Hotel Promo', 'Hotel Package')
  AND o.status = 'Approved'
  AND i.id IS NULL;
```

```sql
SELECT o.id, o.orderno, o.status, i.inv_no, i.balance
FROM orders o
JOIN reservations r ON r.id = o.rsv_id
JOIN invoice_admins i ON i.rsv_id = r.id
WHERE o.service IN ('Hotel', 'Hotel Promo', 'Hotel Package')
  AND o.status = 'Paid'
  AND COALESCE(i.balance, 0) > 0;
```

```sql
SELECT p.id, p.inv_id, p.status, p.amount
FROM payment_confirmations p
LEFT JOIN invoice_admins i ON i.id = p.inv_id
WHERE p.inv_id IS NOT NULL
  AND i.id IS NULL;
```

## 18. Authorization Matrix

| Surface | Guest | Owner | Other Customer | Reservation/Developer | Current Verdict |
| --- | --- | --- | --- | --- | --- |
| Current orders | Blocked | Allowed | Blocked by owner filter | Admin surface separate | Mostly OK for Accommodation list. |
| History | Blocked | Allowed | Blocked by owner filter | Admin surface separate | Mostly OK, but shared service filter. |
| Hotel detail | Blocked | Allowed by owner/date/status | Blocked by owner filter | Admin detail separate | Missing service guard. |
| Hotel edit | Blocked | Allowed by owner/date | Query blocks other user but can 500 | Admin detail separate | Missing service guard and null check order. |
| Hotel submit | Blocked | Allowed for Draft/Invalid | Blocked by owner filter | Admin status routes separate | Missing service guard. |
| Invoice preview/download | Blocked | Allowed for Approved | Blocked by owner filter | Admin links separate | Controller OK for owner; public file path risk. |
| Payment upload | Blocked | Allowed if Approved/invoice | Not blocked by owner guard | Admin upload separate | P0 IDOR. |
| Payment update | Blocked | Allowed by route ID | Not blocked by owner guard | Admin validation separate | P0 IDOR. |
| Admin receipt validation | Blocked | Not customer route | Not customer route | Allowed by role | Backend missing assignment guard. |
| Receipt direct URL | Public if filename known | Public if filename known | Public if filename known | Public if filename known | P1 file access risk. |

## 19. IDOR Test Matrix for Future Fixes

Tests should be added only after test database isolation is confirmed.

| Test | Expected Future Result |
| --- | --- |
| Customer A opens Customer B hotel detail ID | 404 or 403; no data rendered. |
| Customer A opens Customer B invoice preview/download ID | 404 or 403; no file returned. |
| Customer A posts payment receipt to Customer B approved Accommodation order ID | 404 or 403; no file stored; no payment row created; no auto-cancel side effect. |
| Customer A updates Customer B payment confirmation through order ID | 404 or 403; no file deleted or replaced. |
| Customer A posts payment to owned non-Accommodation order through Accommodation route | 404 or 422; no receipt row created. |
| Customer A submits owned non-Accommodation Draft order via hotel submit route | 404 or 422; no status change. |
| Reservation user validates receipt for order not assigned to them | 403 unless role policy explicitly permits cross-assignment validation. |
| Direct receipt URL access without auth | Blocked by private/signed download route if file storage is changed. |
| Uploaded `.php`, SVG with script, oversized image, or invalid MIME receipt | Validation error; no file moved. |
| Valid payment receipt upload | Receipt is stored with generated safe filename and linked to invoice derived from owned Accommodation order. |

## 20. P0 Findings

### ACC-SEC-P0-001 - Customer Payment Upload IDOR

Evidence:

- `PaymentConfirmationController@payment_confirmation` uses `Orders::findOrFail($id)`.
- It does not filter by `sales_agent = Auth::id()`.
- It does not restrict service to `Hotel`, `Hotel Promo`, or `Hotel Package`.

Impact:

- An authenticated approved user can attempt to upload a payment confirmation for another user's approved order if the order ID is known.
- The receipt links to the victim order's invoice because reservation and invoice are derived from the unowned order.

Recommended first fix:

- Resolve the order with an owned Accommodation query before any side effect:
  `where('id', $id)->where('sales_agent', Auth::id())->whereIn('service', [...])`.

### ACC-SEC-P0-002 - Auto-Cancel Side Effect Before Authorization

Evidence:

- The customer payment upload action loads any order by ID and can run auto-cancel logic before verifying ownership or Accommodation service.

Impact:

- An authenticated user could trigger cancellation logic against another user's approved order if the route ID is known and the invoice is expired/unpaid.

Recommended first fix:

- Move authorization and service resolution before any status mutation or auto-cancel check.

### ACC-SEC-P0-003 - Customer Payment Update IDOR

Evidence:

- `PaymentConfirmationController@update_payment_confirmation` uses `Orders::findOrFail($id)` without owner/service guard.
- It then derives invoice and selects the first payment confirmation for that invoice.

Impact:

- A user can attempt to replace or reset another order's receipt if the order ID is known.
- The method can delete the previous receipt file and reset receipt status to `Pending`.

Recommended first fix:

- Resolve owned Accommodation order first, derive invoice second, and resolve the editable receipt through that invoice with an allowed status check.

### ACC-SEC-P0-004 - Admin Receipt Validation Missing Backend Assignment Guard

Evidence:

- Admin Blade applies `handled_by` style visibility in some receipt/report UI.
- `OrdersAdminController@fconfirmation_payment` validates and mutates a receipt by route receipt ID without enforcing the same assignment guard in backend.

Impact:

- Any actor passing `checkPosition:developer,reservation,weddingRsv` may be able to validate or alter payment confirmation for orders they do not handle, unless the business intentionally allows that.

Recommended first fix:

- Add backend policy/guard for receipt validation based on role, assignment, current order status, and allowed payment status transition.

## 21. P1 Findings

### ACC-SEC-P1-001 - Public Receipt and Invoice File URLs

Receipt and invoice files are displayed through public `/storage/...` asset paths. If a filename is known or leaked, the file can be accessed without re-checking auth, owner, role, status, or service.

### ACC-SEC-P1-002 - Missing File Validation

Customer upload, customer update, and admin upload actions do not use active validation rules for receipt file type, size, extension, or safe filename generation.

### ACC-SEC-P1-003 - Missing Accommodation Service Guards

Hotel detail, edit, submit, invoice, and payment paths do not consistently enforce `Hotel`, `Hotel Promo`, or `Hotel Package` in the backend. Owner checks reduce exposure for detail/edit/submit, but payment routes remain P0.

### ACC-SEC-P1-004 - Admin Mutation Routes Are Broad

Admin activation, invoice, invalid, rejected, archive, and receipt validation routes are shared routes with broad role middleware. Several methods lack per-order service/status/assignment policy checks.

### ACC-SEC-P1-005 - Request-Driven Payment Accounting

Admin receipt validation accepts status, amount, currency, and date from request input. Without explicit validation and authorization, route access can affect invoice balances and order paid state.

### ACC-SEC-P1-006 - Unescaped Receipt Notes

Receipt note rendering uses unescaped Blade output in receipt modals. If notes can include unsafe HTML from an untrusted or semi-trusted actor, this becomes an XSS path.

## 22. P2 Findings

- `edit_order_hotel()` should handle missing/unauthorized order before dereferencing `$order->sales_agent`.
- Admin detail GET creates reservation records as a side effect; this is risky for auditability even though it is not a direct IDOR.
- Frontend payment button visibility is not aligned exactly with backend Approved-only upload behavior.
- `InvoiceAdmin` relation naming and foreign-key usage should be reviewed in a separate relationship audit because code often resolves invoice manually through `rsv_id`.

## 23. Recommended Fix Sequence

1. Create a frontend resolver for owned Accommodation orders and use it before every customer-facing order, invoice, and payment side effect.
2. Fix customer payment upload so ownership/service/status/invoice authorization runs before auto-cancel, file movement, or database writes.
3. Fix customer payment update so the editable receipt is derived from the owned Accommodation order invoice and is limited by explicit receipt status rules.
4. Add active FormRequest validation or equivalent validator for receipt uploads, including MIME, max size, extension, safe generated filename, and image validation.
5. Move receipt and invoice delivery behind authorized controller routes, private disk, signed URLs, or equivalent access control.
6. Add backend admin policies for receipt validation, admin upload, activation, invoice generation, invalid/rejected/archive, and finalization.
7. Add transaction boundaries around multi-step file, payment, invoice, order, reservation, and log mutations.
8. Add isolated tests for the IDOR matrix before and after fixes.

## 24. Required Business Decisions Before Implementation

- Should customers be allowed to update receipts after status `Valid`, or only while `Pending`/`Invalid`?
- Should direct receipt and invoice URLs ever be public, or must all financial files require an authenticated controller route?
- Should `reservation` users validate only orders assigned to them through `handled_by`, or can they validate all standard orders?
- Should `developer` bypass assignment guard for operational repair?
- Should `weddingRsv` remain inside the shared standard order admin route group for Accommodation orders?
- Should unauthorized access return 404 to avoid order ID enumeration, or 403 for explicit denial?
- Should hotel routes reject non-Accommodation services even if older shared routing maps another service to hotel detail?

## 25. Files Inspected

- `routes/web.php`
- `app/Http/Controllers/OrderController.php`
- `app/Http/Controllers/OrdersAdminController.php`
- `app/Http/Controllers/PaymentConfirmationController.php`
- `app/Http/Requests/StorePaymentConfirmationRequest.php`
- `app/Http/Requests/UpdatePaymentConfirmationRequest.php`
- `app/Http/Middleware/CheckPosition.php`
- `app/Http/Middleware/ApproveUser.php`
- `app/Http/Middleware/CheckProfileCompleteness.php`
- `app/Http/Middleware/AdminType.php`
- `app/Http/Kernel.php`
- `app/Models/Orders.php`
- `app/Models/Reservation.php`
- `app/Models/InvoiceAdmin.php`
- `app/Models/PaymentConfirmation.php`
- `app/Models/Transactions.php`
- `resources/views/frontend/home/orders/details/partials/hotel-detail-modern-modals.blade.php`
- `resources/views/frontend/home/orders/details/partials/hotel-detail-modern-sidebar.blade.php`
- `resources/views/admin/ordersadmindetail.blade.php`
- `resources/views/partials/admin-order-receipt-report-sidebar.blade.php`

## 26. Acceptance Criteria for Future Fix Task

Future implementation should be accepted only when all criteria below are met:

- Customer payment upload cannot create a receipt for another user's order.
- Customer payment update cannot delete, replace, or reset another user's receipt.
- Auto-cancel cannot run against an order before ownership/service authorization succeeds.
- Customer payment routes reject non-Accommodation orders when the route is Accommodation-specific.
- Hotel detail, edit, submit, and payment routes use one consistent Accommodation service set.
- Invoice preview/download remains owner-protected and does not expose financial files through unauthenticated public paths.
- Receipt viewing/download uses an authorization-checked path or a signed/private equivalent.
- Uploaded receipt files are validated for allowed type and size and stored under generated safe names.
- Admin receipt validation enforces backend role plus assignment/status policy, not only Blade visibility.
- Admin request values for status, amount, currency, and date are validated server-side.
- Multi-table payment/invoice/order mutations run in transactions where appropriate.
- Automated or manual IDOR tests cover owner, non-owner, guest, non-Accommodation, admin assigned, and admin unassigned scenarios.

## 27. Recommended Next Task

Recommended next task:

`ACC-SEC-002 - Implement owned Accommodation order and payment authorization guards`

Reason:

- It directly addresses P0 IDOR findings before broader payment, invoice, or status normalization work.
- It has a narrow Accommodation scope and can be implemented without database schema changes.
- It reduces the chance that future payment/status fixes preserve unsafe route-ID behavior.

Dependencies:

- Confirm the Accommodation service set remains exactly `Hotel`, `Hotel Promo`, and `Hotel Package`.
- Decide whether unauthorized owned-scope failures should return 404 or 403.
- Decide customer receipt update rules for `Pending`, `Invalid`, and `Valid`.
- Confirm whether existing direct receipt/invoice public URLs must keep backward compatibility during migration to guarded file delivery.

Risk:

- Routes are shared and some non-Accommodation flows currently reuse hotel detail behavior; service guards must be scoped carefully to avoid breaking non-Accommodation routes outside this task.
- Existing stored public file URLs may remain accessible until storage delivery is changed.
- Payment update behavior may have legacy expectations that must be confirmed before disabling updates for certain receipt statuses.

## 28. ACC-SEC-002 Implementation Result

Status: Implemented 2026-07-27

Implemented safeguards:

- `Orders::ACCOMMODATION_SERVICES` now centralizes the active Accommodation service set: `Hotel`, `Hotel Promo`, and `Hotel Package`.
- `Orders::scopeAccommodationService()` and `Orders::scopeOwnedBy()` now centralize the customer Accommodation ownership query.
- Customer payment upload resolves the order through authenticated owner plus Accommodation service before invoice lookup, auto-cancel, file movement, payment creation, mail, or order log.
- Customer payment update resolves the order through authenticated owner plus Accommodation service and only updates the latest `Pending` receipt attached to the order invoice.
- Customer payment upload and update now use active FormRequest validation for `jpg`, `jpeg`, `png`, and `pdf`, with max size `5120 KB`.
- Receipt filenames are generated server-side and no longer use the original uploaded filename as the final path.
- Receipt creation/update and order-log writes use database transactions, with new-file cleanup on database failure.
- Hotel detail, edit, and submit handlers now apply the Accommodation service scope.
- `edit_order_hotel()` now checks missing/unauthorized orders before reading `$order->sales_agent`.
- Admin standard receipt upload now validates receipt file, generates a safe filename, enforces backend payment mutation authorization, and writes inside a transaction.
- Admin standard payment validation now validates request status, amount, currency, payment date, and note; verifies receipt -> invoice -> reservation -> order relation; enforces backend role/assignment guard; strips payment note HTML; and writes receipt/invoice/order log updates inside a transaction.
- Frontend receipt note output is escaped with `{{ $receipt->note }}`.

Authorization decisions applied:

- Customer-owned order lookup uses `orders.sales_agent = Auth::id()`.
- Customer unauthorized owned-scope misses use Eloquent `findOrFail()` semantics through the owned Accommodation query, producing a not-found response instead of exposing whether another user's order exists.
- Customer payment upload remains eligible only for order status `Approved` with an outstanding invoice balance.
- Customer payment update is allowed only for receipt status `Pending`.
- Admin payment mutation uses existing allowed positions `developer`, `reservation`, and `weddingRsv`. `developer` may bypass assignment for operational repair; other allowed positions must either be handling an unassigned order or match `orders.handled_by`.

Tests added:

- `tests/Feature/AccommodationPaymentAuthorizationTest.php`

Test coverage added:

- Customer payment controller resolves an owned Accommodation order before auto-cancel.
- Non-owner payment upload does not create payment, does not create order log, and does not auto-cancel an expired order.
- Owned non-Accommodation order is rejected by the Accommodation payment route.
- Owner payment upload still creates a `Pending` receipt with a safe generated filename.
- Customer payment update requires owned Accommodation order and `Pending` receipt.
- Accommodation service and ownership query are centralized in `Orders`.
- Store/update payment FormRequests authorize and validate safe file types.
- Admin payment validation has backend guard and relation checks.
- Receipt note rendering is escaped.

Verification run:

```bash
$env:DB_CONNECTION='sqlite'; $env:DB_DATABASE=':memory:'; php artisan test --filter=AccommodationPaymentAuthorizationTest
```

Result:

- Passed: 9 tests, 48 assertions.

Remaining risks:

- Receipt and invoice file delivery still uses public `/storage/...` asset paths. This task intentionally did not redesign storage delivery.
- The added tests include static/source-level checks plus DB-backed route checks that run only when the test command uses SQLite `:memory:`. Broader database feature coverage should still wait for a project-wide disposable testing database configuration.
- Existing shared admin payment logic for non-Accommodation standard services was not redesigned.

Recommended next task:

`ACC-FILE-001 - Audit and plan guarded Accommodation receipt and invoice file delivery`

Reason:

- P0 route-ID and pre-authorization side effects are now guarded, leaving public financial file access as the most important remaining Accommodation security risk.
- Storage delivery redesign can affect existing links, mail attachments, admin previews, customer previews, and historical files, so it should be handled as its own focused task.
