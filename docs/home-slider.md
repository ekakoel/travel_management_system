# Home Slider Feature

## 1. Overview

Add a dynamic, database-driven Hero Slider to the Online Bali Kami Tour homepage.

The Home Slider must be manageable from the backend and displayed on the frontend homepage using reusable Blade components and the existing frontend asset architecture.

The implementation must follow the existing project standards for:

* Laravel architecture
* Blade components
* Vite asset management
* SCSS structure
* Backend page layout
* Authorization and page access control
* Form validation
* File upload handling
* Responsive UI
* Performance optimization
* Feature and project-structure testing

The feature must not introduce a separate frontend framework or an unnecessary slider dependency unless the existing project architecture requires it.

---

# 2. Goals

The feature must provide:

1. Dynamic homepage hero slides.
2. Backend management for slides.
3. Desktop and mobile-specific images.
4. Slide ordering.
5. Active/inactive status.
6. Optional publication scheduling.
7. Optional CTA button.
8. Automatic slide rotation.
9. Previous/next navigation.
10. Pagination dots.
11. Responsive behavior.
12. Accessible controls.
13. Optimized image loading.
14. Reusable frontend Blade component.
15. Clean separation between data, presentation, styling, and behavior.

---

# 3. Non-Goals

The initial implementation should NOT include:

* A third-party slider library unless technically necessary.
* Drag-and-drop ordering unless explicitly required.
* Complex animation builders.
* Multiple slider positions on the homepage.
* A generic page-builder system.
* Hard-coded slide content in the Blade template.
* Inline JavaScript.
* Inline CSS/SCSS.
* A separate Vite entry point solely for the slider.

These can be added later if required.

---

# 4. Recommended Architecture

```text
Admin
  │
  ▼
Home Slider Management
  │
  ▼
home_sliders
  │
  ▼
HomeSlider Model
  │
  ▼
Home Controller
  │
  ▼
Home Blade
  │
  ▼
<x-frontend.home.slider />
  │
  ├── Blade
  ├── SCSS
  └── JavaScript
```

The frontend must consume slider data from the backend rather than querying the database directly from JavaScript.

---

# 5. Database

Create a new table:

```text
home_sliders
```

Recommended schema:

```php
Schema::create('home_sliders', function (Blueprint $table) {
    $table->id();

    $table->string('title');
    $table->text('description')->nullable();

    $table->string('button_text')->nullable();
    $table->string('button_url')->nullable();

    $table->string('image');
    $table->string('mobile_image')->nullable();

    $table->unsignedInteger('sort_order')->default(0);

    $table->boolean('is_active')->default(true);

    $table->dateTime('start_at')->nullable();
    $table->dateTime('end_at')->nullable();

    $table->timestamps();
});
```

## 5.1 Field Description

| Field          | Type             | Required | Description                  |
| -------------- | ---------------- | -------: | ---------------------------- |
| `id`           | bigint           |      Yes | Primary key                  |
| `title`        | string           |      Yes | Main slide title             |
| `description`  | text             |       No | Supporting slide description |
| `button_text`  | string           |       No | CTA label                    |
| `button_url`   | string           |       No | CTA destination              |
| `image`        | string           |      Yes | Desktop image path           |
| `mobile_image` | string           |       No | Mobile-specific image path   |
| `sort_order`   | unsigned integer |      Yes | Display order                |
| `is_active`    | boolean          |      Yes | Enable/disable slide         |
| `start_at`     | datetime         |       No | Publication start            |
| `end_at`       | datetime         |       No | Publication end              |
| `created_at`   | timestamp        |      Yes | Laravel timestamp            |
| `updated_at`   | timestamp        |      Yes | Laravel timestamp            |

---

# 6. Model

Create:

```text
app/Models/HomeSlider.php
```

The model must use mass-assignment protection.

