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
    $dokuCanStart = data_get($paymentState ?? [], 'can_start_doku', false) && $invoice;
    $dokuActive = data_get($paymentState ?? [], 'active_doku');
    $dokuActiveLive = $dokuActive
        && $dokuActive->checkout_url
        && (!$dokuActive->expired_at || $dokuActive->expired_at->isFuture());
    $paymentCurrencyCode = optional($invoice?->currency)->name ?: 'USD';
    $paymentBalance = (float) ($invoice?->balance ?? 0);
    $paymentOutstandingDisplay = match ($paymentCurrencyCode) {
        'IDR' => currencyFormatIdr($paymentBalance),
        'CNY' => currencyFormatCny($paymentBalance),
        'TWD' => currencyFormatTwd($paymentBalance),
        default => currencyFormatUsd($paymentBalance),
    };
    $dokuFormId = 'doku-checkout-' . $order->id;
@endphp

@if ($invoice && ($paymentCanSubmit || $dokuCanStart || $dokuActiveLive))
    <div class="modal fade" id="{{ $paymentModalId }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('messages.Payment Confirmation')</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="@lang('messages.Close')"></button>
                </div>
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

                            {{-- <div class="order-detail-alert mt-3">
                                @lang('messages.Complete payment and upload the proof within 2 x 24 hours after approval to keep this booking active.')
                            </div> --}}
                        </div>
                        <div class="col-md-6">
                            @if ($paymentCanSubmit)
                                <form
                                    id="{{ $paymentFormId }}"
                                    action="{{ $paymentAction }}"
                                    method="post"
                                    enctype="multipart/form-data"
                                    data-payment-confirmation-form
                                >
                                    @csrf
                                    @include('frontend.home.orders.details.partials.payment-confirmation-fields')
                                </form>
                            @elseif ($dokuActiveLive)
                                <div class="order-detail-alert mt-3">
                                    @lang('messages.A DOKU online payment is waiting to be completed.')
                                </div>
                            @endif

                            @if ($dokuCanStart)
                                <div class="order-detail-upload mt-3">
                                    <div class="order-detail-alert">
                                        <strong>@lang('messages.Pay securely with DOKU')</strong>
                                        <p class="mb-2 mt-1">
                                            @lang('messages.Pay the full outstanding balance online. The amount is calculated from the current invoice balance.')
                                        </p>
                                        <span>@lang('messages.Outstanding balance'): {{ $paymentOutstandingDisplay }}</span>
                                    </div>
                                    <form id="{{ $dokuFormId }}" action="{{ route('orders.doku.checkout', ['order' => $order->id]) }}" method="post" data-payment-confirmation-form>
                                        @csrf
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" data-bs-dismiss="modal">
                        @lang('messages.Close')
                    </button>
                    @if ($dokuActiveLive)
                        <a href="{{ $dokuActive->checkout_url }}" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                            @lang('messages.Continue Online Payment')
                        </a>
                    @elseif ($dokuCanStart)
                        <button type="submit" form="{{ $dokuFormId }}" class="btn btn-primary w-100" data-processing-label="@lang('messages.Processing...')">
                            <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
                            @lang('messages.Pay with DOKU')
                        </button>
                    @endif
                    @if ($paymentCanSubmit)
                        <button type="submit" form="{{ $paymentFormId }}" class="btn btn-primary" data-processing-label="@lang('messages.Submitting...')">
                            <i class="fa-solid fa-upload" aria-hidden="true"></i>
                            @lang('messages.Send')
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
