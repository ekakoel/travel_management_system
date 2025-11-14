<div class="card">
    <a href="#" data-toggle="modal" data-target="#detail-room-{{ $room->id }}">
        <div class="card-image-container">
            <img 
                class="img-fluid rounded thumbnail-image" 
                src="{{ getThumbnail('/hotels/hotels-room/' . $room->cover,380,200) }}" 
                alt="{{ $room->rooms }}">
            <div class="card-detail-title">{{ $room->rooms }}</div>
        </div>
    </a>
    @php
        $promoImages = [
            'Hot Deal' => 'hot_deal_promo.png',
            'Best Choice' => 'best_choice_promo.png',
            'Best Price' => 'best_price_promo.png',
            'Special Offer' => 'special_offer_promo.png',
        ];
    @endphp
    @if ($show_promo)
        <div class="promo-hot-deal">
            <img 
                src="{{ asset('storage/icon/' . $promoImages[$show_promo->promotion_type] ?? 'default_promo.png') }}" 
                alt="{{ $show_promo->promotion_type }} Promotion">
        </div>
    @endif

    <!-- Modal -->
    <div class="modal fade" id="detail-room-{{ $room->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-left">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle">
                            <i class="icon-copy fa fa-hotel" aria-hidden="true"></i> 
                            {{ $room->rooms }}
                        </div>
                    </div>
                    <div class="card-box-body">
                        <div class="card-banner m-b-8">
                            <img 
                                class="rounded" 
                                src="{{ asset('storage/hotels/hotels-room/' . $room->cover) }}" 
                                alt="{{ $room->rooms }}" 
                                loading="lazy">
                        </div>
    
                        <!-- Promotions Section -->
                        @if ($room->promos->count())
                            @php
                                $incomingPromotions = $room->promos->where('book_periode_end', '>', $now)->sortBy('periode_start');
                            @endphp
                            @if ($incomingPromotions->isNotEmpty())
                                <div class="card-title">@lang('messages.Promotion')</div>
                                <div class="container-promo-inner-modal">
                                    @foreach ($incomingPromotions as $promotion)
                                        @include('partials.inner-hotel-room-card', ['promotion' => $promotion])
                                    @endforeach
                                </div>
                            @endif
                        @endif
                        <!-- Room Details -->
                        <div class="card-content">
                            <hr class="form-hr">
                            <div class="row">
                                @foreach ([
                                    'messages.Capacity' => $room->capacity_adult.' '.__('messages.Adult')." ~ ".$room->capacity_child.' '.__('messages.Child'),
                                    'messages.View' => $room->view,
                                    'messages.Beds' => $room->beds,
                                    'messages.Size' => $room->size.' m²',
                                ] as $label => $value)
                                    @if ($value != "")
                                        <div class="col-md-4">
                                            <div class="card-text">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="card-subtitle">@lang($label)</div>
                                                        {!! $value !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <hr class="form-hr">
                            <div class="card-text">
                            <div class="card-subtitle">
                                    @lang('messages.Amenities')
                                </div>
                                <p>
                                    @if (app()->getLocale() == 'en')
                                        {!! $room->include !!}
                                    @elseif (app()->getLocale() == 'zh')
                                        {!! $room->include_traditional !!}
                                    @else
                                        {!! $room->include_simplified !!}
                                    @endif
                                </p>
                            </div>
                            @if ($room->additional_info != "")
                                <div class="card-text">
                                    <div class="card-subtitle">
                                        @lang('messages.Additional Information')
                                    </div>
                                    <p>
                                        @if (app()->getLocale() == 'en')
                                            {!! $room->additional_info !!}
                                        @elseif (app()->getLocale() == 'zh')
                                            {!! $room->additional_info_traditional !!}
                                        @else
                                            {!! $room->additional_info_simplified !!}
                                        @endif
                                    </p>
                                </div>
                            @endif
                            <hr class="form-hr">
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="card-box-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                            <i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
