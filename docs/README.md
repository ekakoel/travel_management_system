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
  Approved shared order, reservation, payment, and fulfillment statuses.

* `frontend-standards.md`
  Shared components, SCSS structure, JavaScript initializers, responsive rules, datepicker rules, and UI conventions.

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
  Approved architecture decisions. Existing decisions must not be reversed without explicit approval.

## Documentation Rule

Before modifying a module:

1. Read this index.
2. Read the relevant core documents.
3. Read the relevant module document.
4. Inspect the actual implementation.
5. Update documentation if the approved behavior or architecture changes.
