@extends('frontend.layouts.app')
@section('title', __('messages.Activities'))
@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/activities-index-entry.css') }}">
@endpush
@push('scripts')
    <script src="{{ mix('build/frontend/js/pages/activities-index.js') }}"></script>
@endpush
@section('content')
    @php
        $heroImage =
            $featuredActivity && $featuredActivity->cover
                ? getThumbnail('/activities/activities-cover/' . $featuredActivity->cover, 920, 620)
                : asset('storage/images/default.webp');
        $activitiesByPartner = $activities->getCollection()->groupBy(function ($activity) {
            return $activity->display_partner ?? __('messages.Supplier') . ' -';
        });
    @endphp
    <div class="frontend-page-shell activities-page" data-activities-page>
        <section class="container-fluid frontend-page-topband activities-topband py-5">
            <div class="container py-4">
                <nav aria-label="breadcrumb" class="frontend-breadcrumb-wrap">
                    <ol class="breadcrumb frontend-breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('messages.Home')</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('services') }}">@lang('messages.Services')</a></li>
                        <li class="breadcrumb-item active" aria-current="page">@lang('messages.Activities')</li>
                    </ol>
                </nav>
                <div class="frontend-page-intro">
                    <div class="frontend-page-intro__copy">
                        <h1 class="frontend-page-intro__title">@lang('activities.intro_title')</h1>
                        <p class="frontend-page-intro__text">@lang('activities.intro_text')</p>
                    </div>
                    <div class="frontend-page-summary" data-activities-summary>
                        <div class="frontend-page-summary__item">
                            <span>@lang('activities.summary.activities')</span>
                            <strong>{{ $directoryStats['total_activities'] }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('activities.summary.locations')</span>
                            <strong>{{ $directoryStats['total_locations'] }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('activities.summary.types')</span>
                            <strong>{{ $directoryStats['total_types'] }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('activities.summary.minimum_pax')</span>
                            <strong>{{ $directoryStats['minimum_pax'] }} pax</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="container frontend-content-section" id="partner-access">
            @include('partials.alerts')
            <div class="row g-4">
                <div class="col-xl-4 col-lg-5">
                    <aside class="activities-sidebar frontend-surface-card">
                        <form class="activities-filter-form" action="{{ route('view.activities-service') }}" method="get"
                            data-activities-filter-form>
                            <div class="activities-filter-intro">
                                <div class="accommodation-section__eyebrow">@lang('activities.filters.eyebrow')</div>
                                <h2 class="accommodation-section__title">@lang('activities.filters.title')</h2>
                                <p>@lang('activities.filters.text')</p>
                            </div>
                            <div class="activities-filter-grid">
                                <div class="activities-filter-field">
                                    <label for="searchActivity">@lang('activities.filters.search_label')</label>
                                    <input type="text" id="searchActivity" name="search_name" class="form-control"
                                        value="{{ $searchName }}" placeholder="@lang('activities.filters.search_placeholder')" data-activities-search>
                                </div>
                                <div class="activities-filter-field">
                                    <label for="searchLocation">@lang('activities.filters.location_label')</label>
                                    <select id="searchLocation" name="search_location" class="form-control"
                                        data-activities-location>
                                        <option value="">@lang('activities.filters.all_locations')</option>
                                        @foreach ($locationOptions as $location)
                                            <option value="{{ $location }}" @selected($searchLocation === $location)>
                                                {{ $location }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="activities-filter-field">
                                    <label for="searchType">@lang('activities.filters.type_label')</label>
                                    <select id="searchType" name="search_type" class="form-control" data-activities-type>
                                        <option value="">@lang('activities.filters.all_types')</option>
                                        @foreach ($typeOptions as $type)
                                            <option value="{{ $type }}" @selected($searchType === $type)>
                                                {{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="activities-filter-actions">
                                <a href="{{ route('view.activities-service') }}" class="btn btn-outline-secondary flex-fill"
                                    data-activities-reset>
                                    @lang('activities.filters.reset')
                                </a>
                            </div>
                        </form>
                    </aside>
                </div>

                <div class="col-xl-8 col-lg-7">
                    <section class="activities-results frontend-surface-card" data-activities-results>
                        <div class="activities-results__header">
                            <div>
                                <div class="accommodation-section__eyebrow">@lang('activities.results.eyebrow')</div>
                                <h2 class="accommodation-section__title">@lang('activities.results.title')</h2>
                                <p class="activities-results__subtext">@lang('activities.results.text')</p>
                            </div>
                            <div class="activities-results__count">
                                @lang('activities.results.showing')
                                <strong>{{ $activities->firstItem() ?? 0 }} - {{ $activities->lastItem() ?? 0 }}</strong>
                                <span>@lang('activities.results.of')</span>
                                <strong>{{ $activities->total() }}</strong>
                                <span>@lang('activities.results.activities_suffix')</span>
                            </div>
                        </div>

                        @if ($searchName || $searchLocation || $searchType)
                            <div class="activities-active-filters">
                                @if ($searchName)
                                    <div class="activities-filter-pill">@lang('activities.filters.search_label'): {{ $searchName }}</div>
                                @endif
                                @if ($searchLocation)
                                    <div class="activities-filter-pill">@lang('activities.filters.location_label'): {{ $searchLocation }}</div>
                                @endif
                                @if ($searchType)
                                    <div class="activities-filter-pill">@lang('activities.filters.type_label'): {{ $searchType }}</div>
                                @endif
                            </div>
                        @endif

                        @if ($activities->count() > 0)
                            <div class="activities-groups">
                                <div class="activities-grid">
                                    @foreach ($activitiesByPartner as $partnerName => $partnerActivities)
                                        @foreach ($partnerActivities as $activity)
                                            @php
                                                $activityImage = $activity->cover
                                                    ? getThumbnail(
                                                        '/activities/activities-cover/' . $activity->cover,
                                                        640,
                                                        420,
                                                    )
                                                    : asset('storage/images/default.webp');
                                            @endphp
                                            <article class="activity-directory-card">
                                                <a href="{{ route('view.activity-public-detail', $activity->code) }}"
                                                    class="activity-directory-card__link">
                                                    <div class="activity-directory-card__media">
                                                        <img src="{{ $activityImage }}" alt="{{ $activity->name }}"
                                                            loading="lazy"
                                                            onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';">
                                                        <span class="activity-directory-card__badge">
                                                            <i class="fa fa-map-marker-alt" aria-hidden="true"></i>
                                                            {{ $activity->display_location }}
                                                        </span>
                                                    </div>
                                                    <div class="activity-directory-card__body">
                                                        <div>
                                                            <div class="activity-directory-card__meta">
                                                                <span>{{ $activity->display_type }}</span>
                                                            </div>
                                                            <h3 class="activity-directory-card__title">
                                                                {{ $partnerName }}
                                                            </h3>
                                                            <p class="activity-directory-card__description">
                                                                {{ $activity->name }}
                                                            </p>
                                                        </div>
                                                        <div class="activity-directory-card__facts">
                                                            <div class="activity-directory-card__fact">
                                                                <span>@lang('activities.results.duration')</span>
                                                                <strong>{{ $activity->display_duration }}</strong>
                                                            </div>
                                                            <div class="activity-directory-card__fact">
                                                                <span>@lang('activities.results.minimum_pax')</span>
                                                                <strong>{{ $activity->display_min_pax }} pax</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </article>
                                        @endforeach

                                        {{-- </section> --}}
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div
                            class="activities-empty{{ $activities->count() > 0 || $searchName || $searchLocation || $searchType ? ' d-none' : '' }}">
                            <div class="activities-empty__icon"><i class="fa fa-hiking"></i></div>
                            <h3 class="activities-empty__title">@lang('activities.empty.title')</h3>
                            <p class="activities-empty__text">@lang('activities.empty.text')</p>
                        </div>
                        <div
                            class="activities-empty{{ $activities->count() === 0 && ($searchName || $searchLocation || $searchType) ? '' : ' d-none' }}">
                            <div class="activities-empty__icon"><i class="fa fa-search"></i></div>
                            <h3 class="activities-empty__title">@lang('activities.empty.search_title')</h3>
                            <p class="activities-empty__text">@lang('activities.empty.search_text')</p>
                        </div>

                        @if ($activities->hasPages())
                            <div class="activities-pagination">
                                {{ $activities->onEachSide(1)->links('pagination::bootstrap-5') }}
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
