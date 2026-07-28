# AGENTS.md — Bali Kami Tour Laravel Project

## 1. Role

Act as a senior Laravel engineer working on a production travel-management platform.

The project uses:

* Laravel 10
* PHP 8+
* Blade
* Bootstrap 5
* SCSS/CSS
* JavaScript and jQuery
* MySQL or MariaDB
* Laravel queues, scheduler, notifications, authorization, validation, and service classes

Follow Laravel conventions and the existing project architecture. Do not introduce a new framework, package, architectural pattern, or naming convention unless the task explicitly requires it.

## 2. Primary Source of Truth

Before making changes:

1. Read this `AGENTS.md`.
2. Read `docs/README.md`.
3. Read the documentation related to the requested module.
4. Inspect the current implementation, routes, controllers, services, models, migrations, Blade files, JavaScript, SCSS, tests, and Git history when relevant.
5. Treat the current database schema and approved documentation as the source of truth.

Do not rely only on assumptions or generic Laravel patterns when the project already has an established implementation.

## 3. Mandatory Workflow

For every task, perform the following workflow:

### Phase A — Analysis

* Restate the task internally.
* Identify the affected module and files.
* Inspect the existing flow end-to-end.
* Find existing reusable patterns in the project.
* Identify risks, regressions, database impact, authorization impact, and compatibility impact.
* Do not edit files during this phase unless the user explicitly requests immediate implementation.

### Phase B — Implementation

* Make the smallest complete change needed.
* Reuse existing services, components, helpers, translations, styles, and patterns.
* Keep controllers thin.
* Place business logic in the appropriate service or domain class.
* Use Form Requests or equivalent server-side validation.
* Use authorization policies, gates, ownership checks, or service guards where appropriate.
* Wrap multi-record writes in database transactions.
* Preserve backward compatibility unless the task explicitly approves breaking changes.

### Phase C — Verification

After implementation:

* Review the complete diff.
* Run the most relevant tests.
* Run targeted syntax and static checks where available.
* Verify routes, validation, authorization, database writes, status transitions, and UI behavior.
* Confirm that no unrelated file was modified.
* Report any test that could not be run.

## 4. Scope Control

Do only what the current task requests.

Do not:

* Refactor unrelated modules.
* Rename unrelated classes, methods, routes, columns, CSS classes, or translation keys.
* Replace working libraries without explicit approval.
* Change business flows that are not part of the task.
* Add speculative features.
* Perform broad cleanup outside the affected scope.
* Modify internal Transport Management or SPK flows when working on public Transport services unless explicitly requested.
* Modify Wedding, Private Villa, DOKU payment architecture, or other excluded services unless explicitly requested.

When discovering an unrelated issue, report it separately without fixing it.

## 5. Safety Rules

Never execute or generate destructive actions without explicit approval.

Prohibited without explicit approval:

* `php artisan migrate:fresh`
* `php artisan migrate:refresh`
* `php artisan db:wipe`
* `php artisan schema:drop`
* `DROP DATABASE`
* `DROP TABLE`
* `TRUNCATE`
* Bulk deletion or irreversible updates
* Deleting migrations that may have run
* Replacing `.env`
* Resetting Git history
* Force pushing
* Removing production files
* Running seeders against existing production-like data

Never assume that a local database is disposable.

Before any schema or data operation:

1. Inspect the existing schema.
2. Explain the impact.
3. Use additive and reversible migrations.
4. Preserve existing records.
5. Provide a rollback path.
6. Require explicit approval for destructive or high-risk operations.

## 6. Database Rules

* Never trust input from Blade hidden fields for price, ownership, status, authorization, totals, discounts, or financial calculations.
* Calculate authoritative values on the server.
* Never create duplicate columns, indexes, constraints, or migrations.
* Inspect existing migrations and the live schema before creating a migration.
* Use foreign keys only after confirming existing data is valid.
* Do not silently repair production data.
* Make data migrations explicit, reviewable, and idempotent where possible.
* Use transactions for related writes.
* Avoid N+1 queries.
* Preserve established table and column naming unless explicitly changing them.

## 7. Laravel Standards

