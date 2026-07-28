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
                    title="Edit Promo Price"
                    description="Update promo periods, room assignment, pricing, status, and copy for {{ $hotel->name }}."
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
                            <li class="breadcrumb-item active" aria-current="page">Edit Promo Price</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--{{ strtolower($promo->status) === 'active' ? 'active' : 'draft' }}">{{ $promo->status }}</span>
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

                            <form id="hotelPromoUpdate" action="{{ route('admin.hotels.promos.update', $promo->id) }}" method="post">
                                @csrf
                                @method('put')
                                <div class="hotel-form-panel__body">
                                    <div class="backend-form-grid backend-form-grid--compact">
                                        <div class="backend-form-field">
                                            <label for="name">Promo Name</label>
                                            <input class="backend-form-control" id="name" name="name" value="{{ old('name', $promo->name) }}" placeholder="Promo name" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="status">Status <b>*</b></label>
                                            <select class="backend-form-control" id="status" name="status" required>
                                                @foreach (['Active', 'Draft'] as $status)
                                                    <option value="{{ $status }}" @selected(old('status', $promo->status) === $status)>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="promotion_type">Promotion Type <b>*</b></label>
                                            <select class="backend-form-control" id="promotion_type" name="promotion_type" required>
                                                @foreach (['Special Offer', 'Best Choice', 'Best Price', 'Hot Deal'] as $promotionType)
                                                    <option value="{{ $promotionType }}" @selected(old('promotion_type', $promo->promotion_type) === $promotionType)>{{ $promotionType }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="rooms_id">Room <b>*</b></label>
                                            <select class="backend-form-control" id="rooms_id" name="rooms_id" required>
                                                @foreach ($rooms as $room)
                                                    <option value="{{ $room->id }}" @selected((int) old('rooms_id', $promo->rooms_id) === (int) $room->id)>{{ $room->rooms }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="booking_code">Booking Code</label>
                                            <input class="backend-form-control" id="booking_code" name="booking_code" value="{{ old('booking_code', $promo->booking_code) }}" type="text">
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="quotes">Quote</label>
                                            <input class="backend-form-control" id="quotes" name="quotes" value="{{ old('quotes', $promo->quotes) }}" type="text">
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="book_periode_start">Booking Period Start</label>
                                            <input id="book_periode_start" name="book_periode_start" class="backend-form-control date-picker" value="{{ old('book_periode_start', dateFormat($promo->book_periode_start)) }}" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="book_periode_end">Booking Period End</label>
                                            <input id="book_periode_end" name="book_periode_end" class="backend-form-control date-picker" value="{{ old('book_periode_end', dateFormat($promo->book_periode_end)) }}" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="periode_start">Stay Period Start</label>
                                            <input id="periode_start" name="periode_start" class="backend-form-control date-picker" value="{{ old('periode_start', dateFormat($promo->periode_start)) }}" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="periode_end">Stay Period End</label>
                                            <input id="periode_end" name="periode_end" class="backend-form-control date-picker" value="{{ old('periode_end', dateFormat($promo->periode_end)) }}" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="minimum_stay">Minimum Stay</label>
                                            <input class="backend-form-control" id="minimum_stay" name="minimum_stay" value="{{ old('minimum_stay', $promo->minimum_stay) }}" min="1" type="number" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="contract_rate">Contract Rate</label>
                                            <input class="backend-form-control" id="contract_rate" name="contract_rate" value="{{ old('contract_rate', $promo->contract_rate) }}" min="0" type="number" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="markup">Markup</label>
                                            <input class="backend-form-control" id="markup" name="markup" value="{{ old('markup', $promo->markup) }}" min="0" type="number" required>
                                        </div>

                                        @foreach ([
                                            'benefits' => 'Benefits',
                                            'benefits_traditional' => 'Benefits - Chinese Traditional',
                                            'benefits_simplified' => 'Benefits - Chinese Simplified',
                                            'include' => 'Inclusion',
                                            'include_traditional' => 'Inclusion - Chinese Traditional',
                                            'include_simplified' => 'Inclusion - Chinese Simplified',
                                            'additional_info' => 'Additional Information',
                                            'additional_info_traditional' => 'Additional Information - Chinese Traditional',
                                            'additional_info_simplified' => 'Additional Information - Chinese Simplified',
                                        ] as $field => $label)
                                            <div class="backend-form-field is-wide">
                                                <label for="{{ $field }}">{{ $label }}</label>
                                                <textarea class="backend-form-control" id="{{ $field }}" name="{{ $field }}" data-backend-richtext="true">{{ old($field, $promo->{$field}) }}</textarea>
                                            </div>
                                        @endforeach
                                    </div>

                                    <input class="backend-form-control" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                    <input class="backend-form-control" name="author" value="{{ Auth::user()->id }}" type="hidden">

                                    <div class="backend-form-actions">
                                        <a href="{{ route('admin.hotels.show', $hotel->id) }}#promo" class="backend-button backend-button-secondary">
                                            <i class="fa fa-times"></i>
                                            Cancel
                                        </a>
                                        <button type="submit" class="backend-button backend-button-primary">
                                            <i class="fa fa-floppy-o"></i>
                                            Update Promo
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
                                <span class="backend-status-badge backend-status-badge--{{ strtolower($promo->status) === 'active' ? 'active' : 'draft' }}">{{ $promo->status }}</span>
                                <p class="backend-form-help">Active promos can be used by booking workflows. Keep date periods aligned with the related room and normal price availability.</p>
                            </div>
                        </section>
                    </aside>
                </div>
            </div>
        </main>
    @endcan
@endsection
