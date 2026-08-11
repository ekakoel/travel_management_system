<section class="backend-panel reservation-detail-panel" id="reservation-services" data-reservation-section>
    <header class="backend-section-header reservation-detail-panel__header">
        <div><span class="backend-section-header__label">{{ __('reservations.services_eyebrow') }}</span><h2>{{ __('reservations.linked_services') }}</h2></div>
        <p>{{ __('reservations.linked_services_description') }}</p>
    </header>

    @if ($reservationServiceGroups->isEmpty())
        <div class="backend-empty-state reservation-detail-empty"><i class="fa fa-bell" aria-hidden="true"></i><strong>{{ __('reservations.no_services_title') }}</strong><span>{{ __('reservations.no_services_description') }}</span></div>
    @else
        <div class="reservation-detail-service-groups">
            @foreach ($reservationServiceGroups as $group)
                <section class="reservation-detail-service-group" aria-labelledby="reservation-service-group-{{ $group['key'] }}">
                    <div class="reservation-detail-service-group__header">
                        <div><i class="{{ $group['icon'] }}" aria-hidden="true"></i><h3 id="reservation-service-group-{{ $group['key'] }}">{{ $group['label'] }}</h3></div>
                        <span>{{ trans_choice('reservations.order_count', $group['orders']->count(), ['count' => $group['orders']->count()]) }}</span>
                    </div>
                    <div class="reservation-detail-order-list">
                        @foreach ($group['orders'] as $order)
                            <article class="reservation-detail-order-card">
                                <div class="reservation-detail-order-card__top">
                                    <div><span>{{ $order['reference'] }} · {{ $order['service'] }}</span><h4>{{ $order['name'] }}</h4></div>
                                    <span class="backend-status-badge backend-status-badge--{{ $order['status_tone'] }}">{{ $order['status'] }}</span>
                                </div>
                                <dl class="reservation-detail-order-card__grid">
                                    <div><dt>{{ __('reservations.service_period') }}</dt><dd>{{ $order['period'] }}</dd></div>
                                    <div><dt>{{ __('reservations.pax') }}</dt><dd>{{ $order['pax'] ?: '-' }}</dd></div>
                                    <div><dt>{{ __('reservations.price') }}</dt><dd>@if($order['unit_price'])<small>{{ $order['unit_price'] }} / pax</small>@endif<strong>{{ $order['total_price'] ?: '-' }}</strong></dd></div>
                                    <div><dt>{{ __('reservations.route_location') }}</dt><dd>{{ $order['location'] ?: '-' }}@if($order['destination'])<small>→ {{ $order['destination'] }}</small>@endif</dd></div>
                                </dl>
                                <div class="reservation-detail-order-card__actions">
                                    <a href="{{ $order['detail_url'] }}" class="backend-button backend-button-secondary" data-backend-action-loading><i class="fa fa-external-link" aria-hidden="true"></i>{{ __('reservations.open_order') }}</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</section>
