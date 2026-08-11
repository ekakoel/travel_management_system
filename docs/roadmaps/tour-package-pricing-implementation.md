# Tour Package Pricing Implementation Roadmap

Status: Phase 3 Implementation In Progress
Updated: 2026-07-31
Contract: `docs/standards/pricing-standard.md`
Planning decision: Approved for implementation planning
Implementation status: Phase 3 reopened for Tour Package Price CRUD completion

## Current Position

- Phase 1 — Completed
- Phase 2 — Completed
- Phase 3 — In Progress (Tour Package Price CRUD)
- Phase 4 — Not Started

```markdown
- [x] Phase 1 completed
- [x] Phase 2 completed
- [ ] Phase 3 completed
- [ ] Phase 4 completed
```

### Earlier Phase 3 Pricing Evidence

The earlier pricing runtime implementation was recorded on 2026-07-31. A
targeted CRUD review subsequently found that the Tour Package Price admin flow
still wrote legacy fields, lacked canonical start-date and overlap controls,
and did not expose the complete readiness state. Phase 3 is therefore reopened
for this bounded CRUD completion. This does not
activate production pricing and does not change the pricing contract from
`Proposed` to `Active`.

- Tour reservation, invoice, payment balance, email, PDF, report, and
  admin/customer detail paths use the immutable snapshot adapter. The only
  legacy fallback reads committed invoice/order values, emits a warning, and
  never reconstructs historical money from a current rate or tax.
- New Tour orders without a valid snapshot fail closed. The snapshot reader
  rejects non-Tour services.
- Tour-only architecture guards cover shared-service isolation, generic order
  route isolation, nullable shared schema, historical surfaces, and committed
  invoice balance use.
- Targeted pricing and public Tour tests passed: 40 tests and 207 assertions.
  Closest shared Transport, Activity, and Accommodation pricing regressions
  passed: 56 tests and 325 assertions.
- Disposable MariaDB 10.4.32 verification passed: 5 tests and 17 assertions,
  including pricing migration DDL, foreign keys/indexes, snapshot
  relationships, idempotency, transaction rollback, and row-lock contention.
  The seven pricing migrations passed `up()` and reverse `down()` checks.
- A full project migration remains blocked before the pricing migrations by
  the pre-existing `2025_09_09_100443_create_spks_table` foreign-key error.
  That out-of-scope SPK migration was not changed.
- The full SQLite suite was executed, but existing unrelated test groups
  failed. All targeted pricing/Tour tests remained green; the unrelated
  failures were not changed as part of this Tour-only scope.
- Tour frontend source and registered compiled asset passed JavaScript syntax
  checks. A production asset build was intentionally not run over the dirty
  shared asset worktree; deployment must run `npm run production` and review
  the compiled diff.
- No production migration, backfill, rate update, tax activation, or pricing
  remediation was performed. The disposable database and server were removed
  after verification.

Phase 4 remains a future readiness and approval gate. Production remains
fail-closed until Finance resolves Tour Price IDs 70-72 (or approves other
ready rows), an explicit tax-policy effective time is approved, a fresh USD
sell rate and monitoring are available, migration/build runbooks are reviewed,
and Product, Finance, and Engineering sign off.

### Phase 4 Readiness Checklist

- [x] Phase 3 technical evidence recorded
- [x] Pricing contract remains `Proposed` and runtime remains fail-closed
- [ ] Finance approves remediation for IDs 70-72 or explicitly approves other
  production-ready Tour price rows
- [ ] Product/Finance approve the initial Tour 1.5% tax-policy
  `effective_from` timestamp and approver
- [ ] Operations proves a fresh USD `sell` rate, retrieval metadata, scheduler,
  queue, alerting, and last-success monitoring
- [ ] Engineering reviews the production backup, additive migration,
  rollback, and post-deploy verification runbook
- [x] Frontend production assets are built and the compiled manifest/integrity
  is reviewed
- [ ] Existing full-suite failures are triaged under the release policy
- [ ] Product, Finance, and Engineering sign the production activation record
- [ ] Contract status changes from `Proposed` to `Active`

No unchecked item may be inferred from legacy data or completed automatically
by deployment code.

#### Phase 4 Engineering Evidence — 2026-07-31

- The standard `npm run production` command initially failed before
  compilation because its legacy Webpack CLI `--no-progress` option is not
  supported by the installed Webpack CLI.
- The obsolete option was removed from `package.json`; the standard production
  command then completed successfully.
- Production output contained three non-terminal child-compilation warnings
  and no compilation error.
- The generated Tour detail assets passed JavaScript syntax validation.
- All 95 `public/mix-manifest.json` entries resolve to an existing file and
  every versioned entry matches the generated file MD5; missing files and hash
  mismatches are both zero.
- The generated Tour detail JavaScript contains the server quote request,
  snapshot-compatible display fields, unavailable-state handling, and no
  client-authoritative final-price calculation.
- The build covers the complete current frontend worktree, not only Tour
  assets. Its compiled outputs must stay coupled to the same reviewed source
  commit during deployment.
- Pricing migrations and rollback behavior already passed the isolated
  MariaDB 10.4.32 rehearsal. Production backup/restore ownership and the
  pre-existing SPK migration blocker remain unchecked operational gates.

#### Draft Production Runbook — Approval Required

This sequence is a review artifact, not authorization to run against
production:

1. Pin one reviewed release commit containing matching PHP/Blade/JavaScript
   source, production assets, and `public/mix-manifest.json`.
2. Record the target environment/database identity and abort if it is not the
   explicitly approved target.
3. Take a restorable database backup and have the restore owner acknowledge
   the restore location and procedure.
4. Resolve and rehearse the pre-existing SPK foreign-key migration blocker in
   a separately approved task; do not alter SPK as part of Tour pricing.
5. Run `php artisan migrate --pretend` against a production-equivalent staging
   copy and review that only approved additive migrations are pending.
6. During the approved maintenance window, deploy the pinned release and run
   the approved additive migration command. Stop on any unexpected pending
   migration or DDL.
7. Produce a fresh USD sell rate and verify `retrieved_at`,
   `retrieval_source`, scheduler/queue health, and alerting.
8. Finance remediates selected Tour prices through the reviewed admin fields;
   retain before/after evidence and never infer legacy markup currency.
9. After owner approval, create the initial Tour tax policy using
   `pricing:activate-tour-tax` with the exact approved `--effective-from`,
   existing `--approved-by`, and production acknowledgement.
10. Verify one controlled listing/detail/quote flow before allowing order
    creation, then verify order/snapshot/invoice USD/IDR parity and payment
    balance without changing current rate/tax inputs.
11. Monitor pricing-unavailable codes, stale-rate failures, legacy fallback
    warnings, snapshot/invoice parity, and duplicate/idempotency signals.

Rollback policy:

- Before any Tour order snapshot exists, deployment owners may roll back code
  and the new empty schema using the reviewed reverse migration sequence.
- After any snapshot/order exists, do not drop pricing history. Stop new Tour
  ordering, roll back code only to the compatible snapshot reader, preserve
  additive tables/columns, and investigate from the recorded evidence.
- Never reconstruct or overwrite historical totals with current product,
  rate, discount, or tax data.

## 1. Purpose and Guardrails

This roadmap converts the Proposed pricing contract into one ordered Phase 3
implementation checklist. It does not authorize production migration,
production data mutation, or pricing activation.

The rollout is Tour Package-only:

```text
Pricing Foundation
-> Additive Database Schema
-> Live Tour Pricing
-> Order Pricing
-> Historical Consumers
-> Cleanup
-> Testing
```

Every batch has an independent rollback point. A later batch must not begin
when the prior batch acceptance criteria or its own dependency gate fails.
Accommodation, Transport, Activity, Wedding, Private Villa, DOKU architecture,
internal Transport Management/SPK, and Tour add-ons are out of scope.

## 2. Updated Contract Summary

The contract remains `Status: Proposed`. The following behavior is now marked
**Approved for implementation planning**:

