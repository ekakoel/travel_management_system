@extends('layouts.head')

@section('title', __('admin-orders.detail.title'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/orders-admin/detail.css') }}">
@endpush

@section('content')
    @php
        $canEditOrder = !in_array($order->status, ['Paid', 'Approved'], true);
        $canConfirmOrder = in_array(strtolower((string) $order->status), ['draft', 'pending'], true);
        $isOwner = !$order->handled_by || $order->handled_by == Auth::id();
        $receiptItems = collect($receipts ?? []);
        $isAccommodationOrder = in_array($order->service, \App\Models\Orders::ACCOMMODATION_SERVICES, true);
        $isTourPackageOrder = $order->service === \App\Models\Orders::PUBLIC_TOUR_SERVICE;
        $canEditManifest = $isTourPackageOrder
            ? in_array(strtolower((string) $order->status), ['draft', 'pending'], true)
            : $canEditOrder;
        $workflowRates = $isTourPackageOrder
            ? collect($rates)
                ->whereIn('name', ['USD', 'CNY', 'TWD'])
                ->sortBy(fn ($rate) => array_search($rate->name, ['USD', 'CNY', 'TWD'], true))
                ->values()
            : collect($rates);
        $tourWorkflowCurrencyLabels = [
            'USD' => 'USD - US Dollar',
            'CNY' => 'CNY - Chinese Yuan',
            'TWD' => 'TWD - Taiwan Dollar',
        ];
        $canGenerateInvoice = $order->status === 'Approved';
        $canRegenerateInvoice = in_array($order->status, ['Approved', 'Paid'], true);
        $canSendConfirmation = in_array($order->status, ['Approved', 'Paid'], true);
        $tourCanRejectOrInvalidate = $isTourPackageOrder && in_array($order->status, ['Draft', 'Pending'], true);
        $canArchiveOrder = in_array($order->status, ['Rejected', 'Invalid', 'Canceled'], true);
        $tourCanArchive = $isTourPackageOrder && $canArchiveOrder;
        $isProtectedPublicOrder = app(\App\Services\AccommodationFinancialFileService::class)->isProtectedPublicOrder($order);
        $receiptRoute = match ($order->service) {
            \App\Models\Orders::PUBLIC_TRANSPORT_SERVICE => 'admin.orders.transport.payments.receipt',
            \App\Models\Orders::PUBLIC_TOUR_SERVICE => 'admin.orders.tour.payments.receipt',
            \App\Models\Orders::PUBLIC_ACTIVITY_SERVICE => 'admin.orders.activity.payments.receipt',
            default => $isAccommodationOrder ? 'admin.orders.accommodation.payments.receipt' : null,
        };
        $financialFiles = app(\App\Services\AccommodationFinancialFileService::class);
        $invoiceEnFile = $isProtectedPublicOrder && $invoice ? $financialFiles->resolveInvoiceFile($order, $invoice, 'en') : null;
        $invoiceZhCnFile = $isProtectedPublicOrder && $invoice ? $financialFiles->resolveInvoiceFile($order, $invoice, 'zh-CN') : null;
        $invoiceZhFile = $isProtectedPublicOrder && $invoice ? $financialFiles->resolveInvoiceFile($order, $invoice, 'zh') : null;
        $invoiceEnPath = "storage/document/invoice-{$inv_no}-{$order->id}_en.pdf";
        $invoiceZhCnPath = "storage/document/invoice-{$inv_no}-{$order->id}_zh-CN.pdf";
        $invoiceZhPath = "storage/document/invoice-{$inv_no}-{$order->id}_zh.pdf";
        $hasInvoicePdf = $isProtectedPublicOrder
            ? ($invoiceEnFile || $invoiceZhCnFile || $invoiceZhFile)
            : (File::exists(public_path($invoiceEnPath)) || File::exists(public_path($invoiceZhCnPath)) || File::exists(public_path($invoiceZhPath)));
        $hasCompleteInvoiceSet = $isTourPackageOrder
            ? ($isProtectedPublicOrder
                ? ($invoiceEnFile && $invoiceZhCnFile && $invoiceZhFile)
                : (File::exists(public_path($invoiceEnPath)) && File::exists(public_path($invoiceZhCnPath)) && File::exists(public_path($invoiceZhPath))))
            : $hasInvoicePdf;
        $priceRows = collect([
            ['label' => $isTourPackageOrder ? 'Price / pax' : 'Base price', 'value' => currencyFormatUsd($tourPricing['unit_price_usd'] ?? ($order->price_pax ?: 0)), 'show' => $isTourPackageOrder],
            ['label' => $isTourPackageOrder ? 'Tour price for '.($order->number_of_guests ?: 0).' pax' : 'Base price', 'value' => currencyFormatUsd($tourPricing['gross_total_usd'] ?? ($order->price_total ?: $order->normal_price ?: $order->price_pax ?: 0)), 'show' => true],
            ['label' => 'Optional charges', 'value' => currencyFormatUsd($optional_rate_order_total_price), 'show' => $optional_rate_order_total_price > 0],
            ['label' => 'Additional services', 'value' => currencyFormatUsd($total_additional_service), 'show' => $total_additional_service > 0],
            ['label' => 'Airport shuttle', 'value' => currencyFormatUsd($order->airport_shuttle_price), 'show' => $order->airport_shuttle_price > 0],
            ['label' => 'Promotion', 'value' => '- ' . currencyFormatUsd($total_promotion_disc), 'show' => $total_promotion_disc > 0],
            ['label' => 'Booking code', 'value' => '- ' . currencyFormatUsd($order->bookingcode_disc), 'show' => $order->bookingcode_disc > 0],
            ['label' => 'Discount', 'value' => '- ' . currencyFormatUsd($order->discounts), 'show' => $order->discounts > 0],
        ])->filter(fn ($row) => $row['show']);
        $serviceDetails = collect($isTourPackageOrder ? [
            ['label' => 'Itinerary', 'value' => $order->itinerary ?: $tourOrder?->itinerary],
            ['label' => 'Include', 'value' => $order->include ?: $tourOrder?->include],
            ['label' => 'Exclude', 'value' => $order->exclude ?: $tourOrder?->exclude],
            ['label' => 'Additional Information', 'value' => $order->additional_info ?: $tourOrder?->additional_info],
            ['label' => 'Cancellation Policy', 'value' => $order->cancellation_policy ?: $tourOrder?->cancellation_policy],
        ] : [
            ['label' => 'Benefits', 'value' => $order->benefits],
            ['label' => 'Include', 'value' => $order->include],
            ['label' => 'Additional Information', 'value' => $order->additional_info],
            ['label' => 'Agent Remark', 'value' => $order->note],
            ['label' => 'Cancellation Policy', 'value' => $order->cancellation_policy],
        ])->filter(fn ($item) => filled(strip_tags((string) $item['value'])));
    @endphp

    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <main class="main-container orders-admin-detail-page">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-200px orders-admin-detail-shell">
                    <x-backend.page-hero class="orders-admin-detail-hero">
                        <x-slot name="kicker">
                            @lang('admin-orders.detail.eyebrow')
                        </x-slot>
                        <x-slot name="heading">
                            {{ $order->orderno }}
                        </x-slot>
                        <x-slot name="copy">
                            <p>
                                @lang('admin-orders.detail.subtitle', ['service' => $order->service ?? '-'])
                            </p>
                        </x-slot>
                        <x-slot name="action">
                            <a href="{{ route('admin.order.index') }}" class="backend-page-primary-action">
                                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                @lang('admin-orders.detail.back_to_orders')
                            </a>
                        </x-slot>
                    </x-backend.page-hero>

                    <div class="backend-page-toolbar orders-admin-detail-toolbar">
                        <nav aria-label="{{ __('admin-orders.detail.breadcrumb_label') }}">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">@lang('admin-orders.breadcrumb.admin')</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.order.index') }}">@lang('admin-orders.breadcrumb.orders')</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $order->orderno }}</li>
                            </ol>
                        </nav>
                        <div class="orders-admin-detail-toolbar__meta">
                            <span class="orders-admin-detail-status orders-admin-detail-status--{{ \Illuminate\Support\Str::slug($order->status ?: 'unknown') }}">
                                {{ $order->status ?? '-' }}
                            </span>
                            <span>@lang('admin-orders.detail.created_at', ['date' => dateFormat($order->created_at)])</span>
                        </div>
                    </div>

                    @if($errors->any() || session('success') || session('warning') || session('error_messages'))
                        <div class="info-action">
                            @if($errors->any())
                                <div class="alert alert-danger">{{ $errors->first() }}</div>
                            @endif
                            @if(session('success'))
                                <div class="alert alert-success">{!! session('success') !!}</div>
                            @endif
                            @if(session('warning'))
                                <div class="alert alert-warning">{{ session('warning') }}</div>
                            @endif
                            @if(session('error_messages'))
                                <div class="alert alert-danger">{!! session('error_messages') !!}</div>
                            @endif
                        </div>
                    @endif

                    <div class="orders-admin-detail-workspace">
                        <div class="orders-admin-detail-main">
                            <section id="order-snapshot" class="orders-admin-detail-overview__main" aria-label="{{ __('admin-orders.detail.overview_label') }}">
                            <div class="orders-admin-detail-overview__header">
                                <div>
                                    <span class="backend-section-header__label">@lang('admin-orders.detail.overview_eyebrow')</span>
                                    <h2>@lang('admin-orders.detail.overview_title')</h2>
                                </div>
                                <strong>{{ $orderDetailSummary['total_price'] }}</strong>
                            </div>
                            <dl class="orders-admin-detail-metrics">
                                <div><dt>Order</dt><dd>{{ $order->orderno }}</dd></div>
                                <div><dt>Reservation</dt><dd>{{ $reservation->rsv_no ?? '-' }}</dd></div>
                                <div><dt>Reservation Status</dt><dd>{{ $reservation->status ?? '-' }}</dd></div>
                                <div><dt>Service</dt><dd>{{ $order->service ?? '-' }}</dd></div>
                                @if($isTourPackageOrder)
                                    <div><dt>Tour Package</dt><dd>{{ $tourOrder?->name ?? $order->servicename ?? '-' }}</dd></div>
                                    <div><dt>Tour Type</dt><dd>{{ $tourOrder?->type?->type ?? $order->tour_type ?? '-' }}</dd></div>
                                    <div><dt>Travel Date</dt><dd>{{ $order->travel_date ? dateTimeFormat($order->travel_date) : ($order->checkin ? dateFormat($order->checkin) : '-') }}</dd></div>
                                    <div><dt>Duration</dt><dd>{{ $order->duration ?: (($tourOrder?->duration_days ?: 0).'D'.($tourOrder?->duration_nights > 0 ? ' / '.$tourOrder->duration_nights.'N' : '')) }}</dd></div>
                                    <div><dt>Pickup</dt><dd>{{ $order->pickup_location ?: '-' }}</dd></div>
                                    <div><dt>Drop-off</dt><dd>{{ $order->dropoff_location ?: '-' }}</dd></div>
                                @else
                                    <div><dt>Hotel</dt><dd>{{ $isHotelOrder ? ($hotel?->name ?? $order->servicename ?? '-') : ($order->servicename ?? '-') }}</dd></div>
                                    <div><dt>Room / Subservice</dt><dd>{{ $hotelRoom?->rooms ?? $order->subservice ?? '-' }}</dd></div>
                                    <div><dt>Stay / Schedule</dt><dd>{{ $orderDetailSummary['schedule'] }}</dd></div>
                                    <div><dt>Rooms</dt><dd>{{ $order->number_of_room ?: '-' }}</dd></div>
                                @endif
                                <div><dt>Guests</dt><dd>{{ $orderDetailSummary['guests'] }}</dd></div>
                                <div><dt>Agent</dt><dd>{{ $orderDetailSummary['agent'] }}</dd></div>
                                <div><dt>Handled By</dt><dd>{{ $orderDetailSummary['handled_by'] }}</dd></div>
                                <div><dt>Payment</dt><dd>{{ $orderDetailSummary['payment_status'] }}</dd></div>
                            </dl>
                            <nav class="orders-admin-detail-jump" aria-label="{{ __('admin-orders.detail.quick_links.label') }}">
                                @foreach($orderDetailQuickLinks as $link)
                                    @if(($link['href'] !== '#hotel-validation' || $isHotelOrder) && ($link['href'] !== '#tour-details' || $isTourPackageOrder))
                                        <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
                                    @endif
                                @endforeach
                            </nav>

                            @if($isHotelOrder)
                                <div id="hotel-validation" class="orders-admin-detail-section">
                                    <div class="orders-admin-detail-section__header">
                                        <span class="backend-page-eyebrow">Hotel validation</span>
                                        <h3>Availability & Rate Check</h3>
                                    </div>
                                    <dl class="orders-admin-detail-data-grid">
                                        <div><dt>Hotel</dt><dd>{{ $hotel?->name ?? '-' }}</dd></div>
                                        <div><dt>Hotel Status</dt><dd>{{ $hotel?->status ?? '-' }}</dd></div>
                                        <div><dt>Room</dt><dd>{{ $hotelRoom?->rooms ?? $order->subservice ?? '-' }}</dd></div>
                                        <div><dt>Room Status</dt><dd>{{ $hotelRoom?->status ?? '-' }}</dd></div>
                                        <div><dt>Check In</dt><dd>{{ $order->checkin ? dateFormat($order->checkin) : '-' }}</dd></div>
                                        <div><dt>Check Out</dt><dd>{{ $order->checkout ? dateFormat($order->checkout) : '-' }}</dd></div>
                                        <div><dt>Contract Rate</dt><dd>{{ $hotelRate ? currencyFormatUsd($hotelRate->contract_rate) : 'Needs hotel confirmation' }}</dd></div>
                                        <div><dt>Capacity</dt><dd>{{ $hotelRoom ? $hotelRoom->capacity_adult . ' adult / ' . $hotelRoom->capacity_child . ' child' : '-' }}</dd></div>
                                    </dl>
                                    <div class="orders-admin-detail-checklist">
                                        @foreach($hotelValidationChecklist as $item)
                                            <div class="orders-admin-detail-checklist__item orders-admin-detail-checklist__item--{{ $item['tone'] }}">
                                                <strong>{{ $item['label'] }}</strong>
                                                <span>{{ $item['value'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($serviceDetails->isNotEmpty())
                                <div id="{{ $isTourPackageOrder ? 'tour-details' : 'service-details' }}" class="orders-admin-detail-section">
                                    <div>
                                        <span class="backend-section-header__label">Service</span>
                                        <h3>Order Details</h3>
                                    </div>
                                    {{-- <div class="orders-admin-detail-section__header">
                                        <span class="backend-page-eyebrow">Service</span>
                                        <h3>Order Details</h3>
                                    </div> --}}
                                    <div class="orders-admin-detail-rich-list">
                                        @foreach($serviceDetails as $detail)
                                            <article>
                                                <h3>{{ $detail['label'] }}</h3>
                                                <div>{!! $detail['value'] !!}</div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            </section>
                            <section id="confirmation" class="backend-panel orders-admin-detail-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Validation</span>
                                        <h2>Confirmation Reference</h2>
                                    </div>
                                </div>
                                <form id="updateConfirmationNumber" action="{{ route('admin.orders.confirmation-number.update', ['id' => $order->id]) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="orders-admin-detail-form-grid">
                                        <div class="backend-form-field">
                                                <label for="confirmation_order">{{ $isTourPackageOrder ? 'Tour supplier confirmation number' : 'Hotel / supplier confirmation number' }}</label>
                                            <input id="confirmation_order" name="confirmation_order" type="text" value="{{ old('confirmation_order', $order->confirmation_order) }}" class="backend-form-control @error('confirmation_order') is-invalid @enderror" placeholder="Confirmation number" required>
                                        </div>
                                        <div class="orders-admin-detail-form-action m-b-18">
                                            <button type="submit" class="backend-button backend-button-primary">
                                                <i class="icon-copy fa fa-check" aria-hidden="true"></i> Save
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </section>

                            <section id="guests" class="backend-panel orders-admin-detail-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Manifest</span>
                                        <h2>Guests</h2>
                                    </div>
                                    @if($canEditManifest && $isOwner)
                                        <button type="button" class="backend-button backend-button-primary" data-toggle="modal" data-target="#addGuestModal">
                                            <i class="fa fa-plus" aria-hidden="true"></i> Add Guest
                                        </button>
                                    @endif
                                </div>

                                @if($isTourPackageOrder && $canEditManifest && $isOwner)
                                    <div class="alert alert-info mb-3" role="status">
                                        Adding, editing, or removing a guest recalculates the Tour price from the travel date and current pax tier. The change is cancelled if no valid price is available.
                                    </div>
                                @endif

                                @if($guests->count())
                                    <div class="backend-table-wrap orders-admin-desktop-table">
                                        <table class="backend-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Mandarin</th>
                                                    <th>Sex / Age</th>
                                                    <th>Phone</th>
                                                    <th class="text-right">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($guests as $index => $guest)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td><strong>{{ $guest->name ?: '-' }}</strong></td>
                                                        <td>{{ $guest->name_mandarin ?: '-' }}</td>
                                                        <td>{{ in_array($guest->sex, ['m', 'Male'], true) ? 'Male' : (in_array($guest->sex, ['f', 'Female'], true) ? 'Female' : '-') }} / {{ $guest->age ?: '-' }}</td>
                                                        <td>{{ $guest->phone ?: '-' }}</td>
                                                        <td class="text-right">
                                                            @if($canEditManifest && $isOwner)
                                                                <form id="deleteGuest{{ $guest->id }}" action="{{ url('/delete-guest/' . $guest->id) }}" method="post">
                                                                    @csrf
                                                                    @method('delete')
                                                                </form>
                                                                <div class="backend-table-actions">
                                                                    <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#editGuest{{ $guest->id }}" aria-label="Edit guest">
                                                                        <i class="fa fa-pencil-alt" aria-hidden="true"></i>
                                                                    </button>
                                                                    <button type="submit" form="deleteGuest{{ $guest->id }}" class="backend-danger-icon-action" onclick="return confirm('Delete this guest?')" aria-label="Delete guest">
                                                                        <i class="fa fa-trash-alt" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                            @else
                                                                <span class="orders-admin-muted">Locked</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="backend-table-empty">
                                        <i class="fa fa-users" aria-hidden="true"></i>
                                        <strong>No guest records</strong>
                                        <span>Add structured guests so reservations and documents use the same source of truth.</span>
                                    </div>
                                @endif
                            </section>

                            <section id="billing" class="backend-panel orders-admin-detail-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Billing</span>
                                        <h2>Invoice & Payment</h2>
                                    </div>
                                    <strong class="orders-admin-detail-total">{{ currencyFormatUsd($tourPricing['total_usd'] ?? $order->final_price) }}</strong>
                                </div>
                                <div class="orders-admin-detail-price">
                                    @foreach($priceRows as $row)
                                        <div><span>{{ $row['label'] }}</span><strong>{{ $row['value'] }}</strong></div>
                                    @endforeach
                                    <div class="orders-admin-detail-price__total"><span>Total USD</span><strong>{{ currencyFormatUsd($tourPricing['total_usd'] ?? $order->final_price) }}</strong></div>
                                    @if($invoice && $invoice->currency?->name !== 'USD')
                                        <div class="orders-admin-detail-price__total"><span>Total {{ $invoice->currency?->name }}</span><strong>{{ $invoice->currency?->name === 'CNY' ? currencyFormatCny($invoice->total_cny) : ($invoice->currency?->name === 'TWD' ? currencyFormatTwd($invoice->total_twd) : currencyFormatIdr($invoice->total_idr)) }}</strong></div>
                                    @endif
                                </div>
                                <dl class="orders-admin-detail-data-grid orders-admin-detail-data-grid--compact">
                                    <div><dt>Invoice</dt><dd>{{ $invoice?->inv_no ?? '-' }}</dd></div>
                                    <div><dt>Due Date</dt><dd>{{ $invoice?->due_date ? dateFormat($invoice->due_date) : '-' }}</dd></div>
                                    <div><dt>Balance</dt><dd>{{ $invoice ? ($invoice->balance <= 1 ? 'Paid' : currencyFormatUsd($invoice->balance)) : '-' }}</dd></div>
                                    <div><dt>Document</dt><dd>{{ $hasInvoicePdf ? 'Generated' : 'Not generated' }}</dd></div>
                                </dl>

                                @if($receiptItems->count())
                                    <div class="backend-table-wrap orders-admin-detail-subtable">
                                        <table class="backend-table">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Currency</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th class="text-right">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($receiptItems as $receipt)
                                                    <tr>
                                                        <td>{{ $receipt->created_at ? dateFormat($receipt->created_at) : '-' }}</td>
                                                        <td>{{ $receipt->kurs?->name ?? '-' }}</td>
                                                        <td>{{ currencyFormatUsd($receipt->amount) }}</td>
                                                        <td>{{ $receipt->status ?: '-' }}</td>
                                                        <td class="text-right">
                                                            <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#receiptModal{{ $receipt->id }}" aria-label="Receipt detail">
                                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="backend-table-empty">
                                        <i class="fa fa-file-image-o" aria-hidden="true"></i>
                                        <strong>No payment receipt</strong>
                                        <span>Payment proof will appear here after it is uploaded.</span>
                                    </div>
                                @endif
                            </section>

                        </div>

                        <aside class="orders-admin-detail-rail" id="workflow">
                            <section class="orders-admin-detail-next">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">@lang('admin-orders.detail.recommendations.eyebrow')</span>
                                        <h2>@lang('admin-orders.detail.recommendations.title')</h2>
                                        @forelse($orderDetailRecommendations as $recommendation)
                                            <a class="orders-admin-detail-next__item orders-admin-detail-next__item--{{ $recommendation['tone'] }} m-b-8" href="{{ $recommendation['href'] }}">
                                                <strong>{{ $recommendation['label'] }}</strong>
                                                <span>{{ $recommendation['description'] }}</span>
                                            </a>
                                        @empty
                                            <div class="orders-admin-detail-next__empty">@lang('admin-orders.detail.recommendations.empty')</div>
                                        @endforelse
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel orders-admin-detail-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Workflow</span>
                                        <h2>Validation Actions</h2>
                                    </div>
                                </div>
                                @if($canConfirmOrder)
                                    <form id="factivate-order-{{ $order->id }}" action="{{ route('admin.orders.workflow.activate', $order) }}" method="post">
                                        @csrf
                                        @method('put')
                                        <div class="orders-admin-detail-form-grid orders-admin-detail-form-grid--single">
                                            <div class="backend-form-field">
                                                <label for="bank">Bank</label>
                                                <select id="bank" name="bank" class="backend-form-control" required>
                                                    @foreach($banks as $bank)
                                                        <option {{ $bank->currency == 'USD' ? 'selected' : '' }} value="{{ $bank->id }}">{{ $bank->bank }} - {{ $bank->currency }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="backend-form-field">
                                                <label for="currency">Currency</label>
                                                <select id="currency" name="currency" class="backend-form-control" required>
                                                    @foreach($workflowRates as $rate)
                                                        <option {{ $rate->name == 'USD' ? 'selected' : '' }} value="{{ $rate->id }}">
                                                            {{ $isTourPackageOrder ? ($tourWorkflowCurrencyLabels[$rate->name] ?? $rate->name) : $rate->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                                <form id="generateInvoice" action="{{ route('admin.orders.invoice.generate', $order) }}" method="post">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" name="bank" value="{{ $banks->firstWhere('currency', 'USD')?->id ?? $banks->first()?->id }}">
                                    <input type="hidden" name="currency" value="{{ $workflowRates->firstWhere('name', 'USD')?->id ?? $workflowRates->first()?->id }}">
                                </form>
                                <form id="regenerateInvoice" action="{{ route('admin.orders.invoice.regenerate', $order) }}" method="post">
                                    @csrf
                                    @method('put')
                                </form>
                                <form id="sendConfirmation" action="{{ route('admin.orders.confirmation.send', $order) }}" method="post">@csrf @method('put')</form>
                                <form id="resendConfirmation" action="{{ route('admin.orders.confirmation.resend', $order) }}" method="post">@csrf @method('put')</form>
                                <form id="sendApprovalEmail" action="{{ route('admin.orders.approval-email.send', $order) }}" method="post">@csrf @method('put')</form>
                                <form id="finalizationOrder" action="{{ route('func.admin-finalization-order', $order->id) }}" method="post">@csrf @method('PUT')</form>

                                <div class="orders-admin-detail-action-list">
                                    @if($canConfirmOrder)
                                        <button type="submit" form="factivate-order-{{ $order->id }}" class="backend-button backend-button-primary">
                                            <i class="fa fa-check" aria-hidden="true"></i> Confirm Order
                                        </button>
                                    @endif
                                    @if($canGenerateInvoice && !$hasInvoicePdf)
                                        <button type="submit" form="generateInvoice" class="backend-button backend-button-primary">
                                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Generate Invoice
                                        </button>
                                    @endif
                                    @if($canRegenerateInvoice && $invoice && $hasInvoicePdf && !$hasCompleteInvoiceSet)
                                        <button type="submit" form="regenerateInvoice" class="backend-button backend-button-primary">
                                            <i class="fa fa-refresh" aria-hidden="true"></i> Regenerate 3-language Invoice
                                        </button>
                                    @endif
                                    @if($canSendConfirmation && $hasCompleteInvoiceSet && !$confirmationWasSent)
                                        <button type="submit" form="sendConfirmation" class="backend-button backend-button-primary">
                                            <i class="fa fa-envelope" aria-hidden="true"></i> Send Confirmation
                                        </button>
                                    @elseif($canSendConfirmation && $hasCompleteInvoiceSet && $confirmationWasSent)
                                        <button type="submit" form="resendConfirmation" class="backend-button backend-button-primary">
                                            <i class="fa fa-envelope" aria-hidden="true"></i> Resend Confirmation
                                        </button>
                                    @endif
                                    @if($hasInvoicePdf)
                                        <a class="backend-button backend-button-secondary" href="{{ route('admin.orders.document.print', $order) }}" target="_blank">
                                            <i class="fa fa-print" aria-hidden="true"></i> Print Document
                                        </a>
                                    @endif
                                    @if($isProtectedPublicOrder ? $invoiceEnFile : File::exists(public_path($invoiceEnPath)))
                                        <a class="backend-button backend-button-secondary" href="{{ $isProtectedPublicOrder ? route('admin.orders.accommodation.invoice.preview', ['order' => $order->id, 'locale' => 'en']) : asset($invoiceEnPath) }}" target="_blank">
                                            <i class="fa fa-download" aria-hidden="true"></i> Invoice English
                                        </a>
                                    @endif
                                    @if($isProtectedPublicOrder ? $invoiceZhCnFile : File::exists(public_path($invoiceZhCnPath)))
                                        <a class="backend-button backend-button-secondary" href="{{ $isProtectedPublicOrder ? route('admin.orders.accommodation.invoice.preview', ['order' => $order->id, 'locale' => 'zh-CN']) : asset($invoiceZhCnPath) }}" target="_blank">
                                            <i class="fa fa-download" aria-hidden="true"></i> Invoice Chinese Simplified
                                        </a>
                                    @endif
                                    @if($isProtectedPublicOrder ? $invoiceZhFile : File::exists(public_path($invoiceZhPath)))
                                        <a class="backend-button backend-button-secondary" href="{{ $isProtectedPublicOrder ? route('admin.orders.accommodation.invoice.preview', ['order' => $order->id, 'locale' => 'zh']) : asset($invoiceZhPath) }}" target="_blank">
                                            <i class="fa fa-download" aria-hidden="true"></i> Invoice Chinese Traditional
                                        </a>
                                    @endif
                                </div>
                            </section>

                            <section class="backend-panel orders-admin-detail-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Agent communication</span>
                                        <h2>Notes</h2>
                                    </div>
                                @if(in_array($order->status, ['Draft', 'Pending', 'Approved'], true))
                                        <button type="button" class="backend-button backend-button-primary" data-toggle="modal" data-target="#addNoteModal">
                                            <i class="fa fa-plus" aria-hidden="true"></i> Add
                                        </button>
                                    @endif
                                </div>
                                @if($agentCommunication['status_reason'])
                                    <article class="orders-admin-detail-note orders-admin-detail-note--danger">
                                        <strong>Status reason</strong>
                                        <span>{{ $agentCommunication['status'] }} · Agent-facing</span>
                                        <p>{!! nl2br(e($agentCommunication['status_reason'])) !!}</p>
                                    </article>
                                @endif
                                @foreach($agentCommunication['context'] as $communication)
                                    <article class="orders-admin-detail-note">
                                        <strong>{{ $communication['label'] }}</strong>
                                        <span>Order context</span>
                                        <p>{!! nl2br(e($communication['value'])) !!}</p>
                                    </article>
                                @endforeach
                                @forelse($order_notes as $note)
                                    @php $operator = $admins->where('id', $note->user_id)->first(); @endphp
                                    <article class="orders-admin-detail-note">
                                        <strong>{{ dateTimeFormat($note->created_at) }} - {{ $operator?->name ?? 'Admin' }}</strong>
                                        <span>{{ $note->status }}</span>
                                        <p>{!! nl2br(e($note->note)) !!}</p>
                                    </article>
                                @empty
                                    <div class="orders-admin-detail-next__empty">No operator notes recorded.</div>
                                @endforelse
                            </section>

                            <section class="backend-panel orders-admin-detail-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Control</span>
                                        <h2>Status Control</h2>
                                    </div>
                                </div>
                                <div class="orders-admin-detail-action-list">
                                    @if(!$isTourPackageOrder || $tourCanRejectOrInvalidate)
                                        <button type="button" class="backend-button backend-button-danger" data-toggle="modal" data-target="#rejectOrderModal">
                                            <i class="fa fa-ban" aria-hidden="true"> </i> Reject
                                        </button>
                                        <button type="button" class="backend-button backend-button-danger" data-toggle="modal" data-target="#invalidOrderModal">
                                            <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Mark Invalid
                                        </button>
                                    @endif
                                    @if($canArchiveOrder)
                                        <button type="button" class="backend-button backend-button-danger" data-toggle="modal" data-target="#archiveOrderModal">
                                            <i class="fa fa-archive" aria-hidden="true"></i> Archive
                                        </button>
                                    @endif
                                    @if($isTourPackageOrder && !$tourCanRejectOrInvalidate && !$tourCanArchive)
                                        <span class="orders-admin-muted">No destructive action is available for this Tour order status.</span>
                                    @endif
                                </div>
                            </section>
                        </aside>
                    </div>
                </div>
            </div>
        </main>

        @if($canEditManifest && $isOwner)
            <div class="modal fade" id="addGuestModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content orders-admin-detail-modal">
                        <div class="orders-admin-detail-modal__header"><h3>Add Guest</h3></div>
                        <div class="orders-admin-detail-modal__body">
                            <form id="addGuest" action="{{ route('func.reservation-add-guest', $order->id) }}" method="post">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-sm-6 backend-form-field"><label for="add_guest_name">Name</label><input id="add_guest_name" type="text" name="name" class="backend-form-control" required></div>
                                    <div class="col-sm-6 backend-form-field"><label for="add_guest_name_mandarin">Mandarin Name</label><input id="add_guest_name_mandarin" type="text" name="name_mandarin" class="backend-form-control"></div>
                                    <div class="col-sm-4 backend-form-field"><label for="add_guest_sex">Gender</label><select id="add_guest_sex" name="sex" class="backend-form-control" required><option value="">Select</option><option value="{{ $isTourPackageOrder ? 'Male' : 'm' }}">Male</option><option value="{{ $isTourPackageOrder ? 'Female' : 'f' }}">Female</option></select></div>
                                    <div class="col-sm-4 backend-form-field"><label for="add_guest_age">Age</label><select id="add_guest_age" name="age" class="backend-form-control" required><option value="">Select</option><option value="Adult">Adult</option><option value="Child">Child</option></select></div>
                                    <div class="col-sm-4 backend-form-field"><label for="add_guest_phone">Phone</label><input id="add_guest_phone" type="number" name="phone" class="backend-form-control"></div>
                                    <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                </div>
                            </form>
                        </div>
                        <div class="orders-admin-detail-modal__footer">
                            <button type="submit" form="addGuest" class="backend-button backend-button-primary">Add Guest</button>
                            <button type="button" class="backend-button backend-button-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            @foreach($guests as $guest)
                <div class="modal fade" id="editGuest{{ $guest->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content orders-admin-detail-modal">
                            <div class="orders-admin-detail-modal__header"><h3>Edit Guest</h3></div>
                            <div class="orders-admin-detail-modal__body">
                                <form id="updateGuest{{ $guest->id }}" action="{{ url('/fupdate-guest/' . $guest->id) }}" method="post">
                                    @csrf
                                    @method('put')
                                    <div class="row">
                                        <div class="col-sm-6 backend-form-field"><label for="edit_guest_name_{{ $guest->id }}">Name</label><input id="edit_guest_name_{{ $guest->id }}" type="text" name="name" class="backend-form-control" value="{{ $guest->name }}" required></div>
                                        <div class="col-sm-6 backend-form-field"><label for="edit_guest_name_mandarin_{{ $guest->id }}">Mandarin Name</label><input id="edit_guest_name_mandarin_{{ $guest->id }}" type="text" name="name_mandarin" class="backend-form-control" value="{{ $guest->name_mandarin }}"></div>
                                        <div class="col-sm-4 backend-form-field"><label for="edit_guest_sex_{{ $guest->id }}">Gender</label><select id="edit_guest_sex_{{ $guest->id }}" name="sex" class="backend-form-control" required><option value="{{ $isTourPackageOrder ? 'Male' : 'm' }}" {{ in_array($guest->sex, ['m', 'Male'], true) ? 'selected' : '' }}>Male</option><option value="{{ $isTourPackageOrder ? 'Female' : 'f' }}" {{ in_array($guest->sex, ['f', 'Female'], true) ? 'selected' : '' }}>Female</option></select></div>
                                        <div class="col-sm-4 backend-form-field"><label for="edit_guest_age_{{ $guest->id }}">Age</label><select id="edit_guest_age_{{ $guest->id }}" name="age" class="backend-form-control" required><option value="Adult" {{ $guest->age === 'Adult' ? 'selected' : '' }}>Adult</option><option value="Child" {{ $guest->age === 'Child' ? 'selected' : '' }}>Child</option></select></div>
                                        <div class="col-sm-4 backend-form-field"><label for="edit_guest_phone_{{ $guest->id }}">Phone</label><input id="edit_guest_phone_{{ $guest->id }}" type="number" name="phone" class="backend-form-control" value="{{ $guest->phone }}"></div>
                                    </div>
                                </form>
                            </div>
                            <div class="orders-admin-detail-modal__footer">
                                <button type="submit" form="updateGuest{{ $guest->id }}" class="backend-button backend-button-primary">Update</button>
                                <button type="button" class="backend-button backend-button-danger" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content orders-admin-detail-modal">
                    <div class="orders-admin-detail-modal__header"><h3>Add Note</h3></div>
                    <div class="orders-admin-detail-modal__body">
                        <form id="addOrderNote" action="{{ route('admin.orders.notes.store', ['id' => $order->id]) }}" method="post">
                            @csrf
                            <div class="backend-form-field">
                                <label for="order_note_status">Type</label>
                                <select id="order_note_status" name="status" class="backend-form-control" required>
                                    <option value="Info">Info</option>
                                    <option value="Waiting">Waiting</option>
                                    <option value="Urgent">Urgent</option>
                                    <option value="Error">Error</option>
                                    <option value="Reject">Reject</option>
                                </select>
                            </div>
                            <div class="backend-form-field"><label for="order_note">Note</label><textarea id="order_note" data-backend-richtext="true" name="order_note" class="backend-form-control" rows="4" required></textarea></div>
                            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                        </form>
                    </div>
                    <div class="orders-admin-detail-modal__footer">
                        <button type="submit" form="addOrderNote" class="backend-button backend-button-primary">Submit</button>
                        <button type="button" class="backend-button backend-button-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        @foreach($receiptItems as $receipt)
            <div class="modal fade" id="receiptModal{{ $receipt->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content orders-admin-detail-modal">
                        <div class="orders-admin-detail-modal__header"><h3>Receipt Detail</h3></div>
                        <div class="orders-admin-detail-modal__body">
                            <div class="row">
                                <div class="col-md-6">
                                    @if($receipt->receipt_img)
                                        <img class="orders-admin-detail-receipt-img" src="{{ $receiptRoute ? route($receiptRoute, ['order' => $order->id, 'payment' => $receipt->id]) : asset('storage/receipt/' . $receipt->receipt_img) }}" alt="Receipt">
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <form id="confirmReceipt{{ $receipt->id }}" action="{{ route('admin.confirm.receipt', $receipt->id) }}" method="post">
                                        @csrf
                                        <div class="backend-form-field"><label for="receipt_status_{{ $receipt->id }}">Status</label><select id="receipt_status_{{ $receipt->id }}" name="status" class="backend-form-control" required><option value="{{ $receipt->status }}">{{ $receipt->status }}</option><option value="Valid">Valid</option><option value="Invalid">Invalid</option></select></div>
                                        <div class="backend-form-field"><label for="receipt_currency_{{ $receipt->id }}">Currency</label><select id="receipt_currency_{{ $receipt->id }}" name="kurs_id" class="backend-form-control" required>@foreach($rates as $rate)<option value="{{ $rate->id }}" {{ $receipt->kurs_id == $rate->id ? 'selected' : '' }}>{{ $rate->name }}</option>@endforeach</select></div>
                                        <div class="backend-form-field"><label for="receipt_amount_{{ $receipt->id }}">Amount</label><input id="receipt_amount_{{ $receipt->id }}" type="text" name="amount" class="backend-form-control" value="{{ $receipt->amount }}" required></div>
                                        <div class="backend-form-field"><label for="receipt_payment_date_{{ $receipt->id }}">Payment Date</label><input id="receipt_payment_date_{{ $receipt->id }}" type="text" name="payment_date" class="backend-form-control date-picker" value="{{ $receipt->payment_date ? date('d F Y', strtotime($receipt->payment_date)) : date('d F Y') }}" required></div>
                                        <div class="backend-form-field"><label for="receipt_note_{{ $receipt->id }}">Description</label><textarea id="receipt_note_{{ $receipt->id }}" data-backend-richtext="true" name="note" class="backend-form-control" rows="3">{{ $receipt->note }}</textarea></div>
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="orders-admin-detail-modal__footer">
                            <button type="submit" form="confirmReceipt{{ $receipt->id }}" class="backend-button backend-button-primary">Save Receipt</button>
                            <button type="button" class="backend-button backend-button-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @foreach(['rejectOrderModal' => ['title' => 'Reject Order', 'form' => 'rejectOrder', 'action' => url('/fupdate-order-rejected/' . $order->id), 'button' => 'Reject'], 'invalidOrderModal' => ['title' => 'Mark Invalid', 'form' => 'invalidOrder', 'action' => url('/fupdate-order-invalid/' . $order->id), 'button' => 'Invalid'], 'archiveOrderModal' => ['title' => 'Archive Order', 'form' => 'archiveOrder', 'action' => route('admin.orders.workflow.archive', ['id' => $order->id]), 'button' => 'Archive']] as $modalId => $modal)
            <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content orders-admin-detail-modal">
                        <div class="orders-admin-detail-modal__header"><h3>{{ $modal['title'] }}</h3></div>
                        <div class="orders-admin-detail-modal__body">
                            <form id="{{ $modal['form'] }}" action="{{ $modal['action'] }}" method="post">
                                @csrf
                                @method('put')
                                <input type="hidden" name="author" value="{{ Auth::id() }}">
                                <input type="hidden" name="checkin" value="{{ $order->checkin }}">
                                <input type="hidden" name="checkout" value="{{ $order->checkout }}">
                                <input type="hidden" name="traveldate" value="{{ $order->travel_date }}">
                                <div class="backend-form-field"><label for="{{ $modal['form'] }}_message">Reason visible to agent</label><textarea id="{{ $modal['form'] }}_message" data-backend-richtext="true" name="msg" class="backend-form-control" rows="4" required></textarea></div>
                            </form>
                        </div>
                        <div class="orders-admin-detail-modal__footer">
                            <button type="submit" form="{{ $modal['form'] }}" class="backend-button backend-button-danger">{{ $modal['button'] }}</button>
                            <button type="button" class="backend-button backend-button-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endcan
@endsection
