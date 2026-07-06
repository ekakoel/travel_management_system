@extends('frontend.layouts.app')

@section('title', __('messages.Orders'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/frontend-orders-entry.css') }}">
@endpush

@section('content')
    @php
        $isProfileIncomplete = Auth::user()->email == '' || Auth::user()->phone == '' || Auth::user()->office == '' || Auth::user()->address == '' || Auth::user()->country == '';

        $statusToneMap = [
            'Draft' => 'draft',
            'Pending' => 'pending',
            'Approved' => 'approved',
            'Confirmed' => 'confirmed',
            'Active' => 'active',
            'Paid' => 'paid',
            'Rejected' => 'rejected',
            'Invalid' => 'invalid',
            'Canceled' => 'canceled',
        ];

        $serviceIconMap = [
            'Hotel' => 'fa-hotel',
            'Hotel Promo' => 'fa-tags',
            'Hotel Package' => 'fa-box-open',
            'Tour Package' => 'fa-map-marked-alt',
            'Activity' => 'fa-person-hiking',
            'Transport' => 'fa-car-side',
            'Private Villa' => 'fa-house',
            'Wedding' => 'fa-heart',
        ];

        $resolveStandardRoutes = function ($order) {
            $detailUrl = route('view.detail-order', ['id' => $order->id]);
            $editUrl = null;

            if (in_array($order->service, ['Hotel', 'Hotel Promo', 'Hotel Package', 'Activity'], true)) {
                $detailUrl = route('view.detail-order-hotel', ['id' => $order->id]);
                $editUrl = route('view.edit-order-hotel', ['id' => $order->id]);
            } elseif ($order->service === 'Private Villa') {
                $detailUrl = route('view.detail-order-villa', ['id' => $order->id]);
                $editUrl = route('view.edit-order-villa', ['id' => $order->id]);
            } elseif ($order->service === 'Tour Package') {
                $detailUrl = route('view.detail-order-tour', ['id' => $order->id]);
                $editUrl = route('view.edit-order-tour', ['id' => $order->id]);
            } elseif ($order->service === 'Transport') {
                $detailUrl = route('view.detail-order-transport', ['id' => $order->id]);
                $editUrl = route('view.edit-order-transport', ['id' => $order->id]);
            }

            return [$detailUrl, $editUrl];
        };

        $buildStandardCard = function ($order, $scope = 'current') use ($serviceIconMap, $statusToneMap, $resolveStandardRoutes, $reservations) {
            [$detailUrl, $editUrl] = $resolveStandardRoutes($order);
            $reservation = $reservations->firstWhere('id', $order->rsv_id);
            $serviceLabel = __('messages.' . $order->service) !== 'messages.' . $order->service ? __('messages.' . $order->service) : $order->service;
            $statusLabel = __('messages.' . $order->status) !== 'messages.' . $order->status ? __('messages.' . $order->status) : $order->status;
            $guestsLabel = (int) $order->number_of_guests > 0 ? $order->number_of_guests . ' ' . __('messages.Guests') : __('messages.To be advised');
            $unitsLabel = (int) $order->number_of_room > 0 ? $order->number_of_room . ' ' . __('messages.Unit') : null;
            $scheduleLabel = $order->travel_date ? dateFormat($order->travel_date) : dateFormat($order->checkin);
            $dateRange = $order->checkout ? dateFormat($order->checkin) . ' - ' . dateFormat($order->checkout) : $scheduleLabel;
            $meta = array_filter([
                $order->subservice,
                $guestsLabel,
                $unitsLabel,
                $order->duration ? $order->duration . ' ' . ($order->duration > 1 ? __('messages.Nights') : __('messages.Night')) : null,
            ]);
            $discountTotal = (float) $order->discounts + (float) $order->bookingcode_disc + (float) $order->kick_back;
            $promotionDiscount = 0;
            if (!empty($order->promotion_disc)) {
                $decodedPromotionDisc = json_decode($order->promotion_disc, true);
                $promotionDiscount = is_array($decodedPromotionDisc) ? array_sum($decodedPromotionDisc) : 0;
            }
            $originalTotal = $discountTotal + $promotionDiscount > 0 ? $order->final_price + $discountTotal + $promotionDiscount : null;
            $serviceKey = \Illuminate\Support\Str::slug($order->service . '-' . $order->id);
            $canApprove = $order->status === 'Confirmed' && optional($reservation)->send === 'yes';

            return [
                'key' => 'order-' . $order->id,
                'scope' => $scope,
                'orderno' => $order->orderno,
                'service' => $serviceLabel,
                'service_name' => $order->servicename,
                'headline' => $order->subservice ?: $serviceLabel,
                'status' => $order->status,
                'status_label' => $statusLabel,
                'status_tone' => $statusToneMap[$order->status] ?? 'pending',
                'icon' => $serviceIconMap[$order->service] ?? 'fa-briefcase',
                'schedule' => $dateRange,
                'schedule_label' => $order->travel_date ? __('messages.Travel Date') : __('messages.Dates'),
                'meta' => $meta,
                'price' => $order->final_price,
                'original_price' => $originalTotal,
                'detail_url' => $detailUrl,
                'edit_url' => $editUrl,
                'delete_url' => '/delete-order/' . $order->id,
                'delete_form_id' => 'delete-order-' . $order->id,
                'approve_url' => '/fapprove-order-' . $order->id,
                'approve_form_id' => 'approve-order-' . $serviceKey,
                'can_edit' => in_array($order->status, ['Draft', 'Invalid'], true) && $editUrl,
                'can_delete' => in_array($order->status, ['Draft', 'Invalid', 'Rejected'], true),
                'can_approve' => $canApprove,
                'search' => strtolower(implode(' ', array_filter([
                    $order->orderno,
                    $serviceLabel,
                    $order->servicename,
                    $order->subservice,
                    strip_tags((string) $order->guest_detail),
                    $order->location,
                    $order->status,
                ]))),
                'updated_at' => optional($order->updated_at)->timestamp ?? optional($order->created_at)->timestamp ?? 0,
            ];
        };

        $buildWeddingCard = function ($order, $scope = 'current') use ($serviceIconMap, $statusToneMap, $brides) {
            $bride = $brides->firstWhere('id', $order->brides_id);
            $serviceLabel = __('messages.' . $order->service) !== 'messages.' . $order->service ? __('messages.' . $order->service) : $order->service;
            $statusLabel = __('messages.' . $order->status) !== 'messages.' . $order->status ? __('messages.' . $order->status) : $order->status;
            $eventDate = $order->wedding_date ?: $order->reception_date_start ?: $order->created_at;
            $headline = trim(collect([$bride->groom ?? null, $bride->bride ?? null])->filter()->implode(' & '));
            $meta = array_filter([
                $headline ?: null,
                $order->number_of_guests ? $order->number_of_guests . ' ' . __('messages.Guests') : null,
            ]);

            return [
                'key' => 'wedding-' . $order->id,
                'scope' => $scope,
                'orderno' => $order->orderno,
                'service' => $serviceLabel,
                'service_name' => __('messages.Wedding Order'),
                'headline' => $headline ?: $serviceLabel,
                'status' => $order->status,
                'status_label' => $statusLabel,
                'status_tone' => $statusToneMap[$order->status] ?? 'pending',
                'icon' => $serviceIconMap['Wedding'],
                'schedule' => $eventDate ? dateFormat($eventDate) : '-',
                'schedule_label' => __('messages.Date'),
                'meta' => $meta,
                'price' => $order->final_price,
                'original_price' => null,
                'detail_url' => route('view.detail-order-wedding', ['orderno' => $order->orderno]),
                'edit_url' => route('view.edit-order-wedding', ['orderno' => $order->orderno]),
                'delete_url' => '/delete-wedding-order/' . $order->id,
                'delete_form_id' => 'delete-wedding-order-' . $order->id,
                'approve_url' => null,
                'approve_form_id' => null,
                'can_edit' => in_array($order->status, ['Draft', 'Invalid'], true),
                'can_delete' => in_array($order->status, ['Draft', 'Invalid', 'Rejected'], true),
                'can_approve' => false,
                'search' => strtolower(implode(' ', array_filter([
                    $order->orderno,
                    $serviceLabel,
                    $headline,
                    $order->status,
                ]))),
                'updated_at' => optional($order->updated_at)->timestamp ?? optional($order->created_at)->timestamp ?? 0,
            ];
        };

        $draftOrders = $orders->filter(fn ($order) => $order->status === 'Draft')->map(fn ($order) => $buildStandardCard($order, 'draft'));
        $attentionOrders = $orders->filter(fn ($order) => in_array($order->status, ['Rejected', 'Invalid'], true))->map(fn ($order) => $buildStandardCard($order, 'attention'));
        $currentOrders = $orders->reject(fn ($order) => in_array($order->status, ['Draft', 'Rejected', 'Invalid'], true))->map(fn ($order) => $buildStandardCard($order, 'current'));
        $historyOrdersCollection = $orderhistories->map(fn ($order) => $buildStandardCard($order, 'history'));

        $weddingDraftOrders = $weddingorders->filter(fn ($order) => $order->status === 'Draft')->map(fn ($order) => $buildWeddingCard($order, 'draft'));
        $weddingAttentionOrders = $weddingorders->filter(fn ($order) => in_array($order->status, ['Rejected', 'Invalid'], true))->map(fn ($order) => $buildWeddingCard($order, 'attention'));
        $weddingCurrentOrders = $weddingorders
            ->reject(fn ($order) => in_array($order->status, ['Draft', 'Rejected', 'Invalid'], true))
            ->filter(function ($order) use ($now) {
                $eventDate = $order->wedding_date ?: $order->reception_date_start ?: $order->created_at;
                return !$eventDate || \Carbon\Carbon::parse($eventDate)->gte($now);
            })
            ->map(fn ($order) => $buildWeddingCard($order, 'current'));
        $weddingHistoryOrders = $weddingorders
            ->reject(fn ($order) => in_array($order->status, ['Draft', 'Rejected', 'Invalid'], true))
            ->filter(function ($order) use ($now) {
                $eventDate = $order->wedding_date ?: $order->reception_date_start ?: $order->created_at;
                return $eventDate && \Carbon\Carbon::parse($eventDate)->lt($now);
            })
            ->map(fn ($order) => $buildWeddingCard($order, 'history'));

        $draftCards = $draftOrders->concat($weddingDraftOrders)->sortByDesc('updated_at')->values();
        $attentionCards = $attentionOrders->concat($weddingAttentionOrders)->sortByDesc('updated_at')->values();
        $currentCards = $currentOrders->concat($weddingCurrentOrders)->sortByDesc('updated_at')->values();
        $historyCards = $historyOrdersCollection->concat($weddingHistoryOrders)->sortByDesc('updated_at')->values();

        $allCardsCount = $draftCards->count() + $attentionCards->count() + $currentCards->count() + $historyCards->count();
        $summaryCards = [
            [
                'label' => __('messages.Draft and Cart'),
                'value' => $draftCards->count(),
                'tone' => 'draft',
                'target' => '#orders-draft',
                'note' => __('messages.Newly saved orders that have not been submitted yet.'),
            ],
            [
                'label' => __('messages.In Progress'),
                'value' => $currentCards->count(),
                'tone' => 'current',
                'target' => '#orders-current',
                'note' => __('messages.Active and upcoming orders that are still being processed.'),
            ],
            [
                'label' => __('messages.Needs Attention'),
                'value' => $attentionCards->count(),
                'tone' => 'attention',
                'target' => '#orders-attention',
                'note' => __('messages.Orders that need revision, action, or follow-up from your side.'),
            ],
            [
                'label' => __('messages.Order History'),
                'value' => $historyCards->count(),
                'tone' => 'history',
                'target' => '#orders-history',
                'note' => __('messages.Completed and past orders kept for reference.'),
            ],
        ];
    @endphp

    <div class="frontend-page-shell orders-dashboard-page">
        <section class="orders-dashboard-hero">
            <div class="container">
                <div class="orders-dashboard-hero__content">
                    <div>
                        <span class="orders-dashboard-hero__eyebrow">@lang('messages.Orders')</span>
                        <h1 class="orders-dashboard-hero__title">@lang('messages.Manage Orders')</h1>
                        <p class="orders-dashboard-hero__text">
                            @lang('messages.Track drafts, active bookings, and completed trips in one place.')
                        </p>
                    </div>
                    <div class="orders-dashboard-hero__meta">
                        <div class="orders-dashboard-hero__metric">
                            <span>@lang('messages.Total')</span>
                            <strong>{{ $allCardsCount }}</strong>
                        </div>
                        <div class="orders-dashboard-hero__metric">
                            <span>@lang('messages.Attention')</span>
                            <strong>{{ $attentionCards->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="frontend-content-section orders-dashboard-main">
            <div class="container">
                @include('partials.alerts')

                @if ($isProfileIncomplete)
                    <div class="orders-notice orders-notice--warning">
                        <div>
                            <strong>@lang('messages.Complete Profile')</strong>
                            <p>@lang('messages.Please complete your profile data first to be able to submit orders, by clicking this link')</p>
                        </div>
                        <a class="btn btn-light" href="{{ route('profile') }}">@lang('messages.Edit Profile')</a>
                    </div>
                @endif

                @if ($rejectedorders->count() > 0)
                    <div class="orders-notice orders-notice--danger">
                        <div>
                            <strong>@lang('messages.Attention')</strong>
                            <p>
                                @lang('messages.you have') {{ $rejectedorders->count() }} @lang('messages.rejected order').
                                @lang('messages.Please complete your profile data with actual data to simplify the verification process of your order!')
                            </p>
                        </div>
                    </div>
                @endif

                <div class="orders-summary-grid">
                    @foreach ($summaryCards as $summaryCard)
                        <a class="orders-summary-card orders-summary-card--{{ $summaryCard['tone'] }}" href="{{ $summaryCard['target'] }}">
                            <span>{{ $summaryCard['label'] }}</span>
                            <strong>{{ $summaryCard['value'] }}</strong>
                            <p>{{ $summaryCard['note'] }}</p>
                        </a>
                    @endforeach
                </div>

                <div class="orders-toolbar">
                    <div class="orders-toolbar__search">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input
                            id="ordersSearchInput"
                            type="search"
                            class="form-control"
                            placeholder="@lang('messages.Search order number, service, destination, or guest')"
                        >
                    </div>
                    <div class="orders-toolbar__filters">
                        <button type="button" class="orders-filter-chip is-active" data-order-filter="all">@lang('messages.All Orders')</button>
                        <button type="button" class="orders-filter-chip" data-order-filter="draft">@lang('messages.Draft and Cart')</button>
                        <button type="button" class="orders-filter-chip" data-order-filter="current">@lang('messages.In Progress')</button>
                        <button type="button" class="orders-filter-chip" data-order-filter="attention">@lang('messages.Needs Attention')</button>
                        <button type="button" class="orders-filter-chip" data-order-filter="history">@lang('messages.Order History')</button>
                    </div>
                </div>

                @php
                    $sections = [
                        ['id' => 'orders-draft', 'scope' => 'draft', 'title' => __('messages.Draft and Cart'), 'items' => $draftCards],
                        ['id' => 'orders-current', 'scope' => 'current', 'title' => __('messages.In Progress'), 'items' => $currentCards],
                        ['id' => 'orders-attention', 'scope' => 'attention', 'title' => __('messages.Needs Attention'), 'items' => $attentionCards],
                        ['id' => 'orders-history', 'scope' => 'history', 'title' => __('messages.Order History'), 'items' => $historyCards],
                    ];
                @endphp

                <div class="orders-sections">
                    @foreach ($sections as $section)
                        <section id="{{ $section['id'] }}" class="orders-section" data-order-section="{{ $section['scope'] }}">
                            <div class="orders-section__header">
                                <div>
                                    <span class="orders-section__eyebrow">@lang('messages.Orders')</span>
                                    <h2 class="orders-section__title">{{ $section['title'] }}</h2>
                                </div>
                                <div class="orders-section__count">{{ $section['items']->count() }}</div>
                            </div>

                            @if ($section['items']->isEmpty())
                                <div class="orders-empty" data-empty-default>
                                    <h3>@lang('messages.No orders found for this section yet.')</h3>
                                    <p>@lang('messages.Start from the services page to create your next order when you are ready.')</p>
                                </div>
                            @else
                                <div class="orders-empty d-none" data-empty-search>
                                    <h3>@lang('messages.No orders matched your search.')</h3>
                                    <p>@lang('messages.Try a different keyword, order number, or clear the current filter.')</p>
                                </div>
                                <div class="orders-card-grid">
                                    @foreach ($section['items'] as $item)
                                        <article class="orders-card" data-order-card data-order-scope="{{ $item['scope'] }}" data-order-search="{{ $item['search'] }}">
                                            <div class="orders-card__top">
                                                <div class="orders-card__service">
                                                    <span class="orders-card__icon"><i class="fa {{ $item['icon'] }}" aria-hidden="true"></i></span>
                                                    <div>
                                                        <div class="orders-card__orderno">{{ $item['orderno'] }}</div>
                                                        <div class="orders-card__service-name">{{ $item['service'] }}</div>
                                                    </div>
                                                </div>
                                                <span class="orders-status orders-status--{{ $item['status_tone'] }}">{{ $item['status_label'] }}</span>
                                            </div>

                                            <div class="orders-card__body">
                                                <h3 class="orders-card__headline">{{ $item['headline'] }}</h3>
                                                <p class="orders-card__location">{{ $item['service_name'] }}</p>

                                                <div class="orders-card__facts">
                                                    <div class="orders-card__fact">
                                                        <span>{{ $item['schedule_label'] }}</span>
                                                        <strong>{{ $item['schedule'] }}</strong>
                                                    </div>
                                                    <div class="orders-card__fact">
                                                        <span>@lang('messages.Details')</span>
                                                        <strong>{{ implode(' / ', $item['meta']) ?: '-' }}</strong>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="orders-card__bottom">
                                                <div class="orders-card__price">
                                                    @if ($item['original_price'])
                                                        <span class="orders-card__price-original">{{ currencyFormatUsd($item['original_price']) }}</span>
                                                    @endif
                                                    <strong>{{ currencyFormatUsd($item['price']) }}</strong>
                                                </div>

                                                <div class="orders-card__actions">
                                                    @if ($item['can_edit'])
                                                        <a class="btn btn-primary" href="{{ $item['edit_url'] }}">@lang('messages.Continue Editing')</a>
                                                    @else
                                                        <a class="btn btn-primary" href="{{ $item['detail_url'] }}">@lang('messages.View Order')</a>
                                                    @endif

                                                    @if ($item['can_approve'])
                                                        <form id="{{ $item['approve_form_id'] }}" action="{{ $item['approve_url'] }}" method="post">
                                                            @csrf
                                                            @method('put')
                                                        </form>
                                                        <button type="submit" form="{{ $item['approve_form_id'] }}" class="btn btn-outline-primary">@lang('messages.Approve Order')</button>
                                                    @endif

                                                    @if ($item['can_delete'])
                                                        <form id="{{ $item['delete_form_id'] }}" action="{{ $item['delete_url'] }}" method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        </form>
                                                        <button
                                                            type="submit"
                                                            form="{{ $item['delete_form_id'] }}"
                                                            class="btn btn-outline-danger"
                                                            onclick="return confirm('@lang('messages.Are you sure?')');"
                                                        >
                                                            @lang('messages.Delete')
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ mix('build/frontend/js/pages/frontend-orders.js') }}" defer></script>
@endpush
