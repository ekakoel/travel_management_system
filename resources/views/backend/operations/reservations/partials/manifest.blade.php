<section class="backend-panel reservation-detail-panel" id="reservation-manifest" data-reservation-section>
    <header class="backend-section-header reservation-detail-panel__header">
        <div><span class="backend-section-header__label">{{ __('reservations.manifest_eyebrow') }}</span><h2>{{ __('reservations.guest_manifest') }}</h2></div>
        <span class="backend-status-badge backend-status-badge--info">{{ trans_choice('reservations.guest_count', $reservationGuests->count(), ['count' => $reservationGuests->count()]) }}</span>
    </header>

    @if ($reservationGuests->isEmpty())
        <div class="backend-empty-state reservation-detail-empty"><i class="fa fa-user-times" aria-hidden="true"></i><strong>{{ __('reservations.no_guests_title') }}</strong><span>{{ __('reservations.no_guests_description') }}</span></div>
    @else
        <div class="backend-table-wrap reservation-detail-table-wrap">
            <table class="backend-table reservation-detail-table">
                <thead><tr><th>#</th><th>{{ __('reservations.guest_name') }}</th><th>{{ __('reservations.category_gender') }}</th><th>{{ __('reservations.phone') }}</th><th>{{ __('reservations.linked_service') }}</th></tr></thead>
                <tbody>
                    @foreach ($reservationGuests as $index => $guest)
                        <tr>
                            <td data-label="#">{{ $index + 1 }}</td>
                            <td data-label="{{ __('reservations.guest_name') }}"><strong>{{ $guest['name'] }}</strong>@if($guest['mandarin_name'])<small>{{ $guest['mandarin_name'] }}</small>@endif</td>
                            <td data-label="{{ __('reservations.category_gender') }}">{{ $guest['category'] }} / {{ $guest['gender'] }}</td>
                            <td data-label="{{ __('reservations.phone') }}">{{ $guest['phone'] }}</td>
                            <td data-label="{{ __('reservations.linked_service') }}">
                                @if ($guest['order_url'])<a href="{{ $guest['order_url'] }}" class="reservation-detail-inline-link">{{ $guest['service'] ?: $guest['order_reference'] }}</a>@else - @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="backend-table-card-list reservation-detail-card-list">
            @foreach ($reservationGuests as $guest)
                <article class="backend-table-card reservation-detail-card">
                    <div class="backend-table-card__header"><div><span>{{ __('reservations.guest_name') }}</span><strong>{{ $guest['name'] }}</strong></div><span class="backend-status-badge backend-status-badge--info">{{ $guest['category'] }}</span></div>
                    <dl class="backend-table-card-grid"><div><dt>{{ __('reservations.gender') }}</dt><dd>{{ $guest['gender'] }}</dd></div><div><dt>{{ __('reservations.phone') }}</dt><dd>{{ $guest['phone'] }}</dd></div><div><dt>{{ __('reservations.linked_service') }}</dt><dd>{{ $guest['service'] ?: '-' }}</dd></div></dl>
                </article>
            @endforeach
        </div>
    @endif
</section>
