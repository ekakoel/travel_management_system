@props([
    'label' => __('messages.Loading'),
])

<div {{ $attributes->class(['backend-loading-state']) }} role="status" aria-live="polite">
    <span class="backend-content-spinner" aria-hidden="true"></span>
    <span class="backend-loading-state__label">{{ $label }}</span>
</div>
