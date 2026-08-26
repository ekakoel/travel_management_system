@php
    $invoiceActionVariant = $variant ?? 'modern';
    $canShowInvoiceActions = isset($invoice, $order) && $invoice && in_array($order->status, ['Approved', 'Paid'], true);
    $financialFiles = app(\App\Services\AccommodationFinancialFileService::class);
    $isProtectedPublicInvoice = isset($order) && $financialFiles->isProtectedPublicOrder($order);
    $invoicePreviewRoute = $isProtectedPublicInvoice
        ? route($financialFiles->customerInvoicePreviewRouteName($order), ['order' => $order->id, 'locale' => 'en'])
        : route('orders.invoice.preview', ['id' => $order->id]);
    $invoiceDownloadRoute = $isProtectedPublicInvoice
        ? route($financialFiles->customerInvoiceDownloadRouteName($order), ['order' => $order->id, 'locale' => 'en'])
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
                    <a href="{{ $invoicePreviewRoute }}" target="_blank" rel="noopener" class="ui-btn ui-btn--secondary ui-btn--block" data-invoice-preview-trigger data-invoice-preview-target="#{{ $invoicePreviewModalId }}">
                        <i class="fa-solid fa-file-invoice" aria-hidden="true"></i>
                        @lang('messages.Preview Invoice')
                    </a>
                @else
                    <a href="{{ $invoicePreviewRoute }}" target="_blank" rel="noopener" class="ui-btn ui-btn--secondary ui-btn--block">
                        <i class="fa-solid fa-file-invoice" aria-hidden="true"></i>
                        @lang('messages.Preview Invoice')
                    </a>
                @endif
                <a href="{{ $invoiceDownloadRoute }}" class="ui-btn ui-btn--secondary ui-btn--block">
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
                <a href="{{ $invoicePreviewRoute }}" target="_blank" rel="noopener" class="btn btn-outline-primary" data-invoice-preview-trigger data-invoice-preview-target="#{{ $invoicePreviewModalId }}">
                    <i class="fa fa-eye" aria-hidden="true"></i> @lang('messages.Preview Invoice')
                </a>
                <a href="{{ $invoiceDownloadRoute }}" class="btn btn-primary">
                    <i class="fa fa-download" aria-hidden="true"></i> @lang('messages.Download Invoice')
                </a>
            </div>
    @endif

    @include('frontend.home.orders.details.partials.invoice-preview-modal-compact', ['invoicePreviewModalId' => $invoicePreviewModalId])
@endif
