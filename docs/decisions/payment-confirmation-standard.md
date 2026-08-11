# Public Payment Confirmation Standard

Status: active  
Effective date: 2026-08-03

## Scope

This is the canonical customer payment-confirmation contract for standard
public orders (Accommodation, Transport, Tour Package, and Activity). Wedding
payments remain on their dedicated legacy architecture until an explicitly
approved migration is performed.

## Lifecycle

1. A customer may submit payment proof only while the order is `Approved`, an
   owned invoice exists, the invoice balance is positive, and the payment
   deadline is open.
2. A submission creates a `PaymentConfirmation` with status `Pending`.
   Submission never changes the order to `Paid`.
3. Only the authorized finance/admin validation flow may set a confirmation to
   `Valid` or `Invalid`, update the invoice balance, and set the order to `Paid`
   when the authoritative remaining balance is settled.
4. Only one `Pending` confirmation may exist for an invoice at a time. The
   server enforces this rule inside a transaction with row locking; frontend
   button locking is an additional usability guard, not the authority.
5. A further submission is allowed after an invalid review, or after a valid
   partial payment when the invoice still has a positive balance and no other
   confirmation is pending.

## Submission Data

The canonical form collects payment date, reported amount in the committed
invoice currency, and payment proof in JPG, PNG, or PDF format (maximum 5 MB).
The date cannot be in the future; amount must be greater than zero and no more
than the authoritative outstanding balance.

Invoice identity, currency, ownership, lifecycle state, and outstanding balance
are resolved again on the server. Hidden fields are never authoritative for
financial values. Legacy file field names remain accepted and validated by the
Form Request during gradual page migration; new forms use `receipt_file`.

## Security and Operations

- Proof files use randomized filenames and private storage.
- Delivery is ownership/authorization guarded and sends `no-store` and
  `nosniff` headers.
- Payment notes are plain text and escaped during rendering.
- Database writes and audit logs are atomic. A stored confirmation remains
  successful if the operational notification email is temporarily unavailable;
  that email failure is logged for operations.
- The operational email clearly states that submission is pending review and
  is not final payment approval.

## UI Contract

Customer actions use canonical `.ui-btn` variants. Each page exposes one primary
next action: edit for editable orders or payment confirmation for a payable
approved order. Invoice and navigation actions are secondary; destructive
lifecycle actions use danger styling. Submit/delete forms show an immediate
spinner, become disabled, and are protected against repeat execution.
