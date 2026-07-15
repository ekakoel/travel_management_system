<div class="col-md-8">
    <div class="card-box">
        <div class="card-box-title">
            <div class="subtitle"><i class="fa fa-pencil"></i> @lang('messages.Edit Order')</div>
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
        <form id="checkoutOrder" action="{{ route('func.checkout-order-villa',$order->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <table class="table tb-list nowrap">
                        <tbody>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Order No')
                                </td>
                                <td class="htd-2">
                                    {{ $order->orderno }}
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Order Date')
                                </td>
                                <td class="htd-2">
                                    {{ dateTimeFormat($now) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Service')
                                </td>
                                <td class="htd-2">
                                    @lang('messages.Private Villa')
                                </td>
                            </tr>
                            <tr>
                                <td class="htd-1">
                                    @lang('messages.Region')
                                </td>
                                <td class="htd-2">
                                    {{ $villa->region }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="status-order-container">
                        <div class="status-order-lable">
                            @lang('messages.Status'):
                        </div>
                        <div class="status-order">
                            @if (isset($statusMap[$order->status]))
                                <div class="{{ $statusMap[$order->status]['class'] }}">
                                    {!! $statusMap[$order->status]['label'] !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @canany(['posDev','posAuthor','posRsv'])
                    @include('partials.admin-create-order', compact('agents'))
                @endcanany
            </div>
            <div class="page-subtitle">@lang('messages.Order Details')</div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table tb-list nowrap">
                            <tbody>
                                <tr>
                                    <td class="htd-1">
                                        @lang('messages.Villa')
                                    </td>
                                    <td class="htd-2">
                                        {{ $villa->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="htd-1">
                                        @lang('messages.Number of Room')
                                    </td>
                                    <td class="htd-2">
                                        {{ count($rooms)>1? count($rooms)." ".__('messages.rooms') : count($rooms)." ".__('messages.room') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="htd-1">
                                        @lang('messages.Occupancy')
                                    </td>
                                    <td class="htd-2">
                                        {{ $number_of_adult }} {{ $number_of_adult>1 ? __('messages.adults') : __('messages.adult') }} + {{ $number_of_children }} {{ $number_of_children>1 ? __('messages.children') : __('messages.child') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <table class="table tb-list nowrap">
                            <tbody>
                                <tr>
                                    <td class="htd-1">
                                        @lang('messages.Duration')
                                    </td>
                                    <td class="htd-2">
                                        {{ $order->duration > 1?$order->duration." ".__('messages.nights'):$order->duration." ".__('messages.night') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="htd-1">
                                        @lang('messages.Check In')
                                    </td>
                                    <td class="htd-2">
                                        {{ date('d F Y',strtotime($order->checkin)) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="htd-1">
                                        @lang('messages.Check Out')
                                    </td>
                                    <td class="htd-2">
                                        {{ date('d F Y',strtotime($order->checkout)) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="page-subtitle">@lang('messages.Guest Details')</div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="guest-wrapper">
                            <div class="form-section guest-group">
                                @foreach ($guests as $no_guest=>$guest)
                                    <input type="hidden" name="guests[{{ $no_guest }}][id]" value="{{ $guest->id }}">
                                    <div class="row g-3" data-id="{{ $guest->id }}">
                                        <div class="col-md-5">
                                            <label for="name_{{ $no_guest }}" class="form-label">@lang('messages.Name')</label>
                                            <input type="text" name="guests[{{ $no_guest }}][name]" class="form-control" id="name_{{ $no_guest }}" placeholder="{{ __('messages.Guest name') }}" value="{{ $guest->name }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="sex_{{ $no_guest }}" class="form-label">@lang('messages.Sex')</label>
                                            <select name="guests[{{ $no_guest }}][sex]" class="custom-select" id="sex_{{ $no_guest }}">
                                                <option value="">@lang('messages.Select')...</option>
                                                <option {{ $guest->sex == "m"?"selected":""; }} value="m">@lang('messages.Male')</option>
                                                <option {{ $guest->sex == "f"?"selected":""; }} value="f">@lang('messages.Female')</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="age_{{ $no_guest }}" class="form-label">
                                                @lang('messages.Adult') / @lang('messages.Child') <span> *</span>
                                            </label>
                                            <select name="guests[{{ $no_guest }}][age]" class="custom-select" id="age_{{ $no_guest }}">
                                                <option value="">@lang('messages.Select')...</option>
                                                <option {{ $guest->age == "Adult"?"selected":""; }} value="Adult">{{ __('messages.Adult') }} (12 @lang('messages.years and above'))</option>
                                                <option {{ $guest->age == "Child"?"selected":""; }} value="Child">{{ __('messages.Child') }} (@lang('messages.2-12 years old'))</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            @if (!$loop->first)
                                                <button type="button" class="btn btn-remove-input remove-btn" data-id="{{ $guest->id }}"><i class="icon-copy fa fa-close"></i></button>
                                            @endif
                                        </div>
                                    </div>
                                    @php
                                        $no_guest++;
                                    @endphp
                                @endforeach
                            </div>
                        </div>
                        <div class="text-right m-t-8">
                            <button type="button" class="btn btn-secondary" id="add-more"><i class="icon-copy fa fa-plus"></i> @lang('messages.Add More')</button>
                        </div>
                    </div>
                </div>

                <div class="page-subtitle">@lang('messages.Airport Shuttle')</div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="arrival_flight">@lang('messages.Arrival Flight')</label>
                            <input type="text" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror" placeholder="{{ $airport_shuttle_in ? __('messages.Insert flight number!'):__('messages.Flight Number') }}" value="{{ $airport_shuttle_in->flight_number??"" }}">
                            @error('arrival_flight')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="arrival_time">@lang('messages.Arrival Date and Time')</label>
                            <input readonly type="text" name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="@if ($airport_shuttle_in?->date){{ date('d F Y h:i a',strtotime($airport_shuttle_in->date)) }}@endif">
                            @error('arrival_time')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="airport_shuttle_in">@lang('messages.Airport Shuttle')</label>
                            <select name="airport_shuttle_in" id="transportIn" type="text" class="custom-select @error('airport_shuttle_in') is-invalid @enderror">
                                <option value="" data-transportout="0" data-transportoutprice=0>@lang('messages.Select Transport')</option>
                                @if ($transports)
                                    @foreach ($transports as $inTransport)
                                        @if ($inTransport->calculated_price)
                                            <option
                                               {{ $inTransport->id === $airport_shuttle_in?->transport_id ?"selected":""; }}
                                                value="{{ $inTransport->id }}"
                                                data-transportinprice="{{ $inTransport->calculated_price }}"
                                                data-transportinpriceid="{{ $inTransport->calculated_price_id }}"
                                            >
                                                {{ $inTransport->brand . " " . $inTransport->name . " - (" . $inTransport->capacity . ")" }}
                                            </option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="Request">@lang('messages.Request')</option>
                                @endif
                            </select>
                            @error('airport_shuttle_in')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="departure_flight">@lang('messages.Departure Flight')</label>
                            <input type="text" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror" placeholder="{{ $airport_shuttle_out ? __('messages.Insert flight number!'):__('messages.Flight Number') }}" value="{{ $airport_shuttle_out->flight_number??"" }}">
                            @error('departure_flight')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="departure_time"> @lang('messages.Departure Date and Time')</label>
                            <input readonly type="text" name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="@if ($airport_shuttle_out?->date){{ date('d F Y h:i a',strtotime($airport_shuttle_out->date)) }}@endif">
                            @error('departure_time')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="airport_shuttle_out">@lang('messages.Airport Shuttle')</label>
                            <select name="airport_shuttle_out" id="transportOut" type="text" class="custom-select @error('airport_shuttle_out') is-invalid @enderror">
                                <option value="" data-transportout="0" data-transportoutprice=0>@lang('messages.Select Transport')</option>
                                
                                @if ($transports)
                                    @foreach ($transports as $outTransport)
                                        @if ($outTransport->calculated_price)
                                            <option 
                                                {{ $outTransport->id === $airport_shuttle_out?->transport_id ?"selected":""; }}
                                                value="{{ $outTransport->id }}"
                                                data-transportoutprice="{{ $outTransport->calculated_price }}"
                                                data-transportoutpriceid="{{ $outTransport->calculated_price_id }}"
                                            >
                                                {{ $outTransport->brand . " " . $outTransport->name . " - (" . $outTransport->capacity . ")" }}
                                            </option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="Request">@lang('messages.Request')</option>
                                @endif
                                
                            </select>
                            @error('airport_shuttle_out')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="page-subtitle">@lang('messages.Remark')</div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <textarea id="note" name="note" placeholder="@lang('messages.Optional')" class="tiny_mce form-control border-radius-0">{{ $order->note }}</textarea>
                            @error('note')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="optional_service" class="page-subtitle">@lang('messages.Price')</div>
                    </div>
                    <div class="col-md-12 m-b-8">
                        <div class="box-price-kicked">
                            <div class="row">
                                <div class="col-6 col-md-6">
                                    <div id="airportShuttle" class="normal-text" style="display: none;">
                                        @lang('messages.Airport Shuttle')<br>
                                    </div>
                                    <div class="normal-text">
                                        @lang('messages.Private Villa')<br>
                                        <hr class="form-hr">
                                    </div>
                                    <div class="total-price">@lang('messages.Total Price')</div>
                                </div>
                                <div class="col-6 col-md-6 text-right">
                                    <div id="airportShuttlePrice" class="text-price" style="display: none;">
                                        <span id="airportShuttleText"></span>
                                    </div>
                                    <div class="text-price">
                                        <span>{{ currencyFormatUsd($order->normal_price) }}</span><br>
                                        <hr class="form-hr">
                                    </div>
                                    <div class="total-price">
                                        <span id="totalPrice" data-base-price="{{ $order->normal_price }}">
                                            {{ currencyFormatUsd($order->price_total) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 ">
                        <div class="notif-modal text-left">
                            @if (Auth::user()->email == "" or Auth::user()->phone == "" or Auth::user()->office == "" or Auth::user()->address == "" or Auth::user()->country == "")
                                @lang('messages.Please complete your profile data first to be able to submit orders, by clicking this link') -> <a href="/profile">@lang('messages.Edit Profile')</a>
                            @else
                                @if ($order->status == "Draft")
                                    @lang('messages.Please make sure all the data is correct before you submit the order!')
                                @elseif ($order->status == "Invalid")
                                    - @lang('messages.This order is invalid, please make sure all data is correct!')<br>
                                    - {!! $order->msg !!}
                                @elseif ($order->status == "Rejected")
                                    {{ $order->msg }}
                                @endif
                            @endif
                        
                        </div>
                    </div>
                </div>
                <input type="hidden" name="transport_out_price_id" id="transportOutPriceId" value="">
                <input type="hidden" name="transport_in_price_id" id="transportInPriceId" value="">
                <input type="hidden" name="airport_shuttle_price" id="inputAirportShuttlePrice" value="">
                <input type="hidden" name="final_price" id="inputFinalPrice" value="">
            </form>
            <div class="card-box-footer">
                @if (Auth::user()->email == "" or Auth::user()->phone == "" or Auth::user()->office == "" or Auth::user()->address == "" or Auth::user()->country == "")
                    <button type="button" class="btn btn-light"><i class="icon-copy fa fa-info" aria-hidden="true"> </i> @lang('messages.You cannot submit this order')</button>
                @else
                    @if ($order->status == "Draft")
                        <button type="submit" form="checkoutOrder" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Checkout')</button>
                        <button type="button" onclick="goBack()" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                    @elseif ($order->status == "Rejected")
                        <form id="removeOrder" class="hidden" action="/fremove-order/{{ $order->id }}"method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <input type="hidden" name="status" value="Removed">
                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        </form>
                        <button type="submit" form="removeOrder" class="btn btn-danger"><i class="icon-copy fa fa-trash-o" aria-hidden="true"></i> @lang('messages.Delete')</button>
                    @elseif ($order->status == "Invalid")
                        <button type="button" class="btn btn-light"><i class="icon-copy fa fa-info" aria-hidden="true"> </i> @lang('messages.You cannot submit this order')</button>
                    @else
                        <div class="form-group">
                            <a href="/orders">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>


@include('partials.loading-form', ['id' => 'checkoutOrder'])
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    function goBack() {
        window.history.back();
    }
</script>
<script>
        const maxGuests = {{ $occupancy }};
        const wrapper = document.getElementById('guest-wrapper');
        const addMoreBtn = document.getElementById('add-more');

        let index = {{ $no_guest }};

        addMoreBtn.addEventListener('click', function () {
            const guestCount = wrapper.querySelectorAll('.guest-group').length;
            if (guestCount >= maxGuests) {
                alert(`Maximum of ${maxGuests} guests allowed.`);
                return;
            }

            const section = document.createElement('div');
            section.className = 'form-section guest-group';
            section.setAttribute('data-index', index);

            section.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="name_${index}" class="form-label">Name</label>
                        <input type="text" name="guests[${index}][name]" class="form-control" id="name_${index}" placeholder="{{ __('messages.Guest name') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="sex_${index}" class="form-label">Sex</label>
                        <select name="guests[${index}][sex]" class="custom-select" id="sex_${index}" required>
                            <option selected value="">@lang('messages.Select')...</option>
                            <option value="m">@lang('messages.Male')</option>
                            <option value="f">@lang('messages.Female')</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="age_${index}" class="form-label">
                            @lang('messages.Adult') / @lang('messages.Child') <span> *</span>
                        </label>
                        <select name="guests[${index}][age]" class="custom-select" id="age_${index}">
                            <option selected value="">@lang('messages.Select')...</option>
                            <option value="Adult">{{ __('messages.Adult') }} (12 @lang('messages.years and above'))</option>
                            <option value="Child">{{ __('messages.Child') }} (@lang('messages.2-12 years old'))</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-remove-input remove-btn"><i class="icon-copy fa fa-close"></i></button>
                    </div>
                </div>
            `;

            // Tambahkan event listener hapus
            section.querySelector('.remove-btn').addEventListener('click', function () {
                section.remove();
            });

            wrapper.appendChild(section);
            index++;
        });
    </script>

    <script>
       $(document).on('click', '.remove-btn', function () {
            let button = $(this);
            let guestId = button.data('id');

            if (!guestId) {
                button.closest('.row.g-3').remove();
                return;
            }
            if (!confirm("{{ __('messages.Are you sure you want to delete this guest?') }}")) return;
            $.ajax({
                url: '/remove-guests/' + guestId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        button.closest('.row.g-3').remove();
                    } else {
                        alert("{{ __('messages.Failed to delete guest.') }}");
                    }
                },
                error: function () {
                    alert("{{ __('messages.Failed to delete guest.') }}");
                }
            });
        });

    </script>
    <script>
            document.addEventListener('DOMContentLoaded', function () {
                const inSelect = document.getElementById('transportIn');
                const outSelect = document.getElementById('transportOut');
                const priceText = document.getElementById('airportShuttleText');
                const priceContainer = document.getElementById('airportShuttlePrice');
                const priceTextContainer = document.getElementById('airportShuttle');
                const totalPrice = document.getElementById('totalPrice');
                const basePrice = parseFloat(totalPrice.dataset.basePrice) || 0;

                const transportInPriceId = document.getElementById('transportInPriceId');
                const transportOutPriceId = document.getElementById('transportOutPriceId');
                const inputAirportShuttlePrice = document.getElementById('inputAirportShuttlePrice');
                const inputFinalPrice = document.getElementById('inputFinalPrice');

                function formatUSD(amount) {
                    return `$ ${amount.toFixed(2)}`;
                }

                function updateShuttlePrice() {
                    const inOption = inSelect.selectedOptions[0];
                    const outOption = outSelect.selectedOptions[0];

                    const inPrice = parseFloat(inOption?.getAttribute('data-transportinprice')) || 0;
                    const outPrice = parseFloat(outOption?.getAttribute('data-transportoutprice')) || 0;
                    const shuttleTotal = inPrice + outPrice;
                    const finalTotal = basePrice + shuttleTotal;

                    // Update airport shuttle text and visibility
                    if (shuttleTotal > 0) {
                        priceText.textContent = formatUSD(shuttleTotal);
                        priceContainer.style.display = 'block';
                        priceTextContainer.style.display = 'block';
                    } else {
                        priceText.textContent = '';
                        priceContainer.style.display = 'none';
                        priceTextContainer.style.display = 'none';
                    }

                    // Update total price
                    totalPrice.textContent = formatUSD(finalTotal);
                    inputAirportShuttlePrice.value = shuttleTotal;
                    inputFinalPrice.value = finalTotal;

                    // Update hidden inputs
                    transportInPriceId.value = inOption?.getAttribute('data-transportinpriceid') || '';
                    transportOutPriceId.value = outOption?.getAttribute('data-transportoutpriceid') || '';
                }

                inSelect.addEventListener('change', updateShuttlePrice);
                outSelect.addEventListener('change', updateShuttlePrice);

                // Initialize on load
                updateShuttlePrice();
            });

        </script>