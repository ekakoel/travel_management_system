@section('title',__('Hotel Availability'))
@extends('layouts.head')
@section('content')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title"><i class="icon-copy dw dw-hotel-o"></i> {{ $hotel->name }}</div>
                            @include('partials.breadcrumbs', [
                                'breadcrumbs' => [
                                    ['url' => route('dashboard.index'), 'label' => __('messages.Dashboard')],
                                    ['url' => route('view.hotels'), 'label' => __('messages.Hotels')],
                                    ['url' => route('view.hotel-detail',$hotel->code), 'label' => $hotel->name],
                                    ['label' => __('messages.Check Price')],
                                ]
                            ])
                        </div>
                    </div>
                </div>
                @include('partials.alerts')
                {{-- PROMOTION =================================================================================================== --}}
                @if ($promotions->count() > 0)
                    <div class="promotion-container">
                        @foreach ($promotions as $promotion)
                            @include('partials.promotion-card', compact('promotion'))
                        @endforeach
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-4 mobile">
                        @include('partials.check-price', compact('nearhotels'))
                    </div>
                    <div class="col-md-8">
                            <div class="row">
                                {{-- PROMO PRICE --}}
                                @if (count($processedPromos) > 0 or count($packages)>0 or $normalPriceData)
                                    @if (count($processedPromos) > 0)
                                        <div class="col-md-12 m-b-18">
                                            <div class="card-box">
                                                <div class="row">
                                                    <div class="col-md-12 m-b-18">
                                                        <div class="pages-description">{{ $hotel->name }}</div>
                                                        <div class="pages-subtitle">
                                                            @lang('messages.Promotion Prices')
                                                        </div>
                                                        <div class="pages-description">{{ dateFormat($checkin)." - ".dateFormat($checkout) }} ({{ $duration }} @lang('messages.nights'))</div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        @foreach ($processedPromos as $hotel_promo_price)
                                                            @php
                                                                $promoId = array_unique($hotel_promo_price['promo_id_list']);
                                                                $hotelPromotions = $hotel_promotions->whereIn('id', $promoId);
                                                            @endphp
                                                            <div class="card-hotel-pricelist-container">
                                                                <div class="item-img">
                                                                    <img src="{{ getThumbnail('/hotels/hotels-room/'.$hotel_promo_price['room']->cover,380,200) }}"  class="img-fluid rounded thumbnail-image" loading="lazy">
                                                                    <div class="promotion-flag-container">
                                                                        @foreach ($hotel_promo_price['promo_id_list'] as $promoid)
                                                                            @php
                                                                                $promotype = $hotel_promotions->where('id', $promoid)->first();
                                                                            @endphp
                                                                            @if ($promotype && !in_array($promotype->id, $displayedPromos))
                                                                                <div class="promotion-flag {{ $promo_colors[$promotype->promotion_type] ?? 'bg-default' }}">
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
                                                                        <span>
                                                                            @if ($hotel_promo_price['room']->capacity <= 3)
                                                                                @php
                                                                                    $adults = 2;
                                                                                    $children = 1;
                                                                                @endphp
                                                                            @elseif ($hotel_promo_price['room']->capacity % 2 == 0) 
                                                                                @php
                                                                                    $adults = $hotel_promo_price['room']->capacity;
                                                                                    $children = $hotel_promo_price['room']->capacity / 2;
                                                                                @endphp
                                                                            @else
                                                                                @php
                                                                                    $adults = $hotel_promo_price['room']->capacity - 1;
                                                                                    $children = ($hotel_promo_price['room']->capacity - 1) / 2;
                                                                                @endphp
                                                                            @endif
                                                                            @for ($i = 0; $i < $adults; $i++)
                                                                                <i class="icon-copy fa fa-user" aria-hidden="true"></i>
                                                                            @endfor
                                                                            @for ($i = 0; $i < $children; $i++)
                                                                                <i class="icon-copy fa fa-child" aria-hidden="true"></i>
                                                                            @endfor
                                                                            ({{ $adults }} adults + {{ $children }} child)
                                                                        </span>
                                                                        @if (session('bookingcode'))
                                                                            <div class="promotion-name bg-red }}">
                                                                                @lang('messages.Booking code') | ${{ session('bookingcode.discounts') }}
                                                                            </div>
                                                                        @endif
                                                                        <div class="pricelist">
                                                                            @foreach ($hotel_promo_price['price_list'] as $index => $price)
                                                                                @php
                                                                                    $pr_type = $hotel_promo_price['promo_type'][$index];
                                                                                    $bg_color = $promo_colors[$pr_type] ?? 'bg-default';
                                                                                @endphp
                                                                                <div class="p-card-info text-center">
                                                                                    <div class="p-card-date">
                                                                                        {{ date('m/d', strtotime($hotel_promo_price['on_dates'][$index])) }}
                                                                                    </div>
                                                                                    <div class="p-card-price-promo {{ $bg_color }}">
                                                                                        {{ "$". number_format($price) }}
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="detail">
                                                                            <b>@lang('messages.Include'):</b><br>
                                                                            @foreach ($hotelPromotions as $hotel_promotion)
                                                                                @if ($hotel_promotion->include)
                                                                                    @include('partials.modal-hotel-promo-include', compact("hotel_promotion"))
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                    <div class="description-action">
                                                                        <div class="description-price">
                                                                            <div class="price">{{ currencyFormatUsd($hotel_promo_price['total_price']) }}</div>
                                                                        </div>
                                                                        <form action="{{ route('view.order-hotel-promo',$hotel_promo_price['room']->id) }}" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="service" value="Hotel Promo">
                                                                            <input type="hidden" name="promo_id" value="{{ json_encode($hotel_promo_price['promo_id_list']) }}">
                                                                            <input type="hidden" name="promo_price" value="{{ $hotel_promo_price['total_price'] - session('bookingcode.discounts') }}">
                                                                            <input type="hidden" name="type_price_list" value="{{ json_encode($hotel_promo_price['type_price_list']) }}">
                                                                            <input type="hidden" name="type_price" value="{{ json_encode($hotel_promo_price['type_price']) }}">
                                                                            <input type="hidden" name="price_list" value="{{ json_encode($hotel_promo_price['price_list']) }}">
                                                                            <button type="submit" class="btn btn-primary w-100"><i class="fa fa-shopping-basket"></i> @lang('messages.Order')</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    {{-- PACKAGE --}}
                                    @if (count($packages)>0)
                                        <div class="col-md-12 m-b-18">
                                            <div class="card-box">
                                                <div class="row">
                                                    <div class="col-md-12 m-b-18">
                                                        <div class="pages-description">{{ $hotel->name }}</div>
                                                        <div class="pages-subtitle">
                                                            @lang('messages.Package Prices')
                                                        </div>
                                                        <div class="pages-description">{{ dateFormat($checkin)." - ".dateFormat($checkout) }} ({{ $duration }} @lang('messages.nights'))</div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        @foreach ($packages as $package)
                                                            <div class="card-hotel-pricelist-container">
                                                                <div class="item-img">
                                                                    <img src="{{ getThumbnail('/hotels/hotels-room/' . $package->room->cover,380,200) }}"  class="img-fluid rounded thumbnail-image" loading="lazy">
                                                                </div>
                                                                <div class="item-description">
                                                                    <div class="description-detail">
                                                                        <div class="description-title">
                                                                            {{ $package->room->rooms }}
                                                                        </div>
                                                                        <span>
                                                                            @if ($package->room->capacity <= 3)
                                                                                @php
                                                                                    $adults = 2;
                                                                                    $children = 1;
                                                                                @endphp
                                                                            @elseif ($package->room->capacity % 2 == 0) 
                                                                                @php
                                                                                    $adults = $package->room->capacity;
                                                                                    $children = $package->room->capacity / 2;
                                                                                @endphp
                                                                            @else
                                                                                @php
                                                                                    $adults = $package->room->capacity - 1;
                                                                                    $children = ($package->room->capacity - 1) / 2;
                                                                                @endphp
                                                                            @endif
                                                                            @for ($i = 0; $i < $adults; $i++)
                                                                                <i class="icon-copy fa fa-user" aria-hidden="true"></i>
                                                                            @endfor
                                                                            @for ($i = 0; $i < $children; $i++)
                                                                                <i class="icon-copy fa fa-child" aria-hidden="true"></i>
                                                                            @endfor
                                                                            ({{ $adults }} adults + {{ $children }} child)
                                                                        </span>
                                                                        @if (session('bookingcode'))
                                                                            <div class="promotion-name bg-red }}">
                                                                                @lang('messages.Booking code') | ${{ session('bookingcode.discounts') }}
                                                                            </div>
                                                                        @endif
                                                                        <div class="detail">
                                                                            <b>
                                                                                {{ $package->name }}
                                                                            </b><br>
                                                                            
                                                                            @if ($package->benefits)
                                                                                @include('partials.modal-hotel-package-benefits', compact('package'))
                                                                            @endif
                                                                            @if ($package->include)
                                                                                @include('partials.modal-hotel-package-include', compact('package'))
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="description-action">
                                                                        <div class="description-price">
                                                                            <div class="price">{{ "$" . number_format($package->calculated_price) }}</div>
                                                                        </div>
                                                                        <form action="{{ route('view.order-hotel-package',$package->room->id) }}" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                                                                            <input type="hidden" name="service" value="Hotel Package">
                                                                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                                                                            <button type="submit" class="btn btn-primary w-100"><i class="fa fa-shopping-basket"></i> @lang('messages.Order')</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    {{-- NORMAL --}}
                                    @if ($normalPriceData)
                                        <div class="col-md-12 m-b-18">
                                            <div class="card-box">
                                                <div class="row">
                                                    <div class="col-md-12 m-b-18">
                                                        <div class="pages-description">{{ $hotel->name }}</div>
                                                        <div class="pages-subtitle">
                                                            @lang('messages.Normal Prices')
                                                        </div>
                                                        <div class="pages-description">{{ dateFormat($checkin)." - ".dateFormat($checkout) }} ({{ $duration }} @lang('messages.nights'))</div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        @foreach($normalPriceData as $room_normal_price)
                                                            @php
                                                                $normal_price_rooms = $room_normal_price['normal_room'];
                                                            @endphp
                                                            <div class="card-hotel-pricelist-container">
                                                                <div class="item-img">
                                                                    @if ($room_normal_price['total_kick_b'] > 0)
                                                                        <div class="table-label promo-label">@lang('messages.Kick Back') {{ "$ ".$room_normal_price['total_kick_b'] }}</div>
                                                                    @endif
                                                                    <img src="{{ getThumbnail('/hotels/hotels-room/' . $room_normal_price['normal_room']?->cover,380,200) }}"  class="img-fluid rounded thumbnail-image" loading="lazy">
                                                                </div>
                                                                <div class="item-description">
                                                                    <div class="description-detail">
                                                                        <div class="description-title">
                                                                            {{ $room_normal_price['normal_room']?->rooms }}
                                                                        </div>
                                                                        <span>
                                                                            @if ($room_normal_price['normal_room']->capacity <= 3)
                                                                                @php
                                                                                    $adults = 2;
                                                                                    $children = 1;
                                                                                @endphp
                                                                            @elseif ($room_normal_price['normal_room']->capacity % 2 == 0) 
                                                                                @php
                                                                                    $adults = $room_normal_price['normal_room']->capacity;
                                                                                    $children = $room_normal_price['normal_room']->capacity / 2;
                                                                                @endphp
                                                                            @else
                                                                                @php
                                                                                    $adults = $room_normal_price['normal_room']->capacity - 1;
                                                                                    $children = ($room_normal_price['normal_room']->capacity - 1) / 2;
                                                                                @endphp
                                                                            @endif
                                                                            @for ($i = 0; $i < $adults; $i++)
                                                                                <i class="icon-copy fa fa-user" aria-hidden="true"></i>
                                                                            @endfor
                                                                            @for ($i = 0; $i < $children; $i++)
                                                                                <i class="icon-copy fa fa-child" aria-hidden="true"></i>
                                                                            @endfor
                                                                            ({{ $adults }} adults + {{ $children }} child)
                                                                        </span>
                                                                        @if ($promotion_price>0)
                                                                            <div class="promotion-text">{{ $promotion_name }} @lang('messages.Discount') {{ currencyFormatUsd($promotion_price) }}</div>
                                                                        @endif
                                                                        @if (session('bookingcode'))
                                                                            <div class="promotion-name bg-red }}">
                                                                                @lang('messages.Booking code') | ${{ session('bookingcode.discounts') }}
                                                                            </div>
                                                                        @endif
                                                                        <div class="pricelist">
                                                                            @foreach($room_normal_price['normal_prices'] as $normal_r_price)
                                                                                <div class="p-card-info text-center">
                                                                                    <div class="p-card-date">
                                                                                        {{ date('m/d', strtotime($normal_r_price['normal_date'])) }}
                                                                                    </div>
                                                                                    <div class="p-card-price-promo bg-gray">
                                                                                        ${{ number_format($normal_r_price['normal_price']) }}
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="detail">
                                                                            @if ($normal_price_rooms->include)
                                                                                @include('partials.modal-hotel-normal-include', compact("normal_price_rooms"))
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="description-action">
                                                                        @if ($room_normal_price['total_kick_b']>0 || $promotion_price)
                                                                            <div class="description-price">
                                                                                <div class="price">
                                                                                    <div class="price-after-discount">{{ currencyFormatUsd($room_normal_price['total_price']) }}</div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="description-price">
                                                                            <div class="price">{{ currencyFormatUsd($room_normal_price['total_price'] - $room_normal_price['total_kick_b'] - $promotion_price) }}</div>
                                                                        </div>
                                                                        <form action="{{ route('view.order-hotel-normal',$room_normal_price['normal_room']->id) }}" method="POST">
                                                                            @csrf
                                                                            @if ($room_normal_price['total_kick_b'] > 0)
                                                                                <input type="hidden" name="kick_back" value="{{ $room_normal_price['total_kick_b'] }}">
                                                                                <input type="hidden" name="kick_back_per_pax" value="{{ $room_normal_price['total_kick_b']/$duration }}">
                                                                            @endif
                                                                            @if (session('bookingcode.id'))
                                                                                <input type="hidden" name="bookingcode" value="{{ session('bookingcode.code') }}">
                                                                            @endif
                                                                            @if ($promotion_price > 0)
                                                                                <input type="hidden" name="promotion" value="{{ $promotion_price }}">
                                                                            @endif
                                                                            <input type="hidden" name="price_pax" value="{{ $room_normal_price['total_price'] / $duration }}">
                                                                            <input type="hidden" name="normal_price" value="{{ $room_normal_price['total_price'] }}">
                                                                            <input type="hidden" name="final_price" value="{{ $room_normal_price['total_price']-$room_normal_price['total_kick_b'] }}">
                                                                            <input type="hidden" name="orderno" value="{{ 'ORD.' . date('Ymd', strtotime($now)) . '.HNP' . $orderno }}">
                                                                            <input type="hidden" name="service" value="Hotel">
                                                                            <input type="hidden" name="checkin" value="{{ $checkin }}">
                                                                            <input type="hidden" name="checkout" value="{{ $checkout }}">
                                                                            <input type="hidden" name="duration" value="{{ $duration }}">
                                                                            <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                                                                            <input type="hidden" name="room_id" value="{{ $room_normal_price['normal_room']->id }}">
                                                                            <button type="submit" class="btn btn-primary w-100"><i class="fa fa-shopping-basket"></i> @lang('messages.Order')</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="col-md-12">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <div class="subtitle"><i class="fa fa-usd"></i>@lang('messages.Normal Prices')</div>
                                            </div>
                                            <div class="notification">
                                                @lang('messages.Price cannot be found, please try another date')
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                    </div>
                    {{-- SEARCH ------------------------------------------------------------------------------------------------------------------- --}}
                    <div class="col-md-4 desktop">
                        @include('partials.check-price')
                    </div>
                    @if (count($nearhotels)>0)
                        @include('partials.near-hotel', compact('nearhotels'))
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
