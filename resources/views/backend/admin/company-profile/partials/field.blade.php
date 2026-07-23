@php
    $fieldType = $type ?? 'input';
    $fieldInputType = $inputType ?? 'text';
    $fieldRows = $rows ?? 3;
    $fieldValue = old($name, $value ?? null);
    $isWide = $wide ?? false;
    $isRequired = $required ?? false;
@endphp

<label class="{{ $isWide ? 'is-wide' : '' }}">
    <span>{{ $label }} @if ($isRequired)<b>*</b>@endif</span>

    @if ($fieldType === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $fieldRows }}" class="backend-form-control @error($name) is-invalid @enderror" data-backend-richtext="true" @if ($isRequired) required @endif>{{ $fieldValue }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $fieldInputType }}" value="{{ $fieldValue }}" placeholder="{{ $placeholder ?? '' }}" class="backend-form-control @error($name) is-invalid @enderror" @if ($isRequired) required @endif>
    @endif

    @if (!empty($help))
        <small>{{ $help }}</small>
    @endif

    @error($name)
        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
    @enderror
</label>
