<label>
    <span>@lang('transport-management.table.date') <b>*</b></span>
    <input readonly class="date-picker" name="date" type="text" value="{{ old('date', $destination?->date ? dateFormat($destination->date) : '') }}" placeholder="DD/MM/YYYY" autocomplete="off" required>
</label>
<label>
    <span>@lang('transport-management.detail.flight.time') <b>*</b></span>
    <input readonly name="time" class="time-input" type="text" maxlength="5" value="{{ old('time', $destination?->date ? date('H:i', strtotime($destination->date)) : '') }}" placeholder="HH:MM" autocomplete="off" required>
</label>
<label class="is-wide">
    <span>@lang('transport-management.detail.destinations.name') <b>*</b></span>
    <input class="backend-form-control" name="destination_name" type="text" value="{{ old('destination_name', $destination?->destination_name) }}" placeholder="{{ __('transport-management.detail.destinations.name_placeholder') }}" required>
</label>
<label class="is-wide">
    <span>@lang('transport-management.detail.destinations.map_location')</span>
    <input class="backend-form-control" name="destination_address" type="text" value="{{ old('destination_address', $destination?->destination_address) }}" placeholder="{{ __('transport-management.detail.destinations.map_placeholder') }}">
</label>
<label class="is-wide">
    <span>@lang('transport-management.detail.destinations.description')</span>
    <textarea class="backend-form-control" data-backend-richtext="true" name="description" placeholder="{{ __('transport-management.detail.destinations.description_placeholder') }}">{{ old('description', $destination?->description) }}</textarea>
</label>