| Area | Approved contract |
| --- | --- |
| Canonical money | IDR integer rupiah |
| Tour display money | USD integer cents, always two displayed decimals |
| Exchange-rate side | `usd_rates.sell`; the new resolver never reads legacy `rate` |
| Freshness | Maximum 24 hours; transition uses `retrieved_at ?? updated_at`, final state uses only `retrieved_at` |
| Stale public price | No HTTP 500 for expected unavailability; no stale numeric price; show `price temporarily unavailable` |
| Stale quote/order | Fail closed and create no transaction |
| Rounding | Round half-up at defined boundaries; no PHP binary float or intermediate `ceil()` |
| Tour markup | New Tour markup is explicit USD; amount, currency, source, verifier, and status are stored |
| Unresolved markup | IDs 70-72 are blocked from quote/order until Finance approves a mapping |
| Tour tax | Versioned 1.5% exclusive tax on contract rate plus markup |
| Tax activation | `effective_from` is a deployment decision and is never guessed |
| Discounts | Promotion and booking code do not stack by default; select the larger financial benefit |
| Percentage base | Gross Tour total after tax |
| Discount trust | Request sends identifiers only; server resolves eligibility and amount |
| Add-ons | Excluded from the initial Phase 3 pilot |
| Historical consumers | Read the active immutable order pricing snapshot |
| Invoice | IDR uses `final_total_idr`; USD uses `final_total_usd_minor`; no current rate/tax |
| Repricing actor | Super Admin or a user with `orders.reprice`; never direct customer action |
| Non-pricing edit | Must preserve snapshot reference and totals |

## 3. Remaining Unresolved Decisions

These decisions do not block the Pricing Foundation implementation:

1. Finance must provide the exact approved markup amount and currency for Tour
   Price IDs 70-72, with approver and timestamp.
2. Release ownership must choose the initial Tour tax
   `tax_policies.effective_from`.
3. Before repricing is enabled, Product/Finance must approve the
   eligible order statuses and the invoice/payment behavior for already
   invoiced or paid orders.

Decision 1 blocks live availability for IDs 70-72 and all production order
writes while no other ready price exists. Decision 2 blocks live tax resolution
and order writes. Decision 3 keeps repricing disabled but does not block
snapshot-only historical reads.

## 4. Batch Control Matrix

| Checklist group | Deployable independently | May write production data | Activation effect |
| --- | --- | --- | --- |
| Pricing Foundation and Additive Database Schema | Code and additive schema can be reviewed independently | No backfill; migrations are not run by Codex on production | None |
| Live Tour Pricing | Yes, behind disabled feature/config gate | No order/snapshot write | Live public prices only after data/rate/tax gates |
| Order Pricing | Yes, after live quote parity | New Tour orders and snapshots after explicit deployment approval | Authoritative Tour order writes |
| Historical Consumers | Consumer-by-consumer | Repricing only if separately approved; normal reads are non-mutating | Historical snapshot reads |
| Cleanup and Testing | Only after all prior gates | No remediation inference; approved cleanup only | Contract may become Active |

## Phase 3 — Implementasi

### Tour Package Price CRUD

- [x] Audit route CRUD existing
- [x] Audit controller CRUD existing
- [x] Audit model dan relation
- [x] Audit admin Blade/form
- [x] Store Form Request
- [x] Update Form Request
- [x] Canonical field mapping
- [x] Create Price
- [x] Edit Price
- [x] Soft delete Price
- [x] Status transition
- [x] Pax/date overlap validation
- [x] Quoteability rule
- [x] Legacy warning
- [x] Needs Review filter
- [x] Admin validation messages
- [x] Frontend integration verified
- [x] Create Order integration verified
- [x] CRUD unit/feature tests
- [x] Documentation updated

The simplified CRUD uses pax bounds, `contract_rate_idr`, explicit
`markup_type` (`percentage`, `usd`, or `idr`), `markup_amount`, and
`valid_from`/`valid_until`. Operator-managed pricing status was removed on
2026-07-31. Complete writes automatically receive internal compatibility and
verification metadata. Legacy `contract_rate`, `markup`, `expired_date`,
`status`, and `pricing_data_status` remain compatibility fields and are not
form inputs. Writes are rejected when canonical input is incomplete or when
another row for the same Tour overlaps both pax and validity intervals.
Expired, deleted, invalid, and operationally unavailable rows fail closed in
quote and Create Order.

Markup simplification closure (2026-07-31):

- [x] Remove status, markup source, and currency fields from operator form
- [x] Add explicit additive `tour_prices.markup_type`
- [x] Percentage markup uses contract IDR as its base
- [x] USD markup remains USD per pax
- [x] IDR markup remains exact whole rupiah per pax
- [x] Automatically write internal active/verified compatibility metadata
- [x] Preserve immutable historical order snapshots
- [x] Exclude current route-bound price during update overlap validation
- [x] Format Percentage/USD markup inputs to two decimals and IDR as integer
- [x] Store exact validated markup numeric-string without six-decimal padding

Date regression closure (2026-07-31):

- [x] Remove legacy `.date-picker` from Tour Price create/update
- [x] Canonical backend picker submits `Y-m-d`
- [x] Server compatibility-normalizes known `d F Y` / `d M Y` payloads
- [x] Ambiguous numeric date formats fail closed
- [x] Create/update date-contract regression tests

### Pricing Foundation

- [x] Money value object
- [x] Fixed-scale arithmetic
- [x] PricingException
- [x] PricingEngine
- [x] CurrencyRateResolver
- [x] TaxResolver
- [x] PricingQuote
- [x] PricingSnapshot
- [x] MoneyFormatter

#### Objective

Build and verify calculation, resolver, quote, snapshot, exception, model, and
additive-schema foundations without connecting them to public Tour UI or order
writes.

#### Scope

- `Money` value object.
- Strict decimal-string parser and fixed-scale integer arithmetic.
- Checked addition, subtraction, multiplication, and half-up
  multiply/divide.
- `PricingException` with stable error codes and safe context.
- Generic `PricingEngine`.
- `CurrencyRateResolver`.
- `TaxResolver`.
- Immutable `PricingQuote`.
- Immutable `PricingSnapshot`.
- Additive Tour/rate/tax/order/snapshot schema.
- `OrderPricingSnapshot` model with no normal update/delete path.
- Unit tests.
- Migration verification only on an explicitly disposable database.

#### Out of Scope

- Frontend, Blade, JavaScript, listing, detail, or quote endpoint.
- `Create Order`.
- Reservation, invoice, payment, email, PDF, report, or admin detail.
- Repricing behavior.
- Promotion/booking-code integration.
- Tour add-ons.
- Legacy formula cleanup.
- Production migration or data backfill.

#### Files to Create

```text
app/ValueObjects/Money.php
app/Support/Pricing/FixedScale.php
app/Services/Pricing/PricingEngine.php
app/Services/Pricing/CurrencyRateResolver.php
app/Services/Pricing/TaxResolver.php
app/Data/Pricing/PricingQuote.php
app/Data/Pricing/PricingSnapshot.php
app/Data/Pricing/ResolvedCurrencyRate.php
app/Data/Pricing/ResolvedTaxPolicy.php
app/Exceptions/PricingException.php
app/Models/TaxPolicy.php
app/Models/OrderPricingSnapshot.php
database/migrations/YYYY_MM_DD_HHMMSS_add_pricing_shadows_to_tour_prices.php
database/migrations/YYYY_MM_DD_HHMMSS_add_retrieval_metadata_to_usd_rates.php
database/migrations/YYYY_MM_DD_HHMMSS_create_tax_policies_table.php
database/migrations/YYYY_MM_DD_HHMMSS_create_order_pricing_snapshots_table.php
database/migrations/YYYY_MM_DD_HHMMSS_add_pricing_summary_to_orders.php
tests/Unit/Pricing/MoneyTest.php
tests/Unit/Pricing/FixedScaleTest.php
tests/Unit/Pricing/PricingEngineTest.php
tests/Unit/Pricing/CurrencyRateResolverTest.php
tests/Unit/Pricing/TaxResolverTest.php
tests/Unit/Pricing/PricingQuoteTest.php
tests/Unit/Pricing/PricingSnapshotTest.php
tests/Feature/Pricing/PricingSchemaMigrationTest.php
```

Migration timestamps must be generated at implementation time and ordered:
shadow/rate/tax first, snapshot table next, nullable order reference last.

#### Files to Change

```text
app/Models/TourPrices.php
app/Models/UsdRates.php
app/Models/Orders.php
docs/standards/pricing-standard.md
docs/modules/tour-package.md
```

Changes are limited to casts/relations/constants required by the foundation.
No existing calculation caller is switched in the foundation implementation.

### Additive Database Schema

- [x] Tour Price shadow fields
- [x] Rate `retrieved_at` metadata
- [x] Tax policy table
- [x] Order pricing summary fields
- [x] Order pricing snapshot table
- [x] Snapshot model and relations
- [x] Migration rollback verified on SQLite `:memory:`

