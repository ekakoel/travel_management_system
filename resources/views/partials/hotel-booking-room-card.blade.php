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
            <button
                class="btn btn-remove room-card-head__remove"
                type="button"
                {{ $showRemove ? '' : 'hidden' }}
            >
                <i class="icon-copy fa fa-close" aria-hidden="true"></i>
                @lang('messages.Remove')
            </button>
        </div>

        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label>@lang('messages.Number of Guest')</label>
                    <input
                        type="number"
                        min="1"
                        max="{{ $roomForm['room_capacity'] }}"
                        data-room-capacity="{{ $roomForm['room_capacity'] }}"
                        data-adult-capacity="{{ $roomForm['adult_capacity'] }}"
                        data-child-capacity="{{ $roomForm['child_capacity'] }}"
                        data-extra-bed-trigger="{{ $roomForm['extra_bed_trigger_capacity'] }}"
                        name="number_of_guests[]"
                        class="guest-input form-control m-0 @error('number_of_guests[]') is-invalid @enderror"
                        placeholder="{{ $roomForm['guest_placeholder'] }}"
                        value="{{ old('number_of_guests[]') }}"
                        required
                    >
                    @error('number_of_guests[]')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-sm-9">
                <div class="form-group">
                    <label>@lang('messages.Guest Name') <i data-toggle="tooltip" data-placement="top" title="@lang('messages.Children guests must include the age on the back of their name. ex: Children Name(age)')" class="icon-copy fa fa-info-circle field-help-icon" aria-hidden="true"></i></label>
                    <input
                        type="text"
                        name="guest_detail[]"
                        class="form-control m-0 @error('guest_detail[]') is-invalid @enderror"
                        placeholder="@lang('messages.Separate names with commas')"
                        value="{{ old('guest_detail[]') }}"
                        required
                    >
                    @error('guest_detail[]')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label>@lang('messages.Special Day') <i data-toggle="tooltip" data-placement="top" title="@lang('messages.If during your stay there are guests who have special days such as birthdays, aniversaries, and others')" class="icon-copy fa fa-info-circle field-help-icon" aria-hidden="true"></i></label>
                    <input
                        type="text"
                        name="special_day[]"
                        class="form-control m-0 @error('special_day[]') is-invalid @enderror"
                        placeholder="@lang('messages.ex Birthday')"
                        value="{{ old('special_day[]') }}"
                    >
                    @error('special_day[]')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label>@lang('messages.Insert Date for Special Day')</label>
                    <input
                        type="text"
                        name="special_date[]"
                        class="form-control m-0 booking-date-input @error('special_date[]') is-invalid @enderror"
                        placeholder="@lang('messages.Select date')"
                        value="{{ old('special_date[]') }}"
                        readonly
                    >
                    @error('special_date[]')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-sm-4 room-card-col--align-end">
                <div class="form-group">
                    <label>@lang('messages.Extra Bed')<span> * </span><i data-toggle="tooltip" data-placement="top" title="@lang('messages.Select an extra bed if the room is occupied by more than room capacity')" class="icon-copy fa fa-info-circle field-help-icon" aria-hidden="true"></i></label><br>
                    <select name="extra_bed_id[]" class="custom-select @error('extra_bed_id[]') is-invalid @enderror" data-extra-bed-select>
                        <option selected value="" data-ebprice="0">{{ count($roomForm['extra_bed_options']) > 0 ? __('messages.None') : __('messages.No extra bed available') }}</option>
                        @foreach ($roomForm['extra_bed_options'] as $extraBedOption)
                            <option value="{{ $extraBedOption['id'] }}" data-ebprice="{{ $extraBedOption['price'] }}">{{ $extraBedOption['label'] }}</option>
                        @endforeach
                    </select>
                    @error('extra_bed[]')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>
