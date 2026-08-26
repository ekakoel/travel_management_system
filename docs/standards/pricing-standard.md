# Pricing, Currency, Tax, and Financial Snapshot Standard

Status: Proposed
Updated: 2026-07-31
Owner: Product, Finance, and Engineering
Initial implementation scope: Public Tour Package
Decision state: Approved for implementation planning

The business decisions explicitly marked **Approved for implementation
planning** in this document are locked inputs for Phase 3 planning. The
document remains `Proposed`; it becomes `Active` only after all Phase 3
implementation acceptance criteria, activation approval, and
production-readiness checks pass.

## Current Implementation State

Phase 3 technical implementation completed on 2026-07-31 and Phase 4
production-readiness review has started. Technical completion does not
authorize production activation and does not change this contract to `Active`.

The implemented historical contract is:

- new Tour Package orders must have an immutable active pricing snapshot;
- Tour reservation, invoice, payment balance, email, PDF, report, and detail
  consumers read committed snapshot/invoice values, never current rate or tax;
- a pre-snapshot legacy Tour order may use only its committed invoice or stored
  order values through `OrderPricingSnapshotReader`;
- every legacy fallback is logged, and missing snapshot metadata on a new Tour
  order fails closed;
- the reader rejects non-Tour services, preserving Hotel, Transport, Activity,
  Wedding, Private Villa, and internal SPK pricing isolation.

Current Tour Package quote calculation was adjusted on 2026-08-24:

- price eligibility no longer requires `markup_source`, `markup_verified_at`,
  or `markup_verified_by`;
- USD conversion uses the stored USD sell rate from the database without the
  24-hour freshness gate;
- Tour Package tax uses the currently stored `taxes` row instead of requiring
  an active/effective/approved `tax_policies` row.

Current Tour Package USD rounding was adjusted on 2026-08-24 to match the
project price rounding behavior used by Activity: `ceiling-whole-usd-v1`.
`PricingEngine` now rounds the projected unit USD price up to the next whole
USD, calculates gross USD as rounded unit price times quantity, and rounds final
USD total up again after order-level discount. Unit/gross/final IDR price fields
are projected back from the rounded USD values with the same stored USD sell
rate; raw pre-rounding values remain in the quote breakdown for audit.

Admin readiness controls now collect explicit contract rate, USD markup amount,
source, typed `valid_from`/`valid_until`, reviewer, and readiness status. Ready
pax/date ranges are protected from overlap. No legacy markup is
automatically converted. The initial 1.5% Tour tax policy can only be created
through the guarded `pricing:activate-tour-tax` command with an explicit
effective timestamp and approver; production requires an additional explicit
acknowledgement.

Production remains blocked until Finance-approved price remediation, approved
tax effective time, fresh USD sell-rate operation/monitoring, migration and
frontend build runbooks, and Product/Finance/Engineering sign-off are complete.

## Executive Summary

The Phase 1 audit found that Tour Package pricing is distributed across models,
services, controllers, Blade, JavaScript, invoice generation, and reports. The
current path uses the legacy `usd_rates.rate`, performs intermediate `ceil()`,
does not store a complete financial snapshot, can display a discounted preview
that differs from Create Order, and can reconvert an existing order using a
current rate when generating an invoice.

This Proposed contract establishes:

- IDR integer rupiah as the canonical calculation and authoritative order
  currency;
- USD integer cents as the Tour Package display currency;
- `usd_rates.sell`, resolved only by `CurrencyRateResolver`, with a 24-hour
  freshness limit;
- a Tour Package-specific 1.5% exclusive tax on contract rate plus markup;
- explicit USD markup metadata, with unresolved legacy values blocked;
- post-tax authoritative discount selection, with add-ons deferred from the
  initial pilot;
- fixed-scale arithmetic with round half-up and no intermediate `ceil()`;
- one server-generated immutable quote used by preview and Create Order;
- a hybrid storage model using typed canonical order totals plus immutable,
  versioned pricing snapshots;
- snapshot-only historical reads and an explicit audited repricing design.

These decisions are **Approved for implementation planning**. Approval does not
authorize a production migration, data remediation write, or pricing
activation.

The recommended Phase 3 rollout is additive and Tour-only. It preserves legacy
columns for compatibility, introduces shadow fields and snapshot storage,
backfills only Finance-approved mappings, switches writes and readers in
stages, and defers legacy removal to a separately approved phase.

## 1. Purpose

This document proposes the canonical pricing contract and staged implementation
plan for Bali Kami Tour.

It is intended to:

- make IDR the authoritative calculation currency;
- make Tour Package USD prices reproducible and auditable;
- centralize exchange-rate and tax selection;
- eliminate financial calculations from controllers, models, Blade, JavaScript,
  email, PDF, and reports;
- ensure preview, order, reservation, invoice, payment, email, PDF, and report
  use the same pricing result;
- preserve historical transaction values after rate, tax, product price,
  promotion, or booking-code changes;
- provide a reusable foundation for future Accommodation, Public Transport, and
  Activity adoption without changing those services in the Tour Package pilot.

This is a Phase 2 planning document. It does not authorize PHP, Blade,
JavaScript, migration, scheduler, queue, cache, or live-data changes.

## 2. Scope

### 2.1 Included in the first implementation

- Public Tour Package listing and detail prices.
- Tour Package price eligibility and pax tiers.
- USD sell-rate resolution against IDR.
- Tour Package tax resolution.
- Promotion and booking-code resolution for Tour Package.
- Quote generation.
- Create Order pricing snapshot.
- Historical reads for order, reservation, invoice, payment, email, PDF,
  report, and admin detail.
- Design for explicit repricing without implementing repricing in Phase 2.

### 2.2 Reusable but not activated in the pilot

- Accommodation.
- Public Transport.
- Activity.
- Additional display currencies.
- Commercial whole-USD rounding.
- Tour Package add-ons.

### 2.3 Excluded

- Wedding and Wedding Package.
- Private Villa.
- DOKU payment architecture.
- Transport Management, SPK, driver operations, and internal transport
  fulfillment.
- Production data remediation.
- Scheduler or queue repair.
- Destructive schema conversion or legacy-column removal.

## 3. Terminology

| Term | Definition |
| --- | --- |
| Canonical amount | The authoritative amount used for financial calculation and storage. For the Tour Package pilot it is an integer IDR amount. |
| Base currency | `IDR`. |
| Display currency | `USD` for public Tour Package pricing. |
| Contract rate | Supplier/service unit cost stored in whole IDR. |
| Markup | Commercial uplift. New Tour Package markup is an explicit amount denominated in USD. |
| Pricing rate | The resolved `USD/IDR` sell-side value used by a quote. It represents IDR per USD 1. |
| Live quote | A server-generated price result using currently eligible product, rate, tax, discounts, and add-ons. |
| Snapshot | An immutable copy of the quote and its inputs stored with the transaction. |
| Repricing | An explicit authorized operation that replaces the current pricing snapshot after a pricing-relevant order change. |
| Minor unit | Integer smallest display unit: IDR rupiah for IDR and cents for USD. |
| Fixed discount | A monetary discount with an explicit currency. |
| Percentage discount | A rate applied to an explicitly defined eligible base. |
| Exclusive tax | Tax added after the taxable subtotal. |
| Fresh rate | A valid rate whose age is at most the service policy maximum. |
| Legacy alias | `usd_rates.rate`; retained for compatibility but forbidden as a direct source for new pricing. |

## 4. Canonical Currency

Decision status: **Approved for implementation planning**.

- IDR is the canonical calculation currency.
- Contract rate, converted markup, taxable subtotal, tax, discounts, add-ons,
  and final total must each have an authoritative IDR representation.
- IDR is represented as integer rupiah.
- Authoritative new Tour Package order value is `final_total_idr`.
- The legacy USD-oriented `orders.final_price` cannot be treated as the new
  canonical field.
- No formatter, view, JavaScript code, or API serializer may change a canonical
  amount.

## 5. Display Currency

Decision status: **Approved for implementation planning**.

- Tour Package frontend display currency is USD.
- USD is represented internally as integer cents.
- Every displayed USD value must come from the same `PricingQuote` or stored
  `PricingSnapshot` as the related IDR value.
- Tour Package USD values display exactly two decimal places.
- Listing/detail use a live quote or a server-produced quote summary.
- Transactional pages use a stored snapshot.
- A raw IDR contract rate must never be labeled or formatted as USD.
- A missing display conversion is an operational pricing failure, not a reason
  to fall back to IDR under a USD label.

