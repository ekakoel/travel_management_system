@php
    $wizardSteps = $wizardSteps ?? [];
    $wizardActiveStep = $wizardActiveStep ?? 1;
    $wizardAriaLabel = $wizardAriaLabel ?? __('messages.Booking form steps');
@endphp

<div class="booking-wizard__nav" aria-label="{{ $wizardAriaLabel }}">
    @foreach ($wizardSteps as $index => $wizardStep)
        @php
            $stepNumber = $index + 1;
            $title = $wizardStep['title'] ?? '';
            $description = $wizardStep['description'] ?? '';
        @endphp
        <button
            type="button"
            class="booking-wizard__step{{ $stepNumber === $wizardActiveStep ? ' is-active' : '' }}"
            data-wizard-step-target="{{ $stepNumber }}"
        >
            <span class="booking-wizard__step-index">{{ $stepNumber }}</span>
            <span class="booking-wizard__step-copy">
                <strong>{{ $title }}</strong>
                <span>{{ $description }}</span>
            </span>
        </button>
    @endforeach
</div>
