@if ($order->status == "Approved" or $order->status == "Paid")
    <div class="col-md-4 mobile">
        <div class="card-box">
            <div class="card-box-title">
                <div class="subtitle"><i class="icon-copy fa fa-money" aria-hidden="true"></i> @lang('messages.Payment Status')</div>
            </div>
            @if ($invoice->due_date > $now)
                @if (isset($receipt))
                    @if ($receipt->status == "Paid")
                        <div class="pmt-container">
                            <i class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Paid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Paid on') : {{ dateFormat($receipt->payment_date) }}<br>
                            @lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#mobile-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="mobile-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($receipt->status == "Pending")
                        <div class="pmt-container pending">
                            <i class="icon-copy fa fa-clock-o" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.On Review')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                            <p>@lang('messages.Payment Confirmation') : {{ dateFormat($receipt->created_at) }}</p>
                            
                        </div>
                    @elseif($receipt->status == "Invalid")
                        <div class="pmt-container unpaid">
                            <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Invalid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <p><i style="color: red">{{ $receipt->note }}</i></p>
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#mobile-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="mobile-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="notification-text" style="margin-top: 8px; color:rgb(143, 0, 0);">
                                            {!! $receipt->note !!}
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="pmt-container unpaid">
                            <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Unpaid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#mobile-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="mobile-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="notification-text" style="margin-top: 8px; color:rgb(143, 0, 0);">
                                            {!! $receipt->note !!}
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="pmt-container pending">
                        <i class="icon-copy fa fa-hourglass" aria-hidden="true"></i>
                        <div class="pmt-status">
                            @lang('messages.Awaiting Payment')
                        </div>
                    </div>
                    <div class="pmt-des">
                        <b>{{ $invoice->inv_no }}</b>
                        <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                    </div>
                @endif
            @else
                @if (isset($receipt))
                    @if ($receipt->status == "Paid")
                        <div class="pmt-container">
                            <i class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Paid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $order->orderno." - ". $invoice->inv_no }}</b>
                            <p>@lang('messages.Paid on') : {{ dateFormat($receipt->payment_date) }}<br>
                            @lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                            
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#mobile-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="mobile-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="pmt-container unpaid">
                            <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Invalid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                    @endif
                @else
                    <div class="pmt-container unpaid">
                        <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                        <div class="pmt-status">
                            @lang('messages.Unpaid')
                        </div>
                    </div>
                    <div class="pmt-des">
                        <b>{{ $invoice->inv_no }}</b>
                        <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                    </div>
                {{-- <img src="{{ asset('storage/receipt/paid.png') }}" alt="receipt_paid"> "\f058"--}}
                @endif
            @endif
        </div>
    </div>
