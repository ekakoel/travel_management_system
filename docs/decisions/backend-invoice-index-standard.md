# Backend Invoice Index Work Queue Standard

Status: active  
Updated: 2026-08-11

## Scope

This decision defines the canonical backend invoice directory at `GET
/invoice`. It complements `backend-invoice-detail-standard.md` and does not
change invoice generation, payment processing, order lifecycle, or PDF flow.

## Work Queue Contract

- The index is a bounded operational queue for invoices whose stored payment
  deadline is still active, preserving the existing list scope.
- Each row links to the canonical invoice detail route. The index must not
  render a duplicate full-invoice modal for every record.
- The server projection supplies invoice reference, reservation and agent
  context, canonical 48-hour deadline presentation, recorded USD total,
  payment-currency balance, payment state, payment count, and adjustment count.
- Invoice payment state is resolved by the shared
  `InvoicePaymentStateResolver`; no invoice status column is assumed or written.
- Search and status/currency/deadline filters operate on the bounded in-memory
  queue without additional requests or database queries.

## Performance Contract

- `InvoiceIndexService` selects only required invoice columns.
- Reservation, agent, currency, and payment status are eager loaded with
  constrained columns; adjustments use `withCount`.
- Blade performs no model query, service resolution, date calculation, or
  financial calculation.
- Desktop table and responsive cards are rendered from the same projection.

## UI and Localization Contract

- Use shared backend hero, toolbar, KPI, filter, panel, table, responsive card,
  status badge, action, feedback, and empty-state primitives.
- Page-specific JavaScript contains only search/filter/reset behavior.
- Page-specific SCSS contains only invoice-index composition and responsive
  refinements; shared primitives must not be redefined.
- All visible interface strings are available in `en`, `zh-CN`, and `zh`.
