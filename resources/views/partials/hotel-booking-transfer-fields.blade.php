@php
    $transferTypes = old('flight_type', ['']);
    $transferTimes = old('flight_time', []);
    $transferTransportIds = old('flight_transport_id', []);
    $transferFlightNumbers = old('flight_number', []);
    $transferCheckin = $checkin ?? session('booking_dates.checkin') ?? request('checkin');
    $transferCheckout = $checkout ?? session('booking_dates.checkout') ?? request('checkout');
@endphp

<section class="frontend-detail-block">
    <div class="frontend-detail-block__header">
        <div>
            <div class="frontend-detail-block__eyebrow">@lang('messages.Transfers')</div>
            <h3 class="frontend-detail-block__title">@lang('messages.Flight and transport detail')</h3>
        </div>
    </div>

    @if ($transferCheckin || $transferCheckout)
        <div class="booking-transfer-dates" aria-label="@lang('messages.Check In and Check Out')">
            <div class="booking-transfer-dates__intro">
                <span class="booking-transfer-dates__eyebrow">@lang('messages.Check In and Check Out')</span>
            </div>
            <div class="booking-transfer-dates__grid">
                <div class="booking-transfer-dates__item">
                    <span>@lang('messages.Check-in')</span>
                    <strong>{{ $transferCheckin ? dateFormat($transferCheckin) : '-' }}</strong>
                </div>
                <div class="booking-transfer-dates__item">
                    <span>@lang('messages.Check-out')</span>
                    <strong>{{ $transferCheckout ? dateFormat($transferCheckout) : '-' }}</strong>
                </div>
            </div>
        </div>
    @endif

    <div class="booking-transfer-stack" data-transfer-list>
        @foreach ($transferTypes as $index => $transferType)
            <div class="booking-transfer-row" data-transfer-item>
                <div class="row align-items-end">
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>@lang('messages.Type')</label>
                            <select name="flight_type[]" class="custom-select">
                                <option value="">@lang('messages.Select Type')</option>
                                <option value="arrival" {{ $transferType === 'arrival' ? 'selected' : '' }}>@lang('messages.Arrival')</option>
                                <option value="departure" {{ $transferType === 'departure' ? 'selected' : '' }}>@lang('messages.Departure')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>@lang('messages.Flight Number')</label>
                            <input
                                type="text"
                                name="flight_number[]"
                                class="form-control"
                                placeholder="@lang('messages.Insert flight number')"
                                value="{{ $transferFlightNumbers[$index] ?? '' }}"
                            >
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>@lang('messages.Date and time')</label>
                            <input
                                readonly
                                type="text"
                                name="flight_time[]"
                                class="form-control booking-datetime-input"
                                placeholder="@lang('messages.Select date and time')"
                                value="{{ $transferTimes[$index] ?? '' }}"
                            >
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-8 col-md-6">
                        <div class="form-group">
                            <label>@lang('messages.Airport Shuttle') <i data-toggle="tooltip" data-placement="top" title="@lang('messages.Request')" class="icon-copy fa fa-info-circle field-help-icon" aria-hidden="true"></i></label>
                            <select name="flight_transport_id[]" class="custom-select booking-transfer-select">
                                <option value="" data-transport-active="0" data-transport-price="0" data-transport-price-id="">@lang('messages.Select Transport')</option>
                                @if ($transportOptions->count() > 0)
                                    @foreach ($transportOptions as $transportOption)
                                        <option
                                            value="{{ $transportOption['id'] }}"
                                            data-transport-active="1"
                                            data-transport-price="{{ $transportOption['price'] }}"
                                            data-transport-price-id="{{ $transportOption['price_id'] }}"
                                            {{ (string) ($transferTransportIds[$index] ?? '') === (string) $transportOption['id'] ? 'selected' : '' }}
                                        >
                                            {{ $transportOption['label'] }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="Request" data-transport-active="1" data-transport-price="0" data-transport-price-id="">@lang('messages.Request')</option>
                                @endif
                            </select>
                            <input type="hidden" name="flight_transport_label[]" value="">
                        </div>
                    </div>
                    <div class="col-xl-1 col-lg-1 col-md-12">
                        <button type="button" class="btn btn-remove booking-transfer-row__remove" data-remove-flight>
                            <i class="icon-copy fa fa-close" aria-hidden="true"></i>
                            <span class="sr-only">@lang('messages.Remove')</span>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <button type="button" class="btn btn-outline-primary hotel-booking-page__add-flight" data-add-flight>
        <i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i>
        @lang('messages.Add More Flight')
    </button>

    <div class="d-none" aria-hidden="true">
        <input type="text" name="arrival_flight" value="{{ old('arrival_flight') }}">
        <input type="text" name="departure_flight" value="{{ old('departure_flight') }}">
        <input type="text" name="arrival_time" value="{{ old('arrival_time') }}">
        <input type="text" name="departure_time" value="{{ old('departure_time') }}">

        <select name="airport_shuttle_in" id="airportShuttleIn">
            <option selected value="" data-transportin="0">@lang('messages.Select Transport')</option>
            @if ($transportOptions->count() > 0)
                @foreach ($transportOptions as $transportOption)
                    <option value="{{ $transportOption['id'] }}" data-transportin="1" data-transportpricein="{{ $transportOption['price'] }}" data-transportinpriceid="{{ $transportOption['price_id'] }}">{{ $transportOption['label'] }}</option>
                @endforeach
            @else
                <option value="Request" data-transportin="1">@lang('messages.Request')</option>
            @endif
        </select>

        <select name="airport_shuttle_out" id="airportShuttleOut">
            <option selected value="" data-transportout="0" data-transportpriceout="0">@lang('messages.Select Transport')</option>
            @if ($transportOptions->count() > 0)
                @foreach ($transportOptions as $transportOption)
                    <option value="{{ $transportOption['id'] }}" data-transportout="1" data-transportpriceout="{{ $transportOption['price'] }}" data-transportoutpriceid="{{ $transportOption['price_id'] }}">{{ $transportOption['label'] }}</option>
                @endforeach
            @else
                <option value="Request" data-transportout="1">@lang('messages.Request')</option>
            @endif
        </select>
    </div>

    <template data-transfer-template>
            <div class="booking-transfer-row" data-transfer-item>
                <div class="row align-items-end">
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>@lang('messages.Type')</label>
                            <select name="flight_type[]" class="custom-select">
                                <option value="">@lang('messages.Select Type')</option>
                                <option value="arrival">@lang('messages.Arrival')</option>
                                <option value="departure">@lang('messages.Departure')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>@lang('messages.Flight Number')</label>
                            <input type="text" name="flight_number[]" class="form-control" placeholder="@lang('messages.Insert flight number')">
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>@lang('messages.Date and time')</label>
                            <input readonly type="text" name="flight_time[]" class="form-control booking-datetime-input" placeholder="@lang('messages.Select date and time')">
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-8 col-md-6">
                        <div class="form-group">
                            <label>@lang('messages.Airport Shuttle') <i data-toggle="tooltip" data-placement="top" title="@lang('messages.Request')" class="icon-copy fa fa-info-circle field-help-icon" aria-hidden="true"></i></label>
                            <select name="flight_transport_id[]" class="custom-select booking-transfer-select">
                                <option value="" data-transport-active="0" data-transport-price="0" data-transport-price-id="">@lang('messages.Select Transport')</option>
                            @if ($transportOptions->count() > 0)
                                @foreach ($transportOptions as $transportOption)
                                    <option value="{{ $transportOption['id'] }}" data-transport-active="1" data-transport-price="{{ $transportOption['price'] }}" data-transport-price-id="{{ $transportOption['price_id'] }}">{{ $transportOption['label'] }}</option>
                                @endforeach
                            @else
                                <option value="Request" data-transport-active="1" data-transport-price="0" data-transport-price-id="">@lang('messages.Request')</option>
                            @endif
                        </select>
                            <input type="hidden" name="flight_transport_label[]" value="">
                        </div>
                    </div>
                    <div class="col-xl-1 col-lg-1 col-md-12">
                        <button type="button" class="btn btn-remove booking-transfer-row__remove" data-remove-flight>
                            <i class="icon-copy fa fa-close" aria-hidden="true"></i>
                            <span class="sr-only">@lang('messages.Remove')</span>
                        </button>
                    </div>
            </div>
        </div>
    </template>
</section>
