@extends('layouts.head')

@section('title', __('messages.Hotels'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/hotels/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/hotels/index.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        @php
            $hotelCount = $hotels->count();
            $activeCount = $cactivehotels->count();
            $draftCount = $drafthotels->count();
            $archivedCount = $archivehotels->count();
            $roomCount = $activerooms->count();
            $hotelSummary = [
                ['label' => 'Total Hotels', 'value' => $hotelCount, 'meta' => 'Active and draft inventory', 'icon' => 'dw dw-hotel', 'tone' => 'blue'],
                ['label' => 'Active', 'value' => $activeCount, 'meta' => 'Published accommodation', 'icon' => 'fa fa-check-circle', 'tone' => 'green'],
                ['label' => 'Draft', 'value' => $draftCount, 'meta' => 'Needs content or pricing', 'icon' => 'fa fa-pencil-square-o', 'tone' => 'amber'],
                ['label' => 'Active Rooms', 'value' => $roomCount, 'meta' => 'Rooms available to sell', 'icon' => 'fa fa-bed', 'tone' => 'teal'],
            ];
        @endphp

        <div class="mobile-menu-overlay"></div>
        <main class="main-container hotels-admin-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="hotels-admin-hero"
                    eyebrow="Operations Inventory"
                    title="Hotel Manager"
                    description="Manage hotel profiles, room availability, active pricing, promo periods, packages, and archive visibility from one standardized backend workspace."
                >
                    @canany(['posDev','posAuthor'])
                        <x-slot name="action">
                            <a href="{{ route('admin.hotels.create') }}" class="backend-page-primary-action">
                                <i class="ion-plus-round"></i>
                                Add Hotel
                            </a>
                        </x-slot>
                    @endcanany
                </x-backend.page-hero>

                <section class="backend-page-toolbar hotels-admin-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Hotel Manager</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $now->format('d M Y') }}</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('invalid') || session()->has('error'))
                    <section class="backend-feedback hotels-admin-feedback">
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

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Hotel summary">
                    @foreach ($hotelSummary as $stat)
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

                <section class="backend-filter-panel hotels-admin-filter">
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by name</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="hotelSearchName" class="backend-filter-control" type="search" placeholder="Search hotel name" data-hotel-filter="name">
                        </span>
                    </label>
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by location</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="hotelSearchLocation" class="backend-filter-control" type="search" placeholder="Search location" data-hotel-filter="location">
                        </span>
                    </label>
                </section>

                <section class="backend-panel hotels-admin-panel">
                    <div class="backend-section-header hotels-admin-panel__heading">
                        <div>
                            <span class="backend-section-header__label">Hotel Directory</span>
                            <h2>All Hotels</h2>
                        </div>
                        <p>Track content status, room inventory, normal price, promo, and package readiness for every hotel in backend operations.</p>
                    </div>

                    <div class="backend-table-wrap hotels-admin-table-wrap">
                        <table id="hotelsAdminTable" class="backend-table hotels-admin-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Services</th>
                                    <th>Rooms</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($hotels as $hotel)
                                    @php
                                        $normalPrice = $normal_prices->where('hotels_id', $hotel->id)->first();
                                        $promo = $promos->where('hotels_id', $hotel->id)->first();
                                        $package = $packages->where('hotels_id', $hotel->id)->first();
                                        $activeRooms = $hotel->rooms->where('status', 'Active')->count();
                                        $draftRooms = $hotel->rooms->where('status', 'Draft')->count();
                                        $statusClass = strtolower($hotel->status) === 'active' ? 'active' : 'draft';
                                    @endphp
                                    <tr data-hotel-row data-hotel-name="{{ strtolower($hotel->name ?? '') }}" data-hotel-location="{{ strtolower($hotel->region ?? '') }}">
                                        <td data-label="No">{{ $loop->iteration }}</td>
                                        <td data-label="Name">
                                            <strong>{{ $hotel->name }}</strong>
                                        </td>
                                        <td data-label="Location">{{ $hotel->region ?: '-' }}</td>
                                        <td data-label="Services">
                                            <div class="hotels-admin-service">
                                                @if ($normalPrice || $promo || $package)
                                                    
                                                    @if ($normalPrice)
                                                        <span class="{{ $normalPrice ? 'price-normal is-active' : '' }}" title="{{ $normalPrice ? date('d M y', strtotime($normalPrice->end_date)) : 'Normal Price' }}">NP</span>
                                                    @endif
                                                    @if ($promo)
                                                        <span class="{{ $promo ? 'is-active' : '' }}" title="{{ $promo ? date('d M y', strtotime($promo->book_periode_end)) : 'Promo Price' }}">PR</span>
                                                    @endif
                                                    @if ($package)
                                                        <span class="{{ $package ? 'is-active' : '' }}" title="{{ $package ? date('d M y', strtotime($package->stay_period_end)) : 'Package Price' }}">PA</span>
                                                    @endif
                                                @else
                                                    <i>No price available</i>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Rooms">
                                            <div class="hotels-admin-room-count">
                                                <span class="backend-status-badge backend-status-badge--active">{{ $activeRooms }} A</span>
                                                <span class="backend-status-badge backend-status-badge--draft">{{ $draftRooms }} D</span>
                                            </div>
                                        </td>
                                        <td data-label="Status">
                                            <span class="backend-status-badge backend-status-badge--{{ $statusClass }}">{{ $hotel->status }}</span>
                                        </td>
                                        <td data-label="Action">
                                            <div class="backend-table-actions hotels-admin-actions">
                                                <a href="{{ route('admin.hotels.show', $hotel->id) }}" class="backend-icon-action" aria-label="View {{ $hotel->name }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @canany(['posDev','posAuthor'])
                                                    <a href="{{ route('admin.hotels.edit', $hotel->id) }}" class="backend-icon-action" aria-label="Edit {{ $hotel->name }}">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                    <form action="{{ route('admin.hotels.destroy', $hotel->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        <button type="submit" class="backend-icon-action is-danger" data-hotel-delete="{{ $hotel->name }}" aria-label="Delete {{ $hotel->name }}">
                                                            <i class="far fa-trash-alt"></i>
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
                                                <i class="dw dw-hotel"></i>
                                                <strong>No hotels found.</strong>
                                                <span>Add the first hotel profile to start building accommodation inventory.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="backend-table-card-list hotels-admin-card-list">
                        @forelse ($hotels as $hotel)
                            @php
                                $normalPrice = $normal_prices->where('hotels_id', $hotel->id)->first();
                                $promo = $promos->where('hotels_id', $hotel->id)->first();
                                $package = $packages->where('hotels_id', $hotel->id)->first();
                                $activeRooms = $hotel->rooms->where('status', 'Active')->count();
                                $draftRooms = $hotel->rooms->where('status', 'Draft')->count();
                                $statusClass = strtolower($hotel->status) === 'active' ? 'active' : 'draft';
                            @endphp
                            <article class="backend-table-card hotels-admin-card" data-hotel-row data-hotel-name="{{ strtolower($hotel->name ?? '') }}" data-hotel-location="{{ strtolower($hotel->region ?? '') }}">
                                <div class="backend-table-card__header">
                                    <div>
                                        <span>Hotel</span>
                                        <strong>{{ $hotel->name }}</strong>
                                    </div>
                                    <span class="backend-status-badge backend-status-badge--{{ $statusClass }}">{{ $hotel->status }}</span>
                                </div>
                                <dl class="backend-table-card-grid">
                                    <div><dt>Location</dt><dd>{{ $hotel->region ?: '-' }}</dd></div>
                                    <div><dt>Active Rooms</dt><dd>{{ $activeRooms }}</dd></div>
                                    <div><dt>Draft Rooms</dt><dd>{{ $draftRooms }}</dd></div>
                                    <div><dt>Services</dt><dd>{{ $normalPrice ? 'NP' : '-' }} / {{ $promo ? 'PR' : '-' }} / {{ $package ? 'PA' : '-' }}</dd></div>
                                </dl>
                                <div class="backend-table-actions hotels-admin-card__actions">
                                    <a href="{{ route('admin.hotels.show', $hotel->id) }}" class="backend-icon-action" aria-label="View {{ $hotel->name }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @canany(['posDev','posAuthor'])
                                        <a href="{{ route('admin.hotels.edit', $hotel->id) }}" class="backend-icon-action" aria-label="Edit {{ $hotel->name }}">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('admin.hotels.destroy', $hotel->id) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                            <button type="submit" class="backend-icon-action is-danger" data-hotel-delete="{{ $hotel->name }}" aria-label="Delete {{ $hotel->name }}">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        </form>
                                    @endcanany
                                </div>
                            </article>
                        @empty
                            <div class="backend-empty-state">
                                <i class="dw dw-hotel"></i>
                                <strong>No hotels found.</strong>
                                <span>Add the first hotel profile to start building accommodation inventory.</span>
                            </div>
                        @endforelse
                    </div>
                </section>

                @if ($archivedCount > 0)
                    <section id="archivehotels" class="backend-panel hotels-admin-panel hotels-admin-archive">
                        <div class="backend-section-header hotels-admin-panel__heading">
                            <div>
                                <span class="backend-section-header__label">Archive</span>
                                <h2>Archived Hotels</h2>
                            </div>
                            <p>Reference older hotel profiles that are retained for operational history but hidden from active selling workflows.</p>
                        </div>

                        <div class="backend-table-wrap hotels-admin-table-wrap">
                            <table class="backend-table hotels-admin-table hotels-admin-archive-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Location</th>
                                        <th>Rooms</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($archivehotels as $hotel)
                                        @php
                                            $activeArchivedRooms = $hotel->rooms->where('status', 'Active')->count();
                                        @endphp
                                        <tr>
                                            <td data-label="Name"><strong>{{ $hotel->name }}</strong></td>
                                            <td data-label="Status">
                                                <span class="backend-status-badge backend-status-badge--archived">Archived</span>
                                            </td>
                                            <td data-label="Location">{{ $hotel->region ?: '-' }}</td>
                                            <td data-label="Rooms">{{ $activeArchivedRooms }} {{ $activeArchivedRooms === 1 ? 'Room' : 'Rooms' }}</td>
                                            <td data-label="Action">
                                                <div class="backend-table-actions hotels-admin-actions">
                                                    <a href="{{ route('admin.hotels.show', $hotel->id) }}" class="backend-icon-action" aria-label="View {{ $hotel->name }}">
                                                        <i class="fas fa-eye"></i>
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
