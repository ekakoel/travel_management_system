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
        <main class="main-container activity-form-page activity-form-page--create">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    eyebrow="Operations Inventory"
                    title="Add Activity"
                    description="Create a new activity product with partner ownership, operational capacity, pricing, validity, and publishing details."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.activities.index') }}" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Activities
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <x-backend.breadcrumb-toolbar
                    class="activity-form-toolbar"
                    :items="[
                        ['label' => 'Admin Panel', 'url' => route('admin.panel-main.view')],
                        ['label' => 'Activities', 'url' => route('admin.activities.index')],
                    ]"
                    current="Add Activity"
                >
                    <x-slot name="actions">
                        <span class="backend-status-badge backend-status-badge--draft">Draft by default</span>
                    </x-slot>
                </x-backend.breadcrumb-toolbar>

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

                <form id="activityCreateForm" class="backend-form" action="{{ route('admin.activities.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <x-backend.detail-layout class="activity-create-layout">
                        <x-slot name="main">
                            <section class="backend-panel activity-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Cover / Media</span>
                                        <h2>Cover Image</h2>
                                    </div>
                                    <p>Upload the primary image used to represent this Activity in backend and public views.</p>
                                </div>

                                <div class="backend-form-grid">
                                    <div class="backend-form-field is-wide">
                                        <label for="cover" class="backend-form-label">Cover Image</label>
                                        <div class="activity-form-cover-control">
                                            <figure class="activity-form-cover-preview" data-activity-cover-preview></figure>
                                            <div class="activity-form-cover-input">
                                                <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" data-activity-file-input data-activity-file-input-target="#activityCoverFileStatus" data-activity-cover-input data-activity-cover-preview-target="[data-activity-cover-preview]" required>
                                                <span id="activityCoverFileStatus" class="activity-file-status" data-activity-file-input-default="No cover selected">No cover selected</span>
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
                                        <input type="text" id="name" name="name" class="backend-form-control @error('name') is-invalid @enderror" placeholder="Insert activity name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="partners_id" class="backend-form-label">Partner</label>
                                        <select id="partners_id" name="partners_id" class="backend-form-control @error('partners_id') is-invalid @enderror" required>
                                            <option value="">Select Partner</option>
                                            @foreach ($partners as $partner)
                                                <option value="{{ $partner->id }}" @selected((string) old('partners_id') === (string) $partner->id)>{{ $partner->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('partners_id')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="type" class="backend-form-label">Category / Type</label>
                                        <select id="type" name="type" class="backend-form-control @error('type') is-invalid @enderror" required>
                                            <option value="">Select Type</option>
                                            @foreach ($type as $activityType)
                                                <option value="{{ $activityType->type }}" @selected(old('type') === $activityType->type)>{{ $activityType->type }}</option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="location" class="backend-form-label">Location</label>
                                        <input type="text" id="location" name="location" class="backend-form-control @error('location') is-invalid @enderror" placeholder="Activity location" value="{{ old('location') }}" required>
                                        @error('location')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field is-wide">
                                        <label for="map" class="backend-form-label">Map</label>
                                        <input type="text" id="map" name="map" class="backend-form-control @error('map') is-invalid @enderror" placeholder="Google Maps link" value="{{ old('map') }}">
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
                                            <option value="">Select Duration</option>
                                            @foreach (['15 Minutes', '30 Minutes', '1 Hour', '2 Hours', '3 Hours', '4 Hours', '5 Hours', '6 Hours', '7 Hours', '8 Hours', '9 Hours', '10 Hours'] as $duration)
                                                <option value="{{ $duration }}" @selected(old('duration') === $duration)>{{ $duration === '10 Hours' ? 'Full Day (10 hours)' : $duration }}</option>
                                            @endforeach
                                        </select>
                                        @error('duration')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="min_pax" class="backend-form-label">Minimum Pax</label>
                                        <input type="number" id="min_pax" name="min_pax" value="{{ old('min_pax') }}" class="backend-form-control @error('min_pax') is-invalid @enderror" placeholder="Minimum pax" min="1" required>
                                        @error('min_pax')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="qty" class="backend-form-label">Capacity</label>
                                        <input type="number" id="qty" name="qty" value="{{ old('qty') }}" class="backend-form-control @error('qty') is-invalid @enderror" placeholder="Maximum pax" min="1" required>
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
                                        <input type="text" inputmode="numeric" id="contract_rate" name="contract_rate" class="backend-form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate') }}" required data-backend-money-unit="IDR">
                                        @error('contract_rate')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="markup" class="backend-form-label">Markup</label>
                                        <input type="text" inputmode="numeric" id="markup" name="markup" class="backend-form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup') }}" data-backend-money-unit="USD">
                                        @error('markup')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="backend-form-field">
                                        <label for="validity" class="backend-form-label">Valid Until</label>
                                        <input type="text" id="validity" name="validity" value="{{ old('validity') }}" class="backend-form-control @error('validity') is-invalid @enderror" placeholder="YYYY-MM-DD" required data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
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
                                                    <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="textarea_editor backend-form-control border-radius-0 @error($field['name']) is-invalid @enderror" data-backend-richtext="true" placeholder="{{ $field['placeholder'] }}">{{ old($field['name']) }}</textarea>
                                                    @error($field['name'])
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            @endforeach
                                        </div>
                                    </section>
                                @endforeach

                                <input id="page" name="page" value="add-activity" type="hidden">
                            </section>
                        </x-slot>

                        <x-slot name="side">
                            <section class="backend-panel backend-detail-side-card activity-create-context-panel activity-status-side-card">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Initial Status</span>
                                        <h2><span class="backend-status-badge backend-status-badge--draft">Draft</span></h2>
                                    </div>
                                    <p>The server creates new Activities as Draft. Status is not accepted from this form.</p>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card activity-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Creation Guidance</span>
                                        <h2>Before Save</h2>
                                    </div>
                                    <p>Administrative reminders for creating this Activity record.</p>
                                </div>
                                <ul class="backend-detail-side-list">
                                    <li>
                                        <span>Availability</span>
                                        <strong>Validity controls booking dates</strong>
                                        <small>Customers cannot book this Activity for a travel date after the valid-until date.</small>
                                    </li>
                                    <li>
                                        <span>Capacity</span>
                                        <strong>Minimum pax must fit capacity</strong>
                                        <small>Server validation requires capacity to be greater than or equal to minimum pax.</small>
                                    </li>
                                    <li>
                                        <span>Pricing</span>
                                        <strong>Canonical service calculation</strong>
                                        <small>Contract rate and markup are stored as inputs; public quote and published price remain server-authoritative.</small>
                                    </li>
                                </ul>
                            </section>

                            <section class="backend-panel backend-detail-side-card activity-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Publishing Guidance</span>
                                        <h2>Before Activation</h2>
                                    </div>
                                </div>
                                <ul class="backend-detail-side-list">
                                    <li>
                                        <span>Required</span>
                                        <strong>Partner, cover, capacity, pricing, and validity</strong>
                                        <small>Content translations can be completed later, but operational fields are required to create the record.</small>
                                    </li>
                                    <li>
                                        <span>Next Step</span>
                                        <strong>Review detail before publishing</strong>
                                        <small>After creation, confirm gallery images and customer-facing copy before changing lifecycle status.</small>
                                    </li>
                                </ul>
                            </section>
                        </x-slot>
                    </x-backend.detail-layout>

                    <section class="backend-page-toolbar backend-form-actions activity-form-actions">
                        <div class="backend-page-toolbar__actions">
                            <a href="{{ route('admin.activities.index') }}" class="backend-button backend-button-secondary">Cancel</a>
                            <button type="submit" class="backend-button backend-button-primary">
                                <i class="fa fa-check"></i>
                                Add Activity
                            </button>
                        </div>
                    </section>
                </form>

                @include('layouts.footer')
            </div>
        </main>
    @endcanany
@endsection
