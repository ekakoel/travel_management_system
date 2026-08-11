@php
    use Carbon\Carbon;

    $invoicePreviewModalId = $invoicePreviewModalId ?? ('invoice-preview-' . $order->id);
    $invoiceIssueAt = $invoiceIssueAt ?? ($invoice?->inv_date ? Carbon::parse($invoice->inv_date) : null);
    $paymentDeadlineAt = $paymentDeadlineAt ?? ($paymentDeadline ?? ($invoice?->due_date ? Carbon::parse($invoice->due_date) : null));
    $serviceLabel = $serviceLabel ?? ((__('messages.' . $order->service) !== 'messages.' . $order->service) ? __('messages.' . $order->service) : $order->service);
    $packageName = $packageName ?? trim((string) ($order->servicename ?: $order->subservice ?: $serviceLabel));
    $businessName = $business->name ?? config('app.name', 'Bali Kami Tour');
    $businessCaption = isset($business) && isset($business->caption)
        ? __('messages.' . $business->caption)
        : __('messages.Travel Services');
    $reservationReference = $reservation->rsv_no ?? optional(optional($order->reservations)->invoice?->reservations)->rsv_no ?? '-';
    $invoiceCurrencyCode = $invoiceCurrencyCode ?? (optional($invoice?->currency)->name ?: 'USD');
    $invoiceGrandTotalDisplay = match ($invoiceCurrencyCode) {
        'CNY' => 'CNY ' . number_format((float) $invoice?->total_cny, 0),
        'TWD' => 'NT$ ' . number_format((float) $invoice?->total_twd, 0),
        'IDR' => 'Rp ' . number_format((float) $invoice?->total_idr, 0),
        default => currencyFormatUsd($invoice?->total_usd ?: $order->final_price),
    };
    $paymentStateLabel = $paymentStateLabel ?? ((__('messages.' . $order->status) !== 'messages.' . $order->status) ? __('messages.' . $order->status) : $order->status);
    $isProtectedPublicInvoice = app(\App\Services\AccommodationFinancialFileService::class)->isProtectedPublicOrder($order);
    $invoicePreviewRoute = $invoicePreviewRoute ?? ($isProtectedPublicInvoice
        ? route('orders.accommodation.invoice.preview', ['order' => $order->id, 'locale' => 'en'])
        : route('orders.invoice.preview', ['id' => $order->id]));
    $invoiceDownloadRoute = $invoiceDownloadRoute ?? ($isProtectedPublicInvoice
        ? route('orders.accommodation.invoice.download', ['order' => $order->id, 'locale' => 'en'])
        : route('orders.invoice.download', ['id' => $order->id]));
    $paymentExpired = $paymentExpired ?? ($order->status === 'Canceled');
    $travelDateLabel = $order->travel_date
        ? dateTimeFormat($order->travel_date)
        : ($order->checkin ? dateFormat($order->checkin) : '-');
    $guestCountLabel = ($order->number_of_guests ?: 0) . ' pax';
    $invoiceFactItems = $invoiceFactItems ?? [
        ['label' => __('messages.Service'), 'value' => $packageName ?: $serviceLabel ?: '-'],
        ['label' => __('messages.Travel Date'), 'value' => $travelDateLabel],
        ['label' => __('messages.Guests'), 'value' => $guestCountLabel],
        ['label' => __('messages.Reservation Ref'), 'value' => $reservationReference],
    ];
    $paymentInstructionItems = $paymentInstructionItems ?? array_values(array_filter([
        optional($invoice->bank)->bank ? __('messages.Bank') . ': ' . $invoice->bank->bank : null,
        optional($invoice->bank)->account_name ? __('messages.Account Name') . ': ' . $invoice->bank->account_name : null,
        optional($invoice->bank)->account_number ? __('messages.Account Number') . ': ' . $invoice->bank->account_number : null,
        optional($invoice->bank)->swift_code ? __('messages.SWIFT Code') . ': ' . $invoice->bank->swift_code : null,
        optional($invoice->bank)->bank_code ? __('messages.Bank Code') . ': ' . $invoice->bank->bank_code : null,
    ]));
    $invoiceNotes = $invoiceNotes ?? array_values(array_filter([
        $paymentDeadlineAt ? __('messages.Payment is due no later than :date.', ['date' => dateTimeFormat($paymentDeadlineAt)]) : null,
        __('messages.Please include your invoice number as the transfer reference whenever possible.'),
        __('messages.Upload payment proof through this order page after transfer.'),
        $paymentExpired
            ? __('messages.This invoice has expired and is no longer valid for payment.')
            : __('messages.This booking will be canceled automatically if no payment confirmation is submitted within 2 x 24 hours after approval.'),
    ]));
@endphp