## 6. Exchange-Rate Policy

### 6.1 Final business rule

- Customer pricing uses `usd_rates.sell`.
- `usd_rates.rate` is a legacy alias only.
- New pricing code, including `CurrencyRateResolver`, must not read
  `usd_rates.rate`.
- New pricing code must never read `buy`, `sell`, or `rate` directly outside
  `CurrencyRateResolver`.
- The Tour Package pair is represented as `USD/IDR`, meaning IDR per USD 1.

Decision status: **Approved for implementation planning**.

### 6.2 Resolver result

`CurrencyRateResolver` must return one immutable resolved-rate object containing:

```text
rate_id
pair                 = USD/IDR
side                 = sell
value
source
updated_at
retrieved_at
max_age_seconds      = 86400
age_seconds
is_fresh
```

`source` must distinguish at least:

- external API update;
- authorized manual update;
- approved migration/backfill source.

If the current schema cannot distinguish those sources, the implementation
must add explicit metadata rather than infer it from the value.

### 6.3 Forbidden behavior

- Direct `UsdRates::...` access from a pricing consumer.
- Direct `$rate->sell`, `$rate->buy`, or `$rate->rate` access outside the
  resolver and administration/update boundary.
- Reading `usd_rates.rate` inside `CurrencyRateResolver`, including as a
  fallback.
- Hardcoded exchange rates.
- Rate fallback to zero.
- Returning a zero price for a rate failure.
- Falling back from `sell` to `rate` or `buy`.
- Selecting a row by hardcoded numeric ID.
- Silently choosing one of several matching rows.

### 6.4 Failure behavior

The resolver must reject:

- missing currency pair;
- duplicate or ambiguous active pair;
- null or malformed value;
- zero or negative value;
- unsupported rate side;
- missing update timestamp;
- update timestamp in the future beyond an approved clock-skew tolerance;
- stale rate.

The caller receives a typed `PricingException`; it must not receive a nullable
rate and continue calculating.

Failure presentation is context-specific:

- public listing/detail must not return HTTP 500 for a normal pricing
  availability failure and must show `price temporarily unavailable`;
- public listing/detail must not display a stale or guessed numeric price;
- quote, checkout, and Create Order fail closed and must not create a
  transaction;
- historical order, reservation, invoice, payment, email, PDF, report, and
  admin detail continue from the stored snapshot.

## 7. Rate Freshness

- Tour Package maximum rate age is 24 hours (`86400` seconds).
- Freshness is evaluated at quote calculation time.
- Every newly retrieved rate must persist `retrieved_at`.
- During the additive compatibility transition only, the timestamp is:

```text
freshness_timestamp = retrieved_at ?? updated_at
```

- After the rate metadata migration/backfill is complete and the transition is
  explicitly closed, freshness uses `retrieved_at` only.
- A rate is fresh only when:

```text
0 <= calculated_at - freshness_timestamp <= 86400 seconds
```

- Listing/detail must show an unavailable-price operational state when the rate
  is stale.
- Create Order must reject a stale quote or stale rate.
- A quote cannot be reused after its own validity window expires.
- The server must revalidate freshness during Create Order even if the
  frontend previously received a valid quote.
- Stale rate failures must be logged with rate ID, pair, side, age, maximum
  age, service, and request correlation ID.

### 7.1 Scheduler and job monitoring plan

Phase 3 must plan and verify:

- Laravel scheduler registration and actual scheduler execution;
- queue connection and active worker status, because the hourly rate update is
  currently queued;
- last successful external fetch;
- last successful database update per pair;
- consecutive job failures;
- API error status and timeout;
- stale-rate alert before and after the 24-hour limit;
- cache invalidation after a successful manual or automated update;
- alert ownership and operational response.

Acceptance criteria:

- a successful hourly update advances the timestamp and invalidates the
  resolver cache;
- a failed update preserves the last known row but does not extend freshness;
- a rate older than 24 hours is rejected;
- monitoring detects scheduler-not-running and queue-not-running separately;
- logs never expose the exchange-rate API credential.

Repairing the scheduler, queue, credential storage, or job is not part of
Phase 2.

## 8. Tax Policy

### 8.1 Tour Package pilot contract

- Tour Package tax is `1.5%`.
- Tax is exclusive.
- The taxable amount is contract rate plus converted markup.
- Tax is calculated in canonical IDR.
- This rule is not automatically valid for Accommodation, Public Transport, or
  Activity.

Decision status: **Approved for implementation planning**.

The versioned policy is:

```text
service          = Tour Package
percentage       = 1.5%
calculation_type = exclusive
taxable_base     = contract rate + markup
effective_from   = deployment-approved activation timestamp
effective_until  = null
```

The exact `effective_from` is a deployment/configuration decision and must not
be guessed from document, migration, or deploy timestamps.

```text
taxable_subtotal_idr = contract_rate_idr + markup_idr
tax_amount_idr       = round_half_up(
    taxable_subtotal_idr * tax_percentage / 100
)
```

### 8.2 Tax resolver

`TaxResolver` must select exactly one tax policy using:

```text
service
calculation timestamp
status
effective_from
effective_until
```

It must return:

```text
tax_id
tax_name
service_scope
percentage
calculation_type = exclusive
effective_from
effective_until
```

It must reject:

- missing tax;
- inactive tax;
- overlapping effective policies;
- malformed or negative percentage;
- a policy that does not include Tour Package;
- a timestamp outside the effective period.

Hardcoded `Tax::find(1)` is forbidden in the new pricing path.

### 8.3 Versioning plan

The current `taxes` schema does not contain service scope, status, or effective
period. Phase 3 uses an additive versioned policy structure that records those
fields.
An existing tax row may be mapped to the Tour Package policy only after its
effective start and business ownership are explicitly approved.

The write path must prevent overlapping active policies for the same service
and calculation timestamp. MariaDB 10.4 has no exclusion constraint for time
ranges, so Phase 3 must combine an indexed overlap query, transaction, and
locking; `TaxResolver` must still fail closed if overlap exists.

## 9. Markup Policy

### 9.1 New write contract

- `contract_rate` is whole IDR.
- Tour Package markup is USD.
- Markup must be represented by amount and currency.
- A markup value must never have its currency inferred from its magnitude.
- Negative markup is rejected unless a future explicit discount policy allows
  it as a different component.

### 9.2 Schema options

| Option | Benefit | Risk |
| --- | --- | --- |
| A. Keep `markup`, declare all new values USD | Smallest schema change and easiest legacy compatibility. | Unit remains implicit; invalid legacy values remain indistinguishable; unsafe for multi-service rollout. |
| B. Add `markup_amount` and `markup_currency` | Explicit, auditable, supports future service currencies, prevents magnitude inference. | Requires additive schema, mapping, compatibility reads, and staged rollout. |

Recommendation: **Option B**.

Proposed Tour Price fields:

```text
markup_amount
markup_currency
markup_source
markup_verified_at
markup_verified_by
pricing_data_status
valid_from
```

For the Tour Package pilot:

```text
markup_currency = USD
```

The legacy `markup` field remains read-only during transition. A compatibility
reader may use it only for a row that has an approved mapping record. There is
no heuristic runtime fallback.

Decision status: **Approved for implementation planning**.

### 9.3 Known invalid/legacy data

Tour Price IDs 70-72 currently contain `markup = 150000`. They are considered
likely to represent IDR 150,000, but that observation is not an approved
conversion. They must have `pricing_data_status = unresolved` (or the
schema-equivalent state), cannot produce a quote or order, remain visible to
administrators with a warning, and wait for an approved Finance mapping. Phase
2A does not change those rows.

Required remediation mapping columns:

```text
tour_price_id
legacy_markup
legacy_assumed_currency
approved_markup_amount
approved_markup_currency
approved_by
approved_at
notes
```

Approval fields must never be populated by a magnitude heuristic.

## 10. Discount Policy

- Promotion and booking-code discounts may be fixed or percentage based.
- Fixed discounts require explicit currency.
- Discounts are applied after tax.
- Customer input may contain only discount identifiers/codes.
- Request-provided discount amounts, percentages, currencies, eligibility, or
  totals are never authoritative.
- Promotion and booking code do not stack by default in the pilot.
- If both candidates are eligible, the server calculates both independently
  against the same gross total after tax and selects the candidate with the
  greatest financial benefit to the customer.
- The breakdown stores both candidates and identifies the selected discount;
  only the selected discount reduces the total.

