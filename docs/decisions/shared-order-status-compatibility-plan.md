---
title: Shared Order Status Compatibility Plan
status: proposed
updated_at: 2026-07-27
applies_to:
  - accommodation
  - transports
  - tour-packages
  - activities
  - orders
excludes:
  - transport-management
  - spk
  - wedding
  - doku
  - private-villa
---

# Shared Order Status Compatibility Plan

## 1. Purpose

This proposed plan defines shared `orders.status` compatibility for the four public service booking flows:

1. Accommodation.
2. Transports.
3. Tour Packages.
4. Activities.

The purpose is to provide a safe compatibility and data verification plan before any Accommodation status implementation changes. This document is documentation only. It does not change code, database data, migrations, enum values, payment behavior, scheduler behavior, history behavior, or Transport Management.

## 2. Scope

In scope:

- Shared public order lifecycle for Accommodation, Transports, Tour Packages, and Activities.
- Legacy read compatibility for `orders.status`.
- Manual payment confirmation compatibility.
- Invoice balance derived payment state.
- Reservation review compatibility.
- History compatibility for the four service flows.
- Data verification and test fixture planning.

Out of scope:

- Transport Management.
- SPK and SPK Destinations.
- Driver management.
- Internal vehicle assignment.
- Driver check-in.
- Internal operational transport flow.
- Wedding, Wedding Package, and `order_weddings`.
- DOKU.
- Private Villa.

Development order remains:

```text
Accommodation
-> Transports
-> Tour Packages
-> Activities
```

## 3. Scope Boundary: Transports vs Transport Management

Service Transports is the public/customer-facing booking service and is in scope. Its lifecycle begins at transport listing/detail and continues through public price calculation, date/route selection, order creation, reservation review, approval, invoice/payment, paid state, service date, completion decision, and History Order.

Transport Management is an internal operational domain and is out of scope.

Classification:

```text
Internal Operational Dependency - Out of Scope
```

Transport Management includes SPK, SPK destinations, driver assignment, vehicle operational assignment, driver check-in, destination visited status, driver report, public token SPK, and internal execution monitoring.

Rules:

- SPK is not public order lifecycle.
- SPK status is not shared `orders.status`.
- SPK mismatch is not a blocker for Accommodation.
- SPK mismatch is not a blocker for shared public service order lifecycle.
- Service Transports completion must not be inferred from SPK completion until a separate integration contract is approved.
- A paid Transport order may later hand off to Transport Management, but that is only an internal operational dependency.
- The handoff must not change customer price, corrupt payment state, create duplicate orders, fail the order, or mutate History Order unexpectedly.

## 4. Excluded Modules

| Module | Treatment | Reason |
| --- | --- | --- |
| Transport Management | Out of scope | Internal operation, not public booking lifecycle. |
| SPK / SPK Destinations | Out of scope | Fulfillment execution artifact, not shared order status. |
| Driver / vehicle assignment | Out of scope | Operational dispatch domain. |
| Wedding / Wedding Package / `order_weddings` | Out of scope | Separate lifecycle table/domain. |
| DOKU | Out of scope | Not part of current manual payment compatibility target. |
| Private Villa | Out of scope | Excluded by owner scope correction unless future instruction includes it. |

## 5. Current Shared Schema

Current `orders.status` schema enum from the original migration:

- `Draft`
- `Pending`
- `Confirmed`
- `Approved`
- `Canceled`
- `Rejected`
- `Invalid`
- `Paid`
- `Deleted`

Current compatibility problem:

- Code and views also reference values outside the enum, including `Active`, `Archive`, `Archived`, `Removed`, `Accepted`, lowercase variants, and future/proposed `Completed`.
- New writes must not use values outside the current schema until a migration and data plan are approved.
- Read compatibility may temporarily include legacy aliases, but only with explicit mapping and removal conditions.

## 6. Active Service Inventory

Live read-only inventory from `ACC-STATUS-003` found only `Hotel Promo` and `Transport` rows in `orders`. Missing live rows do not mean the service is unused in code.

