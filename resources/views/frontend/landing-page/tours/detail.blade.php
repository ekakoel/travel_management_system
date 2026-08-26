@extends('frontend.layouts.app')
@section('title', $tour->$langName ?: $tour->name)

@php
    $errors = $errors ?? new Illuminate\Support\ViewErrorBag();
    $tourDisplayName = trim((string) ($tour->$langName ?: $tour->name));
    $tourDisplayType = trim((string) ($tour->type?->$langType ?: $tour->type?->type));
    $tourDisplayDescription = $tour->$langDescription
        ?: $tour->$langShortDescription
        ?: $tour->description
        ?: $tour->short_description;
    $hasTourRouteMap = !empty($tourMapLocations);
    $durationLabel = (int) $tour->duration_nights > 0
        ? __('tour-detail.duration_days_nights', ['days' => $tour->duration_days, 'nights' => $tour->duration_nights])
        : __('tour-detail.duration_days', ['days' => $tour->duration_days]);
    $coverImage = $tour->cover
        ? getThumbnail('/tours/tours-cover/' . $tour->cover, 1200, 760)
        : asset('images/default.webp');
    $availableTourRates = collect($prices)
        ->filter(fn (array $tourPrice) => filled($tourPrice['unit_price_usd'] ?? null))
        ->sortBy('unit_price_usd_minor')
        ->values();
    $lowestRate = $availableTourRates->first()['unit_price_usd'] ?? null;
    $minimumTourPax = $availableTourRates
        ->pluck('min_qty')
        ->filter(fn ($minQty) => filled($minQty))
        ->map(fn ($minQty) => (int) $minQty)
        ->filter(fn (int $minQty) => $minQty > 0)
        ->min();
    $tourDurationDays = max((int) $tour->duration_days, 1);
    $showTourRouteDayTabs = $tourDurationDays > 1;
    $tourMapLocationsByDay = collect($tourMapLocations)->groupBy('day');
    $activeRouteDays = $tourMapLocationsByDay->keys()->sort()->values();
    $firstRouteLocation = collect($tourMapLocations)->first();
    $lastRouteLocation = collect($tourMapLocations)->last();
    $routeLocationTypes = collect($tourMapLocations)->pluck('label')->filter()->unique()->values();
    $tourGalleryLocationTypes = ['Attraction', 'Activity', 'F&B'];
    $tourLocationGalleryImages = collect($tourMapLocations)
        ->filter(fn ($location) => !empty($location['location_image_url']) && in_array($location['type'] ?? 'Attraction', $tourGalleryLocationTypes, true))
        ->unique('location_image_url')
        ->values();
    $tourPackageHighlights = $tour->$langPackageHighlights ?: $tour->package_highlights;
    $tourAdditionalInfo = $tour->$langAdditionalInfo ?: $tour->additional_info;
    $tourCancellationPolicy = $tour->$langCancellationPolicy ?: $tour->cancellation_policy;
    $tourGuestRows = collect(old('guests', []))->values();
    $tourWizardErrorStep = 0;
    if (
        $errors->has('guests') ||
        collect($errors->keys())->contains(fn ($key) => str_starts_with($key, 'guests.'))
    ) {
        $tourWizardErrorStep = 1;
    } elseif ($errors->has('terms_accepted') || $errors->has('note')) {
        $tourWizardErrorStep = 2;
    }
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/tour-detail-entry.css') }}">
    @if ($hasTourRouteMap)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIINfQWTKf0dQYfdh4A8iSrlv6b6R64ORc4=" crossorigin="">
    @endif
@endpush

@push('scripts')
    @if ($hasTourRouteMap)
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjGwZ5i6JSJ9XH2bfOQFh++Swhb0tM=" crossorigin=""></script>
    @endif
    <script src="{{ mix('build/frontend/js/pages/tour-detail.js') }}"></script>
@endpush