Recommended implementation:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HomeSlider extends Model
{
    protected $fillable = [
        'title',
        'description',
        'button_text',
        'button_url',
        'image',
        'mobile_image',
        'sort_order',
        'is_active',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query) {
                $query
                    ->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function (Builder $query) {
                $query
                    ->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            });
    }
}
```

The `active()` scope must be the canonical way for the homepage to determine which slides are currently publishable.

---

# 7. Frontend Query

The homepage controller must retrieve only active slides.

Recommended:

```php
$sliders = HomeSlider::active()
    ->orderBy('sort_order')
    ->get();
```

The controller must pass the collection to the homepage view.

Example:

```php
public function index()
{
    $sliders = HomeSlider::active()
        ->orderBy('sort_order')
        ->get();

    return view('frontend.home', compact('sliders'));
}
```

The exact controller and view names must be determined from the existing project structure before implementation.

Do not create duplicate Home controllers or routes if an existing implementation already exists.

---

# 8. Frontend Blade Component

Create a reusable component:

```text
resources/views/components/frontend/home/slider.blade.php
```

The homepage should consume it through:

```blade
<x-frontend.home.slider :sliders="$sliders" />
```

The homepage Blade file must not contain the complete slider implementation.

This keeps the homepage clean and allows the slider to evolve independently.

---

# 9. Slider HTML Requirements

Each slide must contain:

* Image
* Overlay
* Content
* Optional title
* Optional description
* Optional CTA

Recommended semantic structure:

```html
<section class="home-hero-slider">
    <div class="home-hero-slider__track">

        <article class="home-hero-slide">
            <picture>
                ...
            </picture>

            <div class="home-hero-slide__overlay"></div>

            <div class="home-hero-slide__content">
                ...
            </div>
        </article>

    </div>
</section>
```

Use semantic class names following the existing frontend naming conventions.

---

# 10. Image Handling

Each slide supports:

```text
Desktop Image
Mobile Image
```

The frontend should use the `<picture>` element when a mobile image is available.

Example:

```blade
<picture>
    @if ($slider->mobile_image)
        <source
            media="(max-width: 767px)"
            srcset="{{ asset('storage/' . $slider->mobile_image) }}"
        >
    @endif

    <img
        src="{{ asset('storage/' . $slider->image) }}"
        alt="{{ $slider->title }}"
    >
</picture>
```

---

# 11. Image Upload Requirements

Allowed formats:

```text
jpg
jpeg
png
webp
```

Recommended maximum upload size:

```text
5 MB
```

The backend must validate uploads using Laravel validation rules.

Example:

```php
'image' => [
    'required',
    'image',
    'mimes:jpg,jpeg,png,webp',
    'max:5120',
],
```

For the mobile image:

```php
'mobile_image' => [
    'nullable',
    'image',
    'mimes:jpg,jpeg,png,webp',
    'max:5120',
],
```

Files should be stored through Laravel's filesystem abstraction.

Example:

```php
$imagePath = $request
    ->file('image')
    ->store('home-sliders', 'public');
```

Do not use `move_uploaded_file()`.

---

# 12. Storage

Slider images should be stored on the Laravel public filesystem:

```text
storage/app/public/home-sliders/
```

Ensure the application has:

```bash
php artisan storage:link
```

The implementation must not expose internal filesystem paths.

---

# 13. Backend Management

Create a backend module:

```text
Home Slider Management
```

Recommended location:

```text
Backend
└── Content
    └── Home Sliders
