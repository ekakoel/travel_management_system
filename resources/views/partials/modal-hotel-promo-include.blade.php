<a href="#" data-toggle="modal" data-target="#promo-price-include-{{ $hotel_promotion->id }}">
    <p>
        <i class="icon-copy fa fa-eye" aria-hidden="true"></i> {{ $hotel_promotion->promotion_type }} @lang('messages.Include')
    </p>
</a>
<div class="modal fade" id="promo-price-include-{{ $hotel_promotion->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="card-box">
                <div class="card-box-title">
                    <div class="title"><i class="icon-copy fa fa-check-circle-o" aria-hidden="true"></i> @lang('messages.Include')</div>
                </div>
                <div class="content">
                    {!! $hotel_promotion->include !!}
                </div>
                <div class="card-box-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                </div>
            </div>
        </div>
    </div>
</div>