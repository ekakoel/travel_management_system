@extends('layouts.head')

@section('title', __('messages.Transports'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/transports/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/transports/index.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container transports-admin-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="transports-admin-hero"
                    eyebrow="Operations Inventory"
                    title="Transportation"
                    description="Manage vehicle inventory, publication status, capacity, type, and pricing readiness from the standardized backend workspace."
                >
                    @canany(['posDev','posAuthor'])
                        <x-slot name="action">
                            <a href="{{ route('admin.transports.create') }}" class="backend-page-primary-action">
                                <i class="ion-plus-round"></i>
                                Add Transport
                            </a>
                        </x-slot>
                    @endcanany
                </x-backend.page-hero>

                <section class="backend-page-toolbar transports-admin-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Transportation</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--active">{{ $cactivetransports->count() }} Active</span>
                        <span class="backend-status-badge backend-status-badge--draft">{{ $drafttransports->count() }} Draft</span>
                        <span class="backend-status-badge backend-status-badge--muted">{{ $archivetransports->count() }} Archived</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback transports-admin-feedback">
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

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Transport inventory summary">
                    @foreach ($transportIndex->stats() as $stat)
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

                <section class="backend-filter-panel transports-admin-filter">
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by name</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="transportSearchName" class="backend-filter-control" type="search" placeholder="Search transport name" data-transport-filter="name">
                        </span>
                    </label>
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by type</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="transportSearchType" class="backend-filter-control" type="search" placeholder="Search type" data-transport-filter="type">
                        </span>
                    </label>
                </section>

                <section id="activetransports" class="backend-panel transports-admin-panel">
                    <div class="backend-section-header transports-admin-panel__heading">
                        <div>
                            <span class="backend-section-header__label">Transport Directory</span>
                            <h2>Active and Draft Transports</h2>
                        </div>
                        <p>Track vehicle capacity, type, status, and transport package operations from one consistent list.</p>
                    </div>

                    <div class="backend-table-wrap transports-admin-table-wrap">
                        <table id="transportsAdminTable" class="backend-table transports-admin-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transportIndex->rows() as $row)
                                    @php($transport = $row['model'])
                                    <tr data-transport-row data-transport-name="{{ strtolower($transport->name ?? '') }}" data-transport-type="{{ strtolower($transport->type ?? '') }}">
                                        <td data-label="No">{{ $loop->iteration }}</td>
                                        <td data-label="Name"><strong>{{ $transport->name }}</strong><span>{{ $row['brand'] }}</span></td>
                                        <td data-label="Type">{{ $row['type'] }}</td>
                                        <td data-label="Capacity">{{ $row['capacity'] }}</td>
                                        <td data-label="Status">
                                            <span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $transport->status ?: 'Unknown' }}</span>
                                        </td>
                                        <td data-label="Action">
                                            <div class="backend-table-actions">
                                                <a href="{{ route('admin.transports.show', $transport->id) }}" class="backend-icon-action" aria-label="View {{ $transport->name }}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @canany(['posDev','posAuthor'])
                                                    <a href="{{ route('admin.transports.edit', $transport->id) }}" class="backend-icon-action" aria-label="Edit {{ $transport->name }}">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </a>
                                                    <form action="{{ route('admin.transports.destroy', $transport->id) }}" method="post">
                                                        @csrf
                                                        @method('put')
                                                        <input type="hidden" name="author" value="{{ Auth::id() }}">
                                                        <button type="submit" class="backend-icon-action is-danger" data-transport-delete="{{ $transport->name }}" aria-label="Delete {{ $transport->name }}">
                                                            <i class="fa fa-trash-o"></i>
                                                        </button>
                                                    </form>
                                                @endcanany
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="backend-table-empty">
                                                <i class="dw dw-bus"></i>
                                                <strong>No transports available.</strong>
                                                <span>Add a transport package to start managing transportation inventory.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="backend-table-card-list transports-admin-card-list" aria-label="Transport mobile view">
                        @forelse ($transportIndex->rows() as $row)
                            @php($transport = $row['model'])
                            <article class="backend-table-card transports-admin-card" data-transport-row data-transport-name="{{ strtolower($transport->name ?? '') }}" data-transport-type="{{ strtolower($transport->type ?? '') }}">
                                <div class="backend-table-card__header">
                                    <div>
                                        <span class="backend-table-card__label">Transport</span>
                                        <strong>{{ $transport->name }}</strong>
                                    </div>
                                    <span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $transport->status ?: 'Unknown' }}</span>
                                </div>
                                <dl class="backend-table-card-grid">
                                    <div><dt>Brand</dt><dd>{{ $row['brand'] }}</dd></div>
                                    <div><dt>Type</dt><dd>{{ $row['type'] }}</dd></div>
                                    <div><dt>Capacity</dt><dd>{{ $row['capacity'] }}</dd></div>
                                </dl>
                                <div class="backend-table-actions transports-admin-card__actions">
                                    <a href="{{ route('admin.transports.show', $transport->id) }}" class="backend-button backend-button-secondary">View</a>
                                    @canany(['posDev','posAuthor'])
                                        <a href="{{ route('admin.transports.edit', $transport->id) }}" class="backend-button backend-button-primary">Edit</a>
                                    @endcanany
                                </div>
                            </article>
                        @empty
                            <div class="backend-empty-state">
                                <i class="dw dw-bus"></i>
                                <strong>No transports available.</strong>
                                <span>Add a transport package to start managing transportation inventory.</span>
                            </div>
                        @endforelse
                    </div>
                </section>

                @if ($transportIndex->archivedRows()->isNotEmpty())
                    <section id="archivetransports" class="backend-panel transports-admin-panel">
                        <div class="backend-section-header transports-admin-panel__heading">
                            <div>
                                <span class="backend-section-header__label">Archive</span>
                                <h2>Archived Transports</h2>
                            </div>
                        </div>

                        <div class="backend-table-wrap transports-admin-table-wrap">
                            <table class="backend-table transports-admin-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Capacity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transportIndex->archivedRows() as $row)
                                        @php($transport = $row['model'])
                                        <tr>
                                            <td data-label="Name"><strong>{{ $transport->name }}</strong><span>{{ $row['brand'] }}</span></td>
                                            <td data-label="Status">
                                                <span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $transport->status ?: 'Unknown' }}</span>
                                            </td>
                                            <td data-label="Type">{{ $row['type'] }}</td>
                                            <td data-label="Capacity">{{ $row['capacity'] }}</td>
                                            <td data-label="Action">
                                                <div class="backend-table-actions">
                                                    <a href="{{ route('admin.transports.show', $transport->id) }}" class="backend-icon-action" aria-label="View {{ $transport->name }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