| Service Value | Business Service | Code Usage | Database Count | Notes |
| --- | --- | --- | ---: | --- |
| `Hotel` | Accommodation | Accommodation order/detail/status compatibility path in `OrderController` and admin order flow. | 0 | In scope. No live row in audited database. |
| `Hotel Promo` | Accommodation | Hotel promo create/detail/payment/history path. | 3 | In scope. Actual live statuses: `Pending` 2, `Approved` 1. |
| `Hotel Package` | Accommodation | Hotel package create/detail/payment/history path. | 0 | In scope. No live row in audited database. |
| `Transport` | Transports | Public Transport booking order path, detail/history Blade, payment/admin standard order flow. | 1 | In scope. Service Transports only; Transport Management/SPK excluded. |
| `Tour Package` | Tour Packages | Public tour booking path, standard order/admin/payment/history, scheduler auto-cancel. | 0 | In scope. No live row in audited database. |
| `Activity` | Activities | Public activity booking path, standard order/admin/payment/history. | 0 | In scope. No live row in audited database. |

## 7. Global Status Candidates

Classification values used:

- `Global Canonical`
- `Service Fulfillment Only`
- `Legacy Read Alias`
- `Deprecated`
- `Invalid for orders.status`
- `Technical Terminal`
- `Requires Schema Decision`

| Status | Schema Valid | Current Service Usage | Proposed Role | New Write Allowed | Read Compatibility | Future Action |
| --- | --- | --- | --- | --- | --- | --- |
| `Draft` | Yes | Draft/customer-started order paths. | Global Canonical | Yes | Yes | Keep. |
| `Pending` | Yes | Submitted order waiting for review. | Global Canonical | Yes | Yes | Keep. Pending should have reservation compatibility verified per service. |
| `Confirmed` | Yes | Readers/dashboard/list labels; active writer not confirmed in in-scope paths. | Legacy Read Alias / Deprecated | No | Yes, temporary | Decide if still needed for Transports/Tour/Activities; otherwise map/read then retire. |
| `Approved` | Yes | Admin accepted order, payment open. | Global Canonical | Yes | Yes | Keep. |
| `Paid` | Yes | Invoice settled/manual finalization. | Global Canonical | Yes | Yes | Keep as paid commercial state. |
| `Completed` | No | Target Accommodation contract and service fulfillment discussions; not valid in `orders.status`. | Service Fulfillment Only / Requires Schema Decision | No | Derived only | Recommend separate fulfillment state, not immediate global order status. |
| `Canceled` | Yes | Auto-cancel/manual cancellation. | Global Canonical | Yes | Yes | Keep canonical spelling. |
| `Rejected` | Yes | Admin rejection. | Global Canonical | Yes | Yes | Keep. |
| `Invalid` | Yes | Correction/invalid review state. | Global Canonical | Yes | Yes | Keep. |
| `Deleted` | Yes | Dashboard exclusions; technical terminal candidate. | Technical Terminal | Restricted | Yes | Keep only for technical removal/audit policy until soft delete decision. |
| `Active` | No | Reservation/product status; some order UI readers. | Invalid for orders.status / Legacy Read Alias | No | Yes, temporary | Keep for reservation/product only; do not write to orders. |
| `Archive` | No | Admin archive writer and filters. | Invalid for orders.status / Deprecated | No | Yes, temporary | Replace with non-lifecycle archive strategy. |
| `Archived` | No | Display/archive naming candidate. | Legacy Read Alias / Deprecated | No | Yes, temporary | Read only if data exists; do not write. |
| `Removed` | No | Current/history exclusions; product removal concept. | Legacy Read Alias / Deprecated | No | Yes, temporary | Treat as product/archive alias only after data audit. |
| `Accepted` | No | Legacy exclusion in frontend order helper. | Legacy Read Alias / Deprecated | No | Yes, temporary | Remove when reader is replaced and data check passes. |
| `Cancelled` | No for orders enum | Dashboard/reservation spelling variant. | Legacy Read Alias | No | Yes | Map to `Canceled` for reads; normalize later after data audit. |
| lowercase variants | No | Admin index filters `confirmed`, `active`, `invalid`, `rejected`. | Legacy Read Alias / Deprecated | No | Yes, temporary | Replace query filters with exact-case canonical plus temporary aliases. |

## 8. Global Order Lifecycle

Recommended shared lifecycle for the four in-scope services:

```text
Draft
-> Pending
-> Approved
-> Paid
```

Branches:

```text
Pending -> Rejected
Pending -> Invalid
Pending -> Canceled
Approved -> Canceled
Paid -> Approved only by authorized payment reversal
```

Applicability:

