@extends('layouts.head')

@section('title', __('messages.Hotels'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/hotels/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/hotels/forms.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev','posAuthor','posAdm'])
        @php
            $status = $hotels->status ?: 'Draft';
            $statusClass = match (strtolower($status)) {
                'active' => 'active',
                'archived' => 'inactive',
                default => 'draft',
            };
            $coverUrl = $hotels->cover ? asset('storage/hotels/hotels-cover/' . $hotels->cover) : null;
            $translationGroups = [
                [
                    'title' => 'Description',
                    'description' => 'Provide the property description shown to customers.',
                    'fields' => [
                        ['name' => 'description', 'label' => 'English', 'placeholder' => 'Insert description', 'required' => true],
                        ['name' => 'description_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional description', 'required' => false],
                        ['name' => 'description_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified description', 'required' => false],
                    ],
                ],
                [
                    'title' => 'Facility',
                    'description' => 'List facilities and amenities available at this Hotel.',
                    'fields' => [
                        ['name' => 'facility', 'label' => 'English', 'placeholder' => 'Insert facility', 'required' => false],
                        ['name' => 'facility_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional facility', 'required' => false],
                        ['name' => 'facility_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified facility', 'required' => false],
                    ],
                ],
                [
                    'title' => 'Additional Information',
                    'description' => 'Add customer-facing property notes, restrictions, or arrival guidance.',
                    'fields' => [
                        ['name' => 'additional_info', 'label' => 'English', 'placeholder' => 'Insert additional information', 'required' => false],
                        ['name' => 'additional_info_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional additional information', 'required' => false],
                        ['name' => 'additional_info_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified additional information', 'required' => false],
                    ],
                ],
                [
                    'title' => 'Cancellation Policy',
                    'description' => 'Define booking cancellation rules shown to customers.',
                    'fields' => [
                        ['name' => 'cancellation_policy', 'label' => 'English', 'placeholder' => 'Insert cancellation policy', 'required' => false],
                        ['name' => 'cancellation_policy_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert traditional cancellation policy', 'required' => false],
                        ['name' => 'cancellation_policy_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert simplified cancellation policy', 'required' => false],
                    ],
                ],
            ];
        @endphp

        <div class="mobile-menu-overlay"></div>
        <main class="main-container hotel-form-page hotel-form-page--edit">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="hotel-form-hero"
                    eyebrow="Hotel Profile"
                    title="Edit Hotel"
                    description="Maintain the Hotel master profile without changing rooms, prices, promotions, packages, or reservations."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.hotels.show', $hotels->id) }}" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Detail
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar hotel-form-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.hotels.index') }}">Hotel Manager</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.hotels.show', $hotels->id) }}">{{ $hotels->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Hotel</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--{{ $statusClass }}">{{ $status }}</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback hotel-form-feedback">
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
                                <strong>{!! session('success') !!}</strong>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ session('error') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <form id="hotelEditForm" class="backend-form" action="{{ route('func.hotel.edit', $hotels->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    <x-backend.detail-layout class="hotel-edit-layout">
                        <x-slot name="main">
                            <section class="backend-panel backend-form-panel hotel-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Cover / Media</span>
                                        <h2>Cover Image</h2>
                                    </div>
                                    <p>Keep the current cover or choose a new image. Replacement happens only when the form is saved.</p>
                                </div>

                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid">
                                        <div class="backend-form-field is-wide">
                                            <label for="cover" class="backend-form-label">Cover Image</label>
                                            <div class="hotel-form-cover-control">
                                                <figure class="hotel-form-cover-preview" data-hotel-cover-preview>
                                                    @if ($coverUrl)
                                                        <img src="{{ $coverUrl }}" alt="{{ $hotels->name }} cover">
                                                    @endif
                                                </figure>
                                                <div class="hotel-form-cover-input">
                                                    <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" data-hotel-cover-input data-hotel-cover-preview-target="[data-hotel-cover-preview]">
                                                    <span class="hotel-file-status" data-hotel-cover-status data-hotel-cover-status-default="Current cover preserved">Current cover preserved</span>
                                                    @error('cover')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel backend-form-panel hotel-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Basic Information</span>
                                        <h2>Hotel Profile</h2>
                                    </div>
                                    <p>Core property and supplier contact details used by the backend Hotel inventory.</p>
                                </div>

                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid">
                                        <div class="backend-form-field">
                                            <label for="name" class="backend-form-label">Hotel Name <span>*</span></label>
                                            <input type="text" id="name" name="name" class="backend-form-control @error('name') is-invalid @enderror" placeholder="Insert hotel name" value="{{ old('name', $hotels->name) }}" required>
                                            @error('name')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="region" class="backend-form-label">Region <span>*</span></label>
                                            <input type="text" id="region" name="region" class="backend-form-control @error('region') is-invalid @enderror" placeholder="Insert region" value="{{ old('region', $hotels->region) }}" required>
                                            @error('region')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="contact_person" class="backend-form-label">Contact Person <span>*</span></label>
                                            <input type="text" id="contact_person" name="contact_person" class="backend-form-control @error('contact_person') is-invalid @enderror" placeholder="Insert contact person" value="{{ old('contact_person', $hotels->contact_person) }}" required>
                                            @error('contact_person')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="phone" class="backend-form-label">Phone Number <span>*</span></label>
                                            <input type="text" inputmode="tel" id="phone" name="phone" class="backend-form-control @error('phone') is-invalid @enderror" placeholder="Insert contact person phone" value="{{ old('phone', $hotels->phone) }}" required>
                                            @error('phone')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="web" class="backend-form-label">Website <span>*</span></label>
                                            <input type="text" id="web" name="web" class="backend-form-control @error('web') is-invalid @enderror" placeholder="Ex: www.example.com" value="{{ old('web', $hotels->web) }}" required>
                                            @error('web')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field is-wide">
                                            <label for="address" class="backend-form-label">Address <span>*</span></label>
                                            <input type="text" id="address" name="address" class="backend-form-control @error('address') is-invalid @enderror" placeholder="Insert address" value="{{ old('address', $hotels->address) }}" required>
                                            @error('address')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field is-wide">
                                            <label for="map" class="backend-form-label">Map Location <span>*</span></label>
                                            <input type="text" id="map" name="map" class="backend-form-control @error('map') is-invalid @enderror" placeholder="Google Map link" value="{{ old('map', $hotels->map) }}" required>
                                            @error('map')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel backend-form-panel hotel-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Stay and Access</span>
                                        <h2>Stay Rules</h2>
                                    </div>
                                    <p>Set basic stay limits and arrival context used by Hotel operations.</p>
                                </div>

                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid">
                                        <div class="backend-form-field">
                                            <label for="min_stay" class="backend-form-label">Minimum Stay</label>
                                            <input type="number" min="0" id="min_stay" name="min_stay" class="backend-form-control @error('min_stay') is-invalid @enderror" placeholder="Minimum stay" value="{{ old('min_stay', $hotels->min_stay) }}">
                                            @error('min_stay')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="max_stay" class="backend-form-label">Maximum Stay</label>
                                            <input type="number" min="0" id="max_stay" name="max_stay" class="backend-form-control @error('max_stay') is-invalid @enderror" placeholder="Maximum stay" value="{{ old('max_stay', $hotels->max_stay) }}">
                                            @error('max_stay')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="airport_distance" class="backend-form-label">Airport Distance (Km) <span>*</span></label>
                                            <input type="number" min="1" id="airport_distance" name="airport_distance" class="backend-form-control @error('airport_distance') is-invalid @enderror" value="{{ old('airport_distance', $hotels->airport_distance) }}" required>
                                            @error('airport_distance')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="airport_duration" class="backend-form-label">Airport Duration (Hours) <span>*</span></label>
                                            <input type="number" min="1" id="airport_duration" name="airport_duration" class="backend-form-control @error('airport_duration') is-invalid @enderror" value="{{ old('airport_duration', $hotels->airport_duration) }}" required>
                                            @error('airport_duration')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel backend-form-panel hotel-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Content</span>
                                        <h2>Content and Translations</h2>
                                    </div>
                                    <p>Customer-facing Hotel copy is grouped by topic and displayed in the canonical language order.</p>
                                </div>

                                <div class="backend-form-panel__body">
                                    @foreach ($translationGroups as $group)
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
                                                            @if ($field['required'])
                                                                <span>*</span>
                                                            @endif
                                                        </label>
                                                        <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="textarea_editor backend-form-control border-radius-0 @error($field['name']) is-invalid @enderror" data-backend-richtext="true" placeholder="{{ $field['placeholder'] }}" @if ($field['required']) required @endif>{!! old($field['name'], $hotels->{$field['name']}) !!}</textarea>
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
                        </x-slot>

                        <x-slot name="side">
                            <section class="backend-panel backend-detail-side-card hotel-create-context-panel hotel-status-side-card">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Current Status</span>
                                        <h2><span class="backend-status-badge backend-status-badge--{{ $statusClass }}">{{ $status }}</span></h2>
                                    </div>
                                    <p>Control whether this Hotel is visible for operational use.</p>
                                </div>
                                <div class="backend-detail-side-actions">
                                    <div class="backend-form-field">
                                        <label for="status" class="backend-form-label">Status <span>*</span></label>
                                        <select id="status" name="status" class="backend-form-control @error('status') is-invalid @enderror" required>
                                            @foreach (['Active', 'Draft', 'Archived'] as $option)
                                                <option value="{{ $option }}" @selected(old('status', $status) === $option)>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card hotel-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Metadata</span>
                                        <h2>Record Context</h2>
                                    </div>
                                </div>
                                <ul class="backend-detail-side-list">
                                    <li>
                                        <span>Author</span>
                                        <strong>{{ $author?->name ?? 'Unknown' }}</strong>
                                        <small>{{ $author?->email ?? 'No author account found for this record.' }}</small>
                                    </li>
                                    <li>
                                        <span>Created At</span>
                                        <strong>{{ optional($hotels->created_at)->format('d M Y H:i') ?? '-' }}</strong>
                                        <small>Initial Hotel master record creation time.</small>
                                    </li>
                                    <li>
                                        <span>Updated At</span>
                                        <strong>{{ optional($hotels->updated_at)->format('d M Y H:i') ?? '-' }}</strong>
                                        <small>Last saved Hotel master profile update.</small>
                                    </li>
                                </ul>
                            </section>

                            <section class="backend-panel backend-detail-side-card hotel-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Stay Summary</span>
                                        <h2>Current Rules</h2>
                                    </div>
                                </div>
                                <ul class="backend-detail-side-list">
                                    <li>
                                        <span>Minimum Stay</span>
                                        <strong>{{ $hotels->min_stay ?? '-' }}</strong>
                                        <small>Lowest accepted stay length from the master Hotel profile.</small>
                                    </li>
                                    <li>
                                        <span>Maximum Stay</span>
                                        <strong>{{ $hotels->max_stay ?? '-' }}</strong>
                                        <small>Upper stay guidance currently stored for this Hotel.</small>
                                    </li>
                                    <li>
                                        <span>Check-in / Check-out</span>
                                        <strong>{{ $hotels->check_in_time ?: '-' }} / {{ $hotels->check_out_time ?: '-' }}</strong>
                                        <small>Displayed when historical data exists; not edited on this form.</small>
                                    </li>
                                </ul>
                            </section>

                            <section class="backend-panel backend-detail-side-card hotel-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Related Management</span>
                                        <h2>Separate Flows</h2>
                                    </div>
                                    <p>Rooms, prices, promos, packages, and gallery remain managed outside this master edit form.</p>
                                </div>
                                <div class="backend-detail-side-actions">
                                    <a href="{{ route('admin.hotels.show', $hotels->id) }}" class="backend-button backend-button-secondary">View Hotel Detail</a>
                                    <a href="{{ route('admin.hotels.gallery.edit', $hotels->id) }}" class="backend-button backend-button-secondary">Manage Gallery</a>
                                    <a href="{{ route('admin.hotels.prices.create', $hotels->id) }}" class="backend-button backend-button-secondary">Add Normal Price</a>
                                    <a href="{{ route('admin.hotels.promos.create', $hotels->id) }}" class="backend-button backend-button-secondary">Add Promotion</a>
                                    <a href="{{ route('admin.hotels.packages.create', $hotels->id) }}" class="backend-button backend-button-secondary">Add Package</a>
                                </div>
                            </section>
                        </x-slot>
                    </x-backend.detail-layout>

                    <section class="backend-page-toolbar backend-form-actions hotel-form-actions">
                        <div class="backend-page-toolbar__actions">
                            <a href="{{ route('admin.hotels.show', $hotels->id) }}" class="backend-button backend-button-secondary">Cancel</a>
                            <button type="submit" class="backend-button backend-button-primary">
                                <i class="fa fa-check" aria-hidden="true"></i>
                                Save Changes
                            </button>
                        </div>
                    </section>
                </form>

                @include('layouts.footer')
            </div>
        </main>
    @endcanany
@endsection
