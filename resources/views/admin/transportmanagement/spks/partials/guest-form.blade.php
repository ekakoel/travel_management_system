<label>
    <span>@lang('transport-management.detail.guests.name') <b>*</b></span>
    <input class="backend-form-control" name="name" type="text" value="{{ old('name', $guest?->name) }}" placeholder="{{ __('transport-management.detail.guests.name_placeholder') }}" required>
</label>
<label>
    <span>@lang('transport-management.detail.guests.mandarin_name')</span>
    <input class="backend-form-control" name="name_mandarin" type="text" value="{{ old('name_mandarin', $guest?->name_mandarin) }}" placeholder="{{ __('transport-management.detail.guests.mandarin_placeholder') }}">
</label>
<label>
    <span>@lang('transport-management.detail.guests.sex') <b>*</b></span>
    <select class="backend-form-control" name="sex" required>
        <option disabled value="">@lang('transport-management.detail.guests.select_sex')</option>
        <option value="m" @selected(old('sex', $guest?->sex) === 'm')>@lang('transport-management.detail.guests.male')</option>
        <option value="f" @selected(old('sex', $guest?->sex) === 'f')>@lang('transport-management.detail.guests.female')</option>
    </select>
</label>
<label>
    <span>@lang('transport-management.detail.guests.age') <b>*</b></span>
    <select class="backend-form-control" name="age" required>
        <option disabled value="">@lang('transport-management.detail.guests.select_age')</option>
        <option value="Adult" @selected(old('age', $guest?->age) === 'Adult')>@lang('transport-management.detail.guests.adult')</option>
        <option value="Child" @selected(old('age', $guest?->age) === 'Child')>@lang('transport-management.detail.guests.child')</option>
    </select>
</label>
<label class="is-wide">
    <span>@lang('transport-management.detail.guests.phone')</span>
    <input class="backend-form-control" name="phone" type="text" value="{{ old('phone', $guest?->phone) }}" placeholder="{{ __('transport-management.detail.guests.phone_placeholder') }}">
</label>
