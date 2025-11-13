@section('title', __('messages.Orders'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title"><i class="icon-copy fa fa-tags"></i>&nbsp; Add or Edit Order Itinerary</div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="orders">Order</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:history.back()">{{ $order->orderno }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add Additional Service</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @if ($order->service == "Wedding Package")
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">
                                        Order Details
                                    </div>
                                </div>
                                <table class="table tb-list">
                                    <tr>
                                        <td class="htd-1">
                                            <b>Wedding Package</b>
                                        </td>
                                        <td class="htd-2">
                                            <b>{{ $order->servicename }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            <b>Hotel</b>
                                        </td>
                                        <td class="htd-2">
                                            <b>{{ $order->subservice }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            <b>Duration</b>
                                        </td>
                                        <td class="htd-2">
                                            <b>{{ ($order->duration + 1)." days ".$order->duration." night"  }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            <b>Wedding Date</b>
                                        </td>
                                        <td class="htd-2">
                                            <b>{{ dateTimeFormat($order_wedding->wedding_date) }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            <b>Check In</b>
                                        </td>
                                        <td class="htd-2">
                                            <b>{{ dateFormat($order->checkin) }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            <b>Check Out</b>
                                        </td>
                                        <td class="htd-2">
                                            <b>{{ dateFormat($order->checkout) }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            <b>Services</b>
                                        </td>
                                        <td class="htd-2">
                                            <b>{{ dateFormat($order->checkout) }}</b>
                                            
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">
                                        Itinerary
                                    </div>
                                </div>
                                <table class="data-table table nowrap">
                                    <thead>
                                        <th style="width: 30%;">Date</th>
                                        <th style="width: 70%;">Services</th>
                                        <th style="width: 10%;">Actions</th>
                                    </thead>
                                    @php
                                        $day_ones = $wedding_itineraries->where('day',1);
                                        $day_twos = $wedding_itineraries->where('day',2);
                                        $day_thres = $wedding_itineraries->where('day',3);
                                        $day_fours = $wedding_itineraries->where('day',4);
                                        $day_fives = $wedding_itineraries->where('day',5);
                                        $day_sixs = $wedding_itineraries->where('day',6);
                                    @endphp
                                    @if ($day_ones)
                                        <tr>
                                            <td colspan="3"><b>Day 1</b></td>
                                        </tr>
                                        @foreach ($day_ones as $day_one)
                                            <tr>
                                                <td>
                                                    {{ dateTimeFormat($day_one->date) }}
                                                </td>
                                                <td>
                                                    {!! $day_one->itinerary !!}
                                                </td>
                                                <td>
                                                    <a href="#" data-toggle="modal" data-target="#detail-itinerary-{{ $day_one->id }}">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="detail-itinerary-{{ $day_one->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fa fa-pencil"></i>Activity / Service</div>
                                                            </div>
                                                            <form id="updateItinerary{{ $day_one->id }}" action="/fupdate-order-itinerary/{{ $day_one->id }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label for="date" class="form-label">Date</label>
                                                                                <input readonly type="text" name="date" placeholder="@lang('messages.Select date')" class="form-control datetimepicker @error('date') is-invalid @enderror" value="{{ $day_one->date }}" required>
                                                                                @error('date')
                                                                                    <div class="alert alert-danger">
                                                                                        {{ $message }}
                                                                                    </div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-4 m-b-8">
                                                                            <div class="form-group">
                                                                                <label for="day">Day <span> *</span></label>
                                                                                <select name="day" class="custom-select col-12 @error('day') is-invalid @enderror" required>
                                                                                    <option value="1" selected>Day 1</option>
                                                                                    <option value="2">Day 2</option>
                                                                                    <option value="3">Day 3</option>
                                                                                    <option value="4">Day 4</option>
                                                                                    <option value="5">Day 5</option>
                                                                                    <option value="6">Day 6</option>
                                                                                    <option value="7">Day 7</option>
                                                                                    <option value="8">Day 8</option>
                                                                                </select>
                                                                                @error('day')
                                                                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label for="date">Activity /  Service</label>
                                                                                <textarea name="itinerary" placeholder="@lang('messages.Optional')" class="textarea_editor form-control border-radius-0">{{ $day_one->itinerary }}</textarea>
                                                                                @error('itinerary')
                                                                                    <div class="alert alert-danger">
                                                                                        {{ $message }}
                                                                                    </div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="updateItinerary{{ $day_one->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Save')</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if ($day_twos)
                                        <tr>
                                            <td colspan="3"><b>Day 2</b></td>
                                        </tr>
                                        @foreach ($day_twos as $day_two)
                                            <tr>
                                                <td>
                                                    {{ dateTimeFormat($day_two->date) }}
                                                </td>
                                                <td>
                                                    {!! $day_two->itinerary !!}
                                                </td>
                                                <td>
                                                    <a href="#" data-toggle="modal" data-target="#detail-itinerary-{{ $day_two->id }}">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="detail-itinerary-{{ $day_two->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fa fa-pencil"></i>Activity / Service</div>
                                                            </div>
                                                            <form id="updateItinerary{{ $day_two->id }}" action="/fupdate-order-itinerary/{{ $day_two->id }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label for="date" class="form-label">Date</label>
                                                                                <input readonly type="text" name="date" placeholder="@lang('messages.Select date')" class="form-control datetimepicker @error('date') is-invalid @enderror" value="{{ $day_two->date }}" required>
                                                                                @error('date')
                                                                                    <div class="alert alert-danger">
                                                                                        {{ $message }}
                                                                                    </div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-4 m-b-8">
                                                                            <div class="form-group">
                                                                                <label for="day">Day <span> *</span></label>
                                                                                <select name="day" class="custom-select col-12 @error('day') is-invalid @enderror" required>
                                                                                    <option value="1">Day 1</option>
                                                                                    <option value="2" selected>Day 2</option>
                                                                                    <option value="3">Day 3</option>
                                                                                    <option value="4">Day 4</option>
                                                                                    <option value="5">Day 5</option>
                                                                                    <option value="6">Day 6</option>
                                                                                    <option value="7">Day 7</option>
                                                                                    <option value="8">Day 8</option>
                                                                                </select>
                                                                                @error('day')
                                                                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label for="date">Activity /  Service</label>
                                                                                <textarea name="itinerary" placeholder="@lang('messages.Optional')" class="textarea_editor form-control border-radius-0">{{ $day_two->itinerary }}</textarea>
                                                                                @error('itinerary')
                                                                                    <div class="alert alert-danger">
                                                                                        {{ $message }}
                                                                                    </div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="updateItinerary{{ $day_two->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Save')</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if ($day_thres)
                                        <tr>
                                            <td colspan="3"><b>Day 3</b></td>
                                        </tr>
                                        @foreach ($day_thres as $day_thre)
                                            <tr>
                                                <td>
                                                    {{ dateTimeFormat($day_thre->date) }}
                                                </td>
                                                <td>
                                                    {!! $day_thre->itinerary !!}
                                                </td>
                                                <td>
                                                    <a href="#" data-toggle="modal" data-target="#detail-itinerary-{{ $day_thre->id }}">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="detail-itinerary-{{ $day_thre->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fa fa-pencil"></i>Activity / Service</div>
                                                            </div>
                                                            <form id="updateItinerary{{ $day_thre->id }}" action="/fupdate-order-itinerary/{{ $day_thre->id }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label for="date" class="form-label">Date</label>
                                                                                <input readonly type="text" name="date" placeholder="@lang('messages.Select date')" class="form-control datetimepicker @error('date') is-invalid @enderror" value="{{ $day_thre->date }}" required>
                                                                                @error('date')
                                                                                    <div class="alert alert-danger">
                                                                                        {{ $message }}
                                                                                    </div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-4 m-b-8">
                                                                            <div class="form-group">
                                                                                <label for="day">Day <span> *</span></label>
                                                                                <select name="day" class="custom-select col-12 @error('day') is-invalid @enderror" required>
                                                                                    <option value="1">Day 1</option>
                                                                                    <option value="2">Day 2</option>
                                                                                    <option value="3" selected>Day 3</option>
                                                                                    <option value="4">Day 4</option>
                                                                                    <option value="5">Day 5</option>
                                                                                    <option value="6">Day 6</option>
                                                                                    <option value="7">Day 7</option>
                                                                                    <option value="8">Day 8</option>
                                                                                </select>
                                                                                @error('day')
                                                                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label for="date">Activity /  Service</label>
                                                                                <textarea name="itinerary" placeholder="@lang('messages.Optional')" class="textarea_editor form-control border-radius-0">{{ $day_thre->itinerary }}</textarea>
                                                                                @error('itinerary')
                                                                                    <div class="alert alert-danger">
                                                                                        {{ $message }}
                                                                                    </div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="updateItinerary{{ $day_thre->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Save')</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                </table>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-8">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title">
                                    Add Order Itinerary
                                </div>
                            </div>
                            <form id="edit-additional-service" action="/fedit-additional-services-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-md-12">
                                        @if (isset($additional_service))
                                            @php
                                                $cas = count($additional_service);
                                            @endphp
                                            @for ($i = 0; $i < $cas; $i++)
                                                <div class="control-group">
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="additional_service_date[{{ $i }}]">Date</label>
                                                                <input type="date" name="additional_service_date[{{ $i }}]" class="form-control @error('additional_service_date[{{ $i }}]') is-invalid @enderror" placeholder="Service" value="{{ $additional_service_date[$i] }}" required>
                                                                @error('additional_service_date[{{ $i }}]')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="additional_service[{{ $i }}]">Additional Service</label>
                                                                <input type="text" name="additional_service[{{ $i }}]" class="form-control @error('additional_service[{{ $i }}]') is-invalid @enderror" placeholder="Service" value="{{ $additional_service[$i] }}" required>
                                                                @error('additional_service[{{ $i }}]')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="additional_service_qty[{{ $i }}]">Quantity</label>
                                                                <input type="number" name="additional_service_qty[{{ $i }}]" min="1" class="form-control m-0 @error('additional_service_qty[{{ $i }}]') is-invalid @enderror" placeholder="Service" value="{{ $additional_service_qty[$i] }}" required>
                                                                @error('additional_service_qty[{{ $i }}]')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for="additional_service_price[{{ $i }}]">Price /pax</label>
                                                                <input type="number" name="additional_service_price[{{ $i }}]" min="1" class="form-control @error('additional_service_price[{{ $i }}]') is-invalid @enderror" placeholder="Price in USD" value="{{ $additional_service_price[$i] }}" required>
                                                                @error('additional_service_price[{{ $i }}]')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-1" style="align-self: center; padding-bottom:17px;">
                                                            <button class="btn btn-remove remove"  type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i> </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        @else
                                            <div class="control-group">
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="additional_service_date[]">Date</label>
                                                            <input type="date" name="additional_service_date[]" class="form-control @error('additional_service_date[]') is-invalid @enderror" placeholder="Service" value="" required>
                                                            @error('additional_service_date[]')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label for="additional_service[]">Additional Service</label>
                                                            <input type="text" name="additional_service[]" class="form-control @error('additional_service[]') is-invalid @enderror" placeholder="Service" value="" required>
                                                            @error('additional_service[]')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="additional_service_qty[]">Quantity</label>
                                                            <input type="number" name="additional_service_qty[]" min="1" class="form-control m-0 @error('additional_service_qty[]') is-invalid @enderror" placeholder="Service" value="" required>
                                                            @error('additional_service_qty[]')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="additional_service_price[]">Price</label>
                                                            <input type="number" name="additional_service_price[]" min="1" class="form-control @error('additional_service_price[]') is-invalid @enderror" placeholder="Price in USD" value="" required>
                                                            @error('additional_service_price[]')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="after-add-more"></div>
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                </div>
                            </form>
                            {{-- ADD MORE SERVICE --}}
                            <div class="copy hide">
                                <div class="col-md-12">
                                    <div class="control-group">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="additional_service_date[]">Date</label>
                                                    <input type="date" name="additional_service_date[]" class="form-control @error('additional_service_date[]') is-invalid @enderror" placeholder="Service" value="" required>
                                                    @error('additional_service_date[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="additional_service[]">Additional Service</label>
                                                    <input type="text" name="additional_service[]" class="form-control @error('additional_service[]') is-invalid @enderror" placeholder="Service" value="" required>
                                                    @error('additional_service[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="additional_service_qty[]">Quantity</label>
                                                    <input type="number" name="additional_service_qty[]" min="1" class="form-control m-0 @error('additional_service_qty[]') is-invalid @enderror" placeholder="Service" value="" required>
                                                    @error('additional_service_qty[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="additional_service_price[]">Price/pax</label>
                                                    <input type="number" name="additional_service_price[]" min="1" class="form-control @error('additional_service_price[]') is-invalid @enderror" placeholder="Price in USD" value="" required>
                                                    @error('additional_service_price[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-1" style="align-self: center; padding-bottom:17px;">
                                                <button class="btn btn-remove remove"  type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    var ro = 1;
                                    $(".add-more").click(function(){ 
                                        ro++;
                                        var html = $(".copy").html();
                                        $(".after-add-more").before(html);
                                    });
                                    $("body").on("click",".remove",function(){ 
                                        $(this).parents(".control-group").remove();
                                    });
                                });
                            </script>
                            <div class="card-box-footer">
                                <button type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Service</button>
                                <button type="submit" form="edit-additional-service" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                <a href="/orders-admin-{{ $order->id }}#optional_service">
                                    <button type="button" class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>
@endsection