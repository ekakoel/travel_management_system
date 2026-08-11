@props([
    'label',
])

<button
    type="button"
    data-backend-modal-close
    {{ $attributes->class(['backend-modal-close']) }}
    aria-label="{{ $label }}"
>
    <span aria-hidden="true">&times;</span>
</button>
