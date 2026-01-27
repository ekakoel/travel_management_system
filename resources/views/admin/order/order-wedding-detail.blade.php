
@section('title', __('messages.Order Wedding'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="info-action hide-print">
                @if (session('errors_message'))
                    <div class="alert alert-danger">
                        {{ session('errors_message') }}
                    </div>
                @endif
                @if (\Session::has('success'))
                    <div class="alert alert-success">
                        <ul>
                            <li>{!! \Session::get('success') !!}</li>
                        </ul>
                    </div>
                @endif
            </div>
            <div class="page-header hide-print">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <i class="icon-copy fa fa-shopping-basket" aria-hidden="true"></i>@lang('messages.Wedding Order') @lang('messages.Validation')
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="orders-admin">@lang('messages.Orders Admin')</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $orderWedding->orderno }}</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- SIDEBAR MOBILE --}}
                <div class="col-md-4 mobile">
                    <div class="row">
                        @include('layouts.attentions')
                        <div class="col-md-12">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">@lang('messages.Status')</div>
                                </div> 
                                <div class="order-status-container">
                                    @if ($orderWedding->status == "Active")
                                        <div class="status-active-color">@lang('messages.Confirmed')</div>
                                        @if ($reservation->send == "yes")
                                            - <div class="send-email">@lang('messages.Email') <i class="icon-copy fa fa-envelope" aria-hidden="true"></i></div>
                                        @else
                                            - <div class="not-send-email">@lang('messages.Email') <i class="icon-copy fa fa-envelope" aria-hidden="true"></i></div>
                                        @endif
                                        @if ($reservation->status == "Active")
                                            - <div class="not-approved-order">@lang('messages.Waiting') <i class="icon-copy ion-clock"></i></div>
                                        @endif
                                    @elseif ($orderWedding->status == "Pending")
                                        <div class="status-pending-color">{{ $orderWedding->status }}</div>
                                    @elseif ($orderWedding->status == "Invalid")
                                        <div class="status-invalid-color">{{ $orderWedding->status }}</div>
                                    @elseif ($orderWedding->status == "Rejected")
                                        <div class="status-reject-color">{{ $orderWedding->status }}</div>
                                    @elseif ($orderWedding->status == "Confirmed")
                                        <div class="status-confirmed-color">{{ $orderWedding->status }}</div>
                                    @elseif ($orderWedding->status == "Approved" or $orderWedding->status == "Paid")
                                        <div class="status-approved-color"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ $orderWedding->status }}</div>
                                        @if ($reservation->checkin > $now)
                                            - <div class="standby-order"><p><i class="icon-copy ion-clock"> </i> {{ date('D ,d M Y',strtotime($orderWedding->checkin)) }}</p></div>
                                        @elseif ($reservation->checkin <= $now and $reservation->checkout > $now)
                                            - <div class="ongoing-order">@lang('messages.Ongoing') <i class="icon-copy ion-android-walk"></i></div>
                                        @else
                                            - <div class="final-order">@lang('messages.Final')</div>
                                        @endif
                                    @elseif($orderWedding->status == "Paid")
                                        <div class="status-paid-color"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ $orderWedding->status }} ({{ date('d F Y',strtotime($orderWedding->updated_at)) }})</div>
                                    @else
                                        <div class="status-draf-color">{{ $orderWedding->status }}</div>
                                    @endif
                                    @if ($invoice)
                                        @if($invoice->balance <= 0)
                                            <div class="status-paid-color"><i class="icon-copy fa fa-check-square" aria-hidden="true"></i> @lang('messages.Paid')</div>
                                        @endif
                                    @endif
                                </div>
                                @if (count($orderlogs)>0)
                                    <hr class="form-hr">
                                    <p><b>@lang('messages.Order Log'):</b></p>
                                    <table class="table tb-list">
                                        @foreach ($orderlogs as $no=>$orderlog)
                                            @php
                                                $adminorder = $admins->where('id',$orderlog->admin)->first();
                                            @endphp
                                            <tr>
                                                <td>{{ ++$no.". " }}</td>
                                                <td> {!! date('d M Y (H:i)',strtotime($orderlog->created_at)) !!}</td>
                                                <td>{!! $adminorder->code !!}</td>
                                                <td><i>{!! $orderlog->action !!}</i></td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @endif
                            </div>
                        </div>
                        {{-- ORDER NOTE --}}
                        <div class="col-md-12">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">@lang('messages.Order Note')</div>
                                </div> 
                                @foreach ($order_notes as $order_note)
                                    <div class="container-order-note">
                                        @php
                                            $operator = Auth::user()->where('id',$order_note->user_id)->first();
                                        @endphp
                                        <p><b>{{ date('d M Y (H:i)',strtotime($order_note->created_at))." - ".$operator->name }}</b> (<i>{{ $order_note->status }}</i>)</p>
                                        <p class="m-l-18">{!! $order_note->note !!}</p>
                                        
                                        <hr class="form-hr">
                                    </div>
                                @endforeach
                                @if ($orderWedding->status !== "Paid")
                                    <div class="card-box-footer">
                                        <a href="#" data-toggle="modal" data-target="#add-order-note-desktop"><button type="button" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> @lang('messages.Add Note')</button></a>
                                    </div>
                                @endif
                            </div>
                            {{-- MODAL ORDER NOTE --}}
                            <div class="modal fade" id="add-order-note-desktop" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <div class="title"><i class="fa fa-plus" aria-hidden="true"></i> @lang('messages.Add Note')</div>
                                            </div>
                                            <form id="faddAddNote" action="/fadd-order-wedding-note-{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group row">
                                                    <label for="status" class="col-sm-12">@lang('messages.Type')</label>
                                                    <div class="col-sm-12">
                                                        <select name="status" class="custom-select @error('status') is-invalid @enderror" value="{{ old('status') }}">
                                                            <option selected value="Urgent">@lang('messages.Urgent')</option>
                                                            <option value="Waiting">@lang('messages.Waiting')</option>
                                                            <option value="Error">@lang('messages.Error')</option>
                                                            <option value="Cancel">@lang('messages.Cancel')</option>
                                                            <option value="Reject">@lang('messages.Reject')</option>
                                                            <option value="Info">@lang('messages.Info')</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="order_note" class="col-sm-12">@lang('messages.Note')</label>
                                                    <div class="col-sm-12">
                                                        <textarea id="order_note" name="order_note" placeholder="Insert order note" class="textarea_editor form-control border-radius-0" autofocus required></textarea>
                                                        @error('order_note')
                                                            <div class="alert alert-danger">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <input type="hidden" name="user_id" value="{{ Auth::User()->id }}">
                                                <input type="hidden" name="order_id" value="{{ $orderWedding->id }}">
                                            </Form>
                                            <div class="card-box-footer">
                                                <div class="form-group">
                                                    <button type="submit" form="faddAddNote" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Submit')</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if ($orderWedding->status == "Active" or $orderWedding->status == "Approved" or $orderWedding->status == "Paid")
                            {{-- KURS --}}
                            @if ($invoice)
                                @php
                                    $invoice_sell_usd = floatval($invoice->sell_usd);
                                    $invoice_buy_usd = floatval($invoice->rate_usd);
                                    $invoice_sell_cny = floatval($invoice->sell_cny);
                                    $invoice_buy_cny = floatval($invoice->rate_cny);
                                    $invoice_sell_twd = floatval($invoice->sell_twd);
                                    $invoice_buy_twd = floatval($invoice->rate_twd);
                                @endphp
                                <div class="col-md-12">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title">@lang('messages.Kurs on order')</div>
                                            <span>{{ date('d F Y',strtotime($invoice->inv_date)) }}</span>
                                        </div> 
                                        
                                        <div class="row">
                                            <div class="col-4 col-md-4">
                                                <p><b>@lang('messages.Currency')</b></p>
                                            </div>
                                            <div class="col-4 col-md-4">
                                                <p><b>@lang('messages.Sell')</b></p>
                                            </div>
                                            <div class="col-4 col-md-4">
                                                <p><b>@lang('messages.Buy')</b></p>
                                            </div>
                                            <div class="col-4 col-md-4">
                                                <p>USD</p>
                                                <p>CNY</p>
                                                <p>TWD</p>
                                            </div>
                                            <div class="col-4 col-md-4">
                                                <p>{{ currencyFormatIdr($invoice_sell_usd) }}</p>
                                                <p>{{ currencyFormatIdr($invoice_sell_cny) }}</p>
                                                <p>{{ currencyFormatIdr($invoice_sell_twd) }}</p>
                                            </div>
                                            <div class="col-4 col-md-4">
                                                <p>{{ currencyFormatIdr($invoice_buy_usd) }}</p>
                                                <p>{{ currencyFormatIdr($invoice_buy_cny) }}</p>
                                                <p>{{ currencyFormatIdr($invoice_buy_twd) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{-- RECEIPT --}}
                            @if (count($receipts)>0)
                                <div class="col-md-12">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title">@lang('messages.Payment Receipt')</div>
                                            @if ($invoice->balance > 0)
                                                <span>
                                                    <a href="#" data-toggle="modal" data-target="#mobile-admin-add-receipt-wedding-{{ $orderWedding->id }}">
                                                        <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="pmt-des m-b-8">
                                            <div class="card-ptext-content">
                                                <div class="ptext-title"></div>
                                                <div class="ptext-value">{{ date('d F Y',strtotime($invoice->due_date)) }}</div>
                                            </div>
                                        </div>
                                        @foreach ($receipts as $receipt)
                                            <a class="card-link" href="#" data-toggle="modal" data-target="#mobile-receipt-{{ $receipt->id }}">
                                                <div class="card-ptext-margin">
                                                    <div class="row">
                                                        <div class="col-3">
                                                            <div class="pmt-container">
                                                                <i class="icon-copy fa fa-file-image-o" aria-hidden="true"></i>
                                                            </div>
                                                        </div>
                                                        <div class="col-9">
                                                            <div class="pmt-des">
                                                                <b>
                                                                    {{ $invoice->inv_no }}
                                                                    @if ($receipt->status == "Valid")
                                                                        <i data-toggle="tooltip" data-placement="top" title="@lang('messages.Verified')" style="color: rgb(0, 167, 14); cursor:help" class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                                                                    @else
                                                                        <i style="font-size: 0.7rem; color:rgb(255 0 0);">{{ $receipt->status }}</i>
                                                                    @endif
                                                                </b>
                                                                <div class="card-ptext-content">
                                                                    <div class="ptext-title">@lang('messages.Recived on')</div>
                                                                    <div class="ptext-value">{{ date('d F Y',strtotime($receipt->created_at)) }}</div>
                                                                    @if ($receipt->kurs_name == 'USD')
                                                                        <div class="ptext-title">@lang('messages.Amount')</div>
                                                                        <div class="ptext-value">{{ $receipt->kurs_name }} {{ currencyFormatUsd($receipt->amount) }}</div>
                                                                    @elseif($receipt->kurs_name == 'TWD')
                                                                        <div class="ptext-title">@lang('messages.Amount')</div>
                                                                        <div class="ptext-value">{{ $receipt->kurs_name }} {{ currencyFormatTwd($receipt->amount) }}</div>
                                                                    @elseif($receipt->kurs_name == 'CNY')
                                                                        <div class="ptext-title">@lang('messages.Amount')</div>
                                                                        <div class="ptext-value">{{ $receipt->kurs_name }} {{ currencyFormatCny($receipt->amount) }}</div>
                                                                    @elseif($receipt->kurs_name == 'IDR')
                                                                        <div class="ptext-title">@lang('messages.Amount')</div>
                                                                        <div class="ptext-value">{{ $receipt->kurs_name }} {{ currencyFormatIdr($receipt->amount) }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($receipt->status != "Valid")
                                                        <div class="view-receipt">
                                                            <a href="#" data-toggle="modal" data-target="#mobile-receipt-{{ $receipt->id }}">
                                                                <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </a>
                                            {{-- MODAL VALIDATE RECEIPT --}}
                                            <div class="modal fade" id="mobile-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title text-left">
                                                                <div class="title"> <i class="icon-copy fa fa-file-image-o" aria-hidden="true"></i>@lang('messages.Validate Receipt')</div>
                                                            </div>
                                                                <form id="confirmation-payment-mobile-{{ $receipt->id }}" action="/forder-wedding-confirmation-payment-{{ $receipt->id }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <div class="row text-left">
                                                                        <div class="col-md-12">
                                                                            <div class="row">
                                                                                <div class="col-sm-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-12 text-center">
                                                                                            <div class="modal-receipt-container">
                                                                                                <img src="/storage/receipt/weddings/{{ $receipt->receipt_img }}" alt="">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="row">
                                                                                        <div class="col-5"><p>@lang('messages.Order Number')</p></div>
                                                                                        <div class="col-7"><p><b>: {{ $orderWedding->orderno }}</b></p></div>
                                                                                        <div class="col-5"><p>@lang('messages.Reservation Number')</p></div>
                                                                                        <div class="col-7"><p><b>: {{ $reservation->rsv_no }}</b></p></div>
                                                                                        <div class="col-5"><p>@lang('messages.Invoice Number')</p></div>
                                                                                        <div class="col-7"><p><b>: {{ $invoice->inv_no }}</b></p></div>
                                                                                        <div class="col-5"><p>@lang('messages.Due Date')</p></div>
                                                                                        <div class="col-7"><p>: {{ date('d F Y',strtotime($invoice->due_date)) }}</p></div>
                                                                                        
                                                                                        @if ($receipt->status == 'Valid')
                                                                                            <div class="col-12">
                                                                                                <hr class="form-hr">
                                                                                            </div>
                                                                                            <div class="col-5"><p>@lang('messages.Payment Date')</p></div>
                                                                                            <div class="col-7"><p>: {{ date('d F Y',strtotime($receipt->payment_date)) }}</p></div>
                                                                                            <div class="col-5">
                                                                                                <p>@lang('messages.Payment Amount')</p>
                                                                                            </div>
                                                                                            <div class="col-7">
                                                                                                @if ($receipt->kurs_name == "USD")
                                                                                                    <p><b>: {{ currencyFormatUsd($receipt->amount) }}</b></p>
                                                                                                @elseif ($receipt->kurs_name == "CNY")
                                                                                                    <p><b>: {{ currencyFormatCny($receipt->amount) }}</b></p>
                                                                                                @elseif ($receipt->kurs_name == "TWD")
                                                                                                    <p><b>: {{ currencyFormatTwd($receipt->amount) }}</b></p>
                                                                                                @elseif ($receipt->kurs_name == "IDR")
                                                                                                    <p><b>: {{ currencyFormatIdr($receipt->amount) }}</b></p>
                                                                                                @endif
                                                                                            </div>
                                                                                            <div class="col-12">
                                                                                                <hr class="form-hr">
                                                                                            </div>
                                                                                        @endif
                                                                                        @if ($orderWedding->handled_by == $admin->id)
                                                                                            @if ($invoice->balance > 0)
                                                                                                <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="status" class="form-label">@lang('messages.Receipt Status') <span>*</span></label>
                                                                                                        <select name="status" class="custom-select @error('status') is-invalid @enderror" required>
                                                                                                            <option selected value="{{ $receipt->status }}">{{ $receipt->status }}</option>
                                                                                                            <option value="Valid">@lang('messages.Valid')</option>
                                                                                                            <option value="Invalid">@lang('messages.Invalid')</option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                @if ($receipt->status == "Pending" or $receipt->status == "Invalid")
                                                                                                    <div class="col-md-12">
                                                                                                        <div class="form-group">
                                                                                                            <label for="kurs" class="form-label">@lang('messages.Currency')<span>*</span></label>
                                                                                                            <select name="kurs" class="custom-select @error('kurs') is-invalid @enderror" required>
                                                                                                                <option selected value="{{ $receipt->kurs_name ? $receipt->kurs_name : '' }}">{{ $receipt->kurs_name ? $receipt->kurs_name : 'Select Currency' }}</option>
                                                                                                                <option value="USD">USD</option>
                                                                                                                <option value="CNY">CNY</option>
                                                                                                                <option value="TWD">TWD</option>
                                                                                                                <option value="IDR">IDR</option>
                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                        <div class="form-group">
                                                                                                            <label for="amoun" class="form-label col-form-label">@lang('messages.Amount')</label>
                                                                                                            <input type="text" name="amount" class="input-icon form-control @error('amount') is-invalid @enderror" placeholder="Insert Amount" value="{{ $receipt->amount }}" required>
                                                                                                            @error('amount')
                                                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                                                            @enderror
                                                                                                        </div>
                                                                                                    </div>
                                                                                                @else
                                                                                                    <input hidden type="text" name="kurs" value="{{ $receipt->kurs_name }}">
                                                                                                    <input hidden type="text" name="amount" value="{{ $receipt->amount }}">
                                                                                                @endif
                                                                                                <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="payment_date" class="form-label col-form-label">@lang('messages.Payment Date')</label>
                                                                                                        <input readonly type="text" id="payment_date" name="payment_date" class="form-control date-picker @error('payment_date') is-invalid @enderror" placeholder="Payment Date" value="{{ date('d F Y',strtotime($receipt->payment_date)) }}" required>
                                                                                                        @error('payment_date')
                                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="note">@lang('messages.Description') </label>
                                                                                                        <textarea name="note" class="textarea_editor form-control @error('note') is-invalid @enderror" placeholder="Description">{{ $receipt->note }}</textarea>
                                                                                                        @error('note')
                                                                                                            <span class="invalid-feedback">
                                                                                                                <strong>{{ $message }}</strong>
                                                                                                            </span>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div>
                                                                                                <input type="hidden" name="order_id" value="{{ $orderWedding->id }}">
                                                                                            @endif
                                                                                        @else
                                                                                            <div class="col-12">
                                                                                                <div class="notification">
                                                                                                    @php
                                                                                                        $validator = $admins->where('id',$orderWedding->handled_by)->first()
                                                                                                    @endphp
                                                                                                    @lang('messages.This Order can validate by') {{ $validator?$validator->name:"Admin" }}
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
                                                                @if ($orderWedding->handled_by == $admin->id)
                                                                    @if ($invoice->balance > 0)
                                                                        <button type="submit" form="confirmation-payment-mobile-{{ $receipt->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> {{ $receipt->status == 'Pending'?"Validate":"Update" }}</button>
                                                                    @endif
                                                                @endif
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="pmt-des">
                                            <div class="card-ptext-content">
                                                @if ($invoice->currency->name == 'USD')
                                                    <div class="ptext-title">@lang('messages.Total Invoice') USD</div>
                                                    <div class="ptext-value">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                                                @elseif ($invoice->currency->name == 'TWD')
                                                    <div class="ptext-title">@lang('messages.Total Invoice') USD</div>
                                                    <div class="ptext-value">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                                                    <div class="ptext-title">@lang('messages.Total Invoice') TWD</div>
                                                    <div class="ptext-value">{{ currencyFormatTwd($invoice->total_twd) }}</div>
                                                @elseif ($invoice->currency->name == 'CNY')
                                                    <div class="ptext-title">@lang('messages.Total Invoice') USD</div>
                                                    <div class="ptext-value">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                                                    <div class="ptext-title">@lang('messages.Total Invoice') CNY</div>
                                                    <div class="ptext-value">{{ currencyFormatCny($invoice->total_cny) }}</div>
                                                @elseif ($invoice->currency->name == 'IDR')
                                                    <div class="ptext-title">@lang('messages.Total Invoice') USD</div>
                                                    <div class="ptext-value">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                                                    <div class="ptext-title">@lang('messages.Total Invoice') IDR</div>
                                                    <div class="ptext-value">{{ currencyFormatIdr($invoice->total_idr) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        @php
                                            $total_payment_usd = $receipts->where('status', 'Valid')->where('kurs_id', '1')->sum('amount');
                                            $total_payment_cny = $receipts->where('status', 'Valid')->where('kurs_id', '2')->sum('amount');
                                            $total_payment_twd = $receipts->where('status', 'Valid')->where('kurs_id', '3')->sum('amount');
                                            $total_payment_idr = $receipts->where('status', 'Valid')->where('kurs_id', '4')->sum('amount');
                                        @endphp
                                        @if ($total_payment_usd > 0)
                                            <div class="pmt-des">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">@lang('messages.Total Payment') USD</div>
                                                    <div class="ptext-value">{{ currencyFormatUsd($total_payment_usd) }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($total_payment_cny > 0)
                                            <div class="pmt-des">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">@lang('messages.Total Payment') CNY</div>
                                                    <div class="ptext-value">{{ currencyFormatCny($total_payment_cny) }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($total_payment_twd > 0)
                                            <div class="pmt-des">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">@lang('messages.Total Payment') TWD</div>
                                                    <div class="ptext-value">{{ currencyFormatTwd($total_payment_twd) }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($total_payment_idr > 0)
                                            <div class="pmt-des">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">@lang('messages.Total Payment') IDR</div>
                                                    <div class="ptext-value">{{ currencyFormatIdr($total_payment_idr) }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($total_payment_idr > 0 or $total_payment_twd > 0 or $total_payment_cny > 0 or $total_payment_usd > 0)
                                            <div class="col-12">
                                                <hr class="form-hr">
                                            </div>
                                        @endif
                                        <div class="pmt-des">
                                            <div class="card-ptext-content">
                                                <div class="ptext-title">@lang('messages.Balance')</div>
                                                @if ($invoice)
                                                    @if ($invoice->balance <= 1)
                                                        <div class="ptext-value"><div class="usd-rate">@lang('messages.Paid')</div></div>
                                                    @else
                                                        @if ($invoice->currency->name == "USD")
                                                            <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->balance) }}</b></div>
                                                        @elseif ($invoice->currency->name == "CNY")
                                                            <div class="ptext-value"><b>{{ currencyFormatCny($invoice->balance) }}</b></div>
                                                        @elseif ($invoice->currency->name == "TWD")
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
                                            <div class="title">@lang('messages.Payment Receipt')</div>
                                            <span>
                                                <a href="#" data-toggle="modal" data-target="#mobile-admin-add-receipt-wedding-{{ $orderWedding->id }}">
                                                    <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                        </div>
                                        <div class="pmt-des">
                                            <div class="card-ptext-content">
                                                <div class="ptext-title">@lang('messages.Invoice No')</div>
                                                <div class="ptext-value">{{ $invoice->inv_no }}</div>
                                                <div class="ptext-title">@lang('messages.Payment deadline')</div>
                                                <div class="ptext-value">{{ date('d M Y',strtotime($invoice->due_date)) }}</div>
                                                @if ($invoice->currency->name == 'USD')
                                                    <div class="ptext-title"><b>@lang('messages.Total Price')</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></div>
                                                @elseif ($invoice->currency->name == 'TWD')
                                                    <div class="ptext-title"><b>@lang('messages.Total') USD</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></div>
                                                    <div class="ptext-title"><b>@lang('messages.Total') TWD</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatTwd($invoice->total_twd) }}</b></div>
                                                @elseif ($invoice->currency->name == 'CNY')
                                                    <div class="ptext-title"><b>@lang('messages.Total') USD</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></div>
                                                    <div class="ptext-title"><b>@lang('messages.Total') CNY</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatCny($invoice->total_cny) }}</b></div>
                                                @elseif ($invoice->currency->name == 'IDR')
                                                    <div class="ptext-title"><b>@lang('messages.Total') USD</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></div>
                                                    <div class="ptext-title"><b>@lang('messages.Total') IDR</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatIdr($invoice->total_idr) }}</b></div>
                                                @endif
                                            </div>
                                            
                                        </div>
                                        
                                    </div>
                                </div>
                            @endif
                            {{-- MODAL ADD PAYMENT CONFIRMATION WEDDING --}}
                            <div class="modal fade" id="mobile-admin-add-receipt-wedding-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="card-box">
                                            <div class="card-box-title text-left">
                                                <div class="title"><i class="icon-copy fa fa-usd" aria-hidden="true"></i>@lang('messages.Payment Confirmation')</div>
                                            </div>
                                            <form id="mobile-payment-confirm-wedding-{{ $orderWedding->id }}" action="/order-wedding-add-payment-confirmation-{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row text-left">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="row m-t-27">
                                                                    <div class="col-5"><p>@lang('messages.Order Number')</p></div>
                                                                    <div class="col-7"><p><b>: {{ $orderWedding->orderno }}</b></p></div>
                                                                    <div class="col-5"><p>@lang('messages.Reservation Number')</p></div>
                                                                    <div class="col-7"><p><b>: {{ $reservation->rsv_no }}</b></p></div>
                                                                    <div class="col-5"><p>@lang('messages.Invoice Number')</p></div>
                                                                    <div class="col-7"><p><b>: {{ $invoice->inv_no }}</b></p></div>
                                                                    <div class="col-5"><p>@lang('messages.Due Date')</p></div>
                                                                    <div class="col-7"><p>: {{ date('d F Y',strtotime($invoice->due_date)) }}</p></div>
                                                                    <div class="col-5"><p>@lang('messages.Amount')</p></div>
                                                                    <div class="col-7"><p><b>: {{ currencyFormatUsd($orderWedding->final_price) }}</b></p></div>
                                                                    <div class="col-12 m-t-18"><p><i class="icon-copy fa fa-exclamation" aria-hidden="true"></i> @lang('messages.Please make the payment before the due date and provide proof of payment to prevent the cancellation of your order.')</p></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="dropzone">
                                                                            <div class="img-preview">
                                                                                <img id="mobile-img-preview" src="#" alt="Your Image" style="max-width: 100%; display: none;" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-12 m-t-8">
                                                                        <div class="form-group">
                                                                            <label for="mobile_receipt_name" class="form-label">@lang('messages.Select Receipt') </label><br>
                                                                            <input type="file" name="desktop_receipt_name" id="mobile_receipt_name" class="custom-file-input @error('desktop_receipt_name') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('desktop_receipt_name') }}" required>
                                                                            @error('desktop_receipt_name')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="order_id" value="{{ $orderWedding->id }}">
                                                </div>
                                            </form>
                                            <div class="card-box-footer">
                                                <button type="submit" form="mobile-payment-confirm-wedding-{{ $orderWedding->id }}" class="btn btn-primary"><i class="icon-copy fa fa-upload" aria-hidden="true"></i> @lang('messages.lang')</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card-box">
                        <div class="card-box-title hide-print">
                            <div class="subtitle"><i class="icon-copy fa fa-eye" aria-hidden="true"></i>@lang('messages.Detail Order')</div>
                        </div>
                        <div class="document doc-print">
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
                                        {{ date("d M Y", strtotime($orderWedding->created_at)) }}
                                    </div>
                                </div>
                            </div>
                            <div class="business-name">{{ $business->name }}</div>
                            <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                            <hr class="form-hr">
                            <div class="row">
                                <div class="col-md-8">
                                    <table class="table tb-list">
                                        <tr>
                                            <td class="htd-1">
                                                @lang('messages.Order No')
                                            </td>
                                            <td class="htd-2">
                                                <b>{{ $orderWedding->orderno }}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="htd-1">
                                                @lang('messages.Order Date')
                                            </td>
                                            <td class="htd-2">
                                                {{ date("d M Y", strtotime($orderWedding->created_at)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            
                                            <td class="htd-1">
                                                @lang('messages.Service')
                                            </td>
                                            <td class="htd-2">
                                                @lang('messages.'.$orderWedding->service)
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            @if ($orderWedding->service == "Wedding Package")
                                                <td class="htd-1">
                                                    @lang('messages.Wedding Package')
                                                </td>
                                                <td class="htd-2">
                                                    {{ $orderWedding->wedding->name }}
                                                </td>
                                            @elseif ($orderWedding->service == "Ceremony Venue")
                                                <td class="htd-1">
                                                    @lang('messages.Sub Service')
                                                </td>
                                                <td class="htd-2">
                                                    {{ $ceremonyVenue->name }}
                                                </td>
                                            @endif
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-4">
                                    @if ($orderWedding->status == "Active")
                                        <div class="page-status" style="color: rgb(0, 156, 21)"> @lang('messages.Confirmed') <span>@lang('messages.Status'):</span></div>
                                    @elseif ($orderWedding->status == "Pending")
                                        <div class="page-status" style="color: #dd9e00">@lang('messages.'.$orderWedding->status) <span>@lang('messages.Status'):</span></div>
                                    @elseif ($orderWedding->status == "Rejected")
                                        <div class="page-status" style="color: rgb(160, 0, 0)">@lang('messages.'.$orderWedding->status) <span>@lang('messages.Status'):</span></div>
                                    @else
                                        <div class="page-status" style="color: rgb(48, 48, 48)">@lang('messages.'.$orderWedding->status) <span>@lang('messages.Status'):</span></div>
                                    @endif
                                </div>
                                {{-- CONFIRMATION NUMBER --}}
                                @if ($orderWedding->status == "Pending")
                                    <div class="col-md-12">
                                        <div class="page-subtitle {{ $orderWedding->confirmation_number?"":"empty-value" }}">
                                            @lang('messages.Confirmation Number')
                                        </div>
                                        @if ($orderWedding->status != "Approved")
                                            @if ($orderWedding->status != "Paid")
                                                @if (!$orderWedding->confirmation_number)
                                                    <form id="updateConfirmationNumber" action="/admin-fupdate-confirmation-number-{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-container-confirmation">
                                                            <div class="form-group">
                                                                <div class="btn-icon">
                                                                    <span><i class="icon-copy fi-key"></i></span>
                                                                    <input name="confirmation_number" type="text" value="{{ $orderWedding->confirmation_number }}" class="form-control input-icon @error('confirmation_number') is-invalid @enderror" placeholder="Confirmation Numbber" required>
                                                                </div>
                                                                @error('confirmation_number')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                            <div class="form-btn">
                                                                @if ($orderWedding->confirmation_number)
                                                                    <button type="submit" form="updateConfirmationNumber" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang("messages.Update")</button>
                                                                @else
                                                                    <button type="submit" form="updateConfirmationNumber" class="btn btn-primary"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang("messages.Add")</button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </form>
                                                @else
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="card-ptext-margin">
                                                                <div class="row">
                                                                    @if ($orderWedding->handled_by != Auth::user()->id)
                                                                        <div class="col-md-12 text-center">
                                                                            <div class="title">{{ $orderWedding->confirmation_number }}</div><br>
                                                                            <hr class="form-hr">
                                                                        </div>
                                                                    @endif
                                                                    <div class="col-md-6">
                                                                        <div class="card-ptext-content">
                                                                            <div class="ptext-title">Validate by</div>
                                                                            <div class="ptext-value">{{ $admin_reservation->name }}</div>
                                                                            <div class="ptext-title">@lang('messages.Date')</div>
                                                                            <div class="ptext-value">{{ date('d M Y (H:i)',strtotime($orderWedding->handled_date)) }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($orderWedding->handled_by == Auth::user()->id)
                                                        <form id="updateConfirmationNumber" action="/admin-fupdate-confirmation-number-{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="form-container-confirmation">
                                                                <div class="form-group">
                                                                    <div class="btn-icon">
                                                                        <span><i class="icon-copy fi-key"></i></span>
                                                                        <input name="confirmation_number" type="text" value="{{ $orderWedding->confirmation_number }}" class="form-control input-icon @error('confirmation_number') is-invalid @enderror" placeholder="Confirmation Numbber" required>
                                                                    </div>
                                                                    @error('confirmation_number')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                                @if ($orderWedding->handled_by == Auth::user()->id)
                                                                    <div class="form-btn">
                                                                        @if ($orderWedding->confirmation_number)
                                                                            <button type="submit" form="updateConfirmationNumber" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang("messages.Update")</button>
                                                                        @else
                                                                            <button type="submit" form="updateConfirmationNumber" class="btn btn-primary"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang("messages.Add")</button>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </form>
                                                    @endif
                                                @endif
                                            @endif
                                        @else
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="description author">
                                                        <b>@lang('messages.Confirmation Number'): </b><br>
                                                        <b>@lang('messages.Handled by'): </b><br>
                                                        <b>@lang('messages.Date'): </b><br>
                                                    </div>
                                                </div>
                                                <div class="col-6 text-right">
                                                    <div class="description author">
                                                        <b style="text-transform: uppercase;">{{ $orderWedding->confirmation_number }} </b><br>
                                                        <b>{{ $admin_reservation->name }} </b><br>
                                                        <i>{{ date('d M Y (H:i)',strtotime($orderWedding->handled_date)) }}</i>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="col-md-12">
                                        <div class="page-subtitle {{ $orderWedding->confirmation_number?"":"empty-value" }}">
                                            @lang('messages.Confirmation Number')
                                        </div>
                                        <div class="card-ptext-margin">
                                            <div class="row">
                                                <div class="col-12 text-center">
                                                    <b style="text-transform: uppercase;">{{ $orderWedding->confirmation_number }} </b><br>
                                                </div>
                                                <div class="col-12">
                                                    <hr class="form-hr">
                                                </div>
                                                <div class="col-6">
                                                    <div class="description author">
                                                        <b>@lang('messages.Handled by'): </b><br>
                                                        <b>@lang('messages.Date'): </b><br>
                                                    </div>
                                                </div>
                                                <div class="col-6 text-right">
                                                    <div class="description author">
                                                        <b>{{ $admin_reservation->name }} </b><br>
                                                        <i>{{ date('d M Y (H:i)',strtotime($orderWedding->handled_date)) }}</i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                {{-- BRIDE --}}
                                <div id="bride" class="col-md-12">
                                    <div class="page-subtitle">
                                        @lang("messages.Bride's")
                                        @if ($orderWedding->status == "Pending")
                                            @if ($orderWedding->handled_by)
                                                @if ($orderWedding->handled_by == Auth::user()->id)
                                                    <span>
                                                        <a href="#" data-toggle="modal" data-target="#edit-wedding-bride-{{ $bride->id }}"> 
                                                            <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit')" aria-hidden="true"></i>
                                                        </a>
                                                    </span>
                                                @endif
                                            @else
                                                <span>
                                                    <a href="#" data-toggle="modal" data-target="#edit-wedding-bride-{{ $bride->id }}"> 
                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit')" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="card-ptext-margin">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">@lang("messages.Groom")</div>
                                                    <div class="ptext-value">
                                                        Mr. {{ $bride->groom }}
                                                        @if ($bride->groom_chinese)
                                                            ({{ $bride->groom_chinese }})
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="card-ptext-content {{ $bride->groom_pasport_id?"":"text-red" }}">
                                                    <div class="ptext-title">@lang('messages.Passport') / @lang('messages.ID')</div>
                                                    <div class="ptext-value">
                                                        @if ($bride->groom_pasport_id)
                                                            {{ $bride->groom_pasport_id }}
                                                        @else
                                                            -
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">@lang("messages.Bride")</div>
                                                    <div class="ptext-value">
                                                        Ms. {{ $bride->bride }}
                                                        @if ($bride->bride_chinese)
                                                            ({{ $bride->bride_chinese }})
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="card-ptext-content {{ $bride->bride_pasport_id?"":"text-red" }}">
                                                    <div class="ptext-title">@lang('messages.Passport') / @lang('messages.ID')</div>
                                                    <div class="ptext-value">
                                                        @if ($bride->bride_pasport_id)
                                                            {{ $bride->bride_pasport_id }}
                                                        @else
                                                            -
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- MODAL UPDATE BRIDE --}}
                                    <div class="modal fade" id="edit-wedding-bride-{{ $bride->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i>@lang('messages.Bride')</div>
                                                    </div>
                                                    <form id="updateWeddingOrderBride" action="/admin-fupdate-wedding-order-bride/{{ $bride->id }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="groom">@lang("messages.Groom") <span> *</span></label>
                                                                    <div class="btn-icon">
                                                                        <span><i class="icon-copy fi-torso"></i></span>
                                                                        <input name="groom" type="text" value="{{ $bride->groom }}" class="form-control input-icon @error('groom') is-invalid @enderror" placeholder="@lang('messages.Groom Name')" required>
                                                                    </div>
                                                                    @error('groom')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="groom_chinese">@lang("messages.Chinese Name")</label>
                                                                    <input name="groom_chinese" type="text" value="{{ $bride->groom_chinese }}" class="form-control @error('groom_chinese') is-invalid @enderror" placeholder="@lang('messages.Groom Chinese Name')">
                                                                    @error('groom_chinese')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="groom_pasport_id">@lang("messages.Passport") / @lang('messages.ID') <span> *</span></label>
                                                                    <div class="btn-icon">
                                                                        <span><i class="icon-copy fa fa-id-card-o" aria-hidden="true"></i></span>
                                                                        <input name="groom_pasport_id" type="text" value="{{ $bride->groom_pasport_id }}" class="form-control input-icon @error('groom_pasport_id') is-invalid @enderror" placeholder="@lang('messages.Passport') / @lang('messages.ID')" required>
                                                                    </div>
                                                                    @error('groom_pasport_id')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="bride">@lang("messages.Bride's") <span> *</span></label>
                                                                    <div class="btn-icon">
                                                                        <span><i class="icon-copy fi-torso-female"></i></span>
                                                                        <input name="bride" type="text" value="{{ $bride->bride }}" class="form-control input-icon @error('bride') is-invalid @enderror" placeholder="@lang("messages.Bride's Name")" required>
                                                                    </div>
                                                                    @error('bride')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="bride_chinese">@lang("messages.Chinese Name")</label>
                                                                    <input name="bride_chinese" type="text" value="{{ $bride->bride_chinese }}" class="form-control @error('bride_chinese') is-invalid @enderror" placeholder="@lang("messages.Bride's Chinese Name")">
                                                                    @error('bride_chinese')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="bride_pasport_id">@lang("messages.Passport") / @lang('messages.ID') <span> *</span></label>
                                                                    <div class="btn-icon">
                                                                        <span><i class="icon-copy fa fa-id-card-o" aria-hidden="true"></i></span>
                                                                        <input name="bride_pasport_id" type="text" value="{{ $bride->bride_pasport_id }}" class="form-control input-icon @error('bride_pasport_id') is-invalid @enderror" placeholder="@lang('messages.Passport') / @lang('messages.ID')" required>
                                                                    </div>
                                                                    @error('bride_pasport_id')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="order_wedding_id" value="{{ $orderWedding->id }}">
                                                    </form>
                                                    <div class="modal-information">
                                                        <p>Silahkan hubungi Agent untuk melakukan validasi data! </p>
                                                        <p>Perbaiki dan sesuaikan data dengan data yang sebenarnya! </p>
                                                        <p>Klik simpan untuk menyimpan perubahan yang telah dilakukan! </p>
                                                    </div>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="updateWeddingOrderBride" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> @lang("messages.Save")</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- FLIGHT --}}
                                @if ($orderWedding->status == "Approved" or $orderWedding->status == "Paid")
                                    @if (count($flights)>0)
                                        <div id="flightSchedule" class="col-md-12">
                                            <div class="page-subtitle">
                                                @lang('messages.Flight Schedule')
                                            </div>
                                            <table class="data-table table stripe nowrap no-footer dtr-inline" >
                                                <thead>
                                                    <tr>
                                                        <th style="width: 25%" class="datatable-nosort">@lang('messages.Date')</th>
                                                        <th style="width: 20%" class="datatable-nosort">@lang('messages.Flight')</th>
                                                        <th style="width: 25%" class="datatable-nosort">@lang('messages.Type')</th>
                                                        <th style="width: 20%" class="datatable-nosort text-center">@lang('messages.Number of Invitations')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($flights as $flight)
                                                        <tr>
                                                            <td class="pd-2-8">{{ date('d M Y (H:i)',strtotime($flight->time)) }}</td>
                                                            <td class="pd-2-8">{{ $flight->flight }}</td>
                                                            <td class="pd-2-8">{{ $flight->type }} <i>({{ $flight->group }})</i></td>
                                                            <td class="pd-2-8 text-center">{{ $flight->number_of_guests }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @else
                                    <div id="flightSchedule" class="col-md-12">
                                        <div class="page-subtitle">
                                            @lang('messages.Flight Schedule')
                                            @if ($orderWedding->status != "Approved")
                                                @if ($orderWedding->status != "Paid")
                                                    @if ($orderWedding->handled_by)
                                                        @if ($orderWedding->handled_by == Auth::user()->id)
                                                            <span>
                                                                <a href="#" data-toggle="modal" data-target="#add-flight-order-wedding-{{ $orderWedding->id }}"> 
                                                                    <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                                </a>
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span>
                                                            <a href="#" data-toggle="modal" data-target="#add-flight-order-wedding-{{ $orderWedding->id }}"> 
                                                                <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                            </a>
                                                        </span>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                        <table class="data-table table stripe nowrap no-footer dtr-inline" >
                                            <thead>
                                                <tr>
                                                    <th style="width: 25%" class="datatable-nosort">@lang('messages.Date')</th>
                                                    <th style="width: 20%" class="datatable-nosort">@lang('messages.Flight')</th>
                                                    <th style="width: 25%" class="datatable-nosort">@lang('messages.Type')</th>
                                                    <th style="width: 20%" class="datatable-nosort text-center">@lang('messages.Number of Invitations')</th>
                                                    @if ($orderWedding->status != "Approved")
                                                        @if ($orderWedding->status != "Paid")
                                                            <th style="width: 10%" class="datatable-nosort text-center"></th>
                                                        @endif
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($flights as $flight)
                                                    <tr>
                                                        <td class="pd-2-8">{{ date('d M Y (H:i)',strtotime($flight->time)) }}</td>
                                                        <td class="pd-2-8">{{ $flight->flight }}</td>
                                                        <td class="pd-2-8">{{ $flight->type }} <i>({{ $flight->group }})</i></td>
                                                        <td class="pd-2-8 text-center">{{ $flight->number_of_guests }}</td>
                                                        @if ($orderWedding->status != "Approved")
                                                            @if ($orderWedding->status != "Paid")
                                                                <td class="pd-2-8 text-right">
                                                                    @if ($orderWedding->handled_by)
                                                                        @if ($orderWedding->handled_by == Auth::user()->id)
                                                                            <div class="table-action">
                                                                                <a href="#" data-toggle="modal" data-target="#update-wedding-flight-{{ $flight->id }}"> 
                                                                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                                </a>
                                                                                <form id="deleteFlightOrder{{ $flight->id }}" action="/func-delete-order-wedding-flight-admin/{{ $flight->id }}" method="post" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('delete')
                                                                                    <button form="deleteFlightOrder{{ $flight->id }}" class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash"></i></button>
                                                                                </form>
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        <div class="table-action">
                                                                            <a href="#" data-toggle="modal" data-target="#update-wedding-flight-{{ $flight->id }}"> 
                                                                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                            </a>
                                                                            <form id="deleteFlightOrder{{ $flight->id }}" action="/func-delete-order-wedding-flight-admin/{{ $flight->id }}" method="post" enctype="multipart/form-data">
                                                                                @csrf
                                                                                @method('delete')
                                                                                <button form="deleteFlightOrder{{ $flight->id }}" class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash"></i></button>
                                                                            </form>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            @endif
                                                        @endif
                                                    </tr>
                                                    @if ($orderWedding->status != "Approved")
                                                        @if ($orderWedding->status != "Paid")
                                                            {{-- MODAL UPDATE FLIGHT  --}}
                                                            <div class="modal fade" id="update-wedding-flight-{{ $flight->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content text-left">
                                                                        <div class="card-box">
                                                                            <div class="card-box-title">
                                                                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i>Edit Flight</div>
                                                                            </div>
                                                                            <form id="updateWeddingOrderFlight{{ $flight->id }}" action="/func-update-order-wedding-flight-admin/{{ $flight->id }}" method="post" enctype="multipart/form-data">
                                                                                @csrf
                                                                                @method('put')
                                                                                <div class="row">
                                                                                    <div class="col-sm-12">
                                                                                        <div class="modal-form-container">
                                                                                            <div class="row">
                                                                                                <div class="col-sm-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="flight">@lang('messages.Flight Number')</label>
                                                                                                        <input type="text" name="flight" class="form-control uppercase @error('flight') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ $flight->flight }}" required>
                                                                                                        @error('flight')
                                                                                                            <span class="invalid-feedback">
                                                                                                                {{ $message }}
                                                                                                            </span>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-sm-6">
                                                                                                    <div class="form-group">
                                                                                                        <label for="group">@lang('messages.Group') <span>*</span></label>
                                                                                                        <select name="group" class="custom-select @error('group') is-invalid @enderror" required>
                                                                                                            @if ($flight->group == "Brides")
                                                                                                                <option selected value="{{ $flight->group }}">{{ $flight->group }}</option>
                                                                                                                <option value="Invitations">@lang('messages.Invitations')</option>
                                                                                                            @elseif($flight->group == "Invitations")
                                                                                                                <option selected value="{{ $flight->group }}">{{ $flight->group }}</option>
                                                                                                                <option value="Brides">@lang("messages.Bride's")</option>
                                                                                                            @else
                                                                                                                <option selected value="">@lang('messages.Select one')</option>
                                                                                                                <option value="Invitations">@lang('messages.Invitations')</option>  
                                                                                                                <option value="Brides">@lang("messages.Bride's")</option>
                                                                                                            @endif
                                                                                                        </select>
                                                                                                        @error('group')
                                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-sm-6">
                                                                                                    <div class="form-group">
                                                                                                        <label for="type">@lang('messages.Type') <span>*</span></label>
                                                                                                        <select name="type" class="custom-select @error('type') is-invalid @enderror" required>
                                                                                                            @if ($flight->type == "Arrival")
                                                                                                                <option selected value="{{ $flight->type }}">{{ $flight->type }}</option>
                                                                                                                <option value="Departure">@lang('messages.Departure')</option>
                                                                                                            @elseif($flight->type == "Departure")
                                                                                                                <option selected value="{{ $flight->type }}">{{ $flight->type }}</option>
                                                                                                                <option value="Arrival">@lang("messages.Arrival")</option>
                                                                                                            @else
                                                                                                                <option selected value="">@lang('messages.Select one')</option>
                                                                                                                <option value="Departure">@lang('messages.Departure')</option>  
                                                                                                                <option value="Arrival">@lang("messages.Arrival")</option>
                                                                                                            @endif
                                                                                                        </select>
                                                                                                        @error('type')
                                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-sm-6">
                                                                                                    <div class="form-group">
                                                                                                        <label for="number_of_guests">@lang('messages.Number of Guests')</label>
                                                                                                        <input type="number" min="1"  name="number_of_guests" class="form-control @error('number_of_guests') is-invalid @enderror"   value="{{ $flight->number_of_guests }}" required>
                                                                                                        @error('number_of_guests')
                                                                                                            <span class="invalid-feedback">
                                                                                                                {{ $message }}
                                                                                                            </span>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-sm-6">
                                                                                                    <div class="form-group">
                                                                                                        <label for="time">@lang('messages.Date and Time')</label>
                                                                                                        <input type="text" readonly name="time" class="form-control datetimepicker @error('time') is-invalid @enderror"  placeholder="@lang('messages.Select date and time')" value="{{ date('d F Y  H:i',strtotime($flight->time)) }}" required>
                                                                                                        @error('time')
                                                                                                            <span class="invalid-feedback">
                                                                                                                {{ $message }}
                                                                                                            </span>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-sm-6">
                                                                                                    <div class="form-group">
                                                                                                        <label for="guest_name">@lang('messages.Name')</label>
                                                                                                        <input type="text" name="guest_name" class="form-control @error('guest_name') is-invalid @enderror"  placeholder="@lang('messages.Responsible Person')" value="{{ $flight->guests }}" required>
                                                                                                        @error('guest_name')
                                                                                                            <span class="invalid-feedback">
                                                                                                                {{ $message }}
                                                                                                            </span>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-sm-6">
                                                                                                    <div class="form-group">
                                                                                                        <label for="guests_contact">@lang('messages.Contact')</label>
                                                                                                        <input type="text" name="guests_contact" class="form-control @error('guests_contact') is-invalid @enderror"  placeholder="@lang('messages.Telephone')" value="{{ $flight->guests_contact }}" required>
                                                                                                        @error('guests_contact')
                                                                                                            <span class="invalid-feedback">
                                                                                                                {{ $message }}
                                                                                                            </span>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                            <div class="card-box-footer">
                                                                                <button type="submit" form="updateWeddingOrderFlight{{ $flight->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if ($orderWedding->status != "Approved")
                                            @if ($orderWedding->status != "Paid")
                                                {{-- MODAL ADD FLIGHT  --}}
                                                <div class="modal fade" id="add-flight-order-wedding-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i>Add Flight</div>
                                                                </div>
                                                                <form id="addWeddingOrderFlight{{ $orderWedding->id }}" action="/func-add-order-wedding-flight-admin/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('put')
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <div class="modal-form-container">
                                                                                <div class="row">
                                        
                                                                                    <div class="col-sm-12">
                                                                                        <div class="form-group">
                                                                                            <label for="flight[]">@lang('messages.Flight Number')</label>
                                                                                            <input type="text" name="flight[]" class="form-control uppercase @error('flight[]') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ old('flight[]') }}" required>
                                                                                            @error('flight[]')
                                                                                                <span class="invalid-feedback">
                                                                                                    {{ $message }}
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-6">
                                                                                        <div class="form-group">
                                                                                            <label for="flight_group[]">@lang('messages.Group') <span>*</span></label>
                                                                                            <select name="flight_group[]" class="custom-select @error('flight_group[]') is-invalid @enderror" required>
                                                                                                <option selected value="">@lang('messages.Select one')</option>
                                                                                                <option value="Brides">@lang("messages.Bride's")</option>
                                                                                                <option value="Invitations">@lang('messages.Invitations')</option>
                                                                                            </select>
                                                                                            @error('flight_group[]')
                                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-6">
                                                                                        <div class="form-group">
                                                                                            <label for="type[]">@lang('messages.Type') <span>*</span></label>
                                                                                            <select name="type[]" class="custom-select @error('type[]') is-invalid @enderror" required>
                                                                                                <option selected value="">Select</option>
                                                                                                <option value="Arrival">Arrival</option>
                                                                                                <option value="Departure">Departure</option>
                                                                                            </select>
                                                                                            @error('type[]')
                                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-6">
                                                                                        <div class="form-group">
                                                                                            <label for="number_of_guests[]">@lang('messages.Number of Guests')</label>
                                                                                            <input type="number" min="1"  name="number_of_guests[]" class="form-control @error('number_of_guests[]') is-invalid @enderror"   value="{{ old('number_of_guests[]') }}" required>
                                                                                            @error('number_of_guests')
                                                                                                <span class="invalid-feedback">
                                                                                                    {{ $message }}
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-6">
                                                                                        <div class="form-group">
                                                                                            <label for="time[]">@lang('messages.Date and Time')</label>
                                                                                            <input type="datetime-local" name="time[]" class="form-control @error('time[]') is-invalid @enderror"  placeholder="@lang('messages.Select date and time')" value="{{ old('time[]') }}" required>
                                                                                            @error('time[]')
                                                                                                <span class="invalid-feedback">
                                                                                                    {{ $message }}
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-6">
                                                                                        <div class="form-group">
                                                                                            <label for="guests[]">@lang('messages.Name')</label>
                                                                                            <input type="text" name="guests[]" class="form-control @error('guests[]') is-invalid @enderror"  placeholder="@lang('messages.Responsible Person')" value="{{ old('guests[]') }}" required>
                                                                                            @error('guests[]')
                                                                                                <span class="invalid-feedback">
                                                                                                    {{ $message }}
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-6">
                                                                                        <div class="form-group">
                                                                                            <label for="guests_contact[]">@lang('messages.Contact')</label>
                                                                                            <input type="text" name="guests_contact[]" class="form-control @error('guests_contact[]') is-invalid @enderror"  placeholder="@lang('messages.Telephone')" value="{{ old('guests_contact[]') }}" required>
                                                                                            @error('guests_contact[]')
                                                                                                <span class="invalid-feedback">
                                                                                                    {{ $message }}
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="after-add-more"></div>
                                                                        <div class="col-12 col-sm-12 col-md-12 text-right">
                                                                            <button type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Add More')</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                <div class="card-box-footer">
                                                                    <button type="submit" form="addWeddingOrderFlight{{ $orderWedding->id }}" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="copy hide">
                                                    <div class="col-md-12">
                                                        <div class="control-group">
                                                            <div class="modal-form-container">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                    <label for="flight[]">@lang('messages.Flight Number')</label>
                                                                                    <input type="text" name="flight[]" class="form-control uppercase @error('flight[]') is-invalid @enderror"  placeholder="@lang('messages.Insert flight number')" value="{{ old('flight[]') }}" required>
                                                                                    @error('flight[]')
                                                                                        <span class="invalid-feedback">
                                                                                            {{ $message }}
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="flight_group[]">@lang('messages.Group') <span>*</span></label>
                                                                                    <select name="flight_group[]" class="custom-select @error('flight_group[]') is-invalid @enderror" required>
                                                                                        <option selected value="">@lang('messages.Select one')</option>
                                                                                        <option value="Brides">@lang("messages.Bride's")</option>
                                                                                        <option value="Invitations">@lang('messages.Invitations')</option>
                                                                                    </select>
                                                                                    @error('flight_group[]')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="type[]">@lang('messages.Type') <span>*</span></label>
                                                                                    <select name="type[]" class="custom-select @error('type[]') is-invalid @enderror" required>
                                                                                        <option selected value="">Select</option>
                                                                                        <option value="Arrival">Arrival</option>
                                                                                        <option value="Departure">Departure</option>
                                                                                    </select>
                                                                                    @error('type[]')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="number_of_guests[]">@lang('messages.Number of Guests')</label>
                                                                                    <input type="number" min="1"  name="number_of_guests[]" class="form-control @error('number_of_guests[]') is-invalid @enderror"   value="{{ old('number_of_guests[]') }}" required>
                                                                                    @error('number_of_guests')
                                                                                        <span class="invalid-feedback">
                                                                                            {{ $message }}
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="time[]">@lang('messages.Date and Time')</label>
                                                                                    <input type="datetime-local" name="time[]" class="form-control @error('time[]') is-invalid @enderror"  placeholder="@lang('messages.Select date and time')" value="{{ old('time[]') }}" required>
                                                                                    @error('time[]')
                                                                                        <span class="invalid-feedback">
                                                                                            {{ $message }}
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="guests[]">@lang('messages.Name')</label>
                                                                                    <input type="text" name="guests[]" class="form-control @error('guests[]') is-invalid @enderror"  placeholder="@lang('messages.Responsible Person')" value="{{ old('guests[]') }}" required>
                                                                                    @error('guests[]')
                                                                                        <span class="invalid-feedback">
                                                                                            {{ $message }}
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="guests_contact[]">@lang('messages.Contact')</label>
                                                                                    <input type="text" name="guests_contact[]" class="form-control @error('guests_contact[]') is-invalid @enderror"  placeholder="@lang('messages.Telephone')" value="{{ old('guests_contact[]') }}" required>
                                                                                    @error('guests_contact[]')
                                                                                        <span class="invalid-feedback">
                                                                                            {{ $message }}
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="btn-remove-add-more" data-toggle="tooltip" data-placement="top" title="Remove">
                                                                                <button class="remove" type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i></button>
                                                                            </div>
                                                                            <div class="col-sm-12">
                                                                                <hr class="form-hr">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                                {{-- INVITATIONS --}}
                                @if ($orderWedding->status == "Approved" or $orderWedding->status == "Paid")
                                    @if (count($guests)>0)
                                        <div id="weddingInvitations" class="col-md-12">
                                            <div class="page-subtitle">
                                                Invitations
                                            </div>
                                            <table class="data-table table stripe nowrap no-footer dtr-inline" >
                                                <thead>
                                                    <tr>
                                                        <th style="width: 10%">No.</th>
                                                        <th style="width: 20%" class="datatable-nosort">ID/Passport</th>
                                                        <th style="width: 30%">Name</th>
                                                        <th style="width: 25%" class="datatable-nosort">Country</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($guests as $index=>$guest)
                                                        <tr>
                                                            <td class="pd-2-8">{{ ++$index }}</td>
                                                            <td class="pd-2-8">{{ $guest->passport_no }}</td>
                                                            <td class="pd-2-8">{{ $guest->name }} {{ $guest->chinese_name?"(".$guest->chinese_name.")":""; }}</td>
                                                            <td class="pd-2-8">{{ $guest->countries->country_name }}</td>
                                                        </tr>
                                                        {{-- MODAL DETAIL INVITATION  --}}
                                                        <div class="modal fade" id="detail-invitation-{{ $guest->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-eye" aria-hidden="true"></i>Invitations</div>
                                                                        </div>
                                                                    
                                                                        <div class="card-banner">
                                                                            <img class="img-fluid rounded" src="{{ url('storage/guests/id_passport/default.jpg') }}" alt="{{ $guest->name }}">
                                                                        </div>
                                                                        <div class="card-content">
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-12 text-center">
                                                                                        <div class="card-subtitle p-t-8">{{ $guest->name }} {{ $guest->chinese_name }}</div>
                                                                                    </div>
                                                                                    <div class="col-sm-12 text-center">
                                                                                        <p>Contact : {{ $guest->phone }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @else
                                    <div id="weddingInvitations" class="col-md-12">
                                        <div class="page-subtitle">
                                            Invitations
                                            @if ($orderWedding->status != "Approved")
                                                @if ($orderWedding->status != "Paid")
                                                    @if ($orderWedding->handled_by)
                                                        @if ($orderWedding->handled_by == Auth::user()->id)
                                                            <span>
                                                                <a href="#" data-toggle="modal" data-target="#add-invitation-order-wedding-{{ $orderWedding->id }}"> 
                                                                    <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                                </a>
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span>
                                                            <a href="#" data-toggle="modal" data-target="#add-invitation-order-wedding-{{ $orderWedding->id }}"> 
                                                                <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                            </a>
                                                        </span>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                        @if ($orderWedding->status != "Approved")
                                            @if ($orderWedding->status != "Paid")
                                                {{-- MODAL ADD INVITATIONS  --}}
                                                <div class="modal fade" id="add-invitation-order-wedding-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Invitation')</div>
                                                                </div>
                                                                <form id="addInvitationOrderWedding-{{ $orderWedding->id }}" action="/fadd-invitation-order-wedding/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="dropzone">
                                                                                        <div class="img-preview">
                                                                                            <img id="passport-img-preview" src="#" alt="Your Image" style="max-width: 100%; display: none;" />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12 m-t-8">
                                                                                    <div class="form-group">
                                                                                        <label for="passport-cover" class="form-label">Identity Card / Passport <span> *</span></label>
                                                                                        <input type="file" name="cover" id="passport-cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                                                        @error('cover')
                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label for="name">Name</label>
                                                                                        <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ old('name') }}" required>
                                                                                        @error('name')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label for="name_mandarin">Mandarin Name</label>
                                                                                        <input name="name_mandarin" type="text" class="form-control @error('name_mandarin') is-invalid @enderror" placeholder="Name" value="{{ old('name_mandarin') }}">
                                                                                        @error('name_mandarin')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label for="identification_no">ID/Passport Number</label>
                                                                                        <input name="identification_no" type="text" class="form-control @error('identification_no') is-invalid @enderror" placeholder="ID/Passpoer number" value="{{ old('identification_no') }}" required>
                                                                                        @error('identification_no')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label for="phone">Phone</label>
                                                                                        <input name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" placeholder="Contact" value="{{ old('phone') }}" required>
                                                                                        @error('phone')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label for="country">Country <span> *</span></label>
                                                                                        <select name="country" class="custom-select @error('country') is-invalid @enderror" required>
                                                                                            <option selected value="">Select one</option>
                                                                                            @foreach ($countries as $country)
                                                                                                <option value="{{ $country->id }}">{{ $country->code2 }} - {{ $country->country_name }}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                        @error('country')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                <div class="card-box-footer">
                                                                    <button type="submit" form="addInvitationOrderWedding-{{ $orderWedding->id }}" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                        <table class="data-table table stripe nowrap no-footer dtr-inline" >
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%">No.</th>
                                                    <th style="width: 20%" class="datatable-nosort">ID/Passport</th>
                                                    <th style="width: 20%">Name</th>
                                                    <th style="width: 25%" class="datatable-nosort">Country</th>
                                                    <th style="width: 10%" class="datatable-nosort text-center">Passport</th>
                                                    <th style="width: 15%" class="datatable-nosort text-center"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($guests as $index=>$guest)
                                                    @php
                                                        $passportPath = public_path('storage/weddings/invitations/'.$guest->passport_img);
                                                    @endphp
                                                    <tr>
                                                        <td class="pd-2-8">{{ ++$index }}</td>
                                                        <td class="pd-2-8">{{ $guest->passport_no }}</td>
                                                        <td class="pd-2-8">{{ $guest->name }} {{ $guest->chinese_name?"(".$guest->chinese_name.")":""; }}</td>
                                                        <td class="pd-2-8">{{ $guest->countries->country_name }}</td>
                                                        <td class="pd-2-8 text-center">
                                                            @if (file_exists($passportPath))
                                                                <i class="icon-copy fa fa-vcard-o" aria-hidden="true"></i>
                                                            @else
                                                                <i class="icon-copy ion-android-cancel"></i>
                                                            @endif
                                                        </td>
                                                        <td class="pd-2-8 text-right">
                                                            <div class="table-action">
                                                                @if ($orderWedding->status != "Approved")
                                                                    @if ($orderWedding->status != "Paid")
                                                                        @if ($orderWedding->handled_by)
                                                                            @if ($orderWedding->handled_by == Auth::user()->id)
                                                                                <a href="#" data-toggle="modal" data-target="#detail-invitation-{{ $guest->id }}"> 
                                                                                    <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                                </a>
                                                                                <a href="#" data-toggle="modal" data-target="#update-invitation-{{ $guest->id }}"> 
                                                                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit')" aria-hidden="true"></i>
                                                                                </a>
                                                                                <form id="deleteInvitationOrder{{ $guest->id }}" action="/func-delete-order-wedding-invitation-admin/{{ $guest->id }}" method="post" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('delete')
                                                                                    <button form="deleteInvitationOrder{{ $guest->id }}" class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash"></i></button>
                                                                                </form>
                                                                            @endif
                                                                        @else
                                                                            <div class="table-action">
                                                                                <a href="#" data-toggle="modal" data-target="#detail-invitation-{{ $guest->id }}"> 
                                                                                    <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                                </a>
                                                                                <a href="#" data-toggle="modal" data-target="#update-invitation-{{ $guest->id }}"> 
                                                                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit')" aria-hidden="true"></i>
                                                                                </a>
                                                                                <form id="deleteInvitationOrder{{ $guest->id }}" action="/func-delete-order-wedding-invitation-admin/{{ $guest->id }}" method="post" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('delete')
                                                                                    <button form="deleteInvitationOrder{{ $guest->id }}" class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash"></i></button>
                                                                                </form>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                            </div>
                                                            
                                                        </td>
                                                    </tr>
                                                    {{-- MODAL DETAIL INVITATION  --}}
                                                    <div class="modal fade" id="detail-invitation-{{ $guest->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-eye" aria-hidden="true"></i>Invitations</div>
                                                                    </div>
                                                                    
                                                                    <div class="card-banner">
                                                                        @if (file_exists($passportPath))
                                                                            <img class="img-fluid rounded" src="{{ asset('storage/weddings/invitations/'.$guest->passport_img) }}" alt="{{ $guest->name }}">
                                                                        @else
                                                                            <img class="img-fluid rounded" src="{{ asset('storage/weddings/invitations/default.jpg') }}" alt="{{ $guest->name }}">
                                                                        @endif
                                                                    </div>
                                                                    <div class="card-content">
                                                                        <div class="card-text">
                                                                            <div class="row ">
                                                                                <div class="col-sm-12 text-center">
                                                                                    <div class="card-subtitle p-t-8">{{ $guest->name }} {{ $guest->chinese_name }}</div>
                                                                                </div>
                                                                                <div class="col-sm-12 text-center">
                                                                                    <p>Contact : {{ $guest->phone }}</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-box-footer">
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($orderWedding->status != "Approved")
                                                        @if ($orderWedding->status != "Paid")
                                                            {{-- MODAL EDIT INVITATIONS  --}}
                                                            <div class="modal fade" id="update-invitation-{{ $guest->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content text-left">
                                                                        <div class="card-box">
                                                                            <div class="card-box-title">
                                                                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Invitation')</div>
                                                                            </div>
                                                                            <form id="editInvitationOrderWedding-{{ $guest->id }}" action="/fedit-invitation-order-wedding/{{ $guest->id }}" method="post" enctype="multipart/form-data">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <div class="row">
                                                                                    <div class="col-sm-6">
                                                                                        <div class="row">
                                                                                            <div class="col-md-12">
                                                                                                <div class="dropzone">
                                                                                                    <div class="cover-package-preview-div" id="cover-package-preview-div-{{ $index }}">
                                                                                                        @if (file_exists($passportPath))
                                                                                                            <img class="img-fluid rounded" src="{{ asset('storage/weddings/invitations/'.$guest->passport_img) }}" alt="{{ $guest->name }}">
                                                                                                        @else
                                                                                                            <div class="overlay-text">ID / Passport</div>
                                                                                                            <img class="img-fluid rounded thumbnail-image default-img" src="{{ asset('storage/weddings/invitations/default.jpg') }}" alt="{{ $guest->name }}">
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-12 m-t-8">
                                                                                                <div class="form-group">
                                                                                                    <label for="cover-{{ $index }}" class="form-label">ID / Passport</label>
                                                                                                    <input type="file" name="cover" id="cover-{{ $index }}" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                                                                    @error('cover')
                                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <div class="col-md-6">
                                                                                        <div class="row">
                                                                                            <div class="col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="name">Name</label>
                                                                                                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ $guest->name }}" required>
                                                                                                    @error('name')
                                                                                                        <span class="invalid-feedback">
                                                                                                            <strong>{{ $message }}</strong>
                                                                                                        </span>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="name_mandarin">Mandarin Name</label>
                                                                                                    <input name="name_mandarin" type="text" class="form-control @error('name_mandarin') is-invalid @enderror" placeholder="Name" value="{{ $guest->chinese_name }}">
                                                                                                    @error('name_mandarin')
                                                                                                        <span class="invalid-feedback">
                                                                                                            <strong>{{ $message }}</strong>
                                                                                                        </span>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="identification_no">ID/Passport Number</label>
                                                                                                    <input name="identification_no" type="text" class="form-control @error('identification_no') is-invalid @enderror" placeholder="ID/Passpoer number" value="{{ $guest->passport_no }}" required>
                                                                                                    @error('identification_no')
                                                                                                        <span class="invalid-feedback">
                                                                                                            <strong>{{ $message }}</strong>
                                                                                                        </span>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="phone">Phone</label>
                                                                                                    <input name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" placeholder="Contact" value="{{ $guest->phone }}" required>
                                                                                                    @error('phone')
                                                                                                        <span class="invalid-feedback">
                                                                                                            <strong>{{ $message }}</strong>
                                                                                                        </span>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="country">Country <span> *</span></label>
                                                                                                    <select name="country" class="custom-select @error('country') is-invalid @enderror" required>
                                                                                                        <option value="">Select one</option>
                                                                                                        @foreach ($countries as $edit_country)
                                                                                                            <option {{ $guest->country == $edit_country->id?"selected":""; }} value="{{ $edit_country->id }}">{{ $edit_country->code2 }} - {{ $edit_country->country_name }}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('country')
                                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                            <div class="card-box-footer">
                                                                                <button type="submit" form="editInvitationOrderWedding-{{ $guest->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                                {{-- WEDDING --}}
                                <div id="wedding" class="col-md-6">
                                    <div class="page-subtitle">
                                        @if ($orderWedding->wedding_date && $orderWedding->reception_date_start)
                                            @lang('messages.Wedding') & @lang('messages.Reception')
                                        @elseif ($orderWedding->reception_date_start)
                                            @lang('messages.Reception')
                                        @else
                                            @lang('messages.Wedding')
                                        @endif
                                        @if ($orderWedding->status == "Pending")
                                            @if ($orderWedding->handled_by)
                                                @if ($orderWedding->handled_by == Auth::user()->id)
                                                    <span>
                                                        <a href="#" data-toggle="modal" data-target="#update-wedding-order-wedding-{{ $orderWedding->id }}">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </span>
                                                @endif
                                            @else
                                                <span>
                                                    <a href="#" data-toggle="modal" data-target="#update-wedding-order-wedding-{{ $orderWedding->id }}">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                    @if ($orderWedding->wedding_date && $orderWedding->service == "Ceremony Venue")
                                        <div class="card-ptext-margin">
                                            <div class="card-ptext-content">
                                                <div class="ptext-title">
                                                    @lang('messages.Wedding Date')
                                                </div>
                                                <div class="ptext-value">
                                                    {{ date("d M Y",strtotime($orderWedding->wedding_date)) }} ({{ date('H:i',strtotime($orderWedding->slot)) }})
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($orderWedding->reception_date_start && $orderWedding->service == "Reception Venue")
                                        <div class="card-ptext-margin">
                                            <div class="card-ptext-content">
                                                <div class="ptext-title">
                                                    @lang('messages.Reception Date')
                                                </div>
                                                <div class="ptext-value">
                                                    {{ date('d M Y (H:i)',strtotime($orderWedding->reception_date_start)) }} - (22:00)
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($orderWedding->reception_date_start && $orderWedding->service == "Wedding Package")
                                        <div class="card-ptext-margin">
                                            <div class="card-ptext-content">
                                                <div class="ptext-title">
                                                    @lang('messages.Wedding Date')
                                                </div>
                                                <div class="ptext-value">
                                                    {{ date("d M Y",strtotime($orderWedding->wedding_date)) }} {{ date('H:i',strtotime($orderWedding->slot)) }}
                                                </div>
                                                <div class="ptext-title">
                                                    @lang('messages.Reception Date')
                                                </div>
                                                <div class="ptext-value">
                                                    {{ date('d M Y (H:i)',strtotime($orderWedding->reception_date_start)) }}
                                                </div>
                                                <div class="ptext-title">
                                                    @lang('messages.Number of Invitations')
                                                </div>
                                                <div class="ptext-value">
                                                    {{ $orderWedding->number_of_invitation }} @lang('messages.Invitations')
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($orderWedding->status == "Pending")
                                        {{-- MODAL UPDATE WEDDING ORDER WEDDING--}}
                                        <div class="modal fade" id="update-wedding-order-wedding-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i>@lang('messages.Wedding')</div>
                                                        </div>
                                                        <form id="updateWeddingOrderWedding" action="/admin-fupdate-wedding-order-wedding/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                @if ($orderWedding->service == "Ceremony Venue")
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="wedding_date">@lang("messages.Wedding Date") <span> *</span></label>
                                                                            <div class="btn-icon">
                                                                                <span><i class="icon-copy fi-calendar"></i></span>
                                                                                <input readonly name="wedding_date" type="text" id="wedding_date" class="form-control input-icon date-picker @error('wedding_date') is-invalid @enderror" type="text" value="{{ date('d F Y',strtotime($orderWedding->wedding_date)) }}" required>
                                                                            </div>
                                                                            @error('wedding_date')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                @elseif ($orderWedding->service == "Reception Venue")
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="reception_date_start">@lang("messages.Reception Date") <span> *</span></label>
                                                                            <div class="btn-icon">
                                                                                <span><i class="icon-copy fi-calendar"></i></span>
                                                                                <input readonly name="reception_date_start" type="text" id="reception_date_start" class="form-control input-icon date-picker @error('reception_date_start') is-invalid @enderror" type="text" value="{{ date('d F Y',strtotime($orderWedding->reception_date_start)) }}" required>
                                                                            </div>
                                                                            @error('reception_date_start')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                @elseif ($orderWedding->service == "Wedding Package")
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="wedding_date">@lang("messages.Wedding Date") <span> *</span></label>
                                                                            <div class="btn-icon">
                                                                                <span><i class="icon-copy fi-calendar"></i></span>
                                                                                <input readonly name="wedding_date" type="text" id="wedding_date" class="form-control input-icon date-picker @error('wedding_date') is-invalid @enderror" value="{{ date('d F Y',strtotime($orderWedding->wedding_date)) }}" required>
                                                                            </div>
                                                                            @error('wedding_date')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="slot">@lang("messages.Slot") <span> *</span></label>
                                                                            <div class="btn-icon">
                                                                                <span><i class="icon-copy dw dw-wall-clock2"></i></span>
                                                                                <input readonly name="slot" type="text" class="form-control input-icon time-picker-default @error('slot') is-invalid @enderror" value="{{ date('H:i',strtotime($orderWedding->slot)) }}" required>
                                                                            </div>
                                                                            @error('slot')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="reception_date_start">@lang("messages.Reception Date") <span> *</span></label>
                                                                            <div class="btn-icon">
                                                                                <span><i class="icon-copy fi-calendar"></i></span>
                                                                                <input readonly name="reception_date_start" type="text" id="reception_date_start" class="form-control input-icon datetimepicker @error('reception_date_start') is-invalid @enderror" value="{{ date('d F Y H:i',strtotime($orderWedding->reception_date_start)) }}" required>
                                                                            </div>
                                                                            @error('reception_date_start')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="number_of_invitation">@lang('messages.Number of Invitations') <span> *</span></label>
                                                                        <div class="btn-icon">
                                                                            <span><i class="icon-copy fi-torsos-all"></i></span>
                                                                            @if ($ceremonyVenue)
                                                                                @if ($ceremonyVenue->capacity < $orderWedding->number_of_invitation)
                                                                                    <input name="number_of_invitation" type="number" min="1" max="{{ $ceremonyVenue->capacity }}" class="form-control input-icon @error('number_of_invitation') is-invalid @enderror" placeholder="Maximum invitations {{ $orderWedding->capacity }}" type="text" value="{{ $orderWedding->number_of_invitation }}" required>
                                                                                @else
                                                                                    <input name="number_of_invitation" type="number" min="1" max="{{ $maxCapacity->capacity }}" class="form-control input-icon @error('number_of_invitation') is-invalid @enderror" placeholder="Maximum invitations {{ $orderWedding->capacity }}" type="text" value="{{ $orderWedding->number_of_invitation }}" required>
                                                                                @endif
                                                                            @else
                                                                                <input name="number_of_invitation" type="number" min="1" max="{{ $maxCapacity->capacity }}" class="form-control input-icon @error('number_of_invitation') is-invalid @enderror" placeholder="Maximum invitations {{ $orderWedding->capacity }}" type="text" value="{{ $orderWedding->number_of_invitation }}" required>
                                                                            @endif
                                                                        </div>
                                                                        @error('number_of_invitation')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="card-box-footer">
                                                            <button type="submit" form="updateWeddingOrderWedding" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> @lang("messages.Save")</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                {{-- WEDDING VENUE --}}
                                <div id="weddingVenue" class="col-md-6">
                                    <div class="page-subtitle">
                                        @lang('messages.Wedding Venue')
                                        <span>
                                            <a href="#" data-toggle="modal" data-target="#detail-wedding-venue-{{ $hotel->id }}"> 
                                                <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                            </a>
                                        </span>
                                    </div>
                                    {{-- MODAL DETAIL WEDDING VENUE --}}
                                    <div class="modal fade" id="detail-wedding-venue-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="subtitle"><i class="icon-copy dw dw-hotel"></i>@lang('messages.Wedding Venue')</div>
                                                    </div>
                                                    <div class="card-banner">
                                                        <img class="img-fluid rounded" src="{{ url('storage/hotels/hotels-cover/' . $hotel->cover) }}" alt="{{ $hotel->type }}">
                                                    </div>
                                                    <div class="card-content">
                                                        <div class="card-text">
                                                            <div class="row ">
                                                                <div class="col-sm-12 text-center">
                                                                    <div class="card-subtitle">{{ $hotel->name }}</div>
                                                                    <p>{{ date("d M Y",strtotime($orderWedding->wedding_date)) }} ({{ date('H:i',strtotime($orderWedding->slot)) }})</p>
                                                                    {!! $hotel->address !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-box-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-ptext-margin">
                                        <div class="card-ptext-content">
                                            <div class="ptext-title">
                                                @lang('messages.Hotel')
                                            </div>
                                            <div class="ptext-value">
                                                {{ $hotel->name }}
                                            </div>
                                            <div class="ptext-title">
                                                @lang('messages.Address')
                                            </div>
                                            <div class="ptext-value">
                                                {{ $hotel->address }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- CEREMONY VENUE --}}
                                @if ($orderWedding->status == "Pending")
                                    <div id="ceremonyVenue" class="col-md-6">
                                        <div class="page-subtitle {{ $ceremonyVenue?"":"empty-value" }}">
                                            @lang('messages.Ceremony Venue')
                                            <div class="action-container">
                                                <form id="deleteCeremonyVenue" action="/admin-fdelete-wedding-order-ceremony-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                </form>
                                                @if ($ceremonyVenue)
                                                    <button type="button" class="btn-icon" data-toggle="modal" data-target="#detail-ceremony-venue-{{ $ceremonyVenue->id }}">
                                                        <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                    </button>
                                                @endif
                                                @if ($orderWedding->handled_by)
                                                    @if ($orderWedding->handled_by == Auth::user()->id)
                                                        @if ($ceremonyVenue)
                                                            @if ($orderWedding->service != "Wedding Package")
                                                                <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-wedding-order-ceremony-venue-{{ $orderWedding->id }}">
                                                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                </button>
                                                                @if ($orderWedding->service != "Ceremony Venue")
                                                                    <button type="submit" form="deleteCeremonyVenue" class="icon-btn-remove" onclick="return confirm('Are you sure?');" data-toggle="tooltip" data-placement="top" title="Remove">
                                                                        <i class="icon-copy fa fa-trash"></i>
                                                                    </button>
                                                                @endif
                                                            @endif
                                                        @else
                                                            @if ($orderWedding->service != "Ceremony Venue")
                                                                @if ($orderWedding->service != "Wedding Package")
                                                                    <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-wedding-order-ceremony-venue-{{ $orderWedding->id }}">
                                                                        <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                                    </button>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endif
                                                @else
                                                    @if ($ceremonyVenue)
                                                        @if ($orderWedding->service != "Ceremony Venue")
                                                            @if ($orderWedding->service != "Wedding Package")
                                                                <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-wedding-order-ceremony-venue-{{ $orderWedding->id }}">
                                                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                </button>
                                                                <button type="submit" form="deleteCeremonyVenue" class="icon-btn-remove" onclick="return confirm('Are you sure?');" data-toggle="tooltip" data-placement="top" title="Remove">
                                                                    <i class="icon-copy fa fa-trash"></i>
                                                                </button>
                                                            @endif
                                                        @else
                                                            @if ($orderWedding->service != "Wedding Package")
                                                                <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-wedding-order-ceremony-venue-{{ $orderWedding->id }}">
                                                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                </button>
                                                            @endif
                                                        @endif
                                                    @else
                                                        @if ($orderWedding->service != "Wedding Package")
                                                            <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-wedding-order-ceremony-venue-{{ $orderWedding->id }}">
                                                                <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                @endif
                                                
                                            </div>
                                        </div>
                                        @if ($ceremonyVenue)
                                            {{-- MODAL DETAIL CEREMONY VENUE --}}
                                            <div class="modal fade" id="detail-ceremony-venue-{{ $ceremonyVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fa fa-bank"></i>@lang('messages.Ceremony Venue')</div>
                                                            </div>
                                                            <div class="card-banner">
                                                                <img class="img-fluid rounded" src="{{ url('storage/hotels/hotels-wedding-venue/' . $ceremonyVenue->cover) }}" alt="{{ $ceremonyVenue->type }}">
                                                            </div>
                                                            <div class="card-content">
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-12 text-center">
                                                                            <div class="card-subtitle">{{ $ceremonyVenue->name }}</div>
                                                                            <p>{{ '@ '.$hotel->name }}</p>
                                                                            <p>{{ date("d M Y",strtotime($orderWedding->wedding_date)) }} ({{ date('H:i',strtotime($orderWedding->slot)) }})</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-box-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-ptext-margin">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">
                                                        @lang('messages.Venue')
                                                    </div>
                                                    <div class="ptext-value">
                                                        {{ $ceremonyVenue->name }}
                                                    </div>
                                                    <div class="ptext-title">
                                                        @lang('messages.Date')
                                                    </div>
                                                    <div class="ptext-value">
                                                        {{ date("d M Y",strtotime($orderWedding->wedding_date)) }}
                                                        {{ date('(H:i)',strtotime($orderWedding->slot)) }}
                                                    </div>
                                                    <div class="ptext-title">
                                                        @lang('messages.Capacity')
                                                    </div>
                                                    <div class="ptext-value">
                                                        {{ $ceremonyVenue->capacity }} @lang('messages.Invitations')
                                                    </div>
                                                    <div class="ptext-title">
                                                        @lang('messages.Invitations')
                                                    </div>
                                                    <div class="ptext-value">
                                                        {{ $orderWedding->ceremony_venue_invitations }} @lang('messages.Invitations')
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($ceremonyVenue->capacity < $orderWedding->number_of_invitation)
                                                @php
                                                    $guest_outside = $orderWedding->number_of_invitation - $ceremonyVenue->capacity;
                                                @endphp
                                                <div class="notification">
                                                    <p>The ceremony venue can only accommodate {{ $ceremonyVenue->capacity }} guests, {{ $guest_outside }} guests will not have a place during the wedding ceremony.</p>
                                                </div>
                                            @endif
                                        @endif
                                        @if ($orderWedding->status != "Approved")
                                            @if ($orderWedding->status != "Paid")
                                                {{-- MODAL UPDATE CEREMONY VENUE  --}}
                                                <div class="modal fade" id="update-wedding-order-ceremony-venue-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Ceremony Venue')</div>
                                                                </div>
                                                                <form id="updateWeddingOrderCeremonyVenue" action="/admin-fupdate-wedding-order-ceremony-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('put')
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label>@lang("messages.Select Ceremony Venue") <span>*</span></label>
                                                                                <div class="card-box-content">
                                                                                    @foreach ($ceremonyVenues as $cv_id=>$c_venue)
                                                                                        @if ($orderWedding->number_of_invitation > $c_venue->capacity)
                                                                                            <input disabled type="radio" id="{{ "cv".$cv_id }}" name="ceremonial_venue_id" class="form-control @error('ceremonial_venue_id') is-invalid @enderror" value="{{ $c_venue->id }}" data-slots="{{ $c_venue->slot }}" data-basic-prices="{{ $c_venue->basic_price }}" data-arrangement-prices="{{ $c_venue->arrangement_price }}">
                                                                                            <label for="{{ "cv".$cv_id }}" class="label-radio">
                                                                                                <div class="card h-100">
                                                                                                    <img class="card-img" src="{{ asset ('storage/hotels/hotels-wedding-venue/' . $c_venue->cover) }}" alt="{{ $c_venue->service }}">
                                                                                                    <div class="name-card">
                                                                                                        <b>{{ $c_venue->name }}</b>
                                                                                                    </div>
                                                                                                    <div class="label-capacity">{{ $c_venue->capacity." guests" }}</div>
                                                                                                </div>
                                                                                                <div class="overlay-label-radio">
                                                                                                    @lang('messages.Not enough space')
                                                                                                </div>
                                                                                            </label>
                                                                                            @error('ceremonial_venue_id')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                        @else
                                                                                            @if ($orderWedding->service_id)
                                                                                                @if ($orderWedding->service_id == $c_venue->id)
                                                                                                    <input checked type="radio" id="{{ "cv".$cv_id }}" name="ceremonial_venue_id" class="form-control @error('ceremonial_venue_id') is-invalid @enderror" value="{{ $c_venue->id }}" data-slots="{{ $c_venue->slot }}" data-basic-prices="{{ $c_venue->basic_price }}" data-arrangement-prices="{{ $c_venue->arrangement_price }}">
                                                                                                    <label for="{{ "cv".$cv_id }}" class="label-radio">
                                                                                                        <div class="card h-100">
                                                                                                            <img class="card-img" src="{{ asset ('storage/hotels/hotels-wedding-venue/' . $c_venue->cover) }}" alt="{{ $c_venue->service }}">
                                                                                                            <div class="name-card">
                                                                                                                <b>{{ $c_venue->name }}</b>
                                                                                                            </div>
                                                                                                            <div class="label-capacity">{{ $c_venue->capacity." guests" }}</div>
                                                                                                        </div>
                                                                                                    </label>
                                                                                                    @error('ceremonial_venue_id')
                                                                                                        <span class="invalid-feedback">
                                                                                                            <strong>{{ $message }}</strong>
                                                                                                        </span>
                                                                                                    @enderror
                                                                                                @else
                                                                                                    <input type="radio" id="{{ "cv".$cv_id }}" name="ceremonial_venue_id" class="form-control @error('ceremonial_venue_id') is-invalid @enderror" value="{{ $c_venue->id }}" data-slots="{{ $c_venue->slot }}" data-basic-prices="{{ $c_venue->basic_price }}" data-arrangement-prices="{{ $c_venue->arrangement_price }}">
                                                                                                    <label for="{{ "cv".$cv_id }}" class="label-radio">
                                                                                                        <div class="card h-100">
                                                                                                            <img class="card-img" src="{{ asset ('storage/hotels/hotels-wedding-venue/' . $c_venue->cover) }}" alt="{{ $c_venue->service }}">
                                                                                                            <div class="name-card">
                                                                                                                <b>{{ $c_venue->name }}</b>
                                                                                                            </div>
                                                                                                            <div class="label-capacity">{{ $c_venue->capacity." guests" }}</div>
                                                                                                        </div>
                                                                                                    </label>
                                                                                                    @error('ceremonial_venue_id')
                                                                                                        <span class="invalid-feedback">
                                                                                                            <strong>{{ $message }}</strong>
                                                                                                        </span>
                                                                                                    @enderror
                                                                                                @endif
                                                                                            @else
                                                                                                <input type="radio" id="{{ "cv".$cv_id }}" name="ceremonial_venue_id" class="form-control @error('ceremonial_venue_id') is-invalid @enderror" value="{{ $c_venue->id }}" data-slots="{{ $c_venue->slot }}">
                                                                                                <label for="{{ "cv".$cv_id }}" class="label-radio">
                                                                                                    <div class="card h-100">
                                                                                                        <img class="card-img" src="{{ asset ('storage/hotels/hotels-wedding-venue/' . $c_venue->cover) }}" alt="{{ $c_venue->service }}" data-basic-prices="{{ $c_venue->basic_price }}" data-arrangement-prices="{{ $c_venue->arrangement_price }}">
                                                                                                        <div class="name-card">
                                                                                                            <b>{{ $c_venue->name }}</b>
                                                                                                        </div>
                                                                                                        <div class="label-capacity">{{ $c_venue->capacity." guests" }}</div>
                                                                                                    </div>
                                                                                                </label>
                                                                                                @error('ceremonial_venue_id')
                                                                                                    <span class="invalid-feedback">
                                                                                                        <strong>{{ $message }}</strong>
                                                                                                    </span>
                                                                                                @enderror
                                                                                            @endif
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                            <hr class="form-hr">
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="wedding_date">@lang("messages.Wedding Date") <span> *</span></label>
                                                                                <div class="btn-icon">
                                                                                    <span><i class="icon-copy fi-calendar"></i></span>
                                                                                    @if ($orderWedding->wedding_date)
                                                                                        <input readonly name="wedding_date" type="text" class="form-control input-icon date-picker @error('wedding_date') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ date("d M Y",strtotime($orderWedding->wedding_date)) }}" required>
                                                                                    @else
                                                                                        <input readonly name="wedding_date" type="text" class="form-control input-icon date-picker @error('wedding_date') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ old("wedding_date") }}" required>
                                                                                    @endif
                                                                                </div>
                                                                                @error('wedding_date')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label for="slot">Slot <span> *</span></label>
                                                                                <select name="slot" id="slot" class="custom-select @error('slot') is-invalid @enderror" required>
                                                                                    @if ($orderWedding->slot)
                                                                                        <option selected value="{{ $orderWedding->slot }}" data-basic-price="{{ $orderWedding->ceremony_venue_price }}" data-arrangement-price="{{ $orderWedding->ceremony_venue_price }}">{{ $orderWedding->slot }}</option>
                                                                                    @else
                                                                                        <option selected value="">Select Slot</option>
                                                                                    @endif
                                                                                        @if ($ceremonyVenue)
                                                                                            @php
                                                                                                $wv_slots = json_decode($ceremonyVenue->slot);
                                                                                                $wv_basic_price = json_decode($ceremonyVenue->basic_price);
                                                                                                $wv_arrangement_price = json_decode($ceremonyVenue->arrangement_price);
                                                                                                $c_slot = count($wv_slots);
                                                                                            @endphp
                                                                                            @for ($slt = 0; $slt < $c_slot; $slt++)
                                                                                                @if ($wv_slots[$slt] != $orderWedding->slot)
                                                                                                    <option value="{{ $wv_slots[$slt] }}" data-basic-price="{{ $wv_basic_price[$slt] }}" data-arrangement-price="{{ $wv_arrangement_price[$slt] }}">{{ $wv_slots[$slt] }}</option>
                                                                                                @endif
                                                                                            @endfor
                                                                                        @endif
                                                                                </select>
                                                                                @error('slot')
                                                                                    <div class="alert-form">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="ceremony_venue_invitations">@lang("messages.Number of Invitations") <span> *</span></label>
                                                                                <div class="btn-icon">
                                                                                    <span><i class="icon-copy fi-torsos-all"></i></span>
                                                                                    <input name="ceremony_venue_invitations" type="number" min="1" class="form-control input-icon @error('ceremony_venue_invitations') is-invalid @enderror" placeholder="Number of invitations" type="text" value="{{ $orderWedding->number_of_invitation }}" required>
                                                                                </div>
                                                                                @error('ceremony_venue_invitations')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="basic_price" id="basic_price">
                                                                    <input type="hidden" name="arrangement_price" id="arrangement_price">
                                                                </form>
                                                                <div class="card-box-footer">
                                                                    <button type="submit" form="updateWeddingOrderCeremonyVenue" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @else
                                    @if ($ceremonyVenue)
                                        <div id="ceremonyVenue" class="col-md-6">
                                            <div class="page-subtitle">
                                                @lang('messages.Ceremony Venue')
                                                <div class="action-container">
                                                    <button type="button" class="btn-icon" data-toggle="modal" data-target="#detail-ceremony-venue-{{ $ceremonyVenue->id }}">
                                                        <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            {{-- MODAL DETAIL CEREMONY VENUE --}}
                                            <div class="modal fade" id="detail-ceremony-venue-{{ $ceremonyVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fa fa-bank"></i>@lang('messages.Ceremony Venue')</div>
                                                            </div>
                                                            <div class="card-banner">
                                                                <img class="img-fluid rounded" src="{{ url('storage/hotels/hotels-wedding-venue/' . $ceremonyVenue->cover) }}" alt="{{ $ceremonyVenue->type }}">
                                                            </div>
                                                            <div class="card-content">
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-12 text-center">
                                                                            <div class="card-subtitle">{{ $ceremonyVenue->name }}</div>
                                                                            <p>{{ '@ '.$hotel->name }}</p>
                                                                            <p>{{ date("d M Y",strtotime($orderWedding->wedding_date)) }} ({{ date('H:i',strtotime($orderWedding->slot)) }})</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-box-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-ptext-margin">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">
                                                        @lang('messages.Venue')
                                                    </div>
                                                    <div class="ptext-value">
                                                        {{ $ceremonyVenue->name }}
                                                    </div>
                                                    <div class="ptext-title">
                                                        @lang('messages.Date')
                                                    </div>
                                                    <div class="ptext-value">
                                                        {{ date("d M Y",strtotime($orderWedding->wedding_date)) }}
                                                        {{ date('(H:i)',strtotime($orderWedding->slot)) }}
                                                    </div>
                                                    <div class="ptext-title">
                                                        @lang('messages.Capacity')
                                                    </div>
                                                    <div class="ptext-value">
                                                        {{ $ceremonyVenue->capacity }} @lang('messages.Invitations')
                                                    </div>
                                                    <div class="ptext-title">
                                                        @lang('messages.Invitations')
                                                    </div>
                                                    <div class="ptext-value">
                                                        {{ $orderWedding->ceremony_venue_invitations }} @lang('messages.Invitations')
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($ceremonyVenue->capacity < $orderWedding->number_of_invitation)
                                                @php
                                                    $guest_outside = $orderWedding->number_of_invitation - $ceremonyVenue->capacity;
                                                @endphp
                                                <div class="notification">
                                                    <p>The ceremony venue can only accommodate {{ $ceremonyVenue->capacity }} guests, {{ $guest_outside }} guests will not have a place during the wedding ceremony.</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                                {{-- DECORATION CEREMONY VENUE --}}
                                @if ($orderWedding->status == "Pending")
                                    <div id="decoration-ceremony-venue" class="col-md-6">
                                        @php
                                            $cv_decorations = $vendor_packages->where('type','Ceremony Venue Decoration');
                                        @endphp
                                        <div class="page-subtitle">
                                            @lang('messages.Decoration')
                                            <div class="action-container">
                                                @if ($ceremonyVenue)
                                                    @if ($orderWedding->ceremony_venue_decoration_id)
                                                        <form id="deleteDecorationCeremonyVenue" action="/admin-fdelete-wedding-order-decoration-ceremony-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('put')
                                                        </form>
                                                        <button type="button" class="btn-icon" data-toggle="modal" data-target="#detail-decoration-ceremony-venue-{{ $ceremonyVenueDecoration->id }}">
                                                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                        </button>
                                                        @if ($orderWedding->handled_by)
                                                            @if ($orderWedding->handled_by == $admin->id)
                                                                @if ($orderWedding->service != "Wedding Package")
                                                                    <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-decoration-ceremony-venue-{{ $orderWedding->id }}">
                                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                    </button>
                                                                    <button type="submit" form="deleteDecorationCeremonyVenue" class="icon-btn-remove" onclick="return confirm('Are you sure?');" data-toggle="tooltip" data-placement="top" title="Remove">
                                                                        <i class="icon-copy fa fa-trash"></i>
                                                                    </button>
                                                                @endif
                                                            @endif
                                                        @else
                                                            @if ($orderWedding->service != "Wedding Package")
                                                                <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-decoration-ceremony-venue-{{ $orderWedding->id }}">
                                                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                </button>
                                                                <button type="submit" form="deleteDecorationCeremonyVenue" class="icon-btn-remove" onclick="return confirm('Are you sure?');" data-toggle="tooltip" data-placement="top" title="Remove">
                                                                    <i class="icon-copy fa fa-trash"></i>
                                                                </button>
                                                            @endif
                                                        @endif
                                                    @else
                                                        @if ($orderWedding->handled_by)
                                                            @if ($orderWedding->handled_by == $admin->id)
                                                                @if ($orderWedding->status == "Pending")
                                                                    <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-decoration-ceremony-venue-{{ $orderWedding->id }}">
                                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                    </button>
                                                                @endif
                                                            @endif
                                                        @else
                                                            @if ($orderWedding->handled_by == $admin->id)
                                                                @if ($orderWedding->status == "Pending")
                                                                    <button type="button" class="btn-icon" data-toggle="modal" data-target="#update-decoration-ceremony-venue-{{ $orderWedding->id }}">
                                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                    </button>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        @if ($ceremonyVenue)
                                            @if ($orderWedding->ceremony_venue_decoration_id)
                                                {{-- MODAL DETAIL DECORATION CEREMONY VENUE --}}
                                                <div class="modal fade" id="detail-decoration-ceremony-venue-{{ $ceremonyVenueDecoration->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy dw dw-fountain"></i> @lang('messages.Ceremony Venue Decoration')</div>
                                                                </div>
                                                                <div class="card-banner">
                                                                    <img class="img-fluid rounded" src="{{ url('storage/vendors/package/' . $ceremonyVenueDecoration->cover) }}" alt="{{ $ceremonyVenueDecoration->service }}">
                                                                </div>
                                                                <div class="card-content">
                                                                    <div class="card-text">
                                                                        <div class="row ">
                                                                            <div class="col-sm-12 text-center">
                                                                                <div class="card-subtitle">{{ $ceremonyVenueDecoration->service }}</div>
                                                                                <p>{{ '@ '.$ceremonyVenue->name }}</p>
                                                                                <p>{{ date("d M Y",strtotime($orderWedding->wedding_date)) }} ({{ date('H:i',strtotime($orderWedding->slot)) }})</p>
                                                                                {!! $ceremonyVenue->description !!}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-box-footer">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-ptext-margin">
                                                    <div class="card-ptext-content">
                                                        <div class="ptext-title">
                                                            @lang('messages.Decoration')
                                                        </div>
                                                        <div class="ptext-value">
                                                            {{ $ceremonyVenueDecoration->service }}
                                                        </div>
                                                        <div class="ptext-title">
                                                            @lang('messages.Date')
                                                        </div>
                                                        <div class="ptext-value">
                                                            {{ date("d M Y",strtotime($orderWedding->wedding_date)) }}
                                                            {{ date('(H:i)',strtotime($orderWedding->slot)) }}
                                                        </div>
                                                        <div class="ptext-title">
                                                            @lang('messages.Invitations')
                                                        </div>
                                                        <div class="ptext-value">
                                                            {{ $orderWedding->ceremony_venue_invitations }} @lang('messages.Invitations')
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="card-ptext-margin">
                                                    @lang('messages.Basic Decoration, standard decoration provided by the hotel')
                                                </div>
                                            @endif
                                            @if ($orderWedding->status == "Pending")
                                                {{-- MODAL ADD DECORATION TO CEREMONY VENUE  --}}
                                                <div class="modal fade" id="update-decoration-ceremony-venue-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Decoration')</div>
                                                                </div>
                                                                <form id="updateDecorationCeremonyVenue" action="/admin-fupdate-wedding-order-decoration-ceremony-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('put')
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label>@lang("messages.Select one") <span>*</span></label>
                                                                                <div class="card-box-content">
                                                                                    @foreach ($cv_decorations as $dcv_id=>$decoration_c_venue)
                                                                                        @if ($orderWedding->ceremony_venue_decoration_id)
                                                                                            @if ($orderWedding->ceremony_venue_decoration_id == $decoration_c_venue->id)
                                                                                                <input checked type="radio" id="{{ "d_cv".$dcv_id }}" name="ceremony_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
                                                                                                <label for="{{ "d_cv".$dcv_id }}" class="label-radio">
                                                                                                    <div class="card h-100">
                                                                                                        <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration_c_venue->cover) }}" alt="{{ $decoration_c_venue->service }}">
                                                                                                        <div class="name-card">
                                                                                                            <b>{{ $decoration_c_venue->service }}</b>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </label>
                                                                                            @else
                                                                                                <input type="radio" id="{{ "d_cv".$dcv_id }}" name="ceremony_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
                                                                                                <label for="{{ "d_cv".$dcv_id }}" class="label-radio">
                                                                                                    <div class="card h-100">
                                                                                                        <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration_c_venue->cover) }}" alt="{{ $decoration_c_venue->service }}">
                                                                                                        <div class="name-card">
                                                                                                            <b>{{ $decoration_c_venue->service }}</b>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </label>
                                                                                            @endif
                                                                                        @else
                                                                                            <input type="radio" id="{{ "d_cv".$dcv_id }}" name="ceremony_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}">
                                                                                            <label for="{{ "d_cv".$dcv_id }}" class="label-radio">
                                                                                                <div class="card h-100">
                                                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration_c_venue->cover) }}" alt="{{ $decoration_c_venue->service }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
                                                                                                    <div class="name-card">
                                                                                                        <b>{{ $decoration_c_venue->service }}</b>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </label>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="basic_price" id="basic_price">
                                                                    <input type="hidden" name="arrangement_price" id="arrangement_price">
                                                                </form>
                                                                <div class="card-box-footer">
                                                                    <button type="submit" form="updateDecorationCeremonyVenue" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Change')</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @else
                                    @if ($ceremonyVenue)
                                        <div id="decoration-ceremony-venue" class="col-md-6">
                                            @php
                                                $cv_decorations = $vendor_packages->where('type','Ceremony Venue Decoration');
                                            @endphp
                                            <div class="page-subtitle">
                                                @lang('messages.Decoration')
                                                @if ($orderWedding->ceremony_venue_decoration_id)
                                                    <span>
                                                        <a href="#" data-toggle="modal" data-target="#detail-decoration-ceremony-venue-{{ $ceremonyVenueDecoration->id }}"> 
                                                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                        </a>
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($orderWedding->ceremony_venue_decoration_id)
                                                {{-- MODAL DETAIL DECORATION CEREMONY VENUE --}}
                                                <div class="modal fade" id="detail-decoration-ceremony-venue-{{ $ceremonyVenueDecoration->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy dw dw-fountain"></i> @lang('messages.Ceremony Venue Decoration')</div>
                                                                </div>
                                                                <div class="card-banner">
                                                                    <img class="img-fluid rounded" src="{{ url('storage/vendors/package/' . $ceremonyVenueDecoration->cover) }}" alt="{{ $ceremonyVenueDecoration->service }}">
                                                                </div>
                                                                <div class="card-content">
                                                                    <div class="card-text">
                                                                        <div class="row ">
                                                                            <div class="col-sm-12 text-center">
                                                                                <div class="card-subtitle">{{ $ceremonyVenueDecoration->service }}</div>
                                                                                <p>{{ '@ '.$ceremonyVenue->name }}</p>
                                                                                <p>{{ date("d M Y",strtotime($orderWedding->wedding_date)) }} ({{ date('H:i',strtotime($orderWedding->slot)) }})</p>
                                                                                {!! $ceremonyVenue->description !!}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-box-footer">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-ptext-margin">
                                                    <div class="card-ptext-content">
                                                        <div class="ptext-title">
                                                            @lang('messages.Decoration')
                                                        </div>
                                                        <div class="ptext-value">
                                                            {{ $ceremonyVenueDecoration->service }}
                                                        </div>
                                                        <div class="ptext-title">
                                                            @lang('messages.Date')
                                                        </div>
                                                        <div class="ptext-value">
                                                            {{ date("d M Y",strtotime($orderWedding->wedding_date)) }}
                                                            {{ date('(H:i)',strtotime($orderWedding->slot)) }}
                                                        </div>
                                                        <div class="ptext-title">
                                                            @lang('messages.Invitations')
                                                        </div>
                                                        <div class="ptext-value">
                                                            {{ $orderWedding->ceremony_venue_invitations }} @lang('messages.Invitations')
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="card-ptext-margin">
                                                    @lang('messages.Basic Decoration, standard decoration provided by the hotel')
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                                {{-- RECEPTION VENUE --}}
                                @if ($orderWedding->status == "Approved" or $orderWedding->status == "Paid")
                                    @if ($receptionVenue)
                                        <div id="orderWeddingReceptionVenue" class="col-md-6">
                                            <div class="page-subtitle" {{ $receptionVenue?"":"empty-value" }}>
                                                @lang('messages.Reception Venue')
                                                <span>
                                                    <a href="#" data-toggle="modal" data-target="#detail-reception-venue-{{ $receptionVenue->id }}"> 
                                                        <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                            </div>
                                            {{-- MODAL DETAIL RECEPTION VENUE --}}
                                            <div class="modal fade" id="detail-reception-venue-{{ $receptionVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy ion-beer"></i> @lang('messages.Reception Venue')</div>
                                                            </div>
                                                            <div class="card-banner">
                                                                <img class="img-fluid rounded" src="{{ asset ('storage/weddings/reception-venues/' . $receptionVenue->cover) }}" alt="{{ $receptionVenue->service }}">
                                                            </div>
                                                            <div class="card-content">
                                                                <div class="card-text">
                                                                    <div class="row ">
                                                                        <div class="col-sm-12 text-center">
                                                                            <div class="card-subtitle">{{ $receptionVenue->service }}</div>
                                                                            <p>{{ '@ '.$hotel->name }} - {{ $receptionVenue->name }}</p>
                                                                            <p>{{ $orderWedding->number_of_invitation }} @lang('messages.Invitations')</p>
                                                                            <p>{{ date('d M Y (H:i)',strtotime($orderWedding->reception_date_start)) }}</p>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <hr class="form-hr">
                                                                            {!! $receptionVenue->additional_info !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-box-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-ptext-margin">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">
                                                        @lang('messages.Reception Venue')
                                                    </div>
                                                    <div class="ptext-value">
                                                        {{ $receptionVenue->name }}
                                                    </div>
                                                    <div class="ptext-title">
                                                        @lang('messages.Date')
                                                    </div>
                                                    <div class="ptext-value">
                                                        {{ date('d M Y (H:i)',strtotime($orderWedding->reception_date_start)) }}
                                                    </div>
                                                    <div class="ptext-title">
                                                        @lang('messages.Capacity')
                                                    </div>
                                                    <div class="ptext-value">
                                                        {{ $receptionVenue->capacity }} @lang('messages.Invitations')
                                                    </div>
                                                    <div class="ptext-title">
                                                        @lang('messages.Invitations')
                                                    </div>
                                                    <div class="ptext-value">
                                                        {{ $orderWedding->reception_venue_invitations }} @lang('messages.Invitations')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    @if (count($receptionVenuePackages)>0)
                                        <div id="orderWeddingReceptionVenue" class="col-md-6">
                                            @php
                                                $receptionVenues = $receptionVenuePackages;
                                            @endphp
                                            <div class="page-subtitle" {{ $receptionVenue?"":"empty-value" }}>
                                                @lang('messages.Reception Venue')
                                                @if ($orderWedding->service != "Wedding Package")
                                                    @if ($orderWedding->status == "Pending")
                                                        @if ($orderWedding->handled_by)
                                                            @if ($orderWedding->handled_by == Auth::user()->id)
                                                                @if ($receptionVenue)
                                                                    <span>
                                                                        <form action="/admin-fdelete-wedding-order-reception-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('put')
                                                                            <button class="icon-btn-remove" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash"></i></button>
                                                                        </form>
                                                                    </span>
                                                                @endif
                                                                <span>
                                                                    <a href="#" data-toggle="modal" data-target="#update-reception-venue-{{ $orderWedding->id }}"> 
                                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                    </a>
                                                                </span>
                                                            @endif
                                                        @else
                                                            @if ($receptionVenue)
                                                                <span>
                                                                    <form action="/admin-fdelete-wedding-order-reception-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('put')
                                                                        <button class="icon-btn-remove" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash"></i></button>
                                                                    </form>
                                                                </span>
                                                            @endif
                                                            <span>
                                                                <a href="#" data-toggle="modal" data-target="#update-reception-venue-{{ $orderWedding->id }}"> 
                                                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                </a>
                                                            </span>
                                                        @endif
                                                    @endif
                                                @endif
                                                @if ($receptionVenue)
                                                    <span>
                                                        <a href="#" data-toggle="modal" data-target="#detail-reception-venue-{{ $receptionVenue->id }}"> 
                                                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                        </a>
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($receptionVenue)
                                                {{-- MODAL DETAIL RECEPTION VENUE --}}
                                                <div class="modal fade" id="detail-reception-venue-{{ $receptionVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy ion-beer"></i> @lang('messages.Reception Venue')</div>
                                                                </div>
                                                                <div class="card-banner">
                                                                    <img class="img-fluid rounded" src="{{ asset ('storage/weddings/reception-venues/' . $receptionVenue->cover) }}" alt="{{ $receptionVenue->service }}">
                                                                </div>
                                                                <div class="card-content">
                                                                    <div class="card-text">
                                                                        <div class="row ">
                                                                            <div class="col-sm-12 text-center">
                                                                                <div class="card-subtitle">{{ $receptionVenue->service }}</div>
                                                                                <p>{{ '@ '.$hotel->name }} - {{ $receptionVenue->name }}</p>
                                                                                <p>{{ $orderWedding->number_of_invitation }} @lang('messages.Invitations')</p>
                                                                                <p>{{ date('d M Y (H:i)',strtotime($orderWedding->reception_date_start)) }}</p>
                                                                            </div>
                                                                            <div class="col-sm-12">
                                                                                {!! $receptionVenue->additional_info !!}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-box-footer">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-ptext-margin">
                                                    <div class="card-ptext-content">
                                                        <div class="ptext-title">
                                                            @lang('messages.Reception Venue')
                                                        </div>
                                                        <div class="ptext-value">
                                                            {{ $receptionVenue->name }}
                                                        </div>
                                                        <div class="ptext-title">
                                                            @lang('messages.Date')
                                                        </div>
                                                        <div class="ptext-value">
                                                            {{ date('d M Y (H:i)',strtotime($orderWedding->reception_date_start)) }}
                                                        </div>
                                                        <div class="ptext-title">
                                                            @lang('messages.Capacity')
                                                        </div>
                                                        <div class="ptext-value">
                                                            {{ $receptionVenue->capacity }} @lang('messages.Invitations')
                                                        </div>
                                                        <div class="ptext-title">
                                                            @lang('messages.Invitations')
                                                        </div>
                                                        <div class="ptext-value">
                                                            {{ $orderWedding->reception_venue_invitations }} @lang('messages.Invitations')
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($orderWedding->status != "Approved")
                                                @if ($orderWedding->status != "Paid")
                                                    {{-- MODAL UPDATE RECEPTION VENUE --}}
                                                    <div class="modal fade" id="update-reception-venue-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        @if ($receptionVenue)
                                                                            <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Reception Venue')</div>
                                                                        @else
                                                                            <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Reception Venue')</div>
                                                                        @endif
                                                                    </div>
                                                                    <form id="updateReceptionVenue" action="/admin-fupdate-wedding-order-reception-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('put')
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                    <label>@lang("messages.Select Reception Venue") <span>*</span></label>
                                                                                    <div class="card-box-content">
                                                                                        @foreach ($receptionVenues as $rec_v_id=>$reception_venue)
                                                                                            @if ($orderWedding->number_of_invitation > $reception_venue->capacity)
                                                                                                <input disabled type="radio" id="{{ "rv".$rec_v_id }}" name="reception_venue_id" value="{{ $reception_venue->id }}">
                                                                                                <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                                                    <div class="card h-100">
                                                                                                        <img class="card-img" src="{{ asset ('storage/weddings/reception-venues/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}">
                                                                                                        <div class="name-card">
                                                                                                            <b>{{ $reception_venue->name }}</b>
                                                                                                        </div>
                                                                                                        <div class="label-capacity">{{ $reception_venue->capacity." guests" }}</div>
                                                                                                    </div>
                                                                                                    <div class="overlay-label-radio">
                                                                                                        @lang('messages.Not enough space')
                                                                                                    </div>
                                                                                                </label>
                                                                                            @else
                                                                                                @if ($orderWedding->reception_venue_id)
                                                                                                    @if ($orderWedding->reception_venue_id == $reception_venue->id)
                                                                                                        <input checked type="radio" id="{{ "rv".$rec_v_id }}" name="reception_venue_id" value="{{ $reception_venue->id }}">
                                                                                                        <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                                                            <div class="card h-100">
                                                                                                                <img class="card-img" src="{{ asset ('storage/weddings/reception-venues/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}">
                                                                                                                <div class="name-card">
                                                                                                                    <b>{{ $reception_venue->name }}</b>
                                                                                                                </div>
                                                                                                                <div class="label-capacity">{{ $reception_venue->capacity." guests" }}</div>
                                                                                                            </div>
                                                                                                        </label>
                                                                                                    @else
                                                                                                        <input type="radio" id="{{ "rv".$rec_v_id }}" name="reception_venue_id" value="{{ $reception_venue->id }}">
                                                                                                        <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                                                            <div class="card h-100">
                                                                                                                <img class="card-img" src="{{ asset ('storage/weddings/reception-venues/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}">
                                                                                                                <div class="name-card">
                                                                                                                    <b>{{ $reception_venue->name }}</b>
                                                                                                                </div>
                                                                                                                <div class="label-capacity">{{ $reception_venue->capacity." guests" }}</div>
                                                                                                            </div>
                                                                                                        </label>
                                                                                                    @endif
                                                                                                @else
                                                                                                    <input type="radio" id="{{ "rv".$rec_v_id }}" name="reception_venue_id" value="{{ $reception_venue->id }}">
                                                                                                    <label for="{{ "rv".$rec_v_id }}" class="label-radio">
                                                                                                        <div class="card h-100">
                                                                                                            <img class="card-img" src="{{ asset ('storage/weddings/reception-venues/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}">
                                                                                                            <div class="name-card">
                                                                                                                <b>{{ $reception_venue->name }}</b>
                                                                                                            </div>
                                                                                                            <div class="label-capacity">{{ $reception_venue->capacity." guests" }}</div>
                                                                                                        </div>
                                                                                                    </label>
                                                                                                @endif
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label for="reception_date_start">@lang("messages.Date") <span> *</span></label>
                                                                                    <div class="btn-icon">
                                                                                        <span><i class="icon-copy fi-calendar"></i></span>
                                                                                        @if ($orderWedding->reception_date_start)
                                                                                            <input readonly name="reception_date_start" type="text" class="form-control input-icon date-picker @error('reception_date_start') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ date("d M Y",strtotime($orderWedding->reception_date_start)) }}" required>
                                                                                        @else
                                                                                            <input readonly name="reception_date_start" type="text" class="wedding-date form-control input-icon date-picker @error('reception_date_start') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ old('reception_date_start') }}" required>
                                                                                        @endif
                                                                                    </div>
                                                                                    @error('reception_date_start')
                                                                                        <span class="invalid-feedback">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label for="reception_time_start">@lang("messages.Time") <span> *</span></label>
                                                                                    <div class="btn-icon">
                                                                                        <span><i class="icon-copy fi-clock"></i></span>
                                                                                        <input name="reception_time_start" type="text" class="form-control time-picker-default @error('reception_time_start') is-invalid @enderror" placeholder="Select Time"  value="{{ date('H:i',strtotime($orderWedding->reception_date_start)) }}" required>
                                                                                    </div>
                                                                                    @error('reception_time_start')
                                                                                        <span class="invalid-feedback">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label for="reception_venue_invitations">@lang("messages.Number of Invitations") <span> *</span></label>
                                                                                    <div class="btn-icon">
                                                                                        <span><i class="icon-copy fi-torsos-all"></i></span>
                                                                                        <input name="reception_venue_invitations" type="number" min="1" class="form-control input-icon @error('reception_venue_invitations') is-invalid @enderror" placeholder="Number of invitations" type="text" value="{{ $orderWedding->reception_venue_invitations }}" required>
                                                                                    </div>
                                                                                    @error('reception_venue_invitations')
                                                                                        <span class="invalid-feedback">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                    <div class="card-box-footer">
                                                                        @if ($receptionVenue)
                                                                            <button type="submit" form="updateReceptionVenue" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                                                        @else
                                                                            <button type="submit" form="updateReceptionVenue" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                        @endif
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                @endif
                                {{-- DECORATION RECEPTION VENUE --}}
                                @if ($orderWedding->status == "Approved" or $orderWedding->status == "Paid")
                                    @if ($receptionVenue)
                                        <div id="orderWeddingDecorationReceptionVenue" class="col-md-6">
                                            <div class="page-subtitle">
                                                @lang('messages.Decoration')
                                                @if ($orderWedding->reception_venue_decoration_id)
                                                    <span>
                                                        <a href="#" data-toggle="modal" data-target="#detail-decoration-reception-venue-{{ $decorationReceptionVenue->id }}"> 
                                                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                        </a>
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($orderWedding->reception_venue_decoration_id)
                                                {{-- MODAL DETAIL DECORATION RECEPTION VENUE --}}
                                                <div class="modal fade" id="detail-decoration-reception-venue-{{ $decorationReceptionVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy dw dw-fountain"></i> @lang('messages.Reception Venue Decoration')</div>
                                                                </div>
                                                                <div class="card-banner">
                                                                    <img class="img-fluid rounded" src="{{ url('storage/vendors/package/' . $decorationReceptionVenue->cover) }}" alt="{{ $decorationReceptionVenue->service }}">
                                                                </div>
                                                                <div class="card-content">
                                                                    <div class="card-text">
                                                                        <div class="row ">
                                                                            <div class="col-sm-12 text-center">
                                                                                <div class="card-subtitle">{{ $decorationReceptionVenue->service }}</div>
                                                                                <p>{{ '@ '.$receptionVenue->name }}</p>
                                                                            </div>
                                                                            <div class="col-sm-12">
                                                                                <hr class="form-hr">
                                                                                {!! $decorationReceptionVenue->description !!}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-box-footer">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-ptext-margin">
                                                    <div class="card-ptext-content">
                                                        <div class="ptext-title">
                                                            @lang('messages.Decoration')
                                                        </div>
                                                        <div class="ptext-value">
                                                            {{ $decorationReceptionVenue->service }}
                                                        </div>
                                                        <div class="ptext-title">
                                                            @lang('messages.Date')
                                                        </div>
                                                        <div class="ptext-value">
                                                            {{ date('d M Y (H:i)',strtotime($orderWedding->reception_date_start)) }}
                                                        </div>
                                                        <div class="ptext-title">
                                                            @lang('messages.Invitations')
                                                        </div>
                                                        <div class="ptext-value">
                                                            {{ $orderWedding->reception_venue_invitations }} @lang('messages.Invitations')
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="card-ptext-margin">
                                                    @lang('messages.Basic Decoration, standard decoration provided by the hotel')
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    @if ($receptionVenuePackages)
                                        <div id="orderWeddingDecorationReceptionVenue" class="col-md-6">
                                            @php
                                                $reception_v_decorations = $vendor_packages->where('type','Reception Venue Decoration')
                                            @endphp
                                            <div class="page-subtitle">
                                                @lang('messages.Decoration')
                                                @if ($orderWedding->reception_venue_decoration_id)
                                                    @if ($orderWedding->status != "Approved")
                                                        @if ($orderWedding->status != "Paid")
                                                            @if ($orderWedding->handled_by == Auth::user()->id)
                                                                @if ($orderWedding->service != "Wedding Package")
                                                                    <span>
                                                                        <form action="/admin-fdelete-wedding-order-decoration-reception-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('put')
                                                                            <button class="icon-btn-remove" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash"></i></button>
                                                                        </form>
                                                                    </span>
                                                                    <span>
                                                                        <a href="#" data-toggle="modal" data-target="#update-decoration-reception-venue-{{ $orderWedding->id }}"> 
                                                                            <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                        </a>
                                                                    </span>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endif
                                                    <span>
                                                        <a href="#" data-toggle="modal" data-target="#detail-decoration-reception-venue-{{ $decorationReceptionVenue->id }}"> 
                                                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                        </a>
                                                    </span>
                                                @else
                                                    @if ($orderWedding->status != "Approved")
                                                        @if ($orderWedding->status != "Paid")
                                                            @if ($receptionVenue)
                                                                @if ($orderWedding->handled_by == Auth::user()->id)
                                                                    @if ($orderWedding->service != "Wedding Package")
                                                                        <span>
                                                                            <a href="#" data-toggle="modal" data-target="#update-decoration-reception-venue-{{ $orderWedding->id }}"> 
                                                                                <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                            </a>
                                                                        </span>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            @if ($receptionVenue)
                                                @if ($orderWedding->reception_venue_decoration_id)
                                                    {{-- MODAL DETAIL DECORATION RECEPTION VENUE --}}
                                                    <div class="modal fade" id="detail-decoration-reception-venue-{{ $decorationReceptionVenue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy dw dw-fountain"></i> @lang('messages.Reception Venue Decoration')</div>
                                                                    </div>
                                                                    <div class="card-banner">
                                                                        <img class="img-fluid rounded" src="{{ url('storage/vendors/package/' . $decorationReceptionVenue->cover) }}" alt="{{ $decorationReceptionVenue->service }}">
                                                                    </div>
                                                                    <div class="card-content">
                                                                        <div class="card-text">
                                                                            <div class="row ">
                                                                                <div class="col-sm-12 text-center">
                                                                                    <div class="card-subtitle">{{ $decorationReceptionVenue->service }}</div>
                                                                                    <p>{{ '@ '.$receptionVenue->name }}</p>
                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <hr class="form-hr">
                                                                                    {!! $decorationReceptionVenue->description !!}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-box-footer">
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-ptext-margin">
                                                        <div class="card-ptext-content">
                                                            <div class="ptext-title">
                                                                @lang('messages.Decoration')
                                                            </div>
                                                            <div class="ptext-value">
                                                                {{ $decorationReceptionVenue->service }}
                                                            </div>
                                                            <div class="ptext-title">
                                                                @lang('messages.Date')
                                                            </div>
                                                            <div class="ptext-value">
                                                                {{ date('d M Y (H:i)',strtotime($orderWedding->reception_date_start)) }}
                                                            </div>
                                                            <div class="ptext-title">
                                                                @lang('messages.Invitations')
                                                            </div>
                                                            <div class="ptext-value">
                                                                {{ $orderWedding->reception_venue_invitations }} @lang('messages.Invitations')
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="card-ptext-margin">
                                                        @lang('messages.Basic Decoration, standard decoration provided by the hotel')
                                                    </div>
                                                @endif
                                                @if ($orderWedding->status != "Approved")
                                                    @if ($orderWedding->status != "Paid")
                                                        @if ($orderWedding->handled_by == Auth::user()->id)
                                                            @if ($orderWedding->service != "Wedding Package")
                                                                {{-- MODAL ADD DECORATION TO RECEPTION VENUE  --}}
                                                                <div class="modal fade" id="update-decoration-reception-venue-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content text-left">
                                                                            <div class="card-box">
                                                                                <div class="card-box-title">
                                                                                    @if ($decorationReceptionVenue)
                                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Reception Venue Decoration')</div>
                                                                                    @else
                                                                                        <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Decoration')</div>
                                                                                    @endif
                                                                                </div>
                                                                                <form id="updateDecorationReceptionVenue" action="/admin-fupdate-wedding-order-decoration-reception-venue/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('put')
                                                                                    <div class="row">
                                                                                        <div class="col-sm-12">
                                                                                            <div class="form-group">
                                                                                                <label>@lang("messages.Select one") <span>*</span></label>
                                                                                                <div class="card-box-content">
                                                                                                    @foreach ($reception_v_decorations as $dcv_id=>$decoration_c_venue)
                                                                                                        @if ($orderWedding->reception_venue_decoration_id)
                                                                                                            @if ($orderWedding->reception_venue_decoration_id == $decoration_c_venue->id)
                                                                                                                <input checked type="radio" id="{{ "d_cv".$dcv_id }}" name="reception_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
                                                                                                                <label for="{{ "d_cv".$dcv_id }}" class="label-radio">
                                                                                                                    <div class="card h-100">
                                                                                                                        <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration_c_venue->cover) }}" alt="{{ $decoration_c_venue->service }}">
                                                                                                                        <div class="name-card">
                                                                                                                            <b>{{ $decoration_c_venue->service }}</b>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </label>
                                                                                                            @else
                                                                                                                <input type="radio" id="{{ "d_cv".$dcv_id }}" name="reception_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
                                                                                                                <label for="{{ "d_cv".$dcv_id }}" class="label-radio">
                                                                                                                    <div class="card h-100">
                                                                                                                        <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration_c_venue->cover) }}" alt="{{ $decoration_c_venue->service }}">
                                                                                                                        <div class="name-card">
                                                                                                                            <b>{{ $decoration_c_venue->service }}</b>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </label>
                                                                                                            @endif
                                                                                                        @else
                                                                                                            <input type="radio" id="{{ "d_cv".$dcv_id }}" name="reception_venue_decoration_id" value="{{ $decoration_c_venue->id }}" data-slots="{{ $decoration_c_venue->slot }}">
                                                                                                            <label for="{{ "d_cv".$dcv_id }}" class="label-radio">
                                                                                                                <div class="card h-100">
                                                                                                                    <img class="card-img" src="{{ asset ('storage/vendors/package/' . $decoration_c_venue->cover) }}" alt="{{ $decoration_c_venue->service }}" data-basic-prices="{{ $decoration_c_venue->basic_price }}" data-arrangement-prices="{{ $decoration_c_venue->arrangement_price }}">
                                                                                                                    <div class="name-card">
                                                                                                                        <b>{{ $decoration_c_venue->service }}</b>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </label>
                                                                                                        @endif
                                                                                                    @endforeach
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <input type="hidden" name="basic_price" id="basic_price">
                                                                                    <input type="hidden" name="arrangement_price" id="arrangement_price">
                                                                                </form>
                                                                                <div class="card-box-footer">
                                                                                    @if ($decorationReceptionVenue)
                                                                                        <button type="submit" form="updateDecorationReceptionVenue" class="btn btn-primary"><i class="icon-copy fa fa-pencil"></i> @lang('messages.Change')</button>
                                                                                    @else
                                                                                        <button type="submit" form="updateDecorationReceptionVenue" class="btn btn-primary"><i class="icon-copy fa fa-plus-circle"></i> @lang('messages.Add')</button>
                                                                                    @endif
                                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close"></i> @lang('messages.Cancel')</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                @endif
                                {{-- TRANSPORT --}}
                                @if ($orderWedding->status == "Approved" or $orderWedding->status == "Paid")
                                    @if (count($transportInvitations)>0 or $transport_bride_id)
                                        <div id="weddingTransport" class="col-md-12">
                                            <div class="page-subtitle">
                                                Transports
                                            </div>
                                            <table class="data-table table stripe nowrap no-footer dtr-inline" >
                                                <thead>
                                                    <tr>
                                                        <th style="width: 30%" class="datatable-nosort">@lang('messages.Date')</th>
                                                        <th style="width: 40%" class="datatable-nosort">@lang('messages.Services')</th>
                                                        <th style="width: 15%" class="datatable-nosort">@lang('messages.Price')</th>
                                                        <th style="width: 10%" class="datatable-nosort"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{--TRANSPORT BRIDES--}}
                                                    @if ($transport_bride_id)
                                                            @php
                                                                $bride_transport = $transports->where('id',$transport_bride_id)->first();
                                                            @endphp
                                                            @if ($bride_transport)
                                                                <tr>
                                                                    <td class="pd-2-8">{{ date('d M Y',strtotime($orderWedding->checkin)) }}</td>
                                                                    <td class="pd-2-8">Airport Shuttle (Arrival), {{ $bride_transport->brand }} - {{ $bride_transport->name }}</td>
                                                                    <td class="pd-2-8"><i>Include</i></td>
                                                                    <td class="pd-2-8 text-right">
                                                                        <a href="#" data-toggle="modal" data-target="#detail-transport_bride-service-{{ $bride_transport->id }}"> 
                                                                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="pd-2-8">{{ date('d M Y',strtotime($orderWedding->checkout)) }}</td>
                                                                    <td class="pd-2-8">Airport Shuttle (Departure), {{ $bride_transport->brand }} - {{ $bride_transport->name }}</td>
                                                                    <td class="pd-2-8"><i>Include</i></td>
                                                                    <td class="pd-2-8 text-right">
                                                                        <a href="#" data-toggle="modal" data-target="#detail-transport_bride-service-{{ $bride_transport->id }}"> 
                                                                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                {{-- MODAL DETAIL TRANSPORT INVITATION SERVICE --}}
                                                                <div class="modal fade" id="detail-transport_bride-service-{{ $bride_transport->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content text-left">
                                                                            <div class="card-box">
                                                                                <div class="card-box-title">
                                                                                    <div class="subtitle"><i class="icon-copy fa fa-certificate" aria-hidden="true"></i> {{ $bride_transport->brand }} - {{ $bride_transport->name }}</div>
                                                                                </div>
                                                                                <div class="card-banner">
                                                                                    <img class="img-fluid rounded" src="{{ url('/storage/transports/transports-cover/'. $bride_transport->cover) }}" alt="{{ $bride_transport->name }}">
                                                                                </div>
                                                                                <div class="card-content">
                                                                                    <div class="card-text">
                                                                                        <div class="row ">
                                                                                            <div class="col-sm-12 text-center">
                                                                                                <div class="card-subtitle p-t-0">{{ $bride_transport->brand }} - {{ $bride_transport->name }}</div>
                                                                                                <p>
                                                                                                    Airport Shuttle
                                                                                                </p>
                                                                                                <p>
                                                                                                    {{ date("d M Y",strtotime($orderWedding->wedding_date)) }}
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-box-footer">
                                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                    @endif
                                                    {{--TRANSPORT INVITATION--}}
                                                    @if ($transportInvitations)
                                                        @foreach ($transportInvitations as $transport_invitation)
                                                            <tr class="{{ $transport_invitation->price <= 0?"bg-notif":""; }}">
                                                                <td class="pd-2-8">{{ date('d M Y (H:i)',strtotime($transport_invitation->date)) }}</td>
                                                                <td class="pd-2-8">Airport Shuttle ({{ $transport_invitation->desc_type=="In"?"Arrival":"Departure" }}), {{ $transport_invitation->transport->brand }} - {{ $transport_invitation->transport->name }}</td>
                                                                <td class="pd-2-8">{{ $transport_invitation->price?'$ ' . number_format(($transport_invitation->price), 0, ',', '.'): "$ 0" }}</td>
                                                                <td class="pd-2-8 text-right">
                                                                    @if ($orderWedding->handled_by)
                                                                        @if ($orderWedding->handled_by == Auth::user()->id)
                                                                            @if ($orderWedding->status != "Approved")
                                                                                @if ($orderWedding->status != "Paid")
                                                                                    <span>
                                                                                        <a href="#" data-toggle="modal" data-target="#update-transport-invitation-{{ $transport_invitation->id }}">
                                                                                            <i class="fa fa-pencil"></i>
                                                                                        </a>
                                                                                    </span>
                                                                                @endif
                                                                            @endif
                                                                        @endif
                                                                    @else
                                                                        @if ($orderWedding->status != "Approved")
                                                                            @if ($orderWedding->status != "Paid")
                                                                                <span>
                                                                                    <a href="#" data-toggle="modal" data-target="#update-transport-invitation-{{ $transport_invitation->id }}">
                                                                                        <i class="fa fa-pencil"></i>
                                                                                    </a>
                                                                                </span>
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                    <a href="#" data-toggle="modal" data-target="#detail-transport_invitation-service-{{ $transport_invitation->id }}"> 
                                                                        <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            {{-- MODAL DETAIL TRANSPORT INVITATION SERVICE --}}
                                                            <div class="modal fade" id="detail-transport_invitation-service-{{ $transport_invitation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content text-left">
                                                                        <div class="card-box">
                                                                            <div class="card-box-title">
                                                                                <div class="subtitle"><i class="icon-copy fa fa-certificate" aria-hidden="true"></i> {{ $transport_invitation->transport->brand }} - {{ $transport_invitation->transport->name }}</div>
                                                                            </div>
                                                                            <div class="card-banner">
                                                                                <img class="img-fluid rounded" src="{{ url('/storage/transports/transports-cover/'. $transport_invitation->transport->cover) }}" alt="{{ $transport_invitation->transport->name }}">
                                                                            </div>
                                                                            <div class="card-content">
                                                                                <div class="card-text">
                                                                                    <div class="row ">
                                                                                        <div class="col-sm-12 text-center">
                                                                                            <div class="card-subtitle p-t-0">{{ $transport_invitation->transport->brand }} - {{ $transport_invitation->transport->name }}</div>
                                                                                            <p>
                                                                                                Airport Shuttle
                                                                                            </p>
                                                                                            <p>
                                                                                                {{ date("d M Y",strtotime($orderWedding->wedding_date)) }} ({{ date('H:i',strtotime($orderWedding->slot)) }}) | 
                                                                                                {{ '$ ' . number_format(($transport_invitation->price), 0, ',', '.') }}
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card-box-footer">
                                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                            
                                            @if ($transport_price > 0)
                                                <div class="box-price-kicked">
                                                    <div class="row">
                                                        <div class="col-6 col-sm-8 text-right">
                                                            <div class="normal-text">
                                                                <b>
                                                                    @lang('messages.Total')
                                                                </b>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-sm-4 text-right">
                                                            @if ($transportPriceContainZero)
                                                                <div class="normal-text text-red">
                                                                    <b>TBA</b>
                                                                </div>
                                                            @else
                                                                <div class="normal-text">
                                                                    <b>
                                                                        {{ '$ ' . number_format(($transport_price), 0, ',', '.') }}
                                                                    </b>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <div id="weddingTransport" class="col-md-12">
                                        <div class="page-subtitle">
                                            Transports
                                            @if ($orderWedding->handled_by)
                                                @if ($orderWedding->handled_by == $admin->id)
                                                    @if ($orderWedding->status != "Approved")
                                                        @if ($orderWedding->status != "Paid")
                                                            <span>
                                                                <a href="#" data-toggle="modal" data-target="#add-order-wedding-transport-{{ $orderWedding->id }}">
                                                                    <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                                </a>
                                                            </span>
                                                        @endif
                                                    @endif
                                                @endif
                                            @else
                                                @if ($orderWedding->status != "Approved")
                                                    @if ($orderWedding->status != "Paid")
                                                        <span>
                                                            <a href="#" data-toggle="modal" data-target="#add-order-wedding-transport-{{ $orderWedding->id }}">
                                                                <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                            </a>
                                                        </span>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                        <table class="data-table table stripe nowrap no-footer dtr-inline" >
                                            <thead>
                                                <tr>
                                                    <th style="width: 30%" class="datatable-nosort">@lang('messages.Date')</th>
                                                    <th style="width: 40%" class="datatable-nosort">@lang('messages.Services')</th>
                                                    <th style="width: 15%" class="datatable-nosort">@lang('messages.Price')</th>
                                                    <th style="width: 10%" class="datatable-nosort"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{--TRANSPORT BRIDES--}}
                                                @if ($transport_bride_id)
                                                    @php
                                                        $bride_transport = $transports->where('id',$transport_bride_id)->first();
                                                        $no_tr = 0;
                                                    @endphp
                                                    @if ($bride_transport)
                                                        <tr>
                                                            <td class="pd-2-8">{{ date('d M Y',strtotime($orderWedding->checkin)) }}</td>
                                                            <td class="pd-2-8">Airport Shuttle ({{ $transport_bride_type =="In"?"Arrival":"Departure" }}), {{ $bride_transport->brand }} - {{ $bride_transport->name }}</td>
                                                            <td class="pd-2-8"><i>Include</i></td>
                                                            <td class="pd-2-8 text-right">
                                                                <a href="#" data-toggle="modal" data-target="#detail-transport_bride-service-{{ $bride_transport->id }}"> 
                                                                    <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pd-2-8">{{ date('d M Y',strtotime($orderWedding->checkout)) }}</td>
                                                            <td class="pd-2-8">Airport Shuttle ({{ $transport_bride_type =="In"?"Arrival":"Departure" }}), {{ $bride_transport->brand }} - {{ $bride_transport->name }}</td>
                                                            <td class="pd-2-8"><i>Include</i></td>
                                                            <td class="pd-2-8 text-right">
                                                                <a href="#" data-toggle="modal" data-target="#detail-transport_bride-service-{{ $bride_transport->id }}"> 
                                                                    <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        {{-- MODAL DETAIL TRANSPORT INVITATION SERVICE --}}
                                                        <div class="modal fade" id="detail-transport_bride-service-{{ $bride_transport->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-certificate" aria-hidden="true"></i> {{ $bride_transport->brand }} - {{ $bride_transport->name }}</div>
                                                                        </div>
                                                                        <div class="card-banner">
                                                                            <img class="img-fluid rounded" src="{{ url('/storage/transports/transports-cover/'. $bride_transport->cover) }}" alt="{{ $bride_transport->name }}">
                                                                        </div>
                                                                        <div class="card-content">
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-12 text-center">
                                                                                        <div class="card-subtitle p-t-0">{{ $bride_transport->brand }} - {{ $bride_transport->name }}</div>
                                                                                        <p>
                                                                                            Airport Shuttle
                                                                                        </p>
                                                                                        <p>
                                                                                            {{ date("d M Y",strtotime($orderWedding->wedding_date)) }}
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                                {{--TRANSPORT INVITATION--}}
                                                @if ($transportInvitations)
                                                    @foreach ($transportInvitations as $transport_invitation)
                                                        <tr class="{{ $transport_invitation->price <= 0?"bg-notif":""; }}">
                                                            <td class="pd-2-8">{{ date('d M Y (H:i)',strtotime($transport_invitation->date)) }}</td>
                                                            <td class="pd-2-8">Airport Shuttle ({{ $transport_invitation->desc_type=="In"?"Arrival":"Departure" }}), {{ $transport_invitation->transport->brand }} - {{ $transport_invitation->transport->name }}</td>
                                                            <td class="pd-2-8">{{ $transport_invitation->price?'$ ' . number_format(($transport_invitation->price), 0, ',', '.'): "$ 0" }}</td>
                                                            <td class="pd-2-8 text-right">
                                                                @if ($orderWedding->handled_by)
                                                                    @if ($orderWedding->handled_by == Auth::user()->id)
                                                                        @if ($orderWedding->status != "Approved")
                                                                            @if ($orderWedding->status != "Paid")
                                                                                <span>
                                                                                    <a href="#" data-toggle="modal" data-target="#update-transport-invitation-{{ $transport_invitation->id }}">
                                                                                        <i class="fa fa-pencil"></i>
                                                                                    </a>
                                                                                </span>
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                @else
                                                                    @if ($orderWedding->status != "Approved")
                                                                        @if ($orderWedding->status != "Paid")
                                                                            <span>
                                                                                <a href="#" data-toggle="modal" data-target="#update-transport-invitation-{{ $transport_invitation->id }}">
                                                                                    <i class="fa fa-pencil"></i>
                                                                                </a>
                                                                            </span>
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                                <a href="#" data-toggle="modal" data-target="#detail-transport_invitation-service-{{ $transport_invitation->id }}"> 
                                                                    <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @if ($orderWedding->status != "Approved")
                                                            @if ($orderWedding->status != "Paid")
                                                                {{-- MODAL UPDATE TRANSPORT IVITATION --}}
                                                                <div class="modal fade" id="update-transport-invitation-{{ $transport_invitation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="card-box">
                                                                                <div class="card-box-title">
                                                                                    <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i>Airport Shuttle, {{ $transport_invitation->desc_type=="In"?"Arrival":"Departure" }}, {{ $transport_invitation->transport->brand }} - {{ $transport_invitation->transport->name }}</div>
                                                                                </div>
                                                                                <form id="updateTransportInvitation{{ $transport_invitation->id }}" action="/admin-fupdate-transport-invitation/{{ $transport_invitation->id }}" method="post" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('PUT')
                                                                                    <div class="row">
                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="price">Price <span> *</span></label>
                                                                                                <div class="btn-icon">
                                                                                                    <span>$</span>
                                                                                                    <input name="price" type="number" class="form-control input-icon @error('price') is-invalid @enderror" placeholder="Insert Price" type="text" value="{{ $transport_invitation->price }}" required>
                                                                                                </div>
                                                                                                @error('price')
                                                                                                    <span class="invalid-feedback">
                                                                                                        <strong>{{ $message }}</strong>
                                                                                                    </span>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>
                                                                                <div class="card-box-footer">
                                                                                    <button type="submit" form="updateTransportInvitation{{ $transport_invitation->id }}" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> @lang("messages.Save")</button>
                                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif
                                                        {{-- MODAL DETAIL TRANSPORT INVITATION SERVICE --}}
                                                        <div class="modal fade" id="detail-transport_invitation-service-{{ $transport_invitation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-certificate" aria-hidden="true"></i> {{ $transport_invitation->transport->brand }} - {{ $transport_invitation->transport->name }}</div>
                                                                        </div>
                                                                        <div class="card-banner">
                                                                            <img class="img-fluid rounded" src="{{ url('/storage/transports/transports-cover/'. $transport_invitation->transport->cover) }}" alt="{{ $transport_invitation->transport->name }}">
                                                                        </div>
                                                                        <div class="card-content">
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-12 text-center">
                                                                                        <div class="card-subtitle p-t-0">{{ $transport_invitation->transport->brand }} - {{ $transport_invitation->transport->name }}</div>
                                                                                        <p>
                                                                                            Airport Shuttle
                                                                                        </p>
                                                                                        <p>
                                                                                            {{ date("d M Y",strtotime($orderWedding->wedding_date)) }} ({{ date('H:i',strtotime($orderWedding->slot)) }}) | 
                                                                                            {{ '$ ' . number_format(($transport_invitation->price), 0, ',', '.') }}
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                        {{-- MODAL ADD TRANSPORT  --}}
                                        <div class="modal fade" id="add-order-wedding-transport-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Transport')</div>
                                                        </div>
                                                        <form id="addWeddingPlannerTransport-{{ $orderWedding->id }}" action="/admin-fadd-transport-invitation/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>@lang('messages.Select Transport') <span>*</span></label>
                                                                        <div class="card-box-content">
                                                                            @foreach ($transports as $transport)
                                                                                <input type="radio" id="{{ "trns_rv".$transport->id }}" name="transport_id" value="{{ $transport->id }}" data-capacity="{{ $transport->capacity }}">
                                                                                <label for="{{ "trns_rv".$transport->id }}" class="label-radio">
                                                                                    <div class="card h-100">
                                                                                        <img class="card-img" src="{{ asset ('storage/transports/transports-cover/' . $transport->cover) }}" alt="{{ $transport->brand." ".$transport->name }}">
                                                                                        <div class="name-card">
                                                                                            <b>{{ $transport->brand." ".$transport->name }}</b>
                                                                                        </div>
                                                                                        <div class="label-capacity">{{ $transport->capacity }} @lang('messages.seats')</div>
                                                                                    </div>
                                                                                </label>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if ($orderWedding->service == "Wedding Package")
                                                                    <div class="col-md-6">
                                                                        <div class="form-group ">
                                                                            <label for="date">@lang('messages.Date')<span> *</span></label>
                                                                            <select name="date" class="custom-select @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                                                                                <option selected value="">@lang('messages.Select one')</option>
                                                                                @for ($wd = 0; $wd <= $orderWedding->duration; $wd++)
                                                                                    <option value="{{ date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) }}">{{ date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) }} 
                                                                                        {{ date('Y-m-d',strtotime($orderWedding->checkin)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) ? "Check-in" : "" }}
                                                                                        {{ date('Y-m-d',strtotime($orderWedding->wedding_date)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) ? "Wedding Ceremony" : "" }}
                                                                                        {{ date('Y-m-d',strtotime($orderWedding->checkout)) == date('Y-m-d',strtotime('+'.$wd .' days',strtotime($orderWedding->checkin))) ? "Check-out" : "" }}
                                                                                    </option>
                                                                                @endfor
                                                                            </select>
                                                                            @error('date')
                                                                                <div class="alert-form">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="date" class="form-label">@lang('messages.Date')</label>
                                                                            <input readonly type="text" name="date" class="form-control date-picker td-input @error('date') is-invalid @enderror" placeholder="@lang('messages.Select date')" value="{{ old('date') }}" required>
                                                                            @error('date')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="time" class="form-label">@lang('messages.Time')</label>
                                                                        <input type="text" name="time" class="form-control time-picker @error('time') is-invalid @enderror" placeholder="@lang('messages.Select time')" value="{{ old('time') }}" required>
                                                                        @error('time')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group ">
                                                                        <label for="type">@lang('messages.Type')<span> *</span></label>
                                                                        <select name="type" class="custom-select @error('type') is-invalid @enderror" required>
                                                                            <option selected value="">@lang('messages.Select one')</option>
                                                                            <option value="Airport Shuttle In">@lang('messages.Airport Shuttle') (@lang('messages.Arrival'))</option>
                                                                            <option value="Airport Shuttle Out">@lang('messages.Airport Shuttle') (@lang('messages.Departure'))</option>
                                                                        </select>
                                                                        @error('type')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                @if ($orderWedding->service == "Wedding Package")
                                                                    <input type="hidden" name="passenger" value="Invitations">
                                                                @else
                                                                    <div class="col-md-6">
                                                                        <div class="form-group ">
                                                                            <label for="passenger">@lang('messages.Passenger')<span> *</span></label>
                                                                            <select name="passenger" class="custom-select @error('passenger') is-invalid @enderror" value="{{ old('passenger') }}" required>
                                                                                <option selected value="">@lang('messages.Select one')</option>
                                                                                <option value="Bride">@lang('messages.Bride')</option>
                                                                                <option value="Invitations">@lang('messages.Invitations')</option>
                                                                            </select>
                                                                            @error('passenger')
                                                                                <div class="alert-form">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="remark" class="form-label">@lang('messages.Remark')</label>
                                                                        <textarea name="remark" class="textarea_editor form-control @error('remark') is-invalid @enderror" placeholder="Insert remark">{!! old('remark') !!}</textarea>
                                                                        @error('remark')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="card-box-footer">
                                                            <button type="submit" form="addWeddingPlannerTransport-{{ $orderWedding->id }}" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Add')</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($transport_price > 0)
                                            <div class="box-price-kicked">
                                                <div class="row">
                                                    <div class="col-6 col-sm-8 text-right">
                                                        <div class="normal-text">
                                                            <b>
                                                                @lang('messages.Total')
                                                            </b>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-sm-4 text-right">
                                                        @if ($transportPriceContainZero)
                                                            <div class="normal-text text-red">
                                                                <b>TBA</b>
                                                            </div>
                                                        @else
                                                            <div class="normal-text">
                                                                <b>
                                                                    {{ '$ ' . number_format(($transport_price), 0, ',', '.') }}
                                                                </b>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- ACCOMMODATION --}}
                                @if ($orderWedding->status == "Approved" or $orderWedding->status == "Paid")
                                    @if (count($weddingAccommodations)>0 or $orderWedding->room_bride_id)
                                        <div id="weddingAccommodation" class="col-md-12">
                                            <div class="page-subtitle">
                                                Accommodation
                                            </div>
                                            <table class="data-table table stripe nowrap no-footer dtr-inline" >
                                                <thead>
                                                    <tr>
                                                        <th style="width: 20%" class="datatable-nosort">@lang('messages.Date')</th>
                                                        <th style="width: 40%" class="datatable-nosort">@lang('messages.Services')</th>
                                                        <th style="width: 15%" class="datatable-nosort">@lang('messages.Extra Bed')</th>
                                                        <th style="width: 15%" class="datatable-nosort">@lang('messages.Suite') / @lang('messages.Villa')</th>
                                                        <th style="width: 10%" class="datatable-nosort"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{--ACCOMMODATION BRIDES--}}
                                                    @if ($orderWedding->room_bride_id)
                                                        <tr>
                                                            <td class="pd-2-8">{{ date('d M Y',strtotime($orderWedding->checkin)) }} - {{ date('d M Y',strtotime($orderWedding->checkout)) }}</td>
                                                            <td class="pd-2-8">{{ $orderWedding->hotel->name }} | {{ $orderWedding->suite_villa->rooms }} | Bride's</td>
                                                            <td class="pd-2-8">-</td>
                                                            <td class="pd-2-8"><i>Include</i></td>
                                                            <td class="pd-2-8 text-right">
                                                                <a href="#" data-toggle="modal" data-target="#detail-accommodation-bride-service-{{ $orderWedding->id }}"> 
                                                                    <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        {{-- MODAL DETAIL ACCOMMODATION BRIDES --}}
                                                        <div class="modal fade" id="detail-accommodation-bride-service-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-hotel" aria-hidden="true"></i> {{ $orderWedding->hotel->name }} - {{ $orderWedding->suite_villa->rooms }} (Bride's)</div>
                                                                        </div>
                                                                        <div class="card-banner">
                                                                            <img class="img-fluid rounded" src="{{ url('/storage/hotels/hotels-room/'. $orderWedding->suite_villa->cover) }}" alt="{{ $orderWedding->hotel->name }}">
                                                                        </div>
                                                                        <div class="card-content">
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-12 text-center">
                                                                                        <div class="card-subtitle p-t-0">{{ $orderWedding->hotel->name }} - {{ $orderWedding->suite_villa->rooms }}</div>
                                                                                        <p>
                                                                                            {{ date("d M Y",strtotime($orderWedding->checkin)) }} - {{ date("d M Y",strtotime($orderWedding->checkout)) }} | 
                                                                                            <i>Include</i>
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($weddingAccommodations)
                                                        @foreach ($weddingAccommodations as $accommodation_invitation)
                                                            <tr class="{{ $accommodation_invitation->public_rate <= 0?"bg-notif":""; }}">
                                                                <td class="pd-2-8">{{ date('d M Y',strtotime($accommodation_invitation->checkin)) }} - {{ date('d M Y',strtotime($accommodation_invitation->checkout)) }}</td>
                                                                <td class="pd-2-8">{{ $accommodation_invitation->hotel->name }} | {{ $accommodation_invitation->room->rooms }} {{ $accommodation_invitation->extra_bed_order?"+ ".$accommodation_invitation->extra_bed_order->extra_bed->type." Extra Bed":""; }}</td>
                                                                <td class="pd-2-8">{{ $accommodation_invitation->extra_bed_order?'$ ' . number_format(($accommodation_invitation->extra_bed_order->total_price), 0, ',', '.'):"-"; }}</td>
                                                                <td class="pd-2-8">{{ $accommodation_invitation->public_rate?'$ ' . number_format(($accommodation_invitation->public_rate), 0, ',', '.'): "$ 0" }}</td>
                                                                <td class="pd-2-8 text-right">
                                                                    @if ($orderWedding->handled_by)
                                                                        @if ($orderWedding->handled_by == Auth::user()->id)
                                                                            @if ($orderWedding->status != "Approved")
                                                                                @if ($orderWedding->status != "Paid")
                                                                                    <span>
                                                                                        <a href="#" data-toggle="modal" data-target="#update-accommodation-invitation-{{ $accommodation_invitation->id }}">
                                                                                            <i class="fa fa-pencil"></i>
                                                                                        </a>
                                                                                    </span>
                                                                                @endif
                                                                            @endif
                                                                        @endif
                                                                    @else
                                                                        @if ($orderWedding->status != "Approved")
                                                                            @if ($orderWedding->status != "Paid")
                                                                                <span>
                                                                                    <a href="#" data-toggle="modal" data-target="#update-accommodation-invitation-{{ $accommodation_invitation->id }}">
                                                                                        <i class="fa fa-pencil"></i>
                                                                                    </a>
                                                                                </span>
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                    <a href="#" data-toggle="modal" data-target="#detail-accommodation_invitation-service-{{ $accommodation_invitation->id }}"> 
                                                                        <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            {{-- MODAL DETAIL ACCOMMODATION INVITATION SERVICE --}}
                                                            <div class="modal fade" id="detail-accommodation_invitation-service-{{ $accommodation_invitation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content text-left">
                                                                        <div class="card-box">
                                                                            <div class="card-box-title">
                                                                                <div class="subtitle"><i class="icon-copy fa fa-hotel" aria-hidden="true"></i> {{ $accommodation_invitation->hotel->name }} - {{ $accommodation_invitation->room->rooms }} {{ $accommodation_invitation->extra_bed_order?"+ ".$accommodation_invitation->extra_bed_order->extra_bed->type." Extra Bed":""; }}</div>
                                                                            </div>
                                                                            <div class="card-banner">
                                                                                <img class="img-fluid rounded" src="{{ url('/storage/hotels/hotels-room/'. $accommodation_invitation->room->cover) }}" alt="{{ $accommodation_invitation->hotel->name }}">
                                                                            </div>
                                                                            <div class="card-content">
                                                                                <div class="card-text">
                                                                                    <div class="row ">
                                                                                        <div class="col-sm-12 text-center">
                                                                                            <div class="card-subtitle p-t-0">{{ $accommodation_invitation->hotel->name }} - {{ $accommodation_invitation->room->rooms }}</div>
                                                                                            <p>
                                                                                                {{ date("d M Y",strtotime($orderWedding->checkin)) }} - {{ date("d M Y",strtotime($orderWedding->checkout)) }} | 
                                                                                                {{ '$ ' . number_format(($accommodation_invitation->public_rate), 0, ',', '.') }}
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card-box-footer">
                                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        
                                                    @endif
                                                </tbody>
                                            </table>
                                            @if ($extraBedOrderPrice>0 or $invitation_accommodation_price>0)
                                                <div class="box-price-kicked">
                                                    <div class="row">
                                                        <div class="col-6 col-sm-8 text-right">
                                                            <div class="normal-text">
                                                                Extra Bed
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-sm-4 text-right">
                                                            <div class="normal-text">
                                                                {{ '$ ' . number_format(($extraBedOrderPrice), 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-sm-8 text-right">
                                                            <div class="normal-text">
                                                                Suite / Villa
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-sm-4 text-right">
                                                            @if ($accommodationPriceContainZero)
                                                                <div class="normal-text text-red">
                                                                    TBA
                                                                </div>
                                                            @else
                                                                <div class="normal-text">
                                                                    {{ '$ ' . number_format(($invitation_accommodation_price), 0, ',', '.') }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-12">
                                                            <hr class="form-hr">
                                                        </div>
                                                        <div class="col-6 col-sm-8 text-right">
                                                            <div class="normal-text">
                                                                <b>
                                                                    @lang('messages.Total')
                                                                </b>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-sm-4 text-right">
                                                            @if ($accommodationPriceContainZero)
                                                                <div class="normal-text text-red">
                                                                    <b>TBA</b>
                                                                </div>
                                                            @else
                                                                <div class="normal-text">
                                                                    <b>
                                                                        {{ '$ ' . number_format(($invitation_accommodation_price+$extraBedOrderPrice), 0, ',', '.') }}
                                                                    </b>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <div id="weddingAccommodation" class="col-md-12">
                                        <div class="page-subtitle">
                                            Accommodation
                                            @if ($orderWedding->handled_by)
                                                @if ($orderWedding->handled_by == $admin->id)
                                                    @if ($orderWedding->status != "Approved")
                                                        @if ($orderWedding->status != "Paid")
                                                            <span>
                                                                <a href="/admin-validate-order-wedding-accommodation-{{ $orderWedding->id }}"> 
                                                                    @if ($weddingAccommodations)
                                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                    @else
                                                                        <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                                    @endif
                                                                </a>
                                                            </span>
                                                        @endif
                                                    @endif
                                                @endif
                                            @else
                                                @if ($orderWedding->status != "Approved")
                                                    @if ($orderWedding->status != "Paid")
                                                        <span>
                                                            <a href="/admin-validate-order-wedding-accommodation-{{ $orderWedding->id }}"> 
                                                                @if ($weddingAccommodations)
                                                                    <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                @else
                                                                    <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                                @endif
                                                            </a>
                                                        </span>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                        <table class="data-table table stripe nowrap no-footer dtr-inline" >
                                            <thead>
                                                <tr>
                                                    <th style="width: 20%" class="datatable-nosort">@lang('messages.Date')</th>
                                                    <th style="width: 40%" class="datatable-nosort">@lang('messages.Services')</th>
                                                    <th style="width: 15%" class="datatable-nosort">@lang('messages.Extra Bed')</th>
                                                    <th style="width: 15%" class="datatable-nosort">@lang('messages.Suite') / @lang('messages.Villa')</th>
                                                    <th style="width: 10%" class="datatable-nosort"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{--ACCOMMODATION BRIDES--}}
                                                @if ($orderWedding->room_bride_id)
                                                    <tr>
                                                        <td class="pd-2-8">{{ date('d M Y',strtotime($orderWedding->checkin)) }} - {{ date('d M Y',strtotime($orderWedding->checkout)) }}</td>
                                                        <td class="pd-2-8">{{ $orderWedding->hotel->name }} | {{ $orderWedding->suite_villa->rooms }} | Bride's</td>
                                                        <td class="pd-2-8">-</td>
                                                        <td class="pd-2-8"><i>Include</i></td>
                                                        <td class="pd-2-8 text-right">
                                                            <a href="#" data-toggle="modal" data-target="#detail-accommodation-bride-service-{{ $orderWedding->id }}"> 
                                                                <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    {{-- MODAL DETAIL ACCOMMODATION BRIDES --}}
                                                    <div class="modal fade" id="detail-accommodation-bride-service-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="subtitle"><i class="icon-copy fa fa-hotel" aria-hidden="true"></i> {{ $orderWedding->hotel->name }} - {{ $orderWedding->suite_villa->rooms }} (Bride's)</div>
                                                                    </div>
                                                                    <div class="card-banner">
                                                                        <img class="img-fluid rounded" src="{{ url('/storage/hotels/hotels-room/'. $orderWedding->suite_villa->cover) }}" alt="{{ $orderWedding->hotel->name }}">
                                                                    </div>
                                                                    <div class="card-content">
                                                                        <div class="card-text">
                                                                            <div class="row ">
                                                                                <div class="col-sm-12 text-center">
                                                                                    <div class="card-subtitle p-t-0">{{ $orderWedding->hotel->name }} - {{ $orderWedding->suite_villa->rooms }}</div>
                                                                                    <p>
                                                                                        {{ date("d M Y",strtotime($orderWedding->checkin)) }} - {{ date("d M Y",strtotime($orderWedding->checkout)) }} | 
                                                                                        <i>Include</i>
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-box-footer">
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($weddingAccommodations)
                                                    @foreach ($weddingAccommodations as $accommodation_invitation)
                                                        <tr class="{{ $accommodation_invitation->public_rate <= 0?"bg-notif":""; }}">
                                                            <td class="pd-2-8">{{ date('d M Y',strtotime($accommodation_invitation->checkin)) }} - {{ date('d M Y',strtotime($accommodation_invitation->checkout)) }}</td>
                                                            <td class="pd-2-8">{{ $accommodation_invitation->hotel->name }} | {{ $accommodation_invitation->room->rooms }} {{ $accommodation_invitation->extra_bed_order?"+ ".$accommodation_invitation->extra_bed_order->extra_bed->type." Extra Bed":""; }}</td>
                                                            <td class="pd-2-8">{{ $accommodation_invitation->extra_bed_order?'$ ' . number_format(($accommodation_invitation->extra_bed_order->total_price), 0, ',', '.'):"-"; }}</td>
                                                            <td class="pd-2-8">{{ $accommodation_invitation->public_rate?'$ ' . number_format(($accommodation_invitation->public_rate), 0, ',', '.'): "$ 0" }}</td>
                                                            <td class="pd-2-8 text-right">
                                                                @if ($orderWedding->handled_by)
                                                                    @if ($orderWedding->handled_by == Auth::user()->id)
                                                                        @if ($orderWedding->status != "Approved")
                                                                            @if ($orderWedding->status != "Paid")
                                                                                <span>
                                                                                    <a href="#" data-toggle="modal" data-target="#update-accommodation-invitation-{{ $accommodation_invitation->id }}">
                                                                                        <i class="fa fa-pencil"></i>
                                                                                    </a>
                                                                                </span>
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                @else
                                                                    @if ($orderWedding->status != "Approved")
                                                                        @if ($orderWedding->status != "Paid")
                                                                            <span>
                                                                                <a href="#" data-toggle="modal" data-target="#update-accommodation-invitation-{{ $accommodation_invitation->id }}">
                                                                                    <i class="fa fa-pencil"></i>
                                                                                </a>
                                                                            </span>
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                                <a href="#" data-toggle="modal" data-target="#detail-accommodation_invitation-service-{{ $accommodation_invitation->id }}"> 
                                                                    <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        {{-- MODAL UPDATE ACCOMMODATION IVITATION --}}
                                                        <div class="modal fade" id="update-accommodation-invitation-{{ $accommodation_invitation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> {{ $accommodation_invitation->hotel->name }} | {{ $accommodation_invitation->room->rooms }} {{ $accommodation_invitation->extra_bed_order?"+ ".$accommodation_invitation->extra_bed_order->extra_bed->type." Extra Bed":""; }}</div>
                                                                        </div>
                                                                        <form id="updateTransportInvitation{{ $accommodation_invitation->id }}" action="/admin-fupdate-accommodation-invitation-price/{{ $accommodation_invitation->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <div class="row">
                                                                                @if ($accommodation_invitation->extra_bed_order)
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label for="extra_bed_price">Extra Bed Price <span> * <i>(Price / Night)</i></span></label>
                                                                                            <div class="btn-icon">
                                                                                                <span>$</span>
                                                                                                <input name="extra_bed_price" type="number" class="form-control input-icon @error('extra_bed_price') is-invalid @enderror" placeholder="Insert Price" type="text" value="{{ $accommodation_invitation->extra_bed_order->price_pax }}" required>
                                                                                            </div>
                                                                                            @error('extra_bed_price')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="public_rate">Suite / Villa Price <span> * <i>(Price / Night)</i></span></label>
                                                                                        <div class="btn-icon">
                                                                                            <span>$</span>
                                                                                            <input name="public_rate" type="number" class="form-control input-icon @error('public_rate') is-invalid @enderror" placeholder="Insert Price" type="text" value="{{ $accommodation_invitation->public_rate/$accommodation_invitation->duration }}" required>
                                                                                        </div>
                                                                                        @error('public_rate')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                        <div class="card-box-footer">
                                                                            <button type="submit" form="updateTransportInvitation{{ $accommodation_invitation->id }}" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> @lang("messages.Save")</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- MODAL DETAIL ACCOMMODATION INVITATION SERVICE --}}
                                                        <div class="modal fade" id="detail-accommodation_invitation-service-{{ $accommodation_invitation->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-hotel" aria-hidden="true"></i> {{ $accommodation_invitation->hotel->name }} - {{ $accommodation_invitation->room->rooms }} {{ $accommodation_invitation->extra_bed_order?"+ ".$accommodation_invitation->extra_bed_order->extra_bed->type." Extra Bed":""; }}</div>
                                                                        </div>
                                                                        <div class="card-banner">
                                                                            <img class="img-fluid rounded" src="{{ url('/storage/hotels/hotels-room/'. $accommodation_invitation->room->cover) }}" alt="{{ $accommodation_invitation->hotel->name }}">
                                                                        </div>
                                                                        <div class="card-content">
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-12 text-center">
                                                                                        <div class="card-subtitle p-t-0">{{ $accommodation_invitation->hotel->name }} - {{ $accommodation_invitation->room->rooms }}</div>
                                                                                        <p>
                                                                                            {{ date("d M Y",strtotime($orderWedding->checkin)) }} - {{ date("d M Y",strtotime($orderWedding->checkout)) }} | 
                                                                                            {{ '$ ' . number_format(($accommodation_invitation->public_rate), 0, ',', '.') }}
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    
                                                @endif
                                            </tbody>
                                        </table>
                                        <div class="box-price-kicked">
                                            <div class="row">
                                                <div class="col-6 col-sm-8 text-right">
                                                    <div class="normal-text">
                                                        Extra Bed
                                                    </div>
                                                </div>
                                                <div class="col-6 col-sm-4 text-right">
                                                    <div class="normal-text">
                                                        {{ '$ ' . number_format(($extraBedOrderPrice), 0, ',', '.') }}
                                                    </div>
                                                </div>
                                                <div class="col-6 col-sm-8 text-right">
                                                    <div class="normal-text">
                                                        Suite / Villa
                                                    </div>
                                                </div>
                                                <div class="col-6 col-sm-4 text-right">
                                                    @if ($accommodationPriceContainZero)
                                                        <div class="normal-text text-red">
                                                            TBA
                                                        </div>
                                                    @else
                                                        <div class="normal-text">
                                                            {{ '$ ' . number_format(($invitation_accommodation_price), 0, ',', '.') }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-12">
                                                    <hr class="form-hr">
                                                </div>
                                                <div class="col-6 col-sm-8 text-right">
                                                    <div class="normal-text">
                                                        <b>
                                                            @lang('messages.Total')
                                                        </b>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-sm-4 text-right">
                                                    @if ($accommodationPriceContainZero)
                                                        <div class="normal-text text-red">
                                                            <b>TBA</b>
                                                        </div>
                                                    @else
                                                        <div class="normal-text">
                                                            <b>
                                                                {{ '$ ' . number_format(($invitation_accommodation_price+$extraBedOrderPrice), 0, ',', '.') }}
                                                            </b>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- ADDITIONAL SERVICE --}}
                                @if ($orderWedding->service == "Wedding Package")
                                    <div id="weddingPackageServices" class="col-md-12">
                                        @php
                                            $additional_services = $vendor_packages;
                                            $addser_ids = json_decode($orderWedding->additional_services);
                                            $total_additional_service = 0;
                                            $no_addser = 0;
                                        @endphp
                                            <div class="page-subtitle">
                                            Additional Services
                                        </div>
                                        <table class="data-table table stripe nowrap no-footer dtr-inline">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30%" class="datatable-nosort">@lang('messages.Date')</th>
                                                    <th style="width: 40%" class="datatable-nosort">@lang('messages.Services')</th>
                                                    <th style="width: 15%" class="datatable-nosort">@lang('messages.Price')</th>
                                                    <th style="width: 10%" class="datatable-nosort"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($addser_ids)
                                                    @foreach ($addser_ids as $addser_id)
                                                        @php
                                                            $additionalService = $vendor_packages->where('id',$addser_id)->first();
                                                            $total_additional_service = $additionalService->publish_rate + $total_additional_service;
                                                        @endphp
                                                        <tr>
                                                            @if ($additionalService->venue == "Ceremony Venue")
                                                                <td class="pd-2-8">{{ date('d M Y',strtotime($orderWedding->wedding_date)) }}{{ date(' (H:i)',strtotime($orderWedding->slot)) }}</td>
                                                            @elseif($additionalService->venue == "Reception Venue")
                                                                <td class="pd-2-8">{{ date('d M Y (H:i)',strtotime($orderWedding->reception_date_start)) }}</td>
                                                            @else
                                                                <td class="pd-2-8">
                                                                    {{ date('d M Y',strtotime($orderWedding->checkin)) }} - {{ date('d M Y',strtotime($orderWedding->checkout)) }}
                                                                </td>
                                                            @endif
                                                            <td class="pd-2-8">{{ $additionalService->service }}</td>
                                                            <td class="pd-2-8">
                                                                <i>Include</i>
                                                            </td>
                                                            <td class="pd-2-8 text-right">
                                                                <a href="#" data-toggle="modal" data-target="#detail-additional-service-{{ $additionalService->id }}"> 
                                                                    <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        {{-- MODAL DETAIL ADDITIONAL SERVICE --}}
                                                        <div class="modal fade" id="detail-additional-service-{{ $additionalService->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content text-left">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="subtitle"><i class="icon-copy fa fa-certificate" aria-hidden="true"></i> {{ $additionalService->service }}</div>
                                                                        </div>
                                                                        <div class="card-banner">
                                                                            <img class="img-fluid rounded" src="{{ url('storage/vendors/package/' . $additionalService->cover) }}" alt="{{ $additionalService->service }}">
                                                                        </div>
                                                                        <div class="card-content">
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-sm-12 text-center">
                                                                                        <div class="card-subtitle p-t-0">{{ $additionalService->service }}</div>
                                                                                            <p>
                                                                                                {{ '$ ' . number_format($additionalService->publish_rate, 0, ',', '.') }} / {{ $additionalService->duration }}
                                                                                                @if ($additionalService->time == "days")
                                                                                                    @if ($additionalService->duration > 1)
                                                                                                        @lang('messages.days')
                                                                                                    @else
                                                                                                        @lang('messages.day')
                                                                                                    @endif
                                                                                                @else
                                                                                                    @lang('messages.'.$additionalService->time)
                                                                                                @endif
                                                                                            </p>
                                                                                        </div>
                                                                                    <div class="col-sm-12">
                                                                                        <hr class="form-hr">
                                                                                        {!! $additionalService->description !!}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    {{-- ADDITIONAL CHARGE --}}
                                    <div id="weddingAdditionalCharge" class="col-md-12">
                                        <div class="page-subtitle">
                                            Additional Charge
                                            <span>
                                                <a href="#" data-toggle="modal" data-target="#add-additional-charge-{{ $orderWedding->id }}">
                                                    <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                        </div>
                                        {{-- MODAL ADD ADDITIONAL CHARGE  --}}
                                        <div class="modal fade" id="add-additional-charge-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Request Service')</div>
                                                        </div>
                                                        <form id="addRequestService" action="/admin-fadd-additional-charge/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('put')
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group ">
                                                                        <label for="date">@lang('messages.Date')<span> *</span></label>
                                                                        <select name="date" class="custom-select @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                                                                            <option selected value="">@lang('messages.Select date')</option>
                                                                            @for ($dt = 0; $dt < $orderWedding->duration+1; $dt++)
                                                                                <option value="{{ date('Y-m-d',strtotime('+'.$dt.' days',strtotime($orderWedding->checkin))) }}">{{ date('d M Y',strtotime('+'.$dt.' days',strtotime($orderWedding->checkin))) }}</option>
                                                                            @endfor
                                                                        </select>
                                                                        @error('date')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="time" class="form-label">@lang('messages.Time')</label>
                                                                        <input readonly type="text" name="time" class="form-control time-picker-default td-input @error('time') is-invalid @enderror" placeholder="@lang('messages.Select time')" value="{{ old('time') }}" required>
                                                                        @error('time')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group ">
                                                                        <label for="type">@lang('messages.Type')<span> *</span></label>
                                                                        <select name="type" class="custom-select @error('type') is-invalid @enderror" value="{{ old('type') }}" required>
                                                                            <option selected value="">@lang('messages.Select one')</option>
                                                                            <option value="Other">@lang('messages.Other')</option>
                                                                            <option value="Accessories">@lang('messages.Accessories')</option>
                                                                            <option value="Entertainment">@lang('messages.Entertainment')</option>
                                                                            <option value="Wedding Property">@lang('messages.Wedding Property')</option>
                                                                        </select>
                                                                        @error('type')
                                                                            <div class="alert-form">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="service" class="form-label">@lang('messages.Service')</label>
                                                                        <input type="text" name="service" class="form-control @error('service') is-invalid @enderror" placeholder="@lang('messages.Service name')" value="{{ old('service') }}" required>
                                                                        @error('service')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="quantity" class="form-label">@lang('messages.Quantity')</label>
                                                                        <input type="number" min="1" name="quantity" class="form-control @error('quantity') is-invalid @enderror" placeholder="@lang('messages.Quantity')" value="{{ old('quantity') }}" required>
                                                                        @error('quantity')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="price" class="form-label">@lang('messages.Price')</label>
                                                                        <input type="number" min="1" name="price" class="form-control @error('price') is-invalid @enderror" placeholder="@lang('messages.Price')" value="{{ old('price') }}" required>
                                                                        @error('price')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="note" class="form-label">@lang('messages.Note')</label>
                                                                        <textarea name="note" class="textarea_editor form-control @error('note') is-invalid @enderror" placeholder="Insert note">{!! old('note') !!}</textarea>
                                                                        @error('note')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="card-box-footer">
                                                            <button type="submit" form="addRequestService" class="btn btn-primary"><i class="icon-copy fa fa-plus"></i> @lang('messages.Add')</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close"></i> @lang('messages.Cancel')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="data-table table stripe nowrap no-footer dtr-inline">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%" class="datatable-nosort">@lang('messages.Date')</th>
                                                    <th style="width: 20%" class="datatable-nosort">@lang('messages.Services')</th>
                                                    <th style="width: 35%" class="datatable-nosort">@lang('messages.Description')</th>
                                                    <th style="width: 15%" class="datatable-nosort">@lang('messages.Status')</th>
                                                    <th style="width: 15%" class="datatable-nosort">@lang('messages.Price')</th>
                                                    <th style="width: 5%" class="datatable-nosort"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($serviceRequests)
                                                    @foreach ($serviceRequests as $no_rexser=>$request_service)
                                                        <tr class="{{ $request_service->price > 0 ?"":"bg-notif"; }} {{ $request_service->status == "Request"?"bg-notif":""; }}">
                                                            <td class="pd-2-8">{{ date('d M Y',strtotime($request_service->date)) }}</td>
                                                            <td class="pd-2-8">{{ $request_service->service }}</td>
                                                            <td class="pd-2-8">
                                                                @if ($request_service->note)
                                                                    {!! $request_service->note !!}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td class="pd-2-8">{{ $request_service->status }}</td>
                                                            <td class="pd-2-8">{{ $request_service->price?'$ ' . number_format(($request_service->price), 0, ',', '.'): "$ 0" }}</td>
                                                            <td class="pd-2-8 text-right">
                                                                @if ($orderWedding->handled_by)
                                                                    @if ($orderWedding->handled_by == Auth::user()->id)
                                                                        <span>
                                                                            <a href="#" data-toggle="modal" data-target="#update-additional-charge-{{ $request_service->id }}">
                                                                                <i class="fa fa-pencil"></i>
                                                                            </a>
                                                                        </span>
                                                                    @endif
                                                                @else
                                                                    <span>
                                                                        <a href="#" data-toggle="modal" data-target="#update-additional-charge-{{ $request_service->id }}">
                                                                            <i class="fa fa-pencil"></i>
                                                                        </a>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        {{-- MODAL UPDATE ADDITIONAL CHARGE --}}
                                                        <div class="modal fade" id="update-additional-charge-{{ $request_service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> {{ $request_service->service }}</div>
                                                                        </div>
                                                                        <form id="updateRequestServices{{ $request_service->id }}" action="/admin-fupdate-additional-charge/{{ $request_service->id }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group ">
                                                                                        <label for="status_{{ $no_rexser }}">@lang('messages.Status')<span> *</span></label>
                                                                                        <select id="status_{{ $no_rexser }}" name="status" class="custom-select @error('status') is-invalid @enderror" value="{{ old('status') }}" required>
                                                                                            <option {{ $request_service->status == "Request"?"selected":""; }} value="Request">@lang('messages.Request')</option>
                                                                                            <option {{ $request_service->status == "Approved"?"selected":""; }} value="Approved">@lang('messages.Approved')</option>
                                                                                            <option {{ $request_service->status == "Rejected"?"selected":""; }} value="Rejected">@lang('messages.Rejected')</option>
                                                                                        </select>
                                                                                        @error('status')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group ">
                                                                                        <label for="date">@lang('messages.Date')<span> *</span></label>
                                                                                        <select name="date" class="custom-select @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                                                                                            <option value="">@lang('messages.Select date')</option>
                                                                                            @for ($dt = 0; $dt < $orderWedding->duration+1; $dt++)
                                                                                                <option {{ date('Y-m-d',strtotime($request_service->date)) == date('Y-m-d',strtotime('+'.$dt.' days',strtotime($orderWedding->checkin)))?"selected":""; }} value="{{ date('Y-m-d',strtotime('+'.$dt.' days',strtotime($orderWedding->checkin))) }}">{{ date('d M Y',strtotime('+'.$dt.' days',strtotime($orderWedding->checkin))) }}</option>
                                                                                            @endfor
                                                                                        </select>
                                                                                        @error('date')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="time" class="form-label">@lang('messages.Time')</label>
                                                                                        <input readonly type="text" name="time" class="form-control time-picker td-input @error('time') is-invalid @enderror" placeholder="@lang('messages.Select time')" value="{{ date('H:i',strtotime($request_service->date)) }}" required>
                                                                                        @error('time')
                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group ">
                                                                                        <label for="type">@lang('messages.Type')<span> *</span></label>
                                                                                        <select name="type" class="custom-select @error('type') is-invalid @enderror" value="{{ old('type') }}" required>
                                                                                            <option value="">@lang('messages.Select one')</option>
                                                                                            <option {{ $request_service->type == "Other"?"selected":""; }} value="Other">@lang('messages.Other')</option>
                                                                                            <option {{ $request_service->type == "Accessories"?"selected":""; }} value="Accessories">@lang('messages.Accessories')</option>
                                                                                            <option {{ $request_service->type == "Entertainment"?"selected":""; }} value="Entertainment">@lang('messages.Entertainment')</option>
                                                                                            <option {{ $request_service->type == "Wedding Property"?"selected":""; }} value="Wedding Property">@lang('messages.Wedding Property')</option>
                                                                                        </select>
                                                                                        @error('type')
                                                                                            <div class="alert-form">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="service" class="form-label">@lang('messages.Service')</label>
                                                                                        <input type="text" name="service" class="form-control @error('service') is-invalid @enderror" placeholder="@lang('messages.Service name')" value="{{ $request_service->service }}" required>
                                                                                        @error('service')
                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="quantity" class="form-label">@lang('messages.Quantity')</label>
                                                                                        <input type="number" min="1" name="quantity" class="form-control @error('quantity') is-invalid @enderror" placeholder="@lang('messages.Quantity')" value="{{ $request_service->quantity }}" required>
                                                                                        @error('quantity')
                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="price" class="form-label">@lang('messages.Price')</label>
                                                                                        <input type="number" min="1" name="price" class="form-control @error('price') is-invalid @enderror" placeholder="@lang('messages.Price')" value="{{ $request_service->price }}" required>
                                                                                        @error('price')
                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="note" class="form-label">@lang('messages.Note')</label>
                                                                                        <textarea name="note" class="textarea_editor form-control @error('note') is-invalid @enderror" placeholder="Insert note">{!! $request_service->note !!}</textarea>
                                                                                        @error('note')
                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12 {{ $request_service->status == "Rejected" ? "" : "display-none" }}" id="remark-div_{{ $no_rexser }}">
                                                                                    <div class="form-group">
                                                                                        <label for="remark_{{ $no_rexser }}" class="form-label">Reason</label>
                                                                                        <input type="text" id="remark_{{ $no_rexser }}" name="remark" class="form-control @error('remark') is-invalid @enderror" placeholder="Please provide your reason for why the service was denied!" value="{{ $request_service->remark }}" required>
                                                                                        @error('remark')
                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                        <div class="card-box-footer">
                                                                            <button type="submit" form="updateRequestServices{{ $request_service->id }}" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> @lang("messages.Update")</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @foreach ($serviceRequestRejected as $rejected_service)
                                                    <tr class="{{ $rejected_service->status == "Rejected"?"bg-reject":""; }}" data-toggle="tooltip" title="Status: {{ $rejected_service->status }} | Reason: {{ $rejected_service->remark }}">
                                                        <td class="pd-2-8">{{ date('d M Y',strtotime($rejected_service->date)) }}</td>
                                                        <td class="pd-2-8">{{ $rejected_service->service }}</td>
                                                        <td class="pd-2-8">{!! $rejected_service->note !!}</td>
                                                        <td class="pd-2-8">{{ $rejected_service->status }}</td>
                                                        <td class="pd-2-8">{{ $rejected_service->price?'$ ' . number_format(($rejected_service->price), 0, ',', '.'): "-" }}</td>
                                                        <td class="pd-2-8 text-right">
                                                            @if ($orderWedding->handled_by)
                                                                @if ($orderWedding->handled_by == Auth::user()->id)
                                                                    <span>
                                                                        <a href="#" data-toggle="modal" data-target="#update-rejected-additional-charge-{{ $rejected_service->id }}">
                                                                            <i class="fa fa-pencil"></i>
                                                                        </a>
                                                                    </span>
                                                                @endif
                                                            @else
                                                                <span>
                                                                    <a href="#" data-toggle="modal" data-target="#update-rejected-additional-charge-{{ $rejected_service->id }}">
                                                                        <i class="fa fa-pencil"></i>
                                                                    </a>
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    {{-- MODAL UPDATE REJECTED ADDITIONAL CHARGE --}}
                                                    <div class="modal fade" id="update-rejected-additional-charge-{{ $rejected_service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> {{ $rejected_service->service }}</div>
                                                                    </div>
                                                                    <form id="updateRejectedServices{{ $rejected_service->id }}" action="/admin-fupdate-additional-charge/{{ $rejected_service->id }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group ">
                                                                                    <label for="status">@lang('messages.Status')<span> *</span></label>
                                                                                    <select id="status" name="status" class="custom-select @error('status') is-invalid @enderror" value="{{ old('status') }}" required>
                                                                                        <option {{ $rejected_service->status == "Request"?"selected":""; }} value="Request">@lang('messages.Request')</option>
                                                                                        <option {{ $rejected_service->status == "Approved"?"selected":""; }} value="Approved">@lang('messages.Approved')</option>
                                                                                        <option {{ $rejected_service->status == "Rejected"?"selected":""; }} value="Rejected">@lang('messages.Rejected')</option>
                                                                                    </select>
                                                                                    @error('status')
                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group ">
                                                                                    <label for="date">@lang('messages.Date')<span> *</span></label>
                                                                                    <select name="date" class="custom-select @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                                                                                        <option value="">@lang('messages.Select date')</option>
                                                                                        @for ($dt = 0; $dt < $orderWedding->duration+1; $dt++)
                                                                                            <option {{ date('Y-m-d',strtotime($rejected_service->date)) == date('Y-m-d',strtotime('+'.$dt.' days',strtotime($orderWedding->checkin)))?"selected":""; }} value="{{ date('Y-m-d',strtotime('+'.$dt.' days',strtotime($orderWedding->checkin))) }}">{{ date('d M Y',strtotime('+'.$dt.' days',strtotime($orderWedding->checkin))) }}</option>
                                                                                        @endfor
                                                                                    </select>
                                                                                    @error('date')
                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="time" class="form-label">@lang('messages.Time')</label>
                                                                                    <input readonly type="text" name="time" class="form-control time-picker td-input @error('time') is-invalid @enderror" placeholder="@lang('messages.Select time')" value="{{ date('H:i',strtotime($rejected_service->date)) }}" required>
                                                                                    @error('time')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group ">
                                                                                    <label for="type">@lang('messages.Type')<span> *</span></label>
                                                                                    <select name="type" class="custom-select @error('type') is-invalid @enderror" value="{{ old('type') }}" required>
                                                                                        <option value="">@lang('messages.Select one')</option>
                                                                                        <option {{ $rejected_service->type == "Other"?"selected":""; }} value="Other">@lang('messages.Other')</option>
                                                                                        <option {{ $rejected_service->type == "Accessories"?"selected":""; }} value="Accessories">@lang('messages.Accessories')</option>
                                                                                        <option {{ $rejected_service->type == "Entertainment"?"selected":""; }} value="Entertainment">@lang('messages.Entertainment')</option>
                                                                                        <option {{ $rejected_service->type == "Wedding Property"?"selected":""; }} value="Wedding Property">@lang('messages.Wedding Property')</option>
                                                                                    </select>
                                                                                    @error('type')
                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="service" class="form-label">@lang('messages.Service')</label>
                                                                                    <input type="text" name="service" class="form-control @error('service') is-invalid @enderror" placeholder="@lang('messages.Service name')" value="{{ $rejected_service->service }}" required>
                                                                                    @error('service')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="quantity" class="form-label">@lang('messages.Quantity')</label>
                                                                                    <input type="number" min="1" name="quantity" class="form-control @error('quantity') is-invalid @enderror" placeholder="@lang('messages.Quantity')" value="{{ $rejected_service->quantity }}" required>
                                                                                    @error('quantity')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="price" class="form-label">@lang('messages.Price')</label>
                                                                                    <input type="number" min="1" name="price" class="form-control @error('price') is-invalid @enderror" placeholder="@lang('messages.Price')" value="{{ $rejected_service->price }}" required>
                                                                                    @error('price')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label for="note" class="form-label">@lang('messages.Note')</label>
                                                                                    <textarea name="note" class="textarea_editor form-control @error('note') is-invalid @enderror" placeholder="Insert note">{!! $rejected_service->note !!}</textarea>
                                                                                    @error('note')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label for="remark" class="form-label">Reason</label>
                                                                                    <input type="text" name="remark" class="form-control @error('remark') is-invalid @enderror" placeholder="Please provide your reason for why the service was denied!" value="{{ $rejected_service->remark }}">
                                                                                    @error('remark')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                    <div class="card-box-footer">
                                                                        <button type="submit" form="updateRejectedServices{{ $rejected_service->id }}" class="btn btn-primary"><i class="icon-copy fa fa-save" aria-hidden="true"></i> @lang("messages.Update")</button>
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="box-price-kicked">
                                            <div class="row">
                                                <div class="col-8 text-right">
                                                    <div class="normal-text"><b>@lang('messages.Total')</b></div>
                                                </div>
                                                <div class="col-4 text-right">
                                                    @if ($serviceRequestPriceContainZero)
                                                        <div class="normal-text text-red">
                                                            <b>
                                                                TBA
                                                            </b>
                                                        </div>
                                                    @else
                                                        <div class="normal-text">
                                                            <b>
                                                                {{ '$ ' . number_format(($serviceRequestPrice), 0, ',', '.') }}
                                                            </b>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if ($orderWedding->status != "Approved")
                                            @if ($orderWedding->status != "Paid")
                                                {{-- MODAL ADD ADDITIONAL SERVICE  --}}
                                                <div class="modal fade" id="add-additional-service-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    @if ($addser_ids)
                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Additional Services')</div>
                                                                    @else
                                                                        <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Additional Services')</div>
                                                                    @endif
                                                                </div>
                                                                <form id="addAdditionalService" action="/admin-fadd-additional-service-to-order-wedding/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('put')
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label>@lang("messages.Select one") <span>*</span></label>
                                                                                <div class="card-box-content">
                                                                                    @foreach ($additional_services as $addser)
                                                                                            <div class="card-checkbox" onclick="toggleCard(this)">
                                                                                                <img class="card-img" src="{{ asset('storage/vendors/package/' . $addser->cover) }}" alt="{{ $addser->service }}">
                                                                                                @if ($addser_ids)
                                                                                                    <input type="checkbox" id="addser_id_{{ $addser->id }}" name="addser_id[]" value="{{ $addser->id }}" class="d-none checkbox-input" @if(in_array($addser->id, $addser_ids)) checked @endif>
                                                                                                @else
                                                                                                    <input type="checkbox" id="addser_id_{{ $addser->id }}" name="addser_id[]" value="{{ $addser->id }}" class="d-none checkbox-input" @if($addser->isSelected) checked @endif>
                                                                                                @endif
                                                                                                <div class="card-lable-left">
                                                                                                    <div class="meta-box lable-text-black">
                                                                                                        {{ '$ ' . number_format($addser->publish_rate, 0, ',', '.') }}
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="name-card">
                                                                                                    <b>{{ $addser->service }}</b>
                                                                                                </div>
                                                                                            </div>
                                                                                            @error('addser_id[]')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                <div class="card-box-footer">
                                                                    @if ($addser_ids)
                                                                        <button type="submit" form="addAdditionalService" class="btn btn-primary"><i class="icon-copy fa fa-pencil"></i> @lang('messages.Change')</button>
                                                                    @else
                                                                        <button type="submit" form="addAdditionalService" class="btn btn-primary"><i class="icon-copy fa fa-plus"></i> @lang('messages.Add')</button>
                                                                    @endif
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close"></i> @lang('messages.Cancel')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @else
                                    @if ($orderWedding->status == "Approved" or $orderWedding->status == "Paid")
                                        @php
                                            $addser_ids = json_decode($orderWedding->additional_services);
                                            $total_additional_service = 0;
                                        @endphp
                                        @if ($addser_ids)
                                            <div id="additionalServices" class="col-md-12" style="page-break-after: always;">
                                                    <div class="page-subtitle {{ $addser_ids?"":"empty-value" }}">
                                                    @lang('messages.Additional Services')
                                                </div>
                                                @if ($addser_ids)
                                                    <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                                                        <thead>
                                                            <tr>
                                                                <th class="datatable-nosort">@lang('messages.Date')</th>
                                                                <th class="datatable-nosort">@lang('messages.Services')</th>
                                                                <th class="datatable-nosort">@lang('messages.Price')</th>
                                                                <th class="datatable-nosort"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if ($addser_ids)
                                                                @foreach ($addser_ids as $no_addser=>$addser_id)
                                                                    @php
                                                                        $additionalService = $vendor_packages->where('id',$addser_id)->first();
                                                                        $total_additional_service = $additionalService->publish_rate + $total_additional_service;
                                                                    @endphp
                                                                    <tr>
                                                                        @if ($additionalService->venue == "Ceremony Venue")
                                                                            <td class="pd-2-8">{{ date('d F Y',strtotime($orderWedding->wedding_date)) }}</td>
                                                                        @elseif($additionalService->venue == "Reception Venue")
                                                                            <td class="pd-2-8">{{ date('d F Y',strtotime($orderWedding->reception_date_start)) }}</td>
                                                                        @else
                                                                            <td class="pd-2-8">{{ date('d F Y',strtotime($orderWedding->checkin)) }}</td>
                                                                        @endif
                                                                        <td class="pd-2-8">{{ $additionalService->service }}</td>
                                                                        <td class="pd-2-8">
                                                                            {{ '$ ' . number_format($additionalService->publish_rate, 0, ',', '.') }}
                                                                        </td>
                                                                        <td class="pd-2-8 text-right">
                                                                            <a href="#" data-toggle="modal" data-target="#detail-additional-service-{{ $additionalService->id }}"> 
                                                                                <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    {{-- MODAL DETAIL ADDITIONAL SERVICE --}}
                                                                    <div class="modal fade" id="detail-additional-service-{{ $additionalService->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                            <div class="modal-content text-left">
                                                                                <div class="card-box">
                                                                                    <div class="card-box-title">
                                                                                        <div class="subtitle"><i class="icon-copy fa fa-certificate" aria-hidden="true"></i> {{ $additionalService->service }}</div>
                                                                                    </div>
                                                                                    <div class="card-banner">
                                                                                        <img class="img-fluid rounded" src="{{ url('storage/vendors/package/' . $additionalService->cover) }}" alt="{{ $additionalService->service }}">
                                                                                    </div>
                                                                                    <div class="card-content">
                                                                                        <div class="card-text">
                                                                                            <div class="row ">
                                                                                                <div class="col-sm-12 text-center">
                                                                                                    <div class="card-subtitle p-t-0">{{ $additionalService->service }}</div>
                                                                                                        <p>
                                                                                                            {{ '$ ' . number_format($additionalService->publish_rate, 0, ',', '.') }} / {{ $additionalService->duration }}
                                                                                                            @if ($additionalService->time == "days")
                                                                                                                @if ($additionalService->duration > 1)
                                                                                                                    @lang('messages.days')
                                                                                                                @else
                                                                                                                    @lang('messages.day')
                                                                                                                @endif
                                                                                                            @else
                                                                                                                @lang('messages.'.$additionalService->time)
                                                                                                            @endif
                                                                                                        </p>
                                                                                                    </div>
                                                                                                <div class="col-sm-12">
                                                                                                    <hr class="form-hr">
                                                                                                    {!! $additionalService->description !!}
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="card-box-footer">
                                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                    <div class="box-price-kicked">
                                                        <div class="row">
                                                            <div class="col-6 col-sm-8 text-right">
                                                                <div class="normal-text">
                                                                    <b>
                                                                        @lang('messages.Total')
                                                                    </b>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-sm-4 text-right">
                                                                <div class="normal-text">
                                                                    <b>
                                                                        {{ "$".number_format($total_additional_service,0,',','.') }}
                                                                    </b>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <div id="additionalServices" class="col-md-12" style="page-break-after: always;">
                                            @php
                                                $additional_services = $vendor_packages;
                                                $adser_entertainments = $vendor_packages->where('type','Entertainment');
                                                $adser_makeups = $vendor_packages->where('type','Make-up');
                                                $adser_documentations = $vendor_packages->where('type','Documentation');
                                                $adser_others = $vendor_packages->where('type','Other');
                                                $addser_ids = json_decode($orderWedding->additional_services);
                                                $total_additional_service = 0;
                                            @endphp
                                                <div class="page-subtitle">
                                                @lang('messages.Additional Services')
                                                @if ($orderWedding->handled_by)
                                                    @if ($orderWedding->handled_by == $admin->id)
                                                        @if ($orderWedding->status != "Approved")
                                                            @if ($orderWedding->status != "Paid")
                                                                <span>
                                                                    <a href="#" data-toggle="modal" data-target="#add-additional-service-{{ $orderWedding->id }}"> 
                                                                        @if ($addser_ids)
                                                                            <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                        @else
                                                                            <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                                        @endif
                                                                    </a>
                                                                </span>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @else
                                                    @if ($orderWedding->status != "Approved")
                                                        @if ($orderWedding->status != "Paid")
                                                            <span>
                                                                <a href="#" data-toggle="modal" data-target="#add-additional-service-{{ $orderWedding->id }}"> 
                                                                    @if ($addser_ids)
                                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                                    @else
                                                                        <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                                    @endif
                                                                </a>
                                                            </span>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            @if ($addser_ids)
                                                <table class="data-table table stripe hover nowrap no-footer dtr-inline" >
                                                    <thead>
                                                        <tr>
                                                            <th class="datatable-nosort">@lang('messages.Date')</th>
                                                            <th class="datatable-nosort">@lang('messages.Services')</th>
                                                            <th class="datatable-nosort">@lang('messages.Price')</th>
                                                            <th class="datatable-nosort"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if ($addser_ids)
                                                            @foreach ($addser_ids as $no_addser=>$addser_id)
                                                                @php
                                                                    $additionalService = $vendor_packages->where('id',$addser_id)->first();
                                                                    $total_additional_service = $additionalService->publish_rate + $total_additional_service;
                                                                @endphp
                                                                <tr>
                                                                    @if ($additionalService->venue == "Ceremony Venue")
                                                                        <td class="pd-2-8">{{ date('d F Y',strtotime($orderWedding->wedding_date)) }}</td>
                                                                    @elseif($additionalService->venue == "Reception Venue")
                                                                        <td class="pd-2-8">{{ date('d F Y',strtotime($orderWedding->reception_date_start)) }}</td>
                                                                    @else
                                                                        <td class="pd-2-8">{{ date('d F Y',strtotime($orderWedding->checkin)) }}</td>
                                                                    @endif
                                                                    <td class="pd-2-8">{{ $additionalService->service }}</td>
                                                                    <td class="pd-2-8">
                                                                        {{ '$ ' . number_format($additionalService->publish_rate, 0, ',', '.') }}
                                                                    </td>
                                                                    <td class="pd-2-8 text-right">
                                                                        <a href="#" data-toggle="modal" data-target="#detail-additional-service-{{ $additionalService->id }}"> 
                                                                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                {{-- MODAL DETAIL ADDITIONAL SERVICE --}}
                                                                <div class="modal fade" id="detail-additional-service-{{ $additionalService->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content text-left">
                                                                            <div class="card-box">
                                                                                <div class="card-box-title">
                                                                                    <div class="subtitle"><i class="icon-copy fa fa-certificate" aria-hidden="true"></i> {{ $additionalService->service }}</div>
                                                                                </div>
                                                                                <div class="card-banner">
                                                                                    <img class="img-fluid rounded" src="{{ url('storage/vendors/package/' . $additionalService->cover) }}" alt="{{ $additionalService->service }}">
                                                                                </div>
                                                                                <div class="card-content">
                                                                                    <div class="card-text">
                                                                                        <div class="row ">
                                                                                            <div class="col-sm-12 text-center">
                                                                                                <div class="card-subtitle p-t-0">{{ $additionalService->service }}</div>
                                                                                                    <p>
                                                                                                        {{ '$ ' . number_format($additionalService->publish_rate, 0, ',', '.') }} / {{ $additionalService->duration }}
                                                                                                        @if ($additionalService->time == "days")
                                                                                                            @if ($additionalService->duration > 1)
                                                                                                                @lang('messages.days')
                                                                                                            @else
                                                                                                                @lang('messages.day')
                                                                                                            @endif
                                                                                                        @else
                                                                                                            @lang('messages.'.$additionalService->time)
                                                                                                        @endif
                                                                                                    </p>
                                                                                                </div>
                                                                                            <div class="col-sm-12">
                                                                                                <hr class="form-hr">
                                                                                                {!! $additionalService->description !!}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-box-footer">
                                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                                <div class="box-price-kicked">
                                                    <div class="row">
                                                        <div class="col-6 col-sm-8 text-right">
                                                            <div class="normal-text">
                                                                <b>
                                                                    @lang('messages.Total')
                                                                </b>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-sm-4 text-right">
                                                            <div class="normal-text">
                                                                <b>
                                                                    {{ "$".number_format($total_additional_service,0,',','.') }}
                                                                </b>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($orderWedding->status != "Approved")
                                                @if ($orderWedding->status != "Paid")
                                                {{-- MODAL ADD ADDITIONAL SERVICE  --}}
                                                <div class="modal fade" id="add-additional-service-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    @if ($addser_ids)
                                                                        <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Additional Services')</div>
                                                                    @else
                                                                        <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Additional Services')</div>
                                                                    @endif
                                                                </div>
                                                                <form id="addAdditionalService" action="/admin-fadd-additional-service-to-order-wedding/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('put')
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <div class="subtitle">Entertainment</div>
                                                                                <div class="card-box-content">
                                                                                    @foreach ($adser_entertainments as $addser_entertainment)
                                                                                        <div class="card-checkbox" onclick="toggleCard(this)">
                                                                                            <img class="card-img" src="{{ asset('storage/vendors/package/' . $addser_entertainment->cover) }}" alt="{{ $addser_entertainment->service }}">
                                                                                            @if ($addser_ids)
                                                                                                <input type="checkbox" id="addser_id_{{ $addser_entertainment->id }}" name="addser_id[]" value="{{ $addser_entertainment->id }}" class="d-none checkbox-input" @if(in_array($addser_entertainment->id, $addser_ids)) checked @endif>
                                                                                            @else
                                                                                                <input type="checkbox" id="addser_id_{{ $addser_entertainment->id }}" name="addser_id[]" value="{{ $addser_entertainment->id }}" class="d-none checkbox-input" @if($addser_entertainment->isSelected) checked @endif>
                                                                                            @endif
                                                                                            <div class="card-lable-left">
                                                                                                <div class="meta-box lable-text-black">
                                                                                                    {{ '$ ' . number_format($addser_entertainment->publish_rate, 0, ',', '.') }}
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="name-card">
                                                                                                <b>{{ $addser_entertainment->service }}</b>
                                                                                            </div>
                                                                                        </div>
                                                                                        @error('addser_id[]')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    @endforeach
                                                                                </div>
                                                                                <div class="subtitle m-t-18">Make-up</div>
                                                                                <div class="card-box-content">
                                                                                    @foreach ($adser_makeups as $addser_makeup)
                                                                                        <div class="card-checkbox" onclick="toggleCard(this)">
                                                                                            <img class="card-img" src="{{ asset('storage/vendors/package/' . $addser_makeup->cover) }}" alt="{{ $addser_makeup->service }}">
                                                                                            @if ($addser_ids)
                                                                                                <input type="checkbox" id="addser_id_{{ $addser_makeup->id }}" name="addser_id[]" value="{{ $addser_makeup->id }}" class="d-none checkbox-input" @if(in_array($addser_makeup->id, $addser_ids)) checked @endif>
                                                                                            @else
                                                                                                <input type="checkbox" id="addser_id_{{ $addser_makeup->id }}" name="addser_id[]" value="{{ $addser_makeup->id }}" class="d-none checkbox-input" @if($addser_makeup->isSelected) checked @endif>
                                                                                            @endif
                                                                                            <div class="card-lable-left">
                                                                                                <div class="meta-box lable-text-black">
                                                                                                    {{ '$ ' . number_format($addser_makeup->publish_rate, 0, ',', '.') }}
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="name-card">
                                                                                                <b>{{ $addser_makeup->service }}</b>
                                                                                            </div>
                                                                                        </div>
                                                                                        @error('addser_id[]')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    @endforeach
                                                                                </div>
                                                                                <div class="subtitle m-t-18">Documentation</div>
                                                                                <div class="card-box-content">
                                                                                    @foreach ($adser_documentations as $addser_documentation)
                                                                                        <div class="card-checkbox" onclick="toggleCard(this)">
                                                                                            <img class="card-img" src="{{ asset('storage/vendors/package/' . $addser_documentation->cover) }}" alt="{{ $addser_documentation->service }}">
                                                                                            @if ($addser_ids)
                                                                                                <input type="checkbox" id="addser_id_{{ $addser_documentation->id }}" name="addser_id[]" value="{{ $addser_documentation->id }}" class="d-none checkbox-input" @if(in_array($addser_documentation->id, $addser_ids)) checked @endif>
                                                                                            @else
                                                                                                <input type="checkbox" id="addser_id_{{ $addser_documentation->id }}" name="addser_id[]" value="{{ $addser_documentation->id }}" class="d-none checkbox-input" @if($addser_documentation->isSelected) checked @endif>
                                                                                            @endif
                                                                                            <div class="card-lable-left">
                                                                                                <div class="meta-box lable-text-black">
                                                                                                    {{ '$ ' . number_format($addser_documentation->publish_rate, 0, ',', '.') }}
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="name-card">
                                                                                                <b>{{ $addser_documentation->service }}</b>
                                                                                            </div>
                                                                                        </div>
                                                                                        @error('addser_id[]')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    @endforeach
                                                                                </div>
                                                                                <div class="subtitle m-t-18">Other Services</div>
                                                                                <div class="card-box-content">
                                                                                    @foreach ($adser_others as $addser_other)
                                                                                        <div class="card-checkbox" onclick="toggleCard(this)">
                                                                                            <img class="card-img" src="{{ asset('storage/vendors/package/' . $addser_other->cover) }}" alt="{{ $addser_other->service }}">
                                                                                            @if ($addser_ids)
                                                                                                <input type="checkbox" id="addser_id_{{ $addser_other->id }}" name="addser_id[]" value="{{ $addser_other->id }}" class="d-none checkbox-input" @if(in_array($addser_other->id, $addser_ids)) checked @endif>
                                                                                            @else
                                                                                                <input type="checkbox" id="addser_id_{{ $addser_other->id }}" name="addser_id[]" value="{{ $addser_other->id }}" class="d-none checkbox-input" @if($addser_other->isSelected) checked @endif>
                                                                                            @endif
                                                                                            <div class="card-lable-left">
                                                                                                <div class="meta-box lable-text-black">
                                                                                                    {{ '$ ' . number_format($addser_other->publish_rate, 0, ',', '.') }}
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="name-card">
                                                                                                <b>{{ $addser_other->service }}</b>
                                                                                            </div>
                                                                                        </div>
                                                                                        @error('addser_id[]')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                <div class="card-box-footer">
                                                                    @if ($addser_ids)
                                                                        <button type="submit" form="addAdditionalService" class="btn btn-primary"><i class="icon-copy fa fa-pencil"></i> @lang('messages.Change')</button>
                                                                    @else
                                                                        <button type="submit" form="addAdditionalService" class="btn btn-primary"><i class="icon-copy fa fa-plus"></i> @lang('messages.Add')</button>
                                                                    @endif
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close"></i> @lang('messages.Cancel')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                @endif
                                {{-- REMARK --}}
                                @if ($orderWedding->status == "Pending")
                                    <div id="remarkPage" class="col-md-12">
                                        <div class="page-subtitle">
                                            @lang('messages.Remark')
                                            @if ($orderWedding->remark)
                                                <span>
                                                    <form action="/fdelete-order-wedding-remark/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('put')
                                                        <button class="icon-btn-remove" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash"></i></button>
                                                    </form>
                                                </span>
                                                <span>
                                                    <a href="#" data-toggle="modal" data-target="#add-order-wedding-remark-{{ $orderWedding->id }}">
                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change')" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                            @else
                                                <span>
                                                    <a href="#" data-toggle="modal" data-target="#add-order-wedding-remark-{{ $orderWedding->id }}"> 
                                                        <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                            @endif
                                        </div>
                                        @if ($orderWedding->remark)
                                            <div class="box-price-kicked">
                                                {!! $orderWedding->remark !!}
                                            </div>
                                        @endif
                                        {{-- MODAL ADD REMARK  --}}
                                        <div class="modal fade" id="add-order-wedding-remark-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            @if ($addser_ids)
                                                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Remark')</div>
                                                            @else
                                                                <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Remark')</div>
                                                            @endif
                                                        </div>
                                                        <form id="addOrderWeddingRemark" action="/admin-fupdate-order-wedding-remark/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('put')
                                                            <div class="row">
                                                                <div class="col-sm-12">
                                                                    <div class="form-group">
                                                                        <textarea name="remark" placeholder="@lang('messages.Insert guest name')" class="textarea_editor form-control border-radius-0 @error('remark') is-invalid @enderror">{!! $orderWedding->remark !!}</textarea>
                                                                        @error('remark')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="card-box-footer">
                                                            @if ($addser_ids)
                                                                <button type="submit" form="addOrderWeddingRemark" class="btn btn-primary"><i class="icon-copy fa fa-pencil"></i> @lang('messages.Change')</button>
                                                            @else
                                                                <button type="submit" form="addOrderWeddingRemark" class="btn btn-primary"><i class="icon-copy fa fa-plus"></i> @lang('messages.Create')</button>
                                                            @endif
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close"></i> @lang('messages.Cancel')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @if ($orderWedding->remark)
                                        <div id="remarkPage" class="col-md-12">
                                            <div class="page-subtitle {{ $orderWedding->remark?"":"empty-value"; }}">
                                                @lang('messages.Remark')
                                            </div>
                                            @if ($orderWedding->remark)
                                                <div class="box-price-kicked">
                                                    {!! $orderWedding->remark !!}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                                {{-- PRICES --}}
                                <div class="col-md-12">
                                    <div class="page-subtitle">
                                        @lang('messages.Price')
                                    </div>
                                   <div class="box-price-kicked">
                                        <div class="row">
                                            @if ($orderWedding->service == "Wedding Package")
                                                @if ($orderWedding->accommodation_price>0)
                                                    <div class="col-6">
                                                        <div class="normal-text">Accommodation + Extra Bed</div>
                                                    </div>
                                                    <div class="col-6 text-right">
                                                        @if ($accommodationPriceContainZero)
                                                            <div class="normal-text text-red">TBA</div>
                                                        @else
                                                            <div class="normal-text">{{ '$ ' . number_format($orderWedding->accommodation_price + $orderWedding->extra_bed_price, 0, ',', '.') }}</div>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if ($orderWedding->transport_invitations_price>0)
                                                    <div class="col-6">
                                                        <div class="normal-text">Transports</div>
                                                    </div>
                                                    <div class="col-6 text-right">
                                                        @if ($transportPriceContainZero)
                                                            <div class="normal-text text-red">TBA</div>
                                                        @else
                                                            <div class="normal-text">{{ '$ ' . number_format($orderWedding->transport_invitations_price, 0, ',', '.') }}</div>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if (count($serviceRequests)>0)
                                                    <div class="col-6">
                                                        <div class="normal-text">Additional Charge</div>
                                                    </div>
                                                    <div class="col-6 text-right">
                                                        @if ($serviceRequestPriceContainZero)
                                                            <div class="normal-text text-red">TBA</div>
                                                        @else
                                                            <div class="normal-text">{{ '$ ' . number_format($serviceRequestPrice, 0, ',', '.') }}</div>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if ($transportInvitations or $weddingAccommodations)
                                                    <div class="col-6">
                                                        <div class="normal-text">@lang('messages.Wedding Package')</div>
                                                    </div>
                                                    <div class="col-6 text-right">
                                                        <div class="normal-text">{{ '$ ' . number_format($orderWedding->package_price, 0, ',', '.') }}</div>
                                                    </div>
                                                @endif
                                                <div class="col-6">
                                                    <hr class="form-hr">
                                                    @if ($invoice)
                                                        <div class="price-name">@lang('messages.Total Price') USD</div>
                                                        @if ($kurs->name != "USD" )
                                                            <div class="price-name">@lang('messages.Total Price') {{ $kurs->name }}</div>
                                                        @endif
                                                    @else
                                                        <div class="price-name">@lang('messages.Total Price')</div>
                                                    @endif
                                                </div>
                                                <div class="col-6 text-right">
                                                    <hr class="form-hr">
                                                    @if ($serviceRequestPriceContainZero)
                                                        <div class="usd-rate text-red">TBA</div>
                                                    @else
                                                        <div class="usd-rate">{{ '$ ' .number_format($orderWedding->final_price, 0, ',', '.') }}</div>
                                                    @endif
                                                    @if ($invoice)
                                                        @if ($kurs->name != "USD")
                                                            @if ($kurs->name == "TWD")
                                                                <div class="usd-rate">{{ 'NT$ ' .number_format($invoice->total_twd, 0, ',', '.') }}</div>
                                                            @elseif($kurs->name == "CNY")
                                                                <div class="usd-rate">{{ '¥ ' .number_format($invoice->total_cny, 0, ',', '.') }}</div>
                                                            @elseif($kurs->name == "IDR")
                                                                <div class="usd-rate">{{ 'Rp ' .number_format($invoice->total_idr, 0, ',', '.') }}</div>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                            @else
                                                <div class="col-6">
                                                    @if ($orderWedding->accommodation_price>0)
                                                        <div class="normal-text">@lang('messages.Accommodation') + @lang('messages.Extra Bed')</div>
                                                    @endif
                                                    @if ($orderWedding->ceremony_venue_price > 0)
                                                        <div class="normal-text">@lang('messages.Ceremony Venue')</div>
                                                    @endif
                                                    @if ($orderWedding->ceremony_venue_decoration_price > 0)
                                                        <div class="normal-text">@lang('messages.Ceremony Venue Decoration')</div>
                                                    @endif
                                                    @if ($orderWedding->reception_venue_price > 0)
                                                        <div class="normal-text">@lang('messages.Reception Venue')</div>
                                                    @endif
                                                    @if ($orderWedding->reception_venue_decoration_price > 0)
                                                        <div class="normal-text">@lang('messages.Reception Venue Decoration')</div>
                                                    @endif
                                                    @if ($orderWedding->additional_services_price > 0)
                                                        <div class="normal-text">@lang('messages.Additional Service')</div>
                                                    @endif
                                                    @if ($orderWedding->transport_invitations_price>0)
                                                        <div class="normal-text">@lang('messages.Transportation')</div>
                                                    @endif
                                                    <hr class="form-hr">
                                                    @if ($invoice)
                                                        <div class="price-name">@lang('messages.Total Price') USD</div>
                                                        @if ($kurs->name != "USD" )
                                                            <div class="price-name">@lang('messages.Total Price') {{ $kurs->name }}</div>
                                                        @endif
                                                    @else
                                                        <div class="price-name">@lang('messages.Total Price')</div>
                                                    @endif
                                                </div>
                                                <div class="col-6 text-right">
                                                    @if ($orderWedding->accommodation_price>0)
                                                        @if ($accommodationPriceContainZero)
                                                            <div class="normal-text text-red">TBA</div>
                                                        @else
                                                            <div class="normal-text">{{ '$ ' . number_format($orderWedding->accommodation_price + $orderWedding->extra_bed_price, 0, ',', '.') }}</div>
                                                        @endif
                                                    @endif
                                                    @if ($orderWedding->ceremony_venue_price > 0)
                                                        <div class="normal-text">{{ '$ ' .number_format($orderWedding->ceremony_venue_price, 0, ',', '.') }}</div>
                                                    @endif
                                                    @if ($orderWedding->ceremony_venue_decoration_price > 0)
                                                        <div class="normal-text">{{ '$ ' .number_format($orderWedding->ceremony_venue_decoration_price, 0, ',', '.') }}</div>
                                                    @endif
                                                    @if ($orderWedding->reception_venue_price > 0)
                                                        <div class="normal-text">{{ '$ ' .number_format($orderWedding->reception_venue_price, 0, ',', '.') }}</div>
                                                    @endif
                                                    @if ($orderWedding->reception_venue_decoration_price > 0)
                                                        <div class="normal-text">{{ '$ ' .number_format($orderWedding->reception_venue_decoration_price, 0, ',', '.') }}</div>
                                                    @endif
                                                    @if ($orderWedding->additional_services_price > 0)
                                                        <div class="normal-text">{{ '$ ' .number_format($orderWedding->additional_services_price, 0, ',', '.') }}</div>
                                                    @endif
                                                    @if ($orderWedding->transport_invitations_price>0)
                                                        @if ($transportPriceContainZero)
                                                            <div class="normal-text text-red">TBA</div>
                                                        @else
                                                            <div class="normal-text">{{ '$ ' . number_format($orderWedding->transport_invitations_price, 0, ',', '.') }}</div>
                                                        @endif
                                                    @endif
                                                    <hr class="form-hr">
                                                    @if ($serviceRequestPriceContainZero || $accommodationPriceContainZero || $transportPriceContainZero)
                                                        <div class="usd-rate text-red">TBA</div>
                                                    @else
                                                        <div class="usd-rate">{{ '$ ' .number_format($orderWedding->final_price, 0, ',', '.') }}</div>
                                                    @endif
                                                    @if ($invoice)
                                                        @if ($kurs->name != "USD")
                                                            @if ($kurs->name == "TWD")
                                                                <div class="usd-rate">{{ 'NT$ ' .number_format($invoice->total_twd, 0, ',', '.') }}</div>
                                                            @elseif($kurs->name == "CNY")
                                                                <div class="usd-rate">{{ '¥ ' .number_format($invoice->total_cny, 0, ',', '.') }}</div>
                                                            @elseif($kurs->name == "IDR")
                                                                <div class="usd-rate">{{ 'Rp ' .number_format($invoice->total_idr, 0, ',', '.') }}</div>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                   </div>
                                </div>
                                {{-- CURRENCY --}}
                                @if ($orderWedding->handled_by)
                                    @if ($orderWedding->handled_by == $admin->id)
                                        @if ($orderWedding->status != "Approved")
                                            @if ($orderWedding->status != "Paid")
                                                <div class="col-md-12">
                                                    <div id="optional_service" class="tab-inner-title m-t-8">@lang('messages.Currency')</div>
                                                    <div class="row urgent-box">
                                                        <div class="col-md-12">
                                                            <div class="notif-modal text-left">
                                                                Please select BANK and Currency, depend on Agent currency before you confirm the order!
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12" style="place-self:center;">
                                                            <form id="validateOrderWedding" action="/fvalidate-order-wedding/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('put')
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="bank">BANK</label>
                                                                            <select name="bank" class="custom-select">
                                                                                @foreach ($banks as $bank)
                                                                                    <option {{ $bank->currency == "USD"?"selected":""; }} value="{{ $bank->id }}">{{ $bank->bank }}</option>
                                                                                @endforeach
                                                                            
                                                                            </select>
                                                                            @error('note')
                                                                                <div class="alert alert-danger">
                                                                                    {{ $message }}
                                                                                </div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="currency">@lang('messages.Currency')</label>
                                                                            <select name="currency" class="custom-select">
                                                                                @foreach ($rates as $rate)
                                                                                    <option {{ $rate->name == "USD"?"selected":""; }} value="{{ $rate->id }}">{{ $rate->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('note')
                                                                                <div class="alert alert-danger">
                                                                                    {{ $message }}
                                                                                </div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if ($orderWedding->service == "Wedding Package")
                                                                    <input type="hidden" name="accommodation_price" value="{{ $orderWedding->accommodation_price>0?$orderWedding->accommodation_price:NULL; }}">
                                                                    <input type="hidden" name="transport_price" value="{{ $orderWedding->transport_invitations_price>0?$orderWedding->transport_invitations_price:NULL; }}">
                                                                @endif
                                                                <input type="hidden" name="status" value="Active">
                                                            </form>
                                                        </div>
                                                    </div>
                                                    @if ($orderWedding->status != "Paid")
                                                        <div class="card-ptext-margin m-t-8">
                                                            <div class="notif-modal text-left">
                                                                Please make sure all the data is correct before you confirm the order!
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="card-ptext-margin m-t-8">
                                                            <div class="notif-modal text-left">
                                                                This order has been validated and the status is Active!
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    @endif
                                @else
                                    @if ($orderWedding->status != "Approved")
                                        @if ($orderWedding->status != "Paid")
                                            <div class="col-md-12 hide-print">
                                                <div class="page-subtitle">@lang('messages.Currency')</div>
                                                <div class="row urgent-box">
                                                    <div class="col-md-8">
                                                        <div class="notif-modal text-left">
                                                            <p>
                                                                Please select currency, depend on Agent currency before you confirm the order!<br> "Default = USD"
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4" style="place-self:center;">
                                                        <form id="validateOrderWedding" action="/fvalidate-order-wedding/{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('put')
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="bank">BANK</label>
                                                                        <select name="bank" class="custom-select">
                                                                            @foreach ($banks as $bank)
                                                                                <option {{ $bank->currency == "USD"?"selected":""; }} value="{{ $bank->id }}">{{ $bank->bank }}</option>
                                                                            @endforeach
                                                                        
                                                                        </select>
                                                                        @error('note')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="currency">@lang('messages.Currency')</label>
                                                                        <select name="currency" class="custom-select">
                                                                            @foreach ($rates as $rate)
                                                                                <option {{ $rate->name == "USD"?"selected":""; }} value="{{ $rate->id }}">{{ $rate->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('note')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if ($orderWedding->service == "Wedding Package")
                                                                <input type="hidden" name="accommodation_price" value="{{ $orderWedding->accommodation_price>0?$orderWedding->accommodation_price:NULL; }}">
                                                                <input type="hidden" name="transport_price" value="{{ $orderWedding->transport_invitations_price>0?$orderWedding->transport_invitations_price:NULL; }}">
                                                            @endif
                                                            <input type="hidden" name="status" value="Active">
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </div>
                            @if (!$orderWedding->confirmation_number or $orderWedding->final_price < 1 or !$bride->groom_pasport_id or !$bride->bride_pasport_id or $accommodation_containt_zero or $transportPriceContainZero or $serviceRequestPriceContainZero)
                                <div class="notification-action-container">
                                    @if (!$orderWedding->confirmation_number)
                                        <p>@lang('messages.Confirmation number is empty')</p>
                                    @endif
                                    @if ($orderWedding->final_price < 1)
                                        <p>@lang('messages.Order not found')</p>
                                    @endif
                                    @if (!$bride->groom_pasport_id or !$bride->bride_pasport_id)
                                        <p>@lang("messages.Bride's data is incomplete")</p>
                                    @endif
                                    @if ($accommodation_containt_zero)
                                        <p>@lang('messages.Recheck Accommodation Price')</p>
                                    @endif
                                    @if ($transportPriceContainZero)
                                        <p>@lang('messages.Recheck Transport prices')</p>
                                    @endif
                                    @if ($serviceRequestPriceContainZero)
                                        <p>@lang('messages.Recheck Additional Charge')</p>
                                    @endif
                                </div>
                            @endif
                            <div class="card-box-footer">
                                {{ $accommodation_containt_zero?"Recheck all service prices!":""; }}
                                @if ($invoice)
                                    @if ($orderWedding->status == 'Approved')
                                        @if ($orderWedding->handled_by)
                                            @if ($orderWedding->handled_by == Auth::user()->id)
                                                {{-- MODAL SEND INVOICE TO AGENT  --}}
                                                <div class="modal fade" id="upload-invoice-pdf-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i> @lang('messages.Send Invoice to') {{ $agent->name }}</div>
                                                                </div>
                                                                <form id="uploadInvoicePdf" action="{{ route('upload.pdf.store',['id'=>$orderWedding->id]) }}" method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <div class="form-group">
                                                                        <label for="emailContent">@lang('messages.Email Content') <span>*</span></label>
                                                                        <textarea name="email_content" class="textarea_editor form-control" placeholder="Insert email content" required></textarea>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="pdf_file">@lang('messages.Select Invoice')</label>
                                                                        <input type="file" name="pdf_file" id="pdf_file" accept=".pdf" class="custom-file-input @error('pdf_file') is-invalid @enderror" placeholder="Choose Contract" value="{{ old('pdf_file') }}" required>
                                                                        @error('pdf_file')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </form>
                                                                <div class="card-box-footer">
                                                                    <button type="submit" form="uploadInvoicePdf" class="btn btn-primary"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i> @lang('messages.Send')</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close"></i> @lang('messages.Cancel')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a href="#" data-toggle="modal" data-target="#upload-invoice-pdf-{{ $orderWedding->id }}"> 
                                                    <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i> @lang('messages.Send Invoice by Email')</button>
                                                </a>
                                            @endif
                                        @else
                                            {{-- MODAL SEND INVOICE TO AGENT  --}}
                                            <div class="modal fade" id="upload-invoice-pdf-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i> @lang('messages.Send Invoice to') {{ $agent->name }}</div>
                                                            </div>
                                                            <form id="uploadInvoicePdf" action="{{ route('upload.pdf.store',['id'=>$orderWedding->id]) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="form-group">
                                                                    <label for="emailContent">@lang('messages.Email Content') <span>*</span></label>
                                                                    <textarea name="email_content" class="textarea_editor form-control" placeholder="Insert email content" required></textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="pdf_file">@lang('messages.Select Invoice')</label>
                                                                    <input type="file" name="pdf_file" id="pdf_file" accept=".pdf" class="custom-file-input @error('pdf_file') is-invalid @enderror" placeholder="Choose Contract" value="{{ old('pdf_file') }}" required>
                                                                    @error('pdf_file')
                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="uploadInvoicePdf" class="btn btn-primary"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i> @lang('messages.Send')</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close"></i> @lang('messages.Cancel')</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="#" data-toggle="modal" data-target="#upload-invoice-pdf-{{ $orderWedding->id }}"> 
                                                <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i> @lang('messages.Send Invoice by Email')</button>
                                            </a>
                                        @endif
                                    @endif
                                    
                                    <a href="en-print-contract-wedding-{{ $orderWedding->id }}" target="__blank" >
                                        <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-print" aria-hidden="true"></i> @lang('messages.Print Invoice') (EN)</button>
                                    </a>
                                    <a href="zh-print-contract-wedding-{{ $orderWedding->id }}" target="__blank" >
                                        <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-print" aria-hidden="true"></i> @lang('messages.Print Invoice') (ZH)</button>
                                    </a>
                                @endif
                                @if ($orderWedding->status == "Pending")
                                    @if ($orderWedding->handled_by == $reservation_staf->id)
                                        @if ($orderWedding->final_price > 0 and $orderWedding->confirmation_number)
                                            @if ($bride->groom_pasport_id and $bride->bride_pasport_id)
                                                @if ($accommodationPriceContainZero or $transportPriceContainZero or $serviceRequestPriceContainZero)
                                                    <button disabled type="submit" form="validateOrderWedding" class="btn btn-success"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Confirm')</button>
                                                @else
                                                    <button {{ $is_valid == 0?"disabled":""; }} {{ $transportPriceContainZero?"disabled":""; }} type="submit" form="validateOrderWedding" class="btn btn-success"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Confirm')</button>
                                                @endif
                                            @else
                                                <button disabled type="submit" form="validateOrderWedding" class="btn btn-success"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Confirm')</button>
                                            @endif
                                        @else
                                            <button disabled type="submit" form="validateOrderWedding" class="btn btn-success"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Confirm')</button>
                                        @endif
                                    @endif
                                @endif
                                <a href="/orders-admin#weddingOrders">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- SIDEBAR DESKTOP --}}
                <div class="col-md-4 desktop">
                    <div class="row">
                        @include('layouts.attentions')
                        <div class="col-md-12">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">@lang('messages.Status')</div>
                                </div> 
                                <div class="order-status-container">
                                    @if ($orderWedding->status == "Active")
                                        <div class="status-active-color">@lang('messages.Confirmed')</div>
                                        @if ($reservation->send == "yes")
                                            - <div class="send-email">@lang('messages.Email') <i class="icon-copy fa fa-envelope" aria-hidden="true"></i></div>
                                        @else
                                            - <div class="not-send-email">@lang('messages.Email') <i class="icon-copy fa fa-envelope" aria-hidden="true"></i></div>
                                        @endif
                                        @if ($reservation->status == "Active")
                                            - <div class="not-approved-order">@lang('messages.Waiting') <i class="icon-copy ion-clock"></i></div>
                                        @endif
                                    @elseif ($orderWedding->status == "Pending")
                                        <div class="status-pending-color">{{ $orderWedding->status }}</div>
                                    @elseif ($orderWedding->status == "Invalid")
                                        <div class="status-invalid-color">{{ $orderWedding->status }}</div>
                                    @elseif ($orderWedding->status == "Rejected")
                                        <div class="status-reject-color">{{ $orderWedding->status }}</div>
                                    @elseif ($orderWedding->status == "Confirmed")
                                        <div class="status-confirmed-color">{{ $orderWedding->status }}</div>
                                    @elseif ($orderWedding->status == "Approved" or $orderWedding->status == "Paid")
                                        <div class="status-approved-color"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ $orderWedding->status }}</div>
                                        @if ($reservation->checkin > $now)
                                            - <div class="standby-order"><p><i class="icon-copy ion-clock"> </i> {{ date('D ,d M Y',strtotime($orderWedding->checkin)) }}</p></div>
                                        @elseif ($reservation->checkin <= $now and $reservation->checkout > $now)
                                            - <div class="ongoing-order">@lang('messages.Ongoing') <i class="icon-copy ion-android-walk"></i></div>
                                        @else
                                            - <div class="final-order">@lang('messages.Final')</div>
                                        @endif
                                    @elseif($orderWedding->status == "Paid")
                                        <div class="status-paid-color"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ $orderWedding->status }} ({{ date('d F Y',strtotime($orderWedding->updated_at)) }})</div>
                                    @else
                                        <div class="status-draf-color">{{ $orderWedding->status }}</div>
                                    @endif
                                    @if ($invoice)
                                        @if($invoice->balance <= 0)
                                            <div class="status-paid-color"><i class="icon-copy fa fa-check-square" aria-hidden="true"></i> @lang('messages.Paid')</div>
                                        @endif
                                    @endif
                                </div>
                                @if (count($orderlogs)>0)
                                    <hr class="form-hr">
                                    <p><b>@lang('messages.Order Log'):</b></p>
                                    <table class="table tb-list">
                                        @foreach ($orderlogs as $no=>$orderlog)
                                            @php
                                                $adminorder = $admins->where('id',$orderlog->admin)->first();
                                            @endphp
                                            <tr>
                                                <td>{{ ++$no.". " }}</td>
                                                <td> {!! date('d M Y (H:i)',strtotime($orderlog->created_at)) !!}</td>
                                                <td>{!! $adminorder->code !!}</td>
                                                <td><i>{!! $orderlog->action !!}</i></td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @endif
                            </div>
                        </div>
                        {{-- ORDER NOTE --}}
                        <div class="col-md-12">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">@lang('messages.Order Note')</div>
                                </div> 
                                @foreach ($order_notes as $order_note)
                                    <div class="container-order-note">
                                        @php
                                            $operator = Auth::user()->where('id',$order_note->user_id)->first();
                                        @endphp
                                        <p><b>{{ date('d M Y (H:i)',strtotime($order_note->created_at))." - ".$operator->name }}</b> (<i>{{ $order_note->status }}</i>)</p>
                                        <p class="m-l-18">{!! $order_note->note !!}</p>
                                        
                                        <hr class="form-hr">
                                    </div>
                                @endforeach
                                @if ($orderWedding->status !== "Paid")
                                    <div class="card-box-footer">
                                        <a href="#" data-toggle="modal" data-target="#add-order-note"><button type="button" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> @lang('messages.Add Note')</button></a>
                                    </div>
                                @endif
                            </div>
                            {{-- MODAL ORDER NOTE --}}
                            <div class="modal fade" id="add-order-note" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <div class="title"><i class="fa fa-plus" aria-hidden="true"></i> @lang('messages.Add Note')</div>
                                            </div>
                                            <form id="faddAddNoteDesktop" action="/fadd-order-wedding-note-{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group row">
                                                    <label for="status" class="col-sm-12">Type</label>
                                                    <div class="col-sm-12">
                                                        <select name="status" class="custom-select @error('status') is-invalid @enderror" value="{{ old('status') }}">
                                                            <option selected value="Urgent">@lang('messages.Urgent')</option>
                                                            <option value="Waiting">@lang('messages.Waiting')</option>
                                                            <option value="Error">@lang('messages.Error')</option>
                                                            <option value="Cancel">@lang('messages.Cancel')</option>
                                                            <option value="Reject">@lang('messages.Reject')</option>
                                                            <option value="Info">@lang('messages.Info')</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="order_note" class="col-sm-12">@lang('messages.Note')</label>
                                                    <div class="col-sm-12">
                                                        <textarea id="order_note" name="order_note" placeholder="Insert order note" class="textarea_editor form-control border-radius-0" autofocus required></textarea>
                                                        @error('order_note')
                                                            <div class="alert alert-danger">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <input type="hidden" name="user_id" value="{{ Auth::User()->id }}">
                                                <input type="hidden" name="order_id" value="{{ $orderWedding->id }}">
                                            </Form>
                                            <div class="card-box-footer">
                                                <div class="form-group">
                                                    <button type="submit" form="faddAddNoteDesktop" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Submit')</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if ($orderWedding->status == "Active" or $orderWedding->status == "Approved" or $orderWedding->status == "Paid")
                            {{-- KURS --}}
                            @if ($invoice)
                                @php
                                    $invoice_sell_usd = floatval($invoice->sell_usd);
                                    $invoice_buy_usd = floatval($invoice->rate_usd);
                                    $invoice_sell_cny = floatval($invoice->sell_cny);
                                    $invoice_buy_cny = floatval($invoice->rate_cny);
                                    $invoice_sell_twd = floatval($invoice->sell_twd);
                                    $invoice_buy_twd = floatval($invoice->rate_twd);
                                @endphp
                                <div class="col-md-12">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title">@lang('messages.Kurs on order')</div>
                                            <span>{{ date('d F Y',strtotime($invoice->inv_date)) }}</span>
                                        </div> 
                                        
                                        <div class="row">
                                            <div class="col-4 col-md-4">
                                                <p><b>@lang('messages.Currency')</b></p>
                                            </div>
                                            <div class="col-4 col-md-4">
                                                <p><b>@lang('messages.Sell')</b></p>
                                            </div>
                                            <div class="col-4 col-md-4">
                                                <p><b>@lang('messages.Buy')</b></p>
                                            </div>
                                            <div class="col-4 col-md-4">
                                                <p>USD</p>
                                                <p>CNY</p>
                                                <p>TWD</p>
                                            </div>
                                            <div class="col-4 col-md-4">
                                                <p>{{ currencyFormatIdr($invoice_sell_usd) }}</p>
                                                <p>{{ currencyFormatIdr($invoice_sell_cny) }}</p>
                                                <p>{{ currencyFormatIdr($invoice_sell_twd) }}</p>
                                            </div>
                                            <div class="col-4 col-md-4">
                                                <p>{{ currencyFormatIdr($invoice_buy_usd) }}</p>
                                                <p>{{ currencyFormatIdr($invoice_buy_cny) }}</p>
                                                <p>{{ currencyFormatIdr($invoice_buy_twd) }}</p>
                                            </div>
                                        </div>
                                        <div class="card-ptext-margin">
                                            <div class="notification p-0">
                                                @lang('messages.Payments made in currencies other than the specified currency will be automatically converted based on the exchange rate in effect at the time of your service booking.')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{-- RECEIPT --}}
                            @if (count($receipts)>0)
                                <div class="col-md-12">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title">@lang('messages.Payment Receipt')</div>
                                            @if ($invoice->balance > 0)
                                                <span>
                                                    <a href="#" data-toggle="modal" data-target="#desktop-admin-add-receipt-wedding-{{ $orderWedding->id }}">
                                                        <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="pmt-des m-b-8">
                                            <div class="card-ptext-content">
                                                <div class="ptext-title">@lang('messages.Payment deadline')</div>
                                                <div class="ptext-value">{{ date('d F Y',strtotime($invoice->due_date)) }}</div>
                                            </div>
                                        </div>
                                        <div class="pmt-des m-b-8">
                                            <div class="card-ptext-content">
                                                @if ($invoice->currency->name == 'USD')
                                                    <div class="ptext-title">@lang('messages.Total Invoice') USD</div>
                                                    <div class="ptext-value">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                                                @elseif ($invoice->currency->name == 'TWD')
                                                    <div class="ptext-title">@lang('messages.Total Invoice') USD</div>
                                                    <div class="ptext-value">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                                                    <div class="ptext-title">@lang('messages.Total Invoice') TWD</div>
                                                    <div class="ptext-value">{{ currencyFormatTwd($invoice->total_twd) }}</div>
                                                @elseif ($invoice->currency->name == 'CNY')
                                                    <div class="ptext-title">@lang('messages.Total Invoice') USD</div>
                                                    <div class="ptext-value">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                                                    <div class="ptext-title">@lang('messages.Total Invoice') CNY</div>
                                                    <div class="ptext-value">{{ currencyFormatCny($invoice->total_cny) }}</div>
                                                @elseif ($invoice->currency->name == 'IDR')
                                                    <div class="ptext-title">@lang('messages.Total Invoice') USD</div>
                                                    <div class="ptext-value">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                                                    <div class="ptext-title">@lang('messages.Total Invoice') IDR</div>
                                                    <div class="ptext-value">{{ currencyFormatIdr($invoice->total_idr) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="m-b-18">
                                            @foreach ($receipts as $receipt)
                                                <a class="card-link" href="#" data-toggle="modal" data-target="#desktop-receipt-{{ $receipt->id }}">
                                                    <div class="card-ptext-margin">
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
                                                                        @if ($receipt->status == "Valid")
                                                                            <i data-toggle="tooltip" data-placement="top" title="@lang('messages.Verified')" style="color: rgb(0, 167, 14); cursor:help" class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                                                                        @else
                                                                            <i style="font-size: 0.7rem; color:rgb(255 0 0);">{{ $receipt->status }}</i>
                                                                        @endif
                                                                    </b>
                                                                    <div class="card-ptext-content">
                                                                        <div class="ptext-title">@lang('messages.Recived on')</div>
                                                                        <div class="ptext-value">{{ date('d F Y',strtotime($receipt->created_at)) }}</div>
                                                                        @if ($receipt->kurs_name == 'USD')
                                                                            <div class="ptext-title">@lang('messages.Amount')</div>
                                                                            <div class="ptext-value">{{ $receipt->kurs_name }} {{ currencyFormatUsd($receipt->amount) }}</div>
                                                                        @elseif($receipt->kurs_name == 'TWD')
                                                                            <div class="ptext-title">@lang('messages.Amount')</div>
                                                                            <div class="ptext-value">{{ $receipt->kurs_name }} {{ currencyFormatTwd($receipt->amount) }}</div>
                                                                        @elseif($receipt->kurs_name == 'CNY')
                                                                            <div class="ptext-title">@lang('messages.Amount')</div>
                                                                            <div class="ptext-value">{{ $receipt->kurs_name }} {{ currencyFormatCny($receipt->amount) }}</div>
                                                                        @elseif($receipt->kurs_name == 'IDR')
                                                                            <div class="ptext-title">@lang('messages.Amount')</div>
                                                                            <div class="ptext-value">{{ $receipt->kurs_name }} {{ currencyFormatIdr($receipt->amount) }}</div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if ($receipt->status != "Valid")
                                                            <div class="view-receipt">
                                                                <a href="#" data-toggle="modal" data-target="#desktop-receipt-{{ $receipt->id }}">
                                                                    <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </a>
                                                {{-- MODAL VALIDATE RECEIPT --}}
                                                <div class="modal fade" id="desktop-receipt-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="card-box">
                                                                <div class="card-box-title text-left">
                                                                    <div class="title"> <i class="icon-copy fa fa-file-image-o" aria-hidden="true"></i>@lang('messages.Validate Receipt')</div>
                                                                </div>
                                                                    <form id="confirmation-payment-desktop-{{ $receipt->id }}" action="/forder-wedding-confirmation-payment-{{ $receipt->id }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        <div class="row text-left">
                                                                            <div class="col-md-12">
                                                                                <div class="row">
                                                                                    <div class="col-sm-6">
                                                                                        <div class="row">
                                                                                            <div class="col-md-12 text-center">
                                                                                                <div class="modal-receipt-container">
                                                                                                    <img src="/storage/receipt/weddings/{{ $receipt->receipt_img }}" alt="">
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-6">
                                                                                        <div class="row">
                                                                                            <div class="col-5"><p>@lang('messages.Order Number')</p></div>
                                                                                            <div class="col-7"><p><b>: {{ $orderWedding->orderno }}</b></p></div>
                                                                                            <div class="col-5"><p>@lang('messages.Reservation Number')</p></div>
                                                                                            <div class="col-7"><p><b>: {{ $reservation->rsv_no }}</b></p></div>
                                                                                            <div class="col-5"><p>@lang('messages.Invoice Number')</p></div>
                                                                                            <div class="col-7"><p><b>: {{ $invoice->inv_no }}</b></p></div>
                                                                                            <div class="col-5"><p>@lang('messages.Due Date')</p></div>
                                                                                            <div class="col-7"><p>: {{ date('d F Y',strtotime($invoice->due_date)) }}</p></div>
                                                                                            
                                                                                            @if ($receipt->status == 'Valid')
                                                                                                <div class="col-12">
                                                                                                    <hr class="form-hr">
                                                                                                </div>
                                                                                                <div class="col-5"><p>@lang('messages.Payment Date')</p></div>
                                                                                                <div class="col-7"><p>: {{ date('d F Y',strtotime($receipt->payment_date)) }}</p></div>
                                                                                                <div class="col-5">
                                                                                                    <p>@lang('messages.Payment Amount')</p>
                                                                                                </div>
                                                                                                <div class="col-7">
                                                                                                    @if ($receipt->kurs_name == "USD")
                                                                                                        <p><b>: {{ currencyFormatUsd($receipt->amount) }}</b></p>
                                                                                                    @elseif ($receipt->kurs_name == "CNY")
                                                                                                        <p><b>: {{ currencyFormatCny($receipt->amount) }}</b></p>
                                                                                                    @elseif ($receipt->kurs_name == "TWD")
                                                                                                        <p><b>: {{ currencyFormatTwd($receipt->amount) }}</b></p>
                                                                                                    @elseif ($receipt->kurs_name == "IDR")
                                                                                                        <p><b>: {{ currencyFormatIdr($receipt->amount) }}</b></p>
                                                                                                    @endif
                                                                                                </div>
                                                                                                <div class="col-12">
                                                                                                    <hr class="form-hr">
                                                                                                </div>
                                                                                            @endif
                                                                                            @if ($orderWedding->handled_by == $admin->id)
                                                                                                @if ($invoice->balance > 0)
                                                                                                    <div class="col-md-12">
                                                                                                        <div class="form-group">
                                                                                                            <label for="status" class="form-label">@lang('messages.Receipt Status') <span>*</span></label>
                                                                                                            <select name="status" class="custom-select @error('status') is-invalid @enderror" required>
                                                                                                                <option selected value="{{ $receipt->status }}">{{ $receipt->status }}</option>
                                                                                                                <option value="Valid">@lang('messages.Valid')</option>
                                                                                                                <option value="Invalid">@lang('messages.Invalid')</option>
                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    @if ($receipt->status == "Pending" or $receipt->status == "Invalid")
                                                                                                        <div class="col-md-12">
                                                                                                            <div class="form-group">
                                                                                                                <label for="kurs" class="form-label">@lang('messages.Currency')<span>*</span></label>
                                                                                                                <select name="kurs" class="custom-select @error('kurs') is-invalid @enderror" required>
                                                                                                                    <option selected value="{{ $receipt->kurs_name ? $receipt->kurs_name : '' }}">{{ $receipt->kurs_name ? $receipt->kurs_name : 'Select Currency' }}</option>
                                                                                                                    <option value="USD">USD</option>
                                                                                                                    <option value="CNY">CNY</option>
                                                                                                                    <option value="TWD">TWD</option>
                                                                                                                    <option value="IDR">IDR</option>
                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-12">
                                                                                                            <div class="form-group">
                                                                                                                <label for="amoun" class="form-label col-form-label">@lang('messages.Amount')</label>
                                                                                                                <input type="text" name="amount" class="input-icon form-control @error('amount') is-invalid @enderror" placeholder="Insert Amount" value="{{ $receipt->amount }}" required>
                                                                                                                @error('amount')
                                                                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                                                                @enderror
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    @else
                                                                                                        <input hidden type="text" name="kurs" value="{{ $receipt->kurs_name }}">
                                                                                                        <input hidden type="text" name="amount" value="{{ $receipt->amount }}">
                                                                                                    @endif
                                                                                                    <div class="col-md-12">
                                                                                                        <div class="form-group">
                                                                                                            <label for="payment_date" class="form-label col-form-label">@lang('messages.Payment Date')</label>
                                                                                                            <input readonly type="text" id="payment_date" name="payment_date" class="form-control date-picker @error('payment_date') is-invalid @enderror" placeholder="Payment Date" value="{{ date('d F Y',strtotime($receipt->payment_date)) }}" required>
                                                                                                            @error('payment_date')
                                                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                                                            @enderror
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                        <div class="form-group">
                                                                                                            <label for="note">@lang('messages.Description') </label>
                                                                                                            <textarea name="note" class="textarea_editor form-control @error('note') is-invalid @enderror" placeholder="Description">{{ $receipt->note }}</textarea>
                                                                                                            @error('note')
                                                                                                                <span class="invalid-feedback">
                                                                                                                    <strong>{{ $message }}</strong>
                                                                                                                </span>
                                                                                                            @enderror
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <input type="hidden" name="order_id" value="{{ $orderWedding->id }}">
                                                                                                @endif
                                                                                            @else
                                                                                                <div class="col-12">
                                                                                                    <div class="notification">
                                                                                                        @php
                                                                                                            $validator = $admins->where('id',$orderWedding->handled_by)->first()
                                                                                                        @endphp
                                                                                                        @lang('messages.This Order can validate by') {{ $validator?$validator->name:"Admin" }}
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
                                                                    @if ($orderWedding->handled_by == $admin->id)
                                                                        @if ($invoice->balance > 0)
                                                                            <button type="submit" form="confirmation-payment-desktop-{{ $receipt->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> {{ $receipt->status == 'Pending'?"Validate":"Update" }}</button>
                                                                        @endif
                                                                    @endif
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @php
                                            $total_payment_usd = $receipts->where('status', 'Valid')->where('kurs_id', '1')->sum('amount');
                                            $total_payment_cny = $receipts->where('status', 'Valid')->where('kurs_id', '2')->sum('amount');
                                            $total_payment_twd = $receipts->where('status', 'Valid')->where('kurs_id', '3')->sum('amount');
                                            $total_payment_idr = $receipts->where('status', 'Valid')->where('kurs_id', '4')->sum('amount');
                                        @endphp
                                        @if ($total_payment_usd > 0)
                                            <div class="pmt-des">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">@lang('messages.Total Payment') USD</div>
                                                    <div class="ptext-value">{{ currencyFormatUsd($total_payment_usd) }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($total_payment_cny > 0)
                                            <div class="pmt-des">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">@lang('messages.Total Payment') CNY</div>
                                                    <div class="ptext-value">{{ currencyFormatCny($total_payment_cny) }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($total_payment_twd > 0)
                                            <div class="pmt-des">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">@lang('messages.Total Payment') TWD</div>
                                                    <div class="ptext-value">{{ currencyFormatTwd($total_payment_twd) }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($total_payment_idr > 0)
                                            <div class="pmt-des">
                                                <div class="card-ptext-content">
                                                    <div class="ptext-title">@lang('messages.Total Payment') IDR</div>
                                                    <div class="ptext-value">{{ currencyFormatIdr($total_payment_idr) }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($total_payment_usd > 0 || $total_payment_cny > 0 || $total_payment_twd > 0 || $total_payment_idr > 0)
                                            <hr class="form-hr">
                                        @endif
                                        <div class="pmt-des m-t-8">
                                            <div class="card-ptext-content">
                                                <div class="ptext-title"><b>@lang('messages.Balance')</b></div>
                                                @if ($invoice)
                                                    @if ($invoice->balance <= 1)
                                                        <div class="ptext-value usd-rate">@lang('messages.Paid')</div>
                                                    @else
                                                        @if ($invoice->currency->name == "USD")
                                                            <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->balance) }}</b></div>
                                                        @elseif ($invoice->currency->name == "CNY")
                                                            <div class="ptext-value"><b>{{ currencyFormatCny($invoice->balance) }}</b></div>
                                                        @elseif ($invoice->currency->name == "TWD")
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
                                            <div class="title">@lang('messages.Payment Receipt')</div>
                                            <span>
                                                <a href="#" data-toggle="modal" data-target="#desktop-admin-add-receipt-wedding-{{ $orderWedding->id }}">
                                                    <i class="icon-copy fa fa-plus" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                        </div>
                                        <div class="pmt-des">
                                            <div class="card-ptext-content">
                                                <div class="ptext-title">@lang('messages.Invoice No')</div>
                                                <div class="ptext-value">{{ $invoice->inv_no }}</div>
                                                <div class="ptext-title">@lang('messages.Payment deadline')</div>
                                                <div class="ptext-value">{{ date('d M Y',strtotime($invoice->due_date)) }}</div>
                                                @if ($invoice->currency->name == 'USD')
                                                    <div class="ptext-title"><b>@lang('messages.Total Price')</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></div>
                                                @elseif ($invoice->currency->name == 'TWD')
                                                    <div class="ptext-title"><b>Total USD</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></div>
                                                    <div class="ptext-title"><b>Total TWD</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatTwd($invoice->total_twd) }}</b></div>
                                                @elseif ($invoice->currency->name == 'CNY')
                                                    <div class="ptext-title"><b>Total USD</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></div>
                                                    <div class="ptext-title"><b>Total CNY</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatCny($invoice->total_cny) }}</b></div>
                                                @elseif ($invoice->currency->name == 'IDR')
                                                    <div class="ptext-title"><b>Total USD</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></div>
                                                    <div class="ptext-title"><b>Total IDR</b></div>
                                                    <div class="ptext-value"><b>{{ currencyFormatIdr($invoice->total_idr) }}</b></div>
                                                @endif
                                            </div>
                                            
                                        </div>
                                        
                                    </div>
                                </div>
                            @endif
                            {{-- MODAL ADD PAYMENT CONFIRMATION WEDDING --}}
                            <div class="modal fade" id="desktop-admin-add-receipt-wedding-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="card-box">
                                            <div class="card-box-title text-left">
                                                <div class="title"><i class="icon-copy fa fa-usd" aria-hidden="true"></i>@lang('messages.Payment Confirmation')</div>
                                            </div>
                                            <form id="desktop-payment-confirm-wedding-{{ $orderWedding->id }}" action="/order-wedding-add-payment-confirmation-{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row text-left">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="row m-t-27">
                                                                    <div class="col-5"><p>@lang('messages.Order Number')</p></div>
                                                                    <div class="col-7"><p><b>: {{ $orderWedding->orderno }}</b></p></div>
                                                                    <div class="col-5"><p>@lang('messages.Reservation Number')</p></div>
                                                                    <div class="col-7"><p><b>: {{ $reservation->rsv_no }}</b></p></div>
                                                                    <div class="col-5"><p>@lang('messages.Invoice Number')</p></div>
                                                                    <div class="col-7"><p><b>: {{ $invoice->inv_no }}</b></p></div>
                                                                    <div class="col-5"><p>@lang('messages.Due Date')</p></div>
                                                                    <div class="col-7"><p>: {{ date('d F Y',strtotime($invoice->due_date)) }}</p></div>
                                                                    <div class="col-5"><p>@lang('messages.Amount')</p></div>
                                                                    <div class="col-7"><p><b>: {{ currencyFormatUsd($orderWedding->final_price) }}</b></p></div>
                                                                    <div class="col-12 m-t-18"><p><i class="icon-copy fa fa-exclamation" aria-hidden="true"></i> @lang('messages.Please make the payment before the due date and provide proof of payment to prevent the cancellation of your order.')</p></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="dropzone">
                                                                            <div class="img-preview">
                                                                                <img id="desktop-img-preview" src="#" alt="Your Image" style="max-width: 100%; display: none;" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-12 m-t-8">
                                                                        <div class="form-group">
                                                                            <label for="desktop_receipt_name" class="form-label">@lang('messages.Select Receipt') </label><br>
                                                                            <input type="file" name="desktop_receipt_name" id="desktop_receipt_name" class="custom-file-input @error('desktop_receipt_name') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('desktop_receipt_name') }}" required>
                                                                            @error('desktop_receipt_name')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="order_id" value="{{ $orderWedding->id }}">
                                                </div>
                                            </form>
                                            <div class="card-box-footer">
                                                <button type="submit" form="desktop-payment-confirm-wedding-{{ $orderWedding->id }}" class="btn btn-primary"><i class="icon-copy fa fa-upload" aria-hidden="true"></i> @lang('messages.Send')</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- LOADING SPINNER -----------------------------------------------------------> --}}
    <div id="loading" style="display: none;">
        <div class="spinner"></div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("validateOrderWedding");
            const loading = document.getElementById("loading");
    
            form.addEventListener("submit", function(event) {
                loading.style.display = "block";
                loading.style.opacity = "1";
                document.body.style.overflow = "hidden";
            });
        });
    </script>
    {{-- LOADING SPINNER -----------------------------------------------------------> --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
        // Asumsikan ada beberapa form, jadi kita gunakan querySelectorAll
        const forms = document.querySelectorAll('[id^="kurs_"]');

        // Mapping dari currency ke symbol
        const currencySymbols = {
            "USD": "$",
            "CNY": "¥",
            "TWD": "NT$",
            "IDR": "Rp"
        };

        forms.forEach((kursSelect, index) => {
            const amountInput = document.getElementById(`amount_${index}`);
            const currencySymbol = document.getElementById(`currency-symbol-${index}`);

            // Fungsi untuk memperbarui placeholder dan simbol
            function updateCurrency() {
                const selectedCurrency = kursSelect.value;
                const symbol = currencySymbols[selectedCurrency] || "$"; // Default ke "$" jika tidak ada

                // Perbarui simbol dan placeholder
                currencySymbol.textContent = symbol;
                amountInput.placeholder = selectedCurrency;
            }

            // Panggil updateCurrency saat halaman dimuat
            updateCurrency();

            // Event listener untuk perubahan di dropdown
            kursSelect.addEventListener("change", function() {
                updateCurrency();
            });
        });
    });
    </script>
    <script>
        $(document).ready(function() {
            var ro = 1;
            var limit = 5;
            var t = 1
            $(".add-more").click(function(){ 
                if (t < limit) {
                    t++;
                    ro++;
                    var html = $(".copy").html();
                    $(".after-add-more").before(html);
                }
            });
            $("body").on("click",".remove",function(){ 
                $(this).parents(".control-group").remove();
                t--;
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            var venueRadios = document.querySelectorAll('input[name="ceremonial_venue_id"]');
            var slotSelect = document.getElementById('slot');
    
            venueRadios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    var selectedVenue = JSON.parse(this.getAttribute('data-slots'));
                    var basicPrices = JSON.parse(this.getAttribute('data-basic-prices'));
                    var arrangementPrices = JSON.parse(this.getAttribute('data-arrangement-prices'));
                    populateSlots(selectedVenue, basicPrices, arrangementPrices);
                });
            });
    
            function populateSlots(slots, basicPrices, arrangementPrices) {
                slotSelect.innerHTML = '';
                slots.forEach(function(slot, index) {
                    var option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    option.setAttribute('data-basic-price',basicPrices[index]);
                    option.setAttribute('data-arrangement-price',arrangementPrices[index]);
                    slotSelect.appendChild(option);
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
            var slotSelect = document.getElementById('slot');
            var basicPriceInput = document.getElementById('basic_price');
            var arrangementPriceInput = document.getElementById('arrangement_price');
    
            slotSelect.addEventListener('change', function () {
                var selectedOption = slotSelect.options[slotSelect.selectedIndex];
                var basicPrice = selectedOption.getAttribute('data-basic-price');
                var arrangementPrice = selectedOption.getAttribute('data-arrangement-price');
    
                basicPriceInput.value = basicPrice ? basicPrice : '';
                arrangementPriceInput.value = arrangementPrice ? arrangementPrice : '';
            });
            var event = new Event('change');
            slotSelect.dispatchEvent(event);
        });
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.checkbox-input').forEach(function(checkbox) {
                toggleCheck(checkbox);
            });
        });

        function toggleCheck(checkbox) {
            if (checkbox.checked) {
                checkbox.closest('.card-checkbox').classList.add('checked');
                checkbox.setAttribute('checked', 'checked');
            } else {
                checkbox.closest('.card-checkbox').classList.remove('checked');
                checkbox.removeAttribute('checked');
            }
        }

        function toggleCard(card) {
            const checkbox = card.querySelector('.checkbox-input');
            checkbox.checked = !checkbox.checked;
            toggleCheck(checkbox);
        }
    </script>
    <script>
        $(function() {
            var previewCoverPackage = function(input, imgPreviewPlaceholder) {
                if (input.files) {
                    var filesAmount = input.files.length;
                    for (i = 0; i < filesAmount; i++) {
                        var reader = new FileReader();
                        reader.onload = function(event) {
                            // Clear existing images
                            $(imgPreviewPlaceholder).html('');
                            $($.parseHTML('<img>')).attr('src', event.target.result).appendTo(imgPreviewPlaceholder);
                        }
                        reader.readAsDataURL(input.files[i]);
                    }
                }
            };
    
            @foreach($guests as $index => $guest)
                $('#cover-{{ $index }}').on('change', function() {
                    previewCoverPackage(this, '#cover-package-preview-div-{{ $index }}');
                });
            @endforeach
        });
    </script>
    <script>
        var roomFor = document.getElementById('roomForForm');
        var guestsName = document.getElementById('guestsNameForm');
        var numberOfGuests = document.getElementById('numberOfGuestsForm');
        var extraBed = document.getElementById('extraBedIdForm');
         
        
        
        if (roomFor.value == 'Couple') {
            roomFor.addEventListener('change', function() {
                if (this.value === 'Couple') {
                    guestsName.setAttribute('hidden', 'hidden');
                    numberOfGuests.setAttribute('hidden', 'hidden');
                    extraBed.setAttribute('hidden', 'hidden');
                } else {
                    guestsName.removeAttribute('hidden');
                    numberOfGuests.removeAttribute('hidden');
                    extraBed.removeAttribute('hidden');
                }
                if (typeSelect.value === 'Couple') {
                    guestNameField.setAttribute('hidden', 'hidden');
                    numberOfGuestsField.setAttribute('hidden', 'hidden');
                    extraBed.setAttribute('hidden', 'hidden');
                }
            });
        } else {
            guestsName.removeAttribute('hidden');
            numberOfGuests.removeAttribute('hidden');
            extraBed.removeAttribute('hidden');
        }
        document.addEventListener("DOMContentLoaded", function() {
            var venueRadios = document.querySelectorAll('input[name="ceremonial_venue_id"]');
            var slotSelect = document.getElementById('slot');
    
            venueRadios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    var selectedVenue = JSON.parse(this.getAttribute('data-slots'));
                    var basicPrices = JSON.parse(this.getAttribute('data-basic-prices'));
                    var arrangementPrices = JSON.parse(this.getAttribute('data-arrangement-prices'));
                    populateSlots(selectedVenue, basicPrices, arrangementPrices);
                });
            });
    
            function populateSlots(slots, basicPrices, arrangementPrices) {
                slotSelect.innerHTML = '';
                slots.forEach(function(slot, index) {
                    var option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    option.setAttribute('data-basic-price',basicPrices[index]);
                    option.setAttribute('data-arrangement-price',arrangementPrices[index]);
                    slotSelect.appendChild(option);
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
            var slotSelect = document.getElementById('slot');
            var basicPriceInput = document.getElementById('basic_price');
            var arrangementPriceInput = document.getElementById('arrangement_price');
    
            slotSelect.addEventListener('change', function () {
                var selectedOption = slotSelect.options[slotSelect.selectedIndex];
                var basicPrice = selectedOption.getAttribute('data-basic-price');
                var arrangementPrice = selectedOption.getAttribute('data-arrangement-price');
    
                basicPriceInput.value = basicPrice ? basicPrice : '';
                arrangementPriceInput.value = arrangementPrice ? arrangementPrice : '';
            });
            var event = new Event('change');
            slotSelect.dispatchEvent(event);
        });
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.checkbox-input').forEach(function(checkbox) {
                toggleCheck(checkbox);
            });
        });

        function toggleCheck(checkbox) {
            if (checkbox.checked) {
                checkbox.closest('.card-checkbox').classList.add('checked');
                checkbox.setAttribute('checked', 'checked');
            } else {
                checkbox.closest('.card-checkbox').classList.remove('checked');
                checkbox.removeAttribute('checked');
            }
        }

        function toggleCard(card) {
            const checkbox = card.querySelector('.checkbox-input');
            checkbox.checked = !checkbox.checked;
            toggleCheck(checkbox);
        }

        $(document).ready(function() {
            var ro = 1;
            var limit = 5;
            var t = 1
            $(".add-more").click(function(){ 
                if (t < limit) {
                    t++;
                    ro++;
                    var html = $(".copy").html();
                    $(".after-add-more").before(html);
                }
            });
            $("body").on("click",".remove",function(){ 
                $(this).parents(".control-group").remove();
                t--;
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            const roomRadios = document.querySelectorAll('input[name="transport_id"]');
            const roomRadiosEdit = document.querySelectorAll('input[name="edit_transport_id"]');
            const numberOfGuestsInput = document.getElementById('nogTransport');
            const numberOfGuestsInputEdit = document.getElementById('nogTransportEdit');

            roomRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    const selectedCapacity = this.getAttribute('data-capacity');
                    const selectedCapacityEdit = this.getAttribute('data-capacity-edit');
                    numberOfGuestsInput.max = selectedCapacity;
                    numberOfGuestsInputEdit.max = selectedCapacityEdit;
                    if (parseInt(numberOfGuestsInput.value) > parseInt(selectedCapacity)) {
                        numberOfGuestsInput.value = selectedCapacity;
                    }
                    if (parseInt(numberOfGuestsInputEdit.value) > parseInt(selectedCapacityEdit)) {
                        numberOfGuestsInputEdit.value = selectedCapacityEdit;
                    }
                });
            });
        });
    </script>
    
    <script>
        $(document).ready(function() {
            function toggleElements(no_rexser) {
                var status = $('#status_' + no_rexser).val();
                if (status === 'Rejected') {
                    $('#remark-div_' + no_rexser).removeClass('display-none');
                    $('#remark_' + no_rexser).attr('required', 'required');
                    
                } else {
                    $('#remark-div_' + no_rexser).addClass('display-none');
                    $('#remark_' + no_rexser).removeAttr('required');
                    
                }
            }
    
            $('select[name="status"]').change(function() {
                var no_rexser = $(this).attr('id').split('_')[1];
                toggleElements(no_rexser);
            });
    
            // Initial call to set the correct state on page load
            @foreach($serviceRequests as $no_rexser => $request_service)
                toggleElements({{ $no_rexser }});
            @endforeach
        });
    </script>
    <script>
        document.getElementById('passport-cover').addEventListener('change', function(event) {
            const imgPreview = document.getElementById('passport-img-preview');
            const file = event.target.files[0];
    
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imgPreview.src = '#';
                imgPreview.style.display = 'none';
            }
        });
    </script>
    <script>
        document.getElementById('desktop_receipt_name').addEventListener('change', function(event) {
            const imgPreview = document.getElementById('desktop-img-preview');
            const file = event.target.files[0];
    
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imgPreview.src = '#';
                imgPreview.style.display = 'none';
            }
        });
    </script>
    <script>
        document.getElementById('mobile_receipt_name').addEventListener('change', function(event) {
            const imgPreview = document.getElementById('mobile-img-preview');
            const file = event.target.files[0];
    
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imgPreview.src = '#';
                imgPreview.style.display = 'none';
            }
        });
    </script>
@endsection