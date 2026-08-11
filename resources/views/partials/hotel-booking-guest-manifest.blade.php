<section class="frontend-detail-block" data-guest-manifest
    data-label-room="@lang('messages.Room')"
    data-label-guest="@lang('messages.Guest')"
    data-label-full-name="@lang('messages.Guest Full Name')"
    data-label-phone="@lang('messages.Phone')"
    data-label-optional="@lang('messages.Optional')"
    data-label-category="@lang('messages.Age/Category')"
    data-label-gender="@lang('messages.Gender')"
    data-label-adult="@lang('messages.Adult')"
    data-label-child="@lang('messages.Child')"
    data-label-child-ages="@lang('messages.Child ages')"
    data-label-male="@lang('messages.Male')"
    data-label-female="@lang('messages.Female')">
    <div class="frontend-detail-block__header">
        <div>
            <div class="frontend-detail-block__eyebrow">@lang('messages.Guests')</div>
            <h3 class="frontend-detail-block__title">@lang('messages.Guest details')</h3>
        </div>
        <div class="frontend-detail-block__meta">@lang('messages.One record for every staying guest')</div>
    </div>
    <p class="booking-wizard__text">@lang('messages.Guest records are generated from the room occupancy selected in step one.')</p>
    @if ($errors->has('guest_name') || $errors->has('guest_name.*'))
        <div class="alert alert-danger" role="alert">{{ $errors->first('guest_name') ?: $errors->first('guest_name.*') }}</div>
    @endif
    <div class="hotel-guest-manifest" data-guest-list></div>
    @php
        $oldBookingState = [
            'room_adults' => old('room_adults', []),
            'room_children' => old('room_children', []),
            'room_child_ages' => old('room_child_ages', []),
            'guest_name' => old('guest_name', []),
            'guest_phone' => old('guest_phone', []),
            'guest_sex' => old('guest_sex', []),
            'special_day' => old('special_day', []),
            'special_date' => old('special_date', []),
            'extra_bed_id' => old('extra_bed_id', []),
        ];
    @endphp
    <script type="application/json" data-booking-old-state>{!! json_encode(
        $oldBookingState,
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ) !!}</script>
</section>
