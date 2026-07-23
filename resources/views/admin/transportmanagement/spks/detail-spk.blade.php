@extends('layouts.head')

@section('title', __('transport-management.detail.title'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/transport-management/detail.css') }}">
@endpush

@section('content')
    @php
        $statusClass = \Illuminate\Support\Str::slug($spk->status ?? 'pending');
        $vehicleName = trim(($spk->transport?->brand ?? '') . ' ' . ($spk->transport?->name ?? '')) ?: __('transport-management.empty.na');
        $spkDate = $spk->spk_date ? \Carbon\Carbon::parse($spk->spk_date) : null;
        $hasDestinations = $spk->destinations->isNotEmpty();
        $firstDestinationMapLink = optional($spk->destinations->first(fn ($destination) => filled($destination->destination_address)))->destination_address;
        $firstDestinationMapLink = \Illuminate\Support\Str::startsWith($firstDestinationMapLink, ['http://', 'https://']) ? $firstDestinationMapLink : null;
        $mapRouteReady = $mapRouteReady ?? false;
    @endphp

    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <main
            class="main-container transport-spk-detail-page"
            data-transport-spk-detail
            data-destinations="{{ e($destinationsJson) }}"
            data-wa-status-route="{{ route('wa.status') }}"
            data-wa-qr-route="{{ route('wa.qr') }}"
            data-wa-disconnect-route="{{ route('wa.disconnect') }}"
            data-label-sending="{{ __('transport-management.detail.actions.sending') }}"
            data-label-sent="{{ __('transport-management.detail.actions.sent') }}"
            data-label-send-failed="{{ __('transport-management.detail.actions.send_failed') }}"
            data-label-missing-phone="{{ __('transport-management.detail.actions.missing_phone') }}"
            data-label-select-time="{{ __('transport-management.detail.actions.select_time') }}"
            data-label-checking="{{ __('transport-management.detail.wa.checking') }}"
            data-label-connected="{{ __('transport-management.detail.wa.connected') }}"
            data-label-not-connected="{{ __('transport-management.detail.wa.not_connected') }}"
            data-label-request-failed="{{ __('transport-management.detail.wa.request_failed') }}"
            data-label-loading-qr="{{ __('transport-management.detail.wa.loading_qr') }}"
            data-label-waiting-qr="{{ __('transport-management.detail.wa.waiting_qr') }}"
            data-label-open-map="{{ __('transport-management.detail.map.open_map') }}"
            data-label-route-unavailable="{{ __('transport-management.detail.map.route_unavailable') }}"
            data-label-no-coordinate="{{ __('transport-management.detail.map.no_coordinate') }}"
            data-map-ready="{{ $mapRouteReady ? 'true' : 'false' }}"
        >
            <script id="transportSpkMapData" type="application/json">{!! $destinationsJson !!}</script>
            <div class="pd-ltr-20">
                <x-backend.page-hero class="transport-spk-detail-hero">
                    <x-slot name="kicker">
                        @lang('transport-management.detail.eyebrow')
                    </x-slot>
                    <x-slot name="heading">
                        {{ $spk->spk_number ?? __('transport-management.detail.title') }}
                    </x-slot>
                    <x-slot name="copy">
                        <p>
                            @lang('transport-management.detail.subtitle', ['order' => $spk->order_number ?? '-'])
                        </p>
                    </x-slot>
                    <x-slot name="action">
                        <span class="backend-status-badge backend-status-badge--{{ $statusClass }} transport-spk-detail-status transport-spk-detail-status--{{ $statusClass }}">
                            {{ $spk->status ?? __('transport-management.empty.na') }}
                        </span>
                    </x-slot>
                </x-backend.page-hero>

                <div class="backend-page-toolbar transport-spk-detail-toolbar">
                    <nav aria-label="{{ __('transport-management.breadcrumb.label') }}">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/admin-panel') }}">@lang('transport-management.detail.breadcrumb.admin')</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('view.transport-management.index') }}">@lang('transport-management.title')</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $spk->order_number ?? '-' }}</li>
                        </ol>
                    </nav>
                    <div class="transport-spk-detail-toolbar__actions">
                        @if($spk->send_report === 1 && $spk->operator?->phone)
                            <button
                                id="btnSendWaToDriver"
                                class="backend-button backend-button-secondary sendWA"
                                type="button"
                                data-route="{{ route('send.whatsapp-driver') }}"
                                data-phone="{{ $spk->driver?->phone }}"
                                data-spk="{{ $spk->id }}"
                            >
                                <i class="fa fa-share" aria-hidden="true"></i>
                                @lang('transport-management.detail.actions.share_driver')
                            </button>
                            <button
                                id="btnSendWaToOperator"
                                class="backend-button backend-button-secondary sendWA"
                                type="button"
                                data-route="{{ route('send.whatsapp-operator') }}"
                                data-phone="{{ $spk->operator?->phone }}"
                                data-spk="{{ $spk->id }}"
                            >
                                <i class="fa fa-share" aria-hidden="true"></i>
                                @lang('transport-management.detail.actions.share_operator')
                            </button>
                        @endif
                        @if($spk->send_report === 0 && $spk->operator?->phone)
                            <button
                                id="btnSendWa"
                                class="backend-button backend-button-secondary sendWA"
                                type="button"
                                data-route="{{ route('send.whatsapp-both') }}"
                                data-phone="{{ $spk->driver?->phone }}"
                                data-spk="{{ $spk->id }}"
                            >
                                <i class="fa fa-share" aria-hidden="true"></i>
                                @lang('transport-management.detail.actions.share_both')
                            </button>
                        @endif
                        <a class="backend-button backend-button-secondary" href="{{ route('spks.print', $spk->id) }}" target="_blank" rel="noopener">
                            <i class="fa fa-print" aria-hidden="true"></i>
                            @lang('transport-management.detail.actions.print')
                        </a>
                        <button class="backend-button backend-button-primary" type="button" data-toggle="modal" data-target="#editSpkDetail">
                            <i class="fa fa-pencil" aria-hidden="true"></i>
                            @lang('transport-management.detail.actions.edit_spk')
                        </button>
                        <a class="backend-button backend-button-danger" href="{{ route('view.transport-management.index') }}">
                            <i class="icon-copy dw dw-left-arrow1" aria-hidden="true"></i>
                            @lang('transport-management.detail.actions.back')
                        </a>
                    </div>
                </div>

                @if(session('success') || session('error') || $errors->any())
                    <div class="backend-feedback transport-spk-detail-feedback">
                        @if(session('success'))
                            <div class="backend-alert backend-alert--success transport-spk-detail-alert transport-spk-detail-alert--success">
                                <strong>@lang('transport-management.feedback.success')</strong>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="backend-alert backend-alert--danger transport-spk-detail-alert transport-spk-detail-alert--danger">
                                <strong>@lang('transport-management.feedback.error')</strong>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="backend-alert backend-alert--danger transport-spk-detail-alert transport-spk-detail-alert--danger">
                                <strong>@lang('transport-management.feedback.validation')</strong>
                                <span>{{ $errors->first() }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                <section class="transport-spk-detail-summary" aria-label="{{ __('transport-management.detail.summary.label') }}">
                    <article>
                        <span>@lang('transport-management.detail.summary.guests')</span>
                        <strong>{{ $detailSummary['guests'] }}</strong>
                        <small>{{ trans_choice('transport-management.table.pax', (int) $spk->number_of_guests, ['count' => (int) $spk->number_of_guests]) }}</small>
                    </article>
                    <article>
                        <span>@lang('transport-management.detail.summary.destinations')</span>
                        <strong>{{ $detailSummary['destinations'] }}</strong>
                        <small>@lang('transport-management.detail.summary.visited', ['count' => $detailSummary['visited_destinations']])</small>
                    </article>
                    <article>
                        <span>@lang('transport-management.detail.summary.flights')</span>
                        <strong>{{ $detailSummary['airport_shuttles'] }}</strong>
                        <small>{{ $spk->type ?? '-' }}</small>
                    </article>
                    <article>
                        <span>@lang('transport-management.detail.summary.distance')</span>
                        <strong>{{ $spk->total_distance ?? 0 }}</strong>
                        <small>@lang('transport-management.detail.summary.km')</small>
                    </article>
                </section>

                <div class="transport-spk-detail-layout">
                    <div class="transport-spk-detail-main">
                        <section class="backend-panel transport-spk-detail-panel">
                            <div class="backend-section-header transport-spk-detail-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">@lang('transport-management.detail.overview.eyebrow')</span>
                                    <h2>@lang('transport-management.detail.overview.title')</h2>
                                </div>
                                <span class="backend-status-badge backend-status-badge--{{ $statusClass }} transport-spk-detail-status transport-spk-detail-status--{{ $statusClass }}">
                                    {{ $spk->status ?? __('transport-management.empty.na') }}
                                </span>
                            </div>
                            <dl class="transport-spk-detail-grid">
                                <div>
                                    <dt>@lang('transport-management.form.order_number')</dt>
                                    <dd>{{ $spk->order_number ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt>@lang('transport-management.table.spk_number')</dt>
                                    <dd>{{ $spk->spk_number ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt>@lang('transport-management.form.spk_date')</dt>
                                    <dd>{{ $spkDate ? $spkDate->locale('en')->translatedFormat('l, d M Y') : '-' }}</dd>
                                </div>
                                <div>
                                    <dt>@lang('transport-management.form.service')</dt>
                                    <dd>{{ $spk->type ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt>@lang('transport-management.modal.reserved_by')</dt>
                                    <dd>{{ $spk->operator?->name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt>@lang('transport-management.form.guests')</dt>
                                    <dd>{{ trans_choice('transport-management.table.pax', (int) $spk->number_of_guests, ['count' => (int) $spk->number_of_guests]) }}</dd>
                                </div>
                                <div>
                                    <dt>@lang('transport-management.form.vehicle')</dt>
                                    <dd>{{ $vehicleName }}</dd>
                                </div>
                                <div>
                                    <dt>@lang('transport-management.form.driver')</dt>
                                    <dd>{{ $spk->driver?->name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt>@lang('transport-management.form.plate_number')</dt>
                                    <dd>{{ $spk->plate_number ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt>@lang('transport-management.detail.overview.reservation')</dt>
                                    <dd>{{ $spk->reservation?->rsv_no ?? '-' }}</dd>
                                </div>
                            </dl>
                        </section>

                        @if ($spk->type === 'Airport Shuttle')
                            <section class="backend-panel transport-spk-detail-panel">
                                <div class="transport-spk-detail-section">
                                    <div class="backend-section-header transport-spk-detail-section__heading">
                                        <div>
                                            <span class="backend-section-header__label">@lang('transport-management.detail.flight.eyebrow')</span>
                                            <h3>@lang('transport-management.modal.airport_shuttle')</h3>
                                        </div>
                                        <button class="backend-button backend-button-primary" type="button" data-toggle="modal" data-target="#addAirportShuttle">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                            @lang('transport-management.detail.actions.add_airport_shuttle')
                                        </button>
                                    </div>

                                    <div class="backend-table-wrap transport-spk-detail-desktop-table">
                                        <table class="backend-table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('transport-management.table.no')</th>
                                                    <th>@lang('transport-management.table.date')</th>
                                                    <th>@lang('transport-management.detail.flight.number')</th>
                                                    <th>@lang('transport-management.detail.flight.type')</th>
                                                    <th class="text-right">@lang('transport-management.table.actions')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($airport_shuttles as $airportShuttle)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td><strong>{{ $airportShuttle->date ? dateTimeFormat($airportShuttle->date) : '-' }}</strong></td>
                                                        <td>{{ $airportShuttle->flight_number ?? '-' }}</td>
                                                        <td>{{ $airportShuttle->nav ?? '-' }}</td>
                                                        <td class="text-right">
                                                            <div class="backend-table-actions">
                                                                <button class="backend-icon-action" type="button" data-toggle="modal" data-target="#editAirportShuttle{{ $airportShuttle->id }}" aria-label="{{ __('transport-management.detail.actions.edit') }}">
                                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                                </button>
                                                                <form action="{{ route('func.spk-airport-shuttle.delete', $airportShuttle->id) }}" method="POST" data-confirm-delete="{{ __('transport-management.detail.actions.confirm_airport_delete') }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="backend-danger-icon-action" aria-label="{{ __('transport-management.detail.actions.delete') }}">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">
                                                            <div class="backend-table-empty">
                                                                <i class="fa fa-plane" aria-hidden="true"></i>
                                                                <strong>@lang('transport-management.empty.airport_shuttle')</strong>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="backend-table-card-list transport-spk-detail-mobile-list">
                                        @forelse ($airport_shuttles as $airportShuttle)
                                            <article class="backend-table-card transport-spk-detail-card">
                                                <div class="backend-table-card__header">
                                                    <div>
                                                        <span>{{ $airportShuttle->date ? dateTimeFormat($airportShuttle->date) : '-' }}</span>
                                                        <strong>{{ $airportShuttle->flight_number ?? '-' }}</strong>
                                                    </div>
                                                    <span class="backend-status-badge backend-status-badge--pending transport-spk-detail-status transport-spk-detail-status--pending">{{ $airportShuttle->nav ?? '-' }}</span>
                                                </div>
                                                <dl class="backend-table-card-grid">
                                                    <div>
                                                        <dt>@lang('transport-management.detail.flight.number')</dt>
                                                        <dd>{{ $airportShuttle->flight_number ?? '-' }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>@lang('transport-management.detail.flight.type')</dt>
                                                        <dd>{{ $airportShuttle->nav ?? '-' }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>@lang('transport-management.table.actions')</dt>
                                                        <dd>
                                                            <div class="backend-table-actions">
                                                                <button class="backend-icon-action" type="button" data-toggle="modal" data-target="#editAirportShuttle{{ $airportShuttle->id }}" aria-label="{{ __('transport-management.detail.actions.edit') }}">
                                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                                </button>
                                                                <form action="{{ route('func.spk-airport-shuttle.delete', $airportShuttle->id) }}" method="POST" data-confirm-delete="{{ __('transport-management.detail.actions.confirm_airport_delete') }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="backend-danger-icon-action" aria-label="{{ __('transport-management.detail.actions.delete') }}">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </dd>
                                                    </div>
                                                </dl>
                                            </article>
                                        @empty
                                            <div class="backend-table-empty">
                                                <i class="fa fa-plane" aria-hidden="true"></i>
                                                <strong>@lang('transport-management.empty.airport_shuttle')</strong>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </section>
                        @endif

                        <section class="backend-panel transport-spk-detail-panel">
                            <div class="transport-spk-detail-section">
                                <div class="backend-section-header transport-spk-detail-section__heading">
                                    <div>
                                        <span class="backend-section-header__label">@lang('transport-management.detail.guests.eyebrow')</span>
                                        <h3>@lang('transport-management.modal.guests')</h3>
                                    </div>
                                    <button class="backend-button backend-button-primary" type="button" data-toggle="modal" data-target="#addGuest">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        @lang('transport-management.detail.actions.add_guest')
                                    </button>
                                </div>

                                <div class="backend-table-wrap transport-spk-detail-desktop-table">
                                    <table class="backend-table">
                                        <thead>
                                            <tr>
                                                <th>@lang('transport-management.table.no')</th>
                                                <th>@lang('transport-management.detail.guests.name')</th>
                                                <th>@lang('transport-management.detail.guests.profile')</th>
                                                <th>@lang('transport-management.detail.guests.contact')</th>
                                                <th class="text-right">@lang('transport-management.table.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($guests as $guest)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <strong>{{ $guest->name }}</strong>
                                                        @if($guest->name_mandarin)
                                                            <small>{{ $guest->name_mandarin }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ ($guest->sex === 'm' ? __('transport-management.detail.guests.male') : __('transport-management.detail.guests.female')) }} / {{ $guest->age }}</td>
                                                    <td>{{ $guest->phone ?: '-' }}</td>
                                                    <td class="text-right">
                                                        <div class="backend-table-actions">
                                                            <button class="backend-icon-action" type="button" data-toggle="modal" data-target="#editGuest{{ $guest->id }}" aria-label="{{ __('transport-management.detail.actions.edit') }}">
                                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                                            </button>
                                                            <form action="{{ route('func.spk-guest.delete', $guest->id) }}" method="POST" data-confirm-delete="{{ __('transport-management.detail.actions.confirm_guest_delete') }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="backend-danger-icon-action" aria-label="{{ __('transport-management.detail.actions.delete') }}">
                                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5">
                                                        <div class="backend-table-empty">
                                                            <i class="fa fa-users" aria-hidden="true"></i>
                                                            <strong>@lang('transport-management.empty.guests')</strong>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="backend-table-card-list transport-spk-detail-mobile-list">
                                    @forelse ($guests as $guest)
                                        <article class="backend-table-card transport-spk-detail-card">
                                            <div class="backend-table-card__header">
                                                <div>
                                                    <span>@lang('transport-management.detail.guests.name')</span>
                                                    <strong>{{ $guest->name }}</strong>
                                                </div>
                                                <span class="transport-spk-detail-status transport-spk-detail-status--pending">{{ $guest->age }}</span>
                                            </div>
                                            <dl class="backend-table-card-grid">
                                                <div>
                                                    <dt>@lang('transport-management.detail.guests.profile')</dt>
                                                    <dd>{{ ($guest->sex === 'm' ? __('transport-management.detail.guests.male') : __('transport-management.detail.guests.female')) }} / {{ $guest->age }}</dd>
                                                </div>
                                                <div>
                                                    <dt>@lang('transport-management.detail.guests.contact')</dt>
                                                    <dd>{{ $guest->phone ?: '-' }}</dd>
                                                </div>
                                                <div>
                                                    <dt>@lang('transport-management.table.actions')</dt>
                                                    <dd>
                                                        <div class="backend-table-actions">
                                                            <button class="backend-icon-action" type="button" data-toggle="modal" data-target="#editGuest{{ $guest->id }}" aria-label="{{ __('transport-management.detail.actions.edit') }}">
                                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                                            </button>
                                                            <form action="{{ route('func.spk-guest.delete', $guest->id) }}" method="POST" data-confirm-delete="{{ __('transport-management.detail.actions.confirm_guest_delete') }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="backend-danger-icon-action" aria-label="{{ __('transport-management.detail.actions.delete') }}">
                                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </dd>
                                                </div>
                                            </dl>
                                        </article>
                                    @empty
                                        <div class="backend-table-empty">
                                            <i class="fa fa-users" aria-hidden="true"></i>
                                            <strong>@lang('transport-management.empty.guests')</strong>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </section>

                        <section class="backend-panel transport-spk-detail-panel">
                            <div class="transport-spk-detail-section">
                                <div class="backend-section-header transport-spk-detail-section__heading">
                                    <div>
                                        <span class="backend-section-header__label">@lang('transport-management.detail.destinations.eyebrow')</span>
                                        <h3>@lang('transport-management.modal.destinations')</h3>
                                    </div>
                                    <button class="backend-button backend-button-primary" type="button" data-toggle="modal" data-target="#addDestination">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        @lang('transport-management.detail.actions.add_destination')
                                    </button>
                                </div>

                                <div class="backend-table-wrap transport-spk-detail-desktop-table">
                                    <table class="backend-table">
                                        <thead>
                                            <tr>
                                                <th>@lang('transport-management.table.date')</th>
                                                <th>@lang('transport-management.detail.destinations.name')</th>
                                                <th>@lang('transport-management.table.status')</th>
                                                <th>@lang('transport-management.detail.destinations.checkin_at')</th>
                                                <th>@lang('transport-management.detail.destinations.checkin_location')</th>
                                                <th class="text-right">@lang('transport-management.table.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($spk->destinations as $destination)
                                                @php $destinationStatus = \Illuminate\Support\Str::slug($destination->status ?? 'pending'); @endphp
                                                <tr>
                                                    <td><strong>{{ $destination->date ? dateTimeFormat($destination->date) : '-' }}</strong></td>
                                                    <td>
                                                        @if($destination->destination_address)
                                                            <a href="{{ $destination->destination_address }}" target="_blank" rel="noopener">
                                                                <strong>{{ $destination->destination_name ?? '-' }}</strong>
                                                            </a>
                                                        @else
                                                            <strong>{{ $destination->destination_name ?? '-' }}</strong>
                                                        @endif
                                                        @if($destination->description)
                                                            <small>{{ strip_tags($destination->description) }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="transport-spk-detail-status transport-spk-detail-status--{{ $destinationStatus }}">
                                                            {{ $destination->status ?? 'Pending' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $destination->visited_at ? dateTimeFormat($destination->visited_at) : '-' }}</td>
                                                    <td>
                                                        @if($destination->status === 'Visited' && $destination->checkin_map_link)
                                                            <a class="backend-button backend-button-secondary" href="{{ $destination->checkin_map_link }}" target="_blank" rel="noopener">
                                                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                                                <span>@lang('transport-management.detail.destinations.see_map')</span>
                                                            </a>
                                                        @else
                                                            <span class="transport-spk-detail-muted">@lang('transport-management.detail.destinations.not_visited')</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        @if ($destination->status !== 'Visited')
                                                            <div class="backend-table-actions">
                                                                <button class="backend-icon-action" type="button" data-toggle="modal" data-target="#updateSpkDestination{{ $destination->id }}" aria-label="{{ __('transport-management.detail.actions.edit') }}">
                                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                                </button>
                                                                <form action="{{ route('func.spk-destination.delete', $destination->id) }}" method="POST" data-confirm-delete="{{ __('transport-management.detail.actions.confirm_destination_delete') }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="backend-danger-icon-action" aria-label="{{ __('transport-management.detail.actions.delete') }}">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6">
                                                        <div class="backend-table-empty">
                                                            <i class="fa fa-map-marker" aria-hidden="true"></i>
                                                            <strong>@lang('transport-management.empty.destinations')</strong>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="backend-table-card-list transport-spk-detail-mobile-list">
                                    @forelse($spk->destinations as $destination)
                                        @php $destinationStatus = \Illuminate\Support\Str::slug($destination->status ?? 'pending'); @endphp
                                        <article class="backend-table-card transport-spk-detail-card">
                                            <div class="backend-table-card__header">
                                                <div>
                                                    <span>{{ $destination->date ? dateTimeFormat($destination->date) : '-' }}</span>
                                                    <strong>{{ $destination->destination_name ?? '-' }}</strong>
                                                </div>
                                                    <span class="backend-status-badge backend-status-badge--{{ $destinationStatus }} transport-spk-detail-status transport-spk-detail-status--{{ $destinationStatus }}">{{ $destination->status ?? 'Pending' }}</span>
                                            </div>
                                            <dl class="backend-table-card-grid">
                                                <div>
                                                    <dt>@lang('transport-management.detail.destinations.checkin_at')</dt>
                                                    <dd>{{ $destination->visited_at ? dateTimeFormat($destination->visited_at) : '-' }}</dd>
                                                </div>
                                                <div>
                                                    <dt>@lang('transport-management.detail.destinations.checkin_location')</dt>
                                                    <dd>
                                                        @if($destination->status === 'Visited' && $destination->checkin_map_link)
                                                            <a href="{{ $destination->checkin_map_link }}" target="_blank" rel="noopener">@lang('transport-management.detail.destinations.see_map')</a>
                                                        @else
                                                            @lang('transport-management.detail.destinations.not_visited')
                                                        @endif
                                                    </dd>
                                                </div>
                                                <div>
                                                    <dt>@lang('transport-management.table.actions')</dt>
                                                    <dd>
                                                        @if ($destination->status !== 'Visited')
                                                            <div class="backend-table-actions">
                                                                <button class="backend-icon-action" type="button" data-toggle="modal" data-target="#updateSpkDestination{{ $destination->id }}" aria-label="{{ __('transport-management.detail.actions.edit') }}">
                                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                                </button>
                                                                <form action="{{ route('func.spk-destination.delete', $destination->id) }}" method="POST" data-confirm-delete="{{ __('transport-management.detail.actions.confirm_destination_delete') }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="backend-danger-icon-action" aria-label="{{ __('transport-management.detail.actions.delete') }}">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </dd>
                                                </div>
                                            </dl>
                                        </article>
                                    @empty
                                        <div class="backend-table-empty">
                                            <i class="fa fa-map-marker" aria-hidden="true"></i>
                                            <strong>@lang('transport-management.empty.destinations')</strong>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside class="transport-spk-detail-side">
                        <section class="backend-panel transport-spk-detail-panel">
                            <div class="backend-section-header transport-spk-detail-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">@lang('transport-management.detail.wa.eyebrow')</span>
                                    <h2>@lang('transport-management.detail.wa.title')</h2>
                                </div>
                            </div>
                            <div class="transport-spk-detail-section">
                                <div id="wa-status" class="transport-spk-detail-wa-status">
                                    <span class="backend-status-badge backend-status-badge--checking transport-spk-detail-status transport-spk-detail-status--checking">@lang('transport-management.detail.wa.checking')</span>
                                </div>
                                <div id="wa-status-box" class="transport-spk-detail-wa-box"></div>
                                <div class="transport-spk-detail-wa-actions">
                                    <button id="btnConnectWA" type="button" class="backend-button backend-button-primary" hidden>
                                        <i class="fa fa-qrcode" aria-hidden="true"></i>
                                        @lang('transport-management.detail.wa.connect')
                                    </button>
                                    <button id="btnDisconnectWA" type="button" class="backend-button backend-button-danger" hidden>
                                        <i class="fa fa-unlink" aria-hidden="true"></i>
                                        @lang('transport-management.detail.wa.disconnect')
                                    </button>
                                    <button id="btnRefreshWA" type="button" class="backend-button backend-button-secondary">
                                        <i class="fa fa-refresh" aria-hidden="true"></i>
                                        @lang('transport-management.detail.wa.refresh')
                                    </button>
                                </div>
                            </div>
                        </section>

                        <section class="backend-panel transport-spk-detail-panel">
                            <div class="backend-section-header transport-spk-detail-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">@lang('transport-management.detail.map.eyebrow')</span>
                                    <h2>@lang('transport-management.detail.map.title')</h2>
                                </div>
                            </div>
                            <div class="transport-spk-detail-section">
                                <p class="transport-spk-detail-muted">
                                    @lang('transport-management.detail.map.total_distance'):
                                    <strong>{{ $spk->total_distance ?? 0 }} @lang('transport-management.detail.summary.km')</strong>
                                </p>
                                @if($hasDestinations)
                                    <div id="transportSpkMap" class="transport-spk-detail-map">
                                        <div class="transport-spk-detail-map-fallback" data-map-fallback>
                                            <i class="fa fa-map" aria-hidden="true"></i>
                                            <strong>
                                                @if($mapRouteReady)
                                                    @lang('transport-management.detail.map.loading')
                                                @else
                                                    @lang('transport-management.detail.map.no_coordinate')
                                                @endif
                                            </strong>
                                            <small>
                                                @if($mapRouteReady)
                                                    @lang('transport-management.detail.map.loading_help')
                                                @else
                                                    @lang('transport-management.detail.map.no_coordinate_help')
                                                @endif
                                            </small>
                                            @if($firstDestinationMapLink)
                                                <a class="backend-button backend-button-secondary" href="{{ $firstDestinationMapLink }}" target="_blank" rel="noopener">
                                                    <i class="fa fa-external-link" aria-hidden="true"></i>
                                                    @lang('transport-management.detail.map.open_map')
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="backend-table-empty">
                                        <i class="fa fa-map" aria-hidden="true"></i>
                                        <strong>@lang('transport-management.detail.map.no_coordinate')</strong>
                                    </div>
                                @endif
                                <ol class="transport-spk-detail-route-list">
                                    @forelse($spk->destinations as $destination)
                                        <li>
                                            <span class="transport-spk-detail-route-marker {{ $destination->status === 'Visited' ? 'is-visited' : '' }}">{{ $loop->iteration }}</span>
                                            <div>
                                                <strong>{{ $destination->destination_name ?? '-' }}</strong>
                                                <small>{{ $destination->date ? \Carbon\Carbon::parse($destination->date)->format('H:i') : '-' }}</small>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="transport-spk-detail-muted">@lang('transport-management.empty.destinations')</li>
                                    @endforelse
                                </ol>
                            </div>
                        </section>
                    </aside>
                </div>
            </div>
        </main>

        @include('admin.transportmanagement.spks.partials.detail-modals', [
            'spk' => $spk,
            'operators' => $operators,
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'guests' => $guests,
            'airport_shuttles' => $airport_shuttles,
        ])

        <div id="transportSpkTimePicker" class="transport-spk-detail-time-picker" aria-hidden="true">
            <div class="transport-spk-detail-time-picker__header">
                <strong>@lang('transport-management.detail.actions.select_time_title')</strong>
                <button type="button" class="backend-icon-action" data-time-close aria-label="{{ __('messages.Close') }}">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
            <div class="transport-spk-detail-time-picker__columns">
                <div class="transport-spk-detail-time-picker__column" data-time-hours></div>
                <div class="transport-spk-detail-time-picker__column" data-time-minutes></div>
            </div>
            <div class="transport-spk-detail-time-picker__footer">
                <span class="transport-spk-detail-muted">HH:MM</span>
                <button type="button" class="backend-button backend-button-primary" data-time-set>@lang('transport-management.detail.actions.set_time')</button>
            </div>
        </div>

        <div class="modal fade backend-modal transport-spk-detail-modal" id="waModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="backend-modal__header transport-spk-detail-modal__header">
                        <div>
                            <span class="backend-section-header__label">@lang('transport-management.detail.wa.eyebrow')</span>
                            <h3>@lang('transport-management.detail.wa.scan_title')</h3>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('messages.Close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="backend-modal__body transport-spk-detail-modal__body">
                        <div id="wa-qrcode" class="transport-spk-detail-qr"></div>
                        <p class="transport-spk-detail-muted">@lang('transport-management.detail.wa.scan_help')</p>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/transport-management/detail.js') }}"></script>
    <script>
        (function () {
            if (window.initTransportSpkOpenMap) {
                window.initTransportSpkOpenMap();
            }

            var mapElement = document.getElementById('transportSpkMap');
            var mapData = document.getElementById('transportSpkMapData');

            if (!mapElement || !mapData || mapElement.dataset.mapInitialized === 'true') {
                return;
            }

            var destinations = [];
            try {
                destinations = JSON.parse(mapData.textContent || '[]');
            } catch (error) {
                destinations = [];
            }

            if (!destinations.length) {
                return;
            }

            var points = [];
            for (var i = 0; i < destinations.length; i += 1) {
                var lat = parseFloat(destinations[i].lat);
                var lng = parseFloat(destinations[i].lng);

                if (isFinite(lat) && isFinite(lng)) {
                    points.push({ lat: lat, lng: lng, destination: destinations[i] });
                }
            }

            if (!points.length) {
                return;
            }

            mapElement.dataset.mapInitialized = 'true';

            var fallback = mapElement.querySelector('[data-map-fallback]');
            if (fallback) {
                fallback.parentNode.removeChild(fallback);
            }

            var width = Math.max(Math.round(mapElement.clientWidth || 360), 320);
            var height = Math.max(Math.round(mapElement.clientHeight || 360), 320);
            var tileSize = 256;

            function clamp(value, min, max) {
                return Math.min(Math.max(value, min), max);
            }

            function project(lat, lng, zoom) {
                var scale = tileSize * Math.pow(2, zoom);
                var safeLat = clamp(lat, -85.05112878, 85.05112878);
                var sinLat = Math.sin((safeLat * Math.PI) / 180);

                return {
                    x: ((lng + 180) / 360) * scale,
                    y: (0.5 - (Math.log((1 + sinLat) / (1 - sinLat)) / (4 * Math.PI))) * scale
                };
            }

            function chooseZoom() {
                if (points.length < 2) {
                    return 15;
                }

                for (var zoom = 17; zoom >= 9; zoom -= 1) {
                    var minX = Infinity;
                    var maxX = -Infinity;
                    var minY = Infinity;
                    var maxY = -Infinity;

                    for (var p = 0; p < points.length; p += 1) {
                        var projected = project(points[p].lat, points[p].lng, zoom);
                        minX = Math.min(minX, projected.x);
                        maxX = Math.max(maxX, projected.x);
                        minY = Math.min(minY, projected.y);
                        maxY = Math.max(maxY, projected.y);
                    }

                    if ((maxX - minX) <= width - 88 && (maxY - minY) <= height - 88) {
                        return zoom;
                    }
                }

                return 9;
            }

            function layer(className) {
                var element = document.createElement('div');
                element.className = className;
                return element;
            }

            var zoomLevel = chooseZoom();
            var centerLat = 0;
            var centerLng = 0;
            for (var c = 0; c < points.length; c += 1) {
                centerLat += points[c].lat;
                centerLng += points[c].lng;
            }
            centerLat /= points.length;
            centerLng /= points.length;

            var centerPixels = project(centerLat, centerLng, zoomLevel);
            var tiles = layer('transport-spk-detail-map-tiles');
            var route = layer('transport-spk-detail-map-route');
            var markers = layer('transport-spk-detail-map-markers');
            var attribution = layer('transport-spk-detail-map-attribution');
            attribution.innerHTML = '&copy; OpenStreetMap contributors';
            mapElement.appendChild(tiles);
            mapElement.appendChild(route);
            mapElement.appendChild(markers);
            mapElement.appendChild(attribution);

            var tileCount = Math.pow(2, zoomLevel);
            var minTileX = Math.floor((centerPixels.x - (width / 2)) / tileSize);
            var maxTileX = Math.floor((centerPixels.x + (width / 2)) / tileSize);
            var minTileY = Math.floor((centerPixels.y - (height / 2)) / tileSize);
            var maxTileY = Math.floor((centerPixels.y + (height / 2)) / tileSize);

            for (var tileX = minTileX; tileX <= maxTileX; tileX += 1) {
                for (var tileY = minTileY; tileY <= maxTileY; tileY += 1) {
                    if (tileY < 0 || tileY >= tileCount) {
                        continue;
                    }

                    var wrappedTileX = ((tileX % tileCount) + tileCount) % tileCount;
                    var image = document.createElement('img');
                    image.alt = '';
                    image.src = 'https://tile.openstreetmap.org/' + zoomLevel + '/' + wrappedTileX + '/' + tileY + '.png';
                    image.style.left = (((tileX * tileSize) - centerPixels.x) + (width / 2)) + 'px';
                    image.style.top = (((tileY * tileSize) - centerPixels.y) + (height / 2)) + 'px';
                    tiles.appendChild(image);
                }
            }

            function viewportPoint(point) {
                var projected = project(point.lat, point.lng, zoomLevel);
                return {
                    x: projected.x - centerPixels.x + (width / 2),
                    y: projected.y - centerPixels.y + (height / 2)
                };
            }

            var polyline = '';
            for (var r = 0; r < points.length; r += 1) {
                var routePoint = viewportPoint(points[r]);
                polyline += routePoint.x.toFixed(1) + ',' + routePoint.y.toFixed(1) + ' ';
            }
            route.innerHTML = '<svg viewBox="0 0 ' + width + ' ' + height + '" preserveAspectRatio="none" aria-hidden="true"><polyline points="' + polyline + '" class="is-routed" /></svg>';

            for (var m = 0; m < points.length; m += 1) {
                var markerPoint = viewportPoint(points[m]);
                var marker = document.createElement('button');
                marker.type = 'button';
                marker.className = 'transport-spk-detail-map-marker' + (points[m].destination.status === 'Visited' ? ' is-visited' : '');
                marker.style.left = markerPoint.x + 'px';
                marker.style.top = markerPoint.y + 'px';
                marker.textContent = points[m].destination.order || (m + 1);
                marker.title = points[m].destination.name || '';
                markers.appendChild(marker);
            }
        }());
    </script>
@endpush
