<label class="company-profile-logo-field">
    <span>{{ $label }}</span>
    <div class="company-profile-logo-preview company-profile-logo-preview--{{ $variant }}" data-company-logo-preview="{{ $name }}">
        @if ($logoUrl)
            <img src="{{ asset($logoUrl) }}">
        @else
            <strong>No {{ strtolower($label) }} uploaded</strong>
        @endif
    </div>
    <input id="{{ $name }}" name="{{ $name }}" type="file" accept="image/*" class="backend-form-control @error($name) is-invalid @enderror" data-company-logo-input="{{ $name }}">
    <small>{{ $help }}</small>
    @error($name)
        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
    @enderror
</label>
