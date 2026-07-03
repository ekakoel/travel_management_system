@php
    $formId = $formId ?? 'hotelCheckPriceForm';
    $selectedDuration = (int) ($duration ?? session('booking_dates.duration') ?? max((int) ($hotel->min_stay ?? 1), 1));
    $selectedDuration = max($selectedDuration, 1);

    $checkinDate = $checkin ?? session('booking_dates.checkin');
    if (!$checkinDate) {
        $checkinDate = \Carbon\Carbon::now()->addDays(7)->format('Y-m-d');
    }

    $checkoutDate = $checkout ?? session('booking_dates.checkout');
    if (!$checkoutDate) {
        $checkoutDate = \Carbon\Carbon::parse($checkinDate)->addDays($selectedDuration)->format('Y-m-d');
    }

    $displayCheckInOut = dateFormat($checkinDate) . ' - ' . dateFormat($checkoutDate);
    $isMinStayWarning = $selectedDuration < (int) ($hotel->min_stay ?? 0);
@endphp

<div
    class="card-box hotel-check-card p-3 {{ $isMinStayWarning ? 'form-alert' : '' }}"
    data-check-price-card
    data-min-stay="{{ (int) ($hotel->min_stay ?? 0) }}"
    data-night-label="@lang('messages.nights')"
>
    <div class="card-box-title">
        <div class="hotel-check-kicker">
            <i class="icon-copy fa fa-search" aria-hidden="true"></i>
            @lang('messages.Check Price')
        </div>
        <h3 class="hotel-check-heading">{{ $hotel->name }}</h3>
        <p class="hotel-check-subheading">{{ $hotel->region }}</p>
    </div>

    <div class="card-box-body">
        <div class="hotel-check-meta">
            <div class="hotel-check-meta-item">
                <span class="hotel-check-meta-label">@lang('messages.Region')</span>
                <span class="hotel-check-meta-value">{{ $hotel->region }}</span>
            </div>
            <div class="hotel-check-meta-item">
                <span class="hotel-check-meta-label">@lang('messages.Minimum stay')</span>
                <span class="hotel-check-meta-value">{{ $hotel->min_stay }} @lang('messages.nights')</span>
            </div>
        </div>

        <form
            id="{{ $formId }}"
            action="{{ url('/hotel-price-' . $hotel->code) }}"
            method="POST"
            role="search"
            data-submit-url="{{ url('/hotel-price-' . $hotel->code) }}"
        >
            @csrf
            <div class="hotel-check-form-group">
                <label for="{{ $formId }}-checkincout">@lang('messages.Check In') - @lang('messages.Check Out')</label>
                <div class="input-group-icon">
                    <i class="icon-copy dw dw-calendar1"></i>
                    <input
                        readonly
                        id="{{ $formId }}-checkincout"
                        name="stay_range"
                        data-check-price-input
                        data-initial-checkin="{{ $checkinDate }}"
                        data-initial-checkout="{{ $checkoutDate }}"
                        class="form-control input-icon @error('stay_range') is-invalid @enderror"
                        type="text"
                        value="{{ $displayCheckInOut }}"
                        placeholder="@lang('messages.Check In') - @lang('messages.Check Out')"
                        autocomplete="off"
                        required
                    >
                    @error('stay_range')
                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <input type="hidden" name="checkin" value="{{ $checkinDate }}" data-check-price-checkin>
            <input type="hidden" name="checkout" value="{{ $checkoutDate }}" data-check-price-checkout>
            <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
            <input type="hidden" name="hotelcode" value="{{ $hotel->code }}">
        </form>

        <p class="hotel-check-note" data-check-price-note>
            {{ dateFormat($checkinDate) }} - {{ dateFormat($checkoutDate) }} | {{ $selectedDuration }} @lang('messages.nights')
        </p>

        @if ($isMinStayWarning)
            <p class="hotel-check-warning" data-check-price-warning>
                @lang('messages.Minimum stay') {{ $hotel->min_stay }} @lang('messages.nights')
            </p>
        @else
            <p class="hotel-check-warning d-none" data-check-price-warning>
                @lang('messages.Minimum stay') {{ $hotel->min_stay }} @lang('messages.nights')
            </p>
        @endif
    </div>

    <div class="card-box-footer">
        <button form="{{ $formId }}" type="submit" class="btn btn-primary btn-block">
            <i class="icon-copy fa fa-search" aria-hidden="true"></i> @lang('messages.Check Price')
        </button>
    </div>
</div>
