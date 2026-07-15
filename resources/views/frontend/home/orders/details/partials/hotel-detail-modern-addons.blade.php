@if (count($optional_rate_orders) > 0)
    <section class="order-detail-section">
        <div class="order-detail-section__header">
            <div>
                <div class="order-detail-eyebrow">@lang('messages.Additional Charge')</div>
                <h2 class="order-detail-section__title">@lang('messages.Mandatory and optional rates')</h2>
            </div>
        </div>
        <div class="order-detail-section__body">
            <div class="order-detail-table-wrap">
                <table class="order-detail-table">
                    <thead>
                        <tr>
                            <th>@lang('messages.Date')</th>
                            <th>@lang('messages.Service')</th>
                            <th>@lang('messages.Guests')</th>
                            <th>@lang('messages.Price')</th>
                            <th>@lang('messages.Total')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($optional_rate_orders as $order_optional_rate)
                            <tr>
                                <td>{{ dateFormat($order_optional_rate->service_date) }}</td>
                                <td>{{ optional($order_optional_rate->optional_rate)->name }}</td>
                                <td>{{ $order_optional_rate->number_of_guest }}</td>
                                <td>{{ currencyFormatUsd($order_optional_rate->price_pax) }}</td>
                                <td>{{ currencyFormatUsd($order_optional_rate->price_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="order-detail-total-box">
                <div class="order-detail-total-row">
                    <span>@lang('messages.Additional Charge')</span>
                    <strong>{{ currencyFormatUsd($optionalServiceTotalPrice) }}</strong>
                </div>
            </div>
        </div>
    </section>
@endif

@if ($additional_service_total_price > 0)
    <section class="order-detail-section">
        <div class="order-detail-section__header">
            <div>
                <div class="order-detail-eyebrow">@lang('messages.Additional Services')</div>
                <h2 class="order-detail-section__title">@lang('messages.Service add-ons')</h2>
            </div>
        </div>
        <div class="order-detail-section__body">
            <div class="order-detail-table-wrap">
                <table class="order-detail-table">
                    <thead>
                        <tr>
                            <th>@lang('messages.Date')</th>
                            <th>@lang('messages.Service')</th>
                            <th>@lang('messages.Quantity')</th>
                            <th>@lang('messages.Price')</th>
                            <th>@lang('messages.Total')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($additionalServices as $service)
                            <tr>
                                <td>{{ $service['date'] }}</td>
                                <td>{{ $service['service'] }}</td>
                                <td>{{ $service['qty'] }}</td>
                                <td>{{ currencyFormatUsd($service['price']) }}</td>
                                <td>{{ currencyFormatUsd($service['total']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="order-detail-total-box">
                <div class="order-detail-total-row">
                    <span>@lang('messages.Additional Service')</span>
                    <strong>{{ currencyFormatUsd($additional_service_total_price) }}</strong>
                </div>
            </div>
        </div>
    </section>
@endif

@if (count($airport_shuttles) > 0)
    <section class="order-detail-section">
        <div class="order-detail-section__header">
            <div>
                <div class="order-detail-eyebrow">@lang('messages.Airport Shuttle')</div>
                <h2 class="order-detail-section__title">@lang('messages.Flight and transport detail')</h2>
            </div>
        </div>
        <div class="order-detail-section__body">
            <div class="order-detail-table-wrap">
                <table class="order-detail-table">
                    <thead>
                        <tr>
                            <th>@lang('messages.Date')</th>
                            <th>@lang('messages.Flight')</th>
                            <th>@lang('messages.Type')</th>
                            <th>@lang('messages.Transport')</th>
                            <th>@lang('messages.Price')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($airport_shuttles as $airport_shuttle)
                            <tr>
                                <td>{{ $airport_shuttle->date ? dateTimeFormat($airport_shuttle->date) : '-' }}</td>
                                <td>{{ $airport_shuttle->flight_number ?: '-' }}</td>
                                <td>{{ $airport_shuttle->nav === 'In' ? __('messages.Arrival') : __('messages.Departure') }}</td>
                                <td>{{ optional($airport_shuttle->transport)->brand }} {{ optional($airport_shuttle->transport)->name }}</td>
                                <td>{{ $airport_shuttle_any_zero ? __('messages.To be advised') : currencyFormatUsd($airport_shuttle->price) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="order-detail-total-box">
                <div class="order-detail-total-row">
                    <span>@lang('messages.Airport Shuttle')</span>
                    <strong>{{ $airport_shuttle_any_zero ? __('messages.To be advised') : currencyFormatUsd($total_price_airport_shuttle) }}</strong>
                </div>
            </div>
        </div>
    </section>
@endif

@if ($order->note)
    <section class="order-detail-section">
        <div class="order-detail-section__header">
            <div>
                <div class="order-detail-eyebrow">@lang('messages.Note')</div>
                <h2 class="order-detail-section__title">@lang('messages.Travel notes')</h2>
            </div>
        </div>
        <div class="order-detail-section__body">
            <div class="order-detail-rich">{!! $order->note !!}</div>
        </div>
    </section>
@endif
