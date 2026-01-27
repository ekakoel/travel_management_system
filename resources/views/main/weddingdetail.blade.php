@section('title', __('messages.Weddings'))
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
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <div class="title">
                        @if ($service != "")
                            {!! $service->icon !!} @lang('messages.'.$service->name)
                        @endif
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/dashboard">@lang('messages.Dashboard')</a></li>
                            <li class="breadcrumb-item"><a href="/{{ $service->nicname }}">@lang('messages.'.$service->name)</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{!! $wedding->name !!}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            {{-- ATTENTIONS MOBILE --}}
            <div class="col-md-4 mobile">
                <div class="row">
                    @include('layouts.attentions')
                </div>
            </div>
            <div class="col-md-8">
                <div class="card-box p-b-18">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy fi-torso-business"></i><i class="icon-copy fi-torso-female"></i>{{ $wedding->name }}</div>
                    </div>
                    {{-- WEDDING DETAIL --------------------------------------------------------------------------------------------------------------------------------}}
                    <div class="page-card">
                        <figure class="card-banner">
                            <img src="{{ asset ('storage/weddings/wedding-cover/' . $wedding->cover) }}" alt="{{ $wedding->name }}" loading="lazy">
                        </figure>
                        <div class="card-content">
                            <div class="row">
                                <div class="col-6">
                                    <div class="card-subtitle"><i class="icon-copy dw dw-hotel" aria-hidden="true"></i>@lang('messages.Hotel'):</div>
                                    <p>{{  $hotel->name  }}</p>
                                </div>
                                <div class="col-6">
                                    <div class="card-subtitle"><i class="icon-copy fa fa-users" aria-hidden="true"></i>@lang('messages.Capacity'):</div>
                                    <p>{{  $wedding->capacity }} @lang('messages.pax')</p>
                                </div>
                                <div class="col-6">
                                    <div class="card-subtitle"><i class="icon-copy fa fa-calendar" aria-hidden="true"></i>@lang('messages.Period'):</div>
                                    <p>{{  date("m/d/y",strtotime($wedding->period_start))." - ".date("m/d/y",strtotime($wedding->period_end)) }} </p>
                                </div>
                            </div>
                            <div class="card-text">
                                <div class="row ">
                                    <div class="col-12">
                                        <div class="card-subtitle p-b-8"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i>@lang('messages.Wedding Brochure'):</div>
                                        @foreach ($hotel->contract_wedding as $br_no=>$brochure)
                                            <a href="#" data-target="#wedding-pdf-{{ $brochure->id }}" data-toggle="modal">
                                                <div class="icon-list view-pdf" data-toggle="tooltip" data-placement="top" title="View PDF Rate">
                                                    {{ ++$br_no.". ".$brochure->name." PDF" }}
                                                </div>
                                            </a>
                                            {{-- Modal Property PDF ----------------------------------------------------------------------------------------------------------- --}}
                                            <div class="modal fade" id="wedding-pdf-{{ $brochure->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                        <div class="modal-body pd-5">
                                                            <embed src="storage/hotels/wedding-contract/{{ $brochure->file_name }}" frameborder="10" width="100%" height="850px">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-b-8">
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
                                        @if ($fixed_services)
                                            @foreach ($fixed_services_id as $fixed_service_id)
                                                @php
                                                    $f_service = $fixed_services->where('id',$fixed_service_id)->first();
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
                        @if ($wedding->remark)
                            <div class="col-md-12">
                                <div class="tab-inner-title">@lang('messages.Remark')</div>
                                <div class="data-web-text-area">{!! $wedding->remark !!} </div>
                            </div>
                        @endif
                        @if ($wedding->cancellation_policy)
                            <div class="col-md-12">
                                <div class="tab-inner-title">@lang('messages.Cancellation Policy')</div>
                                <div class="data-web-text-area">{!! $wedding->cancellation_policy !!} </div>
                            </div>
                        @endif
                        <div class="col-md-12 m-b-8">
                            {{-- WEDDING OPTIONAL SERVICES ------------------------------------------------------------------------------------------------------------------------}}
                            <div class="tab-inner-title">@lang('messages.Optional Services')</div>
                            <div class="card-box-content">
                                @php
                                    $wed_venues_id = json_decode($wedding->wedding_venue_id);
                                    $din_venues_id = json_decode($wedding->dinner_venue_id);
                                    $decos = json_decode($wedding->decorations_id);
                                    $v_ids = json_decode($wedding->entertainments_id);
                                    $ms_id = json_decode($wedding->makeup_id);
                                    $snvs_id = json_decode($wedding->suite_and_villas_id);
                                    $ot_ser_ids = json_decode($wedding->other_service_id);
                                    $trans_id = json_decode($wedding->transport_id);
                                @endphp
                                {{-- WEDDING VENUE --------------------------------------------------------------------------------------------------------------------------------}}
                                @if ($wedding->wedding_venue_id)
                                    @if ($wed_venues_id)
                                        
                                    @endif
                                @endif
                                {{-- WEDDING SUITES & VILLAS ----------------------------------------------------------------------------------------------------------------------}}
                                @if ($wedding->suite_and_villas_id)
                                    @if ($snvs_id)
                                        @foreach ($snvs_id as $snv_id)
                                            @php
                                                $suite_adn_room = $suite_and_villas->where('id',$snv_id)->first();
                                                $hotel_name = $hotels->where('id',$suite_adn_room->hotels_id)->first();
                                            @endphp
                                            <div class="card">
                                                <a href="#" data-toggle="modal" data-target="#detail-suite_adn_room-{{ $suite_adn_room->id }}">
                                                    <div class="image-container">
                                                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/hotels/hotels-room/' . $suite_adn_room->cover) }}" alt="{{ $suite_adn_room->rooms }}">
                                                        <div class="name-card">
                                                            <b>@lang('messages.Suites and Villas')</b>
                                                            <p>{{ $suite_adn_room->rooms }}</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            {{-- MODAL VIEW DINNER VENUE --------------------------------------------------------------------------------------------------------------- --}}
                                            <div class="modal fade" id="detail-suite_adn_room-{{ $suite_adn_room->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fa fa-hotel" aria-hidden="true"></i>{{ $hotel_name->name." - ". $suite_adn_room->rooms }}</div>
                                                            </div>
                                                            <div class="card-banner m-b-8">
                                                                <img class="rounded" src="{{ asset('storage/hotels/hotels-room/' . $suite_adn_room->cover) }}" alt="{{ $suite_adn_room->cover }}" loading="lazy">
                                                            </div>
                                                            @if ($suite_adn_room->service)
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-4">
                                                                            <b>Service: </b><p>{!! $suite_adn_room->service !!}</p>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <b>Duration: </b><p>{!! $suite_adn_room->duration." ".$suite_adn_room->time !!}</p>
                                                                        </div>
                                                                        @if ($suite_adn_room->description)
                                                                            <div class="col-sm-12">
                                                                                <b>Description: </b><p>{!! $suite_adn_room->description !!}</p>
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
                                        @endforeach
                                    @endif
                                @endif
                                {{-- WEDDING DECORATION ---------------------------------------------------------------------------------------------------------------------------}}
                                @if ($wedding->decorations_id)
                                    @if ($decos)
                                        @foreach ($decos as $deco)
                                            @php
                                                $decoration = $decorations->where('id',$deco)->first();
                                            @endphp
                                            @if ($decoration)
                                                <div class="card">
                                                    <a href="#" data-toggle="modal" data-target="#detail-decoration-{{ $decoration->id }}">
                                                        <div class="image-container">
                                                            <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $decoration->cover) }}" alt="{{ $decoration->name }}">
                                                            <div class="name-card">
                                                                <b>@lang('messages.Decoration')</b>
                                                                <p>{{ $decoration->service }}</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                {{-- MODAL VIEW DECORATION --------------------------------------------------------------------------------------------------------------- --}}
                                                <div class="modal fade" id="detail-decoration-{{ $decoration->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fi-trees"></i>{{ $decoration->service }}</div>
                                                                </div>
                                                                <div class="card-banner m-b-8">
                                                                    <img class="rounded" src="{{ asset('storage/vendors/package/' . $decoration->cover) }}" alt="{{ $decoration->cover }}" loading="lazy">
                                                                </div>
                                                                @if ($decoration->service)
                                                                    <div class="card-text">
                                                                        <div class="row ">
                                                                            <div class="col-sm-4">
                                                                                <b>@lang('messages.Service'): </b><p>{!! $decoration->service !!}</p>
                                                                            </div>
                                                                            <div class="col-sm-4">
                                                                                <b>@lang('messages.Duration'): </b><p>{!! $decoration->duration." ".$decoration->time !!}</p>
                                                                            </div>
                                                                            @if ($decoration->description)
                                                                                <div class="col-sm-12">
                                                                                    <b>@lang('messages.Description'): </b><p>{!! $decoration->description !!}</p>
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
                                @endif
                                {{-- WEDDING DINNER VENUE -------------------------------------------------------------------------------------------------------------------------}}
                                @if ($wedding->dinner_venue_id)
                                    @if ($din_venues_id)
                                    @foreach ($din_venues_id as $din_venue_id)
                                        <div class="card">
                                                @php
                                                    $dinner_venue = $dinner_venues->where('id',$din_venue_id)->first();
                                                @endphp
                                                <a href="#" data-toggle="modal" data-target="#detail-dinner_venue-{{ $din_venue_id }}">
                                                    <div class="image-container">
                                                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $dinner_venue->cover) }}" alt="{{ $dinner_venue->name }}">
                                                        <div class="name-card">
                                                            <b>@lang('messages.Dinner Venue')</b>
                                                            <p>{{ $dinner_venue->service }}</p>
                                                        </div>
                                                    </div>
                                                </a>
                                                {{-- MODAL VIEW DINNER VENUE --------------------------------------------------------------------------------------------------------------- --}}
                                                <div class="modal fade" id="detail-dinner_venue-{{ $din_venue_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-birthday-cake" aria-hidden="true"></i>{{ $dinner_venue->service }}</div>
                                                                </div>
                                                                <div class="card-banner m-b-8">
                                                                    <img class="rounded" src="{{ asset('storage/vendors/package/' . $dinner_venue->cover) }}" alt="{{ $dinner_venue->cover }}" loading="lazy">
                                                                </div>
                                                                @if ($dinner_venue->service)
                                                                    <div class="card-text">
                                                                        <div class="row ">
                                                                            <div class="col-sm-4">
                                                                                <b>@lang('messages.Service'): </b><p>{!! $dinner_venue->service !!}</p>
                                                                            </div>
                                                                            <div class="col-sm-4">
                                                                                <b>@lang('messages.Duration'): </b><p>{!! $dinner_venue->duration." ".$dinner_venue->time !!}</p>
                                                                            </div>
                                                                            @if ($dinner_venue->description)
                                                                                <div class="col-sm-12">
                                                                                    <b>@lang('messages.Description'): </b><p>{!! $dinner_venue->description !!}</p>
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
                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                                {{-- WEDDING MAKE-UP ------------------------------------------------------------------------------------------------------------------------------}}
                                @if ($wedding->makeup_id)
                                    @if ($ms_id)
                                        @foreach ($ms_id as $m_id)
                                            @php
                                                $makeup_artis = $muas->where('id',$m_id)->first();
                                            @endphp
                                            @if ($makeup_artis)
                                                <div class="card">
                                                    <a href="#" data-toggle="modal" data-target="#detail-makeup_artis-{{ $m_id }}">
                                                        <div class="image-container">
                                                            <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $makeup_artis->cover) }}" alt="{{ $makeup_artis->service }}">
                                                            <div class="name-card">
                                                                <b>@lang('messages.Make-up')</b>
                                                                <p>{{ $makeup_artis->service }}</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                {{-- MODAL VIEW DINNER VENUE --------------------------------------------------------------------------------------------------------------- --}}
                                                <div class="modal fade" id="detail-makeup_artis-{{ $m_id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy ion-paintbrush"></i>{{ $makeup_artis->service }}</div>
                                                                </div>
                                                                <div class="card-banner m-b-8">
                                                                    <img class="rounded" src="{{ asset('storage/vendors/package/' . $makeup_artis->cover) }}" alt="{{ $makeup_artis->cover }}" loading="lazy">
                                                                </div>
                                                                @if ($makeup_artis->service)
                                                                    <div class="card-text">
                                                                        <div class="row ">
                                                                            <div class="col-sm-4">
                                                                                <b>@lang('messages.Service'): </b><p>{!! $makeup_artis->service !!}</p>
                                                                            </div>
                                                                            <div class="col-sm-4">
                                                                                <b>@lang('messages.Duration'): </b><p>{!! $makeup_artis->duration." ".$makeup_artis->time !!}</p>
                                                                            </div>
                                                                            @if ($makeup_artis->description)
                                                                                <div class="col-sm-12">
                                                                                    <b>@lang('messages.Description'): </b><p>{!! $makeup_artis->description !!}</p>
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
                                @endif
                                {{-- WEDDING ENTERTAINMENT ------------------------------------------------------------------------------------------------------------------------}}
                                @if ($wedding->entertainments_id)
                                    @if ($v_ids)
                                        @foreach ($v_ids as $v_id)
                                            @php
                                                $ents = $entertainments->where('id',$v_id)->first();
                                            @endphp
                                            @if ($ents)
                                                <div class="card">
                                                    <a href="#" data-toggle="modal" data-target="#detail-ents-{{ $ents->id }}">
                                                        <div class="image-container">
                                                            <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $ents->cover) }}" alt="{{ $ents->service }}">
                                                            <div class="name-card">
                                                                <b>@lang('messages.Entertainment')</b>
                                                                <p>{{ $ents->service }}</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                {{-- MODAL VIEW ENTERTAINMENT --------------------------------------------------------------------------------------------------------------- --}}
                                                <div class="modal fade" id="detail-ents-{{ $ents->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy ion-android-color-palette"></i>{{ $ents->service }}</div>
                                                                </div>
                                                                <div class="card-banner m-b-8">
                                                                    <img class="rounded" src="{{ asset('storage/vendors/package/' . $ents->cover) }}" alt="{{ $ents->cover }}" loading="lazy">
                                                                </div>
                                                                @if ($ents->service)
                                                                    <div class="card-text">
                                                                        <div class="row ">
                                                                            <div class="col-sm-4">
                                                                                <b>@lang('messages.Service'): </b><p>{!! $ents->service !!}</p>
                                                                            </div>
                                                                            <div class="col-sm-4">
                                                                                <b>@lang('messages.Duration'): </b><p>{!! $ents->duration." ".$ents->time !!}</p>
                                                                            </div>
                                                                            @if ($ents->description)
                                                                                <div class="col-sm-12">
                                                                                    <b>@lang('messages.Description'): </b><p>{!! $ents->description !!}</p>
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
                                @endif
                                {{-- WEDDING DOCUMENTATIONS -----------------------------------------------------------------------------------------------------------------------}}
                                @if ($wedding->documentations_id)
                                    @php
                                        $doc_ids = json_decode($wedding->documentations_id)
                                    @endphp
                                    @if ($doc_ids)
                                        @foreach ($doc_ids as $doc_id)
                                            @php
                                                $documentation = $documentations->where('id',$doc_id)->first();
                                            @endphp
                                            <div class="card">
                                                <a href="#" data-toggle="modal" data-target="#detail-documentations-{{ $documentation->id }}">
                                                    <div class="image-container">
                                                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $documentation->cover) }}" alt="{{ $documentation->service }}">
                                                        <div class="name-card">
                                                            <b>@lang('messages.Documentation')</b>
                                                            <p>{{ $documentation->service }}</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            {{-- MODAL VIEW Documentation --------------------------------------------------------------------------------------------------------------- --}}
                                            <div class="modal fade" id="detail-documentations-{{ $documentation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fi-camera"></i>{{ $documentation->service }}</div>
                                                            </div>
                                                            <div class="card-banner m-b-8">
                                                                <img class="rounded" src="{{ asset('storage/vendors/package/' . $documentation->cover) }}" alt="{{ $documentation->cover }}" loading="lazy">
                                                            </div>
                                                            @if ($documentation->service)
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-4">
                                                                            <b>@lang('messages.Service'): </b><p>{!! $documentation->service !!}</p>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <b>@lang('messages.Duration'): </b><p>{!! $documentation->duration." ".$documentation->time !!}</p>
                                                                        </div>
                                                                        @if ($documentation->description)
                                                                            <div class="col-sm-12">
                                                                                <b>@lang('messages.Description'): </b><p>{!! $documentation->description !!}</p>
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
                                        @endforeach
                                    @endif
                                @endif
                                {{-- WEDDING OTHER SERVICES -----------------------------------------------------------------------------------------------------------------------}}
                                @if ($wedding->other_service_id)
                                    @if ($ot_ser_ids)
                                        @foreach ($ot_ser_ids as $ot_ser_id)
                                            @php
                                                $other_service = $other_services->where('id',$ot_ser_id)->first();
                                            @endphp
                                            @if ($other_service)
                                                <div class="card">
                                                    <a href="#" data-toggle="modal" data-target="#detail-other_service-{{ $other_service->id }}">
                                                        <div class="image-container">
                                                            <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/vendors/package/' . $other_service->cover) }}" alt="{{ $other_service->service }}">
                                                            <div class="name-card">
                                                                <b>@lang('messages.Other Services')</b>
                                                                <p>{{ $other_service->service }}</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                {{-- MODAL VIEW OTHER SERVICE --------------------------------------------------------------------------------------------------------------- --}}
                                                <div class="modal fade" id="detail-other_service-{{ $other_service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy ion-ios-flower"></i>{{ $other_service->service }}</div>
                                                                </div>
                                                                <div class="card-banner m-b-8">
                                                                    <img class="rounded" src="{{ asset('storage/vendors/package/' . $other_service->cover) }}" alt="{{ $other_service->cover }}" loading="lazy">
                                                                </div>
                                                                @if ($other_service->service)
                                                                    <div class="card-text">
                                                                        <div class="row ">
                                                                            <div class="col-sm-4">
                                                                                <b>@lang('messages.Service'): </b><p>{!! $other_service->service !!}</p>
                                                                            </div>
                                                                            <div class="col-sm-4">
                                                                                <b>@lang('messages.Duration'): </b><p>{!! $other_service->duration." ".$other_service->time !!}</p>
                                                                            </div>
                                                                            @if ($other_service->description)
                                                                                <div class="col-sm-12">
                                                                                    <b>@lang('messages.Description'): </b><p>{!! $other_service->description !!}</p>
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
                                @endif
                                {{-- WEDDING TRANSPORT ------------------------------------------------------------------------------------------------------------------------}}
                                @if ($trans_id)
                                    @foreach ($trans_id as $tran_id)
                                        @php
                                            $transport = $transports->where('id',$tran_id)->first();
                                        @endphp
                                        @if ($transport)
                                            <div class="card">
                                                <a href="#" data-toggle="modal" data-target="#detail-transport-{{ $transport->id }}">
                                                    <div class="image-container">
                                                        <img class="img-fluid rounded thumbnail-image" src="{{ asset('storage/transports/transports-cover/' . $transport->cover) }}" alt="{{ $transport->name }}">
                                                        <div class="name-card">
                                                            <b>@lang('messages.Transport')</b>
                                                            <p>{{ $transport->name }}</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            {{-- MODAL VIEW TRANSPORT --------------------------------------------------------------------------------------------------------------- --}}
                                            <div class="modal fade" id="detail-transport-{{ $transport->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy ion-android-color-palette"></i>{{ $transport->name }}</div>
                                                            </div>
                                                            <div class="card-banner m-b-8">
                                                                <img class="rounded" src="{{ asset('storage/transports/transports-cover/' . $transport->cover) }}" alt="{{ $transport->name }}" loading="lazy">
                                                            </div>
                                                            @if ($transport->name)
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-4">
                                                                            <b>@lang('messages.Transport'): </b>
                                                                            <p>{!! $transport->brand." ".$transport->name !!}</p>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <b>@lang('messages.Capacity'): </b><p>{!! $transport->capacity." seats" !!}</p>
                                                                        </div>
                                                                        @if ($transport->description)
                                                                            <div class="col-sm-12">
                                                                                <b>@lang('messages.Description'): </b><p>{!! $transport->description !!}</p>
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
                            </div>
                        </div>
                    </div>
                    <div class="card-box-footer">
                        <div class="text-right">
                            <a href="order-wedding-{{ $wedding->code }}">
                                <button class="btn btn-primary">@lang('messages.Order')</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ATTENTIONS DESKTOP --}}
            <div class="col-md-4 desktop">
                <div class="row">
                    @include('layouts.attentions')
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>
    </div>
@endsection

