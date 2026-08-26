@extends('layouts.head')

@section('title', __('reservations.title'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/reservations/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/reservations/index.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container reservations-admin-page" data-reservation-confirm="{{ __('reservations.delete_confirm') }}">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="reservations-admin-hero"
                    eyebrow="{{ __('reservations.eyebrow') }}"
                    title="{{ __('reservations.title') }}"
                    description="{{ __('reservations.description') }}"
                />

                <section class="backend-page-toolbar reservations-admin-toolbar">
                    <nav aria-label="{{ __('reservations.breadcrumb') }}">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">{{ __('reservations.admin_panel') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('reservations.title') }}</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $now->format('d M Y') }}</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('invalid') || session()->has('error'))
                    <section class="backend-feedback reservations-admin-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ __('reservations.action_attention') }}</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session()->has('success'))
                            <div class="backend-alert backend-alert--success"><strong>{{ session('success') }}</strong></div>
                        @endif
                        @if (session()->has('invalid') || session()->has('error'))
                            <div class="backend-alert backend-alert--danger"><strong>{{ session('invalid') ?? session('error') }}</strong></div>
                        @endif
                    </section>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="{{ __('reservations.summary') }}">
                    @foreach ($reservationStats as $stat)
                        <article class="backend-kpi-card backend-kpi-card--{{ $stat['tone'] }}">
                            <div class="backend-kpi-card__icon"><i class="{{ $stat['icon'] }}" aria-hidden="true"></i></div>
                            <div>
                                <span>{{ $stat['label'] }}</span>
                                <strong>{{ number_format($stat['value']) }}</strong>
                                <small>{{ $stat['meta'] }}</small>
                            </div>
                        </article>
                    @endforeach
                </section>
                <section class="backend-panel reservations-admin-calendar-panel" aria-labelledby="reservationCalendarTitle">
                    <div class="backend-section-header reservations-admin-calendar-panel__heading">
                        <div>
                            <span class="backend-section-header__label">{{ __('reservations.calendar_eyebrow') }}</span>
                            <h2 id="reservationCalendarTitle">{{ __('reservations.calendar_title') }}</h2>
                        </div>
                        <p>{{ __('reservations.calendar_description') }}</p>
                    </div>
                    <div class="reservations-admin-calendar-legend" aria-label="{{ __('reservations.calendar_legend') }}">
                        <span><i class="reservations-admin-calendar-dot reservations-admin-calendar-dot--active" aria-hidden="true"></i>{{ __('reservations.calendar_active') }}</span>
                        <span><i class="reservations-admin-calendar-dot reservations-admin-calendar-dot--in-service" aria-hidden="true"></i>{{ __('reservations.calendar_in_service') }}</span>
                        <span><i class="reservations-admin-calendar-dot reservations-admin-calendar-dot--overdue" aria-hidden="true"></i>{{ __('reservations.calendar_overdue') }}</span>
                    </div>
                    <div class="reservations-admin-calendar-wrap">
                        <div id="reservationCalendar" data-reservation-calendar aria-label="{{ __('reservations.calendar_title') }}"></div>
                        <div class="backend-empty-state reservations-admin-calendar-empty" data-reservation-calendar-empty hidden>
                            <i class="fa fa-calendar-o" aria-hidden="true"></i>
                            <strong>{{ __('reservations.calendar_empty') }}</strong>
                        </div>
                        <div class="backend-empty-state reservations-admin-calendar-fallback" data-reservation-calendar-fallback hidden>
                            <i class="fa fa-calendar-times-o" aria-hidden="true"></i>
                            <strong>{{ __('reservations.calendar_unavailable') }}</strong>
                        </div>
                    </div>
                    <script type="application/json" data-reservation-calendar-events>@json($reservationCalendarEvents, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)</script>
                    <script type="application/json" data-reservation-calendar-settings>@json($reservationCalendarSettings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)</script>
                </section>
                <section class="backend-filter-panel reservations-admin-filter" aria-label="{{ __('reservations.filters') }}">
                    <label class="backend-filter-field" for="reservationSearch">
                        <span class="backend-filter-label">{{ __('reservations.search') }}</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="reservationSearch" class="backend-filter-control" type="search" placeholder="{{ __('reservations.search_placeholder') }}" data-reservation-filter="search">
                        </span>
                    </label>
                    <label class="backend-filter-field" for="reservationServiceFilter">
                        <span class="backend-filter-label">{{ __('reservations.service') }}</span>
                        <select id="reservationServiceFilter" class="backend-filter-control" data-reservation-filter="service">
                            <option value="">{{ __('reservations.all_services') }}</option>
                            @foreach ($reservationServices as $service)
                                <option value="{{ strtolower($service) }}">{{ $service }}</option>
                            @endforeach
                        </select>
                    </label>
                </section>
                <section class="backend-panel reservations-admin-panel">
                    <div class="backend-section-header reservations-admin-panel__heading">
                        <div>
                            <span class="backend-section-header__label">{{ __('reservations.work_queue') }}</span>
                            <h2>{{ __('reservations.my_reservations') }}</h2>
                        </div>
                        <p>{{ __('reservations.queue_description') }}</p>
                    </div>

                    <div class="backend-table-wrap reservations-admin-table-wrap">
                        <table class="backend-table reservations-admin-table">
                            <thead>
                                <tr>
                                    <th>{{ __('reservations.reference') }}</th>
                                    <th>{{ __('reservations.agent') }}</th>
                                    <th>{{ __('reservations.service') }}</th>
                                    <th>{{ __('reservations.service_period') }}</th>
                                    <th>{{ __('reservations.invoice_due') }}</th>
                                    <th>{{ __('reservations.status') }}</th>
                                    <th>{{ __('reservations.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reservationRows as $row)
                                    <tr data-reservation-row data-reservation-search="{{ $row['search'] }}" data-reservation-service="{{ strtolower($row['service']) }}">
                                        <td data-label="{{ __('reservations.reference') }}">
                                            <strong>{{ $row['number'] }}</strong>
                                            <small>{{ trans_choice('reservations.guest_count', $row['guest_count'], ['count' => $row['guest_count']]) }} · {{ trans_choice('reservations.spk_count', $row['spk_count'], ['count' => $row['spk_count']]) }}</small>
                                        </td>
                                        <td data-label="{{ __('reservations.agent') }}">
                                            <strong>{{ $row['agent'] }}</strong>
                                            @if ($row['agent_office'])<small>{{ $row['agent_office'] }}</small>@endif
                                        </td>
                                        <td data-label="{{ __('reservations.service') }}">{{ $row['service'] }}</td>
                                        <td data-label="{{ __('reservations.service_period') }}">{{ $row['period'] }}</td>
                                        <td data-label="{{ __('reservations.invoice_due') }}">
                                            <strong>{{ $row['invoice'] ?: '-' }}</strong>
                                            <small class="{{ $row['is_overdue'] ? 'is-overdue' : '' }}">{{ $row['due_date'] ?: __('reservations.not_generated') }}</small>
                                        </td>
                                        <td data-label="{{ __('reservations.status') }}"><span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $row['status'] }}</span></td>
                                        <td data-label="{{ __('reservations.action') }}">
                                            <div class="backend-table-actions reservations-admin-actions">
                                                <a href="{{ route('view.reservation.detail', $row['id']) }}" class="backend-icon-action backend-icon-action--view" aria-label="{{ __('reservations.view_reference', ['reference' => $row['number']]) }}">
                                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7"><div class="backend-table-empty"><i class="fa fa-calendar-check" aria-hidden="true"></i><strong>{{ __('reservations.empty_title') }}</strong><span>{{ __('reservations.empty_description') }}</span></div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="backend-table-card-list reservations-admin-card-list">
                        @foreach ($reservationRows as $row)
                            <article class="backend-table-card reservations-admin-card" data-reservation-row data-reservation-search="{{ $row['search'] }}" data-reservation-service="{{ strtolower($row['service']) }}">
                                <div class="backend-table-card__header">
                                    <div><span>{{ __('reservations.reference') }}</span><strong>{{ $row['number'] }}</strong></div>
                                    <span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $row['status'] }}</span>
                                </div>
                                <dl class="backend-table-card-grid">
                                    <div><dt>{{ __('reservations.agent') }}</dt><dd>{{ $row['agent'] }}</dd></div>
                                    <div><dt>{{ __('reservations.service') }}</dt><dd>{{ $row['service'] }}</dd></div>
                                    <div><dt>{{ __('reservations.service_period') }}</dt><dd>{{ $row['period'] }}</dd></div>
                                    <div><dt>{{ __('reservations.invoice_due') }}</dt><dd>{{ $row['invoice'] ?: '-' }} / {{ $row['due_date'] ?: __('reservations.not_generated') }}</dd></div>
                                </dl>
                                <div class="backend-table-card__actions reservations-admin-card__actions">
                                    <a href="{{ route('view.reservation.detail', $row['id']) }}" class="backend-button backend-button-secondary"><i class="fas fa-eye" aria-hidden="true"></i> {{ __('reservations.view') }}</a>
                                </div>
                            </article>
                        @endforeach
                        <div class="backend-empty-state reservations-admin-filter-empty" data-reservation-filter-empty hidden>
                            <i class="fa fa-search" aria-hidden="true"></i><strong>{{ __('reservations.no_matches') }}</strong><span>{{ __('reservations.adjust_filters') }}</span>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <div class="modal fade backend-modal reservations-admin-calendar-modal" id="reservationCalendarEventModal" tabindex="-1" role="dialog" aria-labelledby="reservationCalendarEventTitle" aria-hidden="true" data-reservation-calendar-modal>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="backend-modal__header">
                        <div>
                            <span>{{ __('reservations.calendar_event_eyebrow') }}</span>
                            <h2 id="reservationCalendarEventTitle" data-calendar-detail="reference">{{ __('reservations.calendar_event_details') }}</h2>
                        </div>
                        <x-backend.modal-close :label="__('reservations.calendar_close')" />
                    </div>
                    <div class="backend-modal__body">
                        <dl class="reservations-admin-calendar-detail">
                            <div><dt>{{ __('reservations.service') }}</dt><dd data-calendar-detail="service">-</dd></div>
                            <div><dt>{{ __('reservations.agent') }}</dt><dd data-calendar-detail="agent">-</dd></div>
                            <div><dt>{{ __('reservations.service_period') }}</dt><dd data-calendar-detail="period">-</dd></div>
                            <div><dt>{{ __('reservations.calendar_guests_spk') }}</dt><dd data-calendar-detail="manifest">-</dd></div>
                            <div><dt>{{ __('reservations.invoice_due') }}</dt><dd data-calendar-detail="invoice">-</dd></div>
                            <div class="reservations-admin-calendar-detail__note"><dt>{{ __('reservations.calendar_note') }}</dt><dd data-calendar-detail="note">-</dd></div>
                        </dl>
                    </div>
                    <div class="backend-modal__footer">
                        <a href="#" class="backend-button backend-button-primary" data-calendar-detail="url" data-backend-action-loading>
                            <i class="fas fa-external-link-square-alt"></i> {{ __('reservations.calendar_open_detail') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

    @endcan
@endsection
