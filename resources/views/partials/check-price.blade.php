<div class="row">
    <div class="col-md-12">
        <div class="card-box {{ session('booking_dates.duration') < $hotel->min_stay?"form-alert":""; }}">
            <div class="card-box-title">
                <div class="subtitle"><i class="icon-copy fa fa-search" aria-hidden="true"></i>@lang('messages.Check Price')</div>
            </div>
            <div class="detail-item p-b-18">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('view.hotel-prices',$hotel->code) }}" method="POST" role="search">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <input readonly id="checkincout" name="checkincout" class="form-control @error('checkincout') is-invalid @enderror" type="text" value="{{ dateFormat($checkin)." - ".dateFormat($checkout) }}" placeholder="@lang('messages.Check In') - @lang('messages.Check Out')" required>
                                @error('checkincout')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                            <input type="hidden" name="hotelcode" value="{{ $hotel->code }}">
                            <div class="row">
                                <div class="col-6">
                                    @if (session('booking_dates.duration') < $hotel->min_stay)
                                        <p class="error-notification"> @lang('messages.Minimum stay') {{ $hotel->min_stay }} @lang('messages.nights')</p>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary" style="float: right;"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Check Price')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>