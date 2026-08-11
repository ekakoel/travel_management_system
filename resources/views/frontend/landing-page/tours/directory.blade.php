@extends('frontend.layouts.app')
@section('title', __('messages.Tour Packages'))
@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/tour-packages-index-entry.css') }}">
@endpush
@push('scripts')
    <script src="{{ mix('build/frontend/js/pages/tour-packages-index.js') }}"></script>
@endpush
@section('content')
    @php
        $heroImage = $featuredTour && $featuredTour->cover
            ? getThumbnail('/tours/tours-cover/' . $featuredTour->cover, 920, 620)
            : asset('storage/images/default.webp');
    @endphp

    <div class="frontend-page-shell tour-packages-page" data-tour-packages-page>
        <section class="container-fluid frontend-page-topband tour-packages-topband py-5">
            <div class="container py-4">
                <nav aria-label="breadcrumb" class="frontend-breadcrumb-wrap">
                    <ol class="breadcrumb frontend-breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('messages.Home')</a></li>
                        <li class="breadcrumb-item active" aria-current="page">@lang('messages.Tour Packages')</li>
                    </ol>
                </nav>

                <div class="frontend-page-intro">
                    <div class="frontend-page-intro__copy">
                        <h1 class="frontend-page-intro__title">@lang('tour-packages.hero.title')</h1>
                        <p class="frontend-page-intro__text">@lang('tour-packages.hero.text')</p>
                    </div>
                    <div class="frontend-page-summary" data-tour-packages-summary>
                        <div class="frontend-page-summary__item">
                            <span>@lang('tour-packages.summary.tours')</span>
                            <strong>{{ $directoryStats['total_tours'] }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('tour-packages.summary.areas')</span>
                            <strong>{{ $directoryStats['total_areas'] }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('tour-packages.summary.styles')</span>
                            <strong>{{ $directoryStats['total_types'] }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('tour-packages.summary.active_rates')</span>
                            <strong>{{ $directoryStats['active_rates'] }}</strong>
                        </div>
                    </div>
                </div>

                {{-- <article class="tour-packages-hero-card">
                    <div class="tour-packages-hero-card__media">
                        <img src="{{ $heroImage }}" alt="@lang('tour-packages.hero.image_alt')" loading="eager">
                    </div>
                    <div class="tour-packages-hero-card__body">
                        <span>@lang('tour-packages.hero.card_eyebrow')</span>
                        <h2>@lang('tour-packages.hero.card_title')</h2>
                        <p>@lang('tour-packages.hero.card_text')</p>
                        <div class="tour-packages-hero-facts">
                            <div class="tour-packages-hero-facts__item">
                                <span>@lang('tour-packages.hero.top_area')</span>
                                <strong>{{ $directoryStats['top_area_name'] ?? __('tour-packages.fallback.area') }}</strong>
                            </div>
                            <div class="tour-packages-hero-facts__item">
                                <span>@lang('tour-packages.hero.visible_now')</span>
                                <strong>{{ $tours->count() }} @lang('tour-packages.hero.visible_suffix')</strong>
                            </div>
                        </div>
                    </div>
                </article> --}}
            </div>
        </section>

        <div class="container frontend-content-section">
            @include('partials.alerts')

            <div class="row g-4">
                <div class="col-xl-4 col-lg-5">
                    <aside class="tour-packages-sidebar frontend-surface-card">
                        <form class="tour-packages-filter-form" action="{{ route('view.tour-packages-service') }}"
                            method="get" data-tour-packages-filter-form>
                            <div class="tour-packages-filter-intro">
                                <div class="accommodation-section__eyebrow">@lang('tour-packages.filters.eyebrow')</div>
                                <h2 class="accommodation-section__title">@lang('tour-packages.filters.title')</h2>
                                <p>@lang('tour-packages.filters.text')</p>
                            </div>

                            <div class="tour-packages-filter-grid">
                                <div class="tour-packages-filter-field">
                                    <label for="searchTour">@lang('tour-packages.filters.search_label')</label>
                                    <input type="text" id="searchTour" name="search_name" class="form-control"
                                        value="{{ $searchName }}" placeholder="@lang('tour-packages.filters.search_placeholder')"
                                        data-tour-packages-search>
                                </div>
                                <div class="tour-packages-filter-field">
                                    <label for="searchArea">@lang('tour-packages.filters.area_label')</label>
                                    <select id="searchArea" name="search_area" class="form-control" data-tour-packages-filter>
                                        <option value="">@lang('tour-packages.filters.all_areas')</option>
                                        @foreach ($areaOptions as $area)
                                            <option value="{{ $area }}" @selected($searchArea === $area)>{{ $area }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="tour-packages-filter-field">
                                    <label for="searchType">@lang('tour-packages.filters.type_label')</label>
                                    <select id="searchType" name="search_type" class="form-control" data-tour-packages-filter>
                                        <option value="">@lang('tour-packages.filters.all_types')</option>
                                        @foreach ($typeOptions as $type)
                                            @php
                                                $typeValue = is_object($type) ? $type->id : $type;
                                                $typeLabel = is_object($type)
                                                    ? (app()->getLocale() === 'zh'
                                                        ? ($type->type_traditional ?: $type->type)
                                                        : (app()->getLocale() === 'zh-CN'
                                                            ? ($type->type_simplified ?: $type->type)
                                                            : $type->type))
                                                    : $type;
                                            @endphp
                                            <option value="{{ $typeValue }}" @selected((string) $searchType === (string) $typeValue)>{{ $typeLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="tour-packages-filter-actions">
                                <a href="{{ route('view.tour-packages-service') }}" class="btn btn-outline-secondary flex-fill" data-tour-packages-reset>
                                    @lang('tour-packages.filters.reset')
                                </a>
                            </div>
                        </form>
                    </aside>
                </div>

                <div class="col-xl-8 col-lg-7">
                    <section class="tour-packages-results frontend-surface-card" data-tour-packages-results>
                        <div class="tour-packages-results__header">
                            <div>
                                <div class="accommodation-section__eyebrow">@lang('tour-packages.results.eyebrow')</div>
                                <h2 class="accommodation-section__title">@lang('tour-packages.results.title')</h2>
                                <p class="tour-packages-results__subtext">@lang('tour-packages.results.text')</p>
                            </div>
                            <div class="tour-packages-results__count">
                                @lang('tour-packages.results.showing')
                                <strong>{{ $tours->firstItem() ?? 0 }} - {{ $tours->lastItem() ?? 0 }}</strong>
                                <span>@lang('tour-packages.results.of')</span>
                                <strong>{{ $tours->total() }}</strong>
                                <span>@lang('tour-packages.results.tours_suffix')</span>
                            </div>
                        </div>

                        @if ($searchName || $searchArea || $searchType)
                            <div class="tour-packages-active-filters">
                                @if ($searchName)
                                    <div class="tour-packages-filter-pill">@lang('tour-packages.filters.search_label'): {{ $searchName }}</div>
                                @endif
                                @if ($searchArea)
                                    <div class="tour-packages-filter-pill">@lang('tour-packages.filters.area_label'): {{ $searchArea }}</div>
                                @endif
                                @if ($searchType)
                                    <div class="tour-packages-filter-pill">@lang('tour-packages.filters.type_label')</div>
                                @endif
                            </div>
                        @endif

                        @if ($tours->count() > 0)
                            <div class="tour-packages-grid">
                                @foreach ($tours as $tour)
                                    @php
                                        $tourImage = $tour->cover
                                            ? getThumbnail('/tours/tours-cover/' . $tour->cover, 640, 420)
                                            : asset('storage/images/default.webp');
                                        $duration = (int) $tour->duration_nights > 0
                                            ? __('tour-detail.duration_days_nights', ['days' => $tour->duration_days, 'nights' => $tour->duration_nights])
                                            : __('tour-detail.duration_days', ['days' => $tour->duration_days]);
                                    @endphp
                                    <article class="tour-package-card">
                                        <a class="tour-package-card__link" href="{{ route('view.tour-detail', $tour->slug) }}">
                                            <div class="tour-package-card__media">
                                                <img src="{{ $tourImage }}" alt="{{ $tour->display_name }}" loading="lazy"
                                                    onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';">
                                                {{-- <span class="tour-package-card__badge"><i class="fa fa-map-marker-alt" aria-hidden="true"></i>{{ $tour->display_area }}</span> --}}
                                                <span class="tour-package-card__duration"><i class="fa fa-clock" aria-hidden="true"></i>{{ $duration }}</span>
                                            </div>
                                            <div class="tour-package-card__body">
                                                <div>
                                                    <span class="tour-package-card__type">{{ $tour->display_type }}</span>
                                                    <h3 class="tour-package-card__title">{{ $tour->display_name }}</h3>
                                                    <p class="tour-package-card__price">{{ $tour->display_starting_price }}</p>
                                                </div>
                                                <div class="tour-package-card__facts">
                                                    <div class="tour-package-card__fact">
                                                        <span>@lang('tour-packages.results.destination_highlights')</span>
                                                        <strong>{{ $tour->tour_destination_highlights_count ?? 0 }}</strong>
                                                    </div>
                                                    <div class="tour-package-card__fact">
                                                        <span>@lang('tour-packages.results.food_stops')</span>
                                                        <strong>{{ $tour->tour_food_stops_count ?? 0 }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        @endif

                        <div class="tour-packages-empty{{ $tours->count() > 0 || $searchName || $searchArea || $searchType ? ' d-none' : '' }}">
                            <div class="tour-packages-empty__icon"><i class="fa fa-map-marked-alt"></i></div>
                            <h3 class="tour-packages-empty__title">@lang('tour-packages.empty.title')</h3>
                            <p class="tour-packages-empty__text">@lang('tour-packages.empty.text')</p>
                        </div>
                        <div class="tour-packages-empty{{ $tours->count() === 0 && ($searchName || $searchArea || $searchType) ? '' : ' d-none' }}">
                            <div class="tour-packages-empty__icon"><i class="fa fa-search"></i></div>
                            <h3 class="tour-packages-empty__title">@lang('tour-packages.empty.search_title')</h3>
                            <p class="tour-packages-empty__text">@lang('tour-packages.empty.search_text')</p>
                        </div>

                        @if ($tours->hasPages())
                            <div class="tour-packages-pagination">
                                {{ $tours->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
