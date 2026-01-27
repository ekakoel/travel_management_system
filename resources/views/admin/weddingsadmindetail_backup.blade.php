@php
    $priceWedding = $weddings->price;
    if ($weddings->duration == "1D/0N") {
        $wedding_duration = 1;
    }elseif ($weddings->duration == "2D/1N"){
        $wedding_duration = 1;
    }elseif ($weddings->duration == "3D/2N"){
        $wedding_duration = 2;
    }elseif ($weddings->duration == "4D/3N"){
        $wedding_duration = 3;
    }elseif ($weddings->duration == "5D/4N"){
        $wedding_duration = 4;
    }elseif ($weddings->duration == "6D/5N"){
        $wedding_duration = 5;
    }elseif ($weddings->duration == "7D/6N"){
        $wedding_duration = 6;
    }elseif ($weddings->duration == "8D/7N"){
        $wedding_duration = 7;
    }else{
        $wedding_duration = 1;
    }

    $price_suites_and_villa = 0;
    $recomended_price = 0;
    $rooms_id = json_decode($weddings->suite_and_villas_id);
    $decorations_id = json_decode($weddings->decorations_id);
    $dinner_venues_id = json_decode($weddings->dinner_venue_id);
    $makeups_id = json_decode($weddings->makeup_id);
    $entertainments_id = json_decode($weddings->entertainments_id);
    $documentations_id = json_decode($weddings->documentations_id);
    $others_id = json_decode($weddings->other_service_id);
    $transports_id = json_decode($weddings->transport_id);
    $venues_id = json_decode($weddings->wedding_venue_id);
    $fixed_services_id = json_decode($weddings->fixed_services_id);
    $room_pr =[];
    $f_service_price =[];
    $dec_price =[];
    $din_price =[];
    $makeup_price =[];
    $entertainment_price =[];
    $documentation_price =[];
    $other_price =[];
    $transporstPrice =[];
    $weddingVenuePrice =[];
    $ven_price =[];
    if ($transports_id != 'null' and $transports_id) {
        foreach ($transports_id as $transport_id) {
            $transport = $transports->where('id',$transport_id)->first();
            if ($transport) {
                $transprice = $transport_prices->where('transports_id',$transport->id)->first();
                
                if ($transprice) {
                    $cr_trans = ceil($transprice->contract_rate/$usdrates->rate); 
                    $tr_markup = $cr_trans + $transprice->markup;
                    $tr_tax =  ceil($tr_markup * ($taxes->tax / 100));
                    $tranPrice = ceil($tr_tax + $tr_markup);
                    array_push($transporstPrice,$tranPrice);
                }else{
                    $tranPrice = 0;
                    array_push($transporstPrice,$tranPrice);
                }
            }
        }
    }else{
        $tranPrice = 0;
    }
    if ( $transporstPrice != 0) {
        $wedding_transport_price = array_sum($transporstPrice);
    }else{
        $wedding_transport_price = $transporstPrice;
    }

    if ($rooms_id) {
        foreach ($rooms_id as $roomsid) {
            $rooms = $suite_and_villas->where('id',$roomsid)->first();
            if ($rooms) {
                $roomandprice = $room_price->where('rooms_id',$rooms->id)->first();
                
                if ($roomandprice) {
                    $cr_room = ceil($roomandprice->contract_rate/$usdrates->rate); 
                    $cr_markup = $cr_room + $roomandprice->markup;
                    $cr_tax =  ceil($cr_markup * ($taxes->tax / 100));
                    $roomprice = ceil($cr_tax + $cr_markup)*$wedding_duration;
                    array_push($room_pr,$roomprice);
                }else{
                    $roomprice = 0;
                    array_push($room_pr,$roomprice);
                }
            }

            if ( $room_pr != 0) {
                $suite_and_villa_price = array_sum($room_pr);
            }else{
                $suite_and_villa_price = 0;
            }
        }
    }else{
        $suite_and_villa_price = 0;
    }

    if ($venues_id) {
        foreach ($venues_id as $venue_id) {
            $weddingVenue = $wedding_venues->where('id',$venue_id)->first();
            if ($weddingVenue) {
                array_push($ven_price,$weddingVenue->publish_rate);
            }
        }
        $ven_price = array_sum($ven_price);
    }else{
        $ven_price = 0;
    }
    
    if ($fixed_services_id) {
        foreach ($fixed_services_id as $fservice_id) {
            $fixed_s = $fixed_services->where('id',$fservice_id)->first();
            if ($fixed_s) {
                array_push($f_service_price,$fixed_s->publish_rate);
            }
        }
        $fixedService_price = array_sum($f_service_price);
    }else{
        $fixedService_price = 0;
    }

    if ($decorations_id) {
        foreach ($decorations_id as $decoration_id) {
            $dec_p = $decorations->where('id',$decoration_id)->first();
            if ($dec_p) {
                array_push($dec_price,$dec_p->publish_rate);
            }
        }
        $decorationPrice = array_sum($dec_price);
    }else{
        $decorationPrice = 0;
    }

    if ($dinner_venues_id) {
        foreach ($dinner_venues_id as $dinner_venue_id) {
            $din_p = $dinner_venues->where('id',$dinner_venue_id)->first();
            if ($din_p) {
                array_push($din_price,$din_p->publish_rate);
            }
        }
        $dinnerVenuePrice = array_sum($din_price);
    }else{
        $dinnerVenuePrice = 0;

    }

    if ($makeups_id) {
        foreach ($makeups_id as $makeup_id) {
            $makeup_p = $muas->where('id',$makeup_id)->first();
            if ($makeup_p) {
                array_push($makeup_price, $makeup_p->publish_rate);
            }
        }
        $makeupPrice = array_sum($makeup_price);
    }else{
        $makeupPrice = 0;
    }

    if ($entertainments_id) {
        foreach ($entertainments_id as $entertainment_id) {
            $entertain_p = $entertainments->where('id',$entertainment_id)->first();
            if ($entertain_p) {
                array_push($entertainment_price,$entertain_p->publish_rate);
            }
        }
        $entertainmentPrice = array_sum($entertainment_price);
    }else{
        $entertainmentPrice = 0;
    }
    

    if ($documentations_id) {
        foreach ($documentations_id as $documentation_id) {
            $documentation_p = $documentations->where('id',$documentation_id)->first();
            if ($documentation_p) {
                array_push($documentation_price,$documentation_p->publish_rate);
            }
        }
        $documentationPrice = array_sum($documentation_price);
    }else{
        $documentationPrice = 0;
    }
    if ($others_id) {
        foreach ($others_id as $other_id) {
            $other_p = $other_services->where('id',$other_id)->first();
            if ($other_p) {
                array_push($other_price,$other_p->publish_rate);
            }
        }
        $otherPrice = array_sum($other_price);
    }else{
        $otherPrice = 0;
    }

    $total_service_price = $fixedService_price + $ven_price + $wedding_transport_price + $suite_and_villa_price + $decorationPrice + $dinnerVenuePrice + $makeupPrice + $entertainmentPrice + $documentationPrice + $otherPrice;
    $finalRecomendedPrice = ceil(($total_service_price * $taxes->tax)/100) + $total_service_price + $weddings->markup;
    $total_tax = ceil(($total_service_price * $taxes->tax)/100);
    $published_price = $weddings->price;