| Service | Order Lifecycle | Reservation Review | Payment | Service Date | Completion | History |
| --- | --- | --- | --- | --- | --- | --- |
| Accommodation | Shared lifecycle applies. Accommodation contract additionally targets operational completion after paid checkout/manual override. | Reservation should exist by `Pending`; `Approved` should create/activate reservation and invoice. | Manual payment confirmation; paid when invoice balance settled. | `checkin`/`checkout`. | Fulfillment completion after checkout/manual override. | Should not be checkin-only; use upcoming/in-service/completed/closed grouping. |
| Transports | Shared lifecycle applies for public transport order. | Requires Transports Business Flow Audit. | Manual payment confirmation shared flow. | Pickup/service date fields require audit by Transport subtype. | Requires Transports Business Flow Audit. Do not use SPK as canonical completion. | Transitional date rules allowed only until Transport completion contract is approved. |
| Tour Packages | Shared lifecycle applies. | Requires Tour Packages Business Flow Audit. | Manual payment confirmation shared flow; scheduler auto-cancel currently exists for approved unpaid tour orders. | Tour/travel/checkin date usage requires audit. | Requires Tour Packages Business Flow Audit. | History must not hide unpaid/incomplete orders solely by date. |
| Activities | Shared lifecycle applies. | Requires Activities Business Flow Audit. | Manual payment confirmation shared flow. | Activity/checkin/travel date usage requires audit. | Requires Activities Business Flow Audit; check-in/no-show only if supported by implementation. | History must not be date-only if order is unpaid or unresolved. |

No new global status should be created only to solve one service's operational completion. Fulfillment must remain separate until the four service audits confirm a shared design.

## 9. Service Fulfillment Lifecycle

Fulfillment describes service delivery state, not commercial order/payment state.

| Service | Target Fulfillment Lifecycle | Current Support Level | Notes |
| --- | --- | --- | --- |
| Accommodation | `Upcoming -> In Service -> Completed` | Approved in active Accommodation contract; implementation pending. | Derive upcoming/in-service from paid order plus `checkin`/`checkout`; completion by scheduler/manual override in future task. |
| Transports | `Scheduled -> Service Due -> Completed` | Requires Transports Business Flow Audit. | Do not use SPK completion as canonical public service completion. |
| Tour Packages | `Scheduled -> In Service -> Completed` | Requires Tour Packages Business Flow Audit. | Existing scheduler only auto-cancels unpaid approved tour orders; completion not verified. |
| Activities | `Scheduled -> Checked In / In Service -> Completed` | Requires Activities Business Flow Audit. | `No Show` and `Rescheduled` are candidates only if implementation/business rule supports them. |

Potential branches:

- `Canceled`: supported as shared order branch.
- `No Show`: Activities candidate only; not canonical until audited.
- `Rescheduled`: candidate only; not canonical until audited.

## 10. Completed Decision Analysis

| Option | Advantages | Risks | Migration Impact | Cross-Service Compatibility |
| --- | --- | --- | --- | --- |
| A. Add `Completed` to global `orders.status`. | Simple to query; clear final commercial lifecycle. | Current enum does not support it; completion meaning differs by service; can conflate paid/commercial state with service delivery. | Requires migration, backfill, writer/reader changes, tests, rollback plan. | Not safe until Transports, Tour Packages, and Activities completion rules are approved. |
| B. Use separate fulfillment status. | Preserves shared order lifecycle; supports service-specific completion; avoids changing meaning of `Paid`. | Requires new schema/model/design later; more moving parts. | Future migration or relation needed; no immediate enum change. | Strong compatibility across services because each service can define delivery rules separately. |
| C. Use `completed_at` without changing global status. | Minimal semantic surface; easy history grouping; preserves `Paid`. | Single timestamp may not capture no-show/reschedule/reopen; needs audit rules. | Future nullable timestamp migration; backfill plan needed. | Good for services where completion is binary, weaker for Activities if no-show is needed. |
| D. Combination: global order remains `Paid`, fulfillment has `Completed`, history uses fulfillment completion. | Best domain separation; aligns payment/order/history; allows Accommodation contract and Transports unknowns. | Requires careful UI/reporting compatibility and future schema decision. | Future staged migration for fulfillment field/table or `completed_at`; no immediate schema change in this task. | Recommended. Allows service-specific completion while keeping global order statuses stable. |

Final recommendation: choose Option D.

Do not add `Completed` to `orders.status` in the first implementation. Keep global order status at `Paid` for commercial settlement, add/read service fulfillment completion separately in a later approved implementation, and use fulfillment completion for completed history after data and code are ready.

## 11. Legacy Status Mapping

