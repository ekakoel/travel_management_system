@if (count($processedPromos) > 0)
    <div class="subtitle">
        @lang('messages.Promo Prices')
    </div>
    <div class="pricelist-container m-b-53">
        @foreach ($processedPromos as $hotel_promo_price)
            <div class="card-hotel-pricelist-container">
                <div class="item-img">
                    <img src="{{ getThumbnail('/hotels/hotels-room/'.$hotel_promo_price['room']->cover,380,200) }}"  class="img-fluid rounded small-card-image" loading="lazy">
                    <div class="promotion-flag-container">
                        @foreach ($hotel_promo_price['promo_id_list'] as $promoid)
                            @php
                                $promotype = $hotel_promotions->where('id', $promoid)->first();
                            @endphp
                            @if ($promotype && !in_array($promotype->id, $displayedPromos))
                                <div class="promotion-flag bg-blue">
                                    {{ $promotype->promotion_type }} | {{ $promotype->minimum_stay . " N" }}
                                </div>
                                @php
                                    $displayedPromos[] = $promotype->id;
                                @endphp
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="item-description">
                    <div class="description-detail">
                        <div class="description-title">
                            {{ $hotel_promo_price['room']->rooms }}
                        </div>
                        <div class="pricelist pb-2">
                            @foreach ($hotel_promo_price['hotel_promo'] as $index => $price)
                                <div class="p-card-info text-center">
                                    <div class="p-card-date">
                                        {{ date('m/d', strtotime($hotel_promo_price['on_dates'][$index])) }}
                                    </div>
                                    <div class="p-card-price-promo ">
                                        {{ "$". number_format($hotel_promo_price['price_list'][$index]) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="description-action">
                            <div class="description-price">
                                <div class="price">{!! currencyFormatUsd($hotel_promo_price['total_price']) !!}</div>
                            </div>
                            <form action="{{ route('view.reservation.store') }}" method="GET">
                                @csrf
                                <input type="hidden" name="service" value="Hotel">
                                <input type="hidden" name="service_id" value="{{ $hotel->id }}">
                                <input type="hidden" name="room_id" value="{{ $hotel_promo_price['room']->id }}">
                                <input type="hidden" name="checkin" value="{{ $checkin }}">
                                <input type="hidden" name="checkout" value="{{ $checkout }}">
                                <input type="hidden" name="quantity" value="1">
                                <button class="btn btn-primary w-100">
                                    <i class="icon-copy dw dw-calendar1"></i> @lang('messages.Book Now')
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif


