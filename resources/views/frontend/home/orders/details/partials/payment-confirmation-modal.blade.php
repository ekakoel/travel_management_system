@php
    use Carbon\Carbon;

    $paymentModalId = $paymentModalId ?? ('payment-confirmation-' . $order->id);
    $paymentFormId = $paymentFormId ?? ('payment-confirm-' . $order->id);
    $paymentAction = $paymentAction ?? route('upload.payment-confirmation', ['id' => $order->id]);
    $paymentDeadlineAt = $paymentDeadlineAt ?? ($paymentDeadline ?? ($invoice?->due_date ? Carbon::parse($invoice->due_date) : null));
    $paymentAmountDisplay = $paymentAmountDisplay ?? (isset($invoiceGrandTotal)
        ? $invoiceGrandTotal
        : currencyFormatUsd($invoice?->total_usd ?: $order->final_price));
    $paymentCanSubmit = $paymentCanSubmit ?? data_get($paymentState ?? [], 'can_submit', $invoice && $order->status !== 'Paid');
@endphp

@if ($invoice && $paymentCanSubmit)
    <div class="modal fade" id="{{ $paymentModalId }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('messages.Payment Confirmation')</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="@lang('messages.Close')"></button>
                </div>
                <form
                    id="{{ $paymentFormId }}"
                    action="{{ $paymentAction }}"
                    method="post"
                    enctype="multipart/form-data"
                    data-payment-confirmation-form
                >
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="order-detail-grid">
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Order Number')</span>
                                        <strong>{{ $order->orderno }}</strong>
                                    </div>
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Reservation Number')</span>
                                        <strong>{{ $reservation->rsv_no ?? '-' }}</strong>
                                    </div>
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Invoice Number')</span>
                                        <strong>{{ $invoice->inv_no }}</strong>
                                    </div>
                                    <div class="order-detail-info">
                                        <span>@lang('messages.Due Date')</span>
                                        <strong>{{ $paymentDeadlineAt ? dateTimeFormat($paymentDeadlineAt) : '-' }}</strong>
                                    </div>
                                    <div class="order-detail-info order-detail-info--quote">
                                        <span>@lang('messages.Amount')</span>
                                        <strong>{{ $paymentAmountDisplay }}</strong>
                                    </div>
                                </div>

                                <div class="order-detail-alert mt-3">
                                    @lang('messages.Complete payment and upload the proof within 2 x 24 hours after approval to keep this booking active.')
                                </div>
                            </div>
                            <div class="col-md-6">
                                @include('frontend.home.orders.details.partials.payment-confirmation-fields')
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" data-bs-dismiss="modal">
                            @lang('messages.Close')
                        </button>
                        <button type="submit" class="btn btn-primary" data-processing-label="@lang('messages.Submitting...')">
                            <i class="fa-solid fa-upload" aria-hidden="true"></i>
                            @lang('messages.Send')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