#### Migration Plan

- All new columns are additive.
- Existing legacy columns remain unchanged.
- Shadow financial fields begin unresolved/null.
- No automatic monetary mapping is performed.
- Tax policy table is empty until an approved activation record is inserted.
- Snapshot table is empty.
- Order summary fields are nullable, preserving every existing service/order.
- Circular references are added in safe order: create snapshots with
  `order_id`, add order summary/reference, then add the nullable active-snapshot
  foreign key only after both tables exist.
- `down()` first drops the order active-snapshot foreign key/columns, then the
  snapshot table, tax policies, and newly added shadow fields. It must not
  alter legacy values.

#### Tests

- Money currency mismatch and non-negative invariants.
- Decimal parsing: valid, excessive scale, signs, whitespace, exponent,
  malformed, and overflow.
- Half-up below/tie/above boundaries.
- Required example: IDR 1,000,000, USD markup 20.00, rate 16,000.000000,
  tax 10% -> IDR 1,452,000 and USD 90.75.
- Rate resolver: sell only, no `rate` fallback, missing, duplicate, malformed,
  non-positive, future timestamp, 24-hour boundary, stale, transition timestamp,
  and post-transition timestamp.
- Tax resolver: exact policy, inactive/missing, boundary, and overlap failure.
- Quote/snapshot immutability and serialization.
- Migration up/down on SQLite where supported and disposable MariaDB for
  MariaDB-specific DDL.

#### Acceptance Criteria

- No pricing calculation uses binary float or `ceil()`.
- Native integer operations detect overflow before it occurs.
- Resolver code contains no read of `usd_rates.rate`.
- Rate at exactly 24 hours follows the approved inclusive boundary; older rate
  fails.
- Tax overlap fails closed.
- Required numerical example passes.
- Migrations are additive and reversible on a disposable database.
- No public route/controller/view/order behavior changes.

#### Rollback Point

Revert foundation service wiring and, only before any environment contains
snapshot/backfill data, run the new migrations' `down()` on that disposable
environment. In a populated environment, leave additive structures in place
and roll back code; never drop populated financial history blindly.

#### Dependencies

- PHP 64-bit integers.
- No new Composer package.
- Explicit isolated test connection.
- MariaDB 10.4.32 compatibility review.
- Existing dirty worktree ownership preserved.

#### Risks

- Integer overflow if multiplication is not reduced/checked.
- SQLite falsely passing MariaDB-specific DDL or locking behavior.
- Accidental use of legacy `rate`.
- Mutable snapshot model through generic Eloquent methods.
- Adding a circular FK in the wrong migration order.

#### Stop Conditions

- Stop before any DB-backed test if connection/database identity is not
  explicitly disposable.
- Stop before running migration on `online_bali_kami_26`.
- Stop if a required amount cannot fit the documented integer bounds.
- Stop if migration diff alters/drops legacy columns.
- Stop if implementation requires frontend/order/historical integration.

### Live Tour Pricing

- [x] TourPackagePricingService
- [x] Tour price eligibility
- [x] Pax tier validation
- [x] Validity-date validation
- [x] Deleted-price protection
- [x] Unresolved markup protection
- [x] Public listing quote
- [x] Authenticated listing/search/filter/sort quote
- [x] Tour detail quote
- [x] Price endpoint
- [x] Server-authoritative preview
- [x] Promotion and booking-code candidate selection
- [x] Price unavailable behavior

#### Objective

Replace public Tour calculations and preview with one server-produced live
quote while keeping order creation unchanged.

#### Scope

- `TourPackagePricingService`.
- Tour ownership, Active status, valid canonical date, unresolved-data guard,
  and soft-deletion protection.
- Pax tier selection using inclusive `min_qty`/`max_qty`.
- Live quote endpoint and versioned response.
- Listing quote summary and detail tier quotes.
- Frontend preview rendering server quote; request sends identifiers/date/pax.
- Promotion and booking-code eligibility/candidate calculation.
- Store both candidate breakdowns and select the larger IDR benefit.
- User-safe `price temporarily unavailable` behavior.
- Expected availability failures must not become HTTP 500.

#### Out of Scope

- Create Order writes or order snapshot writes.
- Reservation, invoice, payment, email, PDF, report, or repricing.
- Add-ons.
- Removal of legacy formula code that is still used by non-switched consumers.

#### Files to Create

```text
app/Services/Tours/TourPackagePricingService.php
app/Http/Requests/Tours/QuoteTourPackageRequest.php
app/Http/Resources/Pricing/PricingQuoteResource.php
app/Http/Controllers/TourPackageQuoteController.php
tests/Feature/Pricing/TourPackageLiveQuoteTest.php
tests/Feature/Pricing/TourPackagePriceUnavailableTest.php
tests/Feature/Pricing/TourPackageDiscountSelectionTest.php
```

#### Files to Change

```text
app/Services/Tours/TourPricingService.php
app/Services/Tours/TourInventoryService.php
app/Http/Controllers/FrontEndController.php
app/Http/Controllers/ToursController.php
app/Http/Controllers/TourPricesController.php
app/Models/TourPrices.php
app/Models/Promotion.php
app/Models/BookingCode.php
resources/views/frontend/landing-page/tours/directory.blade.php
resources/views/frontend/landing-page/tours/detail.blade.php
resources/frontend/js/landing-page/tours/detail.js
routes/web.php
docs/modules/tour-package.md
```

Only files/routes proven by route and view inspection at implementation time
are changed. Proposed paths do not authorize a broad controller refactor.

#### Migration

If the existing promotion/booking-code tables still lack explicit type,
currency, service scope, validity, and verification status, create additive
shadow columns in a separate Live Tour Pricing migration. Do not infer fixed versus
percentage from magnitude. Unmapped discounts remain unavailable. No order or
snapshot schema change belongs here.

#### Tests

- Correct tour ownership and cross-tour price-ID rejection.
- Active/inactive/deleted/unresolved/expired/invalid-date rows.
- Parent Tour active and not deleted.
- Pax boundaries, gap, and overlap ambiguity.
- Travel date equal to `valid_until`.
- Stale/missing/ambiguous rate and tax.
- Listing/detail unavailable state and non-500 response.
- Quote response version, integer amounts, two-decimal USD formatting.
- Promotion only, booking code only, both, neither, equal-value tie.
- Fixed discount explicit currency and percentage gross-after-tax base.
- Request tampering with rate/tax/discount amount ignored.
- JavaScript displays server totals and performs no authoritative arithmetic.

#### Acceptance Criteria

- Listing, detail, endpoint, and preview use the same service/engine.
- IDs 70-72 return unavailable until Finance mapping is approved.
- Stale rates never appear as valid prices.
- Expected pricing unavailability has a translated UI state and safe API
  response.
- Discount candidates and selected candidate are present in the breakdown.
- Existing Create Order behavior is untouched in this batch.

#### Rollback Point

Disable the new quote feature/config gate and restore existing read routes.
Keep additive mapped fields; do not delete mappings. No order data exists from
this batch.

#### Dependencies

- Pricing Foundation accepted.
- At least one Finance-verified, currently eligible price is required before
  public availability can be enabled.
- Fresh USD sell rate and effective Tour tax policy.
- Explicit promotion/booking-code mapping for any enabled discount.

#### Risks

- Listing N+1 quote calculation.
- Quote endpoint abuse or actor-specific cache leakage.
- Race between preview and later checkout.
- Legacy date rows accidentally parsed permissively.
- Hidden client arithmetic remaining in a second initializer.

#### Stop Conditions

- Stop activation if no Tour price has `pricing_data_status = ready`.
- Stop if rate/tax resolver is unavailable or stale.
- Stop if promotion/booking-code unit cannot be proven.
- Stop before any Create Order or snapshot write change.

### Order Pricing

- [x] Create Order authoritative quote
- [x] Request price tampering protection
- [x] Pricing snapshot write
- [x] Active snapshot reference
- [x] Canonical totals
- [x] Legacy dual-write
- [x] Transaction
- [x] Idempotency
- [x] Concurrency protection

#### Objective

Make Create Order recompute and commit one authoritative quote and immutable
snapshot atomically.

#### Scope

- Create Order accepts identifiers and user inputs only.
- Ignore submitted price, rate, tax, discount amount/currency/percentage, and
  totals.
