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
                                <i class="icon-copy fi-page-edit"></i>{{ $bride->groom }}{{ $bride->groom_chinese ? "(".$bride->groom_chinese.")" : "" }} & {{ $bride->bride }}{{ $bride->bride_chinese ? "(".$bride->bride_chinese.")" : ""}}
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="wedding-planner">@lang('messages.Wedding Planner')</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ 'Mr.'.$bride->groom." & Ms.".$bride->bride }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                {{-- ALERT --}}
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
                <div class="row">
                    {{-- MOBILE --}}
                    <div class="col-md-4 mobile">
                        <div class="row">
                            @include('layouts.attentions')
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-box m-b-18">
                            <div class="card-box-title m-b-18">
                                <div class="title">
                                    @lang('messages.Wedding Plans')
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-6">
                                    <div class="order-bil text-left">
                                        <img src="/storage/logo/logo-color-bali-kami.png" alt="Bali Kami Tour & Travel">
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
                                                @lang('messages.Wedding Planner')
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="htd-1">@lang('messages.Wedding Venue') </td>
                                            <td class="htd-2">
                                                <a href="#" data-toggle="modal" data-target="#detail-wedding-venue-{{ $hotel->id }}">
                                                    {{ $hotel->name }}
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                    {{-- MODAL DETAIL Hotel --}}
                                    <div class="modal fade" id="detail-wedding-venue-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="subtitle"><i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>@lang('messages.Wedding Venue')</div>
                                                    </div>
                                                    <div class="card-banner">
                                                        <img class="rounded" src="{{ asset ('storage/hotels/hotels-cover/' . $hotel->cover) }}" alt="{{ $hotel->name }}" loading="lazy">
                                                    </div>
                                                    <div class="card-content">
                                                        <div class="card-text">
                                                            <div class="row ">
                                                                <div class="col-sm-12 text-center">
                                                                    <div class="card-subtitle">{{ $hotel->name }}</div>
                                                                    <p>{{ date('Y-m-d',strtotime($wedding_planner->checkin)) }} | {{ date('Y-m-d',strtotime($wedding_planner->checkout)) }}</p>
                                                                    <p>
                                                                        <a href="{{ $hotel->map }}" target="_blank" rel="noopener noreferrer">
                                                                            {!! $hotel->address !!}
                                                                        </a>
                                                                    </p>
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
                                    <td colspan="4" class="tb-bordered text-center f-w-700">{{ date('l,F d,Y',strtotime($wedding_planner->wedding_date))." (".date('H.i',strtotime($wedding_planner->slot))." - ".date('H.i',strtotime('+2 hours',strtotime($wedding_planner->slot))).")" }}</td>
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
                                @include('layouts.wedding-planner.Bride')
                                @include('layouts.wedding-planner.wedding')
                                @include('layouts.wedding-planner.bride_flight')
                                @include('layouts.wedding-planner.invitation_flight')
                                @include('layouts.wedding-planner.invitations')
                                @include('layouts.wedding-planner.service')
                                @include('layouts.wedding-planner.transports')
                                @include('layouts.wedding-planner.accommodations')
                            </div>
                            @if (date('Y-m-d',strtotime($wedding_planner->checkin)) > date('Y-m-d',strtotime($wedding_planner->wedding_date)) or date('Y-m-d',strtotime($wedding_planner->checkout)) < date('Y-m-d',strtotime($wedding_planner->wedding_date)) or date('Y-m-d',strtotime($wedding_planner->checkin)) >= date('Y-m-d',strtotime($wedding_planner->checkout)) or !$wedding_planner->ceremonial_venue_id)
                                <div class="card-box-notification">
                                    @if (!$wedding_planner->ceremonial_venue_id)
                                        <p><b>"@lang('messages.Ceremony Venue')"</b> -> @lang('messages.Please be reminded that you have not yet chosen a venue for the wedding ceremony')</p>
                                    @endif
                                    @if (date('Y-m-d',strtotime($wedding_planner->checkin)) >= date('Y-m-d',strtotime($wedding_planner->checkout)))
                                        <p><b>"@lang('messages.Check-in')"</b> -> @lang('messages.Check-in date or Check-out date is invalid')</p>
                                    @endif
                                    @if (date('Y-m-d',strtotime($wedding_planner->checkin)) > date('Y-m-d',strtotime($wedding_planner->wedding_date)) or date('Y-m-d',strtotime($wedding_planner->checkout)) < date('Y-m-d',strtotime($wedding_planner->wedding_date)))
                                        <p><b>"@lang('messages.Wedding Date')"</b> -> @lang('messages.Wedding date is invalid')</p>
                                    @endif
                                </div>
                            @endif
                            @if ($wedding_planner->status == "Draft")
                                <div class="card-box-footer">
                                    @if (date('Y-m-d',strtotime($wedding_planner->checkin)) < date('Y-m-d',strtotime($wedding_planner->wedding_date)) and date('Y-m-d',strtotime($wedding_planner->checkout)) > date('Y-m-d',strtotime($wedding_planner->wedding_date)))
                                        @if ($wedding_planner->ceremonial_venue_id)
                                            <form action="/fsubmit-wedding-planner/{{ $wedding_planner->id }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                @method('put')
                                                <button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-send" aria-hidden="true"></i> @lang('messages.Submit')</button>
                                            </form>
                                        @else
                                            <button disabled class="btn btn-primary"><i class="icon-copy fa fa-send" aria-hidden="true"></i> @lang('messages.Submit')</button>
                                        @endif
                                    @else
                                        <button disabled class="btn btn-primary"><i class="icon-copy fa fa-send" aria-hidden="true"></i> @lang('messages.Submit')</button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- DESKTOP --}}
                    <div class="col-md-4 desktop">
                        <div class="row">
                            @include('layouts.attentions')
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var roomFor = document.getElementById('roomForForm');
        var guestsName = document.getElementById('guestsNameForm');
        var numberOfGuests = document.getElementById('numberOfGuestsForm');
        var extraBed = document.getElementById('extraBedIdForm');
        if (roomFor.value == "Couple") {
            roomFor.addEventListener('change', function() {
                if (this.value === 'Couple') {
                    guestsName.setAttribute('hidden', 'hidden');
                    numberOfGuests.setAttribute('hidden', 'hidden');
                    extraBed.setAttribute('hidden', 'hidden');
                } else {
                    guestsName.removeAttribute('hidden');
                    numberOfGuests.removeAttribute('hidden');
                    extraBed.removeAttribute('hidden');
                }
                if (typeSelect.value === 'Couple') {
                    guestNameField.setAttribute('hidden', 'hidden');
                    numberOfGuestsField.setAttribute('hidden', 'hidden');
                    extraBed.setAttribute('hidden', 'hidden');
                }
            });
        }else{
            guestsName.removeAttribute('hidden');
            numberOfGuests.removeAttribute('hidden');
            extraBed.removeAttribute('hidden');
        }

        $(document).ready(function() {
            var ro = 1;
            var limit = 5;
            var t = 1
            $(".add-more").click(function(){ 
                if (t < limit) {
                    t++;
                    ro++;
                    var html = $(".copy").html();
                    $(".after-add-more").before(html);
                }
            });
            $("body").on("click",".remove",function(){ 
                $(this).parents(".control-group").remove();
                t--;
            });
        });
        $(document).ready(function() {
            var inv = 1;
            var inv_limit = 10;
            var u = 1
            $(".add-more-invitation").click(function(){ 
                if (u < inv_limit) {
                    u++;
                    inv++;
                    var html = $(".copy-invitation").html();
                    $(".after-add-more-invitaton").before(html);
                }
            });
            $("body").on("click",".remove",function(){ 
                $(this).parents(".control-group-invitation").remove();
                u--;
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            var venueRadios = document.querySelectorAll('input[name="ceremonial_venue_id"]');
            var slotSelect = document.getElementById('slot');
    
            venueRadios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    var selectedVenue = JSON.parse(this.getAttribute('data-slots'));
                    populateSlots(selectedVenue);
                });
            });
    
            function populateSlots(slots) {
                slotSelect.innerHTML = '';
                slots.forEach(function(slot) {
                    var option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    slotSelect.appendChild(option);
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const roomRadios = document.querySelectorAll('input[name="transport_id"]');
            const roomRadiosEdit = document.querySelectorAll('input[name="edit_transport_id"]');
            const numberOfGuestsInput = document.getElementById('nogTransport');
            const numberOfGuestsInputEdit = document.getElementById('nogTransportEdit');

            roomRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    const selectedCapacity = this.getAttribute('data-capacity');
                    const selectedCapacityEdit = this.getAttribute('data-capacity-edit');
                    numberOfGuestsInput.max = selectedCapacity;
                    numberOfGuestsInputEdit.max = selectedCapacityEdit;
                    if (parseInt(numberOfGuestsInput.value) > parseInt(selectedCapacity)) {
                        numberOfGuestsInput.value = selectedCapacity;
                    }
                    if (parseInt(numberOfGuestsInputEdit.value) > parseInt(selectedCapacityEdit)) {
                        numberOfGuestsInputEdit.value = selectedCapacityEdit;
                    }
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            var roomInputs = document.querySelectorAll('input[name="rooms_id"]');
            
            roomInputs.forEach(function (input) {
                input.addEventListener('change', function () {
                    var capacity = this.getAttribute('data-room-capacity');
                    var guestsForm = document.getElementById('numberOfGuestsForm');
                    var numberOfGuestsInput = document.getElementById('number_of_guests_room');
                    
                    numberOfGuestsInput.setAttribute('max', capacity);
                    guestsForm.removeAttribute('hidden');
                });
            });
        });
    </script>
@endsection