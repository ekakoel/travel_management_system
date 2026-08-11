# Service Registry Navigation Standard

Status: active
Updated: 2026-08-11

## Contract

The `services` table is the source of truth for service navigation metadata and
visibility. A record is eligible for the public Services menu, frontend footer
Our Services section, and backend Services menu only when `services.status` is
`Active`.

Navigation routes are resolved by
`App\Services\Navigation\ServiceNavigationRegistry`; Blade files must not build
route names by concatenating `services.nicname`. An active record is rendered
in each menu only when that menu's resolved named route exists, preventing an
incomplete registry entry from publishing a broken link. Registry health is
ready only when both its public and backend routes are available.

The canonical Tour Package slug is `tour-packages`. The legacy slugs `tours`,
`tour`, and `tour-package` remain read-compatible and resolve to:

- public: `view.tour-packages-service`;
- backend: `admin.tour-packages.index`.

Adding a database registry record does not generate controllers, routes, or
views. A new service type must first add its route contract to the centralized
registry; the Admin Panel health check flags active records that have no valid
navigation route.

## Metadata and Security

- `name`, `nicname`, and `icon` are validated server-side.
- `nicname` is unique and uses lowercase kebab-case.
- `icon` stores CSS class names only; legacy `<i>` markup is normalized to its
  class list for compatibility.
- status transitions are determined by the server, not hidden form fields.
- the authenticated user is the audit actor; submitted actor IDs are ignored.
- registry writes and their audit logs run in one database transaction.

## Query Ownership

`BackendNavigationService` loads active services once per request and produces
the shared navigation view model. Backend navbar, frontend navbar, and frontend
footer only render that view model and do not execute registry queries or infer
routes. Static footer content remains cached, while its service links are
merged outside that cache so enable/disable changes apply on the next request.

Legacy `footer_links` rows in the `services` group are compatibility data and
are not a visibility source. Quick Links and Policies remain controlled by the
Footer Manager.
