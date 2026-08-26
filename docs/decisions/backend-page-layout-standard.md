# Backend Create Edit Detail Layout Standard

Status: active
Updated: 2026-08-14

This decision is the authoritative UI contract for backend Create, Edit, and
Detail pages. Module documentation may add service-specific behavior, but it
must not override this shared layout standard without a new approved decision.

## Scope

This standard applies whenever the project creates, edits, or standardizes a
backend page whose primary purpose is:

- Create a resource.
- Edit a resource.
- Show resource detail.

Index, dashboard, modal-only, report, and public frontend pages are outside this
document unless they explicitly opt into the same layout primitives.

Backend admin modal-only detail surfaces may opt into the shared modal detail
standard in this document when they display a compact resource detail inside an
index or parent detail page.

## Document Hierarchy

Backend Create/Edit/Detail work must follow this hierarchy:

```text
AGENTS.md
docs/README.md
docs/decisions/backend-page-layout-standard.md
module documentation
actual implementation
```

## Canonical Layout

All backend Create/Edit/Detail pages use the shared backend shell, hero or page
header, breadcrumb toolbar when applicable, and a two-column content layout:

```text
Header
Main | Sidebar
Actions
```

The canonical implementation uses existing project primitives:

- `x-backend.page-hero`
- `x-backend.breadcrumb-toolbar`
- `backend-page-toolbar`
- `x-backend.detail-layout`
- `backend-detail-main`
- `backend-detail-side`
- `backend-panel`
- `backend-form-panel`
- `backend-form-panel__body`
- `backend-section-header`
- `backend-form`
- `backend-form-grid`
- `backend-form-field`
- `backend-form-control`
- `backend-form-actions`

Do not create page-specific layout themes such as `activity-create-theme` or
`hotel-edit-theme` for normal Create/Edit/Detail layout concerns.

The standardized backend Activity Create, Edit, Detail, and Gallery pages are
the current reference implementation for this contract. Future backend
Create/Edit/Detail standardization work should match the Activity page
structure, density, spacing, section hierarchy, and sidebar behavior before
introducing module-specific variations.

Backend Hotel Room Add/Edit pages are the Room-specific reference for keeping
Create and Edit information architecture parallel. Room Create/Edit pages must
use the same main section order and reserve the sidebar for status, parent Hotel
context, metadata, summaries, and related management actions.

Canonical page order:

```text
Page hero
Breadcrumb/status toolbar
Feedback alerts
Two-column detail layout
Main semantic panels
Right admin sidebar
Bottom form actions when the page mutates data
```

## Breadcrumb Toolbar Standard

Backend Create, Edit, Detail, and standardized index pages must use the shared
`x-backend.breadcrumb-toolbar` component for breadcrumbs and any compact
right-side page context such as status badges. The component is the canonical
theme entry point and outputs the legacy-compatible `backend-page-toolbar`
wrapper, so existing styles and pages remain compatible while pages migrate.

Use this structure:

```blade
<x-backend.breadcrumb-toolbar
    class="module-toolbar"
    :items="[
        ['label' => 'Admin Panel', 'url' => route('admin.panel-main.view')],
        ['label' => 'Activities', 'url' => route('admin.activities.index')],
    ]"
    current="Add Activity"
>
    <x-slot name="actions">
        <span class="backend-status-badge backend-status-badge--draft">Draft</span>
    </x-slot>
</x-backend.breadcrumb-toolbar>
```

Requirements:

- first breadcrumb item points to `route('admin.panel-main.view')`;
- use the `actions` slot for status badges or contextual toolbar actions;
- do not create module-specific breadcrumb spacing or separator styles;
- long labels must truncate through the shared SCSS, not page-specific CSS;
- legacy manual breadcrumb markup remains supported only for compatibility and
  should be replaced when the page is next standardized.

Use `backend-panel` for major page sections only. Do not nest cards inside
cards, do not create a separate card for every field, and do not use decorative
summary cards to fill empty space.

Create and Edit form sections should add `backend-form-panel` to the section and
wrap fields in `backend-form-panel__body` when the section uses a header plus
form fields. The shared wrapper owns the border, header divider, and inner
spacing so modules do not recreate page-specific panel padding rules.

## Responsive Layout

Desktop layout:

```text
Main 70% | Sidebar 30%
```

Tablet and mobile layout:

```text
Main
Sidebar
```

Requirements:

- no horizontal overflow;
- no fixed desktop-only width;
- sidebar moves below main content naturally;
- actions remain visible and usable;
- no separate mobile markup.

Use the shared `x-backend.detail-layout` / `backend-detail-layout` grid unless a
documented exception is approved.

The desktop column ratio is mandatory for backend Create, Edit, and Detail
pages:

```text
Main content: 70%
Right sidebar: 30%
```

