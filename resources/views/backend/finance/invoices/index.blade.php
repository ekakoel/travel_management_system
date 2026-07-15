@section('title', __('messages.Invoice'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20 p-b-18">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <i class="icon-copy fa fa-file-text-o" aria-hidden="true"></i> @lang('messages.Invoices')
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/admin-panel">@lang('messages.Admin Panel')</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">@lang('messages.Invoices')</li>
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
                {{-- ACTIVE INVOICE  ============================================================================================================================= --}}
                <div class="row">
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 mobile">
                        <div class="row">
                            @include('layouts.attentions')
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-box mb-30 p-b-14">
                            <div class="search-container">
                                <div class="row">
                                    <div class="input-group">
                                        <div class="col-md-12">
                                            <div class="search-title">Search tools</div>
                                        </div>
                                        <div class="col-md-6 p-b-8">
                                            <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                            <input id="searchInvByNo" type="text" onkeyup="searchInvByNo()" class="form-control" name="search-byno" placeholder="Find Invoice by No">
                                        </div>
                                        <div class="col-md-6 p-b-8">
                                            <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                            <input id="searchInvByAgn" type="text" onkeyup="searchInvByAgn()" class="form-control" name="search-byagn" placeholder="Sort Infoice by agent">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <table id="tb_inv" class="data-table table">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 25%;">Invoice</th>
                                        <th style="width: 20%;">Due Date</th>
                                        <th style="width: 25%;">Agent</th>
                                        <th style="width: 15%;">Amount</th>
                                        <th style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $no => $inv)
                                    @php
                                        $reservation = $rsv->where('id',$inv->rsv_id)->first();
                                        $agent = Auth::user()->where('id',$reservation->agn_id)->first();
                                    @endphp
                                        <tr>
                                            <td><p>{{ ++$no }}</p></td>
                                            <td>
                                                <p>{{ $inv->inv_no }}</p>
                                                <p>{{ "Date: ".dateFormat($inv->created_at) }}</p>
                                            </td>
                                            <td><p>{{ dateFormat($inv->due_date) }}</p></td>
                                            <td><p>{{ $agent->name }}</p></td>
                                            <td>
                                                @if ($inv->total_usd > 0)
                                                    <p>{{ currencyFormatUsd($inv->total_usd) }}</p>
                                                @else
                                                    <p>$ 0</p>
                                                @endif
                                                
                                            </td>
                                            
                                            <td>
                                                <div class="row">
                                                    <div class="col-2">
                                                        <a href="#" data-toggle="modal" data-target="#invoice-{{ $inv->id }}"> <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail Invoice"><i class="icon-copy fa fa-eye" aria-hidden="true"></i></button></a>
                                                    </div>
                                                    <div class="col-2">
                                                        <a href="/inv-det-{{ $inv->id }}" data-toggle="tooltip"> <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Edit Invoice"><i class="icon-copy ion-compose"></i></button></a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    
                                    {{-- Modal Detail Invoice --------------------------------------------------------------------------------------------------------------- --}}
                                    <div class="modal fade" id="invoice-{{ $inv->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="order-bil text-left">
                                                            <img src="images/balikami/bali-kami-tour-logo.png"
                                                                alt="Bali Kami Tour & Travel">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <div class="inv-head-con">

                                                            <div class="inv-title">INVOICE</div>
                                                            <p>{{ 'Invoice No: '. $inv->inv_no }}</p>
                                                            <p>{{ 'Invoice Date: '. date('D d M Y', strtotime($inv->created_at)) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-body pd-5">
                                                    <div class="business-name">{{ $business->name }}</div>
                                                    <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                                    <hr class="form-hr">
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <div class="row">
                                                                <div class="col-4">
                                                                    
                                                                    <div class="order-list">Invoice No</div>
                                                                    <div class="order-list">Invoice Date</div>
                                                                    <div class="order-list">Due Date</div>
                                                                    
                                                                </div>
                                                                <div class="col-8">
                                                                    
                                                                    <div class="order-list-value">
                                                                        {{ $inv->inv_no }}
                                                                    </div>
                                                                    <div class="order-list-value">
                                                                        {{ date('D d M Y', strtotime($inv->created_at)) }}
                                                                    </div>
                                                                    <div class="order-list-value">
                                                                        <?php 
                                                                            $datenow = date('ymd', strtotime($now));
                                                                            $inv_date = date("ymd", strtotime($inv->created_at));
                                                                            $Payment deadline = date("ymd", strtotime('+'.$inv->due_date.'days', strtotime($inv->created_at)));
                                                                            $day_left = $Payment deadline-$datenow;
                                                                        ?>
                                                                        {{ date("ymd", strtotime($Payment deadline)) }}<br>
                                                                        {{-- {{ $inv_date }}<br>
                                                                        
                                                                        {{ $inv->due_date }}
                                                                        {{ $day_left }} --}}
                                                                        {{-- {{ date('D d M Y', strtotime($Payment deadline)) }}<br> --}}
                                                                        {{ date('ymd', strtotime($Payment deadline)).' ('.$day_left.' days left)' }}
                                                                    </div>
                                                                
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            @if ($inv->status == "Active")
                                                                <div class="order-status" style="color: rgb(0, 156, 21)">{{ $inv->status }} <span>Status:</span></div>
                                                            @elseif ($inv->status == "Waiting")
                                                                <div class="order-status" style="color: blue">{{ $inv->status }} <span>Status:</span></div>
                                                            @elseif ($inv->status == "Rejected")
                                                                <div class="order-status" style="color: rgb(160, 0, 0)">{{ $inv->status }} <span>Status:</span></div>
                                                            @else
                                                                <div class="order-status" style="color: rgb(48, 48, 48)">{{ $inv->status }} <span>Status:</span></div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    {{-- AGENT DETAIL ===================================================================================================================== --}}
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="order-subtitle">Agent Details</div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="order-list">Name</div>
                                                                            <div class="order-list">Office</div>
                                                                            <div class="order-list">Phone</div>
                                                                            <div class="order-list">Email</div>
                                                                        </div>
                                                                        <div class="col-9">
                                                                            <div class="order-list-value">
                                                                                @if (Auth::user()->name == "")
                                                                                <i style="color: red;">: Not available</i> 
                                                                                @else
                                                                                    {{ ': ' . Auth::user()->name }}
                                                                                @endif
                                                                            </div>
                                                                            <div class="order-list-value">
                                                                                @if (Auth::user()->office == "")
                                                                                <i style="color: red;">: Not available</i> 
                                                                                @else
                                                                                    {{ ': ' . Auth::user()->office }}
                                                                                @endif
                                                                            </div>
                                                                            <div class="order-list-value">
                                                                                @if (Auth::user()->phone == "")
                                                                                <i style="color: red;">: Not available</i> 
                                                                                @else
                                                                                    {{ ': ' . Auth::user()->phone }}
                                                                                @endif
                                                                            </div>
                                                                            <div class="order-list-value">
                                                                                @if (Auth::user()->email == "")
                                                                                <i style="color: red;">: Not available</i> 
                                                                                @else
                                                                                    {{ ': ' . Auth::user()->email  }}
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="order-subtitle">Guest Details</div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <div class="order-list">Guest Name</div>
                                                                            <div class="order-list">Phone</div>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <div class="order-list-value">
                                                                                @if ($inv->gst_id == "")
                                                                                    <i style="color: red;">: Not available</i> 
                                                                                @else
                                                                                    {{ $inv->gst_id }}
                                                                                @endif
                                                                            </div>
                                                                            <div class="order-list-value">
                                                                                @if ($inv->gst_phone == "")
                                                                                    <i style="color: red;">: Not available</i> 
                                                                                @else
                                                                                    {{ $$inv->gst_phone }}
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- GUEST DETAIL ===================================================================================================================== --}}
                                                    @if ($inv->service == "Transport")
                                                    @else
                                                        <div class="order-subtitle">Guest Details <span>客人详情</span></div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-3">
                                                                        <div class="order-list">Number of guest</div>
                                                                        <div class="order-list">Guest name</div>
                                                                    </div>
                                                                    <div class="col-9">
                                                                        <div class="order-list-value">{{ $inv->number_of_guests." Guests" }}</div>
                                                                        <div class="order-list-value">{{ $inv->guest_detail }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    {{-- FLIGHT DETAIL ===================================================================================================================== --}}
                                                    @if ($inv->service == "Transport")
                                                    @elseif ($inv->service == "Activity")
                                                    @elseif ($inv->service == "Tour Package")
                                                    @else
                                                        <div class="order-subtitle">Flight Details <span>航班详情</span></div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-3">
                                                                        <div class="order-list"> Arrival Flight</div>
                                                                        <div class="order-list"> Arrival Time</div>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <div class="order-list-value">
                                                                            {{ $inv->arrival_flight }}
                                                                        </div>
                                                                        <div class="order-list-value">
                                                                            {{ $inv->arrival_time }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <div class="order-list"> Departure Flight</div>
                                                                        <div class="order-list"> Departure Time</div>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <div class="order-list-value">
                                                                            {{ $inv->departure_flight }}
                                                                        </div>
                                                                        <div class="order-list-value">
                                                                            {{ $inv->departure_time }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    {{-- ORDER DETAIL ===================================================================================================================== --}}
                                                    <div class="order-subtitle">Invoice Details<span> 订单详细信息</span></div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                {{-- HOTEL ===================================================================================================================== --}}
                                                                @if ($inv->service == "Hotel")
                                                                    <div class="col-6">
                                                                        <div class="order-list">Hotel</div>
                                                                        <div class="order-list">Room</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="order-list-value">{{ $inv->servicename }}</div>
                                                                        <div class="order-list-value">{{ $inv->subservice }}</div>
                                                                    </div>
                                                                @elseif ($inv->service == "Hotel Promo")
                                                                    <div class="col-6">
                                                                        <div class="order-list">Hotel</div>
                                                                        <div class="order-list">Promo</div>
                                                                        <div class="order-list">Room</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="order-list-value">{{ $inv->servicename }}</div>
                                                                        <div class="order-list-value">{{ $inv->promo_name }}</div>
                                                                        <div class="order-list-value">{{ $inv->subservice }}</div>
                                                                    </div>
                                                                @elseif ($inv->service == "Hotel Package")
                                                                    <div class="col-6">
                                                                        <div class="order-list">Hotel</div>
                                                                        <div class="order-list">Package</div>
                                                                        <div class="order-list">Room</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="order-list-value">{{ $inv->servicename }}</div>
                                                                        <div class="order-list-value">{{ $inv->package_name }}</div>
                                                                        <div class="order-list-value">{{ $inv->subservice .' Room'}}</div>
                                                                    </div>
                                                                {{-- TOUR PACKAGE ===================================================================================================================== --}}    
                                                                @elseif ($inv->service == "Tour Package")
                                                                    <div class="col-6">
                                                                        <div class="order-list">Tour Package</div>
                                                                        <div class="order-list">Price</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="order-list-value">{{ $inv->servicename }}</div>
                                                                        <div class="order-list-value">{{ ': $ ' . $inv->price_pax . ' /Pax' }}</div>
                                                                    </div>
                                                                {{-- ACTIVITY ===================================================================================================================== --}} 
                                                                @elseif ($inv->service == "Activity")
                                                                    <div class="col-6">
                                                                        <div class="order-list">Activity</div>
                                                                        <div class="order-list">Price</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="order-list-value">{{ $inv->servicename }}</div>
                                                                        <div class="order-list-value">{{ ': $ ' . $inv->price_pax . ' /Pax' }}</div>
                                                                    </div>
                                                                {{-- TRANSPORT ===================================================================================================================== --}} 
                                                                @elseif ($inv->service == "Transport")
                                                                    <div class="col-6">
                                                                        <div class="order-list">Transport</div>
                                                                        <div class="order-list">Type</div>
                                                                        <div class="order-list">Capacity</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="order-list-value">{{ ': '. $inv->servicename }}</div>
                                                                        <div class="order-list-value">{{ $inv->subservice }}</div>
                                                                        <div class="order-list-value">{{ ': '. $inv->capacity . ' Seats' }}</div>
                                                                    </div>
                                                                @else
                                                                @endif
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                {{-- HOTEL  =========================================================================================================  --}}
                                                                @if ($inv->service == "Hotel")
                                                                    <div class="col-6">
                                                                        <div class="order-list">Duration</div>
                                                                        <div class="order-list">Check-in</div>
                                                                        <div class="order-list">Check-out</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="order-list-value">{{ $inv->duration . ' Night' }}</div>
                                                                        <div class="order-list-value">{{ date('D, d-M-y', strtotime($inv->checkin)) }}</div>
                                                                        <div class="order-list-value">{{ date('D, d-M-y', strtotime($inv->checkout)) }}</div>
                                                                    </div>
                                                                @elseif ($inv->service == "Hotel Promo")
                                                                    <div class="col-6">
                                                                        <div class="order-list">Duration</div>
                                                                        <div class="order-list">Check-in</div>
                                                                        <div class="order-list">Check-out</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="order-list-value">{{ $inv->duration . ' Night' }}</div>
                                                                        <div class="order-list-value">{{ date('D, d-M-y', strtotime($inv->checkin)) }}</div>
                                                                        <div class="order-list-value">{{ date('D, d-M-y', strtotime($inv->checkout)) }}</div>
                                                                    </div>
                                                                @elseif ($inv->service == "Hotel Package")
                                                                    <div class="col-6">
                                                                        <div class="order-list">Duration</div>
                                                                        <div class="order-list">Check-in</div>
                                                                        <div class="order-list">Check-out</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="order-list-value">{{ $inv->duration . ' Night' }}</div>
                                                                        <div class="order-list-value">{{ date('D, d-M-y', strtotime($inv->checkin)) }}</div>
                                                                        <div class="order-list-value">{{ date('D, d-M-y', strtotime($inv->checkout)) }}</div>
                                                                    </div>
                                                                {{-- TOUR PACKAGE  =========================================================================================================  --}}
                                                                @elseif ($inv->service == "Tour Package")
                                                                    <div class="col-6">
                                                                        <div class="order-list">Duration</div>
                                                                        <div class="order-list">Tour Start</div>
                                                                        <div class="order-list">Tour End</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="order-list-value">{{ $inv->duration }}</div>
                                                                        <div class="order-list-value">{{ date('D, d-M-y (H.i)', strtotime($inv->travel_date)) }}</div>
                                                                        <div class="order-list-value">
                                                                            @if ($inv->duration == "1D")
                                                                                <?php $tour_end=date('Y-m-d (H.i)', strtotime("+10 hours", strtotime($inv->travel_date))); ?>
                                                                                {{ date('D, d-M-y (H.i)', strtotime($tour_end)) }}
                                                                            @elseif ($inv->duration == "2D/1N")
                                                                                <?php $tour_end=date('Y-m-d (H.i)', strtotime("+34 hours", strtotime($inv->travel_date))); ?>
                                                                                {{ date('D, d-M-y (H.i)', strtotime($tour_end)) }}
                                                                            @elseif ($inv->duration == "3D/2N")
                                                                                <?php $tour_end=date('Y-m-d (H.i)', strtotime("+58 hours", strtotime($inv->travel_date))); ?>
                                                                                {{ date('D, d-M-y (H.i)', strtotime($tour_end)) }}
                                                                            @elseif ($inv->duration == "4D/3N")
                                                                                <?php $tour_end=date('Y-m-d (H.i)', strtotime("+82 hours", strtotime($inv->travel_date))); ?>
                                                                                {{ date('D, d-M-y (H.i)', strtotime($tour_end)) }}
                                                                            @elseif ($inv->duration == "5D/4N")
                                                                                <?php $tour_end=date('Y-m-d (H.i)', strtotime("+106 hours", strtotime($inv->travel_date))); ?>
                                                                                {{ date('D, d-M-y (H.i)', strtotime($tour_end)) }}
                                                                            @elseif ($inv->duration == "6D/5N")
                                                                                <?php $tour_end=date('Y-m-d (H.i)', strtotime("+130 hours", strtotime($inv->travel_date))); ?>
                                                                                {{ date('D, d-M-y (H.i)', strtotime($tour_end)) }}
                                                                            @elseif ($inv->duration == "7D/6N")
                                                                                <?php $tour_end=date('Y-m-d (H.i)', strtotime("+154 hours", strtotime($inv->travel_date))); ?>
                                                                                {{ date('D, d-M-y (H.i)', strtotime($tour_end)) }}
                                                                            @else
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                {{-- ACTIVITY  =========================================================================================================  --}}
                                                                @elseif ($inv->service == "Activity")
                                                                    <div class="col-6">
                                                                        <div class="order-list">Duration</div>
                                                                        <div class="order-list">Activity Start</div>
                                                                        <div class="order-list">Activity End</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="order-list-value">{{ $inv->duration.' Hours' }}</div>
                                                                        <div class="order-list-value">{{ date('D, d-M-y (H.i)', strtotime($inv->travel_date)) }}</div>
                                                                        <div class="order-list-value">
                                                                            <?php
                                                                                $activity_duration = $inv->duration;
                                                                                $activity_end=date('Y-m-d H.i', strtotime('+'.$activity_duration.'hours', strtotime($inv->travel_date))); 
                                                                            ?>
                                                                            {{ ': ' .date('D, d-M-y (H.i)', strtotime($activity_end)) }}
                                                                        </div>
                                                                    </div>
                                                                {{-- TRANSPORT  =========================================================================================================  --}}
                                                                @elseif ($inv->service == "Transport")
                                                                    <div class="col-6">
                                                                        <div class="order-list">Duration</div>
                                                                        <div class="order-list">Pickup Date</div>
                                                                        <div class="order-list">Return Date</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        @if ($inv->duration == "1")
                                                                            <div class="order-list-value">{{ $inv->duration . ' Day' }}</div>
                                                                        @else
                                                                            <div class="order-list-value">{{ $inv->duration . ' Days' }}</div>
                                                                        @endif
                                                                        <div class="order-list-value">{{ date('D, d-M-y (H.i)', strtotime($inv->travel_date)) }}</div>
                                                                        <div class="order-list-value">
                                                                            <?php 
                                                                                $duration = $inv->duration;
                                                                                $return_date=date('Y-m-d H.i', strtotime( '+'.$duration.'days', strtotime($inv->travel_date)));
                                                                            ?>
                                                                            {{ date('D, d-M-y (H.i)', strtotime($return_date)) }}
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                @endif
                                                                {{-- End Duration  =========================================================================================================  --}}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="order-note">
                                                                @if ($inv->include != "")
                                                                    <b>Include:</b> <br>
                                                                    {!! $inv->include !!}
                                                                    <hr class="form-hr">
                                                                @else
                                                                @endif
                                                                @if ($inv->itinerary != "")
                                                                    <b>Itinerary:</b> <br>
                                                                    {!! $inv->itinerary !!}
                                                                    <hr class="form-hr">
                                                                @else
                                                                @endif
                                                                @if ($inv->note != "")
                                                                    <b>Note:</b> <br>
                                                                    {!! $inv->note !!}
                                                                    <hr class="form-hr">
                                                                @else
                                                                @endif
                                                                @if ($inv->additional_info != "")
                                                                    <b>Additional Information:</b> <br>
                                                                    {!! $inv->additional_info !!}
                                                                @else
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- PRICE =============================================================================================================== --}}
                                                    <div class="row">
                                                        <div class="col-md-12 ">
                                                            <div class="booking-price text-right">
                                                                @if ($inv->service == "Hotel")
                                                                    <div class="price-name">
                                                                        Total Price :
                                                                    </div>
                                                                    <div class="price-tag">
                                                                        {{ '$ ' . number_format($inv->price_total, 0, ',', '.') }}
                                                                    </div>
                                                                @elseif ($inv->service == "Hotel Promo")
                                                                    <div class="price-name">
                                                                        Total Price :
                                                                    </div>
                                                                    <div class="price-tag">
                                                                        {{ '$ ' . number_format($inv->price_total, 0, ',', '.') }}
                                                                    </div>
                                                                @elseif ($inv->service == "Hotel Package")
                                                                    <div class="price-name">
                                                                        Total Price :
                                                                    </div>
                                                                    <div class="price-tag">
                                                                        {{ '$ ' . number_format($inv->price_total, 0, ',', '.') }}
                                                                    </div>
                                                                @elseif ($inv->service == "Tour Package")
                                                                    <div class="price-name">
                                                                        Total price for {{ $inv->number_of_guests." Pax" }}
                                                                    </div>
                                                                    <div class="price-tag">
                                                                        {{ '$ ' . number_format($inv->price_total, 0, ',', '.') }}
                                                                    </div>
                                                                @elseif ($inv->service == "Activity")
                                                                    <div class="price-name">
                                                                        Total price for {{ $inv->number_of_guests." Pax" }}
                                                                    </div>
                                                                    <div class="price-tag">
                                                                        {{ '$ ' . number_format($inv->price_total, 0, ',', '.') }}
                                                                    </div>
                                                                @elseif ($inv->service == "Transport")
                                                                    <div class="price-name">
                                                                        Total price for
                                                                        @if ($inv->duration == "1")
                                                                            {{ $inv->duration." Day" }}
                                                                        @else
                                                                            {{ $inv->duration." Days" }}
                                                                        @endif
                                                                    </div>
                                                                    <div class="price-tag">
                                                                        {{ '$ ' . number_format($inv->price_total, 0, ',', '.') }}
                                                                    </div>
                                                                @else
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-foot">
                                                    <div class="row">
                                                        <div class="col-md-6 text-left">
                                                            <div class="notif-order m-t-0">This order has been expired!</div>   
                                                        </div>
                                                        <div class="col-md-6 text-right">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    {{-- End Modal Hostory Invoice  --------------------------------------------------------------------------------------------------------------- --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 desktop">
                        <div class="row">
                            @include('layouts.attentions')
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endcan
@endsection
