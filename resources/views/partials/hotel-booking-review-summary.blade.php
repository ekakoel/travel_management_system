<div class="booking-review">
    <div class="booking-review__grid">
        <section class="booking-review__card">
            <div class="booking-review__card-head">
                <div class="booking-review__eyebrow">@lang('messages.Stay')</div>
                <h3 class="booking-review__title">@lang('messages.Stay and guests')</h3>
            </div>
            <div class="booking-review__facts">
                <div class="booking-review__fact">
                    <span>@lang('messages.Hotel')</span>
                    <strong data-review-hotel>-</strong>
                </div>
                <div class="booking-review__fact">
                    <span>@lang('messages.Room')</span>
                    <strong data-review-room>-</strong>
                </div>
                <div class="booking-review__fact">
                    <span>@lang('messages.Dates')</span>
                    <strong data-review-dates>-</strong>
                </div>
                <div class="booking-review__fact">
                    <span>@lang('messages.Length of stay')</span>
                    <strong data-review-duration>-</strong>
                </div>
                <div class="booking-review__fact">
                    <span>@lang('messages.Total rooms')</span>
                    <strong data-review-room-count>1</strong>
                </div>
                <div class="booking-review__fact">
                    <span>@lang('messages.Total guests')</span>
                    <strong data-review-guest-count>0</strong>
                </div>
            </div>
        </section>

        <section class="booking-review__card">
            <div class="booking-review__card-head">
                <div class="booking-review__eyebrow">@lang('messages.Guests')</div>
                <h3 class="booking-review__title">@lang('messages.Rooming list')</h3>
            </div>
            <div class="booking-review__rooms" data-review-room-list>
                <div class="booking-review__empty">@lang('messages.Add guest names and rooming details to review them here.')</div>
            </div>
        </section>

        <section class="booking-review__card" data-review-airport-shuttle-card>
            <div class="booking-review__card-head">
                <div class="booking-review__eyebrow">@lang('messages.Airport Shuttle')</div>
                <h3 class="booking-review__title">@lang('messages.Airport Shuttle')</h3>
            </div>
            <div class="booking-review__rooms" data-review-transfer-list>
                <div class="booking-review__empty">@lang('messages.Not added')</div>
            </div>
        </section>
    </div>
</div>
