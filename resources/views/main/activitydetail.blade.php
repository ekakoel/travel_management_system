@section('title', __('messages.Activities'))
@section('content')
    @extends('layouts.head')
	<div class="mobile-menu-overlay"></div>
	<div class="main-container">
		<div class="pd-ltr-20">
            @if (count($errors) > 0)
                <div class="alert-error-code">
                    <div class="alert alert-danger w3-animate-top">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{{ $error }}</div></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            @if (\Session::has('success'))
                <div class="alert-error-code">
                    <div class="alert alert-success w3-animate-top">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{!! \Session::get('success') !!}</div></li>
                        </ul>
                    </div>
                </div>
            @endif
            @if ($bookingcode_status == "Invalid")
                <div class="alert-error-code w3-animate-top">
                    <div class="alert alert-danger">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Invalid Code')</div></li>
                        </ul>
                    </div>
                </div>
            @endif
            @if ($bookingcode_status == "Expired")
                <div class="alert-error-code w3-animate-top">
                    <div class="alert alert-danger">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Expired Code')</div></li>
                        </ul>
                    </div>
                </div>
            @endif
            @if ($bookingcode_status == "Used")
                <div class="alert-error-code w3-animate-top">
                    <div class="alert alert-danger">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Used Code')</div></li>
                        </ul>
                    </div>
                </div>
            @endif
            {{-- BOOKING CODE AND PROMOTION =================================================================================================== --}}
            @if (isset($bookingcode->code) || isset($promotions))
                <div class="row" id="bookingcode_promotion">
                    <div class="col-12 promotion-bookingcode">
                        @if ($bookingcode_status == "Valid")
                            <div class="bookingcode-card w3-animate-top">
                                <div class="icon-card bookingcode">
                                    <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                </div>
                                <div class="content-card">
                                    <div class="code">{{ $bookingcode->code }}</div>
                                    <div class="text-card">@lang('messages.Booking Code') @lang('messages.Aplied')</div>
                                    <div class="text-card">@lang('messages.Expired') {{ date('d M y', strtotime($bookingcode->expired_date)) }}</div>
                                </div>
                                <div class="content-card-price">
                                    <div class="price"><span>$</span>{{ $bookingcode->discounts }}</div>
                                    <a href="/activity-{{ $activity->code }}">
                                        <button type="submit" class="btn-remove-code m-t-10"><i class="fa fa-close" aria-hidden="true"></i></button>
                                    </a>
                                </div>
                            </div>
                            @if (isset($promotions))
                                @foreach ($promotions as $promotion)
                                    <div class="bookingcode-card w3-animate-top">
                                        <div class="icon-card promotion">
                                            <i class="fa fa-bullhorn" aria-hidden="true"></i>
                                        </div>
                                        <div class="content-card">
                                            <div class="code">{{ $promotion->name }}</div>
                                            <div class="text-card">@lang('messages.Promo Period')</div>
                                            <div class="text-card">{{ date('d M y', strtotime($promotion->periode_start))." - ".date('d M y', strtotime($promotion->periode_end)) }}</div>
                                        </div>
                                        <div class="content-card-promo">
                                            <div class="price"><span>$</span>{{ $promotion->discounts }}</div>
                                            <button class="btn-remove-code m-t-10" data-toggle="tooltip" data-placement="top" title='@lang('messages.Ongoing promotion'){{" ". $promotion->name." "}}@lang('messages.and get discounts'){{ " $".$promotion->discounts." " }}@lang('messages.until'){{ " ". date('d M y',strtotime($promotion->periode_end)) }}'><i class="fa fa-question" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @else
                            @if (isset($promotions))
                                @foreach ($promotions as $promotion)
                                    <div class="bookingcode-card w3-animate-top">
                                        <div class="icon-card promotion">
                                            <i class="fa fa-bullhorn" aria-hidden="true"></i>
                                        </div>
                                        <div class="content-card">
                                            <div class="code">{{ $promotion->name }}</div>
                                            <div class="text-card">@lang('messages.Promo Period')</div>
                                            <div class="text-card">{{ date('d M y', strtotime($promotion->periode_start))." - ".date('d M y', strtotime($promotion->periode_end)) }}</div>
                                        </div>
                                        <div class="content-card-promo">
                                            <div class="price"><span>$</span>{{ $promotion->discounts }}</div>
                                            <button class="btn-remove-code m-t-10" data-toggle="tooltip" data-placement="top" title='@lang('messages.Ongoing promotion'){{" ". $promotion->name." "}}@lang('messages.and get discounts'){{ " $".$promotion->discounts." " }}@lang('messages.until'){{ " ". date('d M y',strtotime($promotion->periode_end)) }}'><i class="fa fa-question" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>
            @endif
            {{-- END BOOKING CODE AND PROMOTION =================================================================================================== --}}
            <div class="row">
                <div class="col-md-4 mobile">
                    <div class="card-box m-b-18">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fa fa-qrcode" aria-hidden="true"></i>@lang('messages.Booking Code')</div>
                        </div>
                        <div class="detail-item text-right">
                            <form action="/activity-detail" method="POST" role="search" style="padding:0px;">
                                @csrf
                                <div class="form-group m-b-8">
                                    @if (isset($bookingcode->code))
                                        <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ $bookingcode->code }}">
                                    @else
                                        <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ old('bookingcode') }}">
                                    @endif
                                    <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                                </div>
                                <button type="submit" class="btn btn-primary m-t-8"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Check Code')</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card-box m-b-18">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="fa fa-child" aria-hidden="true"></i>{{ $activity->name }}</div>
                        </div>
                        <div class="page-card">
                            <div class="card-banner">
                                <img src="{{ asset ('storage/activities/activities-cover/' . $activity->cover) }}" alt="{{ $activity->name }}" loading="lazy">
                            </div>
                            <div class="card-content">
                                <div class="tab">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active"  data-toggle="tab" href="#detail" role="tab" aria-selected="false">Detail</a>
                                        </li>
                                        @if (isset($activity->include))
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#include-tab" role="tab" aria-selected="false">Include</a>
                                            </li>
                                        @endif
                                        @if (isset($activity->itinerary))
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#itinerary-tab" role="tab" aria-selected="false">Itinerary</a>
                                            </li>
                                        @endif
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="detail" role="tabpanel">
                                            <div class="row">
                                                <div class="col-12">
                                                    <p>{!! $activity->description !!}</p>
                                                </div>
                                                <div class="col-4 col-md-4">
                                                    <div class="card-subtitle">@lang('messages.Duration'):</div>
                                                    <p>{{ $activity->duration }}</p>
                                                </div>
                                                <div class="col-4 col-md-4">
                                                    <div class="card-subtitle">@lang('messages.Capacity'):</div>
                                                    <p>{{ $activity->qty }} @lang('messages.Guests')</p>
                                                </div>
                                                <div class="col-4 col-md-4">
                                                    <div class="card-subtitle">@lang('messages.Location'):</div>
                                                    <p>{{ $activity->location }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @if (isset($activity->include))
                                            <div class="tab-pane fade" id="include-tab" role="tabpanel">
                                                <p>{!! $activity->include !!}</p>
                                            </div>
                                        @endif
                                        @if (isset($activity->itinerary))
                                            <div class="tab-pane fade" id="itinerary-tab" role="tabpanel">
                                                <p>{!! $activity->itinerary !!}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-text">
                                    <div class="row">
                                        <div class="col-12 text-right">
                                            <hr class="form-hr">
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-6 text-left">
                                                    @if (isset($bookingcode->code) or $promotion_price >0)
                                                        <div class="card-subtitle">@lang('messages.Normal Price'):</div>
                                                        <div class="price-usd-normal p-l-0">{{"$ " . number_format($normal_price) }}<span>/@lang('messages.pax')</span></div>
                                                    @endif
                                                </div>
                                                <div class="col-6 text-right">
                                                    <div class="card-subtitle">@lang('messages.Price'):</div>
                                                    <div class="price-usd float-right">{{"$ " . number_format($final_price) }}<span>/@lang('messages.pax')</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-box-footer">
                            @if ($activity->cancellation_policy > 0)
                                <div class="btn-cancelation-policy" data-toggle="tooltip" data-placement="left" title="@lang('messages.Cancelation Policy')">
                                    <a href="#" data-toggle="modal" data-target="#cancellation-policy-{{ $activity->id }}">
                                        <i class="icon-copy fa fa-info" aria-hidden="true"></i>
                                    </a>
                                </div>
                            @endif
                            <a href="#" data-toggle="modal" data-target="#order-activity-{{ $activity->id }}">
                                <button class="btn btn-primary"><i class="fa fa-shopping-basket" aria-hidden="true"></i> @lang('messages.Order')</button>
                            </a>
                        </div>
                        {{-- Modal Cancellation Policy --------------------------------------------------------------------------------------------------------------- --}}
                        <div class="modal fade" id="cancellation-policy-{{ $activity->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content ">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class='fas fa-shield-alt'></i>@lang('messages.Cancellation Policy')</div>
                                        </div>
                                            <div class="booking-bil text-center">
                                                <img src="{{ asset(config('app.logo_dark')) }}" alt="{{ config('app.alt_logo') }}">
                                                <hr class="form-hr">
                                                <div class="modal-title">@lang('messages.Cancellation Policy')</div>
                                            </div>
                                            <div class="cancelation-policy-view">
                                                {!! $activity->cancellation_policy !!}
                                            </div>
                                        
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Modal Order Activity --------------------------------------------------------------------------------------------------------------- --}}
                        <div class="modal fade" id="order-activity-{{ $activity->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle">
                                                <i class="icon-copy fa fa-tag" aria-hidden="true"></i>@lang('messages.Create Order')
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-6">
                                                <div class="order-bil text-left">
                                                    <img src="{{ asset('storage/logo/bali-kami-tour-logo.png') }}" alt="Bali Kami Tour & Travel">
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
                                        <form id="create-order" action="/fadd-order" method="POST">
                                            @csrf
                                            <div class="modal-body pd-5">
                                                <div class="business-name">{{ $business->name }}</div>
                                                <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                                <hr class="form-hr">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="page-list"> @lang('messages.Order No')</div>
                                                                <div class="page-list"> @lang('messages.Order Date') </div>
                                                                <div class="page-list"> @lang('messages.Service') </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="page-list-value">
                                                                    {{ ': '.'ORD.' . date('Ymd', strtotime($now)) . '.ACT' . $orderno }}
                                                                </div>
                                                                <div class="page-list-value">
                                                                    {{ date('D, d-M-Y', strtotime($now)) }}
                                                                </div>
                                                                <div class="page-list-value">: @lang('messages.Activity')</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- Admin create order ================================================================= --}}
                                                    @canany(['posDev','posAuthor','posRsv'])
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="user_id" >Select Agent <span>*</span></label><br>
                                                                <div class="custom-select" style="width: 100%">
                                                                    <select name="user_id" class="custom-select @error('user_id') is-invalid @enderror" value="{{ old('user_id') }}" required>
                                                                        <option selected value="">Select Agent</option>
                                                                        @foreach ($agents as $agent)
                                                                            <option value="{{ $agent->id }}">{{ $agent->name." (".$agent->code.") @".$agent->office }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('user_id')
                                                                        <div class="alert-form">{{ $message }}</div>
                                                                    @enderror
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endcan
                                                {{-- Admin create order ================================================================= --}}
                                                </div>
                                                <hr class="form-hr">
                                                <div class="page-subtitle">@lang('messages.Activity')</div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <div class="page-list">@lang('messages.Activity')</div>
                                                                <div class="page-list">@lang('messages.Location')</div>
                                                                <div class="page-list">@lang('messages.Type')</div>
                                                            </div>
                                                            <div class="col-8">
                                                                <div class="page-list-value">{{ $activity->name }}</div>
                                                                <div class="page-list-value">{{ $activity->location }}</div>
                                                                <div class="page-list-value">{{ $activity->type }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <div class="page-list">@lang('messages.Duration')</div>
                                                                <div class="page-list">@lang('messages.Capacity')</div>
                                                            </div>
                                                            <div class="col-8">
                                                                <div class="page-list-value">{{ $activity->duration.' ' }}</div>
                                                                <div class="page-list-value">{{ $activity->qty . ' ' }}@lang('messages.Guest') </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="page-note">
                                                            <b>Include :</b> <br>
                                                            {!! $activity->include !!}
                                                            <hr class="form-hr">
                                                            @if ($activity->additional_info != "" )
                                                                <b>@lang('messages.Additional Information') :</b> <br>
                                                                {!! $activity->additional_info !!}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="page-subtitle">@lang('messages.Guest')</div>
                                                <div class="row m-b-18">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="number_of_guests">@lang('messages.Number of Guests') </label>
                                                            <input type="number" id="nog" onchange="calculate()"  min="1" max="{{ $activity->qty }}" name="number_of_guests" class="form-control @error('number_of_guests') is-invalid @enderror" placeholder="@lang('messages.Number of Guest')" value="1" required>
                                                            @error('number_of_guests')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="travel_date">@lang('messages.Activity Date') </label>
                                                            <input id="travel_date" name="travel_date" class="form-control datetimepicker @error('travel_date') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" type="text" required>
                                                            @error('travel_date')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="guest_detail">@lang('messages.Guest Detail') </label>
                                                            <textarea name="guest_detail" placeholder="@lang('messages.Enter the names of all guests!, ex: 1. Mr. name, 2. Mrs. name')" class="textarea_editor form-control bpage-radius-0" required>{{ old('guest_detail') }}</textarea>
                                                            @error('guest_detail')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="note">@lang('messages.Note')</label>
                                                            <textarea id="note" name="note" placeholder="@lang('messages.Optional')" class="textarea_editor form-control bpage-radius-0" value="{{ old('note') }}"></textarea>
                                                            @error('note')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 m-b-8">
                                                        <div class="box-price-kicked">
                                                            <div class="row">
                                                                <div class="col-6 col-md-6">
                                                                    @if (isset($bookingcode->code) or $promotion_price > 0)
                                                                        <div class="modal-text-price">@lang('messages.Price')</div>
                                                                        <div class="modal-text-price">@lang('messages.Number of Guests')</div>
                                                                        <div class="modal-text-price">@lang('messages.Normal Price')</div>
                                                                        <hr class="form-hr">
                                                                        @if (isset($bookingcode->code))
                                                                            <div class="modal-text-price">@lang('messages.Booking Code')</div>
                                                                        @endif
                                                                        @if ($promotion_price > 0)
                                                                            <div class="modal-text-price">@lang('messages.Promotion')</div>
                                                                        @endif
                                                                        <hr class="form-hr">
                                                                    @else
                                                                        <div class="modal-text-price">@lang('messages.Price')</div>
                                                                        <div class="modal-text-price">@lang('messages.Number of Guests')</div>
                                                                        <hr class="form-hr">
                                                                    @endif
                                                                    <div class="price-name">@lang('messages.Total Price')</div>
                                                                </div>
                                                                <div class="col-6 col-md-6 text-right">
                                                                    @if (isset($bookingcode->code) or $promotion_price > 0)
                                                                        <div class="modal-num-price">{{ currencyFormatUsd($normal_price) }}/@lang('messages.pax')</div>
                                                                        <div class="modal-num-price" id="number_of_guests">1</div>
                                                                        <div class="modal-num-price"><span id="normal_price">{{ $normal_price }}</span></div>
                                                                        <hr class="form-hr">
                                                                        @if (isset($bookingcode->code))
                                                                            <div class="kick-back">{{ "- $ ".number_format($bookingcode->discounts) }}</div>
                                                                        @endif
                                                                        @if ($promotion_price > 0)
                                                                            <div class="kick-back">{{ "- $ ".number_format($promotion_price) }}</div>
                                                                        @endif
                                                                        <hr class="form-hr">
                                                                    @else
                                                                        <div class="modal-num-price">{{ currencyFormatUsd($normal_price) }}/@lang('messages.pax')</div>
                                                                        <div class="modal-num-price"><span id="number_of_guests">1</span></div>
                                                                        <hr class="form-hr">
                                                                    @endif
                                                                    <div class="price-tag"><span id="final_price">{{ number_format($final_price) }}</span></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="notif-modal text-left">
                                                @lang('messages.Please make sure all the data is correct before you submit an order')
                                            </div>
                                            
                                            {{-- USER LOG ---------------------------------}}
                                            <input type="hidden" name="page" value="activity-detail">
                                            <input type="hidden" name="action" value="Create Order">
                                            {{-- END USER LOG ---------------------------------}}
                                            <input type="hidden" name="orderno" value="{{ 'ORD.' . date('Ymd', strtotime($now)) . '.ACT' . $orderno }}">
                                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                            <input type="hidden" name="name" value="{{ Auth::user()->name }}">
                                            <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                                            <input type="hidden" name="service" value="Activity">
                                            <input type="hidden" name="capacity" value="{{ $activity->qty }}">
                                            <input type="hidden" name="checkin" value="{{ date('Y-m-d', strtotime('+2 month', strtotime($now))) }}">
                                            <input type="hidden" name="itinerary" value="{{ $activity->itinerary }}">
                                            <input type="hidden" name="servicename" value="{{ $activity->partners->name }}">
                                            <input type="hidden" name="subservice" value="{{ $activity->name }}">
                                            <input type="hidden" name="subservice_id" value="{{ $activity->id }}">
                                            <input type="hidden" name="activity_type" value="{{ $activity->type }}">
                                            <input type="hidden" name="duration" value="{{ $activity->duration }}">
                                            <input type="hidden" name="additional_info" value="{{ $activity->additional_info }}">
                                            <input type="hidden" name="cancellation_policy" value="{{ $activity->cancellation_policy }}">
                                            <input type="hidden" name="location" value="{{ $activity->location }}">
                                            <input type="hidden" name="include" value="{{ $activity->include }}">
                                            <input type="hidden" id="price_pax" name="price_pax" value="{{ $normal_price }}">
                                            <input type="hidden" id="normal_price" name="normal_price" value="{{ $normal_price }}">
                                            <input type="hidden" id="promo_disc" name="promotion_disc" value="{{ $promotion_price }}">
                                            @if (isset($bookingcode->code))
                                                <input type="hidden" name="bookingcode_id" value="{{ $bookingcode->id }}">
                                                <input type="hidden" id="bk_disc" name="bk_disc" value="{{ $bookingcode->discounts }}">
                                            @else
                                                <input type="hidden" id="bk_disc" name="bk_disc" value=0>
                                            @endif
                                        </form>
                                        <div class="card-box-footer">
                                            <button type="submit" form="create-order" id="normal-reserve" onclick="myFunction()" class="btn btn-primary"><i class="fa fa-shopping-basket"></i> @lang('messages.Order')</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> @lang('messages.Cancel')</button>
                                        </div>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End Modal Order Activity  --------------------------------------------------------------------------------------------------------------- --}}
                    </div>
                </div>
                <div class="col-md-4 desktop">
                    <div class="card-box m-b-18">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fa fa-qrcode" aria-hidden="true"></i>@lang('messages.Booking Code')</div>
                        </div>
                        <div class="detail-item text-right">
                            <form action="/activity-detail" method="POST" role="search" style="padding:0px;">
                                @csrf
                                <div class="form-group m-b-8">
                                    @if (isset($bookingcode->code))
                                        <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ $bookingcode->code }}">
                                    @else
                                        <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ old('bookingcode') }}">
                                    @endif
                                    <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                                </div>
                                <button type="submit" class="btn btn-primary m-t-8"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Check Code')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ACTIVITY AROUND --}}
            @if (count($nearactivities)>0)
                <div class="card-box m-b-18">
                    <div class="card-box-title m-b-18">
                        <div class="subtitle"><i class="icon-copy fa fa-map-signs" aria-hidden="true"></i>@lang('messages.Activities around'){{ " ".$activity->location }}</div>
                    </div>
                    <div class="card-box-content">
                        @foreach ($nearactivities as $near_activity)
                            @php
                                $nprice_non_tax = (ceil($near_activity->contract_rate / $usdrates->rate))+$near_activity->markup;
                                $ntax = ceil(($taxes->tax/100) * $nprice_non_tax);
                                $nnormal_price = $nprice_non_tax + $ntax;
                                if (isset($bookingcode->code) or isset($promotions)) {
                                    if (isset($bookingcode->code)) {
                                        $nprice_per_pax = $nnormal_price;
                                        
                                        if (isset($promotions)) {
                                            $nfinal_price = $nnormal_price - $bookingcode->discounts - $promotion_price;
                                        }else{
                                            $nfinal_price = $nnormal_price - $bookingcode->discounts;
                                        }
                                    }else{
                                        $nprice_per_pax = $nnormal_price ;
                                        $nfinal_price = $nnormal_price  - $promotion_price;
                                    }
                                }else {
                                    $nprice_per_pax = $nnormal_price;
                                    $nfinal_price = $nnormal_price;
                                }
                            @endphp
                            <div class="card">
                                @if (isset($bookingcode->code))
                                    <a href="activity-{{ $near_activity->code }}-{{ $bookingcode->code }}">
                                @else
                                    <a href="activity-{{ $near_activity->code }}">
                                @endif
                                    <div class="card">
                                        <div class="image-container">
                                            <div class="first">
                                                <ul class="card-lable">
                                                    <li class="item">
                                                        <div class="meta-box">
                                                            <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
                                                            <p class="text">{{ $activity->location }}</p>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="product-detail-container">
                                                <ul class="card-meta-list-top">
                                                    <li class="card-meta-item">
                                                        <div class="meta-box">
                                                            @if ($near_activity->type == "Group")
                                                                <i class="icon-copy ion-ios-people"></i>
                                                                <p class="text">@lang('messages.'.$near_activity->type)</p>
                                                            @elseif ($near_activity->type == "Private")
                                                                <i class="icon-copy ion-ios-person"></i>
                                                                <p class="text">@lang('messages.'.$near_activity->type)</p>
                                                            @elseif ($near_activity->type == "Couple")
                                                                <i class="icon-copy ion-android-people"></i>
                                                                <p class="text">@lang('messages.'.$near_activity->type)</p>
                                                            @else
                                                            @endif
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            @if (isset($bookingcode->code))
                                                <a href="/activity-{{ $near_activity->code }}-{{ $bookingcode->code }}">
                                            @else
                                                <a href="/activity-{{ $near_activity->code }}">
                                            @endif
                                                <img src="{{ asset('storage/activities/activities-cover/' . $near_activity->cover) }}" class="img-fluid rounded thumbnail-image">
                                            </a>
                                            @if (isset($bookingcode->code) or $promotion_price >0)
                                                <div class="price-card-normal m-t-8">
                                                    {{"$ " . number_format($nnormal_price) }}
                                                </div>
                                                <div class="price-card m-t-27">
                                                    {{"$ " . number_format($nfinal_price) }}
                                                </div>
                                            @else
                                                <div class="price-card m-t-8">
                                                    {{"$ " . number_format($nfinal_price) }}
                                                </div>
                                            @endif
                                            @if (isset($bookingcode->code))
                                                <a href="/activity-{{ $near_activity->code }}-{{ $bookingcode->code }}">
                                            @else
                                                <a href="/activity-{{ $near_activity->code }}">
                                            @endif
                                                <div class="card-detail-title">{{ $near_activity->name }}</div>
                                            </a>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            @include('layouts.footer')
        </div>
	</div>
	@endsection
<script>
    function calculate(){
        var nog = document.getElementById('nog').value;
        var p_pax = document.getElementById('price_pax').value;
        var promo_disc = document.getElementById('promo_disc').value;
        var bk_disc = document.getElementById('bk_disc').value;
        var np = (p_pax * nog);
        var fp = (p_pax * nog)-promo_disc-bk_disc;
        var final_price = fp.toLocaleString('en-US');
        var normal_price = np.toLocaleString('en-US');
        document.getElementById("final_price").innerHTML = final_price;
        document.getElementById("number_of_guests").innerHTML = nog;
        document.getElementById("normal_price").innerHTML = normal_price;
        document.getElementById("normal_price").value = normal_price;
    }
</script>