```

The module should provide:

```text
Index
Create
Edit
Delete
```

A detail page is optional unless it provides meaningful additional value.

---

# 14. Backend Index

The index should display at minimum:

| Column   | Description     |
| -------- | --------------- |
| Preview  | Thumbnail       |
| Title    | Slide title     |
| Status   | Active/inactive |
| Schedule | Start/end date  |
| Order    | Display order   |
| Actions  | Edit/delete     |

The page must follow the existing backend page layout standard.

Do not introduce a new backend UI pattern.

Use the existing shared components and primitives, including where applicable:

```text
backend-page-toolbar
backend-panel
backend-section-header
backend-form-*
x-backend.page-hero
x-backend.detail-layout
```

The exact components must follow the current project implementation.

---

# 15. Create/Edit Form

The Create and Edit pages must follow the project's canonical backend form layout.

Recommended fields:

```text
Title
Description
Desktop Image
Mobile Image
Button Text
Button URL
Sort Order
Active
Start At
End At
```

The implementation must reuse the project's shared form controls.

Do not introduce:

* Inline styles
* Legacy Bootstrap-only layouts
* Duplicate date picker implementations
* Custom input components when a canonical project component already exists

---

# 16. Image Preview

The Edit page should show the existing desktop and mobile images.

Recommended:

```text
Desktop Image
[ current image preview ]

Replace Desktop Image
[ upload ]

Mobile Image
[ current image preview ]

Replace Mobile Image
[ upload ]
```

The preview should be compact and appropriate for backend UI.

---

# 17. Status

The slide must have:

```text
Active
Inactive
```

Inactive slides must never appear on the public homepage.

The frontend must not rely on JavaScript to hide inactive slides.

Filtering must happen server-side through the `active()` scope.

---

# 18. Scheduling

A slide may optionally have:

```text
Start At
End At
```

Rules:

### No schedule

If both are null:

```text
Immediately active while is_active = true
```

### Start date only

The slide becomes visible from `start_at` onward.

### End date only

The slide remains visible until `end_at`.

### Both dates

The slide is visible only within the configured period.

The canonical active query must be equivalent to:

```text
is_active = true
AND
(start_at IS NULL OR start_at <= now)
AND
(end_at IS NULL OR end_at >= now)
```

---

# 19. Ordering

Slides must be ordered using:

```text
sort_order ASC
```

Example:

```text
1 → Discover Bali
2 → Luxury Bali
3 → Bali Adventure
```

The initial implementation may use a numeric field.

Drag-and-drop ordering is considered a future enhancement.

---

# 20. CTA

CTA is optional.

If both:

```text
button_text
button_url
```

exist, render the CTA.

If either is missing, do not render an empty button.

Example:

```blade
@if ($slider->button_text && $slider->button_url)
    <a href="{{ $slider->button_url }}">
        {{ $slider->button_text }}
    </a>
