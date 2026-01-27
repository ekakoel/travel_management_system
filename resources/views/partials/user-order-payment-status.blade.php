@if ($invoice)
    @if ($order->status == "Paid")
        @if (count($receipts)>0)
            <div class="col-md-12">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy fa fa-money" aria-hidden="true"></i> @lang('messages.Payment Status')</div>
                    </div>
                    <div class="receipt-description">
                        @foreach ($receipts as $receipt)
                            @if ($receipt->status == "Valid")
                                <div class="receipt-container">
                                    <div class="receipt-status">
                                        <i class="icon-copy fa fa-check-circle color-green" aria-hidden="true"></i>
                                        <div class="pmt-status">
                                            @lang('messages.Paid') ({{ currencyFormatUsd($receipt->amount) }})
                                        </div>
                                    </div>
                                    <div class="receipt-description">
                                        <b>{{ $invoice->inv_no }}</b>
                                        <p>@lang('messages.Paid on') : {{ dateFormat($receipt->payment_date) }}<br>
                                    </div>
                                    <div class="receipt-action">
                                        <a class="action-btn" href="#" data-toggle="modal" data-target="#{{ $device }}-paid-receipt-{{ $receipt->id }}">
                                            <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="modal fade" id="{{ $device }}-paid-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')
                                                </div>
                                                <div class="card-box-body text-center">
                                                    <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                                </div>
                                                <div class="card-box-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        @if ($doku_payment_paid)
            <div class="col-md-12">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="title">@lang('messages.DOKU Payment')</div>
                    </div>
                    <button id="checkout-button-{{ $device }}" class="w-100">
                        <div class="card-ptext-margin">
                            <div class="pmt-des m-b-8">
                                <div class="card-ptext-content">
                                    <div class="ptext-title">@lang('messages.Invoice No').</div>
                                    <div class="ptext-value">{{ $doku_payment_paid->invoice_number }}</div>
                                    <div class="ptext-title">@lang('messages.Payment Dateline')</div>
                                    <div class="ptext-value">{{ date('d F Y',strtotime($doku_payment_paid->expired_date)) }}</div>
                                    <div class="ptext-title">@lang('messages.Amount')</div>
                                    <div class="ptext-value">{{ currencyFormatIdr($doku_payment_paid->amount) }}</div>
                                </div>
                            </div>
                        </div>
                    </button>
                    <script type="text/javascript">
                        var device = @json($device);
                        var checkoutButton = document.getElementById('checkout-button-'+ device);
                        var paymentUrl = @json($doku_payment_paid->checkout_url);
                        checkoutButton.addEventListener('click', function () {
                            loadJokulCheckout(paymentUrl);
                        });
                    </script>
                    @if ($doku_payment_paid->status == "Paid")
                        <div class="paid-stamp">
                            <img src="{{ asset('storage/icon/paid_icon.png') }}" alt="Paid Icon">
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @else
        <div class="col-md-12">
            <div class="card-box">
                <div class="card-box-title">
                    <div class="subtitle"><i class="icon-copy fa fa-money" aria-hidden="true"></i> @lang('messages.Payment Status')</div>
                </div>
                @if (count($receipts)>0)
                    @foreach ($receipts as $receipt)
                        @if ($receipt->status == "Valid")
                            <div class="receipt-container">
                                <div class="receipt-status">
                                    <i class="icon-copy fa fa-check-circle color-green" aria-hidden="true"></i>
                                    <div class="pmt-status">
                                        @lang('messages.Paid') ({{ currencyFormatUsd($receipt->amount) }})
                                    </div>
                                </div>
                                <div class="receipt-description">
                                    <b>{{ $invoice->inv_no }}</b>
                                    <p>@lang('messages.Paid on') : {{ dateFormat($receipt->payment_date) }}<br>
                                </div>
                                <div class="receipt-action">
                                    <a class="action-btn" href="#" data-toggle="modal" data-target="#{{ $device }}-paid-receipt-{{ $receipt->id }}">
                                        <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="modal fade" id="{{ $device }}-paid-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content modal-receipt">
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
                            <div class="receipt-container">
                                <div class="receipt-status">
                                    <i class="icon-copy fa fa-clock-o" aria-hidden="true"></i>
                                    <div class="pmt-status">
                                        @lang('messages.On Review')
                                    </div>
                                </div>
                                <div class="receipt-description">
                                    <b>{{ $invoice->inv_no }}</b>
                                    <i>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</i>
                                    <i>@lang('messages.Payment Confirmation') : {{ dateFormat($receipt->created_at) }}</i>
                                </div>
                            </div>
                        @elseif($receipt->status == "Invalid")
                            <div class="receipt-container">
                                <div class="receipt-status">
                                    <i class="icon-copy fa fa-window-close color-red" aria-hidden="true"></i>
                                    <div class="pmt-status">
                                        @lang('messages.Invalid')
                                    </div>
                                </div>
                                <div class="receipt-description">
                                    <p><i style="color: red">{{ $receipt->note }}</i></p>
                                    <b>{{ $invoice->inv_no }}</b>
                                    <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                                </div>
                                <div class="receipt-action">
                                    <a class="action-btn" href="#" data-toggle="modal" data-target="#{{ $device }}-invalid-receipt-{{ $receipt->id }}">
                                        <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="modal fade" id="{{ $device }}-invalid-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content modal-receipt">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                            </div>
                                            <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                            <div class="notification-text" style="margin-top: 8px; color:rgb(143, 0, 0);">
                                                {!! $receipt->note !!}
                                            </div>
                                            <div class="card-box-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="receipt-container">
                                <div class="receipt-status">
                                    <i class="icon-copy fa fa-clock-o" aria-hidden="true"></i>
                                    <div class="pmt-status">
                                        @lang('messages.On Review')
                                    </div>
                                </div>
                                <div class="receipt-description">
                                    <b>{{ $invoice->inv_no }}</b>
                                    <i>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</i>
                                    <i>@lang('messages.Payment Confirmation') : {{ dateFormat($receipt->created_at) }}</i>
                                </div>
                                <div class="receipt-action">
                                    <a class="action-btn" href="#" data-toggle="modal" data-target="#{{ $device }}-payment-receipt-{{ $receipt->id }}">
                                        <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="modal fade" id="{{ $device }}-payment-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content modal-receipt">
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
                    @endforeach
                @else
                    <div class="pmt-container pending">
                        <i class="icon-copy fa fa-hourglass" aria-hidden="true"></i>
                        <div class="pmt-status">
                            @lang('messages.Waiting Payment')
                        </div>
                    </div>
                    @if ($invoice)
                        <div class="pmt-des">
                            <b>{{ $invoice->inv_no }}</b>
                            <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endif
@endif