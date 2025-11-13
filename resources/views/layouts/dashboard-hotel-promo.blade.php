
@if (count($hotel_promos)>0)
@php
    $hotel_id = 0;
    $limit = 0;
    $looping = 8;
@endphp
    <div class="trans-box">
        <div class="trans-box-title"><i class="dw dw-hotel"></i> @lang('messages.'.$titleHotel)</div>
        <div class="trans-box-content">
            @foreach ($hotel_promos as $hotel_promo)
                @php
                    $hotel = $hotel_promo->hotels;
                @endphp
                @if ($hotel->id != $hotel_id)
                    @if ($limit < $looping)
                        @if ($hotel_promo)
                            <div class="trans-card anim-feed-up">
                                <a href="hotel-{{ $hotel->code }}">
                                    <div class="persen-promo"><img src="{{ asset('storage/icon/persen.png') }}" alt="Promo discount"></div>
                                    @if ($hotel_promo->promotion_type == "Hot Deal")
                                        <div class="promo-hot-deal"><img src="{{ asset('/storage/icon/hot_deal_promo.png') }}" alt="Hot Deal Promotion"></div>
                                    @elseif ($hotel_promo->promotion_type == "Best Choice")
                                        <div class="promo-hot-deal"><img src="{{ asset('/storage/icon/best_choice_promo.png') }}" alt="Best Choice Promotion"></div>
                                    @elseif ($hotel_promo->promotion_type == "Best Price")
                                        <div class="promo-hot-deal"><img src="{{ asset('/storage/icon/best_price_promo.png') }}" alt="Best Price Promotion"></div>
                                    @elseif ($hotel_promo->promotion_type == "Special Offer")
                                        <div class="promo-hot-deal"><img src="{{ asset('/storage/icon/special_offer_promo.png') }}" alt="Special Offer Promotion"></div>
                                    @endif
                                
                                    <div class="trans-img-container">
                                        <img src="{{ asset('storage/hotels/hotels-cover/' . $hotel->cover) }}" class="img-fluid rounded thumbnail-image">
                                    </div>
                                    <div class="image-title-bottom">
                                        <p>{{ $hotel->name }}</p>
                                    </div>
                                    <div class="persen-promo"><img src="{{ asset('storage/icon/persen.png') }}" alt="Promo discount"></div>
                                </a>
                            </div>
                        @endif
                        @php
                            $hotel_id = $hotel->id;
                            ++$limit;
                        @endphp
                    @endif
                @endif
            @endforeach
        </div>
        <div class="trans-box-footer">
            <a href="/hotels">
                <button class="btn btn-primary text-white"> @lang('messages.Hotels') <i class="icon-copy fa fa-arrow-circle-right" aria-hidden="true"></i></button>
            </a>
        </div>
    </div>
@endif