@endif
```

URLs must be safely escaped through Blade.

---

# 21. JavaScript Behavior

The slider must support:

1. Initial slide.
2. Automatic rotation.
3. Next.
4. Previous.
5. Pagination dots.
6. Pause on desktop hover.
7. Resume after interaction.
8. Graceful behavior when only one slide exists.
9. No JavaScript errors when no slider exists.

Recommended autoplay interval:

```text
6000 ms
```

Recommended transition:

```text
700 ms
```

These values may be adjusted during UI testing.

---

# 22. JavaScript Initialization

The slider must be initialized through the project's existing Vite/JavaScript entry point.

Do not create a separate Vite build solely for the slider unless required.

Recommended pattern:

```javascript
export function initHomeSlider() {
    const slider = document.querySelector('.home-hero-slider');

    if (!slider) {
        return;
    }

    // slider initialization
}
```

This ensures pages without the slider do not generate errors.

---

# 23. Autoplay

Autoplay should:

* Start automatically.
* Move to the next slide.
* Stop when the user hovers over the slider.
* Resume when the pointer leaves.
* Restart after manual navigation.

Recommended interval:

```text
6 seconds
```

---

# 24. Navigation

Desktop:

```text
Previous
Next
```

Mobile:

The previous/next buttons may be hidden if swipe navigation is implemented.

Pagination dots should remain available.

---

# 25. Responsive Behavior

The slider must be responsive for:

```text
Desktop
Tablet
Mobile
```

Recommended breakpoints should follow the existing frontend responsive system rather than introducing arbitrary breakpoints.

At mobile sizes:

* Use mobile image where available.
* Reduce title size.
* Reduce content width.
* Keep CTA accessible.
* Prevent horizontal overflow.
* Ensure content does not overlap controls.

---

# 26. Accessibility

The slider must provide:

* Semantic `<section>`.
* Semantic `<article>` for slides.
* Meaningful image `alt`.
* Accessible previous button.
* Accessible next button.
* Accessible pagination buttons.

Example:

```html
aria-label="Previous slide"
```

and:

```html
aria-label="Next slide"
```

Pagination:

```html
aria-label="Go to slide 1"
```

Buttons must remain keyboard accessible.

---

# 27. Image Loading Performance

The first slide is the most important image and should be loaded eagerly.

Recommended:

```blade
loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
```

The first image should not be unnecessarily lazy-loaded because it is likely to be the Largest Contentful Paint element.

Subsequent images should use lazy loading.

---

# 28. Image Dimensions

Recommended source dimensions:

### Desktop

```text
1920 × 800 px
```

or:

```text
1920 × 900 px
```

### Mobile

```text
1080 × 1350 px
```

or:

```text
1080 × 1440 px
```

Images should be compressed before upload.

WebP should be preferred where practical.

---

# 29. SCSS

Create a dedicated SCSS component:

```text
resources/frontend/scss/components/_home-slider.scss
```

The SCSS must be imported through the existing frontend SCSS entry point.

Do not use:

```blade
<style>
...
</style>
```

inside the Blade template.

Do not add large inline style blocks.

---

# 30. CSS Naming

Use a consistent component naming scheme.

Recommended:

```text
.home-hero-slider
.home-hero-slider__track
.home-hero-slide
.home-hero-slide__media
.home-hero-slide__overlay
.home-hero-slide__content
.home-hero-slider__control
.home-hero-slider__dots
```

Avoid generic names such as:

```text
.slider
.slide
.content
.next
.prev
```

because these can easily conflict with other frontend components.

---

# 31. Empty State

If there are no active sliders, the homepage must remain functional.

The slider component should render nothing when:

```php
$sliders->isEmpty()
```

Recommended future enhancement:

```text
Dynamic Slider
      ↓
if empty
      ↓
Default Static Hero
```

This fallback may be implemented if the homepage requires a permanent Hero section.

---

# 32. Security

The implementation must:

* Validate uploaded files.
* Escape Blade output.
* Validate URLs appropriately.
* Prevent unauthorized backend access.
* Use existing authentication middleware.
* Use existing page-access authorization.
* Avoid direct filesystem manipulation.
* Avoid raw HTML output unless explicitly required.

Do not bypass the project's existing authorization system.

---

# 33. Authorization

The Home Slider backend must follow the existing backend permission/page-access system.

If the project uses:

```text
UiConfig
CheckPageAccess
```

or an equivalent authorization mechanism, Home Slider routes must be integrated into that mechanism.

Do not create an admin route that bypasses existing page access controls.

---

# 34. Route Naming

Use conventional resource naming.

Recommended:

```text
home-sliders.index
home-sliders.create
home-sliders.store
home-sliders.edit
home-sliders.update
home-sliders.destroy
```

The exact prefix/group must follow the existing backend route structure.

Do not duplicate an existing route name.

---

# 35. Validation

Create/update validation must cover:

```text
title
description
button_text
button_url
image
mobile_image
sort_order
is_active
start_at
end_at
```

Recommended constraints:

```php
'title' => [
    'required',
    'string',
    'max:255',
],

'description' => [
    'nullable',
    'string',
],

'button_text' => [
    'nullable',
    'string',
    'max:100',
],

'button_url' => [
    'nullable',
    'string',
    'max:2048',
],

