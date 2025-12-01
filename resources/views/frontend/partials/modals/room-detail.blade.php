<div class="card">
    @php
        $show_promo = $room->promos->where('book_periode_end','>',$now)->first();
        $incomingPromotions = $room->promos->where('book_periode_end', '>=', $now)->sortBy('periode_start');
    @endphp
    <a href="#" data-bs-toggle="modal" data-bs-target="#detail-room-{{ $room->id }}">
        <div class="card-image-container">
            <img 
                class="img-fluid rounded thumbnail-image" 
                src="{{ getThumbnail('/hotels/hotels-room/' . $room->cover,380,200) }}" 
                onerror="this.onerror=null;this.src='{{ getThumbnail('/images/default.webp',380,200) }}';"
                alt="{{ $room->rooms }}">
            <div class="card-detail-title">{{ $room->rooms }}</div>
        </div>
    </a>
    @if ($show_promo)
        <div class="promo-hot-deal">
            <img src="{{ asset('storage/icon/' . $promoImages[$show_promo->promotion_type] ?? 'default_promo.png') }}" 
                alt="{{ $show_promo->promotion_type }} Promotion">
        </div>
    @endif

    <!-- Modal -->
    <div class="modal fade" id="detail-room-{{ $room->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl"> <!-- XL biar lega -->
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <!-- Header dengan Cover -->
                <div class="position-relative">
                    <img src="{{ asset('storage/hotels/hotels-room/' . $room->cover) }}" 
                        alt="{{ $room->rooms }}" 
                        class="w-100" style="max-height: 500px; object-fit: cover;"
                        onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';">
                </div>

                <!-- Body -->
                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                     @php
                        if (config('app.locale') == "zh") {
                            $contents = [
                                'messages.Amenities' => $room->amenities_traditional,
                                'messages.Additional Information' => $room->additional_info_traditional
                            ];
                        } elseif (config('app.locale') == "zh-CN") {
                            $contents = [
                                'messages.Amenities' => $room->amenities_simplified,
                                'messages.Additional Information' => $room->additional_info_simplified
                            ];
                        } else {
                            $contents = [
                                'messages.Amenities' => $room->amenities,
                                'messages.Additional Information' => $room->additional_info
                            ];
                        }
                    @endphp
                    <div class="modal-title">
                        <h4>{{ $room->rooms }}</h4>
                    </div>
                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-1" id="roomTab-{{ $room->id }}" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab-{{ $room->id }}" 
                                    data-bs-toggle="tab" data-bs-target="#details-{{ $room->id }}" 
                                    type="button" role="tab">@lang('messages.Details')</button>
                        </li>
                        @if ($contents['messages.Amenities'])
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="amenities-tab-{{ $room->id }}" 
                                        data-bs-toggle="tab" data-bs-target="#amenities-{{ $room->id }}" 
                                        type="button" role="tab">@lang('messages.Amenities')</button>
                            </li>
                        @endif
                        @if ($incomingPromotions->isNotEmpty())
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="promo-tab-{{ $room->id }}" 
                                        data-bs-toggle="tab" data-bs-target="#promo-{{ $room->id }}" 
                                        type="button" role="tab">@lang('messages.Promotion')</button>
                            </li>
                        @endif
                    </ul>

                    <!-- Tab Contents -->
                    <div class="tab-content" id="roomTabContent-{{ $room->id }}">
                        <!-- Details -->
                        <div class="tab-pane fade show active" id="details-{{ $room->id }}" role="tabpanel">
                            <div class="row g-4">
                                @if ($room->view)
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">@lang('messages.View'):</h6>
                                        <p>{{ $room->view }}</p>
                                    </div>
                                @endif
                                @if ($room->beds)
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">@lang('messages.Beds'):</h6>
                                        <p>{{ $room->beds }}</p>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <h6 class="fw-bold">@lang('messages.Capacity'):</h6>
                                    <p>
                                        <i class="fa fa-user me-1"></i> {{ $room->capacity_adult }} {{ $room->capacity_adult > 1 ? __('messages.adults') : __('messages.adult') }}
                                        @if ($room->capacity_child > 0)
                                            + <i class="fa fa-child me-1"></i> {{ $room->capacity_child }} {{ $room->capacity_child > 1 ? __('messages.children') : __('messages.child') }}
                                        @endif
                                    </p>
                                </div>
                                @if ($room->size)
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">@lang('messages.Size'):</h6>
                                        <p>{{ $room->size }} m²</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Amenities -->
                        @if ($contents['messages.Amenities'])
                            <div class="tab-pane fade" id="amenities-{{ $room->id }}" role="tabpanel">
                                @foreach ($contents as $label => $value)
                                    @if ($value)
                                        <h6 class="fw-bold">@lang($label):</h6>
                                        <p class="mb-1">{!! $value !!}</p>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <!-- Promotions -->
                        @if ($room->promos->count())
                            <div class="tab-pane fade" id="promo-{{ $room->id }}" role="tabpanel">
                                @if ($incomingPromotions->isNotEmpty())
                                    <div class="row g-3">
                                        @foreach ($incomingPromotions as $promotion)
                                            <div class="col-md-6">
                                                @include('frontend.partials.modals.inner-hotel-room-card', ['promotion' => $promotion])
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> @lang('messages.Close')
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