| Legacy Value | Canonical Mapping | Read During Transition | New Write Allowed | Data Rewrite Required | Removal Condition |
| --- | --- | --- | --- | --- | --- |
| `Confirmed` | Treat as legacy alias requiring business decision; likely `Approved` if it means accepted/payment-open. | Yes | No | Only if data exists and owner approves mapping. | No in-scope service needs it and read-only data check shows no remaining rows. |
| `confirmed` | `Confirmed` legacy alias, then same decision as `Confirmed`. | Yes | No | Maybe | Lowercase readers removed and data has no lowercase rows. |
| `Active` | Do not map silently for orders; valid for reservation/product only. If found in orders, manual classification required. | Yes | No | Yes if any order row exists. | All order writers reject it and data has no order rows with it. |
| `active` | Legacy lowercase alias requiring manual classification. | Yes | No | Maybe | Lowercase readers removed and data has no lowercase rows. |
| `Archive` | No lifecycle mapping; replace with archive flag/timestamp or history grouping. | Yes | No | Yes if any rows exist. | Archive mechanism implemented and no order rows use `Archive`. |
| `Archived` | Archive display/legacy alias only. | Yes | No | Maybe | Archive mechanism implemented and no rows/readers require it. |
| `Removed` | Product/archive-like legacy value; not canonical order status. | Yes | No | Maybe | Product/order semantics separated and no order rows require it. |
| `Accepted` | Do not map silently; likely obsolete alias for approved/active helper. | Yes | No | Maybe | Reader removed and data check has no rows. |
| `invalid` | `Invalid`. | Yes | No | Yes if data exists. | Data normalized and queries use `Invalid`. |
| `rejected` | `Rejected`. | Yes | No | Yes if data exists. | Data normalized and queries use `Rejected`. |
| `Cancelled` | `Canceled`. | Yes | No | Yes if data exists in order/reservation data selected for normalization. | Canonical spelling enforced and no remaining `Cancelled` rows in relevant tables. |
| `Deleted` | Keep as `Deleted` technical terminal, not archive. | Yes | Restricted admin/system only | No unless soft-delete strategy replaces it. | Owner decides soft delete replacement and audit retention path. |

Principles:

- New writes use canonical exact-case values only.
- Legacy aliases are temporary read compatibility only.
- Compatibility must have removal criteria.
- Do not silently map a value if the business meaning is ambiguous.

## 12. Archive Strategy

Archive must not be a canonical order lifecycle status.

| Strategy | Advantages | Risks | Recommendation |
| --- | --- | --- | --- |
| `archived_at` timestamp | Preserves when archive happened; auditable; easy restore by nulling timestamp. | Requires migration and query updates later. | Preferred future option. |
| `is_archived` flag | Simple query filter. | No timestamp unless paired with audit log. | Acceptable only with `archived_at` or audit log. |
| Soft delete | Laravel-supported visibility mechanism; keeps data recoverable. | Can hide records from relationships/reports if global scopes are not handled carefully. | Consider only after relationship audit. |
| Archive relation/table | Rich audit metadata. | More complex for current need. | Overkill unless archive workflow needs approvals/reasons/history. |
| History grouping without archive state | No schema change; low risk. | Does not support manual hiding/archive. | Good transitional rule. |

Target:

- Use history grouping immediately in query/design planning.
- Prefer future `archived_at` for explicit archive.
- Keep archived records accessible by authorized admin/detail routes.
- Preserve reservation, invoice, payment, guest, and order log relations.
- Do not write `Archive` into `orders.status`.

## 13. Deleted Strategy

`Deleted` exists in the current enum and should be treated as a technical terminal status, not a normal business lifecycle state.

Recommendations:

- Do not use `Deleted` for routine archive.
- Restrict any future `Deleted` write to authorized admin/system flows with an audit reason.
- Keep deleted records available for audit/admin reporting unless legal/privacy requirements say otherwise.
- Preserve relations to reservations, invoices, payments, guests, notes, and logs.
- Customer visibility should be a business decision: default recommendation is hidden from normal current/history lists but available to support/admin.
- Soft delete can replace or complement `Deleted` only after relationship and report behavior is audited.

## 14. Runtime Compatibility Strategy

Compatibility target:

- Canonical writes only.
- Temporary read aliases for legacy values.
- Centralized status comparison before query normalization.
- No data rewrite until verification passes.

Do not normalize behavior in this document. The staged implementation below is a plan for future tasks only.

## 15. Data Verification Plan

