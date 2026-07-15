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
                            : @lang('messages.'.$order->service)
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Location')
                        </td>
                        <td class="htd-2">
                            {{  $order->location }}
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
                            @lang('messages.Activity')
                        </td>
                        <td class="htd-2">
                            {{ $order->subservice }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Partner')
                        </td>
                        <td class="htd-2">
                            {{ $order->servicename }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Type')
                        </td>
                        <td class="htd-2">
                            {{ $order->service_type }}
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
                            {{ $order->duration." " }}
                        </td>
                    </tr>
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Capacity')
                        </td>
                        <td class="htd-2">
                            {{ $order->capacity." Pax" }}
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
        <form id="edit-order" action="/fupdate-order/{{ $order->id }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            {{-- GUESTS  --}}
            <div class="page-subtitle">@lang('messages.Guests')</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="number_of_guests">@lang('messages.Number of Guests')</label>
                        <input type="number" id="nog" onchange="calculate()"  min="1" max="{{ $order->capacity }}" name="number_of_guests" class="form-control @error('number_of_guests') is-invalid @enderror" placeholder="@lang('messages.Number of Guest')" value="{{ $order->number_of_guests }}" required>
                        @error('number_of_guests')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="travel_date"> @lang('messages.Activity Date')</label>
                        <input id="travel-date" name="travel_date" min="{{ date('Y-m-d',strtotime($now)) }}" type="text" class="form-control datetimepicker  @error('travel_date') is-invalid @enderror" value="{{ date('d M Y H.i',strtotime($order->travel_date)) }}" required>
                        @error('travel_date')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="guest_detail"> @lang('messages.Guest Detail')</label>
                        <input type="text" name="guest_detail" class="form-control  @error('guest_detail') is-invalid @enderror" placeholder="@lang('messages.Insert the names of all guests')" value="{{ $order->guest_detail }}" required>
                        @error('guest_detail')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="page-subtitle">@lang('messages.Note')</div>
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
                                @if ($order->bookingcode_disc > 0 or $order->discounts > 0 or $order->kick_back > 0 or isset($order->promotion_disc))
                                    <div class="modal-text-price">@lang('messages.Price')</div>
                                    <div class="modal-text-price">@lang('messages.Number of Guests')</div>
                                    <div class="modal-text-price">@lang('messages.Normal Price')</div>
                                    <hr class="form-hr">
                                    @if ($order->bookingcode_disc > 0)
                                        <div class="promo-text">@lang('messages.Booking Code')</div>
                                    @endif
                                    @if ($order->discounts > 0)
                                        <div class="promo-text">@lang('messages.Discounts')</div>
                                    @endif

                                    @php
                                        $tpro = json_decode($order->promotion_disc);
                                        $pro_name = json_decode($order->promotion_name);
                                        if (isset($pro_name)) {
                                            $cpro = count($pro_name);
                                        }else {
                                            $cpro = 0;
                                        }
                                        if (isset($tpro)) {
                                            $total_promotion = array_sum($tpro);
                                        }else {
                                            $total_promotion = 0;
                                        }
                                        $promotion_name = "";
                                        for ($i=0; $i < $cpro ; $i++) { 
                                            $promotion_name = $promotion_name.$pro_name[$i];
                                        }
                                    @endphp
                                    @if (isset($order->promotion_disc))
                                        <div class="promo-text">@lang('messages.Promotion')</div>
                                    @endif
                                @else
                                    <div class="modal-text-price">@lang('messages.Price') :</div>
                                    <div class="modal-text-price">@lang('messages.Number of Guests')</div>
                                    <hr class="form-hr">
                                @endif
                                <div class="price-name">@lang('messages.Total Price')</div>
                            </div>
                            <div class="col-6 col-md-6 text-right">
                                @if ($order->bookingcode_disc > 0 or $order->discounts > 0 or $order->kick_back > 0 or isset($order->promotion_disc))
                                    <div class="modal-num-price">{{ currencyFormatUsd($order->price_pax) }}/@lang('messages.pax')</div>
                                    <div class="modal-num-price"><span id="number_of_guests">{{ $order->number_of_guests }}</span></div>
                                    <div class="modal-num-price"><span id="normal_price">{{ number_format($order->normal_price) }}</span></div>
                                    <hr class="form-hr">
                                    @if ($order->bookingcode_disc > 0)
                                        <div class="kick-back">{{ "- $ ".number_format($order->bookingcode_disc) }}</div>
                                    @endif

                                    @if ($order->discounts > 0)
                                        <div class="kick-back">{{ "- $ ".number_format($order->discounts) }}</div>
                                    @endif
                                    @if (isset($order->promotion_disc))
                                        <div class="kick-back">{{ "- $ ".number_format($total_promotion) }}</div>
                                    @endif
                                
                                @else
                                    <div class="modal-num-price">{{ currencyFormatUsd($order->price_pax) }}/@lang('messages.pax')</div>
                                    <div class="modal-num-price"><span id="number_of_guests">{{ $order->number_of_guests }}</span></div>
                                    <hr class="form-hr">
                                @endif
                                <div class="price-tag"><span id="final_price">{{ number_format($order->final_price) }}</span></div>
                                
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
            @php
                if (isset($order->promotion_disc)) {
                    $pp = json_decode($order->promotion_disc);
                    $promotion_price = array_sum($pp);
                }else{
                    $promotion_price = 0;
                }
            @endphp
            <input type="hidden" name="service" value="Tour Package">
            <input type="hidden" name="status" value="Pending">
            <input type="hidden" name="page" value="submit-tour-order">
            <input type="hidden" name="action" value="Submit Order">
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <input type="hidden" name="orderno" value="{{ $order->orderno }}">
            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
            <input type="hidden" name="name" value="{{ Auth::user()->name }}">
            <input type="hidden" name="email" value="{{ Auth::user()->email }}">
            <input type="hidden" id="price_pax" name="price_pax" value="{{ $order->price_pax }}">
            <input type="hidden" id="normal_price" name="normal_price" value="{{ $order->normal_price }}">
            <input type="hidden" id="promo_disc" name="promotion_disc" value="{{ $promotion_price }}">
            @if (isset($order->bookingcode))
                <input type="hidden" id="bk_disc" name="bk_disc" value="{{ $order->bookingcode_disc }}">
            @else
                <input type="hidden" id="bk_disc" name="bk_disc" value=0>
            @endif
        </Form>
        <div class="card-box-footer">
            @if ($order->status == "Draft")
                <div class="form-group">
                    @if ($order->status != "Invalid")
                        @if (Auth::user()->email == "" or Auth::user()->phone == "" or Auth::user()->office == "" or Auth::user()->address == "" or Auth::user()->country == "")
                            <button type="button" class="btn btn-light"><i class="icon-copy fa fa-info" aria-hidden="true"> </i> @lang('messages.You cannot submit this order')</button>
                        @else
                            <button type="submit" form="edit-order" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Submit')</button>
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
    $("#edit-order").submit(function() {
        $(".result").text("");
        $(".loading-icon").removeClass("hidden");
        $(".submit").attr("disabled", true);
        $(".btn-txt").text("Processing ...");
    });
    });
</script>
<script>
    function calculate(){
        var nog = document.getElementById('nog').value;
        var p_pax = document.getElementById('price_pax').value;
        var promo_disc = document.getElementById('promo_disc').value;
        var bk_disc = document.getElementById('bk_disc').value;
        var np = (p_pax * nog);
        var fp = (p_pax * nog)-promo_disc-bk_disc;
        var final_price = fp.toLocaleString('en-US');
        var normal_price = np.toLocaleString('en-US');
        document.getElementById("final_price").innerHTML = final_price;
        document.getElementById("number_of_guests").innerHTML = nog;
        document.getElementById("normal_price").innerHTML = normal_price;
        // document.getElementById("normal_price").value = normal_price;
    }
</script>