@endphp
@section('title', __('messages.Wedding Detail'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title">
                            @if ($service != "")
                                {!! $service->icon !!} {{ $service->name }}
                            @endif
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="/weddings-admin">{{ $service->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $weddings->name }}</li>
                            </ol>
                        </nav>
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
                    <div class="product-wrap">
                        <form id="activatePackage" action="/factivate-wedding-package/{{ $weddings->id }}" method="post" enctype="multipart/form-data">
                            @method('put')
                            {{ csrf_field() }}
                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                        </form>
                        <form id="draftedPackage" action="/fdrafted-wedding-package/{{ $weddings->id }}" method="post" enctype="multipart/form-data">
                            @method('put')
                            {{ csrf_field() }}
                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                        </form>
                        <form id="removePackage" action="/fremove-wedding-package/{{ $weddings->id }}" method="post" enctype="multipart/form-data">
                            @method('put')
                            {{ csrf_field() }}
                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                        </form>
                        <div class="product-detail-wrap">
                            <div class="row">
                                {{-- ATTENTIONS --}}
                                <div class="col-md-4 mobile">
                                    <div class="row">
                                        @include('admin.usd-rate')
                                        @include('layouts.attentions')
                                    </div>
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy fa fa-check-circle-o" aria-hidden="true"></i> Status</div>
                                        </div>
                                        <div class="card-title">
                                            <div class="order-status-container">
                                                <div class="subtitle">{{ $weddings->name }}</div>
                                                @if ($weddings->status == "Active")
                                                    <div class="status-active"></div>
                                                @else
                                                    <div class="status-draft"></div>
                                                @endif
                                            </div>
                                        </div>
                                        {{-- STATUS SERVICE VENUE --}}
                                        <div class="row m-t-8">
                                            <div class="col-6">
                                                <p>- Wedding Venue</p>
                                            </div>
                                            <div class="col-6">
                                                @if ($weddings->wedding_venue_id !== "null" and $weddings->wedding_venue_id)
                                                    @php
                                                        $wedding_venue_status = json_decode($weddings->wedding_venue_id);
                                                    @endphp
                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_venue_status) }} Service</div>
                                                @else
                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                @endif
                                            </div>
                                            {{-- STATUS SERVICE SUITE AND VILLAS --}}
                                            <div class="col-6">
                                                <p>- Suites and Villas</p>
                                            </div>
                                            <div class="col-6">
                                                @if ($weddings->suite_and_villas_id !== "null" and $weddings->suite_and_villas_id)
                                                    @php
                                                        $wedding_suite_and_villa_status = json_decode($weddings->suite_and_villas_id);
                                                    @endphp
                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_suite_and_villa_status) }} Room</div>
                                                @else
                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                @endif
                                            </div>
                                            {{-- STATUS SERVICE DECORATION --}}
                                            <div class="col-6">
                                                <p>- Decoration</p>
                                            </div>
                                            <div class="col-6">
                                                @if ($weddings->decorations_id !== "null" and $weddings->decorations_id)
                                                    @php
                                                        $wedding_decoration_status = json_decode($weddings->decorations_id);
                                                    @endphp
                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_decoration_status) }} Service</div>
                                                @else
                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                @endif
                                            </div>
                                            {{-- STATUS SERVICE DECORATION --}}
                                            <div class="col-6">
                                                <p>- Dinner Venue</p>
                                            </div>
                                            <div class="col-6">
                                                @if ($weddings->dinner_venue_id !== "null" and $weddings->dinner_venue_id)
                                                    @php
                                                        $wedding_dinner_venue_status = json_decode($weddings->dinner_venue_id);
                                                    @endphp
                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_dinner_venue_status) }} Service</div>
                                                @else
                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                @endif
                                            </div>
                                            {{-- STATUS SERVICE MAKEUP --}}
                                            <div class="col-6">
                                                <p>- Make-up</p>
                                            </div>
                                            <div class="col-6">
                                                @if ($weddings->makeup_id !== "null" and $weddings->makeup_id)
                                                    @php
                                                        $wedding_makeup_status = json_decode($weddings->makeup_id);
                                                    @endphp
                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_makeup_status) }} Service</div>
                                                @else
                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                @endif
                                            </div>
                                            {{-- STATUS SERVICE ENTERTAINENT --}}
                                            <div class="col-6">
                                                <p>- Entertainment</p>
                                            </div>
                                            <div class="col-6">
                                                @if ($weddings->entertainments_id != "null" and $weddings->entertainments_id)
                                                    @php
                                                        $entertainment_status = json_decode($weddings->entertainments_id);
                                                    @endphp
                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($entertainment_status) }} Service</div>
                                                @else
                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                @endif
                                            </div>
                                            {{-- STATUS SERVICE DOCUMENTATION --}}
                                            <div class="col-6">
                                                <p>- Documentation</p>
                                            </div>
                                            <div class="col-6">
                                                @if ($weddings->documentations_id != "null" and $weddings->documentations_id)
                                                    @php
                                                        $wedding_documentation_status = json_decode($weddings->documentations_id);
                                                    @endphp
                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_documentation_status) }} Service</div>
                                                @else
                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                @endif
                                            </div>
                                            {{-- STATUS SERVICE TRANSPORT --}}
                                            <div class="col-6">
                                                <p>- Transportation</p>
                                            </div>
                                            <div class="col-6">
                                                @if ($weddings->transport_id != "null" and $weddings->transport_id)
                                                    @php
                                                        $wedding_transport_status = json_decode($weddings->transport_id);
                                                    @endphp
                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_transport_status) }} Service</div>
                                                @else
                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                @endif
                                            </div>
                                            {{-- STATUS SERVICE OTHER --}} 
                                            <div class="col-6">
                                                <p>- Other Service</p>
                                            </div>
                                            <div class="col-6">
                                                @if ($weddings->other_service_id != "null" and $weddings->other_service_id)
                                                    @php
                                                        $wedding_other_status = json_decode($weddings->other_service_id);
                                                    @endphp
                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_other_status) }} Service</div>
                                                @else
                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                @endif
                                            </div>
                                            
                                            @if ($total_service_price > $published_price)
                                                <div class="col-12">
                                                    <div class="notification-error blink_me">Attentions!</div>
                                                    <div class="notification-error">Please refresh price first to activate this wedding package!</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-box-footer">
                                            @if ($weddings->status !== "Active")
                                                <button type="submit" form="removePackage" class="btn btn-danger" onclick="return confirm('Are you sure to remove {{ $weddings->name }}?');" data-toggle="tooltip" data-placement="top" title="Remove Wedding Package"><i class="icon-copy fa fa-trash"></i> Remove</button>
                                                @if ($weddings->markup > 0)
                                                    @if ($total_service_price < $published_price)
                                                        <button type="submit" form="activatePackage" class="btn btn-primary" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Activate Wedding Package"><i class="icon-copy fa fa-check"></i> Activate</button>
                                                    @endif
                                                @endif
                                            @endif
                                            @if ($weddings->status !== "Draft")
                                                <button type="submit" form="draftedPackage" class="btn btn-secondary"  aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Drafted Wedding Package"><i class="icon-copy fa fa-pencil"></i> Draft</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy fi-torso-business"></i><i class="icon-copy fi-torso-female"></i>{{ $weddings->name }}</div>
                                            <div class="status-card">
                                                @if ($weddings->status == "Rejected")
                                                    <div class="status-rejected"></div>
                                                @elseif ($weddings->status == "Invalid")
                                                    <div class="status-invalid"></div>
                                                @elseif ($weddings->status == "Active")
                                                    <div class="status-active"></div>
                                                @elseif ($weddings->status == "Waiting")
                                                    <div class="status-waiting"></div>
                                                @elseif ($weddings->status == "Draft")
                                                    <div class="status-draft"></div>
                                                @elseif ($weddings->status == "Archived")
                                                    <div class="status-archived"></div>
                                                @else
                                                @endif
                                            </div>
                                        </div>
                                        <div class="page-card">
                                            <figure class="card-banner">
                                                <img src="{{ asset ('storage/weddings/wedding-cover/' . $weddings->cover) }}" alt="{{ $weddings->name }}" loading="lazy">
                                            </figure>
                                            <div class="card-content">
                                                <div class="card-text">
                                                    <div class="row ">
                                                        <div class="col-4">
                                                            <div class="card-title"><i class="icon-copy dw dw-hotel" aria-hidden="true"></i>Hotel:</div>
                                                            <div class="data-web">{{  $hotel->name  }}</div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="card-title"><i class="icon-copy fa fa-users" aria-hidden="true"></i>Capacity:</div>
                                                            <div class="data-web">{{  $weddings->capacity }} @lang('messages.pax')</div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="card-title"><i class="icon-copy fa fa-calendar-check-o" aria-hidden="true"></i>@lang('messages.Duration'):</div>
                                                            <div class="data-web">{{  $weddings->duration }} </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="card-title"><i class="icon-copy fa fa-calendar" aria-hidden="true"></i>@lang('messages.Period'):</div>
                                                            <div class="data-web">{{  date("m/d/y",strtotime($weddings->period_start))." - ".date("m/d/y",strtotime($weddings->period_end)) }} </div>
                                                        </div>
                                                       
                                                        <div class="col-12 m-t-8">
                                                            <div class="card-title"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> PDF Rate:</div>
                                                            <a href="#" data-target="#wedding-pdf-{{ $weddings->id }}" data-toggle="modal">
                                                                <div class="icon-list" data-toggle="tooltip" data-placement="top" title="View PDF Rate">
                                                                    {{ $weddings->name }} PDF
                                                                </div>
                                                            </a>
                                                            {{-- Modal Property PDF ----------------------------------------------------------------------------------------------------------- --}}
                                                            <div class="modal fade" id="wedding-pdf-{{ $weddings->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                                        <div class="modal-body pd-5">
                                                                            <embed src="storage/weddings/wedding-pdf/{{ $weddings->pdf }}" frameborder="10" width="100%" height="850px">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {{-- End Modal Contract ----------------------------------------------------------------------------------------------------------- --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="tab">
                                                    <ul class="nav nav-tabs" role="tablist">
                                                        @if ($weddings->description)
                                                            <li class="nav-item">
                                                                <a class="nav-link active"  data-toggle="tab" href="#description-text" role="tab" aria-selected="false">@lang('messages.Description')</a>
                                                            </li>
                                                        @endif
                                                        @if ($weddings->include)
                                                            <li class="nav-item">
                                                                <a class="nav-link"  data-toggle="tab" href="#include-text" role="tab" aria-selected="false">@lang('messages.Include')</a>
                                                            </li>
                                                        @endif
                                                        @if ($weddings->additional_info)
                                                            <li class="nav-item">
                                                                <a class="nav-link"  data-toggle="tab" href="#additional-info-text" role="tab" aria-selected="false">@lang('messages.Additional Information')</a>
                                                            </li>
                                                        @endif
                                                        @if ($weddings->cancellation_policy)
                                                            <li class="nav-item">
                                                                <a class="nav-link"  data-toggle="tab" href="#cancellation-policy-text" role="tab" aria-selected="false">@lang('messages.Cancellation Policy')</a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                    <div class="tab-content">
                                                        @if ($weddings->include)
                                                            <div class="tab-pane fade" id="include-text" role="tabpanel">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="tab-inner-title">Include</div>
                                                                        <div class="data-web-text-area">
                                                                            {!! $weddings->include !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($weddings->description)
                                                            <div class="tab-pane fade active show" id="description-text" role="tabpanel">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="tab-inner-title">Description</div>
                                                                        <div class="data-web-text-area">
                                                                            {!! $weddings->description !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($weddings->additional_info or $weddings->payment_process or $weddings->payment_process)
                                                            <div class="tab-pane fade" id="additional-info-text" role="tabpanel">
                                                                <div class="row">
                                                                    @if ($weddings->additional_info)
                                                                        <div class="col-md-12">
                                                                            <div class="tab-inner-title">Additional Information</div>
                                                                            <div class="data-web-text-area">{!! $weddings->additional_info !!} </div>
                                                                        </div>
                                                                    @endif
                                                                    @if ($weddings->executive_staff)
                                                                        <div class="col-md-12">
                                                                            <div class="tab-inner-title">Executive Staff</div>
                                                                            <div class="data-web-text-area">{!! $weddings->executive_staff !!} </div>
                                                                        </div>
                                                                    @endif
                                                                    @if ($weddings->payment_process)
                                                                        <div class="col-md-12">
                                                                            <div class="tab-inner-title">Payment Process</div>
                                                                            <div class="data-web-text-area">{!! $weddings->payment_process !!} </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($weddings->cancellation_policy)
                                                            <div class="tab-pane fade" id="cancellation-policy-text" role="tabpanel">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="tab-inner-title">Cancellation Policy</div>
                                                                        <div class="data-web-text-area">{!! $weddings->cancellation_policy !!} </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($weddings->status !== "Active")
                                            <div class="card-box-footer">
                                                <a href="/{{ $service->nicname }}-edit-{{ $weddings['id'] }}"><button class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button></a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 desktop">
                                    <div class="row">
                                        @include('admin.usd-rate')
                                        @include('layouts.attentions')
                                            <div class="col-md-12">
                                                <div id="btnActionOn">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fa fa-check-circle-o" aria-hidden="true"></i> Status</div>
                                                        </div>
                                                        <div class="card-title">
                                                            <div class="order-status-container">
                                                                <div class="subtitle">{{ $weddings->name }}</div>
                                                                @if ($weddings->status == "Active")
                                                                    <div class="status-active"></div>
                                                                @else
                                                                    <div class="status-draft"></div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="row m-t-8">
                                                            {{-- STATUS FIXED SERVICE --}}
                                                            <div class="col-6">
                                                                <p>- Fixed Services</p>
                                                            </div>
                                                            <div class="col-6">
                                                                @if ($weddings->fixed_services_id !== "null" and $weddings->fixed_services_id)
                                                                    @php
                                                                        $fixed_service_status = json_decode($weddings->fixed_services_id);
                                                                    @endphp
                                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($fixed_service_status) }} Service</div>
                                                                @else
                                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i> 0 Services</div>
                                                                @endif
                                                            </div>
                                                            {{-- STATUS SERVICE VENUE --}}
                                                            <div class="col-6">
                                                                <p>- Wedding Venue</p>
                                                            </div>
                                                            <div class="col-6">
                                                                @if ($weddings->wedding_venue_id !== "null" and $weddings->wedding_venue_id)
                                                                    @php
                                                                        $wedding_venue_status = json_decode($weddings->wedding_venue_id);
                                                                    @endphp
                                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_venue_status) }} Service</div>
                                                                @else
                                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i> 0 Services</div>
                                                                @endif
                                                            </div>
                                                            {{-- STATUS SERVICE SUITE AND VILLAS --}}
                                                            <div class="col-6">
                                                                <p>- Suites and Villas</p>
                                                            </div>
                                                            <div class="col-6">
                                                                @if ($weddings->suite_and_villas_id !== "null" and $weddings->suite_and_villas_id)
                                                                    @php
                                                                        $wedding_suite_and_villa_status = json_decode($weddings->suite_and_villas_id);
                                                                    @endphp
                                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_suite_and_villa_status) }} Room</div>
                                                                @else
                                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i> 0 Services</div>
                                                                @endif
                                                            </div>
                                                            {{-- STATUS SERVICE DECORATION --}}
                                                            <div class="col-6">
                                                                <p>- Decoration</p>
                                                            </div>
                                                            <div class="col-6">
                                                                @if ($weddings->decorations_id !== "null" and $weddings->decorations_id)
                                                                    @php
                                                                        $wedding_decoration_status = json_decode($weddings->decorations_id);
                                                                    @endphp
                                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_decoration_status) }} Service</div>
                                                                @else
                                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i> 0 Services</div>
                                                                @endif
                                                            </div>
                                                            {{-- STATUS SERVICE DECORATION --}}
                                                            <div class="col-6">
                                                                <p>- Dinner Venue</p>
                                                            </div>
                                                            <div class="col-6">
                                                                @if ($weddings->dinner_venue_id !== "null" and $weddings->dinner_venue_id)
                                                                    @php
                                                                        $wedding_dinner_venue_status = json_decode($weddings->dinner_venue_id);
                                                                    @endphp
                                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_dinner_venue_status) }} Service</div>
                                                                @else
                                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i> 0 Services</div>
                                                                @endif
                                                            </div>
                                                            {{-- STATUS SERVICE MAKEUP --}}
                                                            <div class="col-6">
                                                                <p>- Make-up</p>
                                                            </div>
                                                            <div class="col-6">
                                                                @if ($weddings->makeup_id !== "null" and $weddings->makeup_id)
                                                                    @php
                                                                        $wedding_makeup_status = json_decode($weddings->makeup_id);
                                                                    @endphp
                                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_makeup_status) }} Service</div>
                                                                @else
                                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i> 0 Services</div>
                                                                @endif
                                                            </div>
                                                            {{-- STATUS SERVICE ENTERTAINENT --}}
                                                            <div class="col-6">
                                                                <p>- Entertainment</p>
                                                            </div>
                                                            <div class="col-6">
                                                                @if ($weddings->entertainments_id != "null" and $weddings->entertainments_id)
                                                                    @php
                                                                        $entertainment_status = json_decode($weddings->entertainments_id);
                                                                    @endphp
                                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($entertainment_status) }} Service</div>
                                                                @else
                                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i> 0 Services</div>
                                                                @endif
                                                            </div>
                                                            {{-- STATUS SERVICE DOCUMENTATION --}}
                                                            <div class="col-6">
                                                                <p>- Documentation</p>
                                                            </div>
                                                            <div class="col-6">
                                                                @if ($weddings->documentations_id != "null" and $weddings->documentations_id)
                                                                    @php
                                                                        $wedding_documentation_status = json_decode($weddings->documentations_id);
                                                                    @endphp
                                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_documentation_status) }} Service</div>
                                                                @else
                                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i> 0 Services</div>
                                                                @endif
                                                            </div>
                                                            {{-- STATUS SERVICE TRANSPORT --}}
                                                            <div class="col-6">
                                                                <p>- Transportation</p>
                                                            </div>
                                                            <div class="col-6">
                                                                @if ($weddings->transport_id != "null" and $weddings->transport_id)
                                                                    @php
                                                                        $wedding_transport_status = json_decode($weddings->transport_id);
                                                                    @endphp
                                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_transport_status) }} Service</div>
                                                                @else
                                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i> 0 Services</div>
                                                                @endif
                                                            </div>
                                                            {{-- STATUS SERVICE OTHER --}} 
                                                            <div class="col-6">
                                                                <p>- Other Service</p>
                                                            </div>
                                                            <div class="col-6">
                                                                @if ($weddings->other_service_id != "null" and $weddings->other_service_id)
                                                                    @php
                                                                        $wedding_other_status = json_decode($weddings->other_service_id);
                                                                    @endphp
                                                                    <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_other_status) }} Service</div>
                                                                @else
                                                                    <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i> 0 Services</div>
                                                                @endif
                                                            </div>
                                                            @if ($total_service_price > $published_price)
                                                                <div class="col-12">
                                                                    <div class="notification-error blink_me">Attentions!</div>
                                                                    <div class="notification-error">Please refresh price first to activate this wedding package!</div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="card-box-footer">
                                                            @if ($weddings->status !== "Active")
                                                                <button type="submit" form="removePackage" class="btn btn-danger" onclick="return confirm('Are you sure to remove {{ $weddings->name }}?');" data-toggle="tooltip" data-placement="top" title="Remove Wedding Package"><i class="icon-copy fa fa-trash"></i> Remove</button>
                                                                @if ($weddings->markup > 0)
                                                                    @if ($total_service_price < $published_price)
                                                                        <button type="submit" form="activatePackage" class="btn btn-primary" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Activate Wedding Package"><i class="icon-copy fa fa-check"></i> Activate</button>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                            @if ($weddings->status !== "Draft")
                                                                <button type="submit" form="draftedPackage" class="btn btn-secondary"  aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Drafted Wedding Package"><i class="icon-copy fa fa-pencil"></i> Draft</button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="col-md-12">
                                            <div id="btnAction">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="subtitle"><i class="icon-copy fa fa-check-circle-o" aria-hidden="true"></i> Status</div>
                                                    </div>
                                                    <div class="card-title">
                                                        <div class="order-status-container">
                                                            <div class="subtitle">{{ $weddings->name }} Services</div>
                                                            @if ($weddings->status == "Active")
                                                                <div class="status-active"></div>
                                                            @else
                                                                <div class="status-draft"></div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    {{-- STATUS SERVICE VENUE --}}
                                                    <div class="row m-t-8">
                                                        <div class="col-6">
                                                            <p>- Wedding Venue</p>
                                                        </div>
                                                        <div class="col-6">
                                                            @if ($weddings->wedding_venue_id !== "null" and $weddings->wedding_venue_id)
                                                                @php
                                                                    $wedding_venue_status = json_decode($weddings->wedding_venue_id);
                                                                @endphp
                                                                <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_venue_status) }} Service</div>
                                                            @else
                                                                <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                            @endif
                                                        </div>
                                                        {{-- STATUS SERVICE SUITE AND VILLAS --}}
                                                        <div class="col-6">
                                                            <p>- Suites and Villas</p>
                                                        </div>
                                                        <div class="col-6">
                                                            @if ($weddings->suite_and_villas_id !== "null" and $weddings->suite_and_villas_id)
                                                                @php
                                                                    $wedding_suite_and_villa_status = json_decode($weddings->suite_and_villas_id);
                                                                @endphp
                                                                <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_suite_and_villa_status) }} Room</div>
                                                            @else
                                                                <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                            @endif
                                                        </div>
                                                        {{-- STATUS SERVICE DECORATION --}}
                                                        <div class="col-6">
                                                            <p>- Decoration</p>
                                                        </div>
                                                        <div class="col-6">
                                                            @if ($weddings->decorations_id !== "null" and $weddings->decorations_id)
                                                                @php
                                                                    $wedding_decoration_status = json_decode($weddings->decorations_id);
                                                                @endphp
                                                                <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_decoration_status) }} Service</div>
                                                            @else
                                                                <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                            @endif
                                                        </div>
                                                        {{-- STATUS SERVICE DECORATION --}}
                                                        <div class="col-6">
                                                            <p>- Dinner Venue</p>
                                                        </div>
                                                        <div class="col-6">
                                                            @if ($weddings->dinner_venue_id !== "null" and $weddings->dinner_venue_id)
                                                                @php
                                                                    $wedding_dinner_venue_status = json_decode($weddings->dinner_venue_id);
                                                                @endphp
                                                                <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_dinner_venue_status) }} Service</div>
                                                            @else
                                                                <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                            @endif
                                                        </div>
                                                        {{-- STATUS SERVICE MAKEUP --}}
                                                        <div class="col-6">
                                                            <p>- Make-up</p>
                                                        </div>
                                                        <div class="col-6">
                                                            @if ($weddings->makeup_id !== "null" and $weddings->makeup_id)
                                                                @php
                                                                    $wedding_makeup_status = json_decode($weddings->makeup_id);
                                                                @endphp
                                                                <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_makeup_status) }} Service</div>
                                                            @else
                                                                <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                            @endif
                                                        </div>
                                                        {{-- STATUS SERVICE ENTERTAINENT --}}
                                                        <div class="col-6">
                                                            <p>- Entertainment</p>
                                                        </div>
                                                        <div class="col-6">
                                                            @if ($weddings->entertainments_id != "null" and $weddings->entertainments_id)
                                                                @php
                                                                    $entertainment_status = json_decode($weddings->entertainments_id);
                                                                @endphp
                                                                <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($entertainment_status) }} Service</div>
                                                            @else
                                                                <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                            @endif
                                                        </div>
                                                        {{-- STATUS SERVICE DOCUMENTATION --}}
                                                        <div class="col-6">
                                                            <p>- Documentation</p>
                                                        </div>
                                                        <div class="col-6">
                                                            @if ($weddings->documentations_id != "null" and $weddings->documentations_id)
                                                                @php
                                                                    $wedding_documentation_status = json_decode($weddings->documentations_id);
                                                                @endphp
                                                                <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_documentation_status) }} Service</div>
                                                            @else
                                                                <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                            @endif
                                                        </div>
                                                        {{-- STATUS SERVICE TRANSPORT --}}
                                                        <div class="col-6">
                                                            <p>- Transportation</p>
                                                        </div>
                                                        <div class="col-6">
                                                            @if ($weddings->transport_id != "null" and $weddings->transport_id)
                                                                @php
                                                                    $wedding_transport_status = json_decode($weddings->transport_id);
                                                                @endphp
                                                                <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_transport_status) }} Service</div>
                                                            @else
                                                                <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                            @endif
                                                        </div>
                                                        {{-- STATUS SERVICE OTHER --}} 
                                                        <div class="col-6">
                                                            <p>- Other Service</p>
                                                        </div>
                                                        <div class="col-6">
                                                            @if ($weddings->other_service_id != "null" and $weddings->other_service_id)
                                                                @php
                                                                    $wedding_other_status = json_decode($weddings->other_service_id);
                                                                @endphp
                                                                <div class="label-active"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ count($wedding_other_status) }} Service</div>
                                                            @else
                                                                <div class="label-danger"><i class="icon-copy fa fa-window-close" aria-hidden="true"></i></div>
                                                            @endif
                                                        </div>
                                                        {{-- STATUS SERVICE NOTIFICATION --}} 
                                                        @if ($total_service_price > $published_price)
                                                            <div class="col-12">
                                                                <div class="notification-error blink_me">Attentions!</div>
                                                                <div class="notification-error">Please refresh price first to activate this wedding package!</div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card-box-footer">
                                                        @if ($weddings->status !== "Active")
                                                            <button type="submit" form="removePackage" class="btn btn-danger" onclick="return confirm('Are you sure to remove {{ $weddings->name }}?');" data-toggle="tooltip" data-placement="top" title="Remove Wedding Package"><i class="icon-copy fa fa-trash"></i> Remove</button>
                                                            @if ($weddings->markup > 0)
                                                                @if ($total_service_price < $published_price)
                                                                    <button type="submit" form="activatePackage" class="btn btn-primary" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Activate Wedding Package"><i class="icon-copy fa fa-check"></i> Activate</button>
                                                                @endif
                                                            @endif
                                                        @endif
                                                        @if ($weddings->status !== "Draft")
                                                            <button type="submit" form="draftedPackage" class="btn btn-secondary"  aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Drafted Wedding Package"><i class="icon-copy fa fa-pencil"></i> Draft</button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    @include('layouts.wedding_fixed_services')
                                </div>
                                <div class="col-md-8">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle">
                                                <i class="icon-copy fa fa-cubes" aria-hidden="true"></i> Optional Services
                                            </div>
                                        </div>
                                        @include('layouts.wedding_venue_admin')
                                        @include('layouts.wedding_room')
                                        @include('layouts.wedding_decoration')
                                        @include('layouts.wedding_dinner_venue')
                                        @include('layouts.wedding_makeup')
                                        @include('layouts.wedding_entertainment')
                                        @include('layouts.wedding_documentation')
                                        @include('layouts.wedding_transport')
                                        @include('layouts.wedding_other')
                                    </div>
                                    @include('layouts.wedding_price')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endcan
@endsection
