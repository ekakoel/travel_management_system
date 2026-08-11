<section class="backend-panel reservation-detail-panel" id="reservation-notes" data-reservation-section>
    <header class="backend-section-header reservation-detail-panel__header">
        <div><span class="backend-section-header__label">{{ __('reservations.notes_eyebrow') }}</span>
            <h2>{{ __('reservations.trip_notes') }}</h2>
        </div>
    </header>

    <div class="reservation-detail-notes-grid">
        <article class="reservation-detail-note-block">
            <div class="reservation-detail-note-block__header"><i class="fas fa-utensils" aria-hidden="true"></i>
                <h3>{{ __('reservations.meal_plan') }}</h3>
            </div>
            @forelse ($reservationMeals as $meal)
                <dl class="reservation-detail-meal">
                    <div>
                        <dt>{{ $meal['date'] }}</dt>
                        <dd></dd>
                    </div>
                    <div>
                        <dt>{{ __('reservations.breakfast') }}</dt>
                        <dd>{{ $meal['breakfast'] }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('reservations.lunch') }}</dt>
                        <dd>{{ $meal['lunch'] }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('reservations.dinner') }}</dt>
                        <dd>{{ $meal['dinner'] }}</dd>
                    </div>
                </dl>
            @empty
                <p class="reservation-detail-muted">{{ __('reservations.no_meal_plan') }}</p>
            @endforelse
        </article>

        @foreach ([['label' => __('reservations.includes'), 'icon' => 'fa fa-check-circle', 'items' => $reservationIncludes, 'tone' => 'success'], ['label' => __('reservations.excludes'), 'icon' => 'fa fa-times-circle', 'items' => $reservationExcludes, 'tone' => 'danger'], ['label' => __('reservations.remarks'), 'icon' => 'fa fa-sticky-note', 'items' => $reservationRemarks, 'tone' => 'info']] as $noteGroup)
            <article class="reservation-detail-note-block reservation-detail-note-block--{{ $noteGroup['tone'] }}">
                <div class="reservation-detail-note-block__header"><i class="{{ $noteGroup['icon'] }}"
                        aria-hidden="true"></i>
                    <h3>{{ $noteGroup['label'] }}</h3>
                </div>
                @if ($noteGroup['items']->isEmpty())
                    <p class="reservation-detail-muted">{{ __('reservations.no_notes_recorded') }}</p>@else<ul>
                        @foreach ($noteGroup['items'] as $item)
                            <li>{!! $item !!}</li>
                        @endforeach
                    </ul>
                @endif
            </article>
        @endforeach
    </div>
</section>
