@php
    $packagePriceRows = $hotelDetail->packageRows();
    $packagePriceGroups = $hotelDetail->packageGroups();
@endphp

<section id="package" class="backend-panel hotel-detail-panel">
    <div class="backend-section-header hotel-detail-panel__heading">
        <div>
            <span class="backend-section-header__label">Bundled Offer</span>
            <h2>Package Price</h2>
        </div>
        @canany(['posDev','posAuthor'])
            <div class="hotel-detail-section-actions">
                <a href="{{ route('admin.hotels.packages.create', $hotel->id) }}" class="backend-toolbar-action">
                    <i class="fa fa-plus"></i>
                    Add Package
                </a>
            </div>
        @endcanany
    </div>
    <div class="hotel-detail-panel__body">
        <section class="backend-filter-panel hotel-detail-filter backend-filter-panel--flush">
            <label class="backend-filter-field">
                <span class="backend-filter-label">Filter package by name</span>
                <span class="backend-filter-search">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input class="backend-filter-control" type="search" placeholder="Search package" data-hotel-detail-filter="package">
                </span>
            </label>
        </section>
    </div>
    @if ($packagePriceGroups->isNotEmpty())
        <div class="hotel-package-price-groups">
            @foreach ($packagePriceGroups as $group)
                <section class="hotel-package-price-group" data-hotel-detail-row="package" data-hotel-detail-search="{{ $group['search'] }}">
                    <div class="hotel-package-price-group__header">
                        <div>
                            <span>Room</span>
                            <h3>{{ $group['room_name'] }}</h3>
                        </div>
                        <strong>{{ $group['rows']->count() }} package row(s)</strong>
                    </div>
                    <div class="backend-table-wrap hotel-detail-table-wrap">
                        <table class="backend-table hotel-detail-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Duration</th>
                                    <th>Stay Period</th>
                                    <th>Published Rate</th>
                                    <th>Status</th>
                                    @canany(['posDev','posAuthor'])
                                        <th>Action</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($group['rows'] as $row)
                                    @php
                                        $package = $row['model'];
                                    @endphp
                                    <tr data-hotel-detail-row="package" data-hotel-detail-search="{{ $row['search'] }}">
                                        <td data-label="Name"><strong>{{ $package->name }}</strong>
                                            <span>{{ $row['room_name'] }}</span>
                                        </td>
                                        <td data-label="Duration">
                                            <span>{{ $package->booking_code ?: '-' }}</span>
                                            {{ $package->duration }} Night
                                        </td>
                                        <td data-label="Stay Period">{{ $row['stay_period'] }}</td>
                                        <td data-label="Published Rate">
                                            <span class="hotel-detail-rate">{{ currencyFormatUsd($row['published_rate']) }}</span>
                                            <span>Per night</span>
                                            <span>Package total: {{ currencyFormatUsd($row['package_total_rate']) }}</span>
                                            <button type="button" class="hotel-price-calculation-action" data-toggle="modal" data-target="#hotelPackagePriceCalculation{{ $package->id }}">
                                                View calculation
                                            </button>
                                        </td>
                                        <td data-label="Status">
                                            @canany(['posDev','posAuthor'])
                                                <button
                                                    type="button"
                                                    class="backend-status-toggle {{ $package->status === 'Active' ? 'is-active' : '' }}"
                                                    data-backend-status-toggle
                                                    data-backend-status-url="{{ route('admin.hotels.packages.status.update', $package->id) }}"
                                                    data-backend-status-current="{{ $package->status }}"
                                                    data-backend-status-next="{{ $package->status === 'Active' ? 'Draft' : 'Active' }}"
                                                    aria-pressed="{{ $package->status === 'Active' ? 'true' : 'false' }}"
                                                    aria-label="Toggle status for {{ $package->name }}"
                                                    title="{{ $package->status === 'Active' ? 'Active' : 'Draft' }}"
                                                >
                                                    <span class="backend-status-toggle__track" aria-hidden="true">
                                                        <span class="backend-status-toggle__knob"></span>
                                                    </span>
                                                    <span class="backend-status-toggle__label" data-backend-status-toggle-label>{{ $package->status === 'Active' ? 'Active' : 'Draft' }}</span>
                                                </button>
                                            @else
                                                <span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $package->status }}</span>
                                            @endcanany
                                        </td>
                                        @canany(['posDev','posAuthor'])
                                            <td data-label="Action">
                                                <div class="hotel-detail-actions">
                                                    <a href="{{ route('admin.hotels.packages.edit', $package->id) }}" class="backend-icon-action" aria-label="Edit {{ $package->name }}">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </a>
                                                    <form action="{{ route('admin.hotels.packages.destroy', $package->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        <input type="hidden" name="hotels_id" value="{{ $hotel->id }}">
                                                        <button type="submit" class="backend-icon-action is-danger" data-hotel-detail-delete="{{ $package->name }}" aria-label="Delete {{ $package->name }}">
                                                            <i class="fa fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endforeach
        </div>
    @else
        <div class="backend-table-empty">
            <i class="fa fa-cubes"></i>
            <strong>No package prices.</strong>
            <span>Package prices are not configured for this hotel yet.</span>
        </div>
    @endif
</section>

@foreach ($packagePriceRows as $row)
    @include('backend.operations.hotels.modals.price-calculation', [
        'modalId' => 'hotelPackagePriceCalculation'.$row['model']->id,
        'eyebrow' => 'Package Price Calculation',
        'title' => $row['model']->name,
        'subtitle' => $row['room_name'].' | '.$row['model']->duration.' nights',
        'pricing' => $row['pricing'],
    ])
@endforeach
