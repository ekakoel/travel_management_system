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
                @forelse ($hotelDetail->promoRows() as $row)
                    @php
                        $promo = $row['model'];
                    @endphp
                    <tr data-hotel-detail-row="promo" data-hotel-detail-search="{{ $row['search'] }}">
                        <td data-label="Name"><strong>{{ $promo->name }}</strong><span>{{ $row['room_name'] }}</span></td>
                        <td data-label="Booking Period">{{ $row['booking_period'] }}</td>
                        <td data-label="Stay Period">{{ $row['stay_period'] }}</td>
                        <td data-label="Published Rate"><span class="hotel-detail-rate">{!! currencyFormatUsd($row['published_rate']) !!}</span></td>
                        <td data-label="Status"><span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $promo->status }}</span></td>
                        @canany(['posDev','posAuthor'])
                            <td data-label="Action">
                                <div class="hotel-detail-actions">
                                    <a href="{{ route('admin.hotels.promos.edit', $promo->id) }}" class="backend-icon-action" aria-label="Edit {{ $promo->name }}">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.hotels.promos.destroy', $promo->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <input type="hidden" name="hotels_id" value="{{ $hotel->id }}">
                                        <button type="submit" class="backend-icon-action is-danger" data-hotel-detail-delete="{{ $promo->name }}" aria-label="Delete {{ $promo->name }}">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endcanany
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="backend-table-empty">
                                <i class="fa fa-percent"></i>
                                <strong>No active promos.</strong>
                                <span>Promotion prices are not configured for this hotel yet.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
