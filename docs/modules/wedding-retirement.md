# Wedding Service Retirement

Status: active
Updated: 2026-11-23

## Scope

Wedding is retired as a service for new bookings. Public listing, booking entry,
catalogue administration, wedding planner routes, and wedding service navigation
are disabled. Wedding review link generation, review submission, and review
validation endpoints are also disabled.

## Existing Orders and Data

- Existing Wedding order records, service tables, migrations, files, and review
  data are retained.
- Existing Wedding order detail/edit, payment, confirmation, contract, invoice,
  and fulfillment paths remain available so already accepted work can be
  completed and audited.
- Shared order endpoints reject new `Wedding Package` submissions server-side.
- Do not drop Wedding tables or delete Wedding records as part of retirement.
- Remove legacy order handling only after all existing Wedding orders and
  financial/document obligations have been reconciled.

## Other Services

Accommodation, Transport, Tour Package, Activity, and Private Villa flows keep
their existing routes and lifecycle. The Wedding retirement must not change
shared order status, payment, invoice, reservation, or fulfillment contracts.
