@extends('frontend.layouts.app')

@section('title', __('messages.Order Details'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/order-detail-entry.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/frontend/js/pages/order-detail.js') }}" defer></script>
@endpush

@php
    use Carbon\Carbon;

    $statusToneMap = [
        'Pending' => 'pending',
        'Approved' => 'approved',
        'Confirmed' => 'active',
        'Active' => 'active',
        'Paid' => 'paid',
        'Rejected' => 'rejected',
        'Invalid' => 'invalid',
        'Canceled' => 'canceled',
        'Draft' => 'default',
    ];
    $translateMessage = function ($value) {
        $value = trim((string) $value);

        if ($value === '') {
            return '-';
        }

        $translated = __('messages.' . $value);

        return $translated !== 'messages.' . $value ? $translated : $value;
    };
    $statusTone = $statusToneMap[$order->status] ?? 'default';
    $statusLabel = $translateMessage($order->status);
    $serviceLabel = $translateMessage($order->service);
    $transportName = trim(collect([
        $transport->brand ?? null,
        $transport->name ?? null,
    ])->filter()->implode(' '));
    $transportName = $transportName !== '' ? $transportName : ($order->servicename ?: $order->subservice ?: $serviceLabel);
    $transportType = trim((string) ($order->service_type ?: $order->subservice ?: '-'));
    $transportTypeLabel = $translateMessage($transportType);
    $serviceDateLabel = $order->pickup_date ? dateTimeFormat($order->pickup_date) : ($order->checkin ? dateTimeFormat($order->checkin) : '-');
    $completionDateLabel = $order->dropoff_date ? dateTimeFormat($order->dropoff_date) : ($order->checkout ? dateTimeFormat($order->checkout) : '-');
    $guestRows = $order->relationLoaded('guests') ? $order->guests : collect();
    $fallbackGuestDetail = trim((string) $order->guest_detail);
    $estimatedDurationLabel = $price
        ? (($price->duration ?: 0) . ' ' . __('messages.hours'))
        : '-';
    $orderedDurationLabel = $order->duration
        ? ($transportType === 'Daily Rent'
            ? trans_choice('messages.:count rental', (int) $order->duration, ['count' => (int) $order->duration])
            : $order->duration . ' ' . __('messages.hours'))
        : '-';
    $routeLabel = trim(collect([
        $order->src ?: null,
        $order->dst ?: null,
    ])->filter()->implode(' - '));
    $routeLabel = $routeLabel !== '' ? $routeLabel : '-';
    $flightType = $order->arrival_flight ? __('messages.Arrival') : ($order->departure_flight ? __('messages.Departure') : '-');
    $flightNumber = $order->arrival_flight ?: ($order->departure_flight ?: '-');
    $flightDate = $order->arrival_time ?: ($order->departure_time ?: '-');
    $flightDateLabel = $order->arrival_flight ? __('messages.Arrival Date and Time') : __('messages.Departure Date and Time');
    $orderNotice = match ($order->status) {
        'Pending' => __('messages.We have received your order, we will contact you as soon as possible to validate the order!'),
        'Approved' => __('messages.Payment is required within 2 x 24 hours after approval. If no payment confirmation is submitted before the deadline, the order will be canceled automatically.'),
        'Paid' => __('messages.Paid'),
        'Canceled' => __('messages.This booking was automatically canceled because no payment confirmation was submitted before the deadline.'),
        default => __('messages.Use this page as your booking reference and continue payment only when needed.'),
    };
    $paxLabel = __('messages.pax');
    $summaryCards = [
        ['label' => __('messages.Service'), 'value' => $serviceLabel],
        ['label' => __('messages.Service Date'), 'value' => $serviceDateLabel],
        ['label' => __('messages.Number of Guests'), 'value' => ($order->number_of_guests ?: 0) . ' ' . $paxLabel],
        ['label' => __('messages.Total Price'), 'value' => currencyFormatUsd($order->final_price)],
    ];
    $bookingInfo = [
        ['label' => __('messages.Order No'), 'value' => $order->orderno],
        ['label' => __('messages.Order Date'), 'value' => dateTimeFormat($order->created_at)],
        ['label' => __('messages.Transport'), 'value' => $transportName],
        ['label' => __('messages.Type'), 'value' => $transportTypeLabel],
        ['label' => __('messages.Capacity'), 'value' => ($order->capacity ?: 0) . ' ' . $paxLabel],
        ['label' => __('messages.Location'), 'value' => $routeLabel],
    ];
    $tripInfo = [
        ['label' => __('messages.Service Date'), 'value' => $serviceDateLabel],
        ['label' => __('messages.Dropoff Date'), 'value' => $completionDateLabel],
        ['label' => __('messages.Duration'), 'value' => $orderedDurationLabel],
        ['label' => __('messages.Estimated travel duration'), 'value' => $transportType === 'Daily Rent' ? $estimatedDurationLabel . ' / ' . __('messages.rental') : $estimatedDurationLabel],
        ['label' => __('messages.Pick up location'), 'value' => $order->pickup_location ?: '-'],
        ['label' => __('messages.Drop off location'), 'value' => $order->dropoff_location ?: '-'],
    ];
    $flightInfo = [
        ['label' => __('messages.Flight Type'), 'value' => $flightType],
        ['label' => __('messages.Flight Number'), 'value' => $flightNumber],
        ['label' => $flightDateLabel, 'value' => $flightDate],
    ];
    $snapshotSections = [
        ['title' => __('messages.Include'), 'content' => trim((string) ($order->include ?: ($transport->include ?? '')))],
        ['title' => __('messages.Additional Information'), 'content' => trim((string) ($order->additional_info ?: ($transport->additional_info ?? '')))],
        ['title' => __('messages.Cancelation Policy'), 'content' => trim((string) ($order->cancellation_policy ?: ($transport->cancellation_policy ?? '')))],
    ];
    $invoicePreviewModalId = $invoice ? 'invoice-preview-' . $order->id : null;
    $invoiceIssueAt = $invoice?->inv_date ? Carbon::parse($invoice->inv_date) : null;
    $paymentDeadlineAt = $paymentDeadline ?? ($invoice?->due_date ? Carbon::parse($invoice->due_date) : null);
    $latestReceipt = ($receipts && count($receipts) > 0) ? collect($receipts)->sortByDesc('created_at')->first() : null;
    $paymentExpired = $order->status === 'Canceled'
        || ($order->status === 'Approved' && $paymentDeadlineAt && $paymentDeadlineAt->isPast() && !$paymentSubmissionExists);
    $canSubmitPayment = $invoice
        && $order->status === 'Approved'
        && !$paymentExpired
        && (!$latestReceipt || $latestReceipt->status === 'Invalid');
    $invoiceCurrencyCode = optional($invoice?->currency)->name ?: 'USD';
    $invoiceGrandTotal = match ($invoiceCurrencyCode) {
        'CNY' => 'CNY ' . number_format((float) $invoice?->total_cny, 0),
        'TWD' => 'NT$ ' . number_format((float) $invoice?->total_twd, 0),
        'IDR' => 'Rp ' . number_format((float) $invoice?->total_idr, 0),
        default => currencyFormatUsd($invoice?->total_usd ?: $order->final_price),
    };
    $paymentStateLabel = match (true) {
        $order->status === 'Paid' => __('messages.Paid'),
        $latestReceipt && $latestReceipt->status === 'Pending' => __('messages.On Review'),
        $latestReceipt && in_array($latestReceipt->status, ['Valid', 'Paid'], true) => __('messages.Paid'),
        $latestReceipt && $latestReceipt->status === 'Invalid' => __('messages.Invalid'),
        $paymentExpired => __('messages.Canceled'),
        default => __('messages.Awaiting Payment'),
    };
    $priceRows = [
        ['label' => __('messages.Selected rate'), 'value' => currencyFormatUsd($order->price_total ?: $order->normal_price)],
    ];
    if (!empty($filteredDiscounts ?? [])) {
        foreach ($filteredDiscounts as $discountLabel => $discountValue) {
            $priceRows[] = [
                'label' => $discountLabel,
                'value' => currencyFormatUsd($discountValue),
                'discount' => true,
            ];
        }
    }
@endphp

@section('content')
    <div
        class="order-detail-page"
        data-countdown-expired="@lang('messages.Payment window expired')"
        data-countdown-remaining-template="@lang('messages.:days d :hours h :minutes m remaining')"
        data-receipt-preview-alt="@lang('messages.Payment Receipt')"
    >
        <header class="order-detail-hero">
            <div class="container">
                @include('partials.alerts')

                <div class="order-detail-hero__content">
                    <div>
                        @include('partials.breadcrumbs', [
                            'breadcrumbs' => [
                                ['url' => route('home'), 'label' => __('messages.Home')],
                                ['url' => route('view.orders'), 'label' => __('messages.Orders')],
                                ['label' => $order->orderno],
                            ],
                            'variant' => 'dark',
                        ])
                        <div class="order-detail-eyebrow">
                            <i class="fa-solid fa-van-shuttle" aria-hidden="true"></i>
                            @lang('messages.Order Details')
                        </div>
                        <h1 class="order-detail-title">{{ $transportName }}</h1>
                        <p class="order-detail-text">
                            {{ $transportTypeLabel }}. {{ strip_tags((string) $orderNotice) }}
                        </p>
                    </div>

                    <div class="order-detail-status order-detail-status--{{ $statusTone }}">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        {{ $statusLabel }}
                    </div>
                </div>

                <div class="order-detail-summary">
                    @foreach ($summaryCards as $summaryCard)
                        <div class="order-detail-metric">
                            <span>{{ $summaryCard['label'] }}</span>
                            <strong>{{ $summaryCard['value'] ?: '-' }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </header>

        <main class="order-detail-main">
            <div class="container">
                <div class="order-detail-layout">
                    <div class="order-detail-stack">
                        <section class="order-detail-section">
                            <div class="order-detail-section__header">
                                <div>
                                    <div class="order-detail-eyebrow">@lang('messages.Order')</div>
                                    <h2 class="order-detail-section__title">@lang('messages.Booking Details')</h2>
                                </div>
                            </div>
                            <div class="order-detail-section__body">
                                <div class="order-detail-grid">
                                    @foreach ($bookingInfo as $item)
                                        <div class="order-detail-info">
                                            <span>{{ $item['label'] }}</span>
                                            <strong>{{ $item['value'] ?: '-' }}</strong>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="order-detail-grid mt-3">
                                    @foreach ($tripInfo as $item)
                                        <div class="order-detail-info">
                                            <span>{{ $item['label'] }}</span>
                                            <strong>{{ $item['value'] ?: '-' }}</strong>
                                        </div>
                                    @endforeach
                                </div>

                                @if ($transportType === 'Airport Shuttle')
                                    <div class="order-detail-grid mt-3">
                                        @foreach ($flightInfo as $item)
                                            <div class="order-detail-info">
                                                <span>{{ $item['label'] }}</span>
                                                <strong>{{ $item['value'] ?: '-' }}</strong>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if (trim((string) $order->note) !== '')
                                    <div class="order-detail-note mt-3">
                                        <strong>@lang('messages.Note')</strong>
                                        <div class="order-detail-rich mt-2">{!! nl2br(e(trim((string) $order->note))) !!}</div>
                                    </div>
                                @endif
                            </div>
                        </section>

                        <section class="order-detail-section">
                            <div class="order-detail-section__header">
                                <div>
                                    <div class="order-detail-eyebrow">@lang('messages.Guest Details')</div>
                                    <h2 class="order-detail-section__title">@lang('messages.Guest Details')</h2>
                                </div>
                            </div>
                            <div class="order-detail-section__body">
                                @if ($guestRows->isNotEmpty())
                                    <div class="order-detail-table-wrap">
                                        <table class="order-detail-table order-detail-table--compact">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.No')</th>
                                                    <th>@lang('messages.Name')</th>
                                                    <th>@lang('messages.Age')</th>
                                                    <th>@lang('messages.Gender')</th>
                                                    <th>@lang('messages.Phone')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($guestRows as $index => $guest)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $guest->name ?: '-' }}</td>
                                                        <td>{{ $guest->age ? $translateMessage($guest->age) : '-' }}</td>
                                                        <td>{{ $guest->sex ? $translateMessage($guest->sex) : '-' }}</td>
                                                        <td>{{ $guest->phone ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @elseif ($fallbackGuestDetail !== '')
                                    <div class="order-detail-note">
                                        <strong>@lang('messages.Guest Details')</strong>
                                        <div class="order-detail-rich mt-2">{!! $fallbackGuestDetail !!}</div>
                                    </div>
                                @else
                                    <div class="order-detail-empty">
                                        <i class="fa-solid fa-users" aria-hidden="true"></i>
                                        <p>@lang('tour-detail.no_guest_added')</p>
                                    </div>
                                @endif
                            </div>
                        </section>

                        <section class="order-detail-section">
                            <div class="order-detail-section__header">
                                <div>
                                    <div class="order-detail-eyebrow">@lang('messages.Transport')</div>
                                    <h2 class="order-detail-section__title">@lang('messages.Transport')</h2>
                                </div>
                            </div>
                            <div class="order-detail-section__body">
                                @foreach ($snapshotSections as $section)
                                    @if ($section['content'] !== '')
                                        <div class="order-detail-content-block">
                                            <div class="order-detail-content-block__title">{{ $section['title'] }}</div>
                                            <div class="order-detail-rich">{!! $section['content'] !!}</div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </section>

                        <section class="order-detail-section">
                            <div class="order-detail-section__header">
                                <div>
                                    <div class="order-detail-eyebrow">@lang('messages.Price')</div>
                                    <h2 class="order-detail-section__title">@lang('messages.Payment Summary')</h2>
                                </div>
                            </div>
                            <div class="order-detail-section__body">
                                <div class="order-detail-price-list">
                                    @foreach ($priceRows as $priceRow)
                                        <div class="order-detail-price-row {{ !empty($priceRow['discount']) ? 'order-detail-price-row--discount' : '' }}">
                                            <span>{{ $priceRow['label'] }}</span>
                                            <strong>{{ $priceRow['value'] }}</strong>
                                        </div>
                                    @endforeach
                                    <div class="order-detail-price-row order-detail-price-row--grand">
                                        <span>@lang('messages.Grand Total')</span>
                                        <strong>{{ currencyFormatUsd($order->final_price) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside class="order-detail-sidebar">
                        <div class="order-detail-sidebar-flow">
                            <div class="order-detail-sidebar-card">
                                <h2 class="order-detail-sidebar-card__title">@lang('messages.Order Summary')</h2>
                                <p class="order-detail-sidebar-card__text">{!! $orderNotice !!}</p>

                                <div class="order-detail-summary-chip-list">
                                    <div class="order-detail-summary-chip">
                                        <span>@lang('messages.Order No')</span>
                                        <strong>{{ $order->orderno }}</strong>
                                    </div>
                                    <div class="order-detail-summary-chip">
                                        <span>@lang('messages.Status')</span>
                                        <strong>{{ $statusLabel }}</strong>
                                    </div>
                                    <div class="order-detail-summary-chip">
                                        <span>@lang('messages.Service Date')</span>
                                        <strong>{{ $serviceDateLabel }}</strong>
                                    </div>
                                </div>
                            </div>

                            @if ($invoice)
                                <div class="order-detail-sidebar-card">
                                    <h2 class="order-detail-sidebar-card__title">@lang('messages.Payment Summary')</h2>
                                    <p class="order-detail-sidebar-card__text">
                                        @lang('messages.Invoice Number') {{ $invoice->inv_no }}
                                        @if ($paymentDeadlineAt)
                                            . @lang('messages.Payment Dateline') {{ dateTimeFormat($paymentDeadlineAt) }}
                                        @endif
                                    </p>

                                    <div class="order-detail-summary-chip-list">
                                        <div class="order-detail-summary-chip">
                                            <span>@lang('messages.Amount')</span>
                                            <strong>{{ $invoiceGrandTotal }}</strong>
                                        </div>
                                        @if ($invoiceIssueAt)
                                            <div class="order-detail-summary-chip">
                                                <span>@lang('messages.Issued')</span>
                                                <strong>{{ dateTimeFormat($invoiceIssueAt) }}</strong>
                                            </div>
                                        @endif
                                        @if ($paymentDeadlineAt)
                                            <div class="order-detail-summary-chip">
                                                <span>@lang('messages.Due Date')</span>
                                                <strong>{{ dateTimeFormat($paymentDeadlineAt) }}</strong>
                                            </div>
                                        @endif
                                        <div class="order-detail-summary-chip">
                                            <span>@lang('messages.Status')</span>
                                            <strong>{{ $paymentStateLabel }}</strong>
                                        </div>
                                    </div>

                                    @if ($order->status === 'Approved' && $paymentDeadlineAt)
                                        <div
                                            class="order-detail-payment-deadline {{ $paymentExpired ? 'order-detail-payment-deadline--expired' : '' }}"
                                            @if (!$paymentExpired)
                                                data-payment-countdown="{{ $paymentDeadlineAt->toIso8601String() }}"
                                            @endif
                                        >
                                            <div class="order-detail-payment-deadline__icon">
                                                <i class="fa-solid {{ $paymentExpired ? 'fa-ban' : 'fa-hourglass-half' }}" aria-hidden="true"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $paymentExpired ? __('messages.Payment window expired') : __('messages.Payment required within 2 x 24 hours') }}</strong>
                                                <p>
                                                    @if ($paymentExpired)
                                                        @lang('messages.This booking was automatically canceled because no payment confirmation was submitted before the deadline.')
                                                    @else
                                                        @lang('messages.Complete payment and upload proof before') <strong>{{ dateTimeFormat($paymentDeadlineAt) }}</strong>
                                                    @endif
                                                </p>
                                                @if (!$paymentExpired)
                                                    <div class="order-detail-countdown" data-payment-countdown-output>
                                                        @lang('messages.Calculating remaining time...')
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    @if ($receipts && count($receipts) > 0)
                                        <div class="order-detail-receipt-list">
                                            @foreach ($receipts as $receipt)
                                                @php
                                                    $receiptTone = match ($receipt->status) {
                                                        'Valid', 'Paid' => 'active',
                                                        'Pending' => 'pending',
                                                        'Invalid' => 'rejected',
                                                        default => 'default',
                                                    };
                                                @endphp
                                                <div class="order-detail-receipt">
                                                    <div class="order-detail-receipt__icon">
                                                        <i class="fa-solid fa-receipt" aria-hidden="true"></i>
                                                    </div>
                                                    <div>
                                                        <p class="order-detail-receipt__title">{{ $translateMessage($receipt->status) }}</p>
                                                        <p class="order-detail-receipt__meta">
                                                            {{ $receipt->payment_date ? dateFormat($receipt->payment_date) : dateFormat($receipt->created_at) }}
                                                        </p>
                                                    </div>
                                                    <button type="button" class="order-detail-badge order-detail-badge--{{ $receiptTone }}" data-toggle="modal" data-target="#receipt-preview-{{ $receipt->id }}" data-bs-toggle="modal" data-bs-target="#receipt-preview-{{ $receipt->id }}">
                                                        @lang('messages.View')
                                                    </button>
                                                </div>

                                                <div class="modal fade" id="receipt-preview-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content order-detail-modal">
                                                            <div class="order-detail-modal__header">
                                                                <h3>@lang('messages.Payment Receipt')</h3>
                                                                <button type="button" class="order-detail-modal__close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="@lang('messages.Close')">
                                                                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                            <div class="order-detail-modal__body">
                                                                <img class="order-detail-receipt-image" src="{{ route('orders.transport.payments.receipt', ['order' => $order->id, 'payment' => $receipt->id]) }}" alt="@lang('messages.Payment Receipt')">
                                                                @if (trim((string) $receipt->note) !== '')
                                                                    <div class="order-detail-inline-alert order-detail-inline-alert--danger mt-3">
                                                                        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                                                                        <div>
                                                                            <strong>{{ $translateMessage($receipt->status) }}</strong>
                                                                            <p>{!! $receipt->note !!}</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="order-detail-alert mt-3">
                                            @lang('messages.Waiting Payment')
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="order-detail-sidebar-sticky">
                            <div class="order-detail-sidebar-card order-detail-sidebar-card--sticky">
                                <h2 class="order-detail-sidebar-card__title">@lang('messages.Actions')</h2>
                                <p class="order-detail-sidebar-card__text">
                                    @if ($order->status === 'Paid')
                                        @lang('messages.This order is already paid and can be used as your final booking reference.')
                                    @else
                                        @lang('messages.Use this page as your booking reference and continue payment only when needed.')
                                    @endif
                                </p>

                                <div class="order-detail-action-list">
                                    @include('frontend.home.orders.details.partials.invoice-action-buttons', ['variant' => 'modern', 'invoicePreviewModalId' => $invoicePreviewModalId])

                                    @if ($canSubmitPayment)
                                        <button type="button" class="order-detail-btn order-detail-btn--soft" data-toggle="modal" data-target="#payment-confirmation-{{ $order->id }}" data-bs-toggle="modal" data-bs-target="#payment-confirmation-{{ $order->id }}">
                                            <i class="fa-solid fa-upload" aria-hidden="true"></i>
                                            @lang('messages.Payment Confirmation')
                                        </button>
                                    @endif

                                    <a href="{{ route('view.orders') }}#orderTransport" class="order-detail-btn order-detail-btn--soft">
                                        <i class="fa-solid fa-list" aria-hidden="true"></i>
                                        @lang('messages.Orders')
                                    </a>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </main>

        @if ($canSubmitPayment)
            <div class="modal fade" id="payment-confirmation-{{ $order->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content order-detail-modal">
                        <div class="order-detail-modal__header">
                            <h3>@lang('messages.Payment Confirmation')</h3>
                            <button type="button" class="order-detail-modal__close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="@lang('messages.Close')">
                                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="order-detail-modal__body">
                            <form id="payment-confirm-{{ $order->id }}" action="/fpayment-confirmation-{{ $order->id }}" method="POST" enctype="multipart/form-data" class="order-detail-upload-form">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">

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
                                        <strong>{{ $invoiceGrandTotal }}</strong>
                                    </div>
                                </div>

                                <div class="order-detail-alert mt-3">
                                    @lang('messages.Complete payment and upload the proof within 2 x 24 hours after approval to keep this booking active.')
                                </div>

                                <div class="order-detail-upload mt-3">
                                    <label for="receipt_name" class="form-label">@lang('messages.Select Receipt')</label>
                                    <input type="file" name="receipt_name" id="receipt_name" class="form-control @error('receipt_name') is-invalid @enderror" data-receipt-input="#transport-receipt-preview-{{ $order->id }}" data-receipt-empty="@lang('messages.No preview available')" required>
                                    @error('receipt_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="order-detail-payment-preview mt-3" id="transport-receipt-preview-{{ $order->id }}">
                                    <span>@lang('messages.No preview available')</span>
                                </div>
                            </form>
                        </div>
                        <div class="order-detail-modal__footer">
                            <button type="submit" form="payment-confirm-{{ $order->id }}" class="order-detail-btn order-detail-btn--primary order-detail-btn--auto">
                                <i class="fa-solid fa-upload" aria-hidden="true"></i>
                                @lang('messages.Send')
                            </button>
                            <button type="button" class="order-detail-btn order-detail-btn--soft order-detail-btn--auto" data-dismiss="modal" data-bs-dismiss="modal">
                                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                @lang('messages.Close')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
