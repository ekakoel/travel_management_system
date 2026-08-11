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
    $statusTone = $statusToneMap[$order->status] ?? 'default';
    $statusLabel = __('messages.' . $order->status) !== 'messages.' . $order->status ? __('messages.' . $order->status) : $order->status;
    $serviceLabel = __('messages.' . $order->service) !== 'messages.' . $order->service ? __('messages.' . $order->service) : $order->service;
    $packageName = trim((string) (($activity->$langName ?: $activity->name) ?: ($order->servicename ?: $order->subservice)));
    $packageType = trim((string) ($activity->type?->$langType ?: $activity->type?->type));
    $durationLabel = $activity->duration_days
        ? ($activity->duration_nights > 0
            ? __('messages.duration_days_nights', ['days' => $activity->duration_days, 'nights' => $activity->duration_nights])
            : __('messages.duration_days', ['days' => $activity->duration_days]))
        : '';
    $profileIncomplete = Auth::user()->email == '';
    $isEditable = in_array($order->status, ['Draft', 'Invalid'], true);
    $canDelete = in_array($order->status, ['Draft', 'Invalid', 'Rejected'], true);
    $guestRows = ($order->relationLoaded('guests') ? $order->guests : $order->guests()->get())
        ->filter(function ($guest) {
            return collect([
                $guest->name,
                $guest->phone,
                $guest->age,
                $guest->sex,
            ])->contains(fn ($value) => trim((string) $value) !== '');
        })
        ->values();
    $itineraryContent = trim((string) ($packageOverviewItinerary ?? $order->itinerary ?: ($generatedActivityItinerary ?? '')));
    $destinationsContent = trim((string) ($order->destinations ?: data_get($activity, $langPackageHighlights) ?: $activity->package_highlights));
    $includeContent = trim((string) (data_get($order, $langInclude) ?: $order->include ?: data_get($activity, $langInclude) ?: $activity->include));
    $excludeContent = trim((string) (data_get($order, $langExclude) ?: $order->exclude ?: data_get($activity, $langExclude) ?: $activity->exclude));
    $additionalInfoContent = trim((string) ($packageOverviewAdditionalInfo ?? $order->additional_info ?: data_get($activity, $langAdditionalInfo) ?: $activity->additional_info));
    $cancellationPolicyContent = trim((string) ($order->cancellation_policy ?: data_get($activity, $langCancellationPolicy) ?: $activity->cancellation_policy));
    $orderNotice = match ($order->status) {
        'Pending' => __('messages.We have received your order, we will contact you as soon as possible to validate the order!'),
        'Rejected', 'Invalid' => trim((string) $order->msg) ?: __('messages.Please make sure all the data is correct before you submit the order!'),
        default => __('messages.Please make sure all the data is correct before you submit the order!'),
    };
    $summaryCards = [
        ['label' => __('messages.Service'), 'value' => $serviceLabel],
        ['label' => __('messages.Travel Date'), 'value' => $order->travel_date ? dateTimeFormat($order->travel_date) : dateFormat($order->checkin)],
        ['label' => __('messages.Number of Guests'), 'value' => __('messages.pax_count', ['count' => $order->number_of_guests ?: 0])],
        ['label' => __('messages.Total Price'), 'value' => $order->request_quotation === 'Yes'
            ? __('messages.To be advised')
            : currencyFormatUsd($activityPricing['total_usd'])],
    ];
    $bookingInfo = [
        ['label' => __('messages.Order No'), 'value' => $order->orderno],
        ['label' => __('messages.Order Date'), 'value' => dateTimeFormat($order->created_at)],
        ['label' => __('messages.Activity Package'), 'value' => $packageName ?: '-'],
        ['label' => __('messages.Type'), 'value' => $packageType ?: '-'],
        ['label' => __('messages.Duration'), 'value' => $durationLabel ?: '-'],
    ];
    $logisticsInfo = [
        ['label' => __('messages.Activity Start'), 'value' => dateFormat($order->checkin)],
        ['label' => __('messages.Activity End'), 'value' => dateFormat($order->checkout)],
        ['label' => __('messages.Pick up location'), 'value' => $order->pickup_location ?: '-'],
        ['label' => __('messages.Drop off location'), 'value' => $order->dropoff_location ?: '-'],
        ['label' => __('messages.Pickup Date'), 'value' => $order->pickup_date ?: '-'],
        ['label' => __('messages.Dropoff Date'), 'value' => $order->dropoff_date ?: '-'],
    ];
    $snapshotSections = [
        ['title' => __('messages.Itinerary'), 'content' => $itineraryContent],
        ['title' => __('messages.package_highlights'), 'content' => $destinationsContent],
        ['title' => __('messages.Include'), 'content' => $includeContent],
        ['title' => __('messages.Exclude'), 'content' => $excludeContent],
        ['title' => __('messages.Additional Information'), 'content' => $additionalInfoContent],
        ['title' => __('messages.Cancelation Policy'), 'content' => $cancellationPolicyContent],
    ];
    $receiptStatusMap = [
        'Valid' => 'active',
        'Paid' => 'active',
        'Pending' => 'pending',
        'Invalid' => 'rejected',
    ];
    $invoicePreviewModalId = $invoice ? 'invoice-preview-' . $order->id : null;
    $invoiceIssueAt = $invoice?->inv_date ? Carbon::parse($invoice->inv_date) : null;
    $paymentDeadlineAt = $paymentDeadline ?? ($invoice?->due_date ? Carbon::parse($invoice->due_date) : null);
    $latestReceipt = data_get($paymentState ?? [], 'latest_receipt')
        ?: (($receipts && count($receipts) > 0) ? collect($receipts)->sortByDesc('id')->first() : null);
    $hasPaymentSubmission = data_get($paymentState ?? [], 'has_submission', $paymentSubmissionExists ?? false);
    $paymentExpired = data_get($paymentState ?? [], 'expired', false);
    $canSubmitPayment = data_get($paymentState ?? [], 'can_submit', false);
    $paymentUnderReview = data_get($paymentState ?? [], 'has_open_review', false);
    $invoiceCurrencyCode = optional($invoice?->currency)->name ?: 'USD';
    $invoiceGrandTotal = match ($invoiceCurrencyCode) {
        'CNY' => '¥ ' . number_format((float) $invoice?->total_cny, 0),
        'TWD' => 'NT$ ' . number_format((float) $invoice?->total_twd, 0),
        'IDR' => 'Rp ' . number_format((float) $invoice?->total_idr, 0),
        default => currencyFormatUsd($activityPricing['total_usd']),
    };
    $paymentStateLabel = match (true) {
        $order->status === 'Paid' => __('messages.Paid'),
        $latestReceipt && $latestReceipt->status === 'Pending' => __('messages.On Review'),
        $latestReceipt && in_array($latestReceipt->status, ['Valid', 'Paid'], true) => __('messages.Paid'),
        $latestReceipt && $latestReceipt->status === 'Invalid' => __('messages.Invalid'),
        $paymentExpired => __('messages.Canceled'),
        default => __('messages.Awaiting Payment'),
    };
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
                            <i class="fa-solid fa-route" aria-hidden="true"></i>
                            @lang('messages.Order Details')
                        </div>
                        <h1 class="order-detail-title">{{ $packageName ?: $order->orderno }}</h1>
                        <p class="order-detail-text">
                            {{ $packageType ?: __('messages.Activity Package') }}.
                            {{ strip_tags((string) $orderNotice) }}
                        </p>
                    </div>

                    <div class="order-detail-status order-detail-status--{{ $statusTone }}">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        {{ $statusLabel }}
                    </div>
                </div>

                <div class="order-detail-summary">
                    {{-- @foreach ($summaryCards as $summaryCard)
                        <div class="order-detail-metric">
                            <span>{{ $summaryCard['label'] }}</span>
                            <strong>{{ $summaryCard['value'] ?: '-' }}</strong>
                        </div>
                    @endforeach --}}
                </div>
            </div>
        </header>

        <main class="order-detail-main">
            <div class="container">
                @if ($profileIncomplete)
                    <div class="order-detail-inline-alert order-detail-inline-alert--warning">
                        <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                        <div>
                            <strong>@lang('messages.Edit Profile')</strong>
                            <p>@lang('messages.Please add your email first to keep order communication and validation running smoothly.') <a href="/profile">@lang('messages.Edit Profile')</a></p>
                        </div>
                    </div>
                @endif

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
                                    @foreach ($logisticsInfo as $item)
                                        <div class="order-detail-info">
                                            <span>{{ $item['label'] }}</span>
                                            <strong>{{ $item['value'] ?: '-' }}</strong>
                                        </div>
                                    @endforeach
                                </div>

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
                                    <div class="order-detail-eyebrow">@lang('messages.Activity Package')</div>
                                    <h2 class="order-detail-section__title">@lang('messages.Package Overview')</h2>
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
                                                    <th>@lang('messages.Phone')</th>
                                                    <th>@lang('messages.Age')</th>
                                                    <th>@lang('messages.Gender')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($guestRows as $index => $guest)
                                                    @php
                                                        $guestAgeLabel = match ($guest->age) {
                                                            'Adult' => __('messages.Adult'),
                                                            'Child' => __('messages.Child'),
                                                            default => $guest->age ?: '-',
                                                        };
                                                        $guestSexLabel = match ($guest->sex) {
                                                            'Male' => __('messages.Male'),
                                                            'Female' => __('messages.Female'),
                                                            default => $guest->sex ?: '-',
                                                        };
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $guest->name ?: '-' }}</td>
                                                        <td>{{ $guest->phone ?: '-' }}</td>
                                                        <td>{{ $guestAgeLabel }}</td>
                                                        <td>{{ $guestSexLabel }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    @if (trim(strip_tags((string) $order->guest_detail)) !== '')
                                        <div class="order-detail-rich">{!! $order->guest_detail !!}</div>
                                    @else
                                        <div class="order-detail-empty">
                                            <i class="fa-solid fa-users" aria-hidden="true"></i>
                                            <p>@lang('messages.no_guest_added')</p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </section>
                        @if ($order->additional_service_total_price > 0 && !empty($additionalServices))
                            <section class="order-detail-section">
                                <div class="order-detail-section__header">
                                    <div>
                                        <div class="order-detail-eyebrow">@lang('messages.Additional Services')</div>
                                        <h2 class="order-detail-section__title">@lang('messages.Additional Services')</h2>
                                    </div>
                                </div>
                                <div class="order-detail-section__body">
                                    <div class="order-detail-table-wrap">
                                        <table class="order-detail-table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.Date')</th>
                                                    <th>@lang('messages.Service')</th>
                                                    <th>@lang('messages.Quantity')</th>
                                                    <th>@lang('messages.Price')</th>
                                                    <th>@lang('messages.Total')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($additionalServices as $service)
                                                    <tr>
                                                        <td>{{ $service['date'] }}</td>
                                                        <td>{{ $service['service'] }}</td>
                                                        <td>{{ $service['qty'] }}</td>
                                                        <td>{{ '$ ' . $service['price'] }}</td>
                                                        <td>{{ '$ ' . $service['total'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="order-detail-total-box">
                                        <div class="order-detail-total-row">
                                            <span>@lang('messages.Additional Service')</span>
                                            <strong>{{ currencyFormatUsd($order->additional_service_total_price) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        @endif

                        <section class="order-detail-section">
                            <div class="order-detail-section__header">
                                <div>
                                    <div class="order-detail-eyebrow">@lang('messages.Price Details')</div>
                                    <h2 class="order-detail-section__title">@lang('messages.Price Details')</h2>
                                </div>
                            </div>
                            <div class="order-detail-section__body">
                                <div class="order-detail-price-list">
                                    <div class="order-detail-price-row">
                                        <span>@lang('messages.Price/pax')</span>
                                        <strong>{{ currencyFormatUsd($activityPricing['unit_price_usd']) }}</strong>
                                    </div>

                                    <div class="order-detail-price-row">
                                        <span>@lang('messages.price_for_guests', ['count' => $order->number_of_guests])</span>
                                        <strong>{{ currencyFormatUsd($activityPricing['gross_total_usd']) }}</strong>
                                    </div>

                                    @if ($order->additional_service_total_price > 0)
                                        <div class="order-detail-price-row">
                                            <span>@lang('messages.Additional Service')</span>
                                            <strong>{{ currencyFormatUsd($order->additional_service_total_price) }}</strong>
                                        </div>
                                    @endif

                                    @if (!empty($filteredDiscounts))
                                        @foreach ($filteredDiscounts as $label => $value)
                                            <div class="order-detail-price-row order-detail-price-row--discount">
                                                <span>{{ $label }}</span>
                                                <strong>{{ $value !== true ? currencyFormatUsd($value) : '-' }}</strong>
                                            </div>
                                        @endforeach
                                    @endif

                                    <div class="order-detail-price-row order-detail-price-row--grand">
                                        <span>@lang('messages.Total Price')</span>
                                        <strong>
                                            @if ($order->request_quotation === 'Yes')
                                                @lang('messages.To be advised')
                                            @else
                                                {{ currencyFormatUsd($activityPricing['total_usd']) }}
                                            @endif
                                        </strong>
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
                                    <span>@lang('messages.Travel Date')</span>
                                    <strong>{{ $order->travel_date ? dateTimeFormat($order->travel_date) : dateFormat($order->checkin) }}</strong>
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
                                        {{-- <strong>{{ $invoiceGrandTotal }}</strong> --}}
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
                                    <div class="order-detail-payment-deadline {{ $paymentExpired ? 'order-detail-payment-deadline--expired' : '' }}"
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
                                                    @lang('messages.Complete payment and upload proof before') <strong>{{ dateTimeFormat($paymentDeadlineAt) }}</strong>.
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

                                @if ($paymentExpired)
                                    <div class="order-detail-inline-alert order-detail-inline-alert--danger mt-3">
                                        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                                        <div>
                                            <strong>@lang('messages.Order canceled automatically')</strong>
                                            <p>@lang('messages.The booking is no longer payable because the 48-hour payment deadline has passed.')</p>
                                        </div>
                                    </div>
                                @endif

                                @if ($receipts && count($receipts) > 0)
                                    <div class="order-detail-receipt-list">
                                        @foreach ($receipts as $receipt)
                                            @php
                                                $receiptTone = $receiptStatusMap[$receipt->status] ?? 'default';
                                            @endphp
                                            <div class="order-detail-receipt">
                                                <div class="order-detail-receipt__icon">
                                                    <i class="fa-solid fa-receipt" aria-hidden="true"></i>
                                                </div>
                                                <div>
                                                    <p class="order-detail-receipt__title">{{ __('messages.'.$receipt->status) }}</p>
                                                    <p class="order-detail-receipt__meta">
                                                        {{ $receipt->payment_date ? dateFormat($receipt->payment_date) : __('messages.On Review') }}
                                                    </p>
                                                </div>
                                                <button type="button" class="ui-btn ui-btn--secondary ui-btn--sm" data-toggle="modal" data-target="#receipt-preview-{{ $receipt->id }}" data-bs-toggle="modal" data-bs-target="#receipt-preview-{{ $receipt->id }}">
                                                    @lang('messages.View')
                                                </button>
                                            </div>

                                            <div class="modal fade" id="receipt-preview-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content order-detail-modal">
                                                        <div class="order-detail-modal__header">
                                                            <h3>@lang('messages.Payment Receipt')</h3>
                                                            <button type="button" class="order-detail-modal__close ui-btn ui-btn--icon" data-dismiss="modal" data-bs-dismiss="modal" aria-label="@lang('messages.Close')">
                                                                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                        <div class="order-detail-modal__body">
                                                            @if (strtolower(pathinfo((string) $receipt->receipt_img, PATHINFO_EXTENSION)) === 'pdf')
                                                                <a class="ui-btn ui-btn--secondary ui-btn--block" href="{{ route('orders.activity.payments.receipt', ['order' => $order->id, 'payment' => $receipt->id]) }}" target="_blank" rel="noopener">
                                                                    <i class="fa-solid fa-file-pdf" aria-hidden="true"></i>
                                                                    @lang('messages.Open Payment Proof')
                                                                </a>
                                                            @else
                                                                <img class="order-detail-receipt-image" src="{{ route('orders.activity.payments.receipt', ['order' => $order->id, 'payment' => $receipt->id]) }}" alt="@lang('messages.Payment Receipt')">
                                                            @endif
                                                            @if (trim((string) $receipt->note) !== '')
                                                                <div class="order-detail-inline-alert order-detail-inline-alert--danger mt-3">
                                                                    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                                                                    <div>
                                                                        <strong>{{ __('messages.'.$receipt->status) }}</strong>
                                                                        <p>{!! nl2br(e($receipt->note)) !!}</p>
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
                                    @if ($isEditable)
                                        @lang('messages.Review the booking carefully before making changes.')
                                    @elseif ($order->status === 'Paid')
                                        @lang('messages.This order is already paid and can be used as your final booking reference.')
                                    @else
                                        @lang('messages.Use this page as your booking reference and continue payment only when needed.')
                                    @endif
                                </p>

                                <div class="order-detail-action-list">
                                    @include('frontend.home.orders.details.partials.invoice-action-buttons', ['variant' => 'modern', 'invoicePreviewModalId' => $invoicePreviewModalId])

                                    @if ($isEditable)
                                        <a href="{{ route('view.edit-order-activity', ['id' => $order->id]) }}" class="ui-btn ui-btn--primary ui-btn--block">
                                            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                            @lang('messages.Edit Order')
                                        </a>
                                    @endif

                                    @if ($canSubmitPayment)
                                        <button type="button" class="ui-btn ui-btn--primary ui-btn--block" data-toggle="modal" data-target="#payment-confirmation-{{ $order->id }}" data-bs-toggle="modal" data-bs-target="#payment-confirmation-{{ $order->id }}">
                                            <i class="fa-solid fa-upload" aria-hidden="true"></i>
                                            @lang('messages.Payment Confirmation')
                                        </button>
                                    @endif

                                    @if ($paymentUnderReview)
                                        <div class="order-detail-alert">
                                            @lang('messages.Your payment proof is under review. A new submission is available after the current review is completed.')
                                        </div>
                                    @endif

                                    <a href="{{ route('view.orders') }}#orderActivity" class="ui-btn ui-btn--secondary ui-btn--block">
                                        <i class="fa-solid fa-list" aria-hidden="true"></i>
                                        @lang('messages.Orders')
                                    </a>

                                    @if ($canDelete)
                                        <form action="{{ route('func.delete-order', $order->id) }}" method="POST" data-confirm-message="@lang('messages.Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ui-btn ui-btn--danger ui-btn--block" data-processing-label="@lang('messages.Processing...')">
                                                <i class="fa-solid fa-trash-alt" aria-hidden="true"></i>
                                                @lang('messages.Delete')
                                            </button>
                                        </form>
                                    @endif
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
                            <button type="button" class="order-detail-modal__close ui-btn ui-btn--icon" data-dismiss="modal" data-bs-dismiss="modal" aria-label="@lang('messages.Close')">
                                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="order-detail-modal__body">
                            <form id="payment-confirm-{{ $order->id }}" action="{{ route('upload.payment-confirmation', ['id' => $order->id]) }}" method="POST" enctype="multipart/form-data" class="order-detail-upload-form" data-payment-confirmation-form>
                                @csrf

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
                                        {{-- <strong>{{ $invoiceGrandTotal }}</strong> --}}
                                    </div>
                                </div>

                                <div class="order-detail-alert mt-3">
                                    @lang('messages.Complete payment and upload the proof within 2 x 24 hours after approval to keep this booking active.')
                                </div>

                                @include('frontend.home.orders.details.partials.payment-confirmation-fields')
                            </form>
                        </div>
                        <div class="order-detail-modal__footer">
                            <button type="submit" form="payment-confirm-{{ $order->id }}" class="ui-btn ui-btn--primary" data-processing-label="@lang('messages.Submitting...')">
                                <i class="fa-solid fa-upload" aria-hidden="true"></i>
                                @lang('messages.Send')
                            </button>
                            <button type="button" class="ui-btn ui-btn--secondary" data-dismiss="modal" data-bs-dismiss="modal">
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