@section('content')
    <div class="frontend-page-shell tour-detail-page" data-tour-detail-page>
        <section class="container-fluid frontend-page-topband tour-detail-topband py-5">
            <div class="container py-4">
                <nav aria-label="breadcrumb" class="frontend-breadcrumb-wrap">
                    <ol class="breadcrumb frontend-breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('messages.Home')</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('view.tour-packages-service') }}">@lang('messages.Tour Packages')</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $tourDisplayName }}</li>
                    </ol>
                </nav>

                <div class="frontend-page-intro">
                    <div class="frontend-page-intro__copy">
                        <div class="tour-detail-kicker">{{ $tourDisplayType ?: __('tour-detail.topband_kicker') }}</div>
                        <h1 class="frontend-page-intro__title">@lang('tour-detail.topband_title')</h1>
                        <p class="frontend-page-intro__text">@lang('tour-detail.topband_text')</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="container frontend-content-section">
            @if ($errors->any() || session('danger') || session('success'))
                <section class="frontend-surface-card tour-detail-alerts">
                    @if ($errors->any())
                        <div class="alert alert-danger mb-0">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    @if (session('danger'))
                        <div class="alert alert-danger mb-0">{!! session('danger') !!}</div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success mb-0">{!! session('success') !!}</div>
                    @endif
                </section>
            @endif

            <section class="tour-detail-hero frontend-surface-card">
                <div class="tour-detail-hero__media">
                    <img src="{{ $coverImage }}" alt="{{ $tourDisplayName }}" loading="eager"
                        onerror="this.onerror=null;this.src='{{ asset('images/default.webp') }}';">
                    <div class="tour-detail-hero__badge">{{ $durationLabel }}</div>
                </div>
                <div class="tour-detail-hero__content">
                    <div class="accommodation-section__eyebrow">@lang('tour-detail.overview')</div>
                    <h2 class="tour-detail-section-title">{{ $tourDisplayName }}</h2>
                    <div class="tour-detail-richtext">
                        {!! $tourDisplayDescription !!}
                    </div>
                    <div class="tour-detail-meta-grid">
                        <div class="tour-detail-meta">
                            <span>@lang('messages.Type')</span>
                            <strong>{{ $tourDisplayType ?: '-' }}</strong>
                        </div>
                        <div class="tour-detail-meta">
                            <span>@lang('messages.Duration')</span>
                            <strong>{{ $durationLabel }}</strong>
                        </div>
                        <div class="tour-detail-meta">
                            <span>@lang('tour-detail.active_rates')</span>
                            <strong>{{ $availableTourRates->count() }}</strong>
                        </div>
                        <div class="tour-detail-meta">
                            <span>@lang('tour-detail.route_map')</span>
                            <strong>{{ $hasTourRouteMap ? __('messages.Available') : __('messages.Not available') }}</strong>
                        </div>
                    </div>
                </div>
            </section>

            <div class="tour-detail-layout m-b-18">
                <main class="tour-detail-main">
                    @if ($tourLocationGalleryImages->count() > 0)
                        @php
                            $tourGalleryImages = $tourLocationGalleryImages;
                            $firstTourGalleryImage = $tourGalleryImages->first();
                            $firstTourGalleryMain = $firstTourGalleryImage['location_image_url'];
                        @endphp
                        <section class="tour-detail-section tour-detail-gallery-section frontend-surface-card">
                            <div class="tour-detail-section__header">
                                <div>
                                    <div class="accommodation-section__eyebrow">@lang('messages.Tour Gallery')</div>
                                    <h2 class="tour-detail-section-title">@lang('tour-detail.visual_preview')</h2>
                                </div>
                                <div class="tour-detail-section__range">{{ $tourGalleryImages->count() }} @lang('tour-detail.images')</div>
                            </div>
                            <div class="tour-gallery-showcase" data-tour-gallery-showcase>
                                <button type="button" class="tour-gallery-showcase__main"
                                    data-tour-gallery-main data-bs-toggle="modal" data-bs-target="#tourGalleryLocation{{ $firstTourGalleryImage['order'] }}"
                                    aria-label="@lang('tour-detail.visual_preview') 1">
                                    <img src="{{ $firstTourGalleryMain }}" alt="{{ $firstTourGalleryImage['name'] }}"
                                        loading="eager" fetchpriority="high" decoding="async"
                                        onerror="this.onerror=null;this.src='{{ asset('images/default.webp') }}';"
                                        data-tour-gallery-main-image>
                                    <span class="tour-gallery-showcase__badge">
                                        <span data-tour-gallery-current>01</span>
                                        <small>/ {{ str_pad($tourGalleryImages->count(), 2, '0', STR_PAD_LEFT) }}</small>
                                    </span>
                                    <span class="tour-gallery-showcase__caption" data-tour-gallery-caption>
                                        {{ $firstTourGalleryImage['name'] }}
                                    </span>
                                    <span class="tour-gallery-showcase__view">@lang('messages.View')</span>
                                </button>

                                <div class="tour-gallery-showcase__navigation" aria-label="@lang('tour-detail.gallery')">
                                    <button type="button" class="tour-gallery-showcase__arrow" data-tour-gallery-prev
                                        aria-label="@lang('tour-detail.previous_image')" @disabled($tourGalleryImages->count() <= 1)>
                                        <i class="fa fa-chevron-left" aria-hidden="true"></i>
                                    </button>
                                    <div class="tour-gallery-showcase__thumbs" data-tour-gallery-thumbs>
                                        @foreach ($tourGalleryImages as $galleryIndex => $locationGalleryImage)
                                            @php
                                                $thumb = $locationGalleryImage['location_image_url'];
                                                $mainImage = $locationGalleryImage['location_image_url'];
                                            @endphp
                                            <button type="button" class="tour-gallery-showcase__thumb {{ $galleryIndex === 0 ? 'is-active' : '' }}"
                                                data-tour-gallery-thumb
                                                data-gallery-index="{{ $galleryIndex }}"
                                                data-gallery-main="{{ $mainImage }}"
                                                data-gallery-modal="#tourGalleryLocation{{ $locationGalleryImage['order'] }}"
                                                data-gallery-caption="{{ $locationGalleryImage['name'] }}"
                                                aria-label="@lang('tour-detail.visual_preview') {{ $galleryIndex + 1 }}"
                                                aria-current="{{ $galleryIndex === 0 ? 'true' : 'false' }}">
                                                <img src="{{ $thumb }}" alt="{{ $locationGalleryImage['name'] }}" loading="lazy" decoding="async"
                                                    onerror="this.onerror=null;this.src='{{ asset('images/default.webp') }}';">
                                                <span>{{ str_pad($galleryIndex + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                    <button type="button" class="tour-gallery-showcase__arrow" data-tour-gallery-next
                                        aria-label="@lang('tour-detail.next_image')" @disabled($tourGalleryImages->count() <= 1)>
                                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                    </button>
                                </div>

                                @foreach ($tourGalleryImages as $galleryIndex => $locationGalleryImage)
                                    @php
                                        $fullImage = $locationGalleryImage['location_image_url'];
                                    @endphp
                                    <div class="modal fade tour-gallery-modal" id="tourGalleryLocation{{ $locationGalleryImage['order'] }}" tabindex="-1"
                                        aria-labelledby="tourGalleryLocationLabel{{ $locationGalleryImage['order'] }}" aria-hidden="true"
                                        data-bs-backdrop="true" data-bs-keyboard="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="tourGalleryLocationLabel{{ $locationGalleryImage['order'] }}">
                                                        {{ $locationGalleryImage['name'] }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('messages.Close')"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="{{ $fullImage }}" alt="{{ $locationGalleryImage['name'] }}" loading="lazy">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    

                    @if ($hasTourRouteMap)
                        <section class="tour-route-map frontend-surface-card" aria-labelledby="tourRouteMapTitle">
                            <div class="tour-route-map__header">
                                <div>
                                    <div class="accommodation-section__eyebrow">@lang('tour-map.overview')</div>
                                    <h2 class="tour-detail-section-title" id="tourRouteMapTitle">@lang('tour-map.title')</h2>
                                    <p>@lang('tour-map.subtitle')</p>
                                </div>
                                <div class="tour-route-map__count">
                                    <i class="fa fa-map-marker-alt" aria-hidden="true"></i>
                                    {{ count($tourMapLocations) }} @lang('tour-map.planned_stops')
                                </div>
                            </div>
                            <div id="tourRouteMap" class="tour-route-map__canvas" role="img" aria-label="@lang('tour-map.title')"
                                data-day-label="@lang('tour-map.day')" data-stop-label="@lang('tour-map.stop')"
                                data-time-label="@lang('tour-map.time')" data-active-day="{{ $showTourRouteDayTabs ? 1 : 'all' }}">
                                <script type="application/json" data-tour-route-locations>@json($tourMapLocations)</script>
                            </div>
                            @if ($showTourRouteDayTabs)
                                <div class="tour-route-map__tabs" role="tablist" aria-label="@lang('tour-map.visit_sequence')">
                                    @for ($day = 1; $day <= $tourDurationDays; $day++)
                                        @php $dayLocations = $tourMapLocationsByDay->get($day, collect()); @endphp
                                        <button type="button" class="tour-route-map__tab {{ $day === 1 ? 'is-active' : '' }}"
                                            id="tourRouteDayTab{{ $day }}" role="tab" aria-selected="{{ $day === 1 ? 'true' : 'false' }}"
                                            aria-controls="tourRouteDayPanel{{ $day }}" data-tour-route-day-tab="{{ $day }}">
                                            @lang('tour-map.day') {{ $day }}
                                        </button>
                                    @endfor
                                </div>
                            @endif
                            <div class="tour-route-map__tab-panels">
                                @for ($day = 1; $day <= ($showTourRouteDayTabs ? $tourDurationDays : 1); $day++)
                                    @php $dayLocations = $showTourRouteDayTabs ? $tourMapLocationsByDay->get($day, collect()) : collect($tourMapLocations); @endphp
                                    <div class="tour-route-map__day-panel {{ $day === 1 ? 'is-active' : '' }}"
                                        id="tourRouteDayPanel{{ $day }}" role="{{ $showTourRouteDayTabs ? 'tabpanel' : 'region' }}"
                                        aria-labelledby="tourRouteDayTab{{ $day }}" data-tour-route-day-panel="{{ $showTourRouteDayTabs ? $day : 'all' }}">
                                        <div class="tour-route-map__legend" aria-label="@lang('tour-map.visit_sequence')">
                                            @forelse ($dayLocations as $mapLocation)
                                                <button type="button" class="tour-route-map__stop" data-tour-route-stop="{{ $mapLocation['order'] }}" data-tour-route-stop-day="{{ $mapLocation['day'] }}">
                                                    <span class="tour-route-map__stop-rail">
                                                        <span class="tour-route-map__marker" style="--tour-marker-color: {{ $mapLocation['color'] }};">
                                                            <i class="fa {{ $mapLocation['icon'] }}" aria-hidden="true"></i>
                                                        </span>
                                                    </span>
                                                    <span class="tour-route-map__stop-thumbnail">
                                                        <img src="{{ $mapLocation['image_url'] }}" alt="{{ $mapLocation['name'] }}"
                                                            loading="lazy" decoding="async">
                                                    </span>
                                                    <div class="tour-route-map__stop-body">
                                                        <div class="tour-route-map__stop-heading">
                                                            <span class="tour-route-map__stop-order">{{ str_pad($mapLocation['display_order'] ?? $mapLocation['visit_order'], 2, '0', STR_PAD_LEFT) }}</span>
                                                            <span class="tour-route-map__stop-title">
                                                                {{ $mapLocation['name'] }}@if (!empty($mapLocation['visit_time'])) ({{ $mapLocation['visit_time'] }})@endif
                                                            </span>
                                                            <span class="tour-route-map__stop-type">{{ $mapLocation['label'] }}</span>
                                                        </div>
                                                        <div>
                                                            <p class="tour-route-map__stop-desc">
                                                                {{ !empty($mapLocation['description']) ? $mapLocation['description'] : __('tour-map.stop') . ' ' . $mapLocation['visit_order'] }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </button>
                                            @empty
                                                <div class="tour-route-map__empty-day">
                                                    <i class="fa fa-map-signs" aria-hidden="true"></i>
                                                    <span>@lang('tour-map.no_locations_for_day')</span>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </section>
                    @endif
                    
                    @if (!empty($tourGeneratedItinerary))
                        <section class="tour-detail-section frontend-surface-card">
                            <div class="tour-detail-section__header">
                                <div>
                                    <div class="accommodation-section__eyebrow">@lang('messages.Itinerary')</div>
                                    <h2 class="tour-detail-section-title">@lang('tour-detail.partner_snapshot')</h2>
                                </div>
                            </div>
                            <div class="tour-detail-richtext tour-detail-richtext--compact">
                                {!! $tourGeneratedItinerary !!}
                            </div>
                        </section>
                    @endif

                    @if ($tourPackageHighlights)
                        <section class="tour-detail-section tour-detail-section--highlights frontend-surface-card">
                            <div class="tour-detail-section__header">
                                <div>
                                    <div class="accommodation-section__eyebrow">@lang('tour-detail.package_highlights')</div>
                                    <h2 class="tour-detail-section-title">@lang('tour-detail.package_highlights_title')</h2>
                                </div>
                            </div>
                            <div class="tour-detail-richtext tour-detail-richtext--compact tour-detail-highlights-content">
                                {!! $tourPackageHighlights !!}
                            </div>
                        </section>
                    @endif
                    
                    <div class="tour-detail-info-grid">
                        <section class="tour-detail-section tour-detail-section--split frontend-surface-card">
                            <div class="tour-detail-section__header">
                                <div>
                                    <div class="accommodation-section__eyebrow">@lang('messages.Inclusions')</div>
                                    <h2 class="tour-detail-section-title">@lang('tour-detail.what_is_included')</h2>
                                </div>
                            </div>
                            <div class="tour-detail-richtext tour-detail-richtext--compact">
                                {!! $tour->$langInclude !!}
                            </div>
                        </section>

                        <section class="tour-detail-section tour-detail-section--split frontend-surface-card">
                            <div class="tour-detail-section__header">
                                <div>
                                    <div class="accommodation-section__eyebrow">@lang('messages.Exclusions')</div>
                                    <h2 class="tour-detail-section-title">@lang('tour-detail.what_is_not_included')</h2>
                                </div>
                            </div>
                            <div class="tour-detail-richtext tour-detail-richtext--compact">
                                {!! $tour->$langExclude !!}
                            </div>
                        </section>
                    </div>

                    @if ($tourAdditionalInfo)
                        <section class="tour-detail-section frontend-surface-card">
                            <div class="tour-detail-section__header">
                                <div>
                                    <div class="accommodation-section__eyebrow">@lang('messages.Additional Information')</div>
                                    <h2 class="tour-detail-section-title">@lang('tour-detail.important_notes')</h2>
                                </div>
                            </div>
                            <div class="tour-detail-richtext tour-detail-richtext--compact">
                                {!! $tourAdditionalInfo !!}
                            </div>
                        </section>
                    @endif

                    @if ($tourCancellationPolicy)
                        <section class="tour-detail-section frontend-surface-card">
                            <div class="tour-detail-section__header">
                                <div>
                                    <div class="accommodation-section__eyebrow">@lang('messages.Cancellation Policy')</div>
                                    <h2 class="tour-detail-section-title">@lang('tour-detail.cancellation_policy_title')</h2>
                                </div>
                                <div class="tour-detail-policy-badge">
                                    <i class="fa fa-shield-alt" aria-hidden="true"></i>
                                    @lang('tour-detail.booking_policy')
                                </div>
                            </div>
                            <div class="tour-detail-richtext tour-detail-richtext--compact">
                                {!! $tourCancellationPolicy !!}
                            </div>
                        </section>
                    @endif

                    
                </main>

                <aside class="tour-detail-sidebar">
                    <section class="tour-booking-card frontend-surface-card">
                        <div class="tour-booking-card__header">
                            <div>
                                <div class="accommodation-section__eyebrow">@lang('tour-detail.reservation')</div>
                                <h2 class="tour-booking-card__title">{{ $canViewTourRates ? __('tour-detail.create_order') : $tourRateAccess['title'] }}</h2>
                            </div>
                            @if ($canViewTourRates && $lowestRate)
                                <div class="tour-booking-card__price">
                                    <span>@lang('tour-detail.from')</span>
                                    <strong>USD {{ $lowestRate }}</strong>
                                </div>
                            @endif
                        </div>

                        @if ($canViewTourRates)
                            <div class="tour-rate-list">
                                @forelse ($availableTourRates as $tourPrice)
                                    @php
                                        $tourRateGuestLabel = $tourPrice['min_qty'] . ' - ' . $tourPrice['max_qty'] . ' ' . __('messages.pax');
                                    @endphp
                                    <div class="tour-rate-card">
                                        <div>
                                            <strong>{{ $tourRateGuestLabel }}</strong>
                                            <small>
                                                @lang('tour-detail.rate_validity', [
                                                    'from' => $tourPrice['valid_from'],
                                                    'until' => $tourPrice['valid_until'],
                                                ])
                                            </small>
                                        </div>
                                        <div class="tour-rate-card__price">USD {{ $tourPrice['unit_price_usd'] }}@lang('messages./pax')</div>
                                    </div>
                                @empty
                                    <div class="tour-detail-empty">@lang('tour-detail.no_active_price')</div>
                                @endforelse
                            </div>

                            @if ($pricingAvailability)
                                <details class="tour-pricing-readiness" @if (! $pricingAvailability['ready']) open @endif>
                                    <summary>
                                        <i class="fa fa-clipboard-check" aria-hidden="true"></i>
                                        @lang('tour-detail.pricing_requirements')
                                    </summary>
                                    <p class="tour-pricing-readiness__intro">
                                        @lang('tour-detail.pricing_checked_for', [
                                            'date' => $pricingAvailability['service_date'],
                                        ])
                                    </p>
                                    <ul class="tour-pricing-readiness__list">
                                        @foreach ($pricingAvailability['requirements'] as $requirement)
                                            <li class="{{ $requirement['ready'] ? 'is-ready' : 'is-blocking' }}">
                                                <i class="fa {{ $requirement['ready'] ? 'fa-check-circle' : 'fa-exclamation-circle' }}" aria-hidden="true"></i>
                                                <div>
                                                    <strong>{{ $requirement['label'] }}</strong>
                                                    <span>{{ $requirement['detail'] }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if ($pricingAvailability['tier_labels']->isNotEmpty())
                                        <div class="tour-pricing-readiness__tiers">
                                            @foreach ($pricingAvailability['tier_labels'] as $tierLabel)
                                                <span>{{ $tierLabel }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </details>
                            @endif

                            <button type="button" class="btn btn-primary tour-booking-card__cta" data-bs-toggle="modal" data-bs-target="#tourReservationModal"
                                @disabled($availableTourRates->count() === 0)>
                                <i class="fa fa-shopping-basket" aria-hidden="true"></i>
                                @lang('messages.Order')
                            </button>
                        @else
                            <div class="tour-rate-locked">
                                <div class="tour-rate-locked__icon">
                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                </div>
                                <p>{{ $tourRateAccess['message'] }}</p>
                            </div>
                            <a href="{{ $tourRateAccess['url'] }}" class="btn btn-primary tour-booking-card__cta">
                                <i class="fa fa-sign-in-alt" aria-hidden="true"></i>
                                {{ $tourRateAccess['button'] }}
                            </a>
                        @endif
                    </section>
                </aside>
            </div>

            @if ($neartours->count() > 0)
                <section class="tour-detail-section frontend-surface-card">
                    <div class="tour-detail-section__header">
                        <div>
                            <div class="accommodation-section__eyebrow">@lang('messages.Similar Tour Package')</div>
                            <h2 class="tour-detail-section-title">@lang('tour-detail.more_itineraries')</h2>
                        </div>
                    </div>
                    <div class="tour-similar-grid">
                        @foreach ($neartours as $nearTour)
                            @php
                                $nearImage = $nearTour->cover
                                    ? getThumbnail('/tours/tours-cover/' . $nearTour->cover, 520, 340)
                                    : asset('images/default.webp');
                                $nearDisplayName = trim((string) ($nearTour->$langName ?: $nearTour->name));
                                $nearDisplayType = trim((string) ($nearTour->type?->$langType ?: $nearTour->type?->type));
                                $nearDuration = (int) $nearTour->duration_nights > 0
                                    ? __('tour-detail.duration_days_nights', ['days' => $nearTour->duration_days, 'nights' => $nearTour->duration_nights])
                                    : __('tour-detail.duration_days', ['days' => $nearTour->duration_days]);
                            @endphp
                            <article class="tour-similar-card">
                                <a href="{{ route('view.tour-detail', $nearTour->slug) }}">
                                    <img src="{{ $nearImage }}" alt="{{ $nearDisplayName }}" loading="lazy"
                                        onerror="this.onerror=null;this.src='{{ asset('images/default.webp') }}';">
                                    <div class="tour-similar-card__body">
                                        <span>{{ $nearDisplayType }}</span>
                                        <h3>{{ $nearDisplayName }}</h3>
                                        <strong>{{ $nearDuration }}</strong>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>

    @if ($canViewTourRates)
    <div class="modal fade tour-reservation-modal frontend-order-modal" id="tourReservationModal" tabindex="-1" aria-labelledby="tourReservationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered frontend-order-modal__dialog">
            <div class="modal-content frontend-order-modal__surface">
                <form action="{{ route('func.order-tour-package.create', $tour->id) }}" method="POST" class="frontend-order-modal__form" data-tour-order-form novalidate
                    data-quote-url="{{ route('tour-package.quote', $tour->id) }}"
                    data-initial-guests='@json($tourGuestRows)'
                    data-submission-key="tour-order:{{ $tour->id }}"
                    data-no-rate-label="@lang('tour-detail.no_active_price')"
                    data-price-unavailable-label="@lang('tour-detail.price_temporarily_unavailable')"
                    data-price-unavailable-on-date-label="@lang('tour-detail.price_unavailable_on_date')"
                    data-loading-price-label="@lang('tour-detail.loading_price')"
                    data-price-from-label="@lang('messages.Price')"
                    data-price-pax-suffix="@lang('messages./pax')"
                    data-min-guests="2"
                    data-max-guests="200"
                    data-guest-label="@lang('tour-detail.guest')"
                    data-adult-label="@lang('tour-detail.age_adult')"
                    data-child-label="@lang('tour-detail.age_child')"
                    data-male-label="@lang('tour-detail.sex_male')"
                    data-female-label="@lang('tour-detail.sex_female')"
                    data-edit-label="@lang('messages.Edit')"
                    data-remove-label="@lang('messages.Remove')"
                    data-add-guest-label="@lang('messages.Add')"
                    data-update-guest-label="@lang('messages.Update')"
                    data-cancel-edit-label="@lang('messages.Cancel')"
                    data-guest-table-empty-label="@lang('activities.detail.order.guest_table_empty')"
                    data-guest-progress-label="@lang('tour-detail.guest_progress')"
                    data-guest-summary-label="@lang('tour-detail.guest_summary')"
                    data-guest-count-mismatch-label="@lang('tour-detail.guest_minimum_required')"
                    data-processing-label="@lang('messages.Processing')"
                    data-open-on-load="{{ $errors->any() && old('travel_date') ? 'true' : 'false' }}"
                    data-initial-step="{{ $errors->any() && old('travel_date') ? $tourWizardErrorStep : 0 }}">
                    @csrf
                    @include('partials.form-submission-token')
                    <button type="button" class="btn-close frontend-order-modal__close" data-bs-dismiss="modal" aria-label="@lang('messages.Close')"></button>
                    <input type="hidden" name="tour_price_id" value="{{ old('tour_price_id') }}" data-tour-price-id>
                    @if (is_object($bookingcode) && filled($bookingcode->code ?? null))
                        <input type="hidden" name="booking_code" value="{{ $bookingcode->code }}">
                    @endif
                    @include('partials.form-submit-overlay', [
                        'title' => __('messages.Processing'),
                        'message' => __('tour-detail.processing_order_message'),
                    ])

                    <div class="frontend-order-modal__service">
                        <div class="frontend-order-modal__media">
                            <img src="{{ $coverImage }}" alt="{{ $tourDisplayName }}" loading="lazy"
                                onerror="this.onerror=null;this.src='{{ asset('images/default.webp') }}';">
                        </div>
                        <div class="frontend-order-modal__service-content">
                            <div class="frontend-order-modal__eyebrow">@lang('messages.Create Order')</div>
                            <h2 class="frontend-order-modal__title" id="tourReservationModalLabel">{{ $tourDisplayName }}</h2>
                            <div class="frontend-order-modal__summary">
                                <div class="frontend-order-modal__summary-card">
                                    <span>@lang('messages.Type')</span>
                                    <strong>{{ $tourDisplayType ?: __('messages.Tour Package') }}</strong>
                                </div>
                                <div class="frontend-order-modal__summary-card">
                                    <span>@lang('messages.Duration')</span>
                                    <strong>{{ $durationLabel }}</strong>
                                </div>
                                <div class="frontend-order-modal__summary-card">
                                    <span>@lang('messages.Order Date')</span>
                                    <strong>{{ dateFormat($now) }}</strong>
                                </div>
                            </div>
                            <div class="frontend-order-modal__price-card frontend-order-modal__price-card--split">
                                <div class="frontend-order-modal__price-card-item">
                                    <span data-tour-price-card-label>@lang('messages.Price')</span>
                                    <strong data-tour-price-card-value>{{ $lowestRate ? 'USD '.$lowestRate.__('messages./pax') : '-' }}</strong>
                                </div>
                                <div class="frontend-order-modal__price-card-item">
                                    <span>@lang('tour-detail.minimum_order')</span>
                                    <strong>{{ $minimumTourPax ? $minimumTourPax.' '.__('messages.pax') : '-' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tour-reservation-wizard" data-tour-wizard>
                            <div class="tour-reservation-steps tour-reservation-steps--compact frontend-order-modal__nav" role="tablist" aria-label="@lang('tour-detail.reservation_steps')">
                                <button type="button" class="tour-reservation-step frontend-order-modal__nav-item is-active" data-tour-wizard-nav="0">
                                    <span>01</span>
                                    <div>
                                        <strong>@lang('tour-detail.step_trip')</strong>
                                        <small>@lang('tour-detail.trip_information')</small>
                                    </div>
                                </button>
                                <button type="button" class="tour-reservation-step frontend-order-modal__nav-item" data-tour-wizard-nav="1">
                                    <span>02</span>
                                    <div>
                                        <strong>@lang('tour-detail.step_guests')</strong>
                                        <small>@lang('messages.Guest Details')</small>
                                    </div>
                                </button>
                                <button type="button" class="tour-reservation-step frontend-order-modal__nav-item" data-tour-wizard-nav="2">
                                    <span>03</span>
                                    <div>
                                        <strong>@lang('tour-detail.step_review')</strong>
                                        <small>@lang('tour-detail.review_and_confirm')</small>
                                    </div>
                                </button>
                            </div>

                            <div class="tour-reservation-step-panel frontend-order-modal__panel is-active" data-tour-wizard-step>
                                <div class="frontend-order-modal__heading">
                                    <div>
                                        <div class="frontend-order-modal__heading-eyebrow">@lang('activities.detail.order.step_label', ['number' => 1])</div>
                                        <h3>@lang('tour-detail.trip_information')</h3>
                                    </div>
                                    <p>@lang('tour-detail.trip_information_hint')</p>
                                </div>

                                <div class="tour-reservation-form-area">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="tourTravelDate" class="form-label">@lang('messages.Travel Date') <span>*</span></label>
                                            <input
                                                id="tourTravelDate"
                                                name="travel_date"
                                                class="form-control @error('travel_date') is-invalid @enderror"
                                                type="text"
                                                value="{{ str_replace('T', ' ', $tourOrderForm['prefill']['travel_date']) }}"
                                                required
                                                autocomplete="off"
                                                data-ui-picker="datetime"
                                                data-ui-picker-min="{{ $tourOrderForm['minimum_travel_date'] }}"
                                                data-ui-picker-format="YYYY-MM-DD HH:mm"
                                                data-tour-review-field="travelDate"
                                                data-tour-review-format="datetime"
                                            >
                                            @error('travel_date')
                                                <div class="alert-form">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tourPickup" class="form-label">@lang('messages.Pick up location') <span>*</span></label>
                                            <input id="tourPickup" name="pickup_location" class="form-control @error('pickup_location') is-invalid @enderror" type="text"
                                                value="{{ old('pickup_location') }}" placeholder="@lang('messages.ex'): @lang('messages.Hotel Name') / @lang('messages.Airport')" required data-tour-review-field="pickup">
                                            @error('pickup_location')
                                                <div class="alert-form">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tourDropoff" class="form-label">@lang('messages.Drop off location') <span>*</span></label>
                                            <input id="tourDropoff" name="dropoff_location" class="form-control @error('dropoff_location') is-invalid @enderror" type="text"
                                                value="{{ old('dropoff_location') }}" placeholder="@lang('messages.ex'): @lang('messages.Hotel Name') / @lang('messages.Airport')" required data-tour-review-field="dropoff">
                                            @error('dropoff_location')
                                                <div class="alert-form">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="tour-reservation-wizard__actions frontend-order-modal__actions">
                                    <button type="button" class="btn btn-primary" data-tour-wizard-next data-tour-requires-price disabled>@lang('tour-detail.next_step')</button>
                                </div>
                            </div>

                            <div class="tour-reservation-step-panel frontend-order-modal__panel" data-tour-wizard-step hidden>
                                <div class="frontend-order-modal__heading">
                                    <div>
                                        <div class="frontend-order-modal__heading-eyebrow">@lang('activities.detail.order.step_label', ['number' => 2])</div>
                                        <h3>@lang('messages.Guest Details')</h3>
                                    </div>
                                    <p>@lang('tour-detail.guest_manifest_hint')</p>
                                </div>

                                <div class="tour-guest-manifest">
                                    <small class="tour-guest-manifest__progress" data-tour-guest-progress></small>
                                    <div class="tour-guest-table-wrap">
                                        <table class="tour-guest-table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.No')</th>
                                                    <th>@lang('messages.Name')</th>
                                                    <th>@lang('tour-detail.guest_age_group')</th>
                                                    <th>@lang('messages.Gender')</th>
                                                    <th>@lang('messages.Phone')</th>
                                                    <th>@lang('messages.Action')</th>
                                                </tr>
                                            </thead>
                                            <tbody data-tour-guest-table-body>
                                                <tr data-tour-guest-empty-row>
                                                    <td colspan="6" class="tour-guest-table__empty">@lang('activities.detail.order.guest_table_empty')</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="tour-guest-editor">
                                        <input type="hidden" data-tour-guest-edit-index value="">
                                        <div class="tour-guest-editor__grid">
                                            <div class="tour-guest-field">
                                                <label for="tourGuestName" class="form-label">@lang('tour-detail.guest_full_name') <span>*</span></label>
                                                <input id="tourGuestName" type="text" class="form-control" data-tour-guest-field="name" placeholder="@lang('tour-detail.guest_full_name_placeholder')" autocomplete="off">
                                            </div>
                                            <div class="tour-guest-field">
                                                <label for="tourGuestPhone" class="form-label">@lang('tour-detail.guest_contact_optional')</label>
                                                <input id="tourGuestPhone" type="tel" class="form-control" data-tour-guest-field="phone" placeholder="+62 ..." autocomplete="off">
                                            </div>
                                            <div class="tour-guest-field">
                                                <label for="tourGuestAge" class="form-label">@lang('tour-detail.guest_age_group') <span>*</span></label>
                                                <select id="tourGuestAge" class="form-control" data-tour-guest-field="age">
                                                    <option value="">@lang('messages.Select')</option>
                                                    <option value="Adult">@lang('tour-detail.age_adult')</option>
                                                    <option value="Child">@lang('tour-detail.age_child')</option>
                                                </select>
                                            </div>
                                            <div class="tour-guest-field">
                                                <label for="tourGuestSex" class="form-label">@lang('tour-detail.guest_sex') <span>*</span></label>
                                                <select id="tourGuestSex" class="form-control" data-tour-guest-field="sex">
                                                    <option value="">@lang('tour-detail.select_sex')</option>
                                                    <option value="Male">@lang('tour-detail.sex_male')</option>
                                                    <option value="Female">@lang('tour-detail.sex_female')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="tour-guest-editor__actions">
                                            <button type="button" class="btn btn-primary" data-tour-guest-save>@lang('messages.Add')</button>
                                            <button type="button" class="btn btn-light" data-tour-guest-cancel hidden>@lang('messages.Cancel')</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="tour-guest-error" data-tour-guest-error @if($errors->has('guests') || collect($errors->keys())->contains(fn ($key) => str_starts_with($key, 'guests.'))) @else hidden @endif>
                                    @if ($errors->has('guests'))
                                        {{ $errors->first('guests') }}
                                    @else
                                        {{ collect($errors->getMessages())
                                            ->filter(fn ($messages, $key) => str_starts_with($key, 'guests.'))
                                            ->flatten()
                                            ->first() ?: __('tour-detail.guest_minimum_required') }}
                                    @endif
                                </div>

                                <div data-tour-guest-inputs hidden></div>

                                <div class="tour-reservation-wizard__actions frontend-order-modal__actions">
                                    <button type="button" class="btn btn-light" data-tour-wizard-prev>@lang('tour-detail.previous_step')</button>
                                    <button type="button" class="btn btn-primary" data-tour-wizard-next>@lang('tour-detail.next_step')</button>
                                </div>
                            </div>

                            <div class="tour-reservation-step-panel frontend-order-modal__panel" data-tour-wizard-step hidden>
                                <div class="frontend-order-modal__heading">
                                    <div>
                                        <div class="frontend-order-modal__heading-eyebrow">@lang('activities.detail.order.step_label', ['number' => 3])</div>
                                        <h3>@lang('tour-detail.review_and_confirm')</h3>
                                    </div>
                                    <p>@lang('tour-detail.review_and_confirm_hint')</p>
                                </div>

                                <div class="tour-review-grid">
                                        <div>
                                            <span>@lang('messages.Travel Date')</span>
                                            <strong data-tour-review-value="travelDate">-</strong>
                                        </div>
                                        <div>
                                            <span>@lang('messages.Number of Guests')</span>
                                            <strong data-tour-review-value="guestCount">-</strong>
                                        </div>
                                        <div>
                                            <span>@lang('messages.Pick up location')</span>
                                            <strong data-tour-review-value="pickup">-</strong>
                                        </div>
                                        <div>
                                            <span>@lang('messages.Drop off location')</span>
                                            <strong data-tour-review-value="dropoff">-</strong>
                                        </div>
                                        <div>
                                            <span>@lang('tour-detail.guest_manifest')</span>
                                            <strong data-tour-review-value="guestManifest">-</strong>
                                        </div>
                                </div>

                                <div class="tour-review-guests">
                                    <div class="tour-review-guests__head">
                                        <span>@lang('tour-detail.guest_manifest')</span>
                                    </div>
                                    <div class="tour-review-guests__table-wrap">
                                        <table class="tour-review-guests__table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.No')</th>
                                                    <th>@lang('messages.Name')</th>
                                                    <th>@lang('tour-detail.guest_age_group')</th>
                                                    <th>@lang('messages.Gender')</th>
                                                    <th>@lang('messages.Phone')</th>
                                                </tr>
                                            </thead>
                                            <tbody data-tour-review-guest-table-body>
                                                <tr data-tour-review-guest-empty-row>
                                                    <td colspan="5" class="tour-review-guests__empty-row">@lang('activities.detail.order.guest_table_empty')</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tour-reservation-field mt-3">
                                    <label for="tourNote" class="form-label">@lang('messages.Note')</label>
                                    <textarea id="tourNote" name="note" class="form-control @error('note') is-invalid @enderror" rows="3" placeholder="@lang('messages.Optional')">{{ old('note') }}</textarea>
                                    @error('note')
                                        <div class="alert-form">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="tour-price-preview" data-tour-price-preview>
                                    <div>
                                        <span>@lang('messages.Price')/@lang('messages.pax')</span>
                                        <strong data-tour-price-per-pax>-</strong>
                                    </div>
                                    <div>
                                        <span>@lang('messages.Total Price')</span>
                                        <strong data-tour-total-price>-</strong>
                                    </div>
                                </div>
                                <small class="text-muted" data-tour-price-note>
                                    {{ $priceUnavailable ? __('tour-detail.price_temporarily_unavailable') : '' }}
                                </small>

                                @include('partials.order-confirmation-checkbox', [
                                    'id' => 'tourTermsAccepted',
                                    'class' => 'tour-reservation-consent',
                                ])

                                <div class="tour-reservation-wizard__actions frontend-order-modal__actions">
                                    <button type="button" class="btn btn-light" data-tour-wizard-prev>@lang('tour-detail.previous_step')</button>
                                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">@lang('messages.Cancel')</button>
                                    <button type="submit" class="btn btn-primary" data-tour-wizard-submit data-processing-label="@lang('messages.Processing')">@lang('messages.Order')</button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection
