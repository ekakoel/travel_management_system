@php
    use Carbon\Carbon;
    $total_wedding_services = $wedding_order->venue_price + $wedding_order->makeup_price + $wedding_order->room_price + $wedding_order->documentation_price + $wedding_order->decoration_price + $wedding_order->dinner_venue_price + $wedding_order->entertainment_price + $wedding_order->transport_price + $wedding_order->other_price;
@endphp
@if ($order->status == "Approved" or $order->status == "Paid")
    <div class="col-md-4 mobile">
        <div class="card-box">
            <div class="card-box-title">
                <div class="subtitle"><i class="icon-copy fa fa-money" aria-hidden="true"></i> @lang('messages.Payment Status')</div>
            </div>
            @if ($invoice->due_date > $now)
                @if (isset($receipt))
                    @if ($receipt->status == "Paid")
                        <div class="pmt-container">
                            <i class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Paid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Paid on') : {{ dateFormat($receipt->payment_date) }}<br>
                            @lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#mobile-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="mobile-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="Receipt Image">
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($receipt->status == "Pending")
                        <div class="pmt-container pending">
                            <i class="icon-copy fa fa-clock-o" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.On Review')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                            <p>@lang('messages.Payment Confirmation') : {{ dateFormat($receipt->created_at) }}</p>
                            
                        </div>
                    @elseif($receipt->status == "Invalid")
                        <div class="pmt-container unpaid">
                            <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Invalid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <p><i style="color: red">{{ $receipt->note }}</i></p>
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#mobile-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="mobile-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="notification-text" style="margin-top: 8px; color:rgb(143, 0, 0);">
                                            {!! $receipt->note !!}
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="pmt-container unpaid">
                            <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Unpaid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#mobile-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="mobile-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="notification-text" style="margin-top: 8px; color:rgb(143, 0, 0);">
                                            {!! $receipt->note !!}
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="pmt-container pending">
                        <i class="icon-copy fa fa-hourglass" aria-hidden="true"></i>
                        <div class="pmt-status">
                            @lang('messages.Awaiting Payment')
                        </div>
                    </div>
                    <div class="pmt-des">
                        <b>{{ $invoice->inv_no }}</b>
                        <p>@lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                    </div>
                @endif
            @else
                @if (isset($receipt))
                    @if ($receipt->status == "Paid")
                        <div class="pmt-container">
                            <i class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Paid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $order->orderno." - ". $invoice->inv_no }}</b>
                            <p>@lang('messages.Paid on') : {{ dateFormat($receipt->payment_date) }}<br>
                            @lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                            
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#mobile-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="mobile-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="pmt-container unpaid">
                            <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Invalid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                    @endif
                @else
                    <div class="pmt-container unpaid">
                        <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                        <div class="pmt-status">
                            @lang('messages.Unpaid')
                        </div>
                    </div>
                    <div class="pmt-des">
                        <b>{{ $invoice->inv_no }}</b>
                        <p>@lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endif
