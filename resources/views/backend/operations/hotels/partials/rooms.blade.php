<section id="rooms" class="backend-panel hotel-detail-panel">
    <div class="backend-section-header hotel-detail-panel__heading">
        <div>
            <span class="backend-section-header__label">Inventory</span>
            <h2>Suites & Villa</h2>
        </div>
        @canany(['posDev','posAuthor'])
            <div class="hotel-detail-section-actions">
                <a href="{{ route('admin.hotels.rooms.create', $hotel->id) }}" class="backend-toolbar-action">
                    <i class="fa fa-plus"></i>
                    Add Room
                </a>
            </div>
        @endcanany
    </div>
    <div class="hotel-detail-panel__body">
        <div class="hotel-detail-room-list">
            @forelse ($hotel->rooms as $room)
                @php
                    $roomStatusTone = strtolower($room->status) === 'active' ? 'active' : 'draft';
                @endphp
                <article class="hotel-detail-room-card">
                    <img src="{{ asset('storage/hotels/hotels-room/' . $room->cover) }}" alt="{{ $room->rooms }}" loading="lazy">
                    <div class="hotel-detail-room-card__header">
                        <div>
                            <strong>{{ $room->rooms }}</strong>
                            <span>{{ $room->view ?: 'No view data' }}</span>
                        </div>
                        <span class="backend-status-badge backend-status-badge--{{ $roomStatusTone }}">{{ $room->status }}</span>
                    </div>
                    <div class="hotel-detail-room-card__meta">
                        <div><small>Capacity</small><b>{{ $room->capacity_adult }} Adult{{ $room->capacity_child > 0 ? ' + ' . $room->capacity_child . ' Child' : '' }}</b></div>
                        <div><small>Bed</small><b>{{ $room->beds ?: '-' }}</b></div>
                        <div><small>Size</small><b>{!! $room->size ? $room->size . ' m²' : '-' !!}</b></div>
                        <div><small>Status</small><b>{{ $room->status }}</b></div>
                    </div>
                    <div class="hotel-detail-actions">
                        <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#hotelRoomDetail{{ $room->id }}" aria-label="View {{ $room->rooms }}">
                            <i class="fa fa-eye"></i>
                        </button>
                        @canany(['posDev','posAuthor'])
                            <a href="{{ route('admin.hotels.rooms.edit', $room->id) }}" class="backend-icon-action" aria-label="Edit {{ $room->rooms }}">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <form action="{{ route('func.room.delete', $room->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                <input type="hidden" name="hotels_id" value="{{ $hotel->id }}">
                                <button type="submit" class="backend-icon-action is-danger" data-hotel-detail-delete="{{ $room->rooms }}" aria-label="Delete {{ $room->rooms }}">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </form>
                        @endcanany
                    </div>
                </article>
            @empty
                <div class="backend-empty-state">
                    <i class="fa fa-bed"></i>
                    <strong>No rooms yet.</strong>
                    <span>Add the first room type so this hotel can be priced and sold.</span>
                </div>
            @endforelse
        </div>
    </div>
</section>