'sort_order' => [
    'required',
    'integer',
    'min:0',
],
```

Date validation should ensure:

```text
end_at >= start_at
```

when both are provided.

---

# 36. Delete Behavior

When a slide is deleted, associated images should also be removed from the public filesystem where appropriate.

The implementation must avoid leaving orphaned uploaded images.

Image deletion should be handled safely and consistently with the project's existing media/file management patterns.

---

# 37. Existing Image Replacement

When editing a slide:

1. Keep the existing image if no replacement is uploaded.
2. Store the new image if uploaded.
3. Remove the previous image after successful replacement.
4. Do not delete the old image before the new upload has succeeded.

The same rules apply to the mobile image.

---

# 38. Frontend Example

Expected usage:

```blade
@extends('layouts.frontend')

@section('content')

    <x-frontend.home.slider :sliders="$sliders" />

    {{-- Other homepage sections --}}

@endsection
```

The homepage should not contain slider business logic.

---

# 39. Testing

Add automated tests where appropriate.

## 39.1 Model Tests

Test:

* Active slide is returned.
* Inactive slide is excluded.
* Future slide is excluded.
* Expired slide is excluded.
* Valid scheduled slide is returned.

Example scenarios:

```text
is_active = true
start_at = null
end_at = null
→ included
```

```text
is_active = false
→ excluded
```

```text
start_at > now()
→ excluded
```

```text
end_at < now()
→ excluded
```

---

# 40. Feature Tests

Test:

### Admin

* Authorized user can access index.
* Authorized user can create a slide.
* Authorized user can edit a slide.
* Authorized user can delete a slide.
* Unauthorized user cannot access the management page.
* Invalid upload is rejected.
* Required fields are validated.

### Frontend

* Homepage loads successfully.
* Active slides are displayed.
* Inactive slides are not displayed.
* Future slides are not displayed.
* Expired slides are not displayed.
* CTA is displayed only when configured.

---

# 41. Project Structure Test

If the project already has:

```text
tests/Feature/ProjectStructureStandardTest.php
```

the new implementation must comply with it.

Do not modify existing project standards simply to make the new slider implementation pass.

If a new reusable standard is introduced, document it first and update the project-structure test accordingly.

---

# 42. Vite Build Verification

After implementation:

```bash
npm run build
```

must complete successfully.

Check for:

* JavaScript compilation errors.
* SCSS compilation errors.
* Missing imports.
* Duplicate entry points.
* Unused or broken assets.

---

# 43. Laravel Verification

Run:

```bash
php artisan optimize:clear
```

Then:

```bash
php artisan route:list
```

Verify:

* Homepage route.
* Admin Home Slider routes.
* Middleware.
* Authorization.

Run the relevant test suite:

```bash
php artisan test
```

Existing tests must remain passing.

---

# 44. Browser QA

## Desktop

Verify:

* Hero image fills the intended area.
* Text is readable.
* Overlay works.
* CTA works.
* Previous button works.
* Next button works.
* Dots work.
* Autoplay works.
* Hover pause works.

## Tablet

Verify:

* Correct image scaling.
* No content overflow.
* Navigation remains usable.

## Mobile

Verify:

* Mobile image is used.
* Text remains readable.
* CTA remains accessible.
* No horizontal scrolling.
* Navigation does not overlap content.
* Slider height remains appropriate.

---

# 45. Recommended Initial UX

The initial Hero Slider should use:

```text
Autoplay: ON
Interval: 6 seconds
Transition: Fade
Navigation: Previous / Next
Pagination: Dots
Pause on hover: ON
Mobile image: Supported
Swipe: Optional initial implementation
```

A fade transition is recommended for the first implementation because it fits a premium travel website better than a fast horizontal carousel.

---

# 46. Future Enhancements

Potential future features:

```text
Drag & Drop Ordering
Swipe Gesture
Video Hero
Multiple CTA Buttons
Eyebrow Text
Text Alignment
Overlay Opacity
Per-slide Transition
Per-slide Autoplay Duration
Campaign Scheduling
Analytics
Click Tracking
A/B Testing
Image Focal Point
CDN Integration
Automatic WebP/AVIF Conversion
```

These should not be implemented unless there is a concrete requirement.

---

# 47. Implementation Rules

The developer/Codex implementing this feature must follow these rules:

1. Audit the existing homepage structure before modifying files.
2. Identify the existing homepage route and controller.
3. Identify the existing frontend Blade layout.
4. Identify the existing Vite entry point.
5. Identify the existing SCSS entry point.
6. Reuse existing frontend components where possible.
7. Reuse existing backend form components.
8. Reuse existing authorization/page-access mechanisms.
9. Do not introduce duplicate infrastructure.
10. Do not introduce inline CSS.
11. Do not introduce inline JavaScript.
12. Do not create unnecessary Vite entry points.
13. Do not bypass existing backend standards.
14. Do not hard-code production slide content in Blade.
15. Do not bypass Laravel filesystem handling.
16. Do not use a third-party slider library unless justified.
17. Preserve existing functionality.
18. Keep the implementation modular.
19. Add tests for the new behavior.
20. Run the existing test suite before finalization.

---

# 48. Recommended Implementation Sequence

Implementation must proceed in this order:

```text
1. Audit existing Home architecture
       ↓
