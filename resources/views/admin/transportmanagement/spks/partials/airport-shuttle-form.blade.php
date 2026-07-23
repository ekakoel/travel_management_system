<label>
    <span>@lang('transport-management.detail.flight.type') <b>*</b></span>
    <select class="backend-form-control" name="nav" required>
        <option disabled value="">@lang('transport-management.detail.flight.select_type')</option>
        <option value="In" @selected(old('nav', $airport_shuttle?->nav) === 'In')>@lang('transport-management.modal.arrival')</option>
        <option value="Out" @selected(old('nav', $airport_shuttle?->nav) === 'Out')>@lang('transport-management.modal.departure')</option>
    </select>
</label>
<label>
    <span>@lang('transport-management.detail.flight.number') <b>*</b></span>
    <input class="backend-form-control" name="flight_number" type="text" value="{{ old('flight_number', $airport_shuttle?->flight_number) }}" placeholder="{{ __('transport-management.detail.flight.number_placeholder') }}" required>
</label>
<label>
    <span>@lang('transport-management.table.date') <b>*</b></span>
    <input class="backend-form-control" readonly name="flight_date" type="text" value="{{ old('flight_date', $airport_shuttle?->date ? dateFormat($airport_shuttle->date) : '') }}" placeholder="DD/MM/YYYY" autocomplete="off" required>
</label>
<label>
    <span>@lang('transport-management.detail.flight.time') <b>*</b></span>
    <input readonly name="flight_time" class="time-input" type="text" maxlength="5" value="{{ old('flight_time', $airport_shuttle?->date ? date('H:i', strtotime($airport_shuttle->date)) : '') }}" placeholder="HH:MM" autocomplete="off" required>
</label>
