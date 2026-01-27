<div class="col-md-8">
    <div class="card-box">
        <div class="card-box-title">
            <div class="subtitle"><i class="fa fa-pencil"></i> @lang('messages.Edit Order')</div>
        </div>
        <div class="row">
            <div class="col-6 col-md-6">
                <div class="order-bil text-left">
                    <img src="{{ asset(config('app.logo_dark')) }}" alt="{{ config('app.alt_logo') }}">
                </div>
            </div>
            <div class="col-6 col-md-6 flex-end">
                <div class="label-title">@lang('messages.Order')</div>
            </div>
            <div class="col-md-12 text-right">
                <div class="label-date float-right" style="width: 100%">
                    {{ dateFormat($order->created_at) }}
                </div>
            </div>
        </div>
        <div class="business-name">{{ $business->name }}</div>
        <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
        <hr class="form-hr">
        <div class="row">
            <div class="col-md-6">
                <table class="table tb-list">
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Order No')
                        </td>
                        <td class="htd-2">
                            <b>{{ $order->orderno }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Order Date')
                        </td>
                        <td class="htd-2">
                            {{ dateFormat($order->created_at) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Service')
                        </td>
                        <td class="htd-2">
                            @lang('messages.'.$order->service)
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                @if ($order->status == "Active")
                    <div class="page-status" style="color: rgb(0, 156, 21)"> @lang('messages.Confirmed') <span>@lang('messages.Status'):</span></div>
                @elseif ($order->status == "Pending")
                    <div class="page-status" style="color: #dd9e00">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                @elseif ($order->status == "Rejected")
                    <div class="page-status" style="color: rgb(160, 0, 0)">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                @else
                    <div class="page-status" style="color: rgb(48, 48, 48)">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                @endif
            </div>
        </div>
        {{-- ORDER  --}}
        <div class="page-subtitle">@lang('messages.Order')</div>
        <div class="row">
            <div class="col-md-6">
                <table class="table tb-list">
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Transport')
                        </td>
                        <td class="htd-2">
                            {{ $transport->brand." ".$transport->name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Type')
                        </td>
                        <td class="htd-2">
                            @lang('messages.Transport') ({{ $order->subservice }})
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Capacity')
                        </td>
                        <td class="htd-2">
                            {{ $order->capacity." Seat" }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table tb-list">
                    @if ($order->subservice == "Airport SHuttle")
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Src') - @lang('messages.Dst')
                            </td>
                            <td class="htd-2">
                                {{ $order->pickup_location." - ".$order->dropoff_location }}
                            </td>
                        </tr>
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Duration')
                            </td>
                            <td class="htd-2">
                                {{ $order->duration }} {{ $order->subservice == "Daily Rent" ? __('messages.days') : __('messages.hours') }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Extra Time')
                        </td>
                        <td class="htd-2">
                            {{ $order->extra_time."% / hours" }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @if ($order->destinations != "")
            <div class="page-text">
                <hr class="form-hr">
                <b>@lang('messages.Destinations') :</b> <br>
                {!! $order->destinations !!}
            </div>
        @endif
        @if ($order->itinerary != "")
            <div class="page-text">
                <hr class="form-hr">
                <b>@lang('messages.Itinerary') :</b> <br>
                {!! $order->itinerary !!}
            </div>
        @endif
        @if ($order->include != "")
            <div class="page-text">
                <hr class="form-hr">
                <b>@lang('messages.Include') :</b> <br>
                {!! $order->include !!}
            </div>
        @endif
        @if ($order->additional_info != "")
            <div class="page-text">
                <hr class="form-hr">
                <b>@lang('messages.Additional Information') :</b> <br>
                {!! $order->additional_info !!}
            </div>
        @endif
        @if ($order->cancellation_policy != "")
            <div class="page-text">
                <hr class="form-hr">
                <b>@lang('messages.Cancelation Policy') :</b>
                <p>{!! $order->cancellation_policy !!}</p>
            </div>
        @endif
        <form id="submitOrderTransport" action="{{ route('func.submit.order-transport',$order->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            {{-- GUESTS  --}}
            <div class="page-subtitle">@lang('messages.Details')</div>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pickup_name">@lang('messages.Name')</label>
                                <input type="text" name="pickup_name" class="form-control @error('pickup_name') is-invalid @enderror" placeholder="{{ $order->pickup_name }}" value="{{ $order->pickup_name }}" required>
                                @error('pickup_name')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pickup_phone">@lang('messages.Telephone')</label>
                                <input type="text" name="pickup_phone" class="form-control @error('pickup_phone') is-invalid @enderror" placeholder="{{ $order->pickup_phone }}" value="{{ $order->pickup_phone }}" required>
                                @error('pickup_phone')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="number_of_guests">@lang('messages.Number of Guest') </label>
                                    <input name="number_of_guests" min="1" max="{{ $transport->capacity }}" class="form-control @error('number_of_guests') is-invalid @enderror" placeholder="@lang('messages.Maximum') {{ $transport->capacity }} @lang('messages.Guests')" type="number" value="{{ $order->number_of_guests }}" required>
                                @error('number_of_guests')
                                    <span class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        @if ($price->type == "Daily Rent")
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="duration" >@lang('messages.Duration') </label>
                                        <input name="duration" min="1" class="form-control @error('duration') is-invalid @enderror" placeholder="@lang('messages.Insert duration by day')" type="number" value="{{ $order->duration }}" required>
                                    @error('duration')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <input type="hidden" name="order_type" value="{{ $price->type }}">
                        @else
                            <input type="hidden" name="duration" value="{{ $price->duration }}">
                            <input type="hidden" name="order_type" value="{{ $price->type }}">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="airport_shuttle_type">@lang('messages.Type')<span> *</label>
                                    <select name="airport_shuttle_type" id="airport_shuttle_type" class="custom-select @error('airport_shuttle_type') is-invalid @enderror">
                                        <option {{ $order->service_type == "Arrival" ? "selected" : ""; }} value="Arrival">@lang('messages.Arrival')</option>
                                        <option {{ $order->service_type == "Departure" ? "selected" : ""; }} value="Departure">@lang('messages.Departure')</option>
                                    </select>
                                    @error('airport_shuttle_type')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @if ($price->type == "Daily Rent")
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pickup_date"> @lang('messages.Pickup Date')</label>
                                    <input id="pickup_date" name="pickup_date" type="text" class="form-control datetimepicker @error('pickup_date') is-invalid @enderror" value="{{ dateTimeFormat($order->pickup_date) }}" required>
                                    @error('pickup_date')
                                        <div class="alert alert-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pickup_location"> @lang('messages.Pickup Location')</label>
                            <input id="pickup_location" name="pickup_location" type="text" class="form-control  @error('pickup_location') is-invalid @enderror" value="{{ $order->pickup_location }}" required>
                            @error('pickup_location')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dropoff_location"> @lang('messages.Dropoff Location')</label>
                            <input id="dropoff_location" name="dropoff_location" type="text" class="form-control  @error('dropoff_location') is-invalid @enderror" value="{{ $order->dropoff_location }}" required>
                            @error('dropoff_location')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                @elseif($price->type == "Airport Shuttle")
                    <div class="col-md-12">
                        <div id="arrival_fields" class="row" style="display: {{ $order->service_type == "Departure" ? "none" : "flex"; }};">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="arrival_flight">@lang('messages.Arrival Flight')</label>
                                    <input type="text" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror" placeholder="@lang('messages.Arrival Flight')" value="{{ $order->arrival_flight }}">
                                    @error('arrival_flight')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="arrival_time">@lang('messages.Arrival Date and Time')</label>
                                    <input readonly type="text" name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ dateTimeFormat($order->arrival_time) }}">
                                    @error('arrival_time')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div id="departure_fields" class="row" style="display: {{ $order->service_type == "Arrival" ? "none" : "flex"; }};">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="departure_flight">@lang('messages.Departure Flight')</label>
                                    <input type="text" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror" placeholder="@lang('messages.Departure Flight')" value="{{ $order->departure_flight }}">
                                    @error('departure_flight')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="departure_time">@lang('messages.Departure Date and Time')</label>
                                    <input readonly type="text" name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ $order->departure_time }}">
                                    @error('departure_time')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="guest_detail"> @lang('messages.Guest Detail')</label>
                        <textarea id="guest_detail" name="guest_detail" placeholder="@lang('messages.Optional')" class="form-control border-radius-0">{!! $order->guest_detail !!}</textarea>
                        @error('guest_detail')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="page-subtitle">@lang('messages.Remark')</div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <textarea id="note" name="note" placeholder="@lang('messages.Optional')" class="textarea_editor form-control border-radius-0">{!! $order->note !!}</textarea>
                        @error('note')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="page-subtitle">@lang('messages.Price')</div>
            <div class="row">
                <div class="col-md-12 m-b-8">
                    <div class="box-price-kicked">
                        <div class="row">
                            <div class="col-6 col-md-6">
                                @if ($order->bookingcode_disc > 0 || $order->discounts > 0 || $order->kick_back > 0 || $promotion_discount > 0)
                                    <div class="modal-text-price">@lang('messages.Normal Price')</div>
                                    @if ($order->bookingcode_disc > 0)
                                        <div class="normal-text">@lang('messages.Booking Code')</div>
                                    @endif
                                    @if ($order->discounts > 0)
                                        <div class="normal-text">@lang('messages.Discounts')</div>
                                    @endif
                                    @if ($promotion_discount > 0)
                                        <div class="normal-text">@lang('messages.Promotion')</div>
                                    @endif
                                    <hr class="form-hr">
                                @endif
                                <div class="price-name">@lang('messages.Total Price')</div>
                            </div>
                            <div class="col-6 col-md-6 text-right">
                                @if ($order->bookingcode_disc > 0 || $order->discounts > 0 || $order->kick_back > 0 || $promotion_discount > 0)
                                    <div class="modal-num-price"><span id="normal_price">{{ currencyFormatUsd($order->normal_price) }}</span></div>
                                    @if ($order->bookingcode_disc > 0)
                                        <div class="promo-text">{{ currencyFormatUsd($order->bookingcode_disc) }}</div>
                                    @endif
                                    @if ($order->discounts > 0)
                                        <div class="promo-text">{{ currencyFormatUsd($order->discounts) }}</div>
                                    @endif
                                    @if ($promotion_discount > 0)
                                        <div class="promo-text">{{ currencyFormatUsd($promotion_discount) }}</div>
                                    @endif
                                    <hr class="form-hr">
                                @endif
                                <div class="price-tag"><span id="final_price">{{ currencyFormatUsd($order->final_price) }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 ">
                    <div class="notif-modal text-left">
                        @if ($order->status == "Draft")
                            @if (Auth::user()->email == "" or Auth::user()->phone == "" or Auth::user()->office == "" or Auth::user()->address == "" or Auth::user()->country == "")
                                @lang('messages.Please complete your profile data first to be able to submit orders, by clicking this link') -> <a href="/profile">@lang('messages.Edit Profile')</a>
                            @else
                                @if ($order->status == "Invalid")
                                    @lang('messages.This order is invalid, please make sure all data is correct!')
                                @else
                                    @lang('messages.Please make sure all the data is correct before you submit the order!')
                                @endif
                            @endif
                        @elseif ($order->status == "Pending")
                            @lang('messages.We have received your order, we will contact you as soon as possible to validate the order!')
                        @elseif ($order->status == "Rejected")
                            {{ $order->msg }}
                        @elseif ($order->status == "Invalid")
                            {{ $order->msg }}
                        @endif
                    </div>
                </div>
            </div>
        </Form>
        <div class="card-box-footer">
            @if ($order->status == "Draft")
                <div class="form-group">
                    @if ($order->status != "Invalid")
                        @if (Auth::user()->email == "" or Auth::user()->phone == "" or Auth::user()->office == "" or Auth::user()->address == "" or Auth::user()->country == "")
                            <button type="button" class="btn btn-light"><i class="icon-copy fa fa-info" aria-hidden="true"> </i> @lang('messages.You cannot submit this order')</button>
                        @else
                            <button type="submit" form="submitOrderTransport" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Submit')</button>
                        @endif
                    @endif
                    <a href="/orders">
                        <button class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                    </a>
                </div>
            @elseif ($order->status == "Rejected")
                <form id="removeOrder" class="hidden" action="/fremove-order/{{ $order->id }}"method="post" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <input type="hidden" name="status" value="Removed">
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                </form>
                <button type="submit" form="removeOrder" class="btn btn-danger"><i class="icon-copy fa fa-trash-o" aria-hidden="true"></i> @lang('messages.Delete')</button>
            @else
                <div class="form-group">
                    <a href="/orders">
                        <button type="button" class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
<div class="loading-icon hidden pre-loader">
    <div class="pre-loader-box">
        <div class="sys-loader-logo w3-center"> <img class="w3-spin" src="{{ asset('storage/icon/spinner.png') }}" alt="Bali Kami Tour Logo"></div>
        <div class="loading-text">
            Submitting an Order...
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
    $("#submitOrderTransport").submit(function() {
        $(".result").text("");
        $(".loading-icon").removeClass("hidden");
        $(".submit").attr("disabled", true);
        $(".btn-txt").text("Processing ...");
    });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectShuttleType = document.getElementById("airport_shuttle_type");
        const arrivalFields = document.getElementById("arrival_fields");
        const departureFields = document.getElementById("departure_fields");
        function toggleFields() {
            if (selectShuttleType.value === "Arrival") {
                arrivalFields.style.display = "flex";
                departureFields.style.display = "none";
            } else {
                arrivalFields.style.display = "none";
                departureFields.style.display = "flex";
            }
        }
        toggleFields();
        selectShuttleType.addEventListener("change", toggleFields);
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const durationInput = document.querySelector('input[name="duration"]');
        const orderTypeInput = document.querySelector('input[name="order_type"]');
        const finalPriceSpan = document.getElementById('final_price');
        const NormalPriceSpan = document.getElementById('normal_price');
        const bookingCodeDiscount = document.getElementById('booking_code_discount');
        const promotionPrice = document.getElementById('promotion_price');


        const transportPrice = @json($order->price_pax);
        const bookingDiscount = @json(session('bookingcode') ? session('bookingcode')->discounts : 0);
        const promotion = @json($promotion_discount);
        function calculateFinalPrice() {
            let duration = parseInt(durationInput.value) || 1;
            let orderType = orderTypeInput ? orderTypeInput.value : "";

            let totalPrice;
            if (orderType === "Daily Rent") {
                totalPrice = (transportPrice * duration) - bookingDiscount - promotion;
                normalPrice = transportPrice * duration;
            } else {
                totalPrice = transportPrice - bookingDiscount - promotion;
                normalPrice = transportPrice;
            }

            totalPrice = totalPrice < 0 ? 0 : totalPrice;

            finalPriceSpan.textContent = totalPrice.toLocaleString("en-US", {
                style: "currency",
                currency: "USD",
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            NormalPriceSpan.textContent = normalPrice.toLocaleString("en-US", {
                style: "currency",
                currency: "USD",
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
        calculateFinalPrice();
        durationInput.addEventListener('input', calculateFinalPrice);
        if (bookingDiscount === 0 && bookingCodeDiscount) {
            bookingCodeDiscount.style.display = 'none';
            const bookingCodeLabel = document.getElementById('booking_code_label');
            if (bookingCodeLabel) bookingCodeLabel.style.display = 'none';
        }
        if (promotion === 0 && promotionPrice) {
            promotionPrice.style.display = 'none';
            const promotionLabel = document.getElementById('promotion_label');
            if (promotionLabel) promotionLabel.style.display = 'none';
        }
    });
</script>