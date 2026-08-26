@foreach ($hotel->rooms as $room)
    @php
        $roomStatusTone = strtolower((string) $room->status) === 'active' ? 'active' : 'draft';
        $adultCapacity = (int) ($room->capacity_adult ?? 0);
        $childCapacity = (int) ($room->capacity_child ?? 0);
        $totalCapacity = $adultCapacity + $childCapacity;
        $localizedIncludes = app()->getLocale() === 'zh'
            ? ($room->include_traditional ?: $room->include)
            : (app()->getLocale() === 'zh-CN' ? ($room->include_simplified ?: $room->include) : $room->include);
        $localizedAmenities = app()->getLocale() === 'zh'
            ? ($room->amenities_traditional ?: $room->amenities)
            : (app()->getLocale() === 'zh-CN' ? ($room->amenities_simplified ?: $room->amenities) : $room->amenities);
        $localizedAdditionalInfo = app()->getLocale() === 'zh'
            ? ($room->additional_info_traditional ?: $room->additional_info)
            : (app()->getLocale() === 'zh-CN' ? ($room->additional_info_simplified ?: $room->additional_info) : $room->additional_info);
    @endphp

    <div class="modal fade hotel-detail-modal" id="hotelRoomDetail{{ $room->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <section class="backend-modal">
                    <div class="backend-modal__header">
                        <div>
                            <span>Room Detail</span>
                            <h3>{{ $room->rooms }}</h3>
                        </div>
                        <button type="button" class="backend-modal-close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                    </div>
                    <div class="backend-modal__body">
                        <div class="backend-modal-detail hotel-room-detail-modal-layout">
                            <figure class="backend-modal-detail__media">
                                @if ($room->cover)
                                    <img src="{{ asset('storage/hotels/hotels-room/' . $room->cover) }}" alt="{{ $room->rooms }}" loading="lazy" decoding="async">
                                @else
                                    <div class="backend-modal-detail__media-empty" aria-label="No cover image available">
                                        <span>No cover image available</span>
                                    </div>
                                @endif
                                <figcaption>Primary Room cover used by Hotel inventory and author review.</figcaption>
                            </figure>

                            <div class="backend-modal-detail__content">
                                <section class="backend-modal-detail__summary">
                                    <div class="hotel-room-detail-modal__heading">
                                        <h4>{{ $room->rooms }}</h4>
                                        <span id="hotelRoomModalStatusBadge{{ $room->id }}" class="backend-status-badge backend-status-badge--{{ $roomStatusTone }}">{{ $room->status ?: 'Draft' }}</span>
                                    </div>
                                    <p>Author-facing summary of Room master data before pricing, promotions, and package setup.</p>
                                </section>

                                <dl class="backend-modal-detail__grid">
                                    <div>
                                        <dt>Adult Capacity</dt>
                                        <dd>{{ $adultCapacity }}</dd>
                                    </div>
                                    <div>
                                        <dt>Child Capacity</dt>
                                        <dd>{{ $childCapacity }}</dd>
                                    </div>
                                    <div>
                                        <dt>Total Guests</dt>
                                        <dd>{{ $totalCapacity }}</dd>
                                    </div>
                                    <div>
                                        <dt>Inventory</dt>
                                        <dd>{{ $room->inventory ?? 0 }}</dd>
                                    </div>
                                    <div>
                                        <dt>Room View</dt>
                                        <dd>{{ $room->view ?: '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Bed Type</dt>
                                        <dd>{{ $room->beds ?: '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Room Size</dt>
                                        <dd>{{ $room->size ?: '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Room ID</dt>
                                        <dd>#{{ $room->id }}</dd>
                                    </div>
                                </dl>

                                @if (filled($localizedIncludes))
                                    <section class="backend-modal-detail__section">
                                        <span>Includes</span>
                                        <div>{!! $localizedIncludes !!}</div>
                                    </section>
                                @endif

                                @if (filled($localizedAmenities))
                                    <section class="backend-modal-detail__section">
                                        <span>Amenities</span>
                                        <div>{!! $localizedAmenities !!}</div>
                                    </section>
                                @endif

                                @if (filled($localizedAdditionalInfo))
                                    <section class="backend-modal-detail__section">
                                        <span>Additional Information</span>
                                        <div>{!! $localizedAdditionalInfo !!}</div>
                                    </section>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="backend-modal__footer">
                        @canany(['posDev','posAuthor'])
                            <a href="{{ route('admin.hotels.room.edit', $room->id) }}" class="backend-page-primary-action">Edit Room</a>
                        @endcanany
                    </div>
                </section>
            </div>
        </div>
    </div>
@endforeach
