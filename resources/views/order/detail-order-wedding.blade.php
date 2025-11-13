@section('title', __('messages.Orders'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="info-action">
                @if (session('errors_message'))
                    <div class="alert alert-danger">
                        {{ session('errors_message') }}
                    </div>
                @endif
                @if (\Session::has('error'))
                    <div class="alert alert-danger">
                        <ul>
                            <li>{!! \Session::get('error') !!}</li>
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
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <i class="icon-copy fa fa-shopping-basket" aria-hidden="true"></i>@lang('messages.Wedding Order')
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="orders">@lang('messages.Order')</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $orderWedding->orderno }}</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fa fa-eye" aria-hidden="true"></i>@lang('messages.Detail Order')</div>
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
                                    {{ date("m/d/y", strtotime($orderWedding->created_at)) }}
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
                                            <b>{{ $orderWedding->orderno }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            @lang('messages.Order Date')
                                        </td>
                                        <td class="htd-2">
                                            {{ date("m/d/y", strtotime($orderWedding->created_at)) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            @lang('messages.Service')
                                        </td>
                                        <td class="htd-2">
                                            @lang('messages.Wedding')
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            @lang('messages.Sub Service')
                                        </td>
                                        <td class="htd-2">
                                            {{ $orderWedding->service }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                @if ($orderWedding->status == "Active")
                                    <div class="page-status" style="color: rgb(0, 156, 21)"> @lang('messages.Confirmed') <span>@lang('messages.Status'):</span></div>
                                @elseif ($orderWedding->status == "Pending")
                                    <div class="page-status" style="color: #dd9e00">@lang('messages.'.$orderWedding->status) <span>@lang('messages.Status'):</span></div>
                                @elseif ($orderWedding->status == "Rejected")
                                    <div class="page-status" style="color: rgb(160, 0, 0)">@lang('messages.'.$orderWedding->status) <span>@lang('messages.Status'):</span></div>
                                @else
                                    <div class="page-status" style="color: rgb(48, 48, 48)">@lang('messages.'.$orderWedding->status) <span>@lang('messages.Status'):</span></div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            
                            {{-- BRIDE --}}
                            <div id="bride" class="col-md-12">
                                <div class="page-subtitle">
                                    @lang('messages.Brides')
                                </div>
                                <div class="card-ptext-margin">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            @if ($bride->groom_pasport_id)
                                                <table class="table tb-list">
                                            @else
                                                <table class="table tb-list incomplate-form-table">
                                            @endif
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang("messages.Groom")
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ $bride->groom }}
                                                        @if ($bride->groom_chinese)
                                                            ({{ $bride->groom_chinese }})
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Passport') / @lang('messages.ID')
                                                    </td>
                                                    <td class="htd-2">
                                                        @if ($bride->groom_pasport_id)
                                                            {{ $bride->groom_pasport_id }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-sm-6">
                                            @if ($bride->bride_pasport_id)
                                                <table class="table tb-list">
                                            @else
                                                <table class="table tb-list incomplate-form-table">
                                            @endif
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang("messages.Bride's")
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ $bride->bride }}
                                                        @if ($bride->bride_chinese)
                                                            ({{ $bride->bride_chinese }})
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Passport') / @lang('messages.ID')
                                                    </td>
                                                    <td class="htd-2">
                                                        @if ($bride->bride_pasport_id)
                                                            {{ $bride->bride_pasport_id }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- FLIGHT --}}
                            @if (count($flights)>0)
                                <div id="Flight" class="col-md-12">
                                    <div class="page-subtitle">
                                        @lang('messages.Flight Schedule')
                                    </div>
                                    <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                                        <thead>
                                            <tr>
                                                <th style="width: 20%" class="datatable-nosort">@lang('messages.Date')</th>
                                                <th style="width: 20%" class="datatable-nosort">@lang('messages.Flight')</th>
                                                <th style="width: 20%" class="datatable-nosort">@lang('messages.Type')</th>
                                                <th style="width: 20%" class="datatable-nosort">@lang('messages.Guests')</th>
                                                <th style="width: 25%" class="datatable-nosort">@lang('messages.Responsible Person')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($flights as $flight)
                                                <tr>
                                                    <td class="pd-2-8">
                                                        @if ($flight->time)
                                                                {{ dateTimeFormat($flight->time) }}
                                                            @else
                                                                -
                                                            @endif
                                                    </td>
                                                    <td class="pd-2-8">
                                                        @if ($flight->flight)
                                                            {{ $flight->flight }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="pd-2-8">
                                                        {{ $flight->type }}
                                                    </td>
                                                    
                                                    
                                                    <td class="pd-2-8">
                                                        {{ $flight->number_of_guests }}
                                                        @if ($flight->group == "Brides")
                                                            <i>(@lang("messages.Brides"))</i>
                                                        @endif
                                                    </td>
                                                    <td class="pd-2-8">
                                                        {{ $flight->guests?$flight->guests:"-"; }} {{ $flight->guests_contact? " ".$flight->guests_contact:""; }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            {{-- INVITATIONS --}}
                            @if (count($invitations)>0)
                                <div id="Flight" class="col-md-12">
                                    <div class="page-subtitle">
                                        @lang('messages.Invitations')
                                    </div>
                                    <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                                        <thead>
                                            <tr>
                                                <th class="datatable-nosort">@lang('messages.No')</th>
                                                <th class="datatable-nosort">@lang('messages.ID')/@lang('messages.Passport')</th>
                                                <th class="datatable-nosort">@lang('messages.Name')</th>
                                                <th class="datatable-nosort">@lang('messages.Country')</th>
                                                <th class="datatable-nosort">@lang('messages.Contact')</th>
                                                <th class="datatable-nosort"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invitations as $noInv=>$invitation)
                                            <tr>
                                                <td class="pd-2-8">
                                                    {{ ++$noInv }}
                                                </td>
                                                <td class="pd-2-8">
                                                    {{ $invitation->passport_no }}
                                                </td>
                                                <td class="pd-2-8">
                                                    {{ $invitation->name }}
                                                    @if ($invitation->chinese_name)
                                                        ({{ $invitation->chinese_name }})
                                                    @endif
                                                </td>
                                                <td class="pd-2-8">
                                                    {{ $invitation->countries->country_name }}
                                                </td>
                                                <td class="pd-2-8">
                                                    {{ $invitation->phone?$invitation->phone:"-" }}
                                                </td>
                                                <td class="pd-2-8 text-right">
                                                    <a href="#" data-toggle="modal" data-target="#detail-invitation-{{ $invitation->id }}">
                                                        <span>
                                                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                        </span>
                                                    </a>
                                                    {{-- MODAL DETAIL INVITATION --}}
                                                    <div class="modal fade" id="detail-invitation-{{ $invitation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-user"></i> @lang('messages.Invitation')</div>
                                                                    </div>
                                                                    <div class="page-card">

                                                                        <div class="card-banner">
                                                                            <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/guests/id_passport/' . $invitation->passport_img) }}" alt="{{ $invitation->name }}">
                                                                        </div>
                                                                        <div class="card-content">
                                                                            <div class="card-text">
                                                                                <div class="card-subtitle">
                                                                                    {{ $invitation->name }}
                                                                                    @if ($invitation->chinese_name)
                                                                                        ({{ $invitation->chinese_name }})
                                                                                    @endif
                                                                                </div>
                                                                                <p>{{ $invitation->country }}</p>
                                                                                <p>{{ $invitation->phone }}</p>
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
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            {{-- WEDDING --}}
                            <div id="wedding" class="col-md-6 m-b-8">
                                <div class="page-subtitle">
                                    @if ($orderWedding->service == "Ceremony Venue")
                                        @if ($orderWedding->reception_venue_id)
                                            @lang('messages.Wedding') & @lang('messages.Reception')
                                        @else
                                            @lang('messages.Wedding')
                                        @endif
                                    @elseif ($orderWedding->service == "Reception Venue")
                                        @if ($orderWedding->ceremony_venue_id)
                                            @lang('messages.Wedding') & @lang('messages.Reception')
                                        @else
                                            @lang('messages.Reception')
                                        @endif
                                    @elseif ($orderWedding->service == "Wedding Package")
                                        @lang('messages.Wedding') & @lang('messages.Reception')
                                    @endif
                                </div>
                                <div class="card-ptext-margin">
                                    <table class="table tb-list">
                                        @if ($orderWedding->wedding_date)
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Wedding Date')
                                                </td>
                                                <td class="htd-2">
                                                    {{ date("m/d/y",strtotime($orderWedding->wedding_date)) }} ({{ date('H.i',strtotime($orderWedding->slot)) }})
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($orderWedding->reception_date_start)
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Reception Date')
                                                </td>
                                                <td class="htd-2">
                                                    {{ dateTimeFormat($orderWedding->reception_date_start) }}
                                                </td>
                                            </tr>
                                        @endif
                                        
                                    </table>
                                </div>
                            </div>
                            {{-- WEDDING VENUE --}}
                            <div id="weddingVenue" class="col-md-6">
                                <a href="#" data-toggle="modal" data-target="#detail-wedding-venue-{{ $hotel->id }}"> 
                                    <div class="page-subtitle">
                                        @lang('messages.Wedding Venue')
                                        <span>
                                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                </a>
                                {{-- MODAL DETAIL WEDDING VENUE --}}
                                <div class="modal fade" id="detail-wedding-venue-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content text-left">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="subtitle"><i class="icon-copy dw dw-hotel"></i>@lang('messages.Wedding Venue')</div>
                                                </div>
                                                <div class="card-banner">
                                                    <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/hotels/hotels-cover/' . $hotel->cover) }}" alt="{{ $hotel->type }}">
                                                </div>
                                                <div class="card-content">
                                                    <div class="card-text">
                                                        <div class="row ">
                                                            <div class="col-sm-12 text-center">
                                                                <div class="card-subtitle">{{ $hotel->name }}</div>
                                                                @if ($orderWedding->service == "Ceremony Venue")
                                                                    <p>{{ date("m/d/y",strtotime($orderWedding->wedding_date)) }} ({{ date('H.i',strtotime($orderWedding->slot)) }})</p>
                                                                @elseif ($orderWedding->service == "Reception Venue")
                                                                    <p>{{ date("m/d/y",strtotime($orderWedding->reception_date_start)) }} ({{ date('H.i',strtotime($orderWedding->slot)) }})</p>
                                                                @elseif ($orderWedding->service == "Wedding Package")
                                                                    <p>
                                                                        @lang('messages.Wedding Date'): 
                                                                        {{ date("m/d/y",strtotime($orderWedding->wedding_date)) }} ({{ date('H.i',strtotime($orderWedding->slot)) }}) | 
                                                                        @lang('messages.Reception Date'):
                                                                        {{ date("m/d/y",strtotime($orderWedding->reception_date_start)) }}
                                                                    </p>
                                                                @endif
                                                                {!! $hotel->address !!}
                                                            </div>
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
                                <div class="card-ptext-margin">
                                    <table class="table tb-list">
                                        <tr>
                                            <td class="htd-1">
                                                @lang('messages.Hotel')
                                            </td>
                                            <td class="htd-2">
                                                {{ $hotel->name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="htd-1">
                                                @lang('messages.Address')
                                            </td>
                                            <td class="htd-2">
                                                {{ $hotel->address }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            {{-- CEREMONY VENUE --}}
                            @if ($ceremonyVenue)
                                <div id="ceremonyVenue" class="col-md-6">
                                    <a href="#" data-toggle="modal" data-target="#detail-ceremony-venue-{{ $ceremonyVenue->id }}">
                                        <div class="page-subtitle {{ $ceremonyVenue?"":"empty-value" }}">
                                            @lang('messages.Ceremony Venue')
                                            @if ($ceremonyVenue)
                                                <span>
                                                    <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                    @if ($ceremonyVenue)
                                        {{-- MODAL DETAIL CEREMONY VENUE --}}
                                        <div class="modal fade" id="detail-ceremony-venue-{{ $ceremonyVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fa fa-bank"></i>@lang('messages.Ceremony Venue')</div>
                                                        </div>
                                                        <div class="card-banner">
                                                            <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/hotels/hotels-wedding-venue/' . $ceremonyVenue->cover) }}" alt="{{ $ceremonyVenue->type }}">
                                                        </div>
                                                        <div class="card-content">
                                                            <div class="card-text">
                                                                <div class="row ">
                                                                    <div class="col-sm-12 text-center">
                                                                        <div class="card-subtitle">{{ $ceremonyVenue->name }}</div>
                                                                        <p>{{ '@ '.$hotel->name }}</p>
                                                                        <p>{{ date("m/d/y",strtotime($orderWedding->wedding_date)) }} ({{ date('H.i',strtotime($orderWedding->slot)) }})</p>
                                                                    </div>
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
                                        <div class="card-ptext-margin">
                                            <table class="table tb-list">
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Ceremony Venue')
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ $ceremonyVenue->name }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Date')
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ date("m/d/y",strtotime($orderWedding->wedding_date)) }}
                                                        {{ date('(H.i)',strtotime($orderWedding->slot)) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Number of Invitations')
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ $orderWedding->ceremony_venue_invitations }} @lang('messages.Invitations')
                                                    </td>
                                                </tr>
                                            </table>
                                            @if ($ceremonyVenue->capacity < $orderWedding->number_of_invitation)
                                                @php
                                                    $guest_outside = $orderWedding->number_of_invitation - $ceremonyVenue->capacity;
                                                @endphp
                                                <div class="notification">
                                                    <p>The ceremony venue can only accommodate {{ $ceremonyVenue->capacity }} guests, {{ $guest_outside }} guests will not have a place during the wedding ceremony.</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                            {{-- DECORATION CEREMONY VENUE --}}
                            @if ($orderWedding->ceremony_venue_decoration_id)
                                <div id="decoration-ceremony-venue" class="col-md-6">
                                    @php
                                        $cv_decorations = $vendor_packages->where('type','Ceremony Venue Decoration');
                                    @endphp
                                    <a href="#" data-toggle="modal" data-target="#detail-decoration-ceremony-venue-{{ $ceremonyVenueDecoration->id }}"> 
                                        <div class="page-subtitle {{ $orderWedding->ceremony_venue_decoration_id?"":"empty-value" }}">
                                            @lang('messages.Decoration')
                                            @if ($ceremonyVenue)
                                                @if ($orderWedding->ceremony_venue_decoration_id)
                                                    <span>
                                                        <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </a>
                                    @if ($ceremonyVenue)
                                        @if ($orderWedding->ceremony_venue_decoration_id)
                                            {{-- MODAL DETAIL DECORATION CEREMONY VENUE --}}
                                            <div class="modal fade" id="detail-decoration-ceremony-venue-{{ $ceremonyVenueDecoration->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy dw dw-fountain"></i> @lang('messages.Ceremony Venue Decoration')</div>
                                                            </div>
                                                            <div class="card-banner">
                                                                <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/vendors/package/' . $ceremonyVenueDecoration->cover) }}" alt="{{ $ceremonyVenueDecoration->service }}">
                                                            </div>
                                                            <div class="card-content">
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-12 text-center">
                                                                            <div class="card-subtitle">{{ $ceremonyVenueDecoration->service }}</div>
                                                                            <p>{{ '@ '.$ceremonyVenue->name }}</p>
                                                                            <p>{{ date("m/d/y",strtotime($orderWedding->wedding_date)) }} ({{ date('H.i',strtotime($orderWedding->slot)) }})</p>
                                                                            {!! $ceremonyVenue->description !!}
                                                                        </div>
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
                                            <div class="card-ptext-margin">
                                                <table class="table tb-list">
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Decoration')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $ceremonyVenueDecoration->service }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Date')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ date("m/d/y",strtotime($orderWedding->wedding_date)) }}
                                                            {{ date('(H.i)',strtotime($orderWedding->slot)) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Capacity')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $ceremonyVenue->capacity }} @lang('messages.Invitations')
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        @else
                                            <div class="card-ptext-margin">
                                                <div class="description">
                                                    @lang('messages.Basic Decoration, standard decoration provided by the hotel')
                                                </div>
                                            </div>
                                        @endif
                                        {{-- MODAL ADD DECORATION TO CEREMONY VENUE  --}}
                                        <div class="modal fade" id="update-decoration-ceremony-venue-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Decoration')</div>
                                                        </div>
                                                        <form id="updateDecorationCeremonyVenue" action="/fupdate-decoration-ceremony-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('put')
                                                            <div class="row">
                                                                <div class="col-sm-12">
                                                                    <div class="form-group">
                                                                        <label>@lang("messages.Select one") <span>*</span></label>
                                                                        <div class="card-box-content">
                                                                            @foreach ($cv_decorations as $dcv_id=>$decoration_c_venue)
                                                                                @if ($orderWedding->ceremony_venue_decoration_id)
                                                                                    @if ($orderWedding->ceremony_venue_decoration_id == $decoration_c_venue->id)
                                                                                        <input checked type="radio" id="{{ "d_cv".$dcv_id }}" name="ceremony_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
                                                                                        <label for="{{ "d_cv".$dcv_id }}" class="label-radio">
                                                                                            <div class="card h-100">
                                                                                                <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration_c_venue->cover) }}" alt="{{ $decoration_c_venue->service }}">
                                                                                                <div class="name-card">
                                                                                                    <b>{{ $decoration_c_venue->service }}</b>
                                                                                                </div>
                                                                                            </div>
                                                                                        </label>
                                                                                    @else
                                                                                        <input type="radio" id="{{ "d_cv".$dcv_id }}" name="ceremony_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
                                                                                        <label for="{{ "d_cv".$dcv_id }}" class="label-radio">
                                                                                            <div class="card h-100">
                                                                                                <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration_c_venue->cover) }}" alt="{{ $decoration_c_venue->service }}">
                                                                                                <div class="name-card">
                                                                                                    <b>{{ $decoration_c_venue->service }}</b>
                                                                                                </div>
                                                                                            </div>
                                                                                        </label>
                                                                                    @endif
                                                                                @else
                                                                                    <input type="radio" id="{{ "d_cv".$dcv_id }}" name="ceremony_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}">
                                                                                    <label for="{{ "d_cv".$dcv_id }}" class="label-radio">
                                                                                        <div class="card h-100">
                                                                                            <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration_c_venue->cover) }}" alt="{{ $decoration_c_venue->service }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
                                                                                            <div class="name-card">
                                                                                                <b>{{ $decoration_c_venue->service }}</b>
                                                                                            </div>
                                                                                        </div>
                                                                                    </label>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="basic_price" id="basic_price">
                                                            <input type="hidden" name="arrangement_price" id="arrangement_price">
                                                        </form>
                                                        <div class="card-box-footer">
                                                            <button type="submit" form="updateDecorationCeremonyVenue" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Change')</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                @if ($ceremonyVenue)
                                    <div id="decoration-ceremony-venue" class="col-md-6">
                                        <div class="page-subtitle">
                                            @lang('messages.Decoration')
                                        </div>
                                        <div class="card-ptext-margin">
                                            <div class="description">Basic Decoration by Hotel</div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            {{-- RECEPTION VENUE --}}
                            @if ($receptionVenue)
                                <div id="orderWeddingReceptionVenue" class="col-md-6">
                                    <div class="page-subtitle {{ !$receptionVenue? "empty-value" :"" }}">
                                        @lang('messages.Reception Venue')
                                        @if ($receptionVenue)
                                            <span>
                                                <a href="#" data-toggle="modal" data-target="#detail-reception-venue-{{ $receptionVenue->id }}"> 
                                                    <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                        @endif
                                    </div>
                                    {{-- MODAL DETAIL RECEPTION VENUE --}}
                                    <div class="modal fade" id="detail-reception-venue-{{ $receptionVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="subtitle"><i class="icon-copy ion-beer"></i> @lang('messages.Reception Venue')</div>
                                                    </div>
                                                    <div class="card-banner">
                                                        <img class="img-fluid rounded thumbnail-image" src="{{ asset ('storage/weddings/reception-venues/' . $receptionVenue->cover) }}" alt="{{ $receptionVenue->service }}">
                                                    </div>
                                                    <div class="card-content">
                                                        <div class="card-text">
                                                            <div class="row ">
                                                                <div class="col-sm-12 text-center">
                                                                    <div class="card-subtitle">{{ $receptionVenue->service }}</div>
                                                                    <p>{{ '@ '.$hotel->name }} - {{ $receptionVenue->name }}</p>
                                                                    <p>{{ $orderWedding->number_of_invitation }} @lang('messages.Invitations')</p>
                                                                    <p>{{ dateTimeFormat($orderWedding->reception_date_start) }}</p>
                                                                </div>
                                                                <div class="col-sm-12">
                                                                    <hr class="form-hr">
                                                                    {!! $receptionVenue->description !!}
                                                                </div>
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
                                    <div class="card-ptext-margin">
                                        <table class="table tb-list">
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Reception Venue')
                                                </td>
                                                <td class="htd-2">
                                                        {{ $receptionVenue->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Date')
                                                </td>
                                                <td class="htd-2">
                                                    {{ dateTimeFormat($orderWedding->reception_date_start) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Capacity')
                                                </td>
                                                <td class="htd-2">
                                                    {{ $receptionVenue->capacity }} @lang('messages.Invitations')
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            {{-- DECORATION RECEPTION VENUE --}}
                            @if ($receptionVenue)
                                <div id="orderWeddingDecorationReceptionVenue" class="col-md-6">
                                    @php
                                        $reception_v_decorations = $vendor_packages->where('type','Reception Venue Decoration');
                                    @endphp
                                    <a href="#" data-toggle="modal" data-target="#detail-decoration-reception-venue-{{ $orderWedding->id }}"> 
                                        <div class="page-subtitle">
                                            @lang('messages.Decoration')
                                            @if ($orderWedding->reception_venue_decoration_id)
                                                <span>
                                                    <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                    @if ($receptionVenue)
                                        @if ($orderWedding->reception_venue_decoration_id)
                                            {{-- MODAL DETAIL DECORATION RECEPTION VENUE --}}
                                            <div class="modal fade" id="detail-decoration-reception-venue-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy dw dw-fountain"></i> @lang('messages.Reception Venue Decoration')</div>
                                                            </div>
                                                            <div class="card-banner">
                                                                <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/vendors/package/' . $decorationReceptionVenue->cover) }}" alt="{{ $decorationReceptionVenue->service }}">
                                                            </div>
                                                            <div class="card-content">
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-12 text-center">
                                                                            <div class="card-subtitle">{{ $decorationReceptionVenue->service }}</div>
                                                                            <p>{{ '@ '.$receptionVenue->name }}</p>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <hr class="form-hr">
                                                                            {!! $decorationReceptionVenue->description !!}
                                                                        </div>
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
                                            <div class="card-ptext-margin">
                                                <table class="table tb-list">
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Decoration')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $decorationReceptionVenue->service }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Date')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ dateTimeFormat($orderWedding->reception_date_start) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Capacity')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $receptionVenue->capacity }} @lang('messages.Invitations')
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        @else
                                            <div class="card-ptext-margin">
                                                <div class="description">
                                                    @lang('messages.Basic Decoration, standard decoration provided by the hotel')
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                            {{-- LUNCH VENUE --}}
                            @if ($lunchVenue)
                                <div id="weddingLunchVenue" class="col-md-6">
                                    <a href="#" data-toggle="modal" data-target="#detail-lunch-venue-{{ $lunchVenue->id }}"> 
                                        <div class="page-subtitle">
                                            @lang('messages.Lunch Venue')
                                            <span>
                                                <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                            </span>
                                        </div>
                                    </a>
                                    <div class="card-ptext-margin">
                                        <table class="table tb-list">
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Lunch Venue')
                                                </td>
                                                <td class="htd-2">
                                                    {{ $lunchVenue->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Date')
                                                </td>
                                                <td class="htd-2">
                                                    {{ dateTimeFormat($orderWedding->lunch_venue_date) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Invitations')
                                                </td>
                                                <td class="htd-2">
                                                    {{ $orderWedding->number_of_invitation }} @lang('messages.Invitations')
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                {{-- MODAL DETAIL LUNCH VENUE --}}
                                <div class="modal fade" id="detail-lunch-venue-{{ $lunchVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content text-left">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="subtitle"><i class="icon-copy ion-beer"></i> @lang('messages.Lunch Venue')</div>
                                                </div>
                                                <div class="modal-img-container">
                                                    <img class="img-fluid rounded thumbnail-image" src="{{ asset ('storage/weddings/lunch-venues/' . $lunchVenue->cover) }}" alt="{{ $lunchVenue->name }}">
                                                    <div class="modal-service-name">
                                                        {{ $lunchVenue->name }}
                                                        <p>{{ '@ '.$hotel->name }} - {{ $lunchVenue->name }}</p>
                                                        <p>{{ dateTimeFormat($orderWedding->lunch_venue_date) }}</p>
                                                    </div>
                                                </div>
                                                <div class="card-content">
                                                    <div class="card-text">
                                                        <div class="row ">
                                                            <div class="col-sm-12">
                                                                {!! $lunchVenue->description !!}
                                                            </div>
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
                                @if ($lunchVenue->capacity < $orderWedding->number_of_invitations)
                                    <div class="notification-boxed">
                                        <p>The lunch venue can only accommodate {{ $lunchVenue->capacity }} people out of the total {{ $orderWedding->number_of_invitation }} wedding invitations.</p>
                                    </div>
                                @endif
                            @endif
                            {{-- DINNER VENUE --}}
                            @if ($dinnerVenue)
                                <div id="weddingLunchVenue" class="col-md-6">
                                    <a href="#" data-toggle="modal" data-target="#detail-dinner-venue-{{ $dinnerVenue->id }}"> 
                                        <div class="page-subtitle">
                                            @lang('messages.Dinner Venue')
                                            <span>
                                                <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                            </span>
                                        </div>
                                    </a>
                                    {{-- MODAL DETAIL DINNER VENUE --}}
                                    <div class="modal fade" id="detail-dinner-venue-{{ $dinnerVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="subtitle"><i class="icon-copy ion-beer"></i> @lang('messages.Dinner Venue')</div>
                                                    </div>
                                                    <div class="modal-img-container">
                                                        <img class="img-fluid rounded thumbnail-image" src="{{ asset ('storage/weddings/dinner-venues/' . $dinnerVenue->cover) }}" alt="{{ $dinnerVenue->name }}">
                                                        <div class="modal-service-name">
                                                            {{ $dinnerVenue->name }}
                                                            <p>{{ '@ '.$hotel->name }} - {{ $dinnerVenue->name }}</p>
                                                            <p>{{ dateTimeFormat($orderWedding->dinner_venue_date) }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="card-content">
                                                        <div class="card-text">
                                                            <div class="row ">
                                                                <div class="col-sm-12">
                                                                    {!! $dinnerVenue->description !!}
                                                                </div>
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
                                    <div class="card-ptext-margin">
                                        <table class="table tb-list">
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Dinner Venue')
                                                </td>
                                                <td class="htd-2">
                                                    {{ $dinnerVenue->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Date')
                                                </td>
                                                <td class="htd-2">
                                                    {{ dateTimeFormat($orderWedding->dinner_venue_date) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Invitations')
                                                </td>
                                                <td class="htd-2">
                                                    {{ $orderWedding->number_of_invitation }} @lang('messages.Invitations')
                                                </td>
                                            </tr>
                                        </table>
                                        @if ($dinnerVenue->capacity < $orderWedding->number_of_invitations)
                                            <div class="notification-boxed">
                                                <p>The dinner venue can only accommodate {{ $dinnerVenue->capacity }} people out of the total {{ $orderWedding->number_of_invitation }} wedding invitations.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            {{-- ACCOMMODATION --}}
                            @if ($bride_accommodation or count($wedding_accommodations) > 0)
                                @php
                                    $bride_room = $rooms->where('id',$orderWedding->room_bride_id)->first();
                                @endphp
                                <div id="accommodations" class="col-md-12">
                                    <div class="page-subtitle m-b-8">@lang('messages.Accommodation')</div>
                                    <table class="data-table table stripe hover nowrap no-footer dtr-inline">
                                        <thead>
                                            <tr>
                                                <th>@lang('messages.Date')</th>
                                                <th>@lang('messages.Hotel')</th>
                                                <th>@lang('messages.Suites & Villas')</th>
                                                <th>@lang('messages.Guests')</th>
                                                <th style="width: 10%">@lang('messages.Number of Guests')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($bride_accommodation)
                                                <tr>
                                                    <td class="pd-2-8">
                                                        {{ date("m/d/y", strtotime($orderWedding->checkin)) }} -
                                                        {{ date("m/d/y", strtotime($orderWedding->checkout)) }}
                                                    </td>
                                                    <td class="pd-2-8">
                                                        {{ $hotel->name }}
                                                    </td>
                                                    <td class="pd-2-8">
                                                        {{ $bride_room->rooms }}
                                                    </td>
                                                    <td class="pd-2-8">
                                                        {{ $bride->groom.", ".$bride->bride }}
                                                    </td>
                                                    <td class="pd-2-8">
                                                        2 (@lang("messages.Bride's"))
                                                    </td>
                                                </tr>
                                            @endif
                                            @if ($wedding_accommodations)
                                                @foreach ($wedding_accommodations as $inv_suite_villa)
                                                    <tr>
                                                        <td class="pd-2-8">
                                                            {{ date("m/d/y", strtotime($inv_suite_villa->checkin)) }} -
                                                            {{ date("m/d/y", strtotime($inv_suite_villa->checkout)) }}
                                                        </td>
                                                        <td class="pd-2-8">
                                                            {{ $hotel->name }}
                                                        </td>
                                                        <td class="pd-2-8">
                                                            {{ $inv_suite_villa->room->rooms }}
                                                        </td>
                                                        <td class="pd-2-8">
                                                            {{ $inv_suite_villa->guest_detail }}
                                                        </td>
                                                        <td class="pd-2-8">
                                                            {{ $inv_suite_villa->number_of_guests }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            {{-- TRANSPORT --}}
                            @if ($bride_transports_id or count($transports_orders)>0)
                                <div id="Transports" class="col-md-12">
                                    <div class="page-subtitle">
                                        @lang('messages.Transport')
                                    </div>
                                    <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                                        <thead>
                                            <tr>
                                                <th style="width: 20%" class="datatable-nosort">@lang('messages.Date')</th>
                                                <th style="width: 30%" class="datatable-nosort">@lang('messages.Type')</th>
                                                <th style="width: 35%" class="datatable-nosort">@lang('messages.Transports')</th>
                                                <th style="width: 15%" class="datatable-nosort">@lang('messages.Number of Guests')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($bride_transports_id)
                                                @php
                                                    $bride_transport_date_arrival = $flights->where('type',"Arrival")->where('group',"Brides")->first();
                                                    $bride_transport_date_departure = $flights->where('type',"Departure")->where('group',"Brides")->first();
                                                @endphp
                                                    @php
                                                        $bride_transport = $transports->where('id',$bride_transports_id)->first();
                                                    @endphp
                                                    <tr>
                                                        <td class="pd-2-8">{{ date("m/d/y",strtotime($orderWedding->checkin)) }} (<i>@lang('messages.Include')</i>)</td>
                                                        <td class="pd-2-8">@lang('messages.Airport Shuttle') (@lang('messages.Arrival'))</td>
                                                        <td class="pd-2-8">{{ $bride_transport->brand }} {{ $bride_transport->name }}</td>
                                                        <td class="pd-2-8">2 (Bride's)</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pd-2-8">{{ date("m/d/y",strtotime($orderWedding->checkout)) }} (<i>@lang('messages.Include')</i>)</td>
                                                        <td class="pd-2-8">@lang('messages.Airport Shuttle') (@lang('messages.Departure'))</td>
                                                        <td class="pd-2-8">{{ $bride_transport->brand }} {{ $bride_transport->name }}</td>
                                                        <td class="pd-2-8">2 (Bride's)</td>
                                                    </tr>
                                            @endif
                                            @foreach ($transports_orders as $transport_inv)
                                                <tr>
                                                    <td class="pd-2-8">{{ date("m/d/y (H.i)",strtotime($transport_inv->date)) }}</td>
                                                    @if ($transport_inv->desc_type == "In")
                                                        <td class="pd-2-8">@lang('messages.Airport Shuttle') (@lang('messages.Arrival'))</td>
                                                    @else
                                                        <td class="pd-2-8">@lang('messages.Airport Shuttle') (@lang('messages.Departure'))</td>
                                                    @endif
                                                    <td class="pd-2-8">{{ $transport_inv->transport->brand }} {{ $transport_inv->transport->name }}</td>
                                                    <td class="pd-2-8">{{ $transport_inv->number_of_guests }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            {{-- ADDITIONAL SERVICE --}}
                            @if ($orderWedding->service == "Wedding Package")
                                @if ($orderWedding->additional_services)
                                    <div id="weddingPackageServiceInclude" class="col-md-12">
                                        @php
                                            $additional_services = $vendor_packages;
                                            $addser_ids = json_decode($orderWedding->additional_services, true);
                                            $no_addser = 0;
                                        @endphp
                                        <div class="page-subtitle">
                                            @lang('messages.Additional Service')  (@lang('messages.Include'))
                                        </div>
                                        <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                                            <thead>
                                                <tr>
                                                    <th class="datatable-nosort">@lang('messages.Date')</th>
                                                    <th class="datatable-nosort">@lang('messages.Services')</th>
                                                    <th class="datatable-nosort">@lang('messages.Duration')</th>
                                                    <th class="datatable-nosort"></th>
                                                </tr>
                                            </thead>
                                            @if ($addser_ids)
                                                <tbody>
                                                    @foreach ($addser_ids as $addser_id)
                                                        @php
                                                            $service_include = $vendor_packages->where('id',$addser_id)->first();
                                                        @endphp
                                                        <tr>
                                                            <td class="pd-2-8">
                                                                @if ($service_include->venue == "Ceremony Venue")
                                                                    {{ date("m/d/y",strtotime($orderWedding->wedding_date)) }}
                                                                @elseif ($service_include->venue == "Reception Venue")
                                                                    {{ date("m/d/y",strtotime($orderWedding->reception_date_start)) }}
                                                                @else
                                                                    {{ date("m/d/y",strtotime($orderWedding->wedding_date)) }}
                                                                @endif
                                                            </td>
                                                            <td class="pd-2-8">{{ $service_include->service }}</td>
                                                            <td class="pd-2-8">
                                                                {{ $service_include->duration }} @lang('messages.Hours')
                                                                {{-- {{ $service_include->venue?$service_include->venue:"-"; }} --}}
                                                            </td>
                                                            <td class="pd-2-8 text-right">
                                                                <a href="#" data-toggle="modal" data-target="#detail-additional-service-{{ $service_include->id }}"> 
                                                                    <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        {{-- MODAL DETAIL ADDITIONAL SERVICE --}}
                                                        <div class="modal fade" id="detail-additional-service-{{ $service_include->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-certificate" aria-hidden="true"></i> {{ $service_include->service }}</div>
                                                                        </div>
                                                                        <div class="card-banner">
                                                                            <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/vendors/package/' . $service_include->cover) }}" alt="{{ $service_include->service }}">
                                                                        </div>
                                                                        <div class="card-content">
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-12 text-center">
                                                                                        <div class="card-subtitle p-t-0">{{ $service_include->service }}</div>
                                                                                    </div>
                                                                                    <div class="col-sm-12">
                                                                                        <hr class="form-hr">
                                                                                        {!! $service_include->description !!}
                                                                                    </div>
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
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                    </div>
                                @endif
                                {{-- ADDITIONAL CHARGES --}}
                                @if (count($additional_charges)>0)
                                    <div id="AdditionalServices" class="col-md-12">
                                        <div class="page-subtitle">
                                            @lang('messages.Additional Charge')
                                        </div>
                                        <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                                            <thead>
                                                <tr>
                                                    <th class="datatable-nosort">@lang('messages.Date')</th>
                                                    <th class="datatable-nosort">@lang('messages.Type')</th>
                                                    <th class="datatable-nosort">@lang('messages.Services')</th>
                                                    <th class="datatable-nosort">@lang('messages.Description')</th>
                                                    <th class="datatable-nosort">@lang('messages.Status')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($additional_charges as $service_request)
                                                    <tr>
                                                        <td class="pd-2-8">{{ date("m/d/y (H.i)",strtotime($service_request->date)) }}</td>
                                                        <td class="pd-2-8">{{ $service_request->type }}</td>
                                                        <td class="pd-2-8">{{ $service_request->quantity }} {{ $service_request->service }}</td>
                                                        <td class="pd-2-8">{!! $service_request->note?$service_request->note:"-" !!}</td>
                                                        <td class="pd-2-8">{{ $service_request->status }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            @else
                                {{-- ADDITIONAL SERVICE --}}
                                @if ($orderWedding->additional_services)
                                    <div id="additionalServices" class="col-md-12">
                                        @php
                                            $additional_services = $vendor_packages->where('type','Other');
                                            $addser_ids = json_decode($orderWedding->additional_services);
                                        @endphp
                                        <div class="page-subtitle {{ $addser_ids?"":"empty-value" }}">
                                            @lang('messages.Additional Services')
                                        </div>
                                        @if ($addser_ids)
                                            <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                                                <thead>
                                                    <tr>
                                                        <th style="width: 15%" class="datatable-nosort">@lang('messages.Date')</th>
                                                        <th style="width: 45%" class="datatable-nosort">@lang('messages.Services')</th>
                                                        <th style="width: 30%" class="datatable-nosort">@lang('messages.Price')</th>
                                                        <th style="width: 10%" class="datatable-nosort"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($addser_ids)
                                                        @foreach ($addser_ids as $no_addser=>$addser_id)
                                                            @php
                                                                $additionalService = $vendor_packages->where('id',$addser_id)->first();
                                                            @endphp
                                                            <tr>
                                                                <td class="pd-2-8">{{ $additionalService->venue == "Ceremony Venue"? date('d M Y',strtotime($orderWedding->wedding_date)):date('d M Y',strtotime($orderWedding->reception_date_start)) }}</td>
                                                                <td class="pd-2-8">{{ $additionalService->service }}</td>
                                                                <td class="pd-2-8">{{ '$ ' .number_format($additionalService->publish_rate, 0, ',', '.') }}</td>
                                                                <td class="pd-2-8 text-right">
                                                                    <a href="#" data-toggle="modal" data-target="#detail-additional-service-{{ $additionalService->id }}"> 
                                                                        <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            {{-- MODAL DETAIL ADDITIONAL SERVICE --}}
                                                            <div class="modal fade" id="detail-additional-service-{{ $additionalService->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content text-left">
                                                                        <div class="card-box">
                                                                            <div class="card-box-title">
                                                                                <div class="subtitle"><i class="icon-copy fa fa-certificate" aria-hidden="true"></i> {{ $additionalService->service }}</div>
                                                                            </div>
                                                                            <div class="card-banner">
                                                                                <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/vendors/package/' . $additionalService->cover) }}" alt="{{ $additionalService->service }}">
                                                                            </div>
                                                                            <div class="card-content">
                                                                                <div class="card-text">
                                                                                    <div class="row ">
                                                                                        <div class="col-sm-12 text-center">
                                                                                            <div class="card-subtitle p-t-0">{{ $additionalService->service }}</div>
                                                                                        </div>
                                                                                        <div class="col-sm-12">
                                                                                            <hr class="form-hr">
                                                                                            {!! $additionalService->description !!}
                                                                                        </div>
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
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                @endif
                            @endif
                            {{-- REMARK --}}
                            @if ($orderWedding->remark)
                                <div id="remarkPage" class="col-md-12">
                                    @if ($orderWedding->remark)
                                        <div class="page-subtitle">
                                    @else
                                        <div class="page-subtitle empty-value">
                                    @endif
                                        @lang('messages.Remark')
                                    </div>
                                    @if ($orderWedding->remark)
                                        <div class="box-price-kicked">
                                            {!! $orderWedding->remark !!}
                                        </div>
                                    @endif
                                </div>
                            @endif
                            {{-- PRICES --}}
                            <div class="col-md-12">
                                @php
                                    $additional_service_price = json_decode($orderWedding->additional_services_price);
                                    $ceremonyVenuePrice = $orderWedding->ceremony_venue_price;
                                    $ceremonyVenueDecorationPrice = $orderWedding->ceremony_venue_decoration_price ? $orderWedding->ceremony_venue_decoration_price : 0;
                                    $receptionVenuePrice = $orderWedding->reception_venue_price ? $orderWedding->reception_venue_price : 0;
                                    $receptionVenueDecorationPrice = $orderWedding->reception_venue_decoration_price ? $orderWedding->reception_venue_decoration_price : 0;
                                    $additionalServicePrice = is_array($additional_service_price) ? array_sum($additional_service_price): $orderWedding->additional_services_price;
                                    if ($orderWedding->service == "Wedding Package") {
                                        $accommodationPrice = $orderWedding->accommodation_price;
                                    }else{
                                        $accommodationPrice = $orderWedding->room_bride_price + $orderWedding->accommodation_price;
                                    }
                                    $transportPrice = $orderWedding->transport_invitations_price;
                                @endphp
                                <div class="page-subtitle">
                                    @lang('messages.Prices')
                                </div>
                                <div class="box-price-kicked">
                                    <div class="row">
                                        <div class="col-6">
                                            @if ($orderWedding->service == "Wedding Package")
                                                @if ($accommodationPrice > 0)
                                                    <div class="normal-text">@lang('messages.Accommodation') + @lang('messages.Extra Bed')</div>
                                                @endif
                                                @if (count($transports_orders)>0)
                                                    @if ($transportContainsNullPrice)
                                                        <div class="normal-text">@lang('messages.Transports')</div>
                                                    @else
                                                        <div class="normal-text">@lang('messages.Transports')</div>
                                                    @endif
                                                @endif
                                                @if (count($additional_charges)>0)
                                                    @if ($additionalChargeContainZero)
                                                        <div class="normal-text">@lang('messages.Additional Charge')</div>
                                                    @else
                                                        <div class="normal-text">@lang('messages.Additional Charge')</div>
                                                    @endif
                                                @endif
                                                

                                                <div class="normal-text">@lang('messages.Wedding Package')</div>
                                            @else
                                                @if ($ceremonyVenuePrice > 0)
                                                    <div class="normal-text">@lang('messages.Ceremony Venue')</div>
                                                @endif
                                                @if ($ceremonyVenueDecorationPrice > 0)
                                                    <div class="normal-text">@lang('messages.Ceremony Venue Decoration')</div>
                                                @endif
                                                @if ($receptionVenuePrice > 0)
                                                    <div class="normal-text">@lang('messages.Reception Venue')</div>
                                                @endif
                                                @if ($receptionVenueDecorationPrice > 0)
                                                    <div class="normal-text">@lang('messages.Reception Venue Decoration')</div>
                                                @endif
                                                @if ($accommodationPrice > 0)
                                                    <div class="normal-text">@lang('messages.Accommodation')</div>
                                                @endif
                                                @if (count($transports_orders)>0)
                                                    @if ($transportContainsNullPrice)
                                                        <div class="normal-text">@lang('messages.Transports')</div>
                                                    @else
                                                        <div class="normal-text">@lang('messages.Transports')</div>
                                                    @endif
                                                @endif
                                                @if ($additionalServicePrice > 0)
                                                    <div class="normal-text">@lang('messages.Additional Service')</div>
                                                @endif
                                            @endif
                                            <hr class="form-hr">
                                            <div class="price-name">@lang('messages.Total Price')</div>

                                        </div>
                                        <div class="col-6 text-right">
                                            @if ($orderWedding->service == "Wedding Package")
                                                @if ($accommodationPrice > 0)
                                                    @if ($accommodation_containt_zero)
                                                        <div class="promo-price text-blue" data-toggle="tooltip" data-placement="left" title="@lang('messages.To be advised')">TBA</div>
                                                    @else
                                                        <div class="normal-text">{{ '$ ' .number_format($accommodationPrice, 0, ',', '.') }}</div>
                                                    @endif
                                                @endif
                                                @if (count($transports_orders)>0)
                                                    @if ($transportContainsNullPrice)
                                                        <div class="promo-price text-blue" data-toggle="tooltip" data-placement="left" title="@lang('messages.To be advised')">TBA</div>
                                                    @else
                                                        <div class="normal-text">{{ '$ ' .number_format($transportPrice, 0, ',', '.') }}</div>
                                                    @endif
                                                @endif
                                                @if (count($additional_charges)>0)
                                                    @if ($additionalChargeContainZero)
                                                        <div class="normal-text text-blue" data-toggle="tooltip" data-placement="left" title="@lang('messages.To be advised')">TBA</div>
                                                    @else
                                                        <div class="normal-text">{{ '$ ' .number_format($additional_charge_price, 0, ',', '.') }}</div>
                                                    @endif
                                                @endif
                                                <div class="normal-text">{{ '$ ' .number_format($orderWedding->package_price, 0, ',', '.') }}</div>
                                            @else
                                                @if ($ceremonyVenuePrice > 0)
                                                    <div class="normal-text">{{ '$ ' .number_format($ceremonyVenuePrice, 0, ',', '.') }}</div>
                                                @endif
                                                @if ($ceremonyVenueDecorationPrice > 0)
                                                    <div class="normal-text">{{ '$ ' .number_format($ceremonyVenueDecorationPrice, 0, ',', '.') }}</div>
                                                @endif
                                                @if ($receptionVenuePrice > 0)
                                                    <div class="normal-text">{{ '$ ' .number_format($receptionVenuePrice, 0, ',', '.') }}</div>
                                                @endif
                                                @if ($receptionVenueDecorationPrice > 0)
                                                    <div class="normal-text">{{ '$ ' .number_format($receptionVenueDecorationPrice, 0, ',', '.') }}</div>
                                                @endif
                                                @if ($accommodationPrice > 0)
                                                    @if ($accommodation_containt_zero)
                                                        <div class="promo-price" data-toggle="tooltip" data-placement="left" title="@lang('messages.To be advised')">TBA</div>
                                                    @else
                                                        <div class="normal-text">{{ '$ ' .number_format($accommodationPrice, 0, ',', '.') }}</div>
                                                    @endif
                                                @endif
                                                @if (count($transports_orders)>0)
                                                    @if ($transportContainsNullPrice)
                                                        <div class="promo-price" data-toggle="tooltip" data-placement="left" title="@lang('messages.To be advised')">TBA</div>
                                                    @else
                                                        <div class="normal-text">{{ '$ ' .number_format($orderWedding->transport_invitations_price, 0, ',', '.') }}</div>
                                                    @endif
                                                @endif
                                                @if ($additionalServicePrice > 0)
                                                    <div class="normal-text">{{ '$ ' .number_format($additionalServicePrice, 0, ',', '.') }}</div>
                                                @endif
                                            @endif
                                            <hr class="form-hr">
                                            @if ($transportContainsNullPrice or $accommodation_containt_zero or $additionalChargeContainZero)
                                                <div class="usd-rate" data-toggle="tooltip" data-placement="left" title="@lang('messages.To be advised')">TBA</div>
                                            @else
                                                <div class="usd-rate">{{ '$ ' .number_format($orderWedding->final_price, 0, ',', '.') }}</div>
                                            @endif
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($orderWedding->status == "Pending")
                                <div class="col-md-12">
                                    <div class="notification">
                                        <p>@lang("messages.We are pleased to inform you that we have received your order. We will validate the availability of your order. Once your order is verified, we will send you an email regarding the status of your order.")</p>
                                    </div>
                                </div>
                            @endif
                            @if (!$bride->groom_pasport_id or !$bride->bride_pasport_id or $orderWedding->final_price <= 0)
                                <div class="col-md-12">
                                    <div class="notification">
                                        @if (!$bride->groom_pasport_id)
                                            <p>- @lang("messages.Please complete the Groom's data first")</p>
                                        @endif
                                        @if (!$bride->bride_pasport_id)
                                            <p>- @lang("messages.Please complete the Bride's data first")</p>
                                        @endif
                                        @if ($orderWedding->final_price <= 0)
                                            <p>- @lang("messages.Order not found")</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <form id="submitOrderWedding" action="/fsubmit-order-wedding/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <input type="hidden" name="status" value="Pending">
                        </form>
                        <div class="card-box-footer">
                            @if ($invoice)
                                @if ($invoice->balance>=1)
                                    <a href="#" data-toggle="modal" data-target="#weddingPaymentConfirmation-{{ $orderWedding->id }}">
                                        <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-upload" aria-hidden="true"></i> @lang('messages.Payment Confirmation')</button>
                                    </a>
                                @endif
                                {{-- MODAL PAYMENT CONFIRMATION --}}
                                <div class="modal fade" id="weddingPaymentConfirmation-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title text-left">
                                                    <div class="title"><i class="icon-copy fa fa-usd" aria-hidden="true"></i>@lang('messages.Payment Confirmation')</div>
                                                </div>
                                                <form id="wedding-payment-confirm-{{ $orderWedding->id }}" action="/fwedding-payment-confirmation-{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="row text-left">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="row m-t-27">
                                                                        <div class="col-5"><p>@lang('messages.Order Number')</p></div>
                                                                        <div class="col-7"><p><b>: {{ $orderWedding->orderno }}</b></p></div>
                                                                        <div class="col-5"><p>@lang('messages.Reservation Number')</p></div>
                                                                        <div class="col-7"><p><b>: {{ $reservation->rsv_no }}</b></p></div>
                                                                        <div class="col-5"><p>@lang('messages.Invoice Number')</p></div>
                                                                        <div class="col-7"><p><b>: {{ $invoice->inv_no }}</b></p></div>
                                                                        <div class="col-5"><p>@lang('messages.Due Date')</p></div>
                                                                        <div class="col-7"><p>: {{ dateFormat($invoice->due_date) }}</p></div>
                                                                        <div class="col-5"><p>@lang('messages.Amount')</p></div>
                                                                        <div class="col-7"><p><b>: {{ currencyFormatUsd($orderWedding->final_price) }}</b></p></div>
                                                                        <div class="col-12 m-t-18"><p><i class="icon-copy fa fa-exclamation" aria-hidden="true"></i> @lang('messages.Please make the payment before the due date and provide proof of payment to prevent the cancellation of your order.')</p></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="dropzone" class="form-label">@lang('messages.Receipt Image')</label>
                                                                        <div class="dropzone">
                                                                            <div class="tour-receipt-div">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="receipt_name" class="form-label">@lang('messages.Select Receipt') </label><br>
                                                                        <input type="file" name="receipt_name" id="receipt_name" class="custom-file-input @error('receipt_name') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('receipt_name') }}" required>
                                                                        @error('receipt_name')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="order_id" value="{{ $orderWedding->id }}">
                                                    </div>
                                                </form>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="wedding-payment-confirm-{{ $orderWedding->id }}" class="btn btn-primary"><i class="icon-copy fa fa-upload" aria-hidden="true"></i> Send</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{-- CONTRACT / INVOICE --}}
                            @if ($invoice)
                                @if (config('app.locale') == "zh")
                                    <a href="en-print-contract-wedding-{{ $orderWedding->id }}" target="__blank" >
                                        <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> 發票 (EN)</button>
                                    </a>
                                    <a href="zh-print-contract-wedding-{{ $orderWedding->id }}" target="__blank" >
                                        <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> 發票 (ZH)</button>
                                    </a>
                                    {{-- <a href="#" data-toggle="modal" data-target="#contract-zh-{{ $orderWedding->id }}">
                                        <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> 發票</button>
                                    </a> --}}
                                    <a href='{{URL::to('/')}}/storage/document/invoice-{{ $invoice->inv_no }}-{{ $orderWedding->id }}_zh.pdf' target="_blank">
                                        <button type="button" class="btn btn-primary mobile"><i class="fa fa-download"></i> 下載發票</button>
                                    </a>
                                    {{-- MODAL VIEW CONTRACT ZH --}}
                                    <div class="modal fade" id="contract-zh-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                <div class="modal-body pd-5">
                                                    <embed src="storage/document/invoice-{{ $invoice->inv_no."-".$orderWedding->id }}_zh.pdf" frameborder="10" width="100%" height="850px">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @php
                                        $filePath = 'storage/document/invoices/' . $orderWedding->orderno . '.pdf';
                                        $publicPath = public_path($filePath);
                                        $fileExists = \Illuminate\Support\Facades\File::exists($publicPath);
                                    @endphp
                                    
                                    @if($fileExists)
                                        <a href="{{ asset($filePath) }}" target="_blank">
                                            <button type="button" class="btn btn-primary mobile"><i class="fa fa-download"></i> @lang('messages.Download Invoice')</button>
                                        </a>
                                    @endif
                                    <a href="#" data-toggle="modal" data-target="#invoice-{{ $orderWedding->orderno }}">
                                        <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> @lang('messages.Invoice')</button>
                                    </a>
                                    
                                    {{-- MODAL VIEW CONTRACT EN --}}
                                    <div class="modal fade" id="invoice-{{ $orderWedding->orderno }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                <div class="modal-body pd-5">
                                                    <embed src="storage/document/invoices/{{ $orderWedding->orderno }}.pdf" frameborder="10" width="100%" height="850px">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            <a href="/orders">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 desktop">
                    @if ($invoice)
                        {{-- CURRENCY --}}
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-money" aria-hidden="true"></i> @lang('messages.Currency')</div>
                            </div>
                            <div class="card-content">
                                <p><b>USD: </b>{{ 'IDR ' .number_format($invoice->rate_usd, 0, ',', '.') }}</p>
                                <p><b>CNY: </b>{{ 'IDR ' .number_format($invoice->rate_cny, 0, ',', '.') }}</p>
                                <p><b>TWD: </b>{{ 'IDR ' .number_format($invoice->rate_twd, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endif
                    @if ($orderWedding->status == "Approved" or $orderWedding->status == "Paid")
                        @if ($invoice->currency)
                            {{-- INVOICE --}}
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-money" aria-hidden="true"></i> @lang('messages.Invoice')</div>
                                </div>
                                <div class="card-content">
                                    <p>@lang('messages.Invoice Number'): <b>{{ $invoice->inv_no }}</b></p>
                                    <p>@lang('messages.Due Date'): <b>{{ dateFormat($invoice->due_date) }}</b></p>
                                    @if ($invoice->currency->name == "IDR")
                                        <p>@lang('messages.Amount') (USD): <b>{{ '$ ' .number_format($invoice->total_usd, 0, ',', '.') }}</b></p>
                                        <p>@lang('messages.Amount') (IDR): <b>{{ 'Rp ' .number_format($invoice->total_idr, 0, ',', '.') }}</b></p>
                                    @elseif ($invoice->currency->name == "USD")
                                        <p>@lang('messages.Amount') (USD): <b>{{ '$ ' .number_format($invoice->total_usd, 0, ',', '.') }}</b></p>
                                    @elseif ($invoice->currency->name == "TWD")
                                        <p>@lang('messages.Amount') (USD): <b>{{ '$ ' .number_format($invoice->total_usd, 0, ',', '.') }}</b></p>
                                        <p>@lang('messages.Amount') (TWD): <b>{{ 'NT$ ' .number_format($invoice->total_twd, 0, ',', '.') }}</b></p>
                                    @elseif ($invoice->currency->name == "CNY")
                                        <p>@lang('messages.Amount') (USD): <b>{{ '$ ' .number_format($invoice->total_usd, 0, ',', '.') }}</b></p>
                                        <p>@lang('messages.Amount') (CNY): <b>{{ '¥ ' .number_format($invoice->total_cny, 0, ',', '.') }}</b></p>
                                    @endif
                                    <hr class="form-hr">
                                    @if ($invoice->balance <=1)
                                        <p>@lang('messages.Remaining Payment'): <b class="text-green"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> @lang('messages.Paid')</b></p>
                                    @else
                                        @if ($invoice->currency->name == "IDR")
                                            <p>@lang('messages.Remaining Payment'): (IDR): <b>{{ 'Rp ' .number_format($invoice->balance, 0, ',', '.') }}</b></p>
                                        @elseif ($invoice->currency->name == "USD")
                                            <p>@lang('messages.Remaining Payment'): <b>{{ '$ ' .number_format($invoice->balance, 0, ',', '.') }}</b></p>
                                        @elseif ($invoice->currency->name == "TWD")
                                            <p>@lang('messages.Remaining Payment'): (TWD): <b>{{ 'NT$ ' .number_format($invoice->balance, 0, ',', '.') }}</b></p>
                                        @elseif ($invoice->currency->name == "CNY")
                                            <p>@lang('messages.Remaining Payment'): (CNY): <b>{{ '¥ ' .number_format($invoice->balance, 0, ',', '.') }}</b></p>
                                        @endif
                                    @endif
                                    <p> <b></b></p>
                                </div>
                            </div>
                            {{-- RECEIPT --}}
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-money" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                </div>
                                @if ($invoice->due_date > $now)
                                    @if ($receipts)
                                        @foreach ($receipts as $receipt)
                                            @if ($receipt->status == "Valid")
                                                <div class="pmt-container">
                                                    <i class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                                                    <div class="pmt-status">
                                                        @lang('messages.Valid')
                                                    </div>
                                                    <div class="btn-action-right">
                                                        <a href="#" data-toggle="modal" data-target="#desktop-valid-receipt-{{ $receipt->id }}">
                                                            <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="pmt-des">
                                                   
                                                    @if ($receipt->kurs_name == "IDR")
                                                        @if ($invoice->currency->name == "CNY")
                                                            <p>@lang('messages.Amount') (CNY): <b>{{ '¥ ' .number_format(($receipt->amount/$invoice->rate_cny), 0, ',', '.') }}</b></p>
                                                        @elseif ($invoice->currency->name == "TWD")
                                                            <p>@lang('messages.Amount') (TWD): <b>{{ 'NT$ ' .number_format(($receipt->amount/$invoice->rate_twd), 0, ',', '.') }}</b></p>
                                                        @elseif ($invoice->currency->name == "USD")
                                                            <p>@lang('messages.Amount') (USD): <b>{{ '$ ' .number_format(($receipt->amount/$invoice->rate_usd), 0, ',', '.') }}</b></p>
                                                        @endif
                                                        <p>@lang('messages.Amount') (IDR): <b>{{ 'Rp ' .number_format($receipt->amount, 0, ',', '.') }}</b></p>
                                                    @elseif ($receipt->kurs_name == "USD")
                                                        @if ($invoice->currency->name == "CNY")
                                                            <p>@lang('messages.Amount') (CNY): <b>{{ '¥ ' .number_format(($receipt->amount*$invoice->rate_usd)/$invoice->rate_cny, 0, ',', '.') }}</b></p>
                                                        @elseif ($invoice->currency->name == "TWD")
                                                            <p>@lang('messages.Amount') (TWD): <b>{{ 'NT$ ' .number_format(($receipt->amount*$invoice->rate_usd)/$invoice->rate_twd, 0, ',', '.') }}</b></p>
                                                        @elseif ($invoice->currency->name == "IDR")
                                                            <p>@lang('messages.Amount') (IDR): <b>{{ 'RP ' .number_format(($receipt->amount*$invoice->rate_usd), 0, ',', '.') }}</b></p>
                                                        @endif
                                                        <p>@lang('messages.Amount') (USD): <b>{{ '$ ' .number_format($receipt->amount, 0, ',', '.') }}</b></p>
                                                    @elseif ($receipt->kurs_name == "TWD")
                                                        @if ($invoice->currency->name == "IDR")
                                                            <p>@lang('messages.Amount') (IDR): <b>{{ 'Rp ' .number_format(($receipt->amount*$invoice->rate_twd), 0, ',', '.') }}</b></p>
                                                        @elseif ($invoice->currency->name == "CNY")
                                                            <p>@lang('messages.Amount') (CNY): <b>{{ '¥ ' .number_format(($receipt->amount*$invoice->rate_twd)/$invoice->rate_cny, 0, ',', '.') }}</b></p>
                                                        @elseif ($invoice->currency->name == "USD")
                                                            <p>@lang('messages.Amount') (USD): <b>{{ '$ ' .number_format(($receipt->amount*$invoice->rate_twd)/$invoice->rate_usd, 0, ',', '.') }}</b></p>
                                                        @endif
                                                        <p>@lang('messages.Amount') (TWD): <b>{{ 'NT$ ' .number_format($receipt->amount, 0, ',', '.') }}</b></p>

                                                    @elseif ($receipt->kurs_name == "CNY")
                                                        @if ($invoice->currency->name == "IDR")
                                                            <p>@lang('messages.Amount') (IDR): <b>{{ 'Rp ' .number_format(($receipt->amount*$invoice->rate_cny), 0, ',', '.') }}</b></p>
                                                        @elseif ($invoice->currency->name == "TWD")
                                                            <p>@lang('messages.Amount') (TWD): <b>{{ 'NT$ ' .number_format(($receipt->amount*$invoice->rate_cny)/$invoice->rate_twd, 0, ',', '.') }}</b></p>
                                                        @elseif ($invoice->currency->name == "USD")
                                                            <p>@lang('messages.Amount') (USD): <b>{{ '$ ' .number_format(($receipt->amount*$invoice->rate_cny)/$invoice->rate_usd, 0, ',', '.') }}</b></p>
                                                        @endif
                                                        <p>@lang('messages.Amount') (CNY): <b>{{ '¥ ' .number_format($receipt->amount, 0, ',', '.') }}</b></p>
                                                    @endif

                                                    <p>@lang('messages.Paid on') : {{ dateFormat($receipt->payment_date) }}<br>
                                                </div>
                                                <hr class="form-hr">
                                                {{-- MODAL RECEIPT --}}
                                                <div class="modal fade" id="desktop-valid-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content modal-img">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> Payment Receipt</div>
                                                                </div>
                                                                <img style="height: 630px;" src="{{ asset('storage/receipt/weddings/'.$receipt->receipt_img) }}" alt="">
                                                                <div class="card-box-footer">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
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
                                                    <div class="btn-action-right">
                                                        <a href="#" data-toggle="modal" data-target="#desktop-paid-receipt-{{ $receipt->id }}">
                                                            <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="pmt-des">
                                                    <p>@lang('messages.Receipt Received') : {{ dateFormat($receipt->created_at) }}</p>
                                                </div>
                                                <hr class="form-hr">
                                                {{-- MODAL RECEIPT --}}
                                                <div class="modal fade" id="desktop-paid-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content modal-img">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> Payment Receipt</div>
                                                                </div>
                                                                <img style="height: 630px;" src="{{ asset('storage/receipt/weddings/'.$receipt->receipt_img) }}" alt="">
                                                                <div class="card-box-footer">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($receipt->status == "Invalid")
                                                <div class="pmt-container unpaid">
                                                    <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                                                    <div class="pmt-status">
                                                        @lang('messages.Invalid')
                                                    </div>
                                                    <div class="btn-action-right">
                                                        <a href="#" data-toggle="modal" data-target="#desktop-invalid-receipt-{{ $receipt->id }}">
                                                            <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="pmt-des">
                                                    <p><i style="color: red">{!! $receipt->note !!}</i></p>
                                                    <p>@lang('messages.Receipt Received') : {{ dateFormat($receipt->created_at) }}</p>
                                                </div>
                                                <div class="modal fade" id="desktop-invalid-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content modal-img">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                                                </div>
                                                                <img style="height: 630px;" src="{{ asset('storage/receipt/weddings/'.$receipt->receipt_img) }}" alt="">
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
                                                <hr class="form-hr">
                                            @endif
                                        @endforeach
                                    @else
                                        <div class="pmt-container pending">
                                            <i class="icon-copy fa fa-hourglass" aria-hidden="true"></i>
                                            <div class="pmt-status">
                                                @lang('messages.Awaiting Payment')
                                            </div>
                                        </div>
                                        <div class="pmt-des">
                                            <b>{{ $invoice->inv_no }}</b>
                                            <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                                        </div>
                                    @endif
                                @else
                                    {{-- @if (isset($receipts))
                                        @if ($receipts->status == "Paid")
                                            <div class="pmt-container">
                                                <i class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                                                <div class="pmt-status">
                                                    @lang('messages.Paid')
                                                </div>
                                            </div>
                                            <div class="pmt-des">
                                                <b>{{ $orderWedding->orderno." - ". $invoice->inv_no }}</b>
                                                <p>@lang('messages.Paid on') : {{ dateFormat($receipts->payment_date) }}<br>
                                                @lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                                                
                                            </div>
                                            <div class="view-receipt">
                                                <a class="action-btn" href="#" data-toggle="modal" data-target="#receipt-{{ $receipts->id }}">
                                                    <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                            <div class="modal fade" id="receipt-{{ $receipts->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content modal-img">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                                            </div>
                                                            <img style="height: 630px;" src="{{ asset('storage/receipt/'.$receipts->receipt_img) }}" alt="">
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
                                                <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
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
                                            <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                                        </div>
                                    @endif --}}
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection