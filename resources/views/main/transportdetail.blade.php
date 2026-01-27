@section('title', __('messages.Transport'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        @php
            $no_da = 0;
            $no_tr = 0;
            $no_as = 0;
            if (isset($promotions)){
                $pr = count($promotions);
                $promotion_price = 0;
                for ($i=0; $i < $pr; $i++) { 
                    $promotion_price = $promotion_price + $promotions[$i]->discounts;
                }
            }else{
                $promotion_price = 0;
            }
            if ($find_bookingcode) {
                $bookingcode_discounts = $find_bookingcode->discounts;
            }else{
                $bookingcode_discounts = 0;
            }
        @endphp
        <div class="pd-ltr-20">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title"><i class="micon fa fa-car"></i> @lang('messages.Transport')</div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">@lang('messages.Dashboard')</a></li>
                                <li class="breadcrumb-item"><a href="transports">@lang('messages.Transport')</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $transport->brand." ".$transport->name }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
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
            <div class="row" id="bookingcode_promotion">
                <div class="col-12 promotion-bookingcode">
                    @if (isset($bookingcode->code))
                        <div class="bookingcode-card">
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
                                <a href="/transport-{{ $transport->code }}">
                                    <button class="btn-remove-code m-t-10"><i class="fa fa-close" aria-hidden="true"></i></button>
                                </a>
                            </div>
                        </div>
                        @if (isset($promotions))
                            @foreach ($promotions as $promotion)
                                <div class="bookingcode-card">
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
                                <div class="bookingcode-card">
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
            <div class="row">
                <div class="col-md-4 mobile">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fa fa-qrcode" aria-hidden="true"></i>@lang('messages.Booking Code')</div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <form action="/transport-detail" method="POST" role="search" style="padding:0px;">
                                    @csrf
                                    <div class="form-group">
                                        @if (isset($bookingcode->code))
                                            <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ $bookingcode->code }}">
                                        @else
                                            <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ old('bookingcode') }}">
                                        @endif
                                        <input type="hidden" name="transport_id" value="{{ $transport->id }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary" style="float: right;"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Check Code')</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card-box m-b-18">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fa fa-car" aria-hidden="true"></i>{{ $transport->name }}</div>
                        </div>
                        <div class="product-detail-wrap">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="page-card">
                                        <div class="first">
                                            <ul class="card-lable">
                                                <li class="item">
                                                    <div class="meta-box">
                                                        @if ($transport->type == "Car")
                                                            <i class="icon-copy ion-android-car"></i>@lang('messages.'.$transport->type)
                                                        @elseif ($transport->type == "Micro Bus")
                                                            <i class="icon-copy ion-android-bus"></i>@lang('messages.'.$transport->type)
                                                        @elseif ($transport->type == "Bus")
                                                            <i class="icon-copy ion-android-bus"></i>@lang('messages.'.$transport->type)
                                                        @elseif ($transport->type == "Luxuri")
                                                        <i class="icon-copy ion-android-car"></i>@lang('messages.'.$transport->type)
                                                        @else
                                                        @endif
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <figure class="card-banner">
                                            <div class="image-container-transport">
                                                <img class="trasport-img obj-scale-down" src="{{ asset ('storage/transports/transports-cover/' . $transport->cover) }}" alt="{{ $transport->name }}" loading="lazy">
                                            </div>
                                        </figure>
                                        <div class="card-content">
                                            <div class="row ">
                                                <div class="col-6">
                                                    <div class="card-subtitle">@lang('messages.Capacity'):</div>
                                                    <p>{{ $transport->capacity.' ' }}@lang('messages.Seat')</p>
                                                </div>
                                                @if ($transport->include != "")
                                                    <div class="col-6">
                                                        <div class="card-subtitle">@lang('messages.Include'):</div>
                                                        <p>{!! $transport->include !!}</p>
                                                    </div>
                                                @endif
                                                @if ($transport->additional_info != "")
                                                    <div class="col-sm-6">
                                                        <div class="card-subtitle">@lang('messages.Additional Informations'):</div>
                                                        <p>{!! $transport->additional_info !!}</p>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                @if ($transport->cancellation_policy != "")
                                    <div class="pos-cancelation">
                                        <div class="btn-cancelation-policy" data-toggle="tooltip" data-placement="left" title="Cancelation Policy">
                                            <a href="#" data-toggle="modal" data-target="#cancellation-policy-{{ $transport->id }}">
                                                <i class="icon-copy fa fa-info" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                        {{-- Modal Cancellation Policy --------------------------------------------------------------------------------------------------------------- --}}
                                        <div class="modal fade" id="cancellation-policy-{{ $transport->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content ">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fa fa-shield" aria-hidden="true"></i>@lang('messages.Cancellation Policy')</div>
                                                        </div>
                                                            <div class="booking-bil text-center">
                                                                <img src="{{ asset(config('app.logo_dark')) }}" alt="{{ config('app.alt_logo') }}">
                                                                <hr class="form-hr">
                                                                <div class="modal-title">@lang('messages.Cancellation Policy')</div>
                                                            </div>
                                                            <div class="cancelation-policy-view">
                                                                {!! $transport->cancellation_policy !!}
                                                            </div>
                                                        
                                                        <div class="card-box-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-12">
                                    @if (count($transport->prices) > 0)
                                        <div class="card-box-subtitle">
                                            <div class="subtitle"><i class="icon-copy fa fa-gear" aria-hidden="true"></i>@lang('messages.Service')</div>
                                        </div>
                                        <div class="card-content">

                                            <div class="tab">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    @if (isset($prices_airport_shuttle))
                                                        @if (count($prices_airport_shuttle)>0)
                                                            <li class="nav-item">
                                                                <a class="nav-link active"  data-toggle="tab" href="#airport-shuttle" role="tab" aria-selected="false">@lang('messages.Airport Shuttle')</a>
                                                            </li>
                                                        @endif
                                                    @endif
                                                    @if (isset($prices_transfers))
                                                        @if (count($prices_transfers)>0)
                                                            <li class="nav-item">
                                                                <a class="nav-link"  data-toggle="tab" href="#transfers" role="tab" aria-selected="false">@lang('messages.Transfers')</a>
                                                            </li>
                                                        @endif
                                                    @endif
                                                    @if (isset($prices_transfers))
                                                        @if (count($prices_daily_rent)>0)
                                                            <li class="nav-item">
                                                                <a class="nav-link"  data-toggle="tab" href="#daily-rent" role="tab" aria-selected="false">@lang('messages.Daily Rent')</a>
                                                            </li>
                                                        @endif
                                                    @endif
                                                </ul>
                                                <div class="tab-content">
                                                    @if (isset($prices_daily_rent))
                                                        @if (count($prices_daily_rent)>0)
                                                            <div class="tab-pane fade" id="daily-rent" role="tabpanel">
                                                                <table id="tbPriceDr" class="data-table table m-t-0" >
                                                                    <thead>
                                                                        <tr>
                                                                            <th data-priority="1" style="width: 5%;">@lang('messages.No')</th>
                                                                            <th data-priority="3" class="datatable-nosort" style="width: 50%;">@lang('messages.Destination')</th>
                                                                            <th data-priority="3" class="datatable-nosort" style="width: 15%;">@lang('messages.Duration')</th>
                                                                            <th data-priority="1" class="datatable-nosort" style="width: 15%;">@lang('messages.Price')</th>
                                                                            <th data-priority="3" class="datatable-nosort" style="width: 15%;">@lang('messages.Action')</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($prices_daily_rent as $price_dr)
                                                                            <tr>
                                                                                <td>
                                                                                    <p>{{ ++$no_da }}</p>
                                                                                </td>
                                                                                <td>
                                                                                    <p>{{ $price_dr->src }}</p>
                                                                                </td>
                                                                                <td>
                                                                                    <p>{{ $price_dr->duration." " }}@lang('messages.Hours')</p>
                                                                                </td>
                                                                                <td>
                                                                                    @if (isset($bookingcode) || $promotion_price > 0)
                                                                                        <div class="normal-price"><p>{{ currencyFormatUsd($price_dr['final_price']) }}</p></div>
                                                                                        <div class="code-price"><p>{{ currencyFormatUsd($price_dr['final_price'] - $promotion_price - $bookingcode_discounts) }}</p></div>
                                                                                    @else
                                                                                        <p>{{ currencyFormatUsd($price_dr['final_price']) }}</p>
                                                                                    @endif
                                                                                </td>
                                                                                <form id="order-transport-{{ $price_dr->id }}" action="/order-transport-{{ $price_dr->id }}" method="POST">
                                                                                    @csrf
                                                                                    @if (isset($bookingcode->id))
                                                                                        <input type="hidden" name="bookingcode" value="{{ $bookingcode->code }}">
                                                                                    @endif
                                                                                </form>
                                                                                <td>
                                                                                    <button type="submit" form="order-transport-{{ $price_dr->id }}" style="float: right" class="btn btn-primary"><i class="fa fa-shopping-basket"></i> @lang('messages.Order')</button>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (isset($prices_airport_shuttle))
                                                        @if (count($prices_airport_shuttle)>0)
                                                            <div class="tab-pane fade active show" id="airport-shuttle" role="tabpanel">
                                                                <div class="row m-t-8">
                                                                    <div class="col-md-12 flex-right">
                                                                        <div class="search-item">
                                                                            <input id="searchPriceAsByDst" type="text" onkeyup="searchPriceAsByDst()" class="form-control" placeholder="@lang('messages.Source or Destination')">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <table id="tbPriceAs" class="data-table table m-t-0" >
                                                                    <thead>
                                                                        <tr>
                                                                            <th data-priority="1" style="width: 5%;">@lang('messages.No')</th>
                                                                            <th data-priority="1" class="datatable-nosort" style="width: 50%;">@lang('messages.Src') <-> @lang('messages.Dst')</th>
                                                                            <th data-priority="3" class="datatable-nosort" style="width: 15%;">@lang('messages.Duration')</th>
                                                                            <th data-priority="1" class="datatable-nosort" style="width: 15%;">@lang('messages.Price')</th>
                                                                            <th data-priority="3" class="datatable-nosort" style="width: 15%;">@lang('messages.Action')</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($prices_airport_shuttle as $price_as)
                                                                            <tr>
                                                                                <td>
                                                                                    <p>{{ ++$no_as }}</p>
                                                                                </td>
                                                                                <td>
                                                                                    <p>{{ $price_as->src }} - {{ $price_as->dst }}</p>
                                                                                </td>
                                                                                <td>
                                                                                    <p>{{ $price_as->duration." " }}@lang('messages.Hours')</p>
                                                                                </td>
                                                                                <td>
                                                                                    @if ($bookingcode || $promotion_price > 0)
                                                                                        <div class="normal-price"><p>{{ currencyFormatUsd($price_as['final_price']) }}</p></div>
                                                                                        <div class="code-price"><p>{{ currencyFormatUsd($price_as['final_price'] - $promotion_price - $bookingcode_discounts) }}</p></div>
                                                                                    @else
                                                                                        <p>{{ currencyFormatUsd($price_as['final_price']) }}</p>
                                                                                    @endif
                                                                                </td>
                                                                                <form id="order-transport-{{ $price_as->id }}" action="/order-transport-{{ $price_as->id }}" method="POST">
                                                                                    @csrf
                                                                                    @if (isset($bookingcode->id))
                                                                                        <input type="hidden" name="bookingcode" value="{{ $bookingcode->code }}">
                                                                                    @endif
                                                                                </form>
                                                                                <td>
                                                                                    <button type="submit" form="order-transport-{{ $price_as->id }}" style="float: right" class="btn btn-primary"><i class="fa fa-shopping-basket"></i> @lang('messages.Order')</button>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (isset($prices_transfers))
                                                        @if (count($prices_transfers)>0)
                                                            <div class="tab-pane fade" id="Transfers" role="tabpanel">
                                                                <div class="row m-t-8">
                                                                    <div class="col-md-12 flex-right">
                                                                        <div class="search-item">
                                                                            <input id="searchPriceTrByDst" type="text" onkeyup="searchPriceTrByDst()" class="form-control" placeholder="@lang('messages.Source or Destination')">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <table id="tbPriceTr" class="data-table table m-t-0" >
                                                                    <thead>
                                                                        <tr>
                                                                            <th data-priority="1" style="width: 5%;">@lang('messages.No')</th>
                                                                            <th data-priority="1" class="datatable-nosort" style="width: 50%;">@lang('messages.Src') <-> @lang('messages.Dst')</th>
                                                                            <th data-priority="3" class="datatable-nosort" style="width: 15%;">@lang('messages.Duration')</th>
                                                                            <th data-priority="1" class="datatable-nosort" style="width: 15%;">@lang('messages.Price')</th>
                                                                            <th data-priority="3" class="datatable-nosort" style="width: 15%;">@lang('messages.Action')</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($prices_transfers as $price_tr)
                                                                            <tr>
                                                                                <td>
                                                                                    <p>{{ ++$no_tr }}</p>
                                                                                </td>
                                                                                <td>
                                                                                    <p>{{ $price_tr->src." <-> ".$price_tr->dst }}</p>
                                                                                </td>
                                                                                <td>
                                                                                    <p>{{ $price_tr->duration." " }}@lang('messages.Hours')</p>
                                                                                </td>
                                                                                <td>
                                                                                    @if (isset($bookingcode->discounts) or $promotion_price > 0)
                                                                                        <div class="normal-price"><p>{{ currencyFormatUsd($price_tr['final_price']) }}</p></div>
                                                                                        <div class="code-price"><p>{{ currencyFormatUsd($price_tr['final_price'] - $promotion_price - $bookingcode->discounts) }}</p></div>
                                                                                    @else
                                                                                        <p>{{ currencyFormatUsd($price_tr['final_price']) }}</p>
                                                                                    @endif
                                                                                </td>
                                                                                <form id="order-transport-{{ $price_tr->id }}" action="/order-transport-{{ $price_tr->id }}" method="POST">
                                                                                    @csrf
                                                                                    @if (isset($bookingcode->id))
                                                                                        <input type="hidden" name="bookingcode" value="{{ $bookingcode->code }}">
                                                                                    @endif
                                                                                </form>
                                                                                <td>
                                                                                    <button type="submit" form="order-transport-{{ $price_tr->id }}" style="float: right" class="btn btn-primary"><i class="fa fa-shopping-basket"></i> @lang('messages.Order')</button>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 desktop">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fa fa-qrcode" aria-hidden="true"></i>@lang('messages.Booking Code')</div>
                        </div>
                        <div class="detail-item">
                            <div class="row">
                                <div class="col-md-12">
                                    <form action="/transport-detail" method="POST" role="search" style="padding:0px;">
                                        @csrf
                                        <div class="form-group">
                                            @if (isset($bookingcode->code))
                                                <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ $bookingcode->code }}">
                                            @else
                                                <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ old('bookingcode') }}">
                                            @endif
                                            <input type="hidden" name="transport_id" value="{{ $transport->id }}">
                                        </div>
                                        <button type="submit" class="btn btn-primary" style="float: right;"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Check Code')</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if (count($similartransports) > 0)
                    <div class="col-md-12">
                        <div class="card-box">
                            <div class="pages-subtitle">
                                @lang('messages.Similar to') {{ $transport->name }}
                                <span>@lang('messages.Availability') {{ count($similartransports) }}</span>
                            </div>
                            <div class="card-box-content">
                                @foreach ($similartransports as $transports)
                                        <div class="card">
                                            <div class="image-container-transport">
                                                <div class="first">
                                                    <ul class="card-lable">
                                                        <li class="item">
                                                            <div class="meta-box">
                                                            <i class="icon-copy ion-ios-person"></i> {{ $transports->capacity. " " }}@lang('messages.Seat')
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                @if ($promotion_price > 0)
                                                    <div class="price-card m-t-8">
                                                        @lang('messages.Discounts') {{ currencyFormatUsd($promotion_price + $bookingcode_discounts) }}
                                                    </div>
                                                @endif
                                                @if (isset($bookingcode->code))
                                                    <a href="/transport-{{ $transports->code }}-{{ $bookingcode->code }}">
                                                @else
                                                    <a href="/transport-{{ $transports->code }}">
                                                @endif
                                                    <img src="{{ asset('storage/transports/transports-cover/' . $transports->cover) }}" class="img-fluid rounded thumbnail-image">
                                                    <div class="card-detail-title">{{ $transports->name }}</div>
                                                </a>
                                            </div>
                                        </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @include('layouts.footer')
    </div>
@endsection
<script>
    function searchPriceTrByDst() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchPriceTrByDst");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbPriceTr");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
    function searchPriceAsByDst() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchPriceAsByDst");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbPriceAs");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>