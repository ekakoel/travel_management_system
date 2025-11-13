{{-- @include('layouts.loader') --}}
@php
    use Illuminate\Support\Carbon;
@endphp
@section('title', __('messages.Reservation Detail'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header d-print-none">
                    <div class="row">
                        <div class="col-md-7 col-sm-7">
                            <div class="title">
                                <i class="icon-copy fa fa-shopping-cart" aria-hidden="true"></i> Detail - Reservation
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/admin-panel">@lang('messages.Admin Panel')</a></li>
                                    <li class="breadcrumb-item"><a href="/reservation">@lang('messages.Reservation')</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $reservation->rsv_no }}</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-5 col-sm-5 text-right">
                            Status:
                            @if ($reservation->status == "On Progress")
                                <h4 style="color: green">{{ $reservation->status }}</h4>
                            @else
                                <h4 style="color: rgb(65, 65, 65)">{{ $reservation->status }}</h4>
                            @endif
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
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="product-wrap card-box mb-30 d-print">
                            <div class="product-detail-wrap">
                                <div class="row">
                                    <div class="col-6 col-md-6">
                                        <div class="order-bil text-left">
                                            <img src="/storage/logo/logo-color-bali-kami.png"alt="Bali Kami Tour & Travel">
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-6 text-right flex-end">
                                        <div class="label-title">RESERVATION</div>
                                    </div>
                                    <div class="col-md-12 text-right">
                                        <div class="label-date" style="width: 100%;">
                                            {{ dateFormat($reservation->created_at) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="business-name">{{ $business->name }}</div>
                                <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="page-subtitle d-print-none">Reservation 
                                            @if ($reservation->status != "Active")
                                                <span>
                                                    <a href="#" data-toggle="modal" data-target="#update-reservation-{{ $reservation->id }}"> 
                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Reservation" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                            @endif
                                        </div>
                                        {{-- Modal Update Reservation pickup --------------------------------------------------------------------------------------------------------------- --}}
                                        @if ($reservation->status != "Active")
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
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label>Start - Finish</label>
                                                                            <input readonly id="checkincout" name="checkincout" class="form-control @error('checkincout') is-invalid @enderror" type="text" placeholder="@lang('messages.Select date')" value="{{ date("m/d/y",strtotime($reservation->checkin))." - ". date("m/d/y",strtotime($reservation->checkout)) }}" required>
                                                                            @error('checkincout')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="pickup_name">Pick up Name</label>
                                                                            <select name="pickup_name" class="custom-select @error('pickup_name') is-invalid @enderror">
                                                                                @if ($reservation->pickup_name != "")
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
                                        <table class="table tb-list">
                                            <tbody>
                                                <tr>
                                                    <td class="htd-1">Reservation No.</td>
                                                    <td class="htd-2">{{  $reservation->rsv_no }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">Reservation Date</td>
                                                    <td class="htd-2">{{ dateFormat($reservation->created_at) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">Start & Finish</td>
                                                    <td class="htd-2">{{ date('d M Y', strtotime($reservation->checkin))." - ".date('d M Y', strtotime($reservation->checkout)) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">Pickup Name</td>
                                                    <td class="htd-2">
                                                        @if ($reservation->pickup_name != "")
                                                            @php
                                                                $gst_pname = $guests->where('id', $reservation->pickup_name)->first();
                                                            @endphp
                                                            @if (isset($gst_pname))
                                                                {{  $gst_pname->name }}
                                                            @else
                                                                <p class="form-notif">: <span> -</span></p>
                                                            @endif
                                                        @else
                                                            <p class="form-notif">: <span> -</span></p>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">Phone</td>
                                                    <td class="htd-2">
                                                        @if ($reservation->pickup_name != "")
                                                            @if (isset($gst_pname))
                                                                {{  $gst_pname->phone }}
                                                            @else
                                                                <p class="form-notif">: <span> -</span></p>
                                                            @endif
                                                            
                                                        @else
                                                            <p class="form-notif">: <span> -</span></p>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    {{-- FLIGHT --}}
                                    <div class="col-md-6">
                                        <div class="page-subtitle d-print-none">Flight
                                            @if ($reservation->status == "Pending")
                                                <span>
                                                    @if ($reservation->arrival_flight != "" or $reservation->arrival_time != "" or $reservation->departure_flight != "" or $reservation->departure_time != "")
                                                        <a href="#" data-toggle="modal" data-target="#update-flight-{{ $reservation->id }}"> 
                                                            <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Flight" aria-hidden="true"></i>
                                                        </a>
                                                    @else
                                                        <a href="#" data-toggle="modal" data-target="#update-flight-{{ $reservation->id }}"> 
                                                            <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add Flight" aria-hidden="true"></i>
                                                        </a>
                                                    @endif
                                                    
                                                </span>
                                            @endif
                                        </div>
                                        {{-- Modal Update Flight --------------------------------------------------------------------------------------------------------------- --}}
                                        @if ($reservation->status == "Pending")
                                            <div class="modal fade" id="update-flight-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                @if ($reservation->arrival_flight != "" or $reservation->arrival_time != "" or $reservation->departure_flight != "" or $reservation->departure_time != "")
                                                                    <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Flight</div>
                                                                @else
                                                                    <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Flight</div>
                                                                @endif
                                                            </div>
                                                            <form id="updateFlight-{{ $reservation->id }}" action="/fupdate-flight-{{ $reservation->id }}" method="post" enctype="multipart/form-data">
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
                                                                            <input readonly type="text" name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror" placeholder="Select arrival date and time" value="{{ $reservation->arrival_time }}">
                                                                            @error('arrival_time')
                                                                                <div class="alert-form">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="departure_flight">Departure Flight</label>
                                                                            <input type="text" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror" placeholder="Insert arrival flight" value="{{ $reservation->departure_flight }}">
                                                                            @error('departure_flight')
                                                                                <div class="alert-form">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="departure_time">Departure Time </label>
                                                                            <input readonly type="text" name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror" placeholder="Select departure date and time" value="{{ $reservation->departure_time }}">
                                                                            @error('departure_time')
                                                                                <div class="alert-form">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                @if ($reservation->arrival_flight != "" or $reservation->arrival_time != "" or $reservation->departure_flight != "" or $reservation->departure_time != "")
                                                                    <button form="updateFlight-{{ $reservation->id }}" type="submit" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                @else
                                                                    <button form="updateFlight-{{ $reservation->id }}" type="submit" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                @endif
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <table class="table tb-list">
                                            <tr>
                                                <td class="htd-1">Pickup Location</td>
                                                <td class="htd-2">{{ $reservation->arrival_flight }}</td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">Pickup Time</td>
                                                <td class="htd-2">{{ $reservation->arrival_time }}</td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">Drop Off Location</td>
                                                <td class="htd-2">{{ $reservation->departure_flight }}</td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">Drop Off Time</td>
                                                <td class="htd-2">{{ $reservation->departure_time }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                {{-- AGENT DETAIL ===================================================================================================================== --}}
                                
                                    <div class="row">
                                        {{-- AGENT --}}
                                        <div class="col-md-6">
                                            <div class="page-subtitle d-print-none">Agent</div>
                                            <table class="table tb-list">
                                                <tr>
                                                    <td class="htd-1">Name</td>
                                                    <td class="htd-2">
                                                        @if ($agent->name == "")
                                                            <p class="form-notif">: <span> Not available!</span></p>
                                                        @else
                                                            {{ $agent->name }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">Office</td>
                                                    <td class="htd-2">
                                                        @if ($agent->office == "")
                                                            <p class="form-notif">: <span> Not available!</span></p>
                                                        @else
                                                            {{ $agent->office }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">Phone</td>
                                                    <td class="htd-2">
                                                        @if ($agent->phone == "")
                                                            <p class="form-notif">: <span> Not available!</span></p>
                                                        @else
                                                            {{ $agent->phone }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">Email</td>
                                                    <td class="htd-2">
                                                        @if ($agent->email == "")
                                                            <p class="form-notif">: <span> Not available!</span></p>
                                                        @else
                                                            {{ $agent->email }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        {{-- GUEST --}}
                                        <div class="col-md-6">
                                            <div class="page-subtitle d-print-none">Guest
                                                @if ($reservation->status != "Active")
                                                    <span>
                                                        <a href="#" data-toggle="modal" data-target="#add-guests-{{ $reservation->id }}"> 
                                                            <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Guest" aria-hidden="true"></i>
                                                        </a>
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- Modal Add Guest --------------------------------------------------------------------------------------------------------------- --}}
                                            @if ($reservation->status != "Active")
                                                <div class="modal fade" id="add-guests-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Guest</div>
                                                                    </div>
                                                                    
                                                                    <form id="addGuest" action="/fadd-guest" method="post" enctype="multipart/form-data">
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
                                                                            <div class="col-sm-2">
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
                                                                            <div class="col-sm-4">
                                                                                <div class="form-group row">
                                                                                    <label for="date_of_birth" class="col-sm-12 col-md-12 col-form-label">Date of Birth</label>
                                                                                    <div class="col-sm-12">
                                                                                    <input readonly type="text" name="date_of_birth" class="form-control date-picker @error('date_of_birth') is-invalid @enderror" placeholder="Date of birth" value="{{ old('date_of_birth') }}">
                                                                                    </div>
                                                                                    @error('date_of_birth')
                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                    @enderror
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
                                            @if (count($guests) > 0)
                                                <table class="table tb-list">
                                                    @foreach ($guests as $number=>$guest)
                                                        <tr>
                                                            <td>
                                                                <div class="reservation-guest">
                                                                    @if ($guest->sex == "m")
                                                                        {{ ++$number.". " }}Mr. {{ $guest->name }} @if ($guest->date_of_birth != "")
                                                                            {{ " (".dateFormat($guest->date_of_birth).") (". Carbon::parse($guest->date_of_birth)->age.")" }}
                                                                        @endif
                                                                    @else
                                                                        @if (Carbon::parse($guest->date_of_birth)->age > 17)
                                                                            {{ ++$number.". " }}Mrs. {{ $guest->name }} @if ($guest->date_of_birth != "")
                                                                                {{ " (".dateFormat($guest->date_of_birth).") (". Carbon::parse($guest->date_of_birth)->age.")" }}
                                                                            @endif
                                                                        @else
                                                                            {{ ++$number.". " }}Miss. {{ $guest->name }} @if ($guest->date_of_birth != "")
                                                                                {{ " (".dateFormat($guest->date_of_birth).") (". Carbon::parse($guest->date_of_birth)->age.")" }}
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                   @if ($reservation->status != "Active")
                                                                        <span>
                                                                            <a href="#" data-toggle="modal" data-target="#edit-guest-{{ $guest->id }}"> 
                                                                                <button class="btn btn-update" data-toggle="tooltip" data-placement="left" title="Edit {{ $guest->name }}"><i class="icon-copy fa fa-pencil"></i></button>
                                                                            </a>
                                                                            <form action="/delete-guest/{{ $guest->id }}" method="post">
                                                                                @csrf
                                                                                @method('delete')
                                                                                <button class="btn btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Delete {{ $guest->name }}"><i class="icon-copy fa fa-trash"></i></button>
                                                                            </form>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                                {{-- Modal Edit Guest --------------------------------------------------------------------------------------------------------------- --}}
                                                @if ($reservation->status != "Active")
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
                                                                                    <label for="date_of_birth" class="col-sm-12 col-md-12 col-form-label">Date of Birth</label>
                                                                                    <div class="col-sm-12">
                                                                                    <input readonly type="text" name="date_of_birth" class="form-control date-picker @error('date_of_birth') is-invalid @enderror" placeholder="Date of birth" value="{{ $guest->date_of_birth }}">
                                                                                    </div>
                                                                                    @error('date_of_birth')
                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                    @enderror
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
                                            @endif
                                            {{-- Modal Add Guest --------------------------------------------------------------------------------------------------------------- --}}
                                            @if ($reservation->status != "Active")
                                                <div class="modal fade" id="add-guests-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Guest</div>
                                                                </div>
                                                                
                                                                <form id="addGuest" action="/fadd-guest" method="post" enctype="multipart/form-data">
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
                                                                        <div class="col-sm-2">
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
                                                                        <div class="col-sm-4">
                                                                            <div class="form-group row">
                                                                                <label for="date_of_birth" class="col-sm-12 col-md-12 col-form-label">Date of Birth</label>
                                                                                <div class="col-sm-12">
                                                                                <input readonly type="text" name="date_of_birth" class="form-control date-picker @error('date_of_birth') is-invalid @enderror" placeholder="Date of birth" value="{{ old('date_of_birth') }}">
                                                                                </div>
                                                                                @error('date_of_birth')
                                                                                    <div class="alert-form">{{ $message }}</div>
                                                                                @enderror
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
                                        </div>
                                    </div>
                                    {{-- GUIDE --}}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="page-subtitle d-print-none">Guide
                                                @if ($reservation->status != "Active" and $guide == "")
                                                    <span>
                                                        <a href="#" data-toggle="modal" data-target="#add-guide-{{ $reservation->id }}"> 
                                                            <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Guide" aria-hidden="true"></i>
                                                        </a>
                                                    </span>
                                                @endif
                                            </div>
                                            @if (isset($guide->id))
                                                <table class="table tb-list">
                                                    <tr>
                                                        <td>
                                                            <div class="reservation-guest">
                                                                @if ($guide->sex == "f")
                                                                    Mrs. {{ $guide->name }}
                                                                @else
                                                                    Mr. {{ $guide->name }}
                                                                @endif
                                                                <i>({{ $guide->language }})</i>
                                                                @if ($reservation->status != "Active")
                                                                    <span>
                                                                        <a href="#" data-toggle="modal" data-target="#edit-guide-{{ $reservation->id }}"> 
                                                                            <button class="btn btn-update" data-toggle="tooltip" data-placement="left" title="Edit {{ $guide->name }}"><i class="icon-copy fa fa-pencil"></i></button>
                                                                        </a>
                                                                        <form action="/fdelete-guide-order-{{ $reservation->id }}" method="post">
                                                                            @csrf
                                                                            @method('put')
                                                                            <button class="btn btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Delete {{ $guide->name }}"><i class="icon-copy fa fa-trash"></i></button>
                                                                        </form>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                {{-- Modal Edit Guide --------------------------------------------------------------------------------------------------------------- --}}
                                                @if ($reservation->status != "Active")
                                                    <div class="modal fade" id="edit-guide-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Edit Guide</div>
                                                                    </div>
                                                                    <form id="editGuideOrder" action="/fedit-guide-order-{{ $reservation->id }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('put')
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <div class="form-group row">
                                                                                    <label for="guide_id" class="col-sm-12 col-md-12 col-form-label">Select Guide</label>
                                                                                    <div class="col-sm-12">
                                                                                        <select name="guide_id" class="custom-select @error('guide_id') is-invalid @enderror" value="{{ old('guide_id') }}">
                                                                                            <option selected value="{{ $guide->id }}">{{ $guide->name }}</option>
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
                                                <table class="table tb-list">
                                                    <tr>
                                                        <td class="td-1">
                                                            <i style="color: red;">-</i>
                                                        </td>
                                                    </tr>
                                                </table>
                                            @endif
                                            {{-- Modal Add Guide --------------------------------------------------------------------------------------------------------------- --}}
                                            @if ($reservation->status != "Active")
                                                <div class="modal fade" id="add-guide-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Guide</div>
                                                                </div>
                                                                <form id="addGuideOrder" action="/fadd-guide-order-{{ $reservation->id }}" method="post" enctype="multipart/form-data">
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
                                            <div class="page-subtitle d-print-none">Driver
                                                @if ($driver == "")
                                                    <span>
                                                        <a href="#" data-toggle="modal" data-target="#add-driver-{{ $reservation->id }}"> 
                                                            <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Driver" aria-hidden="true"></i>
                                                        </a>
                                                    </span>
                                                @endif
                                            </div>
                                            @if (isset($driver))
                                                <table class="table tb-list">
                                                    <tr>
                                                        <td>
                                                            <div class="reservation-guest">
                                                                Mr. {{ $driver->name." (".$driver->phone.")" }}
                                                                @if ($reservation->status != "Active")
                                                                    <span>
                                                                        <a href="#" data-toggle="modal" data-target="#edit-driver-{{ $reservation->id }}"> 
                                                                            <button class="btn btn-update" data-toggle="tooltip" data-placement="left" title="Change {{ $driver->name }}"><i class="icon-copy fa fa-pencil"></i></button>
                                                                        </a>
                                                                        <form action="/fdelete-driver-order-{{ $reservation->id }}" method="post">
                                                                            @csrf
                                                                            @method('put')
                                                                            <button class="btn btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Delete {{ $driver->name }}"><i class="icon-copy fa fa-trash"></i></button>
                                                                        </form>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                {{-- Modal Edit Driver --------------------------------------------------------------------------------------------------------------- --}}
                                                @if ($reservation->status != "Active")
                                                    <div class="modal fade" id="edit-driver-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Change Driver</div>
                                                                    </div>
                                                                    <form id="editGuideOrder" action="/fedit-driver-order-{{ $reservation->id }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('put')
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <div class="form-group row">
                                                                                    <label for="driver_id" class="col-sm-12 col-md-12 col-form-label">Select Guide</label>
                                                                                    <div class="col-sm-12">
                                                                                        <select name="driver_id" class="custom-select @error('driver_id') is-invalid @enderror" value="{{ old('driver_id') }}">
                                                                                            <option selected value="{{ $reservation->guide_id }}">{{ $guide->name }}</option>
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
                                                <table class="table tb-list">
                                                    <tr>
                                                        <td class="td-1">
                                                            <i style="color: red;">-</i>
                                                        </td>
                                                    </tr>
                                                </table>
                                            @endif
                                            {{-- Modal Add Driver --------------------------------------------------------------------------------------------------------------- --}}
                                            @if ($reservation->status != "Active")
                                                <div class="modal fade" id="add-driver-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Driver</div>
                                                                </div>
                                                                <form id="addDriverOrder" action="/fadd-driver-order-{{ $reservation->id }}" method="post" enctype="multipart/form-data">
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
                                    </div>
                                    {{-- Accomodation ===================================================================================================================== --}}
                                    <div class="page-subtitle d-print-none">Accomodation
                                        @if ($reservation->status != "Active")
                                            <span>
                                                <a href="/add-rsv-order-{{ $reservation->id }}"><i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Order" aria-hidden="true"></i></a>
                                            </span>
                                        @endif
                                    </div>
                                    @if (count($order_accomodation)>0)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="data-table table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 10%">No</th>
                                                            <th style="width: 80%">Detail</th>
                                                            @if ($reservation->status != "Active")
                                                                <th style="width: 10%">Action</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($order_accomodation as $no_acc=>$order)
                                                            @php
                                                                $extra_bed = json_decode($order->extra_bed);
                                                                $c_exb = count($extra_bed);
                                                                $ebc = 0;
                                                                for ($i_exb=0; $i_exb < $c_exb; $i_exb++) { 
                                                                    if ($extra_bed[$i_exb] == "Yes") {
                                                                            $ebc++;
                                                                    }
                                                                }
                                                                $htl = $hotels->where('name',$order->servicename)->first();
                                                            @endphp
                                                            
                                                            <tr>
                                                                <td>
                                                                    {{ ++$no_acc }}
                                                                </td>
                                                                <td>
                                                                    {{ " (".$order->orderno.") ".date('d M y',strtotime($order->checkin))." - ".date('d M y',strtotime($order->checkout)).", ".$order->servicename.", ".$order->subservice." (".$order->number_of_room." unit)" }}
                                                                    @if ($order->extra_bed != null)
                                                                        {{ "+ ".$ebc." Extra Bed" }}
                                                                    @endif
                                                                </td>
                                                                @if ($reservation->status != "Active")
                                                                    <td class="text-center">
                                                                        <div class="table-action">
                                                                            <a href="#" data-toggle="modal" data-target="#detail-order-{{ $order->id }}"> 
                                                                                <button class="btn-view"><i class="icon-copy fa fa-eye" aria-hidden="true"></i></button>
                                                                            </a>
                                                                            <form action="/fupdate-accommodation/{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                                                @csrf
                                                                                @method('put')
                                                                                <input type="hidden" name="rsv_id" value="">
                                                                                <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Remove Order"><i class="icon-copy fa fa-remove" aria-hidden="true"></i></button>
                                                                            </form>
                                                                        </div>
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                            @php
                                                                $nor = $order->number_of_room;
                                                                $nogr = json_decode($order->number_of_guests_room);
                                                                $guest_detail = json_decode($order->guest_detail);
                                                                $special_day = json_decode($order->special_day);
                                                                $special_date = json_decode($order->special_date);
                                                                $extra_bed = json_decode($order->extra_bed);
                                                                $extra_bed_id = json_decode($order->extra_bed_id);
                                                                $extra_bed_price = json_decode($order->extra_bed_price);
                                                                $r=1;
                                                                $room_price_normal = $order->normal_price * $order->number_of_room;
                                                                $totalextrabed = array_sum($extra_bed_price);
                                                                $tp_room_and_suite = $room_price_normal + $totalextrabed;
                                                                if ($nor != "" or $order->number_of_guests < 1) {
                                                                    $optional_service_total_price = 0;
                                                                }
                                                            @endphp
                                                            {{-- Modal Accomodation ===================================================================================================================== --}}
                                                            @if ($reservation->status != "Active")
                                                                <div class="modal fade" id="detail-order-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content text-left">
                                                                            <div class="card-box">
                                                                                <div class="card-box-title">
                                                                                    <div class="title"><span><i class="fa fa-eye"></i></span>{{ $order->orderno }}</div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-6 col-md-6">
                                                                                        <div class="order-bil text-left">
                                                                                            <img src="{{ asset(config('app.logo_dark')) }}" alt="{{ config('app.alt_logo') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-6 col-md-6">
                                                                                        <div class="label-title">@lang('messages.Order')</div>
                                                                                    </div>
                                                                                    <div class="col-md-12 text-right">
                                                                                        <div class="label-date float-right" style="width: 100%">
                                                                                            {{ dateFormat($now) }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="business-name">{{ $business->name }}</div>
                                                                                <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                                                                <hr class="form-hr">
                                                                                <div class="row">
                                                                                    <div class="col-4 col-sm-4 col-xl-3">
                                                                                        <div class="page-list"> @lang('messages.Order No') </div>
                                                                                        <div class="page-list"> @lang('messages.Order Date') </div>
                                                                                        <div class="page-list"> @lang('messages.Service') </div>
                                                                                        <div class="page-list"> @lang('messages.Region') </div>
                                                                                    </div>
                                                                                    <div class="col-8 col-sm-4 col-xl-6">
                                                                                        <div class="page-list-value">
                                                                                            <b>{{ $order->orderno }}</b>
                                                                                        </div>
                                                                                        <div class="page-list-value">
                                                                                            {{ dateFormat($now) }}
                                                                                        </div>
                                                                                        @if ($order->service == "Hotel")
                                                                                            <div class="page-list-value">: @lang('messages.Hotel')</div>
                                                                                        @elseif ($order->service == "Hotel Promo")
                                                                                            <div class="page-list-value">: @lang('messages.Hotel Promo')o</div>
                                                                                        @elseif ($order->service == "Hotel Package")
                                                                                            <div class="page-list-value">: @lang('messages.Hotel Package')</div>
                                                                                        @endif
                                                                                        
                                                                                        <div class="page-list-value">: {{ $order->location }}</div>
                                                                                    </div>
                                                                                    <div class="col-12 col-sm-4 col-xl-3 text-center">
                                                                                        <p> @lang('messages.Status'): </p>
                                                                                        @if ($order->status == "Active")
                                                                                            <h4 style="color: green">@lang('messages.'.$order->status)</h4>
                                                                                        @elseif ($order->status == "Pending")
                                                                                            <h4 style="color: #dd9e00">@lang('messages.'.$order->status)</h4>
                                                                                        @elseif ($order->status == "Invalid")
                                                                                            <h4 style="color: blue">@lang('messages.'.$order->status)</h4>
                                                                                        @elseif ($order->status == "Rejected")
                                                                                            <h4 style="color: red">@lang('messages.'.$order->status)</h4>
                                                                                        @else
                                                                                            <h4 style="color: #5b5b5b">@lang('messages.'.$order->status)</h4>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                                <hr class="form-hr">
                                                                                <div class="page-subtitle">@lang('messages.Hotel Detail')</div>
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <div class="row">
                                                                                            <div class="col-5 col-sm-5">
                                                                                                @if ($order->service == "Hotel")
                                                                                                    <div class="page-list">
                                                                                                        @lang('messages.Hotel Name')
                                                                                                    </div>
                                                                                                @elseif ($order->service == "Hotel Promo")
                                                                                                    <div class="page-list">
                                                                                                        @lang('messages.Promo')
                                                                                                    </div>
                                                                                                    <div class="page-list">
                                                                                                        @lang('messages.Hotel Name')
                                                                                                    </div>
                                                                                                @elseif ($order->service == "Hotel Package")
                                                                                                    <div class="page-list">
                                                                                                        @lang('messages.Package')
                                                                                                    </div>
                                                                                                    <div class="page-list">
                                                                                                        @lang('messages.Hotel Name')
                                                                                                    </div>
                                                                                                @endif
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Room')
                                                                                                </div>
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Capacity')
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-7 col-sm-7">
                                                                                                @if ($order->service == "Hotel")
                                                                                                    <div class="page-list-value">
                                                                                                        {{ $order->servicename }}
                                                                                                    </div>
                                                                                                @elseif ($order->service == "Hotel Promo")
                                                                                                    <div class="page-list-value">
                                                                                                        {{ $order->promo_name }}
                                                                                                    </div>
                                                                                                    <div class="page-list-value">
                                                                                                        {{ $order->servicename }}
                                                                                                    </div>
                                                                                                @elseif ($order->service == "Hotel Package")
                                                                                                    <div class="page-list-value">
                                                                                                        {{ $order->package_name }}
                                                                                                    </div>
                                                                                                    <div class="page-list-value">
                                                                                                        {{ $order->servicename }}
                                                                                                    </div>
                                                                                                @endif
                                                                                                <div class="page-list-value">
                                                                                                    {{ $order->subservice }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ $order->capacity." " }}@lang('messages.Guests')
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="row">
                                                                                            <div class="col-5 col-sm-5">
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Duration')
                                                                                                </div>
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Check In')
                                                                                                </div>
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Check Out')
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-7 col-sm-7">
                                                                                                <div class="page-list-value">
                                                                                                    {{ $order->duration." " }}@lang('messages.Nights')
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ dateFormat($order->checkin) }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ dateFormat($order->checkout) }}
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <div class="page-note">
                                                                                            @if ($order->service == "Hotel")
                                                                                                @if ($order->include != "")
                                                                                                    <b>@lang('messages.Include') :</b> <br>
                                                                                                    {!! $order->include !!}
                                                                                                    <hr class="form-hr">
                                                                                                @endif
                                                                                                @if ($order->additional_info != "")
                                                                                                    <b>@lang('messages.Additional Information') :</b> <br>
                                                                                                    {!! $order->additional_info !!}
                                                                                                @endif
                                                                                            @elseif ($order->service == "Hotel Promo")
                                                                                                @if ($order->include != "")
                                                                                                    <b>@lang('messages.Include') :</b> <br>
                                                                                                    {!! $order->include !!}
                                                                                                    <hr class="form-hr">
                                                                                                @endif
                                                                                                @if ($order->benefits != "")
                                                                                                    <b>@lang('messages.Benefit') :</b> <br>
                                                                                                    {!! $order->benefits !!}
                                                                                                    <hr class="form-hr">
                                                                                                @endif
                                                                                                @if ($order->additional_info != "")
                                                                                                    <b>@lang('messages.Additional Information') :</b> <br>
                                                                                                    {!! $order->additional_info !!}
                                                                                                @endif
                                                                                            @elseif ($order->service == "Hotel Package")
                                                                                                @if ($order->include != "")
                                                                                                    <b>@lang('messages.Include') :</b> <br>
                                                                                                    {!! $order->include !!}
                                                                                                    <hr class="form-hr">
                                                                                                @endif
                                                                                                @if ($order->benefits != "")
                                                                                                    <b>@lang('messages.Benefit') :</b> <br>
                                                                                                    {!! $order->benefits !!}
                                                                                                    <hr class="form-hr">
                                                                                                @endif
                                                                                                @if ($order->additional_info != "")
                                                                                                    <b>@lang('messages.Additional Information') :</b> <br>
                                                                                                    {!! $order->additional_info !!}
                                                                                                @endif
                                                                                            @endif
                                                                                            
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                @if ($order->cancellation_policy != "")
                                                                                    <div class="page-subtitle">@lang('messages.Cancellation Policy')</div>
                                                                                    <div class="row">
                                                                                        <div class="col-md-12">
                                                                                            <div class="cancelation-policy">
                                                                                                {!! $order->cancellation_policy !!}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif             
                                                                                @if ($order->request_quotation == "Yes")
                                                                                    <div class="col-md-12">
                                                                                        <div class="checkbox">
                                                                                            <p style="color:blue;" class="m-t-8 m-b-18">
                                                                                                <i style="color:blue;" class="icon-copy fa fa-check-square" aria-hidden="true"></i>@lang('messages.You are requesting a quote for bookings of more than 8 rooms in this order. We will contact you as soon as possible to confirm your order. Thank You') 
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-12 text-right">
                                                                                        <div class="form-group">
                                                                                            <a href="/orders">
                                                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                @else
                                                                                    @if ($order->number_of_room == "" or $order->number_of_guests_room == "" or $order->guest_detail == "" )
                                                                                        <div class="page-subtitle" style=" background-color: #ffe3e3; border: 2px dotted red;">@lang('messages.Suites and Villas')</div>
                                                                                    @else
                                                                                        <div class="page-subtitle">@lang('messages.Suites and Villas') </div>
                                                                                    @endif
                                                                                    <div class="row">
                                                                                        @if ($order->number_of_room == "" or $order->number_of_guests_room == "" or $order->guest_detail == "" )
                                                                                            <div class="col-sm-12 m-b-18">
                                                                                                <div class="room-container ">
                                                                                                    <p style="color:brown;"><i>@lang('messages.You have not selected a room on this booking!')</i></p>
                                                                                                </div>
                                                                                            </div>
                                                                                        @else
                                                                                            <div class="col-md-12 m-b-8">
                                                                                                <div class="tb-container">
                                                                                                    <div class="tb-head">
                                                                                                        @lang('messages.Room')
                                                                                                    </div>
                                                                                            
                                                                                                    <div class="tb-head">
                                                                                                        @lang('messages.Number of Guest')
                                                                                                    </div>
                                                                                            
                                                                                                    <div class="tb-head">
                                                                                                        @lang('messages.Guest Name')
                                                                                                    </div>
                                                                                                
                                                                                                    <div class="tb-head">
                                                                                                        @lang('messages.Price')
                                                                                                    </div>
                                                                                            
                                                                                                    <div class="tb-head">
                                                                                                        @lang('messages.Extra Bed')
                                                                                                    </div>
                                                                                                </div>
                                                                                                @for ($i = 0; $i < $nor; $i++)
                                                                                                    <div class="tb-container">
                                                                                                        <div class="tb-body">
                                                                                                            {{ $r }}
                                                                                                        </div>
                                                                                                        <div class="tb-body">
                                                                                                            {{ $nogr[$i]." " }}@lang('messages.Guests')
                                                                                                        </div>
                                                                                                        <div class="tb-body">
                                                                                                            {{ $guest_detail[$i] }}
                                                                                                        </div>
                                                                                                        <div class="tb-body">
                                                                                                            {{ currencyFormatUsd($order->normal_price) }}
                                                                                                        </div>
                                                                                                        <div class="tb-body">
                                                                                                            @if ($extra_bed[$i] == "Yes")
                                                                                                                @if ($extra_bed_price[$i] != "")
                                                                                                                @php
                                                                                                                    $extrabed = $extra_beds->where('id',$extra_bed_id[$i])->first();
                                                                                                                @endphp
                                                                                                                    <div class="table-service-name">{{ $extrabed->name." (".$extrabed->type.") $".number_format($extra_bed_price[$i])}}</div>
                                                                                                                @else
                                                                                                                    @php
                                                                                                                        $order_status = "Invalid";
                                                                                                                    @endphp
                                                                                                                    <p class="text-danger"><i>@lang('messages.Invalid') </i> <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.This room is occupied by more than 2 guests, and requires an extra bed, please edit it first to be able to submit an order')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></p>
                                                                                                                @endif
                                                                                                            @else
                                                                                                                <div class="table-service-name">-</div>
                                                                                                            @endif
                                                                                                        </div>
                                                                                                        @php
                                                                                                            $r++;
                                                    
                                                                                                        @endphp
                                                                                                    </div>
                                                                                                @endfor
                                                                                            </div>
                                                                                            <div class="col-md-12">
                                                                                                <div class="box-price-kicked m-b-8">
                                                                                                    <div class="row">
                                                                                                        <div class="col-6 col-md-6">
                                                                                                            <div class="usd-rate-kicked">@lang('messages.Suites and Villas')</div>
                                                                                                            <div class="usd-rate-kicked">@lang('messages.Extra Bed')</div>
                                                                                                            <hr class="form-hr">
                                                                                                            <div class="price-name">Room And Suite</div>
                                                                                                        </div>
                                                                                                        <div class="col-6 col-md-6 text-right">
                                                                                                            <div class="usd-rate-kicked">{{ currencyFormatUsd(($order->normal_price * $order->number_of_room)) }}</div>
                                                                                                            <div class="usd-rate-kicked">{{ currencyFormatUsd(($totalextrabed)) }}</div>
                                                                                                            <hr class="form-hr">
                                                                                                            <div class="usd-rate">{{ currencyFormatUsd(($order->normal_price * $order->number_of_room)+$totalextrabed) }}</div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                    @if ($order->number_of_guests > 0)
                                                                                        @if ($order->optional_price > 0)
                                                                                            <div id="optional_service" class="page-subtitle">@lang('messages.Additional Charge')</div>
                                                                                            <div class="row">
                                                                                                @php
                                                                                                    $optional_rate_orders = $optionalrateorders->where('order_id', $order->id)->first();
                                                                                                    $optional_rate_orders_id = json_decode($optional_rate_orders->optional_rate_id);
                                                                                                    $optional_rate_orders_nog = json_decode($optional_rate_orders->number_of_guest);
                                                                                                    $optional_rate_orders_sd = json_decode($optional_rate_orders->service_date);
                                                                                                    $optional_rate_orders_pp = json_decode($optional_rate_orders->price_pax);
                                                                                                    $optional_rate_orders_pt = json_decode($optional_rate_orders->price_total);
                                                                                                    if ($optional_rate_orders_nog != "") {
                                                                                                        $xsor = count($optional_rate_orders_nog);
                                                                                                    }else{
                                                                                                        $xsor = 0;
                                                                                                        $optional_service_total_price = 0;
                                                                                                    }
                                                                                                @endphp
                                                                                                <div class="col-md-12 m-b-8">
                                                                                                    <div class="tb-container">
                                                                                                        <div class="tb-head">
                                                                                                            @lang('messages.Date')
                                                                                                        </div>
                                                                                                
                                                                                                        <div class="tb-head">
                                                                                                            @lang('messages.Number of Guest')
                                                                                                        </div>
                                                                                                
                                                                                                        <div class="tb-head">
                                                                                                            @lang('messages.Service')
                                                                                                        </div>
                                                                                                    
                                                                                                        <div class="tb-head">
                                                                                                            @lang('messages.Price')
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    @for ($i = 0; $i < $xsor; $i++)
                                                                                                        <div class="tb-container">
                                                                                                            @php
                                                                                                                $optional_service_name = $optionalrates->where('id',$optional_rate_orders_id[$i])->first();
                                                                                                            @endphp
                                                                                                            <div class="tb-body">
                                                                                                                {{ dateFormat($optional_rate_orders_sd[$i]) }}
                                                                                                            </div>
                                                                                                            <div class="tb-body">
                                                                                                                {{ $optional_rate_orders_nog[$i]." Guests" }}
                                                                                                            </div>
                                                                                                            <div class="tb-body">
                                                                                                                {{ $optional_service_name->name }}
                                                                                                            </div>
                                                                                                            <div class="tb-body">
                                                                                                                {{ currencyFormatUsd($optional_rate_orders_pt[$i]) }}
                                                                                                            </div>
                                                                                                        </div>
                                                                                                            @php
                                                                                                                $r++;
                                                                                                                $optional_service_total_price = array_sum($optional_rate_orders_pt);
                                                                                                            @endphp
                                                                                                    @endfor
                                                                                                </div>
                                                                                                <div class="col-sm-12">
                                                                                                    <div class="box-price-kicked m-b-8">
                                                                                                        <div class="row">
                                                                                                            <div class="col-6 col-md-6">
                                                                                                                <div class="price-name">@lang('messages.Additional Charge')</div>
                                                                                                            </div>
                                                                                                            <div class="col-6 col-md-6 text-right">
                                                                                                                <div class="usd-rate">{{ currencyFormatUsd(($optional_service_total_price)) }}</div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endif
                                                                                    @endif
                                                                                    @if (isset($order->arrival_flight)  or isset($order->arrival_time) or isset($order->departure_flight) or isset($order->departure_time))
                                                                                        <div class="page-subtitle">@lang('messages.Flight Detail')</div>
                                                                                        <div class="row">
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group row">
                                                                                                    <label for="arrival_flight" class="col-sm-12 col-md-12 col-form-label">@lang('messages.Arrival Flight')</label>
                                                                                                    <div class="col-sm-12 col-md-12">
                                                                                                        <input type="text" readonly  class="form-control @error('arrival_flight') is-invalid @enderror" placeholder="@lang('messages.Arrival Flight')" value="{{ $order->arrival_flight }}">
                                                                                                        @error('arrival_flight')
                                                                                                            <div
                                                                                                                class="alert alert-danger">
                                                                                                                {{ $message }}
                                                                                                            </div>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group row">
                                                                                                    <label for="arrival_time" class="col-sm-12 col-md-12 col-form-label">@lang('messages.Arrival Date and Time')</label>
                                                                                                    <div class="col-sm-12 col-md-12">
                                                                                                        <input readonly type="text"  class="form-control  @error('arrival_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ $order->arrival_time }}">
                                                                                                        @error('arrival_time')
                                                                                                            <div class="alert alert-danger">
                                                                                                                {{ $message }}
                                                                                                            </div>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group row">
                                                                                                    <label for="departure_flight" class="col-sm-12 col-md-12 col-form-label">@lang('messages.Departure Flight')</label>
                                                                                                    <div class="col-sm-12 col-md-12">
                                                                                                        <input type="text" readonly class="form-control @error('departure_flight') is-invalid @enderror" placeholder="@lang('messages.Departure Flight')" value="{{ $order->departure_flight }}">
                                                                                                        @error('departure_flight')
                                                                                                            <div class="alert alert-danger">
                                                                                                                {{ $message }}
                                                                                                            </div>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group row">
                                                                                                    <label for="departure_time" class="col-sm-12 col-md-12 col-form-label"> @lang('messages.Departure Date and Time')</label>
                                                                                                    <div class="col-sm-12 col-md-12">
                                                                                                        <input readonly type="text"  class="form-control  @error('departure_time') is-invalid @enderror" placeholder="@lang('messages.Select Date and Time')" value="{{ $order->departure_time }}">
                                                                                                        @error('departure_time')
                                                                                                            <div class="alert alert-danger">
                                                                                                                {{ $message }}
                                                                                                            </div>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            @if ($order->note != "")
                                                                                                <div class="col-md-12">
                                                                                                    <div class="form-group row">
                                                                                                        <label for="note" class="col-sm-12 col-md-12 col-form-label">@lang('messages.Note')</label>
                                                                                                        <div class="col-sm-12 col-md-12">
                                                                                                            <textarea id="note" readonly placeholder="Optional" class="textarea_editor form-control border-radius-0">{{ $order->note }}</textarea>
                                                                                                            @error('note')
                                                                                                                <div class="alert alert-danger">
                                                                                                                    {{ $message }}
                                                                                                                </div>
                                                                                                            @enderror
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    @endif
                                                                                    <div class="page-subtitle">@lang('messages.Price')</div>
                                                                                    <div class="row">
                                                                                        <div class="col-md-12 ">
                                                                                            <div class="box-price-kicked">
                                                                                                <div class="row">
                                                                                                    <div class="col-8 col-md-6">
                                                                                                        <div class="usd-rate-kicked">@lang('messages.Extra bed, Suites and Villas') :</div>
                                                                                                        @if ($order->optional_price > 0)
                                                                                                            <div class="usd-rate-kicked">@lang('messages.Additional Charge') :</div>
                                                                                                        @endif
                                                                                                        @if ($order->kick_back > 0 or $order->discounts > 0)
                                                                                                            <hr class="form-hr">
                                                                                                        @endif
                                                                                                        @if ($order->kick_back > 0)
                                                                                                            <div class="kick-back">@lang('messages.Kick Back') :</div>
                                                                                                        @endif
                                                                                                        @if ($order->discounts > 0)
                                                                                                            <div class="kick-back">@lang('messages.Discounts') :</div>
                                                                                                        @endif
                                                                                                        <hr class="form-hr">
                                                                                                        <div class="price-name">@lang('messages.Total Price')</div>
                                                                                                    </div>
                                                                                                    <div class="col-4 col-md-6 text-right">
                                                                                                        @if ($order->number_of_room == "" or $order->number_of_guests_room == "" or $order->guest_detail == "" or $order->guest_detail == ""  )
                                                                                                            <div class="usd-rate" style="color: red;">@lang('messages.Your order has invalid data')</div>
                                                                                                        @else
                                                                                                            <div class="usd-rate-kicked">{{ currencyFormatUsd($tp_room_and_suite) }}</div>
                                                                                                            @if ($order->optional_price > 0)
                                                                                                                <div class="usd-rate-kicked">{{ "$ ".$optional_service_total_price }}</div>
                                                                                                            @endif
                                                                                                            @if ($order->kick_back > 0 or $order->discounts > 0)
                                                                                                                <hr class="form-hr">
                                                                                                            @endif
                                                                                                            @if ($order->kick_back > 0)
                                                                                                                <div class="kick-back">{{ "- $ ".$order->kick_back }}</div>
                                                                                                            @endif
                                                                                                            @if ($order->discounts > 0)
                                                                                                                <div class="kick-back">{{ "- $ ".$order->discounts }}</div>
                                                                                                            @endif
                                                                                                            <hr class="form-hr">
                                                                                                            <div class="usd-rate">{{ currencyFormatUsd($order->final_price) }}</div>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-12 ">
                                                                                            <div class="notif-modal text-left">
                                                                                                @if ($order->status == "Pending")
                                                                                                    @lang('messages.We have received your order, we will contact you as soon as possible to validate the order!')
                                                                                                @elseif ($order->status == "Rejected")
                                                                                                    {{ $order->msg }}
                                                                                                @elseif ($order->status == "Active")
                                                                                                    @lang('messages.Your order has been verified, and everything looks good')
                                                                                                @elseif ($order->status == "Invalid")
                                                                                                    {{ $order->msg }}
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="card-box-footer">
                                                                                        <a href="/orders">
                                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                                        </a>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                
                                    {{-- Tour & Activity ===================================================================================================================== --}}
                                    <div class="page-subtitle d-print-none">Tour and Activity
                                        @if ($reservation->status != "Active")
                                            <span>
                                                <a href="/add-rsv-activity-tour-{{ $reservation->id }}"><i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Order" aria-hidden="true"></i></a>
                                            </span>
                                        @endif
                                    </div>
                                    @if (count($activitytours)>0)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="data-table table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 10%">No</th>
                                                            <th style="width: 80%">Detail</th>
                                                            @if ($reservation->status != "Active")
                                                                <th style="width: 10%">Action</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($activitytours as $no_act=>$activitytour)
                                                            <tr>
                                                                <td>{{ ++$no_act }}</td>
                                                                <td>
                                                                    @if ($activitytour->service == "Tour Package")
                                                                    <p>{{ date('d M Y',strtotime($activitytour->checkin))." - ".date('d M Y',strtotime($activitytour->checkout)) }}</p>
                                                                    @else
                                                                    <p>{{ date('d M Y',strtotime($activitytour->checkin))." - ".date('d M Y',strtotime($activitytour->checkin)) }}</p>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <p>{{ $activitytour->service }}</p>
                                                                </td>
                                                                <td>
                                                                    <p>{{ $activitytour->subservice }}</p>
                                                                </td>
                                                                <td>
                                                                    <p>{{ $activitytour->location }}</p>
                                                                </td>
                                                                <td>
                                                                    <p>{{ $activitytour->number_of_guests }}</p>
                                                                </td>
                                                                @if ($reservation->status != "Active")
                                                                    <td>
                                                                        <div class="reservation-guest">
                                                                            <span>
                                                                                <a href="#" data-toggle="modal" data-target="#detail-activitytour-{{ $activitytour->id }}"> 
                                                                                    <button class="btn-view"><i class="icon-copy fa fa-eye" aria-hidden="true"></i></button>
                                                                                </a>
                                                                                <form action="/fupdate-activity-tour/{{ $activitytour->id }}" method="post" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('put')
                                                                                    <input type="hidden" name="rsv_id" value="">
                                                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Remove Order"><i class="icon-copy fa fa-remove" aria-hidden="true"></i></button>
                                                                                </form>
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                            {{-- Modal Activity Tour ===================================================================================================================== --}}
                                                            <div class="modal fade" id="detail-activitytour-{{ $activitytour->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content text-left">
                                                                        <div class="product-detail-wrap">
                                                                            <div class="row">
                                                                                <div class="col-6 col-md-6">
                                                                                    <div class="order-bil text-left">
                                                                                        <img src="{{ asset(config('app.logo_dark')) }}" alt="{{ config('app.alt_logo') }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-6 col-md-6">
                                                                                    <div class="label-title">@lang('messages.Order')</div>
                                                                                </div>
                                                                                <div class="col-md-12 text-right">
                                                                                    <div class="label-date float-right" style="width: 100%">
                                                                                        {{ dateFormat($now) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="business-name">{{ $business->name }}</div>
                                                                            <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                                                            <hr class="form-hr">
                                                                            <div class="row">
                                                                                <div class="col-4 col-sm-4 col-xl-3">
                                                                                    <div class="page-list"> @lang('messages.Order No') </div>
                                                                                    <div class="page-list"> @lang('messages.Order Date') </div>
                                                                                    <div class="page-list"> @lang('messages.Service') </div>
                                                                                    <div class="page-list"> @lang('messages.Location') </div>
                                                                                </div>
                                                                                <div class="col-8 col-sm-4 col-xl-6">
                                                                                    <div class="page-list-value">
                                                                                        <b>{{ $activitytour->orderno }}</b>
                                                                                    </div>
                                                                                    <div class="page-list-value">
                                                                                        {{ dateFormat($now) }}
                                                                                    </div>
                                                                                    @if ($activitytour->service == "Activity")
                                                                                        <div class="page-list-value">: @lang('messages.Activity')</div>
                                                                                    @elseif ($activitytour->service == "Tour Package")
                                                                                        <div class="page-list-value">: @lang('messages.Tour Package')</div>
                                                                                    @endif
                                                                                    
                                                                                    <div class="page-list-value">: {{ $activitytour->location }}</div>
                                                                                </div>
                                                                                <div class="col-12 col-sm-4 col-xl-3 text-center">
                                                                                    <p> @lang('messages.Status'): </p>
                                                                                    @if ($activitytour->status == "Active")
                                                                                        <h4 style="color: green">@lang('messages.'.$activitytour->status)</h4>
                                                                                    @elseif ($activitytour->status == "Pending")
                                                                                        <h4 style="color: #dd9e00">@lang('messages.'.$activitytour->status)</h4>
                                                                                    @elseif ($activitytour->status == "Invalid")
                                                                                        <h4 style="color: blue">@lang('messages.'.$activitytour->status)</h4>
                                                                                    @elseif ($activitytour->status == "Rejected")
                                                                                        <h4 style="color: red">@lang('messages.'.$activitytour->status)</h4>
                                                                                    @else
                                                                                        <h4 style="color: #5b5b5b">@lang('messages.'.$activitytour->status)</h4>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                            <hr class="form-hr">
                                                                            @if ($activitytour->service == "Activity")
                                                                                <div class="page-subtitle">@lang('messages.Activity')</div>
                                                                            @else
                                                                                <div class="page-subtitle">@lang('messages.Tour Package')</div>
                                                                            @endif
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-5 col-sm-5">
                                                                                            @if ($activitytour->service == "Activity")
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Activity')
                                                                                                </div>
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Number of Guest')
                                                                                                </div>
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Guest Name')
                                                                                                </div>
                                                                                            @elseif ($activitytour->service == "Tour Package")
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Name')
                                                                                                </div>
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Number of Guest')
                                                                                                </div>
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Guest Name')
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                        <div class="col-7 col-sm-7">
                                                                                            @if ($activitytour->service == "Activity")
                                                                                                <div class="page-list-value">
                                                                                                    {{ $activitytour->servicename }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ $activitytour->number_of_guests }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ $activitytour->guest_detail }}
                                                                                                </div>
                                                                                            @elseif ($activitytour->service == "Tour Package")
                                                                                                <div class="page-list-value">
                                                                                                    {{ $activitytour->servicename }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ $activitytour->number_of_guests }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ $activitytour->guest_detail }}
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-5 col-sm-5">
                                                                                            <div class="page-list">
                                                                                                @lang('messages.Duration')
                                                                                            </div>
                                                                                            <div class="page-list">
                                                                                                @lang('messages.Start Date')
                                                                                            </div>
                                                                                            <div class="page-list">
                                                                                                @lang('messages.End Date')
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-7 col-sm-7">
                                                                                            @if ($activitytour->service == "Activity")
                                                                                                <div class="page-list-value">
                                                                                                    {{ $activitytour->duration." " }}@lang('messages.Hours')
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ dateFormat($activitytour->checkin) }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ dateFormat($activitytour->checkin) }}
                                                                                                </div>
                                                                                            @elseif ($activitytour->service == "Tour Package")
                                                                                                <div class="page-list-value">
                                                                                                    {{ $activitytour->duration." " }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ dateFormat($activitytour->checkin) }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ dateFormat($activitytour->checkout) }}
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                @if ($activitytour->include != "" or $activitytour->itinerary != "" or $activitytour->additional_info)
                                                                                    <div class="col-md-12">
                                                                                        <div class="page-note">
                                                                                            @if ($activitytour->include != "")
                                                                                                <b>@lang('messages.Include') :</b> <br>
                                                                                                {!! $activitytour->include !!}
                                                                                                <hr class="form-hr">
                                                                                            @endif
                                                                                            @if ($activitytour->itinerary != "")
                                                                                                <b>@lang('messages.Itinerary') :</b> <br>
                                                                                                {!! $activitytour->itinerary !!}
                                                                                                <hr class="form-hr">
                                                                                            @endif
                                                                                            @if ($activitytour->additional_info != "")
                                                                                                <b>@lang('messages.Additional Information') :</b> <br>
                                                                                                {!! $activitytour->additional_info !!}
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            @if ($activitytour->cancellation_policy != "")
                                                                                <div class="page-subtitle">@lang('messages.Cancellation Policy')</div>
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <div class="cancelation-policy">
                                                                                            {!! $activitytour->cancellation_policy !!}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            <div class="page-subtitle">@lang('messages.Total Price')</div>
                                                                            <div class="row">
                                                                                <div class="col-md-12 m-b-8">
                                                                                    <div class="box-price-kicked">
                                                                                        <div class="row">
                                                                                            <div class="col-8 col-md-6">
                                                                                                <div class="usd-rate-kicked">@lang('messages.Price/pax') :</div>
                                                                                                <div class="usd-rate-kicked">@lang('messages.Number of Guests') :</div>
                                                                                                @if ($activitytour->discounts > 0)
                                                                                                    <div class="kick-back">@lang('messages.Discount') :</div>
                                                                                                @endif
                                                                                                <hr class="form-hr">
                                                                                                <div class="price-name">@lang('messages.Total Price')</div>
                                                                                            </div>
                                                                                            <div class="col-4 col-md-6 text-right">
                                                                                                <div class="usd-rate-kicked">{{ currencyFormatUsd($activitytour->price_pax) }}</div>
                                                                                                <div class="usd-rate-kicked">{{ $activitytour->number_of_guests }}</div>
                                                                                                @if ($activitytour->discounts > 0)
                                                                                                    <div class="kick-back">{{ "- $ ".$activitytour->discounts }}</div>
                                                                                                @endif
                                                                                                <hr class="form-hr">
                                                                                                <div class="usd-rate">{{ currencyFormatUsd($activitytour->final_price) }}</div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12 text-right">
                                                                                    <div class="form-group">
                                                                                        <a href="/orders">
                                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                    {{-- Transport ===================================================================================================================== --}}
                                    <div class="page-subtitle d-print-none">Transportation
                                        @if ($reservation->status != "Active")
                                            <span>
                                                <a href="/add-rsv-transport-{{ $reservation->id }}"><i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Transport" aria-hidden="true"></i></a>
                                            </span>
                                        @endif
                                    </div>
                                    @if (count($transports)>0)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="data-table table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Pickup Date</th>
                                                            <th scope="col">Transport</th>
                                                            <th scope="col">Service</th>
                                                            <th scope="col">Number of Guest</th>
                                                            @if ($reservation->status != "Active")
                                                                <th scope="col">Action</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($transports as $transport)
                                                            <tr>
                                                                <td>
                                                                    <p>{{ date('d M Y',strtotime($transport->checkin)) }}</p>
                                                                </td>
                                                                <td>
                                                                    <p>{{ $transport->servicename }}</p>
                                                                </td>
                                                                <td>
                                                                    <p>{{ $transport->service_type }}</p>
                                                                </td>
                                                                <td>
                                                                    <p>{{ $transport->number_of_guests }}</p>
                                                                </td>
                                                                @if ($reservation->status != "Active")
                                                                    <td>
                                                                        <div class="reservation-guest">
                                                                            <span>
                                                                                <a href="#" data-toggle="modal" data-target="#detail-transport-{{ $transport->id }}"> 
                                                                                    <button class="btn-view"><i class="icon-copy fa fa-eye" aria-hidden="true"></i></button>
                                                                                </a>
                                                                                <form action="/fupdate-activity-tour/{{ $transport->id }}" method="post" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('put')
                                                                                    <input type="hidden" name="rsv_id" value="">
                                                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Remove Order"><i class="icon-copy fa fa-remove" aria-hidden="true"></i></button>
                                                                                </form>
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                            {{-- Modal Activity Tour ===================================================================================================================== --}}
                                                            <div class="modal fade" id="detail-transport-{{ $transport->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content text-left">
                                                                        <div class="product-detail-wrap">
                                                                            <div class="row">
                                                                                <div class="col-6 col-md-6">
                                                                                    <div class="order-bil text-left">
                                                                                        <img src="{{ asset(config('app.logo_dark')) }}" alt="{{ config('app.alt_logo') }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-6 col-md-6">
                                                                                    <div class="label-title">@lang('messages.Order')</div>
                                                                                </div>
                                                                                <div class="col-md-12 text-right">
                                                                                    <div class="label-date float-right" style="width: 100%">
                                                                                        {{ dateFormat($now) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="business-name">{{ $business->name }}</div>
                                                                            <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                                                            <hr class="form-hr">
                                                                            <div class="row">
                                                                                <div class="col-4 col-sm-4 col-xl-3">
                                                                                    <div class="page-list"> @lang('messages.Order No') </div>
                                                                                    <div class="page-list"> @lang('messages.Order Date') </div>
                                                                                    <div class="page-list"> @lang('messages.Service') </div>
                                                                                    <div class="page-list"> @lang('messages.Location') </div>
                                                                                </div>
                                                                                <div class="col-8 col-sm-4 col-xl-6">
                                                                                    <div class="page-list-value">
                                                                                        <b>{{ $transport->orderno }}</b>
                                                                                    </div>
                                                                                    <div class="page-list-value">
                                                                                        {{ dateFormat($now) }}
                                                                                    </div>
                                                                                    @if ($transport->service == "Activity")
                                                                                        <div class="page-list-value">: @lang('messages.Activity')</div>
                                                                                    @elseif ($transport->service == "Tour Package")
                                                                                        <div class="page-list-value">: @lang('messages.Tour Package')</div>
                                                                                    @endif
                                                                                    
                                                                                    <div class="page-list-value">: {{ $transport->location }}</div>
                                                                                </div>
                                                                                <div class="col-12 col-sm-4 col-xl-3 text-center">
                                                                                    <p> @lang('messages.Status'): </p>
                                                                                    @if ($transport->status == "Active")
                                                                                        <h4 style="color: green">@lang('messages.'.$transport->status)</h4>
                                                                                    @elseif ($transport->status == "Pending")
                                                                                        <h4 style="color: #dd9e00">@lang('messages.'.$transport->status)</h4>
                                                                                    @elseif ($transport->status == "Invalid")
                                                                                        <h4 style="color: blue">@lang('messages.'.$transport->status)</h4>
                                                                                    @elseif ($transport->status == "Rejected")
                                                                                        <h4 style="color: red">@lang('messages.'.$transport->status)</h4>
                                                                                    @else
                                                                                        <h4 style="color: #5b5b5b">@lang('messages.'.$transport->status)</h4>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                            <hr class="form-hr">
                                                                            @if ($transport->service == "Activity")
                                                                                <div class="page-subtitle">@lang('messages.Activity')</div>
                                                                            @else
                                                                                <div class="page-subtitle">@lang('messages.Tour Package')</div>
                                                                            @endif
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-5 col-sm-5">
                                                                                            @if ($transport->service == "Activity")
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Activity')
                                                                                                </div>
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Number of Guest')
                                                                                                </div>
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Guest Name')
                                                                                                </div>
                                                                                            @elseif ($transport->service == "Tour Package")
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Name')
                                                                                                </div>
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Number of Guest')
                                                                                                </div>
                                                                                                <div class="page-list">
                                                                                                    @lang('messages.Guest Name')
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                        <div class="col-7 col-sm-7">
                                                                                            @if ($transport->service == "Activity")
                                                                                                <div class="page-list-value">
                                                                                                    {{ $transport->servicename }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ $transport->number_of_guests }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ $transport->guest_detail }}
                                                                                                </div>
                                                                                            @elseif ($transport->service == "Tour Package")
                                                                                                <div class="page-list-value">
                                                                                                    {{ $transport->servicename }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ $transport->number_of_guests }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ $transport->guest_detail }}
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-5 col-sm-5">
                                                                                            <div class="page-list">
                                                                                                @lang('messages.Duration')
                                                                                            </div>
                                                                                            <div class="page-list">
                                                                                                @lang('messages.Start Date')
                                                                                            </div>
                                                                                            <div class="page-list">
                                                                                                @lang('messages.End Date')
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-7 col-sm-7">
                                                                                            @if ($transport->service == "Activity")
                                                                                                <div class="page-list-value">
                                                                                                    {{ $transport->duration." " }}@lang('messages.Hours')
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ dateFormat($transport->checkin) }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ dateFormat($transport->checkin) }}
                                                                                                </div>
                                                                                            @elseif ($transport->service == "Tour Package")
                                                                                                <div class="page-list-value">
                                                                                                    {{ $transport->duration." " }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ dateFormat($transport->checkin) }}
                                                                                                </div>
                                                                                                <div class="page-list-value">
                                                                                                    {{ dateFormat($transport->checkout) }}
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                @if ($transport->include != "" or $transport->itinerary != "" or $transport->additional_info)
                                                                                    <div class="col-md-12">
                                                                                        <div class="page-note">
                                                                                            @if ($transport->include != "")
                                                                                                <b>@lang('messages.Include') :</b> <br>
                                                                                                {!! $transport->include !!}
                                                                                                <hr class="form-hr">
                                                                                            @endif
                                                                                            @if ($transport->itinerary != "")
                                                                                                <b>@lang('messages.Itinerary') :</b> <br>
                                                                                                {!! $transport->itinerary !!}
                                                                                                <hr class="form-hr">
                                                                                            @endif
                                                                                            @if ($transport->additional_info != "")
                                                                                                <b>@lang('messages.Additional Information') :</b> <br>
                                                                                                {!! $transport->additional_info !!}
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            @if ($transport->cancellation_policy != "")
                                                                                <div class="page-subtitle">@lang('messages.Cancellation Policy')</div>
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <div class="cancelation-policy">
                                                                                            {!! $transport->cancellation_policy !!}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            <div class="page-subtitle">@lang('messages.Total Price')</div>
                                                                            <div class="row">
                                                                                <div class="col-md-12 m-b-8">
                                                                                    <div class="box-price-kicked">
                                                                                        <div class="row">
                                                                                            <div class="col-8 col-md-6">
                                                                                                <div class="usd-rate-kicked">@lang('messages.Price/pax') :</div>
                                                                                                <div class="usd-rate-kicked">@lang('messages.Number of Guests') :</div>
                                                                                                @if ($transport->discounts > 0)
                                                                                                    <div class="kick-back">@lang('messages.Discount') :</div>
                                                                                                @endif
                                                                                                <hr class="form-hr">
                                                                                                <div class="price-name">@lang('messages.Total Price')</div>
                                                                                            </div>
                                                                                            <div class="col-4 col-md-6 text-right">
                                                                                                <div class="usd-rate-kicked">{{ currencyFormatUsd($transport->price_pax) }}</div>
                                                                                                <div class="usd-rate-kicked">{{ $transport->number_of_guests }}</div>
                                                                                                @if ($transport->discounts > 0)
                                                                                                    <div class="kick-back">{{ "- $ ".$transport->discounts }}</div>
                                                                                                @endif
                                                                                                <hr class="form-hr">
                                                                                                <div class="usd-rate">{{ currencyFormatUsd($transport->final_price) }}</div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12 text-right">
                                                                                    <div class="form-group">
                                                                                        <a href="/orders">
                                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                               
                                {{-- Itinerary ===================================================================================================================== --}}
                                @php
                                    $from = date('Y-m-d',strtotime($reservation->checkin));
                                    $dur = $dur_res + 1;
                                    $date_stay = [];
                                    for ($a=0; $a < $dur ; $a++) { 
                                        $date_stay[$a] = $from;
                                        $from = date('Y-m-d',strtotime('+1 days',strtotime($from)));
                                    }
                                @endphp
                                <div id="itinerarys" class="page-subtitle d-print-none">Itinerary
                                    @if ($reservation->status != "Active")
                                        <span>
                                            <a href="#" data-toggle="modal" data-target="#add-itinerary-{{ $reservation->id }}"> 
                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Itinerary" aria-hidden="true"></i>
                                            </a>
                                        </span>
                                    @endif
                                </div>
                                @if (count($itinerarys)>0)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="data-table table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 20%" scope="col">Date</th>
                                                            <th style="width: 68%" scope="col">Itinerary</th>
                                                            @if ($reservation->status != "Active")
                                                                <th style="width: 12%" scope="col">Action</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($itinerarys as $itinerary)
                                                            <tr>
                                                                <td>
                                                                    <p>{{ date('d M Y D', strtotime($itinerary->date)) }}</p>
                                                                </td>
                                                                <td>
                                                                    <p>{{ $itinerary->itinerary }}</p>
                                                                </td>
                                                                @if ($reservation->status != "Active")
                                                                    <td class="text-right">
                                                                        <div class="table-action">
                                                                        
                                                                            <a href="#" data-toggle="modal" data-target="#edit-itinerary-{{ $itinerary->id }}"> 
                                                                                <button class="btn-update" data-toggle="tooltip" data-placement="left" title="Edit {{ $itinerary->name }}"><i class="icon-copy fa fa-pencil"></i></button>
                                                                            </a>
                                                                            <form action="/delete-itinerary/{{ $itinerary->id }}" method="post">
                                                                                @csrf
                                                                                @method('delete')
                                                                                <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Remove Itinerary {{ $itinerary->name }}"><i class="icon-copy fa fa-remove" aria-hidden="true"></i></button>
                                                                            </form>
                                                                        
                                                                        </div>
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                            {{-- Modal Edit Guest --------------------------------------------------------------------------------------------------------------- --}}
                                                            @if ($reservation->status != "Active")
                                                                <div class="modal fade" id="edit-itinerary-{{ $itinerary->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content text-left">
                                                                            <div class="card-box">
                                                                                <div class="card-box-title">
                                                                                    <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Edit Itinerary</div>
                                                                                </div>
                                                                                <form id="update-itinerary-{{ $itinerary->id }}" action="/fupdate-itinerary/{{ $itinerary->id }}" method="post" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('put')
                                                                                    <div class="row">
                                                                                        <div class="col-sm-4">
                                                                                            <div class="form-group">
                                                                                                <label for="date">Date <span>*</span></label>
                                                                                                <select name="date" class="custom-select @error('date') is-invalid @enderror" placeholder="Select date" required>
                                                                                                    <option selected value="{{ date('Y-m-d',strtotime($itinerary->date)) }}">{{ date('d M Y D',strtotime($itinerary->date)) }}</option>
                                                                                                    @foreach ($date_stay as $datestay)
                                                                                                        <option value="{{ date('Y-m-d',strtotime($datestay)) }}">{{ date('d M Y D',strtotime($datestay)) }}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-8">
                                                                                            <div class="form-group row">
                                                                                                <label for="itinerary" class="col-sm-12 col-md-12 col-form-label">Itinerary <span>*</span></label>
                                                                                                <div class="col-sm-12">
                                                                                                    <input type="text" name="itinerary" class="form-control @error('itinerary') is-invalid @enderror" placeholder="Insert some text" value="{!! $itinerary->itinerary !!}" required>
                                                                                                </div>
                                                                                                @error('itinerary')
                                                                                                    <div class="alert-form">{{ $message }}</div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>
                                                                                <div class="card-box-footer">
                                                                                    <button type="submit" form="update-itinerary-{{ $itinerary->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                {{-- Modal Add Itinerary --------------------------------------------------------------------------------------------------------------- --}}
                                @if ($reservation->status != "Active")
                                    <div class="modal fade" id="add-itinerary-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Itinerary</div>
                                                    </div>
                                                    <form id="add-itinerary" action="/fadd-itinerary" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <div class="form-group">
                                                                    <label for="date">Date <span>*</span></label>
                                                                    <select name="date" class="custom-select @error('date') is-invalid @enderror" placeholder="Select date" required>
                                                                        <option selected value="">Select Date</option>
                                                                        @foreach ($date_stay as $datestay)
                                                                            <option value="{{ date('Y-m-d',strtotime($datestay)) }}">{{ date('d M Y',strtotime($datestay)) }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <div class="form-group row">
                                                                    <label for="itinerary" class="col-sm-12 col-md-12 col-form-label">Itinerary <span>*</span></label>
                                                                    <div class="col-sm-12">
                                                                        <input type="text" name="itinerary" class="form-control @error('itinerary') is-invalid @enderror" placeholder="Insert some text" required>
                                                                    </div>
                                                                    @error('itinerary')
                                                                        <div class="alert-form">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                                        </div>
                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="add-itinerary" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{-- Restaurant --------------------------------------------------------------------------------------------------------------- --}}
                                <div class="page-subtitle d-print-none">Restaurant
                                    @if ($reservation->status != "Active")
                                        <span>
                                            <a href="#" data-toggle="modal" data-target="#add-restaurant-{{ $reservation->id }}"> 
                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Restaurant" aria-hidden="true"></i>
                                            </a>
                                        </span>
                                    @endif
                                </div>
                                {{-- Modal Add Restaurant --------------------------------------------------------------------------------------------------------------- --}}
                                @if ($reservation->status != "Active")
                                    <div class="modal fade" id="add-restaurant-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="product-detail-wrap">
                                                    
                                                        <div class="row">
                                                            
                                                                <div class="col-md-12">
                                                                    <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Restaurant</div>
                                                                </div>
                                                                <div class="col-12 text-right">
                                                                    <hr class="form-hr">
                                                                </div>
                                                        </div>
                                                        <form action="/fadd-restaurant" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('put')
                                                            <div class="row">
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="date">Date <span>*</span></label>
                                                                        <select name="date" class="custom-select @error('date') is-invalid @enderror" placeholder="Select date" required>
                                                                            <option selected value="">Select Date</option>
                                                                            @foreach ($date_stay as $datestay)
                                                                                <option value="{{ date('Y-m-d',strtotime($datestay)) }}">{{ date('d M Y',strtotime($datestay)) }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-sm-3">
                                                                    <div class="form-group row">
                                                                        <label for="breakfast" class="col-sm-12 col-md-12 col-form-label">Breakfast <span>*</span></label>
                                                                        <div class="col-sm-12">
                                                                        <input type="text" name="breakfast" class="form-control @error('breakfast') is-invalid @enderror" placeholder="Insert breakfast location" value="{{ old('breakfast') }}" required>
                                                                        </div>
                                                                        @error('breakfast')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group row">
                                                                        <label for="lunch" class="col-sm-12 col-md-12 col-form-label">Lunch <span>*</span></label>
                                                                        <div class="col-sm-12">
                                                                        <input type="text" name="lunch" class="form-control @error('lunch') is-invalid @enderror" placeholder="Insert lunch location" value="{{ old('lunch') }}" required>
                                                                        </div>
                                                                        @error('lunch')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group row">
                                                                        <label for="dinner" class="col-sm-12 col-md-12 col-form-label">Dinner <span>*</span></label>
                                                                        <div class="col-sm-12">
                                                                        <input type="text" name="dinner" class="form-control @error('dinner') is-invalid @enderror" placeholder="Insert dinner" value="{{ old('dinner') }}" required>
                                                                        </div>
                                                                        @error('dinner')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-12 col-md-12 text-right">
                                                                    <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                                                    <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row m-b-8">
                                    <div class="col-md-12">
                                        <table class="data-table table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 20%" scope="col">Date</th>
                                                        <th style="width: 25%" scope="col">Breakfast</th>
                                                        <th style="width: 25%" scope="col">Lunch</th>
                                                        <th style="width: 25%" scope="col">Dinner</th>
                                                        @if ($reservation->status != "Active")
                                                            <th style="width: 5%" scope="col">Action</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($restaurants as $restaurant)
                                                        <tr>
                                                            <td>
                                                                <p>{{ dateFormat($restaurant->date) }}</p>
                                                            </td>
                                                            <td>
                                                                <p>{{ $restaurant->breakfast }}</p>
                                                            </td>
                                                            <td>
                                                                <p>{{ $restaurant->lunch }}</p>
                                                            </td>
                                                            <td>
                                                                <p>{{ $restaurant->dinner }}</p>
                                                            </td>
                                                            @if ($reservation->status != "Active")
                                                                <td>
                                                                    <div class="reservation-guest">
                                                                        <span>
                                                                            <a href="#" data-toggle="modal" data-target="#edit-restaurant-{{ $restaurant->id }}"> 
                                                                                <button class="btn-view"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button>
                                                                            </a>
                                                                            <form action="/fdelete-restaurant/{{ $restaurant->id }}" method="post" enctype="multipart/form-data">
                                                                                @csrf
                                                                                @method('delete')
                                                                                <input type="hidden" name="rsv_id" value="">
                                                                                <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Remove Restaurant"><i class="icon-copy fa fa-remove" aria-hidden="true"></i></button>
                                                                            </form>
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                        {{-- Modal Edit Restaurant --------------------------------------------------------------------------------------------------------------- --}}
                                                        @if ($reservation->status != "Active")
                                                            <div class="modal fade" id="edit-restaurant-{{ $restaurant->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content text-left">
                                                                        <div class="product-detail-wrap">
                                                                            
                                                                                <div class="row">
                                                                                    
                                                                                        <div class="col-md-12">
                                                                                            <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Meal Location</div>
                                                                                        </div>
                                                                                        <div class="col-12 text-right">
                                                                                            <hr class="form-hr">
                                                                                        </div>
                                                                                </div>
                                                                                <form action="/fupdate-restaurant/{{ $restaurant->id }}" method="post" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('put')
                                                                                    <div class="row">
                                                                                        <div class="col-sm-3">
                                                                                            <div class="form-group">
                                                                                                <label for="date">Date <span>*</span></label>
                                                                                                <select name="date" class="custom-select @error('date') is-invalid @enderror" placeholder="Select date" required>
                                                                                                    <option selected value="{{ $restaurant->date }}">{{ date('d M Y',strtotime($restaurant->date)) }}</option>
                                                                                                    @foreach ($date_stay as $datestay)
                                                                                                        <option value="{{ date('Y-m-d',strtotime($datestay)) }}">{{ date('d M Y',strtotime($datestay)) }}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                    
                                                                                        <div class="col-sm-3">
                                                                                            <div class="form-group row">
                                                                                                <label for="breakfast" class="col-sm-12 col-md-12 col-form-label">Breakfast <span>*</span></label>
                                                                                                <div class="col-sm-12">
                                                                                                <input type="text" name="breakfast" class="form-control @error('breakfast') is-invalid @enderror" placeholder="Insert breakfast location" value="{{ $restaurant->breakfast }}" required>
                                                                                                </div>
                                                                                                @error('breakfast')
                                                                                                    <div class="alert-form">{{ $message }}</div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3">
                                                                                            <div class="form-group row">
                                                                                                <label for="lunch" class="col-sm-12 col-md-12 col-form-label">Lunch <span>*</span></label>
                                                                                                <div class="col-sm-12">
                                                                                                <input type="text" name="lunch" class="form-control @error('lunch') is-invalid @enderror" placeholder="Insert lunch location" value="{{ $restaurant->lunch }}" required>
                                                                                                </div>
                                                                                                @error('lunch')
                                                                                                    <div class="alert-form">{{ $message }}</div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3">
                                                                                            <div class="form-group row">
                                                                                                <label for="dinner" class="col-sm-12 col-md-12 col-form-label">Dinner <span>*</span></label>
                                                                                                <div class="col-sm-12">
                                                                                                <input type="text" name="dinner" class="form-control @error('dinner') is-invalid @enderror" placeholder="Insert dinner location" value="{{ $restaurant->dinner }}" required>
                                                                                                </div>
                                                                                                @error('dinner')
                                                                                                    <div class="alert-form">{{ $message }}</div>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-12 col-md-12 text-right">
                                                                                            <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                                                                            <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>
                                                                            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                                
                                        </table>
                                    </div>
                                </div>
                                {{-- Include --------------------------------------------------------------------------------------------------------------- --}}
                                <div class="page-subtitle d-print-none">Include
                                    @if ($reservation->status != "Active")
                                        <span>
                                            <a href="#" data-toggle="modal" data-target="#add-include-{{ $reservation->id }}"> 
                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Include" aria-hidden="true"></i>
                                            </a>
                                        </span>
                                    @endif
                                </div>
                                {{-- Modal Add Include --------------------------------------------------------------------------------------------------------------- --}}
                                @if ($reservation->status != "Active")
                                    <div class="modal fade" id="add-include-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="product-detail-wrap">
                                                    <div class="row">
                                                        
                                                            <div class="col-md-12">
                                                                <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Include</div>
                                                            </div>
                                                            <div class="col-12 text-right">
                                                                <hr class="form-hr">
                                                            </div>
                                                    </div>
                                                    <form action="/fadd-include" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group row">
                                                                    <label for="include" class="col-sm-12 col-md-12 col-form-label">Include <span>*</span></label>
                                                                    <div class="col-sm-12">
                                                                        <input type="text" name="include" class="form-control @error('include') is-invalid @enderror" placeholder="Insert include" value="{{ old('include') }}" required>
                                                                    </div>
                                                                    @error('include')
                                                                        <div class="alert-form">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 col-md-12 text-right">
                                                                <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                                                <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row m-b-8">
                                    <div class="col-md-12">
                                        <table class="data-table table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%" scope="col">No</th>
                                                    <th style="width: 80%" scope="col">Include</th>
                                                    @if ($reservation->status != "Active")
                                                        <th style="width: 10%" scope="col">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($includes as $inno=>$include)
                                                    <tr>
                                                        <td class="text-right">
                                                            <p>{{ ++$inno }}</p>
                                                        </td>
                                                        <td>
                                                            <p>{{ $include->include }}</p>
                                                        </td>
                                                        @if ($reservation->status != "Active")
                                                            <td>
                                                                <div class="reservation-guest">
                                                                    <span>
                                                                        <a href="#" data-toggle="modal" data-target="#edit-include-{{ $include->id }}"> 
                                                                            <button class="btn-view"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button>
                                                                        </a>
                                                                        <form action="/fdelete-include/{{ $include->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('delete')
                                                                            <input type="hidden" name="rsv_id" value="">
                                                                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Remove Include"><i class="icon-copy fa fa-remove" aria-hidden="true"></i></button>
                                                                        </form>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                    {{-- Modal Edit Include --------------------------------------------------------------------------------------------------------------- --}}
                                                    @if ($reservation->status != "Active")
                                                        <div class="modal fade" id="edit-include-{{ $include->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="product-detail-wrap">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Edit Include</div>
                                                                            </div>
                                                                            <div class="col-12 text-right">
                                                                                <hr class="form-hr">
                                                                            </div>
                                                                        </div>
                                                                        <form action="/fupdate-include/{{ $include->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('put')
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group row">
                                                                                        <label for="include" class="col-sm-12 col-md-12 col-form-label">Include <span>*</span></label>
                                                                                        <div class="col-sm-12">
                                                                                        <input type="text" name="include" class="form-control @error('include') is-invalid @enderror" placeholder="Insert include location" value="{{ $include->include }}" required>
                                                                                        </div>
                                                                                        @error('include')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12 col-md-12 text-right">
                                                                                    <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{-- Exclude --------------------------------------------------------------------------------------------------------------- --}}
                                <div class="page-subtitle d-print-none">Exclude
                                    @if ($reservation->status != "Active")
                                        <span>
                                            <a href="#" data-toggle="modal" data-target="#add-exclude-{{ $reservation->id }}"> 
                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Exclude" aria-hidden="true"></i>
                                            </a>
                                        </span>
                                    @endif
                                </div>
                                {{-- Modal Add Exclude --------------------------------------------------------------------------------------------------------------- --}}
                                @if ($reservation->status != "Active")
                                    <div class="modal fade" id="add-exclude-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="product-detail-wrap">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Exclude</div>
                                                        </div>
                                                        <div class="col-12 text-right">
                                                            <hr class="form-hr">
                                                        </div>
                                                    </div>
                                                    <form action="/fadd-exclude" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group row">
                                                                    <label for="exclude" class="col-sm-12 col-md-12 col-form-label">Exclude <span>*</span></label>
                                                                    <div class="col-sm-12">
                                                                        <input type="text" name="exclude" class="form-control @error('exclude') is-invalid @enderror" placeholder="Insert exclude" value="{{ old('exclude') }}" required>
                                                                    </div>
                                                                    @error('exclude')
                                                                        <div class="alert-form">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 col-md-12 text-right">
                                                                <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                                                <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row m-b-8">
                                    <div class="col-md-12">
                                        <table class="data-table table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%" scope="col">No</th>
                                                    <th style="width: 80%" scope="col">Exclude</th>
                                                    @if ($reservation->status != "Active")
                                                        <th style="width: 10%" scope="col">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($excludes as $exno=>$exclude)
                                                    <tr>
                                                        <td class="text-right">
                                                            <p>{{ ++$exno }}</p>
                                                        </td>
                                                        <td>
                                                            <p>{{ $exclude->exclude }}</p>
                                                        </td>
                                                        @if ($reservation->status != "Active")
                                                            <td>
                                                                <div class="reservation-guest">
                                                                    <span>
                                                                        <a href="#" data-toggle="modal" data-target="#edit-exclude-{{ $exclude->id }}"> 
                                                                            <button class="btn-view"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button>
                                                                        </a>
                                                                        <form action="/fdelete-exclude/{{ $exclude->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('delete')
                                                                            <input type="hidden" name="rsv_id" value="">
                                                                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Remove Exclude"><i class="icon-copy fa fa-remove" aria-hidden="true"></i></button>
                                                                        </form>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                    {{-- Modal Edit Include --------------------------------------------------------------------------------------------------------------- --}}
                                                    @if ($reservation->status != "Active")
                                                        <div class="modal fade" id="edit-exclude-{{ $exclude->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="product-detail-wrap">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Edit Include</div>
                                                                            </div>
                                                                            <div class="col-12 text-right">
                                                                                <hr class="form-hr">
                                                                            </div>
                                                                        </div>
                                                                        <form action="/fupdate-exclude/{{ $exclude->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('put')
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group row">
                                                                                        <label for="exclude" class="col-sm-12 col-md-12 col-form-label">Include <span>*</span></label>
                                                                                        <div class="col-sm-12">
                                                                                        <input type="text" name="exclude" class="form-control @error('exclude') is-invalid @enderror" placeholder="Insert exclude location" value="{{ $exclude->exclude }}" required>
                                                                                        </div>
                                                                                        @error('exclude')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12 col-md-12 text-right">
                                                                                    <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{-- Remark --------------------------------------------------------------------------------------------------------------- --}}
                                <div class="page-subtitle d-print-none">Remark
                                    @if ($reservation->status != "Active")
                                        <span>
                                            <a href="#" data-toggle="modal" data-target="#add-remark-{{ $reservation->id }}"> 
                                                <i class="icon-copy fa fa-plus-circle" data-toggle="tooltip" data-placement="left" title="Add Remark" aria-hidden="true"></i>
                                            </a>
                                        </span>
                                    @endif
                                </div>
                                {{-- Modal Add Remark --------------------------------------------------------------------------------------------------------------- --}}
                                @if ($reservation->status != "Active")
                                    <div class="modal fade" id="add-remark-{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="product-detail-wrap">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Remark</div>
                                                        </div>
                                                        <div class="col-12 text-right">
                                                            <hr class="form-hr">
                                                        </div>
                                                    </div>
                                                    <form action="/fadd-remark" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group row">
                                                                    <label for="remark" class="col-sm-12 col-md-12 col-form-label">Remark <span>*</span></label>
                                                                    <div class="col-sm-12">
                                                                        <input type="text" name="remark" class="form-control @error('remark') is-invalid @enderror" placeholder="Insert remark" value="{{ old('remark') }}" required>
                                                                    </div>
                                                                    @error('remark')
                                                                        <div class="alert-form">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 col-md-12 text-right">
                                                                <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                                                <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row m-b-8">
                                    <div class="col-md-12">
                                        <table class="data-table table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%" scope="col">No</th>
                                                    <th style="width: 80%" scope="col">Remark</th>
                                                    @if ($reservation->status != "Active")
                                                        <th style="width: 10%" scope="col">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($remarks as $reno=>$remark)
                                                    <tr>
                                                        <td class="text-right">
                                                            <p>{{ ++$reno }}</p>
                                                        </td>
                                                        <td>
                                                            <p>{{ $remark->remark }}</p>
                                                        </td>
                                                        @if ($reservation->status != "Active")
                                                            <td>
                                                                <div class="reservation-guest">
                                                                    <span>
                                                                        <a href="#" data-toggle="modal" data-target="#edit-remark-{{ $remark->id }}"> 
                                                                            <button class="btn-view"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button>
                                                                        </a>
                                                                        <form action="/fdelete-remark/{{ $remark->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('delete')
                                                                            <input type="hidden" name="rsv_id" value="">
                                                                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Remove Remark"><i class="icon-copy fa fa-remove" aria-hidden="true"></i></button>
                                                                        </form>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                    {{-- Modal Edit Remark --------------------------------------------------------------------------------------------------------------- --}}
                                                    @if ($reservation->status != "Active")
                                                        <div class="modal fade" id="edit-remark-{{ $remark->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="product-detail-wrap">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Edit Remark</div>
                                                                            </div>
                                                                            <div class="col-12 text-right">
                                                                                <hr class="form-hr">
                                                                            </div>
                                                                        </div>
                                                                        <form action="/fupdate-remark/{{ $remark->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('put')
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group row">
                                                                                        <label for="remark" class="col-sm-12 col-md-12 col-form-label">Remark <span>*</span></label>
                                                                                        <div class="col-sm-12">
                                                                                        <input type="text" name="remark" class="form-control @error('remark') is-invalid @enderror" placeholder="Insert remark location" value="{{ $remark->remark }}" required>
                                                                                        </div>
                                                                                        @error('remark')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12 col-md-12 text-right">
                                                                                    <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row d-print-none">
                                <div style="display: flex; justify-content: flex-end;" class="col-md-12 text-right p-b-18">
                                    <a href="/reservation"><button type="button" class="btn btn-dark m-b-8 m-r-8"><i class="icon-copy fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                                    <a href="/fdownload-rsv/{{ $reservation->id }}">
                                        <button class="btn btn-primary m-r-8" data-toggle="tooltip" data-placement="top" title="Download Contract"><i class="icon-copy fa fa-eye" aria-hidden="true"></i> Download</button>
                                    </a>
                                    @if ($invoice != "")
                                        <a href="/invoice-{{ $invoice->id }}">
                                            <button class="btn btn-primary m-r-8" data-toggle="tooltip" data-placement="top" title="Detail Invoice"><i class="icon-copy fa fa-eye" aria-hidden="true"></i> Invoice</button>
                                        </a>
                                    @else
                                        <form action="/fadd-invoice" method="post" enctype="multipart/form-data">
                                            @csrf
                                            @method('put')
                                            <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                            <input type="hidden" name="inv_no" value="{{ "INV-".$reservation->rsv_no }}">
                                            <input type="hidden" name="bank_id" value=1>
                                            <input type="hidden" name="inv_date" value="{{ date('Y-m-d',strtotime($now)) }}">
                                            <input type="hidden" name="due_date" value="{{ date('Y-m-d',strtotime('+2 weeks',strtotime($now))) }}">
                                            <button type="submit" class="btn btn-primary m-b-8 p-l-8 m-r-8"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Create Invoice</button>
                                        </form>
                                    @endif
                                    @if ($reservation->status == "Active")
                                        <form action="/deactivate-reservation/{{ $reservation->id }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            @method('put')
                                            <button type="submit" class="btn btn-danger m-b-8 p-l-8"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Deactivate</button>
                                        </form>
                                    @else
                                        <form action="/activate-reservation/{{ $reservation->id }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            @method('put')
                                            <button type="submit" class="btn btn-primary m-b-8 p-l-8 m-r"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Activate</button>
                                        </form>
                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                    </div>
					<div class="col-md-4 d-print-none">
                        <div class="card-box mb-30">
                            <div class="banner-right p-b-18">
                                <div class="title">Attention!</div>
                                <p>1. Contact the agent to validate the reservation!</p>
                                <p>2. Use the "ADD ORDER" button to add an order!</p>
                                <p>3. Use the "EDIT DATA" button to change the reservation data!</p>
                                <p>4. Make sure all data is correct and valid!</p>
                                <p>5. After the reservation is completed, then you can make an invoice!</p>
                                <p>6. Make sure all reservation data, orders, and other data are correct in this reservation to make it easier to create invoices!</p>
                            </div>
                        </div>
                        @if ($reservation->msg != "")
                            <div class="card-box mb-30">
                                <b>Admin Notes</b> <br>
                                <div class="trackrecord">
                                    <p>{{ $reservation->msg }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @include('layouts.footer')
        </div>
    </div>
    <script type="text/javascript">
        function htl(){
            var select_hotels = document.getElementById('select_hotels').value;
            return select_hotels;
        }
        document.write(htl());
    </script>
    @endcan
@endsection
