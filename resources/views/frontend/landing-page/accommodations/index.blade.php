@extends('frontend.layouts.app')
@section('title', __('messages.Accommodations'))
@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/accommodations-index-entry.css') }}">
@endpush
@push('scripts')
    <script src="{{ mix('build/frontend/js/pages/accommodations-index.js') }}"></script>
@endpush
@section('content')
    @php
        $heroImage =
            $featuredHotel && $featuredHotel->cover
                ? getThumbnail('/hotels/hotels-cover/' . $featuredHotel->cover, 920, 620)
                : asset('storage/images/default.webp');
        $currentCoverage =
            $searchRegion ?: $directoryStats['top_region_name'] ?? __('accommodations.fallback.top_region');
    @endphp
    <div class="frontend-page-shell accommodations-page" data-accommodations-page>
        <section class="container-fluid frontend-page-topband accommodations-topband py-5">
            <div class="container py-4">
                <nav aria-label="breadcrumb" class="frontend-breadcrumb-wrap">
                    <ol class="breadcrumb frontend-breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('messages.Home')</a></li>
                        <li class="breadcrumb-item active" aria-current="page">@lang('messages.Accommodation')</li>
                    </ol>
                </nav>
                <div class="frontend-page-intro">
                    <div class="frontend-page-intro__copy">
                        <h1 class="frontend-page-intro__title">@lang('accommodations.intro_title')</h1>
                        <p class="frontend-page-intro__text">@lang('accommodations.intro_text')</p>
                    </div>
                    <div class="frontend-page-summary" data-accommodations-summary>
                        <div class="frontend-page-summary__item">
                            <span>@lang('accommodations.summary.properties')</span>
                            <strong>{{ $directoryStats['total_hotels'] }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('accommodations.summary.regions')</span>
                            <strong>{{ $directoryStats['total_regions'] }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('accommodations.summary.popular_region')</span>
                            <strong>{{ $directoryStats['top_region_name'] ?? __('accommodations.fallback.top_region') }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('accommodations.summary.minimum_stay')</span>
                            <strong>
                                {{ $directoryStats['minimum_stay_nights'] }}
                                {{ $directoryStats['minimum_stay_nights'] > 1 ? __('messages.Nights') : __('messages.Night') }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="container frontend-content-section">
            @include('partials.alerts')
            <div class="row g-4">
                <div class="col-xl-4 col-lg-5">
                    <aside class="accommodations-sidebar frontend-surface-card">
                        <form class="accommodations-filter-form" action="{{ route('view.hotels-service') }}"
                            method="get" data-accommodations-filter-form>
                            <div class="accommodations-filter-intro">
                                <div class="accommodation-section__eyebrow">@lang('accommodations.filters.eyebrow')</div>
                                <h2 class="accommodation-section__title">@lang('accommodations.filters.title')</h2>
                                <p>@lang('accommodations.filters.text')</p>
                            </div>
                            <div class="accommodations-filter-grid">
                                <div class="accommodations-filter-field">
                                    <label for="searchHotel">@lang('accommodations.filters.search_label')</label>
                                    <input type="text" id="searchHotel" name="search_name" class="form-control"
                                        value="{{ $searchName }}" placeholder="@lang('accommodations.filters.search_placeholder')"
                                        data-accommodations-search>
                                </div>
                                <div class="accommodations-filter-field">
                                    <label for="searchRegion">@lang('accommodations.filters.region_label')</label>
                                    <select id="searchRegion" name="search_region" class="form-control"
                                        data-accommodations-region>
                                        <option value="">@lang('accommodations.filters.all_regions')</option>
                                        @foreach ($regions as $region)
                                            <option value="{{ $region }}" @selected($searchRegion === $region)>
                                                {{ $region }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <label class="accommodations-promo-filter" for="promoAvailable">
                                <input type="checkbox" id="promoAvailable" name="promo_available" value="1"
                                    @checked($promoAvailable)>
                                <span class="accommodations-promo-filter__control" aria-hidden="true">
                                    <i class="fa fa-bolt"></i>
                                </span>
                                <span>
                                    <strong>@lang('accommodations.filters.promo_available_label')</strong>
                                    <small>@lang('accommodations.filters.promo_available_help')</small>
                                </span>
                            </label>
                            <div class="accommodations-filter-actions">
                                <a href="{{ route('view.hotels-service') }}"
                                    class="btn btn-outline-secondary flex-fill" data-accommodations-reset>
                                    @lang('accommodations.filters.reset')
                                </a>
                            </div>
                        </form>
                    </aside>
                </div>
                <div class="col-xl-8 col-lg-7">
                    <section class="accommodations-results frontend-surface-card" data-accommodations-results>
                        <div class="accommodations-results__header">
                            <div>
                                <div class="accommodation-section__eyebrow">@lang('accommodations.results.eyebrow')</div>
                                <h2 class="accommodation-section__title">@lang('accommodations.results.title')</h2>
                                <p class="accommodations-results__subtext">@lang('accommodations.results.text')</p>
                            </div>
                            <div class="accommodations-results__count">
                                @lang('accommodations.results.showing')
                                <strong>{{ $hotels->firstItem() ?? 0 }} - {{ $hotels->lastItem() ?? 0 }}</strong>
                                <span>@lang('accommodations.results.of')</span>
                                <strong>{{ $hotels->total() }}</strong>
                                <span>@lang('accommodations.results.hotels_suffix')</span>
                            </div>
                        </div>
                        @if ($searchName || $searchRegion || $promoAvailable)
                            <div class="accommodations-active-filters">
                                @if ($searchName)
                                    <div class="accommodations-filter-pill">
                                        @lang('accommodations.filters.search_label'): {{ $searchName }}
                                    </div>
                                @endif
                                @if ($searchRegion)
                                    <div class="accommodations-filter-pill">
                                        @lang('accommodations.filters.region_label'): {{ $searchRegion }}
                                    </div>
                                @endif
                                @if ($promoAvailable)
                                    <div class="accommodations-filter-pill">
                                        @lang('accommodations.filters.promo_available_label')
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if ($hotels->count() > 0)
                            <div class="accommodations-grid">
                                @foreach ($hotels as $hotel)
                                    @php
                                        $hotelRegion = $hotel->region ?: __('accommodations.fallback.region');
                                        $airportDuration = $hotel->airport_duration
                                            ? $hotel->airport_duration . ' ' . __('accommodations.results.hours')
                                            : __('accommodations.fallback.unknown_duration');
                                        $hotelImage = $hotel->cover
                                            ? getThumbnail('/hotels/hotels-cover/' . $hotel->cover, 640, 420)
                                            : asset('storage/images/default.webp');
                                    @endphp
                                    <article class="accommodation-directory-card">
                                        <a class="accommodation-directory-card__link"
                                            href="{{ route('view.hotel-detail', $hotel->code) }}">
                                            <div class="accommodation-directory-card__media">
                                                <img src="{{ $hotelImage }}" alt="{{ $hotel->name }}" loading="lazy"
                                                    onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';">
                                                <span class="accommodation-directory-card__badge">
                                                    <i class="fa fa-map-marker-alt" aria-hidden="true"></i>
                                                    {{ $hotelRegion }}
                                                </span>
                                            </div>
                                            <div class="accommodation-directory-card__body">
                                                <div>
                                                    <h3 class="accommodation-directory-card__title">{{ $hotel->name }}
                                                    </h3>
                                                    @if ($hotel->active_promos_count > 0 || $hotel->active_packages_count > 0)
                                                        <div class="accommodation-directory-card__inline-offers">
                                                            @if ($hotel->active_promos_count > 0)
                                                                <span
                                                                    class="accommodation-directory-card__inline-offer accommodation-directory-card__inline-offer--promo">
                                                                    <i class="fa fa-bolt" aria-hidden="true"></i>
                                                                    {{ $hotel->active_promos_count }} @lang('accommodations.results.promos_short')
                                                                </span>
                                                            @endif
                                                            @if ($hotel->active_packages_count > 0)
                                                                <span
                                                                    class="accommodation-directory-card__inline-offer accommodation-directory-card__inline-offer--package">
                                                                    <i class="fa fa-gift" aria-hidden="true"></i>
                                                                    {{ $hotel->active_packages_count }} @lang('accommodations.results.packages_short')
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="accommodation-directory-card__facts">
                                                    <div class="accommodation-directory-card__fact">
                                                        <span>@lang('accommodations.results.minimum_stay')</span>
                                                        <strong>
                                                            {{ $hotel->min_stay ?: $directoryStats['minimum_stay_nights'] }}
                                                            {{ ($hotel->min_stay ?: $directoryStats['minimum_stay_nights']) > 1 ? __('messages.Nights') : __('messages.Night') }}
                                                        </strong>
                                                    </div>
                                                    <div class="accommodation-directory-card__fact">
                                                        <span>@lang('accommodations.results.airport_transfer')</span>
                                                        <strong>{{ $airportDuration }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                        <div
                            class="accommodations-empty{{ $hotels->count() > 0 || $searchName || $searchRegion || $promoAvailable ? ' d-none' : '' }}">
                            <div class="accommodations-empty__icon"><i class="fa fa-hotel"></i></div>
                            <h3 class="accommodations-empty__title">@lang('accommodations.empty.title')</h3>
                            <p class="accommodations-empty__text">@lang('accommodations.empty.text')</p>
                        </div>
                        <div
                            class="accommodations-empty{{ $hotels->count() === 0 && ($searchName || $searchRegion || $promoAvailable) ? '' : ' d-none' }}">
                            <div class="accommodations-empty__icon"><i class="fa fa-search"></i></div>
                            <h3 class="accommodations-empty__title">@lang('accommodations.empty.search_title')</h3>
                            <p class="accommodations-empty__text">@lang('accommodations.empty.search_text')</p>
                        </div>
                        @if ($hotels->hasPages())
                            <div class="accommodations-pagination">
                                {{ $hotels->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
        <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i
                class="bi bi-arrow-up"></i></a>
    </div>
@endsection
