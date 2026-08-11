# Project Documentation Index

Codex must use this file to locate the documentation relevant to each task.

## Core Documentation

* `architecture.md`
  Overall Laravel architecture, application boundaries, services, modules, and dependencies.

* `database.md`
  Important tables, relationships, naming conventions, migration rules, and production-data constraints.

* `coding-standards.md`
  PHP, Laravel, Blade, JavaScript, SCSS, naming, validation, services, controllers, and testing standards.

* `security-rules.md`
  Authentication, authorization, ownership, IDOR prevention, financial files, uploads, and private file delivery.

* `status-contract.md`
  Active final contract separating commercial order status (`Paid`) from
  fulfillment (`orders.completed_at` / `orders.completed_by`) for all four
  public services.

* `frontend-standards.md`
  Active mandatory public-frontend contract covering Home visual direction,
  cardless design, canonical components, design tokens, responsive rules, and
  duplication prohibitions.

* `testing.md`
  Test commands, test database requirements, module test coverage, and release verification.

## Public Service Modules

* `modules/accommodation.md`
  Public Accommodation booking, pricing, inventory, reservation, payment, and completion flow.

* `modules/transport.md`
  Public Airport Shuttle, Daily Rent, and Transfer service flow. This excludes internal SPK Transport Management unless explicitly stated.

* `modules/tour-package.md`
  Public Tour Package pricing, reservation, payment, lifecycle, and fulfillment.

* `modules/activity.md`
  Public Activity pricing, reservation, payment, lifecycle, and fulfillment.

## Architecture Decisions

* `decisions/`
  Decision records and audit trail. Read each document's status before using
  it: `active` is authoritative, `proposed` is not active, `superseded` has
  been replaced, and `historical` records point-in-time behavior.

Lifecycle decision status:

* `status-contract.md` — `active`, canonical final contract.
* `decisions/accommodation-status-contract.md` — `superseded`.
* `decisions/shared-order-status-compatibility-plan.md` — `superseded`.
* `decisions/shared-order-status-audit.md` — `historical`.
* `decisions/accommodation-status-lifecycle-audit.md` — `historical`.
* `decisions/schedule.md` — `proposed`; it does not prove scheduler activation.

Active frontend decisions:

* `frontend-standards.md` — canonical public frontend UI contract.
* `decisions/frontend-picker-standard.md` — canonical date/time picker,
  initializer, icon, visual-state, compatibility, and migration contract.
* `decisions/frontend-order-modal-standard.md` — canonical service-order modal
  contract.
* `decisions/form-submit-standard.md` — canonical frontend submit, loading,
  idempotency, and PRG contract.
* `decisions/payment-confirmation-standard.md` — canonical public-order payment
  confirmation data, lifecycle, duplicate protection, security, and UI contract.

Active document decisions:

* `decisions/pdf-localization-standard.md` — canonical locale, embedded-font,
  generation, protected-delivery, and verification contract for PDFs.
* `decisions/order-confirmation-email-standard.md` — canonical transactional
  order-confirmation content, email-client layout, security, and verification
  contract.

Active backend operations decisions:

* `decisions/reservation-backend-workspace-standard.md` — canonical assigned
  Reservation work queue, manual entry, route, UI, query, and safe-delete
  contract.
* `decisions/backend-invoice-detail-standard.md` — canonical backend invoice
  detail projection, pricing, adjustment, bank, UI, and mutation contract.
* `decisions/backend-invoice-index-standard.md` — canonical active invoice
  work queue, query projection, filtering, responsive UI, and performance
  contract.

## Documentation Rule

Before modifying a module:

1. Read this index.
2. Read the relevant core documents.
3. Read the relevant module document.
4. Inspect the actual implementation.
5. Update documentation if the approved behavior or architecture changes.
