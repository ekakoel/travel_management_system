@extends('frontend.layouts.app')
@section('title', __('messages.Transports'))
@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/transportations-index-entry.css') }}">
@endpush
@push('scripts')
    <script src="{{ mix('build/frontend/js/pages/transportations-index.js') }}"></script>
@endpush
@section('content')
    <div class="frontend-page-shell transportations-page" data-transportations-page>
        <section class="container-fluid frontend-page-topband transportations-topband py-5">
            <div class="container py-4">
                @include('partials.breadcrumbs', [
                    'breadcrumbs' => [
                        ['label' => __('messages.Home'), 'url' => route('home')],
                        ['label' => __('messages.Transports')],
                    ],
                ])
                <div class="frontend-page-intro">
                    <div class="frontend-page-intro__copy">
                        <div class="frontend-page-intro__eyebrow">@lang('messages.Transport directory')</div>
                        <h1 class="frontend-page-intro__title">@lang('messages.Reliable Bali transport for every itinerary')</h1>
                        <p class="frontend-page-intro__text">
                            @lang('messages.Compare active private cars, shuttle options, and transfer-ready vehicles with consistent service information before continuing to the transport detail page.')
                        </p>
                    </div>
                    <div class="frontend-page-summary" data-transportations-summary>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Active fleets')</span>
                            <strong>{{ $directoryStats['total_transports'] }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Service types')</span>
                            <strong>{{ $directoryStats['total_types'] }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Vehicle brands')</span>
                            <strong>{{ $directoryStats['total_brands'] }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Largest capacity')</span>
                            <strong>{{ $directoryStats['max_capacity'] }} @lang('messages.Seat')</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="container frontend-content-section">
            @include('partials.alerts')
            <div class="row g-4">
                <div class="col-xl-4 col-lg-5">
                    <aside class="transportations-sidebar frontend-surface-card">
                        <form class="transportations-filter-form" action="{{ route('view.transport-service') }}"
                            method="get" data-transportations-filter-form>
                            <div class="transportations-filter-intro">
                                <div class="accommodation-section__eyebrow">@lang('messages.Filter transport')</div>
                                <h2 class="accommodation-section__title">@lang('messages.Find the right vehicle')</h2>
                                <p>@lang('messages.Filter by keyword, service type, brand, or minimum seat capacity. Results update automatically while keeping the URL shareable.')</p>
                            </div>
                            <div class="transportations-filter-grid">
                                <div class="transportations-filter-field">
                                    <label for="searchTransport">@lang('messages.Search by name')</label>
                                    <input type="text" id="searchTransport" name="search_name" class="form-control"
                                        value="{{ $searchName }}" placeholder="@lang('messages.Search transport, brand, or service')"
                                        data-transportations-search>
                                </div>
                                <div class="transportations-filter-field">
                                    <label for="searchType">@lang('messages.Service type')</label>
                                    <select id="searchType" name="search_type" class="form-control" data-transportations-filter>
                                        <option value="">@lang('messages.All Type')</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type }}" @selected($searchType === $type)>
                                                {{ __("messages.$type") === "messages.$type" ? $type : __("messages.$type") }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="transportations-filter-field">
                                    <label for="searchBrand">@lang('messages.Brand')</label>
                                    <select id="searchBrand" name="search_brand" class="form-control" data-transportations-filter>
                                        <option value="">@lang('messages.All brands')</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand }}" @selected($searchBrand === $brand)>{{ $brand }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="transportations-filter-field">
                                    <label for="minimumCapacity">@lang('messages.Minimum capacity')</label>
                                    <input type="number" min="1" step="1" id="minimumCapacity" name="minimum_capacity"
                                        class="form-control" value="{{ $minimumCapacity ?: '' }}"
                                        placeholder="@lang('messages.Seat')" data-transportations-search>
                                </div>
                            </div>
                            <div class="transportations-filter-actions">
                                <a href="{{ route('view.transport-service') }}" class="btn btn-outline-secondary flex-fill"
                                    data-transportations-reset>
                                    @lang('messages.Reset Filter')
                                </a>
                                <noscript>
                                    <button type="submit" class="btn btn-primary flex-fill">@lang('messages.Apply Filter')</button>
                                </noscript>
                            </div>
                        </form>
                    </aside>
                </div>
                <div class="col-xl-8 col-lg-7">
                    <section class="transportations-results frontend-surface-card" data-transportations-results>
                        <div class="transportations-results__header">
                            <div>
                                <div class="accommodation-section__eyebrow">@lang('messages.Available transport')</div>
                                <h2 class="accommodation-section__title">@lang('messages.Transport options')</h2>
                                <p class="transportations-results__subtext">@lang('messages.Browse active vehicles prepared for agent booking and guest travel coordination.')</p>
                            </div>
                            <div class="transportations-results__count">
                                @lang('messages.Showing')
                                <strong>{{ $transports->firstItem() ?? 0 }} - {{ $transports->lastItem() ?? 0 }}</strong>
                                <span>@lang('messages.of')</span>
                                <strong>{{ $transports->total() }}</strong>
                                <span>@lang('messages.Transports')</span>
                            </div>
                        </div>
                        @if ($searchName || $searchType || $searchBrand || $minimumCapacity)
                            <div class="transportations-active-filters">
                                @if ($searchName)
                                    <span class="transportations-filter-pill">@lang('messages.Keyword'): {{ $searchName }}</span>
                                @endif
                                @if ($searchType)
                                    <span class="transportations-filter-pill">@lang('messages.Service type'): {{ __("messages.$searchType") === "messages.$searchType" ? $searchType : __("messages.$searchType") }}</span>
                                @endif
                                @if ($searchBrand)
                                    <span class="transportations-filter-pill">@lang('messages.Brand'): {{ $searchBrand }}</span>
                                @endif
                                @if ($minimumCapacity)
                                    <span class="transportations-filter-pill">@lang('messages.Minimum capacity'): {{ $minimumCapacity }} @lang('messages.Seat')</span>
                                @endif
                            </div>
                        @endif
                        @if ($transports->count() > 0)
                            <div class="transportations-grid">
                                @foreach ($transports as $transport)
                                    @php
                                        $transportImage = $transport->cover
                                            ? getThumbnail('/transports/transports-cover/' . $transport->cover, 720, 480)
                                            : asset('storage/images/default.webp');
                                        $detailUrl = route('transport.show', $transport->id);
                                    @endphp
                                    <article class="transportation-card">
                                        <a class="transportation-card__link" href="{{ $detailUrl }}">
                                            <div class="transportation-card__media">
                                                <img src="{{ $transportImage }}" alt="{{ $transport->name }}" loading="lazy"
                                                    onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';">
                                                <span class="transportation-card__capacity">
                                                    <i class="fa fa-user-friends" aria-hidden="true"></i>
                                                    {{ $transport->capacity ?: '-' }} @lang('messages.Seat')
                                                </span>
                                            </div>
                                            <div class="transportation-card__body">
                                                <div class="transportation-card__meta">
                                                    <span>{{ $transport->type ?: __('messages.Transport') }}</span>
                                                </div>
                                                <h3 class="transportation-card__title">{{ $transport->name }}</h3>
                                                <div class="transportation-card__facts">
                                                    <div>
                                                        <span>@lang('messages.Transport brand')</span>
                                                        <strong>{{ $transport->brand ?: '-' }}</strong>
                                                    </div>
                                                    <div>
                                                        <span>@lang('messages.Capacity')</span>
                                                        <strong>{{ $transport->capacity ?: '-' }} @lang('messages.Seat')</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                        <div class="transportations-empty{{ $transports->count() > 0 || $searchName || $searchType || $searchBrand || $minimumCapacity ? ' d-none' : '' }}">
                            <div class="transportations-empty__icon"><i class="fa fa-car"></i></div>
                            <h3>@lang('messages.No service available at the moment.')</h3>
                            <p>@lang('messages.Active transport services will appear here once they are available for frontend booking.')</p>
                        </div>
                        <div class="transportations-empty{{ $transports->count() === 0 && ($searchName || $searchType || $searchBrand || $minimumCapacity) ? '' : ' d-none' }}">
                            <div class="transportations-empty__icon"><i class="fa fa-search"></i></div>
                            <h3>@lang('messages.No transport matched your filters')</h3>
                            <p>@lang('messages.Try a different keyword, brand, type, or minimum capacity.')</p>
                        </div>
                        @if ($transports->hasPages())
                            <div class="transportations-pagination">
                                {{ $transports->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
