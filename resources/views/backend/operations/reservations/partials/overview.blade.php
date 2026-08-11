<section class="backend-panel reservation-detail-panel" id="reservation-overview" data-reservation-section>
    <header class="backend-section-header reservation-detail-panel__header">
        <div>
            <span class="backend-section-header__label">{{ __('reservations.overview_eyebrow') }}</span>
            <h2>{{ __('reservations.reservation_overview') }}</h2>
        </div>
        <span class="backend-status-badge backend-status-badge--success">{{ $reservationOverview['status'] }}</span>
    </header>

    <div class="reservation-detail-overview-grid">
        <article class="reservation-detail-info-block">
            <div class="reservation-detail-info-block__heading"><i class="fa fa-calendar" aria-hidden="true"></i><h3>{{ __('reservations.booking_information') }}</h3></div>
            <dl class="reservation-detail-definition-list">
                <div><dt>{{ __('reservations.reference') }}</dt><dd>{{ $reservationOverview['reference'] }}</dd></div>
                <div><dt>{{ __('reservations.service') }}</dt><dd>{{ $reservationOverview['service'] }}</dd></div>
                <div><dt>{{ __('reservations.reservation_date') }}</dt><dd>{{ $reservationOverview['created_at'] ?: '-' }}</dd></div>
                <div><dt>{{ __('reservations.service_period') }}</dt><dd>{{ $reservationOverview['checkin'] }} — {{ $reservationOverview['checkout'] }}<small>{{ $reservationOverview['duration'] }}</small></dd></div>
                <div><dt>{{ __('reservations.pickup_contact') }}</dt><dd>{{ $reservationOverview['pickup_name'] }}<small>{{ $reservationOverview['pickup_phone'] }}</small></dd></div>
            </dl>
        </article>

        <article class="reservation-detail-info-block">
            <div class="reservation-detail-info-block__heading"><i class="fa fa-plane" aria-hidden="true"></i><h3>{{ __('reservations.flight_information') }}</h3></div>
            <dl class="reservation-detail-definition-list">
                <div><dt>{{ __('reservations.arrival_flight') }}</dt><dd>{{ $reservationOverview['arrival_flight'] }}<small>{{ $reservationOverview['arrival_time'] }}</small></dd></div>
                <div><dt>{{ __('reservations.departure_flight') }}</dt><dd>{{ $reservationOverview['departure_flight'] }}<small>{{ $reservationOverview['departure_time'] }}</small></dd></div>
            </dl>
        </article>

        <article class="reservation-detail-info-block">
            <div class="reservation-detail-info-block__heading"><i class="fa fa-building" aria-hidden="true"></i><h3>{{ __('reservations.agent_information') }}</h3></div>
            <dl class="reservation-detail-definition-list">
                <div><dt>{{ __('reservations.agent') }}</dt><dd>{{ $reservationOverview['agent_name'] }}<small>{{ $reservationOverview['agent_office'] }}</small></dd></div>
                <div><dt>{{ __('reservations.phone') }}</dt><dd>{{ $reservationOverview['agent_phone'] }}</dd></div>
                <div><dt>{{ __('reservations.email') }}</dt><dd>{{ $reservationOverview['agent_email'] }}</dd></div>
            </dl>
        </article>

        <article class="reservation-detail-info-block">
            <div class="reservation-detail-info-block__heading"><i class="fa fa-id-badge" aria-hidden="true"></i><h3>{{ __('reservations.operation_team') }}</h3></div>
            <dl class="reservation-detail-definition-list">
                <div><dt>{{ __('reservations.guide') }}</dt><dd>{{ $reservationOverview['guide_name'] }}@if($reservationOverview['guide_meta'])<small>{{ $reservationOverview['guide_meta'] }}</small>@endif</dd></div>
                <div><dt>{{ __('reservations.driver') }}</dt><dd>{{ $reservationOverview['driver_name'] }}@if($reservationOverview['driver_meta'])<small>{{ $reservationOverview['driver_meta'] }}</small>@endif</dd></div>
            </dl>
        </article>
    </div>
</section>
