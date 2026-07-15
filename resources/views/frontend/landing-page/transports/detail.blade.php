@extends('frontend.layouts.app')
@section('title', $transport->name)
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/transport-detail-entry.css') }}">
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ mix('build/frontend/js/pages/transport-detail.js') }}"></script>
@endpush
@section('content')
    @php
        $transportImage = $transport->cover
            ? getThumbnail('/transports/transports-cover/' . $transport->cover, 960, 640)
            : asset('storage/images/default.webp');
        $serviceCount = $priceGroups->flatten(1)->count();
        $serviceTypes = $priceGroups->keys()->filter()->values();
        $translatedServiceTypes = $serviceTypes->map(function ($serviceType) {
            $translation = __("messages.$serviceType");
            return $translation === "messages.$serviceType" ? $serviceType : $translation;
        });
        $allRates = $priceGroups->flatten(1)->values();
        $defaultRate = $allRates->first();
        $selectedRateId = (int) old('transport_price_id', optional($defaultRate)->id);
        $transportReservationHasErrors = $errors->any() && old('transport_booking_flow') === 'detail_modal';
        $transportReservationErrorStep = 1;
        if ($transportReservationHasErrors) {
            $reviewErrorFields = ['note', 'terms_accepted'];
            $guestErrorFields = [
                'duration',
                'pickup_location',
                'dropoff_location',
                'guest_entries',
            ];
            $serviceErrorFields = [
                'user_id',
                'service_date',
                'airport_shuttle_type',
                'flight_number',
                'flight_date',
                'arrival_flight',
                'arrival_time',
                'departure_flight',
                'departure_time',
            ];
            $errorKeys = collect(array_keys($errors->getMessages()));

            if ($errorKeys->contains(fn ($field) => in_array($field, $reviewErrorFields, true))) {
                $transportReservationErrorStep = 3;
            } elseif ($errorKeys->contains(fn ($field) => in_array($field, $guestErrorFields, true) || str_starts_with($field, 'guest_entries.'))) {
                $transportReservationErrorStep = 2;
            } elseif ($errorKeys->contains(fn ($field) => in_array($field, $serviceErrorFields, true))) {
                $transportReservationErrorStep = 1;
            }
        }
        $rateOptions = $allRates->map(function ($price) {
            $routeLabel = $price->type === 'Daily Rent'
                ? ($price->src ?: __('messages.Destination'))
                : trim(($price->src ?: '-') . ' - ' . ($price->dst ?: '-'));
            $durationValue = (int) ($price->duration ?: 0);
            $durationHoursLabel = $durationValue > 0
                ? $durationValue . ' ' . __('messages.Hours')
                : '-';
            $durationDisplayLabel = $price->type === 'Daily Rent'
                ? ($durationValue > 0 ? $durationHoursLabel . ' / rental' : '-')
                : $durationHoursLabel;

            return [
                'id' => $price->id,
                'type' => $price->type,
                'typeLabel' => __("messages.$price->type") === "messages.$price->type" ? $price->type : __("messages.$price->type"),
                'route' => $routeLabel,
                'src' => $price->src ?: '',
                'dst' => $price->dst ?: $routeLabel,
                'durationLabel' => $durationDisplayLabel,
                'durationHoursLabel' => $durationHoursLabel,
                'durationValue' => $durationValue,
                'hasPrice' => !is_null($price->final_price),
                'price' => !is_null($price->final_price) ? currencyFormatUsd($price->final_price) : __('messages.Request'),
                'priceValue' => (float) ($price->final_price ?: 0),
                'extraTime' => $price->extra_time ? currencyFormatUsd($price->extra_time) . '/' . __('messages.Hours') : null,
                'additionalInfo' => trim(strip_tags((string) $price->additional_info)),
                'createAction' => Auth::check() ? route('func.create.order-transport', $price->id) : route('login'),
            ];
        })->values();
        $defaultOrderDuration = max((int) old('duration', ($defaultRate && $defaultRate->type !== 'Daily Rent') ? (int) $defaultRate->duration : 1), 1);
        $defaultRateHasPrice = $defaultRate && !is_null($defaultRate->final_price);
        $defaultRateBasePrice = $defaultRateHasPrice
            ? (float) $defaultRate->final_price * ($defaultRate->type === 'Daily Rent' ? $defaultOrderDuration : 1)
            : null;
        $defaultRateFinalPrice = !is_null($defaultRateBasePrice)
            ? max($defaultRateBasePrice - (float) $bookingCodeDiscount - (float) $promotionDiscount, 0)
            : null;
    @endphp
    <div class="frontend-page-shell transport-detail-page" data-transport-detail-page
        data-transport-rates='{{ $rateOptions->toJson(JSON_HEX_APOS | JSON_HEX_QUOT) }}'
        data-transport-default-rate-id="{{ $selectedRateId }}"
        data-transport-booking-discount="{{ (float) $bookingCodeDiscount }}"
        data-transport-promotion-discount="{{ (float) $promotionDiscount }}"
        data-transport-booking-order-number="{{ $orderNumber }}"
        data-transport-booking-open="{{ $transportReservationHasErrors ? 'true' : 'false' }}"
        data-transport-booking-error-step="{{ $transportReservationErrorStep }}"
        data-transport-old-shuttle-type="{{ old('airport_shuttle_type', 'Arrival') }}"
        data-transport-processing-label="@lang('transports.detail.order.processing_title')"
        data-transport-submitted-warning="@lang('transports.detail.order.submitted_warning')"
        data-transport-flight-date-label="@lang('transports.detail.order.flight_date')"
        data-transport-service-date-label="@lang('transports.detail.order.service_date')"
        data-transport-estimated-rental-duration-label="@lang('transports.detail.order.estimated_rental_duration')"
        data-transport-estimated-duration-label="@lang('transports.detail.order.estimated_duration')"
        data-transport-price-duration-template="@lang('transports.detail.order.price_duration_label')"
        data-transport-validation-guest-name="@lang('transports.detail.order.validation_guest_name')"
        data-transport-validation-guest-required="@lang('transports.detail.order.validation_guest_required')"
        data-transport-validation-flight-required="@lang('transports.detail.order.validation_flight_required')"
        data-transport-validation-service-required="@lang('transports.detail.order.validation_service_required')"
        data-transport-guest-label-template="@lang('transports.detail.order.guest_label')"
        data-transport-label-name="@lang('transports.detail.order.name')"
        data-transport-label-age-category="@lang('transports.detail.order.age_category')"
        data-transport-label-gender="@lang('transports.detail.order.gender')"
        data-transport-label-phone="@lang('transports.detail.order.phone_number')"
        data-transport-label-optional="@lang('transports.detail.order.optional')"
        data-transport-placeholder-guest-name="@lang('transports.detail.order.guest_name_placeholder')"
        data-transport-placeholder-phone="@lang('transports.detail.order.phone_placeholder')"
        data-transport-select-gender="@lang('transports.detail.order.select_gender')"
        data-transport-adult-label="@lang('tour-detail.age_adult')"
        data-transport-child-label="@lang('tour-detail.age_child')"
        data-transport-male-label="@lang('transports.detail.order.male')"
        data-transport-female-label="@lang('transports.detail.order.female')"
        data-transport-pax-label="@lang('messages.pax')"
    >
        <section class="container-fluid frontend-page-topband transport-detail-topband py-5">
            <div class="container py-4">
                @include('partials.breadcrumbs', [
                    'breadcrumbs' => [
                        ['label' => __('messages.Home'), 'url' => route('home')],
                        ['label' => __('messages.Transports'), 'url' => route('view.transport-service')],
                        ['label' => $transport->name],
                    ],
                ])
                <article class="transport-detail-hero">
                    <div class="transport-detail-hero__media">
                        <img src="{{ $transportImage }}" alt="{{ $transport->name }}" loading="eager"
                            onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';">
                    </div>
                    <div class="transport-detail-hero__content">
                        <div class="transport-detail-kicker">@lang('messages.Transport profile')</div>
                        <h1 class="transport-detail-title">{{ trim(($transport->brand ? $transport->brand . ' ' : '') . $transport->name) }}</h1>
                        <p class="transport-detail-subtitle">
                            @lang('messages.Review vehicle capacity, included services, and available transport rates before creating a reservation request.')
                        </p>
                        <div class="transport-detail-meta">
                            <div class="transport-detail-meta__item">
                                <span>@lang('messages.Type')</span>
                                <strong>
                                    @if ($transport->type)
                                        {{ __("messages.$transport->type") === "messages.$transport->type" ? $transport->type : __("messages.$transport->type") }}
                                    @else
                                        @lang('messages.Transport')
                                    @endif
                                </strong>
                            </div>
                            <div class="transport-detail-meta__item">
                                <span>@lang('messages.Brand')</span>
                                <strong>{{ $transport->brand ?: '-' }}</strong>
                            </div>
                            <div class="transport-detail-meta__item">
                                <span>@lang('messages.Capacity')</span>
                                <strong>{{ $transport->capacity ?: '-' }} @lang('messages.Seat')</strong>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>
        <div class="container frontend-content-section">
            @include('partials.alerts')
            <div class="frontend-layout-split transport-detail-layout">
                <main class="frontend-layout-main">
                    <section class="transport-detail-section frontend-surface-card">
                        <div class="transport-detail-section__header">
                            <div>
                                <div class="accommodation-section__eyebrow">@lang('messages.Vehicle overview')</div>
                                <h2 class="accommodation-section__title">@lang('messages.Service information')</h2>
                            </div>
                        </div>
                        <div class="transport-detail-richtext">
                            @if ($transport->description)
                                {!! $transport->description !!}
                            @else
                                <p>@lang('messages.This transport option is available for agent reservation and guest travel coordination.')</p>
                            @endif
                        </div>
                        <div class="transport-detail-info-grid">
                            @if ($transport->include)
                                <div class="transport-detail-info-card">
                                    <div class="transport-detail-info-card__icon"><i class="fa fa-check"></i></div>
                                    <div>
                                        <h3>@lang('messages.Include')</h3>
                                        <div>{!! $transport->include !!}</div>
                                    </div>
                                </div>
                            @endif
                            @if ($transport->additional_info)
                                <div class="transport-detail-info-card">
                                    <div class="transport-detail-info-card__icon"><i class="fa fa-info"></i></div>
                                    <div>
                                        <h3>@lang('messages.Additional Information')</h3>
                                        <div>{!! $transport->additional_info !!}</div>
                                    </div>
                                </div>
                            @endif
                            @if ($transport->cancellation_policy)
                                <div class="transport-detail-info-card">
                                    <div class="transport-detail-info-card__icon"><i class="fa fa-shield-alt"></i></div>
                                    <div>
                                        <h3>@lang('messages.Cancellation Policy')</h3>
                                        <div>{!! $transport->cancellation_policy !!}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>

                    <section class="transport-detail-section frontend-surface-card" id="transport-rates">
                        <div class="transport-detail-section__header">
                            <div>
                                <div class="accommodation-section__eyebrow">@lang('messages.Reservation options')</div>
                                <h2 class="accommodation-section__title">@lang('messages.Available transport rates')</h2>
                            </div>
                            <div class="transport-detail-section__range">
                                {{ $serviceCount }} @lang('messages.options')
                            </div>
                        </div>
                        @if ($serviceCount > 0)
                            <div class="transport-rate-groups">
                                @foreach ($priceGroups as $type => $prices)
                                    <div class="transport-rate-group" data-rate-group data-rate-group-type="{{ $type }}">
                                        <div class="transport-rate-group__title">
                                            <i class="fa fa-route" aria-hidden="true"></i>
                                            {{ __("messages.$type") === "messages.$type" ? $type : __("messages.$type") }}
                                        </div>
                                        <div class="transport-rate-grid">
                                            @foreach ($prices as $price)
                                                @php
                                                    $routeLabel = $price->type === 'Daily Rent'
                                                        ? ($price->src ?: __('messages.Destination'))
                                                        : trim(($price->src ?: '-') . ' - ' . ($price->dst ?: '-'));
                                                @endphp
                                                <article class="transport-rate-card" data-rate-card
                                                    data-rate-id="{{ $price->id }}" data-rate-type="{{ $price->type }}"
                                                    data-rate-dst="{{ $price->dst ?: $routeLabel }}">
                                                    <div class="transport-rate-card__top">
                                                        <div>
                                                            <span>{{ $routeLabel }}</span>
                                                            <small>{{ __("messages.$type") === "messages.$type" ? $type : __("messages.$type") }}</small>
                                                        </div>
                                                        <strong>{{ !is_null($price->final_price) ? currencyFormatUsd($price->final_price) : __('messages.Request') }}</strong>
                                                    </div>
                                                    <div class="transport-rate-card__price">
                                                        <span>@lang('messages.Estimated transport price')</span>
                                                        <strong>{{ !is_null($price->final_price) ? currencyFormatUsd($price->final_price) : __('messages.Request') }}</strong>
                                                    </div>
                                                    <div class="transport-rate-card__facts">
                                                        <div>
                                                            <span>{{ $price->type === 'Daily Rent' ? __('transports.detail.order.estimated_use_time_per_rental') : __('messages.Estimated travel duration') }}</span>
                                                            <strong>
                                                                @if ($price->type === 'Daily Rent')
                                                                    {{ $price->duration ?: '-' }} @lang('messages.Hours') / @lang('transports.detail.order.rental_suffix')
                                                                @else
                                                                    {{ $price->duration ?: '-' }} @lang('messages.Hours')
                                                                @endif
                                                            </strong>
                                                        </div>
                                                        @if ($price->extra_time)
                                                            <div>
                                                                <span>@lang('messages.Extra time')</span>
                                                                <strong>{{ currencyFormatUsd($price->extra_time) }}/@lang('messages.Hours')</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if ($price->additional_info)
                                                        <div class="transport-rate-card__note">{!! $price->additional_info !!}</div>
                                                    @endif
                                                </article>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="transport-detail-empty">
                                <div class="transport-detail-empty__icon"><i class="fa fa-car"></i></div>
                                <h3>@lang('messages.No transport rates available')</h3>
                                <p>@lang('messages.Please contact the reservation team to request a custom transport quotation.')</p>
                            </div>
                        @endif
                    </section>

                    @if ($similarTransports->count() > 0)
                        <section class="transport-detail-section frontend-surface-card">
                            <div class="transport-detail-section__header">
                                <div>
                                    <div class="accommodation-section__eyebrow">@lang('messages.Similar transport')</div>
                                    <h2 class="accommodation-section__title">@lang('messages.Other options in this type')</h2>
                                </div>
                            </div>
                            <div class="transport-similar-grid">
                                @foreach ($similarTransports as $similar)
                                    @php
                                        $similarImage = $similar->cover
                                            ? getThumbnail('/transports/transports-cover/' . $similar->cover, 480, 320)
                                            : asset('storage/images/default.webp');
                                    @endphp
                                    <a class="transport-similar-card" href="{{ route('transport.show', $similar->id) }}">
                                        <img src="{{ $similarImage }}" alt="{{ $similar->name }}" loading="lazy"
                                            onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';">
                                        <div>
                                            <strong>{{ $similar->name }}</strong>
                                            <span>{{ $similar->capacity ?: '-' }} @lang('messages.Seat')</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </main>

                <aside class="frontend-layout-sidebar">
                    <div class="transport-detail-cta frontend-surface-card frontend-sticky-panel">
                        <div class="transport-detail-cta__eyebrow">@lang('messages.Partner reservation')</div>
                        <h2>@lang('messages.Ready to reserve this transport?')</h2>
                        <p>@lang('transports.detail.order.sidebar_text')</p>
                        <div class="transport-detail-cta__facts">
                            <div>
                                <span>@lang('messages.Available services')</span>
                                <strong>{{ $translatedServiceTypes->isNotEmpty() ? $translatedServiceTypes->implode(', ') : '-' }}</strong>
                            </div>
                            <div>
                                <span>@lang('messages.Capacity')</span>
                                <strong>{{ $transport->capacity ?: '-' }} @lang('messages.Seat')</strong>
                            </div>
                        </div>
                        @if ($defaultRate)
                            @auth
                                <div class="transport-reservation-form" data-transport-reservation-form>
                            @else
                                <div class="transport-reservation-form" data-transport-reservation-form>
                            @endauth
                                <div class="transport-reservation-field">
                                    <label for="transportPriceType">@lang('messages.Transport Type')</label>
                                    <select id="transportPriceType" class="form-control" data-transport-price-type>
                                        @foreach ($serviceTypes as $serviceType)
                                            <option value="{{ $serviceType }}" @selected($defaultRate->type === $serviceType)>
                                                {{ __("messages.$serviceType") === "messages.$serviceType" ? $serviceType : __("messages.$serviceType") }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="transport-reservation-field" data-transport-destination-group hidden>
                                    <label for="transportPriceDestination">@lang('messages.Destination / Source')</label>
                                    <select id="transportPriceDestination" class="form-control" data-transport-price-destination></select>
                                </div>
                                <div class="transport-selected-rate" data-transport-selected-rate>
                                    <span>@lang('messages.Selected rate')</span>
                                    <strong data-selected-rate-price>{{ !is_null($defaultRate->final_price) ? currencyFormatUsd($defaultRate->final_price) : __('messages.Request') }}</strong>
                                    <small data-selected-rate-route>
                                        {{ $defaultRate->type === 'Daily Rent' ? ($defaultRate->src ?: __('messages.Destination')) : trim(($defaultRate->src ?: '-') . ' - ' . ($defaultRate->dst ?: '-')) }}
                                    </small>
                                    <em data-selected-rate-duration>
                                        @if ($defaultRate->type === 'Daily Rent')
                                            {{ ($defaultRate->duration ?: '-') . ' ' . __('messages.Hours') . ' / ' . __('transports.detail.order.rental_suffix') }}
                                        @else
                                            {{ $defaultRate->duration ?: '-' }} @lang('messages.Hours')
                                        @endif
                                    </em>
                                </div>
                                @auth
                                    <button type="button" class="btn btn-primary transport-detail-cta__button" data-open-transport-reservation>
                                        @lang('messages.Reserve this service')
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary transport-detail-cta__button">
                                        @lang('messages.Login to reserve')
                                    </a>
                                @endauth
                            </div>
                        @else
                            <a href="#transport-rates" class="btn btn-primary transport-detail-cta__button">
                                @lang('messages.View rates and reserve')
                            </a>
                        @endif
                    </div>
                </aside>
            </div>
        </div>

        @auth
            @if ($defaultRate)
                <div class="transport-reservation-modal frontend-order-modal" data-transport-reservation-modal aria-hidden="true">
                    <div class="transport-reservation-modal__backdrop" data-close-transport-reservation></div>
                    <div class="transport-reservation-modal__dialog frontend-order-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="transportReservationTitle">
                        <div class="transport-reservation-modal__shell frontend-order-modal__surface">
                            <button type="button" class="transport-reservation-modal__close frontend-order-modal__close" data-close-transport-reservation aria-label="@lang('messages.Close')">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button>

                            <div class="transport-reservation-modal__header frontend-order-modal__service">
                                <div class="transport-reservation-modal__media frontend-order-modal__media">
                                    <img src="{{ $transportImage }}" alt="{{ $transport->name }}" loading="lazy"
                                        onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';">
                                </div>
                                <div class="frontend-order-modal__service-content">
                                    <div class="transport-reservation-modal__eyebrow frontend-order-modal__eyebrow">@lang('transports.detail.order.create_order')</div>
                                    <h2 id="transportReservationTitle" class="frontend-order-modal__title">
                                        {{ trim(($transport->brand ? $transport->brand . ' - ' : '') . $transport->name) }}
                                    </h2>
                                    <p>
                                        {!! $transport->description ? \Illuminate\Support\Str::limit(trim(strip_tags($transport->description)), 220) : __('transports.detail.order.fallback_description') !!}
                                    </p>
                                    <div class="transport-reservation-summary-grid transport-reservation-summary-grid--header frontend-order-modal__summary">
                                        <div class="transport-reservation-summary-card frontend-order-modal__summary-card">
                                            <span>@lang('messages.Transport Type')</span>
                                            <strong data-modal-selected-rate-type>{{ __("messages.$defaultRate->type") === "messages.$defaultRate->type" ? $defaultRate->type : __("messages.$defaultRate->type") }}</strong>
                                        </div>
                                        <div class="transport-reservation-summary-card frontend-order-modal__summary-card">
                                            <span>@lang('messages.Destination / Source')</span>
                                            <strong data-modal-selected-rate-route-copy>{{ $defaultRate->type === 'Daily Rent' ? ($defaultRate->src ?: __('messages.Destination')) : trim(($defaultRate->src ?: '-') . ' - ' . ($defaultRate->dst ?: '-')) }}</strong>
                                        </div>
                                        <div class="transport-reservation-summary-card frontend-order-modal__summary-card">
                                            <span data-modal-selected-rate-duration-label>{{ $defaultRate->type === 'Daily Rent' ? __('transports.detail.order.estimated_rental_duration') : __('transports.detail.order.estimated_duration') }}</span>
                                            <strong data-modal-selected-rate-duration>
                                                @if ($defaultRate->type === 'Daily Rent')
                                                    {{ ($defaultRate->duration ?: '-') . ' ' . __('messages.Hours') . ' / ' . __('transports.detail.order.rental_suffix') }}
                                                @else
                                                    {{ $defaultRate->duration ?: '-' }} @lang('messages.Hours')
                                                @endif
                                            </strong>
                                        </div>
                                        <div class="transport-reservation-summary-card frontend-order-modal__summary-card">
                                            <span>@lang('transports.detail.order.extra_time')</span>
                                            <strong data-modal-selected-rate-extra>{{ $defaultRate->extra_time ? currencyFormatUsd($defaultRate->extra_time) . '/' . __('messages.Hours') : '-' }}</strong>
                                        </div>
                                    </div>
                                    <div class="transport-reservation-modal__hero-card frontend-order-modal__price-card">
                                        <span>@lang('transports.detail.order.estimated_total')</span>
                                        <strong data-modal-selected-rate-price>{{ !is_null($defaultRateFinalPrice) ? currencyFormatUsd($defaultRateFinalPrice) : __('messages.Request') }}</strong>
                                        <small data-modal-selected-rate-route>
                                            {{ $defaultRate->type === 'Daily Rent' ? ($defaultRate->src ?: __('messages.Destination')) : trim(($defaultRate->src ?: '-') . ' - ' . ($defaultRate->dst ?: '-')) }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <form
                                action="{{ route('func.create.order-transport', $defaultRate->id) }}"
                                method="POST"
                                class="transport-reservation-wizard frontend-order-modal__form"
                                novalidate
                                data-transport-booking-form
                            >
                                @csrf
                                <input type="hidden" name="orderno" value="{{ $orderNumber }}" data-transport-order-number-input>
                                <input type="hidden" name="transport_id" value="{{ $transport->id }}">
                                <input type="hidden" name="transport_booking_flow" value="detail_modal">
                                <input type="hidden" name="transport_price_id" value="{{ $selectedRateId }}" data-selected-transport-price-id>
                                <input type="hidden" name="duration" value="{{ $defaultOrderDuration }}" data-transport-duration-input>
                                <input type="hidden" name="service_date" value="{{ old('service_date', old('flight_date', old('arrival_time', old('departure_time', old('pickup_date'))))) }}" data-transport-service-date>

                                <div class="transport-reservation-wizard__nav frontend-order-modal__nav">
                                    <button type="button" class="transport-reservation-wizard__step frontend-order-modal__nav-item is-active" data-wizard-step-target="1">
                                        <span>1</span>
                                        <div>
                                            <strong>@lang('transports.detail.order.service_tab')</strong>
                                            <small>@lang('transports.detail.order.service_tab_hint')</small>
                                        </div>
                                    </button>
                                    <button type="button" class="transport-reservation-wizard__step frontend-order-modal__nav-item" data-wizard-step-target="2">
                                        <span>2</span>
                                        <div>
                                            <strong>@lang('transports.detail.order.guest_tab')</strong>
                                            <small>@lang('transports.detail.order.guest_tab_hint')</small>
                                        </div>
                                    </button>
                                    <button type="button" class="transport-reservation-wizard__step frontend-order-modal__nav-item" data-wizard-step-target="3">
                                        <span>3</span>
                                        <div>
                                            <strong>@lang('transports.detail.order.review_tab')</strong>
                                            <small>@lang('transports.detail.order.review_tab_hint')</small>
                                        </div>
                                    </button>
                                </div>

                                <section class="transport-reservation-wizard__panel frontend-order-modal__panel is-active" data-wizard-panel="1">
                                    <div class="transport-reservation-wizard__heading frontend-order-modal__heading">
                                        <div>
                                            <div class="transport-reservation-wizard__eyebrow frontend-order-modal__heading-eyebrow">@lang('transports.detail.order.step_label', ['number' => 1])</div>
                                            <h3>@lang('transports.detail.order.reservation_title')</h3>
                                        </div>
                                        <p>@lang('transports.detail.order.reservation_text')</p>
                                    </div>

                                    @canany(['posDev','posAuthor','posRsv'])
                                        <div class="transport-reservation-field mb-3">
                                            <label for="transportAgent">@lang('transports.detail.order.select_agent') <span class="transport-reservation-required" aria-hidden="true">*</span></label>
                                            <select id="transportAgent" name="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                                <option value="">@lang('messages.Select Agent')</option>
                                                @foreach ($agents as $agent)
                                                    <option value="{{ $agent->id }}" data-order-number="{{ $transportOrderNumbersByAgent[$agent->id] ?? $orderNumber }}" @selected((string) old('user_id') === (string) $agent->id)>
                                                        {{ $agent->name }} ({{ $agent->code }}) @ {{ $agent->office }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                                <div class="alert-form">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endcanany

                                    <div class="transport-reservation-grid transport-reservation-grid--trip transport-reservation-grid--service-pair" data-modal-flight-grid hidden>
                                        <div class="transport-reservation-field" data-modal-shuttle-type-group hidden>
                                            <label for="modal_airport_shuttle_type">@lang('transports.detail.order.flight_type') <span class="transport-reservation-required" aria-hidden="true">*</span></label>
                                            <select name="airport_shuttle_type" id="modal_airport_shuttle_type" class="form-control @error('airport_shuttle_type') is-invalid @enderror" data-modal-airport-shuttle-type>
                                                <option value="Arrival" @selected(old('airport_shuttle_type', 'Arrival') === 'Arrival')>@lang('messages.Arrival')</option>
                                                <option value="Departure" @selected(old('airport_shuttle_type') === 'Departure')>@lang('messages.Departure')</option>
                                            </select>
                                            @error('airport_shuttle_type')
                                                <div class="alert-form">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="transport-reservation-field" data-modal-airport-flight-fields hidden>
                                            <label for="flight_number">@lang('transports.detail.order.flight_number') <span class="transport-reservation-required" aria-hidden="true">*</span></label>
                                            <input id="flight_number" name="flight_number" type="text" value="{{ old('flight_number', old('arrival_flight', old('departure_flight'))) }}" class="form-control @error('flight_number') is-invalid @enderror" placeholder="@lang('transports.detail.order.flight_number_placeholder')">
                                            @error('flight_number')
                                                <div class="alert-form">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="transport-reservation-field">
                                            <label for="flight_date" data-transport-date-label>@lang('transports.detail.order.flight_date') <span class="transport-reservation-required" aria-hidden="true">*</span></label>
                                            <input id="flight_date" name="flight_date" type="text"
                                                value="{{ old('flight_date', old('service_date', old('arrival_time', old('departure_time', old('pickup_date'))))) }}"
                                                class="form-control @error('flight_date') is-invalid @enderror"
                                                placeholder="@lang('messages.Select date and time')" data-transport-datetime required>
                                            @error('flight_date')
                                                <div class="alert-form">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="transport-reservation-field" data-modal-daily-rent-location-fields hidden>
                                            <label for="duration">@lang('transports.detail.order.duration') <span class="transport-reservation-required" aria-hidden="true">*</span></label>
                                            <input id="duration" type="number" min="1" value="{{ old('duration', 1) }}" class="form-control @error('duration') is-invalid @enderror" placeholder="@lang('messages.Minimum 1 day')" data-transport-duration>
                                            @error('duration')
                                                <div class="alert-form">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="transport-reservation-field" data-modal-daily-rent-location-fields hidden>
                                            <label for="pickup_location">@lang('transports.detail.order.pickup_location') <span class="transport-reservation-required" aria-hidden="true">*</span></label>
                                            <input id="pickup_location" name="pickup_location" type="text" value="{{ old('pickup_location') }}" class="form-control @error('pickup_location') is-invalid @enderror" placeholder="@lang('transports.detail.order.location_placeholder')">
                                            @error('pickup_location')
                                                <div class="alert-form">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="transport-reservation-field" data-modal-daily-rent-location-fields hidden>
                                            <label for="dropoff_location">@lang('transports.detail.order.dropoff_location') <span class="transport-reservation-required" aria-hidden="true">*</span></label>
                                            <input id="dropoff_location" name="dropoff_location" type="text" value="{{ old('dropoff_location') }}" class="form-control @error('dropoff_location') is-invalid @enderror" placeholder="@lang('transports.detail.order.location_placeholder')">
                                            @error('dropoff_location')
                                                <div class="alert-form">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="transport-reservation-wizard__actions frontend-order-modal__actions">
                                        <button type="button" class="btn btn-primary" data-wizard-next>@lang('transports.detail.order.continue_to_guest')</button>
                                    </div>
                                </section>

                                <section class="transport-reservation-wizard__panel frontend-order-modal__panel" data-wizard-panel="2">
                                    <div class="transport-reservation-wizard__heading frontend-order-modal__heading">
                                        <div>
                                            <div class="transport-reservation-wizard__eyebrow frontend-order-modal__heading-eyebrow">@lang('transports.detail.order.step_label', ['number' => 2])</div>
                                        </div>
                                        <p>@lang('transports.detail.order.guest_text')</p>
                                    </div>

                                    <div class="transport-reservation-field">
                                        <div class="transport-reservation-guest-head">
                                            <div>
                                                <label class="mb-0">@lang('messages.Guest Detail')</label>
                                                <small class="transport-reservation-helper">@lang('transports.detail.order.guest_helper')</small>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-add-transport-guest>
                                                <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                                <span>@lang('transports.detail.order.add_guest')</span>
                                            </button>
                                        </div>

                                        <div class="transport-reservation-guest-list" data-transport-guest-list data-capacity="{{ $transport->capacity }}">
                                            @php
                                                $oldGuestEntries = old('guest_entries', [['name' => '', 'age' => 'Adult', 'sex' => '', 'phone' => '']]);
                                            @endphp
                                            @foreach ($oldGuestEntries as $guestIndex => $guestEntry)
                                                <div class="transport-reservation-guest-item" data-transport-guest-item>
                                                    <div class="transport-reservation-guest-item__index">
                                                        <strong data-transport-guest-label>@lang('transports.detail.order.guest_label', ['number' => $guestIndex + 1])</strong>
                                                    </div>
                                                    <div class="transport-reservation-guest-item__content">
                                                        <div class="transport-reservation-field transport-reservation-field--compact">
                                                            <label>@lang('transports.detail.order.name') <span class="transport-reservation-required" aria-hidden="true">*</span></label>
                                                            <input type="text" name="guest_entries[{{ $guestIndex }}][name]" value="{{ $guestEntry['name'] ?? '' }}" class="form-control @error('guest_entries.' . $guestIndex . '.name') is-invalid @enderror" placeholder="@lang('transports.detail.order.guest_name_placeholder')" required>
                                                            @error('guest_entries.' . $guestIndex . '.name')
                                                                <div class="alert-form">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="transport-reservation-field transport-reservation-field--compact">
                                                            <label>@lang('transports.detail.order.age_category') <span class="transport-reservation-required" aria-hidden="true">*</span></label>
                                                            <select name="guest_entries[{{ $guestIndex }}][age]" class="form-control @error('guest_entries.' . $guestIndex . '.age') is-invalid @enderror" required>
                                                                <option value="Adult" @selected(($guestEntry['age'] ?? 'Adult') === 'Adult')>@lang('tour-detail.age_adult')</option>
                                                                <option value="Child" @selected(($guestEntry['age'] ?? '') === 'Child')>@lang('tour-detail.age_child')</option>
                                                            </select>
                                                            @error('guest_entries.' . $guestIndex . '.age')
                                                                <div class="alert-form">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="transport-reservation-field transport-reservation-field--compact">
                                                            <label>@lang('transports.detail.order.gender') <span class="transport-reservation-required" aria-hidden="true">*</span></label>
                                                            <select name="guest_entries[{{ $guestIndex }}][sex]" class="form-control @error('guest_entries.' . $guestIndex . '.sex') is-invalid @enderror" required>
                                                                <option value="">@lang('transports.detail.order.select_gender')</option>
                                                                <option value="Male" @selected(($guestEntry['sex'] ?? '') === 'Male')>@lang('transports.detail.order.male')</option>
                                                                <option value="Female" @selected(($guestEntry['sex'] ?? '') === 'Female')>@lang('transports.detail.order.female')</option>
                                                            </select>
                                                            @error('guest_entries.' . $guestIndex . '.sex')
                                                                <div class="alert-form">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="transport-reservation-field transport-reservation-field--compact">
                                                            <label>@lang('transports.detail.order.phone_number') <span class="transport-reservation-optional">(@lang('transports.detail.order.optional'))</span></label>
                                                            <input type="text" name="guest_entries[{{ $guestIndex }}][phone]" value="{{ $guestEntry['phone'] ?? '' }}" class="form-control @error('guest_entries.' . $guestIndex . '.phone') is-invalid @enderror" placeholder="@lang('transports.detail.order.phone_placeholder')">
                                                            @error('guest_entries.' . $guestIndex . '.phone')
                                                                <div class="alert-form">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="transport-reservation-guest-item__action">
                                                            <label class="transport-reservation-guest-item__action-label">&nbsp;</label>
                                                            <button type="button" class="transport-reservation-guest-item__remove" data-remove-transport-guest @if (count($oldGuestEntries) === 1) hidden @endif>
                                                                X
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('guest_entries')
                                            <div class="alert-form m-t-8">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="transport-reservation-wizard__actions frontend-order-modal__actions">
                                        <button type="button" class="btn btn-light" data-wizard-prev>@lang('transports.detail.order.back')</button>
                                        <button type="button" class="btn btn-primary" data-wizard-next data-wizard-next-review disabled aria-disabled="true">@lang('transports.detail.order.continue_to_review')</button>
                                    </div>
                                </section>

                                <section class="transport-reservation-wizard__panel frontend-order-modal__panel" data-wizard-panel="3">
                                    @php
                                        $reviewGuestEntries = old('guest_entries', [['name' => '', 'age' => 'Adult', 'sex' => '', 'phone' => '']]);
                                        $reviewGuestCollection = collect($reviewGuestEntries)
                                            ->map(fn ($guestEntry) => [
                                                'name' => trim($guestEntry['name'] ?? ''),
                                                'age' => $guestEntry['age'] ?? 'Adult',
                                                'sex' => $guestEntry['sex'] ?? '',
                                                'phone' => trim($guestEntry['phone'] ?? ''),
                                            ])
                                            ->filter(fn ($guestEntry) => $guestEntry['name'] !== '')
                                            ->values();
                                        $reviewAdultCount = $reviewGuestCollection->where('age', 'Adult')->count();
                                        $reviewChildCount = $reviewGuestCollection->where('age', 'Child')->count();
                                    @endphp
                                    <div class="transport-reservation-wizard__heading frontend-order-modal__heading">
                                        <div>
                                            <div class="transport-reservation-wizard__eyebrow frontend-order-modal__heading-eyebrow">@lang('transports.detail.order.step_label', ['number' => 3])</div>
                                            <h3>@lang('transports.detail.order.review_title')</h3>
                                        </div>
                                        <p>@lang('transports.detail.order.review_text')</p>
                                    </div>

                                    <div class="transport-reservation-review-grid">
                                        <div class="transport-reservation-review-card">
                                            <span>@lang('transports.detail.order.order_no')</span>
                                            <strong data-review-order-number>{{ $orderNumber }}</strong>
                                        </div>
                                        <div class="transport-reservation-review-card">
                                            <span>@lang('transports.detail.order.service_tab')</span>
                                            <strong data-review-service>{{ __("messages.$defaultRate->type") === "messages.$defaultRate->type" ? $defaultRate->type : __("messages.$defaultRate->type") }}</strong>
                                        </div>
                                        <div class="transport-reservation-review-card">
                                            <span>@lang('transports.detail.order.service_date')</span>
                                            <strong data-review-service-date>{{ old('flight_date', old('service_date', old('arrival_time', old('departure_time', old('pickup_date', '-'))))) }}</strong>
                                        </div>
                                        <div class="transport-reservation-review-card" data-review-route-card @if ($defaultRate->type === 'Daily Rent') hidden @endif>
                                            <span>@lang('transports.detail.order.route')</span>
                                            <strong data-review-route>{{ $defaultRate->type === 'Daily Rent' ? ($defaultRate->src ?: __('messages.Destination')) : trim(($defaultRate->src ?: '-') . ' - ' . ($defaultRate->dst ?: '-')) }}</strong>
                                        </div>
                                        <div class="transport-reservation-review-card" data-review-flight-type-card @if ($defaultRate->type !== 'Airport Shuttle') hidden @endif>
                                            <span>@lang('transports.detail.order.flight_type')</span>
                                            <strong data-review-flight-type>{{ old('airport_shuttle_type', 'Arrival') }}</strong>
                                        </div>
                                        <div class="transport-reservation-review-card" data-review-flight-number-card @if ($defaultRate->type !== 'Airport Shuttle') hidden @endif>
                                            <span>@lang('transports.detail.order.flight_number')</span>
                                            <strong data-review-flight-number>{{ old('flight_number', old('arrival_flight', old('departure_flight', '-'))) }}</strong>
                                        </div>
                                        <div class="transport-reservation-review-card" data-review-pickup-card @if ($defaultRate->type !== 'Daily Rent') hidden @endif>
                                            <span>@lang('transports.detail.order.pickup_location')</span>
                                            <strong data-review-pickup-location>{{ old('pickup_location', $defaultRate->src ?: '-') }}</strong>
                                        </div>
                                        <div class="transport-reservation-review-card" data-review-dropoff-card @if ($defaultRate->type !== 'Daily Rent') hidden @endif>
                                            <span>@lang('transports.detail.order.dropoff_location')</span>
                                            <strong data-review-dropoff-location>{{ old('dropoff_location', $defaultRate->dst ?: '-') }}</strong>
                                        </div>
                                        <div class="transport-reservation-review-card">
                                            <span>@lang('transports.detail.order.guests')</span>
                                            <strong data-review-guests-total>{{ $reviewGuestCollection->count() }} @lang('messages.pax')</strong>
                                        </div>
                                        <div class="transport-reservation-review-card">
                                            <span>@lang('transports.detail.order.adult_guests')</span>
                                            <strong data-review-guests-adult>{{ $reviewAdultCount }} @lang('tour-detail.age_adult')</strong>
                                        </div>
                                        <div class="transport-reservation-review-card">
                                            <span>@lang('transports.detail.order.child_guests')</span>
                                            <strong data-review-guests-child>{{ $reviewChildCount }} @lang('tour-detail.age_child')</strong>
                                        </div>
                                        <div class="transport-reservation-review-card">
                                            <span>@lang('transports.detail.order.total')</span>
                                            <strong data-review-total>{{ !is_null($defaultRateFinalPrice) ? currencyFormatUsd($defaultRateFinalPrice) : __('messages.Request') }}</strong>
                                        </div>
                                    </div>

                                    <div class="transport-reservation-review-guests">
                                        <div class="transport-reservation-review-guests__head">
                                            <span>@lang('transports.detail.order.guest_tab')</span>
                                        </div>
                                        <div class="transport-reservation-review-guests__table-wrap">
                                            <table class="transport-reservation-review-guests__table">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('messages.No')</th>
                                                        <th>@lang('transports.detail.order.name')</th>
                                                        <th>@lang('transports.detail.order.age_category')</th>
                                                        <th>@lang('transports.detail.order.gender')</th>
                                                        <th>@lang('transports.detail.order.phone_number')</th>
                                                    </tr>
                                                </thead>
                                                <tbody data-review-guest-table-body>
                                                    @foreach ($reviewGuestCollection as $guestIndex => $guestEntry)
                                                        <tr>
                                                            <td>{{ $guestIndex + 1 }}</td>
                                                            <td>{{ $guestEntry['name'] }}</td>
                                                            <td>{{ $guestEntry['age'] ?: '-' }}</td>
                                                            <td>{{ $guestEntry['sex'] ?: '-' }}</td>
                                                            <td>{{ $guestEntry['phone'] ?: '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="transport-reservation-review-guests__empty" data-review-guest-empty @if ($reviewGuestCollection->isNotEmpty()) hidden @endif>
                                            @lang('transports.detail.order.guest_table_empty')
                                        </div>
                                    </div>

                                    <div class="transport-reservation-field">
                                        <label for="note">@lang('messages.Note')</label>
                                        <textarea id="note" name="note" rows="4" class="form-control @error('note') is-invalid @enderror" placeholder="@lang('messages.Optional')">{{ old('note') }}</textarea>
                                        @error('note')
                                            <div class="alert-form">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="transport-reservation-price-breakdown">
                                        <div class="transport-reservation-price-breakdown__row">
                                            <span>@lang('transports.detail.order.base_service')</span>
                                            <strong data-review-base-price>{{ !is_null($defaultRateBasePrice) ? currencyFormatUsd($defaultRateBasePrice) : __('messages.Request') }}</strong>
                                        </div>
                                        <div class="transport-reservation-price-breakdown__row @if ($bookingCodeDiscount <= 0) is-hidden @endif" data-review-booking-discount-row>
                                            <span>@lang('messages.Booking Code')</span>
                                            <strong data-review-booking-discount>{{ currencyFormatUsd($bookingCodeDiscount) }}</strong>
                                        </div>
                                        <div class="transport-reservation-price-breakdown__row @if ($promotionDiscount <= 0) is-hidden @endif" data-review-promotion-discount-row>
                                            <span>@lang('messages.Promotion')</span>
                                            <strong data-review-promotion-discount>{{ currencyFormatUsd($promotionDiscount) }}</strong>
                                        </div>
                                        <div class="transport-reservation-price-breakdown__row transport-reservation-price-breakdown__row--total">
                                            <span>@lang('messages.Total Price')</span>
                                            <strong data-review-final-price>{{ !is_null($defaultRateFinalPrice) ? currencyFormatUsd($defaultRateFinalPrice) : __('messages.Request') }}</strong>
                                        </div>
                                    </div>

                                    <div class="transport-reservation-inline-note transport-reservation-inline-note--accent">
                                        @lang('transports.detail.order.review_note')
                                    </div>

                                    @include('partials.order-confirmation-checkbox', [
                                        'id' => 'transportTermsAccepted',
                                    ])

                                    <div class="transport-reservation-wizard__actions frontend-order-modal__actions">
                                        <button type="button" class="btn btn-light" data-wizard-prev>@lang('transports.detail.order.back')</button>
                                        <button type="submit" class="btn btn-primary" data-submit-transport-reservation>
                                            <i class="fa fa-shopping-basket" aria-hidden="true"></i>
                                            <span>@lang('transports.detail.order.submit')</span>
                                        </button>
                                    </div>
                                </section>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="transport-reservation-submit-overlay frontend-order-modal__overlay hidden" aria-hidden="true" data-transport-submit-overlay>
                    <div class="transport-reservation-submit-overlay__card">
                        <span class="transport-reservation-submit-overlay__spinner" aria-hidden="true"></span>
                        <strong>@lang('transports.detail.order.processing_title')</strong>
                        <p>@lang('transports.detail.order.processing_text')</p>
                    </div>
                </div>
            @endif
        @endauth
    </div>
@endsection
