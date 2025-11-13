@php
    use Carbon\Carbon;
@endphp
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
                            <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i>@lang('messages.Edit Order')</div>
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
                                            {{ $orderWedding->service }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            @if ($orderWedding->service == "Wedding Package")
                                                @lang('messages.Wedding Package')
                                            @elseif($orderWedding->service == "Ceremony Venue")
                                                @lang('messages.Ceremony Venue')
                                            @elseif($orderWedding->service == "Reception Venue")
                                                @lang('messages.Reception Venue')
                                            @endif
                                        </td>
                                        <td class="htd-2">
                                            @if ($orderWedding->service == "Wedding Package")
                                                {{ $orderWedding->wedding->name }}
                                            @elseif($orderWedding->service == "Ceremony Venue")
                                                {{ $orderWedding->ceremony_venue->name }}
                                            @elseif($orderWedding->service == "Reception Venue")
                                                {{ $orderWedding->reception_venue->name }}
                                            @endif
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
                            @include('order-wedding-package.bride')
                            @include('order-wedding-package.flight')
                            @include('order-wedding-package.invitations')
                            @include('order-wedding-package.wedding-detail')
                            @include('order-wedding-package.wedding-venue')
                            {{-- @include('order-wedding-package.suite-and-villa-brides')
                            @include('order-wedding-package.suite-and-villa-invitations') --}}
                            @if ($orderWedding->service == "Ceremony Venue" || $orderWedding->service == "Wedding Package")
                                @include('order-wedding-package.ceremony-and-decoration-venue')
                            @endif
                            @if ($orderWedding->service == "Reception Venue" || $orderWedding->service == "Wedding Package")
                                @include('order-wedding-package.reception-and-decoration-venue')
                            @endif
                            {{-- @include('order-wedding-package.wedding-lunch-venue')
                            @include('order-wedding-package.wedding-dinner-venue') --}}
                            
                            @include('order-wedding-package.include-services')
                            @include('order-wedding-package.accommodation')
                            @include('order-wedding-package.transports')
                            @if ($orderWedding->service == "Wedding Package")
                                @include('order-wedding-package.additional-services')
                            @endif
                            {{-- REMARK --}}
                            <div id="remarkPage" class="col-md-12">
                                <div class="page-subtitle">
                                    @lang('messages.Remark')
                                    @if ($orderWedding->remark)
                                        <span>
                                            <form id="deleteOrderWeddingRemark" action="/fdelete-order-wedding-remark/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                @method('put')
                                                <button class="icon-btn-remove" form="deleteOrderWeddingRemark" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash"></i></button>
                                            </form>
                                        </span>
                                        <span>
                                            <a href="#" data-toggle="modal" data-target="#add-order-wedding-remark-{{ $orderWedding->id }}">
                                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                            </a>
                                        </span>
                                    @else
                                        <span>
                                            <a href="#" data-toggle="modal" data-target="#add-order-wedding-remark-{{ $orderWedding->id }}"> 
                                                <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                            </a>
                                        </span>
                                    @endif
                                </div>
                                @if ($orderWedding->remark)
                                    <div class="box-price-kicked">
                                        {!! $orderWedding->remark !!}
                                    </div>
                                @endif
                                {{-- MODAL ADD REMARK  --}}
                                <div class="modal fade" id="add-order-wedding-remark-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content text-left">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    @if ($orderWedding->remark)
                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Remark')</div>
                                                    @else
                                                        <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Remark')</div>
                                                    @endif
                                                </div>
                                                <form id="addOrderWeddingRemark" action="/fadd-order-wedding-remark/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <textarea name="remark" id="remark" placeholder="@lang('messages.Optional')" class="textarea_editor form-control border-radius-0 @error('remark') is-invalid @enderror">{!! $orderWedding->remark !!}</textarea>
                                                                @error('remark')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div class="card-box-footer">
                                                    @if ($orderWedding->remark)
                                                        <button type="submit" form="addOrderWeddingRemark" class="btn btn-primary"><i class="icon-copy fa fa-pencil"></i> @lang('messages.Change')</button>
                                                    @else
                                                        <button type="submit" form="addOrderWeddingRemark" class="btn btn-primary"><i class="icon-copy fa fa-plus"></i> @lang('messages.Create')</button>
                                                    @endif
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close"></i> @lang('messages.Cancel')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- PRICES --}}
                            @if ($orderWedding->service == "Wedding Package")
                                <div class="col-md-12">
                                    <div class="page-subtitle">
                                        @lang('messages.Prices')
                                    </div>
                                    <div class="box-price-kicked">
                                        <div class="row">
                                            @if (count($wedding_accommodations)>0)
                                                <div class="col-6">
                                                    <div class="normal-text">@lang('messages.Accommodations') + @lang('messages.Extra Bed')</div>
                                                </div>
                                                <div class="col-6 text-right">
                                                    @if ($accommodation_containt_zero)
                                                        <div class="normal-text" data-toggle="tooltip" data-placement="left" title="@lang('messages.To be advised')">TBA</div>
                                                    @else
                                                        <div class="normal-text">{{ '$ ' .number_format($wedding_accommodation_price+$extra_bed_orders_price, 0, ',', '.') }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (count($transport_orders)>0)
                                                <div class="col-6">
                                                    <div class="normal-text">@lang('messages.Transports')</div>
                                                </div>
                                                <div class="col-6 text-right">
                                                    @if ($transportContainsNullPrice)
                                                        <div class="normal-text" data-toggle="tooltip" data-placement="left" title="@lang('messages.To be advised')">TBA</div>
                                                    @else
                                                        <div class="normal-text">{{ '$ ' .number_format($transport_orders_prices, 0, ',', '.') }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (count($additionalServices)>0)
                                                <div class="col-6">
                                                    <div class="normal-text">@lang('messages.Additional Charge') / @lang('messages.Services') (@lang('messages.Request'))</div>
                                                </div>
                                                <div class="col-6 text-right">
                                                    @if ($additionalServicesPricesTba)
                                                        <div class="normal-text" data-toggle="tooltip" data-placement="left" title="@lang('messages.To be advised')">TBA</div>
                                                    @else
                                                        <div class="normal-text">{{ '$ ' .number_format($additionalServicesPrices, 0, ',', '.') }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (count($wedding_accommodations)>0 or count($transport_orders)>0 or count($additionalServices)>0)
                                                <div class="col-6">
                                                    <div class="normal-text">@lang('messages.Wedding Package')</div>
                                                </div>
                                                <div class="col-6 text-right">
                                                    <div class="normal-text">{{ '$ ' .number_format($orderWedding->package_price, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="col-12">
                                                    <hr class="form-hr">
                                                </div>
                                            @endif
                                            <div class="col-6">
                                                <div class="price-name">@lang('messages.Total Price')</div>
                                            </div>
                                            <div class="col-6 text-right">
                                                @if ($accommodation_containt_zero or $transportContainsNullPrice or $additionalServicesPricesTba)
                                                    <div class="usd-rate">TBA</div>
                                                @else
                                                    @if ($orderWedding->final_price > 0)
                                                        <div class="usd-rate">{{ '$ ' .number_format($orderWedding->final_price, 0, ',', '.') }}</div>
                                                    @endif
                                                @endif
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-12">
                                    <div class="page-subtitle">
                                        @lang('messages.Price')
                                    </div>
                                    <div class="box-price-kicked">
                                        <div class="row">
                                            <div class="col-6">
                                                @if ($orderWedding->ceremony_venue_price > 0)
                                                    <div class="normal-text">@lang('messages.Ceremony Venue')</div>
                                                @endif
                                                @if ($orderWedding->ceremony_venue_decoration_price > 0)
                                                    <div class="normal-text">@lang('messages.Ceremony Venue Decoration')</div>
                                                @endif
                                                @if ($orderWedding->reception_venue_price > 0)
                                                    <div class="normal-text">@lang('messages.Reception Venue')</div>
                                                @endif
                                                @if ($orderWedding->reception_venue_decoration_price > 0)
                                                    <div class="normal-text">@lang('messages.Reception Venue Decoration')</div>
                                                @endif
                                                @if ($orderWedding->accommodation_price > 0)
                                                    <div class="normal-text">@lang('messages.Accommodation')</div>
                                                @endif
                                                @if ($orderWedding->transport_invitations_price>0)
                                                    <div class="normal-text">@lang('messages.Transports')</div>
                                                @endif
                                                @if ($orderWedding->additional_services_price)
                                                    <div class="normal-text">@lang('messages.Additional Service')</div>
                                                @endif
                                                @if ($orderWedding->addser_price > 0)
                                                    <div class="normal-text">@lang('messages.Additional Service')</div>
                                                @endif
                                                <hr class="form-hr">
                                                <div class="price-name">@lang('messages.Total Price')</div>

                                            </div>
                                            <div class="col-6 text-right">
                                                @if ($orderWedding->ceremony_venue_price > 0)
                                                    <div class="normal-text">{{ '$ ' .number_format($orderWedding->ceremony_venue_price, 0, ',', '.') }}</div>
                                                @endif
                                                @if ($orderWedding->ceremony_venue_decoration_price > 0)
                                                    <div class="normal-text">{{ '$ ' .number_format($orderWedding->ceremony_venue_decoration_price, 0, ',', '.') }}</div>
                                                @endif
                                                @if ($orderWedding->reception_venue_price > 0)
                                                    <div class="normal-text">{{ '$ ' .number_format($orderWedding->reception_venue_price, 0, ',', '.') }}</div>
                                                @endif
                                                @if ($orderWedding->reception_venue_decoration_price > 0)
                                                    <div class="normal-text">{{ '$ ' .number_format($orderWedding->reception_venue_decoration_price, 0, ',', '.') }}</div>
                                                @endif
                                                @if ($orderWedding->accommodation_price > 0)
                                                    <div class="normal-text">{{ '$ ' .number_format($orderWedding->accommodation_price, 0, ',', '.') }}</div>
                                                @endif
                                                @if ($orderWedding->transport_invitations_price>0)
                                                <div class="normal-text">{{ '$ ' .number_format($orderWedding->transport_invitations_price, 0, ',', '.') }}</div>
                                                @endif
                                                @if ($orderWedding->additional_services_price > 0)
                                                <div class="normal-text">{{ '$ ' .number_format($orderWedding->additional_services_price, 0, ',', '.') }}</div>
                                                @endif
                                                @if ($orderWedding->addser_price > 0)
                                                <div class="normal-text">{{ '$ ' .number_format($orderWedding->addser_price, 0, ',', '.') }}</div>
                                                @endif
                                                <hr class="form-hr">
                                                @if ($transportContainsNullPrice or $accommodation_containt_zero)
                                                    <div class="usd-rate" data-toggle="tooltip" data-placement="left" title="@lang('messages.To be advised')">TBA</div>
                                                @else
                                                    <div class="usd-rate">{{ '$ ' .number_format($orderWedding->final_price, 0, ',', '.') }}</div>
                                                @endif
                                            </div>
                                        </div>
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
                            @if ($orderWedding->service == "Ceremony Venue" and $ceremonyVenue->capacity < $orderWedding->ceremony_venue_invitations)
                                <button disabled type="submit" form="submitOrderWedding" class="btn btn-primary"><i class="icon-copy fa fa-send" aria-hidden="true"></i> Submit</button>
                            @elseif ($orderWedding->service == "Reception Venue" and $receptionPackage->capacity < $orderWedding->reception_venue_invitations)
                                <button disabled type="submit" form="submitOrderWedding" class="btn btn-primary"><i class="icon-copy fa fa-send" aria-hidden="true"></i> Submits</button>
                            @else
                                @if ($orderWedding->final_price > 0)
                                    @if ($bride->groom_pasport_id)
                                        @if ($orderWedding->status == "Draft")
                                            <button type="submit" form="submitOrderWedding" class="btn btn-primary"><i class="icon-copy fa fa-send" aria-hidden="true"></i> Submit</button>
                                        @else
                                            <button disabled type="submit" form="submitOrderWedding" class="btn btn-primary"><i class="icon-copy fa fa-send" aria-hidden="true"></i> Submit</button>
                                        @endif
                                    @else
                                        <button disabled type="submit" form="submitOrderWedding" class="btn btn-primary"><i class="icon-copy fa fa-send" aria-hidden="true"></i> Submit</button>
                                    @endif
                                @else
                                    <button disabled type="submit" form="submitOrderWedding" class="btn btn-primary"><i class="icon-copy fa fa-send" aria-hidden="true"></i> Submit</button>
                                @endif
                            @endif
                            <a href="/orders">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- LOADING SPINNER -----------------------------------------------------------> --}}
    <div id="loading" style="display: none;">
        <div class="spinner"></div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("submitOrderWedding");
            const loading = document.getElementById("loading");
    
            form.addEventListener("submit", function(event) {
                loading.style.display = "block";
                loading.style.opacity = "1";
                document.body.style.overflow = "hidden";
            });
        });
    </script>
    {{-- LOADING SPINNER -----------------------------------------------------------> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var today = new Date();
            today.setHours(0, 0, 0, 0); // Reset time to midnight for accurate comparison
            
            flatpickr("#wedding_date", {
                minDate: today,
                dateFormat: "m/d/Y",
                onChange: function(selectedDates, dateStr, instance) {
                    var selectedDate = new Date(selectedDates[0]);
                    
                    // Mengubah class untuk elemen dengan ID subtitle1
                    var subtitleWeddingDate = document.getElementById('subtitle-wedding-date');
                    if (selectedDate < today) {
                        subtitleWeddingDate.classList.add('page-subtitle-error');
                    } else {
                        subtitleWeddingDate.classList.remove('page-subtitle-error');
                    }
                }
            });
        });
    </script>
    <script>
        var roomFor = document.getElementById('roomForForm');
        var guestsName = document.getElementById('guestsNameForm');
        var numberOfGuests = document.getElementById('numberOfGuestsForm');
        var extraBed = document.getElementById('extraBedIdForm');
         
        
        
        if (roomFor.value == 'Couple') {
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
        } else {
            guestsName.removeAttribute('hidden');
            numberOfGuests.removeAttribute('hidden');
            extraBed.removeAttribute('hidden');
        }
        document.addEventListener("DOMContentLoaded", function() {
            var venueRadios = document.querySelectorAll('input[name="ceremonial_venue_id"]');
            var slotSelect = document.getElementById('slot');
    
            venueRadios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    var selectedVenue = JSON.parse(this.getAttribute('data-slots'));
                    var basicPrices = JSON.parse(this.getAttribute('data-basic-prices'));
                    var arrangementPrices = JSON.parse(this.getAttribute('data-arrangement-prices'));
                    populateSlots(selectedVenue, basicPrices, arrangementPrices);
                });
            });
    
            function populateSlots(slots, basicPrices, arrangementPrices) {
                slotSelect.innerHTML = '';
                slots.forEach(function(slot, index) {
                    var option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    option.setAttribute('data-basic-price',basicPrices[index]);
                    option.setAttribute('data-arrangement-price',arrangementPrices[index]);
                    slotSelect.appendChild(option);
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
            var slotSelect = document.getElementById('slot');
            var basicPriceInput = document.getElementById('basic_price');
            var arrangementPriceInput = document.getElementById('arrangement_price');
    
            slotSelect.addEventListener('change', function () {
                var selectedOption = slotSelect.options[slotSelect.selectedIndex];
                var basicPrice = selectedOption.getAttribute('data-basic-price');
                var arrangementPrice = selectedOption.getAttribute('data-arrangement-price');
    
                basicPriceInput.value = basicPrice ? basicPrice : '';
                arrangementPriceInput.value = arrangementPrice ? arrangementPrice : '';
            });
            var event = new Event('change');
            slotSelect.dispatchEvent(event);
        });
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.checkbox-input').forEach(function(checkbox) {
                toggleCheck(checkbox);
            });
        });

        function toggleCheck(checkbox) {
            if (checkbox.checked) {
                checkbox.closest('.card-checkbox').classList.add('checked');
                checkbox.setAttribute('checked', 'checked');
            } else {
                checkbox.closest('.card-checkbox').classList.remove('checked');
                checkbox.removeAttribute('checked');
            }
        }

        function toggleCard(card) {
            const checkbox = card.querySelector('.checkbox-input');
            checkbox.checked = !checkbox.checked;
            toggleCheck(checkbox);
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
    </script>
    <script>
        function previewImage(event, index) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var dataURL = reader.result;
                var imgElement = document.getElementById('preview-img-' + index);
                var overlayText = document.getElementById('overlay-text-' + index);
                
                // Hide overlay text
                if (overlayText) {
                    overlayText.style.display = 'none';
                }
                
                // Remove default-img class
                imgElement.classList.remove('default-img');
                
                // Set new image source
                imgElement.src = dataURL;
            };
            reader.readAsDataURL(input.files[0]);
        }
    </script>
@endsection