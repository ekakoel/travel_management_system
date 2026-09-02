@extends('layouts.head')

@section('title', __('messages.Tour'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/tours/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/tours/forms.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev','posAuthor'])
        @php
            $wizardSteps = [
                'basic' => 'Basic Information',
                'route' => 'Route & Itinerary',
                'content' => 'Content & Translations',
                'media' => 'Media',
                'review' => 'Review & Update',
            ];

            $errorStep = 'basic';
            $stepFieldMap = [
                'basic' => ['name', 'name_traditional', 'name_simplified', 'code', 'type', 'duration_days', 'duration_nights', 'status'],
                'route' => ['locations'],
                'content' => [
                    'short_description', 'short_description_traditional', 'short_description_simplified',
                    'description', 'description_traditional', 'description_simplified',
                    'package_highlights', 'package_highlights_traditional', 'package_highlights_simplified',
                    'include', 'include_traditional', 'include_simplified',
                    'exclude', 'exclude_traditional', 'exclude_simplified',
                    'additional_info', 'additional_info_traditional', 'additional_info_simplified',
                    'cancellation_policy', 'cancellation_policy_traditional', 'cancellation_policy_simplified',
                ],
                'media' => ['cover'],
            ];

            foreach ($stepFieldMap as $step => $fields) {
                foreach ($fields as $field) {
                    if ($errors->has($field) || $errors->has($field . '.*')) {
                        $errorStep = $step;
                        break 2;
                    }
                }
            }

            $statusTone = strtolower($tour->status ?? '') === 'active' ? 'active' : 'draft';
            $statusLabel = $tour->status ?: 'Draft';
            $routeDays = $tour->locations->pluck('day_number')->filter()->unique()->count();
            $routeStops = $tour->locations->count();
            $hasLegacyItinerary = trim(strip_tags((string) $tour->itinerary)) !== ''
                || trim(strip_tags((string) $tour->itinerary_traditional)) !== ''
                || trim(strip_tags((string) $tour->itinerary_simplified)) !== '';
            $usesLegacyItinerary = $routeStops === 0 && $hasLegacyItinerary;
            $coverUrl = $tour->cover ? asset('storage/tours/tours-cover/' . $tour->cover) : null;
            $currentCoverLabel = $tour->cover ?: 'Current cover preserved';

            $nameTranslationGroup = [
                'title' => 'Tour Name',
                'description' => 'Public package name in the canonical language order.',
                'fields' => [
                    ['name' => 'name', 'label' => 'English', 'placeholder' => 'Insert tour package name'],
                    ['name' => 'name_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional tour package name'],
                    ['name' => 'name_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified tour package name'],
                ],
            ];

            $contentTranslationGroups = [
                [
                    'title' => 'Short Description',
                    'description' => 'Brief overview used on public listing and detail surfaces.',
                    'required' => true,
                    'fields' => [
                        ['name' => 'short_description', 'label' => 'English', 'placeholder' => 'Insert short description'],
                        ['name' => 'short_description_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional short description'],
                        ['name' => 'short_description_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified short description'],
                    ],
                ],
                [
                    'title' => 'Description',
                    'description' => 'Full customer-facing package description.',
                    'required' => true,
                    'fields' => [
                        ['name' => 'description', 'label' => 'English', 'placeholder' => 'Insert description'],
                        ['name' => 'description_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional description'],
                        ['name' => 'description_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified description'],
                    ],
                ],
                [
                    'title' => 'Package Highlights',
                    'description' => 'Optional highlights shown as supporting sales copy.',
                    'required' => false,
                    'fields' => [
                        ['name' => 'package_highlights', 'label' => 'English', 'placeholder' => 'Insert package highlights'],
                        ['name' => 'package_highlights_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional package highlights'],
                        ['name' => 'package_highlights_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified package highlights'],
                    ],
                ],
                [
                    'title' => 'Include',
                    'description' => 'Services and benefits included in this Tour Package.',
                    'required' => false,
                    'fields' => [
                        ['name' => 'include', 'label' => 'English', 'placeholder' => 'Insert inclusions'],
                        ['name' => 'include_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional inclusions'],
                        ['name' => 'include_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified inclusions'],
                    ],
                ],
                [
                    'title' => 'Exclude',
                    'description' => 'Items or services excluded from the package.',
                    'required' => false,
                    'fields' => [
                        ['name' => 'exclude', 'label' => 'English', 'placeholder' => 'Insert exclusions'],
                        ['name' => 'exclude_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional exclusions'],
                        ['name' => 'exclude_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified exclusions'],
                    ],
                ],
                [
                    'title' => 'Additional Information',
                    'description' => 'Extra requirements, preparation notes, or restrictions.',
                    'required' => false,
                    'fields' => [
                        ['name' => 'additional_info', 'label' => 'English', 'placeholder' => 'Insert additional information'],
                        ['name' => 'additional_info_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional additional information'],
                        ['name' => 'additional_info_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified additional information'],
                    ],
                ],
                [
                    'title' => 'Cancellation Policy',
                    'description' => 'Cancellation rules shown to customers before booking.',
                    'required' => true,
                    'fields' => [
                        ['name' => 'cancellation_policy', 'label' => 'English', 'placeholder' => 'Insert cancellation policy'],
                        ['name' => 'cancellation_policy_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional cancellation policy'],
                        ['name' => 'cancellation_policy_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified cancellation policy'],
                    ],
                ],
            ];
        @endphp

        <div class="mobile-menu-overlay"></div>
        <main class="main-container tour-form-page tour-form-page--edit">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="tour-form-hero"
                    eyebrow="Tour Package"
                    title="Edit Tour Package"
                    description="Update the Tour master profile through the same structured wizard used by Create Tour."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.tours.show', $tour->id) }}" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Detail
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <x-backend.breadcrumb-toolbar
                    class="tour-form-toolbar"
                    :items="[
                        ['label' => 'Admin Panel', 'url' => route('admin.panel-main.view')],
                        ['label' => 'Tour Packages', 'url' => route('admin.tour-packages.index')],
                        ['label' => $tour->name, 'url' => route('admin.tours.show', $tour->id)],
                    ]"
                    current="Edit"
                >
                    <x-slot name="actions">
                        <span class="backend-status-badge backend-status-badge--{{ $statusTone }}">{{ $statusLabel }}</span>
                    </x-slot>
                </x-backend.breadcrumb-toolbar>

                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback tour-form-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger">
                                <strong>Action needs attention.</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session()->has('success'))
                            <div class="backend-alert backend-alert--success">
                                <strong>{{ session('success') }}</strong>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ session('error') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <form id="tourEditForm" class="backend-form" action="{{ route('admin.tours.update', $tour->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    <x-backend.detail-layout class="tour-create-layout tour-edit-layout">
                        <x-slot name="main">
                            <div class="tour-create-wizard" data-tour-create-wizard data-error-step="{{ $errorStep }}">
                                <nav class="tour-create-wizard__steps" aria-label="Edit Tour steps">
                                    @foreach ($wizardSteps as $key => $label)
                                        <button type="button" class="tour-create-wizard__step" data-tour-wizard-step="{{ $key }}" data-step-title="{{ $label }}">
                                            <span>{{ $loop->iteration }}</span>
                                            <strong>{{ $label }}</strong>
                                        </button>
                                    @endforeach
                                </nav>

                                <section class="backend-panel tour-form-panel backend-form-panel tour-create-wizard__panel" data-tour-wizard-panel="basic">
                                    <div class="backend-section-header tour-form-panel__heading">
                                        <div>
                                            <span class="backend-section-header__label">Step 1</span>
                                            <h2>Basic Information</h2>
                                        </div>
                                        <p>Master fields used to identify and classify the Tour Package.</p>
                                    </div>

                                    <div class="backend-form-panel__body tour-form-panel__body">
                                        <section class="backend-translation-group" data-backend-translation-group>
                                            <div class="backend-translation-group__header">
                                                <h3 class="backend-translation-group__title">{{ $nameTranslationGroup['title'] }}</h3>
                                                <p class="backend-translation-group__description">{{ $nameTranslationGroup['description'] }}</p>
                                            </div>

                                            <div class="backend-translation-grid">
                                                @foreach ($nameTranslationGroup['fields'] as $field)
                                                    <div class="backend-translation-field">
                                                        <label for="{{ $field['name'] }}" class="backend-form-label">{{ $field['label'] }} <span>*</span></label>
                                                        <input type="text" id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="backend-form-control @error($field['name']) is-invalid @enderror" placeholder="{{ $field['placeholder'] }}" value="{{ old($field['name'], $tour->{$field['name']}) }}" required>
                                                        @error($field['name'])
                                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                @endforeach
                                            </div>
                                        </section>

                                        <div class="backend-form-grid">
                                            <div class="backend-form-field">
                                                <label for="code" class="backend-form-label">Tour Code <span>*</span></label>
                                                <input type="text" id="code" name="code" class="backend-form-control @error('code') is-invalid @enderror" placeholder="Insert tour code" value="{{ old('code', $tour->code) }}" required>
                                                @error('code')
                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="backend-form-field">
                                                <label for="type" class="backend-form-label">Type <span>*</span></label>
                                                <select id="type" name="type" class="backend-form-control @error('type') is-invalid @enderror" required>
                                                    <option value="">Select Type</option>
                                                    @foreach ($types as $type)
                                                        <option value="{{ $type->id }}" @selected((string) old('type', $tour->type_id) === (string) $type->id)>{{ $type->type }}</option>
                                                    @endforeach
                                                </select>
                                                @error('type')
                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="backend-form-field">
                                                <label for="duration_days" class="backend-form-label">Duration Days <span>*</span></label>
                                                <select id="duration_days" name="duration_days" class="backend-form-control @error('duration_days') is-invalid @enderror" required>
                                                    @foreach (range(1, 7) as $day)
                                                        <option value="{{ $day }}" @selected((string) old('duration_days', $tour->duration_days) === (string) $day)>{{ $day }}D</option>
                                                    @endforeach
                                                </select>
                                                @error('duration_days')
                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="backend-form-field">
                                                <label for="duration_nights" class="backend-form-label">Duration Nights <span>*</span></label>
                                                <select id="duration_nights" name="duration_nights" class="backend-form-control @error('duration_nights') is-invalid @enderror" required>
                                                    @foreach (range(0, 7) as $night)
                                                        <option value="{{ $night }}" @selected((string) old('duration_nights', $tour->duration_nights) === (string) $night)>{{ $night > 0 ? $night.'N' : '-' }}</option>
                                                    @endforeach
                                                </select>
                                                @error('duration_nights')
                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="backend-panel tour-form-panel backend-form-panel tour-create-wizard__panel" data-tour-wizard-panel="route">
                                    <div class="backend-section-header tour-form-panel__heading">
                                        <div>
                                            <span class="backend-section-header__label">Step 2</span>
                                            <h2>Route & Itinerary</h2>
                                        </div>
                                        <p>Structured stops are the authoritative route and generated itinerary source for Tours that have route locations.</p>
                                    </div>

                                    <div class="backend-form-panel__body tour-form-panel__body">
                                        @if ($usesLegacyItinerary)
                                            <div class="backend-alert backend-alert--warning">
                                                <strong>This Tour uses legacy manual itinerary content.</strong>
                                                <span>Add Route & Itinerary stops to migrate it to the structured itinerary system. Saving other fields will preserve the legacy itinerary columns.</span>
                                            </div>
                                        @endif

                                        @include('backend.operations.tours.partials.tour-location-repeater', [
                                            'tour' => $tour,
                                            'allowEmptyLocations' => true,
                                            'compactLocationCards' => true,
                                        ])
                                    </div>
                                </section>

                                <section class="backend-panel tour-form-panel backend-form-panel tour-create-wizard__panel" data-tour-wizard-panel="content">
                                    <div class="backend-section-header tour-form-panel__heading">
                                        <div>
                                            <span class="backend-section-header__label">Step 3</span>
                                            <h2>Content & Translations</h2>
                                        </div>
                                        <p>Customer-facing copy grouped by English, Traditional Chinese, and Simplified Chinese. Manual itinerary editors are intentionally not part of this step.</p>
                                    </div>

                                    <div class="backend-form-panel__body tour-form-panel__body">
                                        @foreach ($contentTranslationGroups as $group)
                                            <section class="backend-translation-group" data-backend-translation-group>
                                                <div class="backend-translation-group__header">
                                                    <h3 class="backend-translation-group__title">{{ $group['title'] }}</h3>
                                                    <p class="backend-translation-group__description">{{ $group['description'] }}</p>
                                                </div>

                                                <div class="backend-translation-grid">
                                                    @foreach ($group['fields'] as $field)
                                                        <div class="backend-translation-field">
                                                            <label for="{{ $field['name'] }}" class="backend-form-label">
                                                                {{ $field['label'] }}
                                                                @if ($group['required'])
                                                                    <span>*</span>
                                                                @endif
                                                            </label>
                                                            <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="textarea_editor backend-form-control @error($field['name']) is-invalid @enderror" data-backend-richtext="true" placeholder="{{ $field['placeholder'] }}" @if ($group['required']) required @endif>{{ old($field['name'], $tour->{$field['name']}) }}</textarea>
                                                            @error($field['name'])
                                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </section>
                                        @endforeach
                                    </div>
                                </section>

                                <section class="backend-panel tour-form-panel backend-form-panel tour-create-wizard__panel" data-tour-wizard-panel="media">
                                    <div class="backend-section-header tour-form-panel__heading">
                                        <div>
                                            <span class="backend-section-header__label">Step 4</span>
                                            <h2>Media</h2>
                                        </div>
                                        <p>Review the current cover and optionally choose a new primary Tour Package image.</p>
                                    </div>

                                    <div class="backend-form-panel__body tour-form-panel__body">
                                        <div class="backend-form-grid">
                                            <div class="backend-form-field is-wide">
                                                <label for="cover" class="backend-form-label">Cover Image</label>
                                                <div class="tour-form-cover-control">
                                                    <figure class="tour-form-cover-preview" data-tour-cover-preview>
                                                        @if ($coverUrl)
                                                            <img src="{{ $coverUrl }}" alt="{{ $tour->name }} cover">
                                                        @endif
                                                    </figure>
                                                    <div class="tour-form-cover-input">
                                                        <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" data-tour-cover-input data-tour-cover-preview-target="[data-tour-cover-preview]" data-tour-cover-existing="{{ $currentCoverLabel }}">
                                                        <span class="tour-file-status" data-tour-cover-status data-tour-cover-status-default="{{ $currentCoverLabel }}">{{ $currentCoverLabel }}</span>
                                                        <small class="form-text text-muted">Leave empty to preserve the current cover.</small>
                                                        @error('cover')
                                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="backend-panel tour-form-panel backend-form-panel tour-create-wizard__panel" data-tour-wizard-panel="review">
                                    <div class="backend-section-header tour-form-panel__heading">
                                        <div>
                                            <span class="backend-section-header__label">Step 5</span>
                                            <h2>Review & Update</h2>
                                        </div>
                                        <p>Confirm the edited master profile summary before submitting the update.</p>
                                    </div>

                                    <div class="backend-form-panel__body tour-form-panel__body">
                                        <div class="tour-create-review-layout">
                                            <section class="tour-create-review-block tour-create-review-block--wide">
                                                <div class="tour-create-review-block__header">
                                                    <span>Basic Information</span>
                                                    <strong data-tour-summary-name>{{ $tour->name }}</strong>
                                                </div>
                                                <dl class="tour-create-review-list">
                                                    <div>
                                                        <dt>Tour Code</dt>
                                                        <dd data-tour-review-code>{{ $tour->code ?: 'Not filled' }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Type</dt>
                                                        <dd data-tour-summary-type>{{ $tour->type?->type ?: 'Not selected' }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Duration</dt>
                                                        <dd data-tour-summary-duration>{{ (int) $tour->duration_days }}D / {{ (int) $tour->duration_nights }}N</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Status</dt>
                                                        <dd data-tour-review-status>{{ $statusLabel }}</dd>
                                                    </div>
                                                </dl>
                                                <div class="tour-create-review-language-grid">
                                                    <div>
                                                        <span>English</span>
                                                        <strong data-tour-review-name-en>{{ $tour->name ?: 'Not filled' }}</strong>
                                                    </div>
                                                    <div>
                                                        <span>Traditional Chinese</span>
                                                        <strong data-tour-review-name-traditional>{{ $tour->name_traditional ?: 'Not filled' }}</strong>
                                                    </div>
                                                    <div>
                                                        <span>Simplified Chinese</span>
                                                        <strong data-tour-review-name-simplified>{{ $tour->name_simplified ?: 'Not filled' }}</strong>
                                                    </div>
                                                </div>
                                            </section>

                                            <section class="tour-create-review-block">
                                                <div class="tour-create-review-block__header">
                                                    <span>Route & Itinerary</span>
                                                    <strong data-tour-summary-route>{{ $routeDays }} day(s), {{ $routeStops }} stop(s)</strong>
                                                </div>
                                                <div class="tour-create-review-route" data-tour-review-route-days>
                                                    <p>{{ $routeStops ? 'Route stops loaded.' : 'No route stops added yet.' }}</p>
                                                </div>
                                            </section>

                                            <section class="tour-create-review-block">
                                                <div class="tour-create-review-block__header">
                                                    <span>Content & Translations</span>
                                                    <strong data-tour-review-content-summary>0 of 9 required fields filled</strong>
                                                </div>
                                                <div class="tour-create-review-content" data-tour-review-content-list></div>
                                            </section>

                                            <section class="tour-create-review-block">
                                                <div class="tour-create-review-block__header">
                                                    <span>Media</span>
                                                    <strong data-tour-summary-cover>{{ $currentCoverLabel }}</strong>
                                                </div>
                                                <p>Current cover is preserved unless a new image is selected.</p>
                                            </section>
                                        </div>
                                    </div>
                                </section>

                                <section class="backend-page-toolbar backend-form-actions tour-form-actions tour-create-wizard__actions">
                                    <div>
                                        <span class="tour-create-wizard__current">Current step: <strong data-tour-wizard-current-label>Basic Information</strong></span>
                                    </div>
                                    <div class="backend-page-toolbar__actions">
                                        <a href="{{ route('admin.tours.show', $tour->id) }}" class="backend-button backend-button-secondary">Cancel</a>
                                        <button type="button" class="backend-button backend-button-secondary" data-tour-wizard-back>Back</button>
                                        <button type="button" class="backend-button backend-button-primary" data-tour-wizard-next>
                                            Continue
                                        </button>
                                        <button type="submit" class="backend-button backend-button-primary" data-tour-wizard-submit>
                                            <i class="fa fa-check"></i>
                                            Update Tour
                                        </button>
                                    </div>
                                </section>
                            </div>
                        </x-slot>

                        <x-slot name="side">
                            <section class="backend-panel backend-detail-side-card tour-create-context-panel tour-status-side-card">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Current Status</span>
                                        <h2><span class="backend-status-badge backend-status-badge--{{ $statusTone }}">{{ $statusLabel }}</span></h2>
                                    </div>
                                    <p>Status is editable here and validated by the server.</p>
                                </div>
                                <div class="backend-detail-side-card__body">
                                    <div class="backend-form-field">
                                        <label for="status" class="backend-form-label">Status <span>*</span></label>
                                        <select id="status" name="status" class="backend-form-control @error('status') is-invalid @enderror" required>
                                            <option value="Active" @selected(old('status', $tour->status) === 'Active')>Active</option>
                                            <option value="Draft" @selected(old('status', $tour->status) === 'Draft')>Draft</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card tour-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Metadata</span>
                                        <h2>Record Context</h2>
                                    </div>
                                    <p>Operational timestamps and record identifiers for admin review.</p>
                                </div>
                                <div class="backend-detail-side-card__body">
                                    <dl class="backend-detail-side-list">
                                        <div>
                                            <dt>Tour ID</dt>
                                            <dd>#{{ $tour->id }}</dd>
                                        </div>
                                        <div>
                                            <dt>Created</dt>
                                            <dd>{{ optional($tour->created_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt>Updated</dt>
                                            <dd>{{ optional($tour->updated_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card tour-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Setup Progress</span>
                                        <h2>Wizard Coverage</h2>
                                    </div>
                                    <p>Use the main wizard as the editable source of truth.</p>
                                </div>
                                <div class="backend-detail-side-card__body">
                                    <ul class="backend-detail-side-list">
                                        <li><span>Basic Information</span><strong data-tour-summary-name>{{ $tour->name ?: 'Not filled' }}</strong></li>
                                        <li><span>Route & Itinerary</span><strong data-tour-summary-route>{{ $routeDays }} day(s), {{ $routeStops }} stop(s)</strong></li>
                                        <li><span>Content & Translations</span><strong data-tour-review-content-summary>0 of 9 required fields filled</strong></li>
                                        <li><span>Media</span><strong data-tour-summary-cover>{{ $currentCoverLabel }}</strong></li>
                                    </ul>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card tour-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Route Summary</span>
                                        <h2>{{ $routeStops }} Stop(s)</h2>
                                    </div>
                                    <p>{{ $usesLegacyItinerary ? 'Legacy manual itinerary is preserved until structured route stops are added.' : 'Structured locations drive the generated itinerary when available.' }}</p>
                                </div>
                                <div class="backend-detail-side-card__body">
                                    <dl class="backend-detail-side-list">
                                        <div>
                                            <dt>Days</dt>
                                            <dd>{{ $routeDays }}</dd>
                                        </div>
                                        <div>
                                            <dt>Active stops</dt>
                                            <dd>{{ $routeStops }}</dd>
                                        </div>
                                        <div>
                                            <dt>Legacy fallback</dt>
                                            <dd>{{ $usesLegacyItinerary ? 'Preserved' : 'Not active' }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card tour-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Related Actions</span>
                                        <h2>Tour Operations</h2>
                                    </div>
                                    <p>Pricing remains a separate Tour detail workflow.</p>
                                </div>
                                <div class="backend-detail-side-actions">
                                    <a href="{{ route('admin.tours.show', $tour->id) }}" class="backend-button backend-button-secondary">View Tour</a>
                                    <a href="{{ route('admin.tours.show', $tour->id) }}#prices" class="backend-button backend-button-secondary">Manage Tour Price</a>
                                    <a href="{{ route('admin.tour-packages.index') }}" class="backend-button backend-button-secondary">Back to Tours</a>
                                </div>
                            </section>
                        </x-slot>
                    </x-backend.detail-layout>
                </form>
            </div>
        </main>
    @endcanany
@endsection
