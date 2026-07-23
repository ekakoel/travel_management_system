@foreach ($hotel->rooms as $room)
    <div class="modal fade hotel-detail-modal" id="hotelRoomDetail{{ $room->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <section class="backend-modal">
                    <div class="backend-modal__header">
                        <div>
                            <span>Room Detail</span>
                            <h3>{{ $room->rooms }}</h3>
                        </div>
                        <button type="button" class="backend-modal__close" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="backend-modal__body">
                        <figure class="hotel-detail-cover">
                            <img src="{{ asset('storage/hotels/hotels-room/' . $room->cover) }}" alt="{{ $room->rooms }}" loading="lazy">
                        </figure>
                        <dl class="hotel-detail-grid">
                            <div><dt>Capacity</dt><dd>{{ $room->capacity_adult }} Adult{{ $room->capacity_child > 0 ? ' + ' . $room->capacity_child . ' Child' : '' }}</dd></div>
                            <div><dt>View</dt><dd>{{ $room->view ?: '-' }}</dd></div>
                            <div><dt>Bed</dt><dd>{{ $room->beds ?: '-' }}</dd></div>
                            <div><dt>Size</dt><dd>{!! $room->size ? $room->size . ' m²' : '-' !!}</dd></div>
                            @if (filled($room->amenities))
                                <div class="is-wide"><dt>Amenities</dt><dd>{!! app()->getLocale() === 'zh' ? $room->amenities_traditional : (app()->getLocale() === 'zh-CN' ? $room->amenities_simplified : $room->amenities) !!}</dd></div>
                            @endif
                            @if (filled($room->additional_info))
                                <div class="is-wide"><dt>Additional Information</dt><dd>{!! app()->getLocale() === 'zh' ? $room->additional_info_traditional : (app()->getLocale() === 'zh-CN' ? $room->additional_info_simplified : $room->additional_info) !!}</dd></div>
                            @endif
                        </dl>
                    </div>
                    <div class="backend-modal__footer">
                        @canany(['posDev','posAuthor'])
                            <a href="{{ route('admin.hotels.rooms.edit', $room->id) }}" class="backend-page-primary-action">Edit Room</a>
                        @endcanany
                        <button type="button" class="backend-toolbar-action" data-dismiss="modal">Close</button>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endforeach
