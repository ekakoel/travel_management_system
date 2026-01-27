<div class="col-md-12">
    <div class="detail-item m-b-18">
        <div class="row">
            <div class="col-md-12">
                <div class="card-subtitle">@lang('messages.Booking Code')</div>
                <hr class="form-hr">
                    <form action="/hotel-{{ $hotel->code }}" method="GET" role="search" style="padding:0px;">
                    <div class="form-group">
                        @if (isset($bookingcode->code))
                            <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ $bookingcode->code }}">
                            <input type="hidden" name="bookingcode_id"  value="{{ $bookingcode->id }}">
                        @else
                            <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ old('bookingcode') }}">
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary" style="float: right;"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Check Code')</button>
                </form>
            </div>
        </div>
    </div>
</div>