@extends('layouts.head')

@section('title', __('messages.Hotels'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/hotels/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/hotels/forms.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev','posAuthor'])
        @php
            $statusTone = strtolower((string) $promo->status) === 'active' ? 'active' : 'draft';
            $formatDate = static fn ($date) => $date ? date('Y-m-d', strtotime($date)) : '';
            $translationGroups = [
                [
                    'title' => 'Benefits',
                    'description' => 'Optional guest-facing benefits displayed with this promo.',
                    'fields' => [
                        ['name' => 'benefits', 'label' => 'English', 'placeholder' => 'Insert promo benefits'],
                        ['name' => 'benefits_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert promo benefits in Traditional Chinese'],
                        ['name' => 'benefits_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert promo benefits in Simplified Chinese'],
                    ],
                ],
                [
                    'title' => 'Inclusion',
                    'description' => 'Optional inclusions attached to the promo offer.',
                    'fields' => [
                        ['name' => 'include', 'label' => 'English', 'placeholder' => 'Insert promo inclusions'],
                        ['name' => 'include_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert promo inclusions in Traditional Chinese'],
                        ['name' => 'include_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert promo inclusions in Simplified Chinese'],
                    ],
                ],
                [
                    'title' => 'Additional Information',
                    'description' => 'Optional notes, restrictions, or preparation details shown to guests.',
                    'fields' => [
                        ['name' => 'additional_info', 'label' => 'English', 'placeholder' => 'Insert additional information'],
                        ['name' => 'additional_info_traditional', 'label' => 'Traditional Chinese', 'placeholder' => 'Insert additional information in Traditional Chinese'],
                        ['name' => 'additional_info_simplified', 'label' => 'Simplified Chinese', 'placeholder' => 'Insert additional information in Simplified Chinese'],
                    ],
                ],
            ];
        @endphp

        <div class="mobile-menu-overlay"></div>
        <main class="main-container hotel-form-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="hotel-form-hero"
                    eyebrow="Promotion"
                    title="Edit Promo Price"
                    description="Update promo periods, room assignment, pricing inputs, status, and customer-facing copy for {{ $hotel->name }}."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.hotels.show', $hotel->id) }}#promo" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Detail
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <x-backend.breadcrumb-toolbar
                    class="hotel-form-toolbar"
                    :items="[
                        ['label' => 'Admin Panel', 'url' => route('admin.panel-main.view')],
                        ['label' => 'Hotel Manager', 'url' => route('admin.hotels.index')],
                        ['label' => $hotel->name, 'url' => route('admin.hotels.show', $hotel->id)],
                    ]"
                    current="Edit Promo Price"
                >
                    <x-slot name="actions">
                        <span class="backend-status-badge backend-status-badge--{{ $statusTone }}">{{ $promo->status }}</span>
                    </x-slot>
                </x-backend.breadcrumb-toolbar>

                @if ($errors->any() || session()->has('success') || session()->has('invalid') || session()->has('error'))
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
                                <strong>{{ session('success') }}</strong>
                            </div>
                        @endif

                        @if (session()->has('invalid') || session()->has('error'))
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ session('invalid') ?? session('error') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <form id="hotelPromoUpdate" class="backend-form" action="{{ route('admin.hotels.promos.update', $promo->id) }}" method="post">
                    @csrf
                    @method('put')
                    <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                    <input name="hotel_context" value="{{ $hotelContext }}" type="hidden">

                    <x-backend.detail-layout>
                        <x-slot name="main">
                            <section class="backend-panel backend-form-panel hotel-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Basic Information</span>
                                        <h2>Promo Profile</h2>
                                    </div>
                                    <p>Core Hotel, Room, promo identity, lifecycle state, and booking reference used by Hotel operations.</p>
                                </div>

                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid backend-form-grid--2">
                                        <div class="backend-form-field">
                                            <label for="hotel_context" class="backend-form-label">Hotel</label>
                                            <input id="hotel_context" type="text" class="backend-form-control" value="{{ $hotel->name }}" disabled>
                                            <p class="backend-form-help">Hotel relation is locked to this promo context and verified on the server.</p>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="rooms_id" class="backend-form-label is-required">Room</label>
                                            <select class="backend-form-control @error('rooms_id') is-invalid @enderror" id="rooms_id" name="rooms_id" required>
                                                @foreach ($rooms as $room)
                                                    <option value="{{ $room->id }}" @selected((int) old('rooms_id', $promo->rooms_id) === (int) $room->id)>{{ $room->rooms }}</option>
                                                @endforeach
                                            </select>
                                            @error('rooms_id')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="name" class="backend-form-label is-required">Promo Name</label>
                                            <input class="backend-form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $promo->name) }}" placeholder="Promo name" type="text" required>
                                            @error('name')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="status" class="backend-form-label is-required">Status</label>
                                            <select class="backend-form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                                @foreach (['Active', 'Draft'] as $status)
                                                    <option value="{{ $status }}" @selected(old('status', $promo->status) === $status)>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="promotion_type" class="backend-form-label">Promotion Type</label>
                                            <select class="backend-form-control @error('promotion_type') is-invalid @enderror" id="promotion_type" name="promotion_type">
                                                <option value="">Select promotion type</option>
                                                @foreach (['Hot Deal', 'Best Choice', 'Best Price', 'Special Offer'] as $promotionType)
                                                    <option value="{{ $promotionType }}" @selected(old('promotion_type', $promo->promotion_type) === $promotionType)>{{ $promotionType }}</option>
                                                @endforeach
                                            </select>
                                            @error('promotion_type')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="booking_code" class="backend-form-label">Booking Code</label>
                                            <input class="backend-form-control @error('booking_code') is-invalid @enderror" id="booking_code" name="booking_code" value="{{ old('booking_code', $promo->booking_code) }}" placeholder="Booking code" type="text">
                                            @error('booking_code')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field is-wide">
                                            <label for="quotes" class="backend-form-label">Quote</label>
                                            <input class="backend-form-control @error('quotes') is-invalid @enderror" id="quotes" name="quotes" value="{{ old('quotes', $promo->quotes) }}" placeholder="Short promo quote" type="text">
                                            @error('quotes')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel backend-form-panel hotel-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Promotion Period</span>
                                        <h2>Booking and Stay Window</h2>
                                    </div>
                                    <p>Booking period controls when guests can book; stay period controls travel dates that can use this promo.</p>
                                </div>

                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid backend-form-grid--2">
                                        <div class="backend-form-field">
                                            <label for="book_periode_start" class="backend-form-label is-required">Booking Period Start</label>
                                            <input id="book_periode_start" name="book_periode_start" class="backend-form-control @error('book_periode_start') is-invalid @enderror" value="{{ old('book_periode_start', $formatDate($promo->book_periode_start)) }}" placeholder="YYYY-MM-DD" type="text" required data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
                                            @error('book_periode_start')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="book_periode_end" class="backend-form-label is-required">Booking Period End</label>
                                            <input id="book_periode_end" name="book_periode_end" class="backend-form-control @error('book_periode_end') is-invalid @enderror" value="{{ old('book_periode_end', $formatDate($promo->book_periode_end)) }}" placeholder="YYYY-MM-DD" type="text" required data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
                                            @error('book_periode_end')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="periode_start" class="backend-form-label is-required">Stay Period Start</label>
                                            <input id="periode_start" name="periode_start" class="backend-form-control @error('periode_start') is-invalid @enderror" value="{{ old('periode_start', $formatDate($promo->periode_start)) }}" placeholder="YYYY-MM-DD" type="text" required data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
                                            @error('periode_start')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="periode_end" class="backend-form-label is-required">Stay Period End</label>
                                            <input id="periode_end" name="periode_end" class="backend-form-control @error('periode_end') is-invalid @enderror" value="{{ old('periode_end', $formatDate($promo->periode_end)) }}" placeholder="YYYY-MM-DD" type="text" required data-backend-picker="date" data-backend-picker-format="yyyy-mm-dd">
                                            @error('periode_end')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="minimum_stay" class="backend-form-label is-required">Minimum Stay</label>
                                            <input class="backend-form-control @error('minimum_stay') is-invalid @enderror" id="minimum_stay" name="minimum_stay" value="{{ old('minimum_stay', $promo->minimum_stay) }}" min="1" type="number" required>
                                            @error('minimum_stay')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel backend-form-panel hotel-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Pricing</span>
                                        <h2>Pricing Inputs</h2>
                                    </div>
                                    <p>Store only master inputs here. Published promo pricing remains calculated by the existing Hotel pricing architecture.</p>
                                </div>

                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid backend-form-grid--2">
                                        <div class="backend-form-field">
                                            <label for="contract_rate" class="backend-form-label is-required">Contract Rate</label>
                                            <input class="backend-form-control @error('contract_rate') is-invalid @enderror" id="contract_rate" name="contract_rate" value="{{ old('contract_rate', $promo->contract_rate) }}" placeholder="Insert contract rate" inputmode="numeric" type="text" required data-backend-money-unit="IDR">
                                            @error('contract_rate')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="markup" class="backend-form-label is-required">Markup</label>
                                            <input class="backend-form-control @error('markup') is-invalid @enderror" id="markup" name="markup" value="{{ old('markup', $promo->markup) }}" placeholder="Insert markup" inputmode="numeric" type="text" required data-backend-money-unit="USD">
                                            @error('markup')
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
                                        <h2>Benefits and Inclusions</h2>
                                    </div>
                                    <p>Customer-facing copy is optional and grouped by logical field with the canonical language order.</p>
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
                                                        <label for="{{ $field['name'] }}" class="backend-form-label">{{ $field['label'] }}</label>
                                                        <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="textarea_editor backend-form-control border-radius-0 @error($field['name']) is-invalid @enderror" data-backend-richtext="true" placeholder="{{ $field['placeholder'] }}">{{ old($field['name'], $promo->{$field['name']}) }}</textarea>
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
                            <section class="backend-page-toolbar backend-form-actions hotel-form-actions">
                                <div class="backend-page-toolbar__actions">
                                    <a href="{{ route('admin.hotels.show', $hotel->id) }}#promo" class="backend-button backend-button-secondary">
                                        <i class="fa fa-times"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="backend-button backend-button-primary">
                                        <i class="fa fa-floppy-o"></i>
                                        Save Changes
                                    </button>
                                </div>
                            </section>
                        </x-slot>

                        <x-slot name="side">
                            <section class="backend-panel backend-detail-side-card hotel-promo-edit-context-panel hotel-status-side-card">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Current Status</span>
                                        <h2><span class="backend-status-badge backend-status-badge--{{ $statusTone }}">{{ $promo->status }}</span></h2>
                                    </div>
                                    <p>Current lifecycle state before this edit is saved.</p>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card hotel-promo-edit-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Metadata</span>
                                        <h2>Promo Record</h2>
                                    </div>
                                    <p>Admin-only record context for audit and maintenance.</p>
                                </div>
                                <div class="backend-detail-side-card__body">
                                    <dl class="backend-detail-side-list">
                                        <div>
                                            <dt>Promo ID</dt>
                                            <dd>#{{ $promo->id }}</dd>
                                        </div>
                                        <div>
                                            <dt>Author</dt>
                                            <dd>{{ $promo->author ?: '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt>Created</dt>
                                            <dd>{{ optional($promo->created_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt>Updated</dt>
                                            <dd>{{ optional($promo->updated_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card hotel-promo-edit-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Hotel Context</span>
                                        <h2>{{ $hotel->name }}</h2>
                                    </div>
                                    <p>Read-only parent Hotel reference for this promo.</p>
                                </div>
                                <div class="backend-detail-side-card__body">
                                    <dl class="backend-detail-side-list">
                                        <div>
                                            <dt>Region</dt>
                                            <dd>{{ $hotel->region ?: '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt>Room Options</dt>
                                            <dd>{{ $rooms->count() }} available</dd>
                                        </div>
                                    </dl>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card hotel-promo-edit-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Promo Summary</span>
                                        <h2>{{ $promo->name }}</h2>
                                    </div>
                                    <p>Read-only snapshot of the promo before saving changes.</p>
                                </div>
                                <div class="backend-detail-side-card__body">
                                    <dl class="backend-detail-side-list">
                                        <div>
                                            <dt>Room</dt>
                                            <dd>{{ optional($promo->rooms)->rooms ?: '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt>Promotion Type</dt>
                                            <dd>{{ $promo->promotion_type ?: '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt>Booking Code</dt>
                                            <dd>{{ $promo->booking_code ?: '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt>Minimum Stay</dt>
                                            <dd>{{ $promo->minimum_stay ?: '-' }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card hotel-promo-edit-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Pricing Context</span>
                                        <h2>Master Inputs</h2>
                                    </div>
                                    <p>Contract rate and markup are stored as inputs only; this page does not calculate a separate selling price preview.</p>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card hotel-promo-edit-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Related Actions</span>
                                        <h2>Hotel Workspace</h2>
                                    </div>
                                    <p>Return to Hotel detail to review grouped Promotion Price rows.</p>
                                </div>
                                <div class="backend-detail-side-actions">
                                    <a href="{{ route('admin.hotels.show', $hotel->id) }}#promo" class="backend-button backend-button-secondary">
                                        <i class="fa fa-building"></i>
                                        View Hotel
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