All queries must be read-only. Use safe `SELECT` and `SHOW` queries only. Do not write, update, delete, truncate, migrate, seed, or backfill production data during verification.

| Verification ID | Service | Purpose | Query Type | Blocking | Expected Result |
| --- | --- | --- | --- | --- | --- |
| DATA-ACC-001 | Accommodation | Count `Hotel`, `Hotel Promo`, `Hotel Package` orders and status distribution. | SELECT aggregate | Yes | No null/empty/unknown status; pending/approved/paid relations classified. |
| DATA-ACC-002 | Accommodation | Detect `Pending` without reservation, `Approved` without invoice, `Paid` with positive balance, `Paid` without valid payment. | SELECT joins | Yes | Zero anomalies or approved repair plan. |
| DATA-ACC-003 | Accommodation | Detect date/history anomalies: past service still active, future service in history candidate. | SELECT date/status checks | Yes | All anomalies classified before history changes. |
| DATA-TRN-001 | Transports | Count public `Transport` orders by service type and status; exclude SPK/Transport Management. | SELECT aggregate | Yes | Public booking data identified without SPK dependency. |
| DATA-TRN-002 | Transports | Verify pickup/service date fields, route snapshot, passenger/luggage/additional fee presence. | SELECT field completeness | Yes | Required public booking snapshot fields known per subtype. |
| DATA-TRN-003 | Transports | Detect payment/reservation anomalies for public Transport orders. | SELECT joins | Yes | Same payment invariants as shared order flow. |
| DATA-TOUR-001 | Tour Packages | Count `Tour Package` orders and status distribution. | SELECT aggregate | Yes | Data confirms status values or absence is documented. |
| DATA-TOUR-002 | Tour Packages | Verify scheduler auto-cancel candidates and paid/approved invoice consistency. | SELECT joins/date checks | Yes | No unsafe scheduler candidates before status changes. |
| DATA-ACT-001 | Activities | Count `Activity` orders and status distribution. | SELECT aggregate | Yes | Data confirms status values or absence is documented. |
| DATA-ACT-002 | Activities | Verify activity/service date fields and paid/history consistency. | SELECT joins/date checks | Yes | Completion/no-show assumptions remain blocked until data confirms support. |
| DATA-SHARED-001 | Four services | Detect duplicate order numbers. | SELECT aggregate | Yes | `orderno` duplicates are zero or classified. |
| DATA-SHARED-002 | Four services | Detect orphan service records and missing customer/user relation. | SELECT joins | Yes | Orders have valid user/customer and service references or repair plan. |
| DATA-SHARED-003 | Four services | Detect `Active`, `Archive`, `Archived`, `Removed`, `Accepted`, `Cancelled`, and lowercase variants in `orders.status`. | SELECT filter | Yes | Zero rows or explicit mapping/backfill plan. |

Example read-only query shapes:

```sql
SELECT service, service_type, status, COUNT(*) AS total
FROM orders
WHERE service IN ('Hotel', 'Hotel Promo', 'Hotel Package', 'Transport', 'Tour Package', 'Activity')
GROUP BY service, service_type, status
ORDER BY service, service_type, status;
```

```sql
SELECT id, orderno, service, service_type, status, rsv_id, checkin, checkout, travel_date, created_at
FROM orders
WHERE service IN ('Hotel', 'Hotel Promo', 'Hotel Package', 'Transport', 'Tour Package', 'Activity')
  AND (
    status IS NULL
    OR status = ''
    OR status NOT IN ('Draft', 'Pending', 'Confirmed', 'Approved', 'Canceled', 'Rejected', 'Invalid', 'Paid', 'Deleted')
    OR BINARY status <> status
  );
```

```sql
SELECT o.id, o.orderno, o.service, o.status
FROM orders o
LEFT JOIN reservations r ON r.id = o.rsv_id
WHERE o.service IN ('Hotel', 'Hotel Promo', 'Hotel Package', 'Transport', 'Tour Package', 'Activity')
  AND o.status = 'Pending'
  AND r.id IS NULL;
```

```sql
SELECT o.id, o.orderno, o.service, o.status, i.id AS invoice_id
FROM orders o
LEFT JOIN reservations r ON r.id = o.rsv_id
LEFT JOIN invoice_admins i ON i.rsv_id = r.id
WHERE o.service IN ('Hotel', 'Hotel Promo', 'Hotel Package', 'Transport', 'Tour Package', 'Activity')
  AND o.status = 'Approved'
  AND i.id IS NULL;
```

```sql
SELECT o.id, o.orderno, o.service, o.status, i.balance
FROM orders o
JOIN reservations r ON r.id = o.rsv_id
JOIN invoice_admins i ON i.rsv_id = r.id
WHERE o.service IN ('Hotel', 'Hotel Promo', 'Hotel Package', 'Transport', 'Tour Package', 'Activity')
  AND o.status = 'Paid'
  AND i.balance > 0;
```

```sql
SELECT orderno, COUNT(*) AS total
FROM orders
WHERE service IN ('Hotel', 'Hotel Promo', 'Hotel Package', 'Transport', 'Tour Package', 'Activity')
GROUP BY orderno
HAVING COUNT(*) > 1;
```

## 16. Test Data Plan

Test data must be created only in an isolated testing database. Do not create fixtures in production.

| Service | Minimal fixture states |
| --- | --- |
| Accommodation | `Draft`, `Pending`, `Approved` unpaid, `Approved` partial paid, `Paid` upcoming, `Paid` in-service, fulfillment `Completed`, `Canceled`, `Rejected`, `Invalid`. |
| Transports | `Draft`, `Pending`, `Approved`, `Paid` future service, `Paid` service date, fulfillment `Completed`, `Canceled`, `Invalid`. Do not use SPK as lifecycle fixture. |
| Tour Packages | `Draft`, `Pending`, `Approved`, `Paid` future, in-service, fulfillment `Completed`, auto-canceled, `Rejected`. |
| Activities | `Draft`, `Pending`, `Approved`, `Paid` scheduled, in-service/check-in, fulfillment `Completed`, `Canceled`; `No Show` only if implementation/business rule supports it. |

Fixture invariants:

- `Pending` should have reservation when the target service contract requires it.
- `Approved` should have reservation and invoice.
- `Paid` should have invoice balance `<= 0` and a valid payment or manual settlement log.
- Completed fulfillment should not be inferred from date alone unless a service contract approves that rule.

## 17. History Compatibility

| Service | Current Rule | Target Rule | Transitional Rule |
| --- | --- | --- | --- |
| Accommodation | Standard orders currently move to history mainly by `checkin < now`; excludes `Removed`/`Archive`. | Upcoming by check-in, in-service by check-in/check-out, completed history by fulfillment completion, closed history for `Canceled`, `Rejected`, `Invalid`. | Keep current filters with compatibility aliases while adding attention buckets for unpaid/past orders; do not rely on checkin-only completion. |
| Transports | Standard order history currently uses shared date/status behavior; exact public completion is not verified. | Requires Transports Business Flow Audit. Completion may be manual, service-date based, handoff-informed, or hybrid after approval. | Do not use SPK completion. Keep paid/service-date grouping as display only until audit. |
| Tour Packages | Standard history uses shared date/status behavior; scheduler auto-cancel exists for approved unpaid tour orders. | Scheduled/in-service/completed history requires Tour Packages Business Flow Audit. | Keep legacy history plus unpaid/approved attention checks. |
| Activities | Standard history uses shared date/status behavior; no-show/check-in completion not verified. | Scheduled/in-service/completed history requires Activities Business Flow Audit; `No Show` only if supported. | Keep legacy history plus unpaid/approved attention checks. |

History rule: do not move an order to completed history only because the date passed if it is unpaid, unresolved, invalid, or still requires operational review.

## 18. Payment Compatibility

DOKU is excluded from this target architecture. This plan covers active manual payment confirmation behavior.

| Payment Action | Expected Order Effect | Invoice Effect | Reservation Effect | Compatibility Risk |
| --- | --- | --- | --- | --- |
| Upload receipt for `Approved` order | Order remains `Approved`. | No balance change until validation. | Reservation remains current/active. | Duplicate upload and ownership authorization must be audited. |
| Validate receipt as `Valid` with partial amount | Order remains `Approved`. | Balance decreases; derived state `Partially Paid`. | Reservation remains active/current. | UI may imply paid if balance handling is inconsistent. |
| Validate receipt as `Valid` with full settlement | Order becomes `Paid`. | Balance `<= 0`; derived state `Paid`. | Reservation remains active unless service-specific contract says otherwise. | Requires transaction/log consistency. |
| Mark `Pending` receipt `Invalid` | Order remains `Approved`. | Balance unchanged. | Reservation unchanged. | Must not block valid future upload. |
| Reverse `Valid` receipt to `Invalid` | If balance becomes positive, order should downgrade `Paid -> Approved`. | Balance restored by reversed amount. | Reservation remains active unless order is canceled/reopened by policy. | Current standard implementation may not downgrade `Paid`. |
| Upload replacement receipt | Order remains `Approved` unless validation settles invoice. | No balance change until validation. | Reservation unchanged. | Replacement must be idempotent and file ownership must be checked. |
| Manual finalization/settlement | Order can become `Paid` only with auditable reason. | Balance set/derived as settled. | Reservation sync must be defined. | Can hide unpaid invoice if not logged and authorized. |