2. Confirm route/controller/view
       ↓
3. Confirm Vite/SCSS/JS entry points
       ↓
4. Create migration
       ↓
5. Create HomeSlider model
       ↓
6. Implement active scope
       ↓
7. Implement backend CRUD
       ↓
8. Integrate authorization
       ↓
9. Implement image upload/storage
       ↓
10. Implement frontend Blade component
       ↓
11. Implement SCSS
       ↓
12. Implement JavaScript
       ↓
13. Integrate with Home
       ↓
14. Responsive optimization
       ↓
15. Accessibility review
       ↓
16. Performance review
       ↓
17. Automated tests
       ↓
18. Browser QA
       ↓
19. Build verification
       ↓
20. Final cleanup
```

---

# 49. Definition of Done

The feature is considered complete only when:

### Backend

* [ ] Home Slider database table exists.
* [ ] HomeSlider model exists.
* [ ] Active scope works correctly.
* [ ] Admin CRUD works.
* [ ] Image upload works.
* [ ] Image replacement works.
* [ ] Old images are cleaned up appropriately.
* [ ] Validation works.
* [ ] Authorization works.
* [ ] Scheduling works.
* [ ] Ordering works.

### Frontend

* [ ] Slider is displayed on Home.
* [ ] Dynamic database content is displayed.
* [ ] Desktop image works.
* [ ] Mobile image works.
* [ ] CTA works.
* [ ] Autoplay works.
* [ ] Previous/next works.
* [ ] Pagination dots work.
* [ ] Hover pause works.
* [ ] Responsive layout works.
* [ ] Accessibility requirements are met.

### Code Quality

* [ ] No unnecessary third-party dependency.
* [ ] No inline CSS.
* [ ] No inline JavaScript.
* [ ] No duplicate frontend infrastructure.
* [ ] Existing project standards are preserved.
* [ ] Existing tests remain passing.
* [ ] New feature tests pass.
* [ ] `npm run build` passes.
* [ ] `php artisan test` passes.

---

# 50. Final Technical Principle

The Home Slider should be treated as a **content-managed frontend component**, not merely a visual JavaScript widget.

The canonical flow is:

```text
Backend Management
       ↓
Database
       ↓
HomeSlider Model
       ↓
Active + Scheduled Query
       ↓
Home Controller
       ↓
Blade Component
       ↓
SCSS + JavaScript
       ↓
Responsive Homepage
```

The implementation must preserve this separation so that future features such as promotions, campaign scheduling, analytics, or additional homepage content can be added without rewriting the slider architecture.