@if ($invoice && $order->status === 'Approved')
    <div
        id="{{ $invoicePreviewModalId }}"
        class="order-detail-dialog"
        aria-hidden="true"
        hidden
        data-invoice-preview-modal
    >
        <div class="order-detail-dialog__backdrop" data-invoice-preview-close></div>
        <div
            class="order-detail-dialog__surface order-detail-modal order-detail-modal--invoice"
            role="dialog"
            aria-modal="true"
            aria-labelledby="{{ $invoicePreviewModalId }}-title"
        >
            <div class="order-detail-modal__header">
                <div>
                    <h3 id="{{ $invoicePreviewModalId }}-title">@lang('messages.Invoice Preview')</h3>
                    <p class="order-detail-modal__subtext">{{ $invoice->inv_no }}</p>
                </div>
                <button type="button" class="order-detail-modal__close ui-btn ui-btn--icon" data-invoice-preview-close aria-label="@lang('messages.Close')">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
            <div class="order-detail-modal__body order-detail-modal__body--invoice">
                <div class="order-invoice-sheet order-invoice-sheet--compact">
                    <div class="order-invoice-compact-hero">
                        <div>
                            <div class="order-invoice-sheet__eyebrow">@lang('messages.Invoice')</div>
                            <h4>{{ $packageName ?: $businessName }}</h4>
                            <p>{{ $invoice->inv_no }} | {{ $businessCaption }}</p>
                        </div>
                        <div class="order-invoice-compact-hero__due">
                            <span>@lang('messages.Amount Due')</span>
                            <strong>{{ $invoiceGrandTotalDisplay }}</strong>
                            <p>{{ $invoiceCurrencyCode }} @lang('messages.payable before deadline.')</p>
                        </div>
                    </div>

                    <div class="order-invoice-compact-meta">
                        <div class="order-invoice-compact-meta__item">
                            <span>@lang('messages.Issue Date')</span>
                            <strong>{{ $invoiceIssueAt ? dateTimeFormat($invoiceIssueAt) : '-' }}</strong>
                        </div>
                        <div class="order-invoice-compact-meta__item">
                            <span>@lang('messages.Due Date')</span>
                            <strong>{{ $paymentDeadlineAt ? dateTimeFormat($paymentDeadlineAt) : '-' }}</strong>
                        </div>
                        <div class="order-invoice-compact-meta__item">
                            <span>@lang('messages.Order Ref')</span>
                            <strong>{{ $order->orderno ?: '-' }}</strong>
                        </div>
                        <div class="order-invoice-compact-meta__item">
                            <span>@lang('messages.Status')</span>
                            <strong>{{ $paymentStateLabel }}</strong>
                        </div>
                    </div>

                    <div class="order-invoice-compact-facts">
                        @foreach ($invoiceFactItems as $factItem)
                            <div class="order-invoice-compact-facts__item">
                                <span>{{ $factItem['label'] }}</span>
                                <strong>{{ $factItem['value'] }}</strong>
                            </div>
                        @endforeach
                    </div>

                    <div class="order-invoice-sheet__grid order-invoice-sheet__grid--secondary">
                        <section class="order-invoice-sheet__panel">
                            <span class="order-invoice-sheet__label">@lang('messages.Billed To')</span>
                            <strong>{{ Auth::user()->name ?: $order->name ?: '-' }}</strong>
                            <p>{{ Auth::user()->email ?: $order->email ?: '-' }}</p>
                            <p>{{ Auth::user()->office ?: '-' }}</p>
                            <p>{{ Auth::user()->country ?: '-' }}</p>
                        </section>
                        <section class="order-invoice-sheet__panel">
                            <span class="order-invoice-sheet__label">@lang('messages.Payment To')</span>
                            <strong>{{ optional($invoice->bank)->bank ?: $businessName }}</strong>
                            <p>{{ optional($invoice->bank)->account_name ?: '-' }}</p>
                            <p>{{ optional($invoice->bank)->account_number ?: '-' }}</p>
                            <p>{{ optional($invoice->bank)->swift_code ? __('messages.SWIFT') . ': ' . $invoice->bank->swift_code : '-' }}</p>
                        </section>
                    </div>

                    <div class="order-invoice-sheet__grid order-invoice-sheet__grid--secondary">
                        <section class="order-invoice-sheet__panel">
                            <span class="order-invoice-sheet__label">@lang('messages.Payment Instructions')</span>
                            <ol class="order-invoice-list order-invoice-list--compact">
                                @forelse ($paymentInstructionItems as $instruction)
                                    <li>{{ $instruction }}</li>
                                @empty
                                    <li>@lang('messages.Bank payment instructions will be confirmed by our reservation team.')</li>
                                @endforelse
                            </ol>
                        </section>
                        <section class="order-invoice-sheet__panel">
                            <span class="order-invoice-sheet__label">@lang('messages.Important Notes')</span>
                            <ul class="order-invoice-list order-invoice-list--notes order-invoice-list--compact">
                                @foreach ($invoiceNotes as $note)
                                    <li>{{ $note }}</li>
                                @endforeach
                            </ul>
                        </section>
                    </div>

                    <div class="order-invoice-sheet__footer">
                        <div class="order-detail-inline-alert {{ $paymentExpired ? 'order-detail-inline-alert--danger' : 'order-detail-inline-alert--warning' }}">
                            <i class="fa-solid {{ $paymentExpired ? 'fa-circle-exclamation' : 'fa-clock' }}" aria-hidden="true"></i>
                            <div>
                                <strong>{{ $paymentExpired ? __('messages.Invoice expired') : __('messages.Payment deadline policy') }}</strong>
                                <p>
                                    @if ($paymentExpired)
                                        @lang('messages.This invoice can no longer be used because the order was canceled automatically after the 48-hour deadline passed.')
                                    @else
                                        @lang('messages.Payment is required within 2 x 24 hours after approval. If no payment confirmation is submitted before the deadline, the order will be canceled automatically.')
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="order-detail-modal__footer">
                <a href="{{ $invoicePreviewRoute }}" target="_blank" rel="noopener" class="ui-btn ui-btn--secondary">
                    <i class="fa-solid fa-up-right-from-square" aria-hidden="true"></i>
                    @lang('messages.Open PDF')
                </a>
                <a href="{{ $invoiceDownloadRoute }}" class="ui-btn ui-btn--primary">
                    <i class="fa-solid fa-download" aria-hidden="true"></i>
                    @lang('messages.Download PDF')
                </a>
            </div>
        </div>
    </div>
@endif