Decision status: **Approved for implementation planning**.

Canonical order:

```text
contract rate
+ markup
= subtotal

subtotal
+ exclusive tax
= gross total

gross total
- selected promotion or booking-code discount
+ add-ons
= final total
```

### 10.1 Fixed discount

Required metadata:

```text
type = fixed
amount
currency
service_scope
valid_from
valid_until
usage_limit
stacking_group
```

If fixed USD, it is converted to canonical IDR with the quote rate and half-up
rounding. If fixed IDR, it is already canonical.

### 10.2 Percentage discount

Required metadata:

```text
type = percentage
percentage
eligible_base
service_scope
valid_from
valid_until
usage_limit
stacking_group
maximum_discount
```

For the pilot, the percentage eligible base is the gross Tour total after
exclusive tax. Add-ons are outside the initial Phase 3 pilot and therefore are
not part of that eligible base.

### 10.3 Eligibility

`TourPackagePricingService` must validate:

- active status;
- validity period;
- Tour Package scope and applicable tour;
- authenticated actor/agent ownership where applicable;
- usage limits and prior use;
- pax/quantity requirements;
- minimum transaction value;
- stacking compatibility;
- fixed discount currency;
- percentage base and optional maximum.

### 10.4 Stacking

The generic engine can evaluate resolved candidates, but selection belongs to
the Tour Package domain service. Promotion and booking code do not stack by
default; the candidate producing the larger canonical IDR discount is selected.
An exact tie must be resolved deterministically by a versioned policy (promotion
first for the pilot) and recorded in the breakdown.

## 11. Add-on Policy

Add-ons are explicitly out of scope for the initial Phase 3 Tour pilot. The
following rules are reserved for a later approved batch and must not be
activated by all Phase 3 implementation acceptance criteria:

- Add-ons are resolved server-side by identifier.
- Add-on amount, currency, quantity rule, service scope, status, and validity
  must be explicit.
- Request-provided add-on prices and totals are ignored.
- Add-ons are added after discounts under the approved Tour Package order.
- Every add-on produces a snapshot line containing:

```text
addon_id
name
quantity
unit_amount
currency
unit_amount_idr
total_idr
total_usd
source_version
```

- Tax on add-ons is not implied by the Tour Package base tax policy. An add-on
  may only be taxed when an explicit add-on tax rule is approved.
- No existing Accommodation optional-rate or airport-shuttle behavior is
  automatically adopted by Tour Package.

## 12. Precision

Decision status: **Approved for implementation planning**.

- PHP binary float is forbidden for pricing calculation.
- IDR uses integer rupiah.
- USD uses integer cents.
- Exchange rate uses fixed-scale decimal or scaled integer.
- Tax and percentage discounts use fixed-scale decimal or scaled integer.
- Database conversion must preserve the documented scale.
- JSON monetary fields use integer minor units or decimal strings, never JSON
  floating-point numbers.

Recommended scales:

```text
IDR amount             integer rupiah
USD amount             integer cents
rate value             decimal(20, 6) or integer scaled by 10^6
tax percentage         decimal(9, 6) or integer scaled by 10^6
discount percentage    decimal(9, 6) or integer scaled by 10^6
```

Implementation may use integer arithmetic throughout. If an arbitrary-precision
facility is needed, its availability and project compatibility must be
confirmed before implementation; Phase 2 does not approve a new dependency.

## 13. Rounding

Decision status: **Approved for implementation planning**.

- Internal component conversion uses fixed-scale round half-up.
- Commercial Tour Package price boundaries use `ceiling-whole-usd-v1`.
- Unit USD is rounded up to the next whole USD after contract, markup, and tax.
- Gross USD is rounded unit USD multiplied by quantity.
- Final USD is rounded up again after order-level discount.
- USD still displays two decimal places, but whole-dollar rounded values end in
  `.00`.

Examples:

```text
90.001 -> 91.00
90.750 -> 91.00
91.000 -> 91.00
```

### 13.1 Rounding boundaries

Rounding occurs only at documented currency/percentage boundaries:

1. USD markup cents converted to whole IDR.
2. Tax result converted to whole IDR.
3. Fixed USD discount converted to whole IDR.
4. Percentage discount result converted to whole IDR.
5. Add-on foreign currency converted to whole IDR.
6. Canonical IDR component projected to USD cents.

Multiplying an already rounded whole-IDR unit amount by integer quantity is
exact. No formatting-stage rounding changes stored values.

Recommended scaled formulas:

```text
markup_idr = round_half_up(
    markup_usd_cents * rate_scaled
    / (100 * rate_scale)
)

tax_amount_idr = round_half_up(
    subtotal_idr * tax_percentage_scaled
    / (100 * percentage_scale)
)

unit_price_usd_cents = round_half_up(
    unit_price_idr * 100 * rate_scale
    / rate_scaled
)
```

## 14. Canonical Formula

### 14.1 Unit price

```text
markup_idr
    = markup_usd * usd_sell_rate

subtotal_idr
    = contract_rate_idr + markup_idr

tax_amount_idr
    = subtotal_idr * tax_percentage / 100

unit_price_idr
    = subtotal_idr + tax_amount_idr

unit_price_usd
    = unit_price_idr / usd_sell_rate
```

Every division or percentage result follows Section 13.

### 14.2 Quantity

```text
gross_total_idr
    = unit_price_idr * quantity

gross_total_usd
    = unit_price_usd * quantity
```

`quantity` for Tour Package is pax count after tier eligibility is resolved.

### 14.3 Adjustments

```text
discount_total_idr
    = selected_discount_idr

selected_discount_idr
    = max(
        eligible_promotion_discount_idr,
        eligible_booking_code_discount_idr
    )

final_total_idr
    = max(
        gross_total_idr
        - discount_total_idr
        + addon_total_idr,
        0
    )

final_total_usd
    = project final_total_idr through the same quote rate
```

All component USD fields are projections from canonical IDR using the same
snapshot rate. Any one-cent reconciliation caused by component rounding must
be represented explicitly in the breakdown, never silently assigned to an
unrelated component.

### 14.4 Required example

Input:

```text
contract_rate_idr = 1,000,000
markup_usd        = 20.00
usd_sell_rate     = 16,000.000000
tax               = 10%
quantity          = 1
```

Expected:

```text
markup_idr        = 320,000
subtotal_idr      = 1,320,000
tax_amount_idr    = 132,000
unit_price_idr    = 1,452,000
unit_price_usd    = 90.75
gross_total_idr   = 1,452,000
gross_total_usd   = 90.75
```

## 15. Pricing Quote Contract

`PricingQuote` is immutable. It is created only after product, rate, tax,
discount, add-on, and actor eligibility succeed.

Minimum fields:

```text
pricing_version
service
service_id
price_id

base_currency
display_currency

contract_rate_idr
markup_amount
markup_currency
markup_idr

subtotal_idr

tax_id
tax_name
tax_percentage
tax_amount_idr

rate_id
rate_pair
rate_side
rate_value
rate_source
rate_updated_at
rate_max_age
rate_is_fresh

quantity
unit_price_idr
unit_price_usd

gross_total_idr
gross_total_usd

promotion_discount
booking_code_discount
discount_candidates
selected_discount
discount_total_idr
discount_total_usd

addon_total_idr
addon_total_usd

final_total_idr
final_total_usd

rounding_policy
calculated_at
```

Additional recommended fields:

```text
quote_id
quote_expires_at
actor_id
service_date
price_valid_until
tax_effective_from
tax_effective_until
rate_retrieved_at
rate_age_seconds
discount_lines
addon_lines
reconciliation_idr
reconciliation_usd_cents
input_fingerprint
```

### 15.1 Required snapshot fields

All listed quote fields must be stored except fields that are purely
transport-level identifiers for an unaccepted quote. At minimum the snapshot
must persist:

- every product/source ID;
- every component amount and currency;
- resolved rate metadata and freshness evidence;
- resolved tax metadata;
- discount and add-on lines;
- quantity and service date;
- IDR and USD unit/gross/final amounts;
- pricing and rounding versions;
- calculation timestamp and actor;
- input fingerprint/checksum.

### 15.2 Safely derivable fields

The following can be recalculated for integrity verification but must still be
stored to make the transaction self-contained:

- `subtotal_idr`;
- `discount_total_idr`;
- `addon_total_idr`;
- `gross_total_idr`;
- `gross_total_usd`;
- `final_total_idr`;
- `final_total_usd`;
- `rate_is_fresh` as it was at calculation time.

