@extends('layouts.head')

@section('title', __('messages.Driver'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/drivers/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/drivers/index.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev','posAuthor','posRsv'])
        @php
            $driverCount = $drivers->count();
            $reviewedCount = $drivers->filter(fn ($driver) => (float) ($driver->global_rating ?? 0) > 0)->count();
            $activeCount = $drivers->filter(fn ($driver) => strtolower($driver->status ?? 'active') === 'active')->count();
            $licenseCount = $drivers->filter(fn ($driver) => filled($driver->license))->count();
            $driverSummary = [
                ['label' => 'Total Drivers', 'value' => $driverCount, 'meta' => 'Registered driver profiles', 'icon' => 'fa fa-users', 'tone' => 'teal'],
                ['label' => 'Active', 'value' => $activeCount, 'meta' => 'Available for assignment', 'icon' => 'fa fa-check-circle', 'tone' => 'green'],
                ['label' => 'Reviewed', 'value' => $reviewedCount, 'meta' => 'Drivers with review score', 'icon' => 'fa fa-star', 'tone' => 'amber'],
                ['label' => 'Licensed', 'value' => $licenseCount, 'meta' => 'Profiles with license data', 'icon' => 'fa fa-id-card-o', 'tone' => 'blue'],
            ];
        @endphp

        <div class="mobile-menu-overlay"></div>
        <main class="main-container drivers-admin-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="drivers-admin-hero"
                    eyebrow="Operations Resource"
                    title="Driver Manager"
                    description="Manage driver profiles, contact details, license information, availability status, and review performance used by transport operations."
                >
                    @canany(['posDev','posAuthor','posRsv'])
                        <x-slot name="action">
                            <button type="button" class="backend-page-primary-action" data-toggle="modal" data-target="#driverAddModal">
                                <i class="fa fa-plus"></i>
                                Add Driver
                            </button>
                        </x-slot>
                    @endcanany
                </x-backend.page-hero>

                <section class="backend-page-toolbar drivers-admin-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Driver Manager</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $now->format('d M Y') }}</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('invalid') || session()->has('error'))
                    <section class="backend-feedback drivers-admin-feedback">
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

                        @if (session()->has('invalid') || session()->has('error'))
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ session('invalid') ?? session('error') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Driver summary">
                    @foreach ($driverSummary as $stat)
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

                <section class="backend-filter-panel drivers-admin-filter">
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by name</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="driverSearchName" class="backend-filter-control" type="search" placeholder="Search driver name" data-driver-filter="name">
                        </span>
                    </label>
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by license</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="driverSearchLicense" class="backend-filter-control" type="search" placeholder="Search license" data-driver-filter="license">
                        </span>
                    </label>
                </section>

                <section class="backend-panel drivers-admin-panel">
                    <div class="backend-section-header drivers-admin-panel__heading">
                        <div>
                            <span class="backend-section-header__label">Driver Directory</span>
                            <h2>All Drivers</h2>
                        </div>
                        <p>Review contact details, license data, active status, and rating performance for transport assignment.</p>
                    </div>

                    <div class="backend-table-wrap drivers-admin-table-wrap">
                        <table id="driversAdminTable" class="backend-table drivers-admin-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>License</th>
                                    <th>Status</th>
                                    <th>Rating</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($drivers as $driver)
                                    @php
                                        $avg = $driver->averageRating();
                                        $summary = $driver->reviewSummary();
                                        $rating = (float) ($summary['global_rating'] ?? 0);
                                        $fullStars = (int) floor($rating);
                                        $halfStar = ($rating - $fullStars) >= 0.5;
                                        $emptyStars = max(0, 5 - $fullStars - ($halfStar ? 1 : 0));
                                        $status = $driver->status ?: 'Active';
                                        $statusClass = strtolower($status) === 'active' ? 'active' : 'inactive';
                                    @endphp
                                    <tr data-driver-row data-driver-name="{{ strtolower($driver->name ?? '') }}" data-driver-license="{{ strtolower($driver->license ?? '') }}">
                                        <td data-label="No">{{ $loop->iteration }}</td>
                                        <td data-label="Name">
                                            <div class="drivers-admin-person">
                                                <img src="{{ asset('storage/user/profile/default_user_img.png') }}" alt="{{ $driver->name }}">
                                                <div>
                                                    <strong>{{ $driver->name }}</strong>
                                                    <span>{{ $driver->country ?: '-' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Contact">
                                            <strong>{{ $driver->phone ?: '-' }}</strong>
                                            <span>{{ $driver->email ?: '-' }}</span>
                                        </td>
                                        <td data-label="License">
                                            <span class="backend-status-badge backend-status-badge--info drivers-admin-license">{{ $driver->license ?: '-' }}</span>
                                        </td>
                                        <td data-label="Status">
                                            <span class="backend-status-badge backend-status-badge--{{ $statusClass }}">{{ $status }}</span>
                                        </td>
                                        <td data-label="Rating">
                                            <div class="drivers-admin-rating" aria-label="Driver rating {{ number_format($rating, 1) }}">
                                                @for ($i = 0; $i < $fullStars; $i++)
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                @endfor
                                                @if ($halfStar)
                                                    <i class="fa fa-star-half-o" aria-hidden="true"></i>
                                                @endif
                                                @for ($i = 0; $i < $emptyStars; $i++)
                                                    <i class="fa fa-star-o" aria-hidden="true"></i>
                                                @endfor
                                                <strong>{{ number_format($rating, 1) }}</strong>
                                                <span>{{ number_format($summary['count'] ?? 0) }} reviews</span>
                                            </div>
                                        </td>
                                        <td data-label="Action">
                                            <div class="backend-table-actions drivers-admin-actions">
                                                <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#driverDetail{{ $driver->id }}" aria-label="View {{ $driver->name }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @canany(['posDev','posAuthor'])
                                                    <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#driverEdit{{ $driver->id }}" aria-label="Edit {{ $driver->name }}">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <form id="destroyDriver{{ $driver->id }}" action="{{ route('destroy-driver', $driver->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="backend-icon-action is-danger" data-driver-delete="{{ $driver->name }}" aria-label="Delete {{ $driver->name }}">
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
                                                <i class="fa fa-user-circle-o"></i>
                                                <strong>No drivers found.</strong>
                                                <span>Add the first driver profile to start assigning drivers to transport operations.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="backend-table-card-list drivers-admin-card-list">
                        @forelse ($drivers as $driver)
                            @php
                                $summary = $driver->reviewSummary();
                                $rating = (float) ($summary['global_rating'] ?? 0);
                                $status = $driver->status ?: 'Active';
                                $statusClass = strtolower($status) === 'active' ? 'active' : 'inactive';
                            @endphp
                            <article class="backend-table-card drivers-admin-card" data-driver-row data-driver-name="{{ strtolower($driver->name ?? '') }}" data-driver-license="{{ strtolower($driver->license ?? '') }}">
                                <div class="backend-table-card__header">
                                    <div>
                                        <span>Driver</span>
                                        <strong>{{ $driver->name }}</strong>
                                    </div>
                                    <span class="backend-status-badge backend-status-badge--{{ $statusClass }}">{{ $status }}</span>
                                </div>
                                <dl class="backend-table-card-grid">
                                    <div><dt>Phone</dt><dd>{{ $driver->phone ?: '-' }}</dd></div>
                                    <div><dt>Email</dt><dd>{{ $driver->email ?: '-' }}</dd></div>
                                    <div><dt>License</dt><dd>{{ $driver->license ?: '-' }}</dd></div>
                                    <div><dt>Rating</dt><dd>{{ number_format($rating, 1) }} / 5</dd></div>
                                </dl>
                                <div class="backend-table-actions drivers-admin-card__actions">
                                    <button type="button" class="backend-button backend-button-secondary" data-toggle="modal" data-target="#driverDetail{{ $driver->id }}">View</button>
                                    @canany(['posDev','posAuthor'])
                                        <button type="button" class="backend-button backend-button-primary" data-toggle="modal" data-target="#driverEdit{{ $driver->id }}">Edit</button>
                                    @endcanany
                                </div>
                            </article>
                        @empty
                            <div class="backend-empty-state">
                                <i class="fa fa-user-circle-o"></i>
                                <strong>No drivers found.</strong>
                                <span>Add the first driver profile to start assigning drivers to transport operations.</span>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </main>

        @foreach ($drivers as $driver)
            @php
                $avg = $driver->averageRating();
                $summary = $driver->reviewSummary();
                $rating = (float) ($summary['global_rating'] ?? 0);
                $status = $driver->status ?: 'Active';
                $statusClass = strtolower($status) === 'active' ? 'active' : 'inactive';
            @endphp

            <div class="modal fade backend-modal drivers-admin-modal" id="driverDetail{{ $driver->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="backend-modal__header">
                            <div>
                                <span>Driver Detail</span>
                                <h3>{{ $driver->name }}</h3>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="backend-modal__body">
                            <div class="drivers-admin-detail">
                                <aside class="drivers-admin-profile">
                                    <img src="{{ asset('storage/user/profile/default_user_img.png') }}" alt="{{ $driver->name }}">
                                    <strong>{{ $driver->name }}</strong>
                                    <span class="backend-status-badge backend-status-badge--{{ $statusClass }}">{{ $status }}</span>
                                    <small>{{ number_format($rating, 1) }} / 5 from {{ number_format($summary['count'] ?? 0) }} reviews</small>
                                </aside>
                                <dl class="drivers-admin-detail-grid">
                                    <div><dt>Phone</dt><dd>{{ $driver->phone ?: '-' }}</dd></div>
                                    <div><dt>Email</dt><dd>{{ $driver->email ?: '-' }}</dd></div>
                                    <div><dt>License</dt><dd>{{ $driver->license ?: '-' }}</dd></div>
                                    <div><dt>Country</dt><dd>{{ $driver->country ?: '-' }}</dd></div>
                                    <div><dt>Address</dt><dd>{{ $driver->address ?: '-' }}</dd></div>
                                    <div><dt>Punctuality</dt><dd>{{ number_format((float) ($avg->driver_punctuality ?? 0), 1) }} ★</dd></div>
                                    <div><dt>Driving Skills</dt><dd>{{ number_format((float) ($avg->driver_driving_skills ?? 0), 1) }} ★</dd></div>
                                    <div><dt>Neatness</dt><dd>{{ number_format((float) ($avg->driver_neatness ?? 0), 1) }} ★</dd></div>
                                </dl>
                            </div>
                        </div>
                        <div class="backend-modal__footer">
                            <button type="button" class="backend-button backend-button-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            @canany(['posDev','posAuthor'])
                <div class="modal fade backend-modal drivers-admin-modal" id="driverEdit{{ $driver->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <div class="backend-modal__header">
                                <div>
                                    <span>Edit Driver</span>
                                    <h3>{{ $driver->name }}</h3>
                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="updateDriver{{ $driver->id }}" action="{{ route('edit-driver', $driver->id) }}" method="post">
                                @csrf
                                <div class="backend-modal__body">
                                    @include('backend.operations.drivers.partials.form', ['driver' => $driver])
                                </div>
                            </form>
                            <div class="backend-modal__footer">
                                <button type="submit" form="updateDriver{{ $driver->id }}" class="backend-button backend-button-primary">
                                    <i class="fa fa-check"></i>
                                    Save
                                </button>
                                <button type="button" class="backend-button backend-button-danger" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endcanany
        @endforeach

        @canany(['posDev','posAuthor','posRsv'])
            <div class="modal fade backend-modal drivers-admin-modal" id="driverAddModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="backend-modal__header">
                            <div>
                                <span>Add Driver</span>
                                <h3>Create driver profile</h3>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="addDriver" method="post" action="{{ route('create-driver') }}">
                            @csrf
                            <div class="backend-modal__body">
                                @include('backend.operations.drivers.partials.form', ['driver' => null])
                            </div>
                        </form>
                        <div class="backend-modal__footer">
                            <button type="submit" form="addDriver" class="backend-button backend-button-primary">
                                <i class="fa fa-check"></i>
                                Save
                            </button>
                            <button type="button" class="backend-button backend-button-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany
    @endcanany
@endsection
