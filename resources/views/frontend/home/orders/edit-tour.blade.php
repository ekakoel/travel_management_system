@extends('frontend.layouts.app')

@section('title', __('messages.Edit Order'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/order-detail-entry.css') }}">
@endpush

@php
    $statusToneMap = [
        'Pending' => 'pending',
        'Approved' => 'approved',
        'Confirmed' => 'active',
        'Active' => 'active',
        'Paid' => 'paid',
        'Rejected' => 'rejected',
        'Invalid' => 'invalid',
        'Canceled' => 'canceled',
        'Draft' => 'default',
    ];
    $statusTone = $statusToneMap[$order->status] ?? 'default';
    $statusLabel = __('messages.' . $order->status) !== 'messages.' . $order->status ? __('messages.' . $order->status) : $order->status;
    $serviceLabel = __('messages.' . $order->service) !== 'messages.' . $order->service ? __('messages.' . $order->service) : $order->service;
    $travelDateValue = old(
        'travel_date',
        $order->travel_date
            ? \Carbon\Carbon::parse($order->travel_date)->format('Y-m-d')
            : ($order->checkin ? \Carbon\Carbon::parse($order->checkin)->format('Y-m-d') : '')
    );
    $guestCountValue = old('number_of_guests', $order->number_of_guests);
    $tourDisplayName = trim((string) ($tour->$langName ?: $tour->name));
    $tourDisplayType = trim((string) ($tour->type?->$langType ?: $tour->type?->type));
    $tourDisplayArea = trim((string) ($tour->$langArea ?: $tour->area));
    $includeContent = trim((string) (data_get($order, $langInclude) ?: $order->include ?: data_get($tour, $langInclude) ?: $tour->include));
    $excludeContent = trim((string) (data_get($order, $langExclude) ?: $order->exclude ?: data_get($tour, $langExclude) ?: $tour->exclude));
    $additionalInfoContent = trim((string) ($order->additional_info ?: data_get($tour, $langAdditionalInfo) ?: $tour->additional_info));
    $cancellationPolicyContent = trim((string) ($order->cancellation_policy ?: data_get($tour, $langCancellationPolicy) ?: $tour->cancellation_policy));
    $itineraryContent = trim((string) ($order->itinerary ?: ($generatedTourItinerary ?? '') ?: data_get($tour, $langItinerary) ?: $tour->itinerary));
    $destinationsContent = trim((string) ($order->destinations ?: data_get($tour, $langPackageHighlights) ?: $tour->package_highlights));
    $guestManifestContent = trim((string) $order->guest_detail);
@endphp

@section('content')
    <div class="order-detail-page order-edit-page">
        <header class="order-detail-hero">
            <div class="container">
                @include('partials.alerts')

                <div class="order-detail-hero__content">
                    <div>
                        @include('partials.breadcrumbs', [
                            'breadcrumbs' => [
                                ['url' => route('home'), 'label' => __('messages.Home')],
                                ['url' => route('view.orders'), 'label' => __('messages.Orders')],
                                ['url' => route('view.detail-order-tour', ['id' => $order->id]), 'label' => $order->orderno],
                                ['label' => __('messages.Edit Order')],
                            ],
                            'variant' => 'dark',
                        ])
                        <div class="order-detail-eyebrow">
                            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                            @lang('messages.Edit Order')
                        </div>
                        <h1 class="order-detail-title">{{ $order->orderno }}</h1>
                        <p class="order-detail-text">
                            {{ $tourDisplayName }}. @lang('messages.Please make sure all the data is correct before you submit the order!')
                        </p>
                    </div>

                    <div class="order-detail-status order-detail-status--{{ $statusTone }}">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        {{ $statusLabel }}
                    </div>
                </div>

                <div class="order-detail-summary">
                    <div class="order-detail-metric">
                        <span>@lang('messages.Service')</span>
                        <strong>{{ $serviceLabel }}</strong>
                    </div>
                    <div class="order-detail-metric">
                        <span>@lang('messages.Travel Date')</span>
                        <strong data-order-edit-summary-date>{{ $travelDateValue ?: '-' }}</strong>
                    </div>
                    <div class="order-detail-metric">
                        <span>@lang('messages.Number of Guests')</span>
                        <strong data-order-edit-summary-guests>{{ $guestCountValue }}</strong>
                    </div>
                    <div class="order-detail-metric">
                        <span>@lang('messages.Total Price')</span>
                        <strong data-order-edit-summary-total>{{ currencyFormatUsd($order->final_price) }}</strong>
                    </div>
                </div>
            </div>
        </header>

        <main class="order-detail-main">
            <div class="container">
                <div class="order-detail-layout">
                    <div class="order-detail-stack">
                        <section class="order-detail-section">
                            <div class="order-detail-section__header">
                                <div>
                                    <div class="order-detail-eyebrow">@lang('messages.Order')</div>
                                    <h2 class="order-detail-section__title">@lang('messages.Booking Details')</h2>
                                </div>
                            </div>
                            <div class="order-detail-section__body">
                                <div class="order-detail-grid">
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Order No')</span>
                                        <strong>{{ $order->orderno }}</strong>
                                    </div>
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Order Date')</span>
                                        <strong>{{ dateTimeFormat($order->created_at) }}</strong>
                                    </div>
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Tour Package')</span>
                                        <strong>{{ $tourDisplayName }}</strong>
                                    </div>
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Type')</span>
                                        <strong>{{ $tourDisplayType ?: '-' }}</strong>
                                    </div>
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Tour Area')</span>
                                        <strong>{{ $tourDisplayArea ?: '-' }}</strong>
                                    </div>
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Duration')</span>
                                        <strong>{{ $tour->duration_nights > 0
                                            ? __('tour-detail.duration_days_nights', ['days' => $tour->duration_days, 'nights' => $tour->duration_nights])
                                            : __('tour-detail.duration_days', ['days' => $tour->duration_days]) }}</strong>
                                    </div>
                                </div>

                                @foreach ([
                                    'itinerary' => [$itineraryContent, __('messages.Itinerary')],
                                    'destinations' => [$destinationsContent, __('tour-detail.package_highlights')],
                                    'include' => [$includeContent, __('messages.Include')],
                                    'exclude' => [$excludeContent, __('messages.Exclude')],
                                    'additional_info' => [$additionalInfoContent, __('messages.Additional Information')],
                                    'cancellation_policy' => [$cancellationPolicyContent, __('messages.Cancelation Policy')],
                                ] as $section)
                                    @if ($section[0] !== '')
                                        <div class="order-detail-note mt-3">
                                            <strong>{{ $section[1] }}</strong>
                                            <div class="order-detail-rich mt-2">{!! $section[0] !!}</div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </section>

                        <section class="order-detail-section">
                            <div class="order-detail-section__header">
                                <div>
                                    <div class="order-detail-eyebrow">@lang('messages.Edit Order')</div>
                                    <h2 class="order-detail-section__title">@lang('messages.Guest Details')</h2>
                                </div>
                            </div>
                            <div class="order-detail-section__body">
                                <div class="order-edit-banner">
                                    <i class="fa-solid fa-info-circle" aria-hidden="true"></i>
                                    <div>
                                        <strong>@lang('tour-detail.pickup_dropoff_note_title')</strong>
                                        <p>@lang('tour-detail.pickup_dropoff_note_body')</p>
                                    </div>
                                </div>

                                <form id="edit-order-tour" action="{{ route('func.order-tour.update', $order->id) }}" method="POST"
                                    data-order-edit-form
                                    data-rates='@json($prices)'
                                    data-submission-key="tour-order-edit:{{ $order->id }}"
                                    data-processing-label="@lang('messages.Processing')"
                                    data-no-rate-label="@lang('tour-detail.no_active_price')">
                                    @csrf
                                    @method('PUT')
                                    @include('partials.form-submission-token')
                                    @include('partials.form-submit-overlay', [
                                        'title' => __('messages.Processing'),
                                        'message' => __('tour-detail.processing_order_message'),
                                    ])

                                    <div class="order-edit-field-grid">
                                        <div class="order-edit-field">
                                            <label for="orderTravelDate" class="form-label">@lang('messages.Travel Date') <span>*</span></label>
                                            <input id="orderTravelDate" type="date" name="travel_date"
                                                class="form-control @error('travel_date') is-invalid @enderror"
                                                value="{{ $travelDateValue }}" required data-order-edit-field="travelDate">
                                            @error('travel_date')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="order-edit-field">
                                            <label for="orderGuestCount" class="form-label">@lang('messages.Number of Guests') <span>*</span></label>
                                            <input id="orderGuestCount" type="number" min="2" max="200" step="1" name="number_of_guests"
                                                class="form-control @error('number_of_guests') is-invalid @enderror"
                                                value="{{ $guestCountValue }}" required data-order-edit-guests data-order-edit-field="guestCount">
                                            @error('number_of_guests')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="order-edit-field">
                                            <label for="orderPickupLocation" class="form-label">@lang('messages.Pick up location') <span>*</span></label>
                                            <input id="orderPickupLocation" type="text" name="pickup_location"
                                                class="form-control @error('pickup_location') is-invalid @enderror"
                                                value="{{ old('pickup_location', $order->pickup_location) }}"
                                                placeholder="@lang('messages.ex'): @lang('messages.Hotel Name') / @lang('messages.Airport')"
                                                required data-order-edit-field="pickup">
                                            @error('pickup_location')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="order-edit-field">
                                            <label for="orderDropoffLocation" class="form-label">@lang('messages.Drop off location') <span>*</span></label>
                                            <input id="orderDropoffLocation" type="text" name="dropoff_location"
                                                class="form-control @error('dropoff_location') is-invalid @enderror"
                                                value="{{ old('dropoff_location', $order->dropoff_location) }}"
                                                placeholder="@lang('messages.ex'): @lang('messages.Hotel Name') / @lang('messages.Airport')"
                                                required data-order-edit-field="dropoff">
                                            @error('dropoff_location')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="order-edit-field order-edit-field--full">
                                            <label class="form-label">@lang('messages.Guest Detail')</label>
                                            <div class="order-edit-readonly">
                                                @if ($guestManifestContent !== '')
                                                    {!! $guestManifestContent !!}
                                                @else
                                                    <p>@lang('tour-detail.no_guest_added')</p>
                                                @endif
                                            </div>
                                            <p class="order-edit-helptext">@lang('tour-detail.guest_manifest_hint')</p>
                                        </div>

                                        <div class="order-edit-field order-edit-field--full">
                                            <label for="orderNote" class="form-label">@lang('messages.Note')</label>
                                            <textarea id="orderNote" name="note" rows="4"
                                                class="form-control @error('note') is-invalid @enderror"
                                                placeholder="@lang('messages.Optional')">{{ old('note', strip_tags((string) $order->note)) }}</textarea>
                                            @error('note')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="order-edit-price-panel">
                                        <div class="order-edit-price-row">
                                            <span>@lang('messages.Price') / @lang('messages.pax')</span>
                                            <strong data-order-edit-price-per-pax>{{ currencyFormatUsd($order->price_pax) }}</strong>
                                        </div>
                                        <div class="order-edit-price-row">
                                            <span>@lang('messages.Number of Guests')</span>
                                            <strong data-order-edit-price-guests>{{ $guestCountValue }}</strong>
                                        </div>
                                        <div class="order-edit-price-row order-edit-price-row--total">
                                            <span>@lang('messages.Total Price')</span>
                                            <strong data-order-edit-price-total>{{ currencyFormatUsd($order->final_price) }}</strong>
                                        </div>
                                        <p class="order-edit-price-note" data-order-edit-price-note></p>
                                    </div>

                                    <div class="order-edit-actions">
                                        <a href="{{ route('view.detail-order-tour', ['id' => $order->id]) }}" class="order-detail-btn order-detail-btn--soft">
                                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                                            @lang('messages.Back')
                                        </a>
                                        <button type="submit" class="order-detail-btn order-detail-btn--primary" data-order-edit-submit>
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>
                                            @lang('messages.Checkout')
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>

                    <aside class="order-detail-sidebar">
                        <div class="order-detail-sidebar-card">
                            <h2 class="order-detail-sidebar-card__title">@lang('messages.Order Details')</h2>
                            <p class="order-detail-sidebar-card__text">
                                @if ($order->status === 'Pending')
                                    @lang('messages.We have received your order, we will contact you as soon as possible to validate the order!')
                                @elseif ($order->status === 'Rejected' || $order->status === 'Invalid')
                                    {!! $order->msg ?: __('messages.Please make sure all the data is correct before you submit the order!') !!}
                                @else
                                    @lang('messages.Please make sure all the data is correct before you submit the order!')
                                @endif
                            </p>

                            <div class="order-detail-grid mt-3">
                                <div class="order-detail-info">
                                    <span>@lang('messages.Tour Start')</span>
                                    <strong>{{ dateFormat($order->checkin) }}</strong>
                                </div>
                                <div class="order-detail-info">
                                    <span>@lang('messages.Tour End')</span>
                                    <strong>{{ dateFormat($order->checkout) }}</strong>
                                </div>
                                <div class="order-detail-info">
                                    <span>@lang('messages.Pick up location')</span>
                                    <strong data-order-edit-summary-pickup>{{ old('pickup_location', $order->pickup_location) }}</strong>
                                </div>
                                <div class="order-detail-info">
                                    <span>@lang('messages.Drop off location')</span>
                                    <strong data-order-edit-summary-dropoff>{{ old('dropoff_location', $order->dropoff_location) }}</strong>
                                </div>
                            </div>

                            <div class="order-detail-action-list">
                                <a href="{{ route('view.detail-order-tour', ['id' => $order->id]) }}" class="order-detail-btn order-detail-btn--soft">
                                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                    @lang('messages.Order Details')
                                </a>
                                <a href="{{ route('view.orders') }}#orderTour" class="order-detail-btn order-detail-btn--soft">
                                    <i class="fa-solid fa-list" aria-hidden="true"></i>
                                    @lang('messages.Orders')
                                </a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script src="{{ mix('build/frontend/js/pages/order-edit.js') }}" defer></script>
@endpush
