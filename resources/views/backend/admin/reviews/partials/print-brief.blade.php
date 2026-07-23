@php
    $firstReview = $bookingReviews->first();
    $reviewCount = $bookingReviews->count();
    $guideNames = $bookingReviews->map(fn ($review) => optional($review->guide)->name ?: $review->guide_name)->filter()->unique()->values();
    $driverNames = $bookingReviews->map(fn ($review) => optional($review->driver)->name ?: $review->driver_name)->filter()->unique()->values();
    $ratingGroups = [
        'General' => [
            'Accommodation' => $bookingReviews->avg('accommodation'),
            'Meals' => $bookingReviews->avg('meals'),
            'Tour Sites' => $bookingReviews->avg('tour_sites'),
        ],
        'Transportation' => [
            'Cleanliness' => $bookingReviews->avg('transportation_cleanliness'),
            'Air Conditioner' => $bookingReviews->avg('transportation_air_condition'),
        ],
        'Driver' => [
            'Punctuality' => $bookingReviews->avg('driver_punctuality'),
            'Driving Skills' => $bookingReviews->avg('driver_driving_skills'),
            'Neatness' => $bookingReviews->avg('driver_neatness'),
        ],
        'Guide' => [
            'Attitude' => $bookingReviews->avg('attitude'),
            'Explanation' => $bookingReviews->avg('explanation'),
            'Knowledge' => $bookingReviews->avg('knowledge'),
            'Time Control' => $bookingReviews->avg('time_control'),
            'Neatness' => $bookingReviews->avg('guide_neatness'),
        ],
    ];
    $moodValues = [
        'Very Satisfied' => 4,
        'Satisfied' => 3,
        'Neutral' => 2,
        'Normal' => 2,
        'Need Improvement' => 1,
    ];
    $moodScores = $bookingReviews->pluck('travel_mood')
        ->filter()
        ->map(fn ($mood) => $moodValues[$mood] ?? null)
        ->filter();
    $moodAverage = $moodScores->isNotEmpty() ? round($moodScores->avg(), 1) : null;
    $moodSummary = $bookingReviews->pluck('travel_mood')->filter()->countBy()->sortDesc();
    $guestNotes = $bookingReviews->filter(fn ($review) => filled($review->customer_review))->values();
@endphp

<section id="{{ $printId }}" class="tour-review-print-sheet" data-review-print-sheet aria-label="Printable tour review brief">
    <header class="tour-review-print-header">
        <div>
            <span>Tour Review Brief</span>
            <h1>{{ $bookingCode ?: 'No Booking Code' }}</h1>
            <p>Guest feedback summary for the agent and tour operation team.</p>
        </div>
        <div>
            <span>Overall Average</span>
            <strong>{{ $groupAverage ? number_format($groupAverage, 1) : '-' }}</strong>
        </div>
    </header>

    <dl class="tour-review-print-meta">
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
            <dd>{{ $reviewCount }} {{ $reviewCount > 1 ? 'reviews' : 'review' }}</dd>
        </div>
        <div>
            <dt>Printed</dt>
            <dd>{{ now()->format('d M Y') }}</dd>
        </div>
    </dl>

    <section class="tour-review-print-section">
        <h2>Average Ratings</h2>
        <div class="tour-review-print-ratings">
            @foreach ($ratingGroups as $groupLabel => $ratings)
                @php
                    $visibleRatings = collect($ratings)->filter(fn ($value) => is_numeric($value) && $value > 0);
                @endphp

                @if ($visibleRatings->isNotEmpty())
                    <div>
                        <strong>{{ $groupLabel }}</strong>
                        <p>
                            @foreach ($visibleRatings as $label => $value)
                                <span>{{ $label }} {{ number_format($value, 1) }}</span>
                            @endforeach
                        </p>
                    </div>
                @endif
            @endforeach

            @if ($moodAverage)
                <div>
                    <strong>Travel Mood</strong>
                    <p>
                        @foreach ($moodSummary as $mood => $count)
                            <span>{{ $mood }} {{ $count }}</span>
                        @endforeach
                        <span>Average {{ number_format($moodAverage, 1) }}</span>
                    </p>
                </div>
            @endif
        </div>
    </section>

    <section class="tour-review-print-section tour-review-print-team">
        <h2>Assigned Team</h2>
        <dl>
            <div>
                <dt>Guide</dt>
                <dd>{{ $guideNames->isNotEmpty() ? $guideNames->implode(', ') : '-' }}</dd>
            </div>
            <div>
                <dt>Driver</dt>
                <dd>{{ $driverNames->isNotEmpty() ? $driverNames->implode(', ') : '-' }}</dd>
            </div>
        </dl>
    </section>

    @if ($guestNotes->isNotEmpty())
        <section class="tour-review-print-section">
            <h2>Guest Notes</h2>
            <div class="tour-review-print-notes">
                @foreach ($guestNotes as $review)
                    <article>
                        <strong>{{ $review->customer_name ?: 'Anonymous Guest' }}{{ $review->travel_mood ? ' - '.$review->travel_mood : '' }}</strong>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($review->customer_review), 220) }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</section>
