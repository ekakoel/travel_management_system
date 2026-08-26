@extends('layouts.head')

@section('title', __('messages.Hotel Room'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/hotels/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/hotels/forms.js') }}" defer></script>
@endpush

@section('content')
    <div class="mobile-menu-overlay"></div>
    @canany(['posDev','posAuthor','posAdm'])
        @php
            $translationGroups = [
                [
                    'label' => 'Guest-Facing Copy',
                    'title' => 'Room Includes',
                    'description' => 'Optional copy shown to guests about inclusions for this Room.',
                    'fields' => [
                        ['name' => 'include', 'label' => 'English', 'placeholder' => 'Insert room inclusions'],
                        ['name' => 'include_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert room inclusions in Traditional Chinese'],
                        ['name' => 'include_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert room inclusions in Simplified Chinese'],
                    ],
                ],
                [
                    'label' => 'Guest-Facing Copy',
                    'title' => 'Amenities',
                    'description' => 'Optional amenity copy used by the frontend Room profile.',
                    'fields' => [
                        ['name' => 'amenities', 'label' => 'English', 'placeholder' => 'Insert room amenities'],
                        ['name' => 'amenities_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert room amenities in Traditional Chinese'],
                        ['name' => 'amenities_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert room amenities in Simplified Chinese'],
                    ],
                ],
                [
                    'label' => 'Guest-Facing Copy',
                    'title' => 'Additional Information',
                    'description' => 'Optional notes displayed with the Room profile.',
                    'fields' => [
                        ['name' => 'additional_info', 'label' => 'English', 'placeholder' => 'Insert additional information'],
                        ['name' => 'additional_info_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert additional information in Traditional Chinese'],
                        ['name' => 'additional_info_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert additional information in Simplified Chinese'],
                    ],
                ],
            ];
        @endphp

        <div class="main-container hotel-form-page">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <x-backend.page-hero
                        class="hotel-form-hero"
                        eyebrow="Room Inventory"
                        title="Add New Room"
                        description="Create a Room profile for {{ $hotels->name }} using the shared backend layout standard."
                    >
                        <x-slot name="action">
                            <a href="{{ route('admin.hotels.show', $hotels->id) }}#rooms" class="backend-page-primary-action">
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
                                <li class="breadcrumb-item active" aria-current="page">Add Room</li>
                            </ol>
                        </nav>
                        <div class="backend-page-toolbar__actions">
                            <span class="backend-status-badge backend-status-badge--draft">Room setup</span>
                        </div>
                    </section>

                    @if ($errors->any())
                        <div class="backend-feedback hotel-form-feedback">
                            <div class="backend-alert backend-alert--danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('success'))
                        <div class="backend-feedback hotel-form-feedback">
                            <div class="backend-alert backend-alert--success">
                                <ul>
                                    <li>{!! session('success') !!}</li>
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form id="hotelRoomCreateForm" action="{{ route('admin.hotels.room.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="hotels_id" name="hotels_id" value="{{ $hotels->id }}">
                        <input type="hidden" id="hotel_context" name="hotel_context" value="{{ $hotelContext }}">

                        <x-backend.detail-layout>
                            <div class="backend-panel backend-form-panel hotel-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Cover / Media</span>
                                        <h2>Cover Image</h2>
                                    </div>
                                    <p>Upload the primary Room image used inside the Hotel profile.</p>
                                </div>
                                <div class="backend-form-panel__body">
                                    <div class="hotel-form-cover-control">
                                        <div class="hotel-form-cover-preview" data-hotel-cover-preview></div>
                                        <div class="backend-form-field">
                                            <label for="cover" class="backend-form-label is-required">Cover Image</label>
                                            <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" accept="image/*" data-hotel-cover-input required>
                                            <span class="hotel-file-status" data-hotel-cover-status>No cover selected</span>
                                            @error('cover')
                                                <span class="backend-form-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="backend-panel backend-form-panel hotel-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Basic Information</span>
                                        <h2>Room Profile</h2>
                                    </div>
                                    <p>Core Room identity, view, bedding, and size used by Hotel inventory.</p>
                                </div>
                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid backend-form-grid--2">
                                        <div class="backend-form-field">
                                            <label for="rooms" class="backend-form-label is-required">Room Name</label>
                                            <input type="text" id="rooms" name="rooms" class="backend-form-control @error('rooms') is-invalid @enderror" placeholder="Ex: Superior Room" value="{{ old('rooms') }}" required>
                                            @error('rooms')
                                                <span class="backend-form-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="backend-form-field">
                                            <label for="room_view" class="backend-form-label is-required">Room View</label>
                                            <input type="text" id="room_view" name="room_view" class="backend-form-control @error('room_view') is-invalid @enderror" value="{{ old('room_view') }}" placeholder="Start typing..." data-hotel-autocomplete="room-view" data-hotel-autocomplete-url="{{ route('admin.autocomplate.hotels.room_view') }}" data-hotel-autocomplete-results="views" data-hotel-autocomplete-target="#room-view-suggestions" required>
                                            <div id="room-view-suggestions" class="hotel-form-suggestions" hidden></div>
                                            @error('room_view')
                                                <span class="backend-form-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="backend-form-field">
                                            <label for="bed_type" class="backend-form-label is-required">Bed Type</label>
                                            <input type="text" id="bed_type" name="beds" class="backend-form-control @error('beds') is-invalid @enderror" value="{{ old('beds') }}" placeholder="Start typing..." data-hotel-autocomplete="bed-type" data-hotel-autocomplete-url="{{ route('admin.autocomplate.hotels.room.bed_type') }}" data-hotel-autocomplete-results="beds" data-hotel-autocomplete-target="#bed-type-suggestions" required>
                                            <div id="bed-type-suggestions" class="hotel-form-suggestions" hidden></div>
                                            @error('beds')
                                                <span class="backend-form-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="backend-form-field">
                                            <label for="size" class="backend-form-label">Room Size</label>
                                            <input type="text" id="size" name="size" class="backend-form-control @error('size') is-invalid @enderror" value="{{ old('size') }}" placeholder="Ex: 32 sqm">
                                            @error('size')
                                                <span class="backend-form-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="backend-panel backend-form-panel hotel-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Occupancy and Inventory</span>
                                        <h2>Capacity</h2>
                                    </div>
                                    <p>Operational limits used when this Room is selected for bookings.</p>
                                </div>
                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid backend-form-grid--3">
                                        <div class="backend-form-field">
                                            <label for="capacity_adult" class="backend-form-label is-required">Adult Capacity</label>
                                            <input type="number" id="capacity_adult" min="1" name="capacity_adult" class="backend-form-control @error('capacity_adult') is-invalid @enderror" placeholder="Ex: 2" value="{{ old('capacity_adult') }}" required>
                                            @error('capacity_adult')
                                                <span class="backend-form-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="backend-form-field">
                                            <label for="capacity_child" class="backend-form-label">Child Capacity</label>
                                            <input type="number" id="capacity_child" min="0" name="capacity_child" class="backend-form-control @error('capacity_child') is-invalid @enderror" placeholder="Ex: 1" value="{{ old('capacity_child') }}">
                                            @error('capacity_child')
                                                <span class="backend-form-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="backend-form-field">
                                            <label for="inventory" class="backend-form-label is-required">Room Inventory</label>
                                            <input type="number" id="inventory" min="1" name="inventory" class="backend-form-control @error('inventory') is-invalid @enderror" placeholder="Available rooms" value="{{ old('inventory') }}" required>
                                            @error('inventory')
                                                <span class="backend-form-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="backend-panel backend-form-panel hotel-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Content and Translations</span>
                                        <h2>Customer-Facing Copy</h2>
                                    </div>
                                    <p>Optional localized Room content for public Hotel pages and order context.</p>
                                </div>
                                <div class="backend-form-panel__body">
                                    @foreach ($translationGroups as $group)
                                        <section class="backend-translation-group">
                                            <div class="backend-translation-group__header">
                                                <div>
                                                    <span class="backend-section-header__label">{{ $group['label'] }}</span>
                                                    <h3>{{ $group['title'] }}</h3>
                                                </div>
                                                <p>{{ $group['description'] }}</p>
                                            </div>
                                            <div class="backend-translation-grid">
                                                @foreach ($group['fields'] as $field)
                                                    <div class="backend-form-field">
                                                        <label for="{{ $field['name'] }}" class="backend-form-label">{{ $field['label'] }}</label>
                                                        <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="textarea_editor backend-form-control @error($field['name']) is-invalid @enderror" data-backend-richtext="true" placeholder="{{ $field['placeholder'] }}">{{ old($field['name']) }}</textarea>
                                                        @error($field['name'])
                                                            <span class="backend-form-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                @endforeach
                                            </div>
                                        </section>
                                    @endforeach
                                </div>
                            </div>

                            <x-slot name="side">
                                <section class="backend-panel backend-detail-side-card hotel-room-create-context-panel">
                                    <div class="backend-section-header">
                                        <div>
                                            <span class="backend-section-header__label">Hotel Context</span>
                                            <h2>{{ $hotels->name }}</h2>
                                        </div>
                                        <p>Read-only parent Hotel reference for this Room setup.</p>
                                    </div>
                                    <div class="backend-detail-side-card__body">
                                        <dl class="backend-detail-side-list">
                                            <div>
                                                <dt>Region</dt>
                                                <dd>{{ $hotels->region ?: '-' }}</dd>
                                            </div>
                                            <div>
                                                <dt>Address</dt>
                                                <dd>{{ $hotels->address ?: '-' }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                    <div class="backend-detail-side-actions">
                                        <a href="{{ route('admin.hotels.show', $hotels->id) }}#rooms" class="backend-button backend-button-secondary"><i class="fa fa-building"></i> View Hotel</a>
                                    </div>
                                </section>

                                <section class="backend-panel backend-detail-side-card hotel-room-create-context-panel hotel-status-side-card">
                                    <div class="backend-section-header">
                                        <div>
                                            <span class="backend-section-header__label">Initial State</span>
                                            <h2>Room Status</h2>
                                        </div>
                                        <p>The server creates new Rooms with the default active inventory state.</p>
                                    </div>
                                    <div class="backend-detail-side-card__body">
                                        <dl class="backend-detail-side-list">
                                            <div>
                                                <dt>Status</dt>
                                                <dd><span class="backend-status-badge backend-status-badge--active">{{ $initialStatus }}</span></dd>
                                            </div>
                                        </dl>
                                    </div>
                                </section>

                                <section class="backend-panel backend-detail-side-card hotel-room-create-context-panel">
                                    <div class="backend-section-header">
                                        <div>
                                            <span class="backend-section-header__label">Inventory Guidance</span>
                                            <h2>Availability Setup</h2>
                                        </div>
                                        <p>Room inventory is required before prices, packages, or promos are attached to this Room.</p>
                                    </div>
                                </section>

                                <section class="backend-panel backend-detail-side-card hotel-room-create-context-panel">
                                    <div class="backend-section-header">
                                        <div>
                                            <span class="backend-section-header__label">Occupancy Guidance</span>
                                            <h2>Capacity Rules</h2>
                                        </div>
                                        <p>Use admin-only rules here, not duplicate editable service fields.</p>
                                    </div>
                                    <div class="backend-detail-side-card__body">
                                        <dl class="backend-detail-side-list">
                                            <div>
                                                <dt>Adult Capacity</dt>
                                                <dd>Minimum 1 adult</dd>
                                            </div>
                                            <div>
                                                <dt>Child Capacity</dt>
                                                <dd>Optional, minimum 0</dd>
                                            </div>
                                        </dl>
                                    </div>
                                </section>

                                <section class="backend-panel backend-detail-side-card hotel-room-create-context-panel">
                                    <div class="backend-section-header">
                                        <div>
                                            <span class="backend-section-header__label">Next Step</span>
                                            <h2>Room Pricing</h2>
                                        </div>
                                        <p>After the Room is saved, continue from the Hotel detail page to add Room prices and promo rules.</p>
                                    </div>
                                </section>
                            </x-slot>
                        </x-backend.detail-layout>
                    </form>

                    <div class="backend-page-toolbar backend-form-actions ">
                        <a href="{{ route('admin.hotels.show', $hotels->id) }}#rooms" class="backend-button backend-button-secondary">
                            <i class="fa fa-times"></i>
                            Cancel
                        </a>
                        <button type="submit" form="hotelRoomCreateForm" class="backend-button backend-button-primary">
                            <i class="fa fa-check" aria-hidden="true"></i>
                            Add Room
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endcanany
@endsection
