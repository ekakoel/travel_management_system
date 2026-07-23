<section id="normalPrice" class="backend-panel hotel-detail-panel">
    <div class="backend-section-header hotel-detail-panel__heading">
        <div>
            <span class="backend-section-header__label">Rate Plan</span>
            <h2>Normal Price</h2>
        </div>
        @canany(['posDev','posAuthor'])
            <div class="hotel-detail-section-actions">
                <a href="{{ route('admin.hotels.prices.create', $hotel->id) }}" class="backend-toolbar-action">
                    <i class="fa fa-plus"></i>
                    Add Price
                </a>
            </div>
        @endcanany
    </div>
    <div class="hotel-detail-panel__body">
        <section class="backend-filter-panel hotel-detail-filter backend-filter-panel--flush">
            <label class="backend-filter-field">
                <span class="backend-filter-label">Filter price by room</span>
                <span class="backend-filter-search">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input class="backend-filter-control" type="search" placeholder="Search room" data-hotel-detail-filter="price">
                </span>
            </label>
        </section>
    </div>
    <div class="backend-table-wrap hotel-detail-table-wrap">
        <table class="backend-table hotel-detail-table">
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Stay Period</th>
                    <th>Kick Back</th>
                    <th>Published Rate</th>
                    @canany(['posDev','posAuthor'])
                        <th>Action</th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @forelse ($hotelDetail->normalPriceRows() as $row)
                    @php
                        $price = $row['model'];
                    @endphp
                    <tr data-hotel-detail-row="price" data-hotel-detail-search="{{ $row['search'] }}">
                        <td data-label="Room"><strong>{{ $row['room_name'] }}</strong></td>
                        <td data-label="Stay Period">{{ $row['period'] }}</td>
                        <td data-label="Kick Back">{!! $row['kick_back_label'] !!}</td>
                        <td data-label="Published Rate"><span class="hotel-detail-rate">{!! currencyFormatUsd($row['published_rate']) !!}</span></td>
                        @canany(['posDev','posAuthor'])
                            <td data-label="Action">
                                <div class="hotel-detail-actions">
                                    <a href="{{ route('admin.hotels.prices.edit', $price->id) }}" class="backend-icon-action" aria-label="Edit price">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.hotels.normal-prices.destroy', $price->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="backend-icon-action is-danger" data-hotel-detail-delete="{{ $row['room_name'] ?: 'price row' }}" aria-label="Delete price">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endcanany
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="backend-table-empty">
                                <i class="fa fa-usd"></i>
                                <strong>No active normal prices.</strong>
                                <span>Add a normal price to make rooms sellable.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
