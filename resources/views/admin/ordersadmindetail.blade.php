{{-- @include('layouts.loader') --}}

@php
    use Carbon\Carbon;
    $r = 1;
@endphp
@section('title', __('messages.Order Detail'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="title"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Validation Order</div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item"><a href="orders-admin">Orders Admin</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $order->orderno }}</li>
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
                        <div class="col-md-4 mobile">
                            <div class="row">
                                {{-- ATTENTION --}}
                                @include('partials.admin-order-attention',['device' => "mobile"])
                                {{-- STATUS --}}
                                @include('partials.admin-order-status-sidebar',['device' => "mobile"])
                                {{-- ORDER NOTE --}}
                                @include('partials.admin-order-note-sidebar',['device' => "mobile"])
                                {{-- DOKU --}}
                                @include('partials.admin-order-doku-report-sidebar',['device' => "mobile"])
                                {{-- RECEIPT --}}
                                @include('partials.admin-order-receipt-report-sidebar',['device' => "mobile"])
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">
                                        Order {{ $order->orderno }}
                                    </div>
                                </div>
                                <div class="product-detail-wrap">
                                    <div class="row">
                                        <div class="col-6 col-md-6">
                                            <div class="order-bil text-left">
                                                <img src="/storage/logo/logo-color-bali-kami.png"alt="Bali Kami Tour & Travel">
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6 text-right flex-end">
                                            <div class="label-title">ORDER</div>
                                        </div>
                                        <div class="col-md-12 text-right">
                                            <div class="label-date" style="width: 100%;">
                                                {{ date('l, d F Y', strtotime($order->created_at)) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 m-t-8">
                                            <div class="business-name">{{ $business->name }}</div>
                                            <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="tab-inner-title {{ $order->confirmation_order?"":"empty-value" }}">
                                                Confirmation Number
                                            </div>
                                            @if (!$order->confirmation_order)
                                                <form id="updateConfirmationNumber" action="fupdate-confirmation-number-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-container-confirmation">
                                                        <div class="form-group">
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy fi-key"></i></span>
                                                                <input name="confirmation_order" type="text" value="{{ $order->confirmation_order }}" class="form-control input-icon @error('confirmation_order') is-invalid @enderror" placeholder="Confirmation Numbber" required>
                                                            </div>
                                                            @error('confirmation_order')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                        <div class="form-btn">
                                                            @if ($order->confirmation_order)
                                                                <button type="submit" form="updateConfirmationNumber" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang("messages.Update")</button>
                                                            @else
                                                                <button type="submit" form="updateConfirmationNumber" class="btn btn-primary"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang("messages.Add")</button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </form>
                                            @else
                                                @if ($order->status != "Paid" && $order->status != "Approved")
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="card-ptext-margin">
                                                                <div class="row">
                                                                    @if ($order->handled_by != Auth::user()->id)
                                                                        <div class="col-md-12 text-center">
                                                                            <div class="title">{{ $order->confirmation_order }}</div><br>
                                                                            <hr class="form-hr">
                                                                        </div>
                                                                    @endif
                                                                    <div class="col-md-6">
                                                                        <div class="card-ptext-content">
                                                                            <div class="ptext-title">Handled by</div>
                                                                            <div class="ptext-value">{{ $handled_by->name }}</div>
                                                                            <div class="ptext-title">Date</div>
                                                                            <div class="ptext-value"><i>{{ dateTimeFormat($order->handled_date) }}</i></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($order->handled_by == Auth::user()->id)
                                                        <form id="updateConfirmationNumber" action="fupdate-confirmation-number-{{ $order->id }}" method="POST">
                                                            @csrf
                                                            @method("PUT")
                                                            <div class="form-container-confirmation">
                                                                <div class="form-group">
                                                                    <div class="btn-icon">
                                                                        <span><i class="icon-copy fi-key"></i></span>
                                                                        <input name="confirmation_order" type="text" value="{{ $order->confirmation_order }}" class="form-control input-icon @error('confirmation_order') is-invalid @enderror" placeholder="Confirmation Numbber" required>
                                                                    </div>
                                                                    @error('confirmation_order')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                                @if ($order->handled_by == Auth::user()->id)
                                                                    <div class="form-btn">
                                                                        @if ($order->confirmation_order)
                                                                            <button type="submit" form="updateConfirmationNumber" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang("messages.Update")</button>
                                                                        @else
                                                                            <button type="submit" form="updateConfirmationNumber" class="btn btn-primary"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang("messages.Add")</button>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </form>
                                                    @endif
                                                @else
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="card-ptext-margin">
                                                                <div class="row">
                                                                    @if ($order->handled_by != Auth::user()->id)
                                                                        <div class="col-md-12 text-center">
                                                                            <div class="title">{{ $order->confirmation_order }}</div><br>
                                                                            <hr class="form-hr">
                                                                        </div>
                                                                    @endif
                                                                    <div class="col-md-6">
                                                                        <div class="card-ptext-content">
                                                                            <div class="ptext-title"><b>Confirmation Number</b></div>
                                                                            <div class="ptext-value"><b>{{ $order->confirmation_order }}</b></div>
                                                                            <div class="ptext-title">Handled by</div>
                                                                            <div class="ptext-value">{{ $handled_by->name }}</div>
                                                                            <div class="ptext-title">Date</div>
                                                                            <div class="ptext-value"><i>{{ dateTimeFormat($order->handled_date) }}</i></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                        {{-- RESERVATION  --}}
                                        <div class="col-md-6">
                                            <div class="tab-inner-title">Reservation
                                                @if ($order->service != "Transport")
                                                    @if ($order->handled_by)
                                                        @if ($order->handled_by == Auth::user()->id)
                                                            @if ($order->status != "Paid" && $order->status != "Approved")
                                                                <span>
                                                                    <a href="#" data-toggle="modal" data-target="#update-reservation-{{ $reservation->id }}"> 
                                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Reservation" aria-hidden="true"></i>
                                                                    </a>
                                                                </span>
                                                            @endif
                                                        @endif
                                                    @else
                                                        @if ($order->status != "Paid" && $order->status != "Approved")
                                                            <span>
                                                                <a href="#" data-toggle="modal" data-target="#update-reservation-{{ $reservation->id }}"> 
                                                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Reservation" aria-hidden="true"></i>
                                                                </a>
                                                            </span>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            {{-- MODAL UPDATE RESERVATION  --}}
                                            @if ($order->service != "Transport")
                                                @if ($order->status != "Paid" && $order->status != "Approved")
                                                    <div class="modal fade" id="update-reservation-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Reservation</div>
                                                                    </div>
                                                                    <form id="updateReservation" action="/fupdate-reservation-pickup-name/{{ $reservation->id }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('put')
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-6">
                                                                                        <label>In - Out</label>
                                                                                        <input readonly id="checkincout" name="checkincout" class="form-control @error('checkincout') is-invalid @enderror" type="text" placeholder="@lang('messages.Select date')" value="{{ date("m/d/y",strtotime($reservation->checkin))." - ". date("m/d/y",strtotime($reservation->checkout)) }}" required>
                                                                                        @error('checkincout')
                                                                                            <span class="invalid-feedback">
                                                                                                {{ $message }}
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="pickup_name">Pick up Name</label>
                                                                                        <select name="pickup_name" class="custom-select @error('pickup_name') is-invalid @enderror">
                                                                                            @if ($reservation->pickup_name)
                                                                                                @php
                                                                                                    $gst = $guests->where('id', $reservation->pickup_name)->first();
                                                                                                @endphp
                                                                                                @if (isset($gst))
                                                                                                    <option selected value="{{ $gst->id }}">{{ $gst->name }}</option>
                                                                                                @else
                                                                                                    <option selected value="">Select Guest</option>
                                                                                                @endif
                                                                                            @else
                                                                                                <option selected value="">Select Guest</option>
                                                                                            @endif
                                                                                            @foreach ($guests as $pname)
                                                                                                <option value="{{ $pname->id }}">{{ $pname->name }}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    @error('pickup_name')
                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                    <div class="card-box-footer">
                                                                        <button type="submit" form="updateReservation" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                            <div class="card-ptext-margin">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">Number</div>
                                                    <div class="ptext-value">{{  $reservation->rsv_no }}</div>
                                                    <div class="ptext-title">Date</div>
                                                    <div class="ptext-value">{{ dateTimeFormat($reservation->created_at) }}</div>
                                                    <div class="ptext-title">In & Out</div>
                                                    <div class="ptext-value">{{ dateFormat($reservation->checkin)." - ".dateFormat($reservation->checkout) }}</div>
                                                    <div class="ptext-title">Pick up Name</div>
                                                    <div class="ptext-value">
                                                        @if ($order->service == "Transport")
                                                            {{ $order->pickup_name }}
                                                        @else
                                                            @if ($reservation->pickup_name)
                                                                @php
                                                                    $gst_pname = $guests->where('id', $reservation->pickup_name)->first();
                                                                @endphp
                                                                @if (isset($gst_pname))
                                                                    @if ($gst_pname->sex == "m")
                                                                        {{ "Mr. ". $gst_pname->name }}
                                                                    @else
                                                                        {{ "Ms. ". $gst_pname->name }}
                                                                    @endif
                                                                @else
                                                                    ..........................
                                                                @endif
                                                            @else
                                                                ..........................
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="ptext-title">Phone</div>
                                                    <div class="ptext-value">
                                                        @if ($order->service == "Transport")
                                                            {{ $order->pickup_phone }}
                                                        @else
                                                            @if ($guest_pick_up)
                                                                @if (isset($guest_pick_up->phone))
                                                                    {{  $guest_pick_up->phone }}
                                                                @else
                                                                    ..........................
                                                                @endif
                                                            @else
                                                                ..........................
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($order->service == "Activity" or $order->service == "Tour Package" or $order->service == "Transport")
                                            {{-- PICK UP AND DROP OFF --}}
                                            <div class="col-md-6">
                                                <div class="tab-inner-title ">Pick up and Drop off
                                                    @if ($order->handled_by)
                                                        @if ($order->handled_by == Auth::user()->id)
                                                            @if ($order->status != "Paid" && $order->status != "Approved")
                                                                @if ($reservation->status != "Active")
                                                                    <span>
                                                                        <a href="#" data-toggle="modal" data-target="#updatePickupDropoff-{{ $order->id }}"> 
                                                                            <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Pick up and Drop off" aria-hidden="true"></i>
                                                                        </a>
                                                                    </span>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @else
                                                        @if ($order->status != "Paid" && $order->status != "Approved")
                                                            @if ($reservation->status != "Active")
                                                                <span>
                                                                    <a href="#" data-toggle="modal" data-target="#updatePickupDropoff-{{ $order->id }}"> 
                                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Pick up and Drop off" aria-hidden="true"></i>
                                                                    </a>
                                                                </span>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                                {{-- Modal Update PICK UP AND DROP OFF --}}
                                                @if ($order->status != "Paid" && $order->status != "Approved")
                                                    <div class="modal fade" id="updatePickupDropoff-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Pick up and Drop off</div>
                                                                        </div>
                                                                        <form id="update-pickup-dropoff-{{ $order->id }}" action="/fupdate-pickup-dropoff-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('put')
                                                                            <div class="row">
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="pickup_date">Pick up Date</label>
                                                                                        <input readonly type="text" name="pickup_date" class="form-control datetimepicker @error('pickup_date') is-invalid @enderror" placeholder="Select pick up date" value="{{ dateTimeFormat($order->pickup_date) }}">
                                                                                       
                                                                                        @error('pickup_date')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="pickup_location">Pick up Location</label>
                                                                                        <input type="text" name="pickup_location" class="form-control @error('pickup_location') is-invalid @enderror" placeholder="Insert location" value="{{ $order->pickup_location }}">
                                                                                        @error('pickup_location')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="dropoff_date">Drop off Date</label>
                                                                                        <input readonly type="text" name="dropoff_date" class="form-control datetimepicker @error('dropoff_date') is-invalid @enderror" placeholder="Select pick up date" value="{{ dateTimeFormat($order->dropoff_date) }}">
                                                                                       
                                                                                        @error('dropoff_date')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="dropoff_location">Drop off Location</label>
                                                                                        <input type="text" name="dropoff_location" class="form-control @error('dropoff_location') is-invalid @enderror" placeholder="Insert location" value="{{ $order->dropoff_location }}">
                                                                                        @error('dropoff_location')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                        <div class="card-box-footer">
                                                                            <button type="submit" form="update-pickup-dropoff-{{ $order->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                        </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="card-ptext-margin">
                                                    <div class="card-ptext-content">
                                                        <div class="ptext-title">Pick up Date</div>
                                                        @if ($order->pickup_date)
                                                            <div class="ptext-value">{{ dateFormat($order->pickup_date) }}</div>
                                                        @else
                                                            <div class="ptext-value">..........................</div>
                                                        @endif
                                                        <div class="ptext-title">Pick up Location</div>
                                                        @if ($order->pickup_location)
                                                            <div class="ptext-value">{{ $order->pickup_location }}</div>
                                                        @else
                                                            <div class="ptext-value">..........................</div>
                                                        @endif
                                                        <div class="ptext-title">Drop off Date</div>
                                                        @if ($order->dropoff_date)
                                                            <div class="ptext-value">{{ dateFormat($order->dropoff_date) }}</div>
                                                        @else
                                                            <div class="ptext-value">..........................</div>
                                                        @endif
                                                        <div class="ptext-title">Drop off Location</div>
                                                        @if ($order->dropoff_location)
                                                            <div class="ptext-value">{{ $order->dropoff_location }}</div>
                                                        @else
                                                            <div class="ptext-value">..........................</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            {{-- FLIGHT --}}
                                            <div class="col-md-6">
                                                <div class="tab-inner-title ">Flight
                                                    @if ($order->handled_by)
                                                        @if ($order->handled_by == Auth::user()->id)
                                                            @if ($order->status != "Paid" && $order->status != "Approved")
                                                                <span>
                                                                    @if ($order->arrival_flight or $order->arrival_time or $order->departure_flight or $order->departure_time)
                                                                        <a href="#" data-toggle="modal" data-target="#update-flight-{{ $order->id }}"> 
                                                                            <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Flight" aria-hidden="true"></i>
                                                                        </a>
                                                                    @else
                                                                        <a href="#" data-toggle="modal" data-target="#update-flight-{{ $order->id }}"> 
                                                                            <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add Flight" aria-hidden="true"></i>
                                                                        </a>
                                                                    @endif
                                                                    
                                                                </span>
                                                            @endif
                                                        @endif
                                                    @else
                                                        @if ($order->status != "Paid" && $order->status != "Approved")
                                                            <span>
                                                                @if ($order->arrival_flight or $order->arrival_time or $order->departure_flight or $order->departure_time)
                                                                    <a href="#" data-toggle="modal" data-target="#update-flight-{{ $order->id }}"> 
                                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Flight" aria-hidden="true"></i>
                                                                    </a>
                                                                @else
                                                                    <a href="#" data-toggle="modal" data-target="#update-flight-{{ $order->id }}"> 
                                                                        <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add Flight" aria-hidden="true"></i>
                                                                    </a>
                                                                @endif
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                                {{-- Modal Update Flight --------------------------------------------------------------------------------------------------------------- --}}
                                                @if ($order->status != "Paid" && $order->status != "Approved")
                                                    <div class="modal fade" id="update-flight-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            @if ($order->arrival_flight or $order->arrival_time or $order->departure_flight or $order->departure_time)
                                                                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Flight</div>
                                                                            @else
                                                                                <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Flight</div>
                                                                            @endif
                                                                        </div>
                                                                        <form id="updateFlight-{{ $order->id }}" action="/fupdate-flight-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('put')
                                                                            <div class="row">
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="arrival_flight">Arrival Flight</label>
                                                                                        <input type="text" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror" placeholder="Insert arrival flight" value="{{ $order->arrival_flight }}">
                                                                                        @error('arrival_flight')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="arrival_time">Arrival Time </label>
                                                                                        <input readonly type="text" name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror" placeholder="Select arrival date and time" value="{{ dateTimeFormat($order->arrival_time) }}">
                                                                                        @error('arrival_time')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="departure_flight">Departure Flight</label>
                                                                                        <input type="text" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror" placeholder="Insert arrival flight" value="{{ $order->departure_flight }}">
                                                                                        @error('departure_flight')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="departure_time">Departure Time </label>
                                                                                        <input readonly type="text" name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror" placeholder="Select departure date and time" value="{{ dateTimeFormat($order->departure_time) }}">
                                                                                        @error('departure_time')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                        <div class="card-box-footer">
                                                                            @if ($order->arrival_flight or $order->arrival_time or $order->departure_flight or $order->departure_time)
                                                                                <button form="updateFlight-{{ $order->id }}" type="submit" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                            @else
                                                                                <button form="updateFlight-{{ $order->id }}" type="submit" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                            @endif
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                        </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="card-ptext-margin">
                                                    <div class="card-ptext-content">
                                                        <div class="ptext-title">Arrival Flight</div>
                                                        @if ($reservation->arrival_flight)
                                                            <div class="ptext-value">{{ $reservation->arrival_flight }}</div>
                                                        @else
                                                            <div class="ptext-value">-</div>
                                                        @endif
                                                        <div class="ptext-title">Arrival Time</div>
                                                        @if ($reservation->arrival_time)
                                                            <div class="ptext-value">{{ dateTimeFormat($reservation->arrival_time) }}</div>
                                                        @else
                                                            <div class="ptext-value">-</div>
                                                        @endif
                                                        <div class="ptext-title">Departure Flight</div>
                                                        @if ($reservation->departure_flight)
                                                            <div class="ptext-value">{{ $reservation->departure_flight }}</div>
                                                        @else
                                                            <div class="ptext-value">-</div>
                                                        @endif
                                                        <div class="ptext-title">Departure Time</div>
                                                        @if ($reservation->departure_time)
                                                            <div class="ptext-value">{{ dateTimeFormat($reservation->departure_time) }}</div>
                                                        @else
                                                            <div class="ptext-value">-</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        {{-- AGENT --}}
                                        <div class="col-md-6">
                                            <div class="tab-inner-title ">Agent</div>
                                            <div class="card-ptext-margin">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">Name</div>
                                                    <div class="ptext-value">
                                                        @if ($agent->name == "")
                                                            <p class="form-notif">Not available!</p>
                                                        @else
                                                            {{ $agent->name }}
                                                        @endif
                                                    </div>
                                                    <div class="ptext-title">Office</div>
                                                    <div class="ptext-value">
                                                        @if ($agent->office == "")
                                                            <p class="form-notif">Not available!</p>
                                                        @else
                                                            {{ $agent->office }}
                                                        @endif
                                                    </div>
                                                    <div class="ptext-title">Phone</div>
                                                    <div class="ptext-value">
                                                        @if ($agent->phone == "")
                                                            <p class="form-notif">Not available!</p>
                                                        @else
                                                            {{ $agent->phone }}
                                                        @endif
                                                    </div>
                                                    <div class="ptext-title">Email</div>
                                                    <div class="ptext-value">
                                                        @if ($agent->email == "")
                                                            <p class="form-notif">Not available!</p>
                                                        @else
                                                            {{ $agent->email }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- GUEST--}}
                                        <div class="col-md-6">
                                            <div class="tab-inner-title ">Guests
                                                @if ($order->handled_by)
                                                    @if ($order->handled_by == Auth::user()->id)
                                                        @if ($order->status != "Paid" && $order->status != "Approved")
                                                            <span>
                                                                <a href="#" data-toggle="modal" data-target="#add-guests-{{ $reservation->id }}"> 
                                                                    <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Guest" aria-hidden="true"></i>
                                                                </a>
                                                            </span>
                                                        @endif
                                                    @endif
                                                @else
                                                    @if ($order->status != "Paid" && $order->status != "Approved")
                                                        <span>
                                                            <a href="#" data-toggle="modal" data-target="#add-guests-{{ $reservation->id }}"> 
                                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Guest" aria-hidden="true"></i>
                                                            </a>
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                            {{-- Modal Add Guest --------------------------------------------------------------------------------------------------------------- --}}
                                            @if ($order->status != "Paid" && $order->status != "Approved")
                                                <div class="modal fade" id="add-guests-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Guest</div>
                                                                </div>
                                                                <form id="addGuest" action="{{ route('func.reservation-add-guest',$order->id) }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('put')
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            <div class="form-group row">
                                                                                <label for="name" class="col-sm-12 col-md-12 col-form-label">Name <span>*</span></label>
                                                                                <div class="col-sm-12">
                                                                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert guest name" value="{{ old('name') }}" required>
                                                                                </div>
                                                                                @error('name')
                                                                                    <div class="alert-form">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <div class="form-group row">
                                                                                <label for="name_mandarin" class="col-sm-12 col-md-12 col-form-label">Mandarin Name </label>
                                                                                <div class="col-sm-12">
                                                                                <input type="text" name="name_mandarin" class="form-control @error('name_mandarin') is-invalid @enderror" placeholder="Insert guest name" value="{{ old('name_mandarin') }}">
                                                                                </div>
                                                                                @error('name_mandarin')
                                                                                    <div class="alert-form">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-3">
                                                                            <div class="form-group row">
                                                                                <label for="sex" class="col-sm-12 col-md-12 col-form-label">Gender <span>*</span></label>
                                                                                <div class="col-sm-12">
                                                                                    <select name="sex" class="custom-select @error('sex') is-invalid @enderror" value="{{ old('sex') }}" required>
                                                                                        <option selected value="">Select</option>
                                                                                        <option value="m">Male</option>
                                                                                        <option value="f">Female</option>
                                                                                    </select>
                                                                                    @error('sex')
                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-3">
                                                                            <div class="form-group row">
                                                                                <label for="sex" class="col-sm-12 col-md-12 col-form-label">Age <span>*</span></label>
                                                                                <div class="col-sm-12">
                                                                                    <select name="age" class="custom-select @error('age') is-invalid @enderror" value="{{ old('age') }}" required>
                                                                                        <option selected value="">Select</option>
                                                                                        <option value="Adult">Adult</option>
                                                                                        <option value="Child">Child</option>
                                                                                    </select>
                                                                                    @error('age')
                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <div class="form-group row">
                                                                                <label for="phone" class="col-sm-12 col-md-12 col-form-label">Phone Number</label>
                                                                                <div class="col-sm-12">
                                                                                <input type="number" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Insert phone number" value="{{ old('phone') }}">
                                                                                </div>
                                                                                @error('phone')
                                                                                    <div class="alert-form">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                                                    </div>
                                                                </form>
                                                                <div class="card-box-footer">
                                                                    <button type="submit" form="addGuest" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            {{-- GUESTS ------------------------------------------------------------------------------------------------------------------------ --}}
                                            @if (count($guests) > 0)
                                                <div class="card-ptext-margin">
                                                    <table class="table tb-list" style="padding: 0 !important">
                                                        @foreach ($guests as $number=>$guest)
                                                            <form id="deleteGuest{{ $guest->id }}" action="/delete-guest/{{ $guest->id }}" method="post">
                                                                @csrf
                                                                @method('delete')
                                                            </form>
                                                            <tr>
                                                                <td style="padding: 0 !important">
                                                                    <div class="reservation-guest">
                                                                        @if ($guest->sex == "m")
                                                                            {{ ++$number.". " }}Mr. {{ $guest->name }} 
                                                                        @else
                                                                            @if (Carbon::parse($guest->date_of_birth)->age > 17)
                                                                                {{ ++$number.". " }}Ms. {{ $guest->name }} 
                                                                            @else
                                                                                {{ ++$number.". " }} {{ $guest->age == "Adult"?"Ms.":"Miss." }} {{ $guest->name }} 
                                                                            @endif
                                                                        @endif
                                                                        @if ($guest->date_of_birth)
                                                                            {{ " (".dateFormat($guest->date_of_birth).") (". Carbon::parse($guest->date_of_birth)->age.")" }}
                                                                        @endif
                                                                        @if ($order->handled_by)
                                                                            @if ($order->handled_by == Auth::user()->id)
                                                                                @if ($order->status != "Paid" && $order->status != "Approved")
                                                                                    <span>
                                                                                        <a href="#" data-toggle="modal" data-target="#edit-guest-{{ $guest->id }}"> 
                                                                                            <button class="btn btn-update" data-toggle="tooltip" data-placement="left" title="Edit {{ $guest->name }}"><i class="icon-copy fa fa-pencil p-0"></i></button>
                                                                                        </a>
                                                                                        <button form="deleteGuest{{ $guest->id }}" class="btn btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Delete {{ $guest->name }}"><i class="icon-copy fa fa-trash p-0"></i></button>
                                                                                    </span>
                                                                                @endif
                                                                            @endif
                                                                        @else
                                                                            @if ($order->status == "Pending")
                                                                                <span>
                                                                                    <a href="#" data-toggle="modal" data-target="#edit-guest-{{ $guest->id }}"> 
                                                                                        <button form="deleteGuest{{ $guest->id }}" class="btn btn-update" data-toggle="tooltip" data-placement="left" title="Edit {{ $guest->name }}"><i class="icon-copy fa fa-pencil p-0"></i></button>
                                                                                    </a>
                                                                                </span>
                                                                            @endif
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            {{-- Modal Edit Guest --------------------------------------------------------------------------------------------------------------- --}}
                                                            @if ($order->status != "Paid" && $order->status != "Approved")
                                                                <div class="modal fade" id="edit-guest-{{ $guest->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content text-left">
                                                                            <div class="card-box">
                                                                                <div class="card-box-title">
                                                                                    <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Edit Guest</div>
                                                                                </div>
                                                                                <form id="updateGuest-{{ $guest->id }}" action="/fupdate-guest/{{ $guest->id }}" method="post" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('put')
                                                                                    <div class="row">
                                                                                        <div class="col-sm-6">
                                                                                            <div class="form-group row">
                                                                                                <label for="name" class="col-sm-12 col-md-12 col-form-label">Name <span>*</span></label>
                                                                                                <div class="col-sm-12">
                                                                                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert guest name" value="{{ $guest->name }}" required>
                                                                                                </div>
                                                                                                @error('name')
                                                                                                    <div class="alert-form">{{ $message }}</div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-6">
                                                                                            <div class="form-group row">
                                                                                                <label for="name_mandarin" class="col-sm-12 col-md-12 col-form-label">Mandarin Name </label>
                                                                                                <div class="col-sm-12">
                                                                                                <input type="text" name="name_mandarin" class="form-control @error('name_mandarin') is-invalid @enderror" placeholder="Insert guest name" value="{{ $guest->name_mandarin }}">
                                                                                                </div>
                                                                                                @error('name_mandarin')
                                                                                                    <div class="alert-form">{{ $message }}</div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-2">
                                                                                            <div class="form-group row">
                                                                                                <label for="sex" class="col-sm-12 col-md-12 col-form-label">Gender <span>*</span></label>
                                                                                                <div class="col-sm-12">
                                                                                                    <select name="sex" class="custom-select @error('sex') is-invalid @enderror" required>
                                                                                                        <option selected value="{{ $guest->sex }}">@if ($guest->sex == "m")Male @else Female @endif</option>
                                                                                                        @if ($guest->sex == "m")
                                                                                                            <option value="f">Female</option>
                                                                                                        @else
                                                                                                            <option value="m">Male</option>
                                                                                                        @endif
                                                                                                    </select>
                                                                                                    @error('sex')
                                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                         <div class="col-sm-4">
                                                                                            <div class="form-group row">
                                                                                                <label for="sex" class="col-sm-12 col-md-12 col-form-label">Age <span>*</span></label>
                                                                                                <div class="col-sm-12">
                                                                                                    <select name="age" class="custom-select @error('age') is-invalid @enderror" required>
                                                                                                        <option value="">Select</option>
                                                                                                        <option {{ $guest->age === "Adult"?"selected":""; }} value="Adult">Adult</option>
                                                                                                        <option {{ $guest->age === "Child"?"selected":""; }} value="Child">Child</option>
                                                                                                    </select>
                                                                                                    @error('age')
                                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-6">
                                                                                            <div class="form-group row">
                                                                                                <label for="phone" class="col-sm-12 col-md-12 col-form-label">Phone Number</label>
                                                                                                <div class="col-sm-12">
                                                                                                <input type="number" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Insert phone number" value="{{ $guest->phone }}">
                                                                                                </div>
                                                                                                @error('phone')
                                                                                                    <div class="alert-form">{{ $message }}</div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>
                                                                                <div class="card-box-footer">
                                                                                    <button type="submit" form="updateGuest-{{ $guest->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </table>
                                                </div>
                                            @else
                                                <div class="card-ptext-margin">-</div>
                                            @endif
                                            
                                        </div>
                                        {{-- GUIDE --}}
                                        <div class="col-md-6">
                                            <div class="tab-inner-title ">Guide
                                                @if ($order->handled_by)
                                                    @if ($order->handled_by == Auth::id())
                                                        @if ($order->status != "Paid" && $order->status != "Approved")
                                                            @if (!$guideOrder)
                                                                <span>
                                                                    <a href="#" data-toggle="modal" data-target="#add-guide-{{ $order->id }}"> 
                                                                        <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Guide" aria-hidden="true"></i>
                                                                    </a>
                                                                </span>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @else
                                                    @if ($order->status != "Paid" && $order->status != "Approved")
                                                        @if (!$guideOrder)
                                                            <span>
                                                                <a href="#" data-toggle="modal" data-target="#add-guide-{{ $order->id }}"> 
                                                                    <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Guide" aria-hidden="true"></i>
                                                                </a>
                                                            </span>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            @if ($guideOrder)
                                                <form id="deleteGuideOrder{{ $order->id }}" action="/fdelete-guide-order/{{ $order->id }}" method="post">
                                                    @csrf
                                                    @method('put')
                                                </form>
                                                <div class="card-ptext-margin">
                                                    <div class="reservation-guest">
                                                        @if ($guideOrder->sex == "f")
                                                            Ms. {{ $guideOrder->name }}
                                                        @else
                                                            Mr. {{ $guideOrder->name }}
                                                        @endif
                                                        <i>({{ $guideOrder->language }})</i>
                                                        @if ($order->handled_by)
                                                            @if ($order->handled_by == Auth::user()->id)
                                                                @if ($order->status != "Paid" && $order->status != "Approved")
                                                                    <span>
                                                                        <a href="#" data-toggle="modal" data-target="#edit-guide-{{ $order->id }}"> 
                                                                            <button class="btn btn-update" data-toggle="tooltip" data-placement="left" title="Edit {{ $guideOrder->name }}"><i class="icon-copy fa fa-pencil p-0"></i></button>
                                                                        </a>
                                                                        <button form="deleteGuideOrder{{ $order->id }}"  class="btn btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Delete {{ $guideOrder->name }}"><i class="icon-copy fa fa-trash p-0"></i></button>
                                                                    </span>
                                                                @endif
                                                            @endif
                                                        @else
                                                            @if ($order->status != "Paid" && $order->status != "Approved")
                                                                <span>
                                                                    <a href="#" data-toggle="modal" data-target="#edit-guide-{{ $order->id }}"> 
                                                                        <button class="btn btn-update" data-toggle="tooltip" data-placement="left" title="Edit {{ $guideOrder->name }}"><i class="icon-copy fa fa-pencil"></i></button>
                                                                    </a>
                                                                    <button form="deleteGuideOrder{{ $order->id }}" class="btn btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Delete {{ $guideOrder->name }}"><i class="icon-copy fa fa-trash p-0"></i></button>
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                                        
                                                {{-- Modal Edit Guide --------------------------------------------------------------------------------------------------------------- --}}
                                                @if ($reservation->status != "Paid")
                                                    <div class="modal fade" id="edit-guide-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Edit Guide</div>
                                                                    </div>
                                                                    <form id="editGuideOrder" action="/fedit-guide-order-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('put')
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <div class="form-group row">
                                                                                    <label for="guide_id" class="col-sm-12 col-md-12 col-form-label">Select Guide</label>
                                                                                    <div class="col-sm-12">
                                                                                        <select name="guide_id" class="custom-select @error('guide_id') is-invalid @enderror" value="{{ old('guide_id') }}">
                                                                                            <option selected value="{{ $guideOrder->id }}">{{ $guideOrder->name }}</option>
                                                                                            @foreach ($guides as $guide)
                                                                                                <option value="{{ $guide->id }}">{{ $guide->name }}</option>
                                                                                            @endforeach
                                                                                            
                                                                                        </select>
                                                                                        @error('guide_id')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                                                        </div>
                                                                    </form>
                                                                    <div class="card-box-footer">
                                                                        <button type="submit" form="editGuideOrder" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Change</button>
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="card-ptext-margin">
                                                    -
                                                </div>
                                            @endif
                                            {{-- Modal Add Guide --------------------------------------------------------------------------------------------------------------- --}}
                                            @if ($reservation->status != "Paid")
                                                <div class="modal fade" id="add-guide-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Guide</div>
                                                                </div>
                                                                <form id="addGuideOrder" action="/fadd-guide-order-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('put')
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group row">
                                                                                <label for="guide_id" class="col-sm-12 col-md-12 col-form-label">Select Guide</label>
                                                                                <div class="col-sm-12">
                                                                                    <select name="guide_id" class="custom-select @error('guide_id') is-invalid @enderror" value="{{ old('guide_id') }}">
                                                                                        <option selected value="">Select Guide</option>
                                                                                        @foreach ($guides as $guide)
                                                                                            <option value="{{ $guide->id }}">{{ $guide->name }}</option>
                                                                                        @endforeach
                                                                                        
                                                                                    </select>
                                                                                    @error('guide_id')
                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                                                    </div>
                                                                </form>
                                                                <div class="card-box-footer">
                                                                    <button type="submit" form="addGuideOrder" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        {{-- DRIVER --}}
                                        <div class="col-md-6">
                                            <div class="tab-inner-title ">Driver
                                                @if ($order->handled_by)
                                                    @if ($order->handled_by == Auth::user()->id)
                                                        @if ($order->status != "Paid" && $order->status != "Approved")
                                                            @if (!$driverOrder)
                                                                <span>
                                                                    <a href="#" data-toggle="modal" data-target="#add-driver-{{ $order->id }}"> 
                                                                        <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Driver" aria-hidden="true"></i>
                                                                    </a>
                                                                </span>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @else
                                                    @if ($order->status != "Paid" && $order->status != "Approved")
                                                        @if (!$driverOrder)
                                                            <span>
                                                                <a href="#" data-toggle="modal" data-target="#add-driver-{{ $order->id }}"> 
                                                                    <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Driver" aria-hidden="true"></i>
                                                                </a>
                                                            </span>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            @if ($driverOrder)
                                                <form id="deleteDriverOrder{{ $order->id }}" action="/fdelete-driver-order-{{ $order->id }}" method="post">
                                                    @csrf
                                                    @method('put')
                                                </form>
                                                <div class="card-ptext-margin">
                                                    <div class="reservation-guest">
                                                        Mr. {{ $driverOrder->name." (".$driverOrder->phone.")" }}
                                                        @if ($order->handled_by)
                                                            @if ($order->handled_by == Auth::user()->id)
                                                                @if ($order->status != "Paid" && $order->status != "Approved")
                                                                    <span>
                                                                        <a href="#" data-toggle="modal" data-target="#edit-driver-{{ $order->id }}"> 
                                                                            <button class="btn btn-update" data-toggle="tooltip" data-placement="left" title="Change {{ $driverOrder->name }}"><i class="icon-copy fa fa-pencil p-0"></i></button>
                                                                        </a>
                                                                        
                                                                        <button form="deleteDriverOrder{{ $order->id }}" class="btn btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Delete {{ $driverOrder->name }}"><i class="icon-copy fa fa-trash p-0"></i></button>
                                                                    </span>
                                                                @endif
                                                            @endif
                                                        @else
                                                            @if ($order->status != "Paid" && $order->status != "Approved")
                                                                <span>
                                                                    <a href="#" data-toggle="modal" data-target="#edit-driver-{{ $order->id }}"> 
                                                                        <button class="btn btn-update" data-toggle="tooltip" data-placement="left" title="Change {{ $driverOrder->name }}"><i class="icon-copy fa fa-pencil p-0"></i></button>
                                                                    </a>
                                                                    <button form="deleteDriverOrder{{ $order->id }}" class="btn btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Delete {{ $driverOrder->name }}"><i class="icon-copy fa fa-trash p-0"></i></button>
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- Modal Edit Driver --------------------------------------------------------------------------------------------------------------- --}}
                                                @if ($reservation->status != "Approved")
                                                    <div class="modal fade" id="edit-driver-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Change Driver</div>
                                                                    </div>
                                                                    <form id="editGuideOrder" action="/fedit-driver-order-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('put')
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <div class="form-group row">
                                                                                    <label for="driver_id" class="col-sm-12 col-md-12 col-form-label">Select Guide</label>
                                                                                    <div class="col-sm-12">
                                                                                        <select name="driver_id" class="custom-select @error('driver_id') is-invalid @enderror" value="{{ old('driver_id') }}">
                                                                                            <option selected value="{{ $driverOrder->id }}">{{ $driverOrder->name }}</option>
                                                                                            @foreach ($drivers as $driver)
                                                                                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                                                                            @endforeach
                                                                                            
                                                                                        </select>
                                                                                        @error('driver_id')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                                                        </div>
                                                                    </form>
                                                                    <div class="card-box-footer">
                                                                        <button type="submit" form="editGuideOrder" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Change</button>
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="card-ptext-margin">
                                                    -
                                                </div>
                                            @endif
                                            {{-- Modal Add Driver --------------------------------------------------------------------------------------------------------------- --}}
                                            @if ($order->status != "Paid" && $order->status != "Approved")
                                                <div class="modal fade" id="add-driver-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Driver</div>
                                                                </div>
                                                                <form id="addDriverOrder" action="/fadd-driver-order-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('put')
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group row">
                                                                                <label for="driver_id" class="col-sm-12 col-md-12 col-form-label">Driver</label>
                                                                                <div class="col-sm-12">
                                                                                    <select name="driver_id" class="custom-select @error('driver_id') is-invalid @enderror" value="{{ old('driver_id') }}">
                                                                                        <option selected value="">Select Driver</option>
                                                                                        @foreach ($drivers as $driver)
                                                                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                                                                        @endforeach
                                                                                        
                                                                                    </select>
                                                                                    @error('driver_id')
                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                                                    </div>
                                                                </form>
                                                                <div class="card-box-footer">
                                                                    <button type="submit" form="addDriverOrder" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        {{-- ORDER / SERVICE --}}
                                        <div class="col-md-12">
                                            <div id="order" class="tab-inner-title">
                                                Order
                                            </div>
                                            <div class="card-ptext-margin">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card-ptext-content">
                                                            <div class="ptext-title">Order Number</div>
                                                            <div class="ptext-value"><b>{{ $order->orderno }}</b></div>
                                                            @if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package")
                                                                <div class="ptext-title">Hotel</div>
                                                                <div class="ptext-value">{{ $order->servicename }}</div>
                                                            @elseif ($order->service == "Private Villa")
                                                                <div class="ptext-title">Villa</div>
                                                                <div class="ptext-value">{{ $order->servicename }}</div>
                                                            @elseif ($order->service == "Activity")
                                                                <div class="ptext-title">Partner</div>
                                                                <div class="ptext-value">{{ $order->servicename }}</div>
                                                            @elseif ($order->service == "Transport")
                                                                <div class="ptext-title">Transport</div>
                                                                <div class="ptext-value">{{ $order->servicename }}</div>
                                                            @endif
                                                            @if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package")
                                                                <div class="ptext-title">Room</div>
                                                                <div class="ptext-value">{{ $order->subservice }}</div>
                                                            @elseif ($order->service == "Tour Package")
                                                                <div class="ptext-title">Tour Package</div>
                                                                <div class="ptext-value">{{ $order->servicename }}</div>
                                                            @elseif ($order->service == "Activity")
                                                                <div class="ptext-title">Activity</div>
                                                                <div class="ptext-value">{{ $order->subservice }}</div>
                                                            @elseif ($order->service == "Transport")
                                                                <div class="ptext-title">Type</div>
                                                                <div class="ptext-value">{{ $order->subservice }}</div>
                                                            @endif
                                                            <div class="ptext-title">Number of Guests</div>
                                                            <div class="ptext-value">{{ $order->number_of_guests." guests" }}</div>
                                                            @if ($order->service == "Hotel Promo")
                                                                <div class="ptext-title">Promo</div>
                                                                <div class="ptext-value">{{ $order->promo_name }}</div>
                                                            @elseif ($order->service == "Hotel Package")
                                                                <div class="ptext-title">Package</div>
                                                                <div class="ptext-value">{{ $order->package_name }}</div>
                                                            @elseif ($order->service == "Private Villa")
                                                                <div class="ptext-title">Number of Rooms</div>
                                                                <div class="ptext-value">{{ $order->number_of_room }}</div>
                                                            @elseif ($order->service == "Transport")
                                                                <div class="ptext-title">Capacity</div>
                                                                <div class="ptext-value">{{ $order->capacity . ' Seats' }}</div>
                                                            @endif
                                                            @if ($order->status == "Pending")
                                                                @if ($order->service == "Private Villa")
                                                                    @php
                                                                        $guest_children = count($guests->where('age','Child'));
                                                                    @endphp
                                                                    <div class="ptext-title">Number of Guests</div>
                                                                    <div class="ptext-value">
                                                                        {{ count($guests->where('age','Adult'))." adults" }} 
                                                                        @if ($guest_children > 0)
                                                                            + {{ count($guests->where('age','Child')) }} {{ $guest_children > 1 ?"children":"child"; }}
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    @php
                                                                        $ar = is_array($guest_detail);
                                                                    @endphp
                                                                    <div class="ptext-title">Guest Detail</div>
                                                                    <div class="ptext-value">
                                                                        @if ($ar == 1)
                                                                            @php
                                                                                $guests_name = implode(', ',$guest_detail);
                                                                            @endphp
                                                                            {{ $guests_name }}
                                                                        @else
                                                                            {!!  $order->guest_detail !!}
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card-ptext-content">
                                                            @if ($order->service == "Hotel" or $order->service == "Hotel Package" or $order->service == "Hotel Promo")
                                                                @php
                                                                    $final_date = Carbon::parse($now);
                                                                    $cin = Carbon::parse($order->checkin);
                                                                    $release_limit = $final_date->diffInDays($cin);
                                                                @endphp
                                                                <div class="ptext-title">@lang('messages.Service') </div>
                                                                <div class="ptext-value">{{ $order->service }}</div>
                                                                <div class="ptext-title">@lang('messages.Number of Room') </div>
                                                                <div class="ptext-value">
                                                                    @if ($order->number_of_room > 0)
                                                                        {{ $order->number_of_room." Unit" }}
                                                                    @else
                                                                        {{ $order->number_of_room." Unit" }}
                                                                    @endif
                                                                </div>
                                                                <div class="ptext-title">@lang('messages.Duration') </div>
                                                                <div class="ptext-value">{{ $order->duration . ' Night' }}</div>
                                                                <div class="ptext-title">@lang('messages.Check-in') </div>
                                                                <div class="ptext-value">{{ dateFormat($order->checkin) }}</div>
                                                                <div class="ptext-title">@lang('messages.Check-out') </div>
                                                                <div class="ptext-value">{{ dateFormat($order->checkout) }}</div>
                                                            @elseif ($order->service == "Private Villa")
                                                                <div class="ptext-title">@lang('messages.Duration') </div>
                                                                <div class="ptext-value">{{ $order->duration . ' Night' }}</div>
                                                                <div class="ptext-title">@lang('messages.Check-in') </div>
                                                                <div class="ptext-value">{{ dateFormat($order->checkin) }}</div>
                                                                <div class="ptext-title">@lang('messages.Check-out') </div>
                                                                <div class="ptext-value">{{ dateFormat($order->checkout) }}</div>
                                                            @elseif ($order->service == "Tour Package")
                                                                @php
                                                                    $final_date = Carbon::parse($now);
                                                                    $cin = Carbon::parse($order->checkin);
                                                                    $release_limit = $final_date->diffInDays($cin);
                                                                    $tour = $tours->where('id',$order->service_id)->first();
                                                                @endphp 
                                                                <div class="ptext-title">Duration </div>
                                                                <div class="ptext-value">{{ $order->duration }}</div>
                                                                @if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package")
                                                                    @if ($order->duration > 1)
                                                                        <div class="ptext-value">{{ $order->duration." nights" }}</div>
                                                                    @else
                                                                        <div class="ptext-value">{{ $order->duration." night" }}</div>
                                                                    @endif
                                                                @elseif ($order->service == "Tour Package")
                                                                    @if ($order->duration == 1)
                                                                        <div class="ptext-value">{{ $order->duration."D" }}</div>
                                                                    @elseif ($order->duration == 2)
                                                                        <div class="ptext-value">{{ ($order->duration)."D/".($order->duration - 1)."N" }}</div>
                                                                    @elseif ($order->duration == 3)
                                                                        <div class="ptext-value">{{ ($order->duration)."D/".($order->duration - 1)."N" }}</div>
                                                                    @elseif ($order->duration == 4)
                                                                        <div class="ptext-value">{{ ($order->duration)."D/".($order->duration - 1)."N" }}</div>
                                                                    @elseif ($order->duration == 5)
                                                                        <div class="ptext-value">{{ ($order->duration)."D/".($order->duration - 1)."N" }}</div>
                                                                    @elseif ($order->duration == 6)
                                                                        <div class="ptext-value">{{ ($order->duration)."D/".($order->duration - 1)."N" }}</div>
                                                                    @elseif ($order->duration == 7)
                                                                        <div class="ptext-value">{{ ($order->duration)."D/".($order->duration - 1)."N" }}</div>
                                                                    @endif
                                                                @else
                                                                    @if ($order->duration > 1)
                                                                        <div class="ptext-value">{{ $order->duration." hours" }}</div>
                                                                    @else
                                                                        <div class="ptext-value">{{ $order->duration." hour" }}</div>
                                                                    @endif
                                                                @endif
                                                                <div class="ptext-title">@lang('messages.Tour Start') </div>
                                                                <div class="ptext-value">{{ dateFormat($order->checkin) }}</div>
                                                                <div class="ptext-title">@lang('messages.Tour End') </div>
                                                                <div class="ptext-value">{{ dateFormat($order->checkout) }}</div>
                                                            @elseif ($order->service == "Activity")
                                                                <div class="ptext-title">@lang('messages.Service') </div>
                                                                <div class="ptext-value">{{ $order->service }}</div>
                                                                <div class="ptext-title">@lang('messages.Duration') </div>
                                                                <div class="ptext-value">{{ $order->duration." hours" }}</div>
                                                                <div class="ptext-title">@lang('messages.Activity Start') </div>
                                                                <div class="ptext-value">{{ dateTimeFormat($order->travel_date) }}</div>
                                                                <div class="ptext-title">@lang('messages.Activity End') </div>
                                                                <div class="ptext-value">
                                                                    <?php
                                                                        $activity_duration = $order->duration;
                                                                        $activity_end=date('d F Y (H:i)', strtotime('+'.$activity_duration.'hours', strtotime($order->travel_date))); 
                                                                    ?>
                                                                    {{ ': ' .dateTimeFormat($activity_end) }}
                                                                </div>
                                                            @elseif ($order->service == "Transport")
                                                                <div class="ptext-title">@lang('messages.Service') </div>
                                                                <div class="ptext-value">{{ $order->service." (".$order->service_type.")" }}</div>
                                                                <div class="ptext-title">@lang('messages.Duration') </div>
                                                                <div class="ptext-value">{{ $order->duration." hours" }}</div>
                                                                <div class="ptext-title">Start </div>
                                                                <div class="ptext-value">{{ dateTimeFormat($order->pickup_date) }}</div>
                                                                <div class="ptext-title">End </div>
                                                                <div class="ptext-value">
                                                                    <?php 
                                                                        $duration = $order->duration;
                                                                        $return_date=date('d F Y (H:i)', strtotime( '+'.$duration.'hours', strtotime($order->dropoff_date)));
                                                                    ?>
                                                                    {{ dateTimeFormat($return_date) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- BENEFITS --}}
                                    @if ($order->status == "Active" or $order->status == "Approved" or $order->status == "Paid" or $order->status == "Rejected" or $order->status == "Confirmed")
                                        @if ($order->benefits)
                                            @php
                                                $benefits = json_decode($order->benefits);
                                                if (isset($benefits)) {
                                                    $cain = count($benefits);
                                                }else {
                                                    $cain = 0;
                                                }
                                            @endphp
                                            @if ($cain >0)
                                                <div class="page-text">
                                                    <hr class="form-hr">
                                                    <b>@lang('messages.Benefits') :</b>
                                                    @if (isset($benefits))
                                                        @foreach ($benefits as $benefit)
                                                            {!! $benefit !!}
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @else
                                                <div class="page-text">
                                                    <hr class="form-hr">
                                                    <b>@lang('messages.Benefits') :</b>
                                                    {!! $order->benefits !!}
                                                </div>
                                            @endif
                                        @endif
                                        {{-- DESTINATION --}}
                                        @if ($order->destinations)
                                            <div class="page-text">
                                                <hr class="form-hr">
                                                <b>@lang('messages.Destinations') :</b> <br>
                                                {!! $order->destinations !!}
                                            </div>
                                        @endif
                                        {{-- ITINERARY --}}
                                        @if ($order->itinerary)
                                            <div class="page-text">
                                                <hr class="form-hr">
                                                <b>@lang('messages.Itinerary') :</b> <br>
                                                {!! $order->itinerary !!}
                                            </div>
                                        @endif
                                        {{-- INCLUDE --}}
                                        @if ($order->include)
                                            @php
                                                $includes = json_decode($order->include);
                                                if (isset($includes)) {
                                                    $cincl = count($includes);
                                                }else {
                                                    $cincl = 0;
                                                }
                                            @endphp
                                            @if ($cincl >0)
                                                <div class="page-text">
                                                    <hr class="form-hr">
                                                    <b>@lang('messages.Include') :</b>
                                                    @if (isset($includes))
                                                        @foreach ($includes as $include)
                                                            {!! $include !!}
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @else
                                                <div class="page-text">
                                                    <hr class="form-hr">
                                                    <b>@lang('messages.Include') :</b>
                                                    {!! $order->include !!}
                                                </div>
                                            @endif
                                        @endif
                                        @if ($order->additional_info)
                                            @php
                                                $additional_infos = json_decode($order->additional_info);
                                                if (isset($additional_infos)) {
                                                    $cain = count($additional_infos);
                                                }else {
                                                    $cain = 0;
                                                }
                                            @endphp
                                            @if ($cain >0)
                                                <div class="page-text">
                                                    <hr class="form-hr">
                                                    <b>@lang('messages.Additional Information') :</b>
                                                    @if (isset($additional_infos))
                                                        @foreach ($additional_infos as $additional_info)
                                                            {!! $additional_info !!}
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @else
                                                <div class="page-text">
                                                    <hr class="form-hr">
                                                    <b>@lang('messages.Additional Information') :</b>
                                                    {!! $order->additional_info !!}
                                                </div>
                                            @endif
                                        @endif
                                        @if ($order->cancellation_policy)
                                            <div class="page-text">
                                                <hr class="form-hr">
                                                <b>@lang('messages.Cancellation Policy') :</b>
                                                <p>{!! $order->cancellation_policy !!}</p>
                                            </div>
                                        @endif
                                    @endif
                                    
                                    {{-- SUITES AND VILLAS ===================================================================================================================== --}}
                                    @if ($order->service == "Hotel" or $order->service == "Hotel Package" or $order->service == "Hotel Promo")
                                        @if ($order->number_of_room == "" or $order->number_of_guests_room == "" or $order->guest_detail == "" )
                                            <div class="tab-inner-title" style=" background-color: #ffe3e3; border: 2px dotted red;">Suites and Villas</div>
                                        @else
                                            <div class="tab-inner-title">Suites and Villas</div>
                                        @endif
                                        <div class="row">
                                            @if ($order->number_of_room == "" or $order->number_of_guests_room == "" or $order->guest_detail == "" )
                                                <div class="col-sm-12 m-b-18">
                                                    <div class="room-container ">
                                                        <p style="color:brown;"><i>There are no rooms booked in this booking!</i></p>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-md-12">
                                                    <table class="data-table table nowrap" >
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 5%;">Room</th>
                                                                <th style="width: 5%;">Nuber Of Guests</th>
                                                                <th style="width: 15%; max-width:15%;">Guests Name</th>
                                                                <th style="width: 10%;">Price</th>
                                                                <th style="width: 10%;">Extra Bed</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @for ($i = 0; $i < $nor; $i++)
                                                                @if ($special_day[$i])
                                                                    <tr data-toggle="tooltip" data-placement="top" title="{{ date('d M Y', strtotime($special_date[$i]))." ".$special_day[$i]  }}" style="background-color: #ffe695;">
                                                                @else
                                                                    <tr >
                                                                @endif
                                                                    <td>
                                                                        <div class="table-service-name">{{ $r }}</div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="table-service-name">{{ $nogr[$i]." Guests" }}</div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="table-service-name">{{ $guest_detail[$i] }}</div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="table-service-name">{{ currencyFormatUsd($order->price_pax) }}</div>
                                                                    </td>
                                                                    <td>
                                                                        @if ($extra_bed[$i] == "Yes")
                                                                            @php
                                                                                $extrabed = $extra_beds->where('id',$extra_bed_id[$i])->first();
                                                                            @endphp
                                                                            @if ($extra_bed_price[$i] != "0")
                                                                                <div class="table-service-name">{{ $extrabed->name." (".$extrabed->type.") ".currencyFormatUsd($extra_bed_price[$i]) }}</div>
                                                                            @elseif($extra_bed_price[$i] == "0" && $extra_bed_id[$i])
                                                                                <div class="table-service-name">(@lang('messages.Extra Bed') {{ $extrabed->type }}) $0.00</div>
                                                                            @else
                                                                                @php
                                                                                    $order_status = "Invalid";
                                                                                @endphp
                                                                                <p class="text-danger"><i>Invalid! </i> <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="This room is occupied by more than 2 guests, and requires an extra bed, please edit it first to be able to submit an order" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></p>
                                                                            @endif
                                                                        @else
                                                                            <div class="table-service-name">-</div>
                                                                        @endif
                                                                    </td>
                                                                    
                                                                </tr>
                                                                @php
                                                                    $r++;
                                                                @endphp
                                                            @endfor
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="box-price-kicked m-b-8">
                                                        <div class="row">
                                                            <div class="col-6 col-md-6">
                                                                @if ($order->extra_bed_total_price > 0 || $order->kick_back > 0)
                                                                    <div class="normal-text">@lang('messages.Suites and Villas')</div>
                                                                    @if ($order->extra_bed_total_price > 0)
                                                                        <div class="normal-text">@lang('messages.Extra Bed')</div>
                                                                    @endif
                                                                    @if ($order->kick_back > 0)
                                                                        <div class="normal-text">@lang('messages.Kick Back')</div>
                                                                    @endif
                                                                    <hr class="form-hr">
                                                                @endif
                                                                <div class="subtotal-text">@lang('messages.Suites and Villas')</div>
                                                            </div>
                                                            <div class="col-6 col-md-6 text-right">
                                                                @if ($order->extra_bed_total_price > 0 || $order->kick_back > 0)
                                                                    <div class="text-price">{{ currencyFormatUsd($order->price_pax * $nor)  }}</div>
                                                                    @if ($order->extra_bed_total_price > 0)
                                                                        <div class="text-price">{{ currencyFormatUsd($order->extra_bed_total_price) }}</div>
                                                                    @endif
                                                                    @if ($order->kick_back > 0)
                                                                        <div class="promo-text">{{ currencyFormatUsd($order->kick_back) }}</div>
                                                                    @endif
                                                                    <hr class="form-hr"> 
                                                                @endif
                                                                <div class="subtotal-price">{{ currencyFormatUsd($order->price_total) }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($order->handled_by)
                                                @if ($order->handled_by == Auth::user()->id)
                                                    @if ($order->status != "Paid" && $order->status != "Approved")
                                                        @if ($order->number_of_room == "" or $order->number_of_guests_room == "" or $order->guest_detail == "" or $order->guest_detail == ""  )
                                                            <div class="col-md-6 text-right">
                                                                <a href="/admin-edit-order-room-{{ $order->id }}">
                                                                    <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                </a>
                                                            </div>
                                                        @else
                                                            @if ($order->request_quotation == "Yes")
                                                                <div class="col-md-8">
                                                                    <p style="color: blue">This order makes room reservations for more than 8 rooms, confirm immediately.</p>
                                                                </div>
                                                                <div class="col-md-4 text-right">
                                                                    <a href="/admin-edit-order-room-{{ $order->id }}">
                                                                        <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <div class="col-md-12 text-right">
                                                                    <a href="/admin-edit-order-room-{{ $order->id }}">
                                                                        <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                                    </a>
                                                                </div>
                                                            @endif 
                                                        @endif
                                                    @endif
                                                @endif
                                            @else
                                                @if ($order->status != "Paid" && $order->status != "Approved")
                                                    @if ($order->number_of_room == "" or $order->number_of_guests_room == "" or $order->guest_detail == "" or $order->guest_detail == ""  )
                                                        <div class="col-md-6 text-right">
                                                            <a href="/admin-edit-order-room-{{ $order->id }}">
                                                                <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                            </a>
                                                        </div>
                                                    @else
                                                        @if ($order->request_quotation == "Yes")
                                                            <div class="col-md-8">
                                                                <p style="color: blue">This order makes room reservations for more than 8 rooms, confirm immediately.</p>
                                                            </div>
                                                            <div class="col-md-4 text-right">
                                                                <a href="/admin-edit-order-room-{{ $order->id }}">
                                                                    <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                                </a>
                                                            </div>
                                                        @else
                                                            <div class="col-md-12 text-right">
                                                                <a href="/admin-edit-order-room-{{ $order->id }}">
                                                                    <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                                </a>
                                                            </div>
                                                        @endif 
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                    {{-- ADDITIONAL CHARGE   ===================================================================================================================== --}}
                                    @if ($order->service == "Hotel" || $order->service == "Hotel Package" || $order->service == "Hotel Promo" || $order->service == "Private Villa")
                                        @if(count($optional_rate_orders)>0)
                                            <div id="additionalCharge" class="tab-inner-title">Additional Charge</div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <table class="data-table table nowrap" >
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 10%;">Date</th>
                                                                <th style="width: 5%;">Number of Guests</th>
                                                                <th style="width: 15%;">Services</th>
                                                                <th style="width: 10%;">Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($optional_rate_orders as $optional_rate_order)
                                                                <tr>
                                                                    <td>
                                                                        <div class="table-service-name">{{ dateFormat($optional_rate_order->service_date) }}</div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="table-service-name">{{ $optional_rate_order->number_of_guest." Guests" }}</div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="table-service-name">{{ $optional_rate_order->optional_rate->name }}</div>
                                                                    </td>
                                                                    
                                                                    <td>
                                                                        <div class="table-service-name">{{ currencyFormatUsd($optional_rate_order->price_total) }}</div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    <div class="box-price-kicked m-b-8">
                                                        <div class="row">
                                                            <div class="col-6 col-md-6">
                                                                <div class="subtotal-text">Additional Charges</div>
                                                            </div>
                                                            <div class="col-6 col-md-6 text-right">
                                                                <div class="subtotal-price">{{ currencyFormatUsd($optional_rate_order_total_price) }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($order->handled_by)
                                                @if ($order->handled_by == Auth::user()->id)
                                                    @if ($order->status != "Paid" && $order->status != "Approved")
                                                        <div class="row">
                                                            @if ($optional_rate_orders)
                                                                <div class="col-md-12 text-right">
                                                                    <a href="{{ route('view.add-optional-rate-order',$order->id) }}">
                                                                        <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Edit additional charges"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                                    </a>
                                                                
                                                                </div>
                                                            @else
                                                                <div class="col-md-12 text-right">
                                                                    <a href="{{ route('view.add-optional-rate-order',$order->id) }}">
                                                                        <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Add additional charges"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                    </a>
                                                                </div>
                                                            @endif   
                                                        </div>
                                                    @endif
                                                @endif
                                            @else
                                                @if ($order->status != "Paid" && $order->status != "Approved")
                                                    <div class="row">
                                                        @if ($optional_rate_orders)
                                                            <div class="col-md-12 text-right">
                                                                <a href="{{ route('view.add-optional-rate-order',$order->id) }}">
                                                                    <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Edit additional charges"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                                </a>
                                                            
                                                            </div>
                                                        @else
                                                            <div class="col-md-12 text-right">
                                                                <a href="{{ route('view.add-optional-rate-order',$order->id) }}">
                                                                    <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Add additional charges"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                </a>
                                                            </div>
                                                        @endif   
                                                    </div>
                                                @endif
                                            @endif
                                        @else
                                            
                                            @if (($villa_optionalrates && $villa_optionalrates->isNotEmpty()) || ($optionalrates && $optionalrates->isNotEmpty()))
                                                <div id="optional_service" class="tab-inner-title">Additional Charges</div>
                                                <div class="row">
                                                    <div class="col-sm-12 m-b-8">
                                                        <div class="card-ptext-margin">
                                                            <i style="color: red;">In this order there is no additional charge added!</i>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 text-right">
                                                        <a href="{{ route('view.add-optional-rate-order',$order->id) }}">
                                                            <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Edit optional service"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Add</button>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endif
                                    {{-- ADDITIONAL SERVICES --}}
                                    <div class="row" id="additionalServices">
                                        <div class="col-md-12">
                                            <div class="tab-inner-title">Additional Services</div>
                                        </div>
                                        @if ($order->additional_service)
                                            <div class="col-md-12">
                                                <table class="data-table table nowrap" >
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 30%;">Date</th>
                                                            <th style="width: 40%;">Service</th>
                                                            <th style="width: 10%;">QTY</th>
                                                            <th style="width: 10%;">Price</th>
                                                            <th style="width: 10%;">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $cadser = count($additional_service);
                                                        @endphp
                                                        @for ($x = 0; $x < $cadser; $x++)
                                                            <tr>
                                                                <td>
                                                                    <div class="table-service-name">{{ dateFormat($additional_service_date[$x]) }}</div>
                                                                </td>
                                                                <td>
                                                                    <div class="table-service-name">{{ $additional_service[$x] }}</div>
                                                                </td>
                                                                <td>
                                                                    <div class="table-service-name">{{ $additional_service_qty[$x] }}</div>
                                                                </td>
                                                                
                                                                <td>
                                                                    <div class="table-service-name">{{ currencyFormatUsd($additional_service_price[$x]) }}</div>
                                                                </td>
                                                                <td>
                                                                    <div class="table-service-name">{{ currencyFormatUsd($additional_service_price[$x]*$additional_service_qty[$x]) }}</div>
                                                                </td>
                                                            </tr>
                                                        @endfor
                                                    </tbody>
                                                </table>
                                                <div class="box-price-kicked m-b-8">
                                                    <div class="row">
                                                        <div class="col-6 col-md-6">
                                                            <div class="subtotal-text"> Total Additional Service</div>
                                                        </div>
                                                        <div class="col-6 col-md-6 text-right">
                                                            <div class="subtotal-price">{{ currencyFormatUsd($total_additional_service) }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-12">
                                                <div class="card-ptext-margin">
                                                    <i style="color: red;">In this order there is no additional service added!</i>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($order->handled_by)
                                            @if ($order->handled_by == Auth::user()->id)
                                                @if ($order->status != "Paid" && $order->status != "Approved")
                                                    @if ($order->additional_service)
                                                        <div class="col-md-12 text-right">
                                                            <a href="/edit-additional-services-{{ $order->id }}">
                                                                <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Edit additional charge"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="col-md-12 text-right">
                                                            <a href="/edit-additional-services-{{ $order->id }}">
                                                                <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Add additional charge"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif
                                        @else
                                            @if ($order->status != "Paid" && $order->status != "Approved")
                                                @if ($order->additional_service)
                                                    <div class="col-md-12 text-right">
                                                        <a href="/edit-additional-services-{{ $order->id }}">
                                                            <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Edit additional charge"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="col-md-12 text-right">
                                                        <a href="/edit-additional-services-{{ $order->id }}">
                                                            <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Add additional charge"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                        </a>
                                                    </div>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                    
                                    
                                    {{-- AIRPORT SHUTTLE --}}
                                    @if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package" )
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="tab-inner-title">Airport Shuttle</div>
                                            </div>
                                            <div class="col-md-12">
                                                <table class="data-table table nowrap" >
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Flight</th>
                                                            <th>Transport</th>
                                                            <th>Src <=> Dst</th>
                                                            <th>Duration</th>
                                                            <th>Distance</th>
                                                            <th>Price</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($airport_shuttles as $noairport => $airport_shuttle)
                                                            <tr>
                                                                <td>{{ ++$noairport }}</td>
                                                                <td>
                                                                    <b>{{ $airport_shuttle->flight_number }}</b><br>
                                                                    {{ dateTimeFormat($airport_shuttle->date) }}
                                                                </td>
                                                                <td>{{ $airport_shuttle->transport->brand." ".$airport_shuttle->transport->name }}</td>
                                                                <td>{{ $airport_shuttle->src." <=> ".$airport_shuttle->dst }}</td>
                                                                <td>{{ $airport_shuttle->duration." hours" }}</td>
                                                                <td>{{ $airport_shuttle->distance." Km" }}</td>
                                                                <td>{{  currencyFormatUsd($airport_shuttle->price)  }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <div class="box-price-kicked m-b-8">
                                                    <div class="row">
                                                        <div class="col-6 col-md-6">
                                                            <div class="subtotal-text"> Airport Shuttle</div>
                                                        </div>
                                                        <div class="col-6 col-md-6 text-right">
                                                            @if ($order->airport_shuttle_price > 0)
                                                                <div class="subtotal-price">{{ currencyFormatUsd($order->airport_shuttle_price) }}</div>
                                                            @else
                                                                <div class="subtotal-price"><i>@lang('messages.To be advised')</i></div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($order->handled_by)
                                                @if ($order->handled_by == Auth::user()->id)
                                                    @if ($order->status != "Paid" && $order->status != "Approved")
                                                        @if (count($airport_shuttles) > 0)
                                                            <div class="col-md-12 text-right">
                                                                <a href="/edit-airport-shuttle-{{ $order->id }}">
                                                                    <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                                </a>
                                                            </div>
                                                        @else
                                                            <div class="col-md-12 text-right">
                                                                <a href="/edit-airport-shuttle-{{ $order->id }}">
                                                                    <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endif
                                            @else
                                                @if ($order->status != "Paid" && $order->status != "Approved")
                                                    @if (count($airport_shuttles) > 0)
                                                        <div class="col-md-12 text-right">
                                                            <a href="/edit-airport-shuttle-{{ $order->id }}">
                                                                <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="col-md-12 text-right">
                                                            <a href="/edit-airport-shuttle-{{ $order->id }}">
                                                                <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    @endif

                                    @if ($order->status == "Active" or $order->status == "Approved" or $order->status == "Paid" or $order->status == "Rejected" or $order->status == "Confirmed")
                                        @if (isset($order->note))
                                            <div class="card-ptext-margin">
                                                <b>@lang('messages.Remark') :</b>
                                                {!! $order->note !!}
                                            </div>
                                        @endif
                                    @endif
                                    @if ($order->status != "Paid" && $order->status != "Approved")
                                        <form id="fupdate-order" action="{{ route('func.order-admin.update',$order->id) }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            @method('put')
                                            <div class="row">
                                                @if ($order->service == "Hotel Package" or $order->service == "Hotel Promo")
                                                    <div class="col-md-12">
                                                        <div class="tab-inner-title">Benefits</div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <textarea id="benefits" name="benefits" placeholder="Optional" class="textarea_editor form-control border-radius-0">{!! $order->benefits !!}</textarea>
                                                            @error('benefits')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($order->service == "Tour Package")
                                                    <div class="col-md-12">
                                                        <div class="tab-inner-title">Destinations</div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <textarea id="destinations" name="destination" placeholder="Optional" class="textarea_editor form-control border-radius-0">{!! $order->destinations !!}</textarea>
                                                            @error('destination')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($order->service == "Activity" or $order->service == "Tour Package")
                                                    <div class="col-md-12">
                                                        <div class="tab-inner-title">Itinerary</div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <textarea id="itinerary" name="itinerary" placeholder="Optional" class="textarea_editor form-control border-radius-0">{!! $order->itinerary !!}</textarea>
                                                            @error('itinerary')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($order->status != "Paid" && $order->status != "Approved")
                                                    <div class="col-md-12">
                                                        <div class="tab-inner-title">Include</div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <textarea id="include" name="include" placeholder="Optional" class="textarea_editor form-control border-radius-0">{!! $order->include !!}</textarea>
                                                            @error('include')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="tab-inner-title">Additional Information</div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <textarea id="additional_info" name="additional_info" placeholder="Optional" class="textarea_editor form-control border-radius-0">{{ $order->additional_info }}</textarea>
                                                            @error('additional_info')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="tab-inner-title">Remark</div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <textarea id="note" name="note" placeholder="Optional" class="textarea_editor form-control border-radius-0">{{ $order->note }}</textarea>
                                                            @error('note')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="tab-inner-title">Cancellation Policy</div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <textarea id="cancellation_policy" name="cancellation_policy" placeholder="Optional" class="textarea_editor form-control border-radius-0">{{ $order->cancellation_policy }}</textarea>
                                                            @error('cancellation_policy')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </Form>
                                    @endif
                                    <div id="optional_service" class="tab-inner-title m-t-8">Total Price</div>
                                    <div class="row">
                                        <div class="col-md-12 m-b-8">
                                            <div class="box-price-kicked">
                                                <div class="row">
                                                    <div class="col-6 col-md-6">
                                                        @if ($total_additional_service > 0 or $order->bookingcode_disc > 0 or $order->discounts > 0 or $total_promotion_disc > 0 or $order->airport_shuttle_price || $optional_rate_order_total_price>0)
                                                            @if ($order->service == "Activity" or $order->service == "Transport")
                                                                @if ($order->service == "Transport")
                                                                    <div class="normal-text">Transport</div>
                                                                @else
                                                                    <div class="normal-text">Price / Pax</div>
                                                                @endif
                                                            @elseif($order->service == "Private Villa")
                                                                <div class="normal-text">{{ $villa->name }}</div>
                                                            @elseif($order->service == "Tour Package")
                                                                <div class="normal-text">Price / Pax</div>
                                                                <div class="normal-text">Number of Guests</div>
                                                            @else
                                                                <div class="normal-text">@lang('messages.Suites and Villas')</div>
                                                            @endif
                                                            
                                                            @if ($optional_rate_order_total_price > 0)
                                                                <div class="normal-text">Additional Charges</div>
                                                            @endif
                                                            @if ($total_additional_service > 0)
                                                                <div class="normal-text">@lang('messages.Additional Services')</div>
                                                            @endif
                                                            @if ($order->airport_shuttle_price > 0)
                                                                <div class="normal-text">Airport Shuttle</div>
                                                            @endif
                                                            @if ($order->bookingcode_disc > 0 or $order->discounts > 0 or $total_promotion_disc > 0)
                                                                <hr class="form-hr">
                                                                @if ($total_promotion_disc > 0)
                                                                    <div class="normal-text">@lang('messages.Promotion')</div>
                                                                @endif
                                                                @if ($order->bookingcode_disc > 0)
                                                                    <div class="normal-text">@lang('messages.Booking Code')</div>
                                                                @endif
                                                                @if ($order->discounts > 0)
                                                                    <div class="normal-text">@lang('messages.Discounts')</div>
                                                                @endif
                                                            @endif
                                                            <hr class="form-hr">
                                                        @endif

                                                        <div class="price-name">Total Price USD</div>
                                                        @if ($invoice)
                                                            @if ($invoice->currency?->name == "CNY")
                                                                <div class="price-name">Total Price CNY</div>
                                                            @elseif ($invoice->currency?->name == "TWD")
                                                                <div class="price-name">Total Price TWD</div>
                                                            @elseif ($invoice->currency?->name == "IDR")
                                                                <div class="price-name">Total Price IDR</div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="col-6 col-md-6 text-right">
                                                        @if ($total_additional_service > 0 or $order->bookingcode_disc > 0 or $order->discounts > 0 or $total_promotion_disc > 0 or $order->airport_shuttle_price || $optional_rate_order_total_price>0)
                                                            @if ($order->service == "Activity" or $order->service == "Transport")
                                                                @if ($order->service == "Transport")
                                                                    <div class="normal-text">{{ currencyFormatUsd($order->price_total) }}</div>
                                                                @else
                                                                    <div class="normal-text">{{ currencyFormatUsd($order->price_pax) }}</div>
                                                                @endif
                                                            @elseif($order->service == "Private Villa")
                                                                <div class="normal-text">{{ currencyFormatUsd($order->normal_price) }}</div>
                                                            @elseif($order->service == "Tour Package")
                                                                <div class="normal-text">{{ currencyFormatUsd($order->price_pax) }}</div>
                                                                <div class="normal-text">{{ $order->number_of_guests }}</div>
                                                            @else
                                                                <div class="normal-text">{{ currencyFormatUsd($order->price_total) }}</div>
                                                            @endif
                                                            
                                                            
                                                            @if ($optional_rate_order_total_price > 0)
                                                                <div class="normal-text">{{ currencyFormatUsd($optional_rate_order_total_price) }}</div>
                                                            @endif
                                                            @if ($total_additional_service > 0)
                                                                <div class="normal-text">{{ currencyFormatUsd($total_additional_service) }}</div>
                                                            @endif
                                                            @if ($order->airport_shuttle_price > 0)
                                                                <div class="normal-text">{{ currencyFormatUsd($order->airport_shuttle_price) }}</div>
                                                            @endif
                                                            @if ($order->bookingcode_disc > 0 or $order->discounts > 0 or $total_promotion_disc > 0)
                                                                <hr class="form-hr">
                                                                @if ($total_promotion_disc > 0)
                                                                    <div class="promo-text">{{ currencyFormatUsd($total_promotion_disc) }}</div>
                                                                @endif
                                                                @if ($order->bookingcode_disc > 0)
                                                                    <div class="promo-text">{{ currencyFormatUsd($order->bookingcode_disc) }}</div>
                                                                @endif
                                                                @if ($order->discounts > 0)
                                                                    <div class="promo-text">{{ currencyFormatUsd($order->discounts) }}</div>
                                                                @endif
                                                            @endif
                                                            <hr class="form-hr">
                                                        @endif
                                                        <div class="usd-rate">{{ currencyFormatUsd($order->final_price) }}</div>
                                                        @if ($invoice)
                                                            @if ($invoice->currency?->name == 'CNY')
                                                                <div class="usd-rate">{{ currencyFormatCny($invoice->total_cny) }}</div>
                                                            @elseif ($invoice->currency?->name == 'TWD')
                                                                <div class="usd-rate">{{ currencyFormatTwd($invoice->total_twd) }}</div>
                                                            @elseif ($invoice->currency?->name == 'IDR')
                                                                <div class="usd-rate">{{ currencyFormatIdr($invoice->total_idr) }}</div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($order->handled_by)
                                            @if ($order->handled_by == Auth::user()->id)
                                                @if ($order->status != "Paid" && $order->status != "Approved")
                                                    <div class="col-md-12 text-right">
                                                        @if ($order->discounts > 0)
                                                            <a href="#" data-toggle="modal" data-target="#remove-discounts-{{ $order->id }}"><button type="button" class="btn btn-secondary"><i class="fa fa-trash-o" aria-hidden="true"></i> Remove Discounts</button></a>
                                                            <a href="#" data-toggle="modal" data-target="#discounts-{{ $order->id }}"><button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Discounts</button></a>
                                                        @else
                                                            <a href="#" data-toggle="modal" data-target="#discounts-{{ $order->id }}"><button type="button" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Discounts</button></a>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif
                                        @else
                                            @if ($order->status != "Paid" && $order->status != "Approved")
                                                <div class="col-md-12 text-right">
                                                    @if ($order->discounts > 0)
                                                        <a href="#" data-toggle="modal" data-target="#remove-discounts-{{ $order->id }}"><button type="button" class="btn btn-secondary"><i class="fa fa-trash-o" aria-hidden="true"></i> Remove Discounts</button></a>
                                                        <a href="#" data-toggle="modal" data-target="#discounts-{{ $order->id }}"><button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Discounts</button></a>
                                                    @else
                                                        <a href="#" data-toggle="modal" data-target="#discounts-{{ $order->id }}"><button type="button" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Discounts</button></a>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                        
                                    </div>
                                    {{-- CURRENCY --}}
                                    @if ($order->status == "Pending" or $order->status == "Active")
                                        <div id="optional_service" class="tab-inner-title m-t-8">Currency</div>
                                        <div class="row urgent-box">
                                            <div class="col-md-12">
                                                <div class="notif-modal text-left">
                                                    Please select BANK and Currency, depend on Agent currency before you confirm the order!
                                                </div>
                                            </div>
                                            <div class="col-md-12" style="place-self:center;">
                                                <form id="factivate-order-{{ $order->id }}" action="/factivate-order/{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="bank">BANK</label>
                                                                <select name="bank" class="custom-select">
                                                                    @foreach ($banks as $bank)
                                                                        <option {{ $bank->currency == "USD"?"selected":""; }} value="{{ $bank->id }}">{{ $bank->bank }}</option>
                                                                    @endforeach
                                                                   
                                                                </select>
                                                                @error('note')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="currency">Currency</label>
                                                                <select name="currency" class="custom-select">
                                                                    @foreach ($rates as $rate)
                                                                        <option {{ $rate->name == "USD"?"selected":""; }} value="{{ $rate->id }}">{{ $rate->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('note')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($order->status != "approved" || File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf") or File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf"))
                                    @else
                                    <div id="optional_service" class="tab-inner-title m-t-8">Currency</div>
                                        <div class="row urgent-box">
                                            <div class="col-md-12">
                                                <div class="notif-modal text-left">
                                                    Please select BANK and Currency, depend on Agent currency before you confirm the order!
                                                </div>
                                            </div>
                                            <div class="col-md-12" style="place-self:center;">
                                                <form id="generateInvoice"  action="/fgenerate-invoice-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="bank">BANK</label>
                                                                <select name="bank" class="custom-select">
                                                                    @foreach ($banks as $bank)
                                                                        <option {{ $bank->currency == "USD"?"selected":""; }} value="{{ $bank->id }}">{{ $bank->bank }}</option>
                                                                    @endforeach
                                                                    
                                                                </select>
                                                                @error('note')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="currency">Currency</label>
                                                                <select name="currency" class="custom-select">
                                                                    @foreach ($rates as $rate)
                                                                        <option {{ $rate->name == "USD"?"selected":""; }} value="{{ $rate->id }}">{{ $rate->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('note')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif



                                    @if ($order->status != "Paid" && $order->status != "Approved")
                                        <div class="card-ptext-margin m-t-8">
                                            <div class="notif-modal text-left">
                                                Please make sure all the data is correct before you confirm the order!
                                            </div>
                                        </div>
                                    @else
                                        <div class="card-ptext-margin m-t-8">
                                            <div class="notif-modal text-left">
                                                This order has been validated and the status is Active!
                                            </div>
                                        </div>
                                    @endif
                                    <div class="card-box-footer">
                                        @if ($order->handled_by)
                                            @if ($order->handled_by == Auth::user()->id)
                                                @if ($order->status !== "Paid")
                                                    @if ($order->status !== "Archive")
                                                        <a href="#" data-toggle="modal" data-target="#archive-order-{{ $order->id }}"><button type="button" class="btn btn-dark"><i class="icon-copy fa fa-archive" aria-hidden="true"></i> Archive</button></a>
                                                    @endif
                                                @endif
                                                @if ($order->status !== "Paid")
                                                    @if ($order->status !== "Rejected")
                                                        <a href="#" data-toggle="modal" data-target="#reject-order-{{ $order->id }}"><button type="button" class="btn btn-rejected"><i class="fa fa-ban" aria-hidden="true"></i> Reject</button></a>
                                                    @endif
                                                @endif
                                                @if ($order->status == "Pending")
                                                    <button type="submit" form="fupdate-order" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                                    <button type="submit" form="factivate-order-{{ $order->id }}" class="btn btn-success"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Confirm</button>
                                                @else
                                                    <form id="sendConfirmation" class="hidden" action="/fsend-confirmation-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                    </form>
                                                    <form id="resendConfirmation" class="hidden" action="/fresend-confirmation-order-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                    </form>
                                                    @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf") or File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf"))
                                                        <a href="print-contract-order-{{ $order->id }}" target="__blank" >
                                                            <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-print" aria-hidden="true"></i> Print Document</button>
                                                        </a>
                                                    @endif
                                                    @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf"))
                                                        <a href="#" data-toggle="modal" data-target="#contract-en-{{ $order->id }}">
                                                            <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> Invoice EN</button>
                                                        </a>
                                                        <a href='{{URL::to('/')}}/storage/document/invoice-{{ $inv_no }}-{{ $order->id }}_en.pdf' target="_blank">
                                                            <button type="button" class="btn btn-primary mobile"><i class="fa fa-download"></i> Download Invoice EN</button>
                                                        </a>
                                                    @endif
                                                    @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf"))
                                                        <a href="#" data-toggle="modal" data-target="#contract-zh-{{ $order->id }}">
                                                            <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> Invoice ZH</button>
                                                        </a>
                                                        <a href='{{URL::to('/')}}/storage/document/invoice-{{ $inv_no }}-{{ $order->id }}_zh.pdf' target="_blank">
                                                            <button type="button" class="btn btn-primary mobile"><i class="fa fa-download"></i> Download Invoice ZH</button>
                                                        </a>
                                                    @endif
                                                    @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf") or File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf"))
                                                    @else
                                                        <form id="generateInvoice"  action="/fgenerate-invoice-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('put')
                                                        </form>
                                                        <button type="submit" form="generateInvoice" class="btn btn-primary"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> Generate Invoice</button>
                                                    @endif
                                                    @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf") or File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf"))
                                                        @if (!$reservation->send)
                                                            <button type="submit" form="sendConfirmation" class="btn btn-primary"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i> Send Confirmation</button>
                                                            <div class="loading-icon hidden pre-loader">
                                                                <div class="pre-loader-box">
                                                                    <div class="sys-loader-logo w3-center"> <img class="w3-spin" src="{{ asset('storage/icon/spinner.png') }}" alt="Bali Kami Tour Logo"></div>
                                                                    <div class="loading-text">
                                                                        Sending the Confirmation Order...
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            @if ($order->status != "Paid" && $order->status != "Approved")
                                                                <button type="submit" form="factivate-order-{{ $order->id }}" class="btn btn-primary"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i> Reconfirm</button>
                                                                <div class="loading-icon hidden pre-loader">
                                                                    <div class="pre-loader-box">
                                                                        <div class="sys-loader-logo w3-center"> <img class="w3-spin" src="{{ asset('storage/icon/spinner.png') }}" alt="Bali Kami Tour Logo"></div>
                                                                        <div class="loading-text">
                                                                            Resend Confirmation Order...
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endif
                                                    {{-- MODAL VIEW CONTRACT EN --}}
                                                    <div class="modal fade" id="contract-en-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                                <div class="modal-body pd-5">
                                                                    <embed src="storage/document/invoice-{{ $inv_no."-".$order->id }}_en.pdf" frameborder="10" width="100%" height="850px">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- MODAL VIEW CONTRACT ZH --}}
                                                    <div class="modal fade" id="contract-zh-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                                <div class="modal-body pd-5">
                                                                    <embed src="storage/document/invoice-{{ $inv_no."-".$order->id }}_zh.pdf" frameborder="10" width="100%" height="850px">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf") or File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf"))
                                                    @if ($order->status == "Confirmed" and $invoice->due_date <= $now)
                                                        <form id="sendApprovalEmail" class="hidden" action="/fsend-approval-email-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('put')
                                                        </form>
                                                        <button type="submit" form="sendApprovalEmail" class="btn btn-warning"><i class="icon-copy fa fa-exclamation-circle" aria-hidden="true"></i> Send Approval Email</button>
                                                    @endif
                                                @endif
                                                @uiEnabled('doku-payment')
                                                    @if ($invoice && $order->status === "Approved")
                                                        <form id="generateDokuPayment" class="hidden" action="/generate-doku-payment/{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                        </form>
                                                        <button type="submit" form="generateDokuPayment" class="btn btn-img-doku">
                                                            <img src="{{ asset('vendors/DOKU/DOKU Payment.png') }}" alt="Logo DOKU">
                                                            Generate Doku Payment
                                                        </button>
                                                    @endif
                                                @endUiEnabled
                                            @endif
                                        @else 
                                            @if ($order->status !== "Archive" or $order->status !== "Paid")
                                                <a href="#" data-toggle="modal" data-target="#archive-order-{{ $order->id }}"><button type="button" class="btn btn-dark"><i class="icon-copy fa fa-archive" aria-hidden="true"></i> Archive</button></a>
                                            @endif
                                            @if ($order->status !== "Rejected" or $order->status !== "Paid")
                                                <a href="#" data-toggle="modal" data-target="#reject-order-{{ $order->id }}"><button type="button" class="btn btn-rejected"><i class="fa fa-ban" aria-hidden="true"></i> Reject</button></a>
                                            @endif
                                            @if ($order->status !== "Paid")
                                                <button type="submit" form="factivate-order-{{ $order->id }}" class="btn btn-success"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Confirm</button>
                                                <button type="submit" form="fupdate-order" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> Save</button>
                                            @else
                                                <form id="sendConfirmation" class="hidden" action="/fsend-confirmation-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                </form>
                                                <form id="resendConfirmation" class="hidden" action="/fresend-confirmation-order-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                </form>

                                                @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf") or File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf"))
                                                    <a href="print-contract-order-{{ $order->id }}" target="__blank" >
                                                        <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-print" aria-hidden="true"></i> Print Document</button>
                                                    </a>
                                                @endif
                                                @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf"))
                                                    <a href="#" data-toggle="modal" data-target="#contract-en-{{ $order->id }}">
                                                        <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> Invoice EN</button>
                                                    </a>
                                                    <a href='{{URL::to('/')}}/storage/document/invoice-{{ $inv_no }}-{{ $order->id }}_en.pdf' target="_blank">
                                                        <button type="button" class="btn btn-primary mobile"><i class="fa fa-download"></i> Download Invoice EN</button>
                                                    </a>
                                                @endif
                                                @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf"))
                                                    <a href="#" data-toggle="modal" data-target="#contract-zh-{{ $order->id }}">
                                                        <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> Invoice ZH</button>
                                                    </a>
                                                    <a href='{{URL::to('/')}}/storage/document/invoice-{{ $inv_no }}-{{ $order->id }}_zh.pdf' target="_blank">
                                                        <button type="button" class="btn btn-primary mobile"><i class="fa fa-download"></i> Download Invoice ZH</button>
                                                    </a>
                                                @endif
                                                @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf") or File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf"))
                                                @else
                                                    <form id="generateInvoice" class="hidden" action="/fgenerate-invoice-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                    </form>
                                                    <button type="submit" form="generateInvoice" class="btn btn-primary"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> Generate Invoice</button>
                                                @endif
                                                @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf") or File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf"))
                                                    @if ($reservation->send == "")
                                                        <button type="submit" form="sendConfirmation" class="btn btn-primary"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i> Send Confirmation</button>
                                                        <div class="loading-icon hidden pre-loader">
                                                            <div class="pre-loader-box">
                                                                <div class="sys-loader-logo w3-center"> <img class="w3-spin" src="{{ asset('storage/icon/spinner.png') }}" alt="Bali Kami Tour Logo"></div>
                                                                <div class="loading-text">
                                                                    Sending the Confirmation Order...
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        @if ($order->status != "Paid" && $order->status != "Approved")
                                                            <button type="submit" form="factivate-order-{{ $order->id }}" class="btn btn-primary"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i> Reconfirm</button>
                                                            <div class="loading-icon hidden pre-loader">
                                                                <div class="pre-loader-box">
                                                                    <div class="sys-loader-logo w3-center"> <img class="w3-spin" src="{{ asset('storage/icon/spinner.png') }}" alt="Bali Kami Tour Logo"></div>
                                                                    <div class="loading-text">
                                                                        Resend Confirmation Order...
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endif
                                                
                                                {{-- MODAL VIEW CONTRACT EN --}}
                                                <div class="modal fade" id="contract-en-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                            <div class="modal-body pd-5">
                                                                <embed src="storage/document/invoice-{{ $inv_no."-".$order->id }}_en.pdf" frameborder="10" width="100%" height="850px">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- MODAL VIEW CONTRACT ZH --}}
                                                <div class="modal fade" id="contract-zh-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                            <div class="modal-body pd-5">
                                                                <embed src="storage/document/invoice-{{ $inv_no."-".$order->id }}_zh.pdf" frameborder="10" width="100%" height="850px">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf") or File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf"))
                                                @if ($order->status == "Confirmed" and $invoice->due_date <= $now)
                                                    <form id="sendApprovalEmail" class="hidden" action="/fsend-approval-email-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                    </form>
                                                    <button type="submit" form="sendApprovalEmail" class="btn btn-warning"><i class="icon-copy fa fa-exclamation-circle" aria-hidden="true"></i> Send Approval Email</button>
                                                @endif
                                            @endif
                                        @endif
                                        @if ($invoice && $order->status == "Approved")
                                            <form id="finalizationOrder" action="{{ route('func.admin-finalization-order',$order->id) }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                            <button type="submit" form="finalizationOrder" class="btn btn-success"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Final</button>
                                        @endif
                                        <a href="/orders-admin" ><button type="button" class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button></a>
                                    </div>
                                    <script>
                                        $(document).ready(function() {
                                            $("#sendConfirmation").submit(function() {
                                                $(".result").text("");
                                                $(".loading-icon").removeClass("hidden");
                                                $(".submit").attr("disabled", true);
                                                $(".btn-txt").text("Processing ...");
                                            });
                                        });
                                        $(document).ready(function() {
                                            $("#resendConfirmation").submit(function() {
                                                $(".result").text("");
                                                $(".loading-icon").removeClass("hidden");
                                                $(".submit").attr("disabled", true);
                                                $(".btn-txt").text("Processing ...");
                                            });
                                        });
                                    </script>
                                </div>

                                {{-- Modal Add Discount --------------------------------------------------------------------------------------------------------------- --}}
                                <div class="modal fade" id="discounts-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                @if ($order->discounts > 0)
                                                    <div class="card-box-title">
                                                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Discounts</div>
                                                    </div>
                                                @else
                                                    <div class="card-box-title">
                                                        <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Discounts</div>
                                                    </div>
                                                @endif
                                                <form id="updateDiscount" action="{{ route('func.admin-update-order-dicounts',$order->id) }}"method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="alasan_discounts">The reason for giving discounts!</label>
                                                                <textarea id="alasan_discounts" name="alasan_discounts" placeholder="Add your reason here" class="textarea_editor form-control border-radius-0" autofocus>{{ $order->alasan_discounts }}</textarea>
                                                                @error('alasan_discounts')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="discounts">Discounts</label>
                                                                <input type="number" min="1" name="discounts" placeholder="Amount" class="form-control" value="{{ $order->discounts }}" required>
                                                                @error('discounts')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="author" value="{{ Auth::User()->id }}">
                                                </Form>
                                                <div class="card-box-footer">
                                                    <div class="form-group">
                                                        @if ($order->discounts > 0)
                                                            <button type="submit" form="updateDiscount" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                        @else
                                                            <button type="submit" form="updateDiscount" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                        @endif
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Modal Remove Discount --------------------------------------------------------------------------------------------------------------- --}}
                                <div class="modal fade" id="remove-discounts-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="title"><i class="fa fa-trash-o" aria-hidden="true"></i> Remove Discounts</div>
                                                </div>
                                                <form id="remove-discount" action="/fremove-order-discounts/{{ $order->id }}"method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <b>Are you sure to remove the discount on this order?</b>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="author" value="{{ Auth::User()->id }}">
                                                    <input type="hidden" name="alasan_discounts" value="">
                                                    <input type="hidden" name="discounts" value="">
                                                </Form>
                                                <div class="card-box-footer">
                                                    <div class="form-group">
                                                        <button type="submit" form="remove-discount" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Yes</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Modal Archived Order --------------------------------------------------------------------------------------------------------------- --}}
                                <div class="modal fade" id="archive-order-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="title"><i class="icon-copy fa fa-archive" aria-hidden="true"></i> Archive orders</div>
                                                </div>
                                                <form id="arsipkan-order-{{ $order->id }}" action="/farchive-order/{{ $order->id }}"method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                    <div class="form-group row">
                                                        <label for="msg" class="col-sm-12 col-md-12 col-form-label">Why the order is Archived?</label>
                                                        <div class="col-sm-12 col-md-12">
                                                            <textarea id="msg" name="msg" placeholder="Add your reason here" class="textarea_editor form-control border-radius-0" autofocus required></textarea>
                                                            @error('msg')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    {{-- TRAC RECORD --}}
                                                    <input type="hidden" name="status_awal" value="{{ $order->status }}">
                                                    <input type="hidden" name="number_of_guests_awal" value="{{ $order->number_of_guests }}">
                                                    <input type="hidden" name="guest_detail_awal" value="{{ $order->guest_detail }}">
                                                    <input type="hidden" name="arrival_flight_awal" value="{{ $order->arrival_flight }}">
                                                    <input type="hidden" name="arrival_time_awal" value="{{ $order->arrival_time }}">
                                                    <input type="hidden" name="departure_flight_awal" value="{{ $order->departure_flight }}">
                                                    <input type="hidden" name="departure_time_awal" value="{{ $order->departure_time }}">
                                                    <input type="hidden" name="price_pax_awal" value="{{ $order->price_pax }}">
                                                    <input type="hidden" name="price_total_awal" value="{{ $order->price_total }}">
                                                    {{-- END TRAC RECORD --}}
                                                    <input type="hidden" name="admin" value="{{ Auth::User()->name }}">
                                                    <input type="hidden" name="author" value="{{ Auth::User()->id }}">
                                                </Form>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="arsipkan-order-{{ $order->id }}" class="btn btn-primary">Archive</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Modal Order not confirmed --------------------------------------------------------------------------------------------------------------- --}}
                                <div class="modal fade" id="reject-order-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="subtitle"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Reject orders</div>
                                                </div>
                                                <form id="rejected-order" action="/fupdate-order-rejected/{{ $order->id }}"method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                    <div class="form-group row">
                                                        <label for="msg" class="col-sm-12 col-md-12 col-form-label">Why the order is rejected? <span> *</span></label>
                                                        <div class="col-sm-12 col-md-12">
                                                            <textarea id="msg" name="msg" placeholder="Add your reason here" class="textarea_editor form-control border-radius-0" autofocus required></textarea>
                                                            @error('msg')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <div class="form-group">
                                                            {{-- ACTION LOG --}}
                                                            <input type="hidden" name="action_note" value="">
                                                            <input type="hidden" name="page" value="order-admin-detail">
                                                            <input type="hidden" name="initial_state" value="{{ $order->status }}">
                                                            <input type="hidden" name="service" value="Order">
                                                            {{-- END ACTION LOG --}}
                                                            {{-- TRAC RECORD --}}
                                                            <input type="hidden" name="status_awal" value="{{ $order->status }}">
                                                            <input type="hidden" name="number_of_guests_awal" value="{{ $order->number_of_guests }}">
                                                            <input type="hidden" name="guest_detail_awal" value="{{ $order->guest_detail }}">
                                                            <input type="hidden" name="arrival_flight_awal" value="{{ $order->arrival_flight }}">
                                                            <input type="hidden" name="arrival_time_awal" value="{{ $order->arrival_time }}">
                                                            <input type="hidden" name="departure_flight_awal" value="{{ $order->departure_flight }}">
                                                            <input type="hidden" name="departure_time_awal" value="{{ $order->departure_time }}">
                                                            <input type="hidden" name="price_pax_awal" value="{{ $order->price_pax }}">
                                                            <input type="hidden" name="price_total_awal" value="{{ $order->price_total }}">
                                                            {{-- END TRAC RECORD --}}
                                                            <input type="hidden" name="author" value="{{ Auth::User()->id }}">
                                                           
                                                        </div>
                                                    </div>
                                                </Form>
                                                <div class="card-box-footer">
                                                    <button form="rejected-order" type="submit" id="normal-reserve" class="btn btn-danger"><i class="fa fa-ban" aria-hidden="true"></i> Reject Order</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Modal Order Invalid --------------------------------------------------------------------------------------------------------------- --}}
                                <div class="modal fade" id="invalid-order-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="title"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Invalid orders</div>
                                                </div>
                                                <form id="invalid-order" action="/fupdate-order-invalid/{{ $order->id }}"method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                    <div class="form-group row">
                                                        <label for="msg" class="col-sm-12 col-md-12 col-form-label">Why the order is invalid?</label>
                                                        <div class="col-sm-12 col-md-12">
                                                            <textarea id="msg" name="msg" placeholder="Add your reason here" class="textarea_editor form-control border-radius-0" autofocus required></textarea>
                                                            @error('msg')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <div class="form-group">
                                                            {{-- TRAC RECORD --}}
                                                            <input type="hidden" name="status_awal" value="{{ $order->status }}">
                                                            <input type="hidden" name="number_of_guests_awal" value="{{ $order->number_of_guests }}">
                                                            <input type="hidden" name="guest_detail_awal" value="{{ $order->guest_detail }}">
                                                            <input type="hidden" name="arrival_flight_awal" value="{{ $order->arrival_flight }}">
                                                            <input type="hidden" name="arrival_time_awal" value="{{ $order->arrival_time }}">
                                                            <input type="hidden" name="departure_flight_awal" value="{{ $order->departure_flight }}">
                                                            <input type="hidden" name="departure_time_awal" value="{{ $order->departure_time }}">
                                                            <input type="hidden" name="price_pax_awal" value="{{ $order->price_pax }}">
                                                            <input type="hidden" name="price_total_awal" value="{{ $order->price_total }}">
                                                            {{-- END TRAC RECORD --}}
                                                            <input type="hidden" name="admin" value="{{ Auth::User()->name }}">
                                                            <input type="hidden" name="author" value="{{ Auth::User()->id }}">
                                                        </div>
                                                    </div>
                                                </Form>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="invalid-order" id="normal-reserve" class="btn btn-primary">Invalid</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 desktop">
                            <div class="row">
                                {{-- ATTENTION --}}
                                @include('partials.admin-order-attention',['device' => "desktop"])
                                {{-- STATUS --}}
                                @include('partials.admin-order-status-sidebar',['device' => "desktop"])
                                {{-- ORDER NOTE --}}
                                @include('partials.admin-order-note-sidebar',['device' => "desktop"])
                                {{-- DOKU --}}
                                @include('partials.admin-order-doku-report-sidebar',['device' => "desktop"])
                                {{-- RECEIPT --}}
                                @include('partials.admin-order-receipt-report-sidebar',['device' => "desktop"])
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
        @include('partials.loading-form', ['id' => 'factivate-order-{{ $order->id }}'])
    @endcan
@endsection

