<div class="modal fade availability-modal" id="hotelRateDetailModal" tabindex="-1" aria-labelledby="hotelRateDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered availability-modal__dialog" role="document">
        <div class="modal-content availability-modal__content">
            <div class="availability-modal__panel">
                <div class="availability-modal__header">
                    <div class="availability-modal__eyebrow d-none" data-detail-modal-eyebrow></div>
                    <h3 class="availability-modal__title" id="hotelRateDetailModalLabel">
                        <i class="fa fa-check-circle-o" aria-hidden="true" data-detail-modal-icon></i>
                        <span data-detail-modal-title>@lang('messages.Include')</span>
                    </h3>
                </div>

                <div class="availability-modal__body content" data-detail-modal-body></div>

                <div class="availability-modal__footer">
                    <button type="button" class="btn btn-outline-secondary availability-modal__close" data-bs-dismiss="modal">
                        <i class="fa fa-close" aria-hidden="true"></i> @lang('messages.Close')
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