They must not be re-derived for customer-facing historical display using
current database records. Re-derivation is verification only.

## 16. Snapshot Contract and Storage Plan

### 16.1 Existing storage limitations

The current shared `orders` table stores many financial values as text,
including `price_pax`, `normal_price`, `price_total`, discounts,
`order_tax`, and `final_price`. It also stores rate values but not the complete
rate, tax, markup, discount, and rounding policy used by the transaction.

`invoice_admins` stores currency totals and rates as strings. Invoice creation
currently converts the stored USD total using current rates. That behavior is
incompatible with this contract.

### 16.2 Compared approaches

| Approach | Query/reporting | Auditability | Compatibility | Recommendation |
| --- | --- | --- | --- | --- |
| Many snapshot columns on `orders` | Strong for direct SQL. | Weak for nested discount/add-on lines and future policy fields. | High blast radius on a shared table. | Do not use alone. |
| One `orders.pricing_snapshot` JSON | Flexible and low table count. | Good if immutable and validated. | Shared row becomes large; weak indexing; MariaDB 10.4 JSON is stored as text-compatible data. | Do not use alone. |
| Dedicated `order_pricing_snapshots` table | Strong isolation, supports multiple repricing versions and immutable history. | Strong. | Requires joins and staged current-snapshot link. | Recommended foundation. |
| Typed main totals plus dedicated immutable breakdown | Strong reporting and auditability. | Strongest, but requires parity invariants. | Supports staged legacy dual-write. | **Recommended final approach.** |

### 16.3 Recommendation

Use a hybrid design:

1. Add typed current canonical totals and a nullable current-snapshot reference
   to `orders`.
2. Add an immutable, versioned `order_pricing_snapshots` table with searchable
   summary columns and a validated JSON/long-text breakdown.
3. During compatibility rollout, dual-write legacy USD fields from the same
   quote; they are compatibility mirrors, not sources of truth.
4. Historical consumers read the current linked snapshot. Previous snapshots
   remain immutable for future repricing audit.

Proposed `orders` additions:

```text
pricing_version              varchar
pricing_snapshot_id          nullable bigint
base_currency                char(3)
display_currency             char(3)
final_total_idr              unsigned bigint
final_total_usd_minor         unsigned bigint
pricing_calculated_at         timestamp
```

Proposed `order_pricing_snapshots` summary:

```text
id
order_id
snapshot_sequence
pricing_version
service
service_id
price_id

base_currency
display_currency
quantity

contract_rate_idr
markup_amount_minor
markup_currency
markup_idr
subtotal_idr
tax_id
tax_percentage_scaled
tax_amount_idr

rate_id
rate_pair
rate_side
rate_value_decimal
rate_source
rate_updated_at
rate_max_age_seconds

gross_total_idr
gross_total_usd_minor
discount_total_idr
discount_total_usd_minor
addon_total_idr
addon_total_usd_minor
final_total_idr
final_total_usd_minor

rounding_policy
calculated_at
calculated_by
reason
input_fingerprint
snapshot_checksum
breakdown
created_at
```

Recommended constraints/indexes after data compatibility is verified:

- unique `(order_id, snapshot_sequence)`;
- index `(service, calculated_at)`;
- index `(rate_id, calculated_at)`;
- index `(tax_id, calculated_at)`;
- index `final_total_idr` only when required by finance reports;
- non-negative checks for monetary values where MariaDB behavior is verified;
- `JSON_VALID(breakdown)` check where compatible;
- no application update/delete path for a committed snapshot.

`orders.pricing_snapshot_id` points to the current snapshot. The circular
foreign-key relationship, if used, must be added in a separate additive step
after both structures exist and data validity is proven.

### 16.4 Example breakdown

All monetary JSON values are integer minor units or decimal strings.

```json
{
  "pricing_version": "tour-package-v1",
  "service": "Tour Package",
  "service_id": 10,
  "price_id": 70,
  "currencies": {
    "base": "IDR",
    "display": "USD"
  },
  "rate": {
    "id": 1,
    "pair": "USD/IDR",
    "side": "sell",
    "value": "16000.000000",
    "source": "external_api",
    "updated_at": "2026-07-29T10:00:00+08:00",
    "max_age_seconds": 86400,
    "was_fresh": true
  },
  "tax": {
    "id": 1,
    "name": "Tour Package Tax",
    "percentage": "10.000000",
    "type": "exclusive",
    "amount_idr": 132000
  },
  "unit": {
    "contract_rate_idr": 1000000,
    "markup_amount_minor": 2000,
    "markup_currency": "USD",
    "markup_idr": 320000,
    "subtotal_idr": 1320000,
    "price_idr": 1452000,
    "price_usd_minor": 9075
  },
  "quantity": 1,
  "gross": {
    "idr": 1452000,
    "usd_minor": 9075
  },
  "discounts": [],
  "addons": [],
  "final": {
    "idr": 1452000,
    "usd_minor": 9075
  },
  "rounding_policy": "half_up_v1",
  "calculated_at": "2026-07-29T10:05:00+08:00"
}
```

## 17. Historical Transaction Policy

Decision status: **Approved for implementation planning**.

- Order, reservation, invoice, payment, email, PDF, report, and admin order
  detail use the active order pricing snapshot.
- Current exchange rate, tax, product price, promotion, booking code, or add-on
  records must never alter an existing snapshot.
- Invoice IDR uses snapshot `final_total_idr`.
- Invoice USD uses snapshot `final_total_usd_minor`.
- Invoice totals are copied from the order snapshot and are never converted
  with a current rate.
- Reservation must not query current pricing sources.
- Payment pages compare receipts/balance against snapshotted invoice amounts.
- Historical API responses serialize snapshot values.
- A non-pricing edit must retain the same `pricing_snapshot_id` and checksum.
- Missing snapshot on a legacy order is handled by a compatibility reader and
  explicit legacy marker; it must not trigger silent recalculation.

## 18. Repricing Policy

Repricing is not implemented in Phase 2.

Only `Super Admin` or a user granted `orders.reprice` may invoke repricing.
Customers cannot invoke it directly. Decision status: **Approved for
implementation planning**.

Pricing-relevant changes include:

- quantity/pax;
- tier;
- travel date;
- service option;
- promotion;
- booking code;
- pricing add-on.

Non-pricing changes include, unless future service rules say otherwise:

- lead guest contact correction;
- note;
- pickup text that does not select a priced option;
- translation/display preference.

An explicit repricing operation must:

1. authorize the actor;
2. validate that the order lifecycle permits repricing;
3. require a non-empty reason;
4. load and lock the order/current snapshot;
5. generate a new live quote;
6. store a new immutable snapshot;
7. preserve the old snapshot;
8. atomically switch `orders.pricing_snapshot_id` and typed totals;
9. write an audit log containing `reason`, `actor`, `old_snapshot_id`,
   `new_snapshot_id`, `changed_components`, and `created_at`;
10. update dependent invoice state only under a separately approved financial
    workflow.

The operation must be idempotent and reject stale concurrent updates.
Non-pricing edits must not change the snapshot reference or typed totals.

## 19. Error Handling

Use typed `PricingException` error codes. Expected codes include:

```text
PRICING_RATE_MISSING
PRICING_RATE_INVALID
PRICING_RATE_AMBIGUOUS
PRICING_RATE_STALE
PRICING_TAX_MISSING
PRICING_TAX_INVALID
PRICING_TAX_AMBIGUOUS
PRICING_MARKUP_UNRESOLVED
PRICING_PRICE_INACTIVE
PRICING_PRICE_DELETED
PRICING_PRICE_EXPIRED
PRICING_PAX_TIER_NOT_FOUND
PRICING_PROMOTION_INVALID
PRICING_BOOKING_CODE_INVALID
PRICING_DISCOUNT_STACK_INVALID
PRICING_ADDON_INVALID
PRICING_QUOTE_EXPIRED
PRICING_QUOTE_MISMATCH
PRICING_SNAPSHOT_MISSING
PRICING_REPRICE_NOT_ALLOWED
```

Operational failures must:

- fail closed;
- produce a user-safe translated message;
- preserve a correlation ID;
- write structured context without secrets or unnecessary personal data;
- never display a zero or guessed price;
- never expose exception internals to the customer.

Validation/eligibility failures are normally HTTP 422. Authorization remains
403/404 according to the route policy. Infrastructure failures may return a
service-unavailable response.