<div class="col-md-8">
    <div class="card-box">
        <div class="card-box-title">
            <div class="subtitle"><i class="fa fa-eye"></i> @lang('messages.Order')</div>
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
                    {{ dateFormat($order->created_at) }}
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
                            {{ dateFormat($order->created_at) }}
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
                    @if ($order->status == "Confirmed")
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Reconfirm Date')
                            </td>
                            <td class="htd-2">
                                {{ date('D, d M y',strtotime($invoice->due_date)) }}
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="col-sm-6 text-right">
                @if ($order->status == "Active")
                    <div class="page-status order-status-active"> @lang('messages.Confirmed') <span>@lang('messages.Status'):</span></div>
                @elseif ($order->status == "Pending")
                    <div class="page-status order-status-pending">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                @elseif ($order->status == "Rejected")
                    <div class="page-status order-status-rejected">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                @elseif ($order->status == "Approved")
                    <div class="page-status order-status-approve">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                @elseif ($order->status == "Paid")
                    <div class="page-status order-status-paid">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                @elseif ($order->status == "Confirmed")
                    <div class="page-status order-status-confirm">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                @else
                    <div class="page-status" style="color: rgb(48, 48, 48)">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                @endif
            </div>
        </div>
        {{-- BRIDES DETAIL  --}}
        <div class="row">
            <div class="col-md-12">
                <div class="tab-inner-title m-t-8">@lang('messages.Brides Detail')</div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table tb-list">
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Groom')
                                </td>
                                <td class="htd-2">
                                    {{ $bride->groom." ".$bride->groom_chinese }}
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Phone')
                                </td>
                                <td class="htd-2">
                                    {{ $bride->groom_contact }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table tb-list">
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Bride')
                                </td>
                                <td class="htd-2">
                                    {{ $bride->bride." ".$bride->bride_chinese }}
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Phone')
                                </td>
                                <td class="htd-2">
                                    {{ $bride->bride_contact }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- WEDDING PACKAGE  --}}
        <div class="row">
            <div class="col-md-12">
                <div class="tab-inner-title m-t-8">@lang('messages.Wedding Packase')</div>
                <div class="row">
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
                                    {{ $hotel_wedding->name }}
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Number of Invitations')
                                </td>
                                <td class="htd-2">
                                    {{ $order->number_of_guests." guests" }}
                                </td>
                            </tr>
                            
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table tb-list">
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Duration')
                                </td>
                                <td class="htd-2">
                                    {{ $order->duration }} @lang('messages.Night')
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Check-in')
                                </td>
                                <td class="htd-2">
                                    {{ dateFormat($order->checkin) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Wedding Date')
                                </td>
                                <td class="htd-2">
                                    {{ dateFormat($order->wedding_date) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Check-out')
                                </td>
                                <td class="htd-2">
                                    {{ dateFormat($order->checkout) }}
                                </td>
                            </tr>
                            
                        </table>
                    </div>
                </div>
                @if ($order->include != "")
                    <div class="tab-inner-title m-t-8">@lang('messages.Include')</div>
                    <div class="page-text">
                        <p>{!! $order->include !!}</p>
                    </div>
                @endif
                @if ($wedding->include or $wedding->fixed_services_id)
                    <div class="page-text">
                        @if ($wedding->fixed_services_id)
                            @php
                                $fixed_services_id = json_decode($wedding->fixed_services_id);
                            @endphp
                            @if ($fixed_services)
                                @foreach ($fixed_services_id as $no_f_s=>$fixed_service_id)
                                    @php
                                        $f_service = $fixed_services->where('id',$fixed_service_id)->first();
                                    @endphp
                                    @if ($f_service)
                                        <p>{!! ++$no_f_s.". ".$f_service->service !!}</p>
                                    @endif
                                @endforeach
                            @endif
                        @endif
                    </div>
                @endif
                {{-- SERVICE --}}
                <div class="tab-inner-title m-t-8">@lang('messages.Service')</div>
                <div class="row m-b-8">
                    <div class="col-md-6">
                        <table class="table tb-list">
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Wedding Venue')
                                </td>
                                <td class="htd-2">
                                    @if ($wedding_venue !== "null" and $wedding_venue)
                                        @php
                                            $venues_id = json_decode($wedding_order->wedding_venue_id);
                                        @endphp
                                        @if ($venues_id)
                                            @foreach ($venues_id as $venueId)
                                                @php
                                                    $weddingVenue = $wedding_venue->where('id',$venueId)->first();
                                                @endphp
                                                {{ $weddingVenue->service }}
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Suites and Villas')
                                </td>
                                <td class="htd-2">
                                    @if ($wedding_suites_and_villas !== "null" and $wedding_suites_and_villas)
                                        @php
                                            $suites_and_villas_id = json_decode($wedding_order->wedding_room_id);
                                        @endphp
                                        @if ($suites_and_villas_id)
                                            @foreach ($suites_and_villas_id as $suite_and_villa)
                                                @php
                                                    $suiteAndVilla = $wedding_suites_and_villas->where('id',$suite_and_villa)->first();
                                                @endphp
                                                {{ $suiteAndVilla->rooms }}
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Decoration')
                                </td>
                                <td class="htd-2">
                                    @if ($wedding_decoration !== "null" and $wedding_decoration)
                                        @php
                                            $decoration_id = json_decode($wedding_order->wedding_decoration_id);
                                        @endphp
                                        @if ($decoration_id)
                                            @foreach ($decoration_id as $decorationId)
                                                @php
                                                    $weddingDecoration = $wedding_decoration->where('id',$decorationId)->first();
                                                @endphp
                                                {{ $weddingDecoration->service }}
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Make-up')
                                </td>
                                <td class="htd-2">
                                    @if ($weddings_makeup !== "null" and $weddings_makeup)
                                        @php
                                            $makeups_id = json_decode($wedding_order->wedding_makeup_id);
                                        @endphp
                                        @if ($makeups_id)
                                            @foreach ($makeups_id as $makeup_id)
                                                @php
                                                    $weddingMakeup = $weddings_makeup->where('id',$makeup_id)->first();
                                                @endphp
                                                {{ $weddingMakeup->service }}
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Dinner Venue')
                                </td>
                                <td class="htd-2">
                                    @if ($wedding_dinner_venue !== "null" and $wedding_dinner_venue)
                                        @php
                                            $dinner_venues_id = json_decode($wedding_order->wedding_dinner_venue_id);
                                        @endphp
                                        @if ($dinner_venues_id)
                                            @foreach ($dinner_venues_id as $dinner_venue_id)
                                                @php
                                                    $weddingDinnerVenue = $wedding_dinner_venue->where('id',$dinner_venue_id)->first();
                                                @endphp
                                                {{ $weddingDinnerVenue->service }}
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    @endif
                                    
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table tb-list">
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Entertainment')
                                </td>
                                <td class="htd-2">
                                    @if ($wedding_entertainment !== "null" and $wedding_entertainment)
                                        @php
                                            $entertainments_id = json_decode($wedding_order->wedding_entertainment_id);
                                        @endphp
                                        @if ($entertainments_id)
                                            @foreach ($entertainments_id as $entertainment_id)
                                                @php
                                                    $weddingEntertainment = $wedding_entertainment->where('id',$entertainment_id)->first();
                                                @endphp
                                                @if (count($entertainments_id)>1)
                                                    {{ $weddingEntertainment->service }}<br>
                                                @else
                                                    {{ $weddingEntertainment->service }}
                                                @endif
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Documentation')
                                </td>
                                <td class="htd-2">
                                    @if ($wedding_documentation !== "null" and $wedding_documentation)
                                        @php
                                            $documentations_id = json_decode($wedding_order->wedding_documentation_id);
                                        @endphp
                                        @if ($documentations_id)
                                            @foreach ($documentations_id as $documentation_id)
                                                @php
                                                    $weddingDocumentation = $wedding_documentation->where('id',$documentation_id)->first();
                                                @endphp
                                                @if (count($documentations_id)>1)
                                                    {{ $weddingDocumentation->service }}<br>
                                                @else
                                                    {{ $weddingDocumentation->service }}
                                                @endif
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Transport')
                                </td>
                                <td class="htd-2">
                                    @if ($wedding_transport !== "null" and $wedding_transport)
                                        @php
                                            $transports_id = json_decode($wedding_order->wedding_transport_id);
                                        @endphp
                                        @if ($transports_id)
                                            @foreach ($transports_id as $transport_id)
                                                @php
                                                    $weddingTransport = $wedding_transport->where('id',$transport_id)->first();
                                                @endphp
                                                @if (count($transports_id)>1)
                                                    {{ $weddingTransport->brand." ". $weddingTransport->name }}<br>
                                                @else
                                                    {{ $weddingTransport->brand." ". $weddingTransport->name }}
                                                @endif
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Other Services')
                                </td>
                                <td class="htd-2">
                                    @if ($wedding_other_service !== "null" and $wedding_other_service)
                                        @php
                                            $other_services_id = json_decode($wedding_order->wedding_other_id);
                                        @endphp
                                        @if ($other_services_id)
                                            @foreach ($other_services_id as $other_service_id)
                                                @php
                                                    $otherService = $wedding_other_service->where('id',$other_service_id)->first();
                                                @endphp
                                                @if (count($other_services_id)>1)
                                                    {{ $otherService->brand." ". $otherService->service }} <br>
                                                @else
                                                    {{ $otherService->brand." ". $otherService->service }}
                                                @endif
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    @if ($order->status == "Approved")
                        <div class="col-md-12">
                            <div class="box-price-kicked">
                                <div class="row">
                                    <div class="col-6 col-md-6">
                                        <div class="subtotal-text"> Total Services</div>
                                    </div>
                                    <div class="col-6 col-md-6 text-right">
                                        <div class="subtotal-price">{{ currencyFormatUsd(($total_wedding_services)) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- ADDITIONAL CHARGE --}}
        @if (isset($additional_service))
            <div class="tab-inner-title m-t-8">@lang('messages.Additional Services')</div>
            <div class="row">
                @if (isset($order->additional_service))
                    <div class="col-md-12">
                        <table class="data-table table nowrap" >
                            <thead>
                                <tr>
                                    <th style="width: 20%;">@lang('messages.Date')</th>
                                    <th style="width: 50%;">@lang('messages.Service')</th>
                                    <th style="width: 10%;">@lang('messages.QTY')</th>
                                    <th style="width: 10%;">@lang('messages.Price')</th>
                                    <th style="width: 10%;">@lang('messages.Total')</th>
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
                                    <div class="subtotal-price">{{ currencyFormatUsd(($total_additional_service)) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
        @if ($wedding->executive_staff != "")
            <div class="tab-inner-title m-t-8">@lang('messages.Additional Information')</div>
            <div class="page-text">
                <p>{!! $wedding->executive_staff !!}</p>
            </div>
        @endif
        @if ($order->additional_info != "")
            <div class="tab-inner-title m-t-8">@lang('messages.Additional Information')</div>
            <div class="page-text">
                <p>{!! $order->additional_info !!}</p>
            </div>
        @endif
        @if ($order->cancellation_policy != "")
            <div class="tab-inner-title m-t-8">@lang('messages.Cancellation Policy')</div>
            <div class="page-text">
                <p>{!! $order->cancellation_policy !!}</p>
            </div>
        @endif
        @if ($wedding->payment_process != "")
            <div class="tab-inner-title m-t-8">@lang('messages.Payment Process')</div>
            <div class="page-text">
                <p>{!! $wedding->payment_process !!}</p>
            </div>
        @endif
        {{-- NOTE REMARK --}}
        @if (isset($order->note))
            <div class="tab-inner-title m-t-8">@lang('messages.Note') / @lang('messages.Remark')</div>
            <div class="row">
                <div class="col-md-12">
                    <div class="page-text">
                        <p>{!! $order->note !!}</p>
                    </div>
                </div>
            </div>
        @endif
        <div class="tab-inner-title m-t-8">@lang('messages.Price')</div>
        <div class="row">
            <div class="col-md-12">
                <div class="box-price-kicked">
                    <div class="row">
                        <div class="col-6 col-md-6">
                            @if ($total_additional_service > 0)
                                <div class="promo-text">@lang('messages.Wedding Package')</div>
                                <div class="promo-text">@lang('messages.Services')</div>
                                <div class="promo-text">@lang('messages.Additional Services')</div>
                                <hr class="form-hr">
                            @else
                                <div class="promo-text">@lang('messages.Number of Guests')</div>
                            @endif
                            @if ($order->bookingcode_disc > 0 or $order->discounts > 0 or $order->kick_back > 0 or $order->promotion_disc > 0)
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
                            @if ($total_additional_service > 0)
                                <div class="promo-text">{{ currencyFormatUsd($wedding_order->fixed_service_price + $wedding->markup) }}</div>
                                <div class="promo-text">{{ currencyFormatUsd($total_wedding_services) }}</div>
                                <div class="promo-text">{{ currencyFormatUsd($total_additional_service) }}</div>
                                <hr class="form-hr">
                            @else
                                <div class="promo-text">{{ $order->number_of_guests }} @lang('messages.Guests')</div>
                            @endif
                            @if ($order->bookingcode_disc > 0 or $order->discounts > 0 or $order->kick_back > 0 or $order->promotion_disc > 0)
                                @if ($order->kick_back > 0)
                                    <div class="kick-back">{{ "- $ ".number_format($order->kick_back) }}</div>
                                @endif
                                @if ($order->bookingcode_disc > 0)
                                    <div class="kick-back">{{ "- $ ".number_format($order->bookingcode_disc) }}</div>
                                @endif

                                @if ($order->discounts > 0)
                                    <div class="kick-back">{{ number_format($order->discounts) }}</div>
                                @endif
                                @if ($total_promotion > 0)
                                    <div class="kick-back">{{ number_format($total_promotion) }}</div>
                                @endif
                            
                                @if ($order->kick_back > 0 or $order->bookingcode_disc > 0 or $order->discounts > 0 or $total_promotion > 0)
                                    <hr class="form-hr">
                                @endif
                                
                            @endif
                            @if ($order->status !== "Pending")
                                <div class="usd-rate">{{ currencyFormatUsd($order->final_price) }}</div>
                            @else
                                <div class="usd-rate">@lang('messages.To be advised')</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @if ($order->status != "Active" )
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
                        @elseif ($order->status == "Confirmed")
                            @php
                                $due_date = Carbon::parse($invoice->due_date);
                                $dl = $due_date->diffInDays($now);
                                $day_left = $dl+1;
                            @endphp
                            @lang('messages.Please be advised that you are reminded to approve your order before the reconfirm date. Kindly ensure to complete the approval process before the specified deadline. Thank you for your cooperation.')<br>
                            @if ($day_left < 3 )
                                <p class="blink_me">{{ $day_left }} @lang('messages.days left before your order is automatically canceled.')</p>
                            @elseif($day_left < 6)
                                <p>{{ $day_left }} @lang('messages.days left before your order is automatically canceled.')</p>
                            @endif
                        @elseif ($order->status == "Approved")
                            @lang('messages.Your order has been approved. For the next step, please check the order details on the invoice for the payment process.')
                        @endif
                    </div>
                </div>
            @endif
        </div>
        <div class="card-box-footer">
            @if ($order->status == "Rejected")
                <form id="deleteOrder" class="display-content" action="/delete-order/{{ $order->id }}" method="post">
                    @csrf
                    @method('delete')
                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                </form>
                <button type="submit" form="deleteOrder" class="btn btn-dark" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i> Delete Order</button>
            @elseif($order->status == "Active" or $order->status == "Approved" or $order->status == "Paid")
                @if ($status_contract == 1)
                    @if ($receipt)
                        <a href="#" data-toggle="modal" data-target="#payment-confirmation-{{ $order->id }}">
                            <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-upload" aria-hidden="true"></i> @lang('messages.Payment Confirmation')</button>
                        </a>
                        {{-- MODAL PAYMENT CONFIRMATION --}}
                        <div class="modal fade" id="payment-confirmation-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="card-box">
                                        <div class="card-box-title text-left">
                                            <div class="title"><i class="icon-copy fa fa-usd" aria-hidden="true"></i>@lang('messages.Payment Confirmation')</div>
                                        </div>
                                        <form id="payment-confirm-{{ $order->id }}" action="/fpayment-confirmation-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row text-left">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="row m-t-27">
                                                                <div class="col-5"><p>@lang('messages.Order Number')</p></div>
                                                                <div class="col-7"><p><b>: {{ $order->orderno }}</b></p></div>
                                                                <div class="col-5"><p>@lang('messages.Reservation Number')</p></div>
                                                                <div class="col-7"><p><b>: {{ $reservation->rsv_no }}</b></p></div>
                                                                <div class="col-5"><p>@lang('messages.Invoice Number')</p></div>
                                                                <div class="col-7"><p><b>: {{ $invoice->inv_no }}</b></p></div>
                                                                <div class="col-5"><p>@lang('messages.Due Date')</p></div>
                                                                <div class="col-7"><p>: {{ dateFormat($invoice->due_date) }}</p></div>
                                                                <div class="col-5"><p>@lang('messages.Amount')</p></div>
                                                                <div class="col-7"><p><b>: {{ currencyFormatUsd($order->final_price) }}</b></p></div>
                                                                <div class="col-12 m-t-18"><p><i class="icon-copy fa fa-exclamation" aria-hidden="true"></i> @lang('messages.Please make the payment before the due date and provide proof of payment to prevent the cancellation of your order.')</p></div>
                                                            </div>
                                                        </div>

                                                        {{-- Baru sampai disini "Merubah bahasa" ============================================================================ --}}
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="cover" class="form-label">@lang('messages.Receipt Image')</label>
                                                                <div class="dropzone">
                                                                    <div class="tour-receipt-div">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="receipt_name" class="form-label">@lang('messages.Select Receipt')</label><br>
                                                                <input type="file" name="receipt_name" id="receipt_name" class="custom-file-input @error('receipt_name') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('receipt_name') }}" required>
                                                                @error('receipt_name')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                            </div>
                                        </form>
                                        <div class="card-box-footer">
                                            <button type="submit" form="payment-confirm-{{ $order->id }}" class="btn btn-primary"><i class="icon-copy fa fa-upload" aria-hidden="true"></i> @lang('messages.Send')</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    @endif
                    @if (config('app.locale') == "zh")
                        <a href="#" data-toggle="modal" data-target="#contract-zh-{{ $order->id }}">
                            <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> 發票</button>
                        </a>
                        <a href='{{URL::to('/')}}/storage/document/invoice-{{ $inv_no }}-{{ $order->id }}_zh.pdf' target="_blank">
                            <button type="button" class="btn btn-primary mobile"><i class="fa fa-download"></i> 下載發票</button>
                        </a>
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
                    @else
                        <a href="#" data-toggle="modal" data-target="#contract-en-{{ $order->id }}">
                            <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> @lang('messages.Invoice')</button>
                        </a>
                        <a href='{{URL::to('/')}}/storage/document/invoice-{{ $inv_no }}-{{ $order->id }}_en.pdf' target="_blank">
                            <button type="button" class="btn btn-primary mobile"><i class="fa fa-download"></i> @lang('messages.Download Invoice')</button>
                        </a>
                        {{-- MODAL VIEW CONTRACT EN --}}
                        <div class="modal fade" id="contract-en-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                    <div class="modal-body pd-5">
                                        {{-- storage/document/invoice-".$inv_no."-".$order->id.".pdf" --}}
                                        <embed src="storage/document/invoice-{{ $inv_no."-".$order->id }}_en.pdf" frameborder="10" width="100%" height="850px">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="notification-order text-left">
                        <i>@lang('messages.Waiting for Contract')</i>
                    </div>
                @endif
            @endif
            <a href="/orders">
                <button type="button" class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
            </a>
            
        </div>
    </div>
</div>
@if ($order->status == "Active" or $order->status == "Approved" or $order->status == "Paid")
    <div class="col-md-4 desktop">
        <div class="card-box">
            <div class="card-box-title">
                <div class="title">@lang('messages.Payment Information')</div>
            </div>
            <b>@lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</b>
            <p>Payment can be made in two ways, deposit or settlement</p>
            <hr class="form-hr">
            <div class="row">
                @if ($receipt)
                    @foreach ($receipt as $rcpt)
                       
                            <div class="col-md-2">
                                <div class="pmt-container">
                                    <i class="icon-copy fa fa-file-image-o" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="pmt-des">
                                    @if ($rcpt->status == "Valid")
                                        <b>
                                            {{ $invoice->inv_no }}
                                            <i data-toggle="tooltip" data-placement="top" title="@lang('messages.Verified')" style="color: rgb(0, 167, 14); cursor:help" class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                                        </b>
                                        <p><i>Recived on : {{ dateFormat($rcpt->created_at) }}</i></p>
                                        {!! $rcpt->note !!}
                                        Amount : {{ currencyFormatUsd($rcpt->amount) }}
                                    @elseif($rcpt->status == "Invalid")
                                        <b><i style="font-size: 0.7rem; color:rgb(255 0 0);">{{ $rcpt->status }}</i></b>
                                        {!! $rcpt->note !!}
                                        <p>We regret to inform you that your transfer proof has not been approved. Please upload the correct transfer proof to proceed with your transaction.</p>
                                    @else
                                        <b>
                                            {{ $invoice->inv_no }}
                                            <i data-toggle="tooltip" data-placement="top" title="@lang('messages.Pending')" style="color: rgb(0, 89, 255); cursor:help" class="icon-copy fa fa-clock-o" aria-hidden="true"></i>
                                        </b>
                                        <p><i>Recived on : {{ dateFormat($rcpt->created_at) }}</i></p>
                                       
                                    @endif
                                
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="view-receipt">
                                    <a class="action-btn" href="#" data-toggle="modal" data-target="#receipt-{{ $rcpt->id }}">
                                        <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                    @if ($rcpt->status == "Invalid")
                                        <a href="#" data-toggle="modal" data-target="#update-receipt-{{ $rcpt->id }}">
                                            <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                                        </a>
                                        {{-- MODAL Edit PAYMENT CONFIRMATION --}}
                                        <div class="modal fade" id="update-receipt-{{ $rcpt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="card-box">
                                                        <div class="card-box-title text-left">
                                                            <div class="title"><i class="icon-copy fa fa-usd" aria-hidden="true"></i>@lang('messages.Payment Confirmation')</div>
                                                        </div>
                                                        <form id="update-payment-confirm-{{ $order->id }}" action="/fupdate-payment-confirmation/{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('put')
                                                            <div class="row text-left">
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            <div class="row m-t-27">
                                                                                <div class="col-5"><p>@lang('messages.Order Number')</p></div>
                                                                                <div class="col-7"><p><b>: {{ $order->orderno }}</b></p></div>
                                                                                <div class="col-5"><p>@lang('messages.Reservation Number')</p></div>
                                                                                <div class="col-7"><p><b>: {{ $reservation->rsv_no }}</b></p></div>
                                                                                <div class="col-5"><p>@lang('messages.Invoice Number')</p></div>
                                                                                <div class="col-7"><p><b>: {{ $invoice->inv_no }}</b></p></div>
                                                                                <div class="col-5"><p>@lang('messages.Due Date')</p></div>
                                                                                <div class="col-7"><p>: {{ dateFormat($invoice->due_date) }}</p></div>
                                                                                <div class="col-5"><p>@lang('messages.Amount')</p></div>
                                                                                <div class="col-7"><p><b>: {{ currencyFormatUsd($order->final_price) }}</b></p></div>
                                                                                <div class="col-12 m-t-18"><p><i class="icon-copy fa fa-exclamation" aria-hidden="true"></i> @lang('messages.Please make the payment before the due date and provide proof of payment to prevent the cancellation of your order.')</p></div>
                                                                            </div>
                                                                        </div>
                
                                                                        
                                                                        <div class="col-sm-6">
                                                                            <div class="form-group">
                                                                                <label for="cover" class="form-label">@lang('messages.Receipt Image')</label>
                                                                                <div class="dropzone">
                                                                                    <div class="update-receipt-div">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label for="activity_receipt_name" class="form-label">@lang('messages.Select Receipt')</label><br>
                                                                                <input type="file" name="activity_receipt_name" id="activity_receipt_name" class="custom-file-input @error('activity_receipt_name') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('activity_receipt_name') }}" required>
                                                                                @error('activity_receipt_name')
                                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="receipt_id" value="{{ $rcpt->id }}">
                                                            </div>
                                                        </form>
                                                        <div class="card-box-footer">
                                                            <button type="submit" form="update-payment-confirm-{{ $order->id }}" class="btn btn-primary"><i class="icon-copy fa fa-upload" aria-hidden="true"></i> @lang('messages.Send')</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr class="form-hr">
                            </div>
                            {{-- MODAL Detail PAYMENT CONFIRMATION --}}
                            <div class="modal fade" id="receipt-{{ $rcpt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content modal-img">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                            </div>
                                            
                                            <img style="height: 630px !important" src="{{ asset('storage/receipt/'.$rcpt->receipt_img) }}" alt="Receipt">
                                            
                                            <div class="card-box-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        
                    @endforeach
                    @php
                        $total_payment = $receipt->sum("amount");
                        $balance_payment = $order->final_price - $total_payment;
                    @endphp
                
                    <div class="col-6">
                        <p>Total Payment :</p>
                    </div>
                    <div class="col-6 text-right">
                        <p>{{ currencyFormatUsd($total_payment) }}</p>
                    </div>
                    <div class="col-6">
                        <p>Total Invoice :</p>
                    </div>
                    <div class="col-6 text-right">
                        <p>{{ currencyFormatUsd($order->final_price) }}</p>
                    </div>
                    <div class="col-12">
                        <hr class="form-hr">
                    </div>
                    @if ($balance_payment < 0)
                        <div class="col-6">
                            <b>Deposit :</b>
                        </div>
                        <div class="col-6 text-right">
                            <b>{{ currencyFormatUsd($balance_payment*-1) }}</b>
                        </div>
                    @else
                        <div class="col-6">
                            <b>Balance Payment :</b>
                        </div>
                        <div class="col-6 text-right">
                            <b style="color:rgb(153, 0, 0);">{{ "- $ ".number_format($balance_payment) }}</b>
                        </div>
                    @endif
                @else
                <div class="col-md-12">
                    <p>@lang('messages.Important! After you have made the payment, please remember to promptly upload your payment proof before the due date!')</p>
                </div>
                @endif
            </div>
        </div>
    </div>
            {{-- @if ($invoice->due_date > $now)
                @if (isset($receipt))
                    @if ($receipt->status == "Paid")
                        <div class="pmt-container">
                            <i class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Paid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Paid on') : {{ dateFormat($receipt->payment_date) }}<br>
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#paid-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="paid-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($receipt->status == "Pending")
                        <div class="pmt-container pending">
                            <i class="icon-copy fa fa-clock-o" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.On Review')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                            <p>@lang('messages.Payment Confirmation') : {{ dateFormat($receipt->created_at) }}</p>
                            
                        </div>
                    @elseif($receipt->status == "Invalid")
                        <div class="pmt-container unpaid">
                            <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Invalid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <p><i style="color: red">{{ $receipt->note }}</i></p>
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#invalid-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="invalid-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="notification-text" style="margin-top: 8px; color:rgb(143, 0, 0);">
                                            {!! $receipt->note !!}
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="pmt-container unpaid">
                            <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Unpaid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#payment-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="payment-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="notification-text" style="margin-top: 8px; color:rgb(143, 0, 0);">
                                            {!! $receipt->note !!}
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="pmt-container pending">
                        <i class="icon-copy fa fa-hourglass" aria-hidden="true"></i>
                        <div class="pmt-status">
                            @lang('messages.Awaiting Payment')
                        </div>
                    </div>
                    <div class="pmt-des">
                        <b>{{ $invoice->inv_no }}</b>
                        <p>@lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                    </div>
                @endif
            @else
                @if (isset($receipt))
                    @if ($receipt->status == "Paid")
                        <div class="pmt-container">
                            <i class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Paid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $order->orderno." - ". $invoice->inv_no }}</b>
                            <p>@lang('messages.Paid on') : {{ dateFormat($receipt->payment_date) }}<br>
                            @lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                            
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="pmt-container unpaid">
                            <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Invalid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                    @endif
                @else
                    <div class="pmt-container unpaid">
                        <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                        <div class="pmt-status">
                            @lang('messages.Unpaid')
                        </div>
                    </div>
                    <div class="pmt-des">
                        <b>{{ $invoice->inv_no }}</b>
                        <p>@lang('messages.Payment dateline') : {{ dateFormat($invoice->due_date) }}</p>
                    </div>
                @endif
            @endif --}}
        
@endif
