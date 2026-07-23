@php
    $guide = $guide ?? null;
@endphp

<div class="backend-form-grid guides-admin-form-grid">
    <label>
        <span>Name <b>*</b></span>
        <input class="backend-form-control" type="text" name="name" value="{{ old('name', optional($guide)->name) }}" placeholder="Insert guide name" required>
    </label>
    <label>
        <span>Sex <b>*</b></span>
        <select class="backend-form-control" name="sex" required>
            <option value="">Select sex</option>
            <option value="m" @selected(old('sex', optional($guide)->sex) === 'm')>Male</option>
            <option value="f" @selected(old('sex', optional($guide)->sex) === 'f')>Female</option>
        </select>
    </label>
    <label>
        <span>Telephone <b>*</b></span>
        <input class="backend-form-control" type="text" name="phone" value="{{ old('phone', optional($guide)->phone) }}" placeholder="Insert telephone number" required>
    </label>
    <label>
        <span>Email</span>
        <input class="backend-form-control" type="email" name="email" value="{{ old('email', optional($guide)->email) }}" placeholder="Insert email">
    </label>
    <label>
        <span>Language <b>*</b></span>
        <select class="backend-form-control" name="language" required>
            @foreach (['Mandarin', 'English'] as $language)
                <option value="{{ $language }}" @selected(old('language', optional($guide)->language ?? 'Mandarin') === $language)>{{ $language }}</option>
            @endforeach
        </select>
    </label>
    <label>
        <span>Country</span>
        <input class="backend-form-control" type="text" name="country" value="{{ old('country', optional($guide)->country) }}" placeholder="Insert country">
    </label>
    <label class="is-wide">
        <span>Address</span>
        <textarea class="backend-form-control" data-backend-richtext="true" name="address" placeholder="Insert address">{{ old('address', optional($guide)->address) }}</textarea>
    </label>
    <input name="author" value="{{ Auth::id() }}" type="hidden">
</div>
