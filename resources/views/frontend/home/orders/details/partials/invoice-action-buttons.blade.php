@php
    $invoiceActionVariant = $variant ?? 'modern';
    $canShowInvoiceActions = isset($invoice, $order) && $invoice && $order->status === 'Approved';
    $isProtectedPublicInvoice = isset($order) && app(\App\Services\AccommodationFinancialFileService::class)->isProtectedPublicOrder($order);
    $invoicePreviewRoute = $isProtectedPublicInvoice
        ? route('orders.accommodation.invoice.preview', ['order' => $order->id, 'locale' => 'en'])
        : route('orders.invoice.preview', ['id' => $order->id]);
    $invoiceDownloadRoute = $isProtectedPublicInvoice
        ? route('orders.accommodation.invoice.download', ['order' => $order->id, 'locale' => 'en'])
        : route('orders.invoice.download', ['id' => $order->id]);
@endphp

@if ($canShowInvoiceActions)
    @if ($invoiceActionVariant === 'modern')
            @php
                $invoicePreviewModalId = $invoicePreviewModalId ?? ('invoice-preview-' . $order->id);
            @endphp
            <div class="order-detail-invoice-actions__label">@lang('messages.Invoice')</div>
            <div class="order-detail-action-list order-detail-action-list--tight">
                @if ($invoicePreviewModalId)
                    <button type="button" class="order-detail-btn order-detail-btn--soft" data-invoice-preview-trigger data-invoice-preview-target="#{{ $invoicePreviewModalId }}">
                        <i class="fa-solid fa-file-invoice" aria-hidden="true"></i>
                        @lang('messages.Preview Invoice')
                    </button>
                @else
                    <a href="{{ $invoicePreviewRoute }}" target="_blank" rel="noopener" class="order-detail-btn order-detail-btn--soft">
                        <i class="fa-solid fa-file-invoice" aria-hidden="true"></i>
                        @lang('messages.Preview Invoice')
                    </a>
                @endif
                <a href="{{ $invoiceDownloadRoute }}" class="order-detail-btn order-detail-btn--primary">
                    <i class="fa-solid fa-download" aria-hidden="true"></i>
                    @lang('messages.Download PDF')
                </a>
            </div>
    @else
            @php
                $invoicePreviewModalId = $invoicePreviewModalId ?? ('invoice-preview-' . $order->id);
            @endphp
            <div class="order-invoice-action-panel__title">
                <i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i>
                @lang('messages.Invoice')
            </div>
            <div class="order-invoice-action-panel__buttons">
                <button type="button" class="btn btn-outline-primary" data-invoice-preview-trigger data-invoice-preview-target="#{{ $invoicePreviewModalId }}">
                    <i class="fa fa-eye" aria-hidden="true"></i> @lang('messages.Preview Invoice')
                </button>
                <a href="{{ $invoiceDownloadRoute }}" class="btn btn-primary">
                    <i class="fa fa-download" aria-hidden="true"></i> @lang('messages.Download Invoice')
                </a>
            </div>
    @endif

    @include('frontend.home.orders.details.partials.invoice-preview-modal-compact', ['invoicePreviewModalId' => $invoicePreviewModalId])
@endif
