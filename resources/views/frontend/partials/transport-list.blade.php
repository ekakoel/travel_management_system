@forelse ($transports as $transport)
    <div class="car-card">
        <img src="{{ asset('storage/transports/transports-cover/' . $transport->cover) }}" alt="{{ $transport->name }}">
        <div class="car-info">
            <h5>{{ $transport->brand." ".$transport->name }}</h5>
            <p>👨🏻‍✈️ @lang('messages.Driver')</p>
            <p>❄️ @lang('messages.Air Conditioner')</p>
            <p>⚙️ @lang('messages.Automatics')</p>
            <p>👥 {{ $transport->capacity }} @lang('messages.passengers')</p>
        </div>
        <div class="car-price">
            @php
                $price = $transport->selected_price ?? $transport->prices->first();
            @endphp
            <div class="price-type">@lang('messages.'.$price->type)</div>
            <p> {!! $price->type == "Daily Rent" ? "<i class='icon-copy fa fa-map' aria-hidden='true'></i> ".__('messages.Bali Area'): $price->dst." ↔ ".__('messages.Airport') !!}</p>
            <div class="price-value">{!! currencyFormatUsd($price->calculatePrice($usdrates, $tax)) !!}</div>
            <div class="price-time">  {{ $price->type == "Daily Rent" ? "/ ".$price->duration." ". __('messages.hours'): "/ ".$price->duration." ".__('messages.hours'); }}</div>
            <a href="{{ route('view.view-order-transport', [$transport->id, $price->id]) }}">
                <button type="submit" form="orderTransport{{ $transport->id }}" class="btn btn-success w-100">@lang('messages.BOOK NOW')</button>
            </a>
        </div>
    </div>
@empty
    <p>@lang('messages.No transport found')</p>
@endforelse



