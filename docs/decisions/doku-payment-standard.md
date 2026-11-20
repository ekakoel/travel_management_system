# DOKU Checkout Payment Standard

Status: active
Effective date: 2026-11-20

## Scope

DOKU Checkout is an online payment option for the four standard public
services only:

* Accommodation
* Public Transport
* Tour Package
* Activity

Wedding and Private Villa remain excluded until a separate approved migration.

DOKU charges the full outstanding invoice balance. Partial payments remain on
the manual payment-confirmation flow. The merchant absorbs the DOKU MDR; no
surcharge is added to the customer amount.

## Data Model

`doku_payments` stores one row per checkout attempt. Its statuses are
`Pending`, `Success`, `Failed`, `Expired`, and `Review`. It records the unique
DOKU invoice number, request ID, amount in IDR, credited invoice amount,
original request ID, hosted checkout URL, expiry, raw responses, and the linked
`payment_confirmations` row.

`payment_confirmations.source` distinguishes `manual` and `doku`. DOKU
settlements are successful confirmations with status `Valid`, no uploaded
receipt, and `gateway_reference` set to the DOKU invoice number. DOKU statuses
are never written to `orders.status`.

## Checkout

1. The customer must own an eligible order whose status is `Approved`.
2. An owned invoice must have a positive balance and an open payment deadline.
3. A manual `Pending` confirmation or another active DOKU checkout blocks a new
   checkout.
4. The amount is calculated from the authoritative invoice balance:
   `ceil(balance * sell_rate)` for USD, CNY, and TWD invoices, or the balance
   itself for IDR invoices.
5. The request is signed with the DOKU HMAC-SHA256 signature and sent with the
   exact raw JSON body.
6. The customer is redirected to the hosted DOKU checkout URL.

## Notification And Settlement

The notification endpoint is `POST /api/doku/notification`. It verifies the
DOKU signature and client ID, using the configured notification path as the
`Request-Target` and the SHA-256 digest of the raw request body. DOKU may omit
`Request-Target` from notification headers; when the header is present it must
match the configured path. Only `SUCCESS` notifications are settled; `FAILED`
notifications are ignored because the customer may retry another payment
method.

A valid success is applied inside a locked transaction:

1. The DOKU row, order, and invoice are locked.
2. The payment must still match the current outstanding balance.
3. The order must still be payable and the payment must have arrived before the
   payment deadline.
4. A `Valid` payment confirmation with `source=doku` is created.
5. The invoice balance is reduced by the converted amount, capped at the
   current balance.
6. The order becomes `Paid` only when the remaining balance is below 1.
7. Sibling pending DOKU attempts and stale manual pending confirmations are
   marked invalid or expired.
8. An order log is written.

Repeated notifications are idempotent. A success that arrives after the order
was canceled, after the deadline, for a mismatched amount, or without a valid
order/invoice is stored as `Review`; it is not credited automatically and does
not reinstate the order. While an invoice has a DOKU attempt in `Review`, a new
DOKU checkout is blocked; finance retains the manual resolution path.

The admin order detail lists DOKU checkout attempts, including status, amount,
and review reason, so exceptional payments are visible to finance.

## Reconciliation

`doku:reconcile-pending` runs every fifteen minutes. It checks pending DOKU
transactions through the DOKU status API, applies valid successes, and marks
expired or failed transactions. Reconciliation uses the same settlement path
as notifications. The Check Status API uses a `GET` signature without the
`Digest` component; only body-carrying requests include `Digest`.

## Security

* DOKU credentials belong in environment configuration and are never rendered,
  logged, or committed.
* The customer amount, invoice identity, and ownership are always resolved on
  the server.
* The notification endpoint requires a valid DOKU signature.
* DOKU-originated payment confirmations are immutable in the admin validation
  flow.
* Manual payment submission is blocked while a DOKU checkout is active.

## UI

The customer payment modal shows DOKU only when the feature is enabled and the
invoice is eligible. The modal displays the full outstanding balance, offers a
secure DOKU payment action, and lets the customer continue an existing hosted
checkout. DOKU rows have no receipt image and are identified by their gateway
reference.

## Testing And Operations

Set `DOKU_ENABLED=true`, `DOKU_API_URL`, `DOKU_CLIENT_ID`, and
`DOKU_SECRET_KEY` only in the target environment. Register
`https://<host>/api/doku/notification` as the DOKU notification URL and keep
the configured notification path in sync with `config/doku.php`.

Automated coverage verifies IDR conversion, public-service scope, notification
body signature validation, the notification route, and the GET signature
format without database access.

Sandbox verification on 2026-11-20 confirmed hosted checkout creation against
`https://api-sandbox.doku.com` and GET status-signature acceptance. A paid
checkout, real notification delivery, and settlement against disposable
database data remain pending. End-to-end verification must use the DOKU sandbox
and a registered notification URL. Production credentials and the working
database must not be used for sandbox verification.
