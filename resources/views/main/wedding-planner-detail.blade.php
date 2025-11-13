@section('title', __('messages.Weddings'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <i class="icon-copy fi-page-edit"></i>@lang('messages.Edit Wedding Planner')
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="wedding-planner">@lang('messages.Wedding Planner')</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                {{-- ALERT --}}
                @if (count($errors) > 0)
                    @include('partials.alert', ['type' => 'danger', 'messages' => $errors->all()])
                @endif
                @if (\Session::has('success'))
                    @include('partials.alert', ['type' => 'success', 'messages' => [\Session::get('success')]])
                @endif
                @if (\Session::has('danger'))
                    @include('partials.alert', ['type' => 'danger', 'messages' => [\Session::get('danger')]])
                @endif
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card-box m-b-18">
                            
                            <div class="card-box-title m-b-18">
                                <div class="title">
                                    @lang('messages.Wedding Plans')
                                </div>
                            </div>
                            <div class="product-detail-wrap">
                                <div class="row">
                                    <div class="col-6 col-md-6">
                                        <div class="order-bil text-left">
                                            <img src="/storage/logo/logo-color-bali-kami.png"alt="Bali Kami Tour & Travel">
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-6 text-right flex-end">
                                        <div class="label-title">@lang('messages.Wedding')</div>
                                    </div>
                                    <div class="col-md-12 text-right">
                                        <div class="label-date" style="width: 100%;">
                                            {{ dateFormat($wedding_planner->created_at) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="business-name">{{ $business->name }}</div>
                                        <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                    </div>
                                    <div class="col-md-6 text-right v-end">
                                        @if ($handeled_by)
                                            <div class="card-text"><b>@lang('messages.Handled by') : </b>{{ $handeled_by->name }}</div>
                                        @else
                                            <div class="card-text"><b><i>@lang('messages.Unhandled')</i></b></div>
                                        @endif
                                    </div>
                                </div>
                                <hr class="form-hr">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <table class="table tb-list">
                                            <tr>
                                                <td class="htd-1">@lang('messages.No') </td>
                                                <td class="htd-2"><b>{{ $wedding_planner->wedding_planner_no }}</b></td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">@lang('messages.Date') </td>
                                                <td class="htd-2">{{ date('Y-m-d', strtotime($wedding_planner->created_at)) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="htd-1">@lang('messages.Service') </td>
                                                <td class="htd-2">
                                                    @lang('messages.Wedding')
                                                </td>
                                            </tr>
                                            @if ($wedding_planner->status == "Confirmed")
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
                                        @if ($wedding_planner->status == "Active")
                                            <div class="page-status order-status-active"> @lang('messages.Confirmed') <span>@lang('messages.Status'):</span></div>
                                        @elseif ($wedding_planner->status == "Pending")
                                            <div class="page-status order-status-pending">@lang('messages.'.$wedding_planner->status) <span>@lang('messages.Status'):</span></div>
                                        @elseif ($wedding_planner->status == "Rejected")
                                            <div class="page-status order-status-rejected">@lang('messages.'.$wedding_planner->status) <span>@lang('messages.Status'):</span></div>
                                        @elseif ($wedding_planner->status == "Approved")
                                            <div class="page-status order-status-approve">@lang('messages.'.$wedding_planner->status) <span>@lang('messages.Status'):</span></div>
                                        @elseif ($wedding_planner->status == "Confirmed")
                                            <div class="page-status order-status-confirm">@lang('messages.'.$wedding_planner->status) <span>@lang('messages.Status'):</span></div>
                                        @elseif ($wedding_planner->status == "Paid")
                                            <div class="page-status order-status-paid">@lang('messages.'.$wedding_planner->status) <span>@lang('messages.Status'):</span></div>
                                        @else
                                            <div class="page-status" style="color: rgb(48, 48, 48)">@lang('messages.'.$wedding_planner->status) <span>@lang('messages.Status'):</span></div>
                                        @endif
                                    </div>
                                {{-- CONTRACT WEDDING --}}
                                {{-- <table class="w-100 m-b-18">
                                    <tr>
                                        <td colspan="4" class="tb-bordered text-center bg-blue text-white f-w-700">{{ $wedding_planner->wedding_planner_no }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="tb-bordered text-center bg-blue text-white">
                                            <b>@lang('messages.Name of couple'):</b>
                                            {{ "Mr. ".$wedding_planner->groom_name }}
                                            @if ($wedding_planner->groom_chinese)
                                                ({{ $wedding_planner->groom_chinese }})
                                            @endif
                                            & 
                                            {{ "Ms. ".$wedding_planner->bride_name }}
                                            @if ($wedding_planner->bride_chinese)
                                                ({{ $wedding_planner->bride_chinese }})
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="tb-bordered">
                                            <b>@lang('messages.Arrival Flight'): </b>
                                            @if ($wedding_planner->arrival_flight)
                                                {{ $wedding_planner->arrival_flight }} {{ date("y/m/d (H.i)",strtotime($wedding_planner->arrival_time)) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td colspan="2" class="tb-bordered">
                                            <b>@lang('messages.Departure Flight'): </b>
                                            @if ($wedding_planner->departure_flight)
                                                {{ $wedding_planner->departure_flight }} {{ date("y/m/d (H.i)",strtotime($wedding_planner->departure_time)) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center tb-bordered" style="width: 33.3%"><b>@lang('messages.Check-in') & @lang('messages.Check-out')</b></td>
                                        <td colspan="2" class="text-center tb-bordered"><b>@lang('messages.Hotel')</b></td>
                                        <td class="text-center tb-bordered" style="width: 33.3%"><b>@lang('messages.Contact')</b></td>
                                    </tr>
                                    <tr>
                                        @if ($wedding_planner->checkin)
                                            <td class="text-center tb-bordered">{{ date("m/d",strtotime($wedding_planner->checkin)) }} & {{ date("m/d",strtotime($wedding_planner->checkout)) }}</td>
                                        @else
                                            <td class="text-center tb-bordered">-</td>
                                        @endif
                                        <td colspan="2" class="text-center tb-bordered">{{ $hotel->name }}</td>
                                        <td class="text-center tb-bordered">{{ $hotel->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="tb-bordered text-center f-w-700" style="font-size: 1rem !important">{{ $hotel->name }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="tb-bordered text-center f-w-700">{{ date('l,F d,Y',strtotime($wedding_planner->wedding_date))." (".date('H.i A',strtotime($wedding_planner->slot))." - ".date('H.i A',strtotime('+2 hours',strtotime($wedding_planner->slot))).")" }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="tb-bordered" style="width: 50%">
                                            <b>@lang('messages.Wedding Venue'):</b> {!! $wedding_venue->name !!}
                                        </td>
                                        <td colspan="2" class="tb-bordered">
                                            <b>@lang('messages.Number of Invitations'):</b> {!! $wedding_planner->number_of_invitations !!} @lang('messages.Invitations')
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="tb-bordered">
                                            <b>@lang('messages.Include'):</b>
                                            {!! $wedding_package->include !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="tb-bordered">
                                            <b>@lang('messages.Wedding Reception'):</b>
                                            @if ($wedding_reception)
                                                {{ $wedding_reception->name }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr style="height: 100px">
                                        <td rowspan="2" colspan="2" class="tb-bordered" >
                                            <b>@lang('messages.Remarks'):</b>
                                            {{ $wedding_planner->remark }}
                                        </td>
                                        <td colspan="2" class="tb-bordered">
                                            <b>@lang('messages.Company Name'): </b>{{ $business->name }}<br>
                                            <b>@lang('messages.Field of Business'): </b>{{ __('messages.'.$business->caption) }}<br>
                                            <b>@lang('messages.License'): </b>{{ $business->license }}<br>
                                            <b>@lang('messages.Address'): </b>{{ $business->address }}<br>
                                            <b>@lang('messages.TAX Number'): </b>{{ $business->tax_number }}
                                        </td>
                                    </tr>
                                    <tr style="height: 100px">
                                        <td colspan="2" class="tb-bordered">
                                            <b>@lang('messages.Account'): </b>{{ $bank_account->currency }} @lang('messages.account')<br>
                                            <b>@lang('messages.BANK'):</b> {{ $bank_account->bank }}<br>
                                            @if ($bank_account->swift_code)
                                                <b>@lang('messages.SWIFT Code'): </b>{{ $bank_account->swift_code }}<br>
                                            @endif
                                            @if ($bank_account->bank_code)
                                                <b>@lang('messages.Code'): </b>{{ $bank_account->bank_code }}<br>
                                            @endif
                                            <b>@lang('messages.Name'): </b>{{ $bank_account->name }}<br>
                                            <b>@lang('messages.Account Number'): </b>{{ $bank_account->number }}<br>
                                        </td>
                                    </tr>
                                    <tr style="height: 80px">
                                        <td colspan="2" class="tb-bordered">
                                            <b>@lang('messages.Company Seal'):</b>
                                        </td>
                                        <td colspan="2" class="tb-bordered">
                                            <b>@lang('messages.Company Seal'):</b>
                                            <div class="stampel">
                                                <img src="{{ asset('storage/stampel/stempel_bali_kami_group.png') }}" alt="Stamp Bali Kami Group">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tb-bordered text-center bg-blue text-white f-w-600"><b>@lang('messages.Bali Contact Person')</b></td>
                                        <td colspan="2" class="tb-bordered text-center bg-blue text-white f-w-600"><b>@lang('messages.Company Phone Number')</b></td>
                                        <td class="tb-bordered text-center bg-blue text-white f-w-600"><b>@lang('messages.Emergency Contact')</b></td>
                                    </tr>
                                    <tr>
                                        <td class="tb-bordered">{{ env('BALI_CONTACT_NAME_01').": ".env('BALI_CONTACT_PHONE_01') }} </td>
                                        <td rowspan="2" colspan="2" class="tb-bordered">{{ env('BALI_CONTACT_OFFICE_PHONE') }}</td>
                                        <td class="tb-bordered">{{ env('BALI_CONTACT_NAME_03').": ".env('BALI_CONTACT_PHONE_03') }}</td>
                                    </tr>
                                    <tr>
                                        <td  class="tb-bordered">{{ env('BALI_CONTACT_NAME_02').": ".env('BALI_CONTACT_PHONE_02') }} </td>
                                        <td class="tb-bordered"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="tb-bordered text-center"><i>@lang('messages.Do not use +62 for local call, dial 0 and then dial the number')</i></td>
                                    </tr>
                                </table> --}}
                                
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        @if (isset($wedding_planner->arrival_flight)  or isset($wedding_planner->arrival_time) or isset($wedding_planner->departure_flight) or isset($wedding_planner->departure_time))
                                            <div class="page-subtitle m-b-8">@lang('messages.Flight')
                                                <span>
                                                    <a href="#" data-toggle="modal" data-target="#add-flight-{{ $wedding_planner->id }}"> 
                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Update Flight')" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <table class="table tb-list" >
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Arrival Flight')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ $wedding_planner->arrival_flight }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Arrival Time')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ date('Y-m-d (H.i A)',strtotime($wedding_planner->arrival_time)) }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="table tb-list" >
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Departure Flight')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ $wedding_planner->departure_flight }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Departure Time')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ date('Y-m-d (H.i A)',strtotime($wedding_planner->departure_time)) }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        @else
                                            <div class="page-subtitle m-b-8">@lang('messages.Flight')
                                                <span>
                                                    <a href="#" data-toggle="modal" data-target="#add-flight-{{ $wedding_planner->id }}"> 
                                                        <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add Flight')" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                            </div>
                                            <div class="page-text">
                                                <div class="page-notification">
                                                    @lang('messages.Please add the arrival and departure schedule!')
                                                </div>
                                            </div>
                                        @endif
                                        {{-- MODAL ADD OR UPDATE FLIGHT  --}}
                                        <div class="modal fade" id="add-flight-{{ $wedding_planner->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            @if (isset($wedding_planner->arrival_flight)  or isset($wedding_planner->arrival_time) or isset($wedding_planner->departure_flight) or isset($wedding_planner->departure_time))
                                                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Update Flight')</div>
                                                            @else
                                                                <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Add Flight')</div>
                                                            @endif
                                                            
                                                        </div>
                                                        <form id="addFlight" action="/fadd-wedding-planner-flight/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('put')
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="arrival_flight">@lang('messages.Arrival Flight')</label>
                                                                        <input type="text" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ $wedding_planner->arrival_flight }}" required>
                                                                        @error('arrival_flight')
                                                                            <span class="invalid-feedback">
                                                                                {{ $message }}
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="arrival_time">@lang('messages.Arrival Time')</label>
                                                                        <input type="text" readonly name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror"  placeholder="@lang('messages.Select date and time')" value="{{ date('Y-m-d H.i',strtotime($wedding_planner->arrival_time)) }}" required>
                                                                        @error('arrival_time')
                                                                            <span class="invalid-feedback">
                                                                                {{ $message }}
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="departure_flight">@lang('messages.Departure Flight')</label>
                                                                        <input type="text" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ $wedding_planner->departure_flight }}" required>
                                                                        @error('departure_flight')
                                                                            <span class="invalid-feedback">
                                                                                {{ $message }}
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="departure_time">@lang('messages.Departure Time')</label>
                                                                        <input type="text" readonly name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror"  placeholder="@lang('messages.Select date and time')" value="{{ date('Y-m-d H.i',strtotime($wedding_planner->departure_time)) }}" required>
                                                                        @error('departure_time')
                                                                            <span class="invalid-feedback">
                                                                                {{ $message }}
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="card-box-footer">
                                                            @if (isset($wedding_planner->arrival_flight)  or isset($wedding_planner->arrival_time) or isset($wedding_planner->departure_flight) or isset($wedding_planner->departure_time))
                                                                <button type="submit" form="addFlight" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                                            @else
                                                                <button type="submit" form="addFlight" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                            @endif
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                   {{-- COUPLE --}}
                                    <div class="col-md-12">
                                        <div class="page-subtitle">
                                            @lang('messages.Couple')
                                            <span>
                                                <a href="#" data-toggle="modal" data-target="#update-couple-{{ $wedding_planner->id }}"> 
                                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Update Couple')" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                            {{-- MODAL UPDATE COUPLE  --}}
                                            <div class="modal fade" id="update-couple-{{ $wedding_planner->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Update Couple')</div>
                                                            </div>
                                                            <form id="updateCouple" action="/fupdate-wedding-planner-couple/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('put')
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="groom_name">@lang("messages.Groom's Name")</label>
                                                                            <input type="text" name="groom_name" class="form-control @error('groom_name') is-invalid @enderror"  placeholder="@lang("messages.Insert Groom's Name")" value="{{ $wedding_planner->groom_name }}" required>
                                                                            @error('groom_name')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="groom_chinese">@lang("messages.Groom's Chinese Name")</label>
                                                                            <input type="text" name="groom_chinese" class="form-control @error('groom_chinese') is-invalid @enderror"  placeholder="@lang("messages.Chinese Name")" value="{{ $wedding_planner->groom_chinese }}">
                                                                            @error('groom_chinese')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="groom_contact">@lang("messages.Groom's Contact")</label>
                                                                            <input type="text" name="groom_contact" maxlength="19" class="form-control phone @error('groom_contact') is-invalid @enderror"  placeholder="@lang("messages.Contact")" value="{{ $wedding_planner->groom_contact }}">
                                                                            @error('groom_contact')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="bride_name">@lang("messages.Bride's Name")</label>
                                                                            <input type="text" name="bride_name" class="form-control @error('bride_name') is-invalid @enderror"  placeholder="@lang("messages.Insert Bride's Name")" value="{{ $wedding_planner->bride_name }}" required>
                                                                            @error('bride_name')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="bride_chinese">@lang("messages.Bride's Chinese Name")</label>
                                                                            <input type="text" name="bride_chinese" class="form-control @error('bride_chinese') is-invalid @enderror"  placeholder="@lang("messages.Chinese Name")" value="{{ $wedding_planner->bride_chinese }}">
                                                                            @error('bride_chinese')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="bride_contact">@lang("messages.Bride's Contact")</label>
                                                                            <input type="text" name="bride_contact" maxlength="19" class="form-control phone @error('bride_contact') is-invalid @enderror" placeholder="@lang("messages.Contact")" value="{{ $wedding_planner->bride_contact }}">
                                                                            @error('bride_contact')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="updateCouple" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table tb-list" >
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang("messages.Groom's")
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ "Mr. ".$wedding_planner->groom_name }}
                                                            @if ($wedding_planner->groom_chinese)
                                                                {{ "(".$wedding_planner->groom_chinese.")" }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Contact')
                                                        </td>
                                                        <td class="htd-2">
                                                            @if ($wedding_planner->groom_contact)
                                                                {{ $wedding_planner->groom_contact }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                
                                                </table>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <table class="table tb-list" >
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang("messages.Bride's")
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ "Ms. ".$wedding_planner->bride_name }}
                                                            @if ($wedding_planner->bride_chinese)
                                                                {{ "(".$wedding_planner->bride_chinese.")" }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Contact')
                                                        </td>
                                                        <td class="htd-2">
                                                            @if ($wedding_planner->bride_contact)
                                                                {{ $wedding_planner->bride_contact }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="row">
                                        <div class="col-12">
                                        </div>
                                        <div class="col-12">
                                            <div class="page-card">
                                                <div class="card-banner-potrait">
                                                    <img src="{{ asset ('storage/weddings/property/groom-default.png') }}" alt="{{ $wedding_package->name }}" loading="lazy">
                                                </div>
                                                <div class="card-content-potrait">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card-subtitle">
                                                                {{ "Mr.".$bride->groom }}
                                                                @if ($bride->groom_chinese)
                                                                    {{ " / ".$bride->groom_chinese }}
                                                                @endif
                                                            </div>
                                                            <p>
                                                                @if ($bride->groom_contact)
                                                                    {{ $bride->groom_contact }}
                                                                @else
                                                                    ..........
                                                                @endif
                                                            </p>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="page-card">
                                                <div class="card-banner-potrait">
                                                    <img src="{{ asset ('storage/weddings/property/bride-default.png') }}" alt="{{ $wedding_package->name }}" loading="lazy">
                                                </div>
                                                <div class="card-content-potrait">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card-subtitle">
                                                                {{ "Ms.".$bride->bride }}
                                                                @if ($bride->bride_chinese)
                                                                    {{ " / ".$bride->bride_chinese }}
                                                                @endif
                                                            </div>
                                                            <p>
                                                                @if ($bride->bride_contact)
                                                                    {{ $bride->bride_contact }}
                                                                @else
                                                                    ..........
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                    {{-- WEDDING --}}
                                    <div class="col-md-12">
                                        <div class="page-subtitle">
                                            @lang("messages.Wedding")
                                            <span>
                                                <a href="#" data-toggle="modal" data-target="#update-wedding-date-{{ $wedding_planner->id }}"> 
                                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Update Wedding')" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                            {{-- MODAL UPDATE WEDDING  --}}
                                            <div class="modal fade" id="update-wedding-date-{{ $wedding_planner->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Update Wedding')</div>
                                                            </div>
                                                            <form id="updateWeddingDate" action="/fupdate-wedding-date/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('put')
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="wedding_date">@lang("messages.Wedding Date")</label>
                                                                            <input type="text" readonly name="wedding_date" class="form-control date-picker @error('wedding_date') is-invalid @enderror"  placeholder="@lang("messages.Insert Wedding Date")" value="{{ $wedding_planner->wedding_date }}" required>
                                                                            @error('wedding_date')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="slot">Slot <span> *</span></label>
                                                                            <select name="slot" class="custom-select @error('slot') is-invalid @enderror" required>
                                                                                @if ($wedding_planner->slot)
                                                                                    <option selected value="{{ $wedding_planner->slot }}">{{ date('H.i',strtotime($wedding_planner->slot)) }}</option>
                                                                                @else
                                                                                    <option selected value="">@lang('messages.Select slot')</option>
                                                                                @endif
                                                                                @foreach ($slots as $slot)
                                                                                    <option value="{{ $slot }}">{{ date('H.i A',strtotime($slot)) }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('slot')
                                                                                <div class="alert-form">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="number_of_invitations">@lang("messages.Number of Invitations")</label>
                                                                            <input type="number" min="0" name="number_of_invitations" class="form-control @error('number_of_invitations') is-invalid @enderror"  placeholder="@lang("messages.Number of Invitations")" value="{{ $wedding_planner->number_of_invitations }}" required>
                                                                            @error('number_of_invitations')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="updateWeddingDate" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table tb-list" >
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Wedding Date')
                                                        </td>
                                                        @if ($wedding_planner->slot)
                                                            <td class="htd-2">
                                                                {{ date('Y-m-d',strtotime($wedding_planner->wedding_date))." ".date('(H.i A)',strtotime($wedding_planner->slot)) }}
                                                            </td>
                                                        @else
                                                            <td class="htd-2 page-notification">
                                                                {{ date('Y-m-d',strtotime($wedding_planner->wedding_date)) }} (--:--)
                                                            </td>
                                                        @endif
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table tb-list" >
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Invitations')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $wedding_planner->number_of_invitations }} @lang('messages.Invitations')
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            @if (!$wedding_planner->slot)
                                                <div class="col-md-12">
                                                    <div class="page-text">
                                                        <p class="page-notification">
                                                            <i class="icon-copy fi-info"></i> @lang('messages.The wedding ceremony time has not been determined, decide now!')
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- WEDDING PACKAGE --}}
                                    <div class="col-md-12">
                                        <div class="page-subtitle">
                                            @lang('messages.Wedding Package')
                                            <span>
                                                <a href="#" data-toggle="modal" data-target="#wedding-package-{{ $wedding_package->id }}">
                                                    <i class="icon-copy fa fa-eye" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="@lang('messages.Wedding Package Detail')"></i>
                                                </a>
                                            </span>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table tb-list" >
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Package')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $wedding_package->name }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Hotel')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $hotel->name }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table tb-list" >
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Wedding Venue')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $wedding_venue->name }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            Slot
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ date('H.i A',strtotime($wedding_planner->slot)) }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="btn-page-detail">
                                            
                                            {{-- MODAL WEDDING PACKAGE DETAIL --}}
                                            <div class="modal fade" id="wedding-package-{{ $wedding_package->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="title"><i class="icon-copy fa fa-institution" aria-hidden="true"></i> {{ $wedding_package->name }}</div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="card-content">
                                                                        @if ($wedding_package->description)
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-12 col-sm-12">
                                                                                        <div class="card-subtitle">Description</div>
                                                                                        <p>{!! $wedding_package->description !!}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        @if ($wedding_package->include)
                                                                            <hr class="form-hr">
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-12 col-sm-12">
                                                                                        <div class="card-subtitle">Include</div>
                                                                                        <p>{!! $wedding_package->include !!}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        @if ($wedding_package->additional_info)
                                                                            <hr class="form-hr">
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-12 col-sm-12">
                                                                                        <div class="card-subtitle">Additional Information</div>
                                                                                        <p>{!! $wedding_package->additional_info !!}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-box-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- WEDDING INVITATIONS --}}
                                    <div class="col-md-12">
                                        <div class="page-subtitle">
                                            @lang('messages.Invitations')
                                            <span>
                                                <a href="/wedding-invitations-update-{{ $wedding_planner->id }}">
                                                    <i class="icon-copy fa fa-pencil" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="@lang('messages.Update Wedding Invitations')"></i>
                                                </a>
                                            </span>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table id="tbHotels" class="data-table table stripe hover nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th data-priority="1" class="datatable-nosort" style="width: 5%;">No</th>
                                                            <th data-priority="2" style="width: 25%;">Name</th>
                                                            <th style="width: 25%;">ID/Passport</th>
                                                            <th style="width: 15%;">Phone</th>
                                                            <th style="width: 10%;">Country</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($invitations as $no=>$invitation)
                                                            <tr>
                                                                <td>
                                                                    {{ ++$no }}
                                                                </td>
                                                                <td>
                                                                    @if ($invitation->sex == 'f')
                                                                        Mrs.
                                                                    @else
                                                                        Mr.
                                                                    @endif
                                                                    {{ $invitation->name }}
                                                                    @if ($invitation->chinese_name)
                                                                        {{ " (".$invitation->chinese_name.")" }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{ $invitation->passport_no }}
                                                                </td>
                                                                <td>
                                                                    {{ $invitation->phone }}
                                                                </td>
                                                                <td>
                                                                    {{ $invitation->country }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-md-12">
                                                {{-- PAGINATION --}}
                                                <div class="pagination">
                                                    <div class="pagination-panel">
                                                        {{ $invitations->links() }}
                                                    </div>
                                                    <div class="pagination-desk">
                                                        {{ $invitations->total() }} <span>@lang('messages.Invitations')</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- WEDDING ACCOMMODATION --}}
                                    <div class="col-md-12">
                                        <div class="page-subtitle">
                                            @lang("messages.Accommodations")
                                            <span>
                                                <a href="/wedding-accommodation-update-{{ $wedding_planner->id }}">
                                                    <i class="icon-copy fa fa-pencil" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="@lang('messages.Update Wedding Accomodation')"></i>
                                                </a>
                                            </span>
                                            {{-- MODAL UPDATE WEDDING  --}}
                                            <div class="modal fade" id="update-wedding-date-{{ $wedding_planner->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Update Wedding')</div>
                                                            </div>
                                                            <form id="updateWeddingDate" action="/fupdate-wedding-date/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('put')
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="wedding_date">@lang("messages.Wedding Date")</label>
                                                                            <input type="text" readonly name="wedding_date" class="form-control date-picker @error('wedding_date') is-invalid @enderror"  placeholder="@lang("messages.Insert Wedding Date")" value="{{ $wedding_planner->wedding_date }}" required>
                                                                            @error('wedding_date')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="slot">Slot <span> *</span></label>
                                                                            <select name="slot" class="custom-select @error('slot') is-invalid @enderror" required>
                                                                                @if ($wedding_planner->slot)
                                                                                    <option selected value="{{ $wedding_planner->slot }}">{{ date('H.i',strtotime($wedding_planner->slot)) }}</option>
                                                                                @else
                                                                                    <option selected value="">@lang('messages.Select slot')</option>
                                                                                @endif
                                                                                @foreach ($slots as $slot)
                                                                                    <option value="{{ $slot }}">{{ date('H.i A',strtotime($slot)) }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('slot')
                                                                                <div class="alert-form">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="number_of_invitations">@lang("messages.Number of Invitations")</label>
                                                                            <input type="number" min="0" name="number_of_invitations" class="form-control @error('number_of_invitations') is-invalid @enderror"  placeholder="@lang("messages.Number of Invitations")" value="{{ $wedding_planner->number_of_invitations }}" required>
                                                                            @error('number_of_invitations')
                                                                                <span class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="updateWeddingDate" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $acc_no = 1;
                                        @endphp
                                        <table class="data-table table nowrap" >
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%;">@lang('messages.No')</th>
                                                    <th style="width: 30%;">@lang('messages.Hotel')</th>
                                                    <th style="width: 25%;">@lang('messages.Room')</th>
                                                    <th style="width: 25%;">@lang('messages.Guests')</th>
                                                    <th style="width: 15%;">@lang('messages.Status')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($accomodation_couple)
                                                    <tr>
                                                        <td>
                                                            {{ $acc_no }}
                                                        </td>
                                                        <td>
                                                            {{ $hotel->name }}
                                                        </td>
                                                        <td>
                                                            {{ $room->rooms }}
                                                        </td>
                                                        <td>
                                                            {{ "Mr. ".$wedding_planner->groom_name }}
                                                            @if ($wedding_planner->groom_chinese)
                                                                {{ "(".$wedding_planner->groom_chinese.")" }}
                                                            @endif
                                                            ,
                                                            {{ "Mrs. ".$wedding_planner->bride_name }}
                                                            @if ($wedding_planner->bride_chinese)
                                                                {{ "(".$wedding_planner->bride_chinese.")" }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @lang('messages.Include')
                                                        </td>
                                                    </tr>
                                                @endif
                                                @foreach ($accomodation_invs as $accomodation_inv)
                                                    @php
                                                        $room_inv = $rooms->where('id',$accomodation_inv->rooms_id)->first();
                                                        $hotel_inv = $hotels->where('id',$room_inv->hotels_id)->first();
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <div class="table-service-name">{{ ++$acc_no }}</div>
                                                        </td>
                                                        <td>
                                                            @if ($accomodation_inv->room_for == "Inv")
                                                                <div class="table-service-name">@lang('messages.Invitations')</div>
                                                            @else
                                                                {{ $accomodation_inv->room_for->room_for }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="table-service-name">{{ $hotel_inv->name }}</div>
                                                        </td>
                                                        <td>
                                                            <div class="table-service-name">{{ $room_inv->rooms }}</div>
                                                        </td>
                                                        <td>
                                                            <div class="table-service-name">{{ $accomodation_inv->amount }}</div>
                                                        </td>
                                                        <td>
                                                            <div class="table-service-name">{{ $accomodation_inv->status }}</div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        {{-- <div class="row">
                                            <div class="col-md-12">
                                            <b><p>Couple</p></b>
                                            <hr class="form-hr">
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table tb-list" >
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
                                                            @lang('messages.Suites and Villas')
                                                        </td>
                                                        <td class="htd-2">
                                                            -
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table tb-list" >
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Check-in')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ date('Y-m-d (H.i)',strtotime($wedding_planner->checkin)) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Check-out')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ date('Y-m-d (H.i)',strtotime($wedding_planner->checkout)) }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        
                                            <hr class="form-hr">
                                            <div class="col-md-12">
                                                <b><p>Invitations</p></b>
                                                <hr class="form-hr">
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table tb-list" >
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
                                                            @lang('messages.Suites and Villas')
                                                        </td>
                                                        <td class="htd-2">
                                                            -
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table tb-list" >
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Check-in')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ date('Y-m-d (H.i)',strtotime($wedding_planner->checkin)) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Check-out')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ date('Y-m-d (H.i)',strtotime($wedding_planner->checkout)) }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div> --}}
                                    </div>
                                    {{-- WEDDING DINNER RECEPTION  --}}
                                    <div class="col-md-12">
                                        <div class="page-subtitle">@lang('messages.Dinner Reception Package')</div>
                                        @if ($wedding_reception)
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <table class="table tb-list" >
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Package')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ $wedding_reception->name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Dinner Venue')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ $dinner_venue->name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Capacity')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ $dinner_venue->capacity }} @lang('messages.guests')
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                
                                                <div class="col-md-12">
                                                    <div class="page-text">
                                                        <hr class="form-hr">
                                                        <div class="card-subtitle">
                                                            <b>
                                                                @lang('messages.Include'):
                                                            </b>
                                                        </div>
                                                        {!! $wedding_reception->include !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="page-text">
                                                        <hr class="form-hr">
                                                        <div class="card-subtitle">
                                                            <b>
                                                                @lang('messages.Additional Information'):
                                                            </b>
                                                        </div>
                                                        {!! $dinner_venue->additional_info !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    
                                </div>
                            
                            
                        
                        </div>
                        
                    </div>
                    
                    
                </div>
            </div>
           
            @include('layouts.footer')
        </div>
    </div>
@endsection