The shared `backend-detail-layout` stylesheet owns this ratio. Page-specific
Create/Edit/Detail styles must not override the main/sidebar columns unless an
approved module decision documents a concrete exception.

## Sidebar Standard

Every backend Create, Edit, and Detail page must provide a right sidebar. The
sidebar must contain useful operational context, not decorative filler and not
duplicates of editable primary fields.

The `x-backend.detail-layout` component owns the sidebar wrapper. Page views
must place sidebar cards directly inside `<x-slot name="side">` and must not add another `<aside class="backend-detail-side">` inside that slot. Nesting the
sidebar wrapper breaks the shared grid, sticky behavior, and spacing.

Each sidebar card must use shared primitives:

```blade
<section class="backend-panel backend-detail-side-card">
    <div class="backend-section-header">
        <div>
            <span class="backend-section-header__label">Status</span>
            <h2>Current State</h2>
        </div>
        <p>Short admin-only context.</p>
    </div>

    <div class="backend-detail-side-card__body">
        <dl class="backend-detail-side-list">
            <div>
                <dt>Created</dt>
                <dd>{{ $resource->created_at }}</dd>
            </div>
        </dl>
    </div>

    <div class="backend-detail-side-actions">
        <a class="backend-button backend-button-secondary" href="#">Action</a>
    </div>
</section>
```

Use `backend-detail-side-list` for both `<ul><li>` guidance lists and
`<dl><div><dt><dd>` metadata lists. Use `backend-detail-side-card__body` when a
card contains lists, copy blocks, or grouped metadata below the header. Use
`backend-detail-side-actions` for full-width sidebar actions.

The main column is the source of truth for service/resource information such as
partner, supplier, title, location, duration, capacity, validity, pricing,
availability, customer-facing copy, cover, and gallery content. The sidebar must
not repeat those fields merely for convenience. Sidebar content is reserved for
admin-only supplemental context, record metadata, maintenance state, permitted
actions, and concise guidance that helps the admin operate the page.

Sidebar content priority:

1. Status.
2. Record metadata.
3. Admin-only maintenance state.
4. Permitted actions.
5. Help or guidance.

### Create Sidebar

Recommended content:

- initial status;
- creation guidance;
- important requirements;
- admin-only related context that is not already present in the main form;
- calculated preview when it can come from canonical server logic;
- publishing or service rules.

The Create sidebar must not trust hidden inputs for status, author, price,
ownership, or other authoritative values.

### Edit Sidebar

Recommended content:

- current status;
- author;
- created and updated timestamps;
- last modified by when available;
- admin-only lifecycle or maintenance state;
- permitted contextual actions.

Action visibility must follow authorization and server-side guards.

### Detail Sidebar

Recommended content:

- status;
- metadata;
- author;
- created and updated timestamps;
- record identifiers or audit hints;
- admin-only maintenance state;
- permitted contextual actions.

Do not move core resource content into the sidebar only to fill space.
Do not duplicate service/resource fields from the main column in the sidebar.

## Main Content Standard

The main column contains the primary information or editable fields. Group
fields semantically using sections that match the resource information
architecture.

Common section groups:

- Basic Information.
- Operational Information.
- Pricing.
- Availability.
- Cover and Media.
- Content and Translations.
- Policies and Additional Information.

Do not create one card per individual field. Prefer:

```text
Section title
Short contextual description
Fields
```

Create, Edit, and Detail pages for the same resource should preserve the same
information architecture so staff can move between them predictably.

The Activity information architecture is the default backend reference:

```text
Basic Information
Gallery or Cover and Media
Operational Information
Pricing
Content and Translations
Policies and Additional Information when applicable
```

For Detail pages:

- Basic Information should place the primary image or cover on the left and the
  core resource profile on the right when a cover image exists.
- Gallery must be its own main-column panel below Basic Information. It should
  show images and permitted actions only, without duplicating partner, pricing,
  validity, capacity, or other service facts.
- Operational Information, Pricing, and Content panels follow Gallery in that
  order when present.
- Rich text/content translation panels must use equal-height display blocks so
  one language column is not visually shorter or taller than the others.

For Create and Edit pages:

- Place Cover and Media as the first main-column panel when the resource has a
  primary cover image. Activity Create/Edit is the reference implementation for
  this ordering.
- Place price validity fields inside Pricing when the validity date defines the
  active price window. Activity `Valid Until` belongs beside Contract Rate and
  Markup, not in Operational Information.
- Preserve the remaining section order from Detail whenever the fields exist.
- Keep lifecycle status controls in a dedicated first sidebar card when status
  is edited on the same form, so admin can manage publication state before
  secondary context and actions.
- Keep editable service fields in the main column.
- Keep cover upload/change controls in the main column.
- Put form submit controls in the canonical bottom `backend-form-actions`
  toolbar.
