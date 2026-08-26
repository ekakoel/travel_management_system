@extends('layouts.head')

@section('title', __('messages.Activity'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/activities/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/activities/forms.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev','posAuthor','posAdm'])
        @php
            $statusTone = strtolower($activities->status) === 'active'
                ? 'active'
                : (strtolower($activities->status) === 'archived' ? 'muted' : 'draft');
            $validityValue = $activities->validity ? date('Y-m-d', strtotime($activities->validity)) : '';
            $validityLabel = $activities->validity ? date('d M Y', strtotime($activities->validity)) : '-';
            $translationGroups = [
                [
                    'title' => 'Description',
                    'description' => 'Short overview displayed on the public Activity page.',
                    'fields' => [
                        ['name' => 'description', 'label' => 'English', 'placeholder' => 'Insert description'],
                        ['name' => 'description_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional description'],
                        ['name' => 'description_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified description'],
                    ],
                ],
                [
                    'title' => 'Itinerary',
                    'description' => 'Sequence or schedule shown to customers before booking.',
                    'fields' => [
                        ['name' => 'itinerary', 'label' => 'English', 'placeholder' => 'Insert itinerary'],
                        ['name' => 'itinerary_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional itinerary'],
                        ['name' => 'itinerary_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified itinerary'],
                    ],
                ],
                [
                    'title' => 'Include',
                    'description' => 'Services and benefits included in this Activity.',
                    'fields' => [
                        ['name' => 'include', 'label' => 'English', 'placeholder' => 'Insert inclusions'],
                        ['name' => 'include_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional inclusions'],
                        ['name' => 'include_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified inclusions'],
                    ],
                ],
                [
                    'title' => 'Cancellation Policy',
                    'description' => 'Cancellation conditions shown before customers place an order.',
                    'fields' => [
                        ['name' => 'cancellation_policy', 'label' => 'English', 'placeholder' => 'Insert cancellation policy'],
                        ['name' => 'cancellation_policy_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional cancellation policy'],
                        ['name' => 'cancellation_policy_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified cancellation policy'],
                    ],
                ],
                [
                    'title' => 'Additional Information',
                    'description' => 'Extra customer-facing notes, restrictions, or preparation details.',
                    'fields' => [
                        ['name' => 'additional_info', 'label' => 'English', 'placeholder' => 'Insert additional information'],
                        ['name' => 'additional_info_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional additional information'],
                        ['name' => 'additional_info_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified additional information'],
                    ],
                ],
            ];
        @endphp

        <div class="mobile-menu-overlay"></div>
        <main class="main-container activity-form-page activity-form-page--edit">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    eyebrow="Operations Inventory"
                    title="Edit Activity"
                    description="Update profile, pricing, capacity, status, and customer-facing content for {{ $activities->name }}."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.activities.show', $activities->id) }}" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Detail
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar activity-form-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.activities.index') }}">Activities</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.activities.show', $activities->id) }}">{{ $activities->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--{{ $statusTone }}">{{ $activities->status }}</span>
                        <span class="backend-status-badge backend-status-badge--info">USD Rate: {{ $usdrates ? currencyFormatIdr($usdrates->rate) : 'Unavailable' }}</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback activity-form-feedback">
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

                <form id="activityEditForm" class="backend-form" action="{{ route('admin.activities.update', $activities->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    <x-backend.detail-layout class="activity-edit-layout">
                        <x-slot name="main">
                            <section class="backend-panel activity-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Cover / Media</span>
                                        <h2>Cover Image</h2>
                                    </div>
                                    <p>Review the current cover or choose a replacement. The new image is uploaded only when the form is saved.</p>
                                </div>

                                <div class="backend-form-grid">
                                    <div class="backend-form-field is-wide">
                                        <label for="cover" class="backend-form-label">Current Cover</label>
                                        <div class="activity-form-cover-control">
                                            <figure class="activity-form-cover-preview" data-activity-cover-preview>
                                                @if ($activities->coverUrl())
                                                    <img src="{{ $activities->coverUrl() }}" alt="{{ $activities->name }}" loading="lazy">
                                                @endif
                                            </figure>
                                            <div class="activity-form-cover-input">
                                                <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" data-activity-file-input data-activity-file-input-target="#activityCoverFileStatus" data-activity-cover-input data-activity-cover-preview-target="[data-activity-cover-preview]">
                                                <span id="activityCoverFileStatus" class="activity-file-status" data-activity-file-input-default="Keep existing cover">Keep existing cover</span>
                                                @error('cover')
                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel activity-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Basic Information</span>
                                        <h2>Activity Profile</h2>
                                    </div>
                                    <p>Core information used to identify, categorize, and assign this Activity.</p>
                                </div>

                                <div class="backend-form-grid">
                                    <div class="backend-form-field">
                                        <label for="name" class="backend-form-label">Activity Name</label>
                                        <input type="text" id="name" name="name" class="backend-form-control @error('name') is-invalid @enderror" placeholder="Insert activity name" value="{{ old('name', $activities->name) }}" required>
                                        @error('name')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="partners_id" class="backend-form-label">Partner</label>
                                        <select id="partners_id" name="partners_id" class="backend-form-control @error('partners_id') is-invalid @enderror" required>
                                            <option value="">Select Partner</option>
                                            @foreach ($partners as $partnerOption)
                                                <option value="{{ $partnerOption->id }}" @selected((string) old('partners_id', $activities->partners_id) === (string) $partnerOption->id)>{{ $partnerOption->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('partners_id')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="type" class="backend-form-label">Category / Type</label>
                                        <select id="type" name="type" class="backend-form-control @error('type') is-invalid @enderror" required>
                                            @foreach ($type as $activityType)
                                                <option value="{{ $activityType->type }}" @selected(old('type', $activities->type) === $activityType->type)>{{ $activityType->type }}</option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="location" class="backend-form-label">Location</label>
                                        <input type="text" id="location" name="location" class="backend-form-control @error('location') is-invalid @enderror" placeholder="Activity location" value="{{ old('location', $activities->location) }}" required>
                                        @error('location')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field is-wide">
                                        <label for="map" class="backend-form-label">Map</label>
                                        <input type="text" id="map" name="map" class="backend-form-control @error('map') is-invalid @enderror" placeholder="Google Maps link" value="{{ old('map', $activities->map) }}">
                                        @error('map')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel activity-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Operations</span>
                                        <h2>Operational Information</h2>
                                    </div>
                                    <p>Define duration and guest limits used by booking validation.</p>
                                </div>

                                <div class="backend-form-grid">
                                    <div class="backend-form-field">
                                        <label for="duration" class="backend-form-label">Duration</label>
                                        <select id="duration" name="duration" class="backend-form-control @error('duration') is-invalid @enderror" required>
                                            @foreach (['15 Minutes', '30 Minutes', '1 Hour', '2 Hours', '3 Hours', '4 Hours', '5 Hours', '6 Hours', '7 Hours', '8 Hours', '9 Hours', '10 Hours'] as $duration)
                                                <option value="{{ $duration }}" @selected(old('duration', $activities->duration) === $duration)>{{ $duration === '10 Hours' ? 'Full Day (10 hours)' : $duration }}</option>
                                            @endforeach
                                        </select>
                                        @error('duration')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="min_pax" class="backend-form-label">Minimum Pax</label>
                                        <input type="number" id="min_pax" name="min_pax" value="{{ old('min_pax', $activities->min_pax) }}" class="backend-form-control @error('min_pax') is-invalid @enderror" placeholder="Minimum pax" min="1" required>
                                        @error('min_pax')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="qty" class="backend-form-label">Capacity</label>
                                        <input type="number" id="qty" name="qty" value="{{ old('qty', $activities->qty) }}" class="backend-form-control @error('qty') is-invalid @enderror" placeholder="Maximum pax" min="1" required>
                                        @error('qty')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel activity-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Pricing</span>
                                        <h2>Pricing Inputs</h2>
                                    </div>
                                    <p>Master pricing inputs and price validity only. Published prices are calculated by the canonical Activity pricing service.</p>
                                </div>

                                <div class="backend-form-grid">
                                    <div class="backend-form-field">
                                        <label for="contract_rate" class="backend-form-label">Contract Rate</label>
                                        <input type="text" inputmode="numeric" id="contract_rate" name="contract_rate" class="backend-form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate', $activities->contract_rate) }}" required data-backend-money-unit="IDR">
                                        @error('contract_rate')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="markup" class="backend-form-label">Markup</label>
                                        <input type="text" inputmode="numeric" id="markup" name="markup" class="backend-form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup', $activities->markup) }}" data-backend-money-unit="USD">
                                        @error('markup')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="validity" class="backend-form-label">Valid Until</label>
                                        <input type="text" id="validity" name="validity" value="{{ old('validity', $validityValue) }}" class="backend-form-control @error('validity') is-invalid @enderror" placeholder="YYYY-MM-DD" required data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
                                        <p class="backend-form-help">Bookings cannot use a travel date after this price validity date.</p>
                                        @error('validity')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel activity-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Content</span>
                                        <h2>Content and Translations</h2>
                                    </div>
                                    <p>Customer-facing copy is optional, grouped by language, and displayed in the canonical language order.</p>
                                </div>

                                @foreach ($translationGroups as $group)
                                    <section class="backend-translation-group" data-backend-translation-group>
                                        <div class="backend-translation-group__header">
                                            <h3 class="backend-translation-group__title">{{ $group['title'] }}</h3>
                                            <p class="backend-translation-group__description">{{ $group['description'] }}</p>
                                        </div>

                                        <div class="backend-translation-grid">
                                            @foreach ($group['fields'] as $field)
                                                <div class="backend-translation-field">
                                                    <label for="{{ $field['name'] }}" class="backend-form-label">{{ $field['label'] }}</label>
                                                    <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="textarea_editor backend-form-control @error($field['name']) is-invalid @enderror" data-backend-richtext="true" placeholder="{{ $field['placeholder'] }}">{{ old($field['name'], $activities->{$field['name']}) }}</textarea>
                                                    @error($field['name'])
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            @endforeach
                                        </div>
                                    </section>
                                @endforeach
                            </section>
                            <section class="backend-page-toolbar backend-form-actions activity-form-actions">
                                <div class="backend-page-toolbar__actions">
                                    <a href="{{ route('admin.activities.show', $activities->id) }}" class="backend-button backend-button-secondary">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="backend-button backend-button-primary">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                        Save Activity
                                    </button>
                                </div>
                            </section>
                        </x-slot>

                        <x-slot name="side">
                            <section class="backend-panel backend-detail-side-card activity-edit-context-panel activity-status-side-card">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Current Status</span>
                                        <h2><span class="backend-status-badge backend-status-badge--{{ $statusTone }}">{{ $activities->status }}</span></h2>
                                    </div>
                                    <p>Manage lifecycle status for this Activity.</p>
                                </div>

                                <div class="backend-form-field">
                                    <label for="status" class="backend-form-label">Status</label>
                                    <select id="status" name="status" class="backend-form-control @error('status') is-invalid @enderror" required>
                                        @foreach (['Active', 'Draft', 'Archived'] as $status)
                                            <option value="{{ $status }}" @selected(old('status', $activities->status) === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <p class="backend-form-help">Expired Activities cannot be published as Active.</p>
                                    @error('status')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </section>

                            <section
                                class="backend-panel backend-detail-side-card activity-edit-context-panel"
                                data-activity-pricing-preview
                                data-activity-pricing-preview-rate="{{ $usdrates?->rate }}"
                                data-activity-pricing-preview-tax="{{ $activityTax?->tax }}"
                                data-activity-pricing-preview-unavailable="{{ __('messages.Price cannot be calculated.') }}"
                            >
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Record Metadata</span>
                                        <h2>Activity Record</h2>
                                    </div>
                                    <p>Administrative metadata for this Activity record.</p>
                                </div>
                                <ul class="backend-detail-side-list">
                                    <li>
                                        <span>Author ID</span>
                                        <strong>{{ $activities->author_id ?: '-' }}</strong>
                                        <small>On save, the authenticated admin becomes the latest editor.</small>
                                    </li>
                                    <li>
                                        <span>Created At</span>
                                        <strong>{{ $activities->created_at ? $activities->created_at->format('d M Y H:i') : '-' }}</strong>
                                        <small>Initial record timestamp.</small>
                                    </li>
                                    <li>
                                        <span>Updated At</span>
                                        <strong>{{ $activities->updated_at ? $activities->updated_at->format('d M Y H:i') : '-' }}</strong>
                                        <small>Last persisted update timestamp.</small>
                                    </li>
                                </ul>
                            </section>

                            <section class="backend-panel backend-detail-side-card activity-edit-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Pricing Diagnostics</span>
                                        <h2>{{ $activityPricing->priceAvailable() ? __('messages.Calculated Price') : __('messages.Price cannot be calculated.') }}</h2>
                                    </div>
                                    <p>Administrative readiness check resolved through ActivityPricingService.</p>
                                </div>
                                <ul class="backend-detail-side-list">
                                    <li>
                                        <span>Selling Price</span>
                                        <strong data-activity-pricing-preview-usd>{{ $activityPricing->priceAvailable() ? currencyFormatUsd($activityPricing->sellingPrice()) : '-' }}</strong>
                                        @if ($activityPricing->priceAvailable())
                                            <small data-activity-pricing-preview-idr>{{ currencyFormatIdr($activityPricing->sellingPriceIdr()) }}</small>
                                        @else
                                            <small data-activity-pricing-preview-idr>-</small>
                                        @endif
                                        <small data-activity-pricing-preview-message>{{ $activityPricing->priceAvailable() ? 'Available for backend reference even when the record is Draft.' : $activityPricing->pricingUnavailableMessage() }}</small>
                                    </li>
                                    <li>
                                        <span>USD Rate Source</span>
                                        <strong>{{ $usdrates ? 'Stored database rate' : __('messages.USD Rate is not available.') }}</strong>
                                        <small>Pricing uses the latest stored USD sell rate and never a browser-provided value.</small>
                                    </li>
                                    <li>
                                        <span>Lifecycle Reminder</span>
                                        <strong>Draft is manual</strong>
                                        <small>Updating validity does not automatically force this Activity back to Active.</small>
                                    </li>
                                </ul>
                                <div class="backend-detail-side-actions">
                                    <a href="{{ route('admin.activities.show', $activities->id) }}" class="backend-toolbar-action">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                        View Activity
                                    </a>
                                    <a href="{{ route('admin.activities.gallery.edit', $activities->id) }}" class="backend-toolbar-action">
                                        <i class="fa fa-picture-o" aria-hidden="true"></i>
                                        Manage Gallery
                                    </a>
                                </div>
                            </section>
                        </x-slot>
                    </x-backend.detail-layout>
                </form>
            </div>
        </main>
    @endcanany
@endsection
