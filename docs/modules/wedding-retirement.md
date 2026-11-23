# Wedding Service Retirement

Status: active
Updated: 2026-11-23

## Scope

Wedding booking, administration, review, and order-specific runtime flows are
retired. Their routes, controllers, dedicated views, middleware, and frontend
assets have been removed.

## Existing Orders and Data

- Existing Wedding order records, service tables, migrations, files, and review
  data are retained.
- Existing Wedding orders have been reconciled; they are no longer served by
  Wedding-specific runtime pages.
- Wedding migrations, tables, stored records, and existing files remain
  untouched for audit and preservation. Database removal requires a separate
  reviewed data-retention plan and explicit approval.

## Other Services

Accommodation, Transport, Tour Package, Activity, and Private Villa flows keep
their existing routes and lifecycle. The Wedding retirement must not change
shared order status, payment, invoice, reservation, or fulfillment contracts.
