@if ($invoice)
    @if ($receipts)
        <div class="col-md-12">
            <div class="card-box">
                <div class="card-box-title">
                    <div class="title">Payment Receipt</div>
                    @if ($invoice->balance > 0)
                        <span>
                            <a class="action-btn" href="#" data-toggle="modal" data-target="#{{ $device }}-upload-receipt-{{ $order->id }}">
                                <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                            </a>
                        </span>
                    @endif
                </div>
                <div class="pmt-des m-b-8">
                    <div class="card-ptext-content">
                        <div class="ptext-title">Payment deadline</div>
                        <div class="ptext-value">{{ date('d F Y',strtotime($invoice->due_date)) }}</div>
                    </div>
                </div>
                <div class="pmt-des m-b-8">
                    <div class="card-ptext-content">
                        @if ($invoice->currency?->name == 'USD')
                            <div class="ptext-title">Total Invoice USD</div>
                            <div class="ptext-value">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                        @elseif ($invoice->currency?->name == 'TWD')
                            <div class="ptext-title">Total Invoice USD</div>
                            <div class="ptext-value">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                            <div class="ptext-title">Total Invoice TWD</div>
                            <div class="ptext-value">{{ "NT$ ".currencyFormatUsd($invoice->total_twd) }}</div>
                        @elseif ($invoice->currency?->name == 'CNY')
                            <div class="ptext-title">Total Invoice USD</div>
                            <div class="ptext-value">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                            <div class="ptext-title">Total Invoice CNY</div>
                            <div class="ptext-value">{{ "¥ ".currencyFormatUsd($invoice->total_cny) }}</div>
                        @elseif ($invoice->currency?->name == 'IDR')
                            <div class="ptext-title">Total Invoice USD</div>
                            <div class="ptext-value">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                            <div class="ptext-title">Total Invoice IDR</div>
                            <div class="ptext-value">{{ "Rp ".currencyFormatUsd($invoice->total_idr) }}</div>
                        @endif
                    </div>
                </div>
                <div class="m-b-18">
                    @foreach ($receipts as $desktop_receipt)
                        <div class="card-ptext-margin">
                            <a class="card-link" href="#" data-toggle="modal" data-target="#{{ $device }}-receipt-{{ $desktop_receipt->id }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="pmt-container">
                                            <i class="icon-copy fa fa-file-image-o" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="pmt-des">
                                            <b>
                                                {{ $invoice->inv_no }}
                                                @if ($desktop_receipt->status == "Valid")
                                                    <i data-toggle="tooltip" data-placement="top" title="@lang('messages.Verified')" style="color: rgb(0, 167, 14); cursor:help" class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                                                @else
                                                    <i style="font-size: 0.7rem; color:rgb(255 0 0);">{{ $desktop_receipt->status }}</i>
                                                @endif
                                            </b>
                                            <div class="card-ptext-content">
                                                <div class="ptext-title">Recived on</div>
                                                <div class="ptext-value">{{ date('d F Y',strtotime($desktop_receipt->created_at)) }}</div>
                                                @if ($desktop_receipt->kurs_id == '1')
                                                    <div class="ptext-title">Amount</div>
                                                    <div class="ptext-value">{{ $desktop_receipt->kurs?->name }} {{ currencyFormatUsd($desktop_receipt->amount) }}</div>
                                                @elseif($desktop_receipt->kurs_id == '3')
                                                    <div class="ptext-title">Amount</div>
                                                    <div class="ptext-value">{{ $desktop_receipt->kurs?->name }} {{ currencyFormatTwd($desktop_receipt->amount) }}</div>
                                                @elseif($desktop_receipt->kurs_id == '2')
                                                    <div class="ptext-title">Amount</div>
                                                    <div class="ptext-value">{{ $desktop_receipt->kurs?->name }} {{ currencyFormatCny($desktop_receipt->amount) }}</div>
                                                @else
                                                    <div class="ptext-title">Amount</div>
                                                    <div class="ptext-value">{{ $desktop_receipt->kurs?->name }} {{ currencyFormatIdr($desktop_receipt->amount) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @if ($order->status != "Paid")
                                <div class="view-receipt">
                                    <a href="#" data-toggle="modal" data-target="#{{ $device }}-receipt-{{ $desktop_receipt->id }}">
                                        <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                        {{-- MODAL VALIDATE RECEIPT --}}
                        <div class="modal fade" id="{{ $device }}-receipt-{{ $desktop_receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="card-box">
                                        <div class="card-box-title text-left">
                                            <div class="title"> <i class="icon-copy fa fa-file-image-o" aria-hidden="true"></i>Validate Receipt</div>
                                        </div>
                                            <form id="{{ $device }}-confirmation-payment-{{ $desktop_receipt->id }}" action="{{ route('admin.confirm.receipt',$desktop_receipt->id) }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row text-left">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="row">
                                                                    <div class="col-md-12 text-center">
                                                                        <div class="modal-receipt-container">
                                                                            <img src="/storage/receipt/{{ $desktop_receipt->receipt_img }}" alt="">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="row">
                                                                    <div class="col-5"><p>Order Number</p></div>
                                                                    <div class="col-7"><p><b>: {{ $order->orderno }}</b></p></div>
                                                                    <div class="col-5"><p>Reservation Number</p></div>
                                                                    <div class="col-7"><p><b>: {{ $reservation->rsv_no }}</b></p></div>
                                                                    <div class="col-5"><p>Invoice Number</p></div>
                                                                    <div class="col-7"><p><b>: {{ $invoice->inv_no }}</b></p></div>
                                                                    <div class="col-5"><p>Due Date</p></div>
                                                                    <div class="col-7"><p>: {{ date('d F Y',strtotime($invoice->due_date)) }}</p></div>
                                                                    @if ($desktop_receipt->status == 'Valid')
                                                                        <div class="col-12">
                                                                            <hr class="form-hr">
                                                                        </div>
                                                                        <div class="col-5"><p>Payment Date</p></div>
                                                                        <div class="col-7"><p>: {{ date('d F Y',strtotime($desktop_receipt->payment_date)) }}</p></div>
                                                                        <div class="col-5">
                                                                            <p>Payment Amount</p>
                                                                        </div>
                                                                        <div class="col-7">
                                                                            @if ($desktop_receipt->kurs_id == "1")
                                                                                <p><b>: {{ currencyFormatUsd($desktop_receipt->amount) }}</b></p>
                                                                            @elseif ($desktop_receipt->kurs_id == "2")
                                                                                <p><b>: {{ currencyFormatCny($desktop_receipt->amount) }}</b></p>
                                                                            @elseif ($desktop_receipt->kurs_id == "3")
                                                                                <p><b>: {{ currencyFormatTwd($desktop_receipt->amount) }}</b></p>
                                                                            @else
                                                                                <p><b>: {{ currencyFormatIdr($desktop_receipt->amount) }}</b></p>
                                                                            @endif
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <hr class="form-hr">
                                                                        </div>
                                                                    @endif
                                                                    @if ($order->handled_by == $admin->id)
                                                                        @if ($invoice->balance > 0)
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label for="status" class="form-label">Receipt Status <span>*</span></label>
                                                                                    <select name="status" class="custom-select @error('status') is-invalid @enderror" required>
                                                                                        <option selected value="{{ $desktop_receipt->status }}">{{ $desktop_receipt->status }}</option>
                                                                                        <option value="Valid">Valid</option>
                                                                                        <option value="Invalid">Invalid</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            @if ($desktop_receipt->status == "Pending" or $desktop_receipt->status == "Invalid")
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="kurs_id" class="form-label">Currency<span>*</span></label>
                                                                                        <select name="kurs_id" class="custom-select @error('kurs_id') is-invalid @enderror" required>
                                                                                            <option value="1">USD</option>
                                                                                            <option value="2">CNY</option>
                                                                                            <option value="3">TWD</option>
                                                                                            <option value="4">IDR</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="amoun" class="form-label col-form-label">Amount</label>
                                                                                        <input type="text" name="amount" class="input-icon form-control @error('amount') is-invalid @enderror" placeholder="Insert Amount" value="{{ $desktop_receipt->amount }}" required>
                                                                                        @error('amount')
                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <input hidden type="text" name="kurs_id" value="{{ $desktop_receipt->kurs_id }}">
                                                                                <input hidden type="text" name="amount" value="{{ $desktop_receipt->amount }}">
                                                                            @endif
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label for="payment_date" class="form-label col-form-label">Payment Date</label>
                                                                                    <input readonly type="text" name="payment_date" class="form-control date-picker @error('payment_date') is-invalid @enderror" placeholder="Payment Date" value="{{ date('d F Y',strtotime($desktop_receipt->payment_date)) }}" required>
                                                                                    @error('payment_date')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label for="note">Descritption </label>
                                                                                    <textarea name="note" class="textarea_editor form-control @error('note') is-invalid @enderror" placeholder="Description">{{ $desktop_receipt->note }}</textarea>
                                                                                    @error('note')
                                                                                        <span class="invalid-feedback">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                        @endif
                                                                    @else
                                                                        <div class="col-12">
                                                                            <div class="notification">
                                                                                @php
                                                                                    $validator = $admins->where('id',$order->handled_by)->first()
                                                                                @endphp
                                                                                Only the {{ $validator?$validator->name:"Admin" }} can validate the receipt for this order!
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        <div class="card-box-footer">
                                            @if ($order->handled_by == $admin->id)
                                                @if ($invoice->balance > 0)
                                                    <button type="submit" form="{{ $device }}-confirmation-payment-{{ $desktop_receipt->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> {{ $desktop_receipt->status == 'Pending'?"Validate":"Update" }}</button>
                                                @endif
                                            @endif
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($total_payment_usd > 0)
                    <div class="pmt-des">
                        <div class="card-ptext-content">
                            <div class="ptext-title">Total Payment USD</div>
                            <div class="ptext-value">{{ currencyFormatUsd($total_payment_usd) }}</div>
                        </div>
                    </div>
                @endif
                @if ($total_payment_cny > 0)
                    <div class="pmt-des">
                        <div class="card-ptext-content">
                            <div class="ptext-title">Total Payment CNY</div>
                            <div class="ptext-value">{{ "¥ ".currencyFormatUsd($total_payment_cny) }}</div>
                        </div>
                    </div>
                @endif
                @if ($total_payment_twd > 0)
                    <div class="pmt-des">
                        <div class="card-ptext-content">
                            <div class="ptext-title">Total Payment TWD</div>
                            <div class="ptext-value">{{ "NT$ ".currencyFormatUsd($total_payment_twd) }}</div>
                        </div>
                    </div>
                @endif
                @if ($total_payment_idr > 0)
                    <div class="pmt-des">
                        <div class="card-ptext-content">
                            <div class="ptext-title">Total Payment IDR</div>
                            <div class="ptext-value">{{ "Rp ".currencyFormatUsd($total_payment_idr) }}</div>
                        </div>
                    </div>
                @endif
                @if ($total_payment_usd > 0 || $total_payment_cny > 0 || $total_payment_twd > 0 || $total_payment_idr > 0)
                    <hr class="form-hr">
                @endif
                <div class="pmt-des m-t-8">
                    <div class="card-ptext-content">
                        <div class="ptext-title"><b>Balance</b></div>
                        @if ($invoice)
                            @if ($invoice->balance <= 1)
                                <div class="ptext-value usd-rate"><b>Paid</b></div>
                            @else
                                @if ($invoice->currency?->name == "USD")
                                    <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->balance) }}</b></div>
                                @elseif ($invoice->currency?->name == "CNY")
                                    <div class="ptext-value"><b>{{ currencyFormatCny($invoice->balance) }}</b></div>
                                @elseif ($invoice->currency?->name == "TWD")
                                    <div class="ptext-value"><b>{{ currencyFormatTwd($invoice->balance) }}</b></div>
                                @else
                                    <div class="ptext-value"><b>{{ currencyFormatIdr($invoice->balance) }}</b></div>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-12">
            <div class="card-box">
                <div class="card-box-title">
                    <div class="title">Payment Receipt</div>
                    <span>
                        <a class="action-btn" href="#" data-toggle="modal" data-target="#{{ $device }}-upload-receipt-{{ $order->id }}">
                            <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                        </a>
                    </span>
                </div>
                <div class="pmt-des">
                    <div class="card-ptext-content">
                        <div class="ptext-title">Invoice No</div>
                        <div class="ptext-value">{{ $invoice->inv_no }}</div>
                        <div class="ptext-title">Payment deadline</div>
                        <div class="ptext-value">{{ date('d M Y',strtotime($invoice->due_date)) }}</div>
                        @if ($invoice->currency?->name == 'USD')
                            <div class="ptext-title"><b>Total Price</b></div>
                            <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></div>
                        @elseif ($invoice->currency?->name == 'TWD')
                            <div class="ptext-title"><b>Total USD</b></div>
                            <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></div>
                            <div class="ptext-title"><b>Total TWD</b></div>
                            <div class="ptext-value"><b>{{ "NT$ ".currencyFormatUsd($invoice->total_twd) }}</b></div>
                        @elseif ($invoice->currency?->name == 'CNY')
                            <div class="ptext-title"><b>Total USD</b></div>
                            <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></div>
                            <div class="ptext-title"><b>Total CNY</b></div>
                            <div class="ptext-value"><b>{{ "¥ ".currencyFormatUsd($invoice->total_cny) }}</b></div>
                        @elseif ($invoice->currency?->name == 'IDR')
                            <div class="ptext-title"><b>Total USD</b></div>
                            <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></div>
                            <div class="ptext-title"><b>Total IDR</b></div>
                            <div class="ptext-value"><b>{{ "Rp ".currencyFormatUsd($invoice->total_idr) }}</b></div>
                        @endif
                    </div>
                    
                </div>
                
            </div>
        </div>
    @endif
    {{-- MODAL PAYMENT CONFIRMATION --}}
    <div class="modal fade" id="{{ $device }}-upload-receipt-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="card-box">
                    <div class="card-box-title text-left">
                        <div class="title"><i class="icon-copy fa fa-usd" aria-hidden="true"></i>@lang('messages.Payment Confirmation')</div>
                    </div>
                    <form id="{{ $device }}-payment-confirm-{{ $order->id }}" action="{{ route('func.admin-add-payment-confirmation',$order->id) }}" method="post" enctype="multipart/form-data">
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
                                            <input type="file" name="receipt_name" class="custom-file-input @error('receipt_name') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('receipt_name') }}" required>
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
@endif