- Re-resolve eligibility and freshness inside the write path.
- Store immutable snapshot, typed order totals, and active snapshot reference.
- Dual-write legacy order fields from the same quote.
- One database transaction for order, snapshot, reservation, guests, and
  related records in the established flow.
- Duplicate-submission idempotency.
- Row/concurrency protection for price, discount usage, and idempotency key.
- Price-tampering and rollback tests.

#### Out of Scope

- Historical consumer switch.
- Invoice/payment schema redesign.
- Repricing.
- Add-ons.
- Other public services.
- Legacy column removal.

#### Files to Create

```text
app/Http/Requests/Tours/StoreTourPackageOrderRequest.php
app/Services/Tours/CreateTourPackageOrderService.php
tests/Feature/Pricing/TourPackageAuthoritativeOrderTest.php
tests/Feature/Pricing/TourPackageOrderIdempotencyTest.php
tests/Feature/Pricing/TourPackageOrderConcurrencyTest.php
```

If the project already has equivalent request/service classes, extend the
established class rather than create duplicates.

#### Files to Change

```text
app/Http/Controllers/OrderController.php
app/Services/Tours/TourPackagePricingService.php
app/Services/TourReservationService.php
app/Models/Orders.php
app/Models/OrderPricingSnapshot.php
resources/views/frontend/landing-page/tours/detail.blade.php
resources/frontend/js/landing-page/tours/detail.js
routes/web.php
docs/modules/tour-package.md
```

#### Migration

- Add a scoped unique idempotency key only after inspecting current duplicate
  guards and existing values.
- Add no non-null order pricing column until compatibility/backfill is proven.
- Foreign keys/indexes planned in Section 11 are added only after orphan and
  type checks pass.
- No legacy financial column is removed or retyped.

#### Tests

- Every submitted monetary/rate/tax field is ignored.
- Cross-tour, inactive, deleted, expired, unresolved, wrong-pax price rejected.
- Rate becomes stale between preview and submit.
- Discount becomes invalid between preview and submit.
- Exactly one snapshot and one order for repeated idempotency request.
- Concurrent submissions do not double-consume a limited code.
- Exception at each multi-record step rolls back all writes.
- Snapshot totals equal typed order totals and legacy dual-write mirrors.
- Snapshot checksum/fingerprint stable.

#### Acceptance Criteria

- New Tour order cannot exist without a committed snapshot.
- `orders.pricing_snapshot_id` points to that order's snapshot.
- `final_total_idr` and `final_total_usd_minor` exactly match the snapshot.
- Legacy fields derive from the same quote, never a second calculation.
- Transaction, idempotency, and concurrency tests pass on disposable MariaDB
  where row-lock semantics matter.

#### Rollback Point

Disable new Tour order writes and revert controller/service wiring. Preserve all
created snapshots and typed totals. A code rollback may read compatibility
mirrors; it must not delete committed financial records.

#### Dependencies

- Live Tour Pricing parity accepted.
- Finance mapping and effective tax policy complete.
- Fresh rate operations verified.
- Disposable MariaDB concurrency test available.
- Backup and production migration runbook approved.

#### Risks

- Partial writes in the current shared controller.
- Duplicate orders across retry boundaries.
- Snapshot/order mismatch from dual calculations.
- Discount usage race.
- Feature rollback after new-format orders exist.

#### Stop Conditions

- Stop before production enablement if any eligible price is unresolved.
- Stop if active tax policies overlap.
- Stop if fresh rate monitoring is not operational.
- Stop if transaction rollback or idempotency tests fail.
- Stop before changing historical consumers.

### Historical Consumers

- [x] Reservation reads snapshot
- [x] Invoice reads snapshot
- [x] Payment reads committed invoice totals derived from the snapshot
- [x] Email reads snapshot
- [x] PDF reads snapshot
- [x] Report reads snapshot
- [x] Admin/customer Tour detail reads snapshot totals
- [x] Non-pricing edit preserves snapshot
- [x] Historical consumers do not query current rate or tax

#### Objective

Switch each Tour historical/financial consumer from mutable current data to the
active order pricing snapshot.

#### Scope

- Reservation reads order snapshot.
- Invoice IDR reads `final_total_idr`.
- Invoice USD reads `final_total_usd_minor`.
- Payment/balance, email, PDF, report, and admin order detail read snapshot.
- Existing order compatibility with an explicit legacy marker.
- Non-pricing edit preserves snapshot.
- Guarded repricing only if lifecycle/invoice decisions receive separate
  approval.

#### Out of Scope

- Reconstructing old snapshots with current data.
- Global invoice/payment schema conversion.
- Repricing paid/invoiced orders without an approved workflow.
- Other services.
- Legacy formula removal.

#### Files to Create

```text
app/Services/Pricing/OrderPricingSnapshotReader.php
app/Services/Pricing/RepriceOrderService.php
app/Policies/OrderRepricingPolicy.php
app/Models/OrderPricingReprice.php
database/migrations/YYYY_MM_DD_HHMMSS_create_order_pricing_reprices_table.php
tests/Feature/Pricing/TourHistoricalSnapshotConsumersTest.php
tests/Feature/Pricing/TourOrderRepricingAuthorizationTest.php
tests/Feature/Pricing/TourOrderNonPricingEditTest.php
```

`RepriceOrderService`, policy, migration, and tests are created only if
repricing receives approval. Otherwise the route/command remains absent.

#### Files to Change

```text
app/Models/Orders.php
app/Models/Reservation.php
app/Models/InvoiceAdmin.php
app/Models/PaymentConfirmation.php
app/Http/Controllers/OrdersAdminController.php
app/Http/Controllers/InvoiceAdminController.php
app/Http/Controllers/PaymentConfirmationController.php
app/Services/TourReservationService.php
resources/views/frontend/home/orders/details/tour-modern.blade.php
resources/views/frontend/home/orders/edit-tour.blade.php
resources/views/emails/invoiceTourEn.blade.php
resources/views/emails/invoiceTourZh.blade.php
resources/views/backend/reports/downloads/tour.blade.php
resources/views/backend/operations/tours/detail.blade.php
```

Actual consumers must be re-inventoried before editing; only Tour branches may
switch.

#### Migration

The optional reprice audit table from Section 11.6 is additive. Existing orders
without snapshots are not auto-backfilled. No invoice or payment amount is
rewritten during consumer switching.

#### Tests

- Change current rate/tax/product/discount after order; all outputs remain
  unchanged.
- Invoice IDR/USD exact snapshot fields.
- Reservation/payment/email/PDF/report/admin parity.
- Legacy order has explicit unavailable/legacy behavior, never silent current
  repricing.
- Non-pricing edits retain snapshot ID/checksum.
- Customer and ordinary admin cannot reprice.
- Super Admin/`orders.reprice` flow requires reason and records old/new IDs,
  changed components, actor, and timestamp.
- Concurrent/stale repricing rejected.

#### Acceptance Criteria

- No Tour historical consumer queries current USD rate/tax for totals.
- Regenerated invoice/PDF/email remains stable after source-data changes.
- Missing legacy snapshot is explicit and observable.
- Repricing remains disabled unless all specific approval/tests pass.

#### Rollback Point

Rollback one consumer at a time behind a compatibility reader. Do not modify or
delete snapshots. If repricing was enabled, disable its route/permission while
preserving audit rows.

#### Dependencies

- Order Pricing accepted.
- Complete consumer inventory.
- Permission seeding pattern inspected.
- Repricing lifecycle and invoiced/paid behavior approved if repricing is in
  scope.

#### Risks

- Hidden current-rate conversion in PDF/email/report.
- Shared invoice code affecting other services.
- Legacy order without snapshot rendered as a new quote.
- Repricing desynchronizing invoices/payments.

#### Stop Conditions

- Stop if a consumer cannot be isolated to Tour Package.
- Stop repricing if eligible statuses or invoice/payment behavior are
  unresolved.
- Stop if legacy compatibility would require current-data reconstruction.

### Cleanup

- [x] Remove duplicated live Tour formulas
- [x] Remove authoritative calculation from Blade
- [x] Remove authoritative calculation from JavaScript
- [x] Remove current-rate conversion from every Tour invoice/report pathway
- [x] Remove duplicate Tour pricing cache keys
- [x] Retain only required compatibility code

#### Objective

Remove obsolete Tour-only calculations after parity, run final audit, document
compatibility code, and activate the contract only when every gate passes.

#### Scope

