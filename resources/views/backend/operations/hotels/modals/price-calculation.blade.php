<div class="modal fade hotel-detail-modal hotel-price-calculation-modal" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <section class="backend-modal">
                <div class="backend-modal__header">
                    <div>
                        <span>{{ $eyebrow }}</span>
                        <h3 id="{{ $modalId }}Title">{{ $title }}</h3>
                        @if (!empty($subtitle))
                            <p>{{ $subtitle }}</p>
                        @endif
                    </div>
                    <button type="button" class="backend-modal__close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="backend-modal__body">
                    @include('backend.operations.hotels.partials.price-breakdown', ['pricing' => $pricing])
                </div>
                <div class="backend-modal__footer">
                    <button type="button" class="backend-toolbar-action" data-dismiss="modal">Close</button>
                </div>
            </section>
        </div>
    </div>
</div>
