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
                        <img class="order-detail-receipt-image" src="{{ route('orders.accommodation.payments.receipt', ['order' => $order->id, 'payment' => $receipt->id]) }}" alt="@lang('messages.Payment Receipt')">
                        @if ($receipt->note)
                            <div class="order-detail-alert mt-3">{{ $receipt->note }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@include('frontend.home.orders.details.partials.payment-confirmation-modal', [
    'paymentCanSubmit' => data_get($paymentState ?? [], 'can_submit', $invoice && $order->status !== 'Paid'),
])