- Remove obsolete Tour model/controller/service formula.
- Remove authoritative JavaScript calculations.
- Remove current rate/tax queries from Tour historical consumers.
- Add static architecture guards.
- Run quote/order/snapshot parity suite.
- Run dependency, route, query, cache, and consumer audit.
- Document intentionally retained compatibility code.
- Change contract to `Active` only after explicit acceptance.

#### Out of Scope

- Physical removal/retyping of shared legacy DB columns.
- Other service pricing migration.
- Add-ons.
- Global financial refactor.

#### Files to Create

```text
tests/Feature/Pricing/TourPricingArchitectureGuardTest.php
docs/decisions/tour-package-pricing-activation-audit.md
```

#### Files to Change

```text
app/Models/TourPrices.php
app/Services/Tours/TourPricingService.php
app/Services/Tours/TourInventoryService.php
app/Http/Controllers/FrontEndController.php
app/Http/Controllers/ToursController.php
app/Http/Controllers/TourPricesController.php
app/Http/Controllers/OrderController.php
app/Http/Controllers/InvoiceAdminController.php
resources/views/frontend/landing-page/tours/detail.blade.php
resources/frontend/js/landing-page/tours/detail.js
resources/views/backend/reports/downloads/tour.blade.php
docs/standards/pricing-standard.md
docs/modules/tour-package.md
docs/README.md
```

#### Migration

None by default. Physical legacy cleanup is a future separately approved
migration after zero-read proof and export/rollback planning.

### Testing

- [x] Unit tests
- [x] Feature tests
- [x] Snapshot tests
- [x] Tampering tests
- [x] Idempotency tests
- [x] Service isolation tests
- [x] Disposable database verification
- [x] Relevant targeted test suite passes
- [x] Phase 3 completed

#### Tests

- Full Phase 3 pricing matrix.
- Static guards against `TourPrices::calculatePrice`, Tour `ceil()`, direct
  `UsdRates`/`Tax` pricing reads, and client total arithmetic.
- Listing/detail/quote/create parity.
- Snapshot integrity and historical stability.
- Authorization, idempotency, concurrency, cache invalidation, unavailable
  state, and rollback drills.
- Non-Tour regression tests.

#### Acceptance Criteria

- No direct Tour price formula remains outside `PricingEngine`.
- No new pricing consumer reads `usd_rates.rate`.
- No historical Tour consumer reads current rate/tax.
- Every new Tour order has a valid snapshot and matching typed totals.
- All required tests pass on verified disposable databases.
- Operations confirms fresh-rate scheduler/queue monitoring.
- Finance approves remediation and tax activation evidence.
- Rollback rehearsal is documented.
- Product, Finance, and Engineering explicitly approve `Status: Active`.

#### Rollback Point

Keep the contract Proposed and the feature gate disabled. Restore compatibility
adapters without restoring duplicate calculations. Preserve schema, mappings,
orders, snapshots, and audits.

#### Dependencies

- the preceding Phase 3 implementation groups acceptance.
- Production observation window with no parity mismatch.
- Complete audit evidence and sign-off.

#### Risks

- Cleanup deletes a compatibility path still used by a hidden route.
- Contract activated while rates are stale.
- Shared-service regression.
- Static guard has false negatives due dynamic access.

#### Stop Conditions

- Stop activation on any mismatch, stale-rate incident, unresolved active
  price, missing historical consumer, failed test, or missing sign-off.
- Stop before shared column removal or other-service refactor.

## 10. MariaDB 10.4.32 Schema Readiness

### 10.1 Conventions

- All migrations are additive and use new files.
- `BIGINT UNSIGNED` stores non-negative rupiah/minor units; application bounds
  remain below PHP signed 64-bit maximum.
- `DECIMAL` values are read as strings and never cast to float.
- `DATETIME(6)` preserves deterministic calculation/retrieval timestamps.
- Currency codes use uppercase ISO-like `CHAR(3)`.
- Status strings use `VARCHAR`, not MariaDB `ENUM`, so additive states do not
  require destructive enum alteration.
- MariaDB 10.4 treats `JSON` as text-compatible storage. Financial/searchable
  values stay typed; breakdown uses `LONGTEXT` plus application validation and
  an optional verified `JSON_VALID` check.
- Time-range overlap is enforced by service transaction/locking and resolver
  fail-closed behavior; MariaDB has no exclusion constraint.

### 10.2 `tour_prices` Shadow Columns

| Name | Database type | Nullable | Default | Index | Foreign key | Reason | Backfill source | Compatibility behavior |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `contract_rate_idr` | `BIGINT UNSIGNED` | Yes | `NULL` | No initially | None | Typed canonical contract rate | Strictly numeric positive legacy `contract_rate`, only through approved batch | New pricing requires value; legacy column remains untouched |
| `markup_amount` | `VARCHAR(32)` numeric-string | Yes | `NULL` | No | None | Exact operator-entered markup without fixed padding; parsed to fixed-scale integer | Deterministic trim of insignificant legacy decimal zeros | Legacy `markup` is never magnitude-inferred |
| `markup_currency` | `CHAR(3)` | Yes | `NULL` | No | None | Explicit unit; new Tour rows require `USD` | Finance-approved mapping | New pricing rejects null/non-USD Tour value |
| `markup_source` | `VARCHAR(64)` | Yes | `NULL` | No | None | Records evidence source | Mapping source identifier | Admin warning when absent |
| `markup_verified_at` | `DATETIME(6)` | Yes | `NULL` | No | None | Verification audit | Approved mapping timestamp | Required for `ready` |
| `markup_verified_by` | `BIGINT UNSIGNED` | Yes | `NULL` | Yes | `users.id`, added after validity check, `RESTRICT` delete | Verification actor | Approved Finance user | Required for `ready`; no fabricated actor |
| `pricing_data_status` | `VARCHAR(32)` | No | `unresolved` | Composite eligibility index | None | Explicit `unresolved`, `ready`, or `invalid` gate | Conservative default; later approved transition | Only `ready` is quoteable |
| `valid_until` | `DATE` | Yes | `NULL` | Composite eligibility index | None | Canonical replacement for varchar `expired_date` | Approved strict mapping | New pricing rejects null; legacy string remains |

Recommended eligibility index after query-plan review:

```text
(tour_id, status, pricing_data_status, valid_until, min_qty, max_qty, deleted_at)
```

Because current `tour_id` is `VARCHAR(255)` with no live FK, changing its type or
adding a foreign key is not part of Pricing Foundation. The data audit found orphan rows.

### 10.3 `usd_rates` Retrieval Metadata

| Name | Database type | Nullable | Default | Index | Foreign key | Reason | Backfill source | Compatibility behavior |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `retrieved_at` | `DATETIME(6)` | Yes during transition | `NULL` | `(name, retrieved_at)` | None | Source retrieval timestamp | Approved updater log/source evidence only | Resolver temporarily uses `retrieved_at ?? updated_at`; final mode requires it |
| `retrieval_source` | `VARCHAR(64)` | Yes | `NULL` | No | None | API/manual/backfill provenance | Updater/manual actor mapping | Missing source is logged; activation requires approved source |

`rate` remains a legacy compatibility field. The resolver reads only `sell` as
a strict decimal string and never falls back to `rate` or `buy`. A failed fetch
must not update `retrieved_at`.

### 10.4 New `tax_policies` Table

| Name | Database type | Nullable | Default | Index | Foreign key | Reason | Backfill source | Compatibility behavior |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `id` | `BIGINT UNSIGNED` auto increment | No | — | Primary | None | Version identity | New approved policy | Snapshot references immutable version |
| `service` | `VARCHAR(64)` | No | — | Overlap index | None | Service scope | `Tour Package` | No impact to other services |
| `name` | `VARCHAR(191)` | No | — | No | None | Human/audit label | Approved policy | Stored in snapshot breakdown |
| `percentage_scaled` | `BIGINT UNSIGNED` | No | — | No | None | Exact percentage value | 1.5 -> `1500000` at scale `1000000` | Never cast to float |
| `percentage_scale` | `INT UNSIGNED` | No | `1000000` | No | None | Self-describing decimal scale | Contract constant | Snapshot copies value/scale |
| `calculation_type` | `VARCHAR(32)` | No | `exclusive` | No | None | Tax method | Approved contract | Resolver rejects other type for pilot |
| `taxable_base` | `VARCHAR(64)` | No | — | No | None | Formula scope | `contract_plus_markup` | No implied add-on tax |
| `status` | `VARCHAR(32)` | No | `draft` | Overlap index | None | Draft/active/retired gate | Deployment approval | Only active policy resolves |
| `effective_from` | `DATETIME(6)` | No | — | Overlap index | None | Inclusive version start | Deployment decision, never guessed | Draft may exist before activation |
| `effective_until` | `DATETIME(6)` | Yes | `NULL` | Overlap index | None | Exclusive version end; null open-ended | Approved retirement/successor | Resolver checks calculation timestamp |
| `approved_by` | `BIGINT UNSIGNED` | Yes | `NULL` | Yes | `users.id`, `RESTRICT` | Business approver | Approved actor | Active requires non-null |
| `approved_at` | `DATETIME(6)` | Yes | `NULL` | No | None | Approval timestamp | Approval event | Active requires non-null |
| `created_at` | `DATETIME(6)` | No | Current timestamp via Laravel | No | None | Audit | Application | Standard timestamp |
| `updated_at` | `DATETIME(6)` | No | Current/application | No | None | Administrative audit | Application | Policy content is not changed after use; successor version preferred |

