@php
    $driver = $driver ?? null;
@endphp

<div class="backend-form-grid drivers-admin-form-grid">
    <label>
        <span>Name <b>*</b></span>
        <input class="backend-form-control" type="text" name="name" value="{{ old('name', optional($driver)->name) }}" placeholder="Insert driver name" required>
    </label>
    <label>
        <span>Telephone <b>*</b></span>
        <input class="backend-form-control" type="text" name="phone" value="{{ old('phone', optional($driver)->phone) }}" placeholder="Insert telephone number" required>
    </label>
    <label>
        <span>Email</span>
        <input class="backend-form-control" type="email" name="email" value="{{ old('email', optional($driver)->email) }}" placeholder="Insert email">
    </label>
    <label>
        <span>License <b>*</b></span>
        <input class="backend-form-control" type="text" name="license" value="{{ old('license', optional($driver)->license) }}" placeholder="Insert license" required>
    </label>
    <label>
        <span>Country</span>
        <input class="backend-form-control" type="text" name="country" value="{{ old('country', optional($driver)->country) }}" placeholder="Insert country">
    </label>
    <label>
        <span>Status</span>
        <select class="backend-form-control" name="status">
            @foreach (['Active', 'Inactive'] as $status)
                <option value="{{ $status }}" @selected(old('status', optional($driver)->status ?? 'Active') === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </label>
    <label class="is-wide">
        <span>Address</span>
        <textarea class="backend-form-control" data-backend-richtext="true" name="address" placeholder="Insert address">{{ old('address', optional($driver)->address) }}</textarea>
    </label>
    <input name="author" value="{{ Auth::id() }}" type="hidden">
</div>