Payment target:

- Keep payment confirmation statuses `Pending`, `Valid`, and `Invalid`.
- Derive invoice state from invoice balance and valid receipts.
- Keep partial payment out of `orders.status`.
- Require a future implementation to make payment validation/reversal transactional and auditable.

## 19. Implementation Stages

| Stage | Preconditions | Files/modules impacted later | Test requirements | Rollback strategy | Completion criteria |
| --- | --- | --- | --- | --- | --- |
| Stage 1 - Central Status Contract | Proposed plan approved by owner. | Future status constants/service only. | Unit tests for constants/mapping in isolated test DB or pure unit scope. | Remove unused constants; no data rollback. | Central vocabulary exists without behavior change. |
| Stage 2 - Normalize New Writes | Stage 1 complete; writer inventory confirmed. | Order creation, admin actions, payment handlers. | Writer tests for canonical exact-case statuses. | Revert writer guards; data unchanged if no writes occurred. | New writes use only canonical values. |
| Stage 3 - Legacy Read Compatibility | Legacy mapping approved. | Order queries, dashboards, current/history views. | Query tests for canonical and legacy aliases. | Revert alias reader layer. | Readers show canonical and legacy records consistently. |
| Stage 4 - Data Verification | Read-only query checklist approved. | Audit command/report only. | No production tests; read-only verification report. | No rollback because no writes. | DATA checks completed for four services. |
| Stage 5 - Safe Data Normalization | Verification complete; backup/rollback plan approved. | Future command or data migration. | Dry run, idempotency, rollback, sampled record tests. | Restore from backup or reverse mapping command where safe. | Legacy data normalized with audit report. |
| Stage 6 - Normalize Queries | Data normalization complete or aliases proven enough. | Order lists, admin dashboard, history, payment views. | Regression tests for current/history/admin/payment filters. | Restore alias-aware queries. | Queries use canonical values and no records disappear. |
| Stage 7 - Schema Alignment | Code and data use canonical only; migration risk accepted. | Migration, model casts/constants if chosen. | Migration up/down on isolated DB; old-data compatibility tests. | Migration rollback plus code rollback. | Schema matches approved status strategy. |
| Stage 8 - Remove Legacy Compatibility | Monitoring proves no legacy rows/writes. | Alias readers, docs, tests. | Tests prove no legacy dependency. | Re-enable alias readers if monitoring finds missed values. | Compatibility aliases removed and docs updated. |

## 20. Rollback Strategy

General rollback principles for future implementation:

- Do not combine schema migration, data rewrite, and behavior change in one step.
- Each stage must be reversible independently.
- Any data normalization must have dry-run output, affected row count, backup requirement, and idempotency key/log.
- Reader compatibility should be added before data normalization and removed only after monitoring.
- Writer normalization should reject invalid new writes before schema alignment.
- If schema alignment fails, keep application on canonical values supported by the current enum until a corrected migration is ready.

## 21. Required Business Decisions