## 20. Cache Strategy

- Only resolvers may cache rate and tax source records.
- Calculation results are immutable values and must not mutate cached models.
- Proposed rate cache key:

```text
pricing:rate:USD-IDR:sell:v1
```

- Proposed tax cache key:

```text
pricing:tax:Tour-Package:<effective-version>
```

- Cache TTL must not exceed the remaining rate freshness window.
- Resolver revalidates value, timestamp, and freshness after every cache hit.
- Manual and automated rate writes invalidate all related resolver keys only
  after the database transaction succeeds.
- Tax activation/version changes invalidate affected service keys after
  commit.
- Short negative caching may protect the database from repeated failures, but
  it must never convert failure into a valid quote.
- Existing inconsistent keys (`usd_rate`, `usd_rates`, `tax`, `tax_1`, and
  service-specific variants) require an inventory and staged retirement.

## 21. Logging and Observability

Structured events:

```text
pricing.quote.succeeded
pricing.quote.failed
pricing.rate.stale
pricing.rate.update.succeeded
pricing.rate.update.failed
pricing.tax.resolve.failed
pricing.order.snapshot.created
pricing.order.snapshot.mismatch
pricing.order.repriced
pricing.cache.invalidated
```

Required safe context:

- correlation/quote ID;
- pricing version;
- service and service ID;
- price ID;
- actor ID where authenticated;
- rate ID/pair/side/age;
- tax ID;
- error code;
- calculation duration;
- snapshot ID/checksum after commit.

Do not log:

- API credentials;
- full receipt/payment data;
- unnecessary guest identity data;
- raw booking-code secrets when a one-way identifier is sufficient.

Metrics/alerts:

- quote success/failure count by error code;
- stale-rate age;
- last successful rate update;
- duplicate/ambiguous rate or tax count;
- snapshot parity failures;
- preview-to-order quote mismatch;
- cache hit/miss;
- pricing calculation latency.

## 22. Authorization

- Public/authenticated quote access must preserve the existing Tour Package
  visibility and account eligibility rules.
- Quote access does not authorize Create Order.
- Create Order derives actor and sales-agent ownership from authenticated
  server context.
- Customer request cannot select rate ID, tax ID, rate side, markup unit,
  discount amount, or final total.
- Promotion and booking-code eligibility is checked against the actor and
  service.
- Admin price maintenance requires existing Tour administration permission.
- Markup remediation requires Finance-approved mapping and an auditable actor.
- Repricing requires a dedicated authorization decision; UI visibility is not
  authorization.
- Snapshot read access follows order ownership/service scope for customers and
  assigned/authorized policy for staff.

## 23. Service-Specific Extension

### 23.1 Generic `PricingEngine`

The generic engine may:

- convert `Money` using an already-resolved rate;
- add/subtract compatible money;
- apply a resolved percentage;
- apply half-up rounding;
- calculate totals from resolved adjustments;
- assemble a generic quote calculation result.

It must not know:

- Tour models or tables;
- active/deleted price eligibility;
- pax tiers;
- travel dates;
- promotions or booking-code ownership;
- order status;
- Laravel request/session/authentication;
- database selection rules.

### 23.2 `TourPackagePricingService`

The Tour Package domain service owns:

- tour ownership and active state;
- active/non-deleted price selection;
- travel-date validity;
- pax tier;
- quantity;
- promotion;
- booking code;
- add-on eligibility;
- resolver invocation;
- generic engine invocation;
- quote version and service metadata.

Accommodation, Public Transport, and Activity require their own contracts and
domain services before using the generic engine.

## 24. Proposed Component Architecture

### 24.1 Text diagram

```text
HTTP / CLI consumer
        |
        v
TourPackagePricingService
  |       |         |
  |       |         +--> product/tier/discount/add-on repositories
  |       |
  |       +------------> TaxResolver
  |
  +--------------------> CurrencyRateResolver
        |
        v
PricingEngine
  |        |
  |        +-----------> Money
  |
  +--------------------> PricingQuote
                              |
               +--------------+---------------+
               |                              |
               v                              v
        PricingSnapshot               MoneyFormatter
               |                              |
               v                              v
      order snapshot storage           Blade/API/email/PDF
```

### 24.2 Component responsibilities

#### `App\Services\Pricing\PricingEngine`

- Constructor: rounding/scaling policy value object or configuration.
- Input: resolved canonical `Money` components, fixed-scale rate/tax, quantity,
  resolved discount lines, resolved add-on lines.
- Output: generic calculation result consumed by `PricingQuote`.
- Responsibility: arithmetic only.
- Forbidden: Eloquent, cache, auth, request, service eligibility, formatting.
- Failure: throw `PricingException` for currency mismatch, invalid scale,
  negative forbidden values, or overflow.
- Cache: none.
- Tests: pure unit/property tests and rounding-boundary tests.

#### `App\Services\Pricing\CurrencyRateResolver`

- Constructor: rate repository/model boundary, cache repository, clock, logger.
- Input: base currency, display currency, required side, calculation time,
  maximum age.
- Output: immutable resolved rate metadata.
- Responsibility: exact selection, validation, freshness, cache.
- Forbidden: price arithmetic, fallback side, formatting, product eligibility.
- Failure: typed missing/invalid/ambiguous/stale exception.
- Cache: versioned key; TTL capped by remaining freshness; invalidate after
  committed update.
- Tests: resolver/database integration, cache, clock, stale/future timestamps.

#### `App\Services\Pricing\TaxResolver`

- Constructor: tax policy repository/model boundary, cache repository, clock,
  logger.
- Input: service and calculation timestamp.
- Output: one immutable resolved tax policy.
- Responsibility: service scope, effective period, status, ambiguity checks.
- Forbidden: hardcoded tax ID, product eligibility, price arithmetic.
- Failure: typed missing/invalid/ambiguous exception.
- Cache: service and effective-version key.
- Tests: missing, inactive, overlap, boundary dates, service isolation.

#### `App\Services\Tours\TourPackagePricingService`

- Constructor: `CurrencyRateResolver`, `TaxResolver`, `PricingEngine`, Tour
  price/product access, promotion/booking-code/add-on access, clock, logger.
- Input: actor, tour ID, service date, pax, optional preferred price ID,
  promotion ID, booking code, add-on selections.
- Output: immutable `PricingQuote`.
- Responsibility: Tour-specific eligibility and quote assembly.
- Forbidden: HTTP response, Blade formatting, invoice generation, direct rate
  or tax column reads.
- Failure: typed eligibility or pricing exception.
- Cache: may reuse resolver caches; must not cache actor-specific discount
  eligibility as a globally shared quote.
- Tests: feature/service integration and tampering tests.

The existing `App\Services\Tours\TourPricingService` should be retained as a
compatibility adapter during rollout or evolved deliberately; removal/rename
must not happen before references are migrated.

#### `App\Data\Pricing\PricingQuote`

- Constructor: all required scalar/value-object fields.
- Input: fully resolved, validated calculation components.
- Output: immutable getters, safe array serialization, snapshot conversion.
- Responsibility: represent a live quote.
- Forbidden: database reads, recalculation, mutation, formatting.
- Failure: reject incomplete/inconsistent construction.
- Cache: none.
- Tests: immutability, serialization, invariant validation.

For the pilot, `PricingQuote` is also the external pricing result contract.
A second mutable `PricingResult` DTO must not be introduced because it would
create two competing representations. If a future internal arithmetic result
type is necessary, it must remain private to `PricingEngine` and be converted
to one public `PricingQuote`.

#### `App\ValueObjects\Money`

- Constructor: integer minor amount and ISO currency.
- Input/output: immutable money operations.
- Responsibility: currency-safe addition/subtraction/comparison and integer
  multiplication.
- Forbidden: database, localization, rate selection, binary floats.
- Failure: currency mismatch, invalid currency, overflow.
- Cache: none.
- Tests: pure unit/property tests.

#### `App\Support\MoneyFormatter`

- Constructor: locale/format policy only.
- Input: `Money`.
- Output: display string.
- Responsibility: symbol, grouping, decimal places.
- Forbidden: rate conversion, tax, discount, total calculation.
- Failure: reject unsupported currency/locale.
- Cache: optional formatter metadata only.
- Tests: USD two-decimal and IDR zero-decimal formatting.

#### `App\Data\Pricing\PricingSnapshot`

