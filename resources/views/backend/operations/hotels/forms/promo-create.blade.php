@extends('layouts.head')

@section('title', __('messages.Hotels'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/hotels/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/hotels/forms.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container hotel-form-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="hotel-form-hero"
                    eyebrow="Promotion"
                    title="Add Promo Price"
                    description="Create a draft promo price for {{ $hotel->name }} using the standardized backend promotion form."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.hotels.show', $hotel->id) }}#promo" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Detail
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar hotel-form-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hotels-admin.index') }}">Hotel Manager</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.hotels.show', $hotel->id) }}">{{ $hotel->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Promo Price</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--draft">Draft promo</span>
                    </div>
                </section>

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

                <div class="hotel-form-layout">
                    <div class="hotel-form-main">
                        <section class="backend-panel hotel-form-panel">
                            <div class="backend-section-header hotel-form-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Promo Price</span>
                                    <h2>Promotion Details</h2>
                                </div>
                            </div>

                            <form id="hotelPromoCreate" action="{{ route('admin.hotels.promos.store') }}" method="post">
                                @csrf
                                <div class="hotel-form-panel__body">
                                    <div class="backend-form-grid backend-form-grid--compact">
                                        <div class="backend-form-field">
                                            <label for="promotion_type">Promotion Type</label>
                                            <select class="backend-form-control" id="promotion_type" name="promotion_type">
                                                <option value="">Select promotion type</option>
                                                @foreach (['Hot Deal', 'Best Choice', 'Best Price', 'Special Offer'] as $promotionType)
                                                    <option value="{{ $promotionType }}" @selected(old('promotion_type') === $promotionType)>{{ $promotionType }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="name">Promo Name <b>*</b></label>
                                            <input class="backend-form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Promo name" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="rooms_id">Room <b>*</b></label>
                                            <select class="backend-form-control" id="rooms_id" name="rooms_id" required>
                                                <option value="">Select room</option>
                                                @foreach ($hotel->rooms as $room)
                                                    <option value="{{ $room->id }}" @selected((string) old('rooms_id') === (string) $room->id)>{{ $room->rooms }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="booking_code">Booking Code</label>
                                            <input class="backend-form-control" id="booking_code" name="booking_code" value="{{ old('booking_code') }}" placeholder="Booking code" type="text">
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="book_periode_start">Booking Period Start <b>*</b></label>
                                            <input id="book_periode_start" name="book_periode_start" class="backend-form-control date-picker" value="{{ old('book_periode_start') }}" placeholder="Select date" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="book_periode_end">Booking Period End <b>*</b></label>
                                            <input id="book_periode_end" name="book_periode_end" class="backend-form-control date-picker" value="{{ old('book_periode_end') }}" placeholder="Select date" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="periode_start">Stay Period Start <b>*</b></label>
                                            <input id="periode_start" name="periode_start" class="backend-form-control date-picker" value="{{ old('periode_start') }}" placeholder="Select date" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="periode_end">Stay Period End <b>*</b></label>
                                            <input id="periode_end" name="periode_end" class="backend-form-control date-picker" value="{{ old('periode_end') }}" placeholder="Select date" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="minimum_stay">Minimum Stay <b>*</b></label>
                                            <input class="backend-form-control" id="minimum_stay" name="minimum_stay" value="{{ old('minimum_stay', 1) }}" min="1" type="number" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="contract_rate">Contract Rate <b>*</b></label>
                                            <input class="backend-form-control" id="contract_rate" name="contract_rate" value="{{ old('contract_rate') }}" min="0" type="number" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="markup">Markup <b>*</b></label>
                                            <input class="backend-form-control" id="markup" name="markup" value="{{ old('markup', 0) }}" min="0" type="number" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="quotes">Quote</label>
                                            <input class="backend-form-control" id="quotes" name="quotes" value="{{ old('quotes') }}" placeholder="Short promo quote" type="text">
                                        </div>

                                        <div class="backend-form-field is-wide">
                                            <label for="benefits">Benefits</label>
                                            <textarea class="backend-form-control" id="benefits" name="benefits" data-backend-richtext="true" placeholder="Insert benefits">{{ old('benefits') }}</textarea>
                                        </div>

                                        <div class="backend-form-field is-wide">
                                            <label for="include">Inclusion</label>
                                            <textarea class="backend-form-control" id="include" name="include" data-backend-richtext="true" placeholder="Insert inclusion">{{ old('include') }}</textarea>
                                        </div>

                                        <div class="backend-form-field is-wide">
                                            <label for="additional_info">Additional Information</label>
                                            <textarea class="backend-form-control" id="additional_info" name="additional_info" data-backend-richtext="true" placeholder="Insert additional information">{{ old('additional_info') }}</textarea>
                                        </div>

                                        <div class="backend-form-field is-wide">
                                            <label for="cancellation_policy">Cancellation Policy</label>
                                            <textarea class="backend-form-control" id="cancellation_policy" name="cancellation_policy" data-backend-richtext="true" placeholder="Insert cancellation policy">{{ old('cancellation_policy') }}</textarea>
                                        </div>
                                    </div>

                                    <input class="backend-form-control" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                    <input class="backend-form-control" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                    <input class="backend-form-control" name="service" value="{{ $hotel->name }}" type="hidden">

                                    <div class="backend-form-actions">
                                        <a href="{{ route('admin.hotels.show', $hotel->id) }}#promo" class="backend-button backend-button-secondary">
                                            <i class="fa fa-times"></i>
                                            Cancel
                                        </a>
                                        <button type="submit" class="backend-button backend-button-primary">
                                            <i class="fa fa-floppy-o"></i>
                                            Add Promo
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </section>
                    </div>

                    <aside class="hotel-form-sidebar">
                        <section class="backend-panel hotel-form-panel">
                            <div class="backend-section-header hotel-form-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Guide</span>
                                    <h2>Promo Notes</h2>
                                </div>
                            </div>
                            <div class="hotel-form-panel__body">
                                <span class="backend-status-badge backend-status-badge--draft">Created as Draft</span>
                                <p class="backend-form-help">Promo prices are saved as Draft first. After reviewing rate, period, and room assignment, update the promo status through the Hotel detail workflow.</p>
                            </div>
                        </section>
                    </aside>
                </div>

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
