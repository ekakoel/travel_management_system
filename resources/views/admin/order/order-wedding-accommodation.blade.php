@section('title', __('messages.Order Wedding'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="info-action hide-print">
                @if (session('errors_message'))
                    <div class="alert alert-danger">
                        {{ session('errors_message') }}
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
            <div class="page-header hide-print">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <i class="icon-copy fa fa-shopping-basket" aria-hidden="true"></i>Order Wedding Accommodation
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="orders-admin">Orders Admin</a></li>
                                <li class="breadcrumb-item"><a href="validate-orders-wedding-{{ $orderWedding->id }}">{{ $orderWedding->orderno }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Order Accommodations</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="title">Order {{ $orderWedding->orderno }}</div>
                            <span>
                                <a href="#" data-toggle="modal" data-target="#add-accommodation-to-order-wedding-{{ $orderWedding->id }}"> 
                                    <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                </a>
                            </span>
                        </div>
                        {{-- MODAL ADD ACCOMMODATION  --}}
                        <div class="modal fade" id="add-accommodation-to-order-wedding-{{ $orderWedding->id }}" tabindex="-1"
                            role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content text-left">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i>
                                                @lang('messages.Accommodation')</div>
                                        </div>
                                        <form id="addWeddingPlannerAccommodations-{{ $orderWedding->id }}"
                                            action="/fadmin-add-order-wedding-accommodation/{{ $orderWedding->id }}" method="post"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="rooms_id">@lang('messages.Select Room') <span>*</span></label>
                                                        <div class="card-box-content">
                                                            @foreach ($rooms as $room)
                                                                <input type="radio" id="{{ 'acc_rv'. $room->id }}" name="rooms_id" value="{{ $room->id }}" data-room-capacity="{{ $room->capacity }}">
                                                                <label for="{{ 'acc_rv' . $room->id }}" class="label-radio">
                                                                    <div class="card h-100">
                                                                        <img class="card-img"
                                                                            src="{{ asset('storage/hotels/hotels-room/' . $room->cover) }}"
                                                                            alt="{{ $room->rooms }}">
                                                                        <div class="name-card">
                                                                            <b>{{ $room->rooms }}</b>
                                                                        </div>
                                                                        <div class="label-capacity">{{ $room->capacity . ' guests' }}
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($orderWedding->service != "Wedding Package")
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="checkin">@lang('messages.Check-in')</label>
                                                            <input readonly type="text" name="checkin" class="form-control date-picker @error('checkin') is-invalid @enderror" placeholder="@lang('messages.Check-in')" value="{{ old('checkin') }}" required>
                                                            @error('checkin')
                                                                <span class="invalid-feedback">
                                                                    {{ $message }}
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="checkout">@lang('messages.Check-out')</label>
                                                            <input readonly type="text" name="checkout" class="form-control date-picker @error('checkout') is-invalid @enderror" placeholder="@lang('messages.Check-out')" value="{{ old('checkout') }}" required>
                                                            @error('checkout')
                                                                <span class="invalid-feedback">
                                                                    {{ $message }}
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endif
                                                <div id="numberOfGuestsForm" hidden class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="number_of_guests">@lang('messages.Number of Guests')</label>
                                                        <input id="number_of_guests_room" type="number" name="number_of_guests"
                                                            min="1" max="{{ $room->capacity }}"
                                                            class="form-control @error('number_of_guests') is-invalid @enderror"
                                                            placeholder="@lang('messages.Number of guests')" value="{{ old('number_of_guests') }}"
                                                            required>
                                                        @error('number_of_guests')
                                                            <span class="invalid-feedback">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div id="guestsNameForm" hidden class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="guest_detail">@lang('messages.Guests Name')</label>
                                                        <input type="text" name="guest_detail"
                                                            class="form-control @error('guest_detail') is-invalid @enderror"
                                                            placeholder="@lang('messages.Guests Name')" value="{{ old('guest_detail') }}"
                                                            required>
                                                        @error('guest_detail')
                                                            <span class="invalid-feedback">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div id="extraBedIdForm" hidden class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="extra_bed_id">@lang('messages.Extra Bed') <span>*</span></label>
                                                        <select name="extra_bed_id"
                                                            class="custom-select col-12 @error('extra_bed_id') is-invalid @enderror">
                                                            <option selected value="">@lang('messages.None')</option>
                                                            @foreach ($extraBeds as $extra_bed)
                                                                <option value="{{ $extra_bed->id }}">{{ $extra_bed->type }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('extra_bed_id')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="remark" class="form-label">@lang('messages.Remark')</label>
                                                        <textarea name="remark" class="textarea_editor form-control @error('remark') is-invalid @enderror"
                                                            placeholder="Insert remark" value="@lang('messages.Remark')">{!! old('remark') !!}</textarea>
                                                        @error('remark')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <input id="roomForForm" type="hidden" name="room_for" value="Inv">
                                            </div>
                                        </form>
                                        <div class="card-box-footer">
                                            <button type="submit" form="addWeddingPlannerAccommodations-{{ $orderWedding->id }}"
                                                class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                                                @lang('messages.Add')</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                                    class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="data-table table nowrap dataTable no-footer dtr-inline">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Hotel</th>
                                    <th>NOG</th>
                                    <th>Extra Bed</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </th>
                            </thead>
                            <tbody>
                                @php
                                    $exbtp = [];
                                    $invitation_accommodation_price = $accommodationInvs->pluck('public_rate')->sum();
                                @endphp
                                @if ($orderWedding->service == "Wedding Package")
                                    <tr>
                                        <td class="pd-2-8">
                                            {{ date('d M Y',strtotime($orderWedding->checkin)) }} - {{ date('d M Y',strtotime($orderWedding->checkout)) }} <br>
                                            {{ $orderWedding->duration." night" }}
                                        </td>
                                        <td class="pd-2-8">
                                            {{ $orderWedding->hotel->name }} | {{ $orderWedding->suite_villa->rooms }}<br>
                                            {{ $orderWedding->bride->groom }}, {{ $orderWedding->bride->bride }}
                                        </td>
                                        <td class="pd-2-8">2</td>
                                        <td class="pd-2-8">-</td>
                                        <td class="pd-2-8">Include</td>
                                        <td class="pd-2-8">
                                            <i class="icon-copy fa fa-check-circle text-blue" aria-hidden="true"></i> <i>Act</i>
                                        </td>
                                        <td class="pd-2-8"></td>
                                    </tr>
                                @endif
                                @foreach ($accommodationInvs as $no_acc=>$accommodation_inv)
                                    @php
                                        $cin = new DateTime($accommodation_inv->checkin);
                                        $cout = new DateTime($accommodation_inv->checkout);
                                        $interval = $cin->diff($cout);
                                        $accommodation_inv_duration = $interval->days;
                                        $extra_bed_order = $extraBedOrders->where('id',$accommodation_inv->extra_bed_id)->first();
                                        if ($extra_bed_order) {
                                            $extra_bed_price_pax = $extra_bed_order->price_pax;
                                            $extra_bed_order_id = $extra_bed_order->id;
                                            $extra_bed_id = $extra_bed_order->extra_bed_id;
                                            array_push($exbtp,$extra_bed_order->total_price);
                                        }else{
                                            $extra_bed_price_pax = NULL;
                                            $extra_bed_order_id = NULL;
                                            $extra_bed_id = NULL;
                                        }
                                    @endphp
                                    <tr class="{{ $accommodation_inv->public_rate<=0?"bg-notif":""; }} {{ $accommodation_inv_duration<=0?"bg-notif":""; }}">
                                        <td class="pd-2-8">
                                            {{ date('d M Y',strtotime($accommodation_inv->checkin)) }} - {{ date('d M Y',strtotime($accommodation_inv->checkout)) }}<br>
                                            {{ $accommodation_inv->duration." night" }}
                                        </td>
                                        <td class="pd-2-8">
                                            {{ $accommodation_inv->hotel->name }} | {{ $accommodation_inv->room->rooms }}<br>
                                            {{ $accommodation_inv->guest_detail }}
                                        </td>
                                        <td class="pd-2-8">{{ $accommodation_inv->number_of_guests }}</td>
                                        
                                        <td class="pd-2-8">
                                            @if ($accommodation_inv->extra_bed_order)
                                                {{ currencyFormatUsd($accommodation_inv->extra_bed_order->total_price) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="pd-2-8">{{ currencyFormatUsd($accommodation_inv->public_rate) }}</td>
                                        <td class="pd-2-8">
                                            @if ($accommodation_inv->status == "Requested")
                                                <i class="icon-copy fa fa-question-circle text-red" data-toggle="tooltip" title="Please check the availability of rooms and the room rates at the hotel. If rooms are available, update the status and price accordingly." aria-hidden="true"></i> <i>Req</i>
                                            @elseif($accommodation_inv->status == "Active")
                                                <i class="icon-copy fa fa-check-circle text-blue" aria-hidden="true"></i> <i>Act</i>
                                            @endif
                                        </td>
                                        <td class="text-right pd-2-8">
                                            <div class="table-action">
                                                <span>
                                                    <a href="#" data-toggle="modal" data-target="#updatePriceAccommodation-{{ $accommodation_inv->id }}">
                                                        <i class="icon-copy fa fa-dollar" data-toggle="tooltip" title="Change Status and Price" aria-hidden="true"></i>
                                                    </a>
                                                    
                                                </span>
                                                <span>
                                                    <a href="#" data-toggle="modal" data-target="#edit-wedding-order-accommodation-invitation-{{ $accommodation_inv->id }}">
                                                        <i class="fa fa-pencil" data-toggle="tooltip" title="Change Room"></i>
                                                    </a>
                                                </span>
                                                <span>
                                                    <form id="deleteAccommodationOrder{{ $accommodation_inv->id }}" action="/admin-func-delete-order-wedding-accommodation/{{ $accommodation_inv->id }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('delete')
                                                        <button form="deleteAccommodationOrder{{ $accommodation_inv->id }}" class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash"></i></button>
                                                    </form>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- MODAL UPDATE PRICE ACCOMMODATION --}}
                                    <div class="modal fade" id="updatePriceAccommodation-{{ $accommodation_inv->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <i class="icon-copy fa fa-dollar" aria-hidden="true"></i> Change Price
                                                    </div>
                                                    <form id="update_price_accommodation-{{ $accommodation_inv->id }}"
                                                        action="/admin-fupdate-price-accommodation/{{ $accommodation_inv->id }}"
                                                        method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label for="status">Status <span>*</span> </label>
                                                                    <select name="status" class="custom-select @error('status') is-invalid @enderror">
                                                                        <option {{ $accommodation_inv->status == 'Active'?"selected":""; }} value="Active">Active</option>
                                                                        <option {{ $accommodation_inv->status == 'Requested'?"selected":""; }} value="Requested">Requested</option>
                                                                    </select>
                                                                    @error('status')
                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    @if ($extra_bed_order)
                                                                        <label for="extra_bed_price">Extra Bed Price<span> * (Price / night)</span></label>
                                                                        <div class="btn-icon">
                                                                            <span><i class="icon-copy fa fa-dollar" aria-hidden="true"></i></span>
                                                                            <input name="extra_bed_price" type="number" value="{{ $extra_bed_order?$extra_bed_order->price_pax:0; }}" class="form-control input-icon @error('extra_bed_price') is-invalid @enderror" placeholder="Price" required>
                                                                        </div>
                                                                    @else
                                                                        <label for="extra_bed_price">Without Extra Bed</label>
                                                                        <div class="btn-icon">
                                                                            <span><i class="icon-copy fa fa-dollar" aria-hidden="true"></i></span>
                                                                            <input disabled name="extra_bed_price" type="number" value="" class="form-control input-icon @error('extra_bed_price') is-invalid @enderror" placeholder="Without extra bed">
                                                                        </div>
                                                                    @endif
                                                                    @error('extra_bed_price')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="suite_and_villa_price">Suite / Villa Price<span> * (Price / night)</span></label>
                                                                    <div class="btn-icon">
                                                                        <span><i class="icon-copy fa fa-dollar" aria-hidden="true"></i></span>
                                                                        <input name="suite_and_villa_price" type="number" value="{{ $accommodation_inv->public_rate / $accommodation_inv->duration }}" class="form-control input-icon @error('suite_and_villa_price') is-invalid @enderror" placeholder="Price" required>
                                                                    </div>
                                                                    @error('suite_and_villa_price')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit"
                                                            form="update_price_accommodation-{{ $accommodation_inv->id }}"
                                                            class="btn btn-primary"><i class="icon-copy fa fa-pencil"
                                                                aria-hidden="true"></i> @lang('messages.Update')</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                                                class="icon-copy fa fa-close" aria-hidden="true"></i>
                                                            @lang('messages.Cancel')</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- MODAL UPDATE WEDDING ACCOMMODATION --}}
                                    <div class="modal fade" id="edit-wedding-order-accommodation-invitation-{{ $accommodation_inv->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <i class="icon-copy fa fa-hotel"></i> @lang('messages.Edit Accommodation')
                                                    </div>
                                                    <form id="updateWeddingAccommodation-{{ $accommodation_inv->id }}"
                                                        action="/admin-fupdate-accommodation-wedding-order/{{ $accommodation_inv->id }}"
                                                        method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <div class="card-box-content">
                                                                        @foreach ($rooms as $no_inv_room => $room_inv)
                                                                            @if ($room_inv->id == $accommodation_inv->rooms_id)
                                                                                <input checked type="radio"
                                                                                    id="{{ 'inv_acc_rv' . ++$no_inv_room . $accommodation_inv->id }}"
                                                                                    name="rooms_id" value="{{ $room_inv->id }}">
                                                                                <label
                                                                                    for="{{ 'inv_acc_rv' . $no_inv_room . $accommodation_inv->id }}"
                                                                                    class="label-radio">
                                                                                    <div class="card h-100">
                                                                                        <img class="card-img"
                                                                                            src="{{ asset('storage/hotels/hotels-room/' . $room_inv->cover) }}"
                                                                                            alt="{{ $room_inv->rooms }}">
                                                                                        <div class="name-card">
                                                                                            <b>{{ $room_inv->rooms }}</b>
                                                                                        </div>
                                                                                        <div class="label-capacity">
                                                                                            {{ $room_inv->capacity . ' guests' }}
                                                                                        </div>
                                                                                    </div>
                                                                                </label>
                                                                            @else
                                                                                <input type="radio"
                                                                                    id="{{ 'inv_acc_rv' . ++$no_inv_room . $accommodation_inv->id }}"
                                                                                    name="rooms_id" value="{{ $room_inv->id }}">
                                                                                <label
                                                                                    for="{{ 'inv_acc_rv' . $no_inv_room . $accommodation_inv->id }}"
                                                                                    class="label-radio">
                                                                                    <div class="card h-100">
                                                                                        <img class="card-img"
                                                                                            src="{{ asset('storage/hotels/hotels-room/' . $room_inv->cover) }}"
                                                                                            alt="{{ $room_inv->rooms }}">
                                                                                        <div class="name-card">
                                                                                            <b>{{ $room_inv->rooms }}</b>
                                                                                        </div>
                                                                                        <div class="label-capacity">
                                                                                            {{ $room_inv->capacity . ' guests' }}
                                                                                        </div>
                                                                                    </div>
                                                                                </label>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if ($orderWedding->service != "Wedding Package")
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="checkin">@lang('messages.Check-in')</label>
                                                                        <input readonly type="text" name="checkin" class="form-control date-picker @error('checkin') is-invalid @enderror" placeholder="@lang('messages.Check-in')" value="{{ $accommodation_inv->checkin }}" required>
                                                                        @error('checkin')
                                                                            <span class="invalid-feedback">
                                                                                {{ $message }}
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="checkout">@lang('messages.Check-out')</label>
                                                                        <input readonly type="text" name="checkout" class="form-control date-picker @error('checkout') is-invalid @enderror" placeholder="@lang('messages.Check-out')" value="{{ $accommodation_inv->checkout }}" required>
                                                                        @error('checkout')
                                                                            <span class="invalid-feedback">
                                                                                {{ $message }}
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="number_of_guests">@lang('messages.Number of Guests')</label>
                                                                    <input type="number" name="number_of_guests" min="1"
                                                                        max="{{ $room_inv->capacity }}"
                                                                        class="form-control @error('number_of_guests') is-invalid @enderror"
                                                                        placeholder="@lang('messages.Number of guests')"
                                                                        value="{{ $accommodation_inv->number_of_guests }}">
                                                                    @error('number_of_guests')
                                                                        <span class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="guest_detail">@lang('messages.Guests Name')</label>
                                                                    <input type="text" name="guest_detail"
                                                                        class="form-control @error('guest_detail') is-invalid @enderror"
                                                                        placeholder="@lang('messages.Guests Name')"
                                                                        value="{{ $accommodation_inv->guest_detail }}">
                                                                    @error('guest_detail')
                                                                        <span class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="extra_bed_id">@lang('messages.Extra Bed')
                                                                        <span>*</span> </label>
                                                                    <select name="extra_bed_id"
                                                                        class="custom-select @error('extra_bed_id') is-invalid @enderror">
                                                                            <option value="">@lang('messages.None')</option>
                                                                            @foreach ($extraBeds as $extraBed)
                                                                                @if ($extra_bed_order)
                                                                                    <option {{ $extraBed->id == $extra_bed_order->extra_bed_id?"selected":""; }} value="{{ $extraBed->id }}">{{ $extraBed->type }}</option>
                                                                                @else
                                                                                    <option value="{{ $extraBed->id }}">{{ $extraBed->type }}</option>
                                                                                @endif
                                                                            @endforeach
                                                                    </select>
                                                                    @error('extra_bed_id')
                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="remark"
                                                                        class="form-label">@lang('messages.Remark')</label>
                                                                    <textarea name="remark" class="textarea_editor form-control @error('remark') is-invalid @enderror"
                                                                        placeholder="Insert remark" value="@lang('messages.Remark')">{!! old('remark') !!}</textarea>
                                                                    @error('remark')
                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit"
                                                            form="updateWeddingAccommodation-{{ $accommodation_inv->id }}"
                                                            class="btn btn-primary"><i class="icon-copy fa fa-pencil"
                                                                aria-hidden="true"></i> @lang('messages.Update')</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                                                class="icon-copy fa fa-close" aria-hidden="true"></i>
                                                            @lang('messages.Cancel')</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="box-price-kicked">
                            @php
                                $extra_bed_total_price = array_sum($exbtp);
                                $grand_total_accommodation_price = $invitation_accommodation_price + $extra_bed_total_price;
                            @endphp
                            <div class="row">
                                <div class="col-6 col-sm-8 text-right">
                                    <div class="normal-text">
                                        Extra Bed
                                    </div>
                                </div>
                                <div class="col-6 col-sm-4 text-right">
                                    <div class="normal-text">
                                        {{ currencyFormatUsd($extra_bed_total_price,0,',','.') }}
                                    </div>
                                </div>
                                <div class="col-6 col-sm-8 text-right">
                                    <div class="normal-text">
                                        Suite / Villa
                                    </div>
                                </div>
                                <div class="col-6 col-sm-4 text-right">
                                    @if ($accommodation_order_containt_zero)
                                        <div class="normal-text text-red">
                                            TBA
                                        </div>
                                    @else
                                        <div class="normal-text">
                                            {{ currencyFormatUsd($invitation_accommodation_price,0,',','.') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <hr class="form-hr">
                                </div>
                                <div class="col-6 col-sm-8 text-right">
                                    <b>
                                        Total
                                    </b>
                                </div>
                                <div class="col-6 col-sm-4 text-right">
                                    @if ($accommodation_order_containt_zero)
                                        <div class="normal-text text-red">
                                            <b>TBA</b>
                                        </div>
                                    @else
                                        <b>
                                            {{ currencyFormatUsd($grand_total_accommodation_price,0,',','.') }}
                                        </b>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-box-footer">
                            <a href="/validate-orders-wedding-{{ $orderWedding->id }}#weddingAccommodation">
                                <button type="button" class="btn btn-danger"><i class="icon-copy fa fa-arrow-left" aria-hidden="true"></i> Back to Order</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var roomFor = document.getElementById('roomForForm');
        var guestsName = document.getElementById('guestsNameForm');
        var numberOfGuests = document.getElementById('numberOfGuestsForm');
        var extraBed = document.getElementById('extraBedIdForm');
         
        
        
        if (roomFor.value == 'Couple') {
            roomFor.addEventListener('change', function() {
                if (this.value === 'Couple') {
                    guestsName.setAttribute('hidden', 'hidden');
                    numberOfGuests.setAttribute('hidden', 'hidden');
                    extraBed.setAttribute('hidden', 'hidden');
                } else {
                    guestsName.removeAttribute('hidden');
                    numberOfGuests.removeAttribute('hidden');
                    extraBed.removeAttribute('hidden');
                }
                if (typeSelect.value === 'Couple') {
                    guestNameField.setAttribute('hidden', 'hidden');
                    numberOfGuestsField.setAttribute('hidden', 'hidden');
                    extraBed.setAttribute('hidden', 'hidden');
                }
            });
        } else {
            guestsName.removeAttribute('hidden');
            numberOfGuests.removeAttribute('hidden');
            extraBed.removeAttribute('hidden');
        }
    </script>
@endsection