- Constructor: committed quote fields plus order/snapshot metadata.
- Input: `PricingQuote`, order ID, actor, reason, sequence.
- Output: immutable persistence payload and historical read model.
- Responsibility: preserve transaction facts and checksums.
- Forbidden: current rate/tax/product queries or recalculation for display.
- Failure: reject missing required snapshot fields or checksum mismatch.
- Cache: normal order-detail caching may include it, keyed by snapshot ID.
- Tests: round-trip serialization, checksum, old/new snapshot preservation.

#### `App\Exceptions\PricingException`

- Constructor: stable error code, safe message key, structured context,
  previous exception.
- Input/output: typed pricing failure.
- Responsibility: distinguish operational, validation, integrity, and
  authorization-adjacent failures.
- Forbidden: customer PII, secrets, pre-rendered HTML.
- Cache: none.
- Tests: HTTP/CLI mapping and safe logging.

## 25. Backward Compatibility

- Legacy columns remain during the pilot rollout.
- Existing non-Tour services continue their current pricing behavior.
- Tour Package legacy readers use compatibility logic until each integration
  point is switched.
- New Tour Package writes require explicit markup currency and a valid
  snapshot.
- `usd_rates.rate` remains maintained as an administration compatibility alias
  if existing non-pilot consumers need it, but new pricing never reads it.
- Old orders without snapshots are marked `legacy`; they are not silently
  reconstructed with current rates/taxes.
- Because the audited database currently has no live Tour Package orders,
  initial Tour snapshot rollout has lower Tour historical risk, but code must
  still support imported/other-environment legacy orders.
- API and UI response fields may retain legacy names temporarily, but their
  source must be the quote/snapshot and deprecation must be documented.

## 26. Additive Migration Plan

No migration is created or run in Phase 2.

### Stage 0 - Approval and backup design

- Approve this contract.
- Record MariaDB version and environment differences.
- Prepare backup/restore procedure.
- Confirm disposable test database.
- Approve data mappings, especially Tour Price IDs 70-72.
- Inventory all live rows and invalid formats read-only.

### Stage 1 - Add shadow schema

Separate additive migrations should:

1. add explicit Tour markup fields without changing/removing `markup`;
2. add a canonical date shadow field without changing/removing
   `expired_date`;
3. add explicit tax policy/version fields or a new service-tax policy table;
4. add rate source/retrieved metadata if needed;
5. create `order_pricing_snapshots`;
6. add nullable canonical total/current snapshot fields to `orders`;
7. add indexes only after cardinality and query plans are reviewed.

Each migration must have a safe `down()` that removes only newly added,
unpopulated structures when feasible. Rollback after data is populated requires
an explicit compatibility procedure rather than blind column deletion.

### Stage 2 - Verified backfill

- Export/backup affected rows.
- Populate shadow fields only from approved mappings.
- Store before/after audit records.
- Validate counts, nulls, formats, ranges, and checksums.
- Do not backfill an unresolved monetary unit.
- Keep idempotent batch checkpoints.

### Stage 3 - Compatibility read

- Prefer new explicit fields when verified.
- Allow legacy fields only through a centralized compatibility reader and only
  with an approved mapping.
- Emit metrics/logs whenever a legacy path is used.
- Reject unresolved active Tour price rows.

### Stage 4 - Switch Tour Package writes

- Require explicit new fields.
- Create quote and snapshot in the same transaction as order/reservation.
- Dual-write legacy order USD fields from the quote for compatibility.
- Do not dual-calculate; both representations come from one quote.

### Stage 5 - Switch readers

- Move listing/detail/endpoint to live quote output.
- Move order/reservation/invoice/payment/email/PDF/report/admin/API to snapshot.
- Compare shadow and legacy outputs during a controlled observation window.

### Stage 6 - Verify and stabilize

- Run parity, integrity, authorization, idempotency, and rollback checks.
- Confirm no direct rate/tax column reads remain in Tour pricing consumers.
- Confirm old services are unchanged.

### Stage 7 - Legacy retirement

Legacy column removal/type conversion is a separate future phase requiring:

- zero legacy reads;
- data verification;
- explicit approval;
- rollback/export plan;
- no impact on other services sharing `orders`.

Never use `migrate:fresh`, `migrate:refresh`, `db:wipe`, `TRUNCATE`, destructive
column conversion, or live data mutation as part of this plan.

## 27. Data Remediation Plan

No data is changed in Phase 2.

### 27.1 Blocking before implementation

| Data issue | Read-only action | Required decision |
| --- | --- | --- |
| Tour Price IDs 70-72 `markup = 150000` | Export rows, dependent tours, dates, tiers, creator/audit metadata, and compare source documents. | Finance must map original unit and intended USD amount per row. |
| Other legacy markup values | Inventory every Tour price, source, author, range, and business document; classify as unresolved until approved. | Explicit mapping; no magnitude heuristic. |
| Stale USD rate | Verify scheduler, queue, last-success logs, and source timestamps without extending freshness. | Operational owner must restore a fresh approved sell rate before Phase 3 acceptance testing/order writes. |
| Tax row lacks service/effective scope | Inventory all tax consumers and approve Tour Package effective start/end. | Finance must approve the exact Tour policy/version mapping. |
| Active malformed/legacy expiry dates | Inventory non-ISO dates and identify active rows used by the pilot. | Approve canonical mapping to `DATE`; unresolved active rows are not quoteable. |
| Soft-deleted price protection | Inventory `deleted_at` rows and all queries that ignore deletion. | Pilot query contract must exclude deleted rows everywhere. |

### 27.2 Can be corrected alongside the staged rollout

| Data/schema issue | Staged approach |
| --- | --- |
| `tour_prices.markup` implicit unit | Add explicit amount/currency shadow fields; backfill only approved rows; switch new writes. |
| `tour_prices.expired_date` varchar | Add `valid_until` DATE; parse approved values; verify; dual-read; switch writes. |
| `taxes.tax` double | Add fixed-scale percentage in a versioned tax-policy structure; compare values; switch resolver. |
| `usd_rates.rate` legacy alias | Keep alias for non-pilot compatibility; new resolver reads only validated `sell`; add metadata and logging. |
| `usd_rates` string financial values | Add fixed-scale shadow values or a dedicated versioned rate structure; backfill and compare before switch. |
| Cache keys and invalidation | Introduce resolver keys and after-commit invalidation; observe before retiring legacy keys. |

### 27.3 Can be deferred

| Issue | Reason |
| --- | --- |
| Converting all shared `orders` text monetary columns | Cross-service blast radius; use additive Tour canonical totals and snapshot first. |
| Converting every `invoice_admins` string amount | Switch Tour invoice reads first; global finance schema requires separate audit. |
| CNY/TWD pricing-engine rollout | Tour display pilot is USD; invoice compatibility can snapshot existing projections without making those currencies public pricing currencies. |
| Physical legacy-column removal | Requires zero-read proof and separate approval. |
| Accommodation/Transport/Activity adoption | Requires service-specific pricing and tax contracts. |
| Deleting invalid or expired price rows | Historical/audit records must be preserved; use status/eligibility, not deletion. |

### 27.4 Remediation execution safeguards

- Backup exact targeted rows before any write.
- Record source evidence and approver.
- Produce before/after CSV or immutable audit record.
- Use idempotent, resumable batches.
- Verify counts and checksums before switching reads.
- Provide a reverse mapping.
- Never mutate values because they “look like” IDR or USD.

## 28. Integration Map

