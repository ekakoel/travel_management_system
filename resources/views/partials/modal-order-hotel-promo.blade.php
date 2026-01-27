<a href="#" data-toggle="modal" data-target="#order-hotel-promo-{{ $hotel_promotion->id }}">
    <button type="submit" class="btn btn-primary w-100"><i class="fa fa-shopping-basket"></i> @lang('messages.Order')</button>
</a>
<div class="modal fade" id="order-hotel-promo-{{ $hotel_promotion->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="card-box">
                <div class="card-box-title text-left">
                    <div class="title"><i class="fa fa-envelope" aria-hidden="true"></i> @lang('messages.Create Order')</div>
                </div>
                <div class="content">
                    <div class="content-header">
                        <h3>@lang('messages.New Order') ORD{{ date('ymd',strtotime($now)) }}.HPP{{ ++$countOrder }}</h3>
                        <p>@lang('messages.Sales') / @lang('messages.Agent') : Eka Koel (Bali Kami Tour) - 01/20/2025</p>
                    </div>
                    <div class="content-body">
                        <div class="c-b-title">@lang('messages.Hotel Details')</div>
                        <div class="c-b-content">
                            <div class="c-b-c-title">@lang('messages.Hotel')</div>
                            <div class="c-b-c-text">{{ $hotel->name }}</div>
                        </div>
                        <div class="c-b-content">
                            <div class="c-b-c-title">@lang('messages.Suites & Villas')</div>
                            <div class="c-b-c-text">{{ $room }}</div>
                        </div>
                        <div class="c-b-content m-b-8">
                            <div class="c-b-c-title">@lang('messages.Promotion')</div>
                            <div class="c-b-c-text">{{ $promotion_name }}</div>
                        </div>
                        <div class="c-b-title">@lang('messages.Order Details')</div>
                        <div class="c-b-content">
                            <div class="c-b-c-title">@lang('messages.Duration')</div>
                            <div class="c-b-c-text">{{ $duration." ".__('messages.nights') }}</div>
                        </div>
                        <div class="c-b-content">
                            <div class="c-b-c-title">@lang('messages.Check-in')</div>
                            <div class="c-b-c-text">{{ dateFormat($checkin) }}</div>
                        </div>
                        <div class="c-b-content m-b-8">
                            <div class="c-b-c-title">@lang('messages.Check-out')</div>
                            <div class="c-b-c-text">{{ dateFormat($checkout) }}</div>
                        </div>
                        <div class="c-b-title">@lang('messages.Prices')</div>
                        <div class="c-b-content">
                            <div class="c-b-c-title">@lang('messages.Suites & Villas')</div>
                            <div class="c-b-c-text">{{ currencyFormatUsd($room_price) }} /@lang('messages.pax')</div>
                        </div>
                    </div>
                    <div class="content-body">
                        <div class="c-b-title">@lang('messages.Please fill the form below')</div>
                        <form id="create-order-promo-{{ $hotel_promotion->id }}" action="{{ route('func.create.order.hotel.promo') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="number_of_room">@lang('messages.Number of Room')</label>
                                        <input type="number" name="number_of_room" class="form-control @error('number_of_room') is-invalid @enderror" placeholder="@lang('messages.Number of Room')" value="{{ old('number_of_room') }}" required>
                                        @error('number_of_room')
                                            <div class="alert alert-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="number_of_guests">@lang('messages.Number of Guests')</label>
                                        <input type="number" name="number_of_guests" class="form-control @error('number_of_guests') is-invalid @enderror" placeholder="@lang('messages.Number of Guests')" value="{{ old('number_of_guests') }}" required>
                                        @error('number_of_guests')
                                            <div class="alert alert-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="arrival_flight">@lang('messages.Arrival Flight')</label>
                                        <input type="text" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror" placeholder="@lang('messages.Arrival Flight')" value="{{ old('arrival_flight') }}">
                                        @error('arrival_flight')
                                            <div class="alert alert-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="arrival_time">@lang('messages.Arrival Date and Time')</label>
                                        <input readonly type="text" name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ old('arrival_time') }}">
                                        @error('arrival_time')
                                            <div class="alert alert-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="departure_flight">@lang('messages.Departure Flight')</label>
                                        <input type="text" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror" placeholder="@lang('messages.Departure Flight')" value="{{ old('departure_flight') }}">
                                        @error('departure_flight')
                                            <div class="alert alert-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="departure_time"> @lang('messages.Departure Date and Time')</label>
                                        <input readonly type="text" name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ old('departure_time') }}">
                                        @error('departure_time')
                                            <div class="alert alert-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="airport_shuttle">@lang('messages.Airport Shuttle')</label>
                                <select name="airport_shuttle" type="text" class="custom-select @error('airport_shuttle') is-invalid @enderror">
                                    <option selected value="">@lang('messages.Select Transport')</option>
                                    @if ($transports)
                                        @foreach ($transports as $transport)
                                                <option value="{{ $transport->id }}">{{ $transport->brand." ".$transport->name." - (".$transport->capacity.")" }}</option>
                                        @endforeach
                                    @else
                                        <option value="Request" data-transportout="1">@lang('messages.Request')</option>
                                    @endif
                                </select>
                                @error('airport_shuttle')
                                    <span class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="remark">@lang('messages.Remark')</label>
                                <textarea name="remark" placeholder="@lang('messages.Optional')" class="tiny_mce form-control border-radius-0" value="{{ old('remark') }}"></textarea>
                                @error('remark')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <input type="hidden" name="orderno" value="ORD{{ date('ymd',strtotime($now)) }}.HPP{{ ++$countOrder }}">
                            <input type="hidden" name="service" value="Hotel Promo">
                            <input type="hidden" name="service_id" value="{{ $hotel->id }}">
                            <input type="hidden" name="service_name" value="{{ $promotion_name }}">
                            <input type="hidden" name="room_id" value="{{ $room_id }}">
                            <input type="hidden" name="promo_id" value="{{ json_encode($promotionId) }}">
                            <input type="hidden" name="promo_name" value="{{ json_encode($promotionName) }}">
                            <input type="hidden" name="checkin" value="{{ $checkin }}">
                            <input type="hidden" name="checkout" value="{{ $checkout }}">
                            <input type="hidden" name="duration" value="{{ $duration }}">
                        </form>
                    </div>
                    <div class="content-footer">
                        <b>@lang('messages.Note'):</b><br>
                        <i>- Please complete this order form according to your requirements and submit it.</i><br>
                        <i>- Your order will be processed and sent via email automatically.</i><br>
                        <i>- Our reservation team will respond to your order as promptly as possible.</i><br>
                        <i>- Your satisfaction is our top priority.</i><br>
                        <i>- Thank you for placing your order with us.</i>
                    </div>
                </div>
                <div class="card-box-footer">
                    <button type="submit" form="create-order-promo-{{ $hotel_promotion->id }}" id="normal-reserve" class="btn btn-primary"><i class="icon-copy fa fa-shopping-basket" aria-hidden="true"></i> @lang('messages.Order')</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                </div>
            </div>
        </div>
    </div>
</div>