* Prefer route model binding when compatible with current project behavior.
* Use eager loading for required relations.
* Avoid business logic inside Blade templates.
* Avoid raw queries when Eloquent or Query Builder is appropriate.
* Do not use mass assignment without confirming `$fillable` or `$guarded`.
* Validate all request data server-side.
* Escape frontend output unless HTML is intentionally sanitized.
* Protect private files through guarded controllers or signed/private delivery.
* Use Laravel storage APIs rather than hardcoded filesystem paths.
* Use scheduler commands that are idempotent.
* Use queues only when the project infrastructure supports them.

## 8. Status and Lifecycle Rules

Do not invent, rename, normalize, or reinterpret statuses without consulting the approved status documentation.

For public services, follow their documented lifecycle contracts.

Current shared order lifecycle must remain compatible with:

* Draft
* Pending
* Approved
* Paid
* Completed as a fulfillment milestone
* Canceled
* Rejected
* Invalid
* Deleted

Legacy statuses must only be handled through documented read compatibility.

Do not mix:

* Order status
* Reservation status
* Payment confirmation status
* Fulfillment state
* Internal SPK status

Every status transition must be validated and auditable.

## 9. Frontend Rules

* Reuse the project’s existing Blade components and design system.
* Keep frontend styling consistent across pages.
* Modify SCSS source files when SCSS is the maintained source.
* Do not patch compiled CSS as the primary solution unless the project explicitly maintains compiled CSS directly.
* Avoid duplicate or conflicting CSS and JavaScript.
* Do not initialize the same plugin more than once.
* Use namespaced selectors for shared widgets.
* Preserve responsive behavior.
* Do not modify large numbers of Blade files when the change can safely be implemented through a shared component, shared initializer, or centralized stylesheet.

For date and time controls:

* Reuse the approved picker library.
* Use one centralized initializer.
* Use one shared theme.
* Remove obsolete initializers and overlapping styles only after confirming they are unused.
* Prevent recursive events and duplicate initialization.
* Preserve submitted date formats expected by the backend.

## 10. Testing Rules

Every implementation must include the most relevant verification.

Prioritize:

1. Feature tests for user-facing flows.
2. Authorization and ownership tests.
3. Validation tests.
4. Status-transition tests.
5. Pricing and financial calculation tests.
6. Idempotency and duplicate-submission tests.
7. Regression tests for fixed bugs.

Never claim that tests passed unless they were actually run.

Report:

* Commands executed
* Number of tests passed or failed
* Relevant manual verification
* Tests not run and why

## 11. Token and Context Efficiency

Do not repeatedly scan the entire repository when the affected module is already known.

Use this sequence:

1. Read documentation index.
2. Read the relevant module documentation.
3. Search for specific routes, models, services, methods, views, selectors, or status values.
4. Inspect only directly related files.
5. Expand the search only when dependencies require it.

Do not print entire files when a concise diff or focused summary is sufficient.

Do not repeat analysis already established in the same task.

## 12. Communication Format

Before changing code, provide:

* Understanding of the task
* Existing flow found
* Files likely affected
* Risks or ambiguities
* Proposed implementation

After changing code, provide:

* Root cause
* Implementation summary
* Files changed
* Database impact
* Security impact
* Tests and commands run
* Remaining risks
* Recommended next step

Do not state that a feature is complete when verification is incomplete.

## 13. Stop Conditions

Stop and request explicit approval before:

* Destructive database operations
* Breaking API or route changes
* Removing columns or tables
* Replacing a major package
* Large cross-module refactors
* Changing an approved lifecycle contract
* Modifying production configuration
* Changing payment architecture
* Performing changes outside the requested service

For ordinary implementation decisions inside the approved scope, make the safest reasonable choice and continue without unnecessary questions.

## 14. Definition of Done

A task is complete only when:

* The requested behavior is implemented.
* Existing project conventions are followed.
* No unrelated functionality is changed.
* Validation and authorization are present.
* Database changes are safe and reversible.
* Relevant tests pass.
* The diff has been reviewed.
* Documentation is updated when architecture, lifecycle, or behavior changes.
* The final report clearly states what was and was not verified.
