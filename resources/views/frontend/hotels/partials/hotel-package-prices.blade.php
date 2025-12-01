@if (count($packages) > 0)
    <div class="subtitle">
        @lang('messages.Package Prices')
    </div>
    <div class="pricelist-container m-b-53">
        @foreach ($packages as $hotel_package_price)
            <div class="card-hotel-pricelist-container">
                <div class="item-img">
                    <img src="{{ getThumbnail('/hotels/hotels-room/'.$hotel_package_price->room->cover,380,200) }}"  class="img-fluid rounded thumbnail-image" loading="lazy">
                    <div class="promotion-flag-container">
                        <div class="promotion-flag bg-green }}">
                            @lang('messages.Package') {{ $hotel_package_price->duration }} @lang('messages.nights')
                        </div>
                    </div>
                </div>
                <div class="item-description">
                    <div class="description-detail">
                        <div class="description-title">
                            {{ $hotel_package_price['room']->rooms }}
                        </div>
                        <div class="pricelist pb-2">
                            @for ($i = 0; $i < $hotel_package_price->duration; $i++)
                                <div class="p-card-info text-center">
                                    <div class="p-card-date">
                                        {{ isset($hotel_package_price->price_per_day) ? date('m/d',strtotime('+'.$i.'days',strtotime($checkin))) : '-' }}
                                    </div>
                                    <div class="p-card-price-package">
                                        {{ isset($hotel_package_price->price_per_day) ? "$". number_format($hotel_package_price->price_per_day) : "$0" }}
                                    </div>
                                </div>
                            @endfor
                        </div>
                        <div class="description-action">
                            <div class="description-price">
                                <div class="price">{!! currencyFormatUsd($hotel_package_price->calculated_price) !!}</div>
                            </div>
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                                <input type="hidden" name="room_id" value="{{ $hotel_package_price->room->id }}">
                                <input type="hidden" name="checkin" value="{{ $checkin }}">
                                <input type="hidden" name="checkout" value="{{ $checkout }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary w-100">
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