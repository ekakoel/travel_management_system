@php
    $receiptCollection = collect($receipts ?? [])->filter();

    if ($receiptCollection->isEmpty() && isset($receipt) && $receipt) {
        $receiptCollection = collect([$receipt]);
    }

    $paymentToneMap = [
        'Valid' => 'active',
        'Paid' => 'active',
        'Pending' => 'pending',
        'Invalid' => 'rejected',
    ];

    $paymentStatusLabel = match (true) {
        $order->status === 'Paid' => __('messages.Paid'),
        $receiptCollection->contains(fn($item) => in_array($item->status, ['Valid', 'Paid'], true)) => __('messages.Paid'),
        $receiptCollection->contains(fn($item) => $item->status === 'Pending') => __('messages.On Review'),
        $receiptCollection->contains(fn($item) => $item->status === 'Invalid') => __('messages.Invalid'),
        default => __('messages.Awaiting Payment'),
    };

    $paymentSummaryText = match (true) {
        $order->status === 'Pending' => __('messages.We have received your order, we will contact you as soon as possible to validate the order!'),
        $invoice && $invoice->due_date => __('messages.Payment Dateline') . ' : ' . dateFormat($invoice->due_date),
        default => $paymentStatusLabel,
    };

    $backUrl = $backUrl ?? route('view.orders');
    $backLabel = $backLabel ?? __('messages.Back');
@endphp

<link rel="stylesheet" href="{{ mix('build/frontend/css/pages/order-detail-entry.css') }}">
<style>
    .legacy-order-sidebar .order-detail-sidebar-card {
        border: 1px solid #e2ebf5;
        border-radius: 24px;
        background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.08);
    }
    .legacy-order-sidebar .order-detail-action-list { margin-top: 1rem; }
</style>

@if ($order->status == 'Approved' || $order->status == 'Paid' || $invoice || $receiptCollection->isNotEmpty())
    <div class="legacy-order-sidebar">
    <div class="card-box order-detail-sidebar-card">
        <h2 class="order-detail-sidebar-card__title">@lang('messages.Payment Summary')</h2>
        <p class="order-detail-sidebar-card__text">
            @if ($invoice)
                @lang('messages.Invoice Number') {{ $invoice->inv_no }}
                @if ($invoice->due_date)
                    . {{ $paymentSummaryText }}
                @endif
            @else
                {{ $paymentSummaryText }}
            @endif
        </p>

        @if ($invoice)
            <div class="order-detail-summary-chip-list">
                <div class="order-detail-summary-chip">
                    <span>@lang('messages.Invoice Number')</span>
                    <strong>{{ $invoice->inv_no }}</strong>
                </div>
                <div class="order-detail-summary-chip">
                    <span>@lang('messages.Status')</span>
                    <strong>{{ $paymentStatusLabel }}</strong>
                </div>
                @if ($invoice->due_date)
                    <div class="order-detail-summary-chip">
                        <span>@lang('messages.Due Date')</span>
                        <strong>{{ dateFormat($invoice->due_date) }}</strong>
                    </div>
                @endif
            </div>
        @endif

        @if ($receiptCollection->isNotEmpty())
            <div class="order-detail-receipt-list">
                @foreach ($receiptCollection as $receiptItem)
                    @php
                        $receiptTone = $paymentToneMap[$receiptItem->status] ?? 'default';
                        $receiptDate = $receiptItem->payment_date ?: $receiptItem->created_at;
                        $receiptStatusLabel = __('messages.' . $receiptItem->status) !== 'messages.' . $receiptItem->status
                            ? __('messages.' . $receiptItem->status)
                            : $receiptItem->status;
                    @endphp
                    <div class="order-detail-receipt">
                        <div class="order-detail-receipt__icon">
                            <i class="fa-solid fa-receipt" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="order-detail-receipt__title">{{ $receiptStatusLabel }}</p>
                            <p class="order-detail-receipt__meta">{{ $receiptDate ? dateFormat($receiptDate) : '-' }}</p>
                        </div>
                        @if ($receiptItem->receipt_img)
                            <button type="button" class="order-detail-badge order-detail-badge--{{ $receiptTone }}" data-toggle="modal" data-target="#{{ $device ?? 'legacy' }}-payment-receipt-{{ $receiptItem->id }}" data-bs-toggle="modal" data-bs-target="#{{ $device ?? 'legacy' }}-payment-receipt-{{ $receiptItem->id }}">
                                @lang('messages.View')
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif ($invoice)
            <div class="order-detail-alert mt-3">
                @lang('messages.Waiting Payment')
            </div>
        @endif

        <div class="order-detail-action-list">
            @include('frontend.home.orders.details.partials.invoice-action-buttons', ['variant' => 'legacy'])

            @if ($invoice && $order->status !== 'Paid')
                <button type="button" class="order-detail-btn order-detail-btn--primary" data-toggle="modal" data-target="#payment-confirmation-{{ $order->id }}" data-bs-toggle="modal" data-bs-target="#payment-confirmation-{{ $order->id }}">
                    <i class="fa-solid fa-upload" aria-hidden="true"></i>
                    @lang('messages.Payment Confirmation')
                </button>
            @endif

            <a href="{{ $backUrl }}" class="order-detail-btn order-detail-btn--soft">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                {{ $backLabel }}
            </a>
        </div>
    </div>
    </div>
@endif
