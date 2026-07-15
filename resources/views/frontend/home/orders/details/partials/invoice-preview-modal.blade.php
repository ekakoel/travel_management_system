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
        : 'Travel Services';
    $reservationReference = $reservation->rsv_no ?? optional(optional($order->reservations)->invoice?->reservations)->rsv_no ?? '-';
    $invoiceCurrencyCode = $invoiceCurrencyCode ?? (optional($invoice?->currency)->name ?: 'USD');
    $invoiceGrandTotal = $invoiceGrandTotal ?? match ($invoiceCurrencyCode) {
        'CNY' => '¥ ' . number_format((float) $invoice?->total_cny, 0),
        'TWD' => 'NT$ ' . number_format((float) $invoice?->total_twd, 0),
        'IDR' => 'Rp ' . number_format((float) $invoice?->total_idr, 0),
        default => currencyFormatUsd($invoice?->total_usd ?: $order->final_price),
    };
    $paymentStateLabel = $paymentStateLabel ?? ((__('messages.' . $order->status) !== 'messages.' . $order->status) ? __('messages.' . $order->status) : $order->status);
    $paymentExpired = $paymentExpired ?? ($order->status === 'Canceled');
    $travelDateLabel = $order->travel_date
        ? dateTimeFormat($order->travel_date)
        : ($order->checkin ? dateFormat($order->checkin) : '-');
    $guestCountLabel = ($order->number_of_guests ?: 0) . ' pax';
    $invoiceReferenceItems = $invoiceReferenceItems ?? [
        ['label' => 'Invoice Reference', 'value' => $invoice->inv_no ?? '-'],
        ['label' => 'Order Reference', 'value' => $order->orderno ?: '-'],
        ['label' => 'Reservation Reference', 'value' => $reservationReference],
        ['label' => 'Service', 'value' => $packageName ?: $serviceLabel ?: '-'],
    ];
    $paymentInstructionItems = $paymentInstructionItems ?? array_values(array_filter([
        optional($invoice->bank)->bank ? 'Bank: ' . $invoice->bank->bank : null,
        optional($invoice->bank)->account_name ? 'Account Name: ' . $invoice->bank->account_name : null,
        optional($invoice->bank)->account_number ? 'Account Number: ' . $invoice->bank->account_number : null,
        optional($invoice->bank)->swift_code ? 'SWIFT Code: ' . $invoice->bank->swift_code : null,
        optional($invoice->bank)->bank_code ? 'Bank Code: ' . $invoice->bank->bank_code : null,
        optional($invoice->bank)->address ? 'Bank Address: ' . $invoice->bank->address : null,
    ]));
    $invoiceNotes = $invoiceNotes ?? array_values(array_filter([
        $paymentDeadlineAt ? 'Payment is due no later than ' . dateTimeFormat($paymentDeadlineAt) . '.' : null,
        'Please include your invoice number as the transfer reference whenever possible.',
        'After completing payment, upload the payment proof through this order page for verification.',
        $paymentExpired
            ? 'This invoice has expired and is no longer valid for payment.'
            : 'This booking will be canceled automatically if no payment confirmation is submitted within 2 x 24 hours after approval.',
    ]));
@endphp

