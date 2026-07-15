@foreach ($receiptCollection as $receipt)
    @if ($receipt->receipt_img)
        <div class="modal fade" id="receipt-modal-{{ $receipt->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('messages.Payment Receipt')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('messages.Close')"></button>
                    </div>
                    <div class="modal-body">
                        <img class="order-detail-receipt-image" src="{{ asset('storage/receipt/' . $receipt->receipt_img) }}" alt="@lang('messages.Payment Receipt')">
                        @if ($receipt->note)
                            <div class="order-detail-alert mt-3">{!! $receipt->note !!}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@if ($invoice && $order->status !== 'Paid')
    <div class="modal fade" id="payment-confirmation-{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('messages.Payment Confirmation')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('messages.Close')"></button>
                </div>
                <form id="payment-confirm-{{ $order->id }}" action="{{ route('upload.payment-confirmation', ['id' => $order->id]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="order-detail-grid">
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Order Number')</span>
                                        <strong>{{ $order->orderno }}</strong>
                                    </div>
                                    @if ($reservation)
                                        <div class="order-detail-info">
                                            <span>@lang('messages.Reservation Number')</span>
                                            <strong>{{ $reservation->rsv_no }}</strong>
                                        </div>
                                    @endif
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Invoice Number')</span>
                                        <strong>{{ $invoice->inv_no }}</strong>
                                    </div>
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Due Date')</span>
                                        <strong>{{ dateFormat($invoice->due_date) }}</strong>
                                    </div>
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Amount')</span>
                                        <strong>{{ currencyFormatUsd($order->final_price) }}</strong>
                                    </div>
                                </div>
                                <div class="order-detail-alert mt-3">
                                    @lang('messages.Please make the payment before the due date and provide proof of payment to prevent the cancellation of your order.')
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="receipt_name" class="form-label">@lang('messages.Select Receipt')</label>
                                <input type="file" name="receipt_name" id="receipt_name" class="form-control @error('receipt_name') is-invalid @enderror" data-receipt-input="#receipt-preview-{{ $order->id }}" data-receipt-empty="{{ __('messages.No preview available') }}" accept="image/*" required>
                                @error('receipt_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="receipt-preview-{{ $order->id }}" class="order-detail-payment-preview mt-3">
                                    <span>@lang('messages.Receipt Image')</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('messages.Close')</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-upload" aria-hidden="true"></i>
                            @lang('messages.Send')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
