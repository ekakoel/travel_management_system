@php
    $partner = $partner ?? null;
    $formKey = $partner?->id ?? 'new';
@endphp

<div class="backend-form-grid partners-admin-form-grid">

    {{-- Cover Image --}}
    <label class="is-wide">
        <span>
            Cover Image
            @if (!$partner)
                <b>*</b>
            @endif
        </span>

        @if ($partner && $partner->coverUrl())
            <div class="partners-admin-cover-preview" data-partner-cover-preview-wrapper="{{ $formKey }}">
                <img src="{{ $partner->coverUrl() }}" alt="{{ $partner->name }}"
                    id="partner-cover-preview-{{ $formKey }}" data-partner-cover-preview="{{ $formKey }}">
            </div>
        @else
            <div class="partners-admin-cover-preview" id="partner-cover-preview-wrapper-{{ $formKey }}" data-partner-cover-preview-wrapper="{{ $formKey }}" style="display: none;">
                <img src="" alt="Cover preview" id="partner-cover-preview-{{ $formKey }}" data-partner-cover-preview="{{ $formKey }}">
            </div>
        @endif

        <input class="backend-form-control" type="file" name="cover" id="partner-cover-{{ $formKey }}"
            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-partner-cover-input="{{ $formKey }}" @required(!$partner)>

        <small class="backend-form-help">
            JPG, JPEG, PNG or WEBP. Maximum file size 5 MB.
        </small>

        @error('cover')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </label>
    {{-- Partner Name --}}
    <label>
        <span>Partner Name <b>*</b></span>
        <input class="backend-form-control" type="text" name="name"
            value="{{ old('name', optional($partner)->name) }}" placeholder="Insert partner name" required>

        @error('name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </label>

    @if ($partner)
        <label>
            <span>Status <b>*</b></span>
            <select class="backend-form-control" name="status" required>
                @foreach ([\App\Models\Partners::STATUS_DRAFT, \App\Models\Partners::STATUS_ACTIVE] as $status)
                    <option value="{{ $status }}" @selected(old('status', $partner->status) === $status)>
                        {{ $status }}
                    </option>
                @endforeach
            </select>

            @error('status')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </label>
    @endif

    {{-- Partner Type --}}
    <label>
        <span>Partner Type <b>*</b></span>
        <select class="backend-form-control" name="type" required>
            <option value="">Select partner type</option>

            @foreach (['Activity', 'Transport', 'Activity & Transport'] as $type)
                <option value="{{ $type }}" @selected(old('type', optional($partner)->type) === $type)>
                    {{ $type }}
                </option>
            @endforeach
        </select>

        @error('type')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </label>

    {{-- Contact Person --}}
    <label>
        <span>Contact Person <b>*</b></span>
        <input class="backend-form-control" type="text" name="contact_person"
            value="{{ old('contact_person', optional($partner)->contact_person) }}" placeholder="Insert contact person"
            required>

        @error('contact_person')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </label>

    {{-- Phone --}}
    <label>
        <span>Telephone <b>*</b></span>
        <input class="backend-form-control" type="text" name="phone"
            value="{{ old('phone', optional($partner)->phone) }}" placeholder="Insert telephone number" required>

        @error('phone')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </label>

    {{-- Location --}}
    <label>
        <span>Location <b>*</b></span>
        <input class="backend-form-control" type="text" name="location"
            value="{{ old('location', optional($partner)->location) }}" placeholder="Example: Ubud, Bali" required>

        @error('location')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </label>

    {{-- Map --}}
    <label>
        <span>Map <b>*</b></span>
        <input class="backend-form-control" type="text" name="map"
            value="{{ old('map', optional($partner)->map) }}" placeholder="Insert Google Maps link or map reference"
            required>

        @error('map')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </label>

    {{-- Address --}}
    <label class="is-wide">
        <span>Address <b>*</b></span>
        <textarea class="backend-form-control" name="address" rows="4" placeholder="Insert complete partner address"
            required>{{ old('address', optional($partner)->address) }}</textarea>

        @error('address')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </label>

    {{-- Description --}}
    <label class="is-wide">
        <span>Description</span>
        <textarea class="backend-form-control" data-backend-richtext="true" name="description"
            placeholder="Insert partner description">{{ old('description', optional($partner)->description) }}</textarea>

        @error('description')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </label>

</div>
