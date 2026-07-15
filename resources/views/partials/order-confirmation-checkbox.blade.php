@php
    $confirmationClass = trim('frontend-order-confirmation ' . ($class ?? ''));
    $confirmationId = $id ?? 'orderTermsAccepted';
@endphp

<label class="{{ $confirmationClass }}" for="{{ $confirmationId }}">
    <input
        id="{{ $confirmationId }}"
        type="checkbox"
        name="terms_accepted"
        value="1"
        class="@error('terms_accepted') is-invalid @enderror"
        @checked(old('terms_accepted'))
        required
    >
    <span>{!! __('tour-detail.accept_terms_with_link', ['url' => route('terms-and-conditions')]) !!}</span>
</label>
@error('terms_accepted')
    <div class="alert-form">{{ $message }}</div>
@enderror
