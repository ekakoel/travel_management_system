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
        $invoiceEnPath = "storage/document/invoice-{$inv_no}-{$order->id}_en.pdf";
        $invoiceZhPath = "storage/document/invoice-{$inv_no}-{$order->id}_zh.pdf";
        $hasInvoicePdf = File::exists(public_path($invoiceEnPath)) || File::exists(public_path($invoiceZhPath));
        $priceRows = collect([
            ['label' => 'Base price', 'value' => currencyFormatUsd($order->price_total ?: $order->normal_price ?: $order->price_pax ?: 0), 'show' => true],
            ['label' => 'Optional charges', 'value' => currencyFormatUsd($optional_rate_order_total_price), 'show' => $optional_rate_order_total_price > 0],
            ['label' => 'Additional services', 'value' => currencyFormatUsd($total_additional_service), 'show' => $total_additional_service > 0],
            ['label' => 'Airport shuttle', 'value' => currencyFormatUsd($order->airport_shuttle_price), 'show' => $order->airport_shuttle_price > 0],
            ['label' => 'Promotion', 'value' => '- ' . currencyFormatUsd($total_promotion_disc), 'show' => $total_promotion_disc > 0],
            ['label' => 'Booking code', 'value' => '- ' . currencyFormatUsd($order->bookingcode_disc), 'show' => $order->bookingcode_disc > 0],
            ['label' => 'Discount', 'value' => '- ' . currencyFormatUsd($order->discounts), 'show' => $order->discounts > 0],
        ])->filter(fn ($row) => $row['show']);
        $serviceDetails = collect([
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
                            <a href="{{ route('orders-admin') }}" class="backend-page-primary-action">
                                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                @lang('admin-orders.detail.back_to_orders')
                            </a>
                        </x-slot>
                    </x-backend.page-hero>

                    <div class="backend-page-toolbar orders-admin-detail-toolbar">
                        <nav aria-label="{{ __('admin-orders.detail.breadcrumb_label') }}">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">@lang('admin-orders.breadcrumb.admin')</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('orders-admin') }}">@lang('admin-orders.breadcrumb.orders')</a></li>
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
                                    <span class="backend-page-eyebrow">@lang('admin-orders.detail.overview_eyebrow')</span>
                                    <h2>@lang('admin-orders.detail.overview_title')</h2>
                                </div>
                                <strong>{{ $orderDetailSummary['total_price'] }}</strong>
                            </div>
                            <dl class="orders-admin-detail-metrics">
                                <div><dt>Order</dt><dd>{{ $order->orderno }}</dd></div>
                                <div><dt>Reservation</dt><dd>{{ $reservation->rsv_no ?? '-' }}</dd></div>
                                <div><dt>Reservation Status</dt><dd>{{ $reservation->status ?? '-' }}</dd></div>
                                <div><dt>Service</dt><dd>{{ $order->service ?? '-' }}</dd></div>
                                <div><dt>Hotel</dt><dd>{{ $isHotelOrder ? ($hotel?->name ?? $order->servicename ?? '-') : ($order->servicename ?? '-') }}</dd></div>
                                <div><dt>Room / Subservice</dt><dd>{{ $hotelRoom?->rooms ?? $order->subservice ?? '-' }}</dd></div>
                                <div><dt>Stay / Schedule</dt><dd>{{ $orderDetailSummary['schedule'] }}</dd></div>
                                <div><dt>Rooms</dt><dd>{{ $order->number_of_room ?: '-' }}</dd></div>
                                <div><dt>Guests</dt><dd>{{ $orderDetailSummary['guests'] }}</dd></div>
                                <div><dt>Agent</dt><dd>{{ $orderDetailSummary['agent'] }}</dd></div>
                                <div><dt>Handled By</dt><dd>{{ $orderDetailSummary['handled_by'] }}</dd></div>
                                <div><dt>Payment</dt><dd>{{ $orderDetailSummary['payment_status'] }}</dd></div>
                            </dl>
                            <nav class="orders-admin-detail-jump" aria-label="{{ __('admin-orders.detail.quick_links.label') }}">
                                @foreach($orderDetailQuickLinks as $link)
                                    @if($link['href'] !== '#hotel-validation' || $isHotelOrder)
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
                                <div class="orders-admin-detail-section">
                                    <div class="orders-admin-detail-section__header">
                                        <span class="backend-page-eyebrow">Service</span>
                                        <h3>Order Details</h3>
                                    </div>
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
                                <form id="updateConfirmationNumber" action="{{ url('/fupdate-confirmation-number-' . $order->id) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="orders-admin-detail-form-grid">
                                        <div class="backend-form-field">
                                            <label for="confirmation_order">Hotel / supplier confirmation number</label>
                                            <input id="confirmation_order" name="confirmation_order" type="text" value="{{ old('confirmation_order', $order->confirmation_order) }}" class="backend-form-control @error('confirmation_order') is-invalid @enderror" placeholder="Confirmation number" required>
                                        </div>
                                        <div class="orders-admin-detail-form-action">
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
                                    @if($canEditOrder && $isOwner)
                                        <button type="button" class="backend-button backend-button-primary" data-toggle="modal" data-target="#addGuestModal">
                                            <i class="fa fa-plus" aria-hidden="true"></i> Add Guest
                                        </button>
                                    @endif
                                </div>

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
                                                        <td>{{ $guest->sex === 'm' ? 'Male' : ($guest->sex === 'f' ? 'Female' : '-') }} / {{ $guest->age ?: '-' }}</td>
                                                        <td>{{ $guest->phone ?: '-' }}</td>
                                                        <td class="text-right">
                                                            @if($canEditOrder && $isOwner)
                                                                <form id="deleteGuest{{ $guest->id }}" action="{{ url('/delete-guest/' . $guest->id) }}" method="post">
                                                                    @csrf
                                                                    @method('delete')
                                                                </form>
                                                                <div class="backend-table-actions">
                                                                    <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#editGuest{{ $guest->id }}" aria-label="Edit guest">
                                                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                                                    </button>
                                                                    <button type="submit" form="deleteGuest{{ $guest->id }}" class="backend-danger-icon-action" onclick="return confirm('Delete this guest?')" aria-label="Delete guest">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i>
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
                                    <strong class="orders-admin-detail-total">{{ currencyFormatUsd($order->final_price) }}</strong>
                                </div>
                                <div class="orders-admin-detail-price">
                                    @foreach($priceRows as $row)
                                        <div><span>{{ $row['label'] }}</span><strong>{{ $row['value'] }}</strong></div>
                                    @endforeach
                                    <div class="orders-admin-detail-price__total"><span>Total USD</span><strong>{{ currencyFormatUsd($order->final_price) }}</strong></div>
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
                                <span class="backend-page-eyebrow">@lang('admin-orders.detail.recommendations.eyebrow')</span>
                                <h2>@lang('admin-orders.detail.recommendations.title')</h2>
                                @forelse($orderDetailRecommendations as $recommendation)
                                    <a class="orders-admin-detail-next__item orders-admin-detail-next__item--{{ $recommendation['tone'] }}" href="{{ $recommendation['href'] }}">
                                        <strong>{{ $recommendation['label'] }}</strong>
                                        <span>{{ $recommendation['description'] }}</span>
                                    </a>
                                @empty
                                    <div class="orders-admin-detail-next__empty">@lang('admin-orders.detail.recommendations.empty')</div>
                                @endforelse
                            </section>

                            <section class="backend-panel orders-admin-detail-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Workflow</span>
                                        <h2>Validation Actions</h2>
                                    </div>
                                </div>
                                @if($canConfirmOrder)
                                    <form id="factivate-order-{{ $order->id }}" action="{{ url('/factivate-order/' . $order->id) }}" method="post">
                                        @csrf
                                        @method('put')
                                        <div class="orders-admin-detail-form-grid orders-admin-detail-form-grid--single">
                                            <div class="backend-form-field">
                                                <label for="bank">Bank</label>
                                                <select id="bank" name="bank" class="backend-form-control">
                                                    @foreach($banks as $bank)
                                                        <option {{ $bank->currency == 'USD' ? 'selected' : '' }} value="{{ $bank->id }}">{{ $bank->bank }} - {{ $bank->currency }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="backend-form-field">
                                                <label for="currency">Currency</label>
                                                <select id="currency" name="currency" class="backend-form-control">
                                                    @foreach($rates as $rate)
                                                        <option {{ $rate->name == 'USD' ? 'selected' : '' }} value="{{ $rate->id }}">{{ $rate->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                                <form id="generateInvoice" action="{{ url('/fgenerate-invoice-' . $order->id) }}" method="post">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" name="bank" value="{{ $banks->firstWhere('currency', 'USD')?->id ?? $banks->first()?->id }}">
                                    <input type="hidden" name="currency" value="{{ $rates->firstWhere('name', 'USD')?->id ?? $rates->first()?->id }}">
                                </form>
                                <form id="sendConfirmation" action="{{ url('/fsend-confirmation-' . $order->id) }}" method="post">@csrf @method('put')</form>
                                <form id="resendConfirmation" action="{{ url('/fresend-confirmation-order-' . $order->id) }}" method="post">@csrf @method('put')</form>
                                <form id="sendApprovalEmail" action="{{ url('/fsend-approval-email-' . $order->id) }}" method="post">@csrf @method('put')</form>
                                <form id="finalizationOrder" action="{{ route('func.admin-finalization-order', $order->id) }}" method="post">@csrf @method('PUT')</form>

                                <div class="orders-admin-detail-action-list">
                                    @if($canConfirmOrder)
                                        <button type="submit" form="factivate-order-{{ $order->id }}" class="backend-button backend-button-primary">
                                            <i class="fa fa-check" aria-hidden="true"></i> Confirm Order
                                        </button>
                                    @endif
                                    @if(!$hasInvoicePdf)
                                        <button type="submit" form="generateInvoice" class="backend-button backend-button-primary">
                                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Generate Invoice
                                        </button>
                                    @endif
                                    @if($hasInvoicePdf && !$reservation->send)
                                        <button type="submit" form="sendConfirmation" class="backend-button backend-button-primary">
                                            <i class="fa fa-envelope" aria-hidden="true"></i> Send Confirmation
                                        </button>
                                    @elseif($hasInvoicePdf && $canEditOrder)
                                        <button type="submit" form="resendConfirmation" class="backend-button backend-button-primary">
                                            <i class="fa fa-envelope" aria-hidden="true"></i> Resend Confirmation
                                        </button>
                                    @endif
                                    @if($hasInvoicePdf)
                                        <a class="backend-button backend-button-secondary" href="{{ url('/print-contract-order-' . $order->id) }}" target="_blank">
                                            <i class="fa fa-print" aria-hidden="true"></i> Print Document
                                        </a>
                                    @endif
                                    @if(File::exists(public_path($invoiceEnPath)))
                                        <a class="backend-button backend-button-secondary" href="{{ asset($invoiceEnPath) }}" target="_blank">
                                            <i class="fa fa-download" aria-hidden="true"></i> Invoice EN
                                        </a>
                                    @endif
                                    @if(File::exists(public_path($invoiceZhPath)))
                                        <a class="backend-button backend-button-secondary" href="{{ asset($invoiceZhPath) }}" target="_blank">
                                            <i class="fa fa-download" aria-hidden="true"></i> Invoice ZH
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
                                    @if($order->status !== 'Paid')
                                        <button type="button" class="backend-button backend-button-primary" data-toggle="modal" data-target="#addNoteModal">
                                            <i class="fa fa-plus" aria-hidden="true"></i> Add
                                        </button>
                                    @endif
                                </div>
                                @if($order->msg)
                                    <article class="orders-admin-detail-note orders-admin-detail-note--danger">
                                        <strong>Visible rejection/invalid reason</strong>
                                        <span>Agent-facing</span>
                                        <p>{{ $order->msg }}</p>
                                    </article>
                                @endif
                                @forelse($order_notes as $note)
                                    @php $operator = $admins->where('id', $note->user_id)->first(); @endphp
                                    <article class="orders-admin-detail-note">
                                        <strong>{{ dateTimeFormat($note->created_at) }} - {{ $operator?->name ?? 'Admin' }}</strong>
                                        <span>{{ $note->status }}</span>
                                        <p>{!! $note->note !!}</p>
                                    </article>
                                @empty
                                    <div class="orders-admin-detail-next__empty">No notes recorded.</div>
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
                                    <button type="button" class="backend-button backend-button-danger" data-toggle="modal" data-target="#rejectOrderModal">
                                        <i class="fa fa-ban" aria-hidden="true"> </i> Reject
                                    </button>
                                    <button type="button" class="backend-button backend-button-danger" data-toggle="modal" data-target="#invalidOrderModal">
                                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Mark Invalid
                                    </button>
                                    <button type="button" class="backend-button backend-button-danger" data-toggle="modal" data-target="#archiveOrderModal">
                                        <i class="fa fa-archive" aria-hidden="true"></i> Archive
                                    </button>
                                </div>
                            </section>
                        </aside>
                    </div>
                </div>
            </div>
        </main>

        @if($canConfirmOrder)
            @include('partials.loading-form', ['id' => 'factivate-order-{{ $order->id }}'])
        @endif

        @if($canEditOrder && $isOwner)
            <div class="modal fade" id="addGuestModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content orders-admin-detail-modal">
                        <div class="orders-admin-detail-modal__header"><h3>Add Guest</h3></div>
                        <div class="orders-admin-detail-modal__body">
                            <form id="addGuest" action="{{ route('func.reservation-add-guest', $order->id) }}" method="post">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-sm-6 backend-form-field"><label>Name</label><input type="text" name="name" class="backend-form-control" required></div>
                                    <div class="col-sm-6 backend-form-field"><label>Mandarin Name</label><input type="text" name="name_mandarin" class="backend-form-control"></div>
                                    <div class="col-sm-4 backend-form-field"><label>Gender</label><select name="sex" class="backend-form-control" required><option value="">Select</option><option value="m">Male</option><option value="f">Female</option></select></div>
                                    <div class="col-sm-4 backend-form-field"><label>Age</label><select name="age" class="backend-form-control" required><option value="">Select</option><option value="Adult">Adult</option><option value="Child">Child</option></select></div>
                                    <div class="col-sm-4 backend-form-field"><label>Phone</label><input type="number" name="phone" class="backend-form-control"></div>
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
                                        <div class="col-sm-6 backend-form-field"><label>Name <span>*</span></label><input type="text" name="name" class="backend-form-control" value="{{ $guest->name }}" required></div>
                                        <div class="col-sm-6 backend-form-field"><label>Mandarin Name</label><input type="text" name="name_mandarin" class="backend-form-control" value="{{ $guest->name_mandarin }}"></div>
                                        <div class="col-sm-4 backend-form-field"><label>Gender <span>*</span></label><select name="sex" class="backend-form-control" required><option value="m" {{ $guest->sex === 'm' ? 'selected' : '' }}>Male</option><option value="f" {{ $guest->sex === 'f' ? 'selected' : '' }}>Female</option></select></div>
                                        <div class="col-sm-4 backend-form-field"><label>Age <span>*</span></label><select name="age" class="backend-form-control" required><option value="Adult" {{ $guest->age === 'Adult' ? 'selected' : '' }}>Adult</option><option value="Child" {{ $guest->age === 'Child' ? 'selected' : '' }}>Child</option></select></div>
                                        <div class="col-sm-4 backend-form-field"><label>Phone</label><input type="number" name="phone" class="backend-form-control" value="{{ $guest->phone }}"></div>
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
                        <form id="addOrderNote" action="{{ url('/fadd-order-note-' . $order->id) }}" method="post">
                            @csrf
                            <div class="backend-form-field">
                                <label>Type</label>
                                <select name="status" class="backend-form-control">
                                    <option value="Info">Info</option>
                                    <option value="Waiting">Waiting</option>
                                    <option value="Urgent">Urgent</option>
                                    <option value="Error">Error</option>
                                    <option value="Reject">Reject</option>
                                </select>
                            </div>
                            <div class="backend-form-field"><label>Note</label><textarea data-backend-richtext="true" name="order_note" class="backend-form-control" rows="4" required></textarea></div>
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
                                        <img class="orders-admin-detail-receipt-img" src="{{ asset('storage/receipt/' . $receipt->receipt_img) }}" alt="Receipt">
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <form id="confirmReceipt{{ $receipt->id }}" action="{{ route('admin.confirm.receipt', $receipt->id) }}" method="post">
                                        @csrf
                                        <div class="backend-form-field"><label>Status</label><select name="status" class="backend-form-control"><option value="{{ $receipt->status }}">{{ $receipt->status }}</option><option value="Valid">Valid</option><option value="Invalid">Invalid</option></select></div>
                                        <div class="backend-form-field"><label>Currency</label><select name="kurs_id" class="backend-form-control">@foreach($rates as $rate)<option value="{{ $rate->id }}" {{ $receipt->kurs_id == $rate->id ? 'selected' : '' }}>{{ $rate->name }}</option>@endforeach</select></div>
                                        <div class="backend-form-field"><label>Amount</label><input type="text" name="amount" class="backend-form-control" value="{{ $receipt->amount }}"></div>
                                        <div class="backend-form-field"><label>Payment Date</label><input type="text" name="payment_date" class="backend-form-control date-picker" value="{{ $receipt->payment_date ? date('d F Y', strtotime($receipt->payment_date)) : date('d F Y') }}"></div>
                                        <div class="backend-form-field"><label>Description</label><textarea data-backend-richtext="true" name="note" class="backend-form-control" rows="3">{{ $receipt->note }}</textarea></div>
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

        @foreach(['rejectOrderModal' => ['title' => 'Reject Order', 'form' => 'rejectOrder', 'action' => url('/fupdate-order-rejected/' . $order->id), 'button' => 'Reject'], 'invalidOrderModal' => ['title' => 'Mark Invalid', 'form' => 'invalidOrder', 'action' => url('/fupdate-order-invalid/' . $order->id), 'button' => 'Invalid'], 'archiveOrderModal' => ['title' => 'Archive Order', 'form' => 'archiveOrder', 'action' => url('/farchive-order/' . $order->id), 'button' => 'Archive']] as $modalId => $modal)
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
                                <div class="backend-form-field"><label>Reason visible to agent</label><textarea data-backend-richtext="true" name="msg" class="backend-form-control" rows="4" required></textarea></div>
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
