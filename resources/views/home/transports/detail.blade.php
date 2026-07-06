@extends('frontend.layouts.app')
@section('title', $transport->name)
@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/transport-detail-entry.css') }}">
@endpush
@push('scripts')
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
        $rateOptions = $allRates->map(function ($price) {
            $routeLabel = $price->type === 'Daily Rent'
                ? ($price->src ?: __('messages.Destination'))
                : trim(($price->src ?: '-') . ' - ' . ($price->dst ?: '-'));

            return [
                'id' => $price->id,
                'type' => $price->type,
                'route' => $routeLabel,
                'dst' => $price->dst ?: $routeLabel,
                'duration' => ($price->duration ?: '-') . ' ' . __('messages.Hours'),
                'price' => $price->final_price ? currencyFormatUsd($price->final_price) : __('messages.Request'),
                'action' => Auth::check() ? route('view.order-transport', $price->id) : route('login'),
            ];
        })->values();
    @endphp
    <div class="frontend-page-shell transport-detail-page" data-transport-detail-page
        data-transport-rates='{{ $rateOptions->toJson(JSON_HEX_APOS | JSON_HEX_QUOT) }}'>
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
                                                        <strong>{{ $price->final_price ? currencyFormatUsd($price->final_price) : __('messages.Request') }}</strong>
                                                    </div>
                                                    <div class="transport-rate-card__price">
                                                        <span>@lang('messages.Estimated transport price')</span>
                                                        <strong>{{ $price->final_price ? currencyFormatUsd($price->final_price) : __('messages.Request') }}</strong>
                                                    </div>
                                                    <div class="transport-rate-card__facts">
                                                        <div>
                                                            <span>@lang('messages.Duration')</span>
                                                            <strong>{{ $price->duration ?: '-' }} @lang('messages.Hours')</strong>
                                                        </div>
                                                        @if ($price->extra_time)
                                                            <div>
                                                                <span>@lang('messages.Extra time')</span>
                                                                <strong>{{ currencyFormatUsd($price->extra_time) }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if ($price->additional_info)
                                                        <div class="transport-rate-card__note">{!! $price->additional_info !!}</div>
                                                    @endif
                                                    <button type="button" class="btn btn-outline-primary transport-rate-card__button"
                                                        data-select-transport-rate="{{ $price->id }}">
                                                        @lang('messages.Select this rate')
                                                    </button>
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
                        <p>@lang('messages.Select the transport price type and route, then continue into the existing transport order form.')</p>
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
                                <form class="transport-reservation-form" action="{{ route('view.order-transport', $defaultRate->id) }}"
                                    method="POST" data-transport-reservation-form>
                                    @csrf
                            @else
                                <form class="transport-reservation-form" action="{{ route('login') }}" method="GET"
                                    data-transport-reservation-form>
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
                                    <strong data-selected-rate-price>{{ $defaultRate->final_price ? currencyFormatUsd($defaultRate->final_price) : __('messages.Request') }}</strong>
                                    <small data-selected-rate-route>
                                        {{ $defaultRate->type === 'Daily Rent' ? ($defaultRate->src ?: __('messages.Destination')) : trim(($defaultRate->src ?: '-') . ' - ' . ($defaultRate->dst ?: '-')) }}
                                    </small>
                                    <em data-selected-rate-duration>{{ $defaultRate->duration ?: '-' }} @lang('messages.Hours')</em>
                                </div>
                                <button type="submit" class="btn btn-primary transport-detail-cta__button">
                                    @auth
                                        @lang('messages.Reserve this service')
                                    @else
                                        @lang('messages.Login to reserve')
                                    @endauth
                                </button>
                            </form>
                        @else
                            <a href="#transport-rates" class="btn btn-primary transport-detail-cta__button">
                                @lang('messages.View rates and reserve')
                            </a>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endsection
