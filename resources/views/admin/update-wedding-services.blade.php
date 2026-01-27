@php
    $wed_venues_id = json_decode($wedding_order->wedding_venue_id);
    $wed_makeups_id = json_decode($wedding_order->wedding_makeup_id);
    $wed_decorations_id = json_decode($wedding_order->wedding_decoration_id);
    $wed_dinner_venues_id = json_decode($wedding_order->wedding_dinner_venue_id);
    $wed_entertainments_id = json_decode($wedding_order->wedding_entertainment_id);
    $wed_documentations_id = json_decode($wedding_order->wedding_documentation_id);
    $wed_transports_id = json_decode($wedding_order->wedding_transport_id);
    $wed_others_id = json_decode($wedding_order->wedding_other_id);
    $rooms_id = json_decode($wedding_order->wedding_room_id);
@endphp
@section('title', __('messages.Order Wedding'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title"><i class="icon-copy fa fa-check" aria-hidden="true"></i> {{ $wedding->name }} Services</div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin-panel">Admin Panel</a></li>
                                    <li class="breadcrumb-item"><a href="orders-admin">Orders Admin</a></li>
                                    <li class="breadcrumb-item" aria-current="page"><a href="orders-admin-{{ $order->id }}">{{ $order->orderno }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Wedding Package Service</li>
                                </ol>
                            </nav>
                        </div>
                        
                    </div>
                </div>
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
                    @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <ul>
                                <li>{!! \Session::get('success') !!}</li>
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="fa fa-pencil"></i>Services</div>
                            </div>
                            <div class="row">
                                {{-- ORDER WEDDING VENUE --}}
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="tab-inner-title">
                                                Wedding Venue
                                                @if ($order->status == "Pending")
                                                    <span>
                                                        @if ($wedding_order->wedding_venue_id !== "null" and $wedding_order->wedding_venue_id)
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-venue"> 
                                                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Wedding Venue" aria-hidden="true"></i>
                                                            </a>
                                                        @else
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-venue"> 
                                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add Wedding Venue" aria-hidden="true"></i>
                                                            </a>
                                                        @endif
                                                    </span>
                                                    {{-- MODAL UPDATE ORDER WEDDING VENUE --------------------------------------------------------------------------------------------------------------- --}}
                                                    <div class="modal fade" id="add-wedding-venue" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fi-torso"></i><i class="icon-copy fi-torso-female"></i>Add Wedding Venue</div>
                                                                    </div>
                                                                    <form id="addweddingvenue" action="/fupdate-order-wedding-venue/{{ $wedding_order->id }}" method="post" enctype="multipart/form-data">
                                                                        @method('put')
                                                                        {{ csrf_field() }}
                                                                        <div class="row">
                                                                            <div class="col-12 col-sm-12 col-md-12">
                                                                                <div class="row">
                                                                                    @foreach ($wedding_venues as $no_wedding_venue=>$wedding_venue)
                                                                                        @if ($wedding_venue)
                                                                                            @php
                                                                                                $venue_hotel = $hotels->where('id',$wedding_venue->hotel_id)->first();
                                                                                            @endphp
                                                                                                @if ($venue_hotel)
                                                                                                <div class="col-md-4 m-b-8">
                                                                                                    <div class="card">
                                                                                                        <img class="card-img" src="{{ asset ('storage/vendors/package/' . $wedding_venue->cover) }}" alt="{{ $wedding_venue->service }}">
                                                                                                        <input type="checkbox" id="wedding_venue_id[]" name="wedding_venue_id[]" value="{{ $wedding_venue->id }}">
                                                                                                        <div class="name-card">
                                                                                                            <b>{{ $venue_hotel->name }}</b>
                                                                                                            <p>{{ $wedding_venue->service }}</p>
                                                                                                        </div>
                                                                                                        <div class="label-capacity">{{ $wedding_venue->capacity." guests" }}</div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endif
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                                @error('wedding_venue_id[]')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                            <button type="submit" form="addweddingvenue" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($wed_venues_id != "null" and $wed_venues_id )
                                            @foreach ($wed_venues_id as $wed_venue_id)
                                                <div class="col-md-6 m-b-8">
                                                    @php
                                                        $wedding_venue = $wedding_venues->where('id',$wed_venue_id)->first();
                                                    @endphp
                                                    @if ($wedding_venue)
                                                        @php
                                                            $vendor_venue = $vendors->where('id',$wedding_venue->vendor_id)->first();
                                                        @endphp
                                                        <div class="card">
                                                            <a href="#" data-toggle="modal" data-target="#detail-wedding_venue-{{ $wed_venue_id }}">
                                                                <div class="card-image-container">
                                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $wedding_venue->cover) }}" alt="{{ $wedding_venue->name }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $vendor_venue->name }}</b>
                                                                        <p>{{ $wedding_venue->service }}</p>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="price-card-usd m-t-8">
                                                                    {{ currencyFormatUsd($wedding_venue->publish_rate) }}
                                                                </div>
                                                                <div class="label-capacity">
                                                                    <i class="icon-copy fa fa-users" aria-hidden="true"></i> {{ $wedding_venue->capacity }}
                                                                </div>
                                                            </a>
                                                        </div>
                                                        {{-- MODAL WEDDING VENUE --------------------------------------------------------------------------------------------------------------- --}}
                                                        <div class="modal fade" id="detail-wedding_venue-{{ $wed_venue_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fi-torso"></i><i class="icon-copy fi-torso-female"></i>{{ $wedding_venue->service }}</div>
                                                                        </div>
                                                                        <div class="card-banner m-b-8">
                                                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $wedding_venue->cover) }}" alt="{{ $wedding_venue->cover }}" loading="lazy">
                                                                        </div>
                                                                        @if ($wedding_venue->service)
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-4">
                                                                                        <b>Wedding Venue: </b><p>{!! $wedding_venue->service !!}</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Duration: </b><p>{!! $wedding_venue->duration." ".$wedding_venue->time !!}</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Capacity: </b><p>{{ $wedding_venue->capacity." guests" }}</p>
                                                                                    </div>
                                                                                    
                                                                                    @if ($wedding_venue->description)
                                                                                        <div class="col-sm-12">
                                                                                            <b>Description: </b><p>{!! $wedding_venue->description !!}</p>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                        <div class="modal-label-price">
                                                                            {{ currencyFormatUsd($wedding_venue->publish_rate) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-md-12 m-b-8">
                                                <p>The wedding venue have not been added to the wedding package yet.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- ORDER WEDDING ROOM --}}
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="tab-inner-title">
                                                Suites and Villas
                                                @if ($order->status == "Pending")
                                                    <span>
                                                        @if ($wedding_order->wedding_room_id !== "null" and $wedding_order->wedding_room_id)
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-room"> 
                                                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Wedding Room" aria-hidden="true"></i>
                                                            </a>
                                                        @else
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-room"> 
                                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add Wedding Room" aria-hidden="true"></i>
                                                            </a>
                                                        @endif
                                                    </span>
                                                    {{-- MODAL UPDATE ORDER WEDDING ROOM --------------------------------------------------------------------------------------------------------------- --}}
                                                    <div class="modal fade" id="add-wedding-room" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil"></i> Suites and Villas</div>
                                                                    </div>
                                                                    <form id="addRooms" action="/fupdate-order-wedding-room/{{ $wedding_order->id }}" method="post" enctype="multipart/form-data">
                                                                        @method('put')
                                                                        {{ csrf_field() }}
                                                                        <div class="row">
                                                                            <div class="col-12 col-sm-12 col-md-12">
                                                                                <div class="row">
                                                                                    @foreach ($wedding_rooms as $wedding_room)
                                                                                        @php
                                                                                            $addrooms = $suite_and_villas->where('id',$wedding_room->id)->first();
                                                                                            if ($addrooms) {
                                                                                                $viewroomandprice = $room_price->where('rooms_id',$addrooms->id)->where('start_date','<',$order->wedding_date)->where('end_date','>',$order->wedding_date)->first();
                                                                                                if ($viewroomandprice) {
                                                                                                    $viewcr_room = ceil($viewroomandprice->contract_rate/$usdrates->rate); 
                                                                                                    $viewcr_markup = $viewcr_room + $viewroomandprice->markup;
                                                                                                    $viewcr_tax =  ceil($viewcr_markup * ($taxes->tax / 100));
                                                                                                    $viewroomprice = ($viewcr_tax + $viewcr_markup)*$wedding_order->duration;
                                                                                                }else{
                                                                                                    $viewroomprice = 0;
                                                                                                }
                                                                                            }else{
                                                                                                $viewroomprice = 0;
                                                                                            }
                                                                                        @endphp
                                                                                        @if ($wedding_room)
                                                                                            @php
                                                                                                $hotelsroom = $hotels->where('id',$wedding_room->hotels_id)->first();
                                                                                            @endphp
                                                                                            <div class="col-md-4 m-b-8">
                                                                                                <div class="card">
                                                                                                    <img class="card-img" src="{{ asset ('storage/hotels/hotels-room/' . $wedding_room->cover) }}" alt="{{ $wedding_room->service }}">
                                                                                                    <input type="checkbox" name="rooms_id[]" value="{{ $wedding_room->id }}">
                                                                                                    <div class="name-card">
                                                                                                        <b>{{ $hotelsroom->name }}</b>
                                                                                                        <p>{{ $wedding_room->rooms }}</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="label-capacity m-l-18">{{ currencyFormatUsd($viewroomprice) }}</div>
                                                                                            </div>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                                @error('rooms_id[]')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                            <button type="submit" form="addRooms" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($rooms_id != "null" and $rooms_id )
                                            @foreach ($rooms_id as $roomsid)
                                                @php
                                                    $rooms = $suite_and_villas->where('id',$roomsid)->first();
                                                    if ($rooms) {
                                                        $roomandprice = $room_price->where('rooms_id',$rooms->id)->first();
                                                        $cr_room = ceil($roomandprice->contract_rate/$usdrates->rate); 
                                                        $cr_markup = $cr_room + $roomandprice->markup;
                                                        $cr_tax =  ceil($cr_markup * ($taxes->tax / 100));
                                                        $roomprice = ($cr_tax + $cr_markup)*$wedding_order->duration;
                                                    }else{
                                                        $roomprice = 0;
                                                    }
                                                @endphp
                                                @if ($rooms)
                                                    @php
                                                        $hotel_room = $hotels->where('id',$rooms->hotels_id)->first();
                                                    @endphp
                                                    <div class="col-md-6 m-b-8">
                                                        <div class="card">
                                                            <a href="#" data-toggle="modal" data-target="#detail-rooms-{{ $roomsid }}">
                                                                <div class="card-image-container">
                                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/hotels/hotels-room/' . $rooms->cover) }}" alt="{{ $rooms->name }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $hotel_room->name }}</b>
                                                                        <p>{{ $rooms->rooms }}</p>
                                                                    </div>
                                                                </div>
                                                                @if ($roomandprice)
                                                                    <div class="price-card-usd m-t-8">
                                                                        {{ currencyFormatUsd($roomprice) }}
                                                                    </div>
                                                                @endif
                                                                
                                                                <div class="label-capacity">
                                                                    <i class="icon-copy fa fa-moon-o" aria-hidden="true"></i> {{ $order->duration }}
                                                                </div>
                                                            </a>
                                                        </div>
                                                        {{-- MODAL DETAIL WEDDING SUITE AND VILLAS --------------------------------------------------------------------------------------------------------------- --}}
                                                        <div class="modal fade" id="detail-rooms-{{ $roomsid }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-eye"></i>{{ $hotel_room->name." - ". $rooms->rooms }}</div>
                                                                        </div>
                                                                        <div class="card-banner m-b-8">
                                                                            <img class="rounded" src="{{ asset('storage/hotels/hotels-room/' . $rooms->cover) }}" alt="{{ $rooms->cover }}" loading="lazy">
                                                                        </div>
                                                                        @if ($hotel_room->name)
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-4">
                                                                                        <b>Service: </b><p>Suites and Villas</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Duration: </b><p>{!! $order->duration." nights" !!}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                        <div class="modal-label-price">
                                                                            @if ($roomandprice)
                                                                            {{ currencyFormatUsd($roomprice) }}
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <div class="col-md-12 m-b-8">
                                                <p>Suites and Villas have not been added to the wedding package yet.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- ORDER WEDDING MAKE-UP --}}
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="tab-inner-title">
                                                Wedding Make-up
                                                @if ($order->status == "Pending")
                                                    <span>
                                                        @if ($wedding_order->wedding_makeup_id !== "null" and $wedding_order->wedding_makeup_id)
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-makeup"> 
                                                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Wedding Makeup" aria-hidden="true"></i>
                                                            </a>
                                                        @else
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-makeup"> 
                                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add Wedding Makeup" aria-hidden="true"></i>
                                                            </a>
                                                        @endif
                                                    </span>
                                                    {{-- MODAL UPDATE ORDER WEDDING MAKEUP --------------------------------------------------------------------------------------------------------------- --}}
                                                    <div class="modal fade" id="add-wedding-makeup" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil"></i> Update Wedding Make-up</div>
                                                                    </div>
                                                                    <form id="addweddingmakeup" action="/fupdate-order-wedding-makeup/{{ $wedding_order->id }}" method="post" enctype="multipart/form-data">
                                                                        @method('put')
                                                                        {{ csrf_field() }}
                                                                        <div class="row">
                                                                            <div class="col-12 col-sm-12 col-md-12">
                                                                                <div class="row">
                                                                                    @foreach ($wedding_makeups as $wedding_makeup)
                                                                                        @if ($wedding_makeup)
                                                                                            <div class="col-md-4 m-b-8">
                                                                                                <div class="card">
                                                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $wedding_makeup->cover) }}" alt="{{ $wedding_makeup->service }}">
                                                                                                    <input type="checkbox" id="wedding_makeup_id[]" name="wedding_makeup_id[]" value="{{ $wedding_makeup->id }}">
                                                                                                    <div class="name-card">
                                                                                                        <b>{{ $wedding_makeup->vendors->name }}</b>
                                                                                                        <p>{{ $wedding_makeup->service }}</p>
                                                                                                    </div>
                                                                                                    <div class="label-capacity">{{ $wedding_makeup->duration." ".$wedding_makeup->time }}</div>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                                @error('wedding_makeup_id[]')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                            <button type="submit" form="addweddingmakeup" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($wed_makeups_id != "null" and $wed_makeups_id )
                                            @foreach ($wed_makeups_id as $wed_makeup_id)
                                                <div class="col-md-6 m-b-8">
                                                    @php
                                                        $wedding_makeup = $wedding_makeups->where('id',$wed_makeup_id)->first();
                                                    @endphp
                                                    @if ($wedding_makeup)
                                                        @php
                                                            $vendor_makeup = $vendors->where('id',$wedding_makeup->vendor_id)->first();
                                                        @endphp
                                                        <div class="card">
                                                            <a href="#" data-toggle="modal" data-target="#detail-wedding_makeup-{{ $wed_makeup_id }}">
                                                                <div class="card-image-container">
                                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $wedding_makeup->cover) }}" alt="{{ $wedding_makeup->name }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $vendor_makeup->name }}</b>
                                                                        <p>{{ $wedding_makeup->service }}</p>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="price-card-usd m-t-8">
                                                                    {{ currencyFormatUsd($wedding_makeup->publish_rate) }}
                                                                </div>
                                                                <div class="label-capacity">
                                                                    <i class="icon-copy fa fa-clock-o" aria-hidden="true"></i> {{ $wedding_makeup->duration." ".$wedding_makeup->time }}
                                                                </div>
                                                            </a>
                                                        </div>
                                                        {{-- MODAL DETAIL WEDDING VENUE --------------------------------------------------------------------------------------------------------------- --}}
                                                        <div class="modal fade" id="detail-wedding_makeup-{{ $wed_makeup_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-eye"></i>{{ $wedding_makeup->service }}</div>
                                                                        </div>
                                                                        <div class="card-banner m-b-8">
                                                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $wedding_makeup->cover) }}" alt="{{ $wedding_makeup->cover }}" loading="lazy">
                                                                        </div>
                                                                        @if ($wedding_makeup->service)
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-4">
                                                                                        <b>Service: </b><p>{!! $wedding_makeup->service !!}</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Duration: </b><p>{!! $wedding_makeup->duration." ".$wedding_makeup->time !!}</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Capacity: </b><p>{{ $wedding_makeup->capacity." guests" }}</p>
                                                                                    </div>
                                                                                    
                                                                                    @if ($wedding_makeup->description)
                                                                                        <div class="col-sm-12">
                                                                                            <b>Description: </b><p>{!! $wedding_makeup->description !!}</p>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                        <div class="modal-label-price">
                                                                            {{ currencyFormatUsd($wedding_makeup->publish_rate) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-md-12 m-b-8">
                                                <p>The Wedding Venue have not been added to the wedding package yet.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- ORDER WEDDING DECORATION --}}
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="tab-inner-title">
                                                Wedding Decoration
                                                @if ($order->status == "Pending")
                                                    <span>
                                                        @if ($wedding_order->wedding_decoration_id !== "null" and $wedding_order->wedding_decoration_id)
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-decoration"> 
                                                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Wedding Decoration" aria-hidden="true"></i>
                                                            </a>
                                                        @else
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-decoration"> 
                                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add Wedding Decoration" aria-hidden="true"></i>
                                                            </a>
                                                        @endif
                                                    </span>
                                                    {{-- MODAL UPDATE ORDER WEDDING DECORATION --------------------------------------------------------------------------------------------------------------- --}}
                                                    <div class="modal fade" id="add-wedding-decoration" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil"></i> Update Wedding Decoration</div>
                                                                    </div>
                                                                    <form id="addweddingdecoration" action="/fupdate-order-wedding-decoration/{{ $wedding_order->id }}" method="post" enctype="multipart/form-data">
                                                                        @method('put')
                                                                        {{ csrf_field() }}
                                                                        <div class="row">
                                                                            <div class="col-12 col-sm-12 col-md-12">
                                                                                <div class="row">
                                                                                    @foreach ($wedding_decorations as $wedding_decoration)
                                                                                        @if ($wedding_decoration)
                                                                                            <div class="col-md-4 m-b-8">
                                                                                                <div class="card">
                                                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $wedding_decoration->cover) }}" alt="{{ $wedding_decoration->service }}">
                                                                                                    <input type="checkbox" id="wedding_decoration_id[]" name="wedding_decoration_id[]" value="{{ $wedding_decoration->id }}">
                                                                                                    <div class="name-card">
                                                                                                        <b>{{ $wedding_decoration->vendors->name }}</b>
                                                                                                        <p>{{ $wedding_decoration->service }}</p>
                                                                                                    </div>
                                                                                                    <div class="label-capacity">{{ $wedding_decoration->duration." ".$wedding_decoration->time }}</div>
                                                                                                </div>
                                                                                            </div>
                                                                                            
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                                @error('wedding_decoration_id[]')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                            <button type="submit" form="addweddingdecoration" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($wed_decorations_id != "null" and $wed_decorations_id )
                                            @foreach ($wed_decorations_id as $wed_decoration_id)
                                                <div class="col-md-6 m-b-8">
                                                    @php
                                                        $wedding_decoration = $wedding_decorations->where('id',$wed_decoration_id)->first();
                                                    @endphp
                                                    @if ($wedding_decoration)
                                                        @php
                                                            $vendor_decoration = $vendors->where('id',$wedding_decoration->vendor_id)->first();
                                                        @endphp
                                                        <div class="card">
                                                            <a href="#" data-toggle="modal" data-target="#detail-wedding_decoration-{{ $wed_decoration_id }}">
                                                                <div class="card-image-container">
                                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $wedding_decoration->cover) }}" alt="{{ $wedding_decoration->name }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $vendor_decoration->name }}</b>
                                                                        <p>{{ $wedding_decoration->service }}</p>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="price-card-usd m-t-8">
                                                                    {{ currencyFormatUsd($wedding_decoration->publish_rate) }}
                                                                </div>
                                                                <div class="label-capacity">
                                                                    <i class="icon-copy fa fa-users" aria-hidden="true"></i> {{ $wedding_decoration->capacity }}
                                                                </div>
                                                            </a>
                                                        </div>
                                                        {{-- MODAL DETAIL WEDDING DECORATION --------------------------------------------------------------------------------------------------------------- --}}
                                                        <div class="modal fade" id="detail-wedding_decoration-{{ $wed_decoration_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-eye"></i>{{ $wedding_decoration->service }}</div>
                                                                        </div>
                                                                        <div class="card-banner m-b-8">
                                                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $wedding_decoration->cover) }}" alt="{{ $wedding_decoration->cover }}" loading="lazy">
                                                                        </div>
                                                                        @if ($wedding_decoration->service)
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-4">
                                                                                        <b>Service: </b><p>{!! $wedding_decoration->service !!}</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Duration: </b><p>{!! $wedding_decoration->duration." ".$wedding_decoration->time !!}</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Capacity: </b><p>{{ $wedding_decoration->capacity." guests" }}</p>
                                                                                    </div>
                                                                                    
                                                                                    @if ($wedding_decoration->description)
                                                                                        <div class="col-sm-12">
                                                                                            <b>Description: </b><p>{!! $wedding_decoration->description !!}</p>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                        <div class="modal-label-price">
                                                                            {{ currencyFormatUsd($wedding_decoration->publish_rate) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-md-12 m-b-8">
                                                <p>The decoration have not been added to the wedding package yet.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- ORDER WEDDING DINNER VENUE --}}
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="tab-inner-title">
                                                Wedding Dinner Venue
                                                @if ($order->status == "Pending")
                                                    <span>
                                                        @if ($wedding_order->wedding_dinner_venue_id !== "null" and $wedding_order->wedding_dinner_venue_id)
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-dinner_venue"> 
                                                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Wedding Decoration" aria-hidden="true"></i>
                                                            </a>
                                                        @else
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-dinner_venue"> 
                                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add Wedding Decoration" aria-hidden="true"></i>
                                                            </a>
                                                        @endif
                                                    </span>
                                                    {{-- MODAL UPDATE ORDER WEDDING DINNER VENUE --------------------------------------------------------------------------------------------------------------- --}}
                                                    <div class="modal fade" id="add-wedding-dinner_venue" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil"></i> Update Wedding Dinner Venue</div>
                                                                    </div>
                                                                    <form id="addweddingdinner_venue" action="/fupdate-order-wedding-dinner_venue/{{ $wedding_order->id }}" method="post" enctype="multipart/form-data">
                                                                        @method('put')
                                                                        {{ csrf_field() }}
                                                                        <div class="row">
                                                                            <div class="col-12 col-sm-12 col-md-12">
                                                                                <div class="row">
                                                                                    @foreach ($wedding_dinner_venues as $wedding_dinner_venue)
                                                                                        @if ($wedding_dinner_venue)
                                                                                            <div class="col-md-4 m-b-8">
                                                                                                <div class="card">
                                                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $wedding_dinner_venue->cover) }}" alt="{{ $wedding_dinner_venue->service }}">
                                                                                                    <input type="checkbox" id="wedding_dinner_venue_id[]" name="wedding_dinner_venue_id[]" value="{{ $wedding_dinner_venue->id }}">
                                                                                                    <div class="name-card">
                                                                                                        <b>{{ $wedding_dinner_venue->vendors->name }}</b>
                                                                                                        <p>{{ $wedding_dinner_venue->service }}</p>
                                                                                                    </div>
                                                                                                    <div class="label-capacity">{{ $wedding_dinner_venue->duration." ".$wedding_dinner_venue->time }}</div>
                                                                                                </div>
                                                                                            </div>
                                                                                            
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                                @error('wedding_dinner_venue_id[]')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                            <button type="submit" form="addweddingdinner_venue" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($wed_dinner_venues_id != "null" and $wed_dinner_venues_id )
                                            @foreach ($wed_dinner_venues_id as $wed_dinner_venue_id)
                                                <div class="col-md-6 m-b-8">
                                                    @php
                                                        $wedding_dinner_venue = $wedding_dinner_venues->where('id',$wed_dinner_venue_id)->first();
                                                    @endphp
                                                    @if ($wedding_dinner_venue)
                                                        @php
                                                            $vendor_dinner_venue = $vendors->where('id',$wedding_dinner_venue->vendor_id)->first();
                                                        @endphp
                                                        <div class="card">
                                                            <a href="#" data-toggle="modal" data-target="#detail-wedding_dinner_venue-{{ $wed_dinner_venue_id }}">
                                                                <div class="card-image-container">
                                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $wedding_dinner_venue->cover) }}" alt="{{ $wedding_dinner_venue->name }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $vendor_dinner_venue->name }}</b>
                                                                        <p>{{ $wedding_dinner_venue->service }}</p>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="price-card-usd m-t-8">
                                                                    {{ currencyFormatUsd($wedding_dinner_venue->publish_rate) }}
                                                                </div>
                                                                <div class="label-capacity">
                                                                    <i class="icon-copy fa fa-users" aria-hidden="true"></i> {{ $wedding_dinner_venue->capacity }}
                                                                </div>
                                                            </a>
                                                        </div>
                                                        {{-- MODAL DETAIL WEDDING DINNER VENUE --------------------------------------------------------------------------------------------------------------- --}}
                                                        <div class="modal fade" id="detail-wedding_dinner_venue-{{ $wed_dinner_venue_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-eye"></i>{{ $wedding_dinner_venue->service }}</div>
                                                                        </div>
                                                                        <div class="card-banner m-b-8">
                                                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $wedding_dinner_venue->cover) }}" alt="{{ $wedding_dinner_venue->cover }}" loading="lazy">
                                                                        </div>
                                                                        @if ($wedding_dinner_venue->service)
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-4">
                                                                                        <b>Service: </b><p>{!! $wedding_dinner_venue->service !!}</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Duration: </b><p>{!! $wedding_dinner_venue->duration." ".$wedding_dinner_venue->time !!}</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Capacity: </b><p>{{ $wedding_dinner_venue->capacity." guests" }}</p>
                                                                                    </div>
                                                                                    
                                                                                    @if ($wedding_dinner_venue->description)
                                                                                        <div class="col-sm-12">
                                                                                            <b>Description: </b><p>{!! $wedding_dinner_venue->description !!}</p>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                        <div class="modal-label-price">
                                                                            {{ currencyFormatUsd($wedding_dinner_venue->publish_rate) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-md-12 m-b-8">
                                                <p>The dinner venue have not been added to the wedding package yet.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- ORDER WEDDING ENTERTAINMENT --}}
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="tab-inner-title">
                                                Wedding Entertainment
                                                @if ($order->status == "Pending")
                                                    <span>
                                                        @if ($wedding_order->wedding_entertainment_id !== "null" and $wedding_order->wedding_entertainment_id)
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-entertainment"> 
                                                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Wedding Entertainment" aria-hidden="true"></i>
                                                            </a>
                                                        @else
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-entertainment"> 
                                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add Wedding Entertainment" aria-hidden="true"></i>
                                                            </a>
                                                        @endif
                                                    </span>
                                                    {{-- MODAL UPDATE ORDER WEDDING ENTERTAINMENT --------------------------------------------------------------------------------------------------------------- --}}
                                                    <div class="modal fade" id="add-wedding-entertainment" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil"></i> Update Wedding Entertainment</div>
                                                                    </div>
                                                                    <form id="addweddingentertainment" action="/fupdate-order-wedding-entertainment/{{ $wedding_order->id }}" method="post" enctype="multipart/form-data">
                                                                        @method('put')
                                                                        {{ csrf_field() }}
                                                                        <div class="row">
                                                                            <div class="col-12 col-sm-12 col-md-12">
                                                                                <div class="row">
                                                                                    @foreach ($wedding_entertainments as $wedding_entertainment)
                                                                                        @if ($wedding_entertainment)
                                                                                            <div class="col-md-4 m-b-8">
                                                                                                <div class="card">
                                                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $wedding_entertainment->cover) }}" alt="{{ $wedding_entertainment->service }}">
                                                                                                    <input type="checkbox" id="wedding_entertainment_id[]" name="wedding_entertainment_id[]" value="{{ $wedding_entertainment->id }}">
                                                                                                    <div class="name-card">
                                                                                                        <b>{{ $wedding_entertainment->vendors->name }}</b>
                                                                                                        <p>{{ $wedding_entertainment->service }}</p>
                                                                                                    </div>
                                                                                                    <div class="label-capacity">{{ $wedding_entertainment->duration." ".$wedding_entertainment->time }}</div>
                                                                                                </div>
                                                                                            </div>
                                                                                            
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                                @error('wedding_entertainment_id[]')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                            <button type="submit" form="addweddingentertainment" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($wed_entertainments_id != "null" and $wed_entertainments_id )
                                            @foreach ($wed_entertainments_id as $wed_entertainment_id)
                                                <div class="col-md-6 m-b-8">
                                                    @php
                                                        $wedding_entertainment = $wedding_entertainments->where('id',$wed_entertainment_id)->first();
                                                    @endphp
                                                    @if ($wedding_entertainment)
                                                        @php
                                                            $vendor_entertainment = $vendors->where('id',$wedding_entertainment->vendor_id)->first();
                                                        @endphp
                                                        <div class="card">
                                                            <a href="#" data-toggle="modal" data-target="#detail-wedding-entertainment-{{ $wed_entertainment_id }}">
                                                                <div class="card-image-container">
                                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $wedding_entertainment->cover) }}" alt="{{ $wedding_entertainment->name }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $vendor_entertainment->name }}</b>
                                                                        <p>{{ $wedding_entertainment->service }}</p>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="price-card-usd m-t-8">
                                                                    {{ currencyFormatUsd($wedding_entertainment->publish_rate) }}
                                                                </div>
                                                                <div class="label-capacity">
                                                                    <i class="icon-copy fa fa-clock-o" aria-hidden="true"></i> {{ $wedding_entertainment->duration." ".$wedding_entertainment->time }}
                                                                </div>
                                                            </a>
                                                        </div>
                                                        {{-- MODAL DETAIL WEDDING ENTERTAINMENT --------------------------------------------------------------------------------------------------------------- --}}
                                                        <div class="modal fade" id="detail-wedding-entertainment-{{ $wed_entertainment_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-eye"></i> {{ $wedding_entertainment->service }}</div>
                                                                        </div>
                                                                        <div class="card-banner m-b-8">
                                                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $wedding_entertainment->cover) }}" alt="{{ $wedding_entertainment->cover }}" loading="lazy">
                                                                        </div>
                                                                        @if ($wedding_entertainment->service)
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-4">
                                                                                        <b>Service: </b><p>{!! $wedding_entertainment->service !!}</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Duration: </b><p>{!! $wedding_entertainment->duration." ".$wedding_entertainment->time !!}</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Capacity: </b><p>{{ $wedding_entertainment->capacity." guests" }}</p>
                                                                                    </div>
                                                                                    
                                                                                    @if ($wedding_entertainment->description)
                                                                                        <div class="col-sm-12">
                                                                                            <b>Description: </b><p>{!! $wedding_entertainment->description !!}</p>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                        <div class="modal-label-price">
                                                                            {{ currencyFormatUsd($wedding_entertainment->publish_rate) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-md-12 m-b-8">
                                                <p>The Wedding Venue have not been added to the wedding package yet.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- ORDER WEDDING DOCUMENTATION --}}
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="tab-inner-title">
                                                Wedding Documentation
                                                @if ($order->status == "Pending")
                                                    <span>
                                                        @if ($wedding_order->wedding_documentation_id !== "null" and $wedding_order->wedding_documentation_id)
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-documentation"> 
                                                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Wedding Documentation" aria-hidden="true"></i>
                                                            </a>
                                                        @else
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-documentation"> 
                                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add Wedding Documentation" aria-hidden="true"></i>
                                                            </a>
                                                        @endif
                                                    </span>
                                                    {{-- MODAL UPDATE ORDER WEDDING DOCUMENTATION --------------------------------------------------------------------------------------------------------------- --}}
                                                    <div class="modal fade" id="add-wedding-documentation" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil"></i> Add Wedding Documentation</div>
                                                                    </div>
                                                                    <form id="addweddingdocumentation" action="/fupdate-order-wedding-documentation/{{ $wedding_order->id }}" method="post" enctype="multipart/form-data">
                                                                        @method('put')
                                                                        {{ csrf_field() }}
                                                                        <div class="row">
                                                                            <div class="col-12 col-sm-12 col-md-12">
                                                                                <div class="row">
                                                                                    @foreach ($wedding_documentations as $wedding_documentation)
                                                                                        @if ($wedding_documentation)
                                                                                            <div class="col-md-4 m-b-8">
                                                                                                <div class="card">
                                                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $wedding_documentation->cover) }}" alt="{{ $wedding_documentation->service }}">
                                                                                                    <input type="checkbox" id="wedding_documentation_id[]" name="wedding_documentation_id[]" value="{{ $wedding_documentation->id }}">
                                                                                                    <div class="name-card">
                                                                                                        <b>{{ $wedding_documentation->vendors->name }}</b>
                                                                                                        <p>{{ $wedding_documentation->service }}</p>
                                                                                                    </div>
                                                                                                    <div class="label-capacity">{{ $wedding_documentation->duration." ".$wedding_documentation->time }}</div>
                                                                                                </div>
                                                                                            </div>
                                                                                            
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                                @error('wedding_documentation_id[]')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                            <button type="submit" form="addweddingdocumentation" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($wed_documentations_id != "null" and $wed_documentations_id )
                                            @foreach ($wed_documentations_id as $wed_documentation_id)
                                                <div class="col-md-6 m-b-8">
                                                    @php
                                                        $wedding_documentation = $wedding_documentations->where('id',$wed_documentation_id)->first();
                                                    @endphp
                                                    @if ($wedding_documentation)
                                                        @php
                                                            $vendor_documentation = $vendors->where('id',$wedding_documentation->vendor_id)->first();
                                                        @endphp
                                                        <div class="card">
                                                            <a href="#" data-toggle="modal" data-target="#detail-wedding_documentation-{{ $wed_documentation_id }}">
                                                                <div class="card-image-container">
                                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $wedding_documentation->cover) }}" alt="{{ $wedding_documentation->name }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $vendor_documentation->name }}</b>
                                                                        <p>{{ $wedding_documentation->service }}</p>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="price-card-usd m-t-8">
                                                                    {{ currencyFormatUsd($wedding_documentation->publish_rate) }}
                                                                </div>
                                                                <div class="label-capacity">
                                                                    <i class="icon-copy fa fa-clock-o" aria-hidden="true"></i> {{ $wedding_documentation->duration." ".$wedding_documentation->time }}
                                                                </div>
                                                            </a>
                                                        </div>
                                                        {{-- MODAL DETAIL WEDDING DOCUMENTATION --------------------------------------------------------------------------------------------------------------- --}}
                                                        <div class="modal fade" id="detail-wedding_documentation-{{ $wed_documentation_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-eye"></i>{{ $wedding_documentation->service }}</div>
                                                                        </div>
                                                                        <div class="card-banner m-b-8">
                                                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $wedding_documentation->cover) }}" alt="{{ $wedding_documentation->cover }}" loading="lazy">
                                                                        </div>
                                                                        @if ($wedding_documentation->service)
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-4">
                                                                                        <b>Service: </b><p>{!! $wedding_documentation->service !!}</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Duration: </b><p>{!! $wedding_documentation->duration." ".$wedding_documentation->time !!}</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Capacity: </b><p>{{ $wedding_documentation->capacity." guests" }}</p>
                                                                                    </div>
                                                                                    
                                                                                    @if ($wedding_documentation->description)
                                                                                        <div class="col-sm-12">
                                                                                            <b>Description: </b><p>{!! $wedding_documentation->description !!}</p>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                        <div class="modal-label-price">
                                                                            {{ currencyFormatUsd($wedding_documentation->publish_rate) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-md-12 m-b-8">
                                                <p>The documentation have not been added to the wedding package yet.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- ORDER WEDDING TRANSPORT --}}
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="tab-inner-title">
                                                Wedding Transport
                                                @if ($order->status == "Pending")
                                                    <span>
                                                        @if ($wedding_order->wedding_transport_id !== "null" and $wedding_order->wedding_transport_id)
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-transport"> 
                                                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Wedding Transport" aria-hidden="true"></i>
                                                            </a>
                                                        @else
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-transport"> 
                                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add Wedding Transport" aria-hidden="true"></i>
                                                            </a>
                                                        @endif
                                                    </span>
                                                    {{-- MODAL UPDATE ORDER WEDDING TRANSPORT --------------------------------------------------------------------------------------------------------------- --}}
                                                    <div class="modal fade" id="add-wedding-transport" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil"></i> Add Wedding Transport</div>
                                                                    </div>
                                                                    <form id="addweddingtransport" action="/fupdate-order-wedding-transport/{{ $wedding_order->id }}" method="post" enctype="multipart/form-data">
                                                                        @method('put')
                                                                        {{ csrf_field() }}
                                                                        <div class="row">
                                                                            <div class="col-12 col-sm-12 col-md-12">
                                                                                <div class="row">
                                                                                    @if ($wedding_transports)
                                                                                        @foreach ($wedding_transports as $wedding_transport)
                                                                                            @php
                                                                                                $wedding_hotel = $hotels->where('id',$order->subservice_id)->first();
                                                                                                $w_transport = $wedding_transports->where('id',$wedding_transport->id)->first();
                                                                                                $w_transport_price = $transport_price->where('transports_id',$w_transport->id)->where('duration', $wedding_hotel->airport_duration)->where('type',"Airport Shuttle")->first();
                                                                                            @endphp
                                                                                            @if ($w_transport_price)
                                                                                                <div class="col-md-4 m-b-8">
                                                                                                    <div class="card">
                                                                                                        <img class="card-img" src="{{ asset ('storage/transports/transports-cover/' . $wedding_transport->cover) }}" alt="{{ $wedding_transport->service }}">
                                                                                                        <input type="checkbox" id="wedding_transport_id[]" name="wedding_transport_id[]" value="{{ $wedding_transport->id }}">
                                                                                                        <div class="name-card">
                                                                                                            <b>{{ $wedding_transport->name }}</b>
                                                                                                            <p>{{ $wedding_transport->service }}</p>
                                                                                                        </div>
                                                                                                        <div class="label-capacity">{{ $wedding_transport->duration." ".$wedding_transport->time }}</div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    @endif
                                                                                </div>
                                                                                @error('wedding_transport_id[]')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                            <button type="submit" form="addweddingtransport" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($wed_transports_id != "null" and $wed_transports_id )
                                            @foreach ($wed_transports_id as $wed_transport_id)
                                                <div class="col-md-6 m-b-8">
                                                    @php
                                                        $htl = $hotels->where('id',$order->subservice_id)->first();
                                                        $wedding_transport = $wedding_transports->where('id',$wed_transport_id)->first();
                                                        $weddingTransport = $transport_price->where('transports_id',$wedding_transport->id)->where('duration', $htl->airport_duration)->where('type',"Airport Shuttle")->first();
                                                    @endphp
                                                    @if ($weddingTransport)
                                                        @php
                                                            
                                                            if ($weddingTransport) {
                                                                $trns_usd_cr = ceil($weddingTransport->contract_rate / $usdrates->rate);
                                                                $crmr = $trns_usd_cr + $weddingTransport->markup;
                                                                $trns_tax = ceil($crmr*($taxes->tax / 100));
                                                                $weddingTransportPrice = $trns_tax + $crmr;
                                                            }else {
                                                                $weddingTransportPrice = 0;
                                                            }
                                                        @endphp
                                                        <div class="card">
                                                            <a href="#" data-toggle="modal" data-target="#detail-wedding-transport-{{ $wed_transport_id }}">
                                                                <div class="card-image-container">
                                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/transports/transports-cover/' . $wedding_transport->cover) }}" alt="{{ $wedding_transport->name }}">
                                                                    <div class="name-card">
                                                                        <p>{{ $wedding_transport->brand }}</p>
                                                                        <p>{{ $wedding_transport->name }}</p>
                                                                    </div>
                                                                </div>
                                                                @if ($weddingTransportPrice > 0)
                                                                    <div class="price-card-usd m-t-8">
                                                                        {{ currencyFormatUsd($weddingTransportPrice) }}
                                                                    </div>
                                                                @endif
                                                                <div class="label-capacity">
                                                                    <i class="icon-copy fa fa-users" aria-hidden="true"></i> {{ $wedding_transport->capacity }}
                                                                </div>
                                                            </a>
                                                        </div>
                                                        {{-- MODAL DETAIL WEDDING TRANSPORT --------------------------------------------------------------------------------------------------------------- --}}
                                                        <div class="modal fade" id="detail-wedding-transport-{{ $wed_transport_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-eye"></i>Wedding Transport</div>
                                                                        </div>
                                                                        <div class="card-banner m-b-8">
                                                                            <img class="rounded" src="{{ asset('storage/transports/transports-cover/' . $wedding_transport->cover) }}" alt="{{ $wedding_transport->cover }}" loading="lazy">
                                                                        </div>
                                                                        @if ($wedding_transport->name)
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-4">
                                                                                        <b>Transport: </b><p>{!! $wedding_transport->brand." - ".$wedding_transport->name !!}</p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Service: </b>
                                                                                        <p>
                                                                                            {!! $weddingTransport->type !!}<br>
                                                                                            {!! "Airport - ".$htl->name !!}
                                                                                        </p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Capacity: </b><p>{{ $wedding_transport->capacity." guests" }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                        <div class="modal-label-price">
                                                                            {{ currencyFormatUsd($weddingTransportPrice) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-md-12 m-b-8">
                                                <p>Transportation have not been added to the wedding package yet.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {{-- ORDER WEDDING OTHER SERVICE --}}
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="tab-inner-title">
                                                Wedding Other Service
                                                @if ($order->status == "Pending")
                                                    <span>
                                                        @if ($wedding_order->wedding_other_id !== "null" and $wedding_order->wedding_other_id)
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-other"> 
                                                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Wedding Entertainment" aria-hidden="true"></i>
                                                            </a>
                                                        @else
                                                            <a href="#" data-toggle="modal" data-target="#add-wedding-other"> 
                                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add Wedding Entertainment" aria-hidden="true"></i>
                                                            </a>
                                                        @endif
                                                    </span>
                                                    {{-- MODAL UPDATE ORDER WEDDING OTHER SERVICE --------------------------------------------------------------------------------------------------------------- --}}
                                                    <div class="modal fade" id="add-wedding-other" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil"></i> Update Wedding Other Service</div>
                                                                    </div>
                                                                    <form id="addweddingother" action="/fupdate-order-wedding-other/{{ $wedding_order->id }}" method="post" enctype="multipart/form-data">
                                                                        @method('put')
                                                                        {{ csrf_field() }}
                                                                        <div class="row">
                                                                            <div class="col-12 col-sm-12 col-md-12">
                                                                                <div class="row">
                                                                                    @foreach ($wedding_others as $wedding_other)
                                                                                        @if ($wedding_other)
                                                                                            <div class="col-md-4 m-b-8">
                                                                                                <div class="card">
                                                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $wedding_other->cover) }}" alt="{{ $wedding_other->service }}">
                                                                                                    <input type="checkbox" id="wedding_other_id[]" name="wedding_other_id[]" value="{{ $wedding_other->id }}">
                                                                                                    <div class="name-card">
                                                                                                        <b>{{ $wedding_other->vendors->name }}</b>
                                                                                                        <p>{{ $wedding_other->service }}</p>
                                                                                                    </div>
                                                                                                    <div class="label-capacity">{{ $wedding_other->duration." ".$wedding_other->time }}</div>
                                                                                                </div>
                                                                                            </div>
                                                                                            
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                                @error('wedding_other_id[]')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                            <button type="submit" form="addweddingother" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($wed_others_id != "null" and $wed_others_id )
                                            @foreach ($wed_others_id as $wed_other_id)
                                                <div class="col-md-6 m-b-8">
                                                    @php
                                                        $wedding_other = $wedding_others->where('id',$wed_other_id)->first();
                                                    @endphp
                                                    @if ($wedding_other)
                                                        @php
                                                            $vendor_other = $vendors->where('id',$wedding_other->vendor_id)->first();
                                                        @endphp
                                                        <div class="card">
                                                            <a href="#" data-toggle="modal" data-target="#detail-wedding-other-{{ $wed_other_id }}">
                                                                <div class="card-image-container">
                                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $wedding_other->cover) }}" alt="{{ $wedding_other->name }}">
                                                                    <div class="name-card">
                                                                        <b>{{ $vendor_other->name }}</b>
                                                                        <p>{{ $wedding_other->service }}</p>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="price-card-usd m-t-8">
                                                                    {{ currencyFormatUsd($wedding_other->publish_rate) }}
                                                                </div>
                                                                <div class="label-capacity">
                                                                    {{ $wedding_other->duration." ".$wedding_other->time }}
                                                                </div>
                                                            </a>
                                                        </div>
                                                        {{-- MODAL DETAIL WEDDING OTHER SERVICE --------------------------------------------------------------------------------------------------------------- --}}
                                                        <div class="modal fade" id="detail-wedding-other-{{ $wed_other_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-eye"></i> {{ $wedding_other->service }}</div>
                                                                        </div>
                                                                        <div class="card-banner m-b-8">
                                                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $wedding_other->cover) }}" alt="{{ $wedding_other->cover }}" loading="lazy">
                                                                        </div>
                                                                        @if ($wedding_other->service)
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-4">
                                                                                        <b>Service: </b>
                                                                                        <p>
                                                                                            {{ $wedding_other->vendors->name }}<br>
                                                                                            {!! $wedding_other->service !!}
                                                                                        </p>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <b>Duration: </b><p>{!! $wedding_other->duration." ".$wedding_other->time !!}</p>
                                                                                    </div>                                                                                    
                                                                                    @if ($wedding_other->description)
                                                                                        <div class="col-sm-12">
                                                                                            <b>Description: </b><p>{!! $wedding_other->description !!}</p>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                        <div class="modal-label-price">
                                                                            {{ currencyFormatUsd($wedding_other->publish_rate) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-md-12 m-b-8">
                                                <p>The Wedding Venue have not been added to the wedding package yet.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <div class="card-box-footer">
                                <a href="/orders-admin-{{ $order->id }}">
                                    <button class="btn btn-primary"><i class="icon-copy fa fa-arrow-left" aria-hidden="true"></i> Back</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection
