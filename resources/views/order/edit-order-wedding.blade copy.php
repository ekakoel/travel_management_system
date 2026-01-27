<div class="col-md-8">
    @php
        $checkin = date('d M Y',strtotime($order->checkin));
        $wedding_date = date('d M Y',strtotime($order->wedding_date));
        $checkout = date('d M Y',strtotime($order->checkout));
    @endphp
    <div class="card-box">
        <div class="card-box-title">
            <div class="subtitle"><i class="fa fa-pencil"></i> @lang('messages.Edit Order')</div>
        </div>
        <form id="edit-order" action="/fupdate-order/{{ $order->id }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-6 col-md-6">
                    <div class="order-bil text-left">
                        <img src="images/balikami/bali-kami-tour-logo.png" alt="Bali Kami Wedding & Travel">
                    </div>
                </div>
                <div class="col-6 col-md-6 flex-end">
                    <div class="label-title">@lang('messages.Order')</div>
                </div>
                <div class="col-md-12 text-right">
                    <div class="label-date float-right" style="width: 100%">
                        {{ date('m/d/Y', strtotime($order->created_at)) }}
                    </div>
                </div>
            </div>
            <div class="business-name">{{ $business->name }}</div>
            <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
            <hr class="form-hr">
            <div class="row">
                <div class="col-md-6">
                    <table class="table tb-list">
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Order No')
                            </td>
                            <td class="htd-2">
                                <b>{{ $order->orderno }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Order Date')
                            </td>
                            <td class="htd-2">
                                {{ date('m/d/Y', strtotime($order->created_at)) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Service')
                            </td>
                            <td class="htd-2">
                                @lang('messages.'.$order->service)
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    @if ($order->status == "Active")
                        <div class="page-status" style="color: rgb(0, 156, 21)"> @lang('messages.Confirmed') <span>@lang('messages.Status'):</span></div>
                    @elseif ($order->status == "Pending")
                        <div class="page-status" style="color: #dd9e00">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                    @elseif ($order->status == "Rejected")
                        <div class="page-status" style="color: rgb(160, 0, 0)">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                    @else
                        <div class="page-status" style="color: rgb(48, 48, 48)">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                    @endif
                </div>
            </div>
            <div class="row">
                {{-- WEDDING PACKAGE  --}}
                <div class="col-md-12">
                    <div class="tab-inner-title m-t-8">@lang('messages.Wedding Package')</div>
                    <div class="row m-b-8">
                        <div class="col-md-6">
                            <table class="table tb-list">
                                <tr>
                                    <td class="htd-1">
                                        @lang('messages.Package')
                                    </td>
                                    <td class="htd-2">
                                        {{ $order->servicename }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="htd-1">
                                        @lang('messages.Hotel')
                                    </td>
                                    <td class="htd-2">
                                        {{ $order->location }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table tb-list">
                                <tr>
                                    <td class="htd-1">
                                        @lang('messages.Capacity')
                                    </td>
                                    <td class="htd-2">
                                    {{ $order->capacity." guests" }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="htd-1">
                                        @lang('messages.Duration')
                                    </td>
                                    <td class="htd-2">
                                        {{ $order->duration." " }}@lang('messages.Night')
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                @if ($wedding->description)
                    <div class="col-md-12">
                        <div class="tab-inner-title">@lang('messages.Description')</div>
                        <div class="data-web-text-area">{!! $wedding->description !!} </div>
                    </div>
                @endif
                @if ($wedding->venue)
                    <div class="col-md-12">
                        <div class="tab-inner-title">@lang('messages.Venue')</div>
                        <div class="data-web-text-area">{!! $wedding->venue !!} </div>
                    </div>
                @endif
                @if ($wedding->executive_staff)
                    <div class="col-md-12">
                        <div class="tab-inner-title">@lang('messages.Executive Staff')</div>
                        <div class="data-web-text-area">{!! $wedding->executive_staff !!} </div>
                    </div>
                @endif
                @if ($wedding->include or $wedding->fixed_services_id)
                    <div class="col-md-12">
                        <div class="tab-inner-title">@lang('messages.Include')</div>
                        <div class="data-web-text-area">{!! $wedding->include !!} </div>
                        {{-- WEDDING FIXED SERVICES ---------------------------------------------------------------------------------------------------------------------------}}
                        <div class="card-box-content m-b-8">
                            @if ($wedding->fixed_services_id)
                                @php
                                    $fixed_services_id = json_decode($wedding->fixed_services_id);
                                @endphp
                                @if ($vendor_wedding_fixed_services)
                                    @foreach ($fixed_services_id as $fixed_service_id)
                                        @php
                                            $f_service = $vendor_wedding_fixed_services->where('id',$fixed_service_id)->first();
                                        @endphp
                                        @if ($f_service)
                                            <div class="card">
                                                <a href="#" data-toggle="modal" data-target="#detail-wedding-fixed-service-{{ $f_service->id }}">
                                                    <div class="image-container">
                                                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $f_service->cover) }}" alt="{{ $f_service->name }}">
                                                        <div class="name-card">
                                                            <b>@lang('messages.Wedding Venue')</b>
                                                            <p>{{ $f_service->service }}</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            {{-- MODAL WEDDING FIXED SERVICE --------------------------------------------------------------------------------------------------------------- --}}
                                            <div class="modal fade" id="detail-wedding-fixed-service-{{ $f_service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fa fa-check"></i>{{ $f_service->service }}</div>
                                                            </div>
                                                            <div class="card-banner m-b-8">
                                                                <img class="rounded" src="{{ asset('storage/vendors/package/' . $f_service->cover) }}" alt="{{ $f_service->cover }}" loading="lazy">
                                                            </div>
                                                            @if ($f_service->service)
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-4">
                                                                            <b>@lang('messages.Service'): </b><p>{!! $f_service->service !!}</p>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <b>@lang('messages.Capacity'): </b><p>{{ $f_service->capacity." guests" }}</p>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <b>@lang('messages.Duration'): </b><p>{!! $f_service->duration." ".$f_service->time !!}</p>
                                                                        </div>
                                                                        
                                                                        @if ($f_service->description)
                                                                            <div class="col-sm-12">
                                                                                <b>@lang('messages.Description'): </b><p>{!! $f_service->description !!}</p>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="card-box-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            @else
                            @endif
                        </div>
                    </div>
                @endif
                @if ($wedding->additional_info)
                    <div class="col-md-12">
                        <div class="tab-inner-title">@lang('messages.Additional Information')</div>
                        <div class="data-web-text-area">{!! $wedding->additional_info !!} </div>
                    </div>
                @endif
            </div>

            


            {{-- SERVICE ============================================================================================================================================================= --}}
            <div class="tab-inner-title m-t-8">@lang('messages.Services')</div>
            <div class="row m-b-8">
                @php
                    $wedding_venues_id = json_decode($wedding->wedding_venue_id);
                    $order_wedding_venues_id = json_decode($order_wedding->wedding_venue_id);
                    $wedding_suites_and_villas_id = json_decode($wedding->suite_and_villas_id);
                    $order_wedding_suites_and_villas_id = json_decode($order_wedding->wedding_room_id);
                    $w_suites_and_villas_ids = collect($wedding_suites_and_villas_id);
                    $o_w_suites_and_villas_ids = collect($order_wedding_suites_and_villas_id);
                    $order_wedding_decorations_id = json_decode($order_wedding->wedding_decoration_id);
                    $order_wedding_dinner_venues_ids = json_decode($order_wedding->wedding_dinner_venue_id);
                    $order_wedding_documentation_ids = json_decode($order_wedding->wedding_documentation_id);
                    $order_wedding_other_ids = json_decode($order_wedding->wedding_other_id);
                    $order_wedding_transport_ids = json_decode($order_wedding->wedding_transport_id);
                    $order_wedding_makeups_id = json_decode($order_wedding->wedding_makeup_id);
                    $order_wedding_entertainments_id = json_decode($order_wedding->wedding_entertainment_id);
                @endphp
                {{-- WEDDING VENUE --}} 
                @if ($wedding_venues_id !== 'null' and $wedding_venues_id and $vendor_services)
                    <div class="col-md-6">
                        <div class="subtitle-card">@lang('messages.Wedding Venue')</div>
                        <div class="card-grid-content m-b-8">
                            @foreach ($vendor_services as $vendor_service)
                                @foreach ($wedding_venues_id as $w_v_id)
                                    @if ($vendor_service->id == $w_v_id)
                                        @foreach ($order_wedding_venues_id as $o_w_v_id)
                                            @if ($w_v_id == $o_w_v_id)
                                                <div class="card">
                                                    <div class="image-container">
                                                        <a href="#" data-toggle="modal" data-target="#wedding-venue-{{ $vendor_service->id }}">
                                                            <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}">
                                                        </a>
                                                    </div>
                                                    <div class="name-card">
                                                        <b>@lang('messages.'.$vendor_service->type)</b>
                                                        <p>{{ $vendor_service->service }}</p>
                                                    </div>
                                                </div>
                                                {{-- MODAL WEDDING VENUE --------------------------------------------------------------------------------------------------------------- --}}
                                                <div class="modal fade" id="wedding-venue-{{ $vendor_service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fi-torso"></i><i class="icon-copy fi-torso-female"></i> {{ $vendor_service->type }}</div>
                                                                </div>
                                                                <div class="card-banner m-b-8">
                                                                    <img class="rounded" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}" loading="lazy">
                                                                </div>
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-4">
                                                                            <b>Service: </b><p>{!! $vendor_service->service !!}</p>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <b>Duration: </b><p>{!! $vendor_service->duration." ".$vendor_service->time !!}</p>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <b>Capacity: </b><p>{!! $vendor_service->capacity." guests" !!}</p>
                                                                        </div>
                                                                        @if ($vendor_service->description)
                                                                            <div class="col-sm-12">
                                                                                <b>Description: </b><p>{!! $vendor_service->description !!}</p>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="card-box-footer">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>    
                @endif
                {{-- WEDDING SUITES AND VILLAS --}}
                @if ($w_suites_and_villas_ids !== 'null' and $w_suites_and_villas_ids and $vendor_suites_and_villas)
                    <div class="col-md-6">
                        <div class="subtitle-card">@lang('messages.Suites and Villas')</div>
                        <div class="card-grid-content m-b-8">
                            @foreach ($vendor_suites_and_villas as $no=>$vendor_suites_and_villa)
                                @foreach ($w_suites_and_villas_ids as $w_suites_and_villas_id)
                                    @if ($vendor_suites_and_villa->id == $w_suites_and_villas_id)
                                        @foreach ($o_w_suites_and_villas_ids as $o_w_suites_and_villas_id)
                                            @if ($w_suites_and_villas_id == $o_w_suites_and_villas_id)
                                                <div class="card active">
                                                    <div class="image-container">
                                                        <a href="#" data-toggle="modal" data-target="#wedding-suites-and-villas-{{ $vendor_suites_and_villa->id }}">
                                                            <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/hotels/hotels-room/' . $vendor_suites_and_villa->cover) }}" alt="{{ $vendor_suites_and_villa->rooms }}">
                                                        </a>
                                                        <input type="hidden" name="wedding_room_id[]" value="{{ $vendor_suites_and_villa->id }}">
                                                    </div>
                                                    <div class="name-card">
                                                        <b>@lang('messages.Suites and Villas')</b>
                                                        <p>{{ $vendor_suites_and_villa->rooms }}</p>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="wedding-suites-and-villas-{{ $vendor_suites_and_villa->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-hotel"></i> @lang('messages.Suites and Villas')</div>
                                                                </div>
                                                                <div class="card-banner m-b-8">
                                                                    <img class="rounded" src="{{ asset('storage/hotels/hotels-room/' . $vendor_suites_and_villa->cover) }}" alt="{{ $vendor_suites_and_villa->service }}" loading="lazy">
                                                                </div>
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-4">
                                                                            <b>Suites and Villas: </b><p>{!! $vendor_suites_and_villa->rooms !!}</p>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <b>Duration: </b><p>{!! $order->duration." nights" !!}</p>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <b>Capacity: </b><p>{!! $vendor_suites_and_villa->capacity." guests" !!}</p>
                                                                        </div>
                                                                        @if ($vendor_suites_and_villa->description)
                                                                            <div class="col-sm-12">
                                                                                <b>Description: </b><p>{!! $vendor_suites_and_villa->description !!}</p>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="card-box-footer">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>    
                @endif
                {{-- WEDDING DINNER VENUE --}}
                @if ($order_wedding_dinner_venues_ids !== 'null' and $order_wedding_dinner_venues_ids and $vendor_services)
                    <div class="col-md-6">
                        <div class="subtitle-card">@lang('messages.Dinner Venue')</div>
                        <div class="card-grid-content m-b-8">
                            @foreach ($vendor_services as $vendor_service)
                                @foreach ($order_wedding_dinner_venues_ids as $order_wedding_dinner_venues_id)
                                    @if ($vendor_service->id == $order_wedding_dinner_venues_id)
                                        <div class="card">
                                            <div class="image-container">
                                                <a href="#" data-toggle="modal" data-target="#wedding-dinner-venue-{{ $vendor_service->id }}">
                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}">
                                                </a>
                                            </div>
                                            <div class="name-card">
                                                <b>@lang('messages.Dinner Venue')</b>
                                                <p>{{ $vendor_service->service }}</p>
                                            </div>
                                        </div>
                                        {{-- MODAL WEDDING DINNER VENUE --------------------------------------------------------------------------------------------------------------- --}}
                                        <div class="modal fade" id="wedding-dinner-venue-{{ $vendor_service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fa fa-birthday-cake"></i> @lang('messages.Dinner Venue')</div>
                                                        </div>
                                                        <div class="card-banner m-b-8">
                                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}" loading="lazy">
                                                        </div>
                                                        <div class="card-text">
                                                            <div class="row ">
                                                                <div class="col-sm-4">
                                                                    <b>Service: </b><p>{!! $vendor_service->service !!}</p>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <b>Duration: </b><p>{!! $vendor_service->duration." ".$vendor_service->time !!}</p>
                                                                </div>
                                                                @if ($vendor_service->description)
                                                                    <div class="col-sm-12">
                                                                        <b>Description: </b><p>{!! $vendor_service->description !!}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="card-box-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>    
                @endif
                {{-- WEDDING DECORATION --}}
                @if ($order_wedding_decorations_id !== 'null' and $order_wedding_decorations_id and $vendor_services)
                    <div class="col-md-6">
                        <div class="subtitle-card">@lang('messages.Decorations')</div>
                        <div class="card-grid-content m-b-8">
                            @foreach ($vendor_services as $vendor_service)
                                @foreach ($order_wedding_decorations_id as $o_w_decoration_id)
                                    @if ($vendor_service->id == $o_w_decoration_id)
                                        <div class="card">
                                            <div class="image-container">
                                                <a href="#" data-toggle="modal" data-target="#wedding-decoration-{{ $vendor_service->id }}">
                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}">
                                                </a>
                                            </div>
                                            <div class="name-card">
                                                <b>@lang('messages.Decorations')</b>
                                                <p>{{ $vendor_service->service }}</p>
                                            </div>
                                        </div>
                                        {{-- MODAL WEDDING DECORATION --------------------------------------------------------------------------------------------------------------- --}}
                                        <div class="modal fade" id="wedding-decoration-{{ $vendor_service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fi-trees"></i> @lang('messages.Decorations')</div>
                                                        </div>
                                                        <div class="card-banner m-b-8">
                                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}" loading="lazy">
                                                        </div>
                                                        <div class="card-text">
                                                            <div class="row ">
                                                                <div class="col-sm-4">
                                                                    <b>Service: </b><p>{!! $vendor_service->service !!}</p>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <b>Duration: </b><p>{!! $vendor_service->duration." ".$vendor_service->time !!}</p>
                                                                </div>
                                                                @if ($vendor_service->description)
                                                                    <div class="col-sm-12">
                                                                        <b>Description: </b><p>{!! $vendor_service->description !!}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="card-box-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>    
                @endif
                {{-- WEDDING MAKEUP --}}
                @if ($order_wedding_makeups_id !== 'null' and $order_wedding_makeups_id and $vendor_services)
                    <div class="col-md-6">
                        <div class="subtitle-card">@lang('messages.Make-up')</div>
                        <div class="card-grid-content m-b-8">
                            @foreach ($vendor_services as $vendor_service)
                                @foreach ($order_wedding_makeups_id as $o_w_makeup_id)
                                    @if ($vendor_service->id == $o_w_makeup_id)
                                        <div class="card">
                                            <div class="image-container">
                                                <a href="#" data-toggle="modal" data-target="#wedding-makeup-{{ $vendor_service->id }}">
                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}">
                                                </a>
                                            </div>
                                            <div class="name-card">
                                                <b>@lang('messages.Make-up')</b>
                                                <p>{{ $vendor_service->service }}</p>
                                            </div>
                                        </div>
                                        {{-- MODAL WEDDING MAKEUP --------------------------------------------------------------------------------------------------------------- --}}
                                        <div class="modal fade" id="wedding-makeup-{{ $vendor_service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy ion-paintbrush"></i> @lang('messages.Make-up')</div>
                                                        </div>
                                                        <div class="card-banner m-b-8">
                                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}" loading="lazy">
                                                        </div>
                                                        <div class="card-text">
                                                            <div class="row ">
                                                                <div class="col-sm-4">
                                                                    <b>Service: </b><p>{!! $vendor_service->service !!}</p>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <b>Duration: </b><p>{!! $vendor_service->duration." ".$vendor_service->time !!}</p>
                                                                </div>
                                                                @if ($vendor_service->description)
                                                                    <div class="col-sm-12">
                                                                        <b>Description: </b><p>{!! $vendor_service->description !!}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="card-box-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>    
                @endif
                {{-- WEDDING ENTERTAINMENT --}}
                @if ($order_wedding_entertainments_id !== 'null' and $order_wedding_entertainments_id and $vendor_services)
                    <div class="col-md-6">
                        <div class="subtitle-card">@lang('messages.Entertainment')</div>
                        <div class="card-grid-content m-b-8">
                            @foreach ($vendor_services as $vendor_service)
                                @foreach ($order_wedding_entertainments_id as $o_w_entertainment_id)
                                    @if ($vendor_service->id == $o_w_entertainment_id)
                                        <div class="card">
                                            <div class="image-container">
                                                <a href="#" data-toggle="modal" data-target="#wedding-entetainment-{{ $vendor_service->id }}">
                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}">
                                                </a>
                                            </div>
                                            <div class="name-card">
                                                <b>@lang('messages.Entertainment')</b>
                                                <p>{{ $vendor_service->service }}</p>
                                            </div>
                                        </div>
                                        {{-- MODAL WEDDING ENTERTAINMENT --------------------------------------------------------------------------------------------------------------- --}}
                                        <div class="modal fade" id="wedding-entetainment-{{ $vendor_service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy ion-android-color-palette"></i> @lang('messages.Entertainment')</div>
                                                        </div>
                                                        <div class="card-banner m-b-8">
                                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}" loading="lazy">
                                                        </div>
                                                        <div class="card-text">
                                                            <div class="row ">
                                                                <div class="col-sm-4">
                                                                    <b>Service: </b><p>{!! $vendor_service->service !!}</p>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <b>Duration: </b><p>{!! $vendor_service->duration." ".$vendor_service->time !!}</p>
                                                                </div>
                                                                @if ($vendor_service->description)
                                                                    <div class="col-sm-12">
                                                                        <b>Description: </b><p>{!! $vendor_service->description !!}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="card-box-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>    
                @endif
                {{-- WEDDING DOCUMENTATION --}}
                @if ($order_wedding_documentation_ids !== 'null' and $order_wedding_documentation_ids and $vendor_services)
                    <div class="col-md-6">
                        <div class="subtitle-card">@lang('messages.Documentations')</div>
                        <div class="card-grid-content m-b-8">
                            @foreach ($vendor_services as $vendor_service)
                                @foreach ($order_wedding_documentation_ids as $order_wedding_documentation_id)
                                    @if ($vendor_service->id == $order_wedding_documentation_id)
                                        <div class="card">
                                            <div class="image-container">
                                                <a href="#" data-toggle="modal" data-target="#wedding-documentation-{{ $vendor_service->id }}">
                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}">
                                                </a>
                                            </div>
                                            <div class="name-card">
                                                <b>@lang('messages.Documentation')</b>
                                                <p>{{ $vendor_service->service }}</p>
                                            </div>
                                        </div>
                                        {{-- MODAL WEDDING DOCUMENTATION --------------------------------------------------------------------------------------------------------------- --}}
                                        <div class="modal fade" id="wedding-documentation-{{ $vendor_service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fi-camera"></i> @lang('messages.Documentation')</div>
                                                        </div>
                                                        <div class="card-banner m-b-8">
                                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}" loading="lazy">
                                                        </div>
                                                        <div class="card-text">
                                                            <div class="row ">
                                                                <div class="col-sm-4">
                                                                    <b>Service: </b><p>{!! $vendor_service->service !!}</p>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <b>Duration: </b><p>{!! $vendor_service->duration." ".$vendor_service->time !!}</p>
                                                                </div>
                                                                @if ($vendor_service->description)
                                                                    <div class="col-sm-12">
                                                                        <b>Description: </b><p>{!! $vendor_service->description !!}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="card-box-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>    
                @endif
                {{-- WEDDING TRANSPORT --}}
                @if ($order_wedding_transport_ids !== 'null' and $order_wedding_transport_ids and $vendor_wedding_transports)
                    <div class="col-md-6">
                        <div class="subtitle-card">@lang('messages.Transports')</div>
                        <div class="card-grid-content m-b-8">
                            @foreach ($vendor_wedding_transports as $vendor_wedding_transport)
                                @foreach ($order_wedding_transport_ids as $order_wedding_transport_id)
                                    @if ($vendor_wedding_transport->id == $order_wedding_transport_id)
                                        <div class="card">
                                            <div class="image-container">
                                                <a href="#" data-toggle="modal" data-target="#wedding-transport-{{ $vendor_wedding_transport->id }}">
                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/transports/transports-cover/' . $vendor_wedding_transport->cover) }}" alt="{{ $vendor_wedding_transport->service }}">
                                                </a>
                                            </div>
                                            <div class="name-card">
                                                <b>@lang('messages.Transport')</b>
                                                <p>{{ $vendor_wedding_transport->brand." ".$vendor_wedding_transport->name }}</p>
                                            </div>
                                        </div>
                                        {{-- MODAL WEDDING TRANSPORT --------------------------------------------------------------------------------------------------------------- --}}
                                        <div class="modal fade" id="wedding-transport-{{ $vendor_wedding_transport->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fi-camera"></i> @lang('messages.Transport')</div>
                                                        </div>
                                                        <div class="card-banner m-b-8">
                                                            <img class="rounded" src="{{ asset('storage/transports/transports-cover/' . $vendor_wedding_transport->cover) }}" alt="{{ $vendor_wedding_transport->service }}" loading="lazy">
                                                        </div>
                                                        <div class="card-text">
                                                            <div class="row ">
                                                                <div class="col-sm-4">
                                                                    <b>Service: </b><p>@lang('messages.Airport Shuttle')</p>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <b>Duration: </b><p>{!! $hotel->airport_duration." hours" !!}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-box-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @endif
                {{-- WEDDING OTHER SERVICES --}}
                @if ($order_wedding_other_ids !== 'null' and $order_wedding_other_ids and $vendor_services)
                    <div class="col-md-6">
                        <div class="subtitle-card">@lang('messages.Other Services')</div>
                        <div class="card-grid-content m-b-8">
                            @foreach ($vendor_services as $vendor_service)
                                @foreach ($order_wedding_other_ids as $order_wedding_other_id)
                                    @if ($vendor_service->id == $order_wedding_other_id)
                                        <div class="card">
                                            <div class="image-container">
                                                <a href="#" data-toggle="modal" data-target="#wedding-other-{{ $vendor_service->id }}">
                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}">
                                                </a>
                                            </div>
                                            <div class="name-card">
                                                <b>@lang('messages.Documentation')</b>
                                                <p>{{ $vendor_service->service }}</p>
                                            </div>
                                        </div>
                                        {{-- MODAL WEDDING OTHER SERVICES --------------------------------------------------------------------------------------------------------------- --}}
                                        <div class="modal fade" id="wedding-other-{{ $vendor_service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fi-camera"></i> @lang('messages.Documentation')</div>
                                                        </div>
                                                        <div class="card-banner m-b-8">
                                                            <img class="rounded" src="{{ asset('storage/vendors/package/' . $vendor_service->cover) }}" alt="{{ $vendor_service->service }}" loading="lazy">
                                                        </div>
                                                        <div class="card-text">
                                                            <div class="row ">
                                                                <div class="col-sm-4">
                                                                    <b>Service: </b><p>{!! $vendor_service->service !!}</p>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <b>Duration: </b><p>{!! $vendor_service->duration." ".$vendor_service->time !!}</p>
                                                                </div>
                                                                @if ($vendor_service->description)
                                                                    <div class="col-sm-12">
                                                                        <b>Description: </b><p>{!! $vendor_service->description !!}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="card-box-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    @endif
                </div>    
                
            </div>
            {{-- BRIDAL DETAIL  --}}
            <div class="tab-inner-title">@lang('messages.Bridal details')</div>
            <div class="form-group row m-b-18">
                <div class="col-md-4">
                    <label for="groom_name">@lang('messages.Groom Name')</label>
                    <input type="text" name="groom_name" class="form-control m-0 @error('groom_name') is-invalid @enderror" placeholder="@lang('messages.Insert groom name')" value="{{ $bride->groom }}" required>
                    @error('groom_name')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="groom_chinese">@lang('messages.Groom Chinese Name')</label>
                    <input type="text" name="groom_chinese" class="form-control m-0 @error('groom_chinese') is-invalid @enderror" placeholder="@lang('messages.Insert groom name')" value="{{ $bride->groom_chinese }}">
                    @error('groom_chinese')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="groom_contact">@lang('messages.Groom Contact')</label>
                    <input type="text" name="groom_contact" class="form-control m-0 @error('groom_contact') is-invalid @enderror" placeholder="@lang('messages.Insert number')" value="{{ $bride->groom_contact }}">
                    @error('groom_contact')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="bride_name">@lang('messages.Bride Name')</label>
                    <input type="text" name="bride_name" class="form-control m-0 @error('bride_name') is-invalid @enderror" placeholder="@lang('messages.Insert name')" value="{{ $bride->bride }}" required>
                    @error('bride_name')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="bride_chinese">@lang('messages.Bride Chinese Name')</label>
                    <input type="text" name="bride_chinese" class="form-control m-0 @error('bride_chinese') is-invalid @enderror" placeholder="@lang('messages.Insert name')" value="{{ $bride->bride_chinese }}">
                    @error('bride_chinese')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="bride_contact">@lang('messages.Bride Contact')</label>
                    <input type="text" name="bride_contact" class="form-control m-0 @error('bride_contact') is-invalid @enderror" placeholder="@lang('messages.Insert number')" value="{{ $bride->bride_contact }}">
                    @error('bride_contact')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="checkin">@lang('messages.Check In')</label>
                    <input readonly type="text" name="checkin" placeholder="@lang('messages.Select date')" class="form-control date-picker @error('checkin') is-invalid @enderror" value="{{ date('d M Y',strtotime($order->checkin)) }}" required>
                    @error('checkin')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label for="wedding_date">@lang('messages.Wedding Date')</label>
                    <input readonly type="text" name="wedding_date" placeholder="@lang('messages.Select date')" class="form-control date-picker @error('wedding_date') is-invalid @enderror" value="{{ date('d M Y',strtotime($order_wedding->wedding_date)) }}" required>
                    @error('wedding_date')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="number_of_guests">@lang('messages.Number of invitations')</label>
                    <input type="number" min="5" max="{{ $wedding->capacity }}" name="number_of_guests" class="form-control m-0 @error('number_of_guests') is-invalid @enderror" placeholder="@lang('messages.Insert number')" value="{!! $order->number_of_guests !!}" required>
                    @error('number_of_guests')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="tab-inner-title m-t-8">@lang('messages.Flight Detail')</div>
            <div class="form-group row m-b-18">
                <div class="col-md-6">
                    <label for="arrival_flight">@lang('messages.Arrival Flight')</label>
                    <input type="text" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror" placeholder="@lang('messages.Arrival Flight')" value="{{ $order->arrival_flight }}">
                    @error('arrival_flight')
                        <div
                            class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="arrival_time">@lang('messages.Arrival Date and Time')</label>
                    <input readonly type="text" name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ date('Y-m-d H.i  ',strtotime($order->arrival_time)) }}">
                    @error('arrival_time')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="departure_flight">@lang('messages.Departure Flight')</label>
                    <input type="text" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror" placeholder="@lang('messages.Departure Flight')" value="{{ $order->departure_flight }}">
                    @error('departure_flight')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
        
                <div class="col-md-6">
                    <label for="departure_time"> @lang('messages.Departure Date and Time')</label>
                    <input readonly type="text" name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ date('Y-m-d H.i  ',strtotime($order->departure_time)) }}">
                    @error('departure_time')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="tab-inner-title">@lang('messages.Note')</div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <textarea id="note" name="note" placeholder="@lang('messages.Optional')" class="textarea_editor form-control border-radius-0">{{ $order->note }}</textarea>
                        @error('note')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
            @if ($wedding->payment_process)
                <div class="tab-inner-title">@lang('messages.Payment Process')</div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="data-web-text-area">
                            {!! $wedding->payment_process !!} 
                        </div>
                    </div>
                </div>
            @endif
            @if ($wedding->remark)
                <div class="tab-inner-title">@lang('messages.Remark')</div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="data-web-text-area">{!! $wedding->remark !!} </div>
                    </div>
                </div>
            @endif
            @if ($wedding->cancellation_policy)
                <div class="tab-inner-title">@lang('messages.Cancellation Policy')</div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="data-web-text-area">{!! $wedding->cancellation_policy !!} </div>
                    </div>
                </div>
            @endif
            <div class="tab-inner-title">@lang('messages.Price')</div>
            <div class="row">
                <div class="col-md-12 m-b-8">
                    <div class="box-price-kicked">
                        <div class="row">
                            <div class="col-6 col-md-6">
                                {{-- <div class="promo-text">@lang('messages.Price')/@lang('messages.pax')</div> --}}
                                @if ($order->bookingcode_disc > 0 or $order->discounts > 0 or $order->kick_back > 0 or $order->promotion_disc > 0)
                                    <div class="promo-text">@lang('messages.Normal Price')</div>
                                    <hr class="form-hr">
                                    @if ($order->kick_back > 0)
                                        <div class="kick-back">@lang('messages.Kick Back')</div>
                                    @endif
                                    @if ($order->bookingcode_disc > 0)
                                        <div class="promo-text">@lang('messages.Booking Code')</div>
                                    @endif
                                    @if ($order->discounts > 0)
                                        <div class="promo-text">@lang('messages.Discounts')</div>
                                    @endif
                                    @php
                                        $tpro = json_decode($order->promotion_disc);
                                        $pro_name = json_decode($order->promotion_name);
                                        if (isset($pro_name)) {
                                            $cpro = count($pro_name);
                                        }else {
                                            $cpro = 0;
                                        }
                                        if (isset($tpro)) {
                                            $total_promotion = array_sum($tpro);
                                        }else {
                                            $total_promotion = 0;
                                        }
                                        $promotion_name = "";
                                        for ($i=0; $i < $cpro ; $i++) { 
                                            $promotion_name = $promotion_name.$pro_name[$i];
                                        }
                                    @endphp
                                    @if ($total_promotion > 0)
                                        <div class="promo-text">@lang('messages.Promotion')</div>
                                    @endif
                                    @if ($order->kick_back > 0 or $order->bookingcode_disc > 0 or $order->discounts > 0 or $total_promotion > 0)
                                        <hr class="form-hr">
                                    @endif
                                @endif
                                <div class="price-name">@lang('messages.Total Price')</div>
                            </div>
                            <div class="col-6 col-md-6 text-right">
                                {{-- <div class="normal-text-07"><span style="color: black">{{ $order->price_pax }}</span></div> --}}
                                @if ($order->bookingcode_disc > 0 or $order->discounts > 0 or $order->kick_back > 0 or $order->promotion_disc > 0)
                                    <div class="promo-text"><span id="tour-normal-price">{{ number_format($order->normal_price) }}</span></div>
                                    <hr class="form-hr">
                                    @if ($order->kick_back > 0)
                                        <div class="kick-back">{{ "- $ ".number_format($order->kick_back) }}</div>
                                    @endif
                                    @if ($order->bookingcode_disc > 0)
                                        <div class="kick-back">{{ "- $ ".number_format($order->bookingcode_disc) }}</div>
                                    @endif

                                    @if ($order->discounts > 0)
                                        <div class="kick-back">{{ "- $ ".number_format($order->discounts) }}</div>
                                    @endif
                                    @if ($total_promotion > 0)
                                        <div class="kick-back">{{ "- $ ".number_format($total_promotion) }}</div>
                                    @endif
                                
                                    @if ($order->kick_back > 0 or $order->bookingcode_disc > 0 or $order->discounts > 0 or $total_promotion > 0)
                                        <hr class="form-hr">
                                    @endif
                                @endif
                                @if ($order->status == "Approved")
                                    <div class="price-tag"><span>{{ number_format($order->final_price) }}</span></div>
                                @else
                                    <div class="price-name text-right"><span>@lang('messages.To be advised')</span></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 ">
                    <div class="notif-modal text-left">
                        @if ($order->status == "Draft")
                            @if (Auth::user()->email == "" or Auth::user()->phone == "" or Auth::user()->office == "" or Auth::user()->address == "" or Auth::user()->country == "")
                                @lang('messages.Please complete your profile data first to be able to submit orders, by clicking this link') -> <a href="/profile">@lang('messages.Edit Profile')</a>
                            @else
                                @if ($order->status == "Invalid")
                                    @lang('messages.This order is invalid, please make sure all data is correct!')
                                @else
                                    @lang('messages.Please make sure all the data is correct before you submit the order!')
                                @endif
                            @endif
                        @elseif ($order->status == "Pending")
                            @lang('messages.We have received your order, we will contact you as soon as possible to validate the order!')
                        @elseif ($order->status == "Rejected")
                            {{ $order->msg }}
                        @elseif ($order->status == "Invalid")
                            {{ $order->msg }}
                        @endif
                    </div>
                </div>
            </div>
            <input type="hidden" name="service" value="Wedding Package">
            <input type="hidden" name="status" value="Pending">
            <input type="hidden" name="page" value="submit-tour-order">
            <input type="hidden" name="action" value="Submit Order">
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <input type="hidden" name="orderno" value="{{ $order->orderno }}">
            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
            <input type="hidden" name="name" value="{{ Auth::user()->name }}">
            <input type="hidden" name="email" value="{{ Auth::user()->email }}">
            <input type="hidden" name="bk_disc" value="{{ $order->bookingcode_disc }}">
            <input type="hidden" name="promotion_disc" value="{{ $order->promotion_disc }}">
            <input type="hidden" name="normal_price" value="{{ $order->normal_price }}">
            <input type="hidden" name="price_total" value="{{ $order->price_total }}">
            <input type="hidden" name="final_price" value="{{ $order->final_price }}">
            @foreach ($tour_prices as $tppp=>$tour_price_var)
                @php
                    $price_max_qty = $tour_price_var->max_qty;
                    $cr_pax = ceil($tour_price_var->contract_rate/$usdrates->rate)+$tour_price_var->markup;
                    $cr_pax_tax = ceil($cr_pax * ($taxes->tax/100));
                    $tour_price_pax = $cr_pax + $cr_pax_tax;
                @endphp
                <input type="hidden" id="qty_{{ $tppp }}" name="qty" value="{{ $tour_price_var->max_qty }}">
                <input type="hidden" id="price_max_qty_{{ $tppp }}" name="price_pax_qty" value="{{ $price_max_qty }}">
                <input type="hidden" id="tour_price_pax_{{ $tppp }}" name="tour_price_pax" value="{{ $tour_price_pax }}">
            @endforeach

        </Form>
        <div class="card-box-footer">
            @if ($order->status == "Draft")
                <div class="form-group">
                    
                    @if ($order->status != "Invalid")
                        @if (Auth::user()->email == "" or Auth::user()->phone == "" or Auth::user()->office == "" or Auth::user()->address == "" or Auth::user()->country == "")
                            <button type="button" class="btn btn-light"><i class="icon-copy fa fa-info" aria-hidden="true"> </i> @lang('messages.You cannot submit this order')</button>
                        @else
                            <button type="submit" form="edit-order" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Submit')</button>
                        @endif
                    @endif
                    <form id="removeOrder" class="hidden" action="/fremove-order/{{ $order->id }}"method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <input type="hidden" name="status" value="Removed">
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                    </form>
                    <button type="submit" form="removeOrder" class="btn btn-danger"><i class="icon-copy fa fa-trash-o" aria-hidden="true"></i> @lang('messages.Delete')</button>
                    <a href="/orders">
                        <button class="btn btn-dark"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Close')</button>
                    </a>
                   
                </div>
            @elseif ($order->status == "Rejected")
                <form id="removeOrder" class="hidden" action="/fremove-order/{{ $order->id }}"method="post" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <input type="hidden" name="status" value="Removed">
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                </form>
                <button type="submit" form="removeOrder" class="btn btn-danger"><i class="icon-copy fa fa-trash-o" aria-hidden="true"></i> @lang('messages.Delete')</button>
            @else
                <div class="form-group">
                    <a href="/orders">
                        <button type="button" class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                    </a>
                </div>
            @endif
        </div>
    </div>
    <div class="loading-icon hidden pre-loader">
        <div class="pre-loader-box">
            <div class="sys-loader-logo w3-center"> <img class="w3-spin" src="{{ asset('storage/icon/spinner.png') }}" alt="Bali Kami Wedding Logo"></div>
            <div class="loading-text">
                Submitting an Order...
            </div>
        </div>
    </div>
</div>