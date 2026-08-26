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
                'review' => 'Review & Create',
            ];

            $errorStep = 'basic';
            $stepFieldMap = [
                'basic' => ['name', 'name_traditional', 'name_simplified', 'code', 'type', 'duration_days', 'duration_nights'],
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
        <main class="main-container tour-form-page tour-form-page--create">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="tour-form-hero"
                    eyebrow="Tour Package"
                    title="Add Tour Package"
                    description="Create the Tour master profile through a focused wizard before price tiers and public booking availability are managed."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.tour-packages.index') }}" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Tours
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <x-backend.breadcrumb-toolbar
                    class="tour-form-toolbar"
                    :items="[
                        ['label' => 'Admin Panel', 'url' => route('admin.panel-main.view')],
                        ['label' => 'Tour Packages', 'url' => route('admin.tour-packages.index')],
                    ]"
                    current="Add Tour Package"
                >
                    <x-slot name="actions">
                        <span class="backend-status-badge backend-status-badge--draft">Draft by default</span>
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

                <form id="tourCreateForm" class="backend-form" action="{{ route('admin.tours.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <x-backend.detail-layout class="tour-create-layout">
                        <x-slot name="main">
                            <div class="tour-create-wizard" data-tour-create-wizard data-error-step="{{ $errorStep }}">
                                <nav class="tour-create-wizard__steps" aria-label="Create Tour steps">
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
                                                        <input type="text" id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="backend-form-control @error($field['name']) is-invalid @enderror" placeholder="{{ $field['placeholder'] }}" value="{{ old($field['name']) }}" required>
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
                                                <input type="text" id="code" name="code" class="backend-form-control @error('code') is-invalid @enderror" placeholder="Insert tour code" value="{{ old('code') }}" required>
                                                @error('code')
                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="backend-form-field">
                                                <label for="type" class="backend-form-label">Type <span>*</span></label>
                                                <select id="type" name="type" class="backend-form-control @error('type') is-invalid @enderror" required>
                                                    <option value="">Select Type</option>
                                                    @foreach ($types as $type)
                                                        <option value="{{ $type->id }}" @selected((string) old('type') === (string) $type->id)>{{ $type->type }}</option>
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
                                                        <option value="{{ $day }}" @selected((string) old('duration_days', 1) === (string) $day)>{{ $day }}D</option>
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
                                                        <option value="{{ $night }}" @selected((string) old('duration_nights', 0) === (string) $night)>{{ $night > 0 ? $night.'N' : '-' }}</option>
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
                                        <p>Optional structured stops used as the source for the public route map and generated itinerary.</p>
                                    </div>

                                    <div class="backend-form-panel__body tour-form-panel__body">
                                        @include('backend.operations.tours.partials.tour-location-repeater', [
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
                                        <p>Customer-facing copy grouped by English, Traditional Chinese, and Simplified Chinese.</p>
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
                                                            <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="textarea_editor backend-form-control @error($field['name']) is-invalid @enderror" data-backend-richtext="true" placeholder="{{ $field['placeholder'] }}" @if ($group['required']) required @endif>{{ old($field['name']) }}</textarea>
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
                                        <p>Upload the primary image used for backend inventory and public Tour Package listing.</p>
                                    </div>

                                    <div class="backend-form-panel__body tour-form-panel__body">
                                        <div class="backend-form-grid">
                                            <div class="backend-form-field is-wide">
                                                <label for="cover" class="backend-form-label">Cover Image <span>*</span></label>
                                                <div class="tour-form-cover-control">
                                                    <figure class="tour-form-cover-preview" data-tour-cover-preview></figure>
                                                    <div class="tour-form-cover-input">
                                                        <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" data-tour-cover-input data-tour-cover-preview-target="[data-tour-cover-preview]" required>
                                                        <span class="tour-file-status" data-tour-cover-status data-tour-cover-status-default="No cover selected">No cover selected</span>
                                                        <small class="form-text text-muted">This cover image is used as the primary Tour visual.</small>
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
                                            <h2>Review & Create</h2>
                                        </div>
                                        <p>Confirm the master profile summary before submitting one final create request.</p>
                                    </div>

                                    <div class="backend-form-panel__body tour-form-panel__body">
                                        <div class="tour-create-review-layout">
                                            <section class="tour-create-review-block tour-create-review-block--wide">
                                                <div class="tour-create-review-block__header">
                                                    <span>Basic Information</span>
                                                    <strong data-tour-summary-name>Not filled</strong>
                                                </div>
                                                <dl class="tour-create-review-list">
                                                    <div>
                                                        <dt>Tour Code</dt>
                                                        <dd data-tour-review-code>Not filled</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Type</dt>
                                                        <dd data-tour-summary-type>Not selected</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Duration</dt>
                                                        <dd data-tour-summary-duration>1D / 0N</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Status</dt>
                                                        <dd>Draft</dd>
                                                    </div>
                                                </dl>
                                                <div class="tour-create-review-language-grid">
                                                    <div>
                                                        <span>English</span>
                                                        <strong data-tour-review-name-en>Not filled</strong>
                                                    </div>
                                                    <div>
                                                        <span>Traditional Chinese</span>
                                                        <strong data-tour-review-name-traditional>Not filled</strong>
                                                    </div>
                                                    <div>
                                                        <span>Simplified Chinese</span>
                                                        <strong data-tour-review-name-simplified>Not filled</strong>
                                                    </div>
                                                </div>
                                            </section>

                                            <section class="tour-create-review-block">
                                                <div class="tour-create-review-block__header">
                                                    <span>Route & Itinerary</span>
                                                    <strong data-tour-summary-route>0 day(s), 0 stop(s)</strong>
                                                </div>
                                                <div class="tour-create-review-route" data-tour-review-route-days>
                                                    <p>No route stops added yet.</p>
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
                                                    <strong data-tour-summary-cover>No cover selected</strong>
                                                </div>
                                                <p>Cover image is uploaded only during final submit.</p>
                                            </section>
                                        </div>
                                    </div>
                                </section>

                                <section class="backend-page-toolbar backend-form-actions tour-form-actions tour-create-wizard__actions">
                                    <div>
                                        <span class="tour-create-wizard__current">Current step: <strong data-tour-wizard-current-label>Basic Information</strong></span>
                                    </div>
                                    <div class="backend-page-toolbar__actions">
                                        <a href="{{ route('admin.tour-packages.index') }}" class="backend-button backend-button-secondary">Cancel</a>
                                        <button type="button" class="backend-button backend-button-secondary" data-tour-wizard-back>Back</button>
                                        <button type="button" class="backend-button backend-button-primary" data-tour-wizard-next>
                                            Continue
                                        </button>
                                        <button type="submit" class="backend-button backend-button-primary" data-tour-wizard-submit hidden>
                                            <i class="fa fa-check"></i>
                                            Create Tour
                                        </button>
                                    </div>
                                </section>
                            </div>
                        </x-slot>

                        <x-slot name="side">
                            <section class="backend-panel backend-detail-side-card tour-create-context-panel tour-status-side-card">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Initial Status</span>
                                        <h2><span class="backend-status-badge backend-status-badge--draft">Draft</span></h2>
                                    </div>
                                    <p>New Tour Packages are created as Draft by the server.</p>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card tour-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Pricing Context</span>
                                        <h2>TourPrice Boundary</h2>
                                    </div>
                                    <p>Pricing is managed separately after the Tour master profile is created.</p>
                                </div>
                                <div class="backend-detail-side-card__body">
                                    <ul class="backend-detail-side-list">
                                        <li>
                                            <span>Price tiers</span>
                                            <strong>Configured from Tour detail</strong>
                                            <small>Create Tour does not calculate or write TourPrice records.</small>
                                        </li>
                                        <li>
                                            <span>Public quote</span>
                                            <strong>Server authoritative</strong>
                                            <small>Public Tour pricing continues to use the canonical pricing service.</small>
                                        </li>
                                    </ul>
                                </div>
                            </section>
                        </x-slot>
                    </x-backend.detail-layout>
                </form>
            </div>
        </main>
    @endcanany
@endsection
