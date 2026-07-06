<aside class="order-detail-sidebar">
    @if (count($attentions) > 0)
        <div class="order-detail-sidebar-card">
            <h2 class="order-detail-sidebar-card__title">@lang('messages.Attention')</h2>
            <div class="order-detail-receipt-list">
                @foreach ($attentions as $attention)
                    <div class="order-detail-alert">{!! $attention->attention !!}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="order-detail-sidebar-card">
        <h2 class="order-detail-sidebar-card__title">@lang('messages.Payment Status')</h2>
        <p class="order-detail-sidebar-card__text">
            @if ($order->status === 'Pending')
                @lang('messages.We have received your order, we will contact you as soon as possible to validate the order!')
            @elseif ($order->status === 'Paid')
                @lang('messages.Paid')
            @elseif ($invoice)
                @lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}
            @else
                {{ $statusLabel }}
            @endif
        </p>

        @if ($invoice)
            <div class="order-detail-grid mt-3">
                <div class="order-detail-info">
                    <span>@lang('messages.Invoice No')</span>
                    <strong>{{ $invoice->inv_no }}</strong>
                </div>
                @if ($reservation)
                    <div class="order-detail-info">
                        <span>@lang('messages.Reservation Number')</span>
                        <strong>{{ $reservation->rsv_no }}</strong>
                    </div>
                @endif
            </div>
        @endif

        @if ($receiptCollection->isNotEmpty())
            <div class="order-detail-receipt-list">
                @foreach ($receiptCollection as $receipt)
                    <div class="order-detail-receipt">
                        <div class="order-detail-receipt__icon">
                            <i class="fa-solid {{ $receipt->status === 'Valid' ? 'fa-check' : ($receipt->status === 'Invalid' ? 'fa-triangle-exclamation' : 'fa-clock') }}" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="order-detail-receipt__title">@lang('messages.' . $receipt->status)</p>
                            <p class="order-detail-receipt__meta">{{ $receipt->payment_date ? dateFormat($receipt->payment_date) : dateFormat($receipt->created_at) }}</p>
                        </div>
                        @if ($receipt->receipt_img)
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#receipt-modal-{{ $receipt->id }}">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="order-detail-action-list">
            @if ($invoice && $order->status !== 'Paid')
                @uiEnabled('doku-payment')
                    @if ($order->status === 'Approved' && $doku_payment && $doku_payment->expired_date > $now)
                        <button type="button" class="order-detail-btn order-detail-btn--doku" data-doku-checkout-url="{{ $doku_payment->checkout_url }}">
                            <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
                            Pay with DOKU
                        </button>
                    @endif
                @endUiEnabled
                <button type="button" class="order-detail-btn order-detail-btn--primary" data-bs-toggle="modal" data-bs-target="#payment-confirmation-{{ $order->id }}">
                    <i class="fa-solid fa-upload" aria-hidden="true"></i>
                    @lang('messages.Payment Confirmation')
                </button>
            @endif
            <a href="{{ route('view.orders') }}#orderHotel" class="order-detail-btn order-detail-btn--soft">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                @lang('messages.Back')
            </a>
        </div>
    </div>
</aside>