- Use the right sidebar only for status, metadata, maintenance context,
  guidance, and permitted secondary actions.

## Translation Group Standard

Any field set using the database shape:

```text
field
field_traditional
field_simplified
```

must be displayed as one logical translation group, not as three unrelated
vertical sections.

Language order is mandatory:

```text
English
Traditional Chinese
Simplified Chinese
```

Desktop:

```text
Field Title
Short purpose description

English | Traditional Chinese | Simplified Chinese
input   | input               | input
```

Mobile:

```text
Field Title
Short purpose description

English
input
Traditional Chinese
input
Simplified Chinese
input
```

Each translation group must have:

- one field title;
- one short description;
- one responsive language grid;
- explicit language headings;
- consistent input or editor height;
- validation errors next to the relevant language field.

Rich text translation groups use `data-backend-richtext="true"` and must not
initialize a duplicate editor from Blade.

## Form Controls

Use shared backend controls:

- `backend-form-control`;
- `backend-form-grid`;
- `backend-form-field`;
- canonical select styling;
- canonical validation state;
- `data-backend-picker="date"` for date fields;
- `data-backend-money-unit` for monetary fields;
- `data-backend-richtext="true"` or `false` for textarea behavior.

Do not add inline styles, inline event handlers, `.date-picker`, duplicate date
initializers, duplicate rich text initializers, or custom button styling for
ordinary page actions.

## Modal Detail Standard

Backend admin modals that show resource detail data should use the shared
`backend-modal-detail` pattern when a visual preview plus structured metadata is
needed.

Use this structure:

```blade
<div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
        <section class="backend-modal">
            <div class="backend-modal__header">...</div>
            <div class="backend-modal__body">
                <div class="backend-modal-detail">
                    <figure class="backend-modal-detail__media">...</figure>
                    <div class="backend-modal-detail__content">
                        <section class="backend-modal-detail__summary">...</section>
                        <dl class="backend-modal-detail__grid">...</dl>
                        <section class="backend-modal-detail__section">...</section>
                    </div>
                </div>
            </div>
            <div class="backend-modal__footer">...</div>
        </section>
    </div>
</div>
```

Desktop modal detail layout:

```text
Media / Cover | Detail Information
```

Mobile modal detail layout:

```text
Media / Cover
Detail Information
```

Requirements:

- use `modal-xl` for detail modals that need image plus metadata;
- place the primary image or visual preview in `backend-modal-detail__media`;
- place author/admin-readable summary and metadata in
  `backend-modal-detail__content`;
- use `backend-modal-detail__grid` for facts and
  `backend-modal-detail__section` for longer copy;
- do not create one-off modal grid systems when this shared pattern fits;
- keep modal actions in `backend-modal__footer` using canonical backend action
  buttons.

Hotel Room detail modal is the first Accommodation implementation of this
standard and is the reference for Room/Suites & Villa author-facing detail
modals.

## Monetary Display Standard

Backend pages that display service prices must show both USD and IDR whenever
the canonical pricing result can provide both values. The canonical/service
currency remains the authoritative value for storage and order snapshots; the
second currency is an admin display aid derived from the same pricing result and
stored exchange rate.

Display order is mandatory:

```text
USD
IDR
```

Activity is the first implementation of this standard. Activity backend Index,
Detail, and Edit pricing diagnostics must display calculated selling price in
USD and IDR through `ActivityPricingService` / `ActivityPricingQuote`, not by
duplicating conversion formulas in Blade.

## Image Fields

Image fields remain in the main column. Cover management should show a compact
current or selected preview plus upload/change input. The recommended preview
width is 120-180px with `object-fit: cover`.

Do not move primary cover management into the sidebar.

## Page Actions

Create pages:

```text
Cancel
Create / Save
```

Edit pages:

```text
Cancel / Back
Save Changes
```

Detail pages:

```text
Back
Edit
Contextual lifecycle actions
```

Use `backend-form-actions`, `backend-button-primary`,
`backend-button-secondary`, and `backend-button-danger` as appropriate.

## Table Action Alignment

Backend tables that expose row actions must align those actions to the right.
Use `data-label="Action"` or `data-label="Actions"` on the action `<td>` and
wrap multiple controls in `backend-table-actions`. New action headers may add
`backend-table-action-column` so the header label follows the same right-aligned
column rhythm.

## Mandatory Compliance

Any new or standardized backend Create/Edit/Detail page MUST:

1. use canonical two-column layout;
2. provide a right sidebar;
3. use semantic main sections;
4. use canonical form controls;
5. use horizontal translation groups;
6. maintain responsive behavior;
7. reuse shared UI components;
8. avoid page-specific duplicate layout CSS;
9. preserve information architecture between Create/Edit/Detail;
10. pass applicable project structure tests.

Exceptions require explicit documentation and reason. Codex must not silently
deviate from this standard.
