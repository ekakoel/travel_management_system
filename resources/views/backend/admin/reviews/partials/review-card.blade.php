@php
    $overallRating = $average(
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
        $review->guide_neatness
    );
    $guideName = optional($review->guide)->name ?: $review->guide_name;
    $driverName = optional($review->driver)->name ?: $review->driver_name;
    $grouped = $grouped ?? false;
    $ratingGroups = [
        'General' => [
            'Accommodation' => $review->accommodation,
            'Meals' => $review->meals,
            'Tour Sites' => $review->tour_sites,
        ],
        'Transportation' => [
            'Cleanliness' => $review->transportation_cleanliness,
            'Air Conditioner' => $review->transportation_air_condition,
        ],
        'Driver' => [
            'Punctuality' => $review->driver_punctuality,
            'Driving Skills' => $review->driver_driving_skills,
            'Neatness' => $review->driver_neatness,
        ],
        'Guide' => [
            'Attitude' => $review->attitude,
            'Explanation' => $review->explanation,
            'Knowledge' => $review->knowledge,
            'Time Control' => $review->time_control,
            'Neatness' => $review->guide_neatness,
        ],
        'Travel Mood' => [
            'Travel Mood' => $review->travel_mood,
        ],
    ];
    $allowDelete = $allowDelete ?? $review->status !== 'accepted';
    $showPrint = $showPrint ?? false;
@endphp

<article class="tour-review-card {{ $grouped ? 'is-grouped' : '' }}">
    <div class="tour-review-card__score">
        <span class="tour-reviews-badge is-{{ $tab['tone'] }}">{{ $tab['label'] }}</span>
        <strong>{{ $overallRating ? number_format($overallRating, 1) : '-' }}</strong>
        <small>Average</small>
    </div>

    <div class="tour-review-card__main">
        <header class="tour-review-card__header tour-review-card__header--compact">
            <div>
                <h3>{{ $review->customer_name ?: 'Anonymous Guest' }}</h3>
            </div>
            <small>{{ optional($review->created_at)->format('d M Y H:i') }}</small>
        </header>

        @unless ($grouped)
            <dl class="tour-review-meta">
                <div>
                    <dt>Code</dt>
                    <dd>{{ $review->booking_code ?: '-' }}</dd>
                </div>
                <div>
                    <dt>Agent</dt>
                    <dd>{{ $review->travel_agent ?: '-' }}</dd>
                </div>
                <div>
                    <dt>Arrival</dt>
                    <dd>{{ $review->arrival_date ? dateFormat($review->arrival_date) : '-' }}</dd>
                </div>
                <div>
                    <dt>Departure</dt>
                    <dd>{{ $review->departure_date ? dateFormat($review->departure_date) : '-' }}</dd>
                </div>
            </dl>
        @endunless

        <div class="tour-review-compact-body">
            <p class="tour-review-people">
                <span><strong>Guide</strong> {{ $guideName ?: '-' }}</span>
                <span><strong>Driver</strong> {{ $driverName ?: '-' }}</span>
            </p>

            @if ($review->customer_review)
                <p class="tour-review-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($review->customer_review), 150) }}</p>
            @endif
        </div>

        <div class="tour-review-rating-chips tour-review-rating-groups" aria-label="Rating summary">
            @foreach ($ratingGroups as $groupLabel => $ratings)
                @php
                    $visibleRatings = collect($ratings)->filter(fn ($value) => filled($value));
                @endphp

                @if ($visibleRatings->isNotEmpty())
                    <section>
                        <h4>{{ $groupLabel }}</h4>
                        <div>
                            @foreach ($visibleRatings as $label => $value)
                                <span>
                                    @unless ($groupLabel === 'Travel Mood')
                                        <b>{{ $label }}</b>
                                    @endunless
                                    <strong>{{ is_numeric($value) ? number_format($value, 1) : $value }}</strong>
                                </span>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach
        </div>
    </div>

    <aside class="tour-review-card__actions">
        @if ($review->status !== 'accepted')
            <form method="POST" action="{{ route('admin.reviews.updateStatus', $review) }}" data-tour-review-action>
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="accepted">
                <button type="submit" class="tour-reviews-action is-success" data-confirm="Approve this review?">
                    <i class="fa fa-check"></i>
                    Approve
                </button>
            </form>
        @endif

        @if ($review->status !== 'rejected')
            <form method="POST" action="{{ route('admin.reviews.updateStatus', $review) }}" data-tour-review-action>
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <button type="submit" class="tour-reviews-action is-danger" data-confirm="Reject this review?">
                    <i class="fa fa-times"></i>
                    Reject
                </button>
            </form>
        @endif

        @if ($showPrint && !empty($printTargetId))
            <button type="button" class="tour-reviews-action is-muted" data-review-print-trigger aria-controls="{{ $printTargetId }}">
                <i class="fa fa-print"></i>
                Print
            </button>
        @endif

        @if ($allowDelete)
            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" data-tour-review-action>
                @csrf
                @method('DELETE')
                <button type="submit" class="tour-reviews-action is-ghost-danger" data-confirm="Delete this review permanently?">
                    <i class="fa fa-trash-alt"></i>
                    Delete
                </button>
            </form>
        @endif
    </aside>
</article>
