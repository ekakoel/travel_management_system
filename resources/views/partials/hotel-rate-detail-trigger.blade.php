@php
    $detailId = $detailId ?? ('hotel-rate-detail-' . md5(($triggerLabel ?? 'detail') . ($modalTitle ?? 'modal') . ($modalEyebrow ?? '') . ($modalContent ?? '')));
@endphp

<button
    type="button"
    class="availability-detail-trigger"
    data-detail-trigger="hotel-rate-detail"
    data-detail-source="#{{ $detailId }}"
    data-bs-toggle="modal"
    data-bs-target="#hotelRateDetailModal"
    aria-label="{{ $triggerLabel }}"
>
    <span class="availability-detail-trigger__icon" aria-hidden="true">
        <i class="fa {{ $triggerIcon ?? 'fa-check-circle-o' }}"></i>
    </span>
    <span class="availability-detail-trigger__content">
        @if (!empty($triggerEyebrow))
            <span class="availability-detail-trigger__eyebrow">{{ $triggerEyebrow }}</span>
        @endif
        <span class="availability-detail-trigger__label">{{ $triggerLabel }}</span>
    </span>
    <span class="availability-detail-trigger__arrow" aria-hidden="true">
        <i class="fa fa-angle-right"></i>
    </span>
</button>

<template id="{{ $detailId }}">
    <div
        data-detail-eyebrow="{{ $modalEyebrow ?? '' }}"
        data-detail-title="{{ $modalTitle }}"
        data-detail-icon="{{ $modalIcon ?? 'fa-check-circle-o' }}"
    >{!! $modalContent !!}</div>
</template>
