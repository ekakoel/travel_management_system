<div class="promo-inner-modal">
    <img 
        src="{{ asset('/storage/icon/' . strtolower(str_replace(' ', '_', $promotion->promotion_type)) . '_label.png') }}" 
        alt="{{ $promotion->promotion_type }} Promotion">
    <div class="card-text text-left">
        <div class="card-subtitle">@lang('messages.Stay Period')</div>
        <p>{{ date('m/d/Y', strtotime($promotion->periode_start)) }} - {{ date('m/d/Y', strtotime($promotion->periode_end)) }}</p>
        <i>@lang('messages.Min') {{ $promotion->minimum_stay }} @lang('messages.nights stay')</i>
    </div>
</div>
