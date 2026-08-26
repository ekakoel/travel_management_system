@extends('frontend.layouts.app')
@section('title', $activity->name)

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/activity-detail-entry.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/frontend/js/pages/activity-detail.js') }}" defer></script>
@endpush

@section('content')
    @php
        $activityOrderErrors = session('errors', new \Illuminate\Support\ViewErrorBag());
        $errors = $activityOrderErrors;
    @endphp
    <div class="frontend-page-shell activity-detail-page">
        <section class="container-fluid frontend-page-topband activity-detail-topband py-5">
            <div class="container py-4">
                <nav aria-label="breadcrumb" class="frontend-breadcrumb-wrap">
                    <ol class="breadcrumb frontend-breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('messages.Home')</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('services') }}">@lang('messages.Services')</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('view.activities-service') }}">@lang('messages.Activities')</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $activity->name }}</li>
                    </ol>
                </nav>

                <div class="frontend-page-intro">
                    <div class="frontend-page-intro__copy">
                        <h1 class="frontend-page-intro__title">@lang('messages.Partner-ready activity profile')</h1>
                        <p class="frontend-page-intro__text">
                            @lang('messages.Review activity highlights, inclusions, and itinerary context before continuing to the dedicated activity order flow.')
                        </p>
                    </div>
                    <div class="frontend-page-summary">
                        @foreach ($summaryStats as $summaryStat)
                            <div class="frontend-page-summary__item">
                                <span>{{ $summaryStat['label'] }}</span>
                                <strong>{{ $summaryStat['value'] }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if (request()->boolean('continue_order'))
                    <div class="alert alert-info mt-4 mb-0" role="status" aria-live="polite">
                        <strong>@lang('messages.Please review the activity details below before continuing.')</strong>
                    </div>
                @endif

                @if (($activityOrderForm['open_on_load'] ?? false) && $errors->any())
                    <div class="alert alert-danger mt-4 mb-0" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <div class="container frontend-content-section">

            <section class="activity-hero accommodation-hero frontend-surface-card">
                <div class="activity-hero__media accommodation-hero__media">
                    <img
                        src="{{ $galleryImages->first()['src'] ?? asset('images/default.webp') }}"
                        alt="{{ $activity->name }}"
                        loading="lazy"
                        onerror="this.onerror=null;this.src='{{ asset('images/default.webp') }}';"
                    >
                </div>
                <div class="activity-hero__content accommodation-hero__content">
                    <div class="activity-detail-kicker">@lang('messages.Activity Overview')</div>
                    <h2 class="activity-detail-title">{{ $activity->name }}</h2>
                    <p class="activity-detail-subtitle">
                        @lang('messages.A concise activity snapshot for travel agents, with quick access to inclusions, itinerary highlights, and the partner order continuation flow.')
                    </p>

                    <div class="activity-detail-meta accommodation-meta">
                        @foreach ($overviewFacts as $overviewFact)
                            <div class="activity-detail-meta__item accommodation-meta__item">
                                <span class="activity-detail-meta__label accommodation-meta__label">{{ $overviewFact['label'] }}</span>
                                <strong>{{ $overviewFact['value'] }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="accommodation-layout">
                <div class="accommodation-main">
                    @if ($galleryImages->count() > 1)
                        <section class="activity-section accommodation-section frontend-surface-card">
                            <div class="activity-section__header accommodation-section__header">
                                <div>
                                    <div class="activity-section__eyebrow accommodation-section__eyebrow">@lang('messages.Gallery')</div>
                                    <h2 class="activity-section__title accommodation-section__title">@lang('messages.Visual preview')</h2>
                                </div>
                                <div class="activity-section__range accommodation-section__range">{{ $galleryImages->count() }} @lang('messages.items')</div>
                            </div>
                            <div class="activity-gallery-grid">
                                @foreach ($galleryImages as $galleryImage)
                                    <article class="activity-gallery-card">
                                        <img src="{{ $galleryImage['thumb'] }}" alt="{{ $galleryImage['alt'] }}" loading="lazy"
                                            onerror="this.onerror=null;this.src='{{ asset('images/default.webp') }}';">
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @foreach ($activitySections as $activitySection)
                        @if ($activitySection['content'] || isset($activitySection['empty_text']))
                            <section class="activity-section accommodation-section frontend-surface-card">
                                <div class="activity-section__header accommodation-section__header">
                                    <div>
                                        <div class="activity-section__eyebrow accommodation-section__eyebrow">{{ $activitySection['eyebrow'] }}</div>
                                        <h2 class="activity-section__title accommodation-section__title">{{ $activitySection['title'] }}</h2>
                                    </div>
                                </div>

                                <div class="accommodation-richtext{{ !empty($activitySection['compact']) ? ' accommodation-richtext--compact' : '' }}">
                                    @if ($activitySection['content'])
                                        {!! $activitySection['content'] !!}
                                    @elseif (!empty($activitySection['empty_text']))
                                        <p>{{ $activitySection['empty_text'] }}</p>
                                    @endif
                                </div>
                            </section>
                        @endif
                    @endforeach

                    @if ($nearActivities->count() > 0)
                        <section class="activity-section accommodation-section frontend-surface-card">
                            <div class="activity-section__header accommodation-section__header">
                                <div>
                                    <div class="activity-section__eyebrow accommodation-section__eyebrow">@lang('messages.Similar Activities')</div>
                                    <h2 class="activity-section__title accommodation-section__title">@lang('messages.More activity options in the same destination context.')</h2>
                                </div>
                                <div class="activity-section__range accommodation-section__range">{{ $nearActivities->count() }} @lang('messages.items')</div>
                            </div>

                            <div class="activity-related-grid">
                                @foreach ($nearActivities as $nearActivity)
                                    <article class="activity-related-card">
                                        <a href="{{ route('view.activity-public-detail', $nearActivity->code) }}" class="activity-related-card__link">
                                            <div class="activity-related-card__media">
                                                <img
                                                    src="{{ $nearActivity->cover ? getThumbnail('/activities/activities-cover/' . $nearActivity->cover, 520, 320) : asset('images/default.webp') }}"
                                                    alt="{{ $nearActivity->name }}"
                                                    loading="lazy"
                                                    onerror="this.onerror=null;this.src='{{ asset('images/default.webp') }}';"
                                                >
                                                <span class="activity-related-card__badge">{{ $nearActivity->display_location }}</span>
                                            </div>
                                            <div class="activity-related-card__body">
                                                <div class="activity-related-card__meta">{{ $nearActivity->display_type }}</div>
                                                <h3 class="activity-related-card__title">{{ $nearActivity->name }}</h3>
                                                <p class="activity-related-card__text">{{ $nearActivity->display_description }}</p>
                                                <div class="activity-related-card__facts">
                                                    <span>{{ $nearActivity->display_duration }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                <aside class="accommodation-sidebar">
                    <div class="activity-sidebar-card frontend-surface-card frontend-sticky-panel">
                        <div class="activity-sidebar-card__eyebrow">@lang('messages.Reservation')</div>
                        <div class="activity-sidebar-card__section">
                            <h2 class="activity-sidebar-card__title">{{ $activity->name }}</h2>
                            <div class="activity-sidebar-card__list">
                                @foreach ($sidebarFacts as $sidebarFact)
                                    <div class="activity-sidebar-card__item">
                                        <span>{{ $sidebarFact['label'] }}</span>
                                        <strong>{{ $sidebarFact['value'] }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="activity-sidebar-card__cta">

                            @if ($canUseActivityOrderFlow)
                                <button
                                    type="button"
                                    class="btn btn-primary accommodation-cta__button activity-sidebar-card__cta-button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#activityOrderModal"
                                >
                                    {{ $activityOrderCta['button_label'] }}
                                </button>
                            @else
                                <a href="{{ $activityOrderCta['url'] }}" class="btn-primary accommodation-cta__button activity-sidebar-card__cta-button">
                                    {{ $activityOrderCta['button_label'] }}
                                </a>
                            @endif
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

        @if ($canUseActivityOrderFlow)
            @php
                $activityPriceAvailable = (bool) ($activityOrderForm['price_available'] ?? false);
                $priceAfterDiscount = $activityOrderForm['final_total'] ?? null;
            @endphp
            <div
                class="modal fade activity-order-modal frontend-order-modal"
                id="activityOrderModal"
                tabindex="-1"
                aria-labelledby="activityOrderModalLabel"
                aria-hidden="true"
                data-bs-focus="false"
                data-activity-order-modal
                data-activity-name="{{ $activity->name }}"
                data-activity-supplier="{{ $activityOrderForm['supplier'] }}"
            >
                <div class="modal-dialog modal-xl modal-dialog-centered frontend-order-modal__dialog">
                    <div class="modal-content activity-order-modal__content frontend-order-modal__surface">
                        <form
                            method="POST"
                            action="{{ $activityOrderForm['action'] }}"
                            enctype="multipart/form-data"
                            class="activity-reservation-wizard frontend-order-modal__form"
                            data-activity-order-form
                            data-open-on-load="{{ ($activityOrderForm['open_on_load'] ?? false) ? 'true' : 'false' }}"
                            data-quote-url="{{ $activityOrderForm['quote_url'] }}"
                            data-price-available="{{ $activityPriceAvailable ? 'true' : 'false' }}"
                            data-price-per-pax-minor="{{ $activityOrderForm['price_per_pax_minor'] ?? 0 }}"
                            data-promotion-discount-minor="{{ $activityOrderForm['promotion_discount_minor'] ?? 0 }}"
                            data-price-unavailable-label="@lang('messages.Activity pricing is not available.')"
                            data-price-loading-label="@lang('messages.Processing')"
                            data-capacity="{{ $activityOrderForm['capacity'] }}"
                            data-min-pax="{{ $activityOrderForm['min_pax'] }}"
                            data-valid-until="{{ $activityOrderForm['valid_until'] }}"
                            data-valid-until-label="{{ $activityOrderForm['valid_until_label'] }}"
                            data-manual-guest-threshold="{{ $activityOrderForm['manual_guest_threshold'] }}"
                            data-currency-code="USD"
                            data-initial-step="{{ $activityOrderForm['initial_step'] ?? 0 }}"
                            data-initial-guests='@json($activityOrderForm["prefill"]["guests"] ?? [])'
                            data-locale="{{ str_replace('_', '-', app()->getLocale()) }}"
                            data-guest-label="@lang('tour-detail.guest')"
                            data-pax-label="@lang('messages.pax')"
                            data-adult-label="@lang('tour-detail.age_adult')"
                            data-child-label="@lang('tour-detail.age_child')"
                            data-male-label="@lang('tour-detail.sex_male')"
                            data-female-label="@lang('tour-detail.sex_female')"
                            data-phone-label="@lang('messages.Phone')"
                            data-guest-singular-label="@lang('messages.Guest')"
                            data-guest-plural-label="@lang('messages.Guests')"
                            data-review-empty-label="@lang('activities.detail.order.review_empty')"
                            data-table-no-label="@lang('messages.No')"
                            data-table-name-label="@lang('messages.Name')"
                            data-table-age-category-label="@lang('activities.detail.order.table_age_category')"
                            data-table-gender-label="@lang('messages.Gender')"
                            data-table-phone-number-label="@lang('activities.detail.order.table_phone_number')"
                            data-select-label="@lang('messages.Select')"
                            data-guest-progress-label="@lang('activities.detail.order.guest_progress')"
                            data-guest-count-mismatch-label="@lang('activities.detail.order.guest_count_mismatch')"
                            data-guest-list-required-label="@lang('activities.detail.order.guest_list_required')"
                            data-guest-mode-manual-label="@lang('activities.detail.order.manual_mode_label')"
                            data-guest-mode-upload-label="@lang('activities.detail.order.upload_mode_label')"
                            data-guest-list-selected-label="@lang('activities.detail.order.guest_list_selected')"
                            data-guest-list-ready-label="@lang('activities.detail.order.guest_list_ready')"
                            data-file-size-label="@lang('activities.detail.order.file_size')"
                            data-guest-table-empty-label="@lang('activities.detail.order.guest_table_empty')"
                        >
                            @csrf
                            <input type="hidden" name="activity_order_source" value="{{ $activityOrderForm['order_source'] }}">
                            <input type="hidden" name="submission_token" value="{{ $activityOrderForm['submission_token'] }}">

                            <div class="transport-reservation-submit-overlay frontend-order-modal__overlay hidden" data-activity-order-overlay aria-hidden="true">
                                <div class="transport-reservation-submit-overlay__card">
                                    <span class="transport-reservation-submit-overlay__spinner" aria-hidden="true"></span>
                                    <strong>@lang('activities.detail.order.processing_title')</strong>
                                    <p>@lang('activities.detail.order.processing_text')</p>
                                </div>
                            </div>

                            <button type="button" class="activity-reservation-modal__close frontend-order-modal__close" data-bs-dismiss="modal" aria-label="@lang('messages.Close')">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button>

                            <div class="activity-reservation-modal__header frontend-order-modal__service">
                                <div class="activity-reservation-modal__media frontend-order-modal__media">
                                    <img
                                        src="{{ $galleryImages->first()['src'] ?? asset('images/default.webp') }}"
                                        alt="{{ $activity->name }}"
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='{{ asset('images/default.webp') }}';"
                                    >
                                </div>
                                <div class="frontend-order-modal__service-content">
                                    <div class="activity-reservation-modal__eyebrow frontend-order-modal__eyebrow">@lang('messages.Create Order')</div>
                                    <h2 id="activityOrderModalLabel" class="frontend-order-modal__title">{{ $activity->name }}</h2>
                                    <div class="activity-reservation-summary-grid frontend-order-modal__summary m-b-8">
                                        <div class="activity-reservation-summary-card frontend-order-modal__summary-card">
                                            <span>@lang('messages.Vendor')</span>
                                            <strong>{{ $activityOrderForm['supplier'] }}</strong>
                                        </div>
                                        <div class="activity-reservation-summary-card frontend-order-modal__summary-card">
                                            <span>@lang('messages.Duration')</span>
                                            <strong>{{ $activityOrderForm['duration_label'] }}</strong>
                                        </div>
                                        <div class="activity-reservation-summary-card frontend-order-modal__summary-card">
                                            <span>@lang('messages.Capacity')</span>
                                            <strong>{{ $activityOrderForm['capacity'] }} @lang('messages.pax')</strong>
                                        </div>
                                    </div>
                                    <div class="activity-reservation-modal__hero-card frontend-order-modal__price-card">
                                        <span>@lang('messages.Estimated Total')</span>
                                        <strong data-activity-order-price="final_total">{{ $activityPriceAvailable ? currencyFormatUsd($priceAfterDiscount) : '-' }}</strong>
                                        <small data-activity-order-price-status>{{ $activityPriceAvailable ? '' : __('messages.Activity pricing is not available.') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="activity-reservation-wizard__nav frontend-order-modal__nav">
                                <button type="button" class="activity-reservation-wizard__step frontend-order-modal__nav-item is-active" data-activity-order-nav="0">
                                    <span>1</span>
                                    <div>
                                        <strong>@lang('messages.Reservation details')</strong>
                                        <small>@lang('activities.detail.order.nav_schedule')</small>
                                    </div>
                                </button>
                                <button type="button" class="activity-reservation-wizard__step frontend-order-modal__nav-item" data-activity-order-nav="1">
                                    <span>2</span>
                                    <div>
                                        <strong>@lang('messages.Guest Details')</strong>
                                        <small>@lang('activities.detail.order.nav_guest_manifest')</small>
                                    </div>
                                </button>
                                <button type="button" class="activity-reservation-wizard__step frontend-order-modal__nav-item" data-activity-order-nav="2">
                                    <span>3</span>
                                    <div>
                                        <strong>@lang('messages.Review and submit')</strong>
                                        <small>@lang('activities.detail.order.nav_confirm_request')</small>
                                    </div>
                                </button>
                            </div>

                            <section class="activity-reservation-wizard__panel frontend-order-modal__panel is-active" data-activity-order-step="0">
                                <div class="activity-reservation-wizard__heading frontend-order-modal__heading">
                                    <div>
                                        <div class="activity-reservation-wizard__eyebrow frontend-order-modal__heading-eyebrow">@lang('activities.detail.order.step_label', ['number' => 1])</div>
                                    </div>
                                    <p>@lang('activities.detail.order.reservation_details_text')</p>
                                </div>

                                <div class="activity-reservation-form">
                                    <div class="activity-reservation-field">
                                        <label for="activityOrderTravelDate">@lang('messages.Activity Date') <span class="activity-reservation-required" aria-hidden="true">*</span></label>
                                        <input
                                            id="activityOrderTravelDate"
                                            type="text"
                                            name="travel_date"
                                            class="form-control @error('travel_date') is-invalid @enderror"
                                            value="{{ str_replace('T', ' ', $activityOrderForm['prefill']['travel_date']) }}"
                                            required
                                            autocomplete="off"
                                            data-ui-picker="datetime"
                                            data-ui-picker-min="{{ $activityOrderForm['minimum_travel_date'] }}"
                                            @if ($activityOrderForm['valid_until'])
                                                data-ui-picker-max="{{ $activityOrderForm['valid_until'] }} 23:59"
                                            @endif
                                            data-ui-picker-allow-today="true"
                                            data-ui-picker-format="YYYY-MM-DD HH:mm"
                                            data-ui-picker-parent="body"
                                            data-ui-picker-opens="center"
                                            data-ui-picker-drops="auto"
                                            data-ui-picker-show-buttons="true"
                                            data-ui-picker-minute-step="5"
                                            data-activity-order-field="travel_date"
                                        >
                                        @error('travel_date')
                                            <div class="alert-form">{{ $message }}</div>
                                        @enderror
                                        <p class="activity-reservation-helper">
                                            @lang('activities.detail.order.available_until'): {{ $activityOrderForm['valid_until_label'] }}
                                        </p>
                                    </div>

                                    <div class="activity-reservation-field">
                                        <label for="activityOrderGuests">@lang('messages.Number of Guests') <span class="activity-reservation-required" aria-hidden="true">*</span></label>
                                        <input
                                            id="activityOrderGuests"
                                            type="number"
                                            name="number_of_guests"
                                            class="form-control @error('number_of_guests') is-invalid @enderror"
                                            min="{{ $activityOrderForm['min_pax'] }}"
                                            max="{{ $activityOrderForm['capacity'] }}"
                                            value="{{ $activityOrderForm['prefill']['number_of_guests'] }}"
                                            required
                                            data-activity-order-field="number_of_guests"
                                        >
                                        @error('number_of_guests')
                                            <div class="alert-form">{{ $message }}</div>
                                        @enderror
                                        <p class="activity-reservation-helper">
                                            @lang('activities.detail.order.minimum_guests'): {{ $activityOrderForm['min_pax'] }}
                                            &middot;
                                            @lang('activities.detail.order.maximum_guests'): {{ $activityOrderForm['capacity'] }}
                                        </p>
                                    </div>

                                    <div class="activity-reservation-field">
                                        <label for="activityOrderPickupLocation">@lang('messages.Pick up location') <span class="activity-reservation-required" aria-hidden="true">*</span></label>
                                        <input
                                            id="activityOrderPickupLocation"
                                            type="text"
                                            name="pickup_location"
                                            class="form-control @error('pickup_location') is-invalid @enderror"
                                            value="{{ $activityOrderForm['prefill']['pickup_location'] }}"
                                            placeholder="@lang('activities.detail.order.pickup_location_placeholder')"
                                            required
                                            autocomplete="off"
                                            data-activity-order-field="pickup_location"
                                        >
                                        @error('pickup_location')
                                            <div class="alert-form">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="activity-reservation-field">
                                        <label for="activityOrderDropoffLocation">@lang('messages.Drop off location') <span class="activity-reservation-required" aria-hidden="true">*</span></label>
                                        <input
                                            id="activityOrderDropoffLocation"
                                            type="text"
                                            name="dropoff_location"
                                            class="form-control @error('dropoff_location') is-invalid @enderror"
                                            value="{{ $activityOrderForm['prefill']['dropoff_location'] }}"
                                            placeholder="@lang('activities.detail.order.dropoff_location_placeholder')"
                                            required
                                            autocomplete="off"
                                            data-activity-order-field="dropoff_location"
                                        >
                                        @error('dropoff_location')
                                            <div class="alert-form">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="activity-reservation-wizard__actions frontend-order-modal__actions">
                                    <button type="button" class="btn btn-primary" data-activity-order-next>@lang('activities.detail.order.cta_to_guest_details')</button>
                                </div>
                            </section>

                            <section class="activity-reservation-wizard__panel frontend-order-modal__panel" data-activity-order-step="1" hidden>
                                <div class="activity-reservation-wizard__heading frontend-order-modal__heading">
                                    <div>
                                        <div class="activity-reservation-wizard__eyebrow frontend-order-modal__heading-eyebrow">@lang('activities.detail.order.step_label', ['number' => 2])</div>
                                    </div>
                                    <p>@lang('activities.detail.order.guest_details_text')</p>
                                </div>

                                <div class="activity-reservation-field">
                                    <div class="activity-reservation-guest-head">
                                        <div>
                                            <label class="mb-0">@lang('messages.Guest Detail')</label>
                                            <small data-activity-guest-progress></small>
                                        </div>
                                        <span class="activity-reservation-mode-badge" data-activity-guest-mode-label></span>
                                    </div>

                                    <div class="activity-reservation-manual-guests" data-activity-manual-guests>
                                        <div class="activity-reservation-guest-list" data-activity-guest-list></div>
                                        <button type="button" class="btn btn-light activity-reservation-add-guest" data-activity-add-guest>
                                            @lang('activities.detail.order.add_more_guest')
                                        </button>
                                    </div>

                                    <div class="activity-reservation-upload-panel" data-activity-upload-panel hidden>
                                        <p>@lang('activities.detail.order.guest_list_upload_text')</p>
                                        <div class="activity-reservation-template-actions">
                                            <a href="{{ $activityOrderForm['template_xlsx_url'] }}" class="btn btn-light">
                                                @lang('activities.detail.order.download_xlsx_template')
                                            </a>
                                            <a href="{{ $activityOrderForm['template_csv_url'] }}" class="btn btn-light">
                                                @lang('activities.detail.order.download_csv_template')
                                            </a>
                                        </div>
                                        <div class="activity-reservation-field">
                                            <label for="activityGuestList">@lang('activities.detail.order.guest_list')</label>
                                            <input
                                                id="activityGuestList"
                                                type="file"
                                                name="guest_list"
                                                class="form-control @error('guest_list') is-invalid @enderror"
                                                accept=".xlsx,.csv,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                                data-activity-guest-list-input
                                            >
                                            <small data-activity-guest-list-status>@lang('activities.detail.order.guest_list_formats')</small>
                                            @error('guest_list')
                                                <div class="alert-form">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div
                                        class="activity-reservation-guest-error"
                                        data-activity-guest-error
                                        @if($activityOrderErrors->has('guests') || $activityOrderErrors->has('guest_list') || collect($activityOrderErrors->keys())->contains(fn ($key) => str_starts_with($key, 'guests.')))
                                        @else hidden @endif
                                    >
                                        @if ($activityOrderErrors->has('guests'))
                                            {{ $activityOrderErrors->first('guests') }}
                                        @elseif ($activityOrderErrors->has('guest_list'))
                                            {{ $activityOrderErrors->first('guest_list') }}
                                        @else
                                            {{ collect($activityOrderErrors->getMessages())
                                                ->filter(fn ($messages, $key) => str_starts_with($key, 'guests.'))
                                                ->flatten()
                                                ->first() }}
                                        @endif
                                    </div>
                                </div>

                                <div class="activity-reservation-wizard__actions frontend-order-modal__actions">
                                    <button type="button" class="btn btn-light" data-activity-order-prev>@lang('messages.Previous')</button>
                                    <button type="button" class="btn btn-primary" data-activity-order-next>@lang('activities.detail.order.cta_to_review')</button>
                                </div>
                            </section>

                            <section class="activity-reservation-wizard__panel frontend-order-modal__panel" data-activity-order-step="2" hidden>
                                <div class="activity-reservation-wizard__heading frontend-order-modal__heading">
                                    <div>
                                        <div class="activity-reservation-wizard__eyebrow frontend-order-modal__heading-eyebrow">@lang('activities.detail.order.step_label', ['number' => 3])</div>
                                        <h3>@lang('messages.Review and submit')</h3>
                                    </div>
                                    <p>@lang('messages.Review the activity request before sending it to the reservation team.')</p>
                                </div>

                                <div class="activity-reservation-review-grid">
                                    <div class="activity-reservation-review-card">
                                        <span>@lang('messages.Activity Date')</span>
                                        <strong data-activity-order-review="travel_date">{{ $activityOrderForm['prefill']['travel_date'] }}</strong>
                                    </div>
                                    <div class="activity-reservation-review-card">
                                        <span>@lang('messages.Guests')</span>
                                        <strong data-activity-order-review="number_of_guests">{{ $activityOrderForm['prefill']['number_of_guests'] }}</strong>
                                    </div>
                                    <div class="activity-reservation-review-card">
                                        <span>@lang('messages.Pick up location')</span>
                                        <strong data-activity-order-review="pickup_location">{{ $activityOrderForm['prefill']['pickup_location'] ?: '-' }}</strong>
                                    </div>
                                    <div class="activity-reservation-review-card">
                                        <span>@lang('messages.Drop off location')</span>
                                        <strong data-activity-order-review="dropoff_location">{{ $activityOrderForm['prefill']['dropoff_location'] ?: '-' }}</strong>
                                    </div>
                                    <div class="activity-reservation-review-card">
                                        <span>@lang('activities.detail.order.guest_information')</span>
                                        <strong data-activity-order-review="guest_information">-</strong>
                                    </div>
                                </div>

                                <div class="activity-reservation-inline-note activity-reservation-inline-note--table">
                                    <span>@lang('messages.Guest Details')</span>
                                    <div class="activity-reservation-guest-summary" data-activity-order-review-guest-table>
                                        <div class="activity-reservation-guest-summary__empty">@lang('activities.detail.order.review_empty')</div>
                                    </div>
                                </div>

                                <div class="activity-reservation-field">
                                    <label for="activityOrderNote">@lang('messages.Note')</label>
                                    <textarea
                                        id="activityOrderNote"
                                        name="note"
                                        rows="4"
                                        class="form-control @error('note') is-invalid @enderror"
                                        placeholder="@lang('messages.Optional')"
                                        data-activity-order-field="note"
                                    >{{ $activityOrderForm['prefill']['note'] }}</textarea>
                                    @error('note')
                                        <div class="alert-form">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="activity-reservation-price-breakdown">
                                    <div class="activity-reservation-price-breakdown__row">
                                        <span>@lang('messages.Price/Pax')</span>
                                        <strong data-activity-order-price="per_pax">{{ $activityPriceAvailable ? currencyFormatUsd($activityOrderForm['price_per_pax']) : '-' }}</strong>
                                    </div>
                                    <div class="activity-reservation-price-breakdown__row">
                                        <span>@lang('messages.Number of Guests')</span>
                                        <strong data-activity-order-price="guest_count">{{ $activityOrderForm['prefill']['number_of_guests'] }} @lang('messages.pax')</strong>
                                    </div>
                                    <div class="activity-reservation-price-breakdown__row" data-activity-order-promotion-row @if (($activityOrderForm['promotion_discount_minor'] ?? 0) <= 0) hidden @endif>
                                        <span>@lang('messages.Promotion')</span>
                                        <strong data-activity-order-price="promotion_discount">- {{ currencyFormatUsd($activityOrderForm['promotion_discount'] ?? 0) }}</strong>
                                    </div>
                                    <div class="activity-reservation-price-breakdown__row activity-reservation-price-breakdown__row--total">
                                        <span>@lang('messages.Total Price')</span>
                                        <strong data-activity-order-price="final_total">{{ $activityPriceAvailable ? currencyFormatUsd($priceAfterDiscount) : '-' }}</strong>
                                    </div>
                                </div>

                                <div class="activity-reservation-inline-note activity-reservation-inline-note--accent">
                                    @lang('activities.detail.order.review_note')
                                </div>

                                @include('partials.order-confirmation-checkbox', [
                                    'id' => 'activityTermsAccepted',
                                ])

                                <div class="activity-reservation-wizard__actions frontend-order-modal__actions">
                                    <button type="button" class="btn btn-light" data-activity-order-prev>@lang('messages.Previous')</button>
                                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">@lang('messages.Cancel')</button>
                                    <button type="submit" class="btn btn-primary" data-activity-order-submit data-processing-label="@lang('messages.Processing')" @disabled(!$activityPriceAvailable)>@lang('messages.Book Now')</button>
                                </div>
                            </section>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