@if ($invoice && $order->status === 'Approved')
    <div class="modal fade" id="{{ $invoicePreviewModalId }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content order-detail-modal order-detail-modal--invoice">
                <div class="order-detail-modal__header">
                    <div>
                        <h3>Invoice Preview</h3>
                        <p class="order-detail-modal__subtext">{{ $invoice->inv_no }}</p>
                    </div>
                    <button type="button" class="order-detail-modal__close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="@lang('messages.Close')">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="order-detail-modal__body order-detail-modal__body--invoice">
                    <div class="order-invoice-sheet">
                        <div class="order-invoice-sheet__header">
                            <div>
                                <div class="order-invoice-sheet__eyebrow">Invoice</div>
                                <h4>{{ $businessName }}</h4>
                                <p>{{ $businessCaption }}</p>
                            </div>
                            <div class="order-invoice-sheet__meta">
                                <div>
                                    <span>Invoice No</span>
                                    <strong>{{ $invoice->inv_no }}</strong>
                                </div>
                                <div>
                                    <span>Issue Date</span>
                                    <strong>{{ $invoiceIssueAt ? dateTimeFormat($invoiceIssueAt) : '-' }}</strong>
                                </div>
                                <div>
                                    <span>Payment Deadline</span>
                                    <strong>{{ $paymentDeadlineAt ? dateTimeFormat($paymentDeadlineAt) : '-' }}</strong>
                                </div>
                                <div>
                                    <span>Status</span>
                                    <strong>{{ $paymentStateLabel }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="order-invoice-sheet__grid">
                            <section class="order-invoice-sheet__panel">
                                <span class="order-invoice-sheet__label">Billed To</span>
                                <strong>{{ Auth::user()->name ?: $order->name ?: '-' }}</strong>
                                <p>{{ Auth::user()->email ?: $order->email ?: '-' }}</p>
                                <p>{{ Auth::user()->office ?: '-' }}</p>
                                <p>{{ Auth::user()->country ?: '-' }}</p>
                            </section>

                            <section class="order-invoice-sheet__panel">
                                <span class="order-invoice-sheet__label">Payment To</span>
                                <strong>{{ optional($invoice->bank)->bank ?: $businessName }}</strong>
                                <p>{{ optional($invoice->bank)->account_name ?: '-' }}</p>
                                <p>{{ optional($invoice->bank)->account_number ?: '-' }}</p>
                                <p>{{ optional($invoice->bank)->swift_code ? 'SWIFT: ' . $invoice->bank->swift_code : '-' }}</p>
                            </section>
                        </div>

                        <div class="order-invoice-sheet__grid order-invoice-sheet__grid--secondary">
                            <section class="order-invoice-sheet__panel">
                                <span class="order-invoice-sheet__label">Invoice Reference</span>
                                <div class="order-invoice-reference-list">
                                    @foreach ($invoiceReferenceItems as $referenceItem)
                                        <div class="order-invoice-reference-item">
                                            <span>{{ $referenceItem['label'] }}</span>
                                            <strong>{{ $referenceItem['value'] }}</strong>
                                        </div>
                                    @endforeach
                                </div>
                            </section>

                            <section class="order-invoice-sheet__panel order-invoice-sheet__panel--due">
                                <span class="order-invoice-sheet__label">Amount Due</span>
                                <div class="order-invoice-amount-due">
                                    <strong>{{ $invoiceGrandTotal }}</strong>
                                    <p>{{ $invoiceCurrencyCode }} payable before the stated deadline.</p>
                                </div>
                            </section>
                        </div>

                        <div class="order-invoice-sheet__table-wrap">
                            <table class="order-invoice-sheet__table">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Travel Date</th>
                                        <th>Guests</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>{{ $packageName ?: __('messages.Service') }}</strong>
                                            <div class="order-invoice-sheet__table-sub">{{ $order->location ?: '-' }}</div>
                                        </td>
                                        <td>{{ $travelDateLabel }}</td>
                                        <td>{{ $guestCountLabel }}</td>
                                        <td>{{ $invoiceGrandTotal }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="order-invoice-sheet__totals">
                            <div class="order-invoice-sheet__total-row">
                                <span>Subtotal</span>
                                <strong>{{ $invoiceGrandTotal }}</strong>
                            </div>
                            <div class="order-invoice-sheet__total-row order-invoice-sheet__total-row--grand">
                                <span>Total Due</span>
                                <strong>{{ $invoiceGrandTotal }}</strong>
                            </div>
                        </div>

                        <div class="order-invoice-sheet__grid order-invoice-sheet__grid--secondary">
                            <section class="order-invoice-sheet__panel">
                                <span class="order-invoice-sheet__label">Payment Instructions</span>
                                <ol class="order-invoice-list">
                                    @forelse ($paymentInstructionItems as $instruction)
                                        <li>{{ $instruction }}</li>
                                    @empty
                                        <li>Bank payment instructions will be confirmed by our reservation team.</li>
                                    @endforelse
                                </ol>
                            </section>

                            <section class="order-invoice-sheet__panel">
                                <span class="order-invoice-sheet__label">Notes</span>
                                <ul class="order-invoice-list order-invoice-list--notes">
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
                                    <strong>{{ $paymentExpired ? 'Invoice expired' : 'Payment deadline policy' }}</strong>
                                    <p>
                                        @if ($paymentExpired)
                                            This invoice can no longer be used because the order was canceled automatically after the 48-hour deadline passed.
                                        @else
                                            Payment is required within 2 x 24 hours after approval. If no payment confirmation is submitted before the deadline, the order will be canceled automatically.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order-detail-modal__footer">
                    <a href="{{ route('orders.invoice.preview', ['id' => $order->id]) }}" target="_blank" rel="noopener" class="order-detail-btn order-detail-btn--soft order-detail-btn--auto">
                        <i class="fa-solid fa-up-right-from-square" aria-hidden="true"></i>
                        Open PDF
                    </a>
                    <a href="{{ route('orders.invoice.download', ['id' => $order->id]) }}" class="order-detail-btn order-detail-btn--primary order-detail-btn--auto">
                        <i class="fa-solid fa-download" aria-hidden="true"></i>
                        Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif
