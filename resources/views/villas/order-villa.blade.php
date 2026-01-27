@section('title','Order Villa')
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
                                    <i class="icon-copy dw dw-hotel-o"></i>
                                    {{ $orderNumber }}
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    @include('partials.breadcrumbs', [
                                        'breadcrumbs' => [
                                            ['url' => route('dashboard.index'), 'label' => __('messages.Dashboard')],
                                            ['url' => route('view.villas.index'), 'label' => __('messages.Villas')],
                                            ['url' => route('view.villas.show',$villa->code), 'label' => $villa->name],
                                            ['label' => dateFormat($checkin)." - ".dateFormat($checkout) ],
                                        ]
                                    ])
                                </nav>
                            </div>
                        </div>
                    </div>
                    @include('partials.alerts')
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-tag" aria-hidden="true"></i>@lang('messages.Create Order')</div>
                                </div>
                                <div class="row">
                                    <div class="col-6 col-md-6">
                                        <div class="order-bil text-left">
                                            <img src="{{ asset($logoDark) }}" alt="{{ $altLogo }}" loading="lazy">
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
                                <form id="create-order" action="{{ route('func.add-order-villa') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table tb-list nowrap">
                                                <tbody>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Order No')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $orderNumber }}
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
                                                <table class="table tb-list">
                                                    <tbody>
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Duration')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ $duration." ".__("messages.nights")}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Check In')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ dateFormat($checkin) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Check Out')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ dateFormat($checkout) }}
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
                                                        <div class="row g-3">
                                                            <div class="col-md-5">
                                                                <label for="name_0" class="form-label">Name</label>
                                                                <input type="text" name="guests[0][name]" class="form-control" id="name_0" required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="sex_0" class="form-label">Sex</label>
                                                                <select name="guests[0][sex]" class="custom-select" id="sex_0">
                                                                    <option value="">Choose...</option>
                                                                    <option value="m">Male</option>
                                                                    <option value="f">Female</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="age_0" class="form-label">Age <span>*</span></label>
                                                                <select name="guests[0][age]" class="custom-select" id="age_0" required>
                                                                    <option selected value="">Choose...</option>
                                                                    <option value="Adult">@lang('messages.Adult') (12 @lang('messages.years and above'))</option>
                                                                    <option value="Child">@lang('messages.Child') (@lang('messages.2-12 years old'))</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right m-t-8">
                                                    <button type="button" class="btn btn-secondary" id="add-more"><i class="icon-copy fa fa-plus"></i> Add More</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="page-subtitle">@lang('messages.Airport Shuttle')</div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="arrival_flight">@lang('messages.Arrival Flight')</label>
                                                    <input type="text" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror" placeholder="@lang('messages.Arrival Flight')" value="{{ old('arrival_flight') }}">
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
                                                    <input readonly type="text" name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ old('arrival_time') }}">
                                                    @error('arrival_time')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="airport_shuttle_in">@lang('messages.Airport Shuttle') <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Request')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label>
                                                    <select name="airport_shuttle_in" id="transportIn" type="text" class="custom-select @error('airport_shuttle_in') is-invalid @enderror">
                                                        <option selected value="" data-transportinprice="0">@lang('messages.Select Transport')</option>
                                                        @if ($transports)
                                                            @foreach ($transports as $inTransport)
                                                                @if ($inTransport->calculated_price)
                                                                    <option 
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
                                                    <input type="text" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror" placeholder="@lang('messages.Departure Flight')" value="{{ old('departure_flight') }}">
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
                                                    <input readonly type="text" name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ old('departure_time') }}">
                                                    @error('departure_time')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="airport_shuttle_out">@lang('messages.Airport Shuttle') <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Request')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label>
                                                    <select name="airport_shuttle_out" id="transportOut" type="text" class="custom-select @error('airport_shuttle_out') is-invalid @enderror">
                                                        <option selected value="" data-transportout="0" data-transportoutprice=0>@lang('messages.Select Transport')</option>
                                                        
                                                        @if ($transports)
                                                            @foreach ($transports as $outTransport)
                                                                @if ($outTransport->calculated_price)
                                                                    <option 
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
                                                    <textarea id="note" name="note" placeholder="@lang('messages.Optional')" class="tiny_mce form-control border-radius-0" value="{{ old('note') }}"></textarea>
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
                                                                <hr class="form-hr">
                                                            </div>
                                                            <div class="total-price">@lang('messages.Total Price')</div>
                                                        </div>
                                                        <div class="col-6 col-md-6 text-right">
                                                            <div id="airportShuttlePrice" class="text-price" style="display: none;">
                                                                <span id="airportShuttleText"></span><br>
                                                                <hr class="form-hr">
                                                            </div>
                                                            <div class="total-price">
                                                                <span id="totalPrice" data-base-price="{{ $total_price }}">
                                                                    {{ currencyFormatUsd($total_price) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" name="villa_price_id" value="{{ $price->id }}">
                                        <input type="hidden" name="villa_id" value="{{ $villa->id }}">
                                        <input type="hidden" name="transport_out_price_id" id="transportOutPriceId" value="">
                                        <input type="hidden" name="transport_in_price_id" id="transportInPriceId" value="">
                                        <input type="hidden" name="airport_shuttle_price" id="inputAirportShuttlePrice" value="">
                                        <input type="hidden" name="orderno" value="{{ $orderNumber }}">
                                        <div class="card-box-footer">
                                            <button type="submit" form="create-order" id="normal-reserve" class="btn btn-primary"><i class="icon-copy fa fa-shopping-basket" aria-hidden="true"></i> @lang('messages.Add to Cart')</button>
                                            <button type="button" onclick="goBack()" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                        </div>
                                        
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                @include('layouts.footer')
            </div>
        </div>
        
        @include('partials.loading-form', ['id' => 'create-order'])
        <script type="text/javascript">
            function goBack() {
              window.history.back();
            }
        </script>
        <script>
            const maxGuests = {{ $occupancy }};
            const wrapper = document.getElementById('guest-wrapper');
            const addMoreBtn = document.getElementById('add-more');

            let index = 1;

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
                            <input type="text" name="guests[${index}][name]" class="form-control" id="name_${index}" required>
                        </div>
                        <div class="col-md-3">
                            <label for="sex_${index}" class="form-label">Sex</label>
                            <select name="guests[${index}][sex]" class="custom-select" id="sex_${index}" required>
                                <option value="">Choose...</option>
                                <option value="m">Male</option>
                                <option value="f">Female</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="age_${index}" class="form-label">Age <span>*</span></label>
                            <select name="guests[${index}][age]" class="custom-select" id="age_${index}" required>
                                <option selected value="">Choose...</option>
                                <option value="Adult">@lang('messages.Adult') (12 @lang('messages.years and above'))</option>
                                <option value="Child">@lang('messages.Child') (@lang('messages.2-12 years old'))</option>
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

                function formatUSD(amount) {
                    return '$ ' + new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(amount);
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


@endsection