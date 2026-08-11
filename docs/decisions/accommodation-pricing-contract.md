# Accommodation Pricing Contract

Status: active
Updated: 2026-07-27

Scope: Accommodation only (`Hotel`, `Hotel Promo`, `Hotel Package`).

Out of scope: Transports, Tour Packages, Activities, Transport Management/SPK, Wedding, DOKU, and Private Villa.

## Source of Truth

Accommodation order creation must calculate final stored prices on the server through `App\Services\Hotels\HotelPricingService`.

Frontend hidden fields such as displayed room totals, promo totals, package totals, add-on IDs, shuttle price IDs, and final price are display or form compatibility inputs only. They must not be trusted as authoritative monetary totals when creating Accommodation orders.

## Price Sources

Normal hotel orders use `hotel_prices` rows for each selected stay night. Exactly one active rate row must match each night, hotel, and room. Missing or overlapping nightly rates must reject order creation.

Hotel promo orders use selected `hotel_promos` rows when the promo belongs to the selected hotel and room, is active, is inside booking period, covers the stay night, and satisfies minimum stay. Nights not covered by a valid promo fall back to the normal nightly `hotel_prices` source. At least one selected promo night must apply.

Hotel package orders use the selected `hotel_packages` row when the package belongs to the selected hotel and room, is active, matches selected stay duration, and covers the selected stay period.

Normal price CRUD must reject overlapping periods for the same hotel and room,
because normal orders require exactly one authoritative rate for every night.
Legacy overlaps are displayed as `Conflict` in backend detail and must be
resolved manually; the application does not silently mutate historical rates.

Extra bed and airport shuttle totals are resolved server-side from their database records. Request-provided add-on price totals are not authoritative.

## Rounding and Totals

Published Accommodation rate components are calculated in whole USD:

- `contract_rate_usd`: `ceil(contract_rate_idr / usd_rate)`.
- `markup_usd`: `ceil(markup)`.
- `tax_usd`: `ceil((contract_rate_usd + markup_usd) * tax_percent / 100)`.
- `published_rate`: `contract_rate_usd + markup_usd + tax_usd`.

Backend and frontend use the same pricing semantics:

- `published_rate` is always shown before kickback or discount.
- Kickback is a separate adjustment after published rate.
- `net_rate = max(published_rate - kickback, 0)` is a normal-rate preview and
  must not replace the published-rate label.
- Package duration multiplies the IDR contract rate before USD conversion;
  markup is applied once to the package total under this contract.
- Backend `detail-hotel-{id}` renders the breakdown supplied by
  `HotelPricingService`; Blade and JavaScript do not recalculate money.
- Calculation breakdown opens in one reusable backend modal per price record;
  it must not be expanded inline inside pricing table rows.
- Backend Hotel index and detail only display non-expired pricing records. End
  dates are inclusive, so a rate ending today remains visible through today.
  Normal rates use `end_date`; promos require both `book_periode_end` and
  `periode_end` not to have passed; packages use `stay_period_end`. Additional
  charges without an explicit end date remain visible, while `must_buy_end`
  (or legacy `active_date` when no mandatory end exists) is their expiry bound.
- Order pricing fails closed when the authoritative USD conversion rate is
  missing, zero, or negative; backend detail displays the same readiness issue.

Package rates apply package duration as the contract-rate multiplier before USD conversion. Room quantity is applied after nightly or package published rates are summed.

Stored order totals are snapshots taken at order creation:

- `price_pax`: per-room stay total.
- `normal_price`: stay total multiplied by room quantity.
- `price_total`: normal price after kickback plus server-side extra bed and optional rate totals.
- `final_price`: price total minus promotion and booking-code discounts, plus server-side airport shuttle total.

Invoice generation for Accommodation must use the stored order snapshot, especially `orders.final_price`, not current mutable hotel price tables.

## Transaction Safety

Accommodation order creation must keep order, guests, order logs, user logs, booking-code usage, add-ons, airport shuttle rows, mail side effects, and related snapshots transactionally consistent. If a mid-flow database write fails, partial order side effects must roll back and notification mail must not be sent.

## Regression Coverage

Focused tests must cover:

- Normal hotel hidden price tampering.
- Promo happy path, fallback, wrong room/hotel, expired booking period, minimum stay, and missing fallback.
- Package happy path, wrong duration, and expired stay period.
- Server-side add-on and shuttle price resolution.
- Whole-USD rounding without cumulative drift.
- Invoice snapshot parity after rate changes.
- Transaction rollback and mail suppression on mid-flow failure.
