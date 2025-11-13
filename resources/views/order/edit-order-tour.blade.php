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
                            @lang('messages.Tour Package')
                        </td>
                        <td class="htd-2">
                            {{ $tour->$langName }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Type')
                        </td>
                        <td class="htd-2">
                            {{ $tour->type?->$langType }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Tour Area')
                        </td>
                        <td class="htd-2">
                            {{ $tour->$langArea }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table tb-list">
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Duration')
                        </td>
                        <td class="htd-2">
                            {{ $tour->duration_days."D" }}{{ $tour->duration_nights>0? " / ".$tour->duration_nights."N":"" }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Tour Start')
                        </td>
                        <td class="htd-2">
                            {{ dateFormat($order->checkin) }}
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Tour End')
                        </td>
                        <td class="htd-2">
                            {{ dateFormat($order->checkout) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @if ($tour->itinerary != "")
            <div class="page-text">
                <hr class="form-hr">
                <b>@lang('messages.Itinerary') :</b> <br>
                {!! $tour->$langItinerary !!}
            </div>
        @endif
        @if ($tour->include != "")
            <div class="page-text">
                <hr class="form-hr">
                <b>@lang('messages.Include') :</b> <br>
                {!! $tour->$langInclude !!}
            </div>
        @endif
        @if ($tour->additional_info != "")
            <div class="page-text">
                <hr class="form-hr">
                <b>@lang('messages.Additional Information') :</b> <br>
                {!! $tour->$langAdditionalInfo !!}
            </div>
        @endif
        @if ($tour->cancellation_policy != "")
            <div class="page-text">
                <hr class="form-hr">
                <b>@lang('messages.Cancelation Policy') :</b>
                <p>{!! $tour->$langCancellationPolicy !!}</p>
            </div>
        @endif
        <form id="edit-order" action="{{ route('func.order-tour.update',$order->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            {{-- GUESTS  --}}
            <div class="page-subtitle">@lang('messages.Guests')</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="travel_date">@lang('messages.Travel Date') <span>*</span></label>
                        <div class="btn-icon">
                            <span><i class="icon-copy fa fa-calendar-check-o" aria-hidden="true"></i></span>
                            <input readonly
                                class="form-control input-icon @error('travel_date') is-invalid @enderror"
                                name="travel_date"
                                type="text"
                                value="{{ dateFormat($order->travel_date) }}"
                                placeholder="@lang('messages.Select date')" 
                                required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="number_of_guests">@lang('messages.Number of Guests') <span>*</span></label>
                        <div class="btn-icon">
                            <span><i class="icon-copy fa fa-users" aria-hidden="true"></i></span>
                            <input type="number" id="nog" onchange="calculate()"  min="2" name="number_of_guests" class="form-control @error('number_of_guests') is-invalid @enderror" placeholder="@lang('messages.Number of Guests')"  value="{{ $order->number_of_guests }}" required>
                        </div>
                        @error('number_of_guests')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pickup_location">@lang('messages.Pick up location') <span>*</span></label>
                        <div class="btn-icon">
                            <span><i class="icon-copy fa fa-arrow-up" aria-hidden="true"></i></span>
                            <input type="text" id="pickup_location" name="pickup_location" class="form-control @error('pickup_location') is-invalid @enderror" placeholder="@lang('messages.ex'): @lang('messages.Hotel Name') / @lang('messages.Airport')" value="{{ $order->pickup_location }}" required>
                        </div>
                        @error('pickup_location')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="dropoff_location">@lang('messages.Drop off location') <span>*</span></label>
                        <div class="btn-icon">
                            <span><i class="icon-copy fa fa-arrow-down" aria-hidden="true"></i></span>
                            <input type="text" id="dropoff_location" name="dropoff_location" class="form-control @error('dropoff_location') is-invalid @enderror" placeholder="@lang('messages.ex'): @lang('messages.Hotel Name')/@lang('messages.Airport')" value="{{ $order->dropoff_location }}" required>
                        </div>
                        @error('dropoff_location')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="guest_detail">@lang('messages.Guest Detail') <span> *</span></label>
                        <textarea id="guest_detail" name="guest_detail" placeholder="ex: Mr. name, Mrs. name" class="textarea_editor form-control" value="{{ $order->guest_detail }}" required>{!! $order->guest_detail !!}</textarea>
                        @error('guest_detail')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="note">@lang('messages.Note')</label>
                        <textarea id="note" name="note" placeholder="@lang('messages.Optional')" class="textarea_editor form-control" value="{{ $order->note }}">{!! $order->note !!}</textarea>
                        @error('note')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12 m-b-8">
                    <div class="box-price-kicked">
                        <div class="row">
                            <div class="col-6 col-md-6">
                                <div class="normal-text">@lang('messages.Price')/@lang('messages.pax')</div>
                                <div class="normal-text">@lang('messages.Number of Guests')</div>
                                <hr>
                                <div class="price-name">@lang('messages.Total Price')</div>
                            </div>
                            <div class="col-6 col-md-6 text-right">
                                <div class="normal-text-07"><span id="tour_price_per_pax">{{ $order->price_pax }}</span></div>
                                <div class="normal-text"><span id="numberOfGuests">{{ $order->number_of_guests }}</span></div>
                                <hr>
                                <div class="price-name-price"><span id="tour_final_price">{{ $order->final_price }}</span></div>
                            </div>
                        </div>
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
                            <button type="submit" form="edit-order" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Checkout')</button>
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
    <div class="loading-icon hidden pre-loader">
        <div class="pre-loader-box">
            <div class="sys-loader-logo w3-center"> <img class="w3-spin" src="{{ asset('storage/icon/spinner.png') }}" alt="Bali Kami Tour Logo"></div>
            <div class="loading-text">
                Processing an Order...
            </div>
        </div>
    </div>
</div>
<script>
    const prices = @json($prices);
    function calculate() {
        const nogInput = document.getElementById('nog');
        const nog = parseInt(nogInput.value) || 0;

        if (nog <= 0 || prices.length === 0) {
            document.getElementById('tour_price_per_pax').innerText = '-';
            document.getElementById('numberOfGuests').innerText = `-`;
            document.getElementById('tour_final_price').innerText = '-';
            return;
        }

        // cari range harga
        let selected = null;
        for (const p of prices) {
            if (nog >= p.min_qty && nog <= p.max_qty) {
                selected = p;
                break;
            }
        }
        if (!selected) selected = prices[prices.length - 1];

        // pastikan nilai numerik
        const rate = parseFloat(selected.contract_rate ?? selected.price ?? 0);
        const markup = parseFloat(selected.markup ?? 0);
        const pricePerPax = rate + markup;

        if (isNaN(pricePerPax)) {
            console.error('⚠️ pricePerPax is NaN, data from backend may be invalid:', selected);
            return;
        }

        let total = pricePerPax * nog;
        
        // tampilkan ke view
        document.getElementById('tour_price_per_pax').innerText = `${pricePerPax.toLocaleString()}`;
        document.getElementById('numberOfGuests').innerText = `${nog.toLocaleString()}`;
        document.getElementById('tour_final_price').innerText = `${total.toLocaleString()}`;
        // debug (opsional)
        console.log({
            nog, rate, markup, pricePerPax, total, selected
        });
    }
</script>