Recommended overlap lookup index:

```text
(service, status, effective_from, effective_until)
```

Application writes lock matching service policy rows and reject:

```text
existing.effective_from < candidate.effective_until_or_infinity
AND candidate.effective_from < existing.effective_until_or_infinity
```

`TaxResolver` independently rejects more than one effective match.

### 10.5 `orders` Typed Pricing Summary

| Name | Database type | Nullable | Default | Index | Foreign key | Reason | Backfill source | Compatibility behavior |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `pricing_version` | `VARCHAR(64)` | Yes | `NULL` | No | None | Formula/contract version | New quote only | Null marks legacy order |
| `pricing_snapshot_id` | `BIGINT UNSIGNED` | Yes | `NULL` | Yes, unique considered after validation | `order_pricing_snapshots.id`, `RESTRICT` | Active snapshot pointer | New snapshot write/reprice | Null handled explicitly as legacy |
| `base_currency` | `CHAR(3)` | Yes | `NULL` | No | None | Canonical currency | New quote `IDR` | Legacy fields remain mirrors |
| `display_currency` | `CHAR(3)` | Yes | `NULL` | No | None | Tour display currency | New quote `USD` | No guessed fallback |
| `final_total_idr` | `BIGINT UNSIGNED` | Yes | `NULL` | Add only if report plan proves need | None | Canonical report/order total | Same committed quote | Must equal active snapshot |
| `final_total_usd_minor` | `BIGINT UNSIGNED` | Yes | `NULL` | No initially | None | USD cents | Same committed quote | Must equal active snapshot |
| `pricing_calculated_at` | `DATETIME(6)` | Yes | `NULL` | No | None | Quote calculation timestamp | Same committed quote | Not updated by non-pricing edit |

Fields remain nullable because `orders` is shared and existing orders/services do
not have snapshots.

### 10.6 New `order_pricing_snapshots` Table

| Name | Database type | Nullable | Default | Index | Foreign key | Reason | Backfill source | Compatibility behavior |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `id` | `BIGINT UNSIGNED` auto increment | No | — | Primary | None | Snapshot identity | New order/reprice | Immutable |
| `order_id` | `BIGINT UNSIGNED` | No | — | Unique with sequence | `orders.id`, `RESTRICT` | Snapshot owner | Committed order | Prevent financial deletion cascade |
| `snapshot_sequence` | `INT UNSIGNED` | No | `1` | Unique `(order_id, snapshot_sequence)` | None | Reprice version | Service-calculated | Old versions preserved |
| `pricing_version` | `VARCHAR(64)` | No | — | No | None | Formula version | Quote | Required |
| `service` | `VARCHAR(64)` | No | — | `(service, calculated_at)` | None | Service discriminator | Quote | Pilot requires Tour Package |
| `service_id` | `BIGINT UNSIGNED` | No | — | No | None | Product identity | Quote | No polymorphic FK |
| `price_id` | `BIGINT UNSIGNED` | No | — | Yes | None | Tier identity at sale | Quote | Preserved if source later deleted |
| `base_currency` | `CHAR(3)` | No | `IDR` | No | None | Canonical unit | Quote | Self-contained |
| `display_currency` | `CHAR(3)` | No | `USD` | No | None | Display unit | Quote | Self-contained |
| `quantity` | `INT UNSIGNED` | No | — | No | None | Pax count | Quote | Must be positive |
| `contract_rate_idr` | `BIGINT UNSIGNED` | No | — | No | None | Canonical unit component | Quote | Never reread product |
| `markup_amount_minor` | `BIGINT UNSIGNED` | No | — | No | None | Markup in its currency minor unit | Quote | Currency paired |
| `markup_currency` | `CHAR(3)` | No | — | No | None | Markup unit | Quote | Tour requires USD |
| `markup_idr` | `BIGINT UNSIGNED` | No | — | No | None | Converted canonical markup | Quote | Stored, not reconverted |
| `subtotal_idr` | `BIGINT UNSIGNED` | No | — | No | None | Contract plus markup | Quote | Integrity-checkable |
| `tax_policy_id` | `BIGINT UNSIGNED` | No | — | `(tax_policy_id, calculated_at)` | `tax_policies.id`, `RESTRICT` | Tax version | Resolver | Policy cannot disappear |
| `tax_percentage_scaled` | `BIGINT UNSIGNED` | No | — | No | None | Exact copied percentage | Resolver | Historical self-contained |
| `tax_percentage_scale` | `INT UNSIGNED` | No | `1000000` | No | None | Percentage scale | Resolver | Historical self-contained |
| `tax_amount_idr` | `BIGINT UNSIGNED` | No | — | No | None | Exact tax | Quote | Stored |
| `rate_id` | `BIGINT UNSIGNED` | No | — | `(rate_id, calculated_at)` | `usd_rates.id`, `RESTRICT` | Source rate row | Resolver | Snapshot also stores value |
| `rate_pair` | `VARCHAR(16)` | No | `USD/IDR` | No | None | Pair semantics | Resolver | Self-contained |
| `rate_side` | `VARCHAR(16)` | No | `sell` | No | None | Side semantics | Resolver | Must be sell |
| `rate_value_scaled` | `BIGINT UNSIGNED` | No | — | No | None | Exact IDR-per-USD value | Strict decimal resolver | No current rate query |
| `rate_value_scale` | `INT UNSIGNED` | No | `1000000` | No | None | Rate scale | Resolver | Self-contained |
| `rate_source` | `VARCHAR(64)` | No | — | No | None | Provenance | Resolver | Audit |
| `rate_retrieved_at` | `DATETIME(6)` | No | — | No | None | Freshness evidence | Resolver | Historical fact |
| `rate_max_age_seconds` | `INT UNSIGNED` | No | `86400` | No | None | Applied freshness policy | Resolver | Historical fact |
| `unit_price_idr` | `BIGINT UNSIGNED` | No | — | No | None | Per-pax canonical price | Quote | Stored |
| `unit_price_usd_minor` | `BIGINT UNSIGNED` | No | — | No | None | Per-pax USD cents | Quote | Stored |
| `gross_total_idr` | `BIGINT UNSIGNED` | No | — | No | None | Pre-discount total | Quote | Stored |
| `gross_total_usd_minor` | `BIGINT UNSIGNED` | No | — | No | None | Pre-discount display total | Quote | Stored |
| `discount_total_idr` | `BIGINT UNSIGNED` | No | `0` | No | None | Selected discount | Quote | Candidates remain in breakdown |
| `discount_total_usd_minor` | `BIGINT UNSIGNED` | No | `0` | No | None | Selected display discount | Quote | Stored |
| `addon_total_idr` | `BIGINT UNSIGNED` | No | `0` | No | None | Reserved future total | Quote | Always zero in pilot |
| `addon_total_usd_minor` | `BIGINT UNSIGNED` | No | `0` | No | None | Reserved future display total | Quote | Always zero in pilot |
| `final_total_idr` | `BIGINT UNSIGNED` | No | — | Optional finance index | None | Canonical final total | Quote | Matches order |
| `final_total_usd_minor` | `BIGINT UNSIGNED` | No | — | No | None | USD cents final total | Quote | Matches order/invoice |
| `rounding_policy` | `VARCHAR(64)` | No | `half-up-v1` | No | None | Rounding version | Engine | Self-contained |
| `calculated_at` | `DATETIME(6)` | No | — | Service/rate/tax indexes | None | Calculation instant | Quote clock | Immutable |
| `calculated_by` | `BIGINT UNSIGNED` | Yes | `NULL` | Yes | `users.id`, `RESTRICT` | Actor when authenticated | Quote actor | System/guest represented in breakdown |
| `reason` | `VARCHAR(500)` | Yes | `NULL` | No | None | Initial/reprice context | Service | Required for reprice |
| `input_fingerprint` | `CHAR(64)` | No | — | Yes | None | Idempotency/input integrity | Canonical serialized inputs | No PII |
| `snapshot_checksum` | `CHAR(64)` | No | — | No | None | Tamper/parity verification | Canonical snapshot serialization | Recomputed only for audit |
| `breakdown` | `LONGTEXT` | No | — | No | Optional verified `JSON_VALID` check | Complete lines/candidates/metadata | Quote | Application validates JSON schema/version |
| `created_at` | `DATETIME(6)` | No | Application timestamp | No | None | Persistence timestamp | Application | No `updated_at` column |

