@php
    $roomNumber = $roomNumber ?? 1;
    $showRemove = $showRemove ?? false;
    $badgeNumber = str_pad((string) $roomNumber, 2, '0', STR_PAD_LEFT);
@endphp

<div class="m-b-8 room-list-stack__item" data-room-item>
    <div class="room-container">
        <div class="room-card-head">
            <div class="room-card-head__label">
                <span class="room-card-head__badge">{{ $badgeNumber }}</span>
                <div>
                    <div class="room-card-head__title">@lang('messages.Room') {{ $roomNumber }}</div>
                    <div class="room-card-head__subtitle">{{ $showRemove ? __('messages.Additional room') : __('messages.Lead room') }}</div>
                </div>
            </div>
            <button class="ui-btn ui-btn--danger ui-btn--sm room-card-head__remove" type="button" {{ $showRemove ? '' : 'hidden' }}>
                <i class="icon-copy fa fa-close" aria-hidden="true"></i>
                @lang('messages.Remove')
            </button>
        </div>

        <div class="row">
            @if ($errors->has('room_adults.*') || $errors->has('room_children.*') || $errors->has('room_child_ages.*'))
                <div class="col-12"><div class="alert alert-danger" role="alert">{{ $errors->first('room_adults.*') ?: ($errors->first('room_children.*') ?: $errors->first('room_child_ages.*')) }}</div></div>
            @endif
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label>@lang('messages.Adults') <span class="text-danger" aria-hidden="true">*</span></label>
                    <input type="number" min="1" max="{{ $roomForm['adult_capacity'] }}" name="room_adults[]"
                        class="form-control m-0" value="1" data-room-adults required>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label>@lang('messages.Children') <span class="text-danger" aria-hidden="true">*</span></label>
                    <input type="number" min="0" max="{{ $roomForm['child_capacity'] }}" name="room_children[]"
                        class="form-control m-0" value="0" data-room-children required>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="form-group">
                    <label>@lang('messages.Child ages')</label>
                    <div class="hotel-child-age-list" data-child-age-list data-label-no-children="@lang('messages.No children in this room.')">
                        <span class="form-text text-muted" data-no-child-age>@lang('messages.No children in this room.')</span>
                    </div>
                </div>
            </div>

            <input type="hidden" name="number_of_guests[]" value="1" data-room-guest-total
                data-room-capacity="{{ $roomForm['room_capacity'] }}"
                data-adult-capacity="{{ $roomForm['adult_capacity'] }}"
                data-child-capacity="{{ $roomForm['child_capacity'] }}"
                data-extra-bed-trigger="{{ $roomForm['extra_bed_trigger_capacity'] }}">

            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label>@lang('messages.Special Day')</label>
                    <input type="text" name="special_day[]" class="form-control m-0" placeholder="@lang('messages.ex Birthday')">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label>@lang('messages.Insert Date for Special Day')</label>
                    <input type="text" name="special_date[]" class="form-control m-0 booking-date-input"
                        placeholder="@lang('messages.Select date')" data-ui-picker="date" data-ui-picker-format="YYYY-MM-DD" readonly>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 room-card-col--align-end">
                <div class="form-group">
                    <label>@lang('messages.Extra Bed')</label>
                    <select name="extra_bed_id[]" class="custom-select" data-extra-bed-select>
                        <option selected value="" data-ebprice="0">{{ count($roomForm['extra_bed_options']) > 0 ? __('messages.None') : __('messages.No extra bed available') }}</option>
                        @foreach ($roomForm['extra_bed_options'] as $extraBedOption)
                            <option value="{{ $extraBedOption['id'] }}" data-ebprice="{{ $extraBedOption['price'] }}">{{ $extraBedOption['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
