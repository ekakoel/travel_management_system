<article class="availability-rate-card">
    <div class="availability-rate-card__image">
        <img
            src="{{ $card['room_cover'] ? getThumbnail('/hotels/hotels-room/' . $card['room_cover'], 380, 200) : asset('storage/images/default.webp') }}"
            class="img-fluid rounded thumbnail-image"
            alt="{{ $card['room_name'] }}"
            loading="lazy"
            decoding="async"
        >

        @if (!empty($card['badges']))
            <div class="availability-badges">
                @foreach ($card['badges'] as $badge)
                    <span class="{{ $badge['class'] }}">{{ $badge['label'] }}</span>
                @endforeach
            </div>
        @endif
    </div>

    <div class="availability-rate-card__body">
        <div class="availability-rate-card__main">
            <div class="availability-rate-card__header">
                <div>
                    <div class="availability-rate-card__eyebrow">{{ $card['offer_label'] }}</div>
                    <h3 class="availability-rate-card__title">{{ $card['room_name'] }}</h3>
                </div>
            </div>

            <div class="availability-rate-card__facts">
                <div class="availability-rate-card__fact">
                    <span class="availability-rate-card__fact-icon" aria-hidden="true">
                        <i class="fa fa-users"></i>
                    </span>
                    <span>{{ $card['occupancy']['label'] }}</span>
                </div>
                <div class="availability-rate-card__fact">
                    <span class="availability-rate-card__fact-icon" aria-hidden="true">
                        <i class="fa fa-moon-o"></i>
                    </span>
                    <span>{{ $card['meta_label'] }}</span>
                </div>
            </div>

            @if (!empty($card['inline_notes']))
                <div class="availability-inline-notes">
                    @foreach ($card['inline_notes'] as $note)
                        <div class="availability-inline-note">{{ $note }}</div>
                    @endforeach
                </div>
            @endif

            @if (!empty($card['details_title']) || !empty($card['detail_partials']))
                <div class="availability-rate-card__details-stack">
                    @if (!empty($card['details_title']))
                        <div class="availability-details availability-details--headline">
                            <strong>{{ $card['details_title'] }}</strong>
                        </div>
                    @endif

                    @foreach ($card['detail_partials'] as $detail)
                            @if (!empty($detail['label']))
                                <div class="availability-details__label">{{ $detail['label'] }}</div>
                            @endif
                            @include($detail['view'], $detail['data'])
                    @endforeach
                </div>
            @endif
        </div>

        <aside class="availability-rate-card__pricepanel{{ $card['panel_variant'] === 'compact' ? ' availability-rate-card__pricepanel--compact' : '' }}">
            @if (count($card['nightly_rates']) > 0)
                <div class="availability-rate-card__pricehead">
                    <div class="availability-rate-card__priceeyebrow">{{ $card['price_heading'] }}</div>
                    <div class="availability-rate-card__pricemeta">{{ count($card['nightly_rates']) }} @lang('messages.nights selected')</div>
                </div>

                <div class="availability-night-grid">
                    @foreach ($card['nightly_rates'] as $night)
                        <div class="availability-night-chip">
                            <div class="availability-night-chip__date">{{ $night['short_date'] }}</div>
                            <div class="availability-night-chip__price {{ $night['price_class'] }}">${{ number_format($night['price']) }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="availability-rate-card__totals">
                @foreach ($card['totals'] as $total)
                    <div class="availability-total{{ $total['strikethrough'] ? ' availability-total--strikethrough' : '' }}">
                        <span class="availability-total__label">{{ $total['label'] }}</span>
                        <strong class="availability-total__value{{ $total['muted'] ? ' availability-total__value--muted' : '' }}">
                            {{ currencyFormatUsd($total['value']) }}
                        </strong>
                    </div>
                @endforeach
            </div>

            @if (!empty($card['footnote']))
                <div class="availability-package-note">{{ $card['footnote'] }}</div>
            @endif

            @if (!empty($card['booking_action']))
                @php
                    $bookingAction = $card['booking_action'];
                    $bookingFormId = 'availability-booking-' . $card['category'] . '-' . $card['room']->id . '-' . md5($card['room_name'] . $card['sort_price']);
                @endphp
                <div class="availability-booking-cta">
                    <form
                        id="{{ $bookingFormId }}"
                        action="{{ route($bookingAction['route'], $bookingAction['route_parameter']) }}"
                        method="{{ strtoupper($bookingAction['method'] ?? 'POST') === 'GET' ? 'GET' : 'POST' }}"
                    >
                        @csrf
                        @foreach (($bookingAction['fields'] ?? []) as $fieldName => $fieldValue)
                            @if (!is_null($fieldValue))
                                <input type="hidden" name="{{ $fieldName }}" value="{{ $fieldValue }}">
                            @endif
                        @endforeach
                    </form>

                    <button type="submit" form="{{ $bookingFormId }}" class="btn btn-primary availability-booking-cta__button">
                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                        {{ $bookingAction['label'] }}
                    </button>

                    @if (!empty($bookingAction['helper']))
                        <p class="availability-booking-cta__helper">{{ $bookingAction['helper'] }}</p>
                    @endif
                </div>
            @endif
        </aside>
    </div>
</article>
