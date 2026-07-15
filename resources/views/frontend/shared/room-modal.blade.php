<!-- Room Image Modal -->
<div
    class="modal fade"
    id="roomModal"
    tabindex="-1"
    aria-labelledby="roomModalLabel"
    aria-hidden="true"
    data-label-booking-period="@lang('messages.Booking Period')"
    data-label-stay-period="@lang('messages.Stay Period')"
    data-label-duration="@lang('messages.Duration')"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="roomModalTitle">Room Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="roomModalImage" src="" class="img-fluid rounded" alt="Room Image" loading="lazy">
                <div id="roomModalDetails" class="room-modal-details text-start">
                    <div id="roomModalPromos" class="room-modal-section d-none">
                        <div class="room-modal-section__eyebrow">@lang('messages.Promotions')</div>
                        <div class="room-modal-section__list"></div>
                    </div>
                    <div id="roomModalPackages" class="room-modal-section d-none">
                        <div class="room-modal-section__eyebrow">@lang('messages.Packages')</div>
                        <div class="room-modal-section__list"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
