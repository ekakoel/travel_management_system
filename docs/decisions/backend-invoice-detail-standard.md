# Backend Invoice Detail Workspace Standard

Status: active  
Updated: 2026-08-11

## Scope

This decision defines the canonical backend detail workspace for
`GET /invoice/{invoice}`. It covers invoice presentation, linked billable
orders, manual invoice adjustments, payment history, transaction history, and
payment-bank selection. It does not change payment processing, order status,
reservation status, or PDF generation.

## UI Contract

- Use the shared backend page hero, toolbar, KPI grid, panel, status badge,
  empty state, button, form, modal close, and `x-backend.detail-layout`
  primitives.
- Keep invoice overview, line items, payment history, and transaction history
  in the main column. Keep workflow links, bank context, and multi-currency
  totals in the context column.
- Desktop line items use the canonical backend table. Small screens use the
  responsive card projection from the same server-provided collection.
- Every required field relies on the HTML `required` attribute so the shared
  backend initializer supplies the red required marker.
- Mutation buttons use the shared submit guard and spinner. Modal open and
  delete confirmation behavior lives in page JavaScript, not inline Blade
  handlers.
- All interface text is available in `en`, `zh-CN`, and `zh`.

## Data and Pricing Contract

- `InvoiceDetailService` is the only projection layer for the detail Blade.
  Blade must not query models or calculate financial amounts.
- Linked orders are loaded in one ordered query and required relations are
  eager loaded. Deleted orders are never projected.
- Tour Package lines use `OrderPricingSnapshotReader::historicalValues()` so
  later pricing changes cannot rewrite historical invoice presentation.
- Other services use the committed order total fields already used by the
  shared order lifecycle.
- The displayed USD invoice total is calculated server-side from projected
  service lines and manual adjustments. The outstanding balance remains the
  committed invoice balance and is displayed in the invoice payment currency.
- Payment and invoice status labels on this page are read-only projections.
  No new invoice status is written and the shared order lifecycle is unchanged.

## Mutation and Security Contract

- Adjustment create/update requests validate `Y-m-d`, description, rate,
  unit, and times. The client never submits an authoritative amount.
- `InvoiceMutationService` calculates `rate * unit * times` on the server and
  wraps adjustment and bank writes in transactions with row locks.
- Adjustment changes remain unavailable after the reservation becomes Active
  or Completed, matching the established invoice flow.
- A paid invoice or Completed reservation cannot change its payment bank.
- The existing authenticated backend role middleware remains mandatory, and
  mutation Form Requests repeat the allowed-position guard.

## Compatibility

- Existing invoice rows and relationships remain unchanged.
- No database migration is required.
- The previous adjustment update/delete URLs remain available, now with named
  routes and route model binding. A canonical missing create-adjustment route
  is added under the invoice resource.