@endif
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
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Location')
                        </td>
                        <td class="htd-2">
                            {{ $order->location }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-6 text-right">
                @if ($order->status == "Active")
                    <div class="page-status order-status-active"> @lang('messages.Confirmed') <span>@lang('messages.Status'):</span></div>
                @elseif ($order->status == "Pending")
                    <div class="page-status order-status-pending">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                @elseif ($order->status == "Rejected")
                    <div class="page-status order-status-rejected">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                @elseif ($order->status == "Approved")
                    <div class="page-status order-status-approve">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                @elseif ($order->status == "Confirmed")
                    <div class="page-status order-status-confirm">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                @elseif ($order->status == "Paid")
                    <div class="page-status order-status-paid">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
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
                    @if (isset($order->confirmation_order))
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Confirmation No')
                            </td>
                            <td class="htd-2">
                                <b>{{ $order->confirmation_order }}</b>
                            </td>
                        </tr>
                    @endif
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
                </table>
            </div>
            <div class="col-md-6">
                <table class="table tb-list">
                    <tr>
                        <td class="htd-1">
                            @lang('messages.Type')
                        </td>
                        <td class="htd-2">
                            {{ $order->service_type }}
                        </td>
                    </tr>
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
        
         {{-- GUESTS  --}}
         <div class="page-subtitle">@lang('messages.Guests')</div>
         <div class="row">
             <div class="col-md-12">
                 <table class="table tb-list">
                     <tr>
                         <td class="td-1">
                             @lang('messages.Number of Guests')
                         </td>
                         <td class="td-2">
                             {{ $order->number_of_guests." Guests" }}
                         </td>
                     </tr>
                     <tr>
                         <td class="td-1">
                             @lang('messages.Guest Detail')
                         </td>
                         <td class="td-2">
                             {{ $order->guest_detail }}
                         </td>
                     </tr>
                 </table>
             </div>
         </div>
         {{-- NOTE REMARK --}}
         @if (isset($order->note))
             <div class="page-subtitle">@lang('messages.Note') / @lang('messages.Remark')</div>
             <div class="row">
                 <div class="col-md-12">
                     <div class="page-text">
                         <p>{!! $order->note !!}</p>
                     </div>
                 </div>
             </div>
         @endif
         <div class="page-subtitle">@lang('messages.Price')</div>
         <div class="row">
             <div class="col-md-12">
                 <div class="box-price-kicked">
                     <div class="row">
                         <div class="col-6 col-md-6">
                             @if ($order->bookingcode_disc > 0 or $order->discounts > 0 or $order->kick_back > 0 or $order->promotion_disc > 0)
                                 <div class="promo-text">@lang('messages.Normal Price')</div>
                                 <hr class="form-hr">
                                 @if ($order->kick_back > 0)
                                     <div class="kick-back">@lang('messages.Kick Back')</div>
                                 @endif
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
                                 @if ($total_promotion > 0)
                                     <div class="promo-text">@lang('messages.Promotion')</div>
                                 @endif
                                 @if ($order->kick_back > 0 or $order->bookingcode_disc > 0 or $order->discounts > 0 or $total_promotion > 0)
                                     <hr class="form-hr">
                                 @endif
                             @endif
                             <div class="price-name">@lang('messages.Total Price')</div>
                         </div>
                         <div class="col-6 col-md-6 text-right">
                             @if ($order->bookingcode_disc > 0 or $order->discounts > 0 or $order->kick_back > 0 or $order->promotion_disc > 0)
                                 <div class="promo-text">{{ currencyFormatUsd($order->normal_price) }}</div>
                                 <hr class="form-hr">
                                 @if ($order->kick_back > 0)
                                     <div class="kick-back">{{ "- $ ".number_format($order->kick_back) }}</div>
                                 @endif
                                 @if ($order->bookingcode_disc > 0)
                                     <div class="kick-back">{{ "- $ ".number_format($order->bookingcode_disc) }}</div>
                                 @endif
 
                                 @if ($order->discounts > 0)
                                     <div class="kick-back">{{ "- $ ".number_format($order->discounts) }}</div>
                                 @endif
                                 @if ($total_promotion > 0)
                                     <div class="kick-back">{{ "- $ ".number_format($total_promotion) }}</div>
                                 @endif
                             
                                 @if ($order->kick_back > 0 or $order->bookingcode_disc > 0 or $order->discounts > 0 or $total_promotion > 0)
                                     <hr class="form-hr">
                                 @endif
                                 
                             @endif
                             <div class="usd-rate">{{ number_format($order->final_price) }}</div>
                         </div>
                     </div>
                 </div>
             </div>
             @if ($order->status != "Active" )
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
             @endif
         </div>
         <div class="card-box-footer">
            @if ($order->status == "Confirmed")
                @if ($reservation->send == "yes")
                    <form id="approveOrder" class="hidden" action="/fapprove-order-{{ $order->id }}"method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                    </form>
                    <div class="notification-order text-left" style="max-width: 50%;">
                        <i>@lang('messages.In') {{ $payment_period }} @lang('messages.days, your order will be automatically canceled if not approved. Approve now!')</i>
                    </div>
                    <button type="submit" form="approveOrder" class="btn btn-primary"><i class="icon-copy ion-checkmark-circled"></i> @lang('messages.Approve Order')</button>
                @else
                    <div class="notification-order text-left">
                        <i>@lang('messages.Waiting for Contract')</i>
                    </div>
                @endif
            @elseif ($order->status == "Rejected")
                <form id="deleteOrder" class="display-content" action="/delete-order/{{ $order->id }}" method="post">
                    @csrf
                    @method('delete')
                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                </form>
                <button type="submit" form="deleteOrder" class="btn btn-dark" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i> Delete Order</button>
                @elseif($order->status == "Approved" or $order->status == "Paid")
                @if ($status_contract == 1)
                    @if ($receipt == "")
                        <a href="#" data-toggle="modal" data-target="#payment-confirmation-{{ $order->id }}">
                            <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-upload" aria-hidden="true"></i> @lang('messages.Payment Confirmation')</button>
                        </a>
                        {{-- MODAL PAYMENT CONFIRMATION --}}
                        <div class="modal fade" id="payment-confirmation-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="card-box">
                                        <div class="card-box-title text-left">
                                            <div class="title"><i class="icon-copy fa fa-usd" aria-hidden="true"></i>@lang('messages.Payment Confirmation')</div>
                                        </div>
                                        <form id="payment-confirm-{{ $order->id }}" action="/fpayment-confirmation-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row text-left">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="row m-t-27">
                                                                <div class="col-5"><p>@lang('messages.Order Number')</p></div>
                                                                <div class="col-7"><p><b>: {{ $order->orderno }}</b></p></div>
                                                                <div class="col-5"><p>@lang('messages.Reservation Number')</p></div>
                                                                <div class="col-7"><p><b>: {{ $reservation->rsv_no }}</b></p></div>
                                                                <div class="col-5"><p>@lang('messages.Invoice Number')</p></div>
                                                                <div class="col-7"><p><b>: {{ $invoice->inv_no }}</b></p></div>
                                                                <div class="col-5"><p>@lang('messages.Due Date')</p></div>
                                                                <div class="col-7"><p>: {{ dateFormat($invoice->due_date) }}</p></div>
                                                                <div class="col-5"><p>@lang('messages.Amount')</p></div>
                                                                <div class="col-7"><p><b>: {{ currencyFormatUsd($order->final_price) }}</b></p></div>
                                                                <div class="col-12 m-t-18"><p><i class="icon-copy fa fa-exclamation" aria-hidden="true"></i> @lang('messages.Please make the payment before the due date and provide proof of payment to prevent the cancellation of your order.')</p></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="cover" class="form-label">@lang('messages.Receipt Image')</label>
                                                                <div class="dropzone">
                                                                    <div class="tour-receipt-div">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="receipt_name" class="form-label">@lang('messages.Select Receipt') </label><br>
                                                                <input type="file" name="receipt_name" id="receipt_name" class="custom-file-input @error('receipt_name') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('receipt_name') }}" required>
                                                                @error('receipt_name')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                            </div>
                                        </form>
                                        <div class="card-box-footer">
                                            <button type="submit" form="payment-confirm-{{ $order->id }}" class="btn btn-primary"><i class="icon-copy fa fa-upload" aria-hidden="true"></i> @lang('messages.Send')</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        @if ($receipt->status == "Invalid")
                            <a href="#" data-toggle="modal" data-target="#invalid-payment-confirmation-{{ $order->id }}">
                                <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-upload" aria-hidden="true"></i> @lang('messages.Payment Confirmation')</button>
                            </a>
                            {{-- MODAL PAYMENT CONFIRMATION --}}
                            <div class="modal fade" id="invalid-payment-confirmation-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="card-box">
                                            <div class="card-box-title text-left">
                                                <div class="title"><i class="icon-copy fa fa-usd" aria-hidden="true"></i>@lang('messages.Payment Confirmation')</div>
                                            </div>
                                            <form id="invalidpayment-confirm-{{ $order->id }}" action="/fpayment-confirmation-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row text-left">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="row m-t-27">
                                                                    <div class="col-5"><p>@lang('messages.Order Number')</p></div>
                                                                    <div class="col-7"><p><b>: {{ $order->orderno }}</b></p></div>
                                                                    <div class="col-5"><p>@lang('messages.Reservation Number')</p></div>
                                                                    <div class="col-7"><p><b>: {{ $reservation->rsv_no }}</b></p></div>
                                                                    <div class="col-5"><p>@lang('messages.Invoice Number')</p></div>
                                                                    <div class="col-7"><p><b>: {{ $invoice->inv_no }}</b></p></div>
                                                                    <div class="col-5"><p>@lang('messages.Due Date')</p></div>
                                                                    <div class="col-7"><p>: {{ dateFormat($invoice->due_date) }}</p></div>
                                                                    <div class="col-5"><p>@lang('messages.Amount')</p></div>
                                                                    <div class="col-7"><p><b>: {{ currencyFormatUsd($order->final_price) }}</b></p></div>
                                                                    <div class="col-12 m-t-18"><p><i class="icon-copy fa fa-exclamation" aria-hidden="true"></i> @lang('messages.Please make the payment before the due date and provide proof of payment to prevent the cancellation of your order.')</p></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="dropzone" class="form-label">@lang('messages.Receipt Image')</label>
                                                                    <div class="dropzone">
                                                                        <div class="tour-receipt-div">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="receipt_name" class="form-label">@lang('messages.Select Receipt')</label><br>
                                                                    <input type="file" name="receipt_name" id="receipt_name" class="custom-file-input @error('receipt_name') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('receipt_name') }}" required>
                                                                    @error('receipt_name')
                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                </div>
                                            </form>
                                            <div class="card-box-footer">
                                                <button type="submit" form="invalidpayment-confirm-{{ $order->id }}" class="btn btn-primary"><i class="icon-copy fa fa-upload" aria-hidden="true"></i> @lang('messages.Send')</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if (config('app.locale') == "zh")
                        <a href="#" data-toggle="modal" data-target="#contract-zh-{{ $order->id }}">
                            <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> 發票</button>
                        </a>
                        <a href='{{URL::to('/')}}/storage/document/invoice-{{ $inv_no }}-{{ $order->id }}_zh.pdf' target="_blank">
                            <button type="button" class="btn btn-primary mobile"><i class="fa fa-download"></i> 下載發票</button>
                        </a>
                        {{-- MODAL VIEW CONTRACT ZH --}}
                        <div class="modal fade" id="contract-zh-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                    <div class="modal-body pd-5">
                                        <embed src="storage/document/invoice-{{ $inv_no."-".$order->id }}_zh.pdf" frameborder="10" width="100%" height="850px">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="#" data-toggle="modal" data-target="#contract-en-{{ $order->id }}">
                            <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> @lang('messages.Invoice')</button>
                        </a>
                        <a href='{{URL::to('/')}}/storage/document/invoice-{{ $inv_no }}-{{ $order->id }}_en.pdf' target="_blank">
                            <button type="button" class="btn btn-primary mobile"><i class="fa fa-download"></i> @lang('messages.Download Invoice')</button>
                        </a>
                        {{-- MODAL VIEW CONTRACT EN --}}
                        <div class="modal fade" id="contract-en-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                    <div class="modal-body pd-5">
                                        {{-- storage/document/invoice-".$inv_no."-".$order->id.".pdf" --}}
                                        <embed src="storage/document/invoice-{{ $inv_no."-".$order->id }}_en.pdf" frameborder="10" width="100%" height="850px">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="notification-order text-left">
                        <i>@lang('messages.Waiting for Contract')</i>
                    </div>
                @endif
            @endif
            <a href="/orders">
                <button type="button" class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
            </a>
            
         </div>
     </div>
</div>
@if ($order->status == "Approved" or $order->status == "Paid")
    <div class="col-md-4 desktop">
        <div class="card-box">
            <div class="card-box-title">
                <div class="subtitle"><i class="icon-copy fa fa-money" aria-hidden="true"></i> @lang('messages.Payment Status')</div>
            </div>
            @if ($invoice->due_date > $now)
                @if (isset($receipt))
                    @if ($receipt->status == "Paid")
                        <div class="pmt-container">
                            <i class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Paid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Paid on') : {{ dateFormat($receipt->payment_date) }}<br>
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#paid-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="paid-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($receipt->status == "Pending")
                        <div class="pmt-container pending">
                            <i class="icon-copy fa fa-clock-o" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.On Review')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                            <p>@lang('messages.Payment Confirmation') : {{ dateFormat($receipt->created_at) }}</p>
                            
                        </div>
                    @elseif($receipt->status == "Invalid")
                        <div class="pmt-container unpaid">
                            <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Invalid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <p><i style="color: red">{{ $receipt->note }}</i></p>
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#invalid-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="invalid-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="notification-text" style="margin-top: 8px; color:rgb(143, 0, 0);">
                                            {!! $receipt->note !!}
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="pmt-container unpaid">
                            <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Unpaid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#payment-receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="payment-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="notification-text" style="margin-top: 8px; color:rgb(143, 0, 0);">
                                            {!! $receipt->note !!}
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="pmt-container pending">
                        <i class="icon-copy fa fa-hourglass" aria-hidden="true"></i>
                        <div class="pmt-status">
                            @lang('messages.Awaiting Payment')
                        </div>
                    </div>
                    <div class="pmt-des">
                        <b>{{ $invoice->inv_no }}</b>
                        <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                    </div>
                @endif
            @else
                @if (isset($receipt))
                    @if ($receipt->status == "Paid")
                        <div class="pmt-container">
                            <i class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Paid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $order->orderno." - ". $invoice->inv_no }}</b>
                            <p>@lang('messages.Paid on') : {{ dateFormat($receipt->payment_date) }}<br>
                            @lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                            
                        </div>
                        <div class="view-receipt">
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#receipt-{{ $receipt->id }}">
                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-img">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                        </div>
                                        <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                        <div class="card-box-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="pmt-container unpaid">
                            <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                            <div class="pmt-status">
                                @lang('messages.Invalid')
                            </div>
                        </div>
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                    @endif
                @else
                    <div class="pmt-container unpaid">
                        <i class="icon-copy fa fa-window-close" aria-hidden="true"></i>
                        <div class="pmt-status">
                            @lang('messages.Unpaid')
                        </div>
                    </div>
                    <div class="pmt-des">
                        <b>{{ $invoice->inv_no }}</b>
                        <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                    </div>
                {{-- <img src="{{ asset('storage/receipt/paid.png') }}" alt="receipt_paid"> "\f058"--}}
                @endif
            @endif
        </div>
    </div>
@endif