Committed snapshots have no public update/delete service. Database privileges
and application policy should prevent mutation; corrections create a new
sequence.

### 10.7 Optional `order_pricing_reprices` Audit Table

| Name | Database type | Nullable | Default | Index | Foreign key | Reason | Backfill source | Compatibility behavior |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `id` | `BIGINT UNSIGNED` auto increment | No | — | Primary | None | Audit identity | Reprice operation | Immutable |
| `order_id` | `BIGINT UNSIGNED` | No | — | Yes | `orders.id`, `RESTRICT` | Target order | Locked order | No cascade deletion |
| `old_snapshot_id` | `BIGINT UNSIGNED` | No | — | Yes | `order_pricing_snapshots.id`, `RESTRICT` | Before state | Active snapshot | Required |
| `new_snapshot_id` | `BIGINT UNSIGNED` | No | — | Unique | `order_pricing_snapshots.id`, `RESTRICT` | After state | New snapshot | One audit per new snapshot |
| `actor_id` | `BIGINT UNSIGNED` | No | — | Yes | `users.id`, `RESTRICT` | Authorized actor | Auth context | Must be Super Admin/permission |
| `reason` | `TEXT` | No | — | No | None | Mandatory explanation | Actor input | Trimmed/non-empty |
| `changed_components` | `LONGTEXT` | No | — | No | Optional verified `JSON_VALID` check | Structured before/after component keys | Service diff | No arbitrary client amounts |
| `created_at` | `DATETIME(6)` | No | Application timestamp | Yes | None | Audit time | Application clock | Immutable |

This table is planned but not created until repricing is approved.

## 11. Data Readiness Report

### 11.1 Method

Read-only inspection was run on 2026-07-29 against:

```text
database: online_bali_kami_26
server: MariaDB 10.4.32
Tour Price rows: 72
Tour Package orders: 0
```

Only `SELECT` and `information_schema` reads were used. No row, schema, cache,
configuration, or timestamp was changed.

Primary classification uses this precedence:

```text
deleted
-> invalid canonical date
-> invalid contract rate
-> invalid pax tier
-> known unresolved markup
-> expired
-> ambiguous
-> ready
```

One record may also have secondary integrity findings.

### 11.2 Classification

| Classification | Count | IDs | Readiness |
| --- | ---: | --- | --- |
| `ready` | 0 | — | No row currently passes every new-contract gate |
| `expired` | 12 | 58-69 | ISO `valid_until` equivalent is 2026-06-30, before audit date |
| `deleted/logically deleted` | 0 | — | No `tour_prices.deleted_at` value found |
| `invalid date` | 57 | 1-57 | Legacy string `31 December 2024` is not canonical ISO/DATE and is not auto-parsed |
| `unresolved markup` | 3 | 70-72 | Active date but legacy `markup = 150000`; Finance mapping absent |
| `invalid contract rate` | 0 | — | All values are positive digit strings |
| `invalid pax tier` | 0 | — | All rows have `min_qty >= 1` and `max_qty >= min_qty` |
| `ambiguous` | 0 current tier overlaps | — | No overlap among current ISO-date candidates; see secondary orphan finding |

Secondary finding: IDs 1-57 reference tour IDs that are not present in the
current `tours` table. They are both invalid-date rows and orphan/ambiguous
historical catalog data. They must never be reactivated by date backfill alone.

All 72 rows have legacy `status = Active`. Therefore that status alone is not
evidence of quote readiness.

### 11.3 Current Operational Inputs

| Input | Read-only result | Readiness |
| --- | --- | --- |
| USD row | `sell = 18077.043`, `rate = 18077.043`, updated 2026-07-16 17:00:03 | Stale by more than 24 hours; no `retrieved_at` column |
| Tax row | ID 1, `tax = 1.5` | Value matches pilot, but no service/version/effective dates |
| Proposed order columns | None exist | Additive migration required |
| Snapshot table | Does not exist | Additive migration required |
| Tour orders | 0 in inspected DB | Lower local backfill risk, not proof for other environments |

### 11.4 Data Gates

- IDs 70-72 stay visible in admin with an unresolved warning.
- No active row may quote until `contract_rate_idr`, markup verification,
  canonical validity date, and `pricing_data_status = ready` are approved.
- IDs 1-57 require parent-product disposition in addition to date/markup
  review.
- IDs 58-69 remain expired; do not extend validity automatically.
- A new fresh USD sell update with real `retrieved_at` is required.
- The tax policy must be inserted as `draft`, checked for overlap, then
  activated at the deployment-approved timestamp.

## 12. Markup Remediation Template

No approval value is inferred or prefilled:

| tour_price_id | legacy_markup | legacy_assumed_currency | approved_markup_amount | approved_markup_currency | approved_by | approved_at | notes |
| ---: | ---: | --- | --- | --- | --- | --- | --- |
| 70 | 150000 | IDR (unverified observation only) |  |  |  |  |  |
| 71 | 150000 | IDR (unverified observation only) |  |  |  |  |  |
| 72 | 150000 | IDR (unverified observation only) |  |  |  |  |  |

Required remediation workflow:

1. Finance attaches source evidence per row.
2. An authorized approver fills amount/currency; no engineer substitutes a
   guessed USD conversion.
3. A reviewer verifies tier, tour, contract rate, and validity.
4. An idempotent data migration/import writes shadow fields and audit evidence.
5. Before/after export, counts, and checksums are compared.
6. Only then may status transition from `unresolved` to `ready`.

## 13. Disposable Test Database Readiness

### 13.1 Current Configuration

| Check | Result |
| --- | --- |
| `phpunit.xml` APP environment | `APP_ENV=testing` |
| `phpunit.xml` DB connection | SQLite settings exist but are commented out |
| `.env.testing` | Missing |
| Effective default DB driver | MySQL unless process environment overrides it |
| Active DB observed during audit | `online_bali_kami_26` |
| `pdo_sqlite` | Installed |
| BCMath extension | Installed, but not declared as required dependency |
| Existing test guards | Several pricing/public-flow tests explicitly require SQLite `:memory:` and skip otherwise |

Conclusion: a normal unqualified `php artisan test` is **not safe** yet because
the test database is not explicitly isolated.

### 13.2 SQLite In-Memory Support

SQLite `:memory:` is supported by the installed PDO extension and current
Laravel database configuration. Existing Tour tests already create a reduced
SQLite schema manually. It is appropriate for:

- pure pricing unit tests;
- resolver tests with test tables/fakes;
- quote serialization;
- request/authorization/tampering feature tests;
- ordinary transaction rollback behavior.

It is not sufficient evidence for:

- MariaDB `UNSIGNED`, `DECIMAL`, `DATETIME(6)`, index-length, or collation
  behavior;
- `LONGTEXT`/`JSON_VALID` DDL;
- actual foreign-key migration ordering;
- `SELECT ... FOR UPDATE`, deadlocks, isolation, or concurrent idempotency;
- MariaDB query plans and overlap locking.

### 13.3 Recommended Disposable Setup

Use two layers:

1. SQLite `:memory:` for fast unit/feature coverage, with process-local
   environment overrides and a test assertion that aborts unless the effective
   driver/database are exactly `sqlite`/`:memory:`.
2. A separately created MariaDB database such as
   `online_bali_kami_26_pricing_test`, with a least-privilege test user, no
   production data dependency, and an explicit `.env.testing` or CI secret.

Before every DB-backed run, print/assert:

