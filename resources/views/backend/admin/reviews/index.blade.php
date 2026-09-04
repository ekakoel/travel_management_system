@extends('layouts.head')

@section('title', 'Tour Reviews')

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/admin/reviews/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/admin/reviews/index.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        @php
            $average = function (...$values) {
                $ratings = collect($values)->filter(fn ($value) => is_numeric($value));

                return $ratings->isEmpty() ? null : round($ratings->avg(), 1);
            };

            $ratingRows = [
                'General Service' => [
                    'Accommodation' => $serviceStats->accommodation,
                    'Meals' => $serviceStats->meals,
                    'Tour Sites' => $serviceStats->tour_sites,
                ],
                'Transport' => [
                    'Cleanliness' => $transportStats->transportation_cleanliness,
                    'Air Conditioner' => $transportStats->transportation_air_condition,
                ],
                'Guide' => [
                    'Attitude' => $guideStats->attitude,
                    'Explanation' => $guideStats->explanation,
                    'Knowledge' => $guideStats->knowledge,
                    'Time Control' => $guideStats->time_control,
                    'Neatness' => $guideStats->guide_neatness,
                ],
                'Driver' => [
                    'Punctuality' => $driverStats->driver_punctuality,
                    'Driving Skills' => $driverStats->driver_driving_skills,
                    'Neatness' => $driverStats->driver_neatness,
                ],
            ];

            $statusTabs = [
                'pendingReviews' => ['label' => 'Pending', 'count' => $summary['pending'], 'items' => $pendingReviews, 'tone' => 'warning'],
                'approvedReviews' => ['label' => 'Approved', 'count' => $summary['accepted'], 'items' => $approvedReviews, 'tone' => 'success'],
                'rejectedReviews' => ['label' => 'Rejected', 'count' => $summary['rejected'], 'items' => $rejectedReviews, 'tone' => 'danger'],
            ];

            $activeTab = request('tab', $summary['pending'] > 0 ? 'pendingReviews' : ($summary['accepted'] > 0 ? 'approvedReviews' : 'rejectedReviews'));
        @endphp

        <div class="mobile-menu-overlay"></div>
        <main class="main-container tour-reviews-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero class="tour-reviews-hero">
                    <x-slot name="kicker">
                        Review Administration
                    </x-slot>
                    <x-slot name="heading">
                        Tour Reviews
                    </x-slot>
                    <x-slot name="copy">
                        <p>
                            Moderate customer feedback, check service ratings, and prepare approved reviews for printing or sharing.
                        </p>
                    </x-slot>
                    <x-slot name="action">
                        <a href="{{ route('view.generate-review-link') }}" class="backend-page-primary-action">
                            <i class="fa fa-link"></i>
                            Generate Link
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar tour-reviews-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tour Reviews</li>
                        </ol>
                    </nav>
                </section>

                @if ($errors->any() || session('success'))
                    <section class="backend-feedback tour-reviews-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger tour-reviews-alert tour-reviews-alert--danger">
                                <strong>Form needs attention.</strong>
                                @foreach ($errors->all() as $error)
                                    <span>{{ $error }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="backend-alert backend-alert--success tour-reviews-alert tour-reviews-alert--success">
                                <strong>{{ session('success') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Review summary">
                    <article class="backend-kpi-card backend-kpi-card--teal">
                        <div class="backend-kpi-card__icon"><i class="fa fa-comments"></i></div>
                        <div>
                            <span>Total Reviews</span>
                            <strong>{{ number_format($summary['total']) }}</strong>
                            <small>All tour review submissions.</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--amber">
                        <div class="backend-kpi-card__icon"><i class="fa fa-clock-o"></i></div>
                        <div>
                            <span>Pending</span>
                            <strong>{{ number_format($summary['pending']) }}</strong>
                            <small>Need moderation.</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--green">
                        <div class="backend-kpi-card__icon"><i class="fa fa-check-circle"></i></div>
                        <div>
                            <span>Approved</span>
                            <strong>{{ number_format($summary['accepted']) }}</strong>
                            <small>Visible for print output.</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--blue">
                        <div class="backend-kpi-card__icon"><i class="fa fa-ban"></i></div>
                        <div>
                            <span>Rejected</span>
                            <strong>{{ number_format($summary['rejected']) }}</strong>
                            <small>Hidden from print output.</small>
                        </div>
                    </article>
                </section>

                <section class="tour-reviews-layout">
                    <div class="tour-reviews-main">
                        <article class="backend-panel tour-reviews-panel">
                            <header class="backend-section-header tour-reviews-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Moderation</span>
                                    <h2>Review Queue</h2>
                                    <p>Approve, reject, or remove customer reviews without opening a separate detail page.</p>
                                </div>
                            </header>

                            <nav class="tour-reviews-tabs" aria-label="Review status tabs">
                                @foreach ($statusTabs as $tabId => $tab)
                                    <a href="#{{ $tabId }}" class="{{ $activeTab === $tabId ? 'is-active' : '' }}" data-review-tab>
                                        <span>{{ $tab['label'] }}</span>
                                        <strong>{{ number_format($tab['count']) }}</strong>
                                    </a>
                                @endforeach
                            </nav>

                            <div class="tour-reviews-tab-content">
                                @foreach ($statusTabs as $tabId => $tab)
                                    <section id="{{ $tabId }}" class="tour-reviews-tab-pane {{ $activeTab === $tabId ? 'is-active' : '' }}" data-review-tab-pane>
                                        @if ($tabId === 'approvedReviews')
                                            @forelse ($tab['items']->groupBy(fn ($review) => $review->booking_code ?: 'No Booking Code') as $bookingCode => $bookingReviews)
                                                @php
                                                    $firstReview = $bookingReviews->first();
                                                    $groupId = 'approved-review-group-'.\Illuminate\Support\Str::slug($bookingCode).'-'.$loop->index;
                                                    $printId = 'approved-review-print-'.\Illuminate\Support\Str::slug($bookingCode).'-'.$loop->index;
                                                    $groupAverage = $average(...$bookingReviews->flatMap(fn ($review) => [
                                                        $review->accommodation,
                                                        $review->meals,
                                                        $review->tour_sites,
                                                        $review->transportation_cleanliness,
                                                        $review->transportation_air_condition,
                                                        $review->driver_punctuality,
                                                        $review->driver_driving_skills,
                                                        $review->driver_neatness,
                                                        $review->attitude,
                                                        $review->explanation,
                                                        $review->knowledge,
                                                        $review->time_control,
                                                        $review->guide_neatness,
                                                    ])->all());
                                                @endphp

                                                <article class="tour-review-booking-group">
                                                    <header class="tour-review-booking-group__header">
                                                        <div class="tour-review-booking-group__score">
                                                            <span>Average</span>
                                                            <strong>{{ $groupAverage ? number_format($groupAverage, 1) : '-' }}</strong>
                                                        </div>

                                                        <div class="tour-review-booking-group__case">
                                                            <span class="backend-status-badge backend-status-badge--success tour-reviews-badge is-success">Approved Case</span>
                                                            <dl>
                                                                <div>
                                                                    <dt>Booking Code</dt>
                                                                    <dd>{{ $bookingCode }}</dd>
                                                                </div>
                                                                <div>
                                                                    <dt>Agent</dt>
                                                                    <dd>{{ $firstReview->travel_agent ?: '-' }}</dd>
                                                                </div>
                                                                <div>
                                                                    <dt>Arrival</dt>
                                                                    <dd>{{ $firstReview->arrival_date ? dateFormat($firstReview->arrival_date) : '-' }}</dd>
                                                                </div>
                                                                <div>
                                                                    <dt>Departure</dt>
                                                                    <dd>{{ $firstReview->departure_date ? dateFormat($firstReview->departure_date) : '-' }}</dd>
                                                                </div>
                                                                <div>
                                                                    <dt>Reviews</dt>
                                                                    <dd>{{ $bookingReviews->count() }}</dd>
                                                                </div>
                                                            </dl>
                                                        </div>

                                                        <div class="tour-review-booking-group__actions">
                                                            @if ($firstReview->booking_code)
                                                                <button type="button" class="tour-reviews-action is-muted" data-review-print-trigger aria-controls="{{ $printId }}">
                                                                    <i class="fa fa-print"></i>
                                                                    Print
                                                                </button>
                                                            @endif
                                                            <button type="button" class="tour-reviews-action is-success" data-review-group-toggle aria-expanded="false" aria-controls="{{ $groupId }}">
                                                                <i class="fa fa-chevron-down"></i>
                                                                Reviews
                                                            </button>
                                                        </div>
                                                    </header>

                                                    <div id="{{ $groupId }}" class="tour-review-booking-group__list" data-review-group-list hidden>
                                                        @foreach ($bookingReviews as $review)
                                                            @include('backend.admin.reviews.partials.review-card', [
                                                                'review' => $review,
                                                                'tab' => $tab,
                                                                'average' => $average,
                                                                'allowDelete' => false,
                                                                'showPrint' => false,
                                                                'grouped' => true,
                                                            ])
                                                        @endforeach
                                                    </div>

                                                    @include('backend.admin.reviews.partials.print-brief', [
                                                        'bookingCode' => $bookingCode,
                                                        'bookingReviews' => $bookingReviews,
                                                        'groupAverage' => $groupAverage,
                                                        'printId' => $printId,
                                                    ])
                                                </article>
                                            @empty
                                                <div class="tour-reviews-empty">
                                                    <strong>No approved reviews.</strong>
                                                    <span>Approved submissions will be grouped by booking code here.</span>
                                                </div>
                                            @endforelse
                                        @else
                                            @forelse ($tab['items'] as $review)
                                                @include('backend.admin.reviews.partials.review-card', [
                                                    'review' => $review,
                                                    'tab' => $tab,
                                                    'average' => $average,
                                                    'allowDelete' => true,
                                                    'showPrint' => false,
                                                ])
                                            @empty
                                                <div class="tour-reviews-empty">
                                                    <strong>No {{ strtolower($tab['label']) }} reviews.</strong>
                                                    <span>New submissions will appear here after customers submit the tour review form.</span>
                                                </div>
                                            @endforelse
                                        @endif

                                        @if ($tab['items']->hasPages())
                                            <div class="tour-reviews-pagination">
                                                {{ $tab['items']->appends(['tab' => $tabId])->links('pagination::bootstrap-5') }}
                                            </div>
                                        @endif
                                    </section>
                                @endforeach
                            </div>
                        </article>
                    </div>

                    <aside class="tour-reviews-side">
                        <article class="backend-panel tour-reviews-panel">
                            <header class="backend-section-header tour-reviews-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Accepted Ratings</span>
                                    <h2>Service Snapshot</h2>
                                    <p>Average score from approved reviews only.</p>
                                </div>
                            </header>

                            <div class="tour-review-stat-list">
                                @foreach ($ratingRows as $group => $ratings)
                                    <section>
                                        <h3>{{ $group }}</h3>
                                        @foreach ($ratings as $label => $value)
                                            <div>
                                                <span>{{ $label }}</span>
                                                <strong>{{ is_numeric($value) ? number_format($value, 1) : '-' }}</strong>
                                            </div>
                                        @endforeach
                                    </section>
                                @endforeach
                            </div>
                        </article>
                    </aside>
                </section>
            </div>
        </main>
    @endcan
@endsection