| Integration point | Current source | Target source | Calculation allowed? | Live quote or snapshot | Compatibility risk | Required tests |
| --- | --- | --- | --- | --- | --- | --- |
| Public Tour listing | `FrontEndController`, `TourPrices::calculatePrice()`, direct rate/tax models | `TourPackagePricingService` quote summaries | Only in domain/service engine | Live quote | Missing rate currently can label IDR as USD; expiry/string sorting | Active/deleted/expired, stale rate, quote unavailable, sorting |
| Authenticated Tour listing/search | `ToursController`, related price models | Quote summaries from domain service | No controller calculation | Live quote | Legacy route/view duplication | Listing parity and route coverage |
| Tour detail rate table | `ToursController` + model calculation | Server quote collection | No view/controller calculation | Live quote | Current per-tier calculations and cache keys | Tier, date, rate/tax failure, two-decimal display |
| Price endpoint | `TourPricesController@getPrices` | Quote endpoint/resource | No endpoint arithmetic | Live quote | Currently misses expiry/deletion/date/pax rules | Authorization, eligibility, stale quote, JSON contract |
| JavaScript preview | Embedded rate JSON and client total/discount arithmetic | Render server quote; send identifiers/inputs only | Formatting/selection UI only | Live quote response | Current discount preview differs from Create Order | Preview/create parity, tampering, stale response |
| Booking form | Hidden price ID and display totals | Identifiers plus quote ID/fingerprint | No | Live quote reference | Hidden values may be mistaken as authoritative | Hidden price/total tampering |
| Create Order | `OrderController`, current models/rate/tax | `TourPackagePricingService` + snapshot transaction | Only service/engine | New live quote committed as snapshot | Shared controller, legacy fields, duplicate submit | Full formula, transaction rollback, idempotency |
| Edit Order | Current rate/tax/product recalculation | Non-pricing edit preserves snapshot; pricing edit requires repricing command | Only explicit repricing | Snapshot/new quote | Current edit silently reprices | Non-pricing stability, repricing authorization/concurrency |
| Reservation | Order totals and current shared relations | Linked order snapshot | No | Snapshot | Existing service code may read mutable order fields | Reservation snapshot parity |
| Invoice generation | Current `usd_rates` conversion of `orders.final_price` | Snapshot totals/rate; invoice snapshot copy | Projection from snapshot only | Snapshot | Current rate can change invoice totals | Rate/tax changes after order, regeneration parity |
| Payment/balance | Invoice string totals/current currency relations | Invoice/order snapshot amounts | No pricing calculation | Snapshot | Currency amount and balance types are strings | Partial/full payment against snapshot |
| Email | Order/invoice fields | Snapshot read model | Formatting only | Snapshot | Templates may fall back between fields | Email amount parity and localization |
| PDF | Order/invoice fields, legacy templates | Snapshot read model | Formatting only | Snapshot | Regeneration may use current data | PDF regeneration after rate/tax change |
| Tour report download | Blade inline contract/markup/tax calculation | Queryable snapshot/live quote DTO depending report purpose | No Blade calculation | Snapshot for transactions; live quote for catalog price list | Existing inline `ceil` formula | Report parity and no direct field calculation |
| Admin Tour price detail | View model/service calculations | Domain quote/price DTO | No Blade calculation | Live administrative quote | Markup unit unclear | Explicit unit display, invalid legacy state |
| Admin order detail | `orders.final_price`, invoice relations | Current `PricingSnapshot` | Formatting only | Snapshot | Shared order layout | Snapshot/legacy compatibility |
| API/resource | Ad hoc arrays/JSON and model calculations | Versioned quote/snapshot resource | Serializer only | Depends on endpoint | Backward response fields | Version, currency minor units, authorization |
| Search/filter/sort | Stored strings or formatted display | Typed quote summary/snapshot totals | No | Live for catalog; snapshot for transactions | Lexicographic legacy fields | Numeric sort/filter and unavailable prices |
| Cache | Multiple model/query keys | Resolver-owned versioned cache | No calculation | Source metadata | Stale/inconsistent cache | TTL, invalidation, remaining freshness |

### 28.1 Planned files to create in Phase 3

Names are proposed and may be adjusted only to match established project
conventions during implementation review:

```text
app/Services/Pricing/PricingEngine.php
app/Services/Pricing/CurrencyRateResolver.php
app/Services/Pricing/TaxResolver.php
app/Services/Tours/TourPackagePricingService.php
app/Data/Pricing/PricingQuote.php
app/Data/Pricing/PricingSnapshot.php
app/ValueObjects/Money.php
app/Support/MoneyFormatter.php
app/Exceptions/PricingException.php
app/Models/OrderPricingSnapshot.php
tests/Unit/Pricing/PricingEngineTest.php
tests/Unit/Pricing/CurrencyRateResolverTest.php
tests/Unit/Pricing/TaxResolverTest.php
tests/Feature/TourPackagePricingQuoteTest.php
tests/Feature/TourPackagePricingSnapshotTest.php
```

Additive migration filenames are intentionally not generated in Phase 2.

### 28.2 Planned files to modify in Phase 3

Expected Tour pilot integration surface:

```text
app/Models/TourPrices.php
app/Models/Orders.php
app/Models/UsdRates.php
app/Models/Tax.php
app/Models/InvoiceAdmin.php
app/Services/Tours/TourPricingService.php
app/Services/Tours/TourInventoryService.php
app/Http/Controllers/FrontEndController.php
app/Http/Controllers/ToursController.php
app/Http/Controllers/TourPricesController.php
app/Http/Controllers/OrderController.php
app/Http/Controllers/OrdersAdminController.php
app/Http/Controllers/InvoiceAdminController.php
app/Jobs/UpdateCurrencyRates.php
app/Console/Kernel.php
resources/views/frontend/landing-page/tours/directory.blade.php
resources/views/frontend/landing-page/tours/detail.blade.php
resources/frontend/js/landing-page/tours/detail.js
resources/views/frontend/home/orders/details/tour-modern.blade.php
resources/views/frontend/home/orders/edit-tour.blade.php
resources/views/emails/invoiceTourEn.blade.php
resources/views/emails/invoiceTourZh.blade.php
resources/views/backend/reports/downloads/tour.blade.php
resources/views/backend/operations/tours/detail.blade.php
routes/web.php
docs/modules/tour-package.md
docs/README.md
```

Actual Phase 3 changes must be split into bounded stages. Scheduler/job repair,
global invoice schema conversion, and other service refactors must remain
separate tasks.

## 29. Test Standard and Matrix

Tests must run only against an explicitly isolated database as required by
`docs/testing.md`.

### 29.1 Unit calculation tests

| Case | Assertion |
| --- | --- |
| Required example | Preserves raw IDR 1,452,000 for audit and publishes USD 91.00. |
| Sell side | Resolver/quote records `sell`; `rate` and `buy` are not read. |
| Fractional rate | Fixed-scale result is deterministic without float drift. |
| Tax 1.5% | Applied exclusively to contract plus markup. |
| Fractional tax | Fixed scale and half-up are correct. |
| Markup USD | USD cents convert to IDR using quote sell rate. |
| Contract rate IDR | Preserved as whole IDR. |
| Quantity | Gross USD is rounded unit USD times pax. |
| Promotion fixed | Explicit currency conversion and post-tax order. |
| Promotion percentage | Correct eligible base and half-up. |
| Booking code fixed | Explicit currency conversion and post-tax order. |
| Booking code percentage | Correct eligible base and half-up. |
| Discount stacking | Approved order/limits enforced; invalid stack rejected. |
| Add-on | Server source, quantity, currency, and final addition are correct. |
| Lower bound | Final total never becomes negative. |
| Rounding 90.001 | USD displays 91.00. |
| Rounding 90.750 | USD displays 91.00. |
| Other boundaries | IDR conversion, tax, discount, and whole-USD display boundaries. |
| Overflow | Reject instead of wrapping/truncating. |
| Currency mismatch | Money operation rejects incompatible currencies. |

### 29.2 Resolver tests

| Case | Assertion |
| --- | --- |
| Fresh sell rate | Returns full metadata and fresh status. |
| Stale rate | Listing quote and Create Order fail closed. |
| Null rate | Typed failure. |
| Zero rate | Typed failure. |
| Negative rate | Typed failure. |
| Malformed rate | Typed failure. |
| Duplicate rate | Ambiguous failure. |
| Future timestamp | Invalid/freshness failure. |
| Rate at 24-hour boundary | Boundary behavior is deterministic and documented. |
| Cache hit | Revalidates freshness. |
| Cache invalidation | Successful update invalidates after commit. |
| Missing tax | Typed failure. |
| Inactive tax | Typed failure. |
| Ambiguous/overlapping tax | Typed failure. |
| Tax effective boundary | Correct policy selected. |
| Service isolation | Tour tax is not selected for another service. |

### 29.3 Tour Package eligibility tests

| Case | Assertion |
| --- | --- |
| Tour ownership | Price from another tour rejected. |
| Active price | Draft/inactive rejected. |
| Soft-delete | Deleted price rejected everywhere. |
| Expired price | Rejected for service date. |
| Travel-date validity | Correct `valid_until` boundary. |
| Pax tier | Correct min/max tier and no highest-tier fallback. |
| Legacy markup unresolved | IDs/rows without approved unit mapping rejected. |
| Promotion eligibility | Status/date/service/usage/ownership checked. |
| Booking-code eligibility | Status/date/service/usage/ownership checked. |
| Add-on eligibility | Status/date/service/quantity checked. |

