<div class="booking-submit-overlay frontend-order-modal__overlay hidden" data-form-submit-overlay aria-hidden="true">
    <div class="booking-submit-overlay__dialog" role="status" aria-live="assertive">
        <div class="booking-submit-overlay__spinner" aria-hidden="true"></div>
        <div class="booking-submit-overlay__content">
            <div class="booking-submit-overlay__title">
                {{ $title ?? __('messages.Processing') }}
            </div>
            <p class="booking-submit-overlay__text">
                {{ $message ?? __('messages.The order you sent is being processed, and we will contact you as soon as possible to confirm the order!') }}
            </p>
        </div>
    </div>
</div>
