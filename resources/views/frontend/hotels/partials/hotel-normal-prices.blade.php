@if (count($normalPriceData) > 0)
    <div class="subtitle">
        @lang('messages.Regular Prices')
    </div>
    <div class="pricelist-container m-b-53">
        @foreach ($normalPriceData as $hotel_normal_price)
            @php
                $normal_price = json_encode($hotel_normal_price['normal_prices']);
            @endphp
            <div class="card-hotel-pricelist-container">
                <div class="item-img">
                    <img src="{{ getThumbnail('/hotels/hotels-room/'.$hotel_normal_price['normal_room']->cover,380,200) }}"  class="img-fluid rounded thumbnail-image" loading="lazy">
                </div>
                <div class="item-description">
                    <div class="description-detail">
                        <div class="description-title">
                            {{ $hotel_normal_price['normal_room']->rooms }}
                        </div>
                        <div class="pricelist">
                            @foreach ($hotel_normal_price['normal_prices'] as $index => $price)
                                <div class="p-card-info text-center">
                                    <div class="p-card-date">
                                        {{ date('m/d', strtotime($price['normal_date'])) }}
                                    </div>
                                    <div class="p-card-price-normal">
                                        {{ "$". number_format($price['normal_price']) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="description-action">
                            <div class="description-price">
                                <div class="price">{!! currencyFormatUsd($hotel_normal_price['total_price']) !!}</div>
                            </div>
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="prices" value="{{ $normal_price }}">
                                <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                                <input type="hidden" name="room_id" value="{{ $hotel_normal_price['normal_room']->id }}">
                                <input type="hidden" name="checkin" value="{{ $checkin }}">
                                <input type="hidden" name="checkout" value="{{ $checkout }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="icon-copy dw dw-shopping-cart1"></i> @lang('messages.Add to Cart')
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif


