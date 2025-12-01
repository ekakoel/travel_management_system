<div class="col-6">
    <a href="#" data-toggle="modal" data-target="#promo-price-benefits-{{ $hotel_promotion->id }}">
        <p>
            <i class="icon-copy fa fa-eye" aria-hidden="true"></i> @lang('messages.Benefits')
        </p>
    </a>
    <div class="modal fade" id="promo-price-benefits-{{ $hotel_promotion->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="card-box">
                    <div class="card-box-title">
                        <i class="icon-copy fa fa-check-circle-o" aria-hidden="true"></i> @lang('messages.Benefits')
                    </div>
                    <div class="card-box-body">
                        @if (config('app.locale') == "zh")
                            @if ($hotel_promotion->benefits_traditional != "")
                                {!! $hotel_promotion->benefits_traditional !!}
                            @else
                                {!! $hotel_promotion->benefits !!}
                            @endif
                        @elseif (config('app.locale') == "zh-CN")
                            @if ($hotel_promotion->benefits_simplified != "")
                                {!! $hotel_promotion->benefits_simplified !!}
                            @else
                                {!! $hotel_promotion->benefits !!}
                            @endif
                        @else
                            {!! $hotel_promotion->benefits !!}
                        @endif
                    </div>
                    <div class="card-box-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>