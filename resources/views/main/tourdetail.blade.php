@section('title', __('messages.Tour'))
@section('content')
    @extends('layouts.head')
	<div class="mobile-menu-overlay"></div>
	<div class="main-container">
		<div class="pd-ltr-20">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title"><i class="icon-copy fa fa-briefcase"></i>&nbsp; @lang('messages.Tours')</div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">@lang('messages.Dashboard')</a></li>
                                <li class="breadcrumb-item"><a href="tours">@lang('messages.Tours')</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $tour->name }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
			@if (count($errors) > 0)
                <div class="alert-error-code">
                    <div class="alert alert-danger">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{{ $error }}</div></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            @if (\Session::has('danger'))
                <div class="alert-error-code">
                    <div class="alert alert-danger">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{!! \Session::get('danger') !!}</div></li>
                        </ul>
                    </div>
                </div>
            @endif
            @if (\Session::has('success'))
                <div class="alert-error-code">
                    <div class="alert alert-success">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{!! \Session::get('success') !!}</div></li>
                        </ul>
                    </div>
                </div>
            @endif
            @if ($bookingcode)
                
                @if ($bookingcode->status == "Invalid")
                    <div class="alert-error-code">
                        <div class="alert alert-danger">
                            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                            <ul>
                                <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Invalid Code')</div></li>
                            </ul>
                        </div>
                    </div>
                @endif
                @if ($bookingcode->status == "Expired")
                    <div class="alert-error-code">
                        <div class="alert alert-danger">
                            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                            <ul>
                                <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Expired Code')</div></li>
                            </ul>
                        </div>
                    </div>
                @endif
                @if ($bookingcode->status == "Used")
                    <div class="alert-error-code">
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
                            @if ($bookingcode->status == "Valid")
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
                                        <a href="/tour-{{ $tour->code }}">
                                            <button type="submit" class="btn-remove-code"><i class="fa fa-close" aria-hidden="true"></i></button>
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
                                                <button class="btn-remove-code" data-toggle="tooltip" data-placement="top" title='@lang('messages.Ongoing promotion'){{" ". $promotion->name." "}}@lang('messages.and get discounts'){{ " $".$promotion->discounts." " }}@lang('messages.until'){{ " ". date('d M y',strtotime($promotion->periode_end)) }}'><i class="fa fa-question" aria-hidden="true"></i></button>
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
                                                <button class="btn-remove-code" data-toggle="tooltip" data-placement="top" title='@lang('messages.Ongoing promotion'){{" ". $promotion->name." "}}@lang('messages.and get discounts'){{ " $".$promotion->discounts." " }}@lang('messages.until'){{ " ". date('d M y',strtotime($promotion->periode_end)) }}'><i class="fa fa-question" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            @endif
            <div class="row">
                <div class="col-md-4 m-b-18 mobile">
                    {{-- ATTENTIONS --}}
                    <div class="row">
                        @include('layouts.attentions')
                    </div>
                    {{-- BOOKING CODE --}}
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fa fa-qrcode" aria-hidden="true"></i>@lang('messages.Booking Code')</div>
                        </div>
                       <div class="text-right">
                            <form action="/tour-detail" method="POST" role="search" style="padding:0px;">
                                @csrf
                                <div class="form-group">
                                    @if (isset($bookingcode->code))
                                        <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ $bookingcode->code }}">
                                    @else
                                        <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ old('bookingcode') }}">
                                    @endif
                                    <input type="hidden" name="tour_id" value="{{ $tour->id }}">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Check Code')</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card-box m-b-18">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="fa fa-file-text"></i>{{ $tour->name." (".$tour->code.")" }}</div>
                        </div>
                        <div class="page-card">
                            <div class="card-banner m-b-18">
                                <img src="{{ asset ('storage/tours/tours-cover/' . $tour->cover) }}" alt="{{ $tour->name }}" loading="lazy">
                            </div>
                            <div class="card-content">
                                <div class="m-b-18">
                                    {!! $tour->description !!}
                                </div>
                                <div class="card-subtitle">@lang('messages.Destination')</div>
                                <div class="m-b-18">
                                    {!! $tour->destination !!}
                                </div>
                                <div class="modal-galery">
                                    @foreach ($tour->images as $image)
                                        <img src="{{ asset('/storage/tours/tours-galery/' . $image->image) }}" alt="">
                                    @endforeach
                                </div>
                                <div class="card-subtitle">@lang('messages.Itinerary')</div>
                                <div class="m-b-18">
                                    {!! $tour->itinerary !!}
                                </div>
                                <div class="card-subtitle">@lang('messages.Inclusions')</div>
                                <div class="m-b-18">
                                    {!! $tour->include !!}
                                </div>
                                <div class="card-subtitle">@lang('messages.Exclusions')</div>
                                <div class="m-b-18">
                                    {!! $tour->exclude !!}
                                </div>
                                @if ($tour->additional_info)
                                    <div class="card-subtitle">@lang('messages.Additional Information')</div>
                                    <div class="m-b-18">
                                        {!! $tour->additional_info !!}
                                    </div>
                                @endif
                                @if ($tour->cancellation_policy)
                                    <div class="card-subtitle">@lang('messages.Cancellation Policy')</div>
                                    <div class="m-b-18">
                                        {!! $tour->cancellation_policy !!}
                                    </div>
                                @endif
                                @if ($tour->terms_and_conditions)
                                    <div class="card-subtitle">@lang('messages.Terms and Conditions')</div>
                                    <div class="m-b-18">
                                        {!! $tour->terms_and_conditions !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if (isset($tour->prices))
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row" style="justify-content: flex-end;">
                                        <div class="col-12">
                                            <div class="card-box-subtitle m-t-0">
                                                <b>@lang('messages.Price')</b>
                                            </div>
                                            <table class="table stripe ">
                                                <tr>
                                                    <td class="width:5%;"><b>@lang('messages.No')</b></td>
                                                    <td class="width:15%;"><b>@lang('messages.Number of Guests')</b></td>
                                                    <td class="width:80%;"><b>@lang('messages.Price')/@lang('messages.pax')</b></td>
                                                </tr>
                                                @foreach ($tour->prices as $pr_no=>$tour_price)
                                                    <tr>
                                                        <td>{{ ++$pr_no }}</td>
                                                        <td>{{ $tour_price->min_qty." - ".$tour_price->max_qty }} @lang('messages.guests')</td>
                                                        <td>{{ currencyFormatUsd($tour_price->calculated_price) }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                   
                                    
                                    {{-- Modal Order Tour Package --------------------------------------------------------------------------------------------------------------- --}}
                                    <div class="modal fade" id="order-tour-{{ $tour_price->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="subtitle"><i class="fa fa-tags"></i>@lang('messages.Create Order')</div>
                                                    </div>
                                                
                                                    <div class="row">
                                                        <div class="col-6 col-md-6">
                                                            <div class="order-bil text-left">
                                                                <img src="{{ asset(config('app.logo_dark')) }}" alt="{{ config('app.alt_logo') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-6 flex-end">
                                                            <div class="label-title">@lang('messages.Order')</div>
                                                        </div>
                                                        <div class="col-md-12 text-right">
                                                            <div class="label-date float-right" style="width: 100%">
                                                                {{ dateFormat($now) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <form id="add-order-{{ $tour_price->id }}" action="/fadd-order" method="POST">
                                                        @csrf
                                                        <div class="modal-body pd-5">
                                                            <div class="business-name">{{ $business->name }}</div>
                                                            <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                                            <hr class="form-hr">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <div class="page-list">
                                                                                @lang('messages.Order No')
                                                                            </div>
                                                                            <div class="page-list">
                                                                                @lang('messages.Order Date')
                                                                            </div>
                                                                            <div class="page-list">
                                                                                @lang('messages.Service')
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <div class="page-list-value">
                                                                                {{ 'ORD.' . date('Ymd', strtotime($now)) . '.TP' . $ordernotours }}
                                                                            </div>
                                                                            <div class="page-list-value">
                                                                                {{ dateFormat($now) }}
                                                                            </div>
                                                                            <div class="page-list-value">
                                                                                @lang('messages.Tour Package')
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {{-- Admin create order ================================================================= --}}
                                                                @canany(['posDev','posAuthor','posRsv'])
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="user_id">Select Agent <span>*</span></label>
                                                                            <select name="user_id" class="custom-select @error('user_id') is-invalid @enderror" value="{{ old('user_id') }}" required>
                                                                                <option selected value="">Select Agent</option>
                                                                                @foreach ($agents as $agent)
                                                                                    <option class="option-list" value="{{ $agent->id }}">{{ $agent->name." (".$agent->code.") @".$agent->office }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('user_id')
                                                                                <div class="alert-form">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                @endcan
                                                                {{-- Admin create order ================================================================= --}}
                                                            </div>
                                                            <div class="page-subtitle">@lang('messages.Tour Package')</div>
                                                            <div class="content-body">
                                                                <div class="c-b-content">
                                                                    <div class="c-b-c-title">@lang('messages.Tour Package')</div>
                                                                    <div class="c-b-c-text">{{ $tour->name }}</div>
                                                                </div>
                                                                <div class="c-b-content">
                                                                    <div class="c-b-c-title">@lang('messages.Type')</div>
                                                                    <div class="c-b-c-text">{{ $tour->type }}</div>
                                                                </div>
                                                                <div class="c-b-content">
                                                                    <div class="c-b-c-title">@lang('messages.Duration')</div>
                                                                    <div class="c-b-c-text">{{ $tour->duration }}</div>
                                                                </div>
                                                            </div>
                                                            <div class="page-subtitle">@lang('messages.Guest Detail')</div>
                                                            <div class="row m-b-18">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="travel_date">@lang('messages.Travel Date') </label>
                                                                        <input id="travel_date" name="travel_date" class="form-control datetimepicker @error('travel_date') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" type="text" required>
                                                                        @error('travel_date')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                {{-- <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="number_of_guests">@lang('messages.Number of guest') </label>
                                                                        <select type="number" id="nog" onchange="calculate()"  min="2" max="100" name="number_of_guests" class="custom-select @error('number_of_guests') is-invalid @enderror" placeholder="@lang('messages.Number of Guest')"  required> 
                                                                            <option selected value="2">2 pax</option>
                                                                            @foreach ($tour->prices as $price)
                                                                                <option value="{{ $price->calculated_price }}">{{ $price->min_qty." - ". $price->max_qty.' pax' }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('number_of_guests')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div> --}}
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="number_of_guests">@lang('messages.Number of Guests') </label>
                                                                        <input type="number" id="nog" onchange="calculate()"  min="2" name="number_of_guests" class="form-control @error('number_of_guests') is-invalid @enderror" placeholder="@lang('messages.Number of Guests')" value="{{ old('number_of_guests') }}" required>
                                                                        @error('number_of_guests')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="pickup_location">@lang('messages.Pick up location') </label>
                                                                        <input type="text" id="pickup_location" name="pickup_location" class="form-control @error('pickup_location') is-invalid @enderror" placeholder="@lang('messages.ex'): @lang('messages.Hotel Name')/@lang('messages.Airport')" value="{{ old('pickup_location') }}" required>
                                                                        @error('pickup_location')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="dropoff_location">@lang('messages.Drop off location') </label>
                                                                        <input type="text" id="dropoff_location" name="dropoff_location" class="form-control @error('dropoff_location') is-invalid @enderror" placeholder="@lang('messages.ex'): @lang('messages.Hotel Name')/@lang('messages.Airport')" value="{{ old('dropoff_location') }}" required>
                                                                        @error('dropoff_location')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="guest_detail">@lang('messages.Guest Detail') <span> *</span></label>
                                                                        <textarea id="guest_detail" name="guest_detail" placeholder="ex: Mr. name, Mrs. name" class="textarea_editor form-control" value="{{ old('guest_detail') }}" required></textarea>
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
                                                                        <textarea id="note" name="note" placeholder="@lang('messages.Optional')" class="textarea_editor form-control" value="{{ old('note') }}"></textarea>
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
                                                                                <div class="normal-text-07">@lang('messages.Price')/@lang('messages.pax')</div>
                                                                                @if (isset($bookingcode->code) or $promotion_price >0)
                                                                                    <div class="normal-text">@lang('messages.Normal Price')</div>
                                                                                    <hr class="form-hr">
                                                                                    @if (isset($bookingcode->code))
                                                                                        <div class="normal-text">@lang('messages.Booking Code')</div>
                                                                                    @endif
                                                                                    @if ($promotion_price >0)
                                                                                        <div class="normal-text">@lang('messages.Promotion')</div>
                                                                                    @endif
                                                                                    <hr class="form-hr">
                                                                                @endif
                                                                                <div class="price-name">@lang('messages.Total Price')</div>
                                                                            </div>
                                                                            <div class="col-6 col-md-6 text-right">
                                                                                <div class="normal-text-07"><span id="tour_price_per_pax"></span></div>
                                                                                @if (isset($bookingcode->code) or $promotion_price >0)
                                                                                    <div class="promo-text"><span id="tour-normal-price"></span></div>
                                                                                    <div class="usd-rate-kicked"></div>
                                                                                    <hr class="form-hr">
                                                                                    @if (isset($bookingcode->code))
                                                                                        <div class="kick-back">{{ "- $ ".number_format($bookingcode->discounts) }}</div>
                                                                                    @endif
                                                                                    @if ($promotion_price >0)
                                                                                        <div class="kick-back">{{ "- $ ".number_format($promotion_price) }}</div>
                                                                                    @endif
                                                                                    <hr class="form-hr">
                                                                                @endif
                                                                                <div class="price-name-price"><span id="tour_final_price"></span></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="notif-modal text-left">
                                                            @lang('messages.Please make sure all the data is correct before you submit an order!')
                                                        </div>
                                                    </Form>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="add-order-{{ $tour_price->id }}" id="normal-reserve" class="btn btn-primary"><i class="fa fa-shopping-basket" aria-hidden="true"></i> @lang('messages.Order')</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                
                                </div>
                            </div>
                        @endif
                        <div class="card-box-footer">
                            <a href="#" data-toggle="modal" data-target="#order-tour-{{ $tour_price->id }}">
                                <button class="btn btn-primary"><i class="fa fa-shopping-basket" aria-hidden="true"></i> @lang('messages.Order')</button>
                            </a>
                        </div>
                        
                        {{-- <div class="card-box-footer">
                            
                            <a href="#" data-toggle="modal" data-target="#order-tour-{{ $tour->id }}">
                                <button class="btn btn-primary"><i class="fa fa-shopping-basket" aria-hidden="true"></i> @lang('messages.Order')</button>
                            </a>
                        </div> --}}
                        {{-- Modal Cancellation Policy --------------------------------------------------------------------------------------------------------------- --}}
                        <div class="modal fade" id="cancellation-policy-{{ $tour->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content ">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title">@lang('messages.Cancellation Policy')</div>
                                        </div>
                                            <div class="booking-bil text-center">
                                                <img src="{{ asset(config('app.logo_dark')) }}" alt="{{ config('app.alt_logo') }}">
                                                <hr class="form-hr">
                                                <div class="modal-title">@lang('messages.Cancellation Policy')</div>
                                            </div>
                                            <div class="cancelation-policy-view">
                                                {!! $tour->cancellation_policy !!}
                                            </div>
                                        
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="col-md-4 desktop">
                    {{-- ATTENTIONS --}}
                    <div class="row">
                        @include('layouts.attentions')
                    </div>
                    {{-- BOOKING CODE --}}
                    <div class="card-box m-b-18">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fa fa-qrcode" aria-hidden="true"></i>@lang('messages.Booking Code')</div>
                        </div>
                        <div class="text-right">
                            <form action="/tour-detail" method="POST" role="search" style="padding:0px;">
                                @csrf
                                <div class="form-group m-b-8">
                                    @if (isset($bookingcode->code))
                                        <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ $bookingcode->code }}">
                                    @else
                                        <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ old('bookingcode') }}">
                                    @endif
                                    <input type="hidden" name="tour_id" value="{{ $tour->id }}">
                                </div>
                                <button type="submit" class="btn btn-primary m-t-8"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Check Code')</button>
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>
            {{-- SIMILAR TOUR PACKAGE --}}
            @if (count($neartours) > 0)
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy fa fa-map-signs" aria-hidden="true"></i>@lang('messages.Similar Tour Package')</div>
                    </div>
                    <div class="card-box-content">
                        @foreach ($neartours as $near_tour)
                            <div class="card">
                                @if (isset($bookingcode->code))
                                    <a href="tour-{{ $near_tour->code }}-{{ $bookingcode->code }}">
                                @else
                                    <a href="tour-{{ $near_tour->code }}">
                                @endif
                                    <div class="image-container">
                                        <div class="first">
                                            <ul class="card-lable">
                                                <li class="item">
                                                    <div class="meta-box">
                                                        <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
                                                        <p class="text">{{ $near_tour->location }}</p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        @if (isset($bookingcode->code))
                                            <a href="/tour-{{ $near_tour->code }}-{{ $bookingcode->code }}">
                                        @else
                                            <a href="/tour-{{ $near_tour->code }}">
                                        @endif
                                            <img src="{{ asset('storage/tours/tours-cover/' . $near_tour->cover) }}" class="img-fluid rounded thumbnail-image">
                                        </a>
                                        @if (isset($bookingcode->code))
                                            <a href="/tour-{{ $near_tour->code }}-{{ $bookingcode->code }}">
                                        @else
                                            <a href="/tour-{{ $near_tour->code }}">
                                        @endif
                                            <div class="card-detail-title">{{ $near_tour->name }}</div>
                                        </a>
                                        
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
        var bookingcode_disc = @json($bookingcode_disc);
        var promotion_disc = @json($promotion_price);
        // var promotion_disc = document.getElementById('promo_disc').value;
        var cprice = document.getElementById('count_tour_prices').value;
        var price_pax_0 = document.getElementById('tour_price_pax_0').value;
        var price_pax_1 = document.getElementById('tour_price_pax_1').value;
        var price_pax_2 = document.getElementById('tour_price_pax_2').value;
        var qty_0 = document.getElementById('qty_0').value;
        var qty_1 = document.getElementById('qty_1').value;
        var qty_2 = document.getElementById('qty_2').value;
        var tp = 0;
        var ppp = 0;
        var t_price = 0;
        var normal_price = 0;

        if (nog < 5 && qty_0 < 5) {
            tp = (nog * price_pax_0);
            ppp = price_pax_0;
        } else if(nog < 10 && qty_0 < 10) {
            tp = (nog * price_pax_1);
            ppp = price_pax_1;
        } else if(nog < 17 && qty_0 < 17) {
            tp = (nog * price_pax_2);
            ppp = price_pax_2;
        } else{
            tp = (nog * price_pax_2);
            ppp = price_pax_2;
        } 

        if (bookingcode_disc > 0) {
            t_price = tp - bookingcode_disc;
            normal_price = tp;
        }else{
            t_price = tp;
            normal_price = tp;
        }

        if (promotion_disc > 0) {
            tot_price = t_price - promotion_disc;
            normal_price = tp;
        }else{
            tot_price = t_price;
            normal_price = tp;
        }

        var normalprice = normal_price.toLocaleString('en-US');
        var total_price = tot_price.toLocaleString('en-US');
       
        document.getElementById("tour_price_per_pax").innerHTML = ppp;
        document.getElementById("price_per_pax").value = ppp;
        document.getElementById("tour_final_price").innerHTML = total_price;
        document.getElementById("htour_final_price").value = total_price;
        document.getElementById("tour-normal-price").innerHTML = normalprice;
        document.getElementById("val-tour-normal-price").value = normalprice;
    }
</script>