### 29.4 Integration and regression tests

| Case | Assertion |
| --- | --- |
| Frontend quote parity | Listing/detail/endpoint show the same quote for the same inputs. |
| Create Order parity | Stored snapshot matches preview quote or a safely regenerated equivalent input fingerprint. |
| Request price tampering | Hidden/query/JSON price and totals are ignored. |
| Discount tampering | Request discount values are ignored. |
| Rate/tax tampering | Request rate/tax IDs or values are ignored. |
| Invoice snapshot | Invoice uses order snapshot, not current rate/tax. |
| Reservation snapshot | Reservation display/flow uses order snapshot. |
| Payment snapshot | Balance uses invoice/snapshot amount. |
| Rate changes after order | Existing order/invoice/email/PDF/report unchanged. |
| Tax changes after order | Existing order/invoice/email/PDF/report unchanged. |
| Product price changes after order | Existing transaction unchanged. |
| Non-pricing order edit | Snapshot ID/checksum unchanged. |
| Explicit repricing design test | Authorized future operation preserves old/new snapshots and reason. |
| Repricing unauthorized | Rejected without side effects. |
| Idempotent Create Order | Duplicate token produces one order and one initial snapshot. |
| Transaction rollback | Order/reservation/snapshot fail together. |
| Snapshot checksum | Tampering/inconsistent storage detected. |
| Legacy order | Compatibility read is explicit and does not use current rate. |
| Search/sort | Numeric typed amounts, unavailable stale quotes excluded correctly. |
| Email/PDF/report parity | All equal stored snapshot. |
| API resource | Stable fields, minor units, and authorization. |
| Service isolation | Accommodation/Transport/Activity behavior unchanged. |

### 29.5 Static architecture guards

- No direct `buy`, `sell`, or `rate` read in Tour pricing consumers.
- No direct hardcoded Tax ID in Tour pricing consumers.
- No ad hoc financial `ceil()` in Tour pricing consumers; whole-USD rounding is centralized in `PricingEngine`.
- No binary float casts in Pricing components.
- No pricing formula in Blade or JavaScript.
- No current rate/tax query in historical consumers.
- No mutation method on committed snapshot.

## 30. Rollback Plan

### 30.1 Code rollout rollback

- Keep legacy read paths behind an explicit compatibility switch during the
  observation window.
- Revert individual integration consumers to compatibility reads without
  deleting snapshot data.
- Do not revert new orders to current-rate recalculation.
- Preserve snapshot rows for forensic/audit use even if the new UI is rolled
  back.

### 30.2 Schema rollback

- Additive nullable columns/tables allow old code to continue.
- Before snapshot data exists, `down()` may remove only newly added structures.
- After snapshot data exists, rollback means stop new writes and restore code,
  not immediately drop populated financial data.
- Export/backup new structures before any later removal.
- Foreign keys/indexes are removed in reverse dependency order only under an
  approved migration.

### 30.3 Data remediation rollback

- Preserve before values and approved mappings.
- Backfill batches include reversible mapping IDs and checksums.
- Revert only the exact rows written by a batch.
- Never overwrite unresolved legacy values.

### 30.4 Operational rollback

- If rate update/monitoring changes fail, preserve the last known database row
  and allow it to become stale; pricing must fail closed.
- Never extend timestamps to make an old value appear fresh.
- Cache rollback includes invalidating both new and legacy keys.

## 31. Risks

| Risk | Mitigation |
| --- | --- |
| Invalid markup interpreted as USD | Explicit mapping and blocking unresolved rows. |
| Shared `orders` blast radius | Additive nullable fields, dedicated snapshot table, Tour-only switch. |
| Duplicate truth between order totals and snapshot | One quote, one transaction, parity invariants/checksum. |
| MariaDB 10.4 JSON integrity/index limitations | Typed summary columns, application validation, compatible `JSON_VALID` constraint after verification. |
| Current rates are stale | Phase 3 prerequisite and monitoring acceptance criteria. |
| Queue scheduler appears configured but not operating | Verify scheduler and worker independently. |
| Existing controller is large/shared | Introduce bounded service calls; no broad controller refactor. |
| Discount schema lacks type/currency/scope | Add explicit versioned fields before activating fixed/percentage behavior. |
| Tax schema lacks version/scope | TaxResolver requires additive policy data. |
| Legacy date/string financial columns | Shadow columns and staged verified backfill. |
| Preview/order race | Revalidate quote inputs/rate freshness and use input fingerprint at Create Order. |
| Repricing affects invoice/payment | Repricing remains disabled until separate lifecycle rules are approved. |
| API/email/PDF consumers missed | Integration inventory and static guards. |
| API credential in source default | Separate security remediation before production rollout; never log it. |

## 32. Blocking Prerequisites for Phase 3

### 32.1 Gate to start Pricing Foundation

1. This Proposed contract is accepted as the planning input; activation is not
   implied.
2. An isolated SQLite `:memory:` or separately named disposable MariaDB
   connection is explicitly verified before any database-backed test.
3. Additive migration and rollback designs are reviewed before migrations are
   executed even on a disposable database.
4. Native integer/fixed-scale arithmetic and its numerical scales are accepted.
5. Existing worktree changes are inventoried so Pricing Foundation does not overwrite
   unrelated user work.

Finance mapping for IDs 70-72 and the production tax activation timestamp do
not block foundation code or additive migration authoring. They do block
backfill, live quote activation, and order writes.

### 32.2 Gates before live quote and order pricing activation

1. Finance maps the intended unit and amount for every active quoteable Tour
   Price, including IDs 70-72.
2. Tour Package tax policy is inserted with a deployment-approved
   `effective_from`; overlapping policies are absent.
3. Promotion/booking-code metadata supports type, explicit currency, scope, and
   validity; candidate selection follows Section 10.
4. A fresh USD sell rate with `retrieved_at` can be produced operationally and
   scheduler/queue ownership is assigned.
5. Snapshot write authorization and `orders.reprice` permission are reviewed.
6. Backup, remediation mapping, idempotent backfill, and rollback procedures
   are approved before any non-disposable data write.

## 33. Phase 3 Technical and Phase 4 Activation Acceptance

Phase 3 technical implementation may be considered complete when the code,
isolated database verification, Tour-only consumer migration, and targeted
regression criteria below pass. Production activation and `Status: Active`
remain Phase 4 decisions and additionally require the first two bullets:

- this contract has an approved active successor/status decision
  (**Phase 4 production gate**);
- all blocking data mappings are complete (**Phase 4 production gate**);
- Tour pricing uses one domain service and one generic engine;
- all Tour rate reads go through `CurrencyRateResolver` using `sell`;
- stored USD sell rate is used without a freshness gate;
- Tour tax resolves from the stored `taxes` row;
- all calculations avoid binary float and ad hoc consumer rounding;
- required example preserves raw IDR 1,452,000 for audit and publishes USD 91.00;
- preview and Create Order use the same authoritative quote inputs/breakdown;
- order, reservation, invoice, payment, email, PDF, report, admin, and API use
  the stored snapshot;
- new orders store authoritative IDR and USD cents from one quote;
- invoice does not use a current rate;
- non-pricing edits do not reprice;
- unresolved legacy markup is rejected;
- direct calculation in Tour Blade/JavaScript/model/controller/report is
  removed or isolated behind a temporary audited adapter;
- additive migrations and backfills are verified and reversible;
- authorization, tampering, idempotency, transaction rollback, cache, and
  historical stability tests pass on an isolated database;
- no out-of-scope service behavior changes.

## 34. Remaining Blocking Questions

Canonical currency, sell-side rate, transition freshness timestamp, markup
currency for new Tour prices, tax formula, discount selection/base, precision,
historical snapshots, and repricing authorization are **Approved for
implementation planning**.

The remaining decisions are:

1. What exact approved markup amount/currency applies to Tour Price IDs 70-72,
   who approves each mapping, and when?
2. What deployment-approved activation timestamp becomes
   `tax_policies.effective_from` for the initial Tour Package 1.5% policy?
3. For Historical Consumers repricing, which order statuses permit repricing and what
   approved invoice/payment workflow applies to an already invoiced or paid
   order?

Questions 1 and 2 do not block Pricing Foundation foundation work. They block Live Tour Pricing
availability for affected rows and all Order Pricing production order writes.
Question 3 keeps repricing disabled while snapshot-only historical reads can
still proceed. Add-ons remain outside the initial pilot and require a later
contract amendment.