| Decision | Options | Recommendation | Risk | Impact | Blocking |
| --- | --- | --- | --- | --- | --- |
| 1. Is `Completed` global order status or fulfillment state? | Global status; separate fulfillment; `completed_at`; combination. | Combination: global order remains `Paid`, fulfillment records `Completed`, history uses fulfillment completion. | Global `Completed` too early can break cross-service meaning. | Affects schema, history, reporting. | Blocking before migration/schema work. |
| 2. Is `Confirmed` still needed for Transports, Tour Packages, Activities? | Keep as canonical; map to `Approved`; retire after audit. | Retire as new write; keep temporary read alias until each service audit proves safe mapping. | Silent mapping may change meaning. | Affects admin filters and dashboard. | Blocking before removing alias. |
| 3. Archive strategy. | `archived_at`; `is_archived`; soft delete; archive relation; history grouping only. | Transitional history grouping; future `archived_at` preferred. | Archive-as-status can fail enum writes. | Affects list/history/admin access. | Blocking before archive implementation. |
| 4. `Deleted` strategy. | Keep status; replace with soft delete; combine with audit log. | Keep as restricted technical terminal until soft-delete audit. | Hiding data can break audit/support. | Affects customer/admin visibility. | Non-blocking for status compatibility; blocking for delete/archive work. |
| 5. Completion rule for Service Transports. | Manual; service-date; internal handoff-informed; scheduler/manual hybrid. | Requires Transports Business Flow Audit. | Using SPK as canonical too early mixes domains. | Affects transport history and fulfillment. | Blocking before transport completion implementation. |
| 6. Do Activities need `No Show`? | No; yes as fulfillment; yes as order status. | Only as fulfillment if implementation/business rule supports it. | Order status explosion. | Affects activity history/reporting. | Blocking before Activity fulfillment design. |
| 7. When remove legacy compatibility? | After data normalization; after monitoring; never. | After data normalization plus monitoring proves no legacy rows/writes. | Removing too early hides records. | Affects readers/dashboard/history. | Blocking before Stage 8. |
| 8. Must all services have reservation from `Pending`? | Yes all; Accommodation only first; service-specific. | Accommodation yes per active contract; other services require audits before universal rule. | Universal rule may break current transport/tour/activity creation. | Affects order creation/admin detail. | Blocking before global writer guard. |
| 9. Should payment reversal downgrade all `Paid` orders to `Approved`? | Yes if balance positive; no; service-specific. | Yes for in-scope manual payment if invoice balance becomes positive, except completed/reopen rules need approval. | Can reopen closed orders without policy. | Affects payment handler and reporting. | Blocking before payment reversal implementation. |
| 10. Should terminal orders appear in one History or separate categories? | Single history; completed vs closed; hidden/archive. | Separate completed history and closed terminal history. | Single list can confuse customers/admin. | Affects UI/reporting. | Non-blocking for compatibility; blocking for history redesign. |

## 22. Blocking Risks

P0:

- `Archive` and `Active` writes to `orders.status` are unsafe with current enum.
- Lowercase admin filters can hide exact-case statuses.
- Data verification is incomplete for Tour Packages and Activities in current live data.
- Payment reversal can leave `Paid` order inconsistent with invoice balance.

P1:

- `Completed` has no schema support and no verified cross-service semantics.
- History is currently date-heavy and may show unresolved orders as history.
- Reservation creation timing differs by flow and may not be safe to require globally without service audits.

Out-of-scope risks:

- SPK status mismatch belongs to Transport Management tasks and is not a blocker for this plan.
- Wedding/payment differences belong to Wedding domain and must not drive the four-service status contract.

## 23. Recommended Next Task

Recommended next task:

`ACC-SEC-001 - Audit authorization and IDOR protection for Accommodation order detail, invoice, and payment confirmation`

Reason:

- It supports Accommodation, the first service in the approved sequence.
- It is read-only/audit-first and can be performed before status implementation.
- Payment upload, invoice access, and detail routes are high-risk surfaces before status writer changes.
- It does not depend on Transport Management, SPK, Wedding, DOKU, or Private Villa.

## 24. Acceptance Criteria

| Criterion | Result |
| --- | --- |
| Scope confirms only Accommodation, Transports, Tour Packages, and Activities. | Passed |
| Scope Boundary: Transports vs Transport Management is documented. | Passed |
| Current schema and service inventory are documented. | Passed |
| Global status compatibility matrix is complete. | Passed |
| Global order lifecycle and service fulfillment separation are documented. | Passed |
| `Completed` options are compared and a final recommendation is stated. | Passed |
| Legacy status mapping is complete. | Passed |
| Archive and Deleted strategies are documented. | Passed |
| Data verification plan includes required checks and read-only query shapes. | Passed |
| Test data plan is documented for four services. | Passed |
| Runtime compatibility stages include preconditions, impacted modules, tests, rollback, and completion criteria. | Passed |
| History and payment compatibility are documented. | Passed |
| Required business decisions are recorded. | Passed |
| No code, database, migration, enum, payment, scheduler, SPK, or Transport Management behavior is changed. | Passed |

## 25. References

- `AGENTS.md`
- `docs/README.md`
- `docs/decisions/accommodation-status-lifecycle-audit.md`
- `docs/decisions/shared-order-status-audit.md`
- `docs/decisions/accommodation-status-contract.md`
- `docs/decisions/service-booking-flow-audit-roadmap.md`
