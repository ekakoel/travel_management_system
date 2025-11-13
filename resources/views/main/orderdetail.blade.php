@section('title', __('messages.Orders'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title"><i class="icon-copy dw dw-shopping-cart1"></i>&nbsp; @lang('messages.Orders')</div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/dashboard">@lang('messages.Dashboard')</a></li>
                                    <li class="breadcrumb-item"><a href="/orders">Orders</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $order->orderno }} - Additional Charges</li>
                                </ol>
                            </nav>
                    </div>
                    </div>
                </div>
                <div class="row">
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
                    @if (Auth::user()->email == "")
                        <div class="col-xl-12 mb-30">
                            <div class="card-box">
                                <div class="notification-box">
                                    <i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    Please complete your profile data first to be able to submit orders by clicking this link -> <a href="/profile">Edit Profile</a></i>
                                </div>
                            </div>
                        </div>
                    @elseif (Auth::user()->phone == "")
                        <div class="col-xl-12 mb-30">
                            <div class="card-box">
                                <div class="notification-box">
                                    <i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    Please complete your profile data first to be able to submit orders by clicking this link -> <a href="/profile">Edit Profile</a></i>
                                </div>
                            </div>
                        </div>
                    @elseif (Auth::user()->office == "")
                        <div class="col-xl-12 mb-30">
                            <div class="card-box">
                                <div class="notification-box">
                                    <i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    Please complete your profile data first to be able to submit orders by clicking this link -> <a href="/profile">Edit Profile</a></i>
                                </div>
                            </div>
                        </div>
                    @elseif (Auth::user()->address == "")
                        <div class="col-xl-12 mb-30">
                            <div class="card-box">
                                <div class="notification-box">
                                    <i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    Please complete your profile data first to be able to submit orders by clicking this link -> <a href="/profile">Edit Profile</a></i>
                                </div>
                            </div>
                        </div>
                    @elseif (Auth::user()->country == "")
                        <div class="col-xl-12 mb-30">
                            <div class="card-box">
                                <div class="notification-box">
                                    <i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    Please complete your profile data first to be able to submit orders by clicking this link -> <a href="/profile">Edit Profile</a></i>
                                </div>
                            </div>
                        </div>
                    @else
                    @endif
                </div>
                <div class="product-wrap">
                    <div class="product-detail-wrap">
                        <div class="row">
                            <div class="col-md-8 m-b-18">
                                <div class="card-box">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row m-b-8">
                                                <div class="col-md-6">
                                                    <div class="subtitle">Additional Charges</div> 
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <a href="#" data-toggle="modal" data-target="#add-optional-rate-{{ $order->id }}">
                                                        <button type="submit" class="btn btn-primary btn-lg" role="button"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <table id="tbHotels" class="data-table table nowrap" >
                                                <thead>
                                                    <tr>
                                                        
                                                        <th style="width: 10%;">Date</th>
                                                        <th style="width: 10%;">Services</th>
                                                        <th style="width: 10%;">Guests</th>
                                                        <th style="width: 10%;">Price</th>
                                                        <th style="width: 5%;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $optional_rate = $optional_rate_order->where('order_id','=',$order->id);
                                                        $price_unit = $optional_rate_order->where('order_id','=',$order->id)->sum('price_unit');
                                                        $total_price = $price_unit + $order->price_total; 
                                                    @endphp
                                                    @foreach ($optional_rate as $optionalrate)
                                                        @php
                                                            $optional_services = $optionalrates->where('service_id','=',$order->service_id);
                                                            $optional_services_id = $optional_services->where('name','=',$optionalrate->name)->first();
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                {{ date('D, d M y', strtotime($optionalrate->service_date)) }}
                                                            </td>
                                                            <td>
                                                                {{ $optionalrate->name }}
                                                            </td>
                                                            <td>
                                                                {{ $optionalrate->qty." Guests" }}
                                                            </td>
                                                            <td>
                                                                {{ "$ ".$optionalrate->price_unit }}
                                                            </td>
                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-2">
                                                                        <a href="#" data-toggle="modal" data-target="#edit-opser-{{ $optionalrate->id }}">
                                                                            <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                                        </a>
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <form action="/delete-opser/{{ $optionalrate->id }}" method="post">
                                                                            @csrf
                                                                            @method('delete')
                                                                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        {{-- MODAL OPTIONAL RATE EDIT ===========================================================================================================================================================--}}
                                                        <div class="modal fade" id="edit-opser-{{ $optionalrate->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="row">
                                                                        <form action="/fupdate-optional-rate-order/{{ $optionalrate->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('put')
                                                                            {{ csrf_field() }}
                                                                            <div class="col-md-12">
                                                                                <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Additional Charge</div>
                                                                            </div> 
                                                                            <div class="col-12">
                                                                                <div class="page-subtitle">Detail Order</div>
                                                                                <div class="row">
                                                                                    <div class="col-6">
                                                                                        <p><b>Order no: </b> {{ $order->orderno }}</p>
                                                                                        <p><b>Service: </b> {{ $order->service }}</p>
                                                                                        <p><b>Suites & Villas: </b> {{ $order->subservice }}</p>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <p><b>Guests Name : </b> {{ $order->guest_detail }}</p>
                                                                                        <p><b>Check-in: </b> {{ date("m/d/Y", strtotime($order->checkin))}}</p>
                                                                                        <p><b>Check-out: </b> {{ date("m/d/Y", strtotime($order->checkout))}}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="page-subtitle">Additional Charges</div>
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group row">
                                                                                            <label for="optional_rate_id" class="col-12 col-sm-12 col-md-12 col-form-label">Additional Charges</label>
                                                                                            <div class="col-12 col-sm-12 col-md-12">
                                                                                                <select name="optional_rate_id" class="custom-select col-12 @error('optional_rate_id') is-invalid @enderror" required>
                                                                                                    <option selected value="{{ $optional_services_id->id }}">{{ $optionalrate->name }}</option>
                                                                                                    @foreach ($optional_services as $optional_service)
                                                                                                        <option value="{{ $optional_service->id }}">{{ $optional_service->name }}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                                @error('optional_rate_id')
                                                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group row">
                                                                                            <label for="service_date" class="col-sm-12 col-md-12 col-form-label">Date</label>
                                                                                            <div class="col-sm-12 col-md-12">
                                                                                                <input name="service_date" wire:model="date" class="form-control date-picker @error('service_date') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ $optionalrate->service_date }}" required>
                                                                                            @error('service_date')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group row">
                                                                                            <label for="qty" class="col-sm-12 col-md-12 col-form-label">Number Of Guests</label>
                                                                                            <div class="col-sm-12 col-md-12">
                                                                                                <input name="qty" type="number" class="form-control @error('qty') is-invalid @enderror" placeholder="Number of guests" value="{{ $optionalrate->qty }}" required>
                                                                                            @error('qty')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <div class="form-group row">
                                                                                            <label for="note" class="col-sm-12 col-md-12 col-form-label">Note</label>
                                                                                            <div class="col-sm-12 col-md-12">
                                                                                                <textarea name="note" wire:model="note" class="textarea_editor form-control @error('note') is-invalid @enderror" placeholder="Optional" type="text">{{ $optionalrate->note }}</textarea>
                                                                                            @error('note')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12 text-right m-t-18">
                                                                                @php
                                                                                    $price_unit = (ceil($optional_services_id->contract_rate / $usdrates->rate))+$optionalrate->markup;
                                                                                @endphp
                                                                                <input name="order_id" value="{{ $order->id }}" type="hidden">
                                                                                <input name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                                <input name="price_unit" value="{{ $price_unit }}" type="hidden">
                                                                                <button type="submit" class="btn btn-primary"><i class="icon-copy ion-checkmark"></i> Change</button>
                                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- END MODAL OPTIONAL RATE EDIT ====================================================================================================================================================== --}}
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            {{-- PRICE =============================================================================================================== --}}
                                            <div class="row">
                                                <div class="col-md-12 ">
                                                    <div class="box-price text-right">
                                                        
                                                        <div class="price-name">
                                                            Total Price :
                                                        </div>
                                                        @php
                                                            $totalprice = $optional_rate_order->where('order_id','=', $order->id)->sum('price_unit');
                                                        @endphp
                                                        <div class="price-tag">
                                                            {{ '$ ' . number_format($totalprice, 0, ',', '.') }}<br>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="/orders" class="btn btn-default">
                                                <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="icon-copy ion-checkmark"></i> Done</button>
                                                </a>
                                            </div>
                                            {{-- MODAL OPTIONAL RATE ADD ===========================================================================================================================================================--}}
                                            @php
                                                $optional_services = $optionalrates->where('service_id','=',$order->service_id);
                                                $optional_rate = $optional_rate_order->where('order_id','=',$order->id);
                                                $price_unit = $optional_rate_order->where('order_id','=',$order->id)->sum('price_unit');
                                                $total_price = $price_unit + $order->price_total; 
                                            @endphp
                                            <div class="modal fade" id="add-optional-rate-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="row">
                                                            <form action="/fadd-optional-rate" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                {{ csrf_field() }}
                                                                <div class="col-md-12">
                                                                    <div class="title"><i class="icon-copy fa fa-tag" aria-hidden="true"></i> Add Additional Charge</div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="page-subtitle">Detail Order</div>
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <p><b>Order no : </b> {{ $order->orderno }}</p>
                                                                            <p><b>Service : </b> {{ $order->servicename }}</p>
                                                                            <p><b>Suites & Villas : </b> {{ $order->subservice }}</p>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <p><b>Guests Name : </b> {{ $order->guest_detail }}</p>
                                                                            <p><b>Check-in : </b> {{ date("m/d/Y", strtotime($order->checkin))}}</p>
                                                                            <p><b>Check-out : </b> {{ date("m/d/Y", strtotime($order->checkout))}}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="page-subtitle">Additional Charges</div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group row">
                                                                                <label for="optional_rate_id" class="col-12 col-sm-12 col-md-12 col-form-label">Additional Charge</label>
                                                                                <div class="col-12 col-sm-12 col-md-12">
                                                                                    <select id="optional_rate_id" name="optional_rate_id" class="custom-select col-12 @error('optional_rate_id') is-invalid @enderror" required>
                                                                                        <option selected value="">Select Additional Charge</option>
                                                                                        @foreach ($optional_services as $optional_service)
                                                                                            <option value="{{ $optional_service->id }}">{{ $optional_service->name }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    @error('rooms_id')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group row">
                                                                                <label for="service_date" class="col-sm-12 col-md-12 col-form-label">Date</label>
                                                                                <div class="col-sm-12 col-md-12">
                                                                                    <input name="service_date" id="service_date" wire:model="date" class="form-control date-picker @error('service_date') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ old('service_date') }}" required>
                                                                                @error('service_date')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group row">
                                                                                <label for="qty" class="col-sm-12 col-md-12 col-form-label">Number Of Guests</label>
                                                                                <div class="col-sm-12 col-md-12">
                                                                                    <input name="qty" type="number" class="form-control @error('qty') is-invalid @enderror" placeholder="Number of guests" value="{{ old('qty') }}" required>
                                                                                @error('qty')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group row">
                                                                                <label for="note" class="col-sm-12 col-md-12 col-form-label">Note</label>
                                                                                <div class="col-sm-12 col-md-12">
                                                                                    <textarea name="note" wire:model="note" class="textarea_editor form-control @error('note') is-invalid @enderror" placeholder="Optional" type="text"></textarea>
                                                                                @error('note')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 text-right m-t-18">
                                                                    <input name="order_id" value="{{ $order->id }}" type="hidden">
                                                                    <input name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                    <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- END MODAL OPTIONAL RATE ADD ====================================================================================================================================================== --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (count($attentions) > 0 )
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
                                    <div class="card-box mb-30">
                                        <div class="banner-right">
                                            <div class="title">Attention</div>
                                            <ul class="attention">
                                                @foreach ($attentions as $attention)
                                                    <li><p><b>"{{ $attention->name }}"</b> {{ $attention->attention }}</p></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
