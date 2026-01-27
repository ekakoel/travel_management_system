@if (count($promotions)>0 or isset($bookingcode))
    <div class="col-12 promotion-bookingcode">
        @if (isset($bookingcode))
            <div class="bookingcode-card">
                <div class="icon-card bookingcode">
                    <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                </div>
                <div class="content-card">
                    <div class="code">{{ $bookingcode->code }}</div>
                    <div class="text-card">@lang('messages.Booking Code') @lang('messages.Aplied')</div>
                    <div class="text-card">@lang('messages.Expired') {{ date('d M y', strtotime($bookingcode->expired_date)) }}</div>
                </div>
                <div class="content-card-price">
                    <div class="price"><span>$</span>{{ $bookingcode->discounts }}</div>
                    <form action="/search-tours" method="POST" role="search";>
                        {{ csrf_field() }}
                        <button type="submit" class="btn-remove-code"><i class="fa fa-close" aria-hidden="true"></i></button>
                    </form>
                </div>
            </div>
            @if (count($promotions)>0)
                @foreach ($promotions as $promotion)
                    <div class="bookingcode-card">
                        <div class="icon-card promotion">
                            <i class="fa fa-bullhorn" aria-hidden="true"></i>
                        </div>
                        <div class="content-card">
                            <div class="code">{{ $promotion->name }}</div>
                            <div class="text-card">@lang('messages.Promo Period')</div>
                            <div class="text-card">{{ date('d M y', strtotime($promotion->periode_start))." - ".date('d M y', strtotime($promotion->periode_end)) }}</div>
                        </div>
                        <div class="content-card-promo">
                            <div class="price"><span>$</span>{{ $promotion->discounts }}</div>
                            <button class="btn-remove-code" data-toggle="tooltip" data-placement="top" title='@lang('messages.Ongoing promotion'){{" ". $promotion->name." "}}@lang('messages.and get discounts'){{ " $".$promotion->discounts." " }}@lang('messages.until'){{ " ". date('d M y',strtotime($promotion->periode_end)) }}'><i class="fa fa-question" aria-hidden="true"></i></button>
                        </div>
                    </div>
                @endforeach
            @endif
        @else
            @if (count($promotions)>0)
                @foreach ($promotions as $promotion)
                    <div class="bookingcode-card">
                        <div class="icon-card promotion">
                            <i class="fa fa-bullhorn" aria-hidden="true"></i>
                        </div>
                        <div class="content-card">
                            <div class="code">{{ $promotion->name }}</div>
                            <div class="text-card">@lang('messages.Promo Period')</div>
                            <div class="text-card">{{ date('d M y', strtotime($promotion->periode_start))." - ".date('d M y', strtotime($promotion->periode_end)) }}</div>
                        </div>
                        <div class="content-card-promo">
                            <div class="price"><span>$</span>{{ $promotion->discounts }}</div>
                            <button class="btn-remove-code" data-toggle="tooltip" data-placement="top" title='@lang('messages.Ongoing promotion'){{" ". $promotion->name." "}}@lang('messages.and get discounts'){{ " $".$promotion->discounts." " }}@lang('messages.until'){{ " ". date('d M y',strtotime($promotion->periode_end)) }}'><i class="fa fa-question" aria-hidden="true"></i></button>
                        </div>
                    </div>
                @endforeach
            @endif
        @endif
    </div>
@endif
