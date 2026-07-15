<section class="order-detail-section">
    <div class="order-detail-section__header">
        <div>
            <div class="order-detail-eyebrow">@lang('messages.Price')</div>
            <h2 class="order-detail-section__title">@lang('messages.Price summary')</h2>
        </div>
    </div>
    <div class="order-detail-section__body">
        <div class="order-detail-price-list">
            <div class="order-detail-price-row">
                <span>@lang('messages.Suites and Villas')</span>
                <strong>{{ $isQuotation ? __('messages.To be advised') : currencyFormatUsd($order->price_total) }}</strong>
            </div>
            @if ($optionalServiceTotalPrice > 0)
                <div class="order-detail-price-row">
                    <span>@lang('messages.Additional Charge')</span>
                    <strong>{{ currencyFormatUsd($optionalServiceTotalPrice) }}</strong>
                </div>
            @endif
            @if ($additional_service_total_price > 0)
                <div class="order-detail-price-row">
                    <span>@lang('messages.Additional Service')</span>
                    <strong>{{ currencyFormatUsd($additional_service_total_price) }}</strong>
                </div>
            @endif
            @if ($total_price_airport_shuttle > 0)
                <div class="order-detail-price-row">
                    <span>@lang('messages.Airport Shuttle')</span>
                    <strong>{{ $airport_shuttle_any_zero ? __('messages.To be advised') : currencyFormatUsd($total_price_airport_shuttle) }}</strong>
                </div>
            @endif
            @foreach ($filteredDiscounts as $label => $value)
                <div class="order-detail-price-row order-detail-price-row--discount">
                    <span>@lang("messages.$label")</span>
                    <strong>- {{ currencyFormatUsd($value) }}</strong>
                </div>
            @endforeach
            <div class="order-detail-price-row order-detail-price-row--grand">
                <span>@lang('messages.Total Price')</span>
                <strong>{{ $mainTotalLabel }}</strong>
            </div>
        </div>

    </div>
</section>
