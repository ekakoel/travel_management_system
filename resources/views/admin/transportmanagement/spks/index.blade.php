@extends('layouts.head')

@section('title', __('transport-management.title'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/transport-management/index.css') }}">
@endpush

@section('content')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <main class="main-container transport-management-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero class="transport-management-hero">
                    <x-slot name="kicker">
                        @lang('transport-management.eyebrow')
                    </x-slot>
                    <x-slot name="heading">
                        @lang('transport-management.title')
                    </x-slot>
                    <x-slot name="copy">
                        <p>
                            @lang('transport-management.subtitle')
                        </p>
                    </x-slot>
                    <x-slot name="action">
                        <a href="#create-spk" class="backend-page-primary-action">
                            <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                            <span>@lang('transport-management.actions.create_spk')</span>
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <div class="backend-page-toolbar transport-management-toolbar">
                    <nav aria-label="{{ __('transport-management.breadcrumb.label') }}">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('view.admin-panel-main') }}">@lang('left-navbar.Admin Panel')</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('transport-management.title')</li>
                        </ol>
                    </nav>
                    <span class="transport-management-date">
                        <i class="icon-copy fa fa-calendar-check-o" aria-hidden="true"></i>
                        {{ dateFormat($today) }}
                    </span>
                </div>

                @if(session('success') || session('error') || $errors->any())
                    <div class="backend-feedback transport-management-feedback">
                        @if(session('success'))
                            <div class="backend-alert backend-alert--success transport-management-alert transport-management-alert--success">
                                <strong>@lang('transport-management.feedback.success')</strong>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="backend-alert backend-alert--danger transport-management-alert transport-management-alert--danger">
                                <strong>@lang('transport-management.feedback.error')</strong>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="backend-alert backend-alert--danger transport-management-alert transport-management-alert--danger">
                                <strong>@lang('transport-management.feedback.validation')</strong>
                                <ul>
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="{{ __('transport-management.stats.label') }}">
                    <article class="backend-kpi-card backend-kpi-card--teal">
                        <div class="backend-kpi-card__icon"><i class="icon-copy fa fa-road" aria-hidden="true"></i></div>
                        <div>
                            <span>@lang('transport-management.stats.active')</span>
                            <strong>{{ number_format($statusSummary['active']) }}</strong>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--amber">
                        <div class="backend-kpi-card__icon"><i class="icon-copy fa fa-clock-o" aria-hidden="true"></i></div>
                        <div>
                            <span>@lang('transport-management.stats.pending')</span>
                            <strong>{{ number_format($statusSummary['pending']) }}</strong>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--blue">
                        <div class="backend-kpi-card__icon"><i class="icon-copy fa fa-refresh" aria-hidden="true"></i></div>
                        <div>
                            <span>@lang('transport-management.stats.in_progress')</span>
                            <strong>{{ number_format($statusSummary['in_progress']) }}</strong>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--green">
                        <div class="backend-kpi-card__icon"><i class="icon-copy fa fa-archive" aria-hidden="true"></i></div>
                        <div>
                            <span>@lang('transport-management.stats.archived')</span>
                            <strong>{{ number_format($statusSummary['archived']) }}</strong>
                        </div>
                    </article>
                </section>

                <div class="transport-management-grid">
                    <section class="backend-panel transport-management-panel transport-management-panel--list">
                        <div class="backend-section-header transport-management-panel__heading">
                            <div>
                                <span class="backend-section-header__label">@lang('transport-management.active.eyebrow')</span>
                                <h2>@lang('transport-management.active.title')</h2>
                            </div>
                            <span class="transport-management-chip">
                                {{ trans_choice('transport-management.active.count', $spks->count(), ['count' => $spks->count()]) }}
                            </span>
                        </div>

                        <div class="backend-table-wrap transport-management-table-wrap">
                            <table class="backend-table transport-management-table">
                                <thead>
                                    <tr>
                                        <th>@lang('transport-management.table.no')</th>
                                        <th>@lang('transport-management.table.date')</th>
                                        <th>@lang('transport-management.table.order')</th>
                                        <th>@lang('transport-management.table.assignment')</th>
                                        <th>@lang('transport-management.table.status')</th>
                                        <th class="text-right">@lang('transport-management.table.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($spks as $spk)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $spk->spk_date ? dateFormat($spk->spk_date) : '-' }}</strong>
                                            </td>
                                            <td>
                                                <strong>{{ $spk->order_number ?? '-' }}</strong>
                                                <span>{{ $spk->type ?? '-' }} / {{ trans_choice('transport-management.table.pax', (int) $spk->number_of_guests, ['count' => (int) $spk->number_of_guests]) }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $spk->spk_number ?? '-' }}</strong>
                                                <span>
                                                    {{ $spk->transport ? trim($spk->transport->brand . ' ' . $spk->transport->name) : __('transport-management.empty.na') }}
                                                    /
                                                    {{ $spk->driver?->name ?? __('transport-management.empty.na') }}
                                                </span>
                                                <small>{{ $spk->plate_number ?: '-' }}</small>
                                            </td>
                                            <td>
                                                <span class="backend-status-badge backend-status-badge--{{ Str::slug($spk->status ?? 'pending') }} transport-management-status transport-management-status--{{ Str::slug($spk->status ?? 'pending') }}">
                                                    {{ $spk->status ?? __('transport-management.empty.na') }}
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                <div class="backend-table-actions">
                                                    <a class="backend-icon-action backend-table-action-view transport-management-row-action" href="{{ route('view.detail-spk', $spk->id) }}">
                                                        <i class="icon-copy dw dw-eye" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">
                                                <div class="backend-table-empty transport-management-empty">
                                                    <i class="icon-copy fa fa-road" aria-hidden="true"></i>
                                                    <strong>@lang('transport-management.empty.active_title')</strong>
                                                    <span>@lang('transport-management.empty.active_message')</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="backend-table-card-list transport-management-card-list" aria-label="{{ __('transport-management.active.mobile_label') }}">
                            @forelse($spks as $spk)
                                <article class="backend-table-card transport-management-card">
                                    <div class="backend-table-card__header transport-management-card__header">
                                        <div>
                                            <span>{{ $spk->spk_date ? dateFormat($spk->spk_date) : '-' }}</span>
                                            <strong>{{ $spk->order_number ?? '-' }}</strong>
                                        </div>
                                        <span class="backend-status-badge backend-status-badge--{{ Str::slug($spk->status ?? 'pending') }} transport-management-status transport-management-status--{{ Str::slug($spk->status ?? 'pending') }}">
                                            {{ $spk->status ?? __('transport-management.empty.na') }}
                                        </span>
                                    </div>
                                    <dl class="backend-table-card-grid">
                                        <div>
                                            <dt>@lang('transport-management.table.service')</dt>
                                            <dd>{{ $spk->type ?? '-' }} / {{ trans_choice('transport-management.table.pax', (int) $spk->number_of_guests, ['count' => (int) $spk->number_of_guests]) }}</dd>
                                        </div>
                                        <div>
                                            <dt>@lang('transport-management.table.assignment')</dt>
                                            <dd>{{ $spk->transport ? trim($spk->transport->brand . ' ' . $spk->transport->name) : __('transport-management.empty.na') }} / {{ $spk->driver?->name ?? __('transport-management.empty.na') }}</dd>
                                        </div>
                                        <div>
                                            <dt>@lang('transport-management.table.spk_number')</dt>
                                            <dd>{{ $spk->spk_number ?? '-' }}</dd>
                                        </div>
                                    </dl>
                                    <a class="backend-table-action backend-table-action-view transport-management-row-action" href="{{ route('view.detail-spk', $spk->id) }}">
                                        <i class="icon-copy dw dw-eye" aria-hidden="true"></i>
                                        <span>@lang('transport-management.actions.detail')</span>
                                    </a>
                                </article>
                            @empty
                                <div class="backend-table-empty transport-management-empty">
                                    <i class="icon-copy fa fa-road" aria-hidden="true"></i>
                                    <strong>@lang('transport-management.empty.active_title')</strong>
                                    <span>@lang('transport-management.empty.active_message')</span>
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <aside class="backend-panel transport-management-panel transport-management-create" id="create-spk">
                        <div class="backend-section-header transport-management-panel__heading">
                            <div>
                                <span class="backend-section-header__label">@lang('transport-management.create.eyebrow')</span>
                                <h2>@lang('transport-management.create.title')</h2>
                            </div>
                        </div>
                        <p class="transport-management-help">@lang('transport-management.create.help')</p>

                        <form class="transport-management-form" action="{{ route('spks.store') }}" method="post" enctype="multipart/form-data" data-transport-management-form>
                            @csrf
                            <label>
                                <span>@lang('transport-management.form.operator') <b>*</b></span>
                                <select class="backend-form-control" name="operator_id" required>
                                    <option disabled selected value="">@lang('transport-management.form.select_operator')</option>
                                    @foreach ($operator as $operatorUser)
                                        <option value="{{ $operatorUser->id }}" @selected(old('operator_id') == $operatorUser->id)>{{ $operatorUser->name }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label>
                                <span>@lang('transport-management.form.order_number') <b>*</b></span>
                                <input class="backend-form-control" type="text" name="order_number" value="{{ old('order_number') }}" placeholder="{{ __('transport-management.form.order_number_placeholder') }}" required>
                            </label>

                            <label>
                                <span>@lang('transport-management.form.spk_date') <b>*</b></span>
                                <input class="backend-form-control" readonly name="spk_date" type="text" value="{{ old('spk_date') }}" placeholder="{{ __('transport-management.form.select_date') }}" required>
                            </label>

                            <label>
                                <span>@lang('transport-management.form.service') <b>*</b></span>
                                <select class="backend-form-control" name="type" required>
                                    <option disabled selected value="">@lang('transport-management.form.select_service')</option>
                                    @foreach (['Airport Shuttle', 'Hotel Transfer', 'Tour', 'Daily Rent'] as $serviceType)
                                        <option value="{{ $serviceType }}" @selected(old('type') === $serviceType)>{{ $serviceType }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label>
                                <span>@lang('transport-management.form.guests') <b>*</b></span>
                                <input class="backend-form-control" name="number_of_guests" min="1" type="number" value="{{ old('number_of_guests') }}" placeholder="{{ __('transport-management.form.guests_placeholder') }}" required>
                            </label>

                            <div class="transport-management-form__split">
                                <label>
                                    <span>@lang('transport-management.form.vehicle') <b>*</b></span>
                                    <select class="backend-form-control" name="transport_id" required>
                                        <option disabled selected value="">@lang('transport-management.form.select_vehicle')</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" @selected(old('transport_id') == $vehicle->id)>
                                                {{ trim($vehicle->brand . ' ' . $vehicle->name) }}{{ $vehicle->number_plate ? ' (' . $vehicle->number_plate . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </label>

                                <label>
                                    <span>@lang('transport-management.form.plate_number') <b>*</b></span>
                                    <input class="backend-form-control" type="text" name="plate_number" value="{{ old('plate_number') }}" placeholder="{{ __('transport-management.form.plate_number_placeholder') }}" required>
                                </label>
                            </div>

                            <label>
                                <span>@lang('transport-management.form.driver') <b>*</b></span>
                                <select class="backend-form-control" name="driver_id" required>
                                    <option disabled selected value="">@lang('transport-management.form.select_driver')</option>
                                    @foreach ($drivers as $driver)
                                        <option value="{{ $driver->id }}" @selected(old('driver_id') == $driver->id)>{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <button type="submit" class="backend-button backend-button-primary transport-management-submit-action" data-processing-label="{{ __('transport-management.actions.creating') }}">
                                <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                                @lang('transport-management.actions.create_spk')
                            </button>
                        </form>
                    </aside>
                </div>

                <section class="backend-panel transport-management-panel transport-management-archive">
                    <div class="backend-section-header transport-management-panel__heading">
                        <div>
                            <span class="backend-section-header__label">@lang('transport-management.archive.eyebrow')</span>
                            <h2>@lang('transport-management.archive.title')</h2>
                        </div>
                        <label class="transport-management-search">
                            <span class="sr-only">@lang('transport-management.archive.search')</span>
                            <i class="icon-copy fa fa-search" aria-hidden="true"></i>
                            <input class="backend-form-control" type="search" id="filter_order_no" placeholder="{{ __('transport-management.archive.search') }}">
                        </label>
                    </div>

                    <div id="spkArchiveResults">
                        @include('admin.transportmanagement.partials.spk-archive', ['spk_archives' => $spk_archives])
                    </div>
                </section>
            </div>
        </main>
    @endcan
@endsection

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/transport-management/index.js') }}"></script>
@endpush
