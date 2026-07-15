@extends('frontend.layouts.app')

@section('title', __('messages.Detail Order'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/order-detail-entry.css') }}">
@endpush

@php
    $receiptCollection = collect($receipts ?? []);
    $statusToneMap = [
        'Pending' => 'pending',
        'Approved' => 'approved',
        'Confirmed' => 'active',
        'Active' => 'active',
        'Paid' => 'paid',
        'Rejected' => 'rejected',
        'Invalid' => 'invalid',
        'Canceled' => 'canceled',
    ];
    $statusTone = $statusToneMap[$order->status] ?? 'default';
    $statusLabel = __('messages.' . $order->status) !== 'messages.' . $order->status ? __('messages.' . $order->status) : $order->status;
    $serviceLabel = __('messages.' . $order->service) !== 'messages.' . $order->service ? __('messages.' . $order->service) : $order->service;
    $isQuotation = in_array($order->request_quotation, ['Yes', 1, '1', true], true);
    $mainTotalLabel = $isQuotation || $airport_shuttle_any_zero ? __('messages.To be advised') : currencyFormatUsd($order->final_price);
    $hotelName = optional($hotel)->name ?? $order->servicename;
    $roomName = optional($room)->rooms ?? $order->subservice;
    $guestRows = collect($guest_details ?? [])->map(function ($guest, $index) use ($number_of_guests_room, $special_days, $special_dates, $extra_bed_prices, $roomName, $order) {
        return [
            'room' => $roomName,
            'guests' => $number_of_guests_room[$index] ?? '-',
            'guest' => $guest ?: '-',
            'price' => currencyFormatUsd($order->price_pax),
            'extra_bed' => !empty($extra_bed_prices[$index]) ? currencyFormatUsd($extra_bed_prices[$index]) : __('messages.None'),
            'occasion' => !empty($special_days[$index]) ? trim(dateFormat($special_dates[$index] ?? null) . ' ' . $special_days[$index]) : null,
        ];
    });
@endphp

@section('content')
    <div class="order-detail-page">
        <header class="order-detail-hero">
            <div class="container">
                @include('partials.alerts')

                <div class="order-detail-hero__content">
                    <div>
                        @include('partials.breadcrumbs', [
                            'breadcrumbs' => [
                                ['url' => route('home'), 'label' => __('messages.Home')],
                                ['url' => route('view.orders'), 'label' => __('messages.Orders')],
                                ['label' => $order->orderno],
                            ],
                            'variant' => 'dark',
                        ])
                        <div class="order-detail-eyebrow">
                            <i class="fa-solid fa-receipt" aria-hidden="true"></i>
                            @lang('messages.Detail Order')
                        </div>
                        <h1 class="order-detail-title">{{ $order->orderno }}</h1>
                        <p class="order-detail-text">
                            {{ $hotelName }} - {{ $roomName }}. @lang('messages.We have received your order, we will contact you as soon as possible to validate the order!')
                        </p>
                    </div>

                    <div class="order-detail-status order-detail-status--{{ $statusTone }}">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        {{ $statusLabel }}
                    </div>
                    @if ($isQuotation)
                        <div class="order-detail-status order-detail-status--quote">
                            <i class="fa-solid fa-file-signature" aria-hidden="true"></i>
                            @lang('messages.Quote request')
                        </div>
                    @endif
                </div>

                <div class="order-detail-summary">
                    <div class="order-detail-metric">
                        <span>@lang('messages.Service')</span>
                        <strong>{{ $serviceLabel }}</strong>
                    </div>
                    <div class="order-detail-metric">
                        <span>@lang('messages.Check In')</span>
                        <strong>{{ dateFormat($order->checkin) }}</strong>
                    </div>
                    <div class="order-detail-metric">
                        <span>@lang('messages.Check Out')</span>
                        <strong>{{ dateFormat($order->checkout) }}</strong>
                    </div>
                    <div class="order-detail-metric">
                        <span>@lang('messages.Total Price')</span>
                        <strong>{{ $mainTotalLabel }}</strong>
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
                                        <span>@lang('messages.Hotel')</span>
                                        <strong>{{ $hotelName }}</strong>
                                    </div>
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Room')</span>
                                        <strong>{{ $roomName }}</strong>
                                    </div>
                                    @if ($order->service === 'Hotel Promo' && $order->promo_name)
                                        <div class="order-detail-info">
                                            <span>@lang('messages.Promo')</span>
                                            <strong>{{ $order->promo_name }}</strong>
                                        </div>
                                    @endif
                                    @if ($order->service === 'Hotel Package' && $order->package_name)
                                        <div class="order-detail-info">
                                            <span>@lang('messages.Package')</span>
                                            <strong>{{ $order->package_name }}</strong>
                                        </div>
                                    @endif
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Duration')</span>
                                        <strong>{{ $order->duration }} {{ $order->duration > 1 ? __('messages.Nights') : __('messages.Night') }}</strong>
                                    </div>
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Location')</span>
                                        <strong>{{ optional($hotel)->region ?? $order->location }}</strong>
                                    </div>
                                    @if ($isQuotation)
                                        <div class="order-detail-info order-detail-info--quote">
                                            <span>@lang('messages.Booking type')</span>
                                            <strong>@lang('messages.Quote request')</strong>
                                        </div>
                                    @endif
                                </div>

                                @foreach ([
                                    'benefits' => 'messages.Benefit',
                                    'include' => 'messages.Include',
                                    'additional_info' => 'messages.Additional Information',
                                    'cancellation_policy' => 'messages.Cancelation Policy',
                                ] as $key => $label)
                                    @if (!empty($order->$key))
                                        <div class="order-detail-note mt-3">
                                            <strong>@lang($label)</strong>
                                            <div class="order-detail-rich mt-2">{!! $order->$key !!}</div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </section>

                        <section class="order-detail-section">
                            <div class="order-detail-section__header">
                                <div>
                                    <div class="order-detail-eyebrow">@lang('messages.Suites and Villas')</div>
                                    <h2 class="order-detail-section__title">@lang('messages.Guest and room details')</h2>
                                </div>
                            </div>
                            <div class="order-detail-section__body">
                                @if ($isQuotation)
                                    <div class="order-detail-alert order-detail-alert--quote">@lang('messages.Requesting a quote for bookings of more than 8 rooms')</div>
                                @elseif ($guestRows->isNotEmpty())
                                    <div class="order-detail-table-wrap">
                                        <table class="order-detail-table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.Room')</th>
                                                    <th>@lang('messages.Guests')</th>
                                                    <th>@lang('messages.Guest Name')</th>
                                                    <th>@lang('messages.Price')</th>
                                                    <th>@lang('messages.Extra Bed')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($guestRows as $guestRow)
                                                    <tr title="{{ $guestRow['occasion'] }}">
                                                        <td>{{ $guestRow['room'] }}</td>
                                                        <td>{{ $guestRow['guests'] }}</td>
                                                        <td>
                                                            {{ $guestRow['guest'] }}
                                                            @if ($guestRow['occasion'])
                                                                <div class="text-muted small">{{ $guestRow['occasion'] }}</div>
                                                            @endif
                                                        </td>
                                                        <td>{{ $guestRow['price'] }}</td>
                                                        <td>{{ $guestRow['extra_bed'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="order-detail-total-box">
                                        <div class="order-detail-total-row">
                                            <span>@lang('messages.Suites and Villas')</span>
                                            <strong>{{ currencyFormatUsd($order->price_total) }}</strong>
                                        </div>
                                    </div>
                                @else
                                    <div class="order-detail-alert">@lang('messages.You have not selected a room on this booking!')</div>
                                @endif
                            </div>
                        </section>

                        @include('frontend.home.orders.details.partials.hotel-detail-modern-addons')
                        @include('frontend.home.orders.details.partials.hotel-detail-modern-price')
                    </div>

                    @include('frontend.home.orders.details.partials.hotel-detail-modern-sidebar')
                </div>
            </div>
        </main>
    </div>

    @include('frontend.home.orders.details.partials.hotel-detail-modern-modals')
@endsection

@push('scripts')
    <script src="{{ mix('build/frontend/js/pages/order-detail.js') }}" defer></script>
@endpush
