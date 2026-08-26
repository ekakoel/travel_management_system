@extends('layouts.head')

@section('title', __('messages.Tour Package'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/tours/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/tours/index.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container tours-admin-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="tours-admin-hero"
                    eyebrow="Operations Inventory"
                    title="Tour Packages"
                    description="Manage tour package content, duration, status, and selling prices from the standardized backend workspace."
                >
                    @canany(['posDev','posAuthor'])
                        <x-slot name="action">
                            <a href="{{ route('admin.tours.create') }}" class="backend-page-primary-action">
                                <i class="ion-plus-round"></i>
                                Add Tour Package
                            </a>
                        </x-slot>
                    @endcanany
                </x-backend.page-hero>

                <section class="backend-page-toolbar tours-admin-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tour Packages</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--active">{{ $activetours->count() }} Active</span>
                        <span class="backend-status-badge backend-status-badge--draft">{{ $drafttours->count() }} Draft</span>
                        <span class="backend-status-badge backend-status-badge--muted">{{ $archivetours->count() }} Archived</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback tours-admin-feedback">
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

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Tour package summary">
                    @foreach ($tourIndex->stats() as $stat)
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

                <section class="backend-filter-panel tours-admin-filter">
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by name</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="tourSearchName" class="backend-filter-control" type="search" placeholder="Search tour name" data-tour-filter="name">
                        </span>
                    </label>
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by code</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="tourSearchCode" class="backend-filter-control" type="search" placeholder="Search code" data-tour-filter="code">
                        </span>
                    </label>
                </section>

                <section class="backend-panel tours-admin-panel">
                    <div class="backend-section-header tours-admin-panel__heading">
                        <div>
                            <span class="backend-section-header__label">Tour Directory</span>
                            <h2>All Tour Packages</h2>
                        </div>
                        <p>Track tour status, itinerary duration, price readiness, and package operations from one consistent list.</p>
                    </div>

                    <div class="backend-table-wrap tours-admin-table-wrap">
                        <table id="toursAdminTable" class="backend-table tours-admin-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Duration</th>
                                    <th>Prices</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tourIndex->rows() as $row)
                                    @php($tour = $row['model'])
                                    <tr data-tour-row data-tour-name="{{ strtolower($tour->name ?? '') }}" data-tour-code="{{ strtolower($tour->code ?? '') }}">
                                        <td data-label="No">{{ $loop->iteration }}</td>
                                        <td data-label="Name"><strong>{{ $tour->name }}</strong><span>{{ $row['type_name'] }}</span></td>
                                        <td data-label="Code">{{ $tour->code ?: '-' }}</td>
                                        <td data-label="Duration">{{ $row['duration'] }}</td>
                                        <td data-label="Prices"><span class="backend-status-badge backend-status-badge--info">{{ $row['price_count'] }} rows</span></td>
                                        <td data-label="Status"><span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $tour->status }}</span></td>
                                        <td data-label="Action">
                                            <div class="backend-table-actions">
                                                <a href="{{ route('admin.tours.show', $tour->id) }}" class="backend-icon-action" aria-label="View {{ $tour->name }}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @canany(['posDev','posAuthor'])
                                                    <a href="{{ route('admin.tours.edit', $tour->id) }}" class="backend-icon-action" aria-label="Edit {{ $tour->name }}">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </a>
                                                    <form action="{{ route('admin.tours.destroy', $tour->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="hidden" name="author" value="{{ Auth::id() }}">
                                                        <button type="submit" class="backend-icon-action is-danger" data-tour-delete="{{ $tour->name }}" aria-label="Delete {{ $tour->name }}">
                                                            <i class="fa fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endcanany
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="backend-table-empty">
                                                <i class="dw dw-map-6"></i>
                                                <strong>No tour packages.</strong>
                                                <span>Add a tour package to start managing tour inventory.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="backend-table-card-list tours-admin-card-list" aria-label="Tour package mobile view">
                        @forelse ($tourIndex->rows() as $row)
                            @php($tour = $row['model'])
                            <article class="backend-table-card tours-admin-card" data-tour-row data-tour-name="{{ strtolower($tour->name ?? '') }}" data-tour-code="{{ strtolower($tour->code ?? '') }}">
                                <div class="backend-table-card__header">
                                    <div>
                                        <span class="backend-table-card__label">Tour Package</span>
                                        <strong>{{ $tour->name }}</strong>
                                    </div>
                                    <span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $tour->status }}</span>
                                </div>
                                <dl class="backend-table-card-grid">
                                    <div><dt>Code</dt><dd>{{ $tour->code ?: '-' }}</dd></div>
                                    <div><dt>Type</dt><dd>{{ $row['type_name'] }}</dd></div>
                                    <div><dt>Duration</dt><dd>{{ $row['duration'] }}</dd></div>
                                    <div><dt>Prices</dt><dd>{{ $row['price_count'] }} rows</dd></div>
                                </dl>
                                <div class="backend-table-actions tours-admin-card__actions">
                                    <a href="{{ route('admin.tours.show', $tour->id) }}" class="backend-button backend-button-secondary">View</a>
                                    @canany(['posDev','posAuthor'])
                                        <a href="{{ route('admin.tours.edit', $tour->id) }}" class="backend-button backend-button-primary">Edit</a>
                                    @endcanany
                                </div>
                            </article>
                        @empty
                            <div class="backend-empty-state">
                                <i class="dw dw-map-6"></i>
                                <strong>No tour packages.</strong>
                                <span>Add a tour package to start managing tour inventory.</span>
                            </div>
                        @endforelse
                    </div>
                </section>

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
