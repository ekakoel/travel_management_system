@section('title',__('messages.Villa Availability'))
@extends('layouts.head')
@section('content')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title"><i class="icon-copy dw dw-hotel-o"></i> {{ $villa->name }}</div>
                            @include('partials.breadcrumbs', [
                                'breadcrumbs' => [
                                    ['url' => route('dashboard.index'), 'label' => __('messages.Dashboard')],
                                    ['url' => route('view.villas.index'), 'label' => __('messages.Hotels')],
                                    ['url' => route('view.villas.show',$villa->code), 'label' => $villa->name],
                                    ['label' => __('messages.Check Price')],
                                ]
                            ])
                        </div>
                    </div>
                </div>
                @include('partials.alerts')
                <div class="row">
                    <div class="col-md-4 mobile">
                        <div class="card-box m-b-18">
                            @if($calculatedPrice)
                                <div class="card-subtitle">@lang('messages.Additional Information')</div>
                                <p>{!! nl2br(e($price->additional_info)) !!}</p>
                                <div class="card-subtitle">@lang('messages.Cancellation Policy')</div>
                                <p>{!! nl2br(e($price->cancellation_policy)) !!}</p>
                                <div class="card-subtitle">@lang('messages.Price')</div>
                                <div class="price-tag-container">
                                    <div class="price-period">
                                        <b>Stay Period: &nbsp;</b> {{ dateFormat($checkin) }} - {{ dateFormat($checkout) }}
                                        <br>
                                        <b>Duration: &nbsp;</b> {{ $duration }} {{ $duration > 1 ? __('messages.nights') : __('messages.night') }}
                                        <br>
                                        
                                        <div class="pricelist">
                                            @foreach ($price_details as $detail)
                                                <div class="p-card-info text-center">
                                                    <div class="p-card-date">
                                                        {{ date('m/d',strtotime($detail['date'])) }}
                                                    </div>
                                                    <div class="p-card-price-promo bg-gray">
                                                        $ {{ $detail['price_per_night_usd'] }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="price-tag text-center">
                                        <p>@lang('messages.Total Price'):</p>
                                        {{ currencyFormatUsd($calculatedPrice) }}
                                    </div>
                                </div>
                                <div class="text-right" style="margin-top: 18px;">
                                    <a href="{{ route('view.order-villa',$villa->code) }}">    
                                        <button type="btn" class="btn btn-primary"><i class='icon-copy fa fa-shopping-basket' aria-hidden='true'></i> @lang('messages.Order')</button>
                                    </a>
                                </div>
                            @else
                                <div class="card-subtitle">@lang('messages.Price Availability')</div>
                                @foreach ($prices as $no=>$price_availability)
                                    <p class="p-l-18">{{ ++$no.". ". dateFormat($price_availability->start_date) }} - {{ dateFormat($price_availability->end_date) }} ({{ currencyFormatUsd($price_availability->calculatePrice($usdrates, $tax))." /".__('messages.night') }})</p>
                                @endforeach
                                <p class="notification m-t-18">"@lang('messages.No price data available for the selected dates. We suggest trying different dates to check availability and pricing.')"</p>
                            @endif
                        </div>
                        @include('villas.partials.check-price', compact('nearvillas'))
                   </div>
                    <div class="col-md-8">
                        @if (isset($error) && is_array($error) && count($error) > 0 || isset($success) && is_array($success) && count($success) > 0)
                            @include('partials.msg')
                        @endif
                        <div class="info-action">
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (\Session::has('warning'))
                                <div class="alert alert-danger">
                                    <ul>
                                        <li>{!! \Session::get('warning') !!}</li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="card-box m-b-18">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="dw dw-building-1"></i> {{ $villa->name }}</div>
                            </div>
                            <div class="page-card">
                                <div class="card-banner m-b-18">
                                    <img src="{{ asset('storage/villas/covers/' . $villa->cover) }}" alt="{{ $villa->name }}" loading="lazy">
                                </div>
                                <div class="row card-content">
                                    <div class="col-6 col-md-6">
                                        <div class="card-subtitle">@lang('messages.Address')</div>
                                        <p><a target="__blank" href="{{ $villa->map }}">{!! $villa->address !!}</a></p>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <div class="card-subtitle">@lang('messages.Website')</div>
                                        <p><a target="__blank" href="{{ $villa->web }}">{!! $villa->web !!}</a></p>
                                    </div>
                                    @if ($villa->rooms->count() > 0)
                                        <div class="col-6 col-md-6">
                                            <div class="card-subtitle">@lang('messages.Bedroom')</div>
                                            <p><i class="icon-copy fa fa-bed" aria-hidden="true"></i> {{ $villa->rooms->count() }}</p>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <div class="card-subtitle">@lang('messages.Occupancy')</div>
                                            <p><i class="icon-copy fa fa-user" aria-hidden="true"></i> {{ $guestTotals->total_adult }} + <i class="icon-copy fa fa-child" aria-hidden="true"></i> {{ $guestTotals->total_child }}</p>
                                        </div>
                                    @endif
                                    @if ($villa->facility)
                                        @php
                                            $facilities = explode(',', $villa->facility); // atau $villa->amenities
                                        @endphp

                                        <div class="col-12 col-md-6">
                                            <div class="card-subtitle">@lang('messages.Facility')</div>
                                            <ul>
                                                @foreach ($facilities as $facility)
                                                    <li><p>{{ trim($facility) }}</p></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <div class="col-6 col-md-6">
                                        <div class="card-subtitle">@lang('messages.Minimum Stay')</div>
                                        <p>{{ $villa->min_stay }} {{ $villa->min_stay > 1?__('messages.nights'):__('messages.night') }}</p>
                                    </div>
                                    
                                    @if ($villa->benefits)
                                        <div class="col-12 col-md-12">
                                            <div class="card-subtitle">@lang('messages.Benefits')</div>
                                            <p>{!! $villa->benefits !!}</p>
                                        </div>
                                    @endif
                                    <div class="col-12 col-md-12">
                                        <div class="card-subtitle">@lang('messages.About')</div>
                                        <p>{!! $villa->description !!}</p>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <hr class="light-hr">
                                    </div>
                                    @if ($villa->rooms->count() > 0)
                                        <div class="col-12 col-md-12">
                                            <div class="card-subtitle">@lang('messages.Rooms')</div>
                                            <div class="card-box-content">
                                                @foreach ($villa->rooms as $room)
                                                    @include('villas.partials.room-card', compact('room'))
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 desktop">
                        <div class="card-box m-b-18">
                            @if($calculatedPrice)
                                <div class="card-subtitle">@lang('messages.Additional Information')</div>
                                <p>{!! nl2br(e($price->additional_info)) !!}</p>
                                <div class="card-subtitle">@lang('messages.Cancellation Policy')</div>
                                <p>{!! nl2br(e($price->cancellation_policy)) !!}</p>
                                <div class="card-subtitle">@lang('messages.Price')</div>
                                <div class="price-tag-container">
                                    <div class="price-period">
                                        <b>Stay Period: &nbsp;</b> {{ dateFormat($checkin) }} - {{ dateFormat($checkout) }}
                                        <br>
                                        <b>Duration: &nbsp;</b> {{ $duration }} {{ $duration > 1 ? __('messages.nights') : __('messages.night') }}
                                        <br>
                                        
                                        <div class="pricelist">
                                            @foreach ($price_details as $detail)
                                                <div class="p-card-info text-center">
                                                    <div class="p-card-date">
                                                        {{ date('m/d',strtotime($detail['date'])) }}
                                                    </div>
                                                    <div class="p-card-price-promo bg-gray">
                                                        $ {{ $detail['price_per_night_usd'] }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="price-tag text-center">
                                        <p>@lang('messages.Total Price'):</p>
                                        {{ currencyFormatUsd($calculatedPrice) }}
                                    </div>
                                </div>
                                <div class="text-right" style="margin-top: 18px;">
                                    <a href="{{ route('view.order-villa',$villa->code) }}">    
                                        <button type="btn" class="btn btn-primary"><i class='icon-copy fa fa-shopping-basket' aria-hidden='true'></i> @lang('messages.Order')</button>
                                    </a>
                                </div>
                            @else
                                <div class="card-subtitle">@lang('messages.Price Availability')</div>
                                @foreach ($prices as $no=>$price_availability)
                                    <p class="p-l-18">{{ ++$no.". ". dateFormat($price_availability->start_date) }} - {{ dateFormat($price_availability->end_date) }} ({{ currencyFormatUsd($price_availability->calculatePrice($usdrates, $tax))." /".__('messages.night') }})</p>
                                @endforeach
                                <p class="notification m-t-18">"@lang('messages.No price data available for the selected dates. We suggest trying different dates to check availability and pricing.')"</p>
                            @endif
                        </div>
                        @include('villas.partials.check-price', compact('nearvillas'))
                   </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var roomModal = document.getElementById('roomModal');
            roomModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var room = button.getAttribute('data-room');
                if(room){
                    room = JSON.parse(room);

                    document.getElementById('roomCoverImg').src = room.cover ? "{{ asset('storage/villas/cover') }}/" + room.cover : "https://via.placeholder.com/600x400?text=No+Image";
                    document.getElementById('roomName').textContent = room.name || '';
                    document.getElementById('roomType').textContent = room.room_type || '';
                    document.getElementById('bedType').textContent = room.bed_type || '';
                    document.getElementById('guestAdult').textContent = room.guest_adult || '0';
                    document.getElementById('guestChild').textContent = room.guest_child || '0';
                    document.getElementById('roomSize').textContent = room.size || '';
                    document.getElementById('roomAmenities').innerHTML = room.amenities ? room.amenities.replace(/\n/g, "<br>") : '';
                    document.getElementById('roomDescription').innerHTML = room.description ? room.description.replace(/\n/g, "<br>") : '';
                }
            });
        });
    </script>
@endsection