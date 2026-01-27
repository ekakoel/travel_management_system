<div class="bookingcode-card">
    <div class="icon-card promotion">
        <i class="fa fa-bullhorn" aria-hidden="true"></i>
    </div>
    <div class="content-card">
        <div class="code">{{ $promotion->name }}</div>
        <div class="text-card">@lang('messages.Promo Period')</div>
        <div class="text-card">
            {{ dateFormat($promotion->periode_start) . ' - ' . dateFormat($promotion->periode_end) }}
        </div>
    </div>
    <div class="content-card-promo">
        <div class="price"><span>$</span>{{ $promotion->discounts }}</div>
        <button class="btn-remove-code" data-toggle="tooltip" data-placement="top" title='@lang('messages.Ongoing promotion'){{ ' ' . $promotion->name . ' ' }}@lang('messages.and get discounts'){{ " $" . $promotion->discounts . ' ' }}@lang('messages.until'){{ ' ' . dateFormat($promotion->periode_end) }}'><i class="fa fa-question" aria-hidden="true"></i></button>
    </div>
</div>