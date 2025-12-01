@php
    $prices = $transport->prices;
@endphp

@if ($prices->first() && $prices->first()->type == 'Daily Rent')
    @php $price = $prices->first(); @endphp
    <div class="car-card">
        <div class="car-info">
            <div class="price-type">@lang('messages.' . $price->type)</div>
        </div>
        <div class="car-info">
            <p>± {{ $price->duration }} {{ __('messages.hours') }}</p>
        </div>
        <div class="car-info">
            <div class="price-value"><b>{!! currencyFormatUsd($price->calculatePrice($usdrates, $tax)) !!}</b></div>
        </div>
        <div class="car-price align-selft-end">
            <a href="{{ route('view.view-order-transport', [$transport->id, $price->id]) }}">
                <button type="submit" class="btn btn-success w-100">@lang('messages.BOOK NOW')</button>
            </a>
        </div>
    </div>
@else
    @forelse ($prices as $no=>$price)
        <div class="car-card">
            @if (count($prices) > 1)    
                <div class="car-info" style="max-width: max-content;">
                    <p><b>{{ ++$no }}</b></p>
                </div>
            @endif
            <div class="car-info">
                <p>{{ $price->dst }} ↔ @lang('messages.Airport')</p>
            </div>
            <div class="car-info">
                <p>± {{ $price->duration }} {{ __('messages.hours') }}</p>
            </div>
            <div class="car-info" style="max-width: max-content;">
                <div class="price-type"><b>{!! currencyFormatUsd($price->calculatePrice($usdrates, $tax)) !!}</b></div>
            </div>
            <div class="car-price align-selft-end">
                <a href="{{ route('view.view-order-transport', [$transport->id, $price->id]) }}">
                    <button type="submit" class="btn btn-success w-100">@lang('messages.BOOK NOW')</button>
                </a>
            </div>
        </div>
    @empty
        <div class="car-card">
            <p class="text-danger">@lang('messages.No transport found')!</p>
        </div>
    @endforelse
@endif