```text
APP_ENV = testing
DB_CONNECTION = sqlite or mysql
DB_DATABASE = :memory: or an explicitly approved *_test database
DB_DATABASE != online_bali_kami_26
```

### 13.4 Safe Phase 3 Commands

Only after the checks above succeed:

```powershell
$env:APP_ENV='testing'
$env:DB_CONNECTION='sqlite'
$env:DB_DATABASE=':memory:'
php artisan test --testsuite=Unit --filter=Pricing
php artisan test --filter=TourPackageLiveQuoteTest
php artisan test --filter=TourPackageAuthoritativeOrderTest
```

For disposable MariaDB, use an explicitly reviewed `.env.testing` and first
run a read-only identity check. Migration/test commands are safe only when the
database name is the approved disposable name.

### 13.5 Forbidden Commands

Never run these against the active or an unverified connection:

```text
php artisan test
vendor/bin/phpunit
php artisan migrate
php artisan migrate:fresh
php artisan migrate:refresh
php artisan migrate:reset
php artisan db:wipe
php artisan db:seed
DROP / TRUNCATE / bulk DELETE / unscoped UPDATE / destructive ALTER
```

`migrate:fresh`, `migrate:refresh`, and `db:wipe` remain forbidden against any
non-disposable data even if a command is convenient.

## 14. Arithmetic Implementation Recommendation

### 14.1 Decision

Use native signed 64-bit integer scaling with checked/reduced arithmetic. Do not
add a dependency in Pricing Foundation.

| Option | Assessment |
| --- | --- |
| Native integers | Recommended; sufficient for IDR rupiah, USD cents, fixed-scale FX/percent when bounds and overflow checks are enforced |
| BCMath | Extension exists locally but is not declared as a project requirement; using it would create environment portability risk |
| `brick/math` | Not installed and no `composer.lock` is present; unnecessary new dependency for the approved bounds |
| PHP float | Forbidden |

### 14.2 Numerical Scales

```text
IDR money:                 1 rupiah
USD money:                 100 minor units per USD
FX rate:                   1,000,000 scaled units per IDR/USD unit
Tax/discount percentage:   1,000,000 scaled units per percentage unit
1.5 percent stored as:     1,500,000
10 percent stored as:      10,000,000
percentage denominator:    100 * 1,000,000
```

Strict input examples:

```text
sell "18077.043" -> rate_value_scaled 18,077,043,000
markup USD 13.00 -> markup_amount_minor 1,300
```

### 14.3 Required Primitives

- `parseDecimal(string $value, int $scale): int`
- `checkedAdd(int ...$values): int`
- `checkedSubtractNonNegative(int $left, int $right): int`
- `checkedMultiply(int $left, int $right): int`
- `mulDivHalfUp(int $left, int $right, int $divisor): int`
- `convertIdrToUsdCents(int $idr, int $rateScaled): int`
- `convertUsdCentsToIdr(int $cents, int $rateScaled): int`

`mulDivHalfUp` must reduce operands/divisor by greatest common divisors before
multiplication, check overflow, then round non-negative quotient/remainder:

```text
quotient  = intdiv(numerator, divisor)
remainder = numerator % divisor
round up when remainder >= divisor - remainder
```

This avoids `2 * remainder` overflow. Every public constructor rejects
negative money, invalid currency, unsupported scale, exponent notation,
thousands separators, and excessive decimals. Define and test a conservative
application maximum for unit and final totals; do not rely on the wider
`BIGINT UNSIGNED` database range because PHP integers are signed.

## 15. File Plan by Implementation Group

| Implementation group | New domain/runtime files | Main changed integration files | Migration group | Main tests |
| --- | --- | --- | --- | --- |
| Pricing Foundation | Money, FixedScale, engine, resolvers, DTOs, exception, policy/snapshot models | TourPrices/UsdRates/Orders models, docs | Shadow fields, rate metadata, tax policies, snapshots, order summary | Unit arithmetic/resolvers/DTO + disposable migration |
| Live Tour Pricing | TourPackagePricingService, quote request/resource/controller | Tour listing/detail controllers/views/JS, promotion/booking models, routes | Discount shadow metadata only if required | Live quote, eligibility, unavailable, discounts |
| Order Pricing | Order request/service | OrderController, Tour service, reservation integration, order form | Optional idempotency constraint after audit | Tampering, transaction, idempotency, concurrency |
| Historical Consumers | Snapshot reader; optional repricing service/policy/audit model | Reservation/invoice/payment/email/PDF/report/admin/edit | Optional reprice audit table | Historical stability and guarded repricing |
| Cleanup and Testing | Architecture guard and final validation | Remove Tour legacy callers; update the two approved documents | None by default | Full parity, static guard, non-Tour regression |

## 16. Risk Register

| ID | Risk | Likelihood/impact | Mitigation | Gate/owner |
| --- | --- | --- | --- | --- |
| R1 | IDs 70-72 treated as USD 150,000 | High/critical | Explicit unresolved status; Finance mapping only | Before live quote/order activation; Finance |
| R2 | No currently ready Tour Price | Certain/high | Admin warning; approved mapping/new ready price before public enablement | Live activation; Product/Finance |
| R3 | Stale USD rate accepted | High/critical | `retrieved_at`, 24-hour resolver, fail closed, monitoring | Live/order pricing; Operations |
| R4 | Tax policy overlaps | Medium/high | Indexed locked overlap check plus resolver ambiguity failure | Foundation/activation; Engineering/Finance |
| R5 | Test touches active DB | High/critical | Explicit connection assertion; SQLite/disposable DB only | Every test; Engineering |
| R6 | Integer overflow | Medium/critical | Bounds, GCD reduction, checked primitives, boundary tests | Pricing Foundation |
| R7 | SQLite hides MariaDB issue | High/high | Disposable MariaDB DDL/concurrency suite | Foundation/Order Pricing |
| R8 | Snapshot mutated/deleted | Medium/critical | Restricted service/model/API, immutable versions, audit checks | Foundation through Historical Consumers |
| R9 | Dual-write totals diverge | Medium/high | One quote, one transaction, parity invariant/checksum | Order Pricing |
| R10 | Duplicate/concurrent order | Medium/high | Idempotency key, locking, unique guard, concurrency tests | Order Pricing |
| R11 | Historical consumer uses current rate | High/high | Consumer inventory, snapshot reader, static guards | Historical Consumers/Cleanup |
| R12 | Repricing desynchronizes invoice/payment | Medium/critical | Disabled until status and finance workflow approval | Historical Consumers |
| R13 | Orphan legacy prices reactivated | Medium/high | Parent eligibility and explicit remediation; no date-only backfill | Live Tour Pricing |
| R14 | Dirty worktree overwritten | High/high | Diff-by-file review; preserve unrelated changes | Every batch |
| R15 | Shared order/invoice regression | Medium/critical | Nullable additive fields, Tour branch only, non-Tour regression | Order Pricing through Cleanup |
| R16 | Discount unit inferred | Medium/high | Explicit type/currency/status mapping; identifiers only | Live Tour Pricing |
| R17 | Expected unavailable becomes HTTP 500 | Medium/medium | Typed exceptions and context-specific response mapping | Live Tour Pricing |
| R18 | Contract activated prematurely | Medium/critical | Phase 4 checklist and explicit three-owner sign-off | Phase 4 |

## 17. Phase 3 Implementation Authorization

### Decision

```text
GO FOR PHASE 3 — IMPLEMENTASI
```

Reasons:

- canonical currency, display unit, rate side/freshness, arithmetic, tax
  formula, markup structure, snapshot contract, and foundation boundaries are
  approved for implementation planning;
- MariaDB 10.4.32 schema constraints and an additive proposal are documented;
- unresolved IDs 70-72 can safely remain blocked and do not need guessed data
  for foundation code;
- PHP has 64-bit integers and `pdo_sqlite`; no new dependency is required;
- Pricing Foundation has no frontend, order, reservation, invoice, or production
  activation effect.

This GO is bounded by mandatory conditions:

1. Do not activate Live Tour Pricing or Order Pricing without their data gates.
2. Do not run any migration or DB-backed test until the effective database is
   asserted disposable and not `online_bali_kami_26`.
3. Do not backfill markup, validity, tax, or rate metadata in Pricing Foundation.
4. Keep all schema changes additive and nullable/conservatively unresolved.
5. Preserve unrelated existing worktree changes.
6. Keep unresolved production data blocked while completing the remaining
   implementation groups in Phase 3.
