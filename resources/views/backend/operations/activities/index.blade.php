@extends('layouts.head')

@section('title', __('messages.Activities Admin'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/activities/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/activities/index.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev','posAuthor','posRsv','posAdm'])
        <div class="mobile-menu-overlay"></div>
        <main class="main-container activities-admin-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    eyebrow="Operations Inventory"
                    title="Activities"
                    description="Manage activity products, partner ownership, selling status, capacity, validity, and gallery assets from one backend workspace."
                >
                    @canany(['posDev','posAuthor','posAdm'])
                        <x-slot name="action">
                            <a href="{{ route('admin.activities.create') }}" class="backend-page-primary-action">
                                <i class="fa fa-plus"></i>
                                Add Activity
                            </a>
                        </x-slot>
                    @endcanany
                </x-backend.page-hero>

                <section class="backend-page-toolbar activities-admin-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Activities</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--active">{{ $cactiveactivities->count() }} Active</span>
                        <span class="backend-status-badge backend-status-badge--draft">{{ $draftactivities->count() }} Draft</span>
                        <span class="backend-status-badge backend-status-badge--muted">{{ $archiveactivities->count() }} Archived</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback activities-admin-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger">
                                <strong>Action needs attention.</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session()->has('success'))
                            <div class="backend-alert backend-alert--success">
                                <strong>{{ session('success') }}</strong>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ session('error') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Activities summary">
                    @foreach ($activityIndex->stats() as $stat)
                        <article class="backend-kpi-card backend-kpi-card--{{ $stat['tone'] }}">
                            <div class="backend-kpi-card__icon"><i class="{{ $stat['icon'] }}"></i></div>
                            <div>
                                <span>{{ $stat['label'] }}</span>
                                <strong>{{ number_format($stat['value']) }}</strong>
                                <small>{{ $stat['meta'] }}</small>
                            </div>
                        </article>
                    @endforeach
                </section>

                <section class="backend-panel activities-admin-panel">
                    <div class="backend-section-header activities-admin-panel__heading">
                        <div>
                            <span class="backend-section-header__label">Activity Directory</span>
                            <h2>Activity Products</h2>
                        </div>
                    </div>

                    <div class="backend-table-wrap activities-admin-table-wrap">
                        <table class="backend-table activities-admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Partner</th>
                                    <th>Location</th>
                                    <th>Calculated Price</th>
                                    <th>Valid Until</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activityIndex->rows() as $row)
                                    @php
                                        $activity = $row['model'];
                                    @endphp
                                    <tr data-activity-row>
                                        <td data-label="Name"><strong>{{ $activity->name }}</strong><span>{{ $activity->type ?: '-' }}</span></td>
                                        <td data-label="Partner">{{ $row['partner_name'] }}</td>
                                        <td data-label="Location">{{ $activity->location ?: '-' }}</td>
                                        <td data-label="Calculated Price">
                                            @if ($row['price_available'])
                                                {!! currencyFormatUsd($row['published_rate']) !!}
                                                <small class="d-block text-muted">{{ currencyFormatIdr($row['published_rate_idr']) }}</small>
                                            @else
                                                <span class="backend-status-badge backend-status-badge--muted" title="{{ $row['price_unavailable_code'] }}">{{ __('messages.Price cannot be calculated.') }}</span>
                                                <small class="d-block text-muted">{{ $row['price_unavailable_message'] }}</small>
                                            @endif
                                        </td>
                                        <td data-label="Valid Until">{{ $activity->validity ? dateFormat($activity->validity) : '-' }}</td>
                                        <td data-label="Status"><span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $activity->status }}</span></td>
                                        <td data-label="Action">
                                            <div class="backend-table-actions">
                                                <a href="{{ route('admin.activities.show', $activity->id) }}" class="backend-icon-action" aria-label="View {{ $activity->name }}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.activities.edit', $activity->id) }}" class="backend-icon-action" aria-label="Edit {{ $activity->name }}">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </a>
                                                @can('posDev')
                                                    <form action="{{ route('admin.activities.destroy', $activity->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="backend-icon-action is-danger" data-activity-delete="{{ $activity->name }}" aria-label="Delete {{ $activity->name }}">
                                                            <i class="fa fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="backend-table-empty">
                                                <i class="fa fa-child"></i>
                                                <strong>No activity products.</strong>
                                                <span>Add an activity product to start managing activity inventory.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="backend-table-card-list activities-admin-card-list" aria-label="Activity products mobile view">
                        @forelse ($activityIndex->rows() as $row)
                            @php
                                $activity = $row['model'];
                            @endphp
                            <article class="backend-table-card activities-admin-card" data-activity-row>
                                <div class="backend-table-card__header">
                                    <div>
                                        <span class="backend-table-card__label">Activity</span>
                                        <strong>{{ $activity->name }}</strong>
                                    </div>
                                    <span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $activity->status }}</span>
                                </div>
                                <dl class="backend-table-card-grid">
                                    <div>
                                        <dt>Partner</dt>
                                        <dd>{{ $row['partner_name'] }}</dd>
                                    </div>
                                    <div>
                                        <dt>Location</dt>
                                        <dd>{{ $activity->location ?: '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Type</dt>
                                        <dd>{{ $activity->type ?: '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Calculated Price</dt>
                                        <dd>
                                            @if ($row['price_available'])
                                                {!! currencyFormatUsd($row['published_rate']) !!}
                                                <small class="d-block text-muted">{{ currencyFormatIdr($row['published_rate_idr']) }}</small>
                                            @else
                                                <span class="backend-status-badge backend-status-badge--muted" title="{{ $row['price_unavailable_code'] }}">{{ __('messages.Price cannot be calculated.') }}</span>
                                                <small class="d-block text-muted">{{ $row['price_unavailable_message'] }}</small>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt>Valid Until</dt>
                                        <dd>{{ $activity->validity ? dateFormat($activity->validity) : '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Capacity</dt>
                                        <dd>{{ number_format((int) $activity->qty) }} pax</dd>
                                    </div>
                                </dl>
                                <div class="backend-table-actions activities-admin-card__actions">
                                    <a href="{{ route('admin.activities.show', $activity->id) }}" class="backend-button backend-button-secondary">View</a>
                                    @canany(['posDev','posAuthor'])
                                        <a href="{{ route('admin.activities.edit', $activity->id) }}" class="backend-button backend-button-primary">Edit</a>
                                    @endcanany
                                </div>
                            </article>
                        @empty
                            <div class="backend-empty-state">
                                <i class="fa fa-child"></i>
                                <strong>No activity products.</strong>
                                <span>Add an activity product to start managing activity inventory.</span>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </main>
    @endcanany
@endsection
