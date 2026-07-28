@extends('frontend.layouts.app')
@section('title', 'Order Transport')

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/transport-booking-entry.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/frontend/js/pages/transport-booking.js') }}" defer></script>
@endpush

@section('content')
    @php
        $orderNumber = 'ORD.' . date('Ymd', strtotime($now)) . '.TRN' . $orderno;
        $transportTypeLabel = __('messages.' . $transport->type) === 'messages.' . $transport->type ? $transport->type : __('messages.' . $transport->type);
        $routeLabel = $price->type === 'Daily Rent'
            ? ($price->src ?: '-')
            : trim(($price->src ?: '-') . ' - ' . ($price->dst ?: '-'));
        $serviceTypeLabel = __('messages.' . $price->type) === 'messages.' . $price->type ? $price->type : __('messages.' . $price->type);
        $routeTitle = __('messages.Route') === 'messages.Route' ? 'Route' : __('messages.Route');
    @endphp

    <div
        class="frontend-page-shell transport-booking-page"
        data-transport-booking-page
        data-transport-price="{{ $transport_price }}"
        data-booking-discount="{{ $bookingcode_disc }}"
        data-promotion-discount="{{ $promotion_price }}"
        data-order-type="{{ $price->type }}"
        data-processing-label="@lang('messages.Loading')"
        data-submitted-warning="This order has already been submitted. Please start a new transport booking to create another order."
    >
        <section class="container-fluid frontend-page-topband transport-booking-topband py-5">
            <div class="container py-4">
                @include('partials.breadcrumbs', [
                    'breadcrumbs' => [
                        ['label' => __('messages.Home'), 'url' => route('home')],
                        ['label' => __('messages.Transports'), 'url' => route('view.transport-service')],
                        ['label' => $transport->name, 'url' => route('transport.show', $transport->id)],
                        ['label' => __('messages.Create Order')],
                    ],
                ])
                <div class="frontend-page-intro transport-booking-hero">
                    <div>
                        <h1 class="hotel-booking-hero__title">{{ $transport->brand ? $transport->brand . ' ' : '' }}{{ $transport->name }}</h1>
                        <p class="hotel-booking-hero__text">
                            Review the selected transport service, complete route and guest planning, then submit the reservation request to the operations team.
                        </p>
                    </div>
                    <div class="hotel-booking-summary">
                        <div class="hotel-booking-meta__item">
                            <span>@lang('messages.Order No')</span>
                            <strong>{{ $orderNumber }}</strong>
                        </div>
                        <div class="hotel-booking-meta__item">
                            <span>@lang('messages.Service')</span>
                            <strong>{{ $serviceTypeLabel }}</strong>
                        </div>
                        <div class="hotel-booking-meta__item">
                            <span>@lang('messages.Capacity')</span>
                            <strong>{{ $transport->capacity ?: '-' }} @lang('messages.Seat')</strong>
                        </div>
                        <div class="hotel-booking-meta__item">
                            <span>@lang('messages.Order Date')</span>
                            <strong>{{ dateFormat($now) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="container frontend-content-section">
            @include('partials.alerts')

            <form
                id="createOrderTranspor"
                action="{{ route('func.create.order-transport', $price->id) }}"
                method="POST"
                class="transport-booking-form"
                data-transport-booking-form
            >
                @csrf

                <div class="frontend-layout-split transport-booking-layout">
                    <main class="frontend-layout-main">
                        @canany(['posDev','posAuthor','posRsv'])
                            <section class="frontend-surface-card transport-booking-section">
                                <div class="transport-booking-section__header">
                                    <div>
                                        <div class="frontend-detail-block__eyebrow">Admin</div>
                                        <h2 class="frontend-detail-block__title">Agent Assignment</h2>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-0">
                                            <label for="user_id">Select Agent <span>*</span></label>
                                            <select name="user_id" id="user_id" class="custom-select @error('user_id') is-invalid @enderror" required>
                                                <option value="">Select Agent</option>
                                                @foreach ($agents as $agent)
                                                    <option value="{{ $agent->id }}" @selected(old('user_id') == $agent->id)>
                                                        {{ $agent->username . ' (' . $agent->code . ') @' . $agent->office }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                                <div class="alert-form">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>
                        @endcanany

                        <section class="frontend-surface-card transport-booking-section">
                            <div class="transport-booking-section__header">
                                <div>
                                    <div class="frontend-detail-block__eyebrow">@lang('messages.Transport')</div>
                                    <h2 class="frontend-detail-block__title">Service Information</h2>
                                </div>
                            </div>

                            <div class="transport-booking-facts">
                                <div class="frontend-fact-card">
                                    <span>@lang('messages.Transport')</span>
                                    <strong>{{ $transport->name }}</strong>
                                </div>
                                <div class="frontend-fact-card">
                                    <span>@lang('messages.Type')</span>
                                    <strong>{{ $transportTypeLabel }}</strong>
                                </div>
                                <div class="frontend-fact-card">
                                    <span>@lang('messages.Service')</span>
                                    <strong>{{ $serviceTypeLabel }}</strong>
                                </div>
                                <div class="frontend-fact-card frontend-fact-card--wide">
                                    <span>{{ $price->type === 'Daily Rent' ? __('messages.Location') : $routeTitle }}</span>
                                    <strong>{{ $routeLabel }}</strong>
                                </div>
                            </div>

                            @if ($transport->include || $transport->additional_info || $price->additional_info)
                                <div class="transport-booking-notes">
                                    @if ($transport->include)
                                        <article class="transport-booking-note-card">
                                            <h3>@lang('messages.Include')</h3>
                                            <div>{!! $transport->include !!}</div>
                                        </article>
                                    @endif
                                    @if ($transport->additional_info || $price->additional_info)
                                        <article class="transport-booking-note-card">
                                            <h3>@lang('messages.Additional Information')</h3>
                                            <div>
                                                {!! $transport->additional_info !!}
                                                {!! $price->additional_info !!}
                                            </div>
                                        </article>
                                    @endif
                                </div>
                            @endif
                        </section>

                        <section class="frontend-surface-card transport-booking-section">
                            <div class="transport-booking-section__header">
                                <div>
                                    <div class="frontend-detail-block__eyebrow">@lang('messages.Details')</div>
                                    <h2 class="frontend-detail-block__title">Transport Planning</h2>
                                </div>
                                <div class="transport-booking-section__meta">@lang('messages.Please make sure all the data is correct before you place an order')</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="number_of_guests">@lang('messages.Number of Guest')</label>
                                        <input
                                            id="number_of_guests"
                                            name="number_of_guests"
                                            type="number"
                                            min="1"
                                            max="{{ $transport->capacity }}"
                                            value="{{ old('number_of_guests') }}"
                                            class="form-control @error('number_of_guests') is-invalid @enderror"
                                            placeholder="@lang('messages.Maximum') {{ $transport->capacity }} @lang('messages.Guests')"
                                            required
                                        >
                                        @error('number_of_guests')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                @if ($price->type == 'Daily Rent')
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="duration">@lang('messages.Duration')</label>
                                            <input
                                                id="duration"
                                                name="duration"
                                                type="number"
                                                min="1"
                                                value="{{ old('duration', 1) }}"
                                                class="form-control @error('duration') is-invalid @enderror"
                                                placeholder="@lang('messages.Insert duration by day')"
                                                required
                                                data-transport-duration
                                            >
                                            @error('duration')
                                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <input type="hidden" name="order_type" value="{{ $price->type }}">
                                @else
                                    <input type="hidden" name="duration" value="{{ $price->duration }}" data-transport-duration>
                                    <input type="hidden" name="order_type" value="{{ $price->type }}">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="airport_shuttle_type">@lang('messages.Type')</label>
                                            <select
                                                name="airport_shuttle_type"
                                                id="airport_shuttle_type"
                                                class="custom-select @error('airport_shuttle_type') is-invalid @enderror"
                                                data-airport-shuttle-type
                                            >
                                                <option value="Arrival" @selected(old('airport_shuttle_type', 'Arrival') === 'Arrival')>@lang('messages.Arrival')</option>
                                                <option value="Departure" @selected(old('airport_shuttle_type') === 'Departure')>@lang('messages.Departure')</option>
                                            </select>
                                            @error('airport_shuttle_type')
                                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($price->type == 'Daily Rent')
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="pickup_date">@lang('messages.Pickup Date')</label>
                                            <input
                                                id="pickup_date"
                                                type="text"
                                                name="pickup_date"
                                                value="{{ old('pickup_date') }}"
                                                class="form-control @error('pickup_date') is-invalid @enderror"
                                                placeholder="@lang('messages.Select date and time')"
                                                required
                                                autocomplete="off"
                                                data-ui-picker="datetime"
                                                data-ui-picker-format="YYYY-MM-DD HH:mm"
                                                data-booking-datetime
                                            >
                                            @error('pickup_date')
                                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="pickup_location">@lang('messages.Pickup Location')</label>
                                            <input
                                                id="pickup_location"
                                                type="text"
                                                name="pickup_location"
                                                value="{{ old('pickup_location') }}"
                                                class="form-control @error('pickup_location') is-invalid @enderror"
                                                placeholder="Enter hotel name or region"
                                                required
                                            >
                                            @error('pickup_location')
                                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="dropoff_location">@lang('messages.Dropoff Location')</label>
                                            <input
                                                id="dropoff_location"
                                                type="text"
                                                name="dropoff_location"
                                                value="{{ old('dropoff_location') }}"
                                                class="form-control @error('dropoff_location') is-invalid @enderror"
                                                placeholder="Enter hotel name or region"
                                                required
                                            >
                                            @error('dropoff_location')
                                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @elseif ($price->type == 'Airport Shuttle')
                                <div class="transport-booking-airport-group" id="arrival_fields" data-airport-arrival-fields>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="arrival_flight">@lang('messages.Arrival Flight')</label>
                                                <input id="arrival_flight" type="text" name="arrival_flight" value="{{ old('arrival_flight') }}" class="form-control @error('arrival_flight') is-invalid @enderror" placeholder="@lang('messages.Arrival Flight')">
                                                @error('arrival_flight')
                                                    <div class="alert-form">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label for="arrival_time">@lang('messages.Arrival Date and Time')</label>
                                                <input id="arrival_time" type="text" name="arrival_time" value="{{ old('arrival_time') }}" class="form-control @error('arrival_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" autocomplete="off" data-ui-picker="datetime" data-ui-picker-format="YYYY-MM-DD HH:mm" data-booking-datetime>
                                                @error('arrival_time')
                                                    <div class="alert-form">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="transport-booking-airport-group" id="departure_fields" data-airport-departure-fields hidden>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="departure_flight">@lang('messages.Departure Flight')</label>
                                                <input id="departure_flight" type="text" name="departure_flight" value="{{ old('departure_flight') }}" class="form-control @error('departure_flight') is-invalid @enderror" placeholder="@lang('messages.Departure Flight')">
                                                @error('departure_flight')
                                                    <div class="alert-form">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label for="departure_time">@lang('messages.Departure Date and Time')</label>
                                                <input id="departure_time" type="text" name="departure_time" value="{{ old('departure_time') }}" class="form-control @error('departure_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" autocomplete="off" data-ui-picker="datetime" data-ui-picker-format="YYYY-MM-DD HH:mm" data-booking-datetime>
                                                @error('departure_time')
                                                    <div class="alert-form">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="transport-booking-note transport-booking-note--info">
                                        Pick-up Location and Drop-off Location are required for Daily Rent so the order stores where the guest will be picked up and where the final transfer should end.
                                    </div>
                                </div>
                            @endif
                        </section>

                        <section class="frontend-surface-card transport-booking-section">
                            <div class="transport-booking-section__header">
                                <div>
                                    <div class="frontend-detail-block__eyebrow">@lang('messages.Guest Detail')</div>
                                    <h2 class="frontend-detail-block__title">Guest and service notes</h2>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="guest_detail">@lang('messages.Guest Detail')</label>
                                        <textarea
                                            id="guest_detail"
                                            name="guest_detail"
                                            rows="5"
                                            class="form-control @error('guest_detail') is-invalid @enderror"
                                            placeholder="@lang('messages.Insert guest name')"
                                            required
                                        >{{ old('guest_detail') }}</textarea>
                                        <small class="transport-booking-helper">Add guest names, lead contact, or operational details needed by the driver and reservation team.</small>
                                        @error('guest_detail')
                                            <div class="alert-form">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-0">
                                        <label for="note">@lang('messages.Note')</label>
                                        <textarea
                                            id="note"
                                            name="note"
                                            rows="4"
                                            class="form-control @error('note') is-invalid @enderror"
                                            placeholder="@lang('messages.Optional')"
                                        >{{ old('note') }}</textarea>
                                        @error('note')
                                            <div class="alert-form">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </section>
                    </main>

                    <aside class="frontend-layout-sidebar">
                        <div class="frontend-surface-card frontend-sticky-panel transport-booking-sidebar">
                            <div class="transport-booking-sidebar__eyebrow">@lang('messages.Order')</div>
                            <h2>Transport Booking Summary</h2>
                            <p>The final total updates automatically based on the selected booking type and duration.</p>

                            <div class="transport-selected-rate">
                                <span>@lang('messages.Total Price')</span>
                                <strong id="final_price">{{ currencyFormatUsd($final_price) }}</strong>
                            </div>

                            <div class="transport-booking-sidebar__facts">
                                <div>
                                    <span>@lang('messages.Order No')</span>
                                    <strong>{{ $orderNumber }}</strong>
                                </div>
                                <div>
                                    <span>@lang('messages.Transport')</span>
                                    <strong>{{ $transport->name }}</strong>
                                </div>
                                <div>
                                    <span>@lang('messages.Service')</span>
                                    <strong>{{ $serviceTypeLabel }}</strong>
                                </div>
                                <div>
                                    <span>{{ $price->type === 'Daily Rent' ? __('messages.Location') : $routeTitle }}</span>
                                    <strong>{{ $routeLabel }}</strong>
                                </div>
                            </div>

                            <div class="transport-booking-price-card">
                                <div class="transport-booking-price-row">
                                    <span>@lang('messages.Price per pax')</span>
                                    <strong id="normal_price">{{ currencyFormatUsd($normal_price) }}</strong>
                                </div>
                                @if ($bookingcode_disc > 0)
                                    <div class="transport-booking-price-row transport-booking-price-row--discount">
                                        <span>@lang('messages.Booking Code')</span>
                                        <strong id="booking_code_discount">{{ currencyFormatUsd($bookingcode_disc) }}</strong>
                                    </div>
                                @endif
                                @if ($promotion_price > 0)
                                    <div class="transport-booking-price-row transport-booking-price-row--discount">
                                        <span>@lang('messages.Promotion')</span>
                                        <strong id="promotion_price">{{ currencyFormatUsd($promotion_price) }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="transport-booking-sidebar__notice">
                                @lang('messages.Please make sure all the data is correct before you place an order')
                            </div>

                            <input type="hidden" name="orderno" value="{{ $orderNumber }}">
                            <input type="hidden" name="transport_id" value="{{ $transport->id }}">
                            <input type="hidden" name="bookingcode_id" value="{{ $bookingcode ? $bookingcode->id : null }}">

                            <div class="transport-booking-sidebar__actions">
                                <button type="submit" class="btn btn-primary" data-submit-button>
                                    <i class="fa fa-shopping-basket" aria-hidden="true"></i>
                                    <span>@lang('messages.Order')</span>
                                </button>
                                <button type="button" class="btn btn-danger" data-transport-go-back>
                                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                    <span>@lang('messages.Cancel')</span>
                                </button>
                            </div>
                        </div>
                    </aside>
                </div>
            </form>
        </div>

        <div class="booking-submit-overlay hidden" aria-hidden="true" data-form-submit-overlay>
            <div class="booking-submit-overlay__card">
                <span class="booking-submit-overlay__spinner" aria-hidden="true"></span>
                <strong>@lang('messages.Loading')</strong>
                <p>Your transport order is being processed. Please wait a moment.</p>
            </div>
        </div>
    </div>
@endsection
