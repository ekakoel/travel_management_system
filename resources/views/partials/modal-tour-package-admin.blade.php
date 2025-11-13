
<a href="#" data-toggle="modal" data-target="#promo-price-include-{{ $tour->id }}">
    <i class="dw dw-eye"></i>
</a>
<div class="modal fade" id="promo-price-include-{{ $tour->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="card-box">
                <div class="card-box-title text-left">
                    <div class="title"><i class="icon-copy fa fa-check-circle-o" aria-hidden="true"></i> {{ $tour->name." (".$tour->code.')' }}</div>
                </div>
                <div class="content">
                    <div class="card-banner m-b-18">
                        <img src="{{ asset('/storage/tours/tours-cover/' . $tour->cover) }}" alt="">
                    </div>
                    <div class="m-b-18">
                        {!! $tour->description !!}
                    </div>
                    <div class="card-subtitle">Destination</div>
                    <div class="m-b-18">
                        {!! $tour->destination !!}
                    </div>
                    <div class="modal-galery">
                        @foreach ($tour->images as $image)
                            <img src="{{ asset('/storage/tours/tours-galery/' . $image->image) }}" alt="">
                            
                        @endforeach
                    </div>
                    <div class="card-subtitle">Itinerary</div>
                    <div class="m-b-18">
                        {!! $tour->itinerary !!}
                    </div>
                    <div class="card-subtitle">Inclusions</div>
                    <div class="m-b-18">
                        {!! $tour->include !!}
                    </div>
                    <div class="card-subtitle">Exclusions</div>
                    <div class="m-b-18">
                        {!! $tour->exclude !!}
                    </div>
                    @if ($tour->additional_info)
                        <div class="card-subtitle">Additional Information</div>
                        <div class="m-b-18">
                            {!! $tour->additional_info !!}
                        </div>
                    @endif
                    @if ($tour->cancellation_policy)
                        <div class="card-subtitle">Cancellation Policy</div>
                        <div class="m-b-18">
                            {!! $tour->cancellation_policy !!}
                        </div>
                    @endif
                    @if ($tour->terms_and_conditions)
                        <div class="card-subtitle">Terms and Conditions</div>
                        <div class="m-b-18">
                            {!! $tour->terms_and_conditions !!}
                        </div>
                    @endif
                    <div class="card-subtitle">Prices</div>
                    <div class="row">
                        @foreach ($tour->prices as $index=>$price)
                            <div class="col-sm-1">{{ ++$index }}.</div>
                            <div class="col-sm-3">{{ $price->min_qty." pax - ".$price->max_qty." pax" }}</div>
                            <div class="col-sm-8">: {{ currencyFormatUsd($price->calculated_price) }}</div>
                        @endforeach
                    </div>
                </div>
                <div class="card-box-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                </div>
            </div>
        </div>
    </div>
</div>