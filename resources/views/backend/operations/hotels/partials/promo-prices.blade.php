@php
    $promoPriceRows = $hotelDetail->promoRows();
    $promoPriceGroups = $hotelDetail->promoGroups();
@endphp

<section id="promo" class="backend-panel hotel-detail-panel">
    <div class="backend-section-header hotel-detail-panel__heading">
        <div>
            <span class="backend-section-header__label">Promotion</span>
            <h2>Promotion Price</h2>
        </div>
        @canany(['posDev','posAuthor'])
            <div class="hotel-detail-section-actions">
                <a href="{{ route('admin.hotels.promos.create', $hotel->id) }}" class="backend-toolbar-action">
                    <i class="fa fa-plus"></i>
                    Add Promo
                </a>
            </div>
        @endcanany
    </div>
    <div class="hotel-detail-panel__body">
        <section class="backend-filter-panel hotel-detail-filter backend-filter-panel--flush">
            <label class="backend-filter-field">
                <span class="backend-filter-label">Filter promo by name</span>
                <span class="backend-filter-search">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input class="backend-filter-control" type="search" placeholder="Search promo" data-hotel-detail-filter="promo">
                </span>
            </label>
        </section>
    </div>
    <div class="hotel-promo-price-groups">
        @forelse ($promoPriceGroups as $group)
            <section class="hotel-promo-price-group" data-hotel-detail-row="promo" data-hotel-detail-search="{{ $group['search'] }}">
                <div class="hotel-promo-price-group__header">
                    <div>
                        <span>Room</span>
                        <h3>{{ $group['room_name'] }}</h3>
                    </div>
                    <strong>{{ $group['rows']->count() }} {{ $group['rows']->count() === 1 ? 'promo row' : 'promo rows' }}</strong>
                </div>

                <div class="backend-table-wrap hotel-detail-table-wrap">
                    <table class="backend-table hotel-detail-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Booking Period</th>
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
                                    $promo = $row['model'];
                                @endphp
                                <tr data-hotel-detail-row="promo" data-hotel-detail-search="{{ $row['search'] }}">
                                    <td data-label="Name"><strong>{{ $promo->name }}</strong></td>
                                    <td data-label="Booking Period">{{ $row['booking_period'] }}</td>
                                    <td data-label="Stay Period">{{ $row['stay_period'] }}</td>
                                    <td data-label="Published Rate">
                                        <span class="hotel-detail-rate">{{ currencyFormatUsd($row['published_rate']) }}</span>
                                        <button type="button" class="hotel-price-calculation-action" data-toggle="modal" data-target="#hotelPromoPriceCalculation{{ $promo->id }}">
                                            View calculation
                                        </button>
                                    </td>
                                    <td data-label="Status">
                                        <button
                                            type="button"
                                            class="backend-status-toggle {{ $promo->status === 'Active' ? 'is-active' : '' }}"
                                            data-backend-status-toggle
                                            data-backend-status-url="{{ route('admin.hotels.promos.status.update', $promo->id) }}"
                                            data-backend-status-current="{{ $promo->status }}"
                                            data-backend-status-next="{{ $promo->status === 'Active' ? 'Draft' : 'Active' }}"
                                            aria-pressed="{{ $promo->status === 'Active' ? 'true' : 'false' }}"
                                            aria-label="Toggle status for {{ $promo->name }}"
                                            title="{{ $promo->status === 'Active' ? 'Active' : 'Draft' }}"
                                        >
                                            <span class="backend-status-toggle__track" aria-hidden="true">
                                                <span class="backend-status-toggle__knob"></span>
                                            </span>
                                            <span class="backend-status-toggle__label" data-backend-status-toggle-label>{{ $promo->status === 'Active' ? 'Active' : 'Draft' }}</span>
                                        </button>
                                    </td>
                                    @canany(['posDev','posAuthor'])
                                        <td data-label="Action">
                                            <div class="hotel-detail-actions">
                                                <a href="{{ route('admin.hotels.promos.edit', $promo->id) }}" class="backend-icon-action" aria-label="Edit {{ $promo->name }}">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </a>
                                                <form action="{{ route('admin.hotels.promos.destroy', $promo->id) }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                    <input type="hidden" name="hotels_id" value="{{ $hotel->id }}">
                                                    <button type="submit" class="backend-icon-action is-danger" data-hotel-detail-delete="{{ $promo->name }}" aria-label="Delete {{ $promo->name }}">
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
        @empty
            <div class="backend-table-empty">
                <i class="fa fa-percent"></i>
                <strong>No promotion prices.</strong>
                <span>Promotion prices are not configured for this hotel yet.</span>
            </div>
        @endforelse
    </div>
</section>

@foreach ($promoPriceRows as $row)
    @include('backend.operations.hotels.modals.price-calculation', [
        'modalId' => 'hotelPromoPriceCalculation'.$row['model']->id,
        'eyebrow' => 'Promotion Price Calculation',
        'title' => $row['model']->name,
        'subtitle' => $row['room_name'].' | '.$row['stay_period'],
        'pricing' => $row['pricing'],
    ])
@endforeach
