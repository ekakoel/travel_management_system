@section('title', __('messages.Order Wedding'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <i class="icon-copy fa fa-shopping-cart" aria-hidden="true"></i>@lang('messages.Create Order')
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">@lang('messages.Dashboard')</a></li>
                                <li class="breadcrumb-item"><a href="/weddings">@lang('messages.Weddings')</a></li>
                                <li class="breadcrumb-item"><a href="/wedding-hotel-{{ $hotel->code }}">{{ $hotel->name }}</a></li>
                                <li class="breadcrumb-item active">@lang('messages.Create Order')</li>
                                {{-- <li class="breadcrumb-item active" aria-current="page">@lang('messages.Room'){{ " ".$wedding->rooms }}</a></li> --}}
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fa fa-tag"></i>@lang('messages.Create Order')</div>
                        </div>
                        <div class="row">
                            <div class="col-6 col-md-6">
                                <div class="order-bil text-left">
                                    <img src="{{ asset('images/balikami/bali-kami-tour-logo.png') }}" alt="Bali Kami Tour & Travel">
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
                        <div class="business-name">{{ $business->name }}</div>
                        <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                        <hr class="form-hr">
                        <form id="create-order" action="/fadd-order" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table tb-list nowrap">
                                        <tbody>
                                            <tr>
                                                <td class="htd-1"> @lang('messages.Order No')</td>
                                                <td class="htd-2"><b>{{ 'ORD.' . date('Ymd', strtotime($now)) . '.WED' . $orderno }}</b></td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Order Date')
                                                </td>
                                                <td class="htd-2">
                                                    {{ dateFormat($now) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Service')
                                                </td>
                                                <td class="htd-2">
                                                    @lang('messages.Wedding Package')
                                                </td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                {{-- Admin create order ================================================================= --}}
                                @canany(['weddingRsv','weddingDvl','weddingSls','weddingAuthor'])
                                    <div class="col-md-6">
                                        <div class="mobile">
                                            <hr class="form-hr">
                                        </div>
                                        <div class="form-group ">
                                            <label for="user_id">Select Agent <span>*</span></label>
                                            <select name="user_id" class="custom-select @error('user_id') is-invalid @enderror" value="{{ old('user_id') }}" required>
                                                <option selected value="">Select Agent</option>
                                                @foreach ($agents as $agent)
                                                    <option value="{{ $agent->id }}">{{ $agent->username." (".$agent->code.") @".$agent->office }}</option>
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
                            <div class="tab-inner-title m-t-8">@lang('messages.Order Detail')</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table tb-list nowrap">
                                        <tbody>
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
                                                    @lang('messages.Package')
                                                </td>
                                                <td class="htd-2">
                                                    {{ $wedding->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Venue')
                                                </td>
                                                <td class="htd-2">
                                                    {{ $wedding_venue->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">
                                                    @lang('messages.Invitations')
                                                </td>
                                                <td class="htd-2">
                                                    {{ $wedding->capacity." guests" }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if ($wedding->description)
                                    <div class="col-12">
                                        <div class="tab-inner-title">Description</div>
                                        <div class="data-web-text-area">
                                            {!! $wedding->description !!}
                                        </div>
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
                                @if ($wedding->include)
                                    <div class="col-12 m-b-8">
                                        <div class="tab-inner-title">Include</div>
                                        <div class="data-web-text-area">
                                            {!! $wedding->include !!}
                                        </div>
                                        
                                    </div>
                                @endif
                                @if ($wedding->cancellation_policy)
                                    <div class="col-12">
                                        <div class="tab-inner-title">Cancellation Policy</div>
                                        <div class="data-web-text-area">{!! $wedding->cancellation_policy !!} </div>
                                    </div>
                                @endif
                                
                                <div class="col-md-12">
                                    <div class="tab-inner-title m-t-8">@lang('messages.Wedding Detail')</div>
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="groom_name">@lang('messages.Groom Name')</label>
                                            <input type="text" name="groom_name" class="form-control m-0 @error('groom_name') is-invalid @enderror" placeholder="@lang('messages.Insert groom name')" value="{{ old('groom_name') }}" required>
                                            @error('groom_name')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="groom_chinese">@lang('messages.Groom Chinese Name')</label>
                                            <input type="text" name="groom_chinese" class="form-control m-0 @error('groom_chinese') is-invalid @enderror" placeholder="@lang('messages.Insert groom name')" value="{{ old('groom_chinese') }}">
                                            @error('groom_chinese')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="groom_contact">@lang('messages.Groom Contact')</label>
                                            <input type="text" name="groom_contact" class="form-control m-0 @error('groom_contact') is-invalid @enderror" placeholder="@lang('messages.Insert number')" value="{{ old('groom_contact') }}" required>
                                            @error('groom_contact')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="bride_name">@lang('messages.Bride Name')</label>
                                            <input type="text" name="bride_name" class="form-control m-0 @error('bride_name') is-invalid @enderror" placeholder="@lang('messages.Insert name')" value="{{ old('bride_name') }}" required>
                                            @error('bride_name')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="bride_chinese">@lang('messages.Bride Chinese Name')</label>
                                            <input type="text" name="bride_chinese" class="form-control m-0 @error('bride_chinese') is-invalid @enderror" placeholder="@lang('messages.Insert name')" value="{{ old('bride_chinese') }}">
                                            @error('bride_chinese')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="bride_contact">@lang('messages.Bride Contact')</label>
                                            <input type="text" name="bride_contact" class="form-control m-0 @error('bride_contact') is-invalid @enderror" placeholder="@lang('messages.Insert number')" value="{{ old('bride_contact') }}" required>
                                            @error('bride_contact')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="checkin">@lang('messages.Check In')</label>
                                            <input readonly type="text" name="checkin" id="checkIn" placeholder="@lang('messages.Select date')" class="form-control date-picker @error('checkin') is-invalid @enderror" value="{{ old('checkin') }}" required>
                                            @error('checkin')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="wedding_date">@lang('messages.Wedding Date')</label>
                                            <input readonly type="text" name="wedding_date" id="weddingDate" placeholder="@lang('messages.Select date')" class="form-control date-picker @error('wedding_date') is-invalid @enderror" value="{{ old('wedding_date') }}" required>
                                            @error('wedding_date')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="number_of_guests">@lang('messages.Number of invitations')</label>
                                            <input type="number" min="5" max="{{ $wedding->capacity }}" name="number_of_guests" class="form-control m-0 @error('number_of_guests') is-invalid @enderror" placeholder="@lang('messages.Maximum') {{ $wedding->capacity }} @lang('messages.guests')" value="{{ old('number_of_guests') }}" required>
                                            @error('number_of_guests')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="tab-inner-title m-t-8">@lang('messages.Flight Detail')</div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="arrival_flight">@lang('messages.Arrival Flight')</label>
                                            <input type="text" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror" placeholder="@lang('messages.Arrival Flight')" value="{{ old('arrival_flight') }}">
                                            @error('arrival_flight')
                                                <div
                                                    class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="arrival_time">@lang('messages.Arrival Date and Time')</label>
                                            <input readonly type="text" id="arrivalFlightDate" name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ old('arrival_time') }}">
                                            @error('arrival_time')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="departure_flight">@lang('messages.Departure Flight')</label>
                                            <input type="text" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror" placeholder="@lang('messages.Departure Flight')" value="{{ old('departure_flight') }}">
                                            @error('departure_flight')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="departure_time"> @lang('messages.Departure Date and Time')</label>
                                            <input readonly type="text" id="departureFlightDate" name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ old('departure_time') }}">
                                            @error('departure_time')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <label for="note">Note</label>
                                            <textarea id="note" name="note" placeholder="@lang('messages.Optional')" class="textarea_editor form-control border-radius-0" value="{{ old('note') }}"></textarea>
                                            @error('note')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="orderno" value="{{ 'ORD.' . date('Ymd', strtotime($now)) . '.WED' . $orderno }}">
                            <input type="hidden" name="page" value="order-wedding">
                            <input type="hidden" name="capacity" value="{{ $wedding->capacity }}">
                            <input type="hidden" name="package_name" value="{{ $wedding->name }}">
                            <input type="hidden" name="action" value="Create Order">
                            <input type="hidden" name="sales_agent" value="{{ Auth::user()->id }}">
                            <input type="hidden" name="servicename" value="{{ $wedding->name }}">
                            <input type="hidden" name="subservice" value="{{ $wedding->hotels->name }}">
                            <input type="hidden" name="subservice_id" value="{{ $wedding->hotels->id }}">
                            <input type="hidden" name="service" value="Wedding Package">
                            <input type="hidden" name="service_id" value="{{ $wedding->id }}">
                            <input type="hidden" name="duration" value="{{ $wedding->duration }}">
                            <input type="hidden" name="cancellation_policy" value="{{ $wedding->cancellation_policy }}">
                            <input type="hidden" name="location" value="{{ $wedding->hotels->name }}">
                            <input type="hidden" name="include" value="{{ $wedding->include }}">
                            <input type="hidden" name="additional_info" value="{{ $wedding->additional_info }}">
                            {{-- <input type="hidden" name="normal_price" value="{{ $wedding_price }}"> --}}
                            <input type="hidden" name="period_start" value="{{ $wedding->period_start }}">
                            <input type="hidden" name="period_end" value="{{ $wedding->period_end }}">
                            <input type="hidden" name="wedding_id" value="{{ $wedding->id }}">
                            {{-- <input type="hidden" name="wedding_id" value="{{ $wedding->id }}">
                            <input type="hidden" name="wedding_venue_id" value="{{ $wedding->wedding_venue_id }}">
                            <input type="hidden" name="wedding_makeup_id" value="{{ $wedding->makeup_id }}">
                            <input type="hidden" name="wedding_room_id" value="{{ $wedding->suite_and_villas_id }}">
                            <input type="hidden" name="wedding_documentation_id" value="{{ $wedding->documentations_id }}">
                            <input type="hidden" name="wedding_decoration_id" value="{{ $wedding->decorations_id }}">
                            <input type="hidden" name="wedding_dinner_venue_id" value="{{ $wedding->dinner_venue_id }}">
                            <input type="hidden" name="wedding_entertainment_id" value="{{ $wedding->entertainments_id }}">
                            <input type="hidden" name="wedding_transport_id" value="{{ $wedding->transport_id }}">
                            <input type="hidden" name="wedding_other_id" value="{{ $wedding->other_service_id }}"> --}}
                        </form>
                        <div class="card-box-footer">
                            <button type="submit" form="create-order" id="normal-reserve" class="btn btn-primary"><i class="icon-copy fa fa-shopping-basket" aria-hidden="true"></i> @lang('messages.Order')</button>
                            <a href="/wedding-{{ $wedding->code }}">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
@endsection