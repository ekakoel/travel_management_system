<section class="backend-panel backend-detail-side-card reservation-detail-side-card">
    <header class="backend-section-header"><div><span>{{ __('reservations.workflow_eyebrow') }}</span><h2>{{ __('reservations.quick_actions') }}</h2></div></header>
    <div class="backend-detail-side-actions">
        <button type="button" class="backend-button backend-button-secondary" data-reservation-print><i class="fa fa-print" aria-hidden="true"></i>{{ __('reservations.print_summary') }}</button>
        @if ($reservation->invoice)
            <a href="{{ url('/invoice/'.$reservation->invoice->id) }}" class="backend-button backend-button-primary" data-backend-action-loading><i class="fa fa-file-text-o" aria-hidden="true"></i>{{ __('reservations.open_invoice') }}</a>
        @else
            <form action="{{ route('view.reservation.invoice.store', $reservation) }}" method="post">
                @csrf
                @method('put')
                <button type="submit" class="backend-button backend-button-primary"><i class="fa fa-plus" aria-hidden="true"></i>{{ __('reservations.create_invoice') }}</button>
            </form>
        @endif
        <form action="{{ route('view.reservation.deactivate', $reservation) }}" method="post">
            @csrf
            @method('put')
            <button type="submit" class="backend-button backend-button-danger" data-confirm-delete="{{ __('reservations.deactivate_confirm') }}"><i class="fa fa-times-circle" aria-hidden="true"></i>{{ __('reservations.deactivate') }}</button>
        </form>
    </div>
</section>

<section class="backend-panel backend-detail-side-card reservation-detail-side-card">
    <header class="backend-section-header"><div><span>{{ __('reservations.navigation_eyebrow') }}</span><h2>{{ __('reservations.detail_sections') }}</h2></div></header>
    <nav class="reservation-detail-section-nav" aria-label="{{ __('reservations.detail_sections') }}">
        <a href="#reservation-overview"><i class="fa fa-info-circle" aria-hidden="true"></i>{{ __('reservations.reservation_overview') }}</a>
        <a href="#reservation-manifest"><i class="fa fa-users" aria-hidden="true"></i>{{ __('reservations.guest_manifest') }}</a>
        <a href="#reservation-services"><i class="fa fa-bell" aria-hidden="true"></i>{{ __('reservations.linked_services') }}</a>
        <a href="#reservation-notes"><i class="fa fa-list-alt" aria-hidden="true"></i>{{ __('reservations.trip_notes') }}</a>
    </nav>
</section>

<section class="backend-panel backend-detail-side-card reservation-detail-side-card">
    <header class="backend-section-header"><div><span>{{ __('reservations.billing_eyebrow') }}</span><h2>{{ __('reservations.invoice_readiness') }}</h2></div></header>
    <ul class="backend-detail-side-list">
        <li><span>{{ __('reservations.invoice') }}</span><strong>{{ $reservationOverview['invoice_reference'] ?: __('reservations.not_generated') }}</strong><small>{{ $reservationOverview['invoice_due_date'] ?: __('reservations.invoice_pending') }}</small></li>
        <li><span>{{ __('reservations.assignment') }}</span><strong>{{ $reservationOverview['guide_name'] }}</strong><small>{{ $reservationOverview['driver_name'] }}</small></li>
        <li><span>{{ __('reservations.manifest_eyebrow') }}</span><strong>{{ trans_choice('reservations.guest_count', $reservationGuests->count(), ['count' => $reservationGuests->count()]) }}</strong><small>{{ trans_choice('reservations.order_count', $reservationStats[2]['value'], ['count' => $reservationStats[2]['value']]) }}</small></li>
    </ul>
</section>

@if ($reservation->msg)
    <section class="backend-panel backend-detail-side-card reservation-detail-side-card">
        <header class="backend-section-header"><div><span>{{ __('reservations.note_eyebrow') }}</span><h2>{{ __('reservations.admin_notes') }}</h2></div></header>
        <div class="reservation-detail-admin-note">{{ $reservation->msg }}</div>
    